<?php
/**
 * Zlaark Deals Grid - deal cards pulled from the CPT, filtered by category,
 * with 3D cursor tilt, a shine sweep, a rotating border glow and optional
 * animated filter tabs.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Border;

class Zlaark_Deals_Widget extends Zlaark_Query_Widget_Base {

	public function get_name() {
		return 'zlaark_deals';
	}

	public function get_title() {
		return __( 'Zlaark Deals Grid', 'zlaark-deals-pro' );
	}

	public function get_icon() {
		return 'eicon-price-list';
	}

	public function get_keywords() {
		return array( 'deals', 'offers', 'discount', 'pricing', 'coupon', 'zlaark' );
	}

	protected function register_controls() {
		$this->query_controls( 6 );
		$this->layout_controls();
		$this->style_controls();
		$this->animation_controls();
	}

	/* ---------------------------------------------------------------- layout */

	private function layout_controls() {
		$this->start_controls_section(
			'section_layout',
			array( 'label' => __( 'Layout', 'zlaark-deals-pro' ) )
		);

		$this->add_control(
			'show_heading',
			array(
				'label'        => __( 'Show Section Heading', 'zlaark-deals-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'heading',
			array(
				'label'       => __( 'Heading', 'zlaark-deals-pro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Discount Deals 2025', 'zlaark-deals-pro' ),
				'label_block' => true,
				'description' => __( 'Leave empty to use the selected category name.', 'zlaark-deals-pro' ),
				'condition'   => array( 'show_heading' => 'yes' ),
			)
		);

		$this->add_control(
			'subheading',
			array(
				'label'       => __( 'Sub-heading', 'zlaark-deals-pro' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'condition'   => array( 'show_heading' => 'yes' ),
			)
		);

		$this->add_control(
			'show_filters',
			array(
				'label'        => __( 'Animated Category Tabs', 'zlaark-deals-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '',
				'return_value' => 'yes',
				'separator'    => 'before',
				'description'  => __( 'Adds front-end filter tabs with a sliding indicator. Tabs are built from the categories of the deals shown.', 'zlaark-deals-pro' ),
			)
		);

		$this->add_control(
			'filter_all_label',
			array(
				'label'     => __( '"All" Tab Label', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'All deals', 'zlaark-deals-pro' ),
				'condition' => array( 'show_filters' => 'yes' ),
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
				'options'        => array(
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
					'6' => '6',
				),
				'selectors'      => array(
					'{{WRAPPER}} .zd-grid' => 'grid-template-columns: repeat({{VALUE}}, minmax(0, 1fr));',
				),
			)
		);

		$this->add_control(
			'auto_fit',
			array(
				'label'        => __( 'Auto-fit Columns', 'zlaark-deals-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'description'  => __( 'Ignore the fixed column count and fit as many cards per row as the width allows, never narrower than the minimum below.', 'zlaark-deals-pro' ),
			)
		);

		$this->add_responsive_control(
			'card_min',
			array(
				'label'      => __( 'Minimum Card Width', 'zlaark-deals-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 180, 'max' => 640 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 300 ),
				'condition'  => array( 'auto_fit' => 'yes' ),
				// Registered after the column rule so it wins on equal specificity.
				'selectors'  => array(
					'{{WRAPPER}} .zd-grid' => 'grid-template-columns: repeat(auto-fit, minmax({{SIZE}}{{UNIT}}, 1fr));',
				),
			)
		);

		$this->add_responsive_control(
			'gap',
			array(
				'label'      => __( 'Gap', 'zlaark-deals-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 80 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 24 ),
				'selectors'  => array( '{{WRAPPER}} .zd-grid' => 'gap: {{SIZE}}{{UNIT}};' ),
			)
		);

		$this->max_width_control( '{{WRAPPER}} .zd-deals' );

		$this->add_control(
			'card_layout',
			array(
				'label'     => __( 'Card Layout', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'row',
				'separator' => 'before',
				'options'   => array(
					'row'       => __( 'Row - logo beside content', 'zlaark-deals-pro' ),
					'stack'     => __( 'Stack - logo above content', 'zlaark-deals-pro' ),
					'panel'     => __( 'Panel - tall card, button at the base', 'zlaark-deals-pro' ),
					'split'     => __( 'Split - details left, price and CTA right', 'zlaark-deals-pro' ),
					'compact'   => __( 'Compact - dense single-line rows', 'zlaark-deals-pro' ),
					'spotlight' => __( 'Spotlight - full-bleed image with overlay text', 'zlaark-deals-pro' ),
				),
				'description' => __( 'Panel and Spotlight suit 3–4 columns; Split and Compact suit 1–2 wide rows.', 'zlaark-deals-pro' ),
			)
		);

		$this->add_control(
			'equal_height',
			array(
				'label'        => __( 'Equal Card Heights', 'zlaark-deals-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'description'  => __( 'Stretches every card in a row to the tallest, so the buttons line up.', 'zlaark-deals-pro' ),
			)
		);

		$this->add_control(
			'accent_bar',
			array(
				'label'        => __( 'Accent Bar on Card', 'zlaark-deals-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'description'  => __( 'A gradient rule that grows across the top of the card on hover.', 'zlaark-deals-pro' ),
			)
		);

		foreach ( array(
			'show_image'   => __( 'Show Image', 'zlaark-deals-pro' ),
			'show_tagline' => __( 'Show Tagline', 'zlaark-deals-pro' ),
			'show_price'   => __( 'Show Pricing', 'zlaark-deals-pro' ),
			'show_rating'  => __( 'Show Rating Ring', 'zlaark-deals-pro' ),
			'show_button'  => __( 'Show Button', 'zlaark-deals-pro' ),
		) as $key => $label ) {
			$this->add_control(
				$key,
				array(
					'label'        => $label,
					'type'         => Controls_Manager::SWITCHER,
					'default'      => 'show_rating' === $key ? '' : 'yes',
					'return_value' => 'yes',
				)
			);
		}

		$this->add_control(
			'nofollow',
			array(
				'label'        => __( 'Add rel="nofollow sponsored"', 'zlaark-deals-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'description'  => __( 'Recommended for affiliate links.', 'zlaark-deals-pro' ),
				'condition'    => array( 'show_button' => 'yes' ),
			)
		);

		$this->end_controls_section();
	}

	/* ------------------------------------------------------------------ style */

	private function style_controls() {
		/* Heading */
		$this->start_controls_section(
			'section_style_heading',
			array(
				'label'     => __( 'Heading', 'zlaark-deals-pro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'show_heading' => 'yes' ),
			)
		);

		$this->add_control(
			'heading_color',
			array(
				'label'     => __( 'Color', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#5e6c64',
				'selectors' => array( '{{WRAPPER}} .zd-section__title' => 'color: {{VALUE}};' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'heading_typography',
				'selector' => '{{WRAPPER}} .zd-section__title',
			)
		);

		$this->add_control(
			'subheading_color',
			array(
				'label'     => __( 'Sub-heading Color', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#9ca3af',
				'selectors' => array( '{{WRAPPER}} .zd-section__sub' => 'color: {{VALUE}};' ),
			)
		);

		$this->add_responsive_control(
			'heading_spacing',
			array(
				'label'      => __( 'Spacing Below', 'zlaark-deals-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 100 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 26 ),
				'selectors'  => array( '{{WRAPPER}} .zd-section__head' => 'margin-bottom: {{SIZE}}{{UNIT}};' ),
			)
		);

		$this->end_controls_section();

		/* Card */
		$this->start_controls_section(
			'section_style_card',
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
				'default'   => '#0b7a4f',
				'selectors' => array( '{{WRAPPER}} .zd-grid' => '--zd-accent: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'accent_2',
			array(
				'label'     => __( 'Accent Color 2', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#065f42',
				'selectors' => array( '{{WRAPPER}} .zd-grid' => '--zd-accent-2: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'card_bg',
			array(
				'label'     => __( 'Background', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'separator' => 'before',
				'selectors' => array( '{{WRAPPER}} .zd-card' => 'background-color: {{VALUE}};' ),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'card_border',
				'selector' => '{{WRAPPER}} .zd-card',
			)
		);

		$this->add_control(
			'card_radius',
			array(
				'label'      => __( 'Border Radius', 'zlaark-deals-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'default'    => array(
					'top'      => '20',
					'right'    => '20',
					'bottom'   => '20',
					'left'     => '20',
					'unit'     => 'px',
					'isLinked' => true,
				),
				'selectors'  => array(
					'{{WRAPPER}} .zd-card' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'top'      => '28',
					'right'    => '28',
					'bottom'   => '28',
					'left'     => '28',
					'unit'     => 'px',
					'isLinked' => true,
				),
				'selectors'  => array(
					'{{WRAPPER}} .zd-card' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'card_shadow',
				'selector' => '{{WRAPPER}} .zd-card',
			)
		);

		$this->add_control(
			'lift',
			array(
				'label'      => __( 'Hover Lift', 'zlaark-deals-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 24 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 8 ),
				'separator'  => 'before',
				'selectors'  => array( '{{WRAPPER}} .zd-grid' => '--zd-lift: -{{SIZE}}{{UNIT}};' ),
			)
		);

		$this->end_controls_section();

		/* Content */
		$this->start_controls_section(
			'section_style_content',
			array(
				'label' => __( 'Card Content', 'zlaark-deals-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'image_size',
			array(
				'label'      => __( 'Image Size', 'zlaark-deals-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 32, 'max' => 200 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 76 ),
				'condition'  => array( 'show_image' => 'yes' ),
				'selectors'  => array(
					'{{WRAPPER}} .zd-card__media' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'title_color',
			array(
				'label'     => __( 'Title Color', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#0a1310',
				'separator' => 'before',
				'selectors' => array( '{{WRAPPER}} .zd-card__title' => 'color: {{VALUE}};' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .zd-card__title',
			)
		);

		$this->add_control(
			'tagline_color',
			array(
				'label'     => __( 'Tagline Color', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#5e6c64',
				'condition' => array( 'show_tagline' => 'yes' ),
				'selectors' => array( '{{WRAPPER}} .zd-card__tagline' => 'color: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'price_color',
			array(
				'label'     => __( 'Price Color', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#0b7a4f',
				'separator' => 'before',
				'condition' => array( 'show_price' => 'yes' ),
				'selectors' => array( '{{WRAPPER}} .zd-card__price' => 'color: {{VALUE}};' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'price_typography',
				'selector'  => '{{WRAPPER}} .zd-card__price',
				'condition' => array( 'show_price' => 'yes' ),
			)
		);

		$this->add_control(
			'old_price_color',
			array(
				'label'     => __( 'Original Price Color', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#9ca3af',
				'condition' => array( 'show_price' => 'yes' ),
				'selectors' => array( '{{WRAPPER}} .zd-card__old-price' => 'color: {{VALUE}};' ),
			)
		);

		$this->end_controls_section();

		/* Button */
		$this->start_controls_section(
			'section_style_button',
			array(
				'label'     => __( 'Button', 'zlaark-deals-pro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'show_button' => 'yes' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'card_button_typography',
				'selector' => '{{WRAPPER}} .zd-btn',
			)
		);

		$this->add_control(
			'button_bg',
			array(
				'label'     => __( 'Background', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#0a1310',
				'selectors' => array( '{{WRAPPER}} .zd-btn--solid' => 'background-color: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'button_color',
			array(
				'label'     => __( 'Text Color', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array( '{{WRAPPER}} .zd-btn--solid, {{WRAPPER}} .zd-btn--solid .zd-btn__label, {{WRAPPER}} .zd-btn--solid .zd-btn__arrow' => 'color: {{VALUE}};' ),
			)
		);

		$this->add_responsive_control(
			'card_button_padding',
			array(
				'label'      => __( 'Padding', 'zlaark-deals-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'separator'  => 'before',
				'selectors'  => array(
					'{{WRAPPER}} .zd-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'card_button_radius',
			array(
				'label'      => __( 'Border Radius', 'zlaark-deals-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .zd-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
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

		// Collect the deals up front so the filter tabs only offer categories
		// that are actually present in the result set.
		$deals = array();
		$tabs  = array();
		while ( $query->have_posts() ) {
			$query->the_post();
			$deal    = Zlaark_Deals_Meta::get_deal_data( get_post() );
			$deals[] = $deal;
			foreach ( $deal['terms'] as $term ) {
				$tabs[ $term->term_id ] = $term->name;
			}
		}
		wp_reset_postdata();

		$heading = $this->resolve_heading( $s );
		$stagger = isset( $s['reveal_stagger']['size'] ) ? (int) $s['reveal_stagger']['size'] : 90;
		?>
		<div class="zd-deals" data-zd-reveal-root="true" data-zd-stagger="<?php echo esc_attr( $stagger ); ?>">

			<?php if ( 'yes' === $s['show_heading'] && ( '' !== $heading || '' !== $s['subheading'] ) ) : ?>
				<div class="zd-section__head zd-reveal" data-zd-reveal="<?php echo esc_attr( $s['reveal_effect'] ); ?>">
					<?php if ( '' !== $heading ) : ?>
						<h2 class="zd-section__title">
							<span class="zd-section__bar" aria-hidden="true"></span>
							<?php echo esc_html( $heading ); ?>
						</h2>
					<?php endif; ?>
					<?php if ( '' !== $s['subheading'] ) : ?>
						<p class="zd-section__sub"><?php echo esc_html( $s['subheading'] ); ?></p>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<?php if ( 'yes' === $s['show_filters'] && count( $tabs ) > 1 ) : ?>
				<div class="zd-tabs" role="tablist">
					<span class="zd-tabs__indicator" aria-hidden="true"></span>
					<button type="button" class="zd-tabs__btn is-active" data-zd-filter="all" role="tab" aria-selected="true">
						<?php echo esc_html( $s['filter_all_label'] ); ?>
					</button>
					<?php foreach ( $tabs as $term_id => $name ) : ?>
						<button type="button" class="zd-tabs__btn" data-zd-filter="<?php echo esc_attr( $term_id ); ?>" role="tab" aria-selected="false">
							<?php echo esc_html( $name ); ?>
						</button>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>

			<?php
			$grid_classes = array( 'zd-grid', 'zd-grid--' . $s['card_layout'] );
			if ( 'yes' === $s['equal_height'] ) {
				$grid_classes[] = 'zd-grid--equal';
			}
			if ( 'yes' === $s['accent_bar'] ) {
				$grid_classes[] = 'zd-grid--bar';
			}
			?>
			<div class="<?php echo esc_attr( implode( ' ', $grid_classes ) ); ?>">
				<?php
				foreach ( $deals as $i => $deal ) {
					$this->render_card( $deal, $s, $i );
				}
				?>
			</div>
		</div>
		<?php
	}

	private function render_card( $deal, $s, $index ) {
		if ( empty( $deal ) ) {
			return;
		}

		$classes = array_merge( array( 'zd-card', 'zd-reveal' ), $this->motion_classes( $s ) );
		$terms   = implode( ' ', wp_list_pluck( $deal['terms'], 'term_id' ) );
		?>
		<article class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>"
			data-zd-reveal="<?php echo esc_attr( $s['reveal_effect'] ); ?>"
			data-zd-terms="<?php echo esc_attr( $terms ); ?>"
			style="--zd-i:<?php echo (int) $index; ?>">

			<span class="zd-card__glow" aria-hidden="true"></span>
			<span class="zd-card__shine" aria-hidden="true"></span>

			<?php if ( '' !== $deal['badge'] ) : ?>
				<span class="zd-card__badge"><?php echo esc_html( $deal['badge'] ); ?></span>
			<?php endif; ?>

			<?php
			/*
			 * One ember element per card, maximum. A real deadline outranks an
			 * offer-type label, because urgency is the more perishable fact.
			 */
			$offer_types = Zlaark_Deals_Meta::offer_types();
			$type_label  = ( '' !== $deal['offer_type'] && isset( $offer_types[ $deal['offer_type'] ] ) )
				? $offer_types[ $deal['offer_type'] ]
				: '';
			?>
			<?php if ( '' !== $deal['urgency_label'] ) : ?>
				<span class="zd-chip zd-chip--ember zd-card__flag"><?php echo esc_html( $deal['urgency_label'] ); ?></span>
			<?php elseif ( '' !== $type_label ) : ?>
				<span class="zd-chip zd-chip--neutral zd-card__flag"><?php echo esc_html( $type_label ); ?></span>
			<?php endif; ?>

			<?php if ( 'yes' === $s['accent_bar'] ) : ?>
				<span class="zd-card__bar" aria-hidden="true"></span>
			<?php endif; ?>

			<div class="zd-card__inner">
				<?php if ( 'yes' === $s['show_image'] && $deal['image_id'] ) : ?>
					<div class="zd-card__media">
						<?php
						echo wp_get_attachment_image(
							$deal['image_id'],
							'medium',
							false,
							array( 'class' => 'zd-card__image', 'loading' => 'lazy' )
						);
						?>
					</div>
				<?php endif; ?>

				<?php
				// Body and foot are separate so each layout can place the price
				// and button independently of the title block.
				?>
				<div class="zd-card__main">
					<div class="zd-card__body">
						<h3 class="zd-card__title"><?php echo esc_html( $deal['title'] ); ?></h3>

						<?php
						// The score is the mean of the breakdown, so it can never
						// disagree with the bars shown elsewhere for the same deal.
						$score_meta = array();
						if ( '' !== $deal['tested_date'] ) {
							$score_meta[] = sprintf(
								/* translators: %s: month and year the deal was tested. */
								__( 'Tested %s', 'zlaark-deals-pro' ),
								date_i18n( 'M Y', strtotime( $deal['tested_date'] ) )
							);
						}
						if ( '' !== $deal['refund_window'] ) {
							$score_meta[] = $deal['refund_window'];
						}
						?>
						<?php if ( null !== $deal['overall_score'] || ! empty( $score_meta ) ) : ?>
							<p class="zd-card__scoreline">
								<?php if ( null !== $deal['overall_score'] ) : ?>
									<span class="zd-card__score zd-score--<?php echo esc_attr( $deal['score_band'] ); ?>">
										<?php echo esc_html( number_format_i18n( $deal['overall_score'], 1 ) ); ?>
									</span>
								<?php endif; ?>
								<?php if ( ! empty( $score_meta ) ) : ?>
									<span class="zd-card__scoremeta"><?php echo esc_html( implode( ' · ', $score_meta ) ); ?></span>
								<?php endif; ?>
							</p>
						<?php endif; ?>

						<?php if ( 'yes' === $s['show_tagline'] && '' !== $deal['tagline'] ) : ?>
							<p class="zd-card__tagline"><?php echo esc_html( $deal['tagline'] ); ?></p>
						<?php endif; ?>

						<?php if ( '' !== $deal['offer_headline'] && '' === $deal['price'] ) : ?>
							<p class="zd-card__offerline"><?php echo esc_html( $deal['offer_headline'] ); ?></p>
						<?php endif; ?>
					</div>

					<div class="zd-card__foot">
						<?php if ( 'yes' === $s['show_price'] && ( '' !== $deal['price'] || '' !== $deal['old_price'] ) ) : ?>
							<p class="zd-card__pricing">
								<?php if ( '' !== $deal['price'] ) : ?>
									<span class="zd-card__price"><?php echo esc_html( $deal['price'] ); ?></span>
								<?php endif; ?>
								<?php if ( '' !== $deal['old_price'] ) : ?>
									<s class="zd-card__old-price"><?php echo esc_html( $deal['old_price'] ); ?></s>
								<?php endif; ?>
								<?php if ( null !== $deal['discount_pct'] ) : ?>
									<span class="zd-card__save">
										<?php
										printf(
											/* translators: %d: discount percentage, rounded down. */
											esc_html__( 'Save %d%%', 'zlaark-deals-pro' ),
											(int) $deal['discount_pct']
										);
										?>
									</span>
								<?php endif; ?>
							</p>
						<?php endif; ?>

						<?php if ( '' !== $deal['coupon_code'] ) : ?>
							<div class="zd-coupon" data-zd-coupon="<?php echo esc_attr( $deal['coupon_code'] ); ?>">
								<span class="zd-coupon__code"><?php echo esc_html( $deal['coupon_code'] ); ?></span>
								<button type="button" class="zd-coupon__copy"
									data-zd-copied="<?php esc_attr_e( 'Copied', 'zlaark-deals-pro' ); ?>">
									<?php esc_html_e( 'Copy code', 'zlaark-deals-pro' ); ?>
								</button>
							</div>
						<?php endif; ?>

						<?php
						/*
						 * The renewal price is the single most trust-building line
						 * on the card, and the one every competitor hides.
						 */
						$terms = array();
						if ( '' !== $deal['renewal_price'] ) {
							$terms[] = $deal['term_length'] > 0
								? sprintf(
									/* translators: 1: renewal price, 2: term length in months. */
									__( 'Renews at %1$s after %2$d months', 'zlaark-deals-pro' ),
									$deal['renewal_price'],
									(int) $deal['term_length']
								)
								: sprintf(
									/* translators: %s: renewal price. */
									__( 'Renews at %s', 'zlaark-deals-pro' ),
									$deal['renewal_price']
								);
						}
						if ( null !== $deal['first_term_total'] ) {
							$terms[] = sprintf(
								/* translators: %s: total cost across the first term. */
								__( '%s first term', 'zlaark-deals-pro' ),
								Zlaark_Deals_Computed::format_money( $deal['first_term_total'], $deal )
							);
						}
						?>
						<?php if ( ! empty( $terms ) || '' !== $deal['verified_label'] ) : ?>
							<div class="zd-card__terms">
								<?php foreach ( $terms as $line ) : ?>
									<span class="zd-card__term"><?php echo esc_html( $line ); ?></span>
								<?php endforeach; ?>
								<?php if ( '' !== $deal['verified_label'] ) : ?>
									<span class="zd-card__verified"><?php echo esc_html( $deal['verified_label'] ); ?></span>
								<?php endif; ?>
							</div>
						<?php endif; ?>

						<?php if ( 'yes' === $s['show_button'] ) : ?>
							<?php $this->render_cta( $deal, $s ); ?>
						<?php endif; ?>

						<?php if ( '' !== $deal['review_url'] ) : ?>
							<a class="zd-card__review" href="<?php echo esc_url( $deal['review_url'] ); ?>">
								<?php
								printf(
									/* translators: %s: deal title. */
									esc_html__( 'Read the full %s review', 'zlaark-deals-pro' ),
									esc_html( $deal['title'] )
								);
								?>
							</a>
						<?php endif; ?>
					</div>
				</div>

				<?php if ( 'yes' === $s['show_rating'] && null !== $deal['rating'] ) : ?>
					<div class="zd-card__rating">
						<?php $this->render_rating_ring( $deal['rating'] ); ?>
					</div>
				<?php endif; ?>
			</div>
		</article>
		<?php
	}
}
