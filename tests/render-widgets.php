<?php
/**
 * Renders every widget against a POPULATED catalogue.
 *
 * The registration harness proves the controls build; this proves the markup
 * actually renders. It matters because the editor preview runs render(), and a
 * notice there is as fatal to the editor as one during registration.
 *
 * Three deals are used deliberately: one fully filled, one minimal (only the
 * required fields), and one expired — so every `if ( '' !== ... )` branch and
 * every computed value gets exercised in both directions.
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
require $P . 'includes/class-zlaark-deals-computed.php';
require $P . 'includes/class-zlaark-deals-panel.php';
require $P . 'includes/class-zlaark-deals-articles.php';

class Zlaark_Deals_Settings { const SCHEMA = 2; public static function get( $k ) { return 1; } }

require __DIR__ . '/fixtures.php';

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
require $P . 'widgets/class-zlaark-homepage-widget.php';
require $P . 'widgets/class-zlaark-section-widget-base.php';

$WIDGETS = array(
	'homepage' => 'Zlaark_Homepage_Widget', 'navbar' => 'Zlaark_Navbar_Widget', 'footer' => 'Zlaark_Footer_Widget',
	'hero' => 'Zlaark_Hero_Widget', 'hero-classic' => 'Zlaark_Hero_Classic_Widget',
	'hero-bento' => 'Zlaark_Hero_Bento_Widget', 'hero-fresh' => 'Zlaark_Hero_Fresh_Widget',
	'about' => 'Zlaark_About_Widget', 'deals' => 'Zlaark_Deals_Widget',
	'index' => 'Zlaark_Index_Widget', 'top-picks' => 'Zlaark_Top_Picks_Widget',
	'compare' => 'Zlaark_Compare_Widget', 'panel' => 'Zlaark_Panel_Widget',
	'stats' => 'Zlaark_Stats_Widget', 'marquee' => 'Zlaark_Marquee_Widget',
	'scorecard' => 'Zlaark_Scorecard_Widget', 'band' => 'Zlaark_Band_Widget',
	'categories' => 'Zlaark_Categories_Widget', 'expiring' => 'Zlaark_Expiring_Widget',
	'method' => 'Zlaark_Method_Widget', 'aboutus' => 'Zlaark_About_Us_Widget',
	'faq' => 'Zlaark_Faq_Widget', 'cta' => 'Zlaark_Cta_Widget',
	'lineup' => 'Zlaark_Lineup_Widget', 'reviews' => 'Zlaark_Reviews_Widget',
	'comparisons' => 'Zlaark_Comparisons_Widget', 'grid' => 'Zlaark_Article_Grid_Widget',
	'testimonials' => 'Zlaark_Testimonials_Widget',
	'byline' => 'Zlaark_Byline_Widget', 'verdict' => 'Zlaark_Verdict_Widget',
	'offerbar' => 'Zlaark_Offerbar_Widget',
);

$pass = 0; $fail = 0;

echo "rendering every widget against a populated catalogue\n";
echo str_repeat( '-', 78 ) . "\n";

foreach ( $WIDGETS as $slug => $class ) {
	require_once $P . 'widgets/class-zlaark-' . $slug . '-widget.php';
	$before = count( $warnings );

	try {
		$w = new $class();
		$w->zd_build();

		ob_start();
		$w->zd_render();
		$html = ob_get_clean();

		$new = array_slice( $warnings, $before );

		// unbalanced markup is a silent layout-breaker
		$open  = preg_match_all( '/<(div|article|section|aside|span|p|ul|li|a|h[1-6])\b[^>]*(?<!\/)>/i', $html );
		$close = preg_match_all( '/<\/(div|article|section|aside|span|p|ul|li|a|h[1-6])>/i', $html );

		if ( $new ) {
			printf( "  %-14s %6d bytes  WARNINGS: %s\n", $slug, strlen( $html ), implode( ' | ', array_slice( $new, 0, 2 ) ) );
			$fail++;
		} elseif ( $open !== $close ) {
			printf( "  %-14s %6d bytes  UNBALANCED TAGS: %d open vs %d close\n", $slug, strlen( $html ), $open, $close );
			$fail++;
		} else {
			printf( "  %-14s %6d bytes  tags balanced (%d)\n", $slug, strlen( $html ), $open );
			$pass++;
		}
	} catch ( \Throwable $e ) {
		while ( ob_get_level() > 0 ) { ob_end_clean(); }
		printf( "  %-14s FATAL: %s\n", $slug, $e->getMessage() );
		$fail++;
	}
}

echo str_repeat( '-', 78 ) . "\n";
printf( "%d rendered cleanly, %d failed\n", $pass, $fail );
exit( $fail > 0 ? 1 : 0 );
