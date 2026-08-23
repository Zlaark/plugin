<?php
/**
 * Editorial article source for the review and comparison strips.
 *
 * Reviews and comparisons are written posts, not deals - they have a cover
 * image, a title, an excerpt and one link. Rather than bolt a second schema
 * onto the deal meta, they are read straight out of the normal WordPress
 * posts table, picked either by category or by hand.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Zlaark_Deals_Articles {

	/**
	 * Hard cap on the manual picker.
	 *
	 * Every one of these titles is serialised into the editor's JSON config,
	 * once per source block - and there are six of them across the Homepage
	 * widget and the reviews/comparisons/grid sections. At 200 that is 1,200
	 * embedded options before the editor has drawn anything. 100 keeps the
	 * picker useful and halves the payload; a site with a deeper archive can
	 * raise it through zlaark_deals_picker_limit.
	 */
	const PICKER_LIMIT = 100;

	/** @return int Rows the manual picker will offer. */
	public static function picker_limit() {
		return max( 1, (int) apply_filters( 'zlaark_deals_picker_limit', self::PICKER_LIMIT ) );
	}

	/**
	 * Post types offered as an article source. Filterable so a site that keeps
	 * its reviews in a custom post type can point the strips at it.
	 *
	 * @return array slug => label
	 */
	public static function post_type_options() {
		static $cache = null;
		if ( null !== $cache ) {
			return $cache;
		}

		$types = array( 'post' => __( 'Posts', 'zlaark-deals-pro' ) );

		$custom = get_post_types(
			array(
				'public'   => true,
				'_builtin' => false,
			),
			'objects'
		);

		foreach ( $custom as $slug => $obj ) {
			if ( ZLAARK_DEALS_CPT === $slug ) {
				continue; // Deals have their own, richer source control.
			}
			$types[ $slug ] = $obj->labels->name;
		}

		$cache = apply_filters( 'zlaark_deals_article_post_types', $types );

		return $cache;
	}

	/**
	 * Categories for the given post type, as "taxonomy:term_id" => "Name (12)".
	 *
	 * @param string $post_type
	 * @return array
	 */
	public static function category_options( $post_type = 'post' ) {
		/*
		 * Three strips on the Homepage widget ask for this, and each of the
		 * standalone section widgets asks again - all inside the one request
		 * that boots the Elementor editor. Memoize per post type.
		 */
		static $cache = array();
		if ( isset( $cache[ $post_type ] ) ) {
			return $cache[ $post_type ];
		}

		$out = array();

		foreach ( self::taxonomies_for( $post_type ) as $taxonomy ) {
			// Name and count only - no term meta is read from these.
			$terms = get_terms(
				array(
					'taxonomy'               => $taxonomy,
					'hide_empty'             => false,
					'update_term_meta_cache' => false,
				)
			);

			if ( is_wp_error( $terms ) ) {
				continue;
			}

			foreach ( $terms as $term ) {
				// The taxonomy is carried in the key so a site with several
				// hierarchical taxonomies cannot collide on term ids.
				$out[ $taxonomy . ':' . $term->term_id ] = sprintf( '%s (%d)', $term->name, $term->count );
			}
		}

		$cache[ $post_type ] = $out;

		return $out;
	}

	/** Hierarchical, public taxonomies attached to a post type. */
	public static function taxonomies_for( $post_type ) {
		$out = array();

		foreach ( get_object_taxonomies( $post_type, 'objects' ) as $slug => $tax ) {
			if ( $tax->public && $tax->hierarchical ) {
				$out[] = $slug;
			}
		}

		return $out;
	}

	/**
	 * Published posts for the manual picker, as ID => title.
	 *
	 * @param string $post_type
	 * @return array
	 */
	public static function post_options( $post_type = 'post' ) {
		// Up to 200 rows a call, and the editor bootstrap makes six of them.
		static $cache = array();
		if ( isset( $cache[ $post_type ] ) ) {
			return $cache[ $post_type ];
		}

		/*
		 * Only the ID and the title are read below, but get_posts() hydrates
		 * the meta and term caches by default - so this was pulling every
		 * postmeta row and every term for 200 posts, at control-registration
		 * time, on every editor bootstrap. Ask for the posts and nothing else.
		 */
		$posts = get_posts(
			array(
				'post_type'              => $post_type,
				'post_status'            => 'publish',
				'numberposts'            => self::picker_limit(),
				'orderby'                => 'date',
				'order'                  => 'DESC',
				'suppress_filters'       => false,
				'no_found_rows'          => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
			)
		);

		$out = array();
		foreach ( $posts as $post ) {
			$out[ $post->ID ] = wp_strip_all_tags( get_the_title( $post ) );
		}

		$cache[ $post_type ] = $out;

		return $out;
	}

	/**
	 * Runs the article query and returns normalized rows.
	 *
	 * Accepts: post_type, source ('category'|'manual'|'latest'), categories
	 * (array of "taxonomy:term_id"), ids (array, used when source is manual),
	 * limit, orderby ('date'|'title'|'menu_order'|'rand'), excerpt (words).
	 *
	 * @param array $args
	 * @return array
	 */
	public static function fetch( $args ) {
		$args = wp_parse_args(
			$args,
			array(
				'post_type'  => 'post',
				'source'     => 'category',
				'categories' => array(),
				'ids'        => array(),
				'limit'      => 3,
				'orderby'    => 'date',
				'excerpt'    => 24,
			)
		);

		$limit = max( 1, (int) $args['limit'] );

		$query_args = array(
			'post_type'           => $args['post_type'],
			'post_status'         => 'publish',
			'posts_per_page'      => $limit,
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
		);

		if ( 'manual' === $args['source'] ) {
			$ids = array_values( array_filter( array_map( 'absint', (array) $args['ids'] ) ) );
			if ( empty( $ids ) ) {
				return array();
			}
			// The editor's chosen order is the order they see on the page.
			$query_args['post__in']       = $ids;
			$query_args['orderby']        = 'post__in';
			$query_args['posts_per_page'] = min( $limit, count( $ids ) );
		} else {
			if ( 'category' === $args['source'] ) {
				$tax_query = self::tax_query( (array) $args['categories'] );
				if ( ! empty( $tax_query ) ) {
					$query_args['tax_query'] = $tax_query; // phpcs:ignore WordPress.DB.SlowDBQuery
				}
			}

			switch ( $args['orderby'] ) {
				case 'title':
					$query_args['orderby'] = 'title';
					$query_args['order']   = 'ASC';
					break;
				case 'menu_order':
					$query_args['orderby'] = 'menu_order';
					$query_args['order']   = 'ASC';
					break;
				case 'rand':
					$query_args['orderby'] = 'rand';
					break;
				default:
					$query_args['orderby'] = 'date';
					$query_args['order']   = 'DESC';
			}
		}

		$query = new WP_Query( $query_args );
		$out   = array();

		foreach ( $query->posts as $post ) {
			$out[] = self::normalize( $post, (int) $args['excerpt'] );
		}

		wp_reset_postdata();

		return $out;
	}

	/**
	 * Turns "taxonomy:term_id" picks into a grouped OR tax_query.
	 *
	 * @param array $picks
	 * @return array
	 */
	public static function tax_query( $picks ) {
		$grouped = array();

		foreach ( $picks as $pick ) {
			if ( false === strpos( (string) $pick, ':' ) ) {
				continue;
			}
			list( $taxonomy, $term_id ) = explode( ':', $pick, 2 );
			$term_id                    = absint( $term_id );
			if ( '' === $taxonomy || ! $term_id ) {
				continue;
			}
			$grouped[ $taxonomy ][] = $term_id;
		}

		if ( empty( $grouped ) ) {
			return array();
		}

		$tax_query = array( 'relation' => 'OR' );
		foreach ( $grouped as $taxonomy => $ids ) {
			$tax_query[] = array(
				'taxonomy' => $taxonomy,
				'field'    => 'term_id',
				'terms'    => $ids,
			);
		}

		return $tax_query;
	}

	/**
	 * One article, flattened for the templates.
	 *
	 * @param WP_Post $post
	 * @param int     $words Excerpt length.
	 * @return array
	 */
	public static function normalize( $post, $words = 24 ) {
		$excerpt = has_excerpt( $post )
			? get_the_excerpt( $post )
			: wp_strip_all_tags( strip_shortcodes( $post->post_content ) );

		/*
		 * get_the_terms(), not wp_get_post_terms(): the latter always goes to
		 * the database, and this runs once per article per strip. WP_Query has
		 * already primed the term cache these reads come out of.
		 */
		$terms = array();
		foreach ( self::taxonomies_for( $post->post_type ) as $taxonomy ) {
			$found = get_the_terms( $post->ID, $taxonomy );
			if ( is_array( $found ) ) {
				$terms = array_merge( $terms, $found );
			}
		}

		return array(
			'id'        => $post->ID,
			'title'     => get_the_title( $post ),
			'permalink' => get_permalink( $post ),
			'excerpt'   => wp_trim_words( $excerpt, max( 6, (int) $words ), '…' ),
			'image_id'  => (int) get_post_thumbnail_id( $post->ID ),
			'terms'     => $terms,
			'date'      => get_the_date( '', $post ),
		);
	}

	/**
	 * Splits "Shopify vs BigCommerce" into its two sides so the cover can stack
	 * them around a VS chip. Returns an empty array when the title is not a
	 * versus title, which is how the review cover stays a plain title.
	 *
	 * @param string $title
	 * @return array
	 */
	public static function versus_parts( $title ) {
		if ( ! preg_match( '/^(.+?)\s+(?:vs\.?|versus)\s+(.+)$/i', trim( (string) $title ), $m ) ) {
			return array();
		}

		return array( trim( $m[1] ), trim( $m[2] ) );
	}
}
