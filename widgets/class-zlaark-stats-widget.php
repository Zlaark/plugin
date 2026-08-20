<?php
/**
 * Zlaark Stats — a trust strip of counters that roll up from zero the first
 * time the block scrolls into view, each with an orbiting accent ring.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;

class Zlaark_Stats_Widget extends Zlaark_Widget_Base {

	public function get_name() {
		return 'zlaark_stats';
	}

	public function get_title() {
		return __( 'Zlaark Stats', 'zlaark-deals-pro' );
	}

	public function get_icon() {
		return 'eicon-counter';
	}

	public function get_keywords() {
		return array( 'stats', 'counter', 'numbers', 'trust', 'metrics', 'zlaark' );
	}

	protected function register_controls() {
		$this->content_controls();
		$this->style_controls();
		$this->animation_controls( false );
	}

	private function content_controls() {
		$this->start_controls_section(
			'section_content',
			array( 'label' => __( 'Stats', 'zlaark-deals-pro' ) )
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'value',
			array(
				'label'   => __( 'Number', 'zlaark-deals-pro' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 120,
			)
		);

		$repeater->add_control(
			'decimals',
			array(
				'label'   => __( 'Decimals', 'zlaark-deals-pro' ),
				'type'    => Controls_Manager::NUMBER,
				'min'     => 0,
				'max'     => 2,
				'default' => 0,
			)
		);

		$repeater->add_control(
			'prefix',
			array(
				'label' => __( 'Prefix', 'zlaark-deals-pro' ),
				'type'  => Controls_Manager::TEXT,
			)
		);

		$repeater->add_control(
			'suffix',
			array(
				'label'   => __( 'Suffix', 'zlaark-deals-pro' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '+',
			)
		);

		$repeater->add_control(
			'label',
			array(
				'label'   => __( 'Label', 'zlaark-deals-pro' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Deals tested', 'zlaark-deals-pro' ),
			)
		);

		$repeater->add_control(
			'note',
			array(
				'label' => __( 'Small Note', 'zlaark-deals-pro' ),
				'type'  => Controls_Manager::TEXT,
			)
		);

		$this->add_control(
			'items',
			array(
				'label'       => __( 'Items', 'zlaark-deals-pro' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ prefix }}}{{{ value }}}{{{ suffix }}} — {{{ label }}}',
				'default'     => array(
					array(
						'value'  => 120,
						'suffix' => '+',
						'label'  => __( 'Deals tested', 'zlaark-deals-pro' ),
						'note'   => __( 'Bought with our own money', 'zlaark-deals-pro' ),
					),
					array(
						'value'  => 40,
						'suffix' => '%',
						'label'  => __( 'Average saving', 'zlaark-deals-pro' ),
						'note'   => __( 'Across all active offers', 'zlaark-deals-pro' ),
					),
					array(
						'value'    => 9.4,
						'decimals' => 1,
						'suffix'   => '/10',
						'label'    => __( 'Reader rating', 'zlaark-deals-pro' ),
						'note'     => __( 'From 2,400 responses', 'zlaark-deals-pro' ),
					),
					array(
						'value'  => 15,
						'suffix' => 'yrs',
						'label'  => __( 'Testing experience', 'zlaark-deals-pro' ),
						'note'   => __( 'Since 2010', 'zlaark-deals-pro' ),
					),
				),
			)
		);

		$this->add_responsive_control(
			'columns',
			array(
				'label'          => __( 'Columns', 'zlaark-deals-pro' ),
				'type'           => Controls_Manager::SELECT,
				'default'        => '4',
				'tablet_default' => '2',
				'mobile_default' => '1',
				'separator'      => 'before',
				'options'        => array(
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
				),
				'selectors'      => array(
					'{{WRAPPER}} .zd-stats' => 'grid-template-columns: repeat({{VALUE}}, minmax(0, 1fr));',
				),
			)
		);

		$this->add_responsive_control(
			'gap',
			array(
				'label'      => __( 'Gap', 'zlaark-deals-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 60 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 18 ),
				'selectors'  => array( '{{WRAPPER}} .zd-stats' => 'gap: {{SIZE}}{{UNIT}};' ),
			)
		);

		$this->max_width_control( '{{WRAPPER}} .zd-stats' );

		$this->add_control(
			'duration',
			array(
				'label'      => __( 'Count-up Duration', 'zlaark-deals-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'ms' ),
				'range'      => array( 'ms' => array( 'min' => 400, 'max' => 4000, 'step' => 100 ) ),
				'default'    => array( 'unit' => 'ms', 'size' => 1600 ),
			)
		);

		$this->add_control(
			'orbit',
			array(
				'label'        => __( 'Orbiting Accent Ring', 'zlaark-deals-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);

		$this->end_controls_section();
	}

	private function style_controls() {
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
				'label'     => __( 'Accent Color', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#0b7a4f',
				'selectors' => array( '{{WRAPPER}} .zd-stats' => '--zd-accent: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'accent_2',
			array(
				'label'     => __( 'Accent Color 2', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#065f42',
				'selectors' => array( '{{WRAPPER}} .zd-stats' => '--zd-accent-2: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'card_bg',
			array(
				'label'     => __( 'Card Background', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'separator' => 'before',
				'selectors' => array( '{{WRAPPER}} .zd-stat-card' => 'background-color: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'card_radius',
			array(
				'label'      => __( 'Card Radius', 'zlaark-deals-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 18 ),
				'selectors'  => array( '{{WRAPPER}} .zd-stat-card' => 'border-radius: {{SIZE}}{{UNIT}};' ),
			)
		);

		$this->add_responsive_control(
			'card_padding',
			array(
				'label'      => __( 'Card Padding', 'zlaark-deals-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'default'    => array(
					'top'      => '28',
					'right'    => '24',
					'bottom'   => '28',
					'left'     => '24',
					'unit'     => 'px',
					'isLinked' => false,
				),
				'selectors'  => array(
					'{{WRAPPER}} .zd-stat-card' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'card_shadow',
				'selector' => '{{WRAPPER}} .zd-stat-card',
			)
		);

		$this->add_control(
			'value_color',
			array(
				'label'     => __( 'Number Color', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#0a1310',
				'separator' => 'before',
				'selectors' => array( '{{WRAPPER}} .zd-stat-card__value' => 'color: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'gradient_numbers',
			array(
				'label'        => __( 'Gradient Numbers', 'zlaark-deals-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'description'  => __( 'Fills the numbers with an animated accent gradient.', 'zlaark-deals-pro' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'value_typography',
				'selector' => '{{WRAPPER}} .zd-stat-card__value',
			)
		);

		$this->add_control(
			'label_color',
			array(
				'label'     => __( 'Label Color', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#111827',
				'separator' => 'before',
				'selectors' => array( '{{WRAPPER}} .zd-stat-card__label' => 'color: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'note_color',
			array(
				'label'     => __( 'Note Color', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#5e6c64',
				'selectors' => array( '{{WRAPPER}} .zd-stat-card__note' => 'color: {{VALUE}};' ),
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$s = $this->get_settings_for_display();

		if ( empty( $s['items'] ) || ! is_array( $s['items'] ) ) {
			return;
		}

		$stagger  = isset( $s['reveal_stagger']['size'] ) ? (int) $s['reveal_stagger']['size'] : 90;
		$duration = isset( $s['duration']['size'] ) ? (int) $s['duration']['size'] : 1600;

		$classes = array( 'zd-stats' );
		if ( 'yes' === $s['gradient_numbers'] ) {
			$classes[] = 'zd-stats--gradient';
		}
		?>
		<div class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>"
			data-zd-reveal-root="true" data-zd-stagger="<?php echo esc_attr( $stagger ); ?>">
			<?php foreach ( $s['items'] as $i => $item ) : ?>
				<div class="zd-stat-card zd-reveal"
					data-zd-reveal="<?php echo esc_attr( $s['reveal_effect'] ); ?>"
					style="--zd-i:<?php echo (int) $i; ?>">

					<?php if ( 'yes' === $s['orbit'] ) : ?>
						<span class="zd-stat-card__orbit" aria-hidden="true"></span>
					<?php endif; ?>

					<p class="zd-stat-card__value">
						<?php echo esc_html( $item['prefix'] ); ?><span
							data-zd-count="<?php echo esc_attr( $item['value'] ); ?>"
							data-zd-decimals="<?php echo esc_attr( (int) $item['decimals'] ); ?>"
							data-zd-duration="<?php echo esc_attr( $duration ); ?>">0</span><?php echo esc_html( $item['suffix'] ); ?>
					</p>

					<p class="zd-stat-card__label"><?php echo esc_html( $item['label'] ); ?></p>

					<?php if ( '' !== $item['note'] ) : ?>
						<p class="zd-stat-card__note"><?php echo esc_html( $item['note'] ); ?></p>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>
		<?php
	}
}
