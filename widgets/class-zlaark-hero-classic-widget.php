<?php
/**
 * Zlaark Hero Classic — the original text-and-image hero, rebuilt to a
 * premium standard: glass media frame, overlapping stat cards, a feature
 * checklist and a social-proof row, over a soft gradient backdrop.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Icons_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Utils;

class Zlaark_Hero_Classic_Widget extends Zlaark_Widget_Base {

	public function get_name() {
		return 'zlaark_hero_classic';
	}

	public function get_title() {
		return __( 'Zlaark Hero Classic', 'zlaark-deals-pro' );
	}

	public function get_icon() {
		return 'eicon-single-page';
	}

	public function get_keywords() {
		return array( 'hero', 'banner', 'header', 'cta', 'classic', 'zlaark' );
	}

	protected function register_controls() {
		$this->content_controls();
		$this->features_controls();
		$this->buttons_controls();
		$this->proof_controls();
		$this->media_controls();
		$this->layout_controls();
		$this->style_controls();
		$this->motion_section();
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
			'eyebrow_icon',
			array(
				'label'     => __( 'Eyebrow Icon', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::ICONS,
				'condition' => array( 'eyebrow!' => '' ),
				'default'   => array(
					'value'   => 'fas fa-bolt',
					'library' => 'fa-solid',
				),
			)
		);

		$this->add_control(
			'title',
			array(
				'label'       => __( 'Title', 'zlaark-deals-pro' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 3,
				'default'     => __( 'Save big on the tools', 'zlaark-deals-pro' ),
				'label_block' => true,
				'description' => __( 'Each word animates in on its own.', 'zlaark-deals-pro' ),
			)
		);

		$this->add_control(
			'title_highlight',
			array(
				'label'       => __( 'Highlighted Words', 'zlaark-deals-pro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'you already pay for', 'zlaark-deals-pro' ),
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
					'h1'  => 'H1',
					'h2'  => 'H2',
					'h3'  => 'H3',
					'div' => 'div',
				),
			)
		);

		$this->add_control(
			'description',
			array(
				'label'   => __( 'Description', 'zlaark-deals-pro' ),
				'type'    => Controls_Manager::TEXTAREA,
				'rows'    => 4,
				'default' => __( 'Hand-tested discounts on hosting, ecommerce platforms and the software your team runs on — scored, compared and refreshed every month.', 'zlaark-deals-pro' ),
			)
		);

		$this->end_controls_section();
	}

	private function features_controls() {
		$this->start_controls_section(
			'section_features',
			array( 'label' => __( 'Feature Ticks', 'zlaark-deals-pro' ) )
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'text',
			array(
				'label'   => __( 'Text', 'zlaark-deals-pro' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'No credit card needed', 'zlaark-deals-pro' ),
			)
		);

		$this->add_control(
			'features',
			array(
				'label'       => __( 'Items', 'zlaark-deals-pro' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ text }}}',
				'default'     => array(
					array( 'text' => __( 'Independently tested', 'zlaark-deals-pro' ) ),
					array( 'text' => __( 'No paid placements', 'zlaark-deals-pro' ) ),
					array( 'text' => __( 'Prices verified monthly', 'zlaark-deals-pro' ) ),
				),
			)
		);

		$this->add_control(
			'features_layout',
			array(
				'label'   => __( 'Layout', 'zlaark-deals-pro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'inline',
				'options' => array(
					'inline' => __( 'Inline row', 'zlaark-deals-pro' ),
					'stack'  => __( 'Stacked list', 'zlaark-deals-pro' ),
				),
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
				'label'   => __( 'Primary Button', 'zlaark-deals-pro' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Browse the deals', 'zlaark-deals-pro' ),
			)
		);

		$this->add_control(
			'primary_link',
			array(
				'label'   => __( 'Primary Link', 'zlaark-deals-pro' ),
				'type'    => Controls_Manager::URL,
				'default' => array( 'url' => '#' ),
			)
		);

		$this->add_control(
			'secondary_text',
			array(
				'label'     => __( 'Secondary Button', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'How we test', 'zlaark-deals-pro' ),
				'separator' => 'before',
			)
		);

		$this->add_control(
			'secondary_link',
			array(
				'label'     => __( 'Secondary Link', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::URL,
				'condition' => array( 'secondary_text!' => '' ),
			)
		);

		$this->add_control(
			'secondary_icon',
			array(
				'label'     => __( 'Secondary Icon', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::ICONS,
				'condition' => array( 'secondary_text!' => '' ),
				'default'   => array(
					'value'   => 'fas fa-play',
					'library' => 'fa-solid',
				),
			)
		);

		$this->end_controls_section();
	}

	private function proof_controls() {
		$this->start_controls_section(
			'section_proof',
			array( 'label' => __( 'Social Proof', 'zlaark-deals-pro' ) )
		);

		$this->add_control(
			'show_proof',
			array(
				'label'        => __( 'Show Proof Row', 'zlaark-deals-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'image',
			array(
				'label'   => __( 'Avatar', 'zlaark-deals-pro' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => array( 'url' => Utils::get_placeholder_image_src() ),
			)
		);

		$this->add_control(
			'avatars',
			array(
				'label'     => __( 'Avatars', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::REPEATER,
				'fields'    => $repeater->get_controls(),
				'condition' => array( 'show_proof' => 'yes' ),
				'default'   => array(
					array( 'image' => array( 'url' => Utils::get_placeholder_image_src() ) ),
					array( 'image' => array( 'url' => Utils::get_placeholder_image_src() ) ),
					array( 'image' => array( 'url' => Utils::get_placeholder_image_src() ) ),
					array( 'image' => array( 'url' => Utils::get_placeholder_image_src() ) ),
				),
			)
		);

		$this->add_control(
			'rating',
			array(
				'label'     => __( 'Stars', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 0,
				'max'       => 5,
				'step'      => 0.5,
				'default'   => 5,
				'condition' => array( 'show_proof' => 'yes' ),
			)
		);

		$this->add_control(
			'proof_text',
			array(
				'label'       => __( 'Proof Text', 'zlaark-deals-pro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Trusted by 2,400+ teams', 'zlaark-deals-pro' ),
				'label_block' => true,
				'condition'   => array( 'show_proof' => 'yes' ),
			)
		);

		$this->end_controls_section();
	}

	private function media_controls() {
		$this->start_controls_section(
			'section_media',
			array( 'label' => __( 'Media', 'zlaark-deals-pro' ) )
		);

		$this->add_control(
			'image',
			array(
				'label'   => __( 'Image', 'zlaark-deals-pro' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => array( 'url' => Utils::get_placeholder_image_src() ),
			)
		);

		$this->add_control(
			'frame_style',
			array(
				'label'   => __( 'Frame', 'zlaark-deals-pro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'plain',
				'options' => array(
					'plain'  => __( 'Plain image — no card, no shadow', 'zlaark-deals-pro' ),
					'glass'  => __( 'Glass panel', 'zlaark-deals-pro' ),
					'window' => __( 'Browser window', 'zlaark-deals-pro' ),
				),
			)
		);

		/* --- overlapping stat card --- */
		$this->add_control(
			'heading_statcard',
			array(
				'label'     => __( 'Floating Stat Card', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'stat_show',
			array(
				'label'        => __( 'Show', 'zlaark-deals-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'stat_value',
			array(
				'label'     => __( 'Value', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 42,
				'condition' => array( 'stat_show' => 'yes' ),
			)
		);

		$this->add_control(
			'stat_suffix',
			array(
				'label'     => __( 'Suffix', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => '%',
				'condition' => array( 'stat_show' => 'yes' ),
			)
		);

		$this->add_control(
			'stat_label',
			array(
				'label'     => __( 'Label', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Average saving', 'zlaark-deals-pro' ),
				'condition' => array( 'stat_show' => 'yes' ),
			)
		);

		$this->add_control(
			'stat_icon',
			array(
				'label'     => __( 'Icon', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::ICONS,
				'condition' => array( 'stat_show' => 'yes' ),
				'default'   => array(
					'value'   => 'fas fa-arrow-trend-down',
					'library' => 'fa-solid',
				),
			)
		);

		/* --- overlapping badge chip --- */
		$this->add_control(
			'heading_badge',
			array(
				'label'     => __( 'Floating Badge', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'badge_show',
			array(
				'label'        => __( 'Show', 'zlaark-deals-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'badge_text',
			array(
				'label'     => __( 'Text', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Live prices', 'zlaark-deals-pro' ),
				'condition' => array( 'badge_show' => 'yes' ),
			)
		);

		$this->end_controls_section();
	}

	private function layout_controls() {
		$this->start_controls_section(
			'section_layout',
			array( 'label' => __( 'Layout', 'zlaark-deals-pro' ) )
		);

		$this->add_control(
			'media_side',
			array(
				'label'   => __( 'Media Side', 'zlaark-deals-pro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'right',
				'options' => array(
					'right'  => __( 'Text left / media right', 'zlaark-deals-pro' ),
					'left'   => __( 'Media left / text right', 'zlaark-deals-pro' ),
					'center' => __( 'Centred, media below', 'zlaark-deals-pro' ),
					'none'   => __( 'Text only', 'zlaark-deals-pro' ),
				),
			)
		);

		$this->add_responsive_control(
			'min_height',
			array(
				'label'      => __( 'Section Min Height', 'zlaark-deals-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'vh', 'px' ),
				'range'      => array(
					'vh' => array( 'min' => 0, 'max' => 110 ),
					'px' => array( 'min' => 0, 'max' => 1400 ),
				),
				'default'    => array( 'unit' => 'vh', 'size' => 0 ),
				'selectors'  => array( '{{WRAPPER}} .zd-hc' => 'min-height: {{SIZE}}{{UNIT}};' ),
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
				'selectors'  => array( '{{WRAPPER}} .zd-hc__inner' => 'max-width: {{SIZE}}{{UNIT}};' ),
			)
		);

		$this->add_responsive_control(
			'gap',
			array(
				'label'      => __( 'Column Gap', 'zlaark-deals-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 200 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 72 ),
				'selectors'  => array( '{{WRAPPER}} .zd-hc__inner' => 'gap: {{SIZE}}{{UNIT}};' ),
			)
		);

		$this->add_responsive_control(
			'text_ratio',
			array(
				'label'      => __( 'Text Column Width', 'zlaark-deals-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'fr' ),
				'range'      => array( 'fr' => array( 'min' => 0.5, 'max' => 2, 'step' => 0.05 ) ),
				'default'    => array( 'unit' => 'fr', 'size' => 1.05 ),
				'selectors'  => array( '{{WRAPPER}} .zd-hc__inner' => '--zd-hc-col: {{SIZE}}fr;' ),
			)
		);

		$this->add_responsive_control(
			'align',
			array(
				'label'     => __( 'Text Alignment', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'flex-start' => array(
						'title' => __( 'Left', 'zlaark-deals-pro' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center'     => array(
						'title' => __( 'Center', 'zlaark-deals-pro' ),
						'icon'  => 'eicon-text-align-center',
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .zd-hc__content' => 'align-items: {{VALUE}}; text-align: {{VALUE}};',
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
				'label' => __( 'Section', 'zlaark-deals-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'bg',
				'selector' => '{{WRAPPER}} .zd-hc',
			)
		);

		$this->add_responsive_control(
			'padding',
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
					'{{WRAPPER}} .zd-hc' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'radius',
			array(
				'label'      => __( 'Corner Radius', 'zlaark-deals-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 60 ) ),
				'selectors'  => array( '{{WRAPPER}} .zd-hc' => 'border-radius: {{SIZE}}{{UNIT}};' ),
			)
		);

		$this->add_control(
			'accent',
			array(
				'label'     => __( 'Accent Colour', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#0b7a4f',
				'separator' => 'before',
				'selectors' => array( '{{WRAPPER}} .zd-hc' => '--zd-accent: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'accent_2',
			array(
				'label'     => __( 'Accent Colour 2', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#06b6d4',
				'selectors' => array( '{{WRAPPER}} .zd-hc' => '--zd-accent-2: {{VALUE}};' ),
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
			'title_color',
			array(
				'label'     => __( 'Title Colour', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#0a1310',
				'selectors' => array( '{{WRAPPER}} .zd-hc__title' => 'color: {{VALUE}};' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .zd-hc__title',
			)
		);

		$this->add_responsive_control(
			'title_size',
			array(
				'label'      => __( 'Title Size', 'zlaark-deals-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'vw' ),
				'range'      => array(
					'px' => array( 'min' => 26, 'max' => 110 ),
					'vw' => array( 'min' => 2, 'max' => 9, 'step' => 0.1 ),
				),
				'default'    => array( 'unit' => 'px', 'size' => 60 ),
				'selectors'  => array( '{{WRAPPER}} .zd-hc__title' => 'font-size: {{SIZE}}{{UNIT}};' ),
			)
		);

		$this->add_responsive_control(
			'title_word_gap',
			array(
				'label'      => __( 'Word Spacing', 'zlaark-deals-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'em' ),
				'range'      => array( 'em' => array( 'min' => -0.25, 'max' => 0.6, 'step' => 0.01 ) ),
				'default'    => array( 'unit' => 'em', 'size' => -0.01 ),
				'selectors'  => array( '{{WRAPPER}} .zd-hc__title' => 'word-spacing: {{SIZE}}{{UNIT}};' ),
			)
		);

		$this->add_responsive_control(
			'title_line_height',
			array(
				'label'      => __( 'Line Height', 'zlaark-deals-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'em' ),
				'range'      => array( 'em' => array( 'min' => 0.8, 'max' => 1.6, 'step' => 0.01 ) ),
				'default'    => array( 'unit' => 'em', 'size' => 1.06 ),
				'selectors'  => array( '{{WRAPPER}} .zd-hc__title' => 'line-height: {{SIZE}}{{UNIT}};' ),
			)
		);

		$this->add_control(
			'desc_color',
			array(
				'label'     => __( 'Description Colour', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#4a5a52',
				'separator' => 'before',
				'selectors' => array( '{{WRAPPER}} .zd-hc__desc' => 'color: {{VALUE}};' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'desc_typography',
				'selector' => '{{WRAPPER}} .zd-hc__desc',
			)
		);

		$this->add_responsive_control(
			'desc_width',
			array(
				'label'      => __( 'Description Max Width', 'zlaark-deals-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array( 'min' => 240, 'max' => 900 ),
					'%'  => array( 'min' => 20, 'max' => 100 ),
				),
				'default'    => array( 'unit' => 'px', 'size' => 520 ),
				'selectors'  => array( '{{WRAPPER}} .zd-hc__desc' => 'max-width: {{SIZE}}{{UNIT}};' ),
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

		$this->add_control(
			'primary_bg',
			array(
				'label'     => __( 'Primary Background', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#0a1310',
				'selectors' => array( '{{WRAPPER}} .zd-btn--solid' => 'background-color: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'primary_color',
			array(
				'label'     => __( 'Primary Text Colour', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .zd-btn--solid, {{WRAPPER}} .zd-btn--solid .zd-btn__label, {{WRAPPER}} .zd-btn--solid .zd-btn__arrow' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'secondary_color',
			array(
				'label'     => __( 'Secondary Text Colour', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#0a1310',
				'separator' => 'before',
				'condition' => array( 'secondary_text!' => '' ),
				'selectors' => array(
					'{{WRAPPER}} .zd-btn--ghost, {{WRAPPER}} .zd-btn--ghost .zd-btn__label' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'button_padding',
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
			'button_radius',
			array(
				'label'      => __( 'Radius', 'zlaark-deals-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 60 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 12 ),
				'selectors'  => array( '{{WRAPPER}} .zd-btn' => 'border-radius: {{SIZE}}{{UNIT}};' ),
			)
		);

		$this->end_controls_section();

		/* Media */
		$this->start_controls_section(
			'section_style_media',
			array(
				'label'     => __( 'Media', 'zlaark-deals-pro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'media_side!' => 'none' ),
			)
		);

		$this->add_responsive_control(
			'media_radius',
			array(
				'label'      => __( 'Frame Radius', 'zlaark-deals-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 60 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 24 ),
				'selectors'  => array( '{{WRAPPER}} .zd-hc__frame' => 'border-radius: {{SIZE}}{{UNIT}};' ),
			)
		);

		$this->add_responsive_control(
			'media_max',
			array(
				'label'      => __( 'Max Width', 'zlaark-deals-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array( 'min' => 200, 'max' => 1000 ),
					'%'  => array( 'min' => 30, 'max' => 100 ),
				),
				'default'    => array( 'unit' => '%', 'size' => 100 ),
				'selectors'  => array( '{{WRAPPER}} .zd-hc__media' => 'max-width: {{SIZE}}{{UNIT}};' ),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'media_shadow',
				'selector' => '{{WRAPPER}} .zd-hc__frame',
			)
		);

		$this->end_controls_section();
	}

	private function motion_section() {
		$this->start_controls_section(
			'section_hc_motion',
			array(
				'label' => __( 'Ambient Motion', 'zlaark-deals-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'bg_mesh',
			array(
				'label'        => __( 'Gradient Mesh', 'zlaark-deals-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'bg_grid',
			array(
				'label'        => __( 'Dot Grid', 'zlaark-deals-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'media_float',
			array(
				'label'        => __( 'Floating Media', 'zlaark-deals-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '',
				'return_value' => 'yes',
				'condition'    => array( 'media_side!' => 'none' ),
			)
		);

		$this->add_control(
			'media_parallax',
			array(
				'label'        => __( 'Cursor Parallax', 'zlaark-deals-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'condition'    => array( 'media_side!' => 'none' ),
			)
		);

		$this->add_control(
			'media_shine',
			array(
				'label'        => __( 'Frame Shine on Hover', 'zlaark-deals-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'condition'    => array( 'media_side!' => 'none' ),
			)
		);

		$this->add_control(
			'media_glow',
			array(
				'label'        => __( 'Glow Behind Media', 'zlaark-deals-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '',
				'return_value' => 'yes',
				'condition'    => array( 'media_side!' => 'none' ),
				'description'  => __( 'A soft blurred accent gradient behind the frame, for depth.', 'zlaark-deals-pro' ),
			)
		);

		$this->add_control(
			'magnetic',
			array(
				'label'        => __( 'Magnetic Buttons', 'zlaark-deals-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);

		$this->end_controls_section();
	}

	/* ----------------------------------------------------------------- render */

	protected function render() {
		$s    = $this->get_settings_for_display();
		$side = ! empty( $s['media_side'] ) ? $s['media_side'] : 'right';

		$classes = array( 'zd-hc', 'zd-hc--' . $side );
		if ( 'yes' === $s['bg_mesh'] ) {
			$classes[] = 'zd-hc--mesh';
		}
		if ( 'yes' === $s['bg_grid'] ) {
			$classes[] = 'zd-hc--grid';
		}

		$this->add_render_attribute( 'wrapper', 'class', $classes );

		$has_media = ( 'none' !== $side ) && ! empty( $s['image']['url'] );
		?>
		<div <?php $this->print_render_attribute_string( 'wrapper' ); ?>>

			<div class="zd-hc__backdrop" aria-hidden="true">
				<?php if ( 'yes' === $s['bg_mesh'] ) : ?>
					<span class="zd-hc__mesh zd-hc__mesh--1"></span>
					<span class="zd-hc__mesh zd-hc__mesh--2"></span>
				<?php endif; ?>
				<?php if ( 'yes' === $s['bg_grid'] ) : ?>
					<span class="zd-hc__dots"></span>
				<?php endif; ?>
			</div>

			<div class="zd-hc__inner">
				<div class="zd-hc__content">
					<?php $this->render_eyebrow( $s ); ?>
					<?php $this->render_title( $s ); ?>

					<?php if ( ! empty( $s['description'] ) ) : ?>
						<div class="zd-hc__desc">
							<?php echo wp_kses_post( wpautop( $s['description'] ) ); ?>
						</div>
					<?php endif; ?>

					<?php $this->render_features( $s ); ?>
					<?php $this->render_buttons( $s ); ?>
					<?php $this->render_proof( $s ); ?>
				</div>

				<?php if ( $has_media ) : ?>
					<?php $this->render_media( $s ); ?>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}

	private function render_eyebrow( $s ) {
		if ( empty( $s['eyebrow'] ) ) {
			return;
		}
		?>
		<p class="zd-hc__eyebrow">
			<?php if ( ! empty( $s['eyebrow_icon']['value'] ) ) : ?>
				<span class="zd-hc__eyebrow-icon">
					<?php Icons_Manager::render_icon( $s['eyebrow_icon'], array( 'aria-hidden' => 'true' ) ); ?>
				</span>
			<?php endif; ?>
			<?php echo esc_html( $s['eyebrow'] ); ?>
		</p>
		<?php
	}

	private function render_title( $s ) {
		if ( empty( $s['title'] ) && empty( $s['title_highlight'] ) ) {
			return;
		}

		$tag = ! empty( $s['title_tag'] ) ? $s['title_tag'] : 'h1';

		printf( '<%1$s class="zd-hc__title">', esc_attr( $tag ) );

		if ( ! empty( $s['title'] ) ) {
			echo esc_html( $s['title'] ) . ' ';
		}

		if ( ! empty( $s['title_highlight'] ) ) {
			$accent_class = 'zd-hc__word--accent';
			if ( 'yes' === $s['title_underline'] ) {
				$accent_class .= ' zd-hc__word--underline';
			}
			printf( '<span class="%1$s">%2$s</span>', esc_attr( $accent_class ), esc_html( $s['title_highlight'] ) );
		}

		printf( '</%1$s>', esc_attr( $tag ) );
	}

	private function render_features( $s ) {
		if ( empty( $s['features'] ) || ! is_array( $s['features'] ) ) {
			return;
		}
		?>
		<ul class="zd-hc__features zd-hc__features--<?php echo esc_attr( $s['features_layout'] ); ?>">
			<?php foreach ( $s['features'] as $item ) : ?>
				<li>
					<span class="zd-tick" aria-hidden="true">
						<svg viewBox="0 0 16 16" width="12" height="12" fill="none">
							<path d="M3 8.5l3.2 3.2L13 5" stroke="currentColor" stroke-width="2.2"
								stroke-linecap="round" stroke-linejoin="round" />
						</svg>
					</span>
					<?php echo esc_html( $item['text'] ); ?>
				</li>
			<?php endforeach; ?>
		</ul>
		<?php
	}

	private function render_buttons( $s ) {
		if ( empty( $s['primary_text'] ) && empty( $s['secondary_text'] ) ) {
			return;
		}
		?>
		<div class="zd-hc__actions">
			<?php
			$this->render_button( 'solid', $s['primary_text'], $s['primary_link'], $s, null );
			$this->render_button( 'ghost', $s['secondary_text'], $s['secondary_link'], $s, $s['secondary_icon'] );
			?>
		</div>
		<?php
	}

	private function render_button( $variant, $text, $link, $s, $icon ) {
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
			<?php if ( ! empty( $icon['value'] ) ) : ?>
				<span class="zd-btn__icon" aria-hidden="true">
					<?php Icons_Manager::render_icon( $icon, array( 'aria-hidden' => 'true' ) ); ?>
				</span>
			<?php endif; ?>
			<span class="zd-btn__label"><?php echo esc_html( $text ); ?></span>
			<?php if ( empty( $icon['value'] ) ) : ?>
				<span class="zd-btn__arrow" aria-hidden="true">
					<svg viewBox="0 0 16 16" width="14" height="14" fill="none">
						<path d="M2 8h11M9 4l4 4-4 4" stroke="currentColor" stroke-width="2"
							stroke-linecap="round" stroke-linejoin="round" />
					</svg>
				</span>
			<?php endif; ?>
		</a>
		<?php
	}

	private function render_proof( $s ) {
		if ( 'yes' !== $s['show_proof'] ) {
			return;
		}

		$stars = (float) $s['rating'];
		?>
		<div class="zd-hc__proof">
			<?php if ( ! empty( $s['avatars'] ) && is_array( $s['avatars'] ) ) : ?>
				<div class="zd-hc__avatars">
					<?php foreach ( array_slice( $s['avatars'], 0, 6 ) as $i => $avatar ) : ?>
						<?php if ( ! empty( $avatar['image']['url'] ) ) : ?>
							<span class="zd-hc__avatar" style="--zd-i:<?php echo (int) $i; ?>">
								<img src="<?php echo esc_url( $avatar['image']['url'] ); ?>" alt="" loading="lazy" />
							</span>
						<?php endif; ?>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>

			<div class="zd-hc__proof-text">
				<?php if ( $stars > 0 ) : ?>
					<span class="zd-hc__stars" role="img"
						aria-label="<?php echo esc_attr( sprintf( /* translators: %s: star rating */ __( '%s out of 5', 'zlaark-deals-pro' ), $stars ) ); ?>">
						<?php for ( $i = 1; $i <= 5; $i++ ) : ?>
							<?php
							// Half stars are clipped rather than drawn separately.
							$fill = min( 1, max( 0, $stars - ( $i - 1 ) ) ) * 100;
							?>
							<span class="zd-hc__star">
								<span class="zd-hc__star-fill" style="width:<?php echo esc_attr( $fill ); ?>%">★</span>
								<span class="zd-hc__star-bg">★</span>
							</span>
						<?php endfor; ?>
					</span>
				<?php endif; ?>
				<?php if ( '' !== $s['proof_text'] ) : ?>
					<span class="zd-hc__proof-label"><?php echo esc_html( $s['proof_text'] ); ?></span>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}

	private function render_media( $s ) {
		$classes = array( 'zd-hc__media' );
		if ( 'yes' === $s['media_float'] ) {
			$classes[] = 'zd-hc__media--float';
		}
		if ( 'yes' === $s['media_parallax'] ) {
			$classes[] = 'zd-parallax';
		}
		if ( 'yes' === $s['media_glow'] ) {
			$classes[] = 'zd-hc__media--glow';
		}

		$frame = array( 'zd-hc__frame', 'zd-hc__frame--' . $s['frame_style'] );
		if ( 'yes' === $s['media_shine'] ) {
			$frame[] = 'zd-shine';
		}
		?>
		<div class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
			<?php if ( 'yes' === $s['media_glow'] ) : ?>
				<span class="zd-hc__media-glow" aria-hidden="true"></span>
			<?php endif; ?>

			<div class="<?php echo esc_attr( implode( ' ', $frame ) ); ?>">
				<?php if ( 'window' === $s['frame_style'] ) : ?>
					<div class="zd-hc__chrome" aria-hidden="true">
						<span></span><span></span><span></span>
					</div>
				<?php endif; ?>

				<img src="<?php echo esc_url( $s['image']['url'] ); ?>"
					alt="<?php echo esc_attr( zlaark_deals_media_alt( $s['image'] ) ); ?>" />

				<span class="zd-card__shine" aria-hidden="true"></span>
			</div>

			<?php if ( 'yes' === $s['stat_show'] ) : ?>
				<div class="zd-hc__statcard">
					<?php if ( ! empty( $s['stat_icon']['value'] ) ) : ?>
						<span class="zd-hc__statcard-icon">
							<?php Icons_Manager::render_icon( $s['stat_icon'], array( 'aria-hidden' => 'true' ) ); ?>
						</span>
					<?php endif; ?>
					<span class="zd-hc__statcard-body">
						<span class="zd-hc__statcard-value">
							<span data-zd-count="<?php echo esc_attr( $s['stat_value'] ); ?>">0</span><?php echo esc_html( $s['stat_suffix'] ); ?>
						</span>
						<span class="zd-hc__statcard-label"><?php echo esc_html( $s['stat_label'] ); ?></span>
					</span>
				</div>
			<?php endif; ?>

			<?php if ( 'yes' === $s['badge_show'] && '' !== $s['badge_text'] ) : ?>
				<div class="zd-hc__livebadge">
					<span class="zd-hc__livedot" aria-hidden="true"></span>
					<?php echo esc_html( $s['badge_text'] ); ?>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}
}
