<?php
/**
 * Registers the "Deals" post type and its category taxonomy, and adds the
 * admin list-table columns that make the deals manageable from the sidebar.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Zlaark_Deals_Post_Type {

	/** Categories created on activation so the plugin is usable out of the box. */
	const DEFAULT_TERMS = array(
		'discount-deals' => 'Discount Deals',
		'web-hosting'    => 'Web Hosting',
	);

	public static function init() {
		add_action( 'init', array( __CLASS__, 'register_post_type' ) );
		add_action( 'init', array( __CLASS__, 'register_taxonomy' ) );
		add_action( 'init', array( __CLASS__, 'maybe_seed_default_terms' ), 20 );

		add_filter( 'manage_' . ZLAARK_DEALS_CPT . '_posts_columns', array( __CLASS__, 'columns' ) );
		add_action( 'manage_' . ZLAARK_DEALS_CPT . '_posts_custom_column', array( __CLASS__, 'column_content' ), 10, 2 );
		add_action( 'admin_notices', array( __CLASS__, 'seeded_notice' ) );
		add_filter( 'enter_title_here', array( __CLASS__, 'title_placeholder' ), 10, 2 );
	}

	public static function register_post_type() {
		$labels = array(
			'name'               => __( 'Deals', 'zlaark-deals-pro' ),
			'singular_name'      => __( 'Deal', 'zlaark-deals-pro' ),
			'menu_name'          => __( 'Zlaark Deals', 'zlaark-deals-pro' ),
			'add_new'            => __( 'Add Deal', 'zlaark-deals-pro' ),
			'add_new_item'       => __( 'Add New Deal', 'zlaark-deals-pro' ),
			'edit_item'          => __( 'Edit Deal', 'zlaark-deals-pro' ),
			'new_item'           => __( 'New Deal', 'zlaark-deals-pro' ),
			'view_item'          => __( 'View Deal', 'zlaark-deals-pro' ),
			'search_items'       => __( 'Search Deals', 'zlaark-deals-pro' ),
			'not_found'          => __( 'No deals found.', 'zlaark-deals-pro' ),
			'not_found_in_trash' => __( 'No deals found in Trash.', 'zlaark-deals-pro' ),
			'all_items'          => __( 'All Deals', 'zlaark-deals-pro' ),
		);

		register_post_type(
			ZLAARK_DEALS_CPT,
			array(
				'labels'          => $labels,
				'public'          => true,
				'has_archive'     => false,
				'show_ui'         => true,
				'show_in_menu'    => true,
				'show_in_rest'    => true,
				'menu_position'   => 26,
				'menu_icon'       => 'dashicons-tag',
				'supports'        => array( 'title', 'thumbnail', 'page-attributes' ),
				'rewrite'         => array( 'slug' => 'deal' ),
				'capability_type' => 'post',
			)
		);
	}

	public static function register_taxonomy() {
		$labels = array(
			'name'          => __( 'Deal Categories', 'zlaark-deals-pro' ),
			'singular_name' => __( 'Deal Category', 'zlaark-deals-pro' ),
			'menu_name'     => __( 'Categories', 'zlaark-deals-pro' ),
			'all_items'     => __( 'All Categories', 'zlaark-deals-pro' ),
			'edit_item'     => __( 'Edit Category', 'zlaark-deals-pro' ),
			'update_item'   => __( 'Update Category', 'zlaark-deals-pro' ),
			'add_new_item'  => __( 'Add New Category', 'zlaark-deals-pro' ),
			'new_item_name' => __( 'New Category Name', 'zlaark-deals-pro' ),
			'search_items'  => __( 'Search Categories', 'zlaark-deals-pro' ),
		);

		register_taxonomy(
			ZLAARK_DEALS_TAX,
			array( ZLAARK_DEALS_CPT ),
			array(
				'labels'            => $labels,
				'hierarchical'      => true,
				'public'            => true,
				'show_ui'           => true,
				'show_admin_column' => true,
				'show_in_rest'      => true,
				'rewrite'           => array( 'slug' => 'deal-category' ),
			)
		);
	}

	/**
	 * Creates the default categories once. Runs on activation and, as a safety
	 * net, on init for installs where the activation hook never fired.
	 */
	public static function maybe_seed_default_terms() {
		if ( get_option( 'zlaark_deals_defaults_seeded' ) ) {
			return;
		}
		self::seed_default_terms();
	}

	public static function seed_default_terms() {
		foreach ( self::DEFAULT_TERMS as $slug => $name ) {
			if ( ! term_exists( $slug, ZLAARK_DEALS_TAX ) ) {
				wp_insert_term( $name, ZLAARK_DEALS_TAX, array( 'slug' => $slug ) );
			}
		}
		update_option( 'zlaark_deals_defaults_seeded', 1 );
	}

	/**
	 * Placeholder values carried in from a seed import.
	 *
	 * The import writes this; clearing a field in the editor does not remove it
	 * from the list, so the check is done against the live value - a field the
	 * editor has since filled in stops counting as unverified on its own.
	 *
	 * @param int $post_id
	 * @return array Field names still holding their imported placeholder.
	 */
	public static function unverified_fields( $post_id ) {
		$seeded = get_post_meta( $post_id, '_zlaark_seeded', true );

		if ( empty( $seeded ) || ! is_array( $seeded ) ) {
			return array();
		}

		return array_values( array_filter( $seeded ) );
	}

	/** Notice on the deal editor, so the warning is where the fixing happens. */
	public static function seeded_notice() {
		$screen = get_current_screen();

		if ( ! $screen || ZLAARK_DEALS_CPT !== $screen->post_type || 'post' !== $screen->base ) {
			return;
		}

		$fields = self::unverified_fields( get_the_ID() );

		if ( empty( $fields ) ) {
			return;
		}

		printf(
			'<div class="notice notice-warning"><p><strong>%s</strong> %s</p><p><code>%s</code></p></div>',
			esc_html__( 'Imported placeholders on this deal.', 'zlaark-deals-pro' ),
			esc_html__( 'These fields were filled by a seed import and stand in for facts only you have. Check each against the vendor page before this deal goes live.', 'zlaark-deals-pro' ),
			esc_html( implode( ', ', $fields ) )
		);
	}

	public static function columns( $columns ) {
		$reordered = array();
		foreach ( $columns as $key => $label ) {
			if ( 'title' === $key ) {
				$reordered['zlaark_image'] = __( 'Image', 'zlaark-deals-pro' );
			}
			$reordered[ $key ] = $label;
			if ( 'title' === $key ) {
				$reordered['zlaark_price']  = __( 'Pricing', 'zlaark-deals-pro' );
				$reordered['zlaark_rating'] = __( 'Rating', 'zlaark-deals-pro' );
				$reordered['zlaark_seeded'] = __( 'To verify', 'zlaark-deals-pro' );
			}
		}
		return $reordered;
	}

	public static function column_content( $column, $post_id ) {
		if ( 'zlaark_image' === $column ) {
			$image_id = (int) get_post_meta( $post_id, '_zlaark_image_id', true );
			if ( $image_id ) {
				echo wp_get_attachment_image( $image_id, array( 48, 48 ), false, array( 'class' => 'zlaark-col-thumb' ) );
			} else {
				echo '<span class="zlaark-col-thumb zlaark-col-thumb--empty">&mdash;</span>';
			}
		}

		if ( 'zlaark_price' === $column ) {
			$price     = get_post_meta( $post_id, '_zlaark_price', true );
			$old_price = get_post_meta( $post_id, '_zlaark_old_price', true );
			if ( '' === $price && '' === $old_price ) {
				echo '&mdash;';
				return;
			}
			echo '<strong>' . esc_html( $price ) . '</strong>';
			if ( '' !== $old_price ) {
				echo ' <s style="opacity:.6">' . esc_html( $old_price ) . '</s>';
			}
		}

		if ( 'zlaark_rating' === $column ) {
			$rating = get_post_meta( $post_id, '_zlaark_rating', true );
			echo '' !== $rating ? esc_html( $rating ) . '<span style="opacity:.5">/10</span>' : '&mdash;';
		}

		if ( 'zlaark_seeded' === $column ) {
			$fields = self::unverified_fields( $post_id );

			if ( empty( $fields ) ) {
				echo '<span style="color:#0b7a4f" title="' . esc_attr__( 'Nothing imported as a placeholder.', 'zlaark-deals-pro' ) . '">&check;</span>';
				return;
			}

			printf(
				'<span style="color:#c2410c;font-weight:600" title="%s">%s</span>',
				esc_attr( implode( ', ', $fields ) ),
				esc_html(
					sprintf(
						/* translators: %d: number of placeholder fields. */
						_n( '%d field', '%d fields', count( $fields ), 'zlaark-deals-pro' ),
						count( $fields )
					)
				)
			);
		}
	}

	public static function title_placeholder( $title, $post ) {
		if ( $post && ZLAARK_DEALS_CPT === $post->post_type ) {
			return __( 'Deal title (for example, Shopify Ecommerce Platform)', 'zlaark-deals-pro' );
		}
		return $title;
	}

	/**
	 * Returns [ term_id => name ] for every deal category - used to build the
	 * category dropdown inside the Elementor widget controls.
	 */
	public static function get_category_options() {
		/*
		 * The Elementor editor bootstrap instantiates every widget and runs
		 * register_controls() on each, so this is asked for a dozen times in a
		 * single request. Answer once.
		 */
		static $cache = null;
		if ( null !== $cache ) {
			return $cache;
		}

		$options = array();
		$terms   = get_terms(
			array(
				'taxonomy'               => ZLAARK_DEALS_TAX,
				'hide_empty'             => false,
				'update_term_meta_cache' => false,
			)
		);

		if ( is_wp_error( $terms ) ) {
			return $options;
		}

		foreach ( $terms as $term ) {
			$options[ $term->term_id ] = $term->name;
		}

		$cache = $options;
		return $options;
	}
}
