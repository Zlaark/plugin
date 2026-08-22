<?php
/**
 * Zlaark Reviews Strip - the "reviews" section of the Homepage widget, on its own.
 *
 * A rail of review articles pulled from posts, by category or hand-picked.
 *
 * Subclasses the Homepage widget so the controls and the markup have exactly
 * one implementation. See class-zlaark-section-widget-base.php.
 *
 * @package Zlaark_Deals_Pro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Zlaark_Reviews_Widget extends Zlaark_Section_Widget_Base {

	public function get_name() {
		return 'zlaark_sec_reviews';
	}

	public function get_title() {
		return __( 'Zlaark Reviews Strip', 'zlaark-deals-pro' );
	}

	public function get_icon() {
		return 'eicon-post-list';
	}

	public function get_keywords() {
		return array( 'reviews', 'articles', 'posts', 'rail', 'zlaark', 'section' );
	}

	protected function section_key() {
		return 'reviews';
	}

	protected function section_order() {
		return 'none';
	}
}
