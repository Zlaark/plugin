<?php
/**
 * Single deal pages without Elementor Pro.
 *
 * Building a single template for the Deal post type needs Pro's Theme Builder.
 * Most sites running this plugin are on free Elementor, so the offer panel is
 * injected into the deal's content directly instead - same markup as the Deal
 * Panel widget, no Pro required.
 *
 * Sites that DO have Pro can turn this off and place the widget by hand.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Zlaark_Deals_Single {

	public static function init() {
		add_filter( 'the_content', array( __CLASS__, 'inject' ), 20 );
		add_filter( 'body_class', array( __CLASS__, 'body_class' ) );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_readable' ) );
	}

	/**
	 * The stylesheet reaches the front end through each widget's
	 * get_style_depends(), which means it is only there when a Zlaark widget is
	 * on the page. Post typography has no widget - it styles markup the theme
	 * printed - so it has to ask for the stylesheet itself, or the body class
	 * lands on a page with nothing to act on it.
	 */
	public static function enqueue_readable() {
		if ( is_admin() || ! is_singular( 'post' ) ) {
			return;
		}
		if ( ! Zlaark_Deals_Settings::get( 'style_posts' ) ) {
			return;
		}
		if ( wp_style_is( 'zlaark-deals', 'registered' ) ) {
			wp_enqueue_style( 'zlaark-deals' );
		}
	}

	/** Layout choice from Settings: off | above | side. */
	private static function layout() {
		$mode = Zlaark_Deals_Settings::get( 'single_panel' );
		return in_array( $mode, array( 'off', 'above', 'side' ), true ) ? $mode : 'side';
	}

	public static function body_class( $classes ) {
		if ( self::applies() && 'side' === self::layout() ) {
			$classes[] = 'zd-single-side';
		}

		/*
		 * Post typography is opt-out, and it hangs off a body class rather than
		 * styling .entry-content unconditionally: that wrapper belongs to the
		 * theme, and a plugin that restyles every post on the site whether or
		 * not it was asked to is a plugin nobody can debug.
		 *
		 * is_singular() rather than the applies() check above - that one is
		 * scoped to deals and to the main loop, and this is about ordinary
		 * posts, in the <body> tag, long before any loop runs.
		 */
		if ( ! is_admin() && is_singular( 'post' ) && Zlaark_Deals_Settings::get( 'style_posts' ) ) {
			$classes[] = 'zd-readable';
		}

		return $classes;
	}

	/** Only the main query, on a single deal, in the main loop, on the front end. */
	private static function applies() {
		return ! is_admin()
			&& is_singular( ZLAARK_DEALS_CPT )
			&& in_the_loop()
			&& is_main_query();
	}

	public static function inject( $content ) {
		if ( ! self::applies() || 'off' === self::layout() ) {
			return $content;
		}

		// Elementor renders its own canvas for the post; don't fight it.
		if ( class_exists( '\Elementor\Plugin' )
			&& \Elementor\Plugin::$instance->documents->get( get_the_ID() )
			&& \Elementor\Plugin::$instance->documents->get( get_the_ID() )->is_built_with_elementor() ) {
			return $content;
		}

		$deal = Zlaark_Deals_Meta::get_deal_data( get_the_ID() );
		if ( empty( $deal ) ) {
			return $content;
		}

		$panel = Zlaark_Deals_Panel::html( $deal );
		if ( '' === $panel ) {
			return $content;
		}

		if ( 'above' === self::layout() ) {
			return '<div class="zd-single zd-single--above">' . $panel . '</div>' . $content;
		}

		return '<div class="zd-single zd-single--side">'
			. '<div class="zd-single__body">' . $content . '</div>'
			. '<div class="zd-single__aside">' . $panel . '</div>'
			. '</div>';
	}
}
