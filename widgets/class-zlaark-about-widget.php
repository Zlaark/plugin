<?php
/**
 * Zlaark About — a two-column "who we are" section: framed image collage
 * with an overlapping stat card on one side, eyebrow/title/copy, a mission
 * checklist and an inline stat row on the other.
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

class Zlaark_About_Widget extends Zlaark_Widget_Base {

	public function get_name() {
		return 'zlaark_about';
	}

	public function get_title() {
		return __( 'Zlaark About', 'zlaark-deals-pro' );
	}

	public function get_icon() {
		return 'eicon-info-circle-o';
	}

	public function get_keywords() {
		return array( 'about', 'story', 'mission', 'team', 'who we are', 'zlaark' );
	}

	protected function register_controls() {
		$this->content_controls();
		$this->points_controls();
		$this->stats_controls();
		$this->media_controls();
		$this->layout_controls();
		$this->style_controls();
		$this->animation_controls( false );
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
				'label'       => __( 'Eyebrow', 'zlaark-deals-pro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'About Zlaark', 'zlaark-deals-pro' ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'title',
			array(
				'label'       => __( 'Title', 'zlaark-deals-pro' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 3,
				'default'     => __( 'We test every deal', 'zlaark-deals-pro' ),
				'label_block' => true,
				'description' => __( 'Each word animates in on its own.', 'zlaark-deals-pro' ),
			)
		);

		$this->add_control(
			'title_highlight',
			array(
				'label'       => __( 'Highlighted Words', 'zlaark-deals-pro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'before we recommend it', 'zlaark-deals-pro' ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'title_underline',
			array(
				'label'        => __( 'Highlight Underline', 'zlaark-deals-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
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
				'default' => 'h2',
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
				'default' => __( 'We started Zlaark because every "best deals" page we found was really just an affiliate list. Ours is different: our team buys the tools, runs them for weeks, and only publishes a deal once the price and the product both check out.', 'zlaark-deals-pro' ),
			)
		);

		$this->add_control(
			'primary_text',
			array(
				'label'     => __( 'Button Text', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Our story', 'zlaark-deals-pro' ),
				'separator' => 'before',
			)
		);

		$this->add_control(
			'primary_link',
			array(
				'label'   => __( 'Button Link', 'zlaark-deals-pro' ),
				'type'    => Controls_Manager::URL,
				'default' => array( 'url' => '#' ),
			)
		);

		$this->end_controls_section();
	}

	private function points_controls() {
		$this->start_controls_section(
			'section_points',
			array( 'label' => __( 'Mission Points', 'zlaark-deals-pro' ) )
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'icon',
			array(
				'label'   => __( 'Icon', 'zlaark-deals-pro' ),
				'type'    => Controls_Manager::ICONS,
				'default' => array(
					'value'   => 'fas fa-check',
					'library' => 'fa-solid',
				),
			)
		);

		$repeater->add_control(
			'title',
			array(
				'label'   => __( 'Title', 'zlaark-deals-pro' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Independently tested', 'zlaark-deals-pro' ),
			)
		);

		$repeater->add_control(
			'text',
			array(
				'label'   => __( 'Text', 'zlaark-deals-pro' ),
				'type'    => Controls_Manager::TEXTAREA,
				'rows'    => 2,
				'default' => __( 'Every tool is used by our own team before it earns a place on the site.', 'zlaark-deals-pro' ),
			)
		);

		$this->add_control(
			'points',
			array(
				'label'       => __( 'Points', 'zlaark-deals-pro' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ title }}}',
				'default'     => array(
					array(
						'icon'  => array( 'value' => 'fas fa-flask', 'library' => 'fa-solid' ),
						'title' => __( 'Independently tested', 'zlaark-deals-pro' ),
						'text'  => __( 'Every tool is used by our own team before it earns a place on the site.', 'zlaark-deals-pro' ),
					),
					array(
						'icon'  => array( 'value' => 'fas fa-ban', 'library' => 'fa-solid' ),
						'title' => __( 'No paid placements', 'zlaark-deals-pro' ),
						'text'  => __( 'Ranking is never for sale — sponsors get labeled, not ranked higher.', 'zlaark-deals-pro' ),
					),
					array(
						'icon'  => array( 'value' => 'fas fa-rotate', 'library' => 'fa-solid' ),
						'title' => __( 'Refreshed monthly', 'zlaark-deals-pro' ),
						'text'  => __( 'Prices and offers are re-checked every month, and stale deals are pulled.', 'zlaark-deals-pro' ),
					),
				),
			)
		);

		$this->end_controls_section();
	}

	private function stats_controls() {
		$this->start_controls_section(
			'section_stats',
			array( 'label' => __( 'Stat Row', 'zlaark-deals-pro' ) )
		);

		$this->add_control(
			'show_stats',
			array(
				'label'        => __( 'Show', 'zlaark-deals-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'value',
			array(
				'label'   => __( 'Number', 'zlaark-deals-pro' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 12,
			)
		);

		$repeater->add_control(
			'suffix',
			array(
				'label' => __( 'Suffix', 'zlaark-deals-pro' ),
				'type'  => Controls_Manager::TEXT,
			)
		);

		$repeater->add_control(
			'label',
			array(
				'label'   => __( 'Label', 'zlaark-deals-pro' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Years testing software', 'zlaark-deals-pro' ),
			)
		);

		$this->add_control(
			'stats',
			array(
				'label'       => __( 'Stats', 'zlaark-deals-pro' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ value }}}{{{ suffix }}} — {{{ label }}}',
				'condition'   => array( 'show_stats' => 'yes' ),
				'default'     => array(
					array(
						'value'  => 12,
						'label'  => __( 'Years testing software', 'zlaark-deals-pro' ),
					),
					array(
						'value'  => 120,
						'suffix' => '+',
						'label'  => __( 'Deals reviewed', 'zlaark-deals-pro' ),
					),
					array(
						'value'  => 40,
						'suffix' => '%',
						'label'  => __( 'Average saving found', 'zlaark-deals-pro' ),
					),
				),
			)
		);

		$this->end_controls_section();
	}

	private function media_controls() {
		$this->start_controls_section(
			'section_media',
			array( 'label' => __( 'Image', 'zlaark-deals-pro' ) )
		);

		$this->add_control(
			'media_style',
			array(
				'label'   => __( 'Media Style', 'zlaark-deals-pro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'collage',
				'options' => array(
					'collage' => __( 'Collage — two overlapping images', 'zlaark-deals-pro' ),
					'single'  => __( 'Single framed image', 'zlaark-deals-pro' ),
					'stacked' => __( 'Stacked — two layered photos', 'zlaark-deals-pro' ),
					'shape'   => __( 'Shape — single image on a blurred blob with a dashed frame', 'zlaark-deals-pro' ),
					'grid'    => __( 'Grid — four images in a 2×2 grid', 'zlaark-deals-pro' ),
				),
			)
		);

		$this->add_control(
			'image',
			array(
				'label'   => __( 'Main Image', 'zlaark-deals-pro' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => array( 'url' => Utils::get_placeholder_image_src() ),
			)
		);

		$this->add_control(
			'image_2',
			array(
				'label'     => __( 'Second Image', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::MEDIA,
				'default'   => array( 'url' => Utils::get_placeholder_image_src() ),
				'condition' => array( 'media_style' => array( 'collage', 'stacked', 'grid' ) ),
			)
		);

		$this->add_control(
			'image_3',
			array(
				'label'     => __( 'Third Image', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::MEDIA,
				'default'   => array( 'url' => Utils::get_placeholder_image_src() ),
				'condition' => array( 'media_style' => 'grid' ),
			)
		);

		$this->add_control(
			'image_4',
			array(
				'label'     => __( 'Fourth Image', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::MEDIA,
				'default'   => array( 'url' => Utils::get_placeholder_image_src() ),
				'condition' => array( 'media_style' => 'grid' ),
			)
		);

		$this->add_responsive_control(
			'image_height',
			array(
				'label'       => __( 'Image Height', 'zlaark-deals-pro' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( 'px', 'vh' ),
				'range'       => array(
					'px' => array( 'min' => 200, 'max' => 900 ),
					'vh' => array( 'min' => 20, 'max' => 100 ),
				),
				'default'     => array( 'unit' => 'px', 'size' => '' ),
				'separator'   => 'before',
				'description' => __( 'Leave empty to size the image by its natural proportion. Set a value to lock every frame to this height and crop the photo to fill it.', 'zlaark-deals-pro' ),
				'selectors'   => array(
					'{{WRAPPER}} .zd-about' => '--zd-about-img-h: {{SIZE}}{{UNIT}};',
				),
			)
		);

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
			'stat_icon',
			array(
				'label'     => __( 'Icon', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::ICONS,
				'condition' => array( 'stat_show' => 'yes' ),
				'default'   => array(
					'value'   => 'fas fa-users',
					'library' => 'fa-solid',
				),
			)
		);

		$this->add_control(
			'stat_value',
			array(
				'label'     => __( 'Value', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 2400,
				'condition' => array( 'stat_show' => 'yes' ),
			)
		);

		$this->add_control(
			'stat_suffix',
			array(
				'label'     => __( 'Suffix', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => '+',
				'condition' => array( 'stat_show' => 'yes' ),
			)
		);

		$this->add_control(
			'stat_label',
			array(
				'label'     => __( 'Label', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Teams trust us', 'zlaark-deals-pro' ),
				'condition' => array( 'stat_show' => 'yes' ),
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
				'default' => 'left',
				'options' => array(
					'left'  => __( 'Media left / text right', 'zlaark-deals-pro' ),
					'right' => __( 'Text left / media right', 'zlaark-deals-pro' ),
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
				'default'    => array( 'unit' => 'px', 'size' => 1480 ),
				'selectors'  => array( '{{WRAPPER}} .zd-about__inner' => 'max-width: {{SIZE}}{{UNIT}};' ),
			)
		);

		$this->add_responsive_control(
			'gap',
			array(
				'label'      => __( 'Column Gap', 'zlaark-deals-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 200 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 80 ),
				'selectors'  => array( '{{WRAPPER}} .zd-about__inner' => 'gap: {{SIZE}}{{UNIT}};' ),
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
				'selector' => '{{WRAPPER}} .zd-about',
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
					'{{WRAPPER}} .zd-about' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'accent',
			array(
				'label'     => __( 'Accent Colour', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#4f46e5',
				'separator' => 'before',
				'selectors' => array( '{{WRAPPER}} .zd-about' => '--zd-accent: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'accent_2',
			array(
				'label'     => __( 'Accent Colour 2', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#06b6d4',
				'selectors' => array( '{{WRAPPER}} .zd-about' => '--zd-accent-2: {{VALUE}};' ),
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
				'label'     => __( 'Eyebrow Colour', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#4f46e5',
				'selectors' => array( '{{WRAPPER}} .zd-about__eyebrow' => 'color: {{VALUE}};' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'eyebrow_typography',
				'selector' => '{{WRAPPER}} .zd-about__eyebrow',
			)
		);

		$this->add_control(
			'title_color',
			array(
				'label'     => __( 'Title Colour', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#0b1120',
				'separator' => 'before',
				'selectors' => array( '{{WRAPPER}} .zd-about__title' => 'color: {{VALUE}};' ),
			)
		);

		$this->add_responsive_control(
			'title_size',
			array(
				'label'      => __( 'Title Size', 'zlaark-deals-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'vw' ),
				'range'      => array(
					'px' => array( 'min' => 24, 'max' => 90 ),
					'vw' => array( 'min' => 2, 'max' => 7, 'step' => 0.1 ),
				),
				'default'    => array( 'unit' => 'px', 'size' => 44 ),
				'selectors'  => array( '{{WRAPPER}} .zd-about__title' => 'font-size: {{SIZE}}{{UNIT}};' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .zd-about__title',
			)
		);

		$this->add_control(
			'desc_color',
			array(
				'label'     => __( 'Description Colour', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#4b5563',
				'separator' => 'before',
				'selectors' => array( '{{WRAPPER}} .zd-about__desc' => 'color: {{VALUE}};' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'desc_typography',
				'selector' => '{{WRAPPER}} .zd-about__desc',
			)
		);

		$this->end_controls_section();

		/* Points */
		$this->start_controls_section(
			'section_style_points',
			array(
				'label' => __( 'Mission Points', 'zlaark-deals-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'point_icon_bg',
			array(
				'label'     => __( 'Icon Background', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#eef2ff',
				'selectors' => array( '{{WRAPPER}} .zd-about__point-icon' => 'background-color: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'point_icon_color',
			array(
				'label'     => __( 'Icon Colour', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#4f46e5',
				'selectors' => array( '{{WRAPPER}} .zd-about__point-icon' => 'color: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'point_title_color',
			array(
				'label'     => __( 'Point Title Colour', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#0b1120',
				'separator' => 'before',
				'selectors' => array( '{{WRAPPER}} .zd-about__point-title' => 'color: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'point_text_color',
			array(
				'label'     => __( 'Point Text Colour', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#6b7280',
				'selectors' => array( '{{WRAPPER}} .zd-about__point-text' => 'color: {{VALUE}};' ),
			)
		);

		$this->end_controls_section();

		/* Stats */
		$this->start_controls_section(
			'section_style_stats',
			array(
				'label'     => __( 'Stat Row', 'zlaark-deals-pro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'show_stats' => 'yes' ),
			)
		);

		$this->add_control(
			'stat_value_color',
			array(
				'label'     => __( 'Number Colour', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#0b1120',
				'selectors' => array( '{{WRAPPER}} .zd-about__stat-value' => 'color: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'stat_label_color',
			array(
				'label'     => __( 'Label Colour', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#6b7280',
				'selectors' => array( '{{WRAPPER}} .zd-about__stat-label' => 'color: {{VALUE}};' ),
			)
		);

		$this->end_controls_section();

		/* Buttons */
		$this->start_controls_section(
			'section_style_buttons',
			array(
				'label' => __( 'Button', 'zlaark-deals-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'primary_bg',
			array(
				'label'     => __( 'Background', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#0b1120',
				'selectors' => array( '{{WRAPPER}} .zd-about .zd-btn--solid' => 'background-color: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'primary_color',
			array(
				'label'     => __( 'Text Colour', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .zd-about .zd-btn--solid, {{WRAPPER}} .zd-about .zd-btn--solid .zd-btn__label, {{WRAPPER}} .zd-about .zd-btn--solid .zd-btn__arrow' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		/* Media */
		$this->start_controls_section(
			'section_style_media',
			array(
				'label' => __( 'Image', 'zlaark-deals-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
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
				'selectors'  => array( '{{WRAPPER}} .zd-about__frame' => 'border-radius: {{SIZE}}{{UNIT}};' ),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'media_shadow',
				'selector' => '{{WRAPPER}} .zd-about__frame',
			)
		);

		$this->end_controls_section();
	}

	private function motion_section() {
		$this->start_controls_section(
			'section_about_motion',
			array(
				'label' => __( 'Ambient Motion', 'zlaark-deals-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'bg_grid',
			array(
				'label'        => __( 'Dot Grid Backdrop', 'zlaark-deals-pro' ),
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
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'media_parallax',
			array(
				'label'        => __( 'Cursor Parallax', 'zlaark-deals-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'magnetic',
			array(
				'label'        => __( 'Magnetic Button', 'zlaark-deals-pro' ),
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
		$side = ! empty( $s['media_side'] ) ? $s['media_side'] : 'left';

		$classes = array( 'zd-about', 'zd-about--' . $side );
		if ( 'yes' === $s['bg_grid'] ) {
			$classes[] = 'zd-about--grid';
		}

		$this->add_render_attribute( 'wrapper', 'class', $classes );
		$this->add_render_attribute( 'wrapper', 'data-zd-reveal-root', 'true' );
		$this->add_render_attribute(
			'wrapper',
			'data-zd-stagger',
			isset( $s['reveal_stagger']['size'] ) ? (int) $s['reveal_stagger']['size'] : 90
		);

		$effect = ! empty( $s['reveal_effect'] ) ? $s['reveal_effect'] : 'rise';
		?>
		<div <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
			<?php if ( 'yes' === $s['bg_grid'] ) : ?>
				<span class="zd-about__dots" aria-hidden="true"></span>
			<?php endif; ?>

			<div class="zd-about__inner">
				<?php $this->render_media( $s ); ?>

				<div class="zd-about__content">
					<?php if ( ! empty( $s['eyebrow'] ) ) : ?>
						<p class="zd-about__eyebrow zd-reveal" data-zd-reveal="<?php echo esc_attr( $effect ); ?>">
							<?php echo esc_html( $s['eyebrow'] ); ?>
						</p>
					<?php endif; ?>

					<?php $this->render_title( $s, $effect ); ?>

					<?php if ( ! empty( $s['description'] ) ) : ?>
						<div class="zd-about__desc zd-reveal" data-zd-reveal="<?php echo esc_attr( $effect ); ?>">
							<?php echo wp_kses_post( wpautop( $s['description'] ) ); ?>
						</div>
					<?php endif; ?>

					<?php $this->render_points( $s, $effect ); ?>

					<?php if ( ! empty( $s['primary_text'] ) ) : ?>
						<div class="zd-about__actions zd-reveal" data-zd-reveal="<?php echo esc_attr( $effect ); ?>">
							<?php $this->render_button( $s ); ?>
						</div>
					<?php endif; ?>

					<?php $this->render_stats( $s, $effect ); ?>
				</div>
			</div>
		</div>
		<?php
	}

	/** Splits the title into per-word spans so each can animate on its own delay. */
	private function render_title( $s, $effect ) {
		if ( empty( $s['title'] ) && empty( $s['title_highlight'] ) ) {
			return;
		}

		$tag   = ! empty( $s['title_tag'] ) ? $s['title_tag'] : 'h2';
		$words = preg_split( '/\s+/', trim( (string) $s['title'] ), -1, PREG_SPLIT_NO_EMPTY );

		printf( '<%1$s class="zd-about__title" data-zd-reveal="%2$s">', esc_attr( $tag ), esc_attr( $effect ) );

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

	private function render_points( $s, $effect ) {
		if ( empty( $s['points'] ) || ! is_array( $s['points'] ) ) {
			return;
		}
		?>
		<div class="zd-about__points zd-reveal" data-zd-reveal="<?php echo esc_attr( $effect ); ?>">
			<?php foreach ( $s['points'] as $i => $point ) : ?>
				<div class="zd-about__point" style="--zd-i:<?php echo (int) $i; ?>">
					<?php if ( ! empty( $point['icon']['value'] ) ) : ?>
						<span class="zd-about__point-icon">
							<?php Icons_Manager::render_icon( $point['icon'], array( 'aria-hidden' => 'true' ) ); ?>
						</span>
					<?php endif; ?>
					<span class="zd-about__point-body">
						<?php if ( '' !== $point['title'] ) : ?>
							<span class="zd-about__point-title"><?php echo esc_html( $point['title'] ); ?></span>
						<?php endif; ?>
						<?php if ( '' !== $point['text'] ) : ?>
							<span class="zd-about__point-text"><?php echo esc_html( $point['text'] ); ?></span>
						<?php endif; ?>
					</span>
				</div>
			<?php endforeach; ?>
		</div>
		<?php
	}

	private function render_button( $s ) {
		$classes = array( 'zd-btn', 'zd-btn--solid' );
		if ( 'yes' === $s['magnetic'] ) {
			$classes[] = 'zd-magnetic';
		}

		$this->add_render_attribute( 'btn', 'class', $classes );
		if ( ! empty( $s['primary_link']['url'] ) ) {
			$this->add_link_attributes( 'btn', $s['primary_link'] );
		}
		?>
		<a <?php $this->print_render_attribute_string( 'btn' ); ?>>
			<span class="zd-btn__label"><?php echo esc_html( $s['primary_text'] ); ?></span>
			<span class="zd-btn__arrow" aria-hidden="true">
				<svg viewBox="0 0 16 16" width="14" height="14" fill="none">
					<path d="M2 8h11M9 4l4 4-4 4" stroke="currentColor" stroke-width="2"
						stroke-linecap="round" stroke-linejoin="round" />
				</svg>
			</span>
		</a>
		<?php
	}

	private function render_stats( $s, $effect ) {
		if ( 'yes' !== $s['show_stats'] || empty( $s['stats'] ) || ! is_array( $s['stats'] ) ) {
			return;
		}
		?>
		<div class="zd-about__stats zd-reveal" data-zd-reveal="<?php echo esc_attr( $effect ); ?>">
			<?php foreach ( $s['stats'] as $i => $stat ) : ?>
				<div class="zd-about__stat" style="--zd-i:<?php echo (int) $i; ?>">
					<p class="zd-about__stat-value">
						<span data-zd-count="<?php echo esc_attr( $stat['value'] ); ?>">0</span><?php echo esc_html( $stat['suffix'] ); ?>
					</p>
					<p class="zd-about__stat-label"><?php echo esc_html( $stat['label'] ); ?></p>
				</div>
			<?php endforeach; ?>
		</div>
		<?php
	}

	/** One <img>-in-frame block, reused by every media style below. */
	private function render_frame( $image, $extra_class = '' ) {
		if ( empty( $image['url'] ) ) {
			return;
		}
		?>
		<div class="zd-about__frame zd-shine <?php echo esc_attr( $extra_class ); ?>">
			<img src="<?php echo esc_url( $image['url'] ); ?>"
				alt="<?php echo esc_attr( zlaark_deals_media_alt( $image ) ); ?>" loading="lazy" />
			<span class="zd-card__shine" aria-hidden="true"></span>
		</div>
		<?php
	}

	private function render_media( $s ) {
		$style   = ! empty( $s['media_style'] ) ? $s['media_style'] : 'collage';
		$classes = array( 'zd-about__media', 'zd-reveal', 'zd-about__media--' . $style );
		if ( 'yes' === $s['media_float'] ) {
			$classes[] = 'zd-about__media--float';
		}
		if ( 'yes' === $s['media_parallax'] ) {
			$classes[] = 'zd-parallax';
		}
		?>
		<div class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>" data-zd-reveal="zoom">

			<?php if ( 'grid' === $style ) : ?>

				<?php
				$grid_images = array( $s['image'], $s['image_2'], $s['image_3'], $s['image_4'] );
				foreach ( $grid_images as $g => $img ) :
					if ( empty( $img['url'] ) ) {
						continue;
					}
					?>
					<div class="zd-about__frame zd-about__frame--grid zd-shine zd-reveal"
						data-zd-reveal="zoom" style="--zd-i:<?php echo (int) $g; ?>">
						<img src="<?php echo esc_url( $img['url'] ); ?>"
							alt="<?php echo esc_attr( zlaark_deals_media_alt( $img ) ); ?>" loading="lazy" />
						<span class="zd-card__shine" aria-hidden="true"></span>
					</div>
				<?php endforeach; ?>

			<?php elseif ( 'shape' === $style ) : ?>

				<span class="zd-about__blob" aria-hidden="true"></span>
				<span class="zd-about__ring" aria-hidden="true"></span>
				<?php $this->render_frame( $s['image'], 'zd-about__frame--main' ); ?>

			<?php else : // collage | single | stacked ?>

				<?php $this->render_frame( $s['image'], 'zd-about__frame--main' ); ?>

				<?php if ( in_array( $style, array( 'collage', 'stacked' ), true ) && ! empty( $s['image_2']['url'] ) ) : ?>
					<?php $this->render_frame( $s['image_2'], 'zd-about__frame--second' ); ?>
				<?php endif; ?>

			<?php endif; ?>

			<?php if ( 'yes' === $s['stat_show'] ) : ?>
				<div class="zd-about__statcard">
					<?php if ( ! empty( $s['stat_icon']['value'] ) ) : ?>
						<span class="zd-about__statcard-icon">
							<?php Icons_Manager::render_icon( $s['stat_icon'], array( 'aria-hidden' => 'true' ) ); ?>
						</span>
					<?php endif; ?>
					<span class="zd-about__statcard-body">
						<span class="zd-about__statcard-value">
							<span data-zd-count="<?php echo esc_attr( $s['stat_value'] ); ?>">0</span><?php echo esc_html( $s['stat_suffix'] ); ?>
						</span>
						<span class="zd-about__statcard-label"><?php echo esc_html( $s['stat_label'] ); ?></span>
					</span>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}
}
