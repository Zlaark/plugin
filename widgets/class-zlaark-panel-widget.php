<?php
/**
 * Zlaark Deal Panel - the offer panel for a single deal page.
 *
 * The live A2 Hosting page shows a logo, the bare figure "$5.00" with no period
 * or context, and then a wall of marketing copy with no call to action anywhere
 * above the fold. This widget puts the whole offer in one sticky panel: score,
 * price, savings, coupon, renewal, first-term total, refund window, countdown
 * and verification, with the score breakdown underneath.
 *
 * Drop it in an Elementor single template for the Deal post type, or point it
 * at a specific deal to use it anywhere.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;

class Zlaark_Panel_Widget extends Zlaark_Query_Widget_Base {

	public function get_name() {
		return 'zlaark_panel';
	}

	public function get_title() {
		return __( 'Zlaark Deal Panel', 'zlaark-deals-pro' );
	}

	public function get_icon() {
		return 'eicon-price-list';
	}

	public function get_keywords() {
		return array( 'deal', 'offer', 'panel', 'single', 'sticky' );
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_source',
			array(
				'label' => __( 'Deal', 'zlaark-deals-pro' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'source',
			array(
				'label'   => __( 'Deal Source', 'zlaark-deals-pro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'current',
				'options' => array(
					'current' => __( 'Current deal (single template)', 'zlaark-deals-pro' ),
					'pick'    => __( 'A specific deal', 'zlaark-deals-pro' ),
				),
			)
		);

		$this->add_control(
			'deal_id',
			array(
				'label'       => __( 'Choose Deal', 'zlaark-deals-pro' ),
				'type'        => Controls_Manager::SELECT2,
				'label_block' => true,
				'options'     => self::deal_options(),
				'condition'   => array( 'source' => 'pick' ),
			)
		);

		$this->add_control(
			'show_verdict',
			array(
				'label'        => __( 'Verdict Box', 'zlaark-deals-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'show_scores',
			array(
				'label'        => __( 'Score Breakdown', 'zlaark-deals-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'show_fit',
			array(
				'label'        => __( 'Best For / Not For', 'zlaark-deals-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'description'  => __( 'Naming who should not buy is the strongest trust signal on the page.', 'zlaark-deals-pro' ),
			)
		);

		$this->add_control(
			'sticky',
			array(
				'label'        => __( 'Stick On Scroll', 'zlaark-deals-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'sticky_offset',
			array(
				'label'      => __( 'Sticky Offset', 'zlaark-deals-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 200 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 96 ),
				'condition'  => array( 'sticky' => 'yes' ),
				'selectors'  => array(
					'{{WRAPPER}} .zd-panel--sticky' => 'top: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'nofollow',
			array(
				'label'        => __( 'Mark Links Sponsored', 'zlaark-deals-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_layout',
			array(
				'label' => __( 'Layout', 'zlaark-deals-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->max_width_control( '{{WRAPPER}} .zd-panel', 420 );
		$this->end_controls_section();

		$this->animation_controls( false );
	}

	/** Deal titles for the picker, capped so the control stays usable. */

	protected function render() {
		$s = $this->get_settings_for_display();

		$post_id = ( 'pick' === $s['source'] && ! empty( $s['deal_id'] ) )
			? (int) $s['deal_id']
			: get_the_ID();

		$deal = Zlaark_Deals_Meta::get_deal_data( $post_id );

		if ( empty( $deal ) || ZLAARK_DEALS_CPT !== get_post_type( $post_id ) ) {
			if ( $this->is_editor() ) {
				$this->render_empty_notice();
			}
			return;
		}

		echo Zlaark_Deals_Panel::html( // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside.
			$deal,
			array(
				'sticky'   => 'yes' === $s['sticky'],
				'verdict'  => 'yes' === $s['show_verdict'],
				'scores'   => 'yes' === $s['show_scores'],
				'fit'      => 'yes' === $s['show_fit'],
				'nofollow' => 'yes' === $s['nofollow'],
			)
		);
	}

}
