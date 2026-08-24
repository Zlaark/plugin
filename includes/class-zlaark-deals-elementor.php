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
		'header'       => 'Zlaark_Header_Widget',
		'footer'       => 'Zlaark_Footer_Widget',
		'hero'         => 'Zlaark_Hero_Widget',
		'hero-classic' => 'Zlaark_Hero_Classic_Widget',
		'hero-bento'   => 'Zlaark_Hero_Bento_Widget',
		'hero-fresh'   => 'Zlaark_Hero_Fresh_Widget',
		'about'        => 'Zlaark_About_Widget',
		'deals'        => 'Zlaark_Deals_Widget',
		'index'        => 'Zlaark_Index_Widget',
		'article'      => 'Zlaark_Article_Widget',
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
		'lineup'       => 'Zlaark_Lineup_Widget',
		'reviews'      => 'Zlaark_Reviews_Widget',
		'comparisons'  => 'Zlaark_Comparisons_Widget',
		'grid'         => 'Zlaark_Article_Grid_Widget',
		'testimonials' => 'Zlaark_Testimonials_Widget',
		'byline'       => 'Zlaark_Byline_Widget',
		'verdict'      => 'Zlaark_Verdict_Widget',
		'offerbar'     => 'Zlaark_Offerbar_Widget',
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

	/**
	 * Which widgets to register, slug => class.
	 *
	 * Bisecting a broken editor is otherwise a matter of deleting files: this
	 * lets a mu-plugin or the theme's functions.php narrow the set down to the
	 * one widget at fault without touching the plugin. Define
	 * ZLAARK_DEALS_DISABLE_WIDGETS as true to register none of them - the Deals
	 * manager keeps working, so the editor either recovers (the fault is here)
	 * or does not (it is somewhere else).
	 *
	 *     add_filter( 'zlaark_deals_registered_widgets', function ( $widgets ) {
	 *         return array_slice( $widgets, 0, 5, true );
	 *     } );
	 */
	public static function widget_list() {
		if ( defined( 'ZLAARK_DEALS_DISABLE_WIDGETS' ) && ZLAARK_DEALS_DISABLE_WIDGETS ) {
			return array();
		}

		return (array) apply_filters( 'zlaark_deals_registered_widgets', self::WIDGETS );
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
		$widgets = self::widget_list();

		if ( empty( $widgets ) ) {
			return;
		}

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

		/*
		 * One snapshot of what the manager already holds, rather than asking it
		 * per widget. get_widget_types() is the call that lazily triggers
		 * Elementor's own widget initialisation, and this loop runs from inside
		 * that initialisation - re-entering it thirty-one times a request to
		 * answer a question that cannot change while the loop runs is asking
		 * for trouble as well as being slower.
		 */
		$held = $widgets_manager->get_widget_types();
		$held = is_array( $held ) ? $held : array();

		foreach ( $widgets as $slug => $class ) {
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

				/*
				 * Report a name clash, but only a real one. This action can
				 * fire more than once per request, and on the second pass our
				 * own widgets are already in the manager - treating that as a
				 * collision would skip re-registration and fill the diagnostic
				 * with false alarms. A different class holding the name is the
				 * only thing worth flagging.
				 */
				$incumbent = isset( $held[ $name ] ) ? $held[ $name ] : null;

				if ( $incumbent && ! ( $incumbent instanceof $class ) ) {
					$failures[ $slug ] = sprintf(
						'name "%s" is already held by %s',
						$name,
						get_class( $incumbent )
					);
					continue;
				}

				/*
				 * register() is not a promise. Elementor runs every widget past
				 * the elementor/widgets/is_widget_enabled filter and returns
				 * false for the ones that come back blocked - it does not throw
				 * and it does not log. That filter is how Element Manager
				 * (Elementor > Elements) switches a widget off site-wide, and
				 * any other plugin can hook it too. Ignoring the return value
				 * meant a blocked widget looked registered from in here while
				 * never reaching the panel: the exact "everything shows except
				 * this one" report, with a clean diagnostic to match.
				 *
				 * Strict false only - Elementor before 3.5 returned nothing.
				 */
				if ( false === $widgets_manager->register( $widget ) ) {
					$failures[ $slug ] = 'Elementor refused it: switched off in Elementor > Elements '
						. '(Element Manager), or another plugin is blocking it through the '
						. 'elementor/widgets/is_widget_enabled filter';
				}
			} catch ( \Throwable $e ) {
				$failures[ $slug ] = get_class( $e ) . ': ' . $e->getMessage();
			}
		}

		// Only touch the DB when the picture actually changed.
		if ( get_option( 'zlaark_deals_widget_failures' ) !== $failures ) {
			update_option( 'zlaark_deals_widget_failures', $failures, false );
		}
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
