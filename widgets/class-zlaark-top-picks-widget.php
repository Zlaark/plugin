<?php
/**
 * Zlaark Top Picks — ranked "editor's choice" cards with a rotating medal,
 * an animated rating ring, a staggered highlights checklist and a featured
 * card that floats above the rest.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Border;

class Zlaark_Top_Picks_Widget extends Zlaark_Query_Widget_Base {

	public function get_name() {
		return 'zlaark_top_picks';
	}

	public function get_title() {
		return __( 'Zlaark Top Picks', 'zlaark-deals-pro' );
	}

	public function get_icon() {
		return 'eicon-review';
	}

	public function get_keywords() {
		return array( 'top picks', 'best', 'ranked', 'winner', 'editors choice', 'zlaark' );
	}

	protected function register_controls() {
		$this->query_controls( 4 );
		$this->layout_controls();
		$this->style_controls();
		$this->animation_controls();
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
				'default'     => __( 'Our top picks this month', 'zlaark-deals-pro' ),
				'label_block' => true,
				'description' => __( 'Leave empty to use the selected category name.', 'zlaark-deals-pro' ),
			)
		);

		$this->add_control(
			'subheading',
			array(
				'label'       => __( 'Sub-heading', 'zlaark-deals-pro' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 2,
				'default'     => __( 'Every pick is bought, tested and scored by our team — no pay-to-play placements.', 'zlaark-deals-pro' ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'feature_first',
			array(
				'label'        => __( 'Spotlight the First Pick', 'zlaark-deals-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'separator'    => 'before',
				'description'  => __( 'The #1 card sits raised, glowing and slightly larger than the rest.', 'zlaark-deals-pro' ),
			)
		);

		$this->add_control(
			'show_rank',
			array(
				'label'        => __( 'Show Rank Medal', 'zlaark-deals-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'show_rating',
			array(
				'label'        => __( 'Show Rating Ring', 'zlaark-deals-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'show_highlights',
			array(
				'label'        => __( 'Show Highlights', 'zlaark-deals-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'highlight_limit',
			array(
				'label'     => __( 'Max Highlights', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 1,
				'max'       => 12,
				'default'   => 4,
				'condition' => array( 'show_highlights' => 'yes' ),
			)
		);

		$this->add_control(
			'nofollow',
			array(
				'label'        => __( 'Add rel="nofollow sponsored"', 'zlaark-deals-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'separator'    => 'before',
			)
		);

		$this->add_responsive_control(
			'columns',
			array(
				'label'          => __( 'Columns', 'zlaark-deals-pro' ),
				'type'           => Controls_Manager::SELECT,
				'default'        => '4',
				'tablet_default' => '2',
				'mobile_default' => '1',
				'separator'      => 'before',
				'options'        => array(
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
				),
				'selectors'      => array(
					'{{WRAPPER}} .zd-picks__grid' => 'grid-template-columns: repeat({{VALUE}}, minmax(0, 1fr));',
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
				'default'    => array( 'unit' => 'px', 'size' => 22 ),
				'selectors'  => array( '{{WRAPPER}} .zd-picks__grid' => 'gap: {{SIZE}}{{UNIT}};' ),
			)
		);

		$this->max_width_control( '{{WRAPPER}} .zd-picks' );

		$this->end_controls_section();
	}

	private function style_controls() {
		$this->start_controls_section(
			'section_style',
			array(
				'label' => __( 'Card', 'zlaark-deals-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'accent',
			array(
				'label'     => __( 'Accent Color', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#6366f1',
				'selectors' => array( '{{WRAPPER}} .zd-picks' => '--zd-accent: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'accent_2',
			array(
				'label'     => __( 'Accent Color 2', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#22d3ee',
				'selectors' => array( '{{WRAPPER}} .zd-picks' => '--zd-accent-2: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'card_bg',
			array(
				'label'     => __( 'Background', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'separator' => 'before',
				'selectors' => array( '{{WRAPPER}} .zd-pick' => 'background-color: {{VALUE}};' ),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'card_border',
				'selector' => '{{WRAPPER}} .zd-pick',
			)
		);

		$this->add_control(
			'card_radius',
			array(
				'label'      => __( 'Border Radius', 'zlaark-deals-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'default'    => array(
					'top'      => '22',
					'right'    => '22',
					'bottom'   => '22',
					'left'     => '22',
					'unit'     => 'px',
					'isLinked' => true,
				),
				'selectors'  => array(
					'{{WRAPPER}} .zd-pick' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'card_padding',
			array(
				'label'      => __( 'Padding', 'zlaark-deals-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'default'    => array(
					'top'      => '30',
					'right'    => '24',
					'bottom'   => '24',
					'left'     => '24',
					'unit'     => 'px',
					'isLinked' => false,
				),
				'selectors'  => array(
					'{{WRAPPER}} .zd-pick' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'card_shadow',
				'selector' => '{{WRAPPER}} .zd-pick',
			)
		);

		$this->add_control(
			'title_color',
			array(
				'label'     => __( 'Title Color', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#0b1120',
				'separator' => 'before',
				'selectors' => array( '{{WRAPPER}} .zd-pick__title' => 'color: {{VALUE}};' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .zd-pick__title',
			)
		);

		$this->add_control(
			'price_color',
			array(
				'label'     => __( 'Price Color', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#0b1120',
				'selectors' => array( '{{WRAPPER}} .zd-pick__price' => 'color: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'text_color',
			array(
				'label'     => __( 'Body Text Color', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#4b5563',
				'selectors' => array(
					'{{WRAPPER}} .zd-pick__tagline, {{WRAPPER}} .zd-pick__list' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'button_bg',
			array(
				'label'     => __( 'Button Background', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#0b1120',
				'separator' => 'before',
				'selectors' => array( '{{WRAPPER}} .zd-btn--solid' => 'background-color: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'button_color',
			array(
				'label'     => __( 'Button Text Color', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array( '{{WRAPPER}} .zd-btn--solid, {{WRAPPER}} .zd-btn--solid .zd-btn__label, {{WRAPPER}} .zd-btn--solid .zd-btn__arrow' => 'color: {{VALUE}};' ),
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
		<div class="zd-picks" data-zd-reveal-root="true" data-zd-stagger="<?php echo esc_attr( $stagger ); ?>">

			<?php if ( '' !== $heading || '' !== $s['subheading'] ) : ?>
				<div class="zd-picks__head zd-reveal" data-zd-reveal="<?php echo esc_attr( $s['reveal_effect'] ); ?>">
					<?php if ( '' !== $heading ) : ?>
						<h2 class="zd-picks__heading"><?php echo esc_html( $heading ); ?></h2>
					<?php endif; ?>
					<?php if ( '' !== $s['subheading'] ) : ?>
						<p class="zd-picks__sub"><?php echo esc_html( $s['subheading'] ); ?></p>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<div class="zd-picks__grid">
				<?php
				while ( $query->have_posts() ) {
					$query->the_post();
					$this->render_pick( Zlaark_Deals_Meta::get_deal_data( get_post() ), $s, $index );
					$index++;
				}
				?>
			</div>
		</div>
		<?php
		wp_reset_postdata();
	}

	private function render_pick( $deal, $s, $index ) {
		if ( empty( $deal ) ) {
			return;
		}

		$classes = array_merge( array( 'zd-pick', 'zd-reveal' ), $this->motion_classes( $s ) );
		if ( 0 === $index && 'yes' === $s['feature_first'] ) {
			$classes[] = 'zd-pick--featured';
		}

		$rank_label = '' !== $deal['rank_label'] ? $deal['rank_label'] : '';
		$highlights = 'yes' === $s['show_highlights']
			? array_slice( $deal['highlights'], 0, max( 1, (int) $s['highlight_limit'] ) )
			: array();
		?>
		<article class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>"
			data-zd-reveal="<?php echo esc_attr( $s['reveal_effect'] ); ?>"
			style="--zd-i:<?php echo (int) $index; ?>">

			<span class="zd-pick__glow" aria-hidden="true"></span>
			<span class="zd-card__shine" aria-hidden="true"></span>

			<?php if ( 'yes' === $s['show_rank'] ) : ?>
				<span class="zd-pick__medal" aria-hidden="true">
					<span class="zd-pick__medal-ring"></span>
					<span class="zd-pick__medal-num"><?php echo (int) ( $index + 1 ); ?></span>
				</span>
			<?php endif; ?>

			<header class="zd-pick__head">
				<?php if ( '' !== $rank_label ) : ?>
					<p class="zd-pick__rank-label"><?php echo esc_html( $rank_label ); ?></p>
				<?php endif; ?>

				<?php if ( $deal['image_id'] ) : ?>
					<div class="zd-pick__media">
						<?php
						echo wp_get_attachment_image(
							$deal['image_id'],
							'medium',
							false,
							array( 'class' => 'zd-pick__image', 'loading' => 'lazy' )
						);
						?>
					</div>
				<?php endif; ?>

				<h3 class="zd-pick__title"><?php echo esc_html( $deal['title'] ); ?></h3>

				<?php if ( '' !== $deal['tagline'] ) : ?>
					<p class="zd-pick__tagline"><?php echo esc_html( $deal['tagline'] ); ?></p>
				<?php endif; ?>
			</header>

			<?php if ( 'yes' === $s['show_rating'] && null !== $deal['rating'] ) : ?>
				<div class="zd-pick__score">
					<?php $this->render_rating_ring( $deal['rating'], 68 ); ?>
					<span class="zd-pick__score-label"><?php esc_html_e( 'Our score', 'zlaark-deals-pro' ); ?></span>
				</div>
			<?php endif; ?>

			<?php if ( '' !== $deal['price'] || '' !== $deal['old_price'] ) : ?>
				<p class="zd-pick__pricing">
					<?php if ( '' !== $deal['price'] ) : ?>
						<span class="zd-pick__price"><?php echo esc_html( $deal['price'] ); ?></span>
					<?php endif; ?>
					<?php if ( '' !== $deal['old_price'] ) : ?>
						<s class="zd-pick__old-price"><?php echo esc_html( $deal['old_price'] ); ?></s>
					<?php endif; ?>
				</p>
			<?php endif; ?>

			<?php if ( ! empty( $highlights ) ) : ?>
				<ul class="zd-pick__list">
					<?php foreach ( $highlights as $h => $item ) : ?>
						<li style="--zd-i:<?php echo (int) $h; ?>">
							<span class="zd-tick" aria-hidden="true">
								<svg viewBox="0 0 16 16" width="12" height="12" fill="none">
									<path d="M3 8.5l3.2 3.2L13 5" stroke="currentColor" stroke-width="2.2"
										stroke-linecap="round" stroke-linejoin="round" />
								</svg>
							</span>
							<?php echo esc_html( $item ); ?>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>

			<footer class="zd-pick__foot">
				<?php $this->render_cta( $deal, $s ); ?>
			</footer>
		</article>
		<?php
	}
}
