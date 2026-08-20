<?php
/**
 * Values derived at render time rather than stored.
 *
 * Nothing in here is ever typed by an editor. Deriving these means a card can
 * carry far more information than the fields behind it, and - more importantly
 * - a headline score can never contradict its own breakdown, and a discount
 * percentage can never disagree with the two prices printed beside it.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Zlaark_Deals_Computed {

	/** Countdowns only render inside this window; beyond it they read as fake. */
	const URGENCY_DAYS = 14;

	/**
	 * Pulls the first number out of a free-text price such as "$5.00/mo",
	 * "From $29.95" or "€1 200,50". Returns null when there is no number,
	 * which is how "Free Forever" stays free text without breaking the maths.
	 *
	 * @param string $raw
	 * @return float|null
	 */
	public static function amount( $raw ) {
		$raw = (string) $raw;
		if ( '' === trim( $raw ) ) {
			return null;
		}

		if ( ! preg_match( '/\d[\d.,\s]*/', $raw, $m ) ) {
			return null;
		}

		$num = preg_replace( '/\s+/', '', $m[0] );

		// Decide which separator is the decimal point by looking at the last one.
		$last_dot   = strrpos( $num, '.' );
		$last_comma = strrpos( $num, ',' );

		if ( false !== $last_comma && ( false === $last_dot || $last_comma > $last_dot ) ) {
			$num = str_replace( '.', '', $num );
			$num = str_replace( ',', '.', $num );
		} else {
			$num = str_replace( ',', '', $num );
		}

		$num = rtrim( $num, '.' );

		return is_numeric( $num ) ? (float) $num : null;
	}

	/**
	 * Percentage off, rounded DOWN.
	 *
	 * Flooring rather than rounding is deliberate: 61.5% off must advertise as
	 * 61%, never 62%. On a site whose whole position is honesty about pricing,
	 * the discount shown should never be larger than the discount given.
	 *
	 * Null unless both prices are numeric and the maths makes sense.
	 */
	public static function discount_pct( $price, $old_price ) {
		$new = self::amount( $price );
		$old = self::amount( $old_price );

		if ( null === $new || null === $old || $old <= 0 || $new >= $old ) {
			return null;
		}

		return (int) floor( ( ( $old - $new ) / $old ) * 100 );
	}

	/** What the visitor saves over twelve months at the intro price. */
	public static function annual_saving( $price, $old_price ) {
		$new = self::amount( $price );
		$old = self::amount( $old_price );

		if ( null === $new || null === $old || $new >= $old ) {
			return null;
		}

		return round( ( $old - $new ) * 12, 2 );
	}

	/**
	 * Price x term. Turns a cheap monthly figure into the real commitment,
	 * which is the number the vendor would rather nobody worked out.
	 */
	public static function first_term_total( $price, $term_length ) {
		$new  = self::amount( $price );
		$term = (int) $term_length;

		if ( null === $new || $term < 1 ) {
			return null;
		}

		return round( $new * $term, 2 );
	}

	/**
	 * Mean of the score breakdown, falling back to the typed rating when no
	 * breakdown exists. Computing it means the headline number and the bars
	 * underneath it can never disagree.
	 *
	 * @param array      $scores Parsed breakdown rows.
	 * @param float|null $rating Typed fallback.
	 * @return float|null
	 */
	public static function overall_score( $scores, $rating = null ) {
		$values = array();

		if ( is_array( $scores ) ) {
			foreach ( $scores as $row ) {
				if ( isset( $row['value'] ) && null !== $row['value'] ) {
					$values[] = (float) $row['value'];
				}
			}
		}

		if ( ! empty( $values ) ) {
			return round( array_sum( $values ) / count( $values ), 1 );
		}

		return ( null === $rating || '' === $rating ) ? null : round( (float) $rating, 1 );
	}

	/** Maps a 0-10 score onto the semantic ramp. */
	public static function score_band( $score ) {
		if ( null === $score || '' === $score ) {
			return '';
		}
		$score = (float) $score;

		if ( $score >= 8.0 ) {
			return 'good';
		}
		if ( $score >= 6.5 ) {
			return 'fair';
		}
		return 'weak';
	}

	/** Today, in the site's timezone, as a Y-m-d string. */
	private static function today() {
		return current_time( 'Y-m-d' );
	}

	/** Whole days from today to $date. Negative once the date has passed. */
	public static function days_until( $date ) {
		if ( empty( $date ) ) {
			return null;
		}

		$then = strtotime( $date . ' 23:59:59' );
		$now  = strtotime( self::today() . ' 00:00:00' );

		if ( ! $then || ! $now ) {
			return null;
		}

		return (int) floor( ( $then - $now ) / DAY_IN_SECONDS );
	}

	/** Whole days since $date. */
	public static function days_since( $date ) {
		if ( empty( $date ) ) {
			return null;
		}

		$then = strtotime( $date . ' 00:00:00' );
		$now  = strtotime( self::today() . ' 00:00:00' );

		if ( ! $then || ! $now ) {
			return null;
		}

		return (int) max( 0, floor( ( $now - $then ) / DAY_IN_SECONDS ) );
	}

	/** True once the expiry date is in the past. Drives automatic hiding. */
	public static function is_expired( $expiry_date ) {
		if ( empty( $expiry_date ) ) {
			return false;
		}
		$days = self::days_until( $expiry_date );
		return ( null !== $days && $days < 0 );
	}

	/**
	 * "Ends in 6 days" - but only inside the urgency window, and only when a
	 * real deadline exists. Permanent urgency reads as fake, so this returns
	 * an empty string far more often than not.
	 */
	public static function urgency_label( $expiry_date ) {
		$days = self::days_until( $expiry_date );

		if ( null === $days || $days < 0 || $days > self::URGENCY_DAYS ) {
			return '';
		}

		if ( 0 === $days ) {
			return __( 'Ends today', 'zlaark-deals-pro' );
		}
		if ( 1 === $days ) {
			return __( 'Ends tomorrow', 'zlaark-deals-pro' );
		}

		/* translators: %d: number of days remaining. */
		return sprintf( _n( 'Ends in %d day', 'Ends in %d days', $days, 'zlaark-deals-pro' ), $days );
	}

	/**
	 * "Verified 6 days ago". The freshness signal no competitor in this
	 * category records, let alone prints.
	 */
	public static function verified_label( $last_verified ) {
		$days = self::days_since( $last_verified );

		if ( null === $days ) {
			return '';
		}

		if ( 0 === $days ) {
			return __( 'Verified today', 'zlaark-deals-pro' );
		}
		if ( 1 === $days ) {
			return __( 'Verified yesterday', 'zlaark-deals-pro' );
		}
		if ( $days < 45 ) {
			/* translators: %d: number of days since verification. */
			return sprintf( _n( 'Verified %d day ago', 'Verified %d days ago', $days, 'zlaark-deals-pro' ), $days );
		}

		$months = (int) max( 1, round( $days / 30 ) );
		/* translators: %d: number of months since verification. */
		return sprintf( _n( 'Verified %d month ago', 'Verified %d months ago', $months, 'zlaark-deals-pro' ), $months );
	}

	/**
	 * Meta query fragment that keeps expired deals out of every widget.
	 * Deals with no expiry date set are evergreen and always included.
	 */
	public static function not_expired_meta_query() {
		return array(
			'relation' => 'OR',
			array(
				'key'     => '_zlaark_expiry_date',
				'compare' => 'NOT EXISTS',
			),
			array(
				'key'     => '_zlaark_expiry_date',
				'value'   => '',
				'compare' => '=',
			),
			array(
				'key'     => '_zlaark_expiry_date',
				'value'   => current_time( 'Y-m-d' ),
				'compare' => '>=',
				'type'    => 'DATE',
			),
		);
	}
}
