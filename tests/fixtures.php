<?php

/** A stand-in post so the stubbed WP_Query loop has something to hand back. */
if ( ! class_exists( 'ZD_Fake_Post' ) ) {
	class ZD_Fake_Post {
		public $ID = 1;
		public $post_title = 'A2 Hosting';
		public $post_name = 'a2-hosting';
		public function __construct( $id ) { $this->ID = $id; }
	}
}

/** Three deals covering the full / minimal / expired cases. */
function zd_fixture_deals() {
	$mk = function ( $over ) {
		$base = array(
			'id' => 1, 'title' => 'A2 Hosting', 'permalink' => 'https://example.com/a2',
			'image_id' => 0, 'tagline' => 'Turbo servers with 20x faster page loads.',
			'price' => '$5.00/mo', 'old_price' => '$12.99/mo', 'badge' => 'Best Value',
			'rank_label' => 'Best overall', 'rating' => 9.2,
			'highlights' => array( 'Free migration', '99.9% uptime', '24/7 support' ),
			'scores' => array(
				array( 'label' => 'Speed', 'value' => 9.6 ),
				array( 'label' => 'Uptime', 'value' => 9.4 ),
				array( 'label' => 'Value', 'value' => 7.2 ),
			),
			'button_text' => 'Get this deal', 'button_url' => 'https://a2hosting.com/?aid=1',
			'button_new' => true, 'terms' => array(),
			'offer_type' => 'coupon', 'offer_headline' => '61% off',
			'renewal_price' => '$12.99/mo', 'term_length' => 36, 'coupon_code' => 'BYN2026',
			'currency' => 'USD', 'verdict' => 'The best host under six dollars we have measured.',
			'reviewer' => 'Kanish', 'tested_date' => '2026-03-01', 'last_verified' => '2026-08-14',
			'expiry_date' => '2026-12-01', 'refund_window' => '30-day money back',
			'review_url' => 'https://example.com/review',
			'best_for' => array( 'High-traffic WordPress' ), 'not_for' => array( 'Tiny static sites' ),
			'pros' => array( 'Fast' ), 'cons' => array( 'Renews high' ),
		);
		$d = array_merge( $base, $over );

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
	};

	return array(
		$mk( array() ),
		// minimal: only what the README calls required
		$mk( array(
			'id' => 2, 'title' => 'Bare Minimum Co', 'tagline' => '', 'price' => '', 'old_price' => '',
			'badge' => '', 'rank_label' => '', 'rating' => null, 'highlights' => array(),
			'scores' => array(), 'renewal_price' => '', 'term_length' => 0, 'coupon_code' => '',
			'verdict' => '', 'reviewer' => '', 'tested_date' => '', 'last_verified' => '',
			'expiry_date' => '', 'refund_window' => '', 'review_url' => '',
			'best_for' => array(), 'not_for' => array(), 'pros' => array(), 'cons' => array(),
			'offer_type' => '', 'offer_headline' => 'Free forever',
		) ),
		// expired
		$mk( array( 'id' => 3, 'title' => 'Lapsed Offer', 'expiry_date' => '2020-01-01' ) ),
	);
}
