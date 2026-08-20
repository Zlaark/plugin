/* =========================================================================
   Zlaark Deals — motion runtime
   Scroll reveals, count-ups, rating rings, score bars, cursor tilt,
   magnetic buttons, hero parallax and the category filter tabs.
   Everything is idempotent so Elementor can re-render a widget safely.
   ========================================================================= */
( function () {
	'use strict';

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

	function watch( el ) {
		if ( ! once( el ) ) {
			return;
		}
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

	function fillMarquee( marquee ) {
		var viewport = marquee.querySelector( '.zd-marquee__viewport' );
		var tracks = marquee.querySelectorAll( '.zd-marquee__track' );
		if ( ! viewport || ! tracks.length ) {
			return;
		}

		var first = tracks[ 0 ];
		var originals = Array.prototype.slice.call( first.children );
		if ( ! originals.length ) {
			return;
		}

		// Remember the authored set so repeated runs don't compound clones.
		if ( ! marquee.zdOriginals ) {
			marquee.zdOriginals = originals.map( function ( node ) {
				return node.cloneNode( true );
			} );
		}

		Array.prototype.forEach.call( tracks, function ( track ) {
			track.textContent = '';
			marquee.zdOriginals.forEach( function ( node ) {
				track.appendChild( node.cloneNode( true ) );
			} );
		} );

		var guard = 0;
		while ( first.scrollWidth < viewport.clientWidth && guard < 24 ) {
			Array.prototype.forEach.call( tracks, function ( track ) {
				marquee.zdOriginals.forEach( function ( node ) {
					track.appendChild( node.cloneNode( true ) );
				} );
			} );
			guard++;
		}
	}

	function initMarquee( scope ) {
		each( scope, '.zd-marquee', function ( marquee ) {
			if ( ! once( marquee ) ) {
				return;
			}

			sizeMarquee( marquee );
			fillMarquee( marquee );

			// Logos are images, so widths aren't final until they decode.
			each( marquee, 'img', function ( img ) {
				if ( ! img.complete ) {
					img.addEventListener( 'load', function () {
						sizeMarquee( marquee );
						fillMarquee( marquee );
					}, { once: true } );
				}
			} );

			var timer = null;
			window.addEventListener( 'resize', function () {
				clearTimeout( timer );
				timer = setTimeout( function () {
					sizeMarquee( marquee );
					fillMarquee( marquee );
				}, 180 );
			} );
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
			// stylesheet media query — it's evaluated here instead.
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

				window.addEventListener( 'resize', function () {
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
				var onScroll = function () {
					nav.classList.toggle( 'is-stuck', nav.getBoundingClientRect().top <= 1 );
				};
				window.addEventListener( 'scroll', onScroll, { passive: true } );
				onScroll();
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

	function initTabs( scope ) {
		each( scope, '.zd-tabs', function ( tabs ) {
			if ( ! once( tabs ) ) {
				return;
			}

			var deals = tabs.closest( '.zd-deals' );
			var grid = deals ? deals.querySelector( '.zd-grid' ) : null;
			var buttons = tabs.querySelectorAll( '.zd-tabs__btn' );

			var active = tabs.querySelector( '.zd-tabs__btn.is-active' ) || buttons[ 0 ];
			if ( active ) {
				// Fonts can still be loading, which would size the pill wrong.
				requestAnimationFrame( function () {
					moveIndicator( tabs, active );
				} );
			}

			window.addEventListener( 'resize', function () {
				var current = tabs.querySelector( '.zd-tabs__btn.is-active' );
				if ( current ) {
					moveIndicator( tabs, current );
				}
			} );

			Array.prototype.forEach.call( buttons, function ( btn ) {
				btn.addEventListener( 'click', function () {
					Array.prototype.forEach.call( buttons, function ( b ) {
						b.classList.remove( 'is-active' );
						b.setAttribute( 'aria-selected', 'false' );
					} );
					btn.classList.add( 'is-active' );
					btn.setAttribute( 'aria-selected', 'true' );
					moveIndicator( tabs, btn );

					if ( ! grid ) {
						return;
					}

					var filter = btn.getAttribute( 'data-zd-filter' );
					each( grid, '.zd-card', function ( card ) {
						var terms = ( card.getAttribute( 'data-zd-terms' ) || '' ).split( /\s+/ );
						var show = filter === 'all' || terms.indexOf( filter ) !== -1;
						card.classList.toggle( 'is-filtered-out', ! show );
					} );
				} );
			} );
		} );
	}

	/* ----------------------------------------------------------------- boot */

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
	}

	window.ZlaarkDeals = { init: init };

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', function () {
			init( document );
		} );
	} else {
		init( document );
	}

	// Elementor re-renders widgets in the editor and lazy-mounts them on the
	// front end, so re-run against each element as it becomes ready.
	window.addEventListener( 'elementor/frontend/init', function () {
		if ( ! window.elementorFrontend || ! window.elementorFrontend.hooks ) {
			return;
		}
		[
			'zlaark_navbar',
			'zlaark_hero',
			'zlaark_hero_classic',
			'zlaark_hero_bento',
			'zlaark_about',
			'zlaark_deals',
			'zlaark_top_picks',
			'zlaark_compare',
			'zlaark_stats',
			'zlaark_marquee'
		].forEach( function ( name ) {
			window.elementorFrontend.hooks.addAction(
				'frontend/element_ready/' + name + '.default',
				function ( $scope ) {
					init( $scope && $scope[ 0 ] ? $scope[ 0 ] : document );
				}
			);
		} );
	} );
} )();
