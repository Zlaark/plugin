<?php
/**
 * Zlaark Expiring Soon - the "exp" section of the Homepage widget, on its own.
 *
 * A ruled countdown board for offers closing inside the week.
 *
 * Subclasses the Homepage widget so the controls and the markup have exactly
 * one implementation. See class-zlaark-section-widget-base.php.
 *
 * @package Zlaark_Deals_Pro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Zlaark_Expiring_Widget extends Zlaark_Section_Widget_Base {

	public function get_name() {
		return 'zlaark_sec_expiring';
	}

	public function get_title() {
		return __( 'Zlaark Expiring Soon', 'zlaark-deals-pro' );
	}

	public function get_icon() {
		return 'eicon-clock-o';
	}

	public function get_keywords() {
		return array( 'expiring','urgency','countdown','ending', 'zlaark', 'section' );
	}

	protected function section_key() {
		return 'exp';
	}

	protected function render_key() {
		return 'expiring';
	}

	protected function section_order() {
		return 'saving';
	}
}
