<?php
/**
 * Zlaark Comparison - a scorecard table built from each deal's "Score
 * Breakdown" field, with bars that fill on scroll and rows that highlight
 * as the pointer travels down the table.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;

class Zlaark_Compare_Widget extends Zlaark_Query_Widget_Base {

	public function get_name() {
		return 'zlaark_compare';
	}

	public function get_title() {
		return __( 'Zlaark Comparison', 'zlaark-deals-pro' );
	}

	public function get_icon() {
		return 'eicon-table';
	}

	public function get_keywords() {
		return array( 'compare', 'comparison', 'table', 'scores', 'rating', 'zlaark' );
	}

	protected function register_controls() {
		$this->query_controls( 4 );

		$this->start_controls_section(
			'section_compare_source',
			array(
				'label' => __( 'Comparison Page', 'zlaark-deals-pro' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'accept_url_ids',
			array(
				'label'        => __( 'Accept Deals From The Compare Tray', 'zlaark-deals-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '',
				'return_value' => 'yes',
				'description'  => __( 'Turn this on for the page the Deals Index compare tray links to. When the URL carries ?deals=12,34 those deals are shown, in the order they were ticked, and the category filter above is ignored.', 'zlaark-deals-pro' ),
			)
		);

		$this->end_controls_section();
		$this->layout_controls();
		$this->style_controls();
		$this->animation_controls( false );
	}

	private function layout_controls() {
		$this->start_controls_section(
			'section_layout',
			array( 'label' => __( 'Layout', 'zlaark-deals-pro' ) )
		);

		$this->add_control(
			'heading',
			array(
				'label'       => __( 'Heading', 'zlaark-deals-pro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Side-by-side scorecard', 'zlaark-deals-pro' ),
				'label_block' => true,
				'description' => __( 'Leave empty to use the selected category name.', 'zlaark-deals-pro' ),
			)
		);

		$this->add_control(
			'style_mode',
			array(
				'label'   => __( 'Style', 'zlaark-deals-pro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'cards',
				'options' => array(
					'cards' => __( 'Score cards (one per deal)', 'zlaark-deals-pro' ),
					'rows'  => __( 'Compact rows', 'zlaark-deals-pro' ),
				),
			)
		);

		$this->add_control(
			'show_overall',
			array(
				'label'        => __( 'Show Overall Rating', 'zlaark-deals-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'show_button',
			array(
				'label'        => __( 'Show Button', 'zlaark-deals-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'nofollow',
			array(
				'label'        => __( 'Add rel="nofollow sponsored"', 'zlaark-deals-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'condition'    => array( 'show_button' => 'yes' ),
			)
		);

		$this->add_responsive_control(
			'columns',
			array(
				'label'          => __( 'Columns', 'zlaark-deals-pro' ),
				'type'           => Controls_Manager::SELECT,
				'default'        => '3',
				'tablet_default' => '2',
				'mobile_default' => '1',
				'separator'      => 'before',
				'condition'      => array( 'style_mode' => 'cards' ),
				'options'        => array(
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
				),
				'selectors'      => array(
					'{{WRAPPER}} .zd-compare__grid' => 'grid-template-columns: repeat({{VALUE}}, minmax(0, 1fr));',
				),
			)
		);

		$this->add_responsive_control(
			'gap',
			array(
				'label'      => __( 'Gap', 'zlaark-deals-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 60 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 20 ),
				'selectors'  => array( '{{WRAPPER}} .zd-compare__grid' => 'gap: {{SIZE}}{{UNIT}};' ),
			)
		);

		$this->max_width_control( '{{WRAPPER}} .zd-compare' );

		$this->end_controls_section();
	}

	private function style_controls() {
		$this->start_controls_section(
			'section_style',
			array(
				'label' => __( 'Style', 'zlaark-deals-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'accent',
			array(
				'label'     => __( 'Bar Color', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#0b7a4f',
				'selectors' => array( '{{WRAPPER}} .zd-compare' => '--zd-accent: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'accent_2',
			array(
				'label'     => __( 'Bar Color 2', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#22d3ee',
				'selectors' => array( '{{WRAPPER}} .zd-compare' => '--zd-accent-2: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'track_color',
			array(
				'label'     => __( 'Bar Track Color', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ebf0ed',
				'selectors' => array( '{{WRAPPER}} .zd-compare' => '--zd-track: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'panel_bg',
			array(
				'label'     => __( 'Panel Background', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'separator' => 'before',
				'selectors' => array( '{{WRAPPER}} .zd-compare__col, {{WRAPPER}} .zd-compare__row' => 'background-color: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'panel_radius',
			array(
				'label'      => __( 'Panel Radius', 'zlaark-deals-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 20 ),
				'selectors'  => array(
					'{{WRAPPER}} .zd-compare__col, {{WRAPPER}} .zd-compare__row' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'panel_shadow',
				'selector' => '{{WRAPPER}} .zd-compare__col, {{WRAPPER}} .zd-compare__row',
			)
		);

		$this->add_control(
			'title_color',
			array(
				'label'     => __( 'Title Color', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#0a1310',
				'separator' => 'before',
				'selectors' => array( '{{WRAPPER}} .zd-compare__name' => 'color: {{VALUE}};' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .zd-compare__name',
			)
		);

		$this->add_control(
			'label_color',
			array(
				'label'     => __( 'Score Label Color', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#5e6c64',
				'selectors' => array( '{{WRAPPER}} .zd-score__label' => 'color: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'score_colour',
			array(
				'label'        => __( 'Colour Scores By Value', 'zlaark-deals-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'description'  => __( 'Strong scores read green, middling amber, weak red - so the 6 among the 9s is findable at a glance. Turn off to colour every value the same.', 'zlaark-deals-pro' ),
			)
		);

		/*
		 * Only offered when the semantic ramp is off. Elementor's selector wins
		 * over the band class, so leaving both live would silently repaint
		 * every score one colour and make the switch above do nothing.
		 */
		$this->add_control(
			'value_color',
			array(
				'label'     => __( 'Score Value Color', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#0a1310',
				'condition' => array( 'score_colour!' => 'yes' ),
				'selectors' => array( '{{WRAPPER}} .zd-score__value' => 'color: {{VALUE}};' ),
			)
		);

		$this->end_controls_section();
	}

	/* ----------------------------------------------------------------- render */

	protected function render() {
		$s     = $this->get_settings_for_display();
		$query = new WP_Query( $this->build_query_args( $s ) );

		if ( ! $query->have_posts() ) {
			$this->render_empty_notice();
			return;
		}

		$heading = $this->resolve_heading( $s );
		$stagger = isset( $s['reveal_stagger']['size'] ) ? (int) $s['reveal_stagger']['size'] : 90;
		$index   = 0;
		?>
		<div class="zd-compare zd-compare--<?php echo esc_attr( $s['style_mode'] ); ?>"
			data-zd-reveal-root="true" data-zd-stagger="<?php echo esc_attr( $stagger ); ?>">

			<?php if ( '' !== $heading ) : ?>
				<h2 class="zd-compare__heading zd-reveal" data-zd-reveal="<?php echo esc_attr( $s['reveal_effect'] ); ?>">
					<?php echo esc_html( $heading ); ?>
				</h2>
			<?php endif; ?>

			<div class="zd-compare__grid">
				<?php
				while ( $query->have_posts() ) {
					$query->the_post();
					$deal = Zlaark_Deals_Meta::get_deal_data( get_post() );

					if ( 'rows' === $s['style_mode'] ) {
						$this->render_row( $deal, $s, $index );
					} else {
						$this->render_column( $deal, $s, $index );
					}
					$index++;
				}
				?>
			</div>
		</div>
		<?php
		wp_reset_postdata();
	}

	/** One panel per deal, scores stacked vertically. */
	private function render_column( $deal, $s, $index ) {
		if ( empty( $deal ) ) {
			return;
		}
		?>
		<div class="zd-compare__col zd-reveal"
			data-zd-reveal="<?php echo esc_attr( $s['reveal_effect'] ); ?>"
			style="--zd-i:<?php echo (int) $index; ?>">

			<?php
			/*
			 * The verdict cap - "Best overall", "Best value" - is the device that
			 * makes the reference site's scorecard readable at a glance. It comes
			 * from the Rank Label field, so it is a stored fact, not a guess.
			 */
			?>
			<?php if ( '' !== $deal['rank_label'] ) : ?>
				<span class="zd-compare__cap<?php echo 0 === $index ? ' zd-compare__cap--first' : ''; ?>">
					<?php echo esc_html( $deal['rank_label'] ); ?>
				</span>
			<?php endif; ?>

			<div class="zd-compare__top">
				<?php if ( $deal['image_id'] ) : ?>
					<div class="zd-compare__logo">
						<?php echo wp_get_attachment_image( $deal['image_id'], 'medium', false, array( 'loading' => 'lazy' ) ); ?>
					</div>
				<?php endif; ?>

				<div class="zd-compare__ident">
					<h3 class="zd-compare__name"><?php echo esc_html( $deal['title'] ); ?></h3>
					<?php if ( '' !== $deal['price'] ) : ?>
						<p class="zd-compare__price">
							<span class="zd-compare__pricenow"><?php echo esc_html( $deal['price'] ); ?></span>
							<?php if ( '' !== $deal['old_price'] ) : ?>
								<s class="zd-compare__priceold"><?php echo esc_html( $deal['old_price'] ); ?></s>
							<?php endif; ?>
						</p>
					<?php elseif ( '' !== $deal['offer_headline'] ) : ?>
						<p class="zd-compare__price">
							<span class="zd-compare__pricenow"><?php echo esc_html( $deal['offer_headline'] ); ?></span>
						</p>
					<?php endif; ?>
					<?php if ( '' !== $deal['renewal_price'] ) : ?>
						<p class="zd-compare__renewal">
							<?php
							printf(
								/* translators: %s: renewal price. */
								esc_html__( 'Renews at %s', 'zlaark-deals-pro' ),
								esc_html( $deal['renewal_price'] )
							);
							?>
						</p>
					<?php endif; ?>
				</div>

				<?php
				// The computed overall wins over the typed rating, so the ring can
				// never disagree with the bars printed directly beneath it.
				$overall = ( null !== $deal['overall_score'] ) ? $deal['overall_score'] : $deal['rating'];
				?>
				<?php if ( 'yes' === $s['show_overall'] && null !== $overall ) : ?>
					<div class="zd-compare__overall">
						<?php $this->render_rating_ring( $overall, 58 ); ?>
					</div>
				<?php endif; ?>
			</div>

			<?php $this->render_scores( $deal['scores'], 'yes' === $s['score_colour'] ); ?>

			<?php if ( '' !== $deal['verified_label'] ) : ?>
				<p class="zd-compare__verified"><?php echo esc_html( $deal['verified_label'] ); ?></p>
			<?php endif; ?>

			<?php if ( 'yes' === $s['show_button'] ) : ?>
				<div class="zd-compare__foot">
					<?php $this->render_cta( $deal, $s ); ?>
					<?php if ( '' !== $deal['review_url'] ) : ?>
						<a class="zd-compare__review" href="<?php echo esc_url( $deal['review_url'] ); ?>">
							<?php esc_html_e( 'Full review', 'zlaark-deals-pro' ); ?>
						</a>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}

	/** Compact horizontal row - logo, name, inline bars, CTA. */
	private function render_row( $deal, $s, $index ) {
		if ( empty( $deal ) ) {
			return;
		}
		?>
		<div class="zd-compare__row zd-reveal"
			data-zd-reveal="<?php echo esc_attr( $s['reveal_effect'] ); ?>"
			style="--zd-i:<?php echo (int) $index; ?>">

			<span class="zd-compare__rank"><?php echo (int) ( $index + 1 ); ?></span>

			<?php if ( $deal['image_id'] ) : ?>
				<div class="zd-compare__logo">
					<?php echo wp_get_attachment_image( $deal['image_id'], 'medium', false, array( 'loading' => 'lazy' ) ); ?>
				</div>
			<?php endif; ?>

			<div class="zd-compare__ident">
				<h3 class="zd-compare__name"><?php echo esc_html( $deal['title'] ); ?></h3>
				<?php if ( '' !== $deal['tagline'] ) : ?>
					<p class="zd-compare__tagline"><?php echo esc_html( $deal['tagline'] ); ?></p>
				<?php endif; ?>
			</div>

			<div class="zd-compare__bars">
				<?php $this->render_scores( array_slice( $deal['scores'], 0, 3 ), 'yes' === $s['score_colour'] ); ?>
			</div>

			<?php if ( 'yes' === $s['show_overall'] && null !== $deal['rating'] ) : ?>
				<div class="zd-compare__overall">
					<?php $this->render_rating_ring( $deal['rating'], 54 ); ?>
				</div>
			<?php endif; ?>

			<?php if ( 'yes' === $s['show_button'] ) : ?>
				<div class="zd-compare__foot">
					<?php $this->render_cta( $deal, $s ); ?>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Score bars. Width is set through a custom property that the reveal
	 * observer flips on, so the fill animates the first time it is seen.
	 */
	private function render_scores( $scores, $banded = true ) {
		if ( empty( $scores ) ) {
			return;
		}
		?>
		<ul class="zd-scores">
			<?php foreach ( $scores as $i => $score ) : ?>
				<?php
				/*
				 * Every score already knows whether it is good, fair or weak -
				 * score_band() runs on each deal and the ramp is styled - but
				 * this column used to print a flat colour and throw the band
				 * away. A wall of identical numbers is not a comparison; the
				 * whole point is spotting the 6 among the 9s.
				 */
				$band = $banded ? Zlaark_Deals_Computed::score_band( $score['value'] ) : '';
				?>
				<li class="zd-score" style="--zd-i:<?php echo (int) $i; ?>">
					<div class="zd-score__head">
						<span class="zd-score__label"><?php echo esc_html( $score['label'] ); ?></span>
						<?php if ( null !== $score['value'] ) : ?>
							<span class="zd-score__value<?php echo '' !== $band ? ' zd-score--' . esc_attr( $band ) : ''; ?>"
								data-zd-count="<?php echo esc_attr( $score['value'] ); ?>"
								data-zd-decimals="1"><?php echo esc_html( number_format_i18n( $score['value'], 1 ) ); ?></span>
						<?php endif; ?>
					</div>
					<?php if ( null !== $score['value'] ) : ?>
						<span class="zd-score__track">
							<span class="zd-score__bar<?php echo '' !== $band ? ' zd-fill--' . esc_attr( $band ) : ''; ?>"
								data-zd-bar="<?php echo esc_attr( $score['value'] * 10 ); ?>"
								style="--zd-bar:<?php echo esc_attr( max( 0, min( 100, $score['value'] * 10 ) ) ); ?>%"></span>
						</span>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ul>
		<?php
	}
}
