<?php
/**
 * Zlaark Hero Bento — a collage hero: floating cards wired together with
 * elbow connectors on one side, oversized display type on the other.
 *
 * The collage is laid out inside a fixed 540x700 stage. Every card is placed
 * as a percentage of that stage and the connector SVG shares the same
 * coordinate space, so the wiring stays locked to the cards at any width.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Icons_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use Elementor\Utils;

class Zlaark_Hero_Bento_Widget extends Zlaark_Widget_Base {

	/** Stage coordinate space the collage is composed in. */
	const STAGE_W = 540;
	const STAGE_H = 700;

	/**
	 * Decorative outline squares: [ left, top, width, height ] in stage units.
	 * These are the empty "ghost" tiles that fill the negative space.
	 */
	const GHOSTS = array(
		array( 40, 25, 97, 80 ),
		array( 383, 25, 36, 38 ),
		array( 120, 180, 80, 48 ),
		array( 42, 245, 42, 42 ),
		array( 80, 352, 48, 48 ),
		array( 262, 430, 100, 100 ),
		array( 395, 428, 58, 70 ),
		array( 375, 600, 32, 38 ),
		array( 390, 640, 68, 57 ),
		array( 466, 232, 46, 46 ),
		array( 18, 452, 34, 34 ),
		array( 292, 250, 26, 26 ),
	);

	/** Small accent dots: [ left, top, diameter ] in stage units. */
	const DOTS = array(
		array( 96, 148, 7 ),
		array( 288, 232, 5 ),
		array( 486, 372, 9 ),
		array( 66, 612, 6 ),
		array( 372, 512, 5 ),
		array( 236, 92, 6 ),
	);

	public function get_name() {
		return 'zlaark_hero_bento';
	}

	public function get_title() {
		return __( 'Zlaark Hero Bento', 'zlaark-deals-pro' );
	}

	public function get_icon() {
		return 'eicon-gallery-grid';
	}

	public function get_keywords() {
		return array( 'hero', 'bento', 'collage', 'grid', 'banner', 'zlaark' );
	}

	protected function register_controls() {
		$this->content_controls();
		$this->decor_controls();
		$this->collage_controls();
		$this->stats_controls();
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
				'label'       => __( 'Eyebrow Pill', 'zlaark-deals-pro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Now in public beta', 'zlaark-deals-pro' ),
				'label_block' => true,
				'description' => __( 'Small badge above the title. Leave empty to hide.', 'zlaark-deals-pro' ),
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
				'default'     => __( 'Future Through Cutting-Edge Technology.', 'zlaark-deals-pro' ),
				'label_block' => true,
				'description' => __( 'Each word animates in on its own.', 'zlaark-deals-pro' ),
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
				'rows'    => 3,
				'default' => __( 'By empowering users with greater control over their data, identity, and assets.', 'zlaark-deals-pro' ),
			)
		);

		$this->add_control(
			'primary_text',
			array(
				'label'     => __( 'Primary Button', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Get In Touch', 'zlaark-deals-pro' ),
				'separator' => 'before',
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
				'label'   => __( 'Secondary Button', 'zlaark-deals-pro' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Log In', 'zlaark-deals-pro' ),
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
			'collage_side',
			array(
				'label'     => __( 'Collage Side', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'left',
				'separator' => 'before',
				'options'   => array(
					'left'  => __( 'Collage left / text right', 'zlaark-deals-pro' ),
					'right' => __( 'Text left / collage right', 'zlaark-deals-pro' ),
				),
			)
		);

		$this->end_controls_section();
	}

	/* ------------------------------------------------------------------ decor */

	/**
	 * Ambient background layers plus the floating chips that sit around the
	 * text column — the depth that keeps the composition from feeling flat.
	 */
	private function decor_controls() {
		$this->start_controls_section(
			'section_decor',
			array( 'label' => __( 'Background & Chips', 'zlaark-deals-pro' ) )
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
			'bg_blooms',
			array(
				'label'        => __( 'Colour Blooms', 'zlaark-deals-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'description'  => __( 'Two soft gradient washes drifting behind everything.', 'zlaark-deals-pro' ),
			)
		);

		$this->add_control(
			'bg_ring',
			array(
				'label'        => __( 'Outline Ring', 'zlaark-deals-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'description'  => __( 'A large slow-rotating dashed circle behind the text.', 'zlaark-deals-pro' ),
			)
		);

		$this->add_control(
			'accent_dots',
			array(
				'label'        => __( 'Accent Dots in Collage', 'zlaark-deals-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'bloom_1',
			array(
				'label'     => __( 'Bloom Colour 1', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#0b7a4f',
				'condition' => array( 'bg_blooms' => 'yes' ),
				'selectors' => array( '{{WRAPPER}} .zd-bento' => '--zd-bloom-1: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'bloom_2',
			array(
				'label'     => __( 'Bloom Colour 2', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#22d3ee',
				'condition' => array( 'bg_blooms' => 'yes' ),
				'selectors' => array( '{{WRAPPER}} .zd-bento' => '--zd-bloom-2: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'heading_chips',
			array(
				'label'     => __( 'Floating Chips', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'text',
			array(
				'label'   => __( 'Label', 'zlaark-deals-pro' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'SOC 2 Certified', 'zlaark-deals-pro' ),
			)
		);

		$repeater->add_control(
			'icon',
			array(
				'label'   => __( 'Icon', 'zlaark-deals-pro' ),
				'type'    => Controls_Manager::ICONS,
				'default' => array(
					'value'   => 'fas fa-shield-alt',
					'library' => 'fa-solid',
				),
			)
		);

		$repeater->add_control(
			'accent',
			array(
				'label'   => __( 'Icon Colour', 'zlaark-deals-pro' ),
				'type'    => Controls_Manager::COLOR,
				'default' => '#0b7a4f',
			)
		);

		$this->add_control(
			'chips',
			array(
				'label'       => __( 'Chips', 'zlaark-deals-pro' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ text }}}',
				'description' => __( 'Up to three glass chips drift around the text column. Hidden on small screens.', 'zlaark-deals-pro' ),
				'default'     => array(
					array(
						'text'   => __( 'SOC 2 Certified', 'zlaark-deals-pro' ),
						'icon'   => array( 'value' => 'fas fa-shield-alt', 'library' => 'fa-solid' ),
						'accent' => '#0b7a4f',
					),
					array(
						'text'   => __( '99.99% Uptime', 'zlaark-deals-pro' ),
						'icon'   => array( 'value' => 'fas fa-bolt', 'library' => 'fa-solid' ),
						'accent' => '#f59e0b',
					),
				),
			)
		);

		$this->end_controls_section();
	}

	/* ---------------------------------------------------------------- collage */

	private function collage_controls() {
		$this->start_controls_section(
			'section_collage',
			array( 'label' => __( 'Collage', 'zlaark-deals-pro' ) )
		);

		/* --- card 1: the tall portrait tile --- */
		$this->add_control(
			'heading_card1',
			array(
				'label' => __( 'Card 1 — Portrait', 'zlaark-deals-pro' ),
				'type'  => Controls_Manager::HEADING,
			)
		);

		$this->add_control(
			'card1_image',
			array(
				'label'   => __( 'Image', 'zlaark-deals-pro' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => array( 'url' => Utils::get_placeholder_image_src() ),
			)
		);

		$this->add_control(
			'card1_bg',
			array(
				'label'     => __( 'Backing Color', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#c2410c',
				'selectors' => array( '{{WRAPPER}} .zd-bento__card--1' => 'background-color: {{VALUE}};' ),
			)
		);

		/* --- card 2: the big panel with an icon --- */
		$this->add_control(
			'heading_card2',
			array(
				'label'     => __( 'Card 2 — Large Panel', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'card2_image',
			array(
				'label'   => __( 'Image', 'zlaark-deals-pro' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => array( 'url' => Utils::get_placeholder_image_src() ),
			)
		);

		$this->add_control(
			'card2_icon',
			array(
				'label'   => __( 'Center Icon', 'zlaark-deals-pro' ),
				'type'    => Controls_Manager::ICONS,
				'default' => array(
					'value'   => 'fas fa-cube',
					'library' => 'fa-solid',
				),
			)
		);

		$this->add_control(
			'card2_divider',
			array(
				'label'        => __( 'Dashed Center Line', 'zlaark-deals-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);

		/* --- card 3: dark tile with a stat pill --- */
		$this->add_control(
			'heading_card3',
			array(
				'label'     => __( 'Card 3 — Stat Tile', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'card3_image',
			array(
				'label'   => __( 'Image', 'zlaark-deals-pro' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => array( 'url' => Utils::get_placeholder_image_src() ),
			)
		);

		$this->add_control(
			'card3_pill',
			array(
				'label'   => __( 'Pill Text', 'zlaark-deals-pro' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '68K+',
			)
		);

		$this->add_control(
			'card3_pill_icon',
			array(
				'label'   => __( 'Pill Icon', 'zlaark-deals-pro' ),
				'type'    => Controls_Manager::ICONS,
				'default' => array(
					'value'   => 'fas fa-eye',
					'library' => 'fa-solid',
				),
			)
		);

		$this->add_control(
			'card3_delta',
			array(
				'label'   => __( 'Delta Badge', 'zlaark-deals-pro' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '28% ↑',
			)
		);

		/* --- card 4: the wide bottom tile --- */
		$this->add_control(
			'heading_card4',
			array(
				'label'     => __( 'Card 4 — Wide Tile', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'card4_image',
			array(
				'label'   => __( 'Image', 'zlaark-deals-pro' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => array( 'url' => Utils::get_placeholder_image_src() ),
			)
		);

		/* --- floating icon tiles --- */
		$this->add_control(
			'heading_tiles',
			array(
				'label'     => __( 'Floating Icon Tiles', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'icon',
			array(
				'label'   => __( 'Icon', 'zlaark-deals-pro' ),
				'type'    => Controls_Manager::ICONS,
				'default' => array(
					'value'   => 'fas fa-tint',
					'library' => 'fa-solid',
				),
			)
		);

		$repeater->add_control(
			'bg',
			array(
				'label'   => __( 'Background', 'zlaark-deals-pro' ),
				'type'    => Controls_Manager::COLOR,
				'default' => '#fbbf24',
			)
		);

		$repeater->add_control(
			'color',
			array(
				'label'   => __( 'Icon Color', 'zlaark-deals-pro' ),
				'type'    => Controls_Manager::COLOR,
				'default' => '#ffffff',
			)
		);

		$this->add_control(
			'tiles',
			array(
				'label'       => __( 'Tiles', 'zlaark-deals-pro' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ icon.value }}}',
				'description' => __( 'Up to three tiles are placed into the collage, in order.', 'zlaark-deals-pro' ),
				'default'     => array(
					array(
						'icon' => array( 'value' => 'fas fa-tint', 'library' => 'fa-solid' ),
						'bg'   => '#fbbf24',
					),
					array(
						'icon' => array( 'value' => 'fas fa-code-branch', 'library' => 'fa-solid' ),
						'bg'   => '#111111',
					),
					array(
						'icon' => array( 'value' => 'fas fa-chart-bar', 'library' => 'fa-solid' ),
						'bg'   => '#0b7a4f',
					),
				),
			)
		);

		$this->add_control(
			'show_ghosts',
			array(
				'label'        => __( 'Outline Placeholder Squares', 'zlaark-deals-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'separator'    => 'before',
			)
		);

		$this->add_control(
			'show_wires',
			array(
				'label'        => __( 'Connector Wires', 'zlaark-deals-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);

		$this->end_controls_section();
	}

	private function stats_controls() {
		$this->start_controls_section(
			'section_stats',
			array( 'label' => __( 'Stats', 'zlaark-deals-pro' ) )
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'value',
			array(
				'label'   => __( 'Number', 'zlaark-deals-pro' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 100,
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
				'default' => __( 'Company Collaboration', 'zlaark-deals-pro' ),
			)
		);

		$this->add_control(
			'stats',
			array(
				'label'       => __( 'Stats', 'zlaark-deals-pro' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ value }}}{{{ suffix }}} — {{{ label }}}',
				'default'     => array(
					array(
						'value'  => 100,
						'suffix' => '+',
						'label'  => __( 'Company Collaboration', 'zlaark-deals-pro' ),
					),
					array(
						'value'  => 20,
						'suffix' => '+',
						'label'  => __( 'Winning Awards', 'zlaark-deals-pro' ),
					),
					array(
						'value'  => 300,
						'suffix' => 'K+',
						'label'  => __( 'Join Our Communities', 'zlaark-deals-pro' ),
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
				'label' => __( 'Section', 'zlaark-deals-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'bg',
				'selector' => '{{WRAPPER}} .zd-bento',
			)
		);

		$this->add_responsive_control(
			'padding',
			array(
				'label'      => __( 'Padding', 'zlaark-deals-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'default'    => array(
					'top'      => '60',
					'right'    => '40',
					'bottom'   => '60',
					'left'     => '40',
					'unit'     => 'px',
					'isLinked' => false,
				),
				'selectors'  => array(
					'{{WRAPPER}} .zd-bento' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
				'description' => __( 'Set to 100vh for a full-screen hero. The content stays vertically centred.', 'zlaark-deals-pro' ),
				'selectors'  => array( '{{WRAPPER}} .zd-bento' => 'min-height: {{SIZE}}{{UNIT}};' ),
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
				'selectors'  => array( '{{WRAPPER}} .zd-bento__inner' => 'max-width: {{SIZE}}{{UNIT}};' ),
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
				'selectors'  => array( '{{WRAPPER}} .zd-bento__inner' => 'gap: {{SIZE}}{{UNIT}};' ),
			)
		);

		$this->add_control(
			'stage_fit',
			array(
				'label'     => __( 'Collage Sizing', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'height',
				'separator' => 'before',
				'options'   => array(
					'height' => __( 'Fit to a height', 'zlaark-deals-pro' ),
					'width'  => __( 'Fill the column width', 'zlaark-deals-pro' ),
				),
				'description' => __( 'The collage keeps a fixed proportion. "Fit to a height" drives it from the height so the whole thing stays in one screen; "Fill the column width" lets it grow as tall as the column is wide.', 'zlaark-deals-pro' ),
			)
		);

		$this->add_responsive_control(
			'stage_height',
			array(
				'label'      => __( 'Collage Height', 'zlaark-deals-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'vh', 'px' ),
				'range'      => array(
					'vh' => array( 'min' => 30, 'max' => 100 ),
					'px' => array( 'min' => 260, 'max' => 1100 ),
				),
				'default'        => array( 'unit' => 'vh', 'size' => 74 ),
				'tablet_default' => array( 'unit' => 'vh', 'size' => 60 ),
				'mobile_default' => array( 'unit' => 'vh', 'size' => 52 ),
				'condition'  => array( 'stage_fit' => 'height' ),
				// Width is left to the aspect ratio; max-width still wins on a
				// narrow column, which shrinks the height to match.
				'selectors'  => array(
					'{{WRAPPER}} .zd-bento__stage' => 'height: {{SIZE}}{{UNIT}}; width: auto; max-width: 100%; justify-self: center; margin-inline: auto;',
				),
			)
		);

		$this->add_responsive_control(
			'collage_ratio',
			array(
				'label'      => __( 'Collage Column Width', 'zlaark-deals-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'fr' ),
				'range'      => array( 'fr' => array( 'min' => 0.5, 'max' => 2, 'step' => 0.05 ) ),
				'default'    => array( 'unit' => 'fr', 'size' => 0.95 ),
				'selectors'  => array( '{{WRAPPER}} .zd-bento__inner' => '--zd-bento-col: {{SIZE}}fr;' ),
			)
		);

		$this->add_control(
			'card_radius',
			array(
				'label'      => __( 'Card Radius', 'zlaark-deals-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 48 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 22 ),
				'separator'  => 'before',
				'selectors'  => array( '{{WRAPPER}} .zd-bento__stage' => '--zd-bento-radius: {{SIZE}}{{UNIT}};' ),
			)
		);

		$this->add_control(
			'wire_color',
			array(
				'label'     => __( 'Wire & Outline Color', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#111111',
				'selectors' => array( '{{WRAPPER}} .zd-bento__stage' => '--zd-wire: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'ghost_color',
			array(
				'label'     => __( 'Placeholder Outline Color', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#e9e9ec',
				'condition' => array( 'show_ghosts' => 'yes' ),
				'selectors' => array( '{{WRAPPER}} .zd-bento__stage' => '--zd-ghost: {{VALUE}};' ),
			)
		);

		$this->end_controls_section();

		/* Typography ---------------------------------------------------- */

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
				'label'     => __( 'Title Color', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#0a0a0a',
				'selectors' => array( '{{WRAPPER}} .zd-bento__title' => 'color: {{VALUE}};' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .zd-bento__title',
			)
		);

		$this->add_responsive_control(
			'title_size',
			array(
				'label'      => __( 'Title Size', 'zlaark-deals-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'vw' ),
				'range'      => array(
					'px' => array( 'min' => 28, 'max' => 130 ),
					'vw' => array( 'min' => 2, 'max' => 10, 'step' => 0.1 ),
				),
				'default'    => array( 'unit' => 'px', 'size' => 76 ),
				'selectors'  => array( '{{WRAPPER}} .zd-bento__title' => 'font-size: {{SIZE}}{{UNIT}};' ),
			)
		);

		$this->add_responsive_control(
			'title_word_gap',
			array(
				'label'      => __( 'Word Spacing', 'zlaark-deals-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'em' ),
				'range'      => array( 'em' => array( 'min' => -0.25, 'max' => 0.6, 'step' => 0.01 ) ),
				'default'    => array( 'unit' => 'em', 'size' => -0.02 ),
				'description' => __( 'Tightens or opens the gap between words. Negative values pull them closer.', 'zlaark-deals-pro' ),
				'selectors'  => array( '{{WRAPPER}} .zd-bento__title' => 'word-spacing: {{SIZE}}{{UNIT}};' ),
			)
		);

		$this->add_responsive_control(
			'title_line_height',
			array(
				'label'      => __( 'Line Height', 'zlaark-deals-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'em' ),
				'range'      => array( 'em' => array( 'min' => 0.8, 'max' => 1.6, 'step' => 0.01 ) ),
				'default'    => array( 'unit' => 'em', 'size' => 1.02 ),
				'selectors'  => array( '{{WRAPPER}} .zd-bento__title' => 'line-height: {{SIZE}}{{UNIT}};' ),
			)
		);

		$this->add_control(
			'desc_color',
			array(
				'label'     => __( 'Description Color', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#3f3f46',
				'separator' => 'before',
				'selectors' => array( '{{WRAPPER}} .zd-bento__desc' => 'color: {{VALUE}};' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'desc_typography',
				'selector' => '{{WRAPPER}} .zd-bento__desc',
			)
		);

		$this->add_responsive_control(
			'desc_width',
			array(
				'label'      => __( 'Description Max Width', 'zlaark-deals-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array( 'min' => 200, 'max' => 800 ),
					'%'  => array( 'min' => 20, 'max' => 100 ),
				),
				'default'    => array( 'unit' => 'px', 'size' => 400 ),
				'selectors'  => array( '{{WRAPPER}} .zd-bento__desc' => 'max-width: {{SIZE}}{{UNIT}};' ),
			)
		);

		$this->add_control(
			'stat_color',
			array(
				'label'     => __( 'Stat Number Color', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#0a0a0a',
				'separator' => 'before',
				'selectors' => array( '{{WRAPPER}} .zd-bento__stat-value' => 'color: {{VALUE}};' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'stat_typography',
				'selector' => '{{WRAPPER}} .zd-bento__stat-value',
			)
		);

		$this->add_control(
			'stat_label_color',
			array(
				'label'     => __( 'Stat Label Color', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#52525b',
				'selectors' => array( '{{WRAPPER}} .zd-bento__stat-label' => 'color: {{VALUE}};' ),
			)
		);

		$this->end_controls_section();

		/* Buttons ------------------------------------------------------- */

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
				'selector' => '{{WRAPPER}} .zd-pill',
			)
		);

		$this->add_control(
			'primary_bg',
			array(
				'label'     => __( 'Primary Background', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#0a0a0a',
				'selectors' => array( '{{WRAPPER}} .zd-pill--solid' => 'background-color: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'primary_color',
			array(
				'label'     => __( 'Primary Text Color', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array( '{{WRAPPER}} .zd-pill--solid, {{WRAPPER}} .zd-pill--solid .zd-pill__label, {{WRAPPER}} .zd-pill--solid .zd-pill__arrow' => 'color: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'secondary_color',
			array(
				'label'     => __( 'Secondary Text Color', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#0a0a0a',
				'separator' => 'before',
				'condition' => array( 'secondary_text!' => '' ),
				'selectors' => array( '{{WRAPPER}} .zd-pill--bare, {{WRAPPER}} .zd-pill--bare .zd-pill__label, {{WRAPPER}} .zd-pill--bare .zd-pill__arrow' => 'color: {{VALUE}};' ),
			)
		);

		$this->add_responsive_control(
			'pill_padding',
			array(
				'label'      => __( 'Primary Padding', 'zlaark-deals-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'separator'  => 'before',
				'selectors'  => array(
					'{{WRAPPER}} .zd-pill--solid' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/** Bento-specific ambient motion. */
	private function motion_section() {
		$this->start_controls_section(
			'section_bento_motion',
			array(
				'label' => __( 'Collage Motion', 'zlaark-deals-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'float_cards',
			array(
				'label'        => __( 'Floating Cards', 'zlaark-deals-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'description'  => __( 'Each card drifts on its own loop, slightly out of phase.', 'zlaark-deals-pro' ),
			)
		);

		$this->add_control(
			'draw_wires',
			array(
				'label'        => __( 'Draw Wires on Scroll', 'zlaark-deals-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'condition'    => array( 'show_wires' => 'yes' ),
			)
		);

		$this->add_control(
			'parallax',
			array(
				'label'        => __( 'Cursor Parallax', 'zlaark-deals-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'description'  => __( 'Cards shift by depth as the pointer moves across the collage.', 'zlaark-deals-pro' ),
			)
		);

		$this->add_control(
			'hover_pop',
			array(
				'label'        => __( 'Hover Pop', 'zlaark-deals-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'float_speed',
			array(
				'label'      => __( 'Float Speed', 'zlaark-deals-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 's' ),
				'range'      => array( 's' => array( 'min' => 3, 'max' => 20, 'step' => 0.5 ) ),
				'default'    => array( 'unit' => 's', 'size' => 7 ),
				'selectors'  => array( '{{WRAPPER}} .zd-bento__stage' => '--zd-float-speed: {{SIZE}}s;' ),
			)
		);

		$this->end_controls_section();
	}

	/* ----------------------------------------------------------------- render */

	/** Percent helpers so the stage coordinates above stay readable. */
	private function px( $v ) {
		return round( ( $v / self::STAGE_W ) * 100, 3 ) . '%';
	}

	private function py( $v ) {
		return round( ( $v / self::STAGE_H ) * 100, 3 ) . '%';
	}

	/**
	 * Inline placement for a stage rectangle.
	 *
	 * @param array $rect  [ left, top, width, height ] in stage units.
	 * @param int   $index Animation phase offset.
	 * @param float $depth Parallax multiplier — bigger moves further.
	 */
	private function place( $rect, $index = 0, $depth = 1 ) {
		return sprintf(
			'left:%s;top:%s;width:%s;height:%s;--zd-i:%d;--zd-depth:%s;',
			$this->px( $rect[0] ),
			$this->py( $rect[1] ),
			$this->px( $rect[2] ),
			$this->py( $rect[3] ),
			(int) $index,
			$depth
		);
	}

	protected function render() {
		$s = $this->get_settings_for_display();

		$classes = array( 'zd-bento', 'zd-bento--' . $s['collage_side'] );
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
			<?php $this->render_ambient( $s ); ?>

			<div class="zd-bento__inner">
				<?php $this->render_stage( $s ); ?>

				<div class="zd-bento__content">
					<?php $this->render_chips( $s ); ?>

					<?php if ( ! empty( $s['eyebrow'] ) ) : ?>
						<p class="zd-bento__eyebrow zd-reveal" data-zd-reveal="<?php echo esc_attr( $effect ); ?>">
							<?php if ( 'yes' === $s['eyebrow_dot'] ) : ?>
								<span class="zd-bento__eyebrow-dot" aria-hidden="true"></span>
							<?php endif; ?>
							<?php echo esc_html( $s['eyebrow'] ); ?>
						</p>
					<?php endif; ?>

					<?php $this->render_title( $s, $effect ); ?>

					<?php if ( ! empty( $s['description'] ) ) : ?>
						<div class="zd-bento__desc zd-reveal" data-zd-reveal="<?php echo esc_attr( $effect ); ?>">
							<?php echo wp_kses_post( wpautop( $s['description'] ) ); ?>
						</div>
					<?php endif; ?>

					<?php if ( ! empty( $s['primary_text'] ) || ! empty( $s['secondary_text'] ) ) : ?>
						<div class="zd-bento__actions zd-reveal" data-zd-reveal="<?php echo esc_attr( $effect ); ?>">
							<?php
							$this->render_pill( 'solid', $s['primary_text'], $s['primary_link'] );
							$this->render_pill( 'bare', $s['secondary_text'], $s['secondary_link'] );
							?>
						</div>
					<?php endif; ?>

					<?php $this->render_stats( $s, $effect ); ?>
				</div>
			</div>
		</div>
		<?php
	}

	/** Section-wide background layers, behind both columns. */
	private function render_ambient( $s ) {
		$on = array( 'bg_grid', 'bg_blooms', 'bg_ring' );
		$any = false;
		foreach ( $on as $flag ) {
			if ( 'yes' === $s[ $flag ] ) {
				$any = true;
			}
		}
		if ( ! $any ) {
			return;
		}
		?>
		<div class="zd-bento__ambient" aria-hidden="true">
			<?php if ( 'yes' === $s['bg_grid'] ) : ?>
				<span class="zd-bento__ambient-grid"></span>
			<?php endif; ?>

			<?php if ( 'yes' === $s['bg_blooms'] ) : ?>
				<span class="zd-bento__bloom zd-bento__bloom--1"></span>
				<span class="zd-bento__bloom zd-bento__bloom--2"></span>
			<?php endif; ?>

			<?php if ( 'yes' === $s['bg_ring'] ) : ?>
				<span class="zd-bento__ambient-ring"></span>
			<?php endif; ?>
		</div>
		<?php
	}

	/** Glass chips that drift around the text column. */
	private function render_chips( $s ) {
		if ( empty( $s['chips'] ) || ! is_array( $s['chips'] ) ) {
			return;
		}
		?>
		<div class="zd-bento__chips" aria-hidden="true">
			<?php foreach ( array_slice( $s['chips'], 0, 3 ) as $i => $chip ) : ?>
				<span class="zd-bento__chip zd-bento__chip--<?php echo (int) ( $i + 1 ); ?>"
					style="--zd-i:<?php echo (int) $i; ?>">
					<?php if ( ! empty( $chip['icon']['value'] ) ) : ?>
						<span class="zd-bento__chip-icon"
							style="color:<?php echo esc_attr( $chip['accent'] ); ?>">
							<?php Icons_Manager::render_icon( $chip['icon'], array( 'aria-hidden' => 'true' ) ); ?>
						</span>
					<?php endif; ?>
					<?php echo esc_html( $chip['text'] ); ?>
				</span>
			<?php endforeach; ?>
		</div>
		<?php
	}

	/* --- the collage --------------------------------------------------- */

	private function render_stage( $s ) {
		$stage_classes = array( 'zd-bento__stage' );
		if ( 'yes' === $s['float_cards'] ) {
			$stage_classes[] = 'zd-bento__stage--float';
		}
		if ( 'yes' === $s['hover_pop'] ) {
			$stage_classes[] = 'zd-bento__stage--pop';
		}
		if ( 'yes' === $s['parallax'] ) {
			$stage_classes[] = 'zd-bento__stage--parallax';
		}
		?>
		<div class="<?php echo esc_attr( implode( ' ', $stage_classes ) ); ?>"
			style="--zd-stage-w:<?php echo (int) self::STAGE_W; ?>;--zd-stage-h:<?php echo (int) self::STAGE_H; ?>;">

			<?php if ( 'yes' === $s['show_ghosts'] ) : ?>
				<?php foreach ( self::GHOSTS as $i => $rect ) : ?>
					<span class="zd-bento__ghost" aria-hidden="true"
						style="<?php echo esc_attr( $this->place( $rect, $i, 0.35 ) ); ?>"></span>
				<?php endforeach; ?>
			<?php endif; ?>

			<?php if ( 'yes' === $s['accent_dots'] ) : ?>
				<?php foreach ( self::DOTS as $i => $dot ) : ?>
					<span class="zd-bento__dot-accent" aria-hidden="true"
						style="<?php echo esc_attr( $this->place( array( $dot[0], $dot[1], $dot[2], $dot[2] ), $i, 2.6 ) ); ?>"></span>
				<?php endforeach; ?>
			<?php endif; ?>

			<?php if ( 'yes' === $s['show_wires'] ) : ?>
				<?php $this->render_wires( $s ); ?>
			<?php endif; ?>

			<!-- card 1: portrait -->
			<figure class="zd-bento__card zd-bento__card--1"
				style="<?php echo esc_attr( $this->place( array( 147, 68, 125, 122 ), 0, 1.6 ) ); ?>">
				<?php $this->card_image( $s['card1_image'] ); ?>
			</figure>

			<!-- card 2: large panel -->
			<figure class="zd-bento__card zd-bento__card--2"
				style="<?php echo esc_attr( $this->place( array( 305, 63, 192, 290 ), 1, 0.7 ) ); ?>">
				<?php $this->card_image( $s['card2_image'] ); ?>

				<?php if ( 'yes' === $s['card2_divider'] ) : ?>
					<span class="zd-bento__dash" aria-hidden="true"></span>
				<?php endif; ?>

				<?php if ( ! empty( $s['card2_icon']['value'] ) ) : ?>
					<span class="zd-bento__badge" aria-hidden="true">
						<?php Icons_Manager::render_icon( $s['card2_icon'], array( 'aria-hidden' => 'true' ) ); ?>
					</span>
				<?php endif; ?>
			</figure>

			<!-- card 3: dark stat tile -->
			<figure class="zd-bento__card zd-bento__card--3"
				style="<?php echo esc_attr( $this->place( array( 104, 300, 150, 190 ), 2, 1.9 ) ); ?>">
				<?php $this->card_image( $s['card3_image'] ); ?>

				<?php if ( '' !== $s['card3_pill'] || '' !== $s['card3_delta'] ) : ?>
					<div class="zd-bento__stat-pill">
						<?php if ( ! empty( $s['card3_pill_icon']['value'] ) ) : ?>
							<span class="zd-bento__stat-icon">
								<?php Icons_Manager::render_icon( $s['card3_pill_icon'], array( 'aria-hidden' => 'true' ) ); ?>
							</span>
						<?php endif; ?>
						<span class="zd-bento__stat-num"><?php echo esc_html( $s['card3_pill'] ); ?></span>
						<?php if ( '' !== $s['card3_delta'] ) : ?>
							<span class="zd-bento__stat-delta"><?php echo esc_html( $s['card3_delta'] ); ?></span>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			</figure>

			<!-- card 4: wide tile -->
			<figure class="zd-bento__card zd-bento__card--4"
				style="<?php echo esc_attr( $this->place( array( 62, 528, 190, 148 ), 3, 1.2 ) ); ?>">
				<?php $this->card_image( $s['card4_image'] ); ?>
			</figure>

			<?php $this->render_tiles( $s ); ?>
		</div>
		<?php
	}

	private function card_image( $media ) {
		if ( empty( $media['url'] ) ) {
			return;
		}
		printf(
			'<img src="%1$s" alt="%2$s" loading="lazy" />',
			esc_url( $media['url'] ),
			esc_attr( zlaark_deals_media_alt( $media ) )
		);
	}

	/** The three small icon squares, placed in fixed slots. */
	private function render_tiles( $s ) {
		if ( empty( $s['tiles'] ) || ! is_array( $s['tiles'] ) ) {
			return;
		}

		$slots = array(
			array( 305, 396, 54, 54 ),
			array( 410, 524, 52, 52 ),
			array( 289, 632, 50, 50 ),
		);

		foreach ( array_slice( $s['tiles'], 0, count( $slots ) ) as $i => $tile ) {
			$style = $this->place( $slots[ $i ], $i + 4, 2.2 );
			if ( ! empty( $tile['bg'] ) ) {
				$style .= 'background-color:' . $tile['bg'] . ';';
			}
			if ( ! empty( $tile['color'] ) ) {
				$style .= 'color:' . $tile['color'] . ';';
			}
			?>
			<span class="zd-bento__tile" aria-hidden="true" style="<?php echo esc_attr( $style ); ?>">
				<?php Icons_Manager::render_icon( $tile['icon'], array( 'aria-hidden' => 'true' ) ); ?>
			</span>
			<?php
		}
	}

	/**
	 * Elbow connectors between the cards, drawn in stage coordinates so they
	 * stay attached to the card edges at every width.
	 */
	private function render_wires( $s ) {
		$paths = array(
			// card 1 right edge -> card 2 left edge
			'M272 129 H305',
			// card 1 bottom -> card 3 top
			'M180 190 V300',
			// card 3 bottom -> card 4 top
			'M157 490 V528',
		);

		$draw = 'yes' === $s['draw_wires'] ? ' zd-bento__wires--draw' : '';
		?>
		<svg class="zd-bento__wires<?php echo esc_attr( $draw ); ?>"
			viewBox="0 0 <?php echo (int) self::STAGE_W; ?> <?php echo (int) self::STAGE_H; ?>"
			fill="none" aria-hidden="true">
			<?php foreach ( $paths as $i => $d ) : ?>
				<path d="<?php echo esc_attr( $d ); ?>" style="--zd-i:<?php echo (int) $i; ?>" />
			<?php endforeach; ?>
			<!-- junction nodes where a wire meets a card -->
			<rect x="266" y="123" width="12" height="12" rx="3" class="zd-bento__node" />
			<rect x="174" y="294" width="12" height="12" rx="3" class="zd-bento__node" />
		</svg>
		<?php
	}

	/* --- the text column ------------------------------------------------ */

	/** Splits the title into per-word spans so each can animate on its own delay. */
	private function render_title( $s, $effect ) {
		if ( empty( $s['title'] ) ) {
			return;
		}

		$tag   = ! empty( $s['title_tag'] ) ? $s['title_tag'] : 'h1';
		$words = preg_split( '/\s+/', trim( (string) $s['title'] ), -1, PREG_SPLIT_NO_EMPTY );

		printf( '<%1$s class="zd-bento__title" data-zd-reveal="%2$s">', esc_attr( $tag ), esc_attr( $effect ) );
		foreach ( $words as $i => $word ) {
			printf(
				'<span class="zd-word" style="--zd-i:%1$d"><span>%2$s</span></span> ',
				(int) $i,
				esc_html( $word )
			);
		}
		printf( '</%1$s>', esc_attr( $tag ) );
	}

	private function render_pill( $variant, $text, $link ) {
		if ( empty( $text ) ) {
			return;
		}

		$key = 'pill_' . $variant;
		$this->add_render_attribute( $key, 'class', array( 'zd-pill', 'zd-pill--' . $variant ) );

		if ( ! empty( $link['url'] ) ) {
			$this->add_link_attributes( $key, $link );
		}
		?>
		<a <?php $this->print_render_attribute_string( $key ); ?>>
			<span class="zd-pill__label"><?php echo esc_html( $text ); ?></span>
			<span class="zd-pill__arrow" aria-hidden="true">
				<svg viewBox="0 0 18 18" width="15" height="15" fill="none">
					<path d="M3 9h11M10 4.5L14.5 9 10 13.5" stroke="currentColor" stroke-width="1.8"
						stroke-linecap="round" stroke-linejoin="round" />
				</svg>
			</span>
		</a>
		<?php
	}

	private function render_stats( $s, $effect ) {
		if ( empty( $s['stats'] ) || ! is_array( $s['stats'] ) ) {
			return;
		}
		?>
		<div class="zd-bento__stats zd-reveal" data-zd-reveal="<?php echo esc_attr( $effect ); ?>">
			<?php foreach ( $s['stats'] as $i => $stat ) : ?>
				<div class="zd-bento__stat" style="--zd-i:<?php echo (int) $i; ?>">
					<p class="zd-bento__stat-value">
						<span data-zd-count="<?php echo esc_attr( $stat['value'] ); ?>">0</span><?php echo esc_html( $stat['suffix'] ); ?>
					</p>
					<p class="zd-bento__stat-label"><?php echo esc_html( $stat['label'] ); ?></p>
				</div>
			<?php endforeach; ?>
		</div>
		<?php
	}
}
