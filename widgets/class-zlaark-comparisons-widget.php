<?php
/**
 * Zlaark Comparisons Strip - the "comparisons" section of the Homepage widget, on its own.
 *
 * A rail of comparison articles. Titles written "X vs Y" split across
 * a VS chip on the generated cover.
 *
 * Subclasses the Homepage widget so the controls and the markup have exactly
 * one implementation. See class-zlaark-section-widget-base.php.
 *
 * @package Zlaark_Deals_Pro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Zlaark_Comparisons_Widget extends Zlaark_Section_Widget_Base {

	public function get_name() {
		return 'zlaark_sec_comparisons';
	}

	public function get_title() {
		return __( 'Zlaark Comparisons Strip', 'zlaark-deals-pro' );
	}

	public function get_icon() {
		return 'eicon-post-list';
	}

	public function get_keywords() {
		return array( 'comparisons', 'versus', 'vs', 'articles', 'rail', 'zlaark', 'section' );
	}

	protected function section_key() {
		return 'comparisons';
	}

	protected function section_order() {
		return 'none';
	}
}
