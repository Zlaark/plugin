<?php
/**
 * Plugin Name: Zlaark Deals
 * Description: Premium animated Elementor widgets — Hero, Deals Grid, Top Picks, Comparison, Stats and Logo Marquee — powered by a Deals manager with categories in the WordPress sidebar.
 * Version:     4.3.0
 * Author:      Zlaark
 * Text Domain: zlaark-deals-pro
 * Requires PHP: 7.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ZLAARK_DEALS_VERSION', '4.3.0' );
define( 'ZLAARK_DEALS_FILE', __FILE__ );
define( 'ZLAARK_DEALS_PATH', plugin_dir_path( __FILE__ ) );
define( 'ZLAARK_DEALS_URL', plugin_dir_url( __FILE__ ) );

/** Post type + taxonomy slugs used across the plugin. */
define( 'ZLAARK_DEALS_CPT', 'zlaark_deal' );
define( 'ZLAARK_DEALS_TAX', 'zlaark_deal_cat' );

require_once ZLAARK_DEALS_PATH . 'includes/class-zlaark-deals-settings.php';
require_once ZLAARK_DEALS_PATH . 'includes/class-zlaark-deals-post-type.php';
require_once ZLAARK_DEALS_PATH . 'includes/class-zlaark-deals-computed.php';
require_once ZLAARK_DEALS_PATH . 'includes/class-zlaark-deals-meta.php';
require_once ZLAARK_DEALS_PATH . 'includes/class-zlaark-deals-panel.php';
require_once ZLAARK_DEALS_PATH . 'includes/class-zlaark-deals-single.php';
require_once ZLAARK_DEALS_PATH . 'includes/class-zlaark-deals-schema.php';
require_once ZLAARK_DEALS_PATH . 'includes/class-zlaark-deals-elementor.php';

final class Zlaark_Deals {

	/** @var Zlaark_Deals|null */
	private static $instance = null;

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		Zlaark_Deals_Settings::init();
		Zlaark_Deals_Post_Type::init();
		Zlaark_Deals_Meta::init();
		Zlaark_Deals_Elementor::init();
		Zlaark_Deals_Schema::init();
		Zlaark_Deals_Single::init();

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_fonts' ), 5 );
		add_action( 'wp_enqueue_scripts', array( $this, 'register_frontend_assets' ) );
		add_filter( 'wp_resource_hints', array( $this, 'resource_hints' ), 10, 2 );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_assets' ) );
	}

	/**
	 * Registered, not enqueued — every widget declares these through
	 * get_style_depends()/get_script_depends(), so pages without a Zlaark
	 * widget never load them.
	 */
	/**
	 * The Google Fonts URL. Filterable so a site can self-host the three
	 * families and drop the third-party connection from the critical path.
	 *
	 * All three faces are SIL Open Font License — free for commercial use.
	 */
	public function fonts_url() {
		$url = 'https://fonts.googleapis.com/css2'
			. '?family=Bricolage+Grotesque:opsz,wght@12..96,600;12..96,700;12..96,800'
			. '&family=IBM+Plex+Mono:wght@400;500;600'
			. '&family=Instrument+Sans:wght@400;500;600'
			. '&display=swap';

		return apply_filters( 'zlaark_deals_fonts_url', $url );
	}

	/**
	 * Enqueued directly at priority 5 rather than declared through
	 * get_style_depends(): Elementor resolves widget style dependencies after
	 * wp_head has already been printed on this theme, so the font never made it
	 * into the page and every widget fell back to the system UI stack.
	 */
	public function enqueue_fonts() {
		if ( ! Zlaark_Deals_Settings::get( 'load_fonts' ) ) {
			return;
		}

		$url = $this->fonts_url();
		if ( ! $url ) {
			return;
		}

		wp_enqueue_style( 'zlaark-deals-fonts', $url, array(), null );
	}

	/** Warms up the font connection before the stylesheet is parsed. */
	public function resource_hints( $hints, $relation ) {
		if ( 'preconnect' !== $relation || ! Zlaark_Deals_Settings::get( 'load_fonts' ) ) {
			return $hints;
		}

		if ( false === strpos( $this->fonts_url(), 'fonts.googleapis.com' ) ) {
			return $hints;
		}

		$hints[] = array( 'href' => 'https://fonts.googleapis.com' );
		$hints[] = array(
			'href'        => 'https://fonts.gstatic.com',
			'crossorigin' => 'anonymous',
		);

		return $hints;
	}

	public function register_frontend_assets() {
		wp_register_style(
			'zlaark-deals',
			ZLAARK_DEALS_URL . 'assets/css/frontend.css',
			array(),
			ZLAARK_DEALS_VERSION
		);

		wp_register_script(
			'zlaark-deals',
			ZLAARK_DEALS_URL . 'assets/js/frontend.js',
			array(),
			ZLAARK_DEALS_VERSION,
			true
		);
	}

	public function admin_assets() {
		$screen = get_current_screen();
		if ( ! $screen || ZLAARK_DEALS_CPT !== $screen->post_type ) {
			return;
		}


		wp_enqueue_media();
		wp_enqueue_style(
			'zlaark-deals-admin',
			ZLAARK_DEALS_URL . 'assets/css/admin.css',
			array(),
			ZLAARK_DEALS_VERSION
		);
		wp_enqueue_script(
			'zlaark-deals-admin',
			ZLAARK_DEALS_URL . 'assets/js/admin.js',
			array( 'jquery' ),
			ZLAARK_DEALS_VERSION,
			true
		);
		wp_localize_script(
			'zlaark-deals-admin',
			'ZlaarkDealsAdmin',
			array(
				'chooseImage' => __( 'Choose deal image', 'zlaark-deals-pro' ),
				'useImage'    => __( 'Use this image', 'zlaark-deals-pro' ),
				'noImage'     => __( 'No image selected', 'zlaark-deals-pro' ),
			)
		);
	}
}

/**
 * Activation: register the CPT/taxonomy, seed the two default categories and
 * flush rewrite rules so the deal URLs work immediately.
 */
function zlaark_deals_activate() {
	Zlaark_Deals_Post_Type::register_post_type();
	Zlaark_Deals_Post_Type::register_taxonomy();
	Zlaark_Deals_Post_Type::seed_default_terms();
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'zlaark_deals_activate' );

function zlaark_deals_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'zlaark_deals_deactivate' );

add_action( 'plugins_loaded', array( 'Zlaark_Deals', 'instance' ) );
