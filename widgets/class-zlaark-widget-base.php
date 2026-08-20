<?php
/**
 * Shared base for every Zlaark widget: asset dependencies, the animation
 * controls that all widgets expose, and — for the CPT-driven widgets — the
 * category/order query controls and the WP_Query builder behind them.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

abstract class Zlaark_Widget_Base extends Widget_Base {

	public function get_categories() {
		return array( 'zlaark-deals' );
	}

	public function get_style_depends() {
		return array( 'zlaark-deals' );
	}

	public function get_script_depends() {
		return array( 'zlaark-deals' );
	}

	/* --------------------------------------------------------------- helpers */

	/** True while the widget is being rendered inside the Elementor editor. */
	protected function is_editor() {
		return class_exists( '\Elementor\Plugin' )
			&& \Elementor\Plugin::$instance->editor->is_edit_mode();
	}

	/**
	 * A "Content Max Width" control, centred with auto margins, so every
	 * Zlaark section can be lined up to the same width (1240px by default)
	 * regardless of how wide the Elementor column around it is.
	 *
	 * @param string $selector CSS selector for the widget's own inner wrapper.
	 */
	protected function max_width_control( $selector, $default = 1240 ) {
		$this->add_responsive_control(
			'max_width',
			array(
				'label'      => __( 'Content Max Width', 'zlaark-deals-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array( 'min' => 480, 'max' => 1920 ),
					'%'  => array( 'min' => 50, 'max' => 100 ),
				),
				'default'    => array( 'unit' => 'px', 'size' => $default ),
				'selectors'  => array(
					$selector => 'max-width: {{SIZE}}{{UNIT}}; margin-left: auto; margin-right: auto;',
				),
			)
		);
	}

	/** Motion controls shared by all widgets. */
	protected function animation_controls( $with_tilt = true ) {
		$this->start_controls_section(
			'section_motion',
			array(
				'label' => __( 'Motion', 'zlaark-deals-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'reveal_effect',
			array(
				'label'   => __( 'Scroll Reveal', 'zlaark-deals-pro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'rise',
				'options' => array(
					'rise'  => __( 'Rise up', 'zlaark-deals-pro' ),
					'fade'  => __( 'Fade in', 'zlaark-deals-pro' ),
					'zoom'  => __( 'Zoom in', 'zlaark-deals-pro' ),
					'slide' => __( 'Slide from left', 'zlaark-deals-pro' ),
					'flip'  => __( 'Flip in 3D', 'zlaark-deals-pro' ),
					'none'  => __( 'No reveal', 'zlaark-deals-pro' ),
				),
			)
		);

		$this->add_control(
			'reveal_stagger',
			array(
				'label'      => __( 'Stagger Delay', 'zlaark-deals-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'ms' ),
				'range'      => array( 'ms' => array( 'min' => 0, 'max' => 400, 'step' => 10 ) ),
				'default'    => array( 'unit' => 'ms', 'size' => 60 ),
				'condition'  => array( 'reveal_effect!' => 'none' ),
				'description' => __( 'Delay added between each item as the group enters the viewport.', 'zlaark-deals-pro' ),
			)
		);

		if ( $with_tilt ) {
			$this->add_control(
				'enable_tilt',
				array(
					'label'        => __( '3D Cursor Tilt', 'zlaark-deals-pro' ),
					'type'         => Controls_Manager::SWITCHER,
					'default'      => '',
					'return_value' => 'yes',
					'separator'    => 'before',
					'description'  => __( 'Cards lean towards the pointer and lift on hover.', 'zlaark-deals-pro' ),
				)
			);

			$this->add_control(
				'enable_shine',
				array(
					'label'        => __( 'Hover Shine Sweep', 'zlaark-deals-pro' ),
					'type'         => Controls_Manager::SWITCHER,
					'default'      => '',
					'return_value' => 'yes',
				)
			);

			$this->add_control(
				'enable_glow',
				array(
					'label'        => __( 'Animated Border Glow', 'zlaark-deals-pro' ),
					'type'         => Controls_Manager::SWITCHER,
					'default'      => '',
					'return_value' => 'yes',
					'description'  => __( 'A conic gradient rotates around the card edge on hover.', 'zlaark-deals-pro' ),
				)
			);
		}

		$this->end_controls_section();
	}

	/**
	 * Turns the shared motion switches into the class list + data attributes
	 * the frontend script looks for.
	 */
	protected function motion_classes( $s ) {
		$classes = array();
		if ( isset( $s['enable_tilt'] ) && 'yes' === $s['enable_tilt'] ) {
			$classes[] = 'zd-tilt';
		}
		if ( isset( $s['enable_shine'] ) && 'yes' === $s['enable_shine'] ) {
			$classes[] = 'zd-shine';
		}
		if ( isset( $s['enable_glow'] ) && 'yes' === $s['enable_glow'] ) {
			$classes[] = 'zd-glow';
		}
		return $classes;
	}
}

/**
 * Base for the three widgets that read deals out of the CPT.
 */
abstract class Zlaark_Query_Widget_Base extends Zlaark_Widget_Base {

	/** Category + ordering controls, identical across the query widgets. */
	protected function query_controls( $default_limit = 6 ) {
		$this->start_controls_section(
			'section_query',
			array(
				'label' => __( 'Deals Source', 'zlaark-deals-pro' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$categories = Zlaark_Deals_Post_Type::get_category_options();

		if ( empty( $categories ) ) {
			$this->add_control(
				'no_categories_notice',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					'raw'             => __( 'No deal categories found yet. Add some under <strong>Zlaark Deals &rarr; Categories</strong>.', 'zlaark-deals-pro' ),
					'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
				)
			);
		}

		$this->add_control(
			'category',
			array(
				'label'       => __( 'Category', 'zlaark-deals-pro' ),
				'type'        => Controls_Manager::SELECT2,
				'multiple'    => true,
				'label_block' => true,
				'options'     => $categories,
				'default'     => array(),
				'description' => __( 'Leave empty to pull from every category.', 'zlaark-deals-pro' ),
			)
		);

		$this->add_control(
			'limit',
			array(
				'label'   => __( 'Number of Deals', 'zlaark-deals-pro' ),
				'type'    => Controls_Manager::NUMBER,
				'min'     => 1,
				'max'     => 60,
				'default' => $default_limit,
			)
		);

		$this->add_control(
			'orderby',
			array(
				'label'   => __( 'Order By', 'zlaark-deals-pro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'menu_order',
				'options' => array(
					'menu_order' => __( 'Manual order (page attributes)', 'zlaark-deals-pro' ),
					'rating'     => __( 'Rating', 'zlaark-deals-pro' ),
					'date'       => __( 'Date published', 'zlaark-deals-pro' ),
					'title'      => __( 'Title', 'zlaark-deals-pro' ),
					'rand'       => __( 'Random', 'zlaark-deals-pro' ),
				),
			)
		);

		$this->add_control(
			'order',
			array(
				'label'     => __( 'Order', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'ASC',
				'options'   => array(
					'ASC'  => __( 'Ascending', 'zlaark-deals-pro' ),
					'DESC' => __( 'Descending', 'zlaark-deals-pro' ),
				),
				'condition' => array( 'orderby!' => 'rand' ),
			)
		);

		$this->end_controls_section();
	}

	/** The category IDs actually chosen in the panel. */
	protected function selected_categories( $s ) {
		$categories = isset( $s['category'] ) ? (array) $s['category'] : array();
		return array_values( array_filter( array_map( 'intval', $categories ) ) );
	}

	protected function build_query_args( $s ) {
		$args = array(
			'post_type'           => ZLAARK_DEALS_CPT,
			'post_status'         => 'publish',
			'posts_per_page'      => ! empty( $s['limit'] ) ? (int) $s['limit'] : 6,
			'order'               => ! empty( $s['order'] ) ? $s['order'] : 'ASC',
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
		);

		$orderby = ! empty( $s['orderby'] ) ? $s['orderby'] : 'menu_order';

		if ( 'rating' === $orderby ) {
			// Deals without a rating would drop out of a plain meta_key query,
			// so sort on the meta value but keep unrated deals in the results.
			$args['meta_key'] = '_zlaark_rating'; // phpcs:ignore WordPress.DB.SlowDBQuery
			$args['orderby']  = 'meta_value_num';
			$args['meta_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery
				'relation' => 'OR',
				array(
					'key'     => '_zlaark_rating',
					'compare' => 'EXISTS',
				),
				array(
					'key'     => '_zlaark_rating',
					'compare' => 'NOT EXISTS',
				),
			);
		} elseif ( 'menu_order' === $orderby ) {
			// menu_order alone leaves ties unordered; fall back to title.
			$args['orderby'] = 'menu_order title';
		} else {
			$args['orderby'] = $orderby;
		}

		$categories = $this->selected_categories( $s );
		if ( ! empty( $categories ) ) {
			$args['tax_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery
				array(
					'taxonomy' => ZLAARK_DEALS_TAX,
					'field'    => 'term_id',
					'terms'    => $categories,
				),
			);
		}

		/*
		 * Expired deals drop out on their own. A deals site showing a lapsed
		 * coupon loses trust instantly, and it is the most common failure in
		 * this category — so it must not depend on anyone remembering.
		 * Deals with no expiry date are evergreen and always included.
		 */
		$expiry = Zlaark_Deals_Computed::not_expired_meta_query();

		if ( empty( $args['meta_query'] ) ) {
			$args['meta_query'] = $expiry; // phpcs:ignore WordPress.DB.SlowDBQuery
		} else {
			$args['meta_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery
				'relation' => 'AND',
				$args['meta_query'],
				$expiry,
			);
		}

		return $args;
	}

	/** Explicit heading wins; otherwise use the single selected category's name. */
	protected function resolve_heading( $s, $key = 'heading' ) {
		if ( ! empty( $s[ $key ] ) ) {
			return $s[ $key ];
		}

		$categories = $this->selected_categories( $s );
		if ( 1 === count( $categories ) ) {
			$term = get_term( $categories[0], ZLAARK_DEALS_TAX );
			if ( $term && ! is_wp_error( $term ) ) {
				return $term->name;
			}
		}

		return '';
	}

	/** Shown in the editor only, so an empty selection is never a silent blank. */
	protected function render_empty_notice() {
		if ( ! $this->is_editor() ) {
			return;
		}
		echo '<div class="zd-empty">'
			. esc_html__( 'No deals match this selection. Add deals under Zlaark Deals in the WordPress sidebar, then pick a category here.', 'zlaark-deals-pro' )
			. '</div>';
	}

	/**
	 * Renders the CTA anchor shared by the card widgets.
	 *
	 * @param array  $deal    Deal data from Zlaark_Deals_Meta::get_deal_data().
	 * @param array  $s       Widget settings.
	 * @param string $classes Extra classes for the anchor.
	 */
	protected function render_cta( $deal, $s, $classes = 'zd-btn zd-btn--solid' ) {
		if ( '' === $deal['button_text'] ) {
			return;
		}

		$url    = '' !== $deal['button_url'] ? $deal['button_url'] : $deal['permalink'];
		$follow = isset( $s['nofollow'] ) && 'yes' === $s['nofollow'];
		$rel    = $follow ? 'nofollow sponsored noopener' : 'noopener';
		?>
		<a class="<?php echo esc_attr( $classes ); ?>"
			href="<?php echo esc_url( $url ); ?>"
			rel="<?php echo esc_attr( $rel ); ?>"
			<?php echo $deal['button_new'] ? 'target="_blank"' : ''; ?>>
			<span class="zd-btn__label"><?php echo esc_html( $deal['button_text'] ); ?></span>
			<span class="zd-btn__arrow" aria-hidden="true">
				<svg viewBox="0 0 16 16" width="14" height="14" fill="none">
					<path d="M2 8h11M9 4l4 4-4 4" stroke="currentColor" stroke-width="2"
						stroke-linecap="round" stroke-linejoin="round" />
				</svg>
			</span>
		</a>
		<?php
	}

	/**
	 * The animated rating ring — an SVG circle whose dash offset is driven by
	 * a CSS custom property once the element scrolls into view.
	 */
	protected function render_rating_ring( $rating, $size = 62 ) {
		if ( null === $rating ) {
			return;
		}
		$pct    = max( 0, min( 100, ( $rating / 10 ) * 100 ) );
		$radius = 26;
		$circ   = 2 * M_PI * $radius;
		?>
		<div class="zd-ring" data-zd-ring="<?php echo esc_attr( $pct ); ?>"
			style="--zd-ring-size:<?php echo esc_attr( $size ); ?>px;--zd-ring-circ:<?php echo esc_attr( round( $circ, 2 ) ); ?>">
			<svg viewBox="0 0 60 60" aria-hidden="true">
				<circle class="zd-ring__track" cx="30" cy="30" r="<?php echo esc_attr( $radius ); ?>" />
				<circle class="zd-ring__bar" cx="30" cy="30" r="<?php echo esc_attr( $radius ); ?>" />
			</svg>
			<span class="zd-ring__value" data-zd-count="<?php echo esc_attr( $rating ); ?>" data-zd-decimals="1">0.0</span>
		</div>
		<?php
	}
}
