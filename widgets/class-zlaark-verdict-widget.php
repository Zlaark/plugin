<?php
/**
 * Zlaark Verdict - pros, cons, and who the deal is and is not for.
 *
 * Four fields are typed on every deal - pros, cons, best_for, not_for - and
 * until now none of them appeared anywhere except the deal template. The
 * admin says it plainly on the Not For field: recommending against yourself
 * is the strongest trust signal available. That argument was invisible on
 * every comparison and review page on the site.
 *
 * @package Zlaark_Deals_Pro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;

class Zlaark_Verdict_Widget extends Zlaark_Query_Widget_Base {

	public function get_name() {
		return 'zlaark_verdict';
	}

	public function get_title() {
		return __( 'Zlaark Verdict', 'zlaark-deals-pro' );
	}

	public function get_icon() {
		return 'eicon-check-circle';
	}

	public function get_keywords() {
		return array( 'pros', 'cons', 'verdict', 'best for', 'not for', 'zlaark' );
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
			array( 'label' => __( 'Layout', 'zlaark-deals-pro' ) )
		);

		$this->add_control(
			'show_verdict',
			array(
				'label'        => __( 'Verdict Sentence', 'zlaark-deals-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'show_proscons',
			array(
				'label'        => __( 'Pros & Cons', 'zlaark-deals-pro' ),
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
			)
		);

		$this->add_control(
			'pros_title',
			array(
				'label'     => __( 'Pros Heading', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'What we liked', 'zlaark-deals-pro' ),
				'separator' => 'before',
				'condition' => array( 'show_proscons' => 'yes' ),
			)
		);

		$this->add_control(
			'cons_title',
			array(
				'label'     => __( 'Cons Heading', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'What we did not', 'zlaark-deals-pro' ),
				'condition' => array( 'show_proscons' => 'yes' ),
			)
		);

		$this->add_control(
			'best_title',
			array(
				'label'     => __( 'Best For Heading', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Buy it if', 'zlaark-deals-pro' ),
				'separator' => 'before',
				'condition' => array( 'show_fit' => 'yes' ),
			)
		);

		$this->add_control(
			'not_title',
			array(
				'label'     => __( 'Not For Heading', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Skip it if', 'zlaark-deals-pro' ),
				'condition' => array( 'show_fit' => 'yes' ),
			)
		);

		$this->max_width_control( '{{WRAPPER}} .zd-verdict', 960 );

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style',
			array(
				'label' => __( 'Style', 'zlaark-deals-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'accent',
			array(
				'label'     => __( 'Accent', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .zd-verdict' => '--zd-accent: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'con_colour',
			array(
				'label'       => __( 'Cons Colour', 'zlaark-deals-pro' ),
				'type'        => Controls_Manager::COLOR,
				'selectors'   => array( '{{WRAPPER}} .zd-verdict' => '--zd-ember: {{VALUE}};' ),
				'description' => __( 'Used for the minus marks and the Skip it if column.', 'zlaark-deals-pro' ),
			)
		);

		$this->end_controls_section();
		$this->animation_controls( false );
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

		$verdict  = ( 'yes' === $s['show_verdict'] ) ? $deal['verdict'] : '';
		$pros     = ( 'yes' === $s['show_proscons'] ) ? $deal['pros'] : array();
		$cons     = ( 'yes' === $s['show_proscons'] ) ? $deal['cons'] : array();
		$best_for = ( 'yes' === $s['show_fit'] ) ? $deal['best_for'] : array();
		$not_for  = ( 'yes' === $s['show_fit'] ) ? $deal['not_for'] : array();

		if ( '' === $verdict && empty( $pros ) && empty( $cons ) && empty( $best_for ) && empty( $not_for ) ) {
			if ( $this->is_editor() ) {
				$this->render_empty_notice();
			}
			return;
		}

		$reveal = $s['reveal_effect'];
		?>
		<div class="zd-verdict" data-zd-reveal-root="true" data-zd-stagger="60">

			<?php if ( '' !== $verdict ) : ?>
				<?php
				/*
				 * The verdict leads. Someone who reads one line of this widget
				 * should get the conclusion, not the evidence for it.
				 */
				?>
				<p class="zd-verdict__lede zd-reveal" data-zd-reveal="<?php echo esc_attr( $reveal ); ?>">
					<?php echo esc_html( $verdict ); ?>
				</p>
			<?php endif; ?>

			<?php if ( ! empty( $pros ) || ! empty( $cons ) ) : ?>
				<div class="zd-verdict__split">
					<?php
					$this->column( 'pro', $s['pros_title'], $pros, $reveal );
					$this->column( 'con', $s['cons_title'], $cons, $reveal );
					?>
				</div>
			<?php endif; ?>

			<?php if ( ! empty( $best_for ) || ! empty( $not_for ) ) : ?>
				<div class="zd-verdict__split zd-verdict__split--fit">
					<?php
					$this->column( 'best', $s['best_title'], $best_for, $reveal );
					$this->column( 'not', $s['not_title'], $not_for, $reveal );
					?>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * One labelled list. Renders nothing when empty rather than an empty
	 * heading, so a deal with pros but no cons prints one full-width column.
	 *
	 * @param string $kind  pro | con | best | not.
	 * @param string $title Column heading.
	 * @param array  $items Lines.
	 * @param string $reveal Reveal effect name.
	 */
	private function column( $kind, $title, $items, $reveal ) {
		if ( empty( $items ) ) {
			return;
		}

		$negative = in_array( $kind, array( 'con', 'not' ), true );
		?>
		<section class="zd-vcol zd-vcol--<?php echo esc_attr( $kind ); ?> zd-reveal"
			data-zd-reveal="<?php echo esc_attr( $reveal ); ?>">

			<h3 class="zd-vcol__title"><?php echo esc_html( $title ); ?></h3>

			<ul class="zd-vcol__list">
				<?php foreach ( $items as $i => $item ) : ?>
					<li style="--zd-i:<?php echo (int) $i; ?>">
						<span class="zd-vcol__mark" aria-hidden="true">
							<?php if ( $negative ) : ?>
								<svg viewBox="0 0 16 16" width="12" height="12" fill="none">
									<path d="M4 8h8" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" />
								</svg>
							<?php else : ?>
								<svg viewBox="0 0 16 16" width="12" height="12" fill="none">
									<path d="M3 8.5l3.2 3.2L13 5" stroke="currentColor" stroke-width="2.2"
										stroke-linecap="round" stroke-linejoin="round" />
								</svg>
							<?php endif; ?>
						</span>
						<?php echo esc_html( $item ); ?>
					</li>
				<?php endforeach; ?>
			</ul>
		</section>
		<?php
	}
}
