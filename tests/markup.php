<?php
/**
 * Two checks the other harnesses don't cover:
 *
 *   1. ids_from_url() — it parses $_GET, so it gets hostile input.
 *   2. Accessibility of the markup every widget actually emits: heading order,
 *      alt text, accessible names on controls, and labelled form fields.
 */

error_reporting( E_ALL );
ini_set( 'display_errors', '1' );

$warnings = array();
set_error_handler( function ( $n, $s, $f, $l ) use ( &$warnings ) {
	$warnings[] = "$s (" . basename( $f ) . ":$l)";
	return true;
} );

$GLOBALS['zd_fake_posts'] = 3;
require __DIR__ . '/elementor-stub.php';

$P = dirname( __DIR__ ) . '/';
require $P . 'includes/class-zlaark-deals-computed.php';
require __DIR__ . '/fixtures.php';

class Zlaark_Deals_Settings { const SCHEMA = 2; public static function get( $k ) { return 1; } }

class Zlaark_Deals_Meta {
	const FIELDS = array();
	public static function offer_types() {
		return array( '' => '- none -', 'coupon' => 'Coupon', 'exclusive' => 'Exclusive',
		              'free_trial' => 'Free trial', 'free_plan' => 'Free plan', 'seasonal' => 'Seasonal' );
	}
	public static function get_deal_data( $p ) {
		$deals = zd_fixture_deals();
		$id = is_object( $p ) ? $p->ID : (int) $p;
		foreach ( $deals as $d ) { if ( $d['id'] === $id ) { return $d; } }
		return $deals[0];
	}
	public static function parse_lines( $r ) { return array(); }
	public static function parse_scores( $r ) { return array(); }
}

require $P . 'widgets/class-zlaark-widget-base.php';
require $P . 'widgets/class-zlaark-compare-widget.php';

$pass = 0; $fail = 0;
function ck( $l, $got, $want ) {
	global $pass, $fail;
	if ( $got === $want ) { $pass++; printf( "  ok    %-44s %s\n", $l, json_encode( $got ) ); }
	else { $fail++; printf( "  FAIL  %-44s got %s want %s\n", $l, json_encode( $got ), json_encode( $want ) ); }
}

/* ------------------------------------------------ 1. ?deals= parsing ----- */

echo "\n== ids_from_url() against hostile input ==\n";

class ZD_URL_Probe extends Zlaark_Compare_Widget {
	public function probe() { return $this->ids_from_url(); }
}
$probe = new ZD_URL_Probe();

$cases = array(
	array( 'absent',              null,                            array() ),
	array( 'empty',               '',                              array() ),
	array( 'normal',              '12,34',                         array( 12, 34 ) ),
	array( 'spaces',              ' 12 , 34 ',                     array( 12, 34 ) ),
	array( 'duplicates collapse', '12,12,34',                      array( 12, 34 ) ),
	array( 'zero dropped',        '0,12',                          array( 12 ) ),
	array( 'negatives absint',    '-12,34',                        array( 12, 34 ) ),
	array( 'letters dropped',     'abc,12',                        array( 12 ) ),
	array( 'sql injection',       "12 OR 1=1; DROP TABLE wp_posts", array( 12 ) ),
	array( 'script tag',          '<script>alert(1)</script>,7',   array( 7 ) ),
	array( 'capped at six',       '1,2,3,4,5,6,7,8,9',             array( 1, 2, 3, 4, 5, 6 ) ),
);

foreach ( $cases as $c ) {
	list( $label, $input, $want ) = $c;
	unset( $_GET['deals'] );
	if ( null !== $input ) { $_GET['deals'] = $input; }
	ck( $label, $probe->probe(), $want );
}
unset( $_GET['deals'] );

/* --------------------------------------------------- 2. accessibility --- */

echo "\n== accessibility of the emitted markup ==\n";

$WIDGETS = array(
	'homepage' => 'Zlaark_Homepage_Widget', 'deals' => 'Zlaark_Deals_Widget',
	'index' => 'Zlaark_Index_Widget', 'top-picks' => 'Zlaark_Top_Picks_Widget',
	'compare' => 'Zlaark_Compare_Widget', 'panel' => 'Zlaark_Panel_Widget',
	'stats' => 'Zlaark_Stats_Widget',
);

foreach ( $WIDGETS as $slug => $class ) {
	require_once $P . 'widgets/class-zlaark-' . $slug . '-widget.php';
	$w = new $class();
	$w->zd_build();
	ob_start();
	$w->zd_render();
	$html = ob_get_clean();

	$issues = array();

	// images must carry an alt attribute
	preg_match_all( '/<img\b[^>]*>/i', $html, $imgs );
	foreach ( $imgs[0] as $img ) {
		if ( ! preg_match( '/\balt\s*=/i', $img ) ) { $issues[] = 'img without alt'; break; }
	}

	// heading levels must not skip
	preg_match_all( '/<h([1-6])\b/i', $html, $hs );
	$levels = array_map( 'intval', $hs[1] );
	for ( $i = 1; $i < count( $levels ); $i++ ) {
		if ( $levels[ $i ] - $levels[ $i - 1 ] > 1 ) {
			$issues[] = sprintf( 'heading skip h%d->h%d', $levels[ $i - 1 ], $levels[ $i ] );
			break;
		}
	}

	// every button and link needs a discernible name
	preg_match_all( '/<(a|button)\b([^>]*)>(.*?)<\/\1>/is', $html, $ctrl, PREG_SET_ORDER );
	foreach ( $ctrl as $m ) {
		$text = trim( wp_strip_all_tags( $m[3] ) );
		$has  = $text !== ''
			|| preg_match( '/aria-label\s*=/i', $m[2] )
			|| preg_match( '/<img[^>]+alt="[^"]+"/i', $m[3] )
			|| preg_match( '/screen-reader-text/i', $m[3] );
		if ( ! $has ) { $issues[] = 'unnamed <' . $m[1] . '>'; break; }
	}

	// inputs need a label, an aria-label or a wrapping <label>
	preg_match_all( '/<input\b([^>]*)>/i', $html, $ins );
	foreach ( $ins[1] as $attrs ) {
		if ( preg_match( '/type\s*=\s*"(hidden|checkbox)"/i', $attrs ) ) { continue; }
		if ( ! preg_match( '/aria-label|id\s*=/i', $attrs ) && ! preg_match( '/<label/i', $html ) ) {
			$issues[] = 'unlabelled input';
			break;
		}
	}

	ck( $slug, $issues, array() );
}

if ( $warnings ) {
	echo "\nWARNINGS: " . implode( ' | ', array_slice( $warnings, 0, 4 ) ) . "\n";
	$fail += count( $warnings );
}

echo "\n" . str_repeat( '-', 70 ) . "\n";
printf( "%d passed, %d failed\n", $pass, $fail );
exit( $fail ? 1 : 0 );
