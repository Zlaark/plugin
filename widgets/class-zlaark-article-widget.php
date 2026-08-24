<?php
/**
 * Zlaark Article - the blog / review detail page, laid out as a report.
 *
 * Everything else in this plugin argues from measured evidence, and then the
 * article the evidence lives in was an unstyled run of paragraphs. This widget
 * gives it the same spine: a masthead, a byline that signs the work, an
 * at-a-glance instrument, a numbered contents rail, and body type set to an
 * actual reading measure.
 *
 * It renders the post content itself rather than sitting above a separate
 * Post Content widget, because the typography is most of the design and a
 * widget cannot style a sibling.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Group_Control_Typography;

class Zlaark_Article_Widget extends Zlaark_Query_Widget_Base {

	/** Words a minute, for the reading estimate. */
	const WPM = 200;

	public function get_name() {
		return 'zlaark_article';
	}

	public function get_title() {
		return __( 'Zlaark Article', 'zlaark-deals-pro' );
	}

	public function get_icon() {
		return 'eicon-post-content';
	}

	public function get_keywords() {
		return array( 'article', 'blog', 'post', 'review', 'single', 'content', 'zlaark' );
	}

	protected function register_controls() {
		$this->masthead_controls();
		$this->byline_controls();
		$this->readout_controls();
		$this->body_controls();
		$this->style_controls();
		$this->animation_controls( false );
	}

	/* --------------------------------------------------------------- masthead */

	private function masthead_controls() {
		$this->start_controls_section(
			'sec_head',
			array( 'label' => __( 'Masthead', 'zlaark-deals-pro' ) )
		);

		$this->add_control(
			'show_head',
			array(
				'label'        => __( 'Show Masthead', 'zlaark-deals-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'eyebrow',
			array(
				'label'       => __( 'Eyebrow', 'zlaark-deals-pro' ),
				'type'        => Controls_Manager::TEXT,
				'description' => __( 'Leave empty to use the post\'s first category.', 'zlaark-deals-pro' ),
				'condition'   => array( 'show_head' => 'yes' ),
			)
		);

		$this->add_control(
			'show_readtime',
			array(
				'label'        => __( 'Show Reading Time', 'zlaark-deals-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'description'  => __( 'Counted from the post itself, so it cannot go stale.', 'zlaark-deals-pro' ),
				'condition'    => array( 'show_head' => 'yes' ),
			)
		);

		$this->add_control(
			'standfirst',
			array(
				'label'       => __( 'Standfirst', 'zlaark-deals-pro' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 3,
				'description' => __( 'The line under the title. Leave empty to use the post excerpt.', 'zlaark-deals-pro' ),
				'condition'   => array( 'show_head' => 'yes' ),
			)
		);

		$this->end_controls_section();
	}

	/* ----------------------------------------------------------------- byline */

	private function byline_controls() {
		$this->start_controls_section(
			'sec_by',
			array( 'label' => __( 'Byline Strip', 'zlaark-deals-pro' ) )
		);

		$this->add_control(
			'show_by',
			array(
				'label'        => __( 'Show Byline Strip', 'zlaark-deals-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);

		/*
		 * A deal can be attached so the byline and the readout quote the same
		 * numbers the cards on the homepage quote. That is the whole point of
		 * the strip: it is the receipt for the article, not decoration, and
		 * typing the figures again by hand is how the two drift apart.
		 */
		$this->add_control(
			'deal_id',
			array(
				'label'       => __( 'Deal This Article Reviews', 'zlaark-deals-pro' ),
				'type'        => Controls_Manager::SELECT2,
				'label_block' => true,
				'options'     => ( is_admin() || $this->is_editor() ) ? self::deal_options() : array(),
				'description' => __( 'Optional. Fills the tested / re-checked / criteria facts and the score from the deal record.', 'zlaark-deals-pro' ),
			)
		);

		$this->add_control(
			'author_label',
			array(
				'label'     => __( 'Author Label', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Who', 'zlaark-deals-pro' ),
				'condition' => array( 'show_by' => 'yes' ),
			)
		);

		$this->add_control(
			'author_name',
			array(
				'label'       => __( 'Author', 'zlaark-deals-pro' ),
				'type'        => Controls_Manager::TEXT,
				'description' => __( 'Leave empty to use the deal\'s reviewer, then the post author.', 'zlaark-deals-pro' ),
				'condition'   => array( 'show_by' => 'yes' ),
			)
		);

		$fact = new Repeater();

		$fact->add_control(
			'label',
			array(
				'label'   => __( 'Label', 'zlaark-deals-pro' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Label', 'zlaark-deals-pro' ),
			)
		);

		$fact->add_control(
			'value',
			array(
				'label'   => __( 'Value', 'zlaark-deals-pro' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '',
			)
		);

		$this->add_control(
			'facts',
			array(
				'label'       => __( 'Extra Facts', 'zlaark-deals-pro' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $fact->get_controls(),
				'title_field' => '{{{ label }}}',
				'default'     => array(),
				'description' => __( 'Added after the facts read off the deal.', 'zlaark-deals-pro' ),
				'condition'   => array( 'show_by' => 'yes' ),
			)
		);

		$this->end_controls_section();
	}

	/* ---------------------------------------------------------------- readout */

	private function readout_controls() {
		$this->start_controls_section(
			'sec_readout',
			array( 'label' => __( 'At a Glance', 'zlaark-deals-pro' ) )
		);

		$this->add_control(
			'show_readout',
			array(
				'label'        => __( 'Show the Readout', 'zlaark-deals-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'readout_label',
			array(
				'label'     => __( 'Stamp', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'At a glance', 'zlaark-deals-pro' ),
				'condition' => array( 'show_readout' => 'yes' ),
			)
		);

		$this->add_control(
			'verdict',
			array(
				'label'       => __( 'Verdict', 'zlaark-deals-pro' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 3,
				'description' => __( 'One sentence. Leave empty to use the deal\'s verdict.', 'zlaark-deals-pro' ),
				'condition'   => array( 'show_readout' => 'yes' ),
			)
		);

		$this->add_control(
			'show_score',
			array(
				'label'        => __( 'Show the Score Ring', 'zlaark-deals-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'condition'    => array( 'show_readout' => 'yes' ),
			)
		);

		$row = new Repeater();

		$row->add_control(
			'label',
			array(
				'label'   => __( 'Label', 'zlaark-deals-pro' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Label', 'zlaark-deals-pro' ),
			)
		);

		$row->add_control(
			'value',
			array(
				'label'   => __( 'Value', 'zlaark-deals-pro' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '',
			)
		);

		$this->add_control(
			'readout_rows',
			array(
				'label'       => __( 'Extra Rows', 'zlaark-deals-pro' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $row->get_controls(),
				'title_field' => '{{{ label }}}',
				'default'     => array(),
				'description' => __( 'Added after price, renewal and refund window from the deal.', 'zlaark-deals-pro' ),
				'condition'   => array( 'show_readout' => 'yes' ),
			)
		);

		$this->end_controls_section();
	}

	/* ------------------------------------------------------------------- body */

	private function body_controls() {
		$this->start_controls_section(
			'sec_body',
			array( 'label' => __( 'Body', 'zlaark-deals-pro' ) )
		);

		$this->add_control(
			'show_body',
			array(
				'label'        => __( 'Render the Post Content', 'zlaark-deals-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'description'  => __( 'Turn this off only if a separate Post Content widget follows - the typography below applies to whatever this widget renders.', 'zlaark-deals-pro' ),
			)
		);

		$this->add_control(
			'show_toc',
			array(
				'label'        => __( 'Show Contents', 'zlaark-deals-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'description'  => __( 'Built from the H2s in the content, and hidden automatically when there are fewer than three.', 'zlaark-deals-pro' ),
				'condition'    => array( 'show_body' => 'yes' ),
			)
		);

		$this->add_control(
			'toc_label',
			array(
				'label'     => __( 'Contents Stamp', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Contents', 'zlaark-deals-pro' ),
				'condition' => array( 'show_toc' => 'yes', 'show_body' => 'yes' ),
			)
		);

		$this->add_responsive_control(
			'measure',
			array(
				'label'       => __( 'Reading Measure', 'zlaark-deals-pro' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( 'ch' ),
				'range'       => array( 'ch' => array( 'min' => 50, 'max' => 90 ) ),
				'default'     => array( 'unit' => 'ch', 'size' => 68 ),
				'description' => __( 'Characters per line. Long lines are the single biggest cause of readers giving up.', 'zlaark-deals-pro' ),
				'selectors'   => array( '{{WRAPPER}} .zd-art__body' => 'max-width: {{SIZE}}{{UNIT}};' ),
			)
		);

		$this->end_controls_section();
	}

	private function style_controls() {
		$this->start_controls_section(
			'sec_style',
			array(
				'label' => __( 'Colours', 'zlaark-deals-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$colours = array(
			'c_accent'  => array( __( 'Accent', 'zlaark-deals-pro' ), '--zd-accent', '#0b7a4f' ),
			'c_ink'     => array( __( 'Ink', 'zlaark-deals-pro' ), '--zd-ink', '#0a1310' ),
			'c_body'    => array( __( 'Body Text', 'zlaark-deals-pro' ), '--zd-body', '#4a5a52' ),
			'c_rule'    => array( __( 'Rules', 'zlaark-deals-pro' ), '--zd-hairline', '#dce3df' ),
		);

		foreach ( $colours as $id => $c ) {
			$this->add_control(
				$id,
				array(
					'label'     => $c[0],
					'type'      => Controls_Manager::COLOR,
					'default'   => $c[2],
					'selectors' => array( '{{WRAPPER}} .zd-art' => $c[1] . ': {{VALUE}};' ),
				)
			);
		}

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'body_type',
				'selector' => '{{WRAPPER}} .zd-art__body p',
			)
		);

		$this->end_controls_section();
	}

	/* ----------------------------------------------------------------- render */

	/** The attached deal, or an empty array. */
	private function deal( $s ) {
		$id = ! empty( $s['deal_id'] ) ? (int) $s['deal_id'] : 0;
		if ( ! $id || ZLAARK_DEALS_CPT !== get_post_type( $id ) ) {
			return array();
		}
		return Zlaark_Deals_Meta::get_deal_data( $id );
	}

	/**
	 * Gives every H2 an id and collects them, in one pass over the content.
	 *
	 * A contents list that links to anchors the page does not have is worse
	 * than no contents list, so the ids are written here rather than assumed
	 * to exist - most editors never add them.
	 *
	 * @return array [ html, entries ]
	 */
	private function anchor_headings( $html ) {
		$entries = array();
		$seen    = array();

		$out = preg_replace_callback(
			'#<h2\b([^>]*)>(.*?)</h2>#is',
			function ( $m ) use ( &$entries, &$seen ) {
				$attrs = $m[1];
				$text  = trim( wp_strip_all_tags( $m[2] ) );

				if ( '' === $text ) {
					return $m[0];
				}

				if ( preg_match( '#\bid=["\']([^"\']+)["\']#i', $attrs, $has ) ) {
					$id = $has[1];
				} else {
					$id = sanitize_title( $text );
					if ( '' === $id ) {
						$id = 'section';
					}
					// Two headings with the same words would otherwise share an
					// anchor, and the second one becomes unreachable.
					$n = 1;
					$base = $id;
					while ( isset( $seen[ $id ] ) ) {
						$n++;
						$id = $base . '-' . $n;
					}
					$attrs .= ' id="' . esc_attr( $id ) . '"';
				}

				$seen[ $id ] = true;
				$entries[]   = array( 'id' => $id, 'text' => $text );

				return '<h2' . $attrs . '>' . $m[2] . '</h2>';
			},
			$html
		);

		// preg_replace_callback returns null if the content blows the backtrack
		// limit; keep the original rather than printing an empty article.
		if ( null === $out ) {
			return array( $html, array() );
		}

		return array( $out, $entries );
	}

	protected function render() {
		$s    = $this->get_settings_for_display();
		$deal = $this->deal( $s );
		$post = get_post();

		if ( ! $post ) {
			if ( $this->is_editor() ) {
				$this->render_empty_notice();
			}
			return;
		}

		$content = '';
		$toc     = array();

		if ( 'yes' === $s['show_body'] ) {
			$content = apply_filters( 'the_content', $post->post_content );
			list( $content, $toc ) = $this->anchor_headings( $content );
		}

		// Under three headings a contents list is a list of the whole article.
		$show_toc = ( 'yes' === $s['show_toc'] ) && count( $toc ) >= 3;

		?>
		<article class="zd-art<?php echo $show_toc ? ' zd-art--toc' : ''; ?>"
			data-zd-reveal-root="true" data-zd-stagger="50">

			<?php if ( 'yes' === $s['show_head'] ) : ?>
				<?php $this->render_masthead( $s, $post, $deal ); ?>
			<?php endif; ?>

			<?php if ( 'yes' === $s['show_readout'] ) : ?>
				<?php $this->render_readout( $s, $deal ); ?>
			<?php endif; ?>

			<?php if ( '' !== $content ) : ?>
				<div class="zd-art__grid">
					<div class="zd-art__body"><?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- already through the_content. ?></div>
					<?php if ( $show_toc ) : ?>
						<?php $this->render_toc( $s, $toc ); ?>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</article>
		<?php
	}

	private function render_masthead( $s, $post, $deal ) {
		$eyebrow = trim( (string) $s['eyebrow'] );

		if ( '' === $eyebrow ) {
			$terms = get_the_terms( $post, 'category' );
			if ( $terms && ! is_wp_error( $terms ) ) {
				$eyebrow = $terms[0]->name;
			}
		}

		$stand = trim( (string) $s['standfirst'] );
		if ( '' === $stand ) {
			$stand = trim( wp_strip_all_tags( get_the_excerpt( $post ) ) );
		}

		$minutes = 0;
		if ( 'yes' === $s['show_readtime'] ) {
			$words   = str_word_count( wp_strip_all_tags( $post->post_content ) );
			$minutes = max( 1, (int) round( $words / self::WPM ) );
		}
		?>
		<header class="zd-art__head zd-reveal" data-zd-reveal="rise">
			<?php if ( '' !== $eyebrow || $minutes ) : ?>
				<p class="zd-art__eyebrow">
					<?php if ( '' !== $eyebrow ) : ?>
						<span><?php echo esc_html( $eyebrow ); ?></span>
					<?php endif; ?>
					<?php if ( $minutes ) : ?>
						<i>
							<?php
							printf(
								/* translators: %d: reading time in minutes. */
								esc_html( _n( '%d min read', '%d min read', $minutes, 'zlaark-deals-pro' ) ),
								(int) $minutes
							);
							?>
						</i>
					<?php endif; ?>
				</p>
			<?php endif; ?>

			<h1 class="zd-art__title"><?php echo esc_html( get_the_title( $post ) ); ?></h1>

			<?php if ( '' !== $stand ) : ?>
				<p class="zd-art__stand"><?php echo esc_html( $stand ); ?></p>
			<?php endif; ?>

			<?php if ( 'yes' === $s['show_by'] ) : ?>
				<?php $this->render_byline( $s, $post, $deal ); ?>
			<?php endif; ?>
		</header>
		<?php
	}

	/**
	 * The signed byline: who did the work and when, as a ruled row of stamps.
	 *
	 * Everything here is a stored fact. A byline that cannot name anybody is
	 * not printed at all, because "Tested March 2026" with no signature is a
	 * claim the site is unwilling to put its name to.
	 */
	private function render_byline( $s, $post, $deal ) {
		$name = trim( (string) $s['author_name'] );

		if ( '' === $name && ! empty( $deal['reviewer'] ) ) {
			$name = $deal['reviewer'];
		}
		if ( '' === $name ) {
			$name = get_the_author_meta( 'display_name', (int) $post->post_author );
		}
		if ( '' === $name ) {
			return;
		}

		$facts = array(
			array( 'label' => (string) $s['author_label'], 'value' => $name ),
		);

		if ( ! empty( $deal['tested_date'] ) ) {
			$facts[] = array(
				'label' => __( 'Tested', 'zlaark-deals-pro' ),
				'value' => date_i18n( 'F Y', (int) strtotime( $deal['tested_date'] ) ),
			);
		}

		if ( ! empty( $deal['last_verified'] ) ) {
			$facts[] = array(
				'label' => __( 'Re-checked', 'zlaark-deals-pro' ),
				'value' => date_i18n( 'j M Y', (int) strtotime( $deal['last_verified'] ) ),
			);
		}

		if ( ! empty( $deal['scores'] ) ) {
			$facts[] = array(
				'label' => __( 'Measured on', 'zlaark-deals-pro' ),
				'value' => sprintf(
					/* translators: %d: number of scored criteria. */
					_n( '%d criterion', '%d criteria', count( $deal['scores'] ), 'zlaark-deals-pro' ),
					count( $deal['scores'] )
				),
			);
		}

		foreach ( ( is_array( $s['facts'] ) ? $s['facts'] : array() ) as $extra ) {
			if ( '' === trim( (string) $extra['value'] ) ) {
				continue;
			}
			$facts[] = array( 'label' => $extra['label'], 'value' => $extra['value'] );
		}
		?>
		<div class="zd-art__by">
			<?php foreach ( $facts as $fact ) : ?>
				<div class="zd-art__fact">
					<b><?php echo esc_html( $fact['label'] ); ?></b>
					<span><?php echo esc_html( $fact['value'] ); ?></span>
				</div>
			<?php endforeach; ?>
		</div>
		<?php
	}

	/** The instrument: verdict, score, and the numbers a reader checks first. */
	private function render_readout( $s, $deal ) {
		$verdict = trim( (string) $s['verdict'] );
		if ( '' === $verdict && ! empty( $deal['verdict'] ) ) {
			$verdict = $deal['verdict'];
		}

		$rows = array();

		if ( ! empty( $deal['price'] ) ) {
			$rows[] = array( __( 'Price', 'zlaark-deals-pro' ), $deal['price'] );
		}
		if ( ! empty( $deal['renewal_price'] ) ) {
			$rows[] = array( __( 'Renews at', 'zlaark-deals-pro' ), $deal['renewal_price'] );
		}
		if ( ! empty( $deal['refund_window'] ) ) {
			$rows[] = array( __( 'Refund', 'zlaark-deals-pro' ), $deal['refund_window'] );
		}

		foreach ( ( is_array( $s['readout_rows'] ) ? $s['readout_rows'] : array() ) as $row ) {
			if ( '' === trim( (string) $row['value'] ) ) {
				continue;
			}
			$rows[] = array( $row['label'], $row['value'] );
		}

		$score = ( 'yes' === $s['show_score'] && isset( $deal['overall_score'] ) )
			? $deal['overall_score']
			: null;

		// An empty instrument is worse than none: it promises a summary and
		// then shows an outline with nothing in it.
		if ( '' === $verdict && empty( $rows ) && null === $score ) {
			return;
		}
		?>
		<div class="zd-art__readout zd-reveal" data-zd-reveal="rise">
			<div class="zd-art__rohead">
				<b><?php echo esc_html( $s['readout_label'] ); ?></b>
				<?php if ( ! empty( $deal['title'] ) ) : ?>
					<span><?php echo esc_html( $deal['title'] ); ?></span>
				<?php endif; ?>
			</div>

			<div class="zd-art__robody">
				<?php if ( '' !== $verdict ) : ?>
					<p class="zd-art__verdict"><?php echo esc_html( $verdict ); ?></p>
				<?php endif; ?>

				<?php if ( null !== $score ) : ?>
					<div class="zd-art__score"><?php $this->render_rating_ring( $score, 76 ); ?></div>
				<?php endif; ?>
			</div>

			<?php if ( ! empty( $rows ) ) : ?>
				<dl class="zd-art__rogrid">
					<?php foreach ( $rows as $row ) : ?>
						<div>
							<dt><?php echo esc_html( $row[0] ); ?></dt>
							<dd><?php echo esc_html( $row[1] ); ?></dd>
						</div>
					<?php endforeach; ?>
				</dl>
			<?php endif; ?>
		</div>
		<?php
	}

	private function render_toc( $s, $entries ) {
		?>
		<aside class="zd-art__toc" aria-label="<?php esc_attr_e( 'Contents', 'zlaark-deals-pro' ); ?>">
			<div class="zd-art__tocinner">
				<p class="zd-art__toclabel"><?php echo esc_html( $s['toc_label'] ); ?></p>
				<ol class="zd-art__toclist">
					<?php foreach ( $entries as $i => $entry ) : ?>
						<li>
							<a href="#<?php echo esc_attr( $entry['id'] ); ?>">
								<i><?php echo esc_html( str_pad( $i + 1, 2, '0', STR_PAD_LEFT ) ); ?></i>
								<span><?php echo esc_html( $entry['text'] ); ?></span>
							</a>
						</li>
					<?php endforeach; ?>
				</ol>
			</div>
		</aside>
		<?php
	}
}
