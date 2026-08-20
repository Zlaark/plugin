<?php
/**
 * Zlaark Deal Panel — the offer panel for a single deal page.
 *
 * The live A2 Hosting page shows a logo, the bare figure "$5.00" with no period
 * or context, and then a wall of marketing copy with no call to action anywhere
 * above the fold. This widget puts the whole offer in one sticky panel: score,
 * price, savings, coupon, renewal, first-term total, refund window, countdown
 * and verification, with the score breakdown underneath.
 *
 * Drop it in an Elementor single template for the Deal post type, or point it
 * at a specific deal to use it anywhere.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;

class Zlaark_Panel_Widget extends Zlaark_Query_Widget_Base {

	public function get_name() {
		return 'zlaark_panel';
	}

	public function get_title() {
		return __( 'Zlaark Deal Panel', 'zlaark-deals-pro' );
	}

	public function get_icon() {
		return 'eicon-price-list';
	}

	public function get_keywords() {
		return array( 'deal', 'offer', 'panel', 'single', 'sticky' );
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_source',
			array(
				'label' => __( 'Deal', 'zlaark-deals-pro' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'source',
			array(
				'label'   => __( 'Deal Source', 'zlaark-deals-pro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'current',
				'options' => array(
					'current' => __( 'Current deal (single template)', 'zlaark-deals-pro' ),
					'pick'    => __( 'A specific deal', 'zlaark-deals-pro' ),
				),
			)
		);

		$this->add_control(
			'deal_id',
			array(
				'label'       => __( 'Choose Deal', 'zlaark-deals-pro' ),
				'type'        => Controls_Manager::SELECT2,
				'label_block' => true,
				'options'     => self::deal_options(),
				'condition'   => array( 'source' => 'pick' ),
			)
		);

		$this->add_control(
			'show_verdict',
			array(
				'label'        => __( 'Verdict Box', 'zlaark-deals-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'show_scores',
			array(
				'label'        => __( 'Score Breakdown', 'zlaark-deals-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'show_fit',
			array(
				'label'        => __( 'Best For / Not For', 'zlaark-deals-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'description'  => __( 'Naming who should not buy is the strongest trust signal on the page.', 'zlaark-deals-pro' ),
			)
		);

		$this->add_control(
			'sticky',
			array(
				'label'        => __( 'Stick On Scroll', 'zlaark-deals-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'sticky_offset',
			array(
				'label'      => __( 'Sticky Offset', 'zlaark-deals-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 200 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 96 ),
				'condition'  => array( 'sticky' => 'yes' ),
				'selectors'  => array(
					'{{WRAPPER}} .zd-panel--sticky' => 'top: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'nofollow',
			array(
				'label'        => __( 'Mark Links Sponsored', 'zlaark-deals-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_layout',
			array(
				'label' => __( 'Layout', 'zlaark-deals-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->max_width_control( '{{WRAPPER}} .zd-panel', 420 );
		$this->end_controls_section();

		$this->animation_controls( false );
	}

	/** Deal titles for the picker, capped so the control stays usable. */
	private static function deal_options() {
		$out   = array();
		$deals = get_posts(
			array(
				'post_type'              => ZLAARK_DEALS_CPT,
				'post_status'            => 'publish',
				'posts_per_page'         => 100,
				'orderby'                => 'title',
				'order'                  => 'ASC',
				// This runs on every editor load. Skipping other plugins'
				// pre_get_posts hooks and the meta/term caches keeps the
				// editor bootstrap cheap and predictable.
				'suppress_filters'       => true,
				'no_found_rows'          => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
			)
		);
		foreach ( $deals as $deal ) {
			$out[ $deal->ID ] = $deal->post_title;
		}
		return $out;
	}

	protected function render() {
		$s = $this->get_settings_for_display();

		$post_id = ( 'pick' === $s['source'] && ! empty( $s['deal_id'] ) )
			? (int) $s['deal_id']
			: get_the_ID();

		$deal = Zlaark_Deals_Meta::get_deal_data( $post_id );

		if ( empty( $deal ) || ZLAARK_DEALS_CPT !== get_post_type( $post_id ) ) {
			if ( $this->is_editor() ) {
				$this->render_empty_notice();
			}
			return;
		}

		$classes = array( 'zd-panel' );
		if ( 'yes' === $s['sticky'] ) {
			$classes[] = 'zd-panel--sticky';
		}
		if ( $deal['is_expired'] ) {
			$classes[] = 'zd-panel--expired';
		}

		$type_labels = Zlaark_Deals_Meta::offer_types();
		$type_label  = ( '' !== $deal['offer_type'] && isset( $type_labels[ $deal['offer_type'] ] ) )
			? $type_labels[ $deal['offer_type'] ]
			: '';
		?>
		<aside class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">

			<?php if ( $deal['is_expired'] ) : ?>
				<p class="zd-panel__expired">
					<?php esc_html_e( 'This offer has ended. We keep the page up so the price history stays honest.', 'zlaark-deals-pro' ); ?>
				</p>
			<?php endif; ?>

			<div class="zd-panel__head">
				<?php if ( $deal['image_id'] ) : ?>
					<div class="zd-panel__logo">
						<?php echo wp_get_attachment_image( $deal['image_id'], 'medium', false, array( 'loading' => 'lazy' ) ); ?>
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

			<?php if ( '' !== $deal['urgency_label'] || '' !== $type_label ) : ?>
				<div class="zd-panel__flags">
					<?php if ( '' !== $deal['urgency_label'] ) : ?>
						<span class="zd-chip zd-chip--ember"><?php echo esc_html( $deal['urgency_label'] ); ?></span>
					<?php elseif ( '' !== $type_label ) : ?>
						<span class="zd-chip zd-chip--neutral"><?php echo esc_html( $type_label ); ?></span>
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
					/* translators: %s: total cost across the first term. */
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

			<?php $this->render_cta( $deal, $s, 'zd-btn zd-btn--solid zd-panel__cta' ); ?>

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

			<?php if ( 'yes' === $s['show_verdict'] && '' !== $deal['verdict'] ) : ?>
				<div class="zd-panel__verdict">
					<h3><?php esc_html_e( 'Our verdict', 'zlaark-deals-pro' ); ?></h3>
					<p><?php echo esc_html( $deal['verdict'] ); ?></p>
				</div>
			<?php endif; ?>

			<?php if ( 'yes' === $s['show_scores'] && ! empty( $deal['scores'] ) ) : ?>
				<div class="zd-panel__scores">
					<h3><?php esc_html_e( 'How it scored', 'zlaark-deals-pro' ); ?></h3>
					<?php $this->render_scores_bars( $deal['scores'] ); ?>
				</div>
			<?php endif; ?>

			<?php if ( 'yes' === $s['show_fit'] && ( ! empty( $deal['best_for'] ) || ! empty( $deal['not_for'] ) ) ) : ?>
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
	}

	/** Score bars that fill on scroll, coloured by the semantic ramp. */
	private function render_scores_bars( $scores ) {
		?>
		<div class="zd-panel__bars">
			<?php foreach ( $scores as $i => $row ) : ?>
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
		<?php
	}
}
