<?php
/**
 * Zlaark Category Lineup - the "lineup" section of the Homepage widget, on its own.
 *
 * The tabbed four-up: a scrolling category rail over ranked cards
 * with star ratings, review links and a positional cap colour.
 *
 * Subclasses the Homepage widget so the controls and the markup have exactly
 * one implementation. See class-zlaark-section-widget-base.php.
 *
 * @package Zlaark_Deals_Pro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Zlaark_Lineup_Widget extends Zlaark_Section_Widget_Base {

	public function get_name() {
		return 'zlaark_sec_lineup';
	}

	public function get_title() {
		return __( 'Zlaark Category Lineup', 'zlaark-deals-pro' );
	}

	public function get_icon() {
		return 'eicon-tabs';
	}

	public function get_keywords() {
		return array( 'lineup', 'tabs', 'categories', 'picks', 'zlaark', 'section' );
	}

	protected function section_key() {
		return 'lineup';
	}

	protected function section_order() {
		return 'score';
	}
}
