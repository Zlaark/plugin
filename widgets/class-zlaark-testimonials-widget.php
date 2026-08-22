<?php
/**
 * Zlaark Testimonials - the "testimonials" section of the Homepage widget, on its own.
 *
 * Attributed quotes on a scrolling rail, each carrying where it came
 * from and when.
 *
 * Subclasses the Homepage widget so the controls and the markup have exactly
 * one implementation. See class-zlaark-section-widget-base.php.
 *
 * @package Zlaark_Deals_Pro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Zlaark_Testimonials_Widget extends Zlaark_Section_Widget_Base {

	public function get_name() {
		return 'zlaark_sec_testimonials';
	}

	public function get_title() {
		return __( 'Zlaark Testimonials', 'zlaark-deals-pro' );
	}

	public function get_icon() {
		return 'eicon-testimonial';
	}

	public function get_keywords() {
		return array( 'testimonials', 'quotes', 'reviews', 'trust', 'social proof', 'zlaark', 'section' );
	}

	protected function section_key() {
		return 'testimonials';
	}

	protected function section_order() {
		return 'none';
	}
}
