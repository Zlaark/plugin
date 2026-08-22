<?php
/** Validates the JSON-LD emitted for a deal. */
error_reporting( E_ALL );
ini_set( 'display_errors', '1' );
$warnings = array();
set_error_handler( function ( $n, $s, $f, $l ) use ( &$warnings ) { $warnings[] = "$s (" . basename($f) . ":$l)"; return true; } );

$GLOBALS['zd_fake_posts'] = 0;
require __DIR__ . '/elementor-stub.php';
$P = dirname( __DIR__ ) . '/';
require $P . 'includes/class-zlaark-deals-computed.php';
require $P . 'includes/class-zlaark-deals-panel.php';
require __DIR__ . '/fixtures.php';
class Zlaark_Deals_Meta {
	public static function get_deal_data( $p ) { $d = zd_fixture_deals(); return $d[0]; }
}
require $P . 'includes/class-zlaark-deals-schema.php';

$deals = zd_fixture_deals();
$pass = 0; $fail = 0;
function ck( $l, $got, $want ) {
	global $pass, $fail;
	if ( $got === $want ) { $pass++; printf( "  ok    %-42s %s\n", $l, var_export( $got, true ) ); }
	else { $fail++; printf( "  FAIL  %-42s got %s want %s\n", $l, var_export( $got, true ), var_export( $want, true ) ); }
}

echo "\n== full deal ==\n";
$g = Zlaark_Deals_Schema::deal_graph( $deals[0] );
ck( '@type', $g['@type'], 'Product' );
ck( 'has offers', isset( $g['offers'] ), true );
ck( 'offer price is plain decimal', $g['offers']['price'], '5.00' );
ck( 'offer currency', $g['offers']['priceCurrency'], 'USD' );
ck( 'availability', $g['offers']['availability'], 'https://schema.org/InStock' );
ck( 'priceValidUntil from expiry', $g['offers']['priceValidUntil'], '2026-12-01' );
// Derived from the fixture rather than hardcoded, so widening the score
// breakdown does not silently turn a correct average into a red test.
$expected_overall = number_format( array_sum( array_column( $deals[0]['scores'], 'value' ) ) / count( $deals[0]['scores'] ), 1 );
ck( 'aggregateRating value', $g['aggregateRating']['ratingValue'], $expected_overall );
ck( 'aggregateRating bestRating', $g['aggregateRating']['bestRating'], '10' );
ck( 'review author is Person', $g['review']['author']['@type'], 'Person' );
ck( 'review has rating', isset( $g['review']['reviewRating'] ), true );
ck( 'review datePublished', $g['review']['datePublished'], '2026-03-01' );
ck( 'json encodes', is_string( json_encode( $g ) ), true );

echo "\n== minimal deal (only required fields) ==\n";
$m = Zlaark_Deals_Schema::deal_graph( $deals[1] );
ck( '@type', $m['@type'], 'Product' );
ck( 'no offers without a price', isset( $m['offers'] ), false );
ck( 'no review without a verdict', isset( $m['review'] ), false );
ck( 'json encodes', is_string( json_encode( $m ) ), true );

echo "\n== expired deal ==\n";
$e = Zlaark_Deals_Schema::deal_graph( $deals[2] );
ck( 'availability flips', $e['offers']['availability'], 'https://schema.org/Discontinued' );

echo "\n== guards ==\n";
ck( 'empty deal returns null', Zlaark_Deals_Schema::deal_graph( array() ), null );

if ( $warnings ) { echo "\nWARNINGS: " . implode( ' | ', $warnings ) . "\n"; $fail += count( $warnings ); }
echo "\n" . str_repeat( '-', 66 ) . "\n";
printf( "%d passed, %d failed\n", $pass, $fail );
exit( $fail ? 1 : 0 );
