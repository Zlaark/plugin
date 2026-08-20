<?php
/**
 * Zlaark Homepage — the whole homepage as one widget.
 *
 * Rather than assembling eight widgets by hand and keeping their settings in
 * sync, this renders the full eight-section architecture from a single drop:
 * hero, scorecard, the dark methodology band, live deals, editor's picks, the
 * logo marquee and a closing call to action.
 *
 * Every section has a toggle, and every deal-driven section reads the same
 * catalogue, so the numbers on the page can never disagree with each other.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Repeater;

class Zlaark_Homepage_Widget extends Zlaark_Query_Widget_Base {

	public function get_name() {
		return 'zlaark_homepage';
	}

	public function get_title() {
		return __( 'Zlaark Homepage', 'zlaark-deals-pro' );
	}

	public function get_icon() {
		return 'eicon-single-page';
	}

	public function get_keywords() {
		return array( 'homepage', 'home', 'template', 'landing', 'all in one' );
	}

	protected function register_controls() {

		/* ------------------------------------------------------ 01 hero */

		$this->start_controls_section(
			'sec_hero',
			array( 'label' => __( '01 · Hero', 'zlaark-deals-pro' ) )
		);

		$this->add_control( 'show_hero', $this->toggle( true ) );

		$this->add_control(
			'hero_eyebrow',
			array(
				'label'       => __( 'Eyebrow', 'zlaark-deals-pro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Bought with our own money', 'zlaark-deals-pro' ),
				'label_block' => true,
				'condition'   => array( 'show_hero' => 'yes' ),
			)
		);

		$this->add_control(
			'hero_title',
			array(
				'label'       => __( 'Headline', 'zlaark-deals-pro' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 2,
				'default'     => __( 'Save on the tools you already pay for', 'zlaark-deals-pro' ),
				'label_block' => true,
				'condition'   => array( 'show_hero' => 'yes' ),
			)
		);

		$this->add_control(
			'hero_highlight',
			array(
				'label'       => __( 'Highlight Phrase', 'zlaark-deals-pro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'already pay for', 'zlaark-deals-pro' ),
				'label_block' => true,
				'description' => __( 'Rendered in the accent colour where it appears in the headline.', 'zlaark-deals-pro' ),
				'condition'   => array( 'show_hero' => 'yes' ),
			)
		);

		$this->add_control(
			'hero_text',
			array(
				'label'       => __( 'Sub-line', 'zlaark-deals-pro' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 3,
				'default'     => __( 'Hand-tested discounts on hosting, ecommerce platforms and the software your team runs on — scored out of ten, compared, and re-checked every month.', 'zlaark-deals-pro' ),
				'condition'   => array( 'show_hero' => 'yes' ),
			)
		);

		$this->add_control(
			'hero_cta_text',
			array(
				'label'     => __( 'Button Text', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Browse all deals', 'zlaark-deals-pro' ),
				'condition' => array( 'show_hero' => 'yes' ),
			)
		);

		$this->add_control(
			'hero_cta_url',
			array(
				'label'       => __( 'Button URL', 'zlaark-deals-pro' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => '/deals/',
				'condition'   => array( 'show_hero' => 'yes' ),
			)
		);

		$this->add_control(
			'hero_board',
			array(
				'label'        => __( 'Savings Scoreboard', 'zlaark-deals-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'description'  => __( 'The four biggest current savings, computed from your deals. Replaces a stock hero image with something only you can show.', 'zlaark-deals-pro' ),
				'condition'    => array( 'show_hero' => 'yes' ),
			)
		);

		$this->end_controls_section();

		/* ------------------------------------------------ 02 scorecard */

		$this->start_controls_section(
			'sec_score',
			array( 'label' => __( '02 · Scorecard', 'zlaark-deals-pro' ) )
		);

		$this->add_control( 'show_score', $this->toggle( true ) );

		$this->add_control(
			'score_title',
			array(
				'label'       => __( 'Heading', 'zlaark-deals-pro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( "The four we'd actually pay for", 'zlaark-deals-pro' ),
				'label_block' => true,
				'condition'   => array( 'show_score' => 'yes' ),
			)
		);

		$this->add_control(
			'score_count',
			array(
				'label'     => __( 'How Many', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 2,
				'max'       => 4,
				'default'   => 4,
				'condition' => array( 'show_score' => 'yes' ),
			)
		);

		$this->add_control(
			'score_note',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => __( 'Columns are capped with each deal\'s <strong>Rank Label</strong> — set one per deal ("Best overall", "Best value").', 'zlaark-deals-pro' ),
				'content_classes' => 'elementor-descriptor',
				'condition'       => array( 'show_score' => 'yes' ),
			)
		);

		$this->end_controls_section();

		/* ---------------------------------------------- 03 methodology */

		$this->start_controls_section(
			'sec_band',
			array( 'label' => __( '03 · Methodology band', 'zlaark-deals-pro' ) )
		);

		$this->add_control( 'show_band', $this->toggle( true ) );

		$this->add_control(
			'band_eyebrow',
			array(
				'label'     => __( 'Eyebrow', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'How we work', 'zlaark-deals-pro' ),
				'condition' => array( 'show_band' => 'yes' ),
			)
		);

		$this->add_control(
			'band_title',
			array(
				'label'       => __( 'Heading', 'zlaark-deals-pro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'We buy every deal with our own money', 'zlaark-deals-pro' ),
				'label_block' => true,
				'condition'   => array( 'show_band' => 'yes' ),
			)
		);

		$this->add_control(
			'band_text',
			array(
				'label'     => __( 'Body', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::TEXTAREA,
				'rows'      => 4,
				'default'   => __( 'No vendor sends us a free account. We pay, we run a real site on it for at least thirty days, we measure speed and uptime ourselves, and we print the renewal price the vendor would rather you found out later.', 'zlaark-deals-pro' ),
				'condition' => array( 'show_band' => 'yes' ),
			)
		);

		$stats = new Repeater();
		$stats->add_control(
			'value',
			array(
				'label'   => __( 'Number', 'zlaark-deals-pro' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '102',
			)
		);
		$stats->add_control(
			'label',
			array(
				'label'   => __( 'Label', 'zlaark-deals-pro' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'deals bought and tested', 'zlaark-deals-pro' ),
			)
		);

		$this->add_control(
			'band_stats',
			array(
				'label'       => __( 'Receipts', 'zlaark-deals-pro' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $stats->get_controls(),
				'title_field' => '{{{ value }}} — {{{ label }}}',
				'condition'   => array( 'show_band' => 'yes' ),
				'default'     => array(
					array( 'value' => '102', 'label' => __( 'deals bought and tested', 'zlaark-deals-pro' ) ),
					array( 'value' => '$8,400', 'label' => __( 'spent testing this year', 'zlaark-deals-pro' ) ),
					array( 'value' => '14', 'label' => __( 'providers dropped this year', 'zlaark-deals-pro' ) ),
					array( 'value' => '30', 'label' => __( 'day minimum test period', 'zlaark-deals-pro' ) ),
				),
			)
		);

		$this->add_control(
			'band_auto',
			array(
				'label'        => __( 'Use Live Deal Count', 'zlaark-deals-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'description'  => __( 'Replaces the first number with the real count of live deals, so it can never go stale.', 'zlaark-deals-pro' ),
				'condition'    => array( 'show_band' => 'yes' ),
			)
		);

		$this->end_controls_section();

		/* -------------------------------------------------- 04 deals */

		$this->start_controls_section(
			'sec_deals',
			array( 'label' => __( '04 · Live deals', 'zlaark-deals-pro' ) )
		);

		$this->add_control( 'show_deals', $this->toggle( true ) );

		$this->add_control(
			'deals_title',
			array(
				'label'       => __( 'Heading', 'zlaark-deals-pro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Every offer, verified this month', 'zlaark-deals-pro' ),
				'label_block' => true,
				'condition'   => array( 'show_deals' => 'yes' ),
			)
		);

		$this->add_control(
			'deals_count',
			array(
				'label'     => __( 'How Many', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 3,
				'max'       => 24,
				'default'   => 12,
				'condition' => array( 'show_deals' => 'yes' ),
			)
		);

		$this->add_control(
			'deals_more_text',
			array(
				'label'     => __( 'Link Text', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'See all deals', 'zlaark-deals-pro' ),
				'condition' => array( 'show_deals' => 'yes' ),
			)
		);

		$this->add_control(
			'deals_more_url',
			array(
				'label'       => __( 'Link URL', 'zlaark-deals-pro' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => '/deals/',
				'condition'   => array( 'show_deals' => 'yes' ),
			)
		);

		$this->end_controls_section();

		/* -------------------------------------------------- 05 picks */

		$this->start_controls_section(
			'sec_picks',
			array( 'label' => __( '05 · Editor\'s picks', 'zlaark-deals-pro' ) )
		);

		$this->add_control( 'show_picks', $this->toggle( true ) );

		$this->add_control(
			'picks_title',
			array(
				'label'       => __( 'Heading', 'zlaark-deals-pro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( "Editor's picks", 'zlaark-deals-pro' ),
				'label_block' => true,
				'condition'   => array( 'show_picks' => 'yes' ),
			)
		);

		$this->end_controls_section();

		/* ------------------------------------------------ 06 marquee */

		$this->start_controls_section(
			'sec_mq',
			array( 'label' => __( '06 · Trusted by', 'zlaark-deals-pro' ) )
		);

		$this->add_control( 'show_mq', $this->toggle( true ) );

		$this->end_controls_section();

		/* ---------------------------------------------------- 07 cta */

		$this->start_controls_section(
			'sec_cta',
			array( 'label' => __( '07 · Closing CTA', 'zlaark-deals-pro' ) )
		);

		$this->add_control( 'show_cta', $this->toggle( true ) );

		$this->add_control(
			'cta_title',
			array(
				'label'     => __( 'Heading', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Deal alerts', 'zlaark-deals-pro' ),
				'condition' => array( 'show_cta' => 'yes' ),
			)
		);

		$this->add_control(
			'cta_text',
			array(
				'label'     => __( 'Body', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::TEXTAREA,
				'rows'      => 2,
				'default'   => __( 'New deals, verified monthly. No affiliate spam.', 'zlaark-deals-pro' ),
				'condition' => array( 'show_cta' => 'yes' ),
			)
		);

		$this->add_control(
			'cta_shortcode',
			array(
				'label'       => __( 'Form Shortcode', 'zlaark-deals-pro' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 2,
				'placeholder' => '[your_form_shortcode]',
				'description' => __( 'Any signup form shortcode. Leave empty to show the heading and body only.', 'zlaark-deals-pro' ),
				'condition'   => array( 'show_cta' => 'yes' ),
			)
		);

		$this->end_controls_section();

		/* -------------------------------------------------- source */

		$this->query_controls( 12 );
		$this->max_width_control( '{{WRAPPER}} .zd-home__inner' );
		$this->animation_controls( false );
	}

	private function toggle( $on ) {
		return array(
			'label'        => __( 'Show This Section', 'zlaark-deals-pro' ),
			'type'         => Controls_Manager::SWITCHER,
			'default'      => $on ? 'yes' : '',
			'return_value' => 'yes',
		);
	}

	/** One query feeds every section, so no two blocks can disagree. */
	private function fetch( $s ) {
		$args                   = $this->build_query_args( $s );
		$args['posts_per_page'] = 60;

		$query = new WP_Query( $args );
		$deals = array();

		while ( $query->have_posts() ) {
			$query->the_post();
			$deal = Zlaark_Deals_Meta::get_deal_data( get_post() );
			if ( ! empty( $deal ) ) {
				$deals[] = $deal;
			}
		}
		wp_reset_postdata();

		return $deals;
	}

	protected function render() {
		$s     = $this->get_settings_for_display();
		$deals = $this->fetch( $s );

		if ( empty( $deals ) ) {
			$this->render_empty_notice();
			return;
		}

		// Biggest saving first — the ordering the whole page argues from.
		$by_saving = $deals;
		usort(
			$by_saving,
			function ( $a, $b ) {
				$x = ( null === $a['discount_pct'] ) ? -1 : (int) $a['discount_pct'];
				$y = ( null === $b['discount_pct'] ) ? -1 : (int) $b['discount_pct'];
				return $y - $x;
			}
		);

		$by_score = $deals;
		usort(
			$by_score,
			function ( $a, $b ) {
				$x = ( null === $a['overall_score'] ) ? -1 : (float) $a['overall_score'];
				$y = ( null === $b['overall_score'] ) ? -1 : (float) $b['overall_score'];
				return ( $y > $x ) ? 1 : ( ( $y < $x ) ? -1 : 0 );
			}
		);
		?>
		<div class="zd-home" data-zd-reveal-root="true" data-zd-stagger="60">
			<?php
			if ( 'yes' === $s['show_hero'] ) {
				$this->section_hero( $s, $by_saving );
			}
			if ( 'yes' === $s['show_score'] ) {
				$this->section_scorecard( $s, $by_score );
			}
			if ( 'yes' === $s['show_band'] ) {
				$this->section_band( $s, $deals );
			}
			if ( 'yes' === $s['show_deals'] ) {
				$this->section_deals( $s, $by_saving );
			}
			if ( 'yes' === $s['show_picks'] ) {
				$this->section_picks( $s, $by_score );
			}
			if ( 'yes' === $s['show_mq'] ) {
				$this->section_marquee( $deals );
			}
			if ( 'yes' === $s['show_cta'] ) {
				$this->section_cta( $s );
			}
			?>
		</div>
		<?php
	}

	/** Wraps the highlight phrase in an accent span, once. */
	private function highlight( $title, $phrase ) {
		$title = esc_html( $title );
		if ( '' === trim( (string) $phrase ) ) {
			return $title;
		}
		$phrase = esc_html( $phrase );
		$pos    = strpos( $title, $phrase );
		if ( false === $pos ) {
			return $title;
		}
		return substr( $title, 0, $pos )
			. '<em class="zd-home__hl">' . $phrase . '</em>'
			. substr( $title, $pos + strlen( $phrase ) );
	}

	/* ---------------------------------------------------- 01 hero */

	private function section_hero( $s, $deals ) {
		$url = ! empty( $s['hero_cta_url']['url'] ) ? $s['hero_cta_url']['url'] : '';
		$top = array_slice(
			array_values(
				array_filter(
					$deals,
					function ( $d ) {
						return null !== $d['discount_pct'];
					}
				)
			),
			0,
			4
		);
		?>
		<section class="zd-home__sec zd-home__hero">
			<div class="zd-home__inner zd-home__herogrid">
				<div class="zd-reveal" data-zd-reveal="rise">
					<?php if ( '' !== $s['hero_eyebrow'] ) : ?>
						<span class="zd-chip zd-chip--brand"><?php echo esc_html( $s['hero_eyebrow'] ); ?></span>
					<?php endif; ?>
					<h1 class="zd-home__h1">
						<?php echo wp_kses_post( $this->highlight( $s['hero_title'], $s['hero_highlight'] ) ); ?>
					</h1>
					<?php if ( '' !== $s['hero_text'] ) : ?>
						<p class="zd-home__lede"><?php echo esc_html( $s['hero_text'] ); ?></p>
					<?php endif; ?>
					<?php if ( '' !== $s['hero_cta_text'] && '' !== $url ) : ?>
						<a class="zd-btn zd-btn--solid zd-home__herocta" href="<?php echo esc_url( $url ); ?>">
							<span class="zd-btn__label"><?php echo esc_html( $s['hero_cta_text'] ); ?></span>
						</a>
					<?php endif; ?>
				</div>

				<?php if ( 'yes' === $s['hero_board'] && ! empty( $top ) ) : ?>
					<div class="zd-home__board zd-reveal" data-zd-reveal="rise">
						<div class="zd-home__boardhead">
							<b><?php esc_html_e( 'Biggest savings right now', 'zlaark-deals-pro' ); ?></b>
							<span class="zd-home__live"><?php esc_html_e( 'live', 'zlaark-deals-pro' ); ?></span>
						</div>
						<?php foreach ( $top as $deal ) : ?>
							<div class="zd-home__boardrow">
								<span class="zd-home__boardlogo">
									<?php if ( $deal['image_id'] ) : ?>
										<?php echo wp_get_attachment_image( $deal['image_id'], 'thumbnail', false, array( 'loading' => 'lazy' ) ); ?>
									<?php else : ?>
										<?php echo esc_html( mb_substr( $deal['title'], 0, 2 ) ); ?>
									<?php endif; ?>
								</span>
								<span class="zd-home__boardname">
									<b><?php echo esc_html( $deal['title'] ); ?></b>
									<?php if ( '' !== $deal['old_price'] && '' !== $deal['price'] ) : ?>
										<span><?php echo esc_html( $deal['old_price'] . ' → ' . $deal['price'] ); ?></span>
									<?php endif; ?>
								</span>
								<span class="zd-home__boardpc">−<?php echo esc_html( (int) $deal['discount_pct'] ); ?>%</span>
							</div>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>
		</section>
		<?php
	}

	/* ----------------------------------------------- 02 scorecard */

	private function section_scorecard( $s, $deals ) {
		$picks = array_slice( $deals, 0, max( 2, (int) $s['score_count'] ) );
		?>
		<section class="zd-home__sec">
			<div class="zd-home__inner">
				<?php $this->section_head( $s['score_title'], __( 'Best right now', 'zlaark-deals-pro' ) ); ?>
				<div class="zd-home__cards zd-home__cards--4">
					<?php foreach ( $picks as $i => $deal ) : ?>
						<article class="zd-home__sc zd-reveal" data-zd-reveal="rise" style="--zd-i:<?php echo (int) $i; ?>">
							<?php if ( '' !== $deal['rank_label'] ) : ?>
								<span class="zd-compare__cap<?php echo 0 === $i ? ' zd-compare__cap--first' : ''; ?>">
									<?php echo esc_html( $deal['rank_label'] ); ?>
								</span>
							<?php endif; ?>
							<div class="zd-home__sctop">
								<span class="zd-home__sclogo">
									<?php if ( $deal['image_id'] ) : ?>
										<?php echo wp_get_attachment_image( $deal['image_id'], 'thumbnail', false, array( 'loading' => 'lazy' ) ); ?>
									<?php else : ?>
										<?php echo esc_html( mb_substr( $deal['title'], 0, 2 ) ); ?>
									<?php endif; ?>
								</span>
								<div>
									<h3><?php echo esc_html( $deal['title'] ); ?></h3>
									<?php if ( null !== $deal['overall_score'] ) : ?>
										<p class="zd-home__scov">
											<b class="zd-score--<?php echo esc_attr( $deal['score_band'] ); ?>">
												<?php echo esc_html( number_format_i18n( $deal['overall_score'], 1 ) ); ?>
											</b>
											<span><?php esc_html_e( 'overall', 'zlaark-deals-pro' ); ?></span>
										</p>
									<?php endif; ?>
								</div>
							</div>

							<?php if ( ! empty( $deal['scores'] ) ) : ?>
								<div class="zd-home__bars">
									<?php foreach ( array_slice( $deal['scores'], 0, 4 ) as $bi => $row ) : ?>
										<?php
										if ( null === $row['value'] ) {
											continue;
										}
										$band = Zlaark_Deals_Computed::score_band( $row['value'] );
										?>
										<div class="zd-home__bar" style="--zd-i:<?php echo (int) $bi; ?>">
											<span><?php echo esc_html( $row['label'] ); ?></span>
											<span class="zd-home__bartrack">
												<i class="zd-home__barfill zd-fill--<?php echo esc_attr( $band ); ?>"
													data-zd-bar="<?php echo esc_attr( $row['value'] * 10 ); ?>"></i>
											</span>
											<b class="zd-score--<?php echo esc_attr( $band ); ?>">
												<?php echo esc_html( number_format_i18n( $row['value'], 1 ) ); ?>
											</b>
										</div>
									<?php endforeach; ?>
								</div>
							<?php endif; ?>

							<div class="zd-home__scfoot">
								<p class="zd-home__price">
									<?php if ( '' !== $deal['price'] ) : ?>
										<span class="zd-home__pricenow"><?php echo esc_html( $deal['price'] ); ?></span>
									<?php elseif ( '' !== $deal['offer_headline'] ) : ?>
										<span class="zd-home__pricenow"><?php echo esc_html( $deal['offer_headline'] ); ?></span>
									<?php endif; ?>
									<?php if ( '' !== $deal['old_price'] ) : ?>
										<s><?php echo esc_html( $deal['old_price'] ); ?></s>
									<?php endif; ?>
									<?php if ( null !== $deal['discount_pct'] ) : ?>
										<em class="zd-home__save">
											<?php
											printf(
												/* translators: %d: discount percentage. */
												esc_html__( 'Save %d%%', 'zlaark-deals-pro' ),
												(int) $deal['discount_pct']
											);
											?>
										</em>
									<?php endif; ?>
								</p>
								<?php $this->render_cta( $deal, $s, 'zd-btn zd-btn--solid zd-btn--sm zd-home__block' ); ?>
							</div>
						</article>
					<?php endforeach; ?>
				</div>
			</div>
		</section>
		<?php
	}

	/* --------------------------------------------- 03 methodology */

	private function section_band( $s, $deals ) {
		$stats = is_array( $s['band_stats'] ) ? $s['band_stats'] : array();

		// The first figure comes from the live catalogue, so it cannot go stale.
		if ( 'yes' === $s['band_auto'] && ! empty( $stats ) ) {
			$stats[0]['value'] = number_format_i18n( count( $deals ) );
		}
		?>
		<section class="zd-home__sec zd-home__band">
			<div class="zd-home__inner">
				<?php if ( '' !== $s['band_eyebrow'] ) : ?>
					<p class="zd-home__eyebrow zd-reveal" data-zd-reveal="rise"><?php echo esc_html( $s['band_eyebrow'] ); ?></p>
				<?php endif; ?>
				<h2 class="zd-home__bandtitle zd-reveal" data-zd-reveal="rise"><?php echo esc_html( $s['band_title'] ); ?></h2>
				<?php if ( '' !== $s['band_text'] ) : ?>
					<p class="zd-home__bandtext zd-reveal" data-zd-reveal="rise"><?php echo esc_html( $s['band_text'] ); ?></p>
				<?php endif; ?>

				<?php if ( ! empty( $stats ) ) : ?>
					<div class="zd-home__receipts">
						<?php foreach ( $stats as $i => $row ) : ?>
							<div class="zd-reveal" data-zd-reveal="rise" style="--zd-i:<?php echo (int) $i; ?>">
								<b><?php echo esc_html( $row['value'] ); ?></b>
								<span><?php echo esc_html( $row['label'] ); ?></span>
							</div>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>
		</section>
		<?php
	}

	/* --------------------------------------------------- 04 deals */

	private function section_deals( $s, $deals ) {
		$show = array_slice( $deals, 0, max( 3, (int) $s['deals_count'] ) );
		$url  = ! empty( $s['deals_more_url']['url'] ) ? $s['deals_more_url']['url'] : '';
		?>
		<section class="zd-home__sec zd-home__tint">
			<div class="zd-home__inner">
				<?php $this->section_head( $s['deals_title'], __( 'Live deals', 'zlaark-deals-pro' ) ); ?>
				<div class="zd-home__cards zd-home__cards--3">
					<?php foreach ( $show as $i => $deal ) : ?>
						<?php $this->mini_card( $deal, $s, $i ); ?>
					<?php endforeach; ?>
				</div>
				<?php if ( '' !== $s['deals_more_text'] && '' !== $url ) : ?>
					<p class="zd-home__more">
						<a class="zd-btn zd-btn--ghost" href="<?php echo esc_url( $url ); ?>">
							<span class="zd-btn__label"><?php echo esc_html( $s['deals_more_text'] ); ?></span>
						</a>
					</p>
				<?php endif; ?>
			</div>
		</section>
		<?php
	}

	/* --------------------------------------------------- 05 picks */

	private function section_picks( $s, $deals ) {
		$picks = array_slice( $deals, 0, 3 );
		?>
		<section class="zd-home__sec">
			<div class="zd-home__inner">
				<?php $this->section_head( $s['picks_title'], __( 'Ranked', 'zlaark-deals-pro' ) ); ?>
				<div class="zd-home__cards zd-home__cards--3">
					<?php foreach ( $picks as $i => $deal ) : ?>
						<article class="zd-home__pick zd-reveal" data-zd-reveal="rise" style="--zd-i:<?php echo (int) $i; ?>">
							<span class="zd-home__medal"><?php echo esc_html( $i + 1 ); ?></span>
							<h3><?php echo esc_html( $deal['title'] ); ?></h3>
							<?php if ( null !== $deal['overall_score'] ) : ?>
								<p class="zd-home__scov">
									<b class="zd-score--<?php echo esc_attr( $deal['score_band'] ); ?>">
										<?php echo esc_html( number_format_i18n( $deal['overall_score'], 1 ) ); ?>
									</b>
									<span><?php esc_html_e( 'out of 10', 'zlaark-deals-pro' ); ?></span>
								</p>
							<?php endif; ?>
							<?php if ( ! empty( $deal['highlights'] ) ) : ?>
								<ul class="zd-home__ticks">
									<?php foreach ( array_slice( $deal['highlights'], 0, 4 ) as $line ) : ?>
										<li><?php echo esc_html( $line ); ?></li>
									<?php endforeach; ?>
								</ul>
							<?php endif; ?>
							<?php $this->render_cta( $deal, $s, 'zd-btn zd-btn--solid zd-btn--sm zd-home__block' ); ?>
						</article>
					<?php endforeach; ?>
				</div>
			</div>
		</section>
		<?php
	}

	/* ------------------------------------------------- 06 marquee */

	private function section_marquee( $deals ) {
		$logos = array_values(
			array_filter(
				$deals,
				function ( $d ) {
					return ! empty( $d['image_id'] );
				}
			)
		);

		if ( count( $logos ) < 3 ) {
			return;
		}
		?>
		<section class="zd-home__sec zd-home__mq zd-marquee zd-marquee--fade zd-marquee--gray zd-marquee--pause"
			style="--zd-marquee-speed:34s;">
			<div class="zd-marquee__viewport">
				<div class="zd-marquee__track">
					<?php foreach ( $logos as $deal ) : ?>
						<span class="zd-marquee__item">
							<?php echo wp_get_attachment_image( $deal['image_id'], 'medium', false, array( 'loading' => 'lazy' ) ); ?>
						</span>
					<?php endforeach; ?>
				</div>
			</div>
		</section>
		<?php
	}

	/* ----------------------------------------------------- 07 cta */

	private function section_cta( $s ) {
		?>
		<section class="zd-home__sec">
			<div class="zd-home__inner">
				<div class="zd-home__cta zd-reveal" data-zd-reveal="rise">
					<div>
						<h2><?php echo esc_html( $s['cta_title'] ); ?></h2>
						<?php if ( '' !== $s['cta_text'] ) : ?>
							<p><?php echo esc_html( $s['cta_text'] ); ?></p>
						<?php endif; ?>
					</div>
					<?php if ( '' !== trim( (string) $s['cta_shortcode'] ) ) : ?>
						<div class="zd-home__ctaform">
							<?php echo do_shortcode( wp_kses_post( $s['cta_shortcode'] ) ); ?>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</section>
		<?php
	}

	/* -------------------------------------------------- fragments */

	private function section_head( $title, $eyebrow ) {
		if ( '' === trim( (string) $title ) ) {
			return;
		}
		?>
		<div class="zd-home__head zd-reveal" data-zd-reveal="rise">
			<?php if ( '' !== $eyebrow ) : ?>
				<p class="zd-home__eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
			<?php endif; ?>
			<h2><?php echo esc_html( $title ); ?></h2>
		</div>
		<?php
	}

	private function mini_card( $deal, $s, $index ) {
		$type_labels = Zlaark_Deals_Meta::offer_types();
		$type_label  = ( '' !== $deal['offer_type'] && isset( $type_labels[ $deal['offer_type'] ] ) )
			? $type_labels[ $deal['offer_type'] ]
			: '';
		?>
		<article class="zd-home__dc zd-reveal" data-zd-reveal="rise" style="--zd-i:<?php echo (int) $index; ?>">
			<div class="zd-home__dchead">
				<span class="zd-home__sclogo">
					<?php if ( $deal['image_id'] ) : ?>
						<?php echo wp_get_attachment_image( $deal['image_id'], 'thumbnail', false, array( 'loading' => 'lazy' ) ); ?>
					<?php else : ?>
						<?php echo esc_html( mb_substr( $deal['title'], 0, 2 ) ); ?>
					<?php endif; ?>
				</span>
				<?php if ( '' !== $deal['urgency_label'] ) : ?>
					<span class="zd-chip zd-chip--ember"><?php echo esc_html( $deal['urgency_label'] ); ?></span>
				<?php elseif ( '' !== $type_label ) : ?>
					<span class="zd-chip zd-chip--neutral"><?php echo esc_html( $type_label ); ?></span>
				<?php endif; ?>
			</div>

			<h3><?php echo esc_html( $deal['title'] ); ?></h3>

			<?php if ( null !== $deal['overall_score'] || '' !== $deal['tested_date'] ) : ?>
				<p class="zd-home__dcmeta">
					<?php if ( null !== $deal['overall_score'] ) : ?>
						<b class="zd-score--<?php echo esc_attr( $deal['score_band'] ); ?>">
							<?php echo esc_html( number_format_i18n( $deal['overall_score'], 1 ) ); ?>
						</b>
					<?php endif; ?>
					<?php if ( '' !== $deal['tested_date'] ) : ?>
						<span>
							<?php
							printf(
								/* translators: %s: month and year tested. */
								esc_html__( 'Tested %s', 'zlaark-deals-pro' ),
								esc_html( date_i18n( 'M Y', strtotime( $deal['tested_date'] ) ) )
							);
							?>
						</span>
					<?php endif; ?>
				</p>
			<?php endif; ?>

			<p class="zd-home__price">
				<?php if ( '' !== $deal['price'] ) : ?>
					<span class="zd-home__pricenow"><?php echo esc_html( $deal['price'] ); ?></span>
				<?php elseif ( '' !== $deal['offer_headline'] ) : ?>
					<span class="zd-home__pricenow"><?php echo esc_html( $deal['offer_headline'] ); ?></span>
				<?php endif; ?>
				<?php if ( '' !== $deal['old_price'] ) : ?>
					<s><?php echo esc_html( $deal['old_price'] ); ?></s>
				<?php endif; ?>
				<?php if ( null !== $deal['discount_pct'] ) : ?>
					<em class="zd-home__save">
						<?php
						printf(
							/* translators: %d: discount percentage. */
							esc_html__( 'Save %d%%', 'zlaark-deals-pro' ),
							(int) $deal['discount_pct']
						);
						?>
					</em>
				<?php endif; ?>
			</p>

			<?php if ( '' !== $deal['coupon_code'] ) : ?>
				<div class="zd-coupon" data-zd-coupon="<?php echo esc_attr( $deal['coupon_code'] ); ?>">
					<span class="zd-coupon__code"><?php echo esc_html( $deal['coupon_code'] ); ?></span>
					<button type="button" class="zd-coupon__copy"
						data-zd-copied="<?php esc_attr_e( 'Copied', 'zlaark-deals-pro' ); ?>">
						<?php esc_html_e( 'Copy code', 'zlaark-deals-pro' ); ?>
					</button>
				</div>
			<?php endif; ?>

			<?php if ( '' !== $deal['renewal_price'] || '' !== $deal['verified_label'] ) : ?>
				<div class="zd-home__dcterms">
					<?php if ( '' !== $deal['renewal_price'] ) : ?>
						<span>
							<?php
							printf(
								/* translators: %s: renewal price. */
								esc_html__( 'Renews at %s', 'zlaark-deals-pro' ),
								esc_html( $deal['renewal_price'] )
							);
							?>
						</span>
					<?php endif; ?>
					<?php if ( '' !== $deal['verified_label'] ) : ?>
						<span class="zd-card__verified"><?php echo esc_html( $deal['verified_label'] ); ?></span>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<?php $this->render_cta( $deal, $s, 'zd-btn zd-btn--solid zd-home__block' ); ?>
		</article>
		<?php
	}
}
