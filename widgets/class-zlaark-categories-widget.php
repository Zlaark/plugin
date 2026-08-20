<?php
/**
 * Zlaark Category Grid - the "cats" section of the Homepage widget, on its own.
 *
 * Category tiles carrying the best current saving and each category's share of the catalogue.
 *
 * Subclasses the Homepage widget so the controls and the markup have exactly
 * one implementation. See class-zlaark-section-widget-base.php.
 *
 * @package Zlaark_Deals_Pro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Zlaark_Categories_Widget extends Zlaark_Section_Widget_Base {

	public function get_name() {
		return 'zlaark_sec_categories';
	}

	public function get_title() {
		return __( 'Zlaark Category Grid', 'zlaark-deals-pro' );
	}

	public function get_icon() {
		return 'eicon-gallery-grid';
	}

	public function get_keywords() {
		return array( 'categories','browse','taxonomy','tiles', 'zlaark', 'section' );
	}

	protected function section_key() {
		return 'cats';
	}

	protected function render_key() {
		return 'categories';
	}

	protected function section_order() {
		return 'saving';
	}
}
