<?php
/**
 * Elementor bootstrap: adds the "Zlaark Deals" widget category and registers
 * every widget the plugin ships.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Zlaark_Deals_Elementor {

	const MIN_ELEMENTOR_VERSION = '3.5.0';

	/** Widget file (without prefix/suffix) => class name. */
	const WIDGETS = array(
		'homepage'     => 'Zlaark_Homepage_Widget',
		'navbar'       => 'Zlaark_Navbar_Widget',
		'footer'       => 'Zlaark_Footer_Widget',
		'hero'         => 'Zlaark_Hero_Widget',
		'hero-classic' => 'Zlaark_Hero_Classic_Widget',
		'hero-bento'   => 'Zlaark_Hero_Bento_Widget',
		'hero-fresh'   => 'Zlaark_Hero_Fresh_Widget',
		'about'        => 'Zlaark_About_Widget',
		'deals'        => 'Zlaark_Deals_Widget',
		'index'        => 'Zlaark_Index_Widget',
		'top-picks'    => 'Zlaark_Top_Picks_Widget',
		'compare'      => 'Zlaark_Compare_Widget',
		'panel'        => 'Zlaark_Panel_Widget',
		'stats'        => 'Zlaark_Stats_Widget',
		'marquee'      => 'Zlaark_Marquee_Widget',

		/*
		 * Standalone sections. Each is one panel of the Homepage widget, so a
		 * page can be built a block at a time. They subclass the Homepage widget
		 * - it must be required first, which the ordering above guarantees.
		 */
		'scorecard'    => 'Zlaark_Scorecard_Widget',
		'band'         => 'Zlaark_Band_Widget',
		'categories'   => 'Zlaark_Categories_Widget',
		'expiring'     => 'Zlaark_Expiring_Widget',
		'method'       => 'Zlaark_Method_Widget',
		'aboutus'      => 'Zlaark_About_Us_Widget',
		'faq'          => 'Zlaark_Faq_Widget',
		'cta'          => 'Zlaark_Cta_Widget',
	);

	public static function init() {
		add_action( 'plugins_loaded', array( __CLASS__, 'boot' ), 20 );
	}

	public static function boot() {
		if ( ! did_action( 'elementor/loaded' ) ) {
			add_action( 'admin_notices', array( __CLASS__, 'notice_missing_elementor' ) );
			return;
		}

		if ( version_compare( ELEMENTOR_VERSION, self::MIN_ELEMENTOR_VERSION, '<' ) ) {
			add_action( 'admin_notices', array( __CLASS__, 'notice_old_elementor' ) );
			return;
		}

		add_action( 'elementor/elements/categories_registered', array( __CLASS__, 'register_category' ) );
		add_action( 'elementor/widgets/register', array( __CLASS__, 'register_widgets' ) );
	}

	public static function register_category( $manager ) {
		$manager->add_category(
			'zlaark-deals',
			array(
				'title' => __( 'Zlaark Deals', 'zlaark-deals-pro' ),
				'icon'  => 'eicon-price-table',
			)
		);
	}

	public static function register_widgets( $widgets_manager ) {
		require_once ZLAARK_DEALS_PATH . 'widgets/class-zlaark-widget-base.php';
		require_once ZLAARK_DEALS_PATH . 'widgets/class-zlaark-homepage-widget.php';
		require_once ZLAARK_DEALS_PATH . 'widgets/class-zlaark-section-widget-base.php';

		/*
		 * A widget that fails to register disappears from the panel silently:
		 * Elementor keeps going, the editor loads, and the only symptom is a
		 * gap in the widget list. Record why instead, so Deals > Settings can
		 * say what happened rather than leaving it to guesswork.
		 */
		$failures = array();

		foreach ( self::WIDGETS as $slug => $class ) {
			$file = ZLAARK_DEALS_PATH . 'widgets/class-zlaark-' . $slug . '-widget.php';

			if ( ! file_exists( $file ) ) {
				$failures[ $slug ] = 'file missing: ' . basename( $file );
				continue;
			}

			require_once $file;

			if ( ! class_exists( $class ) ) {
				$failures[ $slug ] = 'class ' . $class . ' not defined in ' . basename( $file );
				continue;
			}

			try {
				$widget = new $class();
				$name   = $widget->get_name();

				if ( $widgets_manager->get_widget_types( $name ) ) {
					$failures[ $slug ] = 'widget name "' . $name . '" is already taken by another plugin or theme';
					continue;
				}

				$widgets_manager->register( $widget );
			} catch ( \Throwable $e ) {
				$failures[ $slug ] = get_class( $e ) . ': ' . $e->getMessage();
			}
		}

		update_option( 'zlaark_deals_widget_failures', $failures, false );
	}

	public static function notice_missing_elementor() {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}
		echo '<div class="notice notice-warning"><p>';
		echo esc_html__( 'Zlaark Deals: Elementor is not active. The Deals manager still works, but the widgets need Elementor.', 'zlaark-deals-pro' );
		echo '</p></div>';
	}

	public static function notice_old_elementor() {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}
		echo '<div class="notice notice-warning"><p>';
		printf(
			/* translators: %s: minimum Elementor version */
			esc_html__( 'Zlaark Deals requires Elementor %s or newer.', 'zlaark-deals-pro' ),
			esc_html( self::MIN_ELEMENTOR_VERSION )
		);
		echo '</p></div>';
	}
}
