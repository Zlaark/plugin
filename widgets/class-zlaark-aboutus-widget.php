<?php
/**
 * Zlaark About Us - the "about" section of the Homepage widget, on its own.
 *
 * The people who do the testing. For a review site this is the credibility argument.
 *
 * Subclasses the Homepage widget so the controls and the markup have exactly
 * one implementation. See class-zlaark-section-widget-base.php.
 *
 * @package Zlaark_Deals_Pro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Zlaark_About_Us_Widget extends Zlaark_Section_Widget_Base {

	public function get_name() {
		return 'zlaark_sec_aboutus';
	}

	public function get_title() {
		return __( 'Zlaark About Us', 'zlaark-deals-pro' );
	}

	public function get_icon() {
		return 'eicon-person';
	}

	public function get_keywords() {
		return array( 'about','team','people','reviewers', 'zlaark', 'section' );
	}

	protected function section_key() {
		return 'about';
	}

	protected function section_order() {
		return 'none';
	}
}
