<?php
/**
 * Zlaark Hero - an animated hero section with an aurora background, drifting
 * orbs, a rotating conic ring behind the media and a word-by-word title reveal.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Utils;

class Zlaark_Hero_Widget extends Zlaark_Widget_Base {

	public function get_name() {
		return 'zlaark_hero';
	}

	public function get_title() {
		return __( 'Zlaark Hero', 'zlaark-deals-pro' );
	}

	public function get_icon() {
		return 'eicon-banner';
	}

	public function get_keywords() {
		return array( 'hero', 'banner', 'header', 'cta', 'animated', 'zlaark' );
	}

	protected function register_controls() {
		$this->content_controls();
		$this->buttons_controls();
		$this->stats_controls();
		$this->style_controls();
		$this->animation_controls( false );
		$this->hero_motion_controls();
	}

	/* ---------------------------------------------------------------- content */

	private function content_controls() {
		$this->start_controls_section(
			'section_content',
			array( 'label' => __( 'Content', 'zlaark-deals-pro' ) )
		);

		$this->add_control(
			'eyebrow',
			array(
				'label'       => __( 'Eyebrow Pill', 'zlaark-deals-pro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Verified deals · Updated monthly', 'zlaark-deals-pro' ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'eyebrow_dot',
			array(
				'label'        => __( 'Pulsing Status Dot', 'zlaark-deals-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'condition'    => array( 'eyebrow!' => '' ),
			)
		);

		$this->add_control(
			'title',
			array(
				'label'       => __( 'Title', 'zlaark-deals-pro' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 3,
				'default'     => __( 'The best deals for a', 'zlaark-deals-pro' ),
				'label_block' => true,
				'description' => __( 'Each word animates in on its own.', 'zlaark-deals-pro' ),
			)
		);

		$this->add_control(
			'title_highlight',
			array(
				'label'       => __( 'Highlighted Words', 'zlaark-deals-pro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'successful online store', 'zlaark-deals-pro' ),
				'label_block' => true,
				'description' => __( 'Appended to the title with an animated gradient fill.', 'zlaark-deals-pro' ),
			)
		);

		$this->add_control(
			'title_underline',
			array(
				'label'        => __( 'Highlight Underline', 'zlaark-deals-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '',
				'return_value' => 'yes',
				'condition'    => array( 'title_highlight!' => '' ),
				'description'  => __( 'Draws a gradient line under the highlighted words.', 'zlaark-deals-pro' ),
			)
		);

		$this->add_control(
			'title_tag',
			array(
				'label'   => __( 'Title HTML Tag', 'zlaark-deals-pro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'h1',
				'options' => array(
					'h1'   => 'H1',
					'h2'   => 'H2',
					'h3'   => 'H3',
					'h4'   => 'H4',
					'div'  => 'div',
					'span' => 'span',
				),
			)
		);

		$this->add_control(
			'description',
			array(
				'label'   => __( 'Description', 'zlaark-deals-pro' ),
				'type'    => Controls_Manager::TEXTAREA,
				'rows'    => 4,
				'default' => __( 'Hand-tested discounts on hosting, ecommerce platforms and the tools you already pay for. Scored, compared and refreshed every month.', 'zlaark-deals-pro' ),
			)
		);

		$this->add_control(
			'image',
			array(
				'label'   => __( 'Hero Image', 'zlaark-deals-pro' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => array( 'url' => Utils::get_placeholder_image_src() ),
			)
		);

		$this->add_control(
			'layout',
			array(
				'label'     => __( 'Layout', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'image-right',
				'separator' => 'before',
				'options'   => array(
					'image-right' => __( 'Text left / image right', 'zlaark-deals-pro' ),
					'image-left'  => __( 'Image left / text right', 'zlaark-deals-pro' ),
					'centered'    => __( 'Centered, image below', 'zlaark-deals-pro' ),
					'text-only'   => __( 'Text only', 'zlaark-deals-pro' ),
				),
			)
		);

		$this->add_responsive_control(
			'align',
			array(
				'label'     => __( 'Text Alignment', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'left'   => array(
						'title' => __( 'Left', 'zlaark-deals-pro' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'zlaark-deals-pro' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right'  => array(
						'title' => __( 'Right', 'zlaark-deals-pro' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .zd-hero__content' => 'text-align: {{VALUE}}; align-items: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'scroll_cue',
			array(
				'label'        => __( 'Animated Scroll Cue', 'zlaark-deals-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '',
				'return_value' => 'yes',
				'separator'    => 'before',
			)
		);

		$this->end_controls_section();
	}

	private function buttons_controls() {
		$this->start_controls_section(
			'section_buttons',
			array( 'label' => __( 'Buttons', 'zlaark-deals-pro' ) )
		);

		$this->add_control(
			'primary_text',
			array(
				'label'   => __( 'Primary Button Text', 'zlaark-deals-pro' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Browse the deals', 'zlaark-deals-pro' ),
			)
		);

		$this->add_control(
			'primary_link',
			array(
				'label'       => __( 'Primary Button Link', 'zlaark-deals-pro' ),
				'type'        => Controls_Manager::URL,
				'default'     => array( 'url' => '#' ),
				'placeholder' => 'https://example.com',
			)
		);

		$this->add_control(
			'secondary_text',
			array(
				'label'     => __( 'Secondary Button Text', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'How we test', 'zlaark-deals-pro' ),
				'separator' => 'before',
			)
		);

		$this->add_control(
			'secondary_link',
			array(
				'label'     => __( 'Secondary Button Link', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::URL,
				'condition' => array( 'secondary_text!' => '' ),
			)
		);

		$this->add_control(
			'magnetic',
			array(
				'label'        => __( 'Magnetic Buttons', 'zlaark-deals-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'separator'    => 'before',
				'description'  => __( 'Buttons drift towards the cursor while it hovers them.', 'zlaark-deals-pro' ),
			)
		);

		$this->end_controls_section();
	}

	private function stats_controls() {
		$this->start_controls_section(
			'section_stats',
			array( 'label' => __( 'Stat Strip', 'zlaark-deals-pro' ) )
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'value',
			array(
				'label'   => __( 'Number', 'zlaark-deals-pro' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 120,
			)
		);

		$repeater->add_control(
			'prefix',
			array(
				'label' => __( 'Prefix', 'zlaark-deals-pro' ),
				'type'  => Controls_Manager::TEXT,
			)
		);

		$repeater->add_control(
			'suffix',
			array(
				'label'   => __( 'Suffix', 'zlaark-deals-pro' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '+',
			)
		);

		$repeater->add_control(
			'label',
			array(
				'label'   => __( 'Label', 'zlaark-deals-pro' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Deals tested', 'zlaark-deals-pro' ),
			)
		);

		$this->add_control(
			'stats',
			array(
				'label'       => __( 'Stats', 'zlaark-deals-pro' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ prefix }}}{{{ value }}}{{{ suffix }}} - {{{ label }}}',
				'default'     => array(
					array(
						'value'  => 118,
						'label'  => __( 'Deals tested', 'zlaark-deals-pro' ),
					),
					array(
						'value'  => 37,
						'suffix' => '%',
						'label'  => __( 'Average saving', 'zlaark-deals-pro' ),
					),
					array(
						'value'  => 9,
						'suffix' => '/10',
						'label'  => __( 'Reader rating', 'zlaark-deals-pro' ),
					),
				),
			)
		);

		$this->end_controls_section();
	}

	/* ------------------------------------------------------------------ style */

	private function style_controls() {
		$this->start_controls_section(
			'section_style_box',
			array(
				'label' => __( 'Box', 'zlaark-deals-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'hero_background',
				'selector' => '{{WRAPPER}} .zd-hero',
			)
		);

		$this->add_responsive_control(
			'hero_padding',
			array(
				'label'      => __( 'Padding', 'zlaark-deals-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'default'    => array(
					'top'      => '96',
					'right'    => '40',
					'bottom'   => '96',
					'left'     => '40',
					'unit'     => 'px',
					'isLinked' => false,
				),
				'selectors'  => array(
					'{{WRAPPER}} .zd-hero' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'hero_radius',
			array(
				'label'      => __( 'Border Radius', 'zlaark-deals-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'default'    => array(
					'top'      => '28',
					'right'    => '28',
					'bottom'   => '28',
					'left'     => '28',
					'unit'     => 'px',
					'isLinked' => true,
				),
				'selectors'  => array(
					'{{WRAPPER}} .zd-hero' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'max_width',
			array(
				'label'      => __( 'Content Max Width', 'zlaark-deals-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array( 'min' => 800, 'max' => 1920 ),
					'%'  => array( 'min' => 50, 'max' => 100 ),
				),
				'default'    => array( 'unit' => 'px', 'size' => 1240 ),
				'selectors'  => array( '{{WRAPPER}} .zd-hero__inner' => 'max-width: {{SIZE}}{{UNIT}};' ),
			)
		);

		$this->add_responsive_control(
			'content_gap',
			array(
				'label'      => __( 'Column Gap', 'zlaark-deals-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 160 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 64 ),
				'selectors'  => array(
					'{{WRAPPER}} .zd-hero__inner' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'accent',
			array(
				'label'     => __( 'Accent Color', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#0b7a4f',
				'separator' => 'before',
				'selectors' => array( '{{WRAPPER}} .zd-hero' => '--zd-accent: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'accent_2',
			array(
				'label'     => __( 'Accent Color 2', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#065f42',
				'selectors' => array( '{{WRAPPER}} .zd-hero' => '--zd-accent-2: {{VALUE}};' ),
			)
		);

		$this->end_controls_section();

		/* Typography */

		$this->start_controls_section(
			'section_style_text',
			array(
				'label' => __( 'Typography', 'zlaark-deals-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'eyebrow_color',
			array(
				'label'     => __( 'Eyebrow Color', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#4a5a52',
				'selectors' => array( '{{WRAPPER}} .zd-hero__eyebrow' => 'color: {{VALUE}};' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'eyebrow_typography',
				'selector' => '{{WRAPPER}} .zd-hero__eyebrow',
			)
		);

		$this->add_control(
			'title_color',
			array(
				'label'     => __( 'Title Color', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#0a1310',
				'separator' => 'before',
				'selectors' => array( '{{WRAPPER}} .zd-hero__title' => 'color: {{VALUE}};' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .zd-hero__title',
			)
		);

		$this->add_control(
			'desc_color',
			array(
				'label'     => __( 'Description Color', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#4a5a52',
				'separator' => 'before',
				'selectors' => array( '{{WRAPPER}} .zd-hero__desc' => 'color: {{VALUE}};' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'desc_typography',
				'selector' => '{{WRAPPER}} .zd-hero__desc',
			)
		);

		$this->add_responsive_control(
			'desc_max_width',
			array(
				'label'      => __( 'Description Max Width', 'zlaark-deals-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array( 'min' => 240, 'max' => 900 ),
					'%'  => array( 'min' => 20, 'max' => 100 ),
				),
				'default'    => array( 'unit' => 'px', 'size' => 560 ),
				'selectors'  => array( '{{WRAPPER}} .zd-hero__desc' => 'max-width: {{SIZE}}{{UNIT}};' ),
			)
		);

		$this->end_controls_section();

		/* Buttons */

		$this->start_controls_section(
			'section_style_buttons',
			array(
				'label' => __( 'Buttons', 'zlaark-deals-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'button_typography',
				'selector' => '{{WRAPPER}} .zd-btn',
			)
		);

		$this->add_responsive_control(
			'button_padding',
			array(
				'label'      => __( 'Padding', 'zlaark-deals-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .zd-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'button_radius',
			array(
				'label'      => __( 'Border Radius', 'zlaark-deals-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .zd-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'primary_bg',
			array(
				'label'     => __( 'Primary Background', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#0a1310',
				'separator' => 'before',
				'selectors' => array( '{{WRAPPER}} .zd-btn--solid' => 'background-color: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'primary_color',
			array(
				'label'     => __( 'Primary Text Color', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array( '{{WRAPPER}} .zd-btn--solid, {{WRAPPER}} .zd-btn--solid .zd-btn__label, {{WRAPPER}} .zd-btn--solid .zd-btn__arrow' => 'color: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'secondary_color',
			array(
				'label'     => __( 'Secondary Text Color', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#0a1310',
				'separator' => 'before',
				'condition' => array( 'secondary_text!' => '' ),
				'selectors' => array(
					'{{WRAPPER}} .zd-btn--ghost' => 'color: {{VALUE}}; border-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		/* Image */

		$this->start_controls_section(
			'section_style_image',
			array(
				'label'     => __( 'Image', 'zlaark-deals-pro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'layout!' => 'text-only' ),
			)
		);

		$this->add_responsive_control(
			'image_width',
			array(
				'label'      => __( 'Max Width', 'zlaark-deals-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array( 'min' => 100, 'max' => 1200 ),
					'%'  => array( 'min' => 10, 'max' => 100 ),
				),
				'default'    => array( 'unit' => '%', 'size' => 100 ),
				'selectors'  => array( '{{WRAPPER}} .zd-hero__media img' => 'max-width: {{SIZE}}{{UNIT}};' ),
			)
		);

		$this->add_control(
			'image_radius',
			array(
				'label'      => __( 'Border Radius', 'zlaark-deals-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'default'    => array(
					'top'      => '22',
					'right'    => '22',
					'bottom'   => '22',
					'left'     => '22',
					'unit'     => 'px',
					'isLinked' => true,
				),
				'selectors'  => array(
					'{{WRAPPER}} .zd-hero__media img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'image_shadow',
				'selector' => '{{WRAPPER}} .zd-hero__media img',
			)
		);

		$this->end_controls_section();
	}

	/** Hero-specific ambient motion, on top of the shared reveal controls. */
	private function hero_motion_controls() {
		$this->start_controls_section(
			'section_hero_motion',
			array(
				'label' => __( 'Ambient Motion', 'zlaark-deals-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'aurora',
			array(
				'label'        => __( 'Aurora Background', 'zlaark-deals-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'description'  => __( 'Slow-drifting gradient blooms behind the content.', 'zlaark-deals-pro' ),
			)
		);

		$this->add_control(
			'orbs',
			array(
				'label'        => __( 'Floating Orbs', 'zlaark-deals-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'grid_lines',
			array(
				'label'        => __( 'Moving Grid Lines', 'zlaark-deals-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'media_ring',
			array(
				'label'        => __( 'Rotating Ring Behind Image', 'zlaark-deals-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'condition'    => array( 'layout!' => 'text-only' ),
			)
		);

		$this->add_control(
			'media_float',
			array(
				'label'        => __( 'Floating Image', 'zlaark-deals-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'condition'    => array( 'layout!' => 'text-only' ),
				'description'  => __( 'The image bobs gently and parallaxes with the cursor.', 'zlaark-deals-pro' ),
			)
		);

		$this->add_control(
			'motion_speed',
			array(
				'label'      => __( 'Ambient Speed', 'zlaark-deals-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 's' ),
				'range'      => array( 's' => array( 'min' => 4, 'max' => 40, 'step' => 1 ) ),
				'default'    => array( 'unit' => 's', 'size' => 18 ),
				'selectors'  => array( '{{WRAPPER}} .zd-hero' => '--zd-ambient-speed: {{SIZE}}s;' ),
			)
		);

		$this->end_controls_section();
	}

	/* ----------------------------------------------------------------- render */

	protected function render() {
		$s      = $this->get_settings_for_display();
		$layout = ! empty( $s['layout'] ) ? $s['layout'] : 'image-right';

		$classes = array( 'zd-hero', 'zd-hero--' . $layout );
		foreach ( array( 'aurora', 'orbs', 'grid_lines' ) as $flag ) {
			if ( 'yes' === $s[ $flag ] ) {
				$classes[] = 'zd-hero--' . str_replace( '_', '-', $flag );
			}
		}

		$this->add_render_attribute( 'wrapper', 'class', $classes );
		$this->add_render_attribute( 'wrapper', 'data-zd-reveal-root', 'true' );
		$this->add_render_attribute( 'wrapper', 'data-zd-stagger', isset( $s['reveal_stagger']['size'] ) ? (int) $s['reveal_stagger']['size'] : 90 );

		$has_image = ( 'text-only' !== $layout ) && ! empty( $s['image']['url'] );
		$effect    = ! empty( $s['reveal_effect'] ) ? $s['reveal_effect'] : 'rise';
		?>
		<div <?php $this->print_render_attribute_string( 'wrapper' ); ?>>

			<?php if ( 'yes' === $s['aurora'] ) : ?>
				<div class="zd-hero__aurora" aria-hidden="true">
					<span class="zd-hero__bloom zd-hero__bloom--1"></span>
					<span class="zd-hero__bloom zd-hero__bloom--2"></span>
					<span class="zd-hero__bloom zd-hero__bloom--3"></span>
				</div>
			<?php endif; ?>

			<?php if ( 'yes' === $s['grid_lines'] ) : ?>
				<div class="zd-hero__grid" aria-hidden="true"></div>
			<?php endif; ?>

			<?php if ( 'yes' === $s['orbs'] ) : ?>
				<div class="zd-hero__orbs" aria-hidden="true">
					<?php for ( $i = 1; $i <= 5; $i++ ) : ?>
						<span class="zd-hero__orb zd-hero__orb--<?php echo (int) $i; ?>"></span>
					<?php endfor; ?>
				</div>
			<?php endif; ?>

			<div class="zd-hero__inner">
				<div class="zd-hero__content">

					<?php if ( ! empty( $s['eyebrow'] ) ) : ?>
						<p class="zd-hero__eyebrow zd-reveal" data-zd-reveal="<?php echo esc_attr( $effect ); ?>">
							<?php if ( 'yes' === $s['eyebrow_dot'] ) : ?>
								<span class="zd-hero__dot" aria-hidden="true"></span>
							<?php endif; ?>
							<?php echo esc_html( $s['eyebrow'] ); ?>
						</p>
					<?php endif; ?>

					<?php $this->render_title( $s, $effect ); ?>

					<?php if ( ! empty( $s['description'] ) ) : ?>
						<div class="zd-hero__desc zd-reveal" data-zd-reveal="<?php echo esc_attr( $effect ); ?>">
							<?php echo wp_kses_post( wpautop( $s['description'] ) ); ?>
						</div>
					<?php endif; ?>

					<?php if ( ! empty( $s['primary_text'] ) || ! empty( $s['secondary_text'] ) ) : ?>
						<div class="zd-hero__actions zd-reveal" data-zd-reveal="<?php echo esc_attr( $effect ); ?>">
							<?php
							$this->render_button( 'solid', $s['primary_text'], $s['primary_link'], $s );
							$this->render_button( 'ghost', $s['secondary_text'], $s['secondary_link'], $s );
							?>
						</div>
					<?php endif; ?>

					<?php $this->render_stats( $s, $effect ); ?>
				</div>

				<?php if ( $has_image ) : ?>
					<div class="zd-hero__media zd-reveal
						<?php echo 'yes' === $s['media_float'] ? 'zd-hero__media--float zd-parallax' : ''; ?>"
						data-zd-reveal="zoom">
						<?php if ( 'yes' === $s['media_ring'] ) : ?>
							<span class="zd-hero__ring" aria-hidden="true"></span>
							<span class="zd-hero__ring zd-hero__ring--inner" aria-hidden="true"></span>
						<?php endif; ?>
						<img src="<?php echo esc_url( $s['image']['url'] ); ?>"
							alt="<?php echo esc_attr( zlaark_deals_media_alt( $s['image'] ) ); ?>" />
					</div>
				<?php endif; ?>
			</div>

			<?php if ( 'yes' === $s['scroll_cue'] ) : ?>
				<div class="zd-hero__cue" aria-hidden="true">
					<span class="zd-hero__cue-wheel"></span>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}

	/** Splits the title into per-word spans so each can animate on its own delay. */
	private function render_title( $s, $effect ) {
		if ( empty( $s['title'] ) && empty( $s['title_highlight'] ) ) {
			return;
		}

		$tag   = ! empty( $s['title_tag'] ) ? $s['title_tag'] : 'h1';
		$words = preg_split( '/\s+/', trim( (string) $s['title'] ), -1, PREG_SPLIT_NO_EMPTY );

		printf( '<%1$s class="zd-hero__title" data-zd-reveal="%2$s">', esc_attr( $tag ), esc_attr( $effect ) );

		foreach ( $words as $i => $word ) {
			printf(
				'<span class="zd-word" style="--zd-i:%1$d"><span>%2$s</span></span> ',
				(int) $i,
				esc_html( $word )
			);
		}

		if ( ! empty( $s['title_highlight'] ) ) {
			$accent_class = 'zd-word zd-word--accent';
			if ( 'yes' === $s['title_underline'] ) {
				$accent_class .= ' zd-word--underline';
			}
			printf(
				'<span class="%1$s" style="--zd-i:%2$d"><span>%3$s</span></span>',
				esc_attr( $accent_class ),
				count( $words ),
				esc_html( $s['title_highlight'] )
			);
		}

		printf( '</%1$s>', esc_attr( $tag ) );
	}

	private function render_stats( $s, $effect ) {
		if ( empty( $s['stats'] ) || ! is_array( $s['stats'] ) ) {
			return;
		}
		?>
		<div class="zd-hero__stats zd-reveal" data-zd-reveal="<?php echo esc_attr( $effect ); ?>">
			<?php foreach ( $s['stats'] as $i => $stat ) : ?>
				<div class="zd-stat" style="--zd-i:<?php echo (int) $i; ?>">
					<p class="zd-stat__value">
						<?php echo esc_html( $stat['prefix'] ); ?><span data-zd-count="<?php echo esc_attr( $stat['value'] ); ?>">0</span><?php echo esc_html( $stat['suffix'] ); ?>
					</p>
					<p class="zd-stat__label"><?php echo esc_html( $stat['label'] ); ?></p>
				</div>
			<?php endforeach; ?>
		</div>
		<?php
	}

	/**
	 * @param string $variant solid|ghost
	 * @param string $text
	 * @param array  $link    Elementor URL control value.
	 * @param array  $s       Widget settings.
	 */
	private function render_button( $variant, $text, $link, $s ) {
		if ( empty( $text ) ) {
			return;
		}

		$key     = 'btn_' . $variant;
		$classes = array( 'zd-btn', 'zd-btn--' . $variant );
		if ( 'yes' === $s['magnetic'] ) {
			$classes[] = 'zd-magnetic';
		}

		$this->add_render_attribute( $key, 'class', $classes );

		if ( ! empty( $link['url'] ) ) {
			$this->add_link_attributes( $key, $link );
		}
		?>
		<a <?php $this->print_render_attribute_string( $key ); ?>>
			<span class="zd-btn__label"><?php echo esc_html( $text ); ?></span>
			<span class="zd-btn__arrow" aria-hidden="true">
				<svg viewBox="0 0 16 16" width="14" height="14" fill="none">
					<path d="M2 8h11M9 4l4 4-4 4" stroke="currentColor" stroke-width="2"
						stroke-linecap="round" stroke-linejoin="round" />
				</svg>
			</span>
		</a>
		<?php
	}
}

/**
 * Elementor stores alt text on the attachment, not in the control value.
 * Small helper so hero images always ship an alt attribute.
 */
function zlaark_deals_media_alt( $media ) {
	if ( ! empty( $media['id'] ) ) {
		$alt = get_post_meta( (int) $media['id'], '_wp_attachment_image_alt', true );
		if ( $alt ) {
			return $alt;
		}
	}
	return '';
}
