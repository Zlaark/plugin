<?php
/**
 * What a page of Zlaark widgets costs the database.
 *
 * The Elementor editor renders the whole page in a preview request before it
 * will show the canvas, so anything that makes a page slow makes the editor
 * look hung. Two things did:
 *
 *   1. Every standalone section widget inherited the Homepage widget's pool of
 *      sixty deals and hydrated all sixty to draw three cards.
 *   2. Each deal's categories came from wp_get_post_terms(), which goes to the
 *      database every call - no cache, primed or otherwise.
 *
 * This pins both down. It counts rows pulled and deals assembled across a page
 * built from the section widgets, so a regression shows up as a number rather
 * than as a slow site nobody can measure.
 */

error_reporting( E_ALL );
ini_set( 'display_errors', '1' );

$warnings = array();
set_error_handler( function ( $no, $str, $file, $line ) use ( &$warnings ) {
	$warnings[] = sprintf( '%s (%s:%d)', $str, basename( $file ), $line );
	return true;
} );

// A catalogue deep enough that over-fetching is visible.
$GLOBALS['zd_fake_posts'] = 60;
$GLOBALS['zd_queries']    = array();

require __DIR__ . '/elementor-stub.php';

$P = dirname( __DIR__ ) . '/';
require __DIR__ . '/fixtures.php';
require $P . 'includes/class-zlaark-deals-computed.php';
require $P . 'includes/class-zlaark-deals-panel.php';
require $P . 'includes/class-zlaark-deals-articles.php';

class Zlaark_Deals_Settings { const SCHEMA = 2; public static function get( $k ) { return 1; } }

/**
 * Counts what get_deal_data() would cost. One "hydration" is the block of
 * ~30 meta reads plus the category lookup that assembling one deal takes.
 */
class Zlaark_Deals_Meta {
	const FIELDS = array();
	public static $hydrations = 0;
	public static $term_reads = 0;

	public static function offer_types() { return array( '' => '-' ); }
	public static function parse_lines( $r ) { return array(); }
	public static function parse_scores( $r ) { return array(); }

	public static function get_deal_data( $post ) {
		$id = is_object( $post ) ? $post->ID : (int) $post;

		self::$hydrations++;
		self::$term_reads++;

		$deals = zd_fixture_deals();
		$deal  = $deals[0];
		$deal['id'] = $id;

		return $deal;
	}
}

require $P . 'widgets/class-zlaark-widget-base.php';
require $P . 'widgets/class-zlaark-homepage-widget.php';
require $P . 'widgets/class-zlaark-section-widget-base.php';

/** The section widgets that actually query deals. */
$SECTIONS = array(
	'scorecard' => 'Zlaark_Scorecard_Widget',
	'band'      => 'Zlaark_Band_Widget',
	'lineup'    => 'Zlaark_Lineup_Widget',
	'expiring'  => 'Zlaark_Expiring_Widget',
);

foreach ( $SECTIONS as $slug => $class ) {
	require_once $P . 'widgets/class-zlaark-' . $slug . '-widget.php';
}

$pass = 0;
$fail = 0;

function zd_check( $label, $got, $ok, $detail = '' ) {
	global $pass, $fail;
	printf( "  %-42s %-10s %s%s\n", $label, $got, $ok ? 'ok' : 'FAILED', $detail ? '  ' . $detail : '' );
	$ok ? $pass++ : $fail++;
}

echo "what a page of section widgets costs\n";
echo str_repeat( '-', 74 ) . "\n";

/* ------------------------------------------------- one section on its own */

$GLOBALS['zd_queries']            = array();
Zlaark_Deals_Meta::$hydrations    = 0;

$w = new Zlaark_Scorecard_Widget();
$w->zd_build();
ob_start();
$w->zd_render();
ob_end_clean();

$rows = 0;
foreach ( $GLOBALS['zd_queries'] as $q ) {
	$rows += isset( $q['posts_per_page'] ) ? (int) $q['posts_per_page'] : 0;
}

// query_controls( 12 ) is the section default, plus the dedupe headroom.
zd_check( 'one section: rows requested', $rows, 18 === $rows, '(12 chosen + 6 headroom)' );
zd_check( 'one section: deals hydrated', Zlaark_Deals_Meta::$hydrations,
	Zlaark_Deals_Meta::$hydrations <= 18, '(must not exceed the rows asked for)' );
zd_check( 'one section: queries run', count( $GLOBALS['zd_queries'] ),
	1 === count( $GLOBALS['zd_queries'] ), '(one query per section)' );

/* --------------------------------------------- a page built from four of them */

$GLOBALS['zd_queries']         = array();
Zlaark_Deals_Meta::$hydrations = 0;

foreach ( $SECTIONS as $slug => $class ) {
	$widget = new $class();
	$widget->zd_build();
	ob_start();
	$widget->zd_render();
	ob_end_clean();
}

$page_rows = 0;
foreach ( $GLOBALS['zd_queries'] as $q ) {
	$page_rows += isset( $q['posts_per_page'] ) ? (int) $q['posts_per_page'] : 0;
}

$before = count( $SECTIONS ) * 60; // what the inherited pool used to cost

zd_check( 'four sections: rows requested', $page_rows, $page_rows <= 4 * 18,
	sprintf( '(was %d)', $before ) );
zd_check( 'four sections: deals hydrated', Zlaark_Deals_Meta::$hydrations,
	Zlaark_Deals_Meta::$hydrations <= 4 * 18, sprintf( '(was up to %d)', $before ) );

/* ------------------------------------ the homepage widget keeps its deep pool */

$GLOBALS['zd_queries'] = array();

$home = new Zlaark_Homepage_Widget();
$home->zd_build();
ob_start();
$home->zd_render();
ob_end_clean();

$home_rows = isset( $GLOBALS['zd_queries'][0]['posts_per_page'] )
	? (int) $GLOBALS['zd_queries'][0]['posts_per_page'] : 0;

zd_check( 'homepage: one query, deep pool', $home_rows, 60 === $home_rows,
	'(a dozen sections share it)' );
zd_check( 'homepage: queries run', count( $GLOBALS['zd_queries'] ),
	1 === count( $GLOBALS['zd_queries'] ), '(not one per section)' );

/* --------------------------------------- no uncached term reads in the render */

$uncached = 0;
foreach ( $GLOBALS['zd_calls'] as $c ) {
	if ( 'wp_get_post_terms' === $c ) { $uncached++; }
}
zd_check( 'uncached wp_get_post_terms in render', $uncached, 0 === $uncached,
	'(get_the_terms reads the primed cache)' );

zd_check( 'warnings raised', count( $warnings ), empty( $warnings ),
	$warnings ? implode( ' | ', array_slice( $warnings, 0, 2 ) ) : '' );

echo str_repeat( '-', 74 ) . "\n";
printf( "%d passed, %d failed\n", $pass, $fail );
exit( $fail > 0 ? 1 : 0 );
