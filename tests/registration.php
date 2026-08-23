<?php
/**
 * Exercises the real registration path, not just the widget classes.
 *
 * Elementor can fire `elementor/widgets/register` more than once in a request.
 * The first version of the collision guard treated the second pass as "name
 * already taken" and skipped every widget, so this runs it twice and insists
 * the outcome is identical both times.
 */
error_reporting( E_ALL );
ini_set( 'display_errors', '1' );

$warnings = array();
set_error_handler( function ( $no, $str, $file, $line ) use ( &$warnings ) {
	$warnings[] = sprintf( '%s (%s:%d)', $str, basename( $file ), $line );
	return true;
} );

$GLOBALS['zd_fake_posts'] = 3;
require __DIR__ . '/elementor-stub.php';

$P = dirname( __DIR__ ) . '/';
define( 'ZLAARK_DEALS_PATH', $P );

require $P . 'includes/class-zlaark-deals-computed.php';
require $P . 'includes/class-zlaark-deals-panel.php';
require $P . 'includes/class-zlaark-deals-articles.php';
class Zlaark_Deals_Settings { const SCHEMA = 2; public static function get( $k ) { return 1; } }
require __DIR__ . '/fixtures.php';
class Zlaark_Deals_Meta {
	const FIELDS = array();
	public static function offer_types() { return array( '' => '-' ); }
	public static function get_deal_data( $p ) { $d = zd_fixture_deals(); return $d[0]; }
	public static function parse_lines( $r ) { return array(); }
	public static function parse_scores( $r ) { return array(); }
}

/** Stands in for Elementor's Widgets_Manager. */
class ZD_Fake_Manager {
	public $types = array();
	public function get_widget_types( $name = null ) {
		if ( null === $name ) { return $this->types; }
		return isset( $this->types[ $name ] ) ? $this->types[ $name ] : null;
	}
	public function register( $widget ) {
		$this->types[ $widget->get_name() ] = $widget;
		return true;
	}
}

require $P . 'widgets/class-zlaark-widget-base.php';
require $P . 'includes/class-zlaark-deals-elementor.php';

$expected = count( Zlaark_Deals_Elementor::WIDGETS );
$pass = 0; $fail = 0;

echo "registering through the real code path, twice\n";
echo str_repeat( '-', 74 ) . "\n";

$manager = new ZD_Fake_Manager();

foreach ( array( 'first pass', 'second pass' ) as $round ) {
	$before = count( $warnings );
	Zlaark_Deals_Elementor::register_widgets( $manager );
	$new       = array_slice( $warnings, $before );
	$failures  = get_option( 'zlaark_deals_widget_failures', array() );
	$count     = count( $manager->get_widget_types() );

	$ok = ( $count === $expected ) && empty( $failures ) && empty( $new );
	printf(
		"  %-12s %2d/%d registered  %d failures  %d warnings   %s\n",
		$round, $count, $expected, count( $failures ), count( $new ), $ok ? 'ok' : 'FAILED'
	);
	if ( ! empty( $failures ) ) {
		foreach ( $failures as $slug => $why ) { printf( "      %-12s %s\n", $slug, $why ); }
	}
	if ( $new ) { printf( "      %s\n", implode( ' | ', array_slice( $new, 0, 2 ) ) ); }
	$ok ? $pass++ : $fail++;
}

// Every registered name must be unique and ours.
$names = array_keys( $manager->get_widget_types() );
$dupes = count( $names ) !== count( array_unique( $names ) );
$alien = array_filter( $names, function ( $n ) { return 0 !== strpos( $n, 'zlaark_' ); } );
printf( "  %-12s %d unique names, %d unexpected   %s\n", 'names', count( array_unique( $names ) ),
	count( $alien ), ( ! $dupes && ! $alien ) ? 'ok' : 'FAILED' );
( ! $dupes && ! $alien ) ? $pass++ : $fail++;

// A genuine clash must still be reported.
$manager2 = new ZD_Fake_Manager();
$manager2->types['zlaark_navbar'] = new stdClass();
Zlaark_Deals_Elementor::register_widgets( $manager2 );
$f = get_option( 'zlaark_deals_widget_failures', array() );
$caught = isset( $f['navbar'] ) && false !== strpos( $f['navbar'], 'already held by' );
printf( "  %-12s %s   %s\n", 'real clash', $caught ? 'reported' : 'MISSED', $caught ? 'ok' : 'FAILED' );
$caught ? $pass++ : $fail++;

/*
 * No widget file may define a global function.
 *
 * zlaark_deals_media_alt() used to live at the tail of the hero widget's file
 * while six other widget files called it. That only worked because
 * registration happened to require hero before them; narrowing the widget list
 * - which zlaark_deals_registered_widgets exists to let you do when bisecting a
 * broken editor - or reordering the list killed the others mid-render with
 * "call to undefined function". A shared helper belongs in the base file, which
 * is required before any widget. This keeps it that way.
 */
$strays = array();
foreach ( glob( $P . 'widgets/*.php' ) as $file ) {
	if ( 'class-zlaark-widget-base.php' === basename( $file ) ) {
		continue; // The one file guaranteed to be loaded first.
	}
	if ( preg_match_all( '/^function\s+(\w+)/m', (string) file_get_contents( $file ), $m ) ) {
		foreach ( $m[1] as $fn ) {
			$strays[] = basename( $file ) . ':' . $fn . '()';
		}
	}
}
printf( "  %-12s %s   %s\n", 'helpers',
	$strays ? implode( ', ', $strays ) : 'none outside the base file',
	empty( $strays ) ? 'ok' : 'FAILED' );
empty( $strays ) ? $pass++ : $fail++;

echo str_repeat( '-', 74 ) . "\n";
printf( "%d passed, %d failed\n", $pass, $fail );
exit( $fail > 0 ? 1 : 0 );
