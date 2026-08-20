<?php
/**
 * Structured data.
 *
 * The audit found no Review, AggregateRating, Offer or FAQPage markup anywhere
 * on the site, so the scores and prices published on every deal never reached
 * Google — which is why competitor listings carry stars and this one doesn't.
 *
 * Everything here is serialised from fields the editor already fills in for the
 * card, so it costs no extra data entry.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Zlaark_Deals_Schema {

	/** Deals rendered on this request, collected for the ItemList payload. */
	private static $seen = array();

	public static function init() {
		add_action( 'wp_footer', array( __CLASS__, 'print_list_graph' ), 20 );
		add_action( 'wp_head', array( __CLASS__, 'print_single_graph' ), 20 );

		// Hooking the data filter rather than each widget means every current
		// and future caller is covered by one wire-up.
		add_filter( 'zlaark_deals_deal_data', array( __CLASS__, 'collect' ), 10, 1 );
	}

	/** Records each rendered deal so ranked lists can be marked up. Pass-through. */
	public static function collect( $deal ) {
		if ( ! is_admin() && ! empty( $deal['id'] ) ) {
			self::$seen[ (int) $deal['id'] ] = $deal;
		}
		return $deal;
	}

	private static function currency( $deal ) {
		if ( ! empty( $deal['currency'] ) ) {
			return $deal['currency'];
		}
		return apply_filters( 'zlaark_deals_default_currency', 'USD' );
	}

	/**
	 * Product + Offer + AggregateRating + Review for one deal.
	 *
	 * @param array $deal Output of Zlaark_Deals_Meta::get_deal_data().
	 * @return array|null
	 */
	public static function deal_graph( $deal ) {
		if ( empty( $deal['title'] ) ) {
			return null;
		}

		$node = array(
			'@type' => 'Product',
			'name'  => wp_strip_all_tags( $deal['title'] ),
		);

		if ( ! empty( $deal['tagline'] ) ) {
			$node['description'] = wp_strip_all_tags( $deal['tagline'] );
		}

		if ( ! empty( $deal['image_id'] ) ) {
			$img = wp_get_attachment_image_url( (int) $deal['image_id'], 'full' );
			if ( $img ) {
				$node['image'] = $img;
			}
		}

		if ( ! empty( $deal['permalink'] ) ) {
			$node['url'] = $deal['permalink'];
		}

		/* ---- Offer: price, currency, validity ---- */
		$amount = Zlaark_Deals_Computed::amount( $deal['price'] );
		if ( null !== $amount ) {
			$offer = array(
				'@type'         => 'Offer',
				'price'         => number_format( $amount, 2, '.', '' ),
				'priceCurrency' => self::currency( $deal ),
				'availability'  => empty( $deal['is_expired'] )
					? 'https://schema.org/InStock'
					: 'https://schema.org/Discontinued',
			);

			if ( ! empty( $deal['button_url'] ) ) {
				$offer['url'] = $deal['button_url'];
			}
			if ( ! empty( $deal['expiry_date'] ) ) {
				$offer['priceValidUntil'] = $deal['expiry_date'];
			}

			$node['offers'] = $offer;
		}

		/* ---- AggregateRating: the scores that currently reach nobody ---- */
		if ( null !== $deal['overall_score'] ) {
			$count = is_array( $deal['scores'] ) ? count( $deal['scores'] ) : 0;

			$node['aggregateRating'] = array(
				'@type'       => 'AggregateRating',
				'ratingValue' => (string) $deal['overall_score'],
				'bestRating'  => '10',
				'worstRating' => '0',
				'ratingCount' => (string) max( 1, $count ),
			);
		}

		/* ---- Review: only when there is a real verdict to attribute ---- */
		if ( ! empty( $deal['verdict'] ) && null !== $deal['overall_score'] ) {
			$review = array(
				'@type'        => 'Review',
				'reviewBody'   => wp_strip_all_tags( $deal['verdict'] ),
				'reviewRating' => array(
					'@type'       => 'Rating',
					'ratingValue' => (string) $deal['overall_score'],
					'bestRating'  => '10',
					'worstRating' => '0',
				),
				'author'       => array(
					'@type' => ! empty( $deal['reviewer'] ) ? 'Person' : 'Organization',
					'name'  => ! empty( $deal['reviewer'] ) ? $deal['reviewer'] : get_bloginfo( 'name' ),
				),
			);

			if ( ! empty( $deal['tested_date'] ) ) {
				$review['datePublished'] = $deal['tested_date'];
			}

			$node['review'] = $review;
		}

		return $node;
	}

	/** Single deal permalink: emit the Product graph in the head. */
	public static function print_single_graph() {
		if ( ! is_singular( ZLAARK_DEALS_CPT ) ) {
			return;
		}

		$deal = Zlaark_Deals_Meta::get_deal_data( get_queried_object_id() );
		if ( empty( $deal ) ) {
			return;
		}

		$node = self::deal_graph( $deal );
		if ( ! $node ) {
			return;
		}

		$node['@context'] = 'https://schema.org';
		self::print_json( $node );
	}

	/**
	 * Any page that rendered deals through the widgets gets an ItemList — the
	 * ranked-list markup that wins "best web hosting" style carousels.
	 */
	public static function print_list_graph() {
		if ( is_singular( ZLAARK_DEALS_CPT ) || count( self::$seen ) < 2 ) {
			return;
		}

		$items = array();
		$pos   = 1;

		foreach ( self::$seen as $deal ) {
			$node = self::deal_graph( $deal );
			if ( ! $node ) {
				continue;
			}
			$items[] = array(
				'@type'    => 'ListItem',
				'position' => $pos++,
				'item'     => $node,
			);
		}

		if ( count( $items ) < 2 ) {
			return;
		}

		self::print_json(
			array(
				'@context'        => 'https://schema.org',
				'@type'           => 'ItemList',
				'itemListOrder'   => 'https://schema.org/ItemListOrderDescending',
				'numberOfItems'   => count( $items ),
				'itemListElement' => $items,
			)
		);
	}

	private static function print_json( $data ) {
		$json = wp_json_encode( $data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
		if ( ! $json ) {
			return;
		}
		echo '<script type="application/ld+json">' . $json . '</script>' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_json_encode output.
	}
}
