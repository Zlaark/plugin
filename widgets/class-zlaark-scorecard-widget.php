<?php
/**
 * Zlaark Scorecard - the "scorecard" section of the Homepage widget, on its own.
 *
 * The scored pick row: rank cap, brand, price and the measured score bars.
 *
 * Subclasses the Homepage widget so the controls and the markup have exactly
 * one implementation. See class-zlaark-section-widget-base.php.
 *
 * @package Zlaark_Deals_Pro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Zlaark_Scorecard_Widget extends Zlaark_Section_Widget_Base {

	public function get_name() {
		return 'zlaark_sec_scorecard';
	}

	public function get_title() {
		return __( 'Zlaark Scorecard', 'zlaark-deals-pro' );
	}

	public function get_icon() {
		return 'eicon-price-table';
	}

	public function get_keywords() {
		return array( 'scorecard','ranked','scores','picks', 'zlaark', 'section' );
	}

	protected function section_key() {
		return 'scorecard';
	}

	protected function section_order() {
		return 'score';
	}
}
