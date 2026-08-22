<?php
/**
 * Renders the widgets against the REFERENCE JSON rather than the fixtures.
 *
 * The fixtures prove the markup holds together; this proves the shipped seed
 * data actually produces a page worth looking at. A component can be perfect
 * and still render badly if the data behind it is thin, and that gap is only
 * visible when the two are put together.
 */

error_reporting( E_ALL );
ini_set( 'display_errors', '1' );

$GLOBALS['zd_fake_posts'] = 6;
require __DIR__ . '/elementor-stub.php';

$P = dirname( __DIR__ ) . '/';
require __DIR__ . '/fixtures.php';
require $P . 'includes/class-zlaark-deals-computed.php';
require $P . 'includes/class-zlaark-deals-articles.php';
require $P . 'includes/class-zlaark-deals-panel.php';

class Zlaark_Deals_Settings { const SCHEMA = 2; public static function get( $k ) { return 1; } }

$RAW = json_decode( file_get_contents( $P . 'zlaark-deals-import.json' ), true );

/** Turns one exported row into the shape get_deal_data() returns. */
function zd_json_deal( $row, $index ) {
	$m = $row['meta'];

	$lines = function ( $raw ) {
		$out = array();
		foreach ( preg_split( '/\r\n|\r|\n/', (string) $raw ) as $l ) {
			$l = trim( $l );
			if ( '' !== $l ) { $out[] = $l; }
		}
		return $out;
	};

	$scores = array();
	foreach ( $lines( $m['_zlaark_scores'] ) as $l ) {
		$p = explode( '|', $l, 2 );
		$scores[] = array(
			'label' => trim( $p[0] ),
			'value' => isset( $p[1] ) && is_numeric( trim( $p[1] ) ) ? (float) trim( $p[1] ) : null,
		);
	}

	$terms = array();
	foreach ( $row['categories'] as $slug ) {
		$t = new stdClass();
		$t->term_id = crc32( $slug ) % 1000;
		$t->name    = ucwords( str_replace( '-', ' ', $slug ) );
		$t->slug    = $slug;
		$terms[]    = $t;
	}

	$d = array(
		'id' => $index + 1, 'title' => $row['title'],
		'permalink' => 'https://example.com/' . $row['slug'],
		'image_id' => 0, 'terms' => $terms,
		'tagline' => $m['_zlaark_tagline'], 'price' => $m['_zlaark_price'],
		'old_price' => $m['_zlaark_old_price'], 'badge' => $m['_zlaark_badge'],
		'rank_label' => $m['_zlaark_rank_label'],
		'rating' => '' === $m['_zlaark_rating'] ? null : (float) $m['_zlaark_rating'],
		'highlights' => $lines( $m['_zlaark_highlights'] ), 'scores' => $scores,
		'button_text' => $m['_zlaark_button_text'], 'button_url' => $m['_zlaark_button_url'],
		'button_new' => (bool) $m['_zlaark_button_new'],
		'offer_type' => $m['_zlaark_offer_type'], 'offer_headline' => $m['_zlaark_offer_headline'],
		'renewal_price' => $m['_zlaark_renewal_price'], 'term_length' => (int) $m['_zlaark_term_length'],
		'coupon_code' => $m['_zlaark_coupon_code'], 'currency' => $m['_zlaark_currency'],
		'verdict' => $m['_zlaark_verdict'], 'reviewer' => $m['_zlaark_reviewer'],
		'tested_date' => $m['_zlaark_tested_date'], 'last_verified' => $m['_zlaark_last_verified'],
		'expiry_date' => $m['_zlaark_expiry_date'], 'refund_window' => $m['_zlaark_refund_window'],
		'review_url' => $m['_zlaark_review_url'],
		'best_for' => $lines( $m['_zlaark_best_for'] ), 'not_for' => $lines( $m['_zlaark_not_for'] ),
		'pros' => $lines( $m['_zlaark_pros'] ), 'cons' => $lines( $m['_zlaark_cons'] ),
	);

	$c = 'Zlaark_Deals_Computed';
	$d['discount_pct']     = $c::discount_pct( $d['price'], $d['old_price'] );
	$d['annual_saving']    = $c::annual_saving( $d['price'], $d['old_price'] );
	$d['first_term_total'] = $c::first_term_total( $d['price'], $d['term_length'] );
	$d['overall_score']    = $c::overall_score( $d['scores'], $d['rating'] );
	$d['score_band']       = $c::score_band( $d['overall_score'] );
	$d['days_remaining']   = $c::days_until( $d['expiry_date'] );
	$d['is_expired']       = $c::is_expired( $d['expiry_date'] );
	$d['urgency_label']    = $c::urgency_label( $d['expiry_date'] );
	$d['verified_label']   = $c::verified_label( $d['last_verified'] );

	return $d;
}

$ZD_DEALS = array();
foreach ( $RAW['deals'] as $i => $row ) {
	$ZD_DEALS[] = zd_json_deal( $row, $i );
}

class Zlaark_Deals_Meta {
	const FIELDS = array();
	public static function offer_types() {
		return array( 'coupon' => 'Coupon', 'exclusive' => 'Exclusive',
			'free_trial' => 'Free trial', 'free_plan' => 'Free plan', 'seasonal' => 'Seasonal' );
	}
	public static function get_deal_data( $p ) {
		$i = is_object( $p ) ? (int) $p->ID : (int) $p;
		return isset( $GLOBALS['ZD_DEALS'][ $i - 1 ] ) ? $GLOBALS['ZD_DEALS'][ $i - 1 ] : array();
	}
	public static function parse_lines( $r ) { return array(); }
	public static function parse_scores( $r ) { return array(); }
}

$GLOBALS['ZD_DEALS']      = $ZD_DEALS;
$GLOBALS['zd_fake_posts'] = count( $ZD_DEALS );

require $P . 'widgets/class-zlaark-widget-base.php';
require $P . 'widgets/class-zlaark-homepage-widget.php';
require $P . 'widgets/class-zlaark-section-widget-base.php';

$WIDGETS = array(
	'lineup'  => 'Zlaark_Lineup_Widget',
	'byline'  => 'Zlaark_Byline_Widget',
	'verdict' => 'Zlaark_Verdict_Widget',
	'compare' => 'Zlaark_Compare_Widget',
);

foreach ( $WIDGETS as $slug => $class ) {
	require_once $P . 'widgets/class-zlaark-' . $slug . '-widget.php';
	echo '<div class="zd-probe" id="probe-' . $slug . '" data-slug="' . $slug . '">';
	try {
		$w = new $class();
		$w->zd_build();
		ob_start();
		$w->zd_render();
		echo ob_get_clean();
	} catch ( Throwable $e ) {
		echo '<p style="color:#b00">FATAL: ' . htmlspecialchars( $e->getMessage() ) . '</p>';
	}
	echo '</div>';
}
