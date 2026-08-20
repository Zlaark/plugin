<?php
/** Dumps every widget in sequence into one page so each can be screenshotted. */
error_reporting( E_ALL & ~E_WARNING & ~E_NOTICE );
$GLOBALS['zd_fake_posts'] = 6;
require __DIR__ . '/elementor-stub.php';
$P = dirname( __DIR__ ) . '/';
require $P . 'includes/class-zlaark-deals-computed.php';
require $P . 'includes/class-zlaark-deals-panel.php';
require __DIR__ . '/fixtures.php';
class Zlaark_Deals_Settings { const SCHEMA = 2; public static function get( $k ) { return 1; } }

$THIN = ( isset( $argv[1] ) && 'thin' === $argv[1] );

class Zlaark_Deals_Meta {
	const FIELDS = array();
	public static function offer_types() {
		return array( '' => '-', 'coupon' => 'Coupon', 'exclusive' => 'Exclusive',
		              'free_trial' => 'Free trial', 'free_plan' => 'Free plan', 'seasonal' => 'Seasonal' );
	}
	public static function get_deal_data( $p ) {
		global $THIN;
		$id = is_object( $p ) ? $p->ID : (int) $p;
		$base = zd_fixture_deals();
		$d = $base[0];
		$names = array( 1 => 'A2 Hosting', 2 => 'Hostinger', 3 => 'HawkHost',
		                4 => 'DreamHost', 5 => 'ClickUp', 6 => 'GoDaddy' );
		$price = array( 1 => array('$5.00/mo',''), 2 => array('$0.83/mo','$4.78/mo'),
		                3 => array('$2.49/mo','$4.99/mo'), 4 => array('$2.59/mo','$4.95/mo'),
		                5 => array('','Free Forever'), 6 => array('$5.99/mo','$10.99/mo') );
		$d['id'] = $id;
		$d['title'] = isset( $names[ $id ] ) ? $names[ $id ] : 'Deal ' . $id;
		$d['price'] = $price[ $id ][0];
		$d['old_price'] = ( 5 === $id ) ? '' : $price[ $id ][1];
		$d['offer_headline'] = ( 5 === $id ) ? 'Free Forever' : '';
		$d['button_text'] = 'Get Deal';
		$d['image_id'] = 0;
		$cats = array( 1 => 'Web Hosting', 2 => 'Web Hosting', 3 => 'Web Hosting',
		               4 => 'Web Hosting', 5 => 'Project Management', 6 => 'Domains' );
		$t = new stdClass(); $t->term_id = crc32( $cats[ $id ] ) % 1000; $t->name = $cats[ $id ];
		$d['terms'] = array( $t );
		$soon = array( 2 => '+3 days', 3 => '+6 days' );
		$d['expiry_date'] = isset( $soon[ $id ] ) ? gmdate( 'Y-m-d', strtotime( $soon[ $id ] ) ) : $d['expiry_date'];
		if ( $THIN ) {
			foreach ( array( 'tagline','renewal_price','coupon_code','verdict','reviewer',
			                 'tested_date','last_verified','expiry_date','refund_window',
			                 'review_url','rank_label','badge','offer_type' ) as $k ) { $d[ $k ] = ''; }
			$d['scores'] = array(); $d['highlights'] = array();
			$d['best_for'] = array(); $d['not_for'] = array();
			$d['rating'] = null; $d['term_length'] = 0;
		}
		$c = 'Zlaark_Deals_Computed';
		$d['discount_pct']     = $c::discount_pct( $d['price'], $d['old_price'] );
		$d['first_term_total'] = $c::first_term_total( $d['price'], $d['term_length'] );
		$d['overall_score']    = $c::overall_score( $d['scores'], $d['rating'] );
		$d['score_band']       = $c::score_band( $d['overall_score'] );
		$d['is_expired']       = $c::is_expired( $d['expiry_date'] );
		$d['urgency_label']    = $c::urgency_label( $d['expiry_date'] );
		$d['verified_label']   = $c::verified_label( $d['last_verified'] );
		return $d;
	}
	public static function parse_lines( $r ) { return array(); }
	public static function parse_scores( $r ) { return array(); }
}

require $P . 'widgets/class-zlaark-widget-base.php';

$WIDGETS = array(
	'navbar' => 'Zlaark_Navbar_Widget',
	'hero' => 'Zlaark_Hero_Widget', 'hero-classic' => 'Zlaark_Hero_Classic_Widget',
	'hero-bento' => 'Zlaark_Hero_Bento_Widget', 'hero-fresh' => 'Zlaark_Hero_Fresh_Widget',
	'top-picks' => 'Zlaark_Top_Picks_Widget', 'deals' => 'Zlaark_Deals_Widget',
	'compare' => 'Zlaark_Compare_Widget', 'index' => 'Zlaark_Index_Widget',
	'panel' => 'Zlaark_Panel_Widget', 'about' => 'Zlaark_About_Widget',
	'stats' => 'Zlaark_Stats_Widget', 'marquee' => 'Zlaark_Marquee_Widget',
	'footer' => 'Zlaark_Footer_Widget',
);

foreach ( $WIDGETS as $slug => $class ) {
	require_once $P . 'widgets/class-zlaark-' . $slug . '-widget.php';
	echo '<div class="zd-probe" id="probe-' . $slug . '" data-slug="' . $slug . '">';
	try {
		$w = new $class(); $w->zd_build();
		ob_start(); $w->zd_render(); echo ob_get_clean();
	} catch ( \Throwable $e ) {
		while ( ob_get_level() > 0 ) { ob_end_clean(); }
		echo '<p style="color:red">FATAL ' . htmlspecialchars( $e->getMessage() ) . '</p>';
	}
	echo '</div>';
}
