<?php
/**
 * Zlaark Offer Bar - one deal, pinned to the edge of the viewport, dismissible.
 *
 * The bar is the last thing on the page a reader can act on, so it carries the
 * strongest single line the deal can make: the computed first-year saving where
 * there is one, the headline offer otherwise. A dismissal is remembered per
 * deal, so closing it does not mean seeing it again on the next article.
 *
 * @package Zlaark_Deals_Pro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;

class Zlaark_Offerbar_Widget extends Zlaark_Query_Widget_Base {

	public function get_name() {
		return 'zlaark_offerbar';
	}

	public function get_title() {
		return __( 'Zlaark Offer Bar', 'zlaark-deals-pro' );
	}

	public function get_icon() {
		return 'eicon-header';
	}

	public function get_keywords() {
		return array( 'offer bar', 'sticky', 'banner', 'promo', 'notification', 'zlaark' );
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_source',
			array(
				'label' => __( 'Deal', 'zlaark-deals-pro' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->single_deal_controls();
		$this->end_controls_section();

		$this->start_controls_section(
			'section_layout',
			array( 'label' => __( 'Bar', 'zlaark-deals-pro' ) )
		);

		$this->add_control(
			'position',
			array(
				'label'   => __( 'Position', 'zlaark-deals-pro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'bottom',
				'options' => array(
					'bottom' => __( 'Pinned to the bottom', 'zlaark-deals-pro' ),
					'top'    => __( 'Pinned to the top', 'zlaark-deals-pro' ),
					'inline' => __( 'In the flow, not pinned', 'zlaark-deals-pro' ),
				),
			)
		);

		$this->add_control(
			'message',
			array(
				'label'       => __( 'Message', 'zlaark-deals-pro' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'placeholder' => __( 'Leave empty to build it from the deal', 'zlaark-deals-pro' ),
				'description' => __( 'Empty is usually better: the generated line uses the deal\'s own numbers and cannot go stale when the price changes.', 'zlaark-deals-pro' ),
			)
		);

		$this->add_control(
			'dismissible',
			array(
				'label'        => __( 'Dismissible', 'zlaark-deals-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'description'  => __( 'Remembered per deal in the visitor\'s browser, so a close stays closed.', 'zlaark-deals-pro' ),
			)
		);

		$this->add_control(
			'delay',
			array(
				'label'       => __( 'Appear After', 'zlaark-deals-pro' ),
				'type'        => Controls_Manager::NUMBER,
				'min'         => 0,
				'max'         => 30,
				'default'     => 2,
				'condition'   => array( 'position!' => 'inline' ),
				'description' => __( 'Seconds. A bar that slides in the instant the page loads reads as an ad.', 'zlaark-deals-pro' ),
			)
		);

		$this->add_control(
			'nofollow',
			array(
				'label'        => __( 'Add rel="nofollow sponsored"', 'zlaark-deals-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style',
			array(
				'label' => __( 'Style', 'zlaark-deals-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'bar_bg',
			array(
				'label'     => __( 'Background', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .zd-obar' => '--zd-obar-bg: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'bar_fg',
			array(
				'label'     => __( 'Text', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .zd-obar' => '--zd-obar-fg: {{VALUE}};' ),
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$s    = $this->get_settings_for_display();
		$deal = $this->resolve_single_deal( $s );

		if ( empty( $deal ) ) {
			if ( $this->is_editor() ) {
				$this->render_empty_notice();
			}
			return;
		}

		// An expired deal must never be the thing pinned to the viewport.
		if ( ! empty( $deal['is_expired'] ) ) {
			return;
		}

		$message = ( '' !== $s['message'] ) ? $s['message'] : $this->auto_message( $deal );

		if ( '' === $message || '' === $deal['button_text'] ) {
			if ( $this->is_editor() ) {
				$this->render_empty_notice();
			}
			return;
		}

		$follow = 'yes' === $s['nofollow'];
		$rel    = $follow ? 'nofollow sponsored noopener' : 'noopener';
		$url    = '' !== $deal['button_url'] ? $deal['button_url'] : $deal['permalink'];
		$pinned = 'inline' !== $s['position'];
		$delay  = $pinned ? max( 0, (int) $s['delay'] ) : 0;
		?>
		<aside class="zd-obar zd-obar--<?php echo esc_attr( $s['position'] ); ?>"
			<?php echo $pinned ? 'data-zd-obar="' . esc_attr( $deal['id'] ) . '"' : ''; ?>
			data-zd-obar-delay="<?php echo esc_attr( $delay * 1000 ); ?>"
			role="complementary"
			aria-label="<?php esc_attr_e( 'Current offer', 'zlaark-deals-pro' ); ?>">

			<div class="zd-obar__inner">
				<p class="zd-obar__msg"><?php echo esc_html( $message ); ?></p>

				<a class="zd-obar__cta" href="<?php echo esc_url( $url ); ?>"
					rel="<?php echo esc_attr( $rel ); ?>"
					<?php echo $deal['button_new'] ? 'target="_blank"' : ''; ?>>
					<span><?php echo esc_html( $deal['button_text'] ); ?></span>
					<svg viewBox="0 0 16 16" width="14" height="14" fill="none" aria-hidden="true">
						<path d="M2 8h11M9 4l4 4-4 4" stroke="currentColor" stroke-width="2"
							stroke-linecap="round" stroke-linejoin="round" />
					</svg>
				</a>

				<?php if ( $pinned && 'yes' === $s['dismissible'] ) : ?>
					<button class="zd-obar__close" type="button"
						aria-label="<?php esc_attr_e( 'Dismiss this offer', 'zlaark-deals-pro' ); ?>">
						<svg viewBox="0 0 16 16" width="15" height="15" fill="none" aria-hidden="true">
							<path d="M4 4l8 8M12 4l-8 8" stroke="currentColor" stroke-width="2"
								stroke-linecap="round" />
						</svg>
					</button>
				<?php endif; ?>
			</div>
		</aside>
		<?php
	}

	/**
	 * The strongest line the deal can make on its own numbers.
	 *
	 * Order matters: a whole-year saving beats a percentage, a percentage beats
	 * a price, and a price beats an adjective. annual_saving is computed on
	 * every deal and, until this widget, was read only by the deal template.
	 */
	private function auto_message( $deal ) {
		$name = $deal['title'];

		if ( null !== $deal['annual_saving'] && $deal['annual_saving'] > 0 ) {
			return sprintf(
				/* translators: 1: deal name, 2: saving over the first year. */
				__( '%1$s saves you %2$s over the first year.', 'zlaark-deals-pro' ),
				$name,
				Zlaark_Deals_Computed::format_money( $deal['annual_saving'], $deal )
			);
		}

		if ( '' !== $deal['offer_headline'] ) {
			return sprintf(
				/* translators: 1: deal name, 2: offer headline, e.g. "61% off". */
				__( '%1$s: %2$s right now.', 'zlaark-deals-pro' ),
				$name,
				$deal['offer_headline']
			);
		}

		if ( null !== $deal['discount_pct'] ) {
			return sprintf(
				/* translators: 1: deal name, 2: discount percentage. */
				__( '%1$s is %2$d%% off right now.', 'zlaark-deals-pro' ),
				$name,
				(int) $deal['discount_pct']
			);
		}

		if ( '' !== $deal['price'] ) {
			return sprintf(
				/* translators: 1: deal name, 2: current price. */
				__( '%1$s from %2$s.', 'zlaark-deals-pro' ),
				$name,
				$deal['price']
			);
		}

		return '';
	}

}
