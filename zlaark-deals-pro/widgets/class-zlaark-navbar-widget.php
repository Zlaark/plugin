<?php
/**
 * Zlaark Navbar — logo, a centred pill menu with a sliding active indicator,
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
				'default'   => 'Cobutec',
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

		$this->add_control(
			'items',
			array(
				'label'       => __( 'Items', 'zlaark-deals-pro' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ text }}}',
				'condition'   => array( 'menu_source' => 'manual' ),
				'default'     => array(
					array( 'text' => __( 'Home', 'zlaark-deals-pro' ), 'is_active' => 'yes' ),
					array( 'text' => __( 'Features', 'zlaark-deals-pro' ) ),
					array( 'text' => __( 'Assets', 'zlaark-deals-pro' ) ),
					array( 'text' => __( 'Pricing', 'zlaark-deals-pro' ) ),
					array( 'text' => __( 'Protection', 'zlaark-deals-pro' ) ),
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
				'default'    => array( 'unit' => 'px', 'size' => 1480 ),
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
				'default'   => '#ececf0',
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
				'default'   => '#0a0a0a',
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
				'default'   => '#1d6aff',
				'selectors' => array( '{{WRAPPER}} .zd-nav' => '--zd-nav-accent: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'menu_color',
			array(
				'label'     => __( 'Link Color', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#3f3f46',
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
				'default'   => '#e7e7ec',
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
				'default'   => '#0a0a0a',
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
				'default'   => '#0a0a0a',
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
				'default'   => '#1d6aff',
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
		// stylesheet can't reach it — the script toggles .zd-nav--collapsed
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
				?>
				<li class="zd-nav__item">
					<a <?php $this->print_render_attribute_string( $key ); ?>>
						<?php echo esc_html( $item['text'] ); ?>
					</a>
				</li>
				<?php
			}
			?>
		</ul>
		<?php
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
