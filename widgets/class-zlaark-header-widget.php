<?php
/**
 * Zlaark Header - a masthead for a review publication, not an app bar.
 *
 * Two tiers. The top one is an ink rail carrying counts read off the deals
 * catalogue, so the header states something checkable before the reader has
 * scrolled a pixel - the same argument every other band on the page makes.
 * The bar underneath is a wordmark, a centred menu with a sliding indicator,
 * and one action: the finder. There is no Log In / Sign Up, because nobody
 * signs in to a publication.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Group_Control_Typography;

class Zlaark_Header_Widget extends Zlaark_Widget_Base {

	/** Counts are asked for once per render, not once per ticker row. */
	private $stats = null;

	public function get_name() {
		return 'zlaark_header';
	}

	public function get_title() {
		return __( 'Zlaark Header', 'zlaark-deals-pro' );
	}

	public function get_icon() {
		return 'eicon-header';
	}

	public function get_keywords() {
		return array( 'header', 'masthead', 'nav', 'navbar', 'menu', 'mega', 'zlaark' );
	}

	protected function register_controls() {
		$this->brand_controls();
		$this->ticker_controls();
		$this->menu_controls();
		$this->mega_controls();
		$this->action_controls();
		$this->layout_controls();
		$this->style_controls();
	}

	/* ------------------------------------------------------------------ brand */

	private function brand_controls() {
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
				),
			)
		);

		/*
		 * The same two fields the footer uses, so one site cannot end up with
		 * two different marks. Splitting the accented tail out of the word is
		 * what makes it a mark rather than a bold line of text.
		 */
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
				'condition'   => array( 'logo_type' => 'text' ),
				'description' => __( 'The tail of the wordmark, coloured. Must match the end of the word above.', 'zlaark-deals-pro' ),
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
			'logo_link',
			array(
				'label'   => __( 'Link', 'zlaark-deals-pro' ),
				'type'    => Controls_Manager::URL,
				'default' => array( 'url' => home_url( '/' ) ),
			)
		);

		$this->end_controls_section();
	}

	/* ----------------------------------------------------------------- ticker */

	private function ticker_controls() {
		$this->start_controls_section(
			'sec_ticker',
			array( 'label' => __( 'Ticker Rail', 'zlaark-deals-pro' ) )
		);

		$this->add_control(
			'ticker_on',
			array(
				'label'        => __( 'Show Ticker Rail', 'zlaark-deals-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);

		$row = new Repeater();

		$row->add_control(
			'text',
			array(
				'label'       => __( 'Text', 'zlaark-deals-pro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '{deals} products tracked',
				'label_block' => true,
			)
		);

		$this->add_control(
			'ticker',
			array(
				'label'       => __( 'Items', 'zlaark-deals-pro' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $row->get_controls(),
				'title_field' => '{{{ text }}}',
				'condition'   => array( 'ticker_on' => 'yes' ),
				/*
				 * Tokens rather than typed-in numbers. A masthead that claims
				 * "40+ products" goes stale the day the catalogue changes, and
				 * nobody remembers to edit a header - so the number is read off
				 * the catalogue every time the page is built.
				 */
				'description' => __( 'Use {deals} for the number of published deals, {closing} for offers ending within 14 days, and {checked} for the most recent verification date.', 'zlaark-deals-pro' ),
				'default'     => array(
					array( 'text' => '{deals} products tracked' ),
					array( 'text' => '{closing} offers closing' ),
					array( 'text' => 'Re-checked {checked}' ),
				),
			)
		);

		$this->add_control(
			'rail_cta_text',
			array(
				'label'     => __( 'Rail Link Text', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'How we test', 'zlaark-deals-pro' ),
				'condition' => array( 'ticker_on' => 'yes' ),
			)
		);

		$this->add_control(
			'rail_cta_url',
			array(
				'label'     => __( 'Rail Link', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::URL,
				'default'   => array( 'url' => '#' ),
				'condition' => array( 'ticker_on' => 'yes' ),
			)
		);

		$this->end_controls_section();
	}

	/* ------------------------------------------------------------------- menu */

	private function menu_controls() {
		$this->start_controls_section(
			'sec_menu',
			array( 'label' => __( 'Menu', 'zlaark-deals-pro' ) )
		);

		$item = new Repeater();

		$item->add_control(
			'text',
			array(
				'label'   => __( 'Text', 'zlaark-deals-pro' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Menu item', 'zlaark-deals-pro' ),
			)
		);

		$item->add_control(
			'link',
			array(
				'label'   => __( 'Link', 'zlaark-deals-pro' ),
				'type'    => Controls_Manager::URL,
				'default' => array( 'url' => '#' ),
			)
		);

		$item->add_control(
			'mega',
			array(
				'label'        => __( 'Opens Mega Panel', 'zlaark-deals-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'description'  => __( 'Built from the Mega Panel section, matched on this item\'s text.', 'zlaark-deals-pro' ),
			)
		);

		$item->add_control(
			'is_active',
			array(
				'label'        => __( 'Mark as Active', 'zlaark-deals-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'items',
			array(
				'label'       => __( 'Items', 'zlaark-deals-pro' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $item->get_controls(),
				'title_field' => '{{{ text }}}',
				'default'     => array(
					array( 'text' => __( 'Deals', 'zlaark-deals-pro' ), 'is_active' => 'yes' ),
					array( 'text' => __( 'Hosting', 'zlaark-deals-pro' ), 'mega' => 'yes' ),
					array( 'text' => __( 'Reviews', 'zlaark-deals-pro' ) ),
					array( 'text' => __( 'Compare', 'zlaark-deals-pro' ) ),
					array( 'text' => __( 'How we test', 'zlaark-deals-pro' ) ),
				),
			)
		);

		$this->add_control(
			'auto_active',
			array(
				'label'        => __( 'Auto-detect Current Page', 'zlaark-deals-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'separator'    => 'before',
			)
		);

		$this->add_control(
			'slide_indicator',
			array(
				'label'        => __( 'Indicator Follows the Pointer', 'zlaark-deals-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);

		$this->end_controls_section();
	}

	/* ------------------------------------------------------------- mega panel */

	/**
	 * Elementor cannot nest a repeater inside a repeater, so the columns are
	 * one flat list and each row names the item it belongs to. One repeater
	 * feeds every panel; a fixed "column 1..4" group would have capped the
	 * design at whatever number was guessed here.
	 */
	private function mega_controls() {
		$this->start_controls_section(
			'sec_mega',
			array( 'label' => __( 'Mega Panel', 'zlaark-deals-pro' ) )
		);

		$col = new Repeater();

		$col->add_control(
			'parent',
			array(
				'label'       => __( 'Belongs To', 'zlaark-deals-pro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Hosting', 'zlaark-deals-pro' ),
				'description' => __( 'The menu item text this column appears under. Match it exactly.', 'zlaark-deals-pro' ),
			)
		);

		$col->add_control(
			'heading',
			array(
				'label'   => __( 'Column Heading', 'zlaark-deals-pro' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'By use case', 'zlaark-deals-pro' ),
			)
		);

		$col->add_control(
			'links',
			array(
				'label'       => __( 'Links', 'zlaark-deals-pro' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 6,
				'description' => __( 'One per line as Label|URL. A line with no URL renders as plain text.', 'zlaark-deals-pro' ),
			)
		);

		$this->add_control(
			'mega_cols',
			array(
				'label'       => __( 'Columns', 'zlaark-deals-pro' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $col->get_controls(),
				'title_field' => '{{{ parent }}} - {{{ heading }}}',
				'default'     => array(
					array(
						'parent'  => __( 'Hosting', 'zlaark-deals-pro' ),
						'heading' => __( 'By use case', 'zlaark-deals-pro' ),
						'links'   => "WordPress hosting|#\nVPS hosting|#\nCloud hosting|#\nReseller hosting|#",
					),
					array(
						'parent'  => __( 'Hosting', 'zlaark-deals-pro' ),
						'heading' => __( 'By budget', 'zlaark-deals-pro' ),
						'links'   => "Under $3 a month|#\nUnder $10 a month|#\nBest value overall|#",
					),
					array(
						'parent'  => __( 'Hosting', 'zlaark-deals-pro' ),
						'heading' => __( 'Head to head', 'zlaark-deals-pro' ),
						'links'   => "Hostinger vs Bluehost|#\nWP Engine vs Kinsta|#\nAll comparisons|#",
					),
				),
			)
		);

		$this->add_control(
			'finder_head',
			array(
				'label'     => __( 'Finder Column', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'finder_parent',
			array(
				'label'       => __( 'Belongs To', 'zlaark-deals-pro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Hosting', 'zlaark-deals-pro' ),
				'description' => __( 'Leave empty to hide the finder column.', 'zlaark-deals-pro' ),
			)
		);

		$this->add_control(
			'finder_title',
			array(
				'label'   => __( 'Title', 'zlaark-deals-pro' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Hosting finder', 'zlaark-deals-pro' ),
			)
		);

		$this->add_control(
			'finder_text',
			array(
				'label'   => __( 'Text', 'zlaark-deals-pro' ),
				'type'    => Controls_Manager::TEXTAREA,
				'rows'    => 3,
				'default' => __( 'Three questions, then a shortlist built only from hosts we have paid for and measured ourselves.', 'zlaark-deals-pro' ),
			)
		);

		$this->add_control(
			'finder_cta_text',
			array(
				'label'   => __( 'Button Text', 'zlaark-deals-pro' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Start the finder', 'zlaark-deals-pro' ),
			)
		);

		$this->add_control(
			'finder_cta_url',
			array(
				'label'   => __( 'Button Link', 'zlaark-deals-pro' ),
				'type'    => Controls_Manager::URL,
				'default' => array( 'url' => '#' ),
			)
		);

		$this->end_controls_section();
	}

	/* ----------------------------------------------------------------- action */

	private function action_controls() {
		$this->start_controls_section(
			'sec_action',
			array( 'label' => __( 'Action', 'zlaark-deals-pro' ) )
		);

		$this->add_control(
			'action_text',
			array(
				'label'       => __( 'Button Text', 'zlaark-deals-pro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Hosting finder', 'zlaark-deals-pro' ),
				'description' => __( 'One action, not two. Leave empty to hide it.', 'zlaark-deals-pro' ),
			)
		);

		$this->add_control(
			'action_url',
			array(
				'label'   => __( 'Button Link', 'zlaark-deals-pro' ),
				'type'    => Controls_Manager::URL,
				'default' => array( 'url' => '#' ),
			)
		);

		$this->end_controls_section();
	}

	/* ----------------------------------------------------------------- layout */

	private function layout_controls() {
		$this->start_controls_section(
			'sec_layout',
			array( 'label' => __( 'Layout', 'zlaark-deals-pro' ) )
		);

		$this->add_control(
			'sticky',
			array(
				'label'        => __( 'Stick to the Top', 'zlaark-deals-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'shrink',
			array(
				'label'        => __( 'Shrink Once Stuck', 'zlaark-deals-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'condition'    => array( 'sticky' => 'yes' ),
				'description'  => __( 'The ticker rail rolls up and the bar tightens.', 'zlaark-deals-pro' ),
			)
		);

		$this->add_responsive_control(
			'max_width',
			array(
				'label'      => __( 'Content Max Width', 'zlaark-deals-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 960, 'max' => 1920 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 1240 ),
				'selectors'  => array(
					'{{WRAPPER}} .zd-hdr__inner, {{WRAPPER}} .zd-hdr__railinner' => 'max-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'breakpoint',
			array(
				'label'      => __( 'Collapse Below', 'zlaark-deals-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 600, 'max' => 1400 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 1024 ),
			)
		);

		$this->end_controls_section();
	}

	/* ------------------------------------------------------------------ style */

	private function style_controls() {
		$this->start_controls_section(
			'sec_style',
			array(
				'label' => __( 'Colours', 'zlaark-deals-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		/*
		 * Every default here is a plugin token. The previous header shipped a
		 * blue-and-zinc palette from a different design system, which is why it
		 * read as somebody else's widget dropped onto the page.
		 */
		$colours = array(
			'c_rail'   => array( __( 'Ticker Rail', 'zlaark-deals-pro' ), '--zd-hdr-rail', '#0a1310' ),
			'c_railink'=> array( __( 'Ticker Text', 'zlaark-deals-pro' ), '--zd-hdr-railink', '#8fa79b' ),
			'c_bg'     => array( __( 'Bar Background', 'zlaark-deals-pro' ), '--zd-hdr-bg', '#ffffff' ),
			'c_rule'   => array( __( 'Rules', 'zlaark-deals-pro' ), '--zd-hdr-rule', '#dce3df' ),
			'c_ink'    => array( __( 'Wordmark', 'zlaark-deals-pro' ), '--zd-hdr-ink', '#0a1310' ),
			'c_link'   => array( __( 'Links', 'zlaark-deals-pro' ), '--zd-hdr-link', '#4a5a52' ),
			'c_accent' => array( __( 'Accent', 'zlaark-deals-pro' ), '--zd-hdr-accent', '#0b7a4f' ),
		);

		foreach ( $colours as $id => $c ) {
			$this->add_control(
				$id,
				array(
					'label'     => $c[0],
					'type'      => Controls_Manager::COLOR,
					'default'   => $c[2],
					'selectors' => array( '{{WRAPPER}} .zd-hdr' => $c[1] . ': {{VALUE}};' ),
				)
			);
		}

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'link_type',
				'selector' => '{{WRAPPER}} .zd-hdr__list a',
			)
		);

		$this->end_controls_section();
	}

	/* ----------------------------------------------------------------- render */

	/**
	 * Counts for the ticker, read off the catalogue.
	 *
	 * One pass, cached on the instance: the ticker is a handful of rows and
	 * every one of them would otherwise re-run the same queries.
	 */
	private function stats() {
		if ( null !== $this->stats ) {
			return $this->stats;
		}

		$counts = wp_count_posts( ZLAARK_DEALS_CPT );
		$deals  = isset( $counts->publish ) ? (int) $counts->publish : 0;

		$closing  = 0;
		$verified = '';

		$rows = get_posts(
			array(
				'post_type'        => ZLAARK_DEALS_CPT,
				'post_status'      => 'publish',
				'numberposts'      => 60,
				'fields'           => 'ids',
				'suppress_filters' => false,
			)
		);

		foreach ( $rows as $id ) {
			$days = Zlaark_Deals_Computed::days_until( (string) get_post_meta( $id, '_zlaark_expiry_date', true ) );
			if ( null !== $days && $days >= 0 && $days <= 14 ) {
				$closing++;
			}

			$seen = (string) get_post_meta( $id, '_zlaark_last_verified', true );
			if ( '' !== $seen && $seen > $verified ) {
				$verified = $seen;
			}
		}

		$this->stats = array(
			'{deals}'   => number_format_i18n( $deals ),
			'{closing}' => number_format_i18n( $closing ),
			'{checked}' => '' !== $verified
				? date_i18n( 'j M', (int) strtotime( $verified ) )
				: __( 'this month', 'zlaark-deals-pro' ),
		);

		return $this->stats;
	}

	protected function render() {
		$s = $this->get_settings_for_display();

		$classes = array( 'zd-hdr' );
		if ( 'yes' === $s['sticky'] ) {
			$classes[] = 'zd-hdr--sticky';
			if ( 'yes' === $s['shrink'] ) {
				$classes[] = 'zd-hdr--shrink';
			}
		}
		if ( 'yes' === $s['slide_indicator'] ) {
			$classes[] = 'zd-hdr--slide';
		}

		$bp  = isset( $s['breakpoint']['size'] ) ? (int) $s['breakpoint']['size'] : 1024;
		$uid = 'zd-hdr-' . $this->get_id();
		?>
		<header class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>"
			data-zd-hdr-bp="<?php echo (int) $bp; ?>">

			<?php $this->render_rail( $s ); ?>

			<div class="zd-hdr__bar">
				<div class="zd-hdr__inner">
					<?php $this->render_brand( $s ); ?>

					<button class="zd-hdr__burger" type="button" aria-expanded="false"
						aria-controls="<?php echo esc_attr( $uid ); ?>"
						aria-label="<?php esc_attr_e( 'Toggle menu', 'zlaark-deals-pro' ); ?>">
						<span></span><span></span>
					</button>

					<div class="zd-hdr__panel" id="<?php echo esc_attr( $uid ); ?>">
						<?php $this->render_nav( $s ); ?>
						<?php $this->render_action( $s ); ?>
					</div>
				</div>
			</div>
		</header>
		<?php
	}

	/** The ink rail. Nothing to say means no rail rather than an empty strip. */
	private function render_rail( $s ) {
		if ( 'yes' !== $s['ticker_on'] ) {
			return;
		}

		$rows = is_array( $s['ticker'] ) ? $s['ticker'] : array();
		$out  = array();

		foreach ( $rows as $row ) {
			$text = trim( (string) $row['text'] );
			if ( '' === $text ) {
				continue;
			}
			$stats = $this->stats();
			$out[] = str_replace( array_keys( $stats ), array_values( $stats ), $text );
		}

		$cta = trim( (string) $s['rail_cta_text'] );

		if ( empty( $out ) && '' === $cta ) {
			return;
		}
		?>
		<div class="zd-hdr__rail">
			<div class="zd-hdr__railinner">
				<p class="zd-hdr__ticker">
					<?php foreach ( $out as $line ) : ?>
						<?php
						/*
						 * The numerals are wrapped so they can be set in the
						 * tabular mono face while the words around them stay in
						 * the reading face - a count that shifts width as it
						 * changes is the tell of a decorative number.
						 */
						$line = preg_replace( '/(\d[\d,.]*)/', '<i>$1</i>', esc_html( $line ) );
						?>
						<span><?php echo wp_kses( $line, array( 'i' => array() ) ); ?></span>
					<?php endforeach; ?>
				</p>

				<?php if ( '' !== $cta ) : ?>
					<a class="zd-hdr__railcta"
						href="<?php echo esc_url( ! empty( $s['rail_cta_url']['url'] ) ? $s['rail_cta_url']['url'] : '#' ); ?>">
						<span><?php echo esc_html( $cta ); ?></span>
						<svg viewBox="0 0 16 16" width="12" height="12" fill="none" aria-hidden="true">
							<path d="M2 8h11M9 4l4 4-4 4" stroke="currentColor" stroke-width="2"
								stroke-linecap="round" stroke-linejoin="round" />
						</svg>
					</a>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}

	private function render_brand( $s ) {
		$this->add_render_attribute( 'brand', 'class', 'zd-hdr__brand' );
		if ( ! empty( $s['logo_link']['url'] ) ) {
			$this->add_link_attributes( 'brand', $s['logo_link'] );
		} else {
			$this->add_render_attribute( 'brand', 'href', home_url( '/' ) );
		}
		?>
		<a <?php $this->print_render_attribute_string( 'brand' ); ?>>
			<?php if ( 'image' === $s['logo_type'] && ! empty( $s['logo_image']['url'] ) ) : ?>
				<img class="zd-hdr__logo" src="<?php echo esc_url( $s['logo_image']['url'] ); ?>"
					alt="<?php echo esc_attr( zlaark_deals_media_alt( $s['logo_image'] ) ); ?>" />
			<?php else : ?>
				<span class="zd-hdr__word">
					<?php
					$word   = (string) $s['logo_text'];
					$accent = (string) $s['logo_accent'];

					// Only split when the tail really is the tail, so a mismatch
					// prints the word intact instead of silently losing letters.
					if ( '' !== $accent && substr( $word, -strlen( $accent ) ) === $accent ) {
						echo esc_html( substr( $word, 0, -strlen( $accent ) ) );
						echo '<span>' . esc_html( $accent ) . '</span>';
					} else {
						echo esc_html( $word );
					}
					?>
				</span>
			<?php endif; ?>
		</a>
		<?php
	}

	private function render_nav( $s ) {
		if ( empty( $s['items'] ) || ! is_array( $s['items'] ) ) {
			return;
		}
		?>
		<?php
		/*
		 * Auto-detection wins over the manual flag, but only when it actually
		 * recognises the page. The default menu ships with "Deals" marked
		 * active, so on any other page - Compare, say - both that item and the
		 * detected one lit up at once, and the nav claimed the reader was in
		 * two places. Where nothing matches (a post, a 404) the manual flag is
		 * still the only signal there is, so it keeps its say.
		 */
		$detected = ( 'yes' === $s['auto_active'] ) && $this->detects_current( $s['items'] );
		?>
		<nav class="zd-hdr__nav" aria-label="<?php esc_attr_e( 'Main', 'zlaark-deals-pro' ); ?>">
			<span class="zd-hdr__pill" aria-hidden="true"></span>
			<ul class="zd-hdr__list">
				<?php
				foreach ( $s['items'] as $i => $item ) {
					$url    = ! empty( $item['link']['url'] ) ? $item['link']['url'] : '';
					$active = $detected
						? $this->is_current( $url )
						: ( 'yes' === $item['is_active'] );

					$key = 'hdr_item_' . $i;
					if ( $active ) {
						$this->add_render_attribute( $key, 'class', 'is-active' );
					}
					if ( '' !== $url ) {
						$this->add_link_attributes( $key, $item['link'] );
					} else {
						$this->add_render_attribute( $key, 'href', '#' );
					}

					$panel = ( 'yes' === $item['mega'] ) ? $this->mega_panel( $s, $item['text'] ) : '';
					$pid   = 'zd-mega-' . $this->get_id() . '-' . $i;

					if ( '' !== $panel ) {
						$this->add_render_attribute( $key, 'aria-expanded', 'false' );
						$this->add_render_attribute( $key, 'aria-controls', $pid );
					}
					?>
					<li class="zd-hdr__item<?php echo '' !== $panel ? ' zd-hdr__item--mega' : ''; ?>">
						<a <?php $this->print_render_attribute_string( $key ); ?>>
							<?php echo esc_html( $item['text'] ); ?>
							<?php if ( '' !== $panel ) : ?>
								<svg class="zd-hdr__chev" viewBox="0 0 12 12" width="10" height="10"
									fill="none" aria-hidden="true">
									<path d="M2.5 4.5L6 8l3.5-3.5" stroke="currentColor" stroke-width="1.6"
										stroke-linecap="round" stroke-linejoin="round" />
								</svg>
							<?php endif; ?>
						</a>
						<?php if ( '' !== $panel ) : ?>
							<div class="zd-mega" id="<?php echo esc_attr( $pid ); ?>" hidden>
								<div class="zd-mega__inner"><?php echo $panel; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- assembled and escaped in mega_panel(). ?></div>
							</div>
						<?php endif; ?>
					</li>
					<?php
				}
				?>
			</ul>
		</nav>
		<?php
	}

	private function render_action( $s ) {
		$text = trim( (string) $s['action_text'] );
		if ( '' === $text ) {
			return;
		}
		?>
		<div class="zd-hdr__actions">
			<a class="zd-hdr__cta"
				href="<?php echo esc_url( ! empty( $s['action_url']['url'] ) ? $s['action_url']['url'] : '#' ); ?>"
				<?php echo ! empty( $s['action_url']['is_external'] ) ? 'target="_blank" rel="noopener"' : ''; ?>>
				<span><?php echo esc_html( $text ); ?></span>
				<svg viewBox="0 0 16 16" width="13" height="13" fill="none" aria-hidden="true">
					<path d="M2 8h11M9 4l4 4-4 4" stroke="currentColor" stroke-width="2"
						stroke-linecap="round" stroke-linejoin="round" />
				</svg>
			</a>
		</div>
		<?php
	}

	/**
	 * The panel for one menu item, or '' when that item has nothing to show.
	 *
	 * Returned rather than echoed so the caller can ask "is there a panel?"
	 * before deciding whether the item gets a chevron and the ARIA wiring - an
	 * item announcing a menu it does not have is worse than one that stays a
	 * plain link.
	 */
	private function mega_panel( $s, $label ) {
		$label = trim( (string) $label );
		if ( '' === $label ) {
			return '';
		}

		$mine = array();

		foreach ( ( is_array( $s['mega_cols'] ) ? $s['mega_cols'] : array() ) as $col ) {
			if ( 0 !== strcasecmp( trim( (string) $col['parent'] ), $label ) ) {
				continue;
			}
			$links = $this->parse_links( $col['links'] );
			if ( '' === trim( (string) $col['heading'] ) && empty( $links ) ) {
				continue;
			}
			$mine[] = array( 'heading' => $col['heading'], 'links' => $links );
		}

		$finder = ( 0 === strcasecmp( trim( (string) $s['finder_parent'] ), $label ) )
			&& ( '' !== trim( (string) $s['finder_title'] ) );

		if ( empty( $mine ) && ! $finder ) {
			return '';
		}

		ob_start();
		?>
		<div class="zd-mega__cols">
			<?php foreach ( $mine as $col ) : ?>
				<div class="zd-mega__col">
					<?php if ( '' !== trim( (string) $col['heading'] ) ) : ?>
						<p class="zd-mega__head">
							<span><?php echo esc_html( $col['heading'] ); ?></span>
							<?php if ( ! empty( $col['links'] ) ) : ?>
								<i><?php echo esc_html( number_format_i18n( count( $col['links'] ) ) ); ?></i>
							<?php endif; ?>
						</p>
					<?php endif; ?>

					<?php if ( ! empty( $col['links'] ) ) : ?>
						<ul class="zd-mega__links">
							<?php foreach ( $col['links'] as $link ) : ?>
								<li>
									<?php if ( '' !== $link['url'] ) : ?>
										<a href="<?php echo esc_url( $link['url'] ); ?>"><?php echo esc_html( $link['label'] ); ?></a>
									<?php else : ?>
										<span><?php echo esc_html( $link['label'] ); ?></span>
									<?php endif; ?>
								</li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>

		<?php if ( $finder ) : ?>
			<div class="zd-mega__finder">
				<p class="zd-mega__head"><span><?php esc_html_e( 'Not sure yet', 'zlaark-deals-pro' ); ?></span></p>
				<p class="zd-mega__findertitle"><?php echo esc_html( $s['finder_title'] ); ?></p>
				<?php if ( '' !== $s['finder_text'] ) : ?>
					<p class="zd-mega__findertext"><?php echo esc_html( $s['finder_text'] ); ?></p>
				<?php endif; ?>
				<?php if ( '' !== $s['finder_cta_text'] ) : ?>
					<a class="zd-mega__findercta"
						href="<?php echo esc_url( ! empty( $s['finder_cta_url']['url'] ) ? $s['finder_cta_url']['url'] : '#' ); ?>">
						<span><?php echo esc_html( $s['finder_cta_text'] ); ?></span>
						<svg viewBox="0 0 16 16" width="13" height="13" fill="none" aria-hidden="true">
							<path d="M2 8h11M9 4l4 4-4 4" stroke="currentColor" stroke-width="2"
								stroke-linecap="round" stroke-linejoin="round" />
						</svg>
					</a>
				<?php endif; ?>
			</div>
		<?php endif; ?>
		<?php
		return (string) ob_get_clean();
	}

	/** "Label|URL" per line. A line with no pipe is a label with no link. */
	private function parse_links( $raw ) {
		$out = array();

		foreach ( preg_split( '/\r\n|\r|\n/', (string) $raw ) as $line ) {
			$line = trim( $line );
			if ( '' === $line ) {
				continue;
			}
			$parts = explode( '|', $line, 2 );
			$label = trim( $parts[0] );
			if ( '' === $label ) {
				continue;
			}
			$out[] = array(
				'label' => $label,
				'url'   => isset( $parts[1] ) ? trim( $parts[1] ) : '',
			);
		}

		return $out;
	}

	/** True when one of the menu items points at the page being viewed. */
	private function detects_current( $items ) {
		foreach ( $items as $item ) {
			$url = ! empty( $item['link']['url'] ) ? $item['link']['url'] : '';
			if ( $this->is_current( $url ) ) {
				return true;
			}
		}

		return false;
	}

	/** True when a link points at the page currently being viewed. */
	private function is_current( $url ) {
		if ( '' === $url || '#' === $url ) {
			return false;
		}

		$current = untrailingslashit( strtok( home_url( add_query_arg( array() ) ), '?' ) );
		$target  = untrailingslashit( strtok( $url, '?' ) );

		return $current === $target;
	}
}
