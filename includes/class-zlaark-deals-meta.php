<?php
/**
 * The "Deal Details" meta box - image, tagline, pricing, rating, highlights,
 * score breakdown and button. Every Zlaark widget reads from these fields.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Zlaark_Deals_Meta {

	/** Meta key => sanitize callback. */
	const FIELDS = array(
		// Identity
		'_zlaark_image_id'       => 'absint',
		'_zlaark_tagline'        => 'sanitize_text_field',

		// The offer
		'_zlaark_offer_type'     => 'sanitize_key',
		'_zlaark_offer_headline' => 'sanitize_text_field',
		'_zlaark_price'          => 'sanitize_text_field',
		'_zlaark_old_price'      => 'sanitize_text_field',
		'_zlaark_renewal_price'  => 'sanitize_text_field',
		'_zlaark_term_length'    => 'absint',
		'_zlaark_coupon_code'    => 'zlaark_deals_sanitize_coupon',
		'_zlaark_currency'       => 'zlaark_deals_sanitize_currency',

		// Trust
		'_zlaark_rating'         => 'zlaark_deals_sanitize_rating',
		'_zlaark_scores'         => 'sanitize_textarea_field',
		'_zlaark_verdict'        => 'sanitize_textarea_field',
		'_zlaark_reviewer'       => 'sanitize_text_field',
		'_zlaark_tested_date'    => 'zlaark_deals_sanitize_date',
		'_zlaark_last_verified'  => 'zlaark_deals_sanitize_date',

		// Terms & timing
		'_zlaark_expiry_date'    => 'zlaark_deals_sanitize_date',
		'_zlaark_refund_window'  => 'sanitize_text_field',
		'_zlaark_best_for'       => 'sanitize_textarea_field',
		'_zlaark_not_for'        => 'sanitize_textarea_field',
		'_zlaark_pros'           => 'sanitize_textarea_field',
		'_zlaark_cons'           => 'sanitize_textarea_field',

		// Presentation & actions
		'_zlaark_badge'          => 'sanitize_text_field',
		'_zlaark_rank_label'     => 'sanitize_text_field',
		'_zlaark_highlights'     => 'sanitize_textarea_field',
		'_zlaark_button_text'    => 'sanitize_text_field',
		'_zlaark_button_url'     => 'esc_url_raw',
		'_zlaark_button_new'     => 'absint',
		'_zlaark_review_url'     => 'esc_url_raw',
	);

	/** Offer types. Key => admin label; drives the neutral card chip. */
	public static function offer_types() {
		return array(
			''           => __( '- none -', 'zlaark-deals-pro' ),
			'coupon'     => __( 'Coupon', 'zlaark-deals-pro' ),
			'exclusive'  => __( 'Exclusive', 'zlaark-deals-pro' ),
			'free_trial' => __( 'Free trial', 'zlaark-deals-pro' ),
			'free_plan'  => __( 'Free plan', 'zlaark-deals-pro' ),
			'seasonal'   => __( 'Seasonal', 'zlaark-deals-pro' ),
		);
	}

	public static function init() {
		add_action( 'add_meta_boxes', array( __CLASS__, 'add_meta_box' ) );
		add_action( 'save_post_' . ZLAARK_DEALS_CPT, array( __CLASS__, 'save' ), 10, 2 );
		add_action( 'init', array( __CLASS__, 'register_meta' ) );
	}

	/** Exposes the fields to the REST API so blocks/headless clients can read them. */
	public static function register_meta() {
		foreach ( self::FIELDS as $key => $sanitize ) {
			register_post_meta(
				ZLAARK_DEALS_CPT,
				$key,
				array(
					'show_in_rest'      => true,
					'single'            => true,
					'type'              => ( 'absint' === $sanitize ) ? 'integer' : 'string',
					'sanitize_callback' => $sanitize,
					'auth_callback'     => function () {
						return current_user_can( 'edit_posts' );
					},
				)
			);
		}
	}

	public static function add_meta_box() {
		add_meta_box(
			'zlaark_deal_details',
			__( 'Deal Details', 'zlaark-deals-pro' ),
			array( __CLASS__, 'render' ),
			ZLAARK_DEALS_CPT,
			'normal',
			'high'
		);
	}

	public static function render( $post ) {
		wp_nonce_field( 'zlaark_deals_save_meta', 'zlaark_deals_meta_nonce' );

		$v = array();
		foreach ( array_keys( self::FIELDS ) as $key ) {
			$v[ $key ] = get_post_meta( $post->ID, $key, true );
		}

		$image_id = (int) $v['_zlaark_image_id'];
		$preview  = $image_id ? wp_get_attachment_image_url( $image_id, 'thumbnail' ) : '';
		?>
		<div class="zlaark-fields">

			<div class="zlaark-field zlaark-field--image">
				<label><?php esc_html_e( 'Deal Image / Logo', 'zlaark-deals-pro' ); ?></label>
				<div class="zlaark-image-box">
					<div class="zlaark-image-preview <?php echo $preview ? '' : 'is-empty'; ?>">
						<?php if ( $preview ) : ?>
							<img src="<?php echo esc_url( $preview ); ?>" alt="" />
						<?php else : ?>
							<span><?php esc_html_e( 'No image selected', 'zlaark-deals-pro' ); ?></span>
						<?php endif; ?>
					</div>
					<div class="zlaark-image-actions">
						<button type="button" class="button zlaark-select-image">
							<?php esc_html_e( 'Select image', 'zlaark-deals-pro' ); ?>
						</button>
						<button type="button" class="button-link zlaark-remove-image" <?php echo $image_id ? '' : 'style="display:none"'; ?>>
							<?php esc_html_e( 'Remove', 'zlaark-deals-pro' ); ?>
						</button>
					</div>
					<input type="hidden" class="zlaark-image-id" name="zlaark_image_id" value="<?php echo esc_attr( $image_id ); ?>" />
				</div>
			</div>

			<div class="zlaark-field">
				<label for="zlaark_tagline"><?php esc_html_e( 'Tagline', 'zlaark-deals-pro' ); ?></label>
				<input type="text" id="zlaark_tagline" name="zlaark_tagline"
					value="<?php echo esc_attr( $v['_zlaark_tagline'] ); ?>"
					placeholder="<?php esc_attr_e( 'Best all-round platform for growing stores', 'zlaark-deals-pro' ); ?>" />
			</div>

			<div class="zlaark-row">
				<div class="zlaark-field">
					<label for="zlaark_price"><?php esc_html_e( 'Pricing', 'zlaark-deals-pro' ); ?></label>
					<input type="text" id="zlaark_price" name="zlaark_price"
						value="<?php echo esc_attr( $v['_zlaark_price'] ); ?>" placeholder="$2.59/mo" />
					<p class="description"><?php esc_html_e( 'Free text - "$20.00/mo", "From $29.95", "Free Forever".', 'zlaark-deals-pro' ); ?></p>
				</div>

				<div class="zlaark-field">
					<label for="zlaark_old_price"><?php esc_html_e( 'Original Price', 'zlaark-deals-pro' ); ?></label>
					<input type="text" id="zlaark_old_price" name="zlaark_old_price"
						value="<?php echo esc_attr( $v['_zlaark_old_price'] ); ?>" placeholder="$4.95" />
					<p class="description"><?php esc_html_e( 'Optional. Rendered struck through next to the price.', 'zlaark-deals-pro' ); ?></p>
				</div>
			</div>

			<div class="zlaark-row">
				<div class="zlaark-field">
					<label for="zlaark_offer_type"><?php esc_html_e( 'Offer Type', 'zlaark-deals-pro' ); ?></label>
					<select id="zlaark_offer_type" name="zlaark_offer_type">
						<?php foreach ( self::offer_types() as $key => $label ) : ?>
							<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $v['_zlaark_offer_type'], $key ); ?>>
								<?php echo esc_html( $label ); ?>
							</option>
						<?php endforeach; ?>
					</select>
					<p class="description"><?php esc_html_e( 'Renders as the neutral chip, and lets the deals page filter by type.', 'zlaark-deals-pro' ); ?></p>
				</div>

				<div class="zlaark-field">
					<label for="zlaark_offer_headline"><?php esc_html_e( 'Offer Headline', 'zlaark-deals-pro' ); ?></label>
					<input type="text" id="zlaark_offer_headline" name="zlaark_offer_headline" maxlength="40"
						value="<?php echo esc_attr( $v['_zlaark_offer_headline'] ); ?>"
						placeholder="<?php esc_attr_e( '60-day free trial', 'zlaark-deals-pro' ); ?>" />
					<p class="description"><?php esc_html_e( 'For offers that are not a monthly price. Max 40 characters.', 'zlaark-deals-pro' ); ?></p>
				</div>

				<div class="zlaark-field">
					<label for="zlaark_coupon_code"><?php esc_html_e( 'Coupon Code', 'zlaark-deals-pro' ); ?></label>
					<input type="text" id="zlaark_coupon_code" name="zlaark_coupon_code"
						value="<?php echo esc_attr( $v['_zlaark_coupon_code'] ); ?>" placeholder="BYN2026" />
					<p class="description"><?php esc_html_e( 'Shown with a click-to-copy button. Uppercased automatically.', 'zlaark-deals-pro' ); ?></p>
				</div>
			</div>

			<div class="zlaark-row">
				<div class="zlaark-field">
					<label for="zlaark_renewal_price"><?php esc_html_e( 'Renewal Price', 'zlaark-deals-pro' ); ?></label>
					<input type="text" id="zlaark_renewal_price" name="zlaark_renewal_price"
						value="<?php echo esc_attr( $v['_zlaark_renewal_price'] ); ?>" placeholder="$12.99/mo" />
					<p class="description"><?php esc_html_e( 'What it costs after the intro term. Every competitor hides this - printing it is the most credible thing on the card.', 'zlaark-deals-pro' ); ?></p>
				</div>

				<div class="zlaark-field">
					<label for="zlaark_term_length"><?php esc_html_e( 'Term Length (months)', 'zlaark-deals-pro' ); ?></label>
					<input type="number" min="0" step="1" id="zlaark_term_length" name="zlaark_term_length"
						value="<?php echo esc_attr( $v['_zlaark_term_length'] ); ?>" placeholder="36" />
					<p class="description"><?php esc_html_e( 'Used to compute the first-term total automatically.', 'zlaark-deals-pro' ); ?></p>
				</div>

				<div class="zlaark-field">
					<label for="zlaark_currency"><?php esc_html_e( 'Currency', 'zlaark-deals-pro' ); ?></label>
					<input type="text" id="zlaark_currency" name="zlaark_currency" maxlength="3"
						value="<?php echo esc_attr( $v['_zlaark_currency'] ); ?>" placeholder="USD" />
					<p class="description"><?php esc_html_e( 'Three-letter code, for search-result markup. Leave empty for USD.', 'zlaark-deals-pro' ); ?></p>
				</div>
			</div>

			<h3 class="zlaark-section"><?php esc_html_e( 'Trust', 'zlaark-deals-pro' ); ?></h3>

			<div class="zlaark-row">
				<div class="zlaark-field">
					<label for="zlaark_tested_date"><?php esc_html_e( 'Tested On', 'zlaark-deals-pro' ); ?></label>
					<input type="date" id="zlaark_tested_date" name="zlaark_tested_date"
						value="<?php echo esc_attr( $v['_zlaark_tested_date'] ); ?>" />
					<p class="description"><?php esc_html_e( 'Printed on the card. This is the competitive position.', 'zlaark-deals-pro' ); ?></p>
				</div>

				<div class="zlaark-field">
					<label for="zlaark_last_verified"><?php esc_html_e( 'Last Verified', 'zlaark-deals-pro' ); ?></label>
					<input type="date" id="zlaark_last_verified" name="zlaark_last_verified"
						value="<?php echo esc_attr( $v['_zlaark_last_verified'] ); ?>" />
					<p class="description"><?php esc_html_e( 'Renders as "Verified 6 days ago". No competitor records this.', 'zlaark-deals-pro' ); ?></p>
				</div>

				<div class="zlaark-field">
					<label for="zlaark_reviewer"><?php esc_html_e( 'Reviewer', 'zlaark-deals-pro' ); ?></label>
					<input type="text" id="zlaark_reviewer" name="zlaark_reviewer"
						value="<?php echo esc_attr( $v['_zlaark_reviewer'] ); ?>"
						placeholder="<?php esc_attr_e( 'Who tested it', 'zlaark-deals-pro' ); ?>" />
					<p class="description"><?php esc_html_e( 'Attributed as the review author in search results.', 'zlaark-deals-pro' ); ?></p>
				</div>
			</div>

			<div class="zlaark-field">
				<label for="zlaark_verdict"><?php esc_html_e( 'Verdict', 'zlaark-deals-pro' ); ?></label>
				<textarea id="zlaark_verdict" name="zlaark_verdict" rows="2"
					placeholder="<?php esc_attr_e( 'One sentence, in your own voice.', 'zlaark-deals-pro' ); ?>"><?php echo esc_textarea( $v['_zlaark_verdict'] ); ?></textarea>
				<p class="description"><?php esc_html_e( 'Shown above the fold on the deal page, and emitted as Review markup for search results.', 'zlaark-deals-pro' ); ?></p>
			</div>

			<h3 class="zlaark-section"><?php esc_html_e( 'Terms &amp; timing', 'zlaark-deals-pro' ); ?></h3>

			<div class="zlaark-row">
				<div class="zlaark-field">
					<label for="zlaark_expiry_date"><?php esc_html_e( 'Expires On', 'zlaark-deals-pro' ); ?></label>
					<input type="date" id="zlaark_expiry_date" name="zlaark_expiry_date"
						value="<?php echo esc_attr( $v['_zlaark_expiry_date'] ); ?>" />
					<p class="description"><?php esc_html_e( 'Drives the countdown, and removes the deal from every widget once it passes. Leave empty for evergreen offers.', 'zlaark-deals-pro' ); ?></p>
				</div>

				<div class="zlaark-field">
					<label for="zlaark_refund_window"><?php esc_html_e( 'Refund Window', 'zlaark-deals-pro' ); ?></label>
					<input type="text" id="zlaark_refund_window" name="zlaark_refund_window"
						value="<?php echo esc_attr( $v['_zlaark_refund_window'] ); ?>" placeholder="30-day money back" />
				</div>

				<div class="zlaark-field">
					<label for="zlaark_review_url"><?php esc_html_e( 'Full Review URL', 'zlaark-deals-pro' ); ?></label>
					<input type="url" id="zlaark_review_url" name="zlaark_review_url"
						value="<?php echo esc_attr( $v['_zlaark_review_url'] ); ?>" placeholder="https://blogyouneed.com/reviews/..." />
					<p class="description"><?php esc_html_e( 'A second exit that keeps the visitor on the site.', 'zlaark-deals-pro' ); ?></p>
				</div>
			</div>

			<div class="zlaark-row">
				<div class="zlaark-field">
					<label for="zlaark_best_for"><?php esc_html_e( 'Best For', 'zlaark-deals-pro' ); ?></label>
					<textarea id="zlaark_best_for" name="zlaark_best_for" rows="3"><?php echo esc_textarea( $v['_zlaark_best_for'] ); ?></textarea>
					<p class="description"><?php esc_html_e( 'One per line. Who this is right for.', 'zlaark-deals-pro' ); ?></p>
				</div>

				<div class="zlaark-field">
					<label for="zlaark_not_for"><?php esc_html_e( 'Not For', 'zlaark-deals-pro' ); ?></label>
					<textarea id="zlaark_not_for" name="zlaark_not_for" rows="3"><?php echo esc_textarea( $v['_zlaark_not_for'] ); ?></textarea>
					<p class="description"><?php esc_html_e( 'Recommending against yourself is the strongest trust signal available. One per line.', 'zlaark-deals-pro' ); ?></p>
				</div>
			</div>

			<div class="zlaark-row">
				<div class="zlaark-field">
					<label for="zlaark_pros"><?php esc_html_e( 'Pros', 'zlaark-deals-pro' ); ?></label>
					<textarea id="zlaark_pros" name="zlaark_pros" rows="3"><?php echo esc_textarea( $v['_zlaark_pros'] ); ?></textarea>
					<p class="description"><?php esc_html_e( 'Deal page only. One per line.', 'zlaark-deals-pro' ); ?></p>
				</div>

				<div class="zlaark-field">
					<label for="zlaark_cons"><?php esc_html_e( 'Cons', 'zlaark-deals-pro' ); ?></label>
					<textarea id="zlaark_cons" name="zlaark_cons" rows="3"><?php echo esc_textarea( $v['_zlaark_cons'] ); ?></textarea>
					<p class="description"><?php esc_html_e( 'Deal page only. One per line.', 'zlaark-deals-pro' ); ?></p>
				</div>
			</div>

			<h3 class="zlaark-section"><?php esc_html_e( 'Presentation', 'zlaark-deals-pro' ); ?></h3>

			<div class="zlaark-row">
				<div class="zlaark-field">
					<label for="zlaark_badge"><?php esc_html_e( 'Badge', 'zlaark-deals-pro' ); ?></label>
					<input type="text" id="zlaark_badge" name="zlaark_badge"
						value="<?php echo esc_attr( $v['_zlaark_badge'] ); ?>"
						placeholder="<?php esc_attr_e( 'e.g. Best Value', 'zlaark-deals-pro' ); ?>" />
					<p class="description"><?php esc_html_e( 'Animated ribbon on the card corner.', 'zlaark-deals-pro' ); ?></p>
				</div>

				<div class="zlaark-field">
					<label for="zlaark_rank_label"><?php esc_html_e( 'Rank Label', 'zlaark-deals-pro' ); ?></label>
					<input type="text" id="zlaark_rank_label" name="zlaark_rank_label"
						value="<?php echo esc_attr( $v['_zlaark_rank_label'] ); ?>"
						placeholder="<?php esc_attr_e( 'e.g. Editor\'s Choice', 'zlaark-deals-pro' ); ?>" />
					<p class="description"><?php esc_html_e( 'Shown above the title in the Top Picks widget.', 'zlaark-deals-pro' ); ?></p>
				</div>

				<div class="zlaark-field">
					<label for="zlaark_rating"><?php esc_html_e( 'Rating (0–10)', 'zlaark-deals-pro' ); ?></label>
					<input type="number" step="0.1" min="0" max="10" id="zlaark_rating" name="zlaark_rating"
						value="<?php echo esc_attr( $v['_zlaark_rating'] ); ?>" placeholder="9.4" />
					<p class="description"><?php esc_html_e( 'Drives the animated rating ring.', 'zlaark-deals-pro' ); ?></p>
				</div>
			</div>

			<div class="zlaark-row">
				<div class="zlaark-field">
					<label for="zlaark_highlights"><?php esc_html_e( 'Highlights', 'zlaark-deals-pro' ); ?></label>
					<textarea id="zlaark_highlights" name="zlaark_highlights" rows="5"
						placeholder="<?php esc_attr_e( "Unlimited products\n24/7 support\nFree SSL", 'zlaark-deals-pro' ); ?>"><?php echo esc_textarea( $v['_zlaark_highlights'] ); ?></textarea>
					<p class="description"><?php esc_html_e( 'One per line. Rendered as a staggered checklist.', 'zlaark-deals-pro' ); ?></p>
				</div>

				<div class="zlaark-field">
					<label for="zlaark_scores"><?php esc_html_e( 'Score Breakdown', 'zlaark-deals-pro' ); ?></label>
					<textarea id="zlaark_scores" name="zlaark_scores" rows="5"
						placeholder="<?php esc_attr_e( "Features|9.4\nEase of Use|9.0\nPricing|8.6", 'zlaark-deals-pro' ); ?>"><?php echo esc_textarea( $v['_zlaark_scores'] ); ?></textarea>
					<p class="description"><?php esc_html_e( 'One per line as Label|Score (out of 10). Drives the Comparison widget bars.', 'zlaark-deals-pro' ); ?></p>
				</div>
			</div>

			<div class="zlaark-row">
				<div class="zlaark-field">
					<label for="zlaark_button_text"><?php esc_html_e( 'Button Text', 'zlaark-deals-pro' ); ?></label>
					<input type="text" id="zlaark_button_text" name="zlaark_button_text"
						value="<?php echo esc_attr( $v['_zlaark_button_text'] ); ?>" placeholder="Grab Deal" />
				</div>

				<div class="zlaark-field">
					<label for="zlaark_button_url"><?php esc_html_e( 'Button URL', 'zlaark-deals-pro' ); ?></label>
					<input type="url" id="zlaark_button_url" name="zlaark_button_url"
						value="<?php echo esc_attr( $v['_zlaark_button_url'] ); ?>" placeholder="https://example.com/offer" />
				</div>
			</div>

			<div class="zlaark-field">
				<label class="zlaark-checkbox">
					<input type="checkbox" name="zlaark_button_new" value="1" <?php checked( (int) $v['_zlaark_button_new'], 1 ); ?> />
					<?php esc_html_e( 'Open the button link in a new tab', 'zlaark-deals-pro' ); ?>
				</label>
			</div>

			<?php
			// A live readout of the derived values, so the editor can see what the
			// card will render without saving and hunting for it on the front end.
			$computed = array();
			$pct      = Zlaark_Deals_Computed::discount_pct( $v['_zlaark_price'], $v['_zlaark_old_price'] );
			$total    = Zlaark_Deals_Computed::first_term_total( $v['_zlaark_price'], $v['_zlaark_term_length'] );
			$overall  = Zlaark_Deals_Computed::overall_score( self::parse_scores( $v['_zlaark_scores'] ), $v['_zlaark_rating'] );
			$urgency  = Zlaark_Deals_Computed::urgency_label( $v['_zlaark_expiry_date'] );
			$verified = Zlaark_Deals_Computed::verified_label( $v['_zlaark_last_verified'] );

			if ( null !== $pct ) {
				/* translators: %d: discount percentage. */
				$computed[] = sprintf( __( 'Save %d%%', 'zlaark-deals-pro' ), $pct );
			}
			if ( null !== $total ) {
				/* translators: %s: first-term total. */
				$computed[] = sprintf( __( '%s first term', 'zlaark-deals-pro' ), number_format_i18n( $total, 2 ) );
			}
			if ( null !== $overall ) {
				/* translators: %s: overall score out of ten. */
				$computed[] = sprintf( __( 'Overall %s/10', 'zlaark-deals-pro' ), $overall );
			}
			if ( $urgency ) {
				$computed[] = $urgency;
			}
			if ( $verified ) {
				$computed[] = $verified;
			}
			if ( Zlaark_Deals_Computed::is_expired( $v['_zlaark_expiry_date'] ) ) {
				$computed[] = __( 'EXPIRED, hidden from every widget', 'zlaark-deals-pro' );
			}
			?>
			<?php if ( ! empty( $computed ) ) : ?>
				<p class="zlaark-hint zlaark-hint--computed">
					<strong><?php esc_html_e( 'Computed for you:', 'zlaark-deals-pro' ); ?></strong>
					<?php echo esc_html( implode( '  ·  ', $computed ) ); ?>
				</p>
			<?php endif; ?>

			<p class="zlaark-hint">
				<?php esc_html_e( 'Assign this deal to a category in the "Deal Categories" box - every Zlaark Elementor widget filters by that category.', 'zlaark-deals-pro' ); ?>
			</p>
		</div>
		<?php
	}

	public static function save( $post_id, $post ) {
		if ( ! isset( $_POST['zlaark_deals_meta_nonce'] ) ) {
			return;
		}
		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['zlaark_deals_meta_nonce'] ) ), 'zlaark_deals_save_meta' ) ) {
			return;
		}
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		foreach ( self::FIELDS as $meta_key => $sanitize ) {
			// _zlaark_price -> zlaark_price
			$input_name = ltrim( $meta_key, '_' );

			if ( '_zlaark_button_new' === $meta_key ) {
				// Unchecked checkboxes are simply absent from the request.
				update_post_meta( $post_id, $meta_key, isset( $_POST[ $input_name ] ) ? 1 : 0 );
				continue;
			}

			if ( ! isset( $_POST[ $input_name ] ) ) {
				continue;
			}

			$value = call_user_func( $sanitize, wp_unslash( $_POST[ $input_name ] ) );

			if ( '' === $value || 0 === $value ) {
				delete_post_meta( $post_id, $meta_key );
			} else {
				update_post_meta( $post_id, $meta_key, $value );
			}
		}

		update_post_meta( $post_id, '_zlaark_schema', Zlaark_Deals_Settings::SCHEMA );
	}

	/**
	 * Normalized data for one deal, ready for the frontend templates.
	 *
	 * @param int|WP_Post $post
	 * @return array
	 */
	public static function get_deal_data( $post ) {
		$post = get_post( $post );
		if ( ! $post ) {
			return array();
		}

		$image_id = (int) get_post_meta( $post->ID, '_zlaark_image_id', true );
		if ( ! $image_id && has_post_thumbnail( $post->ID ) ) {
			$image_id = (int) get_post_thumbnail_id( $post->ID );
		}

		$rating = get_post_meta( $post->ID, '_zlaark_rating', true );

		$data = array(
			'id'          => $post->ID,
			'title'       => get_the_title( $post ),
			'permalink'   => get_permalink( $post ),
			'image_id'    => $image_id,
			'tagline'     => (string) get_post_meta( $post->ID, '_zlaark_tagline', true ),
			'price'       => (string) get_post_meta( $post->ID, '_zlaark_price', true ),
			'old_price'   => (string) get_post_meta( $post->ID, '_zlaark_old_price', true ),
			'badge'       => (string) get_post_meta( $post->ID, '_zlaark_badge', true ),
			'rank_label'  => (string) get_post_meta( $post->ID, '_zlaark_rank_label', true ),
			'rating'      => ( '' === $rating ) ? null : (float) $rating,
			'highlights'  => self::parse_lines( get_post_meta( $post->ID, '_zlaark_highlights', true ) ),
			'scores'      => self::parse_scores( get_post_meta( $post->ID, '_zlaark_scores', true ) ),
			'button_text' => (string) get_post_meta( $post->ID, '_zlaark_button_text', true ),
			'button_url'  => (string) get_post_meta( $post->ID, '_zlaark_button_url', true ),
			'button_new'  => (bool) get_post_meta( $post->ID, '_zlaark_button_new', true ),
			'terms'       => wp_get_post_terms( $post->ID, ZLAARK_DEALS_TAX, array( 'fields' => 'all' ) ),
		);

		/* ---------------- stored, added in schema 2 ---------------- */

		$data['offer_type']     = (string) get_post_meta( $post->ID, '_zlaark_offer_type', true );
		$data['offer_headline'] = (string) get_post_meta( $post->ID, '_zlaark_offer_headline', true );
		$data['renewal_price']  = (string) get_post_meta( $post->ID, '_zlaark_renewal_price', true );
		$data['term_length']    = (int) get_post_meta( $post->ID, '_zlaark_term_length', true );
		$data['coupon_code']    = (string) get_post_meta( $post->ID, '_zlaark_coupon_code', true );
		$data['currency']       = (string) get_post_meta( $post->ID, '_zlaark_currency', true );
		$data['verdict']        = (string) get_post_meta( $post->ID, '_zlaark_verdict', true );
		$data['reviewer']       = (string) get_post_meta( $post->ID, '_zlaark_reviewer', true );
		$data['tested_date']    = (string) get_post_meta( $post->ID, '_zlaark_tested_date', true );
		$data['last_verified']  = (string) get_post_meta( $post->ID, '_zlaark_last_verified', true );
		$data['expiry_date']    = (string) get_post_meta( $post->ID, '_zlaark_expiry_date', true );
		$data['refund_window']  = (string) get_post_meta( $post->ID, '_zlaark_refund_window', true );
		$data['review_url']     = (string) get_post_meta( $post->ID, '_zlaark_review_url', true );
		$data['best_for']       = self::parse_lines( get_post_meta( $post->ID, '_zlaark_best_for', true ) );
		$data['not_for']        = self::parse_lines( get_post_meta( $post->ID, '_zlaark_not_for', true ) );
		$data['pros']           = self::parse_lines( get_post_meta( $post->ID, '_zlaark_pros', true ) );
		$data['cons']           = self::parse_lines( get_post_meta( $post->ID, '_zlaark_cons', true ) );

		/* ---------------- computed, never typed ---------------- */

		$c = 'Zlaark_Deals_Computed';

		$data['discount_pct']     = call_user_func( array( $c, 'discount_pct' ), $data['price'], $data['old_price'] );
		$data['annual_saving']    = call_user_func( array( $c, 'annual_saving' ), $data['price'], $data['old_price'] );
		$data['first_term_total'] = call_user_func( array( $c, 'first_term_total' ), $data['price'], $data['term_length'] );
		$data['overall_score']    = call_user_func( array( $c, 'overall_score' ), $data['scores'], $data['rating'] );
		$data['score_band']       = call_user_func( array( $c, 'score_band' ), $data['overall_score'] );
		$data['days_remaining']   = call_user_func( array( $c, 'days_until' ), $data['expiry_date'] );
		$data['is_expired']       = call_user_func( array( $c, 'is_expired' ), $data['expiry_date'] );
		$data['urgency_label']    = call_user_func( array( $c, 'urgency_label' ), $data['expiry_date'] );
		$data['verified_label']   = call_user_func( array( $c, 'verified_label' ), $data['last_verified'] );

		/**
		 * Filters the fully assembled deal, after computed values are attached.
		 *
		 * @param array   $data
		 * @param WP_Post $post
		 */
		return apply_filters( 'zlaark_deals_deal_data', $data, $post );
	}

	/** Splits a textarea into trimmed, non-empty lines. */
	public static function parse_lines( $raw ) {
		if ( ! is_string( $raw ) || '' === trim( $raw ) ) {
			return array();
		}
		$lines = preg_split( '/\r\n|\r|\n/', $raw );
		return array_values( array_filter( array_map( 'trim', $lines ), 'strlen' ) );
	}

	/**
	 * Parses "Label|9.4" lines into [ ['label' => ..., 'value' => float], ... ].
	 * A line with no pipe is kept with a null value so it still renders.
	 */
	public static function parse_scores( $raw ) {
		$out = array();
		foreach ( self::parse_lines( $raw ) as $line ) {
			$parts = explode( '|', $line, 2 );
			$label = trim( $parts[0] );
			if ( '' === $label ) {
				continue;
			}
			$value = isset( $parts[1] ) && is_numeric( trim( $parts[1] ) )
				? max( 0, min( 10, (float) trim( $parts[1] ) ) )
				: null;

			$out[] = array(
				'label' => $label,
				'value' => $value,
			);
		}
		return $out;
	}
}

/** ISO date (Y-m-d) or empty. Anything unparseable is discarded rather than stored. */
function zlaark_deals_sanitize_date( $value ) {
	$value = trim( (string) $value );
	if ( '' === $value ) {
		return '';
	}
	$ts = strtotime( $value );
	return $ts ? gmdate( 'Y-m-d', $ts ) : '';
}

/** Coupon codes are uppercased and stripped of anything a vendor wouldn't issue. */
function zlaark_deals_sanitize_coupon( $value ) {
	$value = strtoupper( trim( (string) $value ) );
	$value = preg_replace( '/[^A-Z0-9._-]/', '', $value );
	return substr( (string) $value, 0, 40 );
}

/** ISO 4217-ish: three letters, uppercased. Defaults to empty (use the site default). */
function zlaark_deals_sanitize_currency( $value ) {
	$value = strtoupper( trim( (string) $value ) );
	return preg_match( '/^[A-Z]{3}$/', $value ) ? $value : '';
}

/** Keeps the rating between 0 and 10 with one decimal, or empty when cleared. */
function zlaark_deals_sanitize_rating( $value ) {
	$value = trim( (string) $value );
	if ( '' === $value || ! is_numeric( $value ) ) {
		return '';
	}
	return (string) round( max( 0, min( 10, (float) $value ) ), 1 );
}
