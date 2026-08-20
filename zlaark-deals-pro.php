<?php
/**
 * Plugin Name: Zlaark Deals
 * Description: Premium animated Elementor widgets — Hero, Deals Grid, Top Picks, Comparison, Stats and Logo Marquee — powered by a Deals manager with categories in the WordPress sidebar.
 * Version:     3.1.2
 * Author:      Zlaark
 * Text Domain: zlaark-deals-pro
 * Requires PHP: 7.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ZLAARK_DEALS_VERSION', '3.1.2' );
define( 'ZLAARK_DEALS_FILE', __FILE__ );
define( 'ZLAARK_DEALS_PATH', plugin_dir_path( __FILE__ ) );
define( 'ZLAARK_DEALS_URL', plugin_dir_url( __FILE__ ) );

/** Post type + taxonomy slugs used across the plugin. */
define( 'ZLAARK_DEALS_CPT', 'zlaark_deal' );
define( 'ZLAARK_DEALS_TAX', 'zlaark_deal_cat' );

require_once ZLAARK_DEALS_PATH . 'includes/class-zlaark-deals-post-type.php';
require_once ZLAARK_DEALS_PATH . 'includes/class-zlaark-deals-meta.php';
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
		Zlaark_Deals_Post_Type::init();
		Zlaark_Deals_Meta::init();
		Zlaark_Deals_Elementor::init();

		add_action( 'wp_enqueue_scripts', array( $this, 'register_frontend_assets' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_assets' ) );
	}

	/**
	 * Registered, not enqueued — every widget declares these through
	 * get_style_depends()/get_script_depends(), so pages without a Zlaark
	 * widget never load them.
	 */
	public function register_frontend_assets() {
		wp_register_style(
			'zlaark-deals-fonts',
			'https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap',
			array(),
			null
		);

		wp_register_style(
			'zlaark-deals',
			ZLAARK_DEALS_URL . 'assets/css/frontend.css',
			array( 'zlaark-deals-fonts' ),
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
