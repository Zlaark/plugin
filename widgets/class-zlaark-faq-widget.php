<?php
/**
 * Zlaark Questions - the "faq" section of the Homepage widget, on its own.
 *
 * A plain accordion, which is the right answer for question and answer content.
 *
 * Subclasses the Homepage widget so the controls and the markup have exactly
 * one implementation. See class-zlaark-section-widget-base.php.
 *
 * @package Zlaark_Deals_Pro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Zlaark_Faq_Widget extends Zlaark_Section_Widget_Base {

	public function get_name() {
		return 'zlaark_sec_faq';
	}

	public function get_title() {
		return __( 'Zlaark Questions', 'zlaark-deals-pro' );
	}

	public function get_icon() {
		return 'eicon-help-o';
	}

	public function get_keywords() {
		return array( 'faq','questions','accordion','answers', 'zlaark', 'section' );
	}

	protected function section_key() {
		return 'faq';
	}

	protected function section_order() {
		return 'none';
	}
}
