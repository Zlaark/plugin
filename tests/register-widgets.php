<?php
/**
 * Loads every Zlaark widget and runs register_controls() exactly as the
 * Elementor editor bootstrap does, turning every warning/notice into a failure.
 */

error_reporting( E_ALL );
ini_set( 'display_errors', '1' );

$warnings = array();
set_error_handler( function ( $no, $str, $file, $line ) use ( &$warnings ) {
	$warnings[] = sprintf( '%s in %s:%d', $str, basename( $file ), $line );
	return true;
} );

require __DIR__ . '/elementor-stub.php';

$P = dirname( __DIR__ ) . '/';
require $P . 'includes/class-zlaark-deals-computed.php';
require $P . 'includes/class-zlaark-deals-articles.php';

// Meta is needed by several widgets; stub the parts that touch the DB.
class Zlaark_Deals_Meta {
	const FIELDS = array();
	public static function offer_types() {
		return array( '' => '— none —', 'coupon' => 'Coupon', 'exclusive' => 'Exclusive',
		              'free_trial' => 'Free trial', 'free_plan' => 'Free plan', 'seasonal' => 'Seasonal' );
	}
	public static function get_deal_data( $p ) { return array(); }
	public static function parse_lines( $r ) { return array(); }
	public static function parse_scores( $r ) { return array(); }
}
class Zlaark_Deals_Settings { const SCHEMA = 2; public static function get( $k ) { return 1; } }

require $P . 'widgets/class-zlaark-widget-base.php';
require $P . 'widgets/class-zlaark-homepage-widget.php';
require $P . 'widgets/class-zlaark-section-widget-base.php';

$WIDGETS = array(
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
	'scorecard'    => 'Zlaark_Scorecard_Widget',
	'band'         => 'Zlaark_Band_Widget',
	'categories'   => 'Zlaark_Categories_Widget',
	'expiring'     => 'Zlaark_Expiring_Widget',
	'method'       => 'Zlaark_Method_Widget',
	'aboutus'      => 'Zlaark_About_Us_Widget',
	'faq'          => 'Zlaark_Faq_Widget',
	'cta'          => 'Zlaark_Cta_Widget',
);

$fail = 0;
$pass = 0;

echo "loading + registering controls for every widget\n";
echo str_repeat( '-', 78 ) . "\n";

foreach ( $WIDGETS as $slug => $class ) {
	$file = $P . 'widgets/class-zlaark-' . $slug . '-widget.php';

	if ( ! file_exists( $file ) ) {
		printf( "  %-26s FILE MISSING: %s\n", $slug, $file );
		$fail++;
		continue;
	}

	require_once $file;

	if ( ! class_exists( $class ) ) {
		printf( "  %-26s CLASS NOT DEFINED: %s\n", $slug, $class );
		$fail++;
		continue;
	}

	$GLOBALS['zd_calls'] = array();
	$before = count( $warnings );

	try {
		$w = new $class();
		$n = $w->zd_build();

		$db = array_values( array_filter( $GLOBALS['zd_calls'], function ( $c ) {
			return in_array( $c, array( 'get_posts', 'WP_Query', 'get_category_options' ), true );
		} ) );

		$newWarn = array_slice( $warnings, $before );

		if ( $newWarn ) {
			printf( "  %-26s %3d controls   WARNINGS: %s\n", $slug, $n, implode( ' | ', $newWarn ) );
			$fail++;
		} else {
			printf( "  %-26s %3d controls   db during registration: %s\n",
				$slug, $n, $db ? implode( ',', $db ) : 'none' );
			$pass++;
		}
	} catch ( \Throwable $e ) {
		printf( "  %-26s FATAL: %s\n", $slug, $e->getMessage() );
		$fail++;
	}
}

echo str_repeat( '-', 78 ) . "\n";
printf( "%d widgets registered cleanly, %d failed\n", $pass, $fail );
exit( $fail > 0 ? 1 : 0 );
