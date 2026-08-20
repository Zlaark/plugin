<?php
/**
 * The offer panel markup, in one place.
 *
 * Both the Deal Panel widget and the single-deal page injector render through
 * this, so the two can never drift apart — and the injector works without
 * Elementor Pro, or without Elementor at all.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Zlaark_Deals_Panel {

	/**
	 * @param array $deal Output of Zlaark_Deals_Meta::get_deal_data().
	 * @param array $args sticky|verdict|scores|fit|nofollow
	 * @return string
	 */
	public static function html( $deal, $args = array() ) {
		if ( empty( $deal ) || empty( $deal['title'] ) ) {
			return '';
		}

		$a = wp_parse_args(
			$args,
			array(
				'sticky'   => true,
				'verdict'  => true,
				'scores'   => true,
				'fit'      => true,
				'nofollow' => true,
			)
		);

		$classes = array( 'zd-panel' );
		if ( $a['sticky'] ) {
			$classes[] = 'zd-panel--sticky';
		}
		if ( ! empty( $deal['is_expired'] ) ) {
			$classes[] = 'zd-panel--expired';
		}

		$types = Zlaark_Deals_Meta::offer_types();
		$type  = ( '' !== $deal['offer_type'] && isset( $types[ $deal['offer_type'] ] ) )
			? $types[ $deal['offer_type'] ]
			: '';

		ob_start();
		?>
		<aside class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">

			<?php if ( ! empty( $deal['is_expired'] ) ) : ?>
				<p class="zd-panel__expired">
					<?php esc_html_e( 'This offer has ended. We keep the page up so the price history stays honest.', 'zlaark-deals-pro' ); ?>
				</p>
			<?php endif; ?>

			<div class="zd-panel__head">
				<?php if ( ! empty( $deal['image_id'] ) ) : ?>
					<div class="zd-panel__logo">
						<?php echo wp_get_attachment_image( (int) $deal['image_id'], 'medium', false, array( 'loading' => 'lazy' ) ); ?>
					</div>
				<?php endif; ?>

				<div class="zd-panel__ident">
					<?php if ( '' !== $deal['rank_label'] ) : ?>
						<span class="zd-chip zd-chip--brand"><?php echo esc_html( $deal['rank_label'] ); ?></span>
					<?php endif; ?>
					<h2 class="zd-panel__name"><?php echo esc_html( $deal['title'] ); ?></h2>
					<?php if ( null !== $deal['overall_score'] ) : ?>
						<p class="zd-panel__score">
							<span class="zd-panel__scorenum zd-score--<?php echo esc_attr( $deal['score_band'] ); ?>">
								<?php echo esc_html( number_format_i18n( $deal['overall_score'], 1 ) ); ?>
							</span>
							<span class="zd-panel__scoreof"><?php esc_html_e( 'out of 10', 'zlaark-deals-pro' ); ?></span>
						</p>
					<?php endif; ?>
				</div>
			</div>

			<?php if ( '' !== $deal['urgency_label'] || '' !== $type ) : ?>
				<div class="zd-panel__flags">
					<?php if ( '' !== $deal['urgency_label'] ) : ?>
						<span class="zd-chip zd-chip--ember"><?php echo esc_html( $deal['urgency_label'] ); ?></span>
					<?php else : ?>
						<span class="zd-chip zd-chip--neutral"><?php echo esc_html( $type ); ?></span>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<div class="zd-panel__pricing">
				<?php if ( '' !== $deal['price'] ) : ?>
					<span class="zd-panel__price"><?php echo esc_html( $deal['price'] ); ?></span>
				<?php elseif ( '' !== $deal['offer_headline'] ) : ?>
					<span class="zd-panel__price"><?php echo esc_html( $deal['offer_headline'] ); ?></span>
				<?php endif; ?>
				<?php if ( '' !== $deal['old_price'] ) : ?>
					<s class="zd-panel__old"><?php echo esc_html( $deal['old_price'] ); ?></s>
				<?php endif; ?>
				<?php if ( null !== $deal['discount_pct'] ) : ?>
					<span class="zd-panel__save">
						<?php
						printf(
							/* translators: %d: discount percentage, rounded down. */
							esc_html__( 'Save %d%%', 'zlaark-deals-pro' ),
							(int) $deal['discount_pct']
						);
						?>
					</span>
				<?php endif; ?>
			</div>

			<?php if ( '' !== $deal['coupon_code'] ) : ?>
				<div class="zd-coupon" data-zd-coupon="<?php echo esc_attr( $deal['coupon_code'] ); ?>">
					<span class="zd-coupon__code"><?php echo esc_html( $deal['coupon_code'] ); ?></span>
					<button type="button" class="zd-coupon__copy"
						data-zd-copied="<?php esc_attr_e( 'Copied', 'zlaark-deals-pro' ); ?>">
						<?php esc_html_e( 'Copy code', 'zlaark-deals-pro' ); ?>
					</button>
				</div>
			<?php endif; ?>

			<?php
			$terms = array();
			if ( '' !== $deal['renewal_price'] ) {
				$terms[] = $deal['term_length'] > 0
					? sprintf(
						/* translators: 1: renewal price, 2: term length in months. */
						__( 'Renews at %1$s after %2$d months', 'zlaark-deals-pro' ),
						$deal['renewal_price'],
						(int) $deal['term_length']
					)
					: sprintf(
						/* translators: %s: renewal price. */
						__( 'Renews at %s', 'zlaark-deals-pro' ),
						$deal['renewal_price']
					);
			}
			if ( null !== $deal['first_term_total'] ) {
				$terms[] = sprintf(
					/* translators: %s: total across the first term. */
					__( '%s total for the first term', 'zlaark-deals-pro' ),
					number_format_i18n( $deal['first_term_total'], 2 )
				);
			}
			if ( '' !== $deal['refund_window'] ) {
				$terms[] = $deal['refund_window'];
			}
			?>
			<?php if ( ! empty( $terms ) ) : ?>
				<ul class="zd-panel__terms">
					<?php foreach ( $terms as $line ) : ?>
						<li><?php echo esc_html( $line ); ?></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>

			<?php if ( '' !== $deal['button_text'] && ( '' !== $deal['button_url'] || '' !== $deal['permalink'] ) ) : ?>
				<?php
				$url = '' !== $deal['button_url'] ? $deal['button_url'] : $deal['permalink'];
				$rel = $a['nofollow'] ? 'nofollow sponsored noopener' : 'noopener';
				?>
				<a class="zd-btn zd-btn--solid zd-panel__cta"
					href="<?php echo esc_url( $url ); ?>"
					rel="<?php echo esc_attr( $rel ); ?>"
					<?php echo ! empty( $deal['button_new'] ) ? 'target="_blank"' : ''; ?>>
					<span class="zd-btn__label"><?php echo esc_html( $deal['button_text'] ); ?></span>
				</a>
			<?php endif; ?>

			<?php if ( '' !== $deal['verified_label'] || '' !== $deal['reviewer'] ) : ?>
				<p class="zd-panel__trust">
					<?php if ( '' !== $deal['verified_label'] ) : ?>
						<span class="zd-panel__verified"><?php echo esc_html( $deal['verified_label'] ); ?></span>
					<?php endif; ?>
					<?php if ( '' !== $deal['reviewer'] ) : ?>
						<span class="zd-panel__by">
							<?php
							printf(
								/* translators: %s: reviewer name. */
								esc_html__( 'Tested by %s', 'zlaark-deals-pro' ),
								esc_html( $deal['reviewer'] )
							);
							?>
						</span>
					<?php endif; ?>
				</p>
			<?php endif; ?>

			<?php if ( $a['verdict'] && '' !== $deal['verdict'] ) : ?>
				<div class="zd-panel__verdict">
					<h3><?php esc_html_e( 'Our verdict', 'zlaark-deals-pro' ); ?></h3>
					<p><?php echo esc_html( $deal['verdict'] ); ?></p>
				</div>
			<?php endif; ?>

			<?php if ( $a['scores'] && ! empty( $deal['scores'] ) ) : ?>
				<div class="zd-panel__scores">
					<h3><?php esc_html_e( 'How it scored', 'zlaark-deals-pro' ); ?></h3>
					<div class="zd-panel__bars">
						<?php foreach ( $deal['scores'] as $i => $row ) : ?>
							<?php
							if ( null === $row['value'] ) {
								continue;
							}
							$band = Zlaark_Deals_Computed::score_band( $row['value'] );
							?>
							<div class="zd-panel__bar" style="--zd-i:<?php echo (int) $i; ?>">
								<span class="zd-panel__barlabel"><?php echo esc_html( $row['label'] ); ?></span>
								<span class="zd-panel__bartrack">
									<i class="zd-panel__barfill zd-fill--<?php echo esc_attr( $band ); ?>"
										data-zd-bar="<?php echo esc_attr( $row['value'] * 10 ); ?>"></i>
								</span>
								<b class="zd-score--<?php echo esc_attr( $band ); ?>">
									<?php echo esc_html( number_format_i18n( $row['value'], 1 ) ); ?>
								</b>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
			<?php endif; ?>

			<?php if ( $a['fit'] && ( ! empty( $deal['best_for'] ) || ! empty( $deal['not_for'] ) ) ) : ?>
				<div class="zd-panel__fit">
					<?php if ( ! empty( $deal['best_for'] ) ) : ?>
						<div class="zd-panel__fitcol">
							<h3><?php esc_html_e( 'Best for', 'zlaark-deals-pro' ); ?></h3>
							<ul>
								<?php foreach ( $deal['best_for'] as $line ) : ?>
									<li><?php echo esc_html( $line ); ?></li>
								<?php endforeach; ?>
							</ul>
						</div>
					<?php endif; ?>
					<?php if ( ! empty( $deal['not_for'] ) ) : ?>
						<div class="zd-panel__fitcol zd-panel__fitcol--not">
							<h3><?php esc_html_e( 'Not for', 'zlaark-deals-pro' ); ?></h3>
							<ul>
								<?php foreach ( $deal['not_for'] as $line ) : ?>
									<li><?php echo esc_html( $line ); ?></li>
								<?php endforeach; ?>
							</ul>
						</div>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<?php if ( '' !== $deal['review_url'] ) : ?>
				<a class="zd-panel__review" href="<?php echo esc_url( $deal['review_url'] ); ?>">
					<?php
					printf(
						/* translators: %s: deal title. */
						esc_html__( 'Read the full %s review', 'zlaark-deals-pro' ),
						esc_html( $deal['title'] )
					);
					?>
				</a>
			<?php endif; ?>
		</aside>
		<?php
		return (string) ob_get_clean();
	}
}
