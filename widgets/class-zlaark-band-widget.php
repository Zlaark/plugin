<?php
/**
 * Zlaark Methodology Band - the "band" section of the Homepage widget, on its own.
 *
 * The dark trust band - how the numbers on this site are produced.
 *
 * Subclasses the Homepage widget so the controls and the markup have exactly
 * one implementation. See class-zlaark-section-widget-base.php.
 *
 * @package Zlaark_Deals_Pro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Zlaark_Band_Widget extends Zlaark_Section_Widget_Base {

	public function get_name() {
		return 'zlaark_sec_band';
	}

	public function get_title() {
		return __( 'Zlaark Methodology Band', 'zlaark-deals-pro' );
	}

	public function get_icon() {
		return 'eicon-info-box';
	}

	public function get_keywords() {
		return array( 'methodology','trust','band','how we work', 'zlaark', 'section' );
	}

	protected function section_key() {
		return 'band';
	}

	protected function section_order() {
		return 'saving';
	}
}
