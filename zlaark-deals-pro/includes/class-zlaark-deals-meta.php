<?php
/**
 * The "Deal Details" meta box — image, tagline, pricing, rating, highlights,
 * score breakdown and button. Every Zlaark widget reads from these fields.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Zlaark_Deals_Meta {

	/** Meta key => sanitize callback. */
	const FIELDS = array(
		'_zlaark_image_id'    => 'absint',
		'_zlaark_tagline'     => 'sanitize_text_field',
		'_zlaark_price'       => 'sanitize_text_field',
		'_zlaark_old_price'   => 'sanitize_text_field',
		'_zlaark_badge'       => 'sanitize_text_field',
		'_zlaark_rank_label'  => 'sanitize_text_field',
		'_zlaark_rating'      => 'zlaark_deals_sanitize_rating',
		'_zlaark_highlights'  => 'sanitize_textarea_field',
		'_zlaark_scores'      => 'sanitize_textarea_field',
		'_zlaark_button_text' => 'sanitize_text_field',
		'_zlaark_button_url'  => 'esc_url_raw',
		'_zlaark_button_new'  => 'absint',
	);

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
					<p class="description"><?php esc_html_e( 'Free text — "$20.00/mo", "From $29.95", "Free Forever".', 'zlaark-deals-pro' ); ?></p>
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

			<p class="zlaark-hint">
				<?php esc_html_e( 'Assign this deal to a category in the "Deal Categories" box — every Zlaark Elementor widget filters by that category.', 'zlaark-deals-pro' ); ?>
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

		return array(
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

/** Keeps the rating between 0 and 10 with one decimal, or empty when cleared. */
function zlaark_deals_sanitize_rating( $value ) {
	$value = trim( (string) $value );
	if ( '' === $value || ! is_numeric( $value ) ) {
		return '';
	}
	return (string) round( max( 0, min( 10, (float) $value ) ), 1 );
}
