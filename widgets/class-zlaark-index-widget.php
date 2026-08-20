<?php
/**
 * Zlaark Deals Index — the deals page.
 *
 * The reference site's /deals index is a flat list of ~40 cards under four
 * headings with no filter, no sort and no search. At 100+ deals that stops
 * being browsable, so this widget adds category and offer-type filters, four
 * sort orders, search, a compare tray and URL state.
 *
 * Filtering and sorting happen client-side against data attributes, so the page
 * stays fully cacheable and works with a static page cache in front of it.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;

class Zlaark_Index_Widget extends Zlaark_Widget_Base {

	public function get_name() {
		return 'zlaark_index';
	}

	public function get_title() {
		return __( 'Zlaark Deals Index', 'zlaark-deals-pro' );
	}

	public function get_icon() {
		return 'eicon-post-list';
	}

	public function get_keywords() {
		return array( 'deals', 'index', 'archive', 'filter', 'sort', 'search' );
	}

	protected function register_controls() {

		/* ------------------------------------------------ content */

		$this->query_controls( 60 );

		$this->start_controls_section(
			'section_index',
			array(
				'label' => __( 'Index', 'zlaark-deals-pro' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'heading',
			array(
				'label'       => __( 'Heading', 'zlaark-deals-pro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( "Every deal we've verified", 'zlaark-deals-pro' ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'subheading',
			array(
				'label'       => __( 'Sub-heading', 'zlaark-deals-pro' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 2,
				'default'     => __( 'Every offer bought and tested by us. Nothing here is sponsored.', 'zlaark-deals-pro' ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'show_counters',
			array(
				'label'        => __( 'Summary Counters', 'zlaark-deals-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'description'  => __( 'Live deals, average saving and how many expire this week — all computed.', 'zlaark-deals-pro' ),
			)
		);

		$this->add_control(
			'show_filters',
			array(
				'label'        => __( 'Category Filters', 'zlaark-deals-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'show_types',
			array(
				'label'        => __( 'Offer Type Filters', 'zlaark-deals-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'show_sort',
			array(
				'label'        => __( 'Sort Control', 'zlaark-deals-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'show_search',
			array(
				'label'        => __( 'Search', 'zlaark-deals-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'show_compare',
			array(
				'label'        => __( 'Compare Tray', 'zlaark-deals-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'description'  => __( 'Tick two or more deals to open a comparison.', 'zlaark-deals-pro' ),
			)
		);

		$this->add_control(
			'compare_url',
			array(
				'label'       => __( 'Comparison Page URL', 'zlaark-deals-pro' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => 'https://example.com/compare/',
				'condition'   => array( 'show_compare' => 'yes' ),
				'description' => __( 'Selected deal IDs are appended as ?deals=12,34.', 'zlaark-deals-pro' ),
			)
		);

		$this->add_control(
			'page_size',
			array(
				'label'   => __( 'Show At A Time', 'zlaark-deals-pro' ),
				'type'    => Controls_Manager::NUMBER,
				'min'     => 6,
				'max'     => 60,
				'step'    => 6,
				'default' => 24,
			)
		);

		$this->add_control(
			'nofollow',
			array(
				'label'        => __( 'Mark Links Sponsored', 'zlaark-deals-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'description'  => __( 'Adds rel="nofollow sponsored" to affiliate links.', 'zlaark-deals-pro' ),
			)
		);

		$this->end_controls_section();

		/* ------------------------------------------------ style */

		$this->max_width_control( '{{WRAPPER}} .zd-index' );
		$this->animation_controls( false );
	}

	/**
	 * Sortable, filterable payload for one row.
	 * Every value here is derived, so the sort orders cannot drift from the card.
	 */
	private function row_data( $deal ) {
		$saving   = ( null !== $deal['discount_pct'] ) ? (int) $deal['discount_pct'] : -1;
		$score    = ( null !== $deal['overall_score'] ) ? (float) $deal['overall_score'] : -1;
		$verified = Zlaark_Deals_Computed::days_since( $deal['last_verified'] );
		$ends     = Zlaark_Deals_Computed::days_until( $deal['expiry_date'] );

		return array(
			'cats'     => implode( ',', wp_list_pluck( $deal['terms'], 'term_id' ) ),
			'type'     => $deal['offer_type'],
			'saving'   => $saving,
			'score'    => $score,
			// Never verified sorts last, so a large sentinel stands in for null.
			'verified' => ( null === $verified ) ? 99999 : (int) $verified,
			'ends'     => ( null === $ends ) ? 99999 : (int) $ends,
			'name'     => $deal['title'],
			'search'   => strtolower( $deal['title'] . ' ' . $deal['tagline'] . ' ' . $deal['offer_headline'] ),
		);
	}

	protected function render() {
		$s     = $this->get_settings_for_display();
		$query = new WP_Query( $this->build_query_args( $s ) );

		if ( ! $query->have_posts() ) {
			$this->render_empty_notice();
			return;
		}

		$deals = array();
		$cats  = array();
		$types = array();

		while ( $query->have_posts() ) {
			$query->the_post();
			$deal = Zlaark_Deals_Meta::get_deal_data( get_post() );
			if ( empty( $deal ) ) {
				continue;
			}
			$deals[] = $deal;

			foreach ( $deal['terms'] as $term ) {
				if ( ! isset( $cats[ $term->term_id ] ) ) {
					$cats[ $term->term_id ] = array( 'name' => $term->name, 'n' => 0 );
				}
				$cats[ $term->term_id ]['n']++;
			}
			if ( '' !== $deal['offer_type'] ) {
				$types[ $deal['offer_type'] ] = isset( $types[ $deal['offer_type'] ] ) ? $types[ $deal['offer_type'] ] + 1 : 1;
			}
		}
		wp_reset_postdata();

		if ( empty( $deals ) ) {
			$this->render_empty_notice();
			return;
		}

		// Counters are computed from the same data the rows render.
		$savings = array();
		$ending  = 0;
		foreach ( $deals as $deal ) {
			if ( null !== $deal['discount_pct'] ) {
				$savings[] = (int) $deal['discount_pct'];
			}
			$days = Zlaark_Deals_Computed::days_until( $deal['expiry_date'] );
			if ( null !== $days && $days >= 0 && $days <= 7 ) {
				$ending++;
			}
		}
		$avg_saving = ! empty( $savings ) ? (int) floor( array_sum( $savings ) / count( $savings ) ) : 0;

		$type_labels = Zlaark_Deals_Meta::offer_types();
		$page_size   = max( 6, (int) $s['page_size'] );
		$compare_url = ! empty( $s['compare_url']['url'] ) ? $s['compare_url']['url'] : '';
		?>
		<div class="zd-index" data-zd-index data-zd-page="<?php echo esc_attr( $page_size ); ?>"
			data-zd-compare-url="<?php echo esc_url( $compare_url ); ?>">

			<?php if ( '' !== $s['heading'] || '' !== $s['subheading'] ) : ?>
				<div class="zd-index__head">
					<?php if ( '' !== $s['heading'] ) : ?>
						<h2 class="zd-index__title"><?php echo esc_html( $s['heading'] ); ?></h2>
					<?php endif; ?>
					<?php if ( '' !== $s['subheading'] ) : ?>
						<p class="zd-index__sub"><?php echo esc_html( $s['subheading'] ); ?></p>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<?php if ( 'yes' === $s['show_counters'] ) : ?>
				<div class="zd-index__counters">
					<div class="zd-index__counter">
						<b><?php echo esc_html( number_format_i18n( count( $deals ) ) ); ?></b>
						<span><?php esc_html_e( 'live deals', 'zlaark-deals-pro' ); ?></span>
					</div>
					<?php if ( $avg_saving > 0 ) : ?>
						<div class="zd-index__counter">
							<b><?php echo esc_html( $avg_saving ); ?>%</b>
							<span><?php esc_html_e( 'average saving', 'zlaark-deals-pro' ); ?></span>
						</div>
					<?php endif; ?>
					<?php if ( $ending > 0 ) : ?>
						<div class="zd-index__counter zd-index__counter--ember">
							<b><?php echo esc_html( $ending ); ?></b>
							<span><?php esc_html_e( 'expiring this week', 'zlaark-deals-pro' ); ?></span>
						</div>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<div class="zd-index__controls">
				<?php if ( 'yes' === $s['show_filters'] && count( $cats ) > 1 ) : ?>
					<div class="zd-index__chips" role="group" aria-label="<?php esc_attr_e( 'Filter by category', 'zlaark-deals-pro' ); ?>">
						<button type="button" class="zd-fchip is-on" data-zd-cat="all" aria-pressed="true">
							<?php
							printf(
								/* translators: %d: total number of deals. */
								esc_html__( 'All %d', 'zlaark-deals-pro' ),
								count( $deals )
							);
							?>
						</button>
						<?php foreach ( $cats as $term_id => $cat ) : ?>
							<button type="button" class="zd-fchip" data-zd-cat="<?php echo esc_attr( $term_id ); ?>" aria-pressed="false">
								<?php echo esc_html( $cat['name'] ); ?>
								<span class="zd-fchip__n"><?php echo esc_html( $cat['n'] ); ?></span>
							</button>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>

				<div class="zd-index__row">
					<?php if ( 'yes' === $s['show_types'] && ! empty( $types ) ) : ?>
						<div class="zd-index__chips zd-index__chips--type" role="group" aria-label="<?php esc_attr_e( 'Filter by offer type', 'zlaark-deals-pro' ); ?>">
							<?php foreach ( $types as $key => $n ) : ?>
								<button type="button" class="zd-tchip" data-zd-type="<?php echo esc_attr( $key ); ?>" aria-pressed="false">
									<?php echo esc_html( isset( $type_labels[ $key ] ) ? $type_labels[ $key ] : $key ); ?>
								</button>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>

					<?php if ( 'yes' === $s['show_sort'] ) : ?>
						<label class="zd-index__sortwrap">
							<span class="screen-reader-text"><?php esc_html_e( 'Sort deals', 'zlaark-deals-pro' ); ?></span>
							<select class="zd-index__sort" data-zd-sort>
								<option value="saving"><?php esc_html_e( 'Sort: Biggest saving', 'zlaark-deals-pro' ); ?></option>
								<option value="score"><?php esc_html_e( 'Highest score', 'zlaark-deals-pro' ); ?></option>
								<option value="verified"><?php esc_html_e( 'Recently verified', 'zlaark-deals-pro' ); ?></option>
								<option value="ends"><?php esc_html_e( 'Ending soonest', 'zlaark-deals-pro' ); ?></option>
								<option value="name"><?php esc_html_e( 'Name A–Z', 'zlaark-deals-pro' ); ?></option>
							</select>
						</label>
					<?php endif; ?>

					<?php if ( 'yes' === $s['show_search'] ) : ?>
						<label class="zd-index__searchwrap">
							<span class="screen-reader-text"><?php esc_html_e( 'Search deals', 'zlaark-deals-pro' ); ?></span>
							<input type="search" class="zd-index__search" data-zd-search
								placeholder="<?php
									printf(
										/* translators: %d: number of deals. */
										esc_attr__( 'Search %d deals…', 'zlaark-deals-pro' ),
										count( $deals )
									);
								?>" />
						</label>
					<?php endif; ?>
				</div>
			</div>

			<div class="zd-index__list" data-zd-list>
				<?php
				foreach ( $deals as $i => $deal ) {
					$this->render_row( $deal, $s, $i );
				}
				?>
			</div>

			<p class="zd-index__empty" data-zd-empty hidden>
				<strong><?php esc_html_e( 'No deals match those filters.', 'zlaark-deals-pro' ); ?></strong>
				<?php esc_html_e( 'Clear a filter, or search for something else.', 'zlaark-deals-pro' ); ?>
				<button type="button" class="zd-index__clear" data-zd-clear><?php esc_html_e( 'Clear all filters', 'zlaark-deals-pro' ); ?></button>
			</p>

			<?php if ( count( $deals ) > $page_size ) : ?>
				<div class="zd-index__more">
					<button type="button" class="zd-btn zd-btn--ghost" data-zd-more>
						<?php esc_html_e( 'Show more deals', 'zlaark-deals-pro' ); ?>
					</button>
				</div>
			<?php endif; ?>

			<?php if ( 'yes' === $s['show_compare'] ) : ?>
				<div class="zd-index__tray" data-zd-tray hidden>
					<span class="zd-index__traycount" data-zd-traycount>0</span>
					<span class="zd-index__traylabel"><?php esc_html_e( 'deals selected', 'zlaark-deals-pro' ); ?></span>
					<button type="button" class="zd-index__trayclear" data-zd-trayclear><?php esc_html_e( 'Clear', 'zlaark-deals-pro' ); ?></button>
					<a class="zd-btn zd-btn--solid" href="#" data-zd-traygo><?php esc_html_e( 'Compare →', 'zlaark-deals-pro' ); ?></a>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}

	private function render_row( $deal, $s, $index ) {
		$d       = $this->row_data( $deal );
		$hidden  = $index >= max( 6, (int) $s['page_size'] );
		$classes = array( 'zd-row' );
		if ( $hidden ) {
			$classes[] = 'is-paged';
		}

		$type_labels = Zlaark_Deals_Meta::offer_types();
		$type_label  = ( '' !== $deal['offer_type'] && isset( $type_labels[ $deal['offer_type'] ] ) )
			? $type_labels[ $deal['offer_type'] ]
			: '';
		?>
		<article class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>"
			data-zd-row
			data-zd-id="<?php echo esc_attr( $deal['id'] ); ?>"
			data-zd-cats="<?php echo esc_attr( $d['cats'] ); ?>"
			data-zd-type="<?php echo esc_attr( $d['type'] ); ?>"
			data-zd-saving="<?php echo esc_attr( $d['saving'] ); ?>"
			data-zd-score="<?php echo esc_attr( $d['score'] ); ?>"
			data-zd-verified="<?php echo esc_attr( $d['verified'] ); ?>"
			data-zd-ends="<?php echo esc_attr( $d['ends'] ); ?>"
			data-zd-name="<?php echo esc_attr( $d['name'] ); ?>"
			data-zd-search="<?php echo esc_attr( $d['search'] ); ?>"
			<?php echo $hidden ? 'hidden' : ''; ?>>

			<?php if ( 'yes' === $s['show_compare'] ) : ?>
				<label class="zd-row__pick">
					<input type="checkbox" data-zd-pick value="<?php echo esc_attr( $deal['id'] ); ?>" />
					<span class="screen-reader-text">
						<?php
						printf(
							/* translators: %s: deal title. */
							esc_html__( 'Add %s to comparison', 'zlaark-deals-pro' ),
							esc_html( $deal['title'] )
						);
						?>
					</span>
				</label>
			<?php endif; ?>

			<div class="zd-row__logo">
				<?php if ( $deal['image_id'] ) : ?>
					<?php echo wp_get_attachment_image( $deal['image_id'], 'thumbnail', false, array( 'loading' => 'lazy' ) ); ?>
				<?php else : ?>
					<span class="zd-row__initials" aria-hidden="true"><?php echo esc_html( mb_substr( $deal['title'], 0, 2 ) ); ?></span>
				<?php endif; ?>
			</div>

			<div class="zd-row__main">
				<h3 class="zd-row__title"><?php echo esc_html( $deal['title'] ); ?></h3>
				<p class="zd-row__meta">
					<?php if ( null !== $deal['overall_score'] ) : ?>
						<span class="zd-row__score zd-score--<?php echo esc_attr( $deal['score_band'] ); ?>">
							<?php echo esc_html( number_format_i18n( $deal['overall_score'], 1 ) ); ?>
						</span>
					<?php endif; ?>
					<?php if ( '' !== $deal['verified_label'] ) : ?>
						<span class="zd-row__verified"><?php echo esc_html( $deal['verified_label'] ); ?></span>
					<?php elseif ( '' !== $deal['tagline'] ) : ?>
						<span class="zd-row__tagline"><?php echo esc_html( $deal['tagline'] ); ?></span>
					<?php endif; ?>
					<?php if ( '' !== $deal['renewal_price'] ) : ?>
						<span class="zd-row__renewal">
							<?php
							printf(
								/* translators: %s: renewal price. */
								esc_html__( 'renews at %s', 'zlaark-deals-pro' ),
								esc_html( $deal['renewal_price'] )
							);
							?>
						</span>
					<?php endif; ?>
				</p>
			</div>

			<div class="zd-row__flags">
				<?php if ( '' !== $deal['urgency_label'] ) : ?>
					<span class="zd-chip zd-chip--ember"><?php echo esc_html( $deal['urgency_label'] ); ?></span>
				<?php elseif ( '' !== $type_label ) : ?>
					<span class="zd-chip zd-chip--neutral"><?php echo esc_html( $type_label ); ?></span>
				<?php endif; ?>
			</div>

			<div class="zd-row__end">
				<p class="zd-row__pricing">
					<?php if ( '' !== $deal['price'] ) : ?>
						<span class="zd-row__price"><?php echo esc_html( $deal['price'] ); ?></span>
					<?php elseif ( '' !== $deal['offer_headline'] ) : ?>
						<span class="zd-row__price"><?php echo esc_html( $deal['offer_headline'] ); ?></span>
					<?php endif; ?>
					<?php if ( '' !== $deal['old_price'] ) : ?>
						<s class="zd-row__old"><?php echo esc_html( $deal['old_price'] ); ?></s>
					<?php endif; ?>
				</p>
				<?php if ( null !== $deal['discount_pct'] ) : ?>
					<span class="zd-row__save">
						<?php
						printf(
							/* translators: %d: discount percentage, rounded down. */
							esc_html__( 'Save %d%%', 'zlaark-deals-pro' ),
							(int) $deal['discount_pct']
						);
						?>
					</span>
				<?php endif; ?>
				<?php $this->render_cta( $deal, $s, 'zd-btn zd-btn--solid zd-btn--sm' ); ?>
			</div>
		</article>
		<?php
	}
}
