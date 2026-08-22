<?php
/**
 * Zlaark Articles Grid - the "grid" section of the Homepage widget, on its own.
 *
 * The same articles laid out four-up on a tinted ground, with a header
 * link, instead of scrolled in a rail.
 *
 * Subclasses the Homepage widget so the controls and the markup have exactly
 * one implementation. See class-zlaark-section-widget-base.php.
 *
 * @package Zlaark_Deals_Pro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Zlaark_Article_Grid_Widget extends Zlaark_Section_Widget_Base {

	public function get_name() {
		return 'zlaark_sec_article_grid';
	}

	public function get_title() {
		return __( 'Zlaark Articles Grid', 'zlaark-deals-pro' );
	}

	public function get_icon() {
		return 'eicon-gallery-grid';
	}

	public function get_keywords() {
		return array( 'articles', 'grid', 'posts', 'latest', 'zlaark', 'section' );
	}

	protected function section_key() {
		return 'grid';
	}

	protected function render_key() {
		return 'article_grid';
	}

	protected function section_order() {
		return 'none';
	}
}
