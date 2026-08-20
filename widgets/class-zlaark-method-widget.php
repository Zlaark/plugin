<?php
/**
 * Zlaark How We Test - the "method" section of the Homepage widget, on its own.
 *
 * The numbered testing process - a real sequence, so it is numbered.
 *
 * Subclasses the Homepage widget so the controls and the markup have exactly
 * one implementation. See class-zlaark-section-widget-base.php.
 *
 * @package Zlaark_Deals_Pro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Zlaark_Method_Widget extends Zlaark_Section_Widget_Base {

	public function get_name() {
		return 'zlaark_sec_method';
	}

	public function get_title() {
		return __( 'Zlaark How We Test', 'zlaark-deals-pro' );
	}

	public function get_icon() {
		return 'eicon-checklist';
	}

	public function get_keywords() {
		return array( 'method','how we test','process','steps', 'zlaark', 'section' );
	}

	protected function section_key() {
		return 'method';
	}

	protected function section_order() {
		return 'none';
	}
}
