<?php
/**
 * Base for the standalone section widgets.
 *
 * Every section on the Homepage widget is also available on its own, so a page
 * can be assembled a block at a time instead of dropping the whole homepage.
 * These subclass the Homepage widget rather than copying it: one renderer, one
 * set of controls, no second implementation to drift out of sync. A subclass
 * only declares which section it is.
 *
 * @package Zlaark_Deals_Pro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;

abstract class Zlaark_Section_Widget_Base extends Zlaark_Homepage_Widget {

	/** Key of the controls_{key}() method on the Homepage widget. */
	abstract protected function section_key();

	/**
	 * Key of the section_{key}() renderer. Two panels name their controls and
	 * their renderer differently (cats/categories, exp/expiring), so this is
	 * overridable rather than assumed to match.
	 */
	protected function render_key() {
		return $this->section_key();
	}

	/**
	 * Which sorted list the section reads.
	 *
	 * 'saving' - biggest discount first, 'score' - highest rated first,
	 * 'none'   - the section takes no deals at all.
	 */
	protected function section_order() {
		return 'none';
	}

	/** Sections that query deals need the source controls; static ones don't. */
	protected function needs_query() {
		return 'none' !== $this->section_order();
	}

	public function get_categories() {
		return array( 'zlaark-deals' );
	}

	protected function register_controls() {
		$method = 'controls_' . $this->section_key();
		$this->$method();

		if ( $this->needs_query() ) {
			$this->query_controls( 12 );
		}

		$this->style_controls();
		$this->animation_controls( false );
	}

	/**
	 * The Homepage widget's Style tab, minus the per-section toggles. Kept in
	 * sync by pulling from the same place: controls_shared() also builds the
	 * source controls, which a static section must not get, so the colour and
	 * spacing controls live here instead.
	 */
	protected function style_controls() {
		$this->start_controls_section(
			'section_layout',
			array(
				'label' => __( 'Layout & colour', 'zlaark-deals-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->max_width_control( '{{WRAPPER}} .zd-home__inner' );

		foreach ( self::style_tokens() as $key => $row ) {
			$this->add_control(
				$key,
				array(
					'label'     => $row['label'],
					'type'      => Controls_Manager::COLOR,
					'selectors' => array( '{{WRAPPER}} .zd-home' => $row['token'] . ': {{VALUE}};' ),
				)
			);
		}

		$this->add_responsive_control(
			'sec_pad',
			array(
				'label'      => __( 'Section Spacing', 'zlaark-deals-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 160 ) ),
				'selectors'  => array( '{{WRAPPER}} .zd-home__sec' => 'padding-top: {{SIZE}}px; padding-bottom: {{SIZE}}px;' ),
			)
		);

		$this->add_control(
			'card_radius',
			array(
				'label'      => __( 'Card Corner Radius', 'zlaark-deals-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 32 ) ),
				'selectors'  => array( '{{WRAPPER}} .zd-home' => '--zd-r-md: {{SIZE}}px;' ),
			)
		);

		$this->end_controls_section();
	}

	/** @return array Colour controls shared with the Homepage widget. */
	protected static function style_tokens() {
		return array(
			'c_accent'      => array( 'label' => __( 'Accent', 'zlaark-deals-pro' ),        'token' => '--zd-accent' ),
			'c_accent_2'    => array( 'label' => __( 'Accent Hover', 'zlaark-deals-pro' ),  'token' => '--zd-accent-2' ),
			'c_accent_tint' => array( 'label' => __( 'Accent Tint', 'zlaark-deals-pro' ),   'token' => '--zd-accent-tint' ),
			'c_ember'       => array( 'label' => __( 'Urgency', 'zlaark-deals-pro' ),       'token' => '--zd-ember' ),
			'c_ink'         => array( 'label' => __( 'Dark Band', 'zlaark-deals-pro' ),     'token' => '--zd-ink' ),
			'c_heading'     => array( 'label' => __( 'Headings', 'zlaark-deals-pro' ),      'token' => '--zd-heading' ),
			'c_body'        => array( 'label' => __( 'Body Text', 'zlaark-deals-pro' ),     'token' => '--zd-body' ),
			'c_surface'     => array( 'label' => __( 'Tinted Sections', 'zlaark-deals-pro' ), 'token' => '--zd-surface' ),
			'c_hairline'    => array( 'label' => __( 'Borders', 'zlaark-deals-pro' ),       'token' => '--zd-hairline' ),
		);
	}

	/**
	 * A section on its own needs its own deals and nobody else's.
	 *
	 * Inheriting the Homepage widget's pool meant every section widget queried
	 * sixty deals and hydrated all sixty - thirty-odd meta reads apiece - to
	 * draw three cards. A page built from eight of these did that eight times
	 * over, which is what made the Elementor preview crawl. The headroom on
	 * top of the chosen count covers the near-duplicate brand names that
	 * fetch() drops, so a section still fills up after the dedupe pass.
	 *
	 * @param array $s Widget settings.
	 * @return int
	 */
	protected function fetch_limit( $s ) {
		$limit = isset( $s['limit'] ) ? (int) $s['limit'] : 12;

		return max( 4, min( 60, $limit + 6 ) );
	}

	protected function render() {
		$s     = $this->get_settings_for_display();
		$deals = array();

		if ( $this->needs_query() ) {
			$deals = $this->fetch( $s );

			if ( empty( $deals ) ) {
				$this->render_empty_notice();
				return;
			}

			$deals = $this->sort_deals( $deals, $this->section_order() );
		}

		$method = 'section_' . $this->render_key();

		/*
		 * `.zd-home` carries the design tokens, so a section rendered on its
		 * own still needs that root or it inherits the theme's colours.
		 */
		echo '<div class="zd-home zd-home--single" data-zd-reveal-root="true" data-zd-stagger="60">';
		if ( $this->needs_query() ) {
			$this->$method( $s, $deals );
		} else {
			$this->$method( $s );
		}
		echo '</div>';
	}

	/** Same orderings the Homepage widget argues from. */
	protected function sort_deals( $deals, $order ) {
		if ( 'saving' === $order ) {
			usort(
				$deals,
				function ( $a, $b ) {
					$x = ( null === $a['discount_pct'] ) ? -1 : (int) $a['discount_pct'];
					$y = ( null === $b['discount_pct'] ) ? -1 : (int) $b['discount_pct'];
					return $y - $x;
				}
			);
		} elseif ( 'score' === $order ) {
			usort(
				$deals,
				function ( $a, $b ) {
					$x = ( null === $a['overall_score'] ) ? -1 : (float) $a['overall_score'];
					$y = ( null === $b['overall_score'] ) ? -1 : (float) $b['overall_score'];
					return ( $y > $x ) ? 1 : ( ( $y < $x ) ? -1 : 0 );
				}
			);
		}
		return $deals;
	}
}
