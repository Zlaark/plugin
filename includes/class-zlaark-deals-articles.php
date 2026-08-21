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

	/** Hard cap on the manual picker, so the Elementor panel stays usable. */
	const PICKER_LIMIT = 200;

	/**
	 * Post types offered as an article source. Filterable so a site that keeps
	 * its reviews in a custom post type can point the strips at it.
	 *
	 * @return array slug => label
	 */
	public static function post_type_options() {
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

		return apply_filters( 'zlaark_deals_article_post_types', $types );
	}

	/**
	 * Categories for the given post type, as "taxonomy:term_id" => "Name (12)".
	 *
	 * @param string $post_type
	 * @return array
	 */
	public static function category_options( $post_type = 'post' ) {
		$out = array();

		foreach ( self::taxonomies_for( $post_type ) as $taxonomy ) {
			$terms = get_terms(
				array(
					'taxonomy'   => $taxonomy,
					'hide_empty' => false,
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
		$posts = get_posts(
			array(
				'post_type'        => $post_type,
				'post_status'      => 'publish',
				'numberposts'      => self::PICKER_LIMIT,
				'orderby'          => 'date',
				'order'            => 'DESC',
				'suppress_filters' => false,
			)
		);

		$out = array();
		foreach ( $posts as $post ) {
			$out[ $post->ID ] = wp_strip_all_tags( get_the_title( $post ) );
		}

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

		$terms = array();
		foreach ( self::taxonomies_for( $post->post_type ) as $taxonomy ) {
			$found = wp_get_post_terms( $post->ID, $taxonomy );
			if ( ! is_wp_error( $found ) ) {
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
