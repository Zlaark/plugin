/* =========================================================================
   Zlaark Deals - motion runtime
   Scroll reveals, count-ups, rating rings, score bars, cursor tilt,
   magnetic buttons, hero parallax and the category filter tabs.
   Everything is idempotent so Elementor can re-render a widget safely.
   ========================================================================= */
( function () {
	'use strict';

	/*
	 * Reveal animations start from opacity:0, so if this file never executes
	 * every animated section renders blank. Publish a marker the stylesheet
	 * can gate on: no marker, no hiding.
	 */
	document.documentElement.classList.add( 'zd-js' );

	var DONE = 'zdInit';
	var reduced = window.matchMedia && window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;

	function once( el ) {
		if ( el.dataset[ DONE ] ) {
			return false;
		}
		el.dataset[ DONE ] = '1';
		return true;
	}

	function each( scope, selector, fn ) {
		Array.prototype.forEach.call( scope.querySelectorAll( selector ), fn );
	}

	/* ------------------------------------------------- shared window events */

	/*
	 * Marquees, navbars, tab rails and article rails all need to hear about
	 * resize and scroll. Binding a listener per widget instance leaks: Elementor
	 * throws the widget's DOM away and rebuilds it on every control change, so
	 * the old handlers survive holding detached nodes, and each one still runs -
	 * the navbar's measured getBoundingClientRect() on every scroll event. After
	 * a few edits the editor canvas is doing dozens of forced layouts a frame.
	 *
	 * One listener per event instead, over a registry that drops jobs whose
	 * element has left the document, and throttled so a burst of scroll events
	 * costs one measurement pass.
	 */
	var resizeJobs = [];
	var scrollJobs = [];

	function attached( el ) {
		return el && ( 'isConnected' in el ? el.isConnected : document.contains( el ) );
	}

	function runJobs( jobs ) {
		var live = [];
		for ( var i = 0; i < jobs.length; i++ ) {
			if ( attached( jobs[ i ].el ) ) {
				live.push( jobs[ i ] );
			}
		}
		// Reuse the array the caller holds, so pruning sticks.
		jobs.length = 0;
		Array.prototype.push.apply( jobs, live );

		live.forEach( function ( job ) {
			job.fn();
		} );
	}

	/**
	 * Collapses a burst of events into a single call on the next frame.
	 *
	 * The rail and tab-strip handlers read scrollWidth/clientWidth and then
	 * write classes and disabled flags, which forces a synchronous layout. Run
	 * straight off a scroll event that is one forced layout per event fired.
	 */
	function onFrame( fn ) {
		var queued = false;
		return function () {
			if ( queued ) {
				return;
			}
			queued = true;
			requestAnimationFrame( function () {
				queued = false;
				fn();
			} );
		};
	}

	/** Runs fn on window resize for as long as el is still in the document. */
	function onResize( el, fn ) {
		resizeJobs.push( { el: el, fn: fn } );
	}

	/** Runs fn on window scroll for as long as el is still in the document. */
	function onScroll( el, fn ) {
		scrollJobs.push( { el: el, fn: fn } );
	}

	var resizeTimer = null;
	window.addEventListener( 'resize', function () {
		window.clearTimeout( resizeTimer );
		resizeTimer = window.setTimeout( function () {
			runJobs( resizeJobs );
		}, 150 );
	} );

	var scrollQueued = false;
	window.addEventListener(
		'scroll',
		function () {
			if ( scrollQueued ) {
				return;
			}
			scrollQueued = true;
			requestAnimationFrame( function () {
				scrollQueued = false;
				runJobs( scrollJobs );
			} );
		},
		{ passive: true }
	);

	/* ------------------------------------------------------------- reveals */

	/**
	 * Stagger is applied per reveal root so a grid of cards cascades, while
	 * two separate widgets on the page stay independent of each other.
	 */
	function applyStagger( root ) {
		var step = parseInt( root.getAttribute( 'data-zd-stagger' ), 10 );
		if ( isNaN( step ) || step <= 0 ) {
			return;
		}

		var items = root.querySelectorAll( '[data-zd-reveal]' );
		Array.prototype.forEach.call( items, function ( el, i ) {
			// Nested reveals (a card inside a revealed group) keep their own
			// delay rather than inheriting the parent's slot.
			if ( ! el.style.getPropertyValue( '--zd-delay' ) ) {
				el.style.setProperty( '--zd-delay', i * step + 'ms' );
			}
		} );
	}

	var revealObserver = 'IntersectionObserver' in window
		? new IntersectionObserver( function ( entries, obs ) {
			entries.forEach( function ( entry ) {
				if ( ! entry.isIntersecting ) {
					return;
				}
				entry.target.classList.add( 'is-in' );
				obs.unobserve( entry.target );
			} );
		}, { threshold: 0.12, rootMargin: '0px 0px -8% 0px' } )
		: null;

	function initReveals( scope ) {
		each( scope, '[data-zd-reveal-root]', function ( root ) {
			applyStagger( root );
		} );

		each( scope, '[data-zd-reveal]', function ( el ) {
			if ( ! once( el ) ) {
				return;
			}
			if ( ! revealObserver || reduced ) {
				el.classList.add( 'is-in' );
				return;
			}
			revealObserver.observe( el );
		} );
	}

	/* ---------------------------------------------------------- count-ups */

	function easeOutQuart( t ) {
		return 1 - Math.pow( 1 - t, 4 );
	}

	function countUp( el ) {
		var target = parseFloat( el.getAttribute( 'data-zd-count' ) );
		if ( isNaN( target ) ) {
			return;
		}

		var decimals = parseInt( el.getAttribute( 'data-zd-decimals' ), 10 ) || 0;
		var duration = parseInt( el.getAttribute( 'data-zd-duration' ), 10 ) || 1500;

		if ( reduced ) {
			el.textContent = target.toFixed( decimals );
			return;
		}

		var start = null;

		function frame( now ) {
			if ( start === null ) {
				start = now;
			}
			var progress = Math.min( ( now - start ) / duration, 1 );
			el.textContent = ( target * easeOutQuart( progress ) ).toFixed( decimals );
			if ( progress < 1 ) {
				requestAnimationFrame( frame );
			}
		}

		requestAnimationFrame( frame );
	}

	/* ------------------------------------------- rings, bars and counters */

	/** One observer drives every "animate when seen" element that isn't a reveal. */
	var valueObserver = 'IntersectionObserver' in window
		? new IntersectionObserver( function ( entries, obs ) {
			entries.forEach( function ( entry ) {
				if ( ! entry.isIntersecting ) {
					return;
				}
				var el = entry.target;
				obs.unobserve( el );

				if ( el.hasAttribute( 'data-zd-count' ) ) {
					countUp( el );
				} else if ( el.hasAttribute( 'data-zd-bar' ) ) {
					el.style.width = Math.max( 0, Math.min( 100, parseFloat( el.getAttribute( 'data-zd-bar' ) ) ) ) + '%';
				} else {
					// Rating rings and the bento connector wires both just need
					// the class flipped for their CSS transition to run.
					el.classList.add( 'is-in' );
				}
			} );
		}, { threshold: 0.35 } )
		: null;

	/**
	 * The markup now ships the real number and the real bar width, so a page
	 * with no scripting - or a crawler that does not run any - reads the
	 * measurements rather than a screen of zeros. The zeroing therefore has to
	 * happen here, at init, where we know scripting is alive: the animation
	 * counts up FROM zero instead of the document starting there.
	 */
	function primeValue( el ) {
		if ( reduced ) {
			return; // Reduced motion: the shipped value is already the answer.
		}
		if ( el.hasAttribute( 'data-zd-count' ) ) {
			var d = parseInt( el.getAttribute( 'data-zd-decimals' ), 10 ) || 0;
			el.textContent = ( 0 ).toFixed( d );
		} else if ( el.hasAttribute( 'data-zd-bar' ) ) {
			el.style.width = '0%';
		}
	}

	function watch( el ) {
		if ( ! once( el ) ) {
			return;
		}

		primeValue( el );

		if ( ! valueObserver ) {
			// No observer support: jump straight to the finished state.
			if ( el.hasAttribute( 'data-zd-count' ) ) {
				var t = parseFloat( el.getAttribute( 'data-zd-count' ) );
				var d = parseInt( el.getAttribute( 'data-zd-decimals' ), 10 ) || 0;
				el.textContent = isNaN( t ) ? '' : t.toFixed( d );
			} else if ( el.hasAttribute( 'data-zd-bar' ) ) {
				el.style.width = parseFloat( el.getAttribute( 'data-zd-bar' ) ) + '%';
			} else {
				el.classList.add( 'is-in' );
			}
			return;
		}
		valueObserver.observe( el );
	}

	function initValues( scope ) {
		each( scope, '.zd-ring', function ( ring ) {
			// The dash offset the bar animates to, derived from the percentage.
			var pct = parseFloat( ring.getAttribute( 'data-zd-ring' ) ) || 0;
			var circ = parseFloat( getComputedStyle( ring ).getPropertyValue( '--zd-ring-circ' ) ) || 163;
			ring.style.setProperty( '--zd-ring-target', ( circ * ( 1 - pct / 100 ) ).toFixed( 2 ) );
			watch( ring );
		} );

		each( scope, '[data-zd-count]', watch );
		each( scope, '[data-zd-bar]', watch );
		each( scope, '.zd-bento__wires--draw', watch );
	}

	/**
	 * Bento collage: the stage publishes the pointer offset once and each card
	 * scales it by its own --zd-depth, so nearer cards travel further.
	 */
	function initDepthParallax( scope ) {
		if ( reduced ) {
			return;
		}

		each( scope, '.zd-bento__stage--parallax', function ( stage ) {
			if ( ! once( stage ) ) {
				return;
			}

			var host = stage.closest( '.zd-bento' ) || stage;
			var frame = null;

			host.addEventListener( 'pointermove', function ( e ) {
				if ( e.pointerType === 'touch' || frame ) {
					return;
				}
				frame = requestAnimationFrame( function () {
					frame = null;
					var box = stage.getBoundingClientRect();
					var x = ( e.clientX - box.left ) / box.width - 0.5;
					var y = ( e.clientY - box.top ) / box.height - 0.5;
					stage.style.setProperty( '--zd-px', ( x * -14 ).toFixed( 2 ) + 'px' );
					stage.style.setProperty( '--zd-py', ( y * -14 ).toFixed( 2 ) + 'px' );
				} );
			} );

			host.addEventListener( 'pointerleave', function () {
				stage.style.setProperty( '--zd-px', '0px' );
				stage.style.setProperty( '--zd-py', '0px' );
			} );
		} );
	}

	/* ------------------------------------------------------ pointer motion */

	var MAX_TILT = 7;

	function initTilt( scope ) {
		if ( reduced ) {
			return;
		}

		each( scope, '.zd-tilt', function ( card ) {
			if ( ! once( card ) ) {
				return;
			}

			var frame = null;

			card.addEventListener( 'pointermove', function ( e ) {
				if ( e.pointerType === 'touch' || frame ) {
					return;
				}
				frame = requestAnimationFrame( function () {
					frame = null;
					var box = card.getBoundingClientRect();
					var x = ( e.clientX - box.left ) / box.width - 0.5;
					var y = ( e.clientY - box.top ) / box.height - 0.5;
					card.style.setProperty( '--zd-ry', ( x * MAX_TILT ).toFixed( 2 ) + 'deg' );
					card.style.setProperty( '--zd-rx', ( -y * MAX_TILT ).toFixed( 2 ) + 'deg' );
				} );
			} );

			card.addEventListener( 'pointerenter', function ( e ) {
				if ( e.pointerType !== 'touch' ) {
					card.classList.add( 'is-hover' );
				}
			} );

			card.addEventListener( 'pointerleave', function () {
				card.classList.remove( 'is-hover' );
				card.style.setProperty( '--zd-rx', '0deg' );
				card.style.setProperty( '--zd-ry', '0deg' );
			} );
		} );
	}

	function initMagnetic( scope ) {
		if ( reduced ) {
			return;
		}

		each( scope, '.zd-magnetic', function ( btn ) {
			if ( ! once( btn ) ) {
				return;
			}

			var frame = null;

			btn.addEventListener( 'pointermove', function ( e ) {
				if ( e.pointerType === 'touch' || frame ) {
					return;
				}
				frame = requestAnimationFrame( function () {
					frame = null;
					var box = btn.getBoundingClientRect();
					var x = ( e.clientX - box.left - box.width / 2 ) * 0.28;
					var y = ( e.clientY - box.top - box.height / 2 ) * 0.4;
					btn.style.setProperty( '--zd-mx', x.toFixed( 1 ) + 'px' );
					btn.style.setProperty( '--zd-my', y.toFixed( 1 ) + 'px' );
				} );
			} );

			btn.addEventListener( 'pointerenter', function ( e ) {
				if ( e.pointerType !== 'touch' ) {
					btn.classList.add( 'is-hover' );
				}
			} );

			btn.addEventListener( 'pointerleave', function () {
				btn.classList.remove( 'is-hover' );
				btn.style.setProperty( '--zd-mx', '0px' );
				btn.style.setProperty( '--zd-my', '0px' );
			} );
		} );
	}

	/** Hero media drifts a few pixels against the pointer across the section. */
	function initParallax( scope ) {
		if ( reduced ) {
			return;
		}

		each( scope, '.zd-parallax', function ( media ) {
			if ( ! once( media ) ) {
				return;
			}

			// Track the pointer across the whole section, not just the image.
			var host = media.closest( '.zd-hero, .zd-hc, .zd-about' ) || media.parentElement;
			if ( ! host ) {
				return;
			}

			var frame = null;

			host.addEventListener( 'pointermove', function ( e ) {
				if ( e.pointerType === 'touch' || frame ) {
					return;
				}
				frame = requestAnimationFrame( function () {
					frame = null;
					var box = host.getBoundingClientRect();
					var x = ( e.clientX - box.left ) / box.width - 0.5;
					var y = ( e.clientY - box.top ) / box.height - 0.5;
					media.style.setProperty( '--zd-px', ( x * -22 ).toFixed( 1 ) + 'px' );
					media.style.setProperty( '--zd-py', ( y * -16 ).toFixed( 1 ) + 'px' );
				} );
			} );

			host.addEventListener( 'pointerleave', function () {
				media.style.setProperty( '--zd-px', '0px' );
				media.style.setProperty( '--zd-py', '0px' );
			} );
		} );
	}

	/* ------------------------------------------------------------- marquee */

	/**
	 * The loop works by translating one full track width. If the logos are
	 * narrower than the strip, that leaves empty space trailing behind them,
	 * so clone the set until each track is at least as wide as the viewport.
	 */
	/**
	 * "Fit a number per view" mode: give every logo an identical slot so
	 * exactly N of them span the strip. A percentage flex-basis can't do this
	 * because the track's own width is content-derived, so it's measured here.
	 */
	function sizeMarquee( marquee ) {
		if ( ! marquee.classList.contains( 'zd-marquee--fit' ) ) {
			return;
		}

		var viewport = marquee.querySelector( '.zd-marquee__viewport' );
		var track = marquee.querySelector( '.zd-marquee__track' );
		if ( ! viewport || ! track ) {
			return;
		}

		// --zd-per comes from a responsive control, so the browser has already
		// resolved which breakpoint's value applies.
		var per = parseFloat( getComputedStyle( marquee ).getPropertyValue( '--zd-per' ) ) || 5;
		per = Math.max( 1, Math.round( per ) );

		var gap = parseFloat( getComputedStyle( track ).columnGap ) || 0;
		var width = viewport.clientWidth;
		if ( ! width ) {
			return;
		}

		var slot = ( width - ( per - 1 ) * gap ) / per;
		marquee.style.setProperty( '--zd-item-w', Math.max( 24, slot ).toFixed( 2 ) + 'px' );
	}

	/*
	 * The track is padded with repeats of the authored set until it covers the
	 * viewport, so the scroll wraps without a visible seam.
	 *
	 * The first version did that by appending one set, reading scrollWidth,
	 * appending again - a forced layout per round - and rebuilt the whole track
	 * from scratch whenever a logo finished decoding. In --auto mode a logo has
	 * no width until it decodes, so the opening pass always ran the full 24
	 * rounds; every one of those hundreds of clones was an <img> that had not
	 * loaded yet; and each one's load event rebuilt all of them again. Hundreds
	 * of rebuilds of hundreds of nodes: a thousand image requests, tens of
	 * megabytes, thousands of forced reflows, and an editor that never settles.
	 *
	 * So: measure one set, derive the repeat count with arithmetic, and only
	 * ever touch the DOM when the count actually has to grow.
	 */
	var MARQUEE_MAX_COPIES = 24;

	function renderMarquee( tracks, set, copies ) {
		Array.prototype.forEach.call( tracks, function ( track ) {
			var frag = document.createDocumentFragment();
			for ( var i = 0; i < copies; i++ ) {
				set.forEach( function ( node ) {
					frag.appendChild( node.cloneNode( true ) );
				} );
			}
			track.textContent = '';
			track.appendChild( frag );
		} );
	}

	function fillMarquee( marquee ) {
		var viewport = marquee.querySelector( '.zd-marquee__viewport' );
		var tracks = marquee.querySelectorAll( '.zd-marquee__track' );
		if ( ! viewport || ! tracks.length ) {
			return;
		}

		var first = tracks[ 0 ];

		// The authored set, taken before anything has been cloned into place.
		if ( ! marquee.zdOriginals ) {
			var authored = Array.prototype.slice.call( first.children );
			if ( ! authored.length ) {
				return;
			}
			marquee.zdOriginals = authored.map( function ( node ) {
				return node.cloneNode( true );
			} );
		}

		var width = viewport.clientWidth;

		/*
		 * Not laid out yet - a collapsed container, a tab the editor has not
		 * shown. Padding against a zero target buys nothing; the resize pass
		 * comes back to it.
		 */
		if ( width <= 0 ) {
			return;
		}

		if ( ! marquee.zdCopies ) {
			renderMarquee( tracks, marquee.zdOriginals, 1 );
			marquee.zdCopies = 1;
		}

		// One layout read, whatever the repeat count already is.
		var unit = first.scrollWidth / marquee.zdCopies;
		if ( unit <= 0 ) {
			return; // Logos still have no width; a decode will call back.
		}

		var needed = Math.min( MARQUEE_MAX_COPIES, Math.ceil( width / unit ) + 1 );

		/*
		 * Grow only. A decoding logo makes the set wider, which can only lower
		 * the count needed, and rebuilding to shed a spare copy is invisible
		 * work - it is also what let one decode cascade into the next. Bailing
		 * here is what breaks the loop.
		 */
		if ( needed <= marquee.zdCopies ) {
			return;
		}

		renderMarquee( tracks, marquee.zdOriginals, needed );
		marquee.zdCopies = needed;
	}

	/*
	 * In --auto mode the item width comes from the logo's intrinsic ratio, so
	 * the track cannot be measured until the images decode. Each image is
	 * watched once; the flag lives on the element, and clones never carry it
	 * because they come from the authored set, which is never flagged.
	 */
	function watchMarqueeLogos( marquee, refresh ) {
		each( marquee, 'img', function ( img ) {
			if ( img.complete || img.getAttribute( 'data-zd-watched' ) ) {
				return;
			}
			img.setAttribute( 'data-zd-watched', '1' );
			img.addEventListener( 'load', refresh, { once: true } );
			img.addEventListener( 'error', refresh, { once: true } );
		} );
	}

	function initMarquee( scope ) {
		each( scope, '.zd-marquee', function ( marquee ) {
			if ( ! once( marquee ) ) {
				return;
			}

			// A burst of decodes costs one pass, not one pass each.
			var refresh = onFrame( function () {
				sizeMarquee( marquee );
				fillMarquee( marquee );
				watchMarqueeLogos( marquee, refresh );
			} );

			sizeMarquee( marquee );
			fillMarquee( marquee );
			watchMarqueeLogos( marquee, refresh );

			onResize( marquee, refresh );
		} );
	}

	/* -------------------------------------------------------------- navbar */

	function initNavbar( scope ) {
		each( scope, '.zd-nav', function ( nav ) {
			if ( ! once( nav ) ) {
				return;
			}

			var menu = nav.querySelector( '.zd-nav__menu' );
			var list = nav.querySelector( '.zd-nav__list' );
			var pill = nav.querySelector( '.zd-nav__pill' );
			var burger = nav.querySelector( '.zd-nav__burger' );
			var breakpoint = parseInt( nav.getAttribute( 'data-zd-nav-bp' ), 10 ) || 1024;

			/* --- collapse ------------------------------------------------ */
			// The collapse width is a per-widget setting, so it can't live in a
			// stylesheet media query - it's evaluated here instead.
			function syncCollapse() {
				var collapsed = window.innerWidth <= breakpoint;
				nav.classList.toggle( 'zd-nav--collapsed', collapsed );
				if ( ! collapsed ) {
					nav.classList.remove( 'is-open' );
					if ( burger ) {
						burger.setAttribute( 'aria-expanded', 'false' );
					}
				}
			}

			/* --- sliding indicator --------------------------------------- */

			function activeLink() {
				return nav.querySelector( '.zd-nav__list a.is-active' )
					|| nav.querySelector( '.zd-nav__list .current-menu-item > a' )
					|| nav.querySelector( '.zd-nav__list .current_page_item > a' );
			}

			function light( link ) {
				each( nav, '.zd-nav__list a', function ( a ) {
					a.classList.remove( 'is-lit' );
				} );
				if ( link ) {
					link.classList.add( 'is-lit' );
				}
			}

			function movePill( link, animate ) {
				if ( ! pill || ! list ) {
					return;
				}
				if ( ! link || nav.classList.contains( 'zd-nav--collapsed' ) ) {
					pill.style.opacity = '0';
					return;
				}

				var listBox = list.getBoundingClientRect();
				var linkBox = link.getBoundingClientRect();

				if ( ! animate ) {
					pill.style.transition = 'none';
				}
				pill.style.opacity = '1';
				pill.style.width = linkBox.width + 'px';
				pill.style.height = linkBox.height + 'px';
				pill.style.transform = 'translate(' +
					( linkBox.left - listBox.left ) + 'px,' +
					( linkBox.top - listBox.top ) + 'px)';

				if ( ! animate ) {
					// Force a reflow so the transition-less jump lands before
					// transitions are handed back.
					void pill.offsetWidth;
					pill.style.transition = '';
				}
			}

			function reset() {
				var current = activeLink();
				movePill( current, true );
				light( current );
			}

			if ( list && pill ) {
				var slides = nav.classList.contains( 'zd-nav--slide' );

				each( nav, '.zd-nav__list a', function ( a ) {
					a.addEventListener( 'pointerenter', function ( e ) {
						if ( ! slides || e.pointerType === 'touch' ) {
							return;
						}
						movePill( a, true );
						light( a );
					} );
				} );

				list.addEventListener( 'pointerleave', function () {
					if ( slides ) {
						reset();
					}
				} );

				onResize( nav, function () {
					syncCollapse();
					movePill( activeLink(), false );
				} );

				// Web fonts land after first paint and change the item widths.
				if ( document.fonts && document.fonts.ready ) {
					document.fonts.ready.then( function () {
						movePill( activeLink(), false );
					} );
				}
			}

			/* --- hamburger ------------------------------------------------ */

			if ( burger && menu ) {
				burger.addEventListener( 'click', function () {
					var open = nav.classList.toggle( 'is-open' );
					burger.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
				} );

				each( menu, 'a', function ( a ) {
					a.addEventListener( 'click', function () {
						nav.classList.remove( 'is-open' );
						burger.setAttribute( 'aria-expanded', 'false' );
					} );
				} );
			}

			/* --- stuck state ---------------------------------------------- */

			if ( nav.classList.contains( 'zd-nav--sticky' ) ) {
				var syncStuck = function () {
					nav.classList.toggle( 'is-stuck', nav.getBoundingClientRect().top <= 1 );
				};
				onScroll( nav, syncStuck );
				syncStuck();
			}

			syncCollapse();
			requestAnimationFrame( function () {
				movePill( activeLink(), false );
				light( activeLink() );
				nav.classList.add( 'is-ready' );
			} );
		} );
	}

	/* --------------------------------------------------------- filter tabs */

	function moveIndicator( tabs, btn ) {
		var indicator = tabs.querySelector( '.zd-tabs__indicator' );
		if ( ! indicator ) {
			return;
		}
		indicator.style.width = btn.offsetWidth + 'px';
		indicator.style.transform = 'translateX(' + btn.offsetLeft + 'px)';
	}

	/**
	 * Which element the tabs filter. `data-zd-tabs-target` names a selector
	 * looked up inside the widget root; without it we fall back to the deals
	 * grid, which is what the original filter tabs shipped against.
	 */
	function tabsTarget( tabs ) {
		var selector = tabs.getAttribute( 'data-zd-tabs-target' );

		if ( selector ) {
			var root = tabs.closest( '.zd-home' ) || tabs.parentNode;
			return root ? root.querySelector( selector ) : null;
		}

		var deals = tabs.closest( '.zd-deals' );
		return deals ? deals.querySelector( '.zd-grid' ) : null;
	}

	/** Greys out an arrow once the rail cannot travel any further that way. */
	function syncArrows( tabs, scroll ) {
		var prev = tabs.querySelector( '.zd-tabs__arrow--prev' );
		var next = tabs.querySelector( '.zd-tabs__arrow--next' );

		if ( ! prev && ! next ) {
			return;
		}

		var max = scroll.scrollWidth - scroll.clientWidth;
		var at = scroll.scrollLeft;

		// Sub-pixel layout means the ends rarely land on an exact integer.
		var atStart = at <= 1;
		var atEnd = at >= max - 1;

		if ( prev ) {
			prev.disabled = atStart;
		}
		if ( next ) {
			next.disabled = atEnd;
		}

		tabs.classList.toggle( 'is-at-start', atStart );
		tabs.classList.toggle( 'is-at-end', atEnd );
	}

	function initTabs( scope ) {
		each( scope, '.zd-tabs', function ( tabs ) {
			if ( ! once( tabs ) ) {
				return;
			}

			var scroll = tabs.querySelector( '.zd-tabs__scroll' ) || tabs;
			var grid = tabsTarget( tabs );
			var buttons = tabs.querySelectorAll( '.zd-tabs__btn' );
			var cardSelector = grid && grid.classList.contains( 'zd-lineup__grid' ) ? '.zd-lcard' : '.zd-card';

			var active = tabs.querySelector( '.zd-tabs__btn.is-active' ) || buttons[ 0 ];
			if ( active ) {
				// Fonts can still be loading, which would size the pill wrong.
				requestAnimationFrame( function () {
					moveIndicator( scroll, active );
					syncArrows( tabs, scroll );
				} );
			}

			onResize( tabs, function () {
				var current = tabs.querySelector( '.zd-tabs__btn.is-active' );
				if ( current ) {
					moveIndicator( scroll, current );
				}
				syncArrows( tabs, scroll );
			} );

			if ( scroll !== tabs ) {
				scroll.addEventListener( 'scroll', onFrame( function () {
					syncArrows( tabs, scroll );
				} ) );
			}

			each( tabs, '.zd-tabs__arrow', function ( arrow ) {
				arrow.addEventListener( 'click', function () {
					var back = arrow.classList.contains( 'zd-tabs__arrow--prev' );
					var step = Math.max( 120, Math.round( scroll.clientWidth * 0.7 ) );
					scroll.scrollLeft += back ? -step : step;
				} );
			} );

			// Filter groups use aria-pressed; the original deals tablist uses
			// aria-selected. Whichever the markup declares is the one we keep
			// in sync, so neither gets a stale or invented attribute.
			var stateAttr = ( active && active.hasAttribute( 'aria-pressed' ) ) ? 'aria-pressed' : 'aria-selected';

			Array.prototype.forEach.call( buttons, function ( btn ) {
				btn.addEventListener( 'click', function () {
					Array.prototype.forEach.call( buttons, function ( b ) {
						b.classList.remove( 'is-active' );
						b.setAttribute( stateAttr, 'false' );
					} );
					btn.classList.add( 'is-active' );
					btn.setAttribute( stateAttr, 'true' );
					moveIndicator( scroll, btn );

					// A tab reached with the keyboard can sit outside the rail.
					if ( scroll !== tabs && btn.scrollIntoView ) {
						btn.scrollIntoView( { block: 'nearest', inline: 'nearest' } );
					}

					if ( ! grid ) {
						return;
					}

					var filter = btn.getAttribute( 'data-zd-filter' );
					each( grid, cardSelector, function ( card ) {
						var terms = ( card.getAttribute( 'data-zd-terms' ) || '' ).split( /\s+/ );
						var show = filter === 'all' || terms.indexOf( filter ) !== -1;
						card.classList.toggle( 'is-filtered-out', ! show );
					} );

					grid.setAttribute( 'data-zd-active', filter );

					// Track count follows the visible cards, so a category with
					// two deals renders two columns rather than two cards and
					// two holes.
					var visible = grid.querySelectorAll( cardSelector + ':not(.is-filtered-out)' ).length;
					grid.style.setProperty( '--zd-cols', Math.max( 1, Math.min( 4, visible ) ) );
				} );
			} );
		} );
	}

	/* ----------------------------------------------------------- offer bar */

	/**
	 * A dismissal is remembered per deal, so closing the bar on one article
	 * does not mean meeting it again on the next. Storage can throw outright
	 * in a private window or with site data blocked, so every access is
	 * guarded - a bar that cannot remember is still better than a page that
	 * throws on load.
	 */
	function barDismissed( id ) {
		try {
			return window.localStorage.getItem( 'zd-obar-' + id ) === '1';
		} catch ( e ) {
			return false;
		}
	}

	function dismissBar( id ) {
		try {
			window.localStorage.setItem( 'zd-obar-' + id, '1' );
		} catch ( e ) {
			/* Nothing to do: the bar still closes for this pageview. */
		}
	}

	/*
	 * True inside the Elementor editor canvas. Elementor holds a live reference
	 * to each widget's element, so anything that deletes one out from under it
	 * leaves the author with a widget they cannot select, move or edit.
	 */
	function inEditor() {
		return !! (
			document.body &&
			( document.body.classList.contains( 'elementor-editor-active' ) ||
			  document.body.classList.contains( 'elementor-editor-preview' ) )
		);
	}

	function initOfferBars( scope ) {
		var editing = inEditor();

		each( scope, '[data-zd-obar]', function ( bar ) {
			if ( ! once( bar ) ) {
				return;
			}

			var id = bar.getAttribute( 'data-zd-obar' );

			/*
			 * A dismissal is a visitor's choice, not an editing one. Honouring
			 * it in the editor deleted the widget from the canvas the moment it
			 * rendered - and because Elementor rebuilds the widget on every
			 * control change, it was deleted again on every edit, so the bar
			 * could never be worked on again once closed while previewing.
			 */
			if ( ! editing && barDismissed( id ) ) {
				bar.parentNode.removeChild( bar );
				return;
			}

			var delay = parseInt( bar.getAttribute( 'data-zd-obar-delay' ), 10 );
			if ( isNaN( delay ) || reduced ) {
				delay = 0;
			}

			window.setTimeout( function () {
				bar.classList.add( 'is-in' );
			}, delay );

			var close = bar.querySelector( '.zd-obar__close' );
			if ( close ) {
				close.addEventListener( 'click', function () {
					bar.classList.remove( 'is-in' );

					// In the editor the close button is there to be previewed,
					// not obeyed: no persisted dismissal, and the element stays
					// where Elementor put it.
					if ( editing ) {
						return;
					}

					dismissBar( id );

					// Let the slide-out finish before the bar leaves the DOM.
					window.setTimeout( function () {
						if ( bar.parentNode ) {
							bar.parentNode.removeChild( bar );
						}
					}, reduced ? 0 : 400 );
				} );
			}
		} );
	}

	/* ------------------------------------------------------ editorial rails */

	function initRails( scope ) {
		each( scope, '.zd-rail', function ( rail ) {
			if ( ! once( rail ) ) {
				return;
			}

			var track = rail.querySelector( '.zd-rail__track' );
			var prev = rail.querySelector( '.zd-rail__btn--prev' );
			var next = rail.querySelector( '.zd-rail__btn--next' );

			if ( ! track ) {
				return;
			}

			function sync() {
				var max = track.scrollWidth - track.clientWidth;

				// Nothing to scroll: hide the controls rather than show two
				// permanently dead buttons.
				var scrollable = max > 1;
				rail.classList.toggle( 'is-static', ! scrollable );

				if ( prev ) {
					prev.disabled = ! scrollable || track.scrollLeft <= 1;
				}
				if ( next ) {
					next.disabled = ! scrollable || track.scrollLeft >= max - 1;
				}
			}

			function step( back ) {
				var card = track.querySelector( '.zd-acard' );
				var width = card ? card.offsetWidth + 24 : Math.round( track.clientWidth * 0.8 );
				track.scrollLeft += back ? -width : width;
			}

			if ( prev ) {
				prev.addEventListener( 'click', function () {
					step( true );
				} );
			}
			if ( next ) {
				next.addEventListener( 'click', function () {
					step( false );
				} );
			}

			track.addEventListener( 'scroll', onFrame( sync ) );
			onResize( rail, sync );
			requestAnimationFrame( sync );
		} );
	}

	/* ----------------------------------------------------------------- boot */

	/* ---------------------------------------------------------- coupon copy */

	function initCoupons( scope ) {
		each( scope, '.zd-coupon', function ( box ) {
			if ( ! once( box ) ) {
				return;
			}

			var btn  = box.querySelector( '.zd-coupon__copy' );
			var code = box.getAttribute( 'data-zd-coupon' );

			if ( ! btn || ! code ) {
				return;
			}

			btn.addEventListener( 'click', function () {
				var original = btn.textContent;
				var done     = function () {
					btn.textContent = btn.getAttribute( 'data-zd-copied' ) || 'Copied';
					btn.classList.add( 'is-copied' );
					window.setTimeout( function () {
						btn.textContent = original;
						btn.classList.remove( 'is-copied' );
					}, 1900 );
				};

				if ( window.navigator.clipboard && window.navigator.clipboard.writeText ) {
					window.navigator.clipboard.writeText( code ).then( done, done );
					return;
				}

				// Clipboard API is unavailable over plain HTTP; fall back to a
				// hidden field so the button still works on staging sites.
				var tmp = document.createElement( 'textarea' );
				tmp.value = code;
				tmp.setAttribute( 'readonly', '' );
				tmp.style.position = 'absolute';
				tmp.style.left = '-9999px';
				document.body.appendChild( tmp );
				tmp.select();
				try {
					document.execCommand( 'copy' );
				} catch ( e ) {} // eslint-disable-line no-empty
				document.body.removeChild( tmp );
				done();
			} );
		} );
	}

	/* ------------------------------------------------------- deals index */

	/*
	 * Filtering and sorting run client-side against data attributes, so the
	 * markup stays fully cacheable. State is mirrored into the query string
	 * so a filtered view is linkable and indexable - the reference site's
	 * index has no filters at all, let alone shareable ones.
	 */
	function initIndex( scope ) {
		each( scope, '[data-zd-index]', function ( root ) {
			if ( ! once( root ) ) {
				return;
			}

			var list     = root.querySelector( '[data-zd-list]' );
			var empty    = root.querySelector( '[data-zd-empty]' );
			var moreBtn  = root.querySelector( '[data-zd-more]' );
			var tray     = root.querySelector( '[data-zd-tray]' );
			var sortSel  = root.querySelector( '[data-zd-sort]' );
			var searchEl = root.querySelector( '[data-zd-search]' );

			if ( ! list ) {
				return;
			}

			var rows     = [].slice.call( root.querySelectorAll( '[data-zd-row]' ) );
			var pageSize = parseInt( root.getAttribute( 'data-zd-page' ), 10 ) || 24;

			var state = {
				cat:    'all',
				types:  [],
				sort:   'saving',
				search: '',
				shown:  pageSize
			};

			function num( row, attr ) {
				return parseFloat( row.getAttribute( attr ) );
			}

			function matches( row ) {
				if ( state.cat !== 'all' ) {
					var cats = ( row.getAttribute( 'data-zd-cats' ) || '' ).split( ',' );
					if ( cats.indexOf( state.cat ) === -1 ) {
						return false;
					}
				}
				if ( state.types.length &&
					state.types.indexOf( row.getAttribute( 'data-zd-type' ) || '' ) === -1 ) {
					return false;
				}
				if ( state.search &&
					( row.getAttribute( 'data-zd-search' ) || '' ).indexOf( state.search ) === -1 ) {
					return false;
				}
				return true;
			}

			var comparators = {
				// Descending: the biggest saving and the best score lead.
				saving:   function ( a, b ) { return num( b, 'data-zd-saving' ) - num( a, 'data-zd-saving' ); },
				score:    function ( a, b ) { return num( b, 'data-zd-score' ) - num( a, 'data-zd-score' ); },
				// Ascending: fewest days since verification, fewest days left.
				verified: function ( a, b ) { return num( a, 'data-zd-verified' ) - num( b, 'data-zd-verified' ); },
				ends:     function ( a, b ) { return num( a, 'data-zd-ends' ) - num( b, 'data-zd-ends' ); },
				name:     function ( a, b ) {
					return ( a.getAttribute( 'data-zd-name' ) || '' )
						.localeCompare( b.getAttribute( 'data-zd-name' ) || '' );
				}
			};

			function apply() {
				var visible = rows.filter( matches );
				var cmp     = comparators[ state.sort ] || comparators.saving;

				visible.sort( cmp );

				// Reorder in a fragment so the list reflows once, not per row.
				var frag = document.createDocumentFragment();
				visible.forEach( function ( row, i ) {
					var over = i >= state.shown;
					row.hidden = over;
					row.classList.toggle( 'is-paged', over );
					frag.appendChild( row );
				} );
				rows.forEach( function ( row ) {
					if ( visible.indexOf( row ) === -1 ) {
						row.hidden = true;
						frag.appendChild( row );
					}
				} );
				list.appendChild( frag );

				if ( empty ) {
					empty.hidden = visible.length > 0;
				}
				if ( moreBtn ) {
					moreBtn.parentNode.hidden = visible.length <= state.shown;
				}

				syncUrl();
			}

			function syncUrl() {
				if ( ! window.history || ! window.history.replaceState ) {
					return;
				}
				var params = new URLSearchParams( window.location.search );

				if ( state.cat !== 'all' ) { params.set( 'cat', state.cat ); } else { params.delete( 'cat' ); }
				if ( state.types.length ) { params.set( 'type', state.types.join( ',' ) ); } else { params.delete( 'type' ); }
				if ( state.sort !== 'saving' ) { params.set( 'sort', state.sort ); } else { params.delete( 'sort' ); }
				if ( state.search ) { params.set( 'q', state.search ); } else { params.delete( 'q' ); }

				var qs = params.toString();
				window.history.replaceState( null, '', window.location.pathname + ( qs ? '?' + qs : '' ) );
			}

			function readUrl() {
				var params = new URLSearchParams( window.location.search );

				if ( params.get( 'cat' ) ) {
					state.cat = params.get( 'cat' );
				}
				if ( params.get( 'type' ) ) {
					state.types = params.get( 'type' ).split( ',' ).filter( Boolean );
				}
				if ( params.get( 'sort' ) && comparators[ params.get( 'sort' ) ] ) {
					state.sort = params.get( 'sort' );
					if ( sortSel ) {
						sortSel.value = state.sort;
					}
				}
				if ( params.get( 'q' ) ) {
					state.search = params.get( 'q' ).toLowerCase();
					if ( searchEl ) {
						searchEl.value = params.get( 'q' );
					}
				}

				each( root, '[data-zd-cat]', function ( b ) {
					var on = b.getAttribute( 'data-zd-cat' ) === state.cat;
					b.classList.toggle( 'is-on', on );
					b.setAttribute( 'aria-pressed', String( on ) );
				} );
				each( root, '[data-zd-type]', function ( b ) {
					var on = state.types.indexOf( b.getAttribute( 'data-zd-type' ) ) !== -1;
					b.classList.toggle( 'is-on', on );
					b.setAttribute( 'aria-pressed', String( on ) );
				} );
			}

			/* ---- category chips: single select ---- */
			each( root, '[data-zd-cat]', function ( btn ) {
				btn.addEventListener( 'click', function () {
					state.cat   = btn.getAttribute( 'data-zd-cat' );
					state.shown = pageSize;
					each( root, '[data-zd-cat]', function ( b ) {
						var on = b === btn;
						b.classList.toggle( 'is-on', on );
						b.setAttribute( 'aria-pressed', String( on ) );
					} );
					apply();
				} );
			} );

			/* ---- offer-type chips: multi select ---- */
			each( root, '[data-zd-type]', function ( btn ) {
				btn.addEventListener( 'click', function () {
					var key = btn.getAttribute( 'data-zd-type' );
					var at  = state.types.indexOf( key );
					if ( at === -1 ) {
						state.types.push( key );
					} else {
						state.types.splice( at, 1 );
					}
					var on = at === -1;
					btn.classList.toggle( 'is-on', on );
					btn.setAttribute( 'aria-pressed', String( on ) );
					state.shown = pageSize;
					apply();
				} );
			} );

			if ( sortSel ) {
				sortSel.addEventListener( 'change', function () {
					state.sort = sortSel.value;
					apply();
				} );
			}

			if ( searchEl ) {
				var timer = null;
				searchEl.addEventListener( 'input', function () {
					window.clearTimeout( timer );
					timer = window.setTimeout( function () {
						state.search = searchEl.value.trim().toLowerCase();
						state.shown  = pageSize;
						apply();
					}, 160 );
				} );
			}

			if ( moreBtn ) {
				moreBtn.addEventListener( 'click', function () {
					state.shown += pageSize;
					apply();
				} );
			}

			each( root, '[data-zd-clear]', function ( btn ) {
				btn.addEventListener( 'click', function () {
					state.cat    = 'all';
					state.types  = [];
					state.search = '';
					state.shown  = pageSize;
					if ( searchEl ) {
						searchEl.value = '';
					}
					each( root, '[data-zd-cat]', function ( b ) {
						var on = b.getAttribute( 'data-zd-cat' ) === 'all';
						b.classList.toggle( 'is-on', on );
						b.setAttribute( 'aria-pressed', String( on ) );
					} );
					each( root, '[data-zd-type]', function ( b ) {
						b.classList.remove( 'is-on' );
						b.setAttribute( 'aria-pressed', 'false' );
					} );
					apply();
				} );
			} );

			/* ---- compare tray ---- */
			if ( tray ) {
				var count = tray.querySelector( '[data-zd-traycount]' );
				var go    = tray.querySelector( '[data-zd-traygo]' );
				var clear = tray.querySelector( '[data-zd-trayclear]' );
				var base  = root.getAttribute( 'data-zd-compare-url' ) || '';

				var refresh = function () {
					var picked = [].slice.call( root.querySelectorAll( '[data-zd-pick]:checked' ) )
						.map( function ( i ) { return i.value; } );

					tray.hidden = picked.length < 2;

					if ( count ) {
						count.textContent = String( picked.length );
					}
					if ( go ) {
						go.href = base
							? base + ( base.indexOf( '?' ) === -1 ? '?' : '&' ) + 'deals=' + picked.join( ',' )
							: '#';
					}
				};

				each( root, '[data-zd-pick]', function ( box ) {
					box.addEventListener( 'change', refresh );
				} );

				if ( clear ) {
					clear.addEventListener( 'click', function () {
						each( root, '[data-zd-pick]', function ( b ) { b.checked = false; } );
						refresh();
					} );
				}
			}

			readUrl();
			apply();
		} );
	}

	/* ------------------------------------------------- scrollbar gutter */

	/*
	 * Full-bleed sections are sized from 100vw, which includes the scrollbar
	 * gutter - so without this the page scrolls sideways by the gutter width.
	 * Published once on the root so every widget can subtract it.
	 */
	function syncScrollbarWidth() {
		var gutter = window.innerWidth - document.documentElement.clientWidth;
		document.documentElement.style.setProperty( '--zd-sbw', Math.max( 0, gutter ) + 'px' );
	}

	function init( scope ) {
		scope = scope || document;
		initReveals( scope );
		initValues( scope );
		initTilt( scope );
		initMagnetic( scope );
		initParallax( scope );
		initDepthParallax( scope );
		initMarquee( scope );
		initNavbar( scope );
		initTabs( scope );
		initRails( scope );
		initOfferBars( scope );
		initCoupons( scope );
		initIndex( scope );
		syncScrollbarWidth();
	}

	var sbwTimer = null;
	window.addEventListener( 'resize', function () {
		window.clearTimeout( sbwTimer );
		sbwTimer = window.setTimeout( syncScrollbarWidth, 120 );
	} );

	window.ZlaarkDeals = { init: init };

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', function () {
			init( document );
		} );
	} else {
		init( document );
	}

	/*
	 * Elementor re-renders widgets in the editor and lazy-mounts them on the
	 * front end. This used to hook a hand-written list of widget names, which
	 * silently went stale the moment new widgets were added - those widgets
	 * mounted with their reveals still at opacity:0 and looked broken in the
	 * editor. 'element_ready/global' fires for every element, so nothing can
	 * be forgotten.
	 */
	var elementorHooked = false;

	function hookElementor() {
		if ( elementorHooked ) {
			return true;
		}
		if ( ! window.elementorFrontend || ! window.elementorFrontend.hooks ) {
			return false;
		}

		elementorHooked = true;

		window.elementorFrontend.hooks.addAction(
			'frontend/element_ready/global',
			function ( $scope ) {
				var el = ( $scope && $scope[ 0 ] ) ? $scope[ 0 ] : null;
				if ( ! el || ! el.querySelector ) {
					return;
				}
				// Cheap bail-out: this runs for third-party widgets too.
				if ( ! el.querySelector( '[class*="zd-"]' ) ) {
					return;
				}
				init( el );
			}
		);

		return true;
	}

	/*
	 * Elementor announces itself with jQuery( window ).trigger(...), and a
	 * jQuery trigger of a custom type never reaches addEventListener - jQuery
	 * dispatches to its own handler list, not to a real DOM event. Listening
	 * natively meant this hook simply never ran: in the editor a widget kept
	 * its reveal styles at opacity:0 after every control change and looked
	 * like it had failed to load. Bind through jQuery, which is always present
	 * wherever elementorFrontend is.
	 */
	if ( ! hookElementor() ) {
		if ( window.jQuery ) {
			window.jQuery( window ).on( 'elementor/frontend/init', hookElementor );
		}
		// Harmless if Elementor ever dispatches a native event as well.
		window.addEventListener( 'elementor/frontend/init', hookElementor );
	}

} )();
