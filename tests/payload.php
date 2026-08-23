<?php
/**
 * Measures what the widget controls weigh in the editor's JSON config.
 *
 * Elementor serialises every registered widget's whole control stack into the
 * editor bootstrap. A picker that embeds a hundred post titles costs the same
 * on a site with one deal as on a site with a thousand, so this models a
 * realistic catalogue - 120 deals, 120 posts, 60 categories - and reports what
 * each widget adds. The editor spinning forever is usually this getting big
 * enough to blow the memory limit or the JSON size the browser will parse.
 */

error_reporting( E_ALL );
ini_set( 'display_errors', '1' );

// A busy but ordinary site.
$GLOBALS['zd_term_count'] = 60;
$GLOBALS['zd_post_count'] = 120;

require __DIR__ . '/elementor-stub.php';

$P = dirname( __DIR__ ) . '/';
require $P . 'includes/class-zlaark-deals-computed.php';
require $P . 'includes/class-zlaark-deals-articles.php';

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
require $P . 'includes/class-zlaark-deals-elementor.php';

printf( "%-16s %9s %9s %9s  %s\n", 'widget', 'controls', 'options', 'bytes', 'heaviest control' );
echo str_repeat( '-', 78 ) . "\n";

$total_bytes = 0;
$total_ctrl  = 0;
$total_opts  = 0;
$rows        = array();

foreach ( Zlaark_Deals_Elementor::WIDGETS as $slug => $class ) {
	require_once $P . 'widgets/class-zlaark-' . $slug . '-widget.php';

	$w = new $class();
	$w->zd_build();

	$bytes = 0;
	$opts  = 0;
	$worst = array( '', 0 );

	foreach ( $w->zd_controls as $id => $args ) {
		$size   = strlen( (string) json_encode( $args ) );
		$bytes += $size;
		if ( isset( $args['options'] ) ) { $opts += count( (array) $args['options'] ); }
		if ( $size > $worst[1] ) { $worst = array( $id, $size ); }
	}

	$rows[] = array( $slug, count( $w->zd_controls ), $opts, $bytes, $worst );
	$total_bytes += $bytes;
	$total_ctrl  += count( $w->zd_controls );
	$total_opts  += $opts;
}

usort( $rows, function ( $a, $b ) { return $b[3] - $a[3]; } );

foreach ( $rows as $r ) {
	printf( "%-16s %9d %9d %9s  %s (%s)\n", $r[0], $r[1], $r[2],
		number_format( $r[3] ), $r[4][0], number_format( $r[4][1] ) );
}

echo str_repeat( '-', 78 ) . "\n";
printf( "%-16s %9d %9d %9s\n", 'TOTAL', $total_ctrl, $total_opts, number_format( $total_bytes ) );
printf( "\npeak memory building all %d stacks: %s MB\n",
	count( Zlaark_Deals_Elementor::WIDGETS ),
	number_format( memory_get_peak_usage( true ) / 1048576, 1 ) );
