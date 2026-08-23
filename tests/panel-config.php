<?php
/**
 * Builds every widget's controls the way the editor panel does: from a widget
 * constructed with NO data.
 *
 * This is the path that hung the editor, and nothing else in the suite walked
 * it. render-widgets.php and branches.php both build a widget and then render
 * it, which is what happens for a widget placed on a page - a real instance,
 * with a settings array. Elementor also builds every registered widget a
 * second way, with no data at all, to produce the control config the panel is
 * drawn from (Widgets_Manager::ajax_get_widget_types_controls_config, reached
 * by the get_widgets_config AJAX call).
 *
 * On that instance Controls_Stack::init() never ran, so $this->data is null,
 * and any register_controls() that calls get_data() dies with a TypeError. It
 * is a fatal inside an AJAX call the editor cannot proceed without: the editor
 * asks for JSON, receives a "critical error" HTML page, and sits on the
 * loading screen indefinitely. Every widget renders perfectly on the front end
 * the whole time, which is what makes it so hard to place.
 */

error_reporting( E_ALL );
ini_set( 'display_errors', '1' );

$warnings = array();
set_error_handler( function ( $no, $str, $file, $line ) use ( &$warnings ) {
	$warnings[] = sprintf( '%s (%s:%d)', $str, basename( $file ), $line );
	return true;
} );

require __DIR__ . '/elementor-stub.php';

$P = dirname( __DIR__ ) . '/';
require $P . 'includes/class-zlaark-deals-computed.php';
require $P . 'includes/class-zlaark-deals-articles.php';

class Zlaark_Deals_Meta {
	const FIELDS = array();
	public static function offer_types() { return array( '' => '-', 'coupon' => 'Coupon' ); }
	public static function get_deal_data( $p ) { return array(); }
	public static function parse_lines( $r ) { return array(); }
	public static function parse_scores( $r ) { return array(); }
}
class Zlaark_Deals_Settings { const SCHEMA = 2; public static function get( $k ) { return 1; } }

require $P . 'widgets/class-zlaark-widget-base.php';
require $P . 'widgets/class-zlaark-homepage-widget.php';
require $P . 'widgets/class-zlaark-section-widget-base.php';
require $P . 'includes/class-zlaark-deals-elementor.php';

$pass = 0;
$fail = 0;

echo "building the panel control config (widgets constructed with no data)\n";
echo str_repeat( '-', 78 ) . "\n";

foreach ( Zlaark_Deals_Elementor::WIDGETS as $slug => $class ) {
	require_once $P . 'widgets/class-zlaark-' . $slug . '-widget.php';

	$before = count( $warnings );

	try {
		// No constructor data: this is Elementor's type instance.
		$w = new $class();

		if ( ! $w->is_type_instance() ) {
			printf( "  %-16s NOT a type instance - the harness is wrong\n", $slug );
			$fail++;
			continue;
		}

		$n       = $w->zd_build();
		$newWarn = array_slice( $warnings, $before );

		if ( $newWarn ) {
			printf( "  %-16s %3d controls   WARNINGS: %s\n", $slug, $n, implode( ' | ', $newWarn ) );
			$fail++;
		} else {
			printf( "  %-16s %3d controls   ok\n", $slug, $n );
			$pass++;
		}
	} catch ( \Throwable $e ) {
		printf( "  %-16s FATAL: %s: %s\n", $slug, get_class( $e ), $e->getMessage() );
		$fail++;
	}
}

echo str_repeat( '-', 78 ) . "\n";
printf( "%d widgets built the panel config cleanly, %d failed\n", $pass, $fail );
exit( $fail > 0 ? 1 : 0 );
