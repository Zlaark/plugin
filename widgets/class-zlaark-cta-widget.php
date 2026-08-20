<?php
/**
 * Zlaark Closing CTA - the "cta" section of the Homepage widget, on its own.
 *
 * The closing call to action that ends the page.
 *
 * Subclasses the Homepage widget so the controls and the markup have exactly
 * one implementation. See class-zlaark-section-widget-base.php.
 *
 * @package Zlaark_Deals_Pro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Zlaark_Cta_Widget extends Zlaark_Section_Widget_Base {

	public function get_name() {
		return 'zlaark_sec_cta';
	}

	public function get_title() {
		return __( 'Zlaark Closing CTA', 'zlaark-deals-pro' );
	}

	public function get_icon() {
		return 'eicon-call-to-action';
	}

	public function get_keywords() {
		return array( 'cta','closing','call to action','banner', 'zlaark', 'section' );
	}

	protected function section_key() {
		return 'cta';
	}

	protected function section_order() {
		return 'none';
	}
}
