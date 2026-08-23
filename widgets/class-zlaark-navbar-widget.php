<?php
/**
 * Zlaark Navbar - logo, a centred pill menu with a sliding active indicator,
 * and a text link plus solid CTA on the right.
 *
 * Menu items come either from a repeater or from any WordPress menu; both
 * render the same markup so the indicator logic doesn't care which is used.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Utils;

class Zlaark_Navbar_Widget extends Zlaark_Widget_Base {

	public function get_name() {
		return 'zlaark_navbar';
	}

	public function get_title() {
		return __( 'Zlaark Navbar', 'zlaark-deals-pro' );
	}

	public function get_icon() {
		return 'eicon-nav-menu';
	}

	public function get_keywords() {
		return array( 'nav', 'navbar', 'menu', 'header', 'navigation', 'zlaark' );
	}

	protected function register_controls() {
		$this->brand_controls();
		$this->menu_controls();
		$this->mega_controls();
		$this->action_controls();
		$this->layout_controls();
		$this->style_controls();
		$this->motion_section();
	}

	/* ------------------------------------------------------------------ brand */

	private function brand_controls() {
		$this->start_controls_section(
			'section_brand',
			array( 'label' => __( 'Brand', 'zlaark-deals-pro' ) )
		);

		$this->add_control(
			'logo_type',
			array(
				'label'   => __( 'Logo Type', 'zlaark-deals-pro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'text',
				'options' => array(
					'text'  => __( 'Text', 'zlaark-deals-pro' ),
					'image' => __( 'Image', 'zlaark-deals-pro' ),
				),
			)
		);

		$this->add_control(
			'logo_text',
			array(
				'label'     => __( 'Logo Text', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => get_bloginfo( 'name' ),
				'condition' => array( 'logo_type' => 'text' ),
			)
		);

		$this->add_control(
			'logo_image',
			array(
				'label'     => __( 'Logo Image', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::MEDIA,
				'default'   => array( 'url' => Utils::get_placeholder_image_src() ),
				'condition' => array( 'logo_type' => 'image' ),
			)
		);

		$this->add_responsive_control(
			'logo_height',
			array(
				'label'      => __( 'Logo Height', 'zlaark-deals-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 16, 'max' => 90 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 34 ),
				'condition'  => array( 'logo_type' => 'image' ),
				'selectors'  => array( '{{WRAPPER}} .zd-nav__logo img' => 'height: {{SIZE}}{{UNIT}};' ),
			)
		);

		$this->add_control(
			'logo_link',
			array(
				'label'   => __( 'Logo Link', 'zlaark-deals-pro' ),
				'type'    => Controls_Manager::URL,
				'default' => array( 'url' => home_url( '/' ) ),
			)
		);

		$this->end_controls_section();
	}

	/* ------------------------------------------------------------------- menu */

	private function menu_controls() {
		$this->start_controls_section(
			'section_menu',
			array( 'label' => __( 'Menu', 'zlaark-deals-pro' ) )
		);

		$this->add_control(
			'menu_source',
			array(
				'label'   => __( 'Menu Source', 'zlaark-deals-pro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'manual',
				'options' => array(
					'manual' => __( 'Manual list', 'zlaark-deals-pro' ),
					'wp'     => __( 'WordPress menu', 'zlaark-deals-pro' ),
				),
			)
		);

		$menus = $this->get_menu_options();

		if ( empty( $menus ) ) {
			$this->add_control(
				'no_menus_notice',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					'raw'             => __( 'No WordPress menus exist yet. Create one under <strong>Appearance &rarr; Menus</strong>.', 'zlaark-deals-pro' ),
					'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
					'condition'       => array( 'menu_source' => 'wp' ),
				)
			);
		}

		$this->add_control(
			'wp_menu',
			array(
				'label'     => __( 'Choose Menu', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => $menus,
				'default'   => key( $menus ),
				'condition' => array( 'menu_source' => 'wp' ),
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'text',
			array(
				'label'   => __( 'Text', 'zlaark-deals-pro' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Menu item', 'zlaark-deals-pro' ),
			)
		);

		$repeater->add_control(
			'link',
			array(
				'label'   => __( 'Link', 'zlaark-deals-pro' ),
				'type'    => Controls_Manager::URL,
				'default' => array( 'url' => '#' ),
			)
		);

		$repeater->add_control(
			'is_active',
			array(
				'label'        => __( 'Mark as Active', 'zlaark-deals-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'description'  => __( 'The indicator pill rests on this item.', 'zlaark-deals-pro' ),
			)
		);

		$repeater->add_control(
			'mega',
			array(
				'label'        => __( 'Opens Mega Panel', 'zlaark-deals-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'description'  => __( 'Builds the panel from the Mega Panel section, matching on this item\'s text.', 'zlaark-deals-pro' ),
			)
		);

		$this->add_control(
			'items',
			array(
				'label'       => __( 'Items', 'zlaark-deals-pro' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ text }}}',
				'condition'   => array( 'menu_source' => 'manual' ),
				/*
				 * The plugin's own information architecture, not a SaaS
				 * template's. A navbar that ships "Features / Assets / Pricing"
				 * next to a deals catalogue reads as somebody else's demo, and
				 * that is exactly how it looked on the page.
				 */
				'default'     => array(
					array( 'text' => __( 'Deals', 'zlaark-deals-pro' ), 'is_active' => 'yes' ),
					array( 'text' => __( 'Hosting', 'zlaark-deals-pro' ), 'mega' => 'yes' ),
					array( 'text' => __( 'Reviews', 'zlaark-deals-pro' ) ),
					array( 'text' => __( 'Compare', 'zlaark-deals-pro' ) ),
					array( 'text' => __( 'How we test', 'zlaark-deals-pro' ) ),
				),
			)
		);

		$this->add_control(
			'auto_active',
			array(
				'label'        => __( 'Auto-detect Current Page', 'zlaark-deals-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'separator'    => 'before',
				'description'  => __( 'Highlights the item whose link matches the page being viewed.', 'zlaark-deals-pro' ),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Mega panel content.
	 *
	 * Elementor cannot nest a repeater inside a repeater, so the columns live
	 * in one flat list and each row names the menu item it belongs to. One
	 * repeater can therefore feed several panels, and the alternative - a fixed
	 * set of "column 1..4" control groups - would cap the design at whatever
	 * number was guessed here.
	 */
	private function mega_controls() {
		$this->start_controls_section(
			'section_mega',
			array(
				'label'     => __( 'Mega Panel', 'zlaark-deals-pro' ),
				'condition' => array( 'menu_source' => 'manual' ),
			)
		);

		$col = new Repeater();

		$col->add_control(
			'parent',
			array(
				'label'       => __( 'Belongs To', 'zlaark-deals-pro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Hosting', 'zlaark-deals-pro' ),
				'description' => __( 'The menu item text this column appears under. Match it exactly.', 'zlaark-deals-pro' ),
			)
		);

		$col->add_control(
			'heading',
			array(
				'label'   => __( 'Column Heading', 'zlaark-deals-pro' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'By use case', 'zlaark-deals-pro' ),
			)
		);

		$col->add_control(
			'links',
			array(
				'label'       => __( 'Links', 'zlaark-deals-pro' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 6,
				'placeholder' => "WordPress hosting|/best-wordpress-hosting/\nVPS hosting|/best-vps-hosting/",
				'description' => __( 'One per line as Label|URL. A line with no URL renders as plain text.', 'zlaark-deals-pro' ),
				'default'     => "WordPress hosting|#\nVPS hosting|#\nCloud hosting|#\nReseller hosting|#",
			)
		);

		$this->add_control(
			'mega_cols',
			array(
				'label'       => __( 'Columns', 'zlaark-deals-pro' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $col->get_controls(),
				'title_field' => '{{{ parent }}} - {{{ heading }}}',
				'default'     => array(
					array(
						'parent'  => __( 'Hosting', 'zlaark-deals-pro' ),
						'heading' => __( 'By use case', 'zlaark-deals-pro' ),
						'links'   => "WordPress hosting|#\nVPS hosting|#\nCloud hosting|#\nReseller hosting|#",
					),
					array(
						'parent'  => __( 'Hosting', 'zlaark-deals-pro' ),
						'heading' => __( 'By budget', 'zlaark-deals-pro' ),
						'links'   => "Under $3 a month|#\nUnder $10 a month|#\nBest value overall|#",
					),
					array(
						'parent'  => __( 'Hosting', 'zlaark-deals-pro' ),
						'heading' => __( 'Head to head', 'zlaark-deals-pro' ),
						'links'   => "Hostinger vs Bluehost|#\nWP Engine vs Kinsta|#\nAll comparisons|#",
					),
				),
			)
		);

		$this->add_control(
			'finder_heading',
			array(
				'label'     => __( 'Finder Column', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'finder_parent',
			array(
				'label'       => __( 'Belongs To', 'zlaark-deals-pro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Hosting', 'zlaark-deals-pro' ),
				'description' => __( 'Leave empty to hide the finder.', 'zlaark-deals-pro' ),
			)
		);

		$this->add_control(
			'finder_title',
			array(
				'label'   => __( 'Title', 'zlaark-deals-pro' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Hosting finder', 'zlaark-deals-pro' ),
			)
		);

		$this->add_control(
			'finder_text',
			array(
				'label'   => __( 'Text', 'zlaark-deals-pro' ),
				'type'    => Controls_Manager::TEXTAREA,
				'rows'    => 3,
				'default' => __( 'Answer three questions and we will shortlist the hosts we have actually paid for and measured.', 'zlaark-deals-pro' ),
			)
		);

		$this->add_control(
			'finder_cta_text',
			array(
				'label'   => __( 'Button Text', 'zlaark-deals-pro' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Start the finder', 'zlaark-deals-pro' ),
			)
		);

		$this->add_control(
			'finder_cta_url',
			array(
				'label'   => __( 'Button Link', 'zlaark-deals-pro' ),
				'type'    => Controls_Manager::URL,
				'default' => array( 'url' => '#' ),
			)
		);

		$this->end_controls_section();
	}

	private function action_controls() {
		$this->start_controls_section(
			'section_actions',
			array( 'label' => __( 'Actions', 'zlaark-deals-pro' ) )
		);

		$this->add_control(
			'link_text',
			array(
				'label'   => __( 'Text Link', 'zlaark-deals-pro' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Log In', 'zlaark-deals-pro' ),
			)
		);

		$this->add_control(
			'link_url',
			array(
				'label'     => __( 'Text Link URL', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::URL,
				'default'   => array( 'url' => '#' ),
				'condition' => array( 'link_text!' => '' ),
			)
		);

		$this->add_control(
			'cta_text',
			array(
				'label'     => __( 'CTA Button', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Sign Up', 'zlaark-deals-pro' ),
				'separator' => 'before',
			)
		);

		$this->add_control(
			'cta_url',
			array(
				'label'     => __( 'CTA URL', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::URL,
				'default'   => array( 'url' => '#' ),
				'condition' => array( 'cta_text!' => '' ),
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
			'sticky',
			array(
				'label'        => __( 'Stick to Top', 'zlaark-deals-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '',
				'return_value' => 'yes',
				'description'  => __( 'Needs an ancestor without overflow hidden to work.', 'zlaark-deals-pro' ),
			)
		);

		$this->add_control(
			'sticky_offset',
			array(
				'label'      => __( 'Sticky Offset', 'zlaark-deals-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 200 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 0 ),
				'condition'  => array( 'sticky' => 'yes' ),
				'selectors'  => array( '{{WRAPPER}} .zd-nav' => 'top: {{SIZE}}{{UNIT}};' ),
			)
		);

		$this->add_responsive_control(
			'max_width',
			array(
				'label'      => __( 'Content Max Width', 'zlaark-deals-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array( 'min' => 600, 'max' => 1920 ),
					'%'  => array( 'min' => 50, 'max' => 100 ),
				),
				'default'    => array( 'unit' => 'px', 'size' => 1240 ),
				'selectors'  => array( '{{WRAPPER}} .zd-nav__inner' => 'max-width: {{SIZE}}{{UNIT}};' ),
			)
		);

		$this->add_responsive_control(
			'bar_padding',
			array(
				'label'      => __( 'Bar Padding', 'zlaark-deals-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'default'    => array(
					'top'      => '18',
					'right'    => '28',
					'bottom'   => '18',
					'left'     => '28',
					'unit'     => 'px',
					'isLinked' => false,
				),
				'selectors'  => array(
					'{{WRAPPER}} .zd-nav__inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'mobile_breakpoint',
			array(
				'label'      => __( 'Collapse Below', 'zlaark-deals-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 480, 'max' => 1400 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 1024 ),
				'separator'  => 'before',
				'description' => __( 'Width at which the menu becomes a hamburger panel.', 'zlaark-deals-pro' ),
			)
		);

		$this->end_controls_section();
	}

	/* ------------------------------------------------------------------ style */

	private function style_controls() {
		/* Bar */
		$this->start_controls_section(
			'section_style_bar',
			array(
				'label' => __( 'Bar', 'zlaark-deals-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'bar_bg',
			array(
				'label'     => __( 'Background', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array( '{{WRAPPER}} .zd-nav' => 'background-color: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'bar_border',
			array(
				'label'     => __( 'Bottom Border', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#dce3df',
				'selectors' => array( '{{WRAPPER}} .zd-nav' => 'border-bottom-color: {{VALUE}};' ),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'bar_shadow',
				'selector' => '{{WRAPPER}} .zd-nav.is-stuck',
				'label'    => __( 'Shadow When Stuck', 'zlaark-deals-pro' ),
			)
		);

		$this->end_controls_section();

		/* Logo */
		$this->start_controls_section(
			'section_style_logo',
			array(
				'label' => __( 'Logo', 'zlaark-deals-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'logo_color',
			array(
				'label'     => __( 'Color', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#0a1310',
				'condition' => array( 'logo_type' => 'text' ),
				'selectors' => array( '{{WRAPPER}} .zd-nav__logo' => 'color: {{VALUE}};' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'logo_typography',
				'selector'  => '{{WRAPPER}} .zd-nav__logo',
				'condition' => array( 'logo_type' => 'text' ),
			)
		);

		$this->end_controls_section();

		/* Menu */
		$this->start_controls_section(
			'section_style_menu',
			array(
				'label' => __( 'Menu', 'zlaark-deals-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'accent',
			array(
				'label'     => __( 'Indicator Color', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#0b7a4f',
				'selectors' => array( '{{WRAPPER}} .zd-nav' => '--zd-nav-accent: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'menu_color',
			array(
				'label'     => __( 'Link Color', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#4a5a52',
				'selectors' => array( '{{WRAPPER}} .zd-nav' => '--zd-nav-link: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'menu_active_color',
			array(
				'label'     => __( 'Active Link Color', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array( '{{WRAPPER}} .zd-nav' => '--zd-nav-link-lit: {{VALUE}};' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'menu_typography',
				'selector' => '{{WRAPPER}} .zd-nav__list a',
			)
		);

		$this->add_control(
			'menu_shell_bg',
			array(
				'label'     => __( 'Capsule Background', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'separator' => 'before',
				'selectors' => array( '{{WRAPPER}} .zd-nav__menu' => 'background-color: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'menu_shell_border',
			array(
				'label'     => __( 'Capsule Border', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#dce3df',
				'selectors' => array( '{{WRAPPER}} .zd-nav__menu' => 'border-color: {{VALUE}};' ),
			)
		);

		$this->add_responsive_control(
			'menu_item_padding',
			array(
				'label'      => __( 'Item Padding', 'zlaark-deals-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'default'    => array(
					'top'      => '10',
					'right'    => '18',
					'bottom'   => '10',
					'left'     => '18',
					'unit'     => 'px',
					'isLinked' => false,
				),
				'selectors'  => array(
					'{{WRAPPER}} .zd-nav__list a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		/* Actions */
		$this->start_controls_section(
			'section_style_actions',
			array(
				'label' => __( 'Actions', 'zlaark-deals-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'link_color',
			array(
				'label'     => __( 'Text Link Color', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#0a1310',
				'selectors' => array( '{{WRAPPER}} .zd-nav__link-action' => 'color: {{VALUE}};' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'action_typography',
				'selector' => '{{WRAPPER}} .zd-nav__link-action, {{WRAPPER}} .zd-nav__cta',
			)
		);

		$this->add_control(
			'cta_bg',
			array(
				'label'     => __( 'CTA Background', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#0a1310',
				'separator' => 'before',
				'selectors' => array( '{{WRAPPER}} .zd-nav__cta' => 'background-color: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'cta_color',
			array(
				'label'     => __( 'CTA Text Color', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array( '{{WRAPPER}} .zd-nav__cta, {{WRAPPER}} .zd-nav__cta span' => 'color: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'cta_bg_hover',
			array(
				'label'     => __( 'CTA Background (Hover)', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#065f42',
				'selectors' => array( '{{WRAPPER}} .zd-nav__cta:hover' => 'background-color: {{VALUE}};' ),
			)
		);

		$this->add_responsive_control(
			'cta_padding',
			array(
				'label'      => __( 'CTA Padding', 'zlaark-deals-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'default'    => array(
					'top'      => '13',
					'right'    => '28',
					'bottom'   => '13',
					'left'     => '28',
					'unit'     => 'px',
					'isLinked' => false,
				),
				'selectors'  => array(
					'{{WRAPPER}} .zd-nav__cta' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'cta_radius',
			array(
				'label'      => __( 'CTA Radius', 'zlaark-deals-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 60 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 999 ),
				'selectors'  => array( '{{WRAPPER}} .zd-nav__cta' => 'border-radius: {{SIZE}}{{UNIT}};' ),
			)
		);

		$this->end_controls_section();
	}

	private function motion_section() {
		$this->start_controls_section(
			'section_nav_motion',
			array(
				'label' => __( 'Motion', 'zlaark-deals-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'slide_indicator',
			array(
				'label'        => __( 'Sliding Indicator', 'zlaark-deals-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'description'  => __( 'The pill glides to whichever item the pointer is over, then returns.', 'zlaark-deals-pro' ),
			)
		);

		$this->add_control(
			'magnetic_cta',
			array(
				'label'        => __( 'Magnetic CTA', 'zlaark-deals-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'shrink',
			array(
				'label'        => __( 'Shrink on Scroll', 'zlaark-deals-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'condition'    => array( 'sticky' => 'yes' ),
			)
		);

		$this->add_control(
			'intro',
			array(
				'label'        => __( 'Intro Animation', 'zlaark-deals-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'description'  => __( 'Logo, items and actions fade down in sequence on load.', 'zlaark-deals-pro' ),
			)
		);

		$this->end_controls_section();
	}

	/* ----------------------------------------------------------------- render */

	/** @return array [ menu_id => name ] for every non-empty nav menu. */
	private function get_menu_options() {
		$options = array();
		$menus   = wp_get_nav_menus();

		if ( is_wp_error( $menus ) || empty( $menus ) ) {
			return $options;
		}

		foreach ( $menus as $menu ) {
			$options[ $menu->term_id ] = $menu->name;
		}
		return $options;
	}

	/** True when a link points at the page currently being viewed. */
	private function is_current( $url ) {
		if ( empty( $url ) || '#' === $url ) {
			return false;
		}

		$current = untrailingslashit( strtok( home_url( add_query_arg( array() ) ), '?' ) );
		$target  = untrailingslashit( strtok( $url, '?' ) );

		return $current === $target;
	}

	protected function render() {
		$s = $this->get_settings_for_display();

		$classes = array( 'zd-nav' );
		if ( 'yes' === $s['sticky'] ) {
			$classes[] = 'zd-nav--sticky';
		}
		if ( 'yes' === $s['sticky'] && 'yes' === $s['shrink'] ) {
			$classes[] = 'zd-nav--shrink';
		}
		if ( 'yes' === $s['intro'] ) {
			$classes[] = 'zd-nav--intro';
		}
		if ( 'yes' === $s['slide_indicator'] ) {
			$classes[] = 'zd-nav--slide';
		}

		$breakpoint = isset( $s['mobile_breakpoint']['size'] ) ? (int) $s['mobile_breakpoint']['size'] : 1024;
		$uid        = 'zd-nav-' . $this->get_id();
		?>
		<?php
		// The collapse width is a widget setting, so a media query in the
		// stylesheet can't reach it - the script toggles .zd-nav--collapsed
		// against this value instead.
		?>
		<nav class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>"
			data-zd-nav-bp="<?php echo (int) $breakpoint; ?>"
			aria-label="<?php esc_attr_e( 'Main', 'zlaark-deals-pro' ); ?>">

			<div class="zd-nav__inner">
				<?php $this->render_logo( $s ); ?>

				<button type="button" class="zd-nav__burger" aria-expanded="false"
					aria-controls="<?php echo esc_attr( $uid ); ?>"
					aria-label="<?php esc_attr_e( 'Toggle menu', 'zlaark-deals-pro' ); ?>">
					<span></span><span></span><span></span>
				</button>

				<div class="zd-nav__menu" id="<?php echo esc_attr( $uid ); ?>">
					<span class="zd-nav__pill" aria-hidden="true"></span>
					<?php $this->render_menu( $s ); ?>
					<?php $this->render_actions( $s, true ); ?>
				</div>

				<?php $this->render_actions( $s, false ); ?>
			</div>
		</nav>
		<?php
	}

	private function render_logo( $s ) {
		$this->add_render_attribute( 'logo', 'class', 'zd-nav__logo' );
		if ( ! empty( $s['logo_link']['url'] ) ) {
			$this->add_link_attributes( 'logo', $s['logo_link'] );
		}
		?>
		<a <?php $this->print_render_attribute_string( 'logo' ); ?>>
			<?php if ( 'image' === $s['logo_type'] && ! empty( $s['logo_image']['url'] ) ) : ?>
				<img src="<?php echo esc_url( $s['logo_image']['url'] ); ?>"
					alt="<?php echo esc_attr( zlaark_deals_media_alt( $s['logo_image'] ) ); ?>" />
			<?php else : ?>
				<?php echo esc_html( $s['logo_text'] ); ?>
			<?php endif; ?>
		</a>
		<?php
	}

	private function render_menu( $s ) {
		if ( 'wp' === $s['menu_source'] ) {
			if ( empty( $s['wp_menu'] ) ) {
				return;
			}
			// WordPress marks the live page with .current-menu-item, which the
			// indicator script treats exactly like a manual "active" item.
			wp_nav_menu(
				array(
					'menu'        => (int) $s['wp_menu'],
					'container'   => false,
					'menu_class'  => 'zd-nav__list',
					'depth'       => 1,
					'fallback_cb' => '__return_empty_string',
				)
			);
			return;
		}

		if ( empty( $s['items'] ) || ! is_array( $s['items'] ) ) {
			return;
		}
		?>
		<ul class="zd-nav__list">
			<?php
			foreach ( $s['items'] as $i => $item ) {
				$url    = ! empty( $item['link']['url'] ) ? $item['link']['url'] : '#';
				$active = ( 'yes' === $item['is_active'] )
					|| ( 'yes' === $s['auto_active'] && $this->is_current( $url ) );

				$key = 'nav_item_' . $i;
				$this->add_render_attribute( $key, 'class', $active ? 'is-active' : '' );
				if ( ! empty( $item['link']['url'] ) ) {
					$this->add_link_attributes( $key, $item['link'] );
				} else {
					$this->add_render_attribute( $key, 'href', '#' );
				}
				$panel = ( 'yes' === $item['mega'] ) ? $this->mega_panel( $s, $item['text'] ) : '';
				$pid   = 'zd-mega-' . $this->get_id() . '-' . $i;

				if ( '' !== $panel ) {
					$this->add_render_attribute( $key, 'aria-expanded', 'false' );
					$this->add_render_attribute( $key, 'aria-controls', $pid );
				}
				?>
				<li class="zd-nav__item<?php echo '' !== $panel ? ' zd-nav__item--mega' : ''; ?>">
					<a <?php $this->print_render_attribute_string( $key ); ?>>
						<?php echo esc_html( $item['text'] ); ?>
						<?php if ( '' !== $panel ) : ?>
							<svg class="zd-nav__chev" viewBox="0 0 12 12" width="10" height="10"
								fill="none" aria-hidden="true">
								<path d="M2.5 4.5L6 8l3.5-3.5" stroke="currentColor" stroke-width="1.6"
									stroke-linecap="round" stroke-linejoin="round" />
							</svg>
						<?php endif; ?>
					</a>
					<?php if ( '' !== $panel ) : ?>
						<div class="zd-mega" id="<?php echo esc_attr( $pid ); ?>" hidden>
							<div class="zd-mega__inner"><?php echo $panel; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built and escaped in mega_panel(). ?></div>
						</div>
					<?php endif; ?>
				</li>
				<?php
			}
			?>
		</ul>
		<?php
	}

	/**
	 * The panel for one menu item, or '' when that item has no content.
	 *
	 * Built as a string rather than echoed, so render_menu() can ask "is there
	 * a panel?" before it decides whether the item needs a chevron and the ARIA
	 * wiring - an item that announces a menu it does not have is worse than one
	 * that never claims to.
	 */
	private function mega_panel( $s, $label ) {
		$label = trim( (string) $label );
		if ( '' === $label ) {
			return '';
		}

		$cols = is_array( $s['mega_cols'] ) ? $s['mega_cols'] : array();
		$mine = array();

		foreach ( $cols as $col ) {
			if ( 0 !== strcasecmp( trim( (string) $col['parent'] ), $label ) ) {
				continue;
			}
			$links = $this->parse_links( $col['links'] );
			if ( '' === trim( (string) $col['heading'] ) && empty( $links ) ) {
				continue;
			}
			$mine[] = array( 'heading' => $col['heading'], 'links' => $links );
		}

		$finder = ( 0 === strcasecmp( trim( (string) $s['finder_parent'] ), $label ) )
			&& ( '' !== trim( (string) $s['finder_title'] ) );

		if ( empty( $mine ) && ! $finder ) {
			return '';
		}

		ob_start();
		?>
		<div class="zd-mega__cols">
			<?php foreach ( $mine as $col ) : ?>
				<div class="zd-mega__col">
					<?php if ( '' !== trim( (string) $col['heading'] ) ) : ?>
						<p class="zd-mega__head">
							<span><?php echo esc_html( $col['heading'] ); ?></span>
							<?php
							/*
							 * The count is read off the column itself. A menu
							 * that says how much is behind each heading is
							 * doing the same job as the score bars further
							 * down the page - telling you before you click.
							 */
							?>
							<?php if ( ! empty( $col['links'] ) ) : ?>
								<i><?php echo esc_html( number_format_i18n( count( $col['links'] ) ) ); ?></i>
							<?php endif; ?>
						</p>
					<?php endif; ?>

					<?php if ( ! empty( $col['links'] ) ) : ?>
						<ul class="zd-mega__links">
							<?php foreach ( $col['links'] as $link ) : ?>
								<li>
									<?php if ( '' !== $link['url'] ) : ?>
										<a href="<?php echo esc_url( $link['url'] ); ?>"><?php echo esc_html( $link['label'] ); ?></a>
									<?php else : ?>
										<span><?php echo esc_html( $link['label'] ); ?></span>
									<?php endif; ?>
								</li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>

		<?php if ( $finder ) : ?>
			<div class="zd-mega__finder">
				<p class="zd-mega__head"><span><?php esc_html_e( 'Not sure yet', 'zlaark-deals-pro' ); ?></span></p>
				<p class="zd-mega__findertitle"><?php echo esc_html( $s['finder_title'] ); ?></p>
				<?php if ( '' !== $s['finder_text'] ) : ?>
					<p class="zd-mega__findertext"><?php echo esc_html( $s['finder_text'] ); ?></p>
				<?php endif; ?>
				<?php if ( '' !== $s['finder_cta_text'] ) : ?>
					<a class="zd-mega__findercta"
						href="<?php echo esc_url( ! empty( $s['finder_cta_url']['url'] ) ? $s['finder_cta_url']['url'] : '#' ); ?>">
						<span><?php echo esc_html( $s['finder_cta_text'] ); ?></span>
						<svg viewBox="0 0 16 16" width="13" height="13" fill="none" aria-hidden="true">
							<path d="M2 8h11M9 4l4 4-4 4" stroke="currentColor" stroke-width="2"
								stroke-linecap="round" stroke-linejoin="round" />
						</svg>
					</a>
				<?php endif; ?>
			</div>
		<?php endif; ?>
		<?php
		return (string) ob_get_clean();
	}

	/** "Label|URL" per line. A line with no pipe is a label with no link. */
	private function parse_links( $raw ) {
		$out = array();

		foreach ( preg_split( '/\r\n|\r|\n/', (string) $raw ) as $line ) {
			$line = trim( $line );
			if ( '' === $line ) {
				continue;
			}
			$parts = explode( '|', $line, 2 );
			$label = trim( $parts[0] );
			if ( '' === $label ) {
				continue;
			}
			$out[] = array(
				'label' => $label,
				'url'   => isset( $parts[1] ) ? trim( $parts[1] ) : '',
			);
		}

		return $out;
	}

	/**
	 * @param array $s
	 * @param bool  $in_panel Whether this copy lives inside the mobile panel.
	 */
	private function render_actions( $s, $in_panel ) {
		if ( '' === $s['link_text'] && '' === $s['cta_text'] ) {
			return;
		}

		$suffix  = $in_panel ? '_panel' : '';
		$classes = 'zd-nav__actions' . ( $in_panel ? ' zd-nav__actions--panel' : '' );
		?>
		<div class="<?php echo esc_attr( $classes ); ?>">
			<?php if ( '' !== $s['link_text'] ) : ?>
				<?php
				$key = 'action_link' . $suffix;
				$this->add_render_attribute( $key, 'class', 'zd-nav__link-action' );
				if ( ! empty( $s['link_url']['url'] ) ) {
					$this->add_link_attributes( $key, $s['link_url'] );
				}
				?>
				<a <?php $this->print_render_attribute_string( $key ); ?>>
					<?php echo esc_html( $s['link_text'] ); ?>
				</a>
			<?php endif; ?>

			<?php if ( '' !== $s['cta_text'] ) : ?>
				<?php
				$key     = 'action_cta' . $suffix;
				$cta_cls = array( 'zd-nav__cta' );
				if ( 'yes' === $s['magnetic_cta'] && ! $in_panel ) {
					$cta_cls[] = 'zd-magnetic';
				}
				$this->add_render_attribute( $key, 'class', $cta_cls );
				if ( ! empty( $s['cta_url']['url'] ) ) {
					$this->add_link_attributes( $key, $s['cta_url'] );
				}
				?>
				<a <?php $this->print_render_attribute_string( $key ); ?>>
					<?php echo esc_html( $s['cta_text'] ); ?>
				</a>
			<?php endif; ?>
		</div>
		<?php
	}
}
