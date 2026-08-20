<?php
/**
 * Zlaark Footer - the site footer.
 *
 * Built to close the page the way the methodology band opens it: a dark ground,
 * four labelled columns, and the affiliate disclosure as a titled block rather
 * than a sentence buried in an about-us paragraph.
 *
 * The figures can read from the live catalogue, so the footer cannot go stale
 * the way a hand-typed "100+ deals" always eventually does.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Repeater;

class Zlaark_Footer_Widget extends Zlaark_Widget_Base {

	public function get_name() {
		return 'zlaark_footer';
	}

	public function get_title() {
		return __( 'Zlaark Footer', 'zlaark-deals-pro' );
	}

	public function get_icon() {
		return 'eicon-footer';
	}

	public function get_keywords() {
		return array( 'footer', 'columns', 'disclosure', 'legal' );
	}

	/** A heading + link list, used for each of the three link columns. */
	private function column_controls( $n, $default_heading, $default_links ) {
		$this->add_control(
			"col{$n}_heading",
			array(
				'label'       => __( 'Heading', 'zlaark-deals-pro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => $default_heading,
				'label_block' => true,
			)
		);

		$links = new Repeater();
		$links->add_control(
			'label',
			array(
				'label'   => __( 'Label', 'zlaark-deals-pro' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Link', 'zlaark-deals-pro' ),
			)
		);
		$links->add_control(
			'url',
			array(
				'label'       => __( 'URL', 'zlaark-deals-pro' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => '/deals/',
			)
		);

		$this->add_control(
			"col{$n}_links",
			array(
				'label'       => __( 'Links', 'zlaark-deals-pro' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $links->get_controls(),
				'title_field' => '{{{ label }}}',
				'default'     => array_map(
					function ( $l ) {
						return array( 'label' => $l, 'url' => array( 'url' => '' ) );
					},
					$default_links
				),
			)
		);
	}

	protected function register_controls() {

		/* ---------------------------------------------------------- brand */

		$this->start_controls_section(
			'sec_brand',
			array( 'label' => __( 'Brand', 'zlaark-deals-pro' ) )
		);

		$this->add_control(
			'logo_type',
			array(
				'label'   => __( 'Logo', 'zlaark-deals-pro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'text',
				'options' => array(
					'text'  => __( 'Wordmark', 'zlaark-deals-pro' ),
					'image' => __( 'Image', 'zlaark-deals-pro' ),
					'none'  => __( 'None', 'zlaark-deals-pro' ),
				),
			)
		);

		$this->add_control(
			'logo_text',
			array(
				'label'     => __( 'Wordmark', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => 'blogyouneed',
				'condition' => array( 'logo_type' => 'text' ),
			)
		);

		$this->add_control(
			'logo_accent',
			array(
				'label'       => __( 'Accented Part', 'zlaark-deals-pro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 'need',
				'description' => __( 'The tail of the wordmark, rendered in the accent colour.', 'zlaark-deals-pro' ),
				'condition'   => array( 'logo_type' => 'text' ),
			)
		);

		$this->add_control(
			'logo_image',
			array(
				'label'     => __( 'Logo Image', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::MEDIA,
				'condition' => array( 'logo_type' => 'image' ),
			)
		);

		$this->add_control(
			'blurb',
			array(
				'label'   => __( 'Blurb', 'zlaark-deals-pro' ),
				'type'    => Controls_Manager::TEXTAREA,
				'rows'    => 3,
				'default' => __( 'We buy every deal with our own money, test it, score it out of ten, and print the renewal price nobody else prints.', 'zlaark-deals-pro' ),
			)
		);

		$figs = new Repeater();
		$figs->add_control(
			'value',
			array( 'label' => __( 'Figure', 'zlaark-deals-pro' ), 'type' => Controls_Manager::TEXT, 'default' => '102' )
		);
		$figs->add_control(
			'label',
			array( 'label' => __( 'Label', 'zlaark-deals-pro' ), 'type' => Controls_Manager::TEXT, 'default' => __( 'deals live', 'zlaark-deals-pro' ) )
		);

		$this->add_control(
			'figures',
			array(
				'label'       => __( 'Figures', 'zlaark-deals-pro' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $figs->get_controls(),
				'title_field' => '{{{ value }}} - {{{ label }}}',
				'default'     => array(
					array( 'value' => '102', 'label' => __( 'deals live', 'zlaark-deals-pro' ) ),
					array( 'value' => '$8,400', 'label' => __( 'spent testing', 'zlaark-deals-pro' ) ),
					array( 'value' => '2010', 'label' => __( 'testing since', 'zlaark-deals-pro' ) ),
				),
			)
		);

		$this->add_control(
			'figures_auto',
			array(
				'label'        => __( 'Use Live Deal Count', 'zlaark-deals-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'description'  => __( 'Replaces the first figure with the real number of published deals, so it can never go stale.', 'zlaark-deals-pro' ),
			)
		);

		$this->end_controls_section();

		/* -------------------------------------------------------- columns */

		$this->start_controls_section(
			'sec_col1',
			array( 'label' => __( 'Column 1', 'zlaark-deals-pro' ) )
		);
		$this->column_controls(
			1,
			__( 'Deals', 'zlaark-deals-pro' ),
			array( __( 'All deals', 'zlaark-deals-pro' ), __( 'Web hosting', 'zlaark-deals-pro' ), __( 'Ecommerce', 'zlaark-deals-pro' ), __( 'Expiring this week', 'zlaark-deals-pro' ) )
		);
		$this->end_controls_section();

		$this->start_controls_section(
			'sec_col2',
			array( 'label' => __( 'Column 2', 'zlaark-deals-pro' ) )
		);
		$this->column_controls(
			2,
			__( 'Research', 'zlaark-deals-pro' ),
			array( __( 'Reviews', 'zlaark-deals-pro' ), __( 'Comparisons', 'zlaark-deals-pro' ), __( 'Guides', 'zlaark-deals-pro' ), __( 'Blog', 'zlaark-deals-pro' ) )
		);
		$this->end_controls_section();

		$this->start_controls_section(
			'sec_col3',
			array( 'label' => __( 'Column 3', 'zlaark-deals-pro' ) )
		);
		$this->column_controls(
			3,
			__( 'Company', 'zlaark-deals-pro' ),
			array( __( 'Our testing method', 'zlaark-deals-pro' ), __( 'How we make money', 'zlaark-deals-pro' ), __( 'Who we are', 'zlaark-deals-pro' ), __( 'Contact', 'zlaark-deals-pro' ) )
		);
		$this->end_controls_section();

		/* ----------------------------------------------------- disclosure */

		$this->start_controls_section(
			'sec_money',
			array( 'label' => __( 'Disclosure', 'zlaark-deals-pro' ) )
		);

		$this->add_control(
			'show_money',
			array(
				'label'        => __( 'Show Disclosure Block', 'zlaark-deals-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'description'  => __( 'Stating the affiliate relationship plainly reads as confidence, and covers you legally. Burying it in an about-us paragraph does neither.', 'zlaark-deals-pro' ),
			)
		);

		$this->add_control(
			'money_title',
			array(
				'label'     => __( 'Heading', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'How we make money', 'zlaark-deals-pro' ),
				'condition' => array( 'show_money' => 'yes' ),
			)
		);

		$this->add_control(
			'money_text',
			array(
				'label'     => __( 'Text', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::TEXTAREA,
				'rows'      => 3,
				'default'   => __( 'Some links here are affiliate links, and we earn a commission if you buy through them, at no extra cost to you. It never changes a score. We buy every product we test.', 'zlaark-deals-pro' ),
				'condition' => array( 'show_money' => 'yes' ),
			)
		);

		$this->add_control(
			'money_link_text',
			array(
				'label'     => __( 'Link Text', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Read the full policy', 'zlaark-deals-pro' ),
				'condition' => array( 'show_money' => 'yes' ),
			)
		);

		$this->add_control(
			'money_link_url',
			array(
				'label'     => __( 'Link URL', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::URL,
				'condition' => array( 'show_money' => 'yes' ),
			)
		);

		$this->add_control(
			'score_title',
			array(
				'label'     => __( 'Second Heading', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'How we score', 'zlaark-deals-pro' ),
				'separator' => 'before',
				'condition' => array( 'show_money' => 'yes' ),
			)
		);

		$this->add_control(
			'score_text',
			array(
				'label'     => __( 'Second Text', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::TEXTAREA,
				'rows'      => 3,
				'default'   => __( 'Every deal is rated out of ten across measured attributes, re-checked monthly, and dropped the moment the offer lapses.', 'zlaark-deals-pro' ),
				'condition' => array( 'show_money' => 'yes' ),
			)
		);

		$this->add_control(
			'score_link_text',
			array(
				'label'     => __( 'Second Link Text', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'See the method', 'zlaark-deals-pro' ),
				'condition' => array( 'show_money' => 'yes' ),
			)
		);

		$this->add_control(
			'score_link_url',
			array(
				'label'     => __( 'Second Link URL', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::URL,
				'condition' => array( 'show_money' => 'yes' ),
			)
		);

		$this->end_controls_section();

		/* ---------------------------------------------------------- legal */

		$this->start_controls_section(
			'sec_legal',
			array( 'label' => __( 'Legal strip', 'zlaark-deals-pro' ) )
		);

		$this->add_control(
			'copyright',
			array(
				'label'       => __( 'Copyright', 'zlaark-deals-pro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '© {year} BlogYouNeed',
				'label_block' => true,
				'description' => __( 'Use {year} for the current year so it never needs editing.', 'zlaark-deals-pro' ),
			)
		);

		$legal = new Repeater();
		$legal->add_control(
			'label',
			array( 'label' => __( 'Label', 'zlaark-deals-pro' ), 'type' => Controls_Manager::TEXT, 'default' => __( 'Privacy', 'zlaark-deals-pro' ) )
		);
		$legal->add_control(
			'url',
			array( 'label' => __( 'URL', 'zlaark-deals-pro' ), 'type' => Controls_Manager::URL )
		);

		$this->add_control(
			'legal_links',
			array(
				'label'       => __( 'Legal Links', 'zlaark-deals-pro' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $legal->get_controls(),
				'title_field' => '{{{ label }}}',
				'default'     => array(
					array( 'label' => __( 'Privacy', 'zlaark-deals-pro' ), 'url' => array( 'url' => '' ) ),
					array( 'label' => __( 'Cookies', 'zlaark-deals-pro' ), 'url' => array( 'url' => '' ) ),
					array( 'label' => __( 'Terms', 'zlaark-deals-pro' ), 'url' => array( 'url' => '' ) ),
					array( 'label' => __( 'Sitemap', 'zlaark-deals-pro' ), 'url' => array( 'url' => '' ) ),
				),
			)
		);

		$this->add_control(
			'show_sweep',
			array(
				'label'        => __( 'Show Last Sweep Date', 'zlaark-deals-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'description'  => __( 'Reads the most recently verified deal, so the freshness signal repeats on every page and stays true.', 'zlaark-deals-pro' ),
			)
		);

		$this->end_controls_section();

		/* ---------------------------------------------------------- style */

		$this->start_controls_section(
			'sec_style',
			array(
				'label' => __( 'Style', 'zlaark-deals-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'bg',
			array(
				'label'     => __( 'Background', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#0a1310',
				'selectors' => array( '{{WRAPPER}} .zd-footer' => '--zd-footer-bg: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'ink',
			array(
				'label'     => __( 'Text', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#bdccc4',
				'selectors' => array( '{{WRAPPER}} .zd-footer' => '--zd-footer-ink: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'heading_ink',
			array(
				'label'     => __( 'Headings', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#e8efea',
				'selectors' => array( '{{WRAPPER}} .zd-footer' => '--zd-footer-heading: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'accent',
			array(
				'label'     => __( 'Accent', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#5fd9a4',
				'selectors' => array( '{{WRAPPER}} .zd-footer' => '--zd-footer-accent: {{VALUE}};' ),
			)
		);

		$this->max_width_control( '{{WRAPPER}} .zd-footer__inner' );

		$this->end_controls_section();
	}

	/** Published deals, for the auto figure. */
	private function live_count() {
		$counts = wp_count_posts( ZLAARK_DEALS_CPT );
		return isset( $counts->publish ) ? (int) $counts->publish : 0;
	}

	/** The most recent _zlaark_last_verified date across the catalogue. */
	private function last_sweep() {
		$rows = get_posts(
			array(
				'post_type'              => ZLAARK_DEALS_CPT,
				'post_status'            => 'publish',
				'posts_per_page'         => 1,
				'meta_key'               => '_zlaark_last_verified', // phpcs:ignore WordPress.DB.SlowDBQuery
				'orderby'                => 'meta_value',
				'order'                  => 'DESC',
				'fields'                 => 'ids',
				'suppress_filters'       => true,
				'no_found_rows'          => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
			)
		);

		if ( empty( $rows ) ) {
			return '';
		}

		$date = get_post_meta( $rows[0], '_zlaark_last_verified', true );
		if ( ! $date ) {
			return '';
		}

		$ts = strtotime( $date );
		return $ts ? date_i18n( get_option( 'date_format' ), $ts ) : '';
	}

	private function render_column( $n, $s ) {
		$heading = isset( $s[ "col{$n}_heading" ] ) ? $s[ "col{$n}_heading" ] : '';
		$links   = isset( $s[ "col{$n}_links" ] ) && is_array( $s[ "col{$n}_links" ] ) ? $s[ "col{$n}_links" ] : array();

		if ( '' === $heading && empty( $links ) ) {
			return;
		}
		?>
		<nav class="zd-footer__col" aria-label="<?php echo esc_attr( $heading ); ?>">
			<?php if ( '' !== $heading ) : ?>
				<h2 class="zd-footer__colhead"><?php echo esc_html( $heading ); ?></h2>
			<?php endif; ?>
			<?php if ( ! empty( $links ) ) : ?>
				<ul>
					<?php foreach ( $links as $link ) : ?>
						<?php $url = ! empty( $link['url']['url'] ) ? $link['url']['url'] : '#'; ?>
						<li>
							<a href="<?php echo esc_url( $url ); ?>"
								<?php echo ! empty( $link['url']['is_external'] ) ? 'target="_blank" rel="noopener"' : ''; ?>>
								<?php echo esc_html( $link['label'] ); ?>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</nav>
		<?php
	}

	protected function render() {
		$s = $this->get_settings_for_display();

		$figures = is_array( $s['figures'] ) ? $s['figures'] : array();
		if ( 'yes' === $s['figures_auto'] && ! empty( $figures ) ) {
			$figures[0]['value'] = number_format_i18n( $this->live_count() );
		}

		$copyright = str_replace( '{year}', date_i18n( 'Y' ), (string) $s['copyright'] );
		$sweep     = ( 'yes' === $s['show_sweep'] ) ? $this->last_sweep() : '';
		?>
		<footer class="zd-footer">
			<div class="zd-footer__inner">

				<div class="zd-footer__top">
					<div class="zd-footer__brand">
						<?php if ( 'image' === $s['logo_type'] && ! empty( $s['logo_image']['url'] ) ) : ?>
							<img class="zd-footer__logo" src="<?php echo esc_url( $s['logo_image']['url'] ); ?>"
								alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" />
						<?php elseif ( 'text' === $s['logo_type'] && '' !== $s['logo_text'] ) : ?>
							<p class="zd-footer__word">
								<?php
								$word   = $s['logo_text'];
								$accent = (string) $s['logo_accent'];
								if ( '' !== $accent && substr( $word, -strlen( $accent ) ) === $accent ) {
									echo esc_html( substr( $word, 0, -strlen( $accent ) ) );
									echo '<span>' . esc_html( $accent ) . '</span>';
								} else {
									echo esc_html( $word );
								}
								?>
							</p>
						<?php endif; ?>

						<?php if ( '' !== $s['blurb'] ) : ?>
							<p class="zd-footer__blurb"><?php echo esc_html( $s['blurb'] ); ?></p>
						<?php endif; ?>

						<?php if ( ! empty( $figures ) ) : ?>
							<div class="zd-footer__figs">
								<?php foreach ( $figures as $fig ) : ?>
									<div>
										<b><?php echo esc_html( $fig['value'] ); ?></b>
										<span><?php echo esc_html( $fig['label'] ); ?></span>
									</div>
								<?php endforeach; ?>
							</div>
						<?php endif; ?>
					</div>

					<?php
					$this->render_column( 1, $s );
					$this->render_column( 2, $s );
					$this->render_column( 3, $s );
					?>
				</div>

				<?php if ( 'yes' === $s['show_money'] ) : ?>
					<div class="zd-footer__money">
						<div>
							<b><?php echo esc_html( $s['money_title'] ); ?></b>
							<p>
								<?php echo esc_html( $s['money_text'] ); ?>
								<?php if ( '' !== $s['money_link_text'] && ! empty( $s['money_link_url']['url'] ) ) : ?>
									<a href="<?php echo esc_url( $s['money_link_url']['url'] ); ?>">
										<?php echo esc_html( $s['money_link_text'] ); ?> &rarr;
									</a>
								<?php endif; ?>
							</p>
						</div>
						<div>
							<b><?php echo esc_html( $s['score_title'] ); ?></b>
							<p>
								<?php echo esc_html( $s['score_text'] ); ?>
								<?php if ( '' !== $s['score_link_text'] && ! empty( $s['score_link_url']['url'] ) ) : ?>
									<a href="<?php echo esc_url( $s['score_link_url']['url'] ); ?>">
										<?php echo esc_html( $s['score_link_text'] ); ?> &rarr;
									</a>
								<?php endif; ?>
							</p>
						</div>
					</div>
				<?php endif; ?>

				<div class="zd-footer__legal">
					<span><?php echo esc_html( $copyright ); ?></span>
					<?php if ( ! empty( $s['legal_links'] ) && is_array( $s['legal_links'] ) ) : ?>
						<?php foreach ( $s['legal_links'] as $link ) : ?>
							<a href="<?php echo esc_url( ! empty( $link['url']['url'] ) ? $link['url']['url'] : '#' ); ?>">
								<?php echo esc_html( $link['label'] ); ?>
							</a>
						<?php endforeach; ?>
					<?php endif; ?>
					<?php if ( '' !== $sweep ) : ?>
						<span class="zd-footer__sweep">
							<?php
							printf(
								/* translators: %s: date of the most recent verification. */
								esc_html__( 'Last catalogue sweep: %s', 'zlaark-deals-pro' ),
								esc_html( $sweep )
							);
							?>
						</span>
					<?php endif; ?>
				</div>
			</div>
		</footer>
		<?php
	}
}
