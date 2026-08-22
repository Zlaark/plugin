<?php
/**
 * Zlaark Review Byline - who tested this, when, and when it was last checked.
 *
 * Every deal already stores a reviewer, a tested date and a last-verified date;
 * until now nothing but the deal template and the schema markup read them. On a
 * site whose whole argument is "we bought it and measured it", the person who
 * did the measuring belongs on the page, not only in the JSON-LD.
 *
 * @package Zlaark_Deals_Pro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

class Zlaark_Byline_Widget extends Zlaark_Query_Widget_Base {

	public function get_name() {
		return 'zlaark_byline';
	}

	public function get_title() {
		return __( 'Zlaark Review Byline', 'zlaark-deals-pro' );
	}

	public function get_icon() {
		return 'eicon-user-circle-o';
	}

	public function get_keywords() {
		return array( 'byline', 'author', 'reviewer', 'tested', 'verified', 'eeat', 'zlaark' );
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

		$this->add_control(
			'photo',
			array(
				'label'       => __( 'Reviewer Photo', 'zlaark-deals-pro' ),
				'type'        => Controls_Manager::MEDIA,
				'separator'   => 'before',
				'description' => __( 'Optional. Falls back to the reviewer\'s initial on a tinted tile.', 'zlaark-deals-pro' ),
			)
		);

		$this->add_control(
			'role',
			array(
				'label'       => __( 'Reviewer Role', 'zlaark-deals-pro' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => __( 'Ecommerce editor', 'zlaark-deals-pro' ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'bio',
			array(
				'label'       => __( 'Short Bio', 'zlaark-deals-pro' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 3,
				'label_block' => true,
				'description' => __( 'One or two sentences on why this person is worth believing.', 'zlaark-deals-pro' ),
			)
		);

		$this->add_control(
			'method_text',
			array(
				'label'     => __( 'Method Link Text', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'How we test', 'zlaark-deals-pro' ),
				'separator' => 'before',
			)
		);

		$this->add_control(
			'method_url',
			array(
				'label'       => __( 'Method Link', 'zlaark-deals-pro' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => 'https://',
				'description' => __( 'Leave empty to hide the link. A byline that shows its working beats one that asks to be trusted.', 'zlaark-deals-pro' ),
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
			'boxed',
			array(
				'label'        => __( 'Boxed', 'zlaark-deals-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'description'  => __( 'Off gives a bare byline line, for sitting directly under a heading.', 'zlaark-deals-pro' ),
			)
		);

		$this->add_control(
			'accent',
			array(
				'label'     => __( 'Accent', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .zd-byline' => '--zd-accent: {{VALUE}};' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'name_typography',
				'selector' => '{{WRAPPER}} .zd-byline__name',
			)
		);

		$this->max_width_control( '{{WRAPPER}} .zd-byline', 760 );

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

		$name = $deal['reviewer'];

		/*
		 * With no reviewer there is no byline to write. Printing "Tested March
		 * 2026" with nobody's name on it is weaker than printing nothing - it
		 * reads as a claim the site is unwilling to sign.
		 */
		if ( '' === $name ) {
			if ( $this->is_editor() ) {
				$this->render_empty_notice();
			}
			return;
		}

		$facts = array();

		if ( '' !== $deal['tested_date'] ) {
			$facts[] = array(
				'label' => __( 'Tested', 'zlaark-deals-pro' ),
				'value' => date_i18n( 'F Y', strtotime( $deal['tested_date'] ) ),
				'time'  => $deal['tested_date'],
			);
		}

		if ( '' !== $deal['last_verified'] ) {
			$facts[] = array(
				'label' => __( 'Prices re-checked', 'zlaark-deals-pro' ),
				'value' => date_i18n( 'j F Y', strtotime( $deal['last_verified'] ) ),
				'time'  => $deal['last_verified'],
			);
		}

		if ( ! empty( $deal['scores'] ) ) {
			$facts[] = array(
				'label' => __( 'Measured on', 'zlaark-deals-pro' ),
				'value' => sprintf(
					/* translators: %d: number of scored criteria. */
					_n( '%d criterion', '%d criteria', count( $deal['scores'] ), 'zlaark-deals-pro' ),
					count( $deal['scores'] )
				),
				'time'  => '',
			);
		}

		$method_url = ! empty( $s['method_url']['url'] ) ? $s['method_url']['url'] : '';
		$classes    = 'zd-byline' . ( 'yes' === $s['boxed'] ? ' zd-byline--boxed' : '' );
		?>
		<div class="<?php echo esc_attr( $classes ); ?> zd-reveal"
			data-zd-reveal="<?php echo esc_attr( $s['reveal_effect'] ); ?>">

			<?php if ( ! empty( $s['photo']['url'] ) ) : ?>
				<img class="zd-byline__photo" loading="lazy" alt=""
					src="<?php echo esc_url( $s['photo']['url'] ); ?>" />
			<?php else : ?>
				<span class="zd-byline__photo zd-byline__photo--initial" aria-hidden="true">
					<?php echo esc_html( mb_substr( $name, 0, 1 ) ); ?>
				</span>
			<?php endif; ?>

			<div class="zd-byline__body">
				<p class="zd-byline__line">
					<span class="zd-byline__by"><?php esc_html_e( 'Tested and written by', 'zlaark-deals-pro' ); ?></span>
					<b class="zd-byline__name"><?php echo esc_html( $name ); ?></b>
					<?php if ( '' !== $s['role'] ) : ?>
						<span class="zd-byline__role"><?php echo esc_html( $s['role'] ); ?></span>
					<?php endif; ?>
				</p>

				<?php if ( '' !== $s['bio'] ) : ?>
					<p class="zd-byline__bio"><?php echo esc_html( $s['bio'] ); ?></p>
				<?php endif; ?>

				<?php if ( ! empty( $facts ) ) : ?>
					<ul class="zd-byline__facts">
						<?php foreach ( $facts as $fact ) : ?>
							<li class="zd-byline__fact">
								<span class="zd-byline__flabel"><?php echo esc_html( $fact['label'] ); ?></span>
								<?php if ( '' !== $fact['time'] ) : ?>
									<time class="zd-byline__fvalue" datetime="<?php echo esc_attr( $fact['time'] ); ?>">
										<?php echo esc_html( $fact['value'] ); ?>
									</time>
								<?php else : ?>
									<span class="zd-byline__fvalue"><?php echo esc_html( $fact['value'] ); ?></span>
								<?php endif; ?>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>

				<?php if ( '' !== $method_url && '' !== $s['method_text'] ) : ?>
					<a class="zd-reviewlink zd-byline__method" href="<?php echo esc_url( $method_url ); ?>"
						<?php echo ! empty( $s['method_url']['is_external'] ) ? 'target="_blank" rel="noopener"' : ''; ?>>
						<span><?php echo esc_html( $s['method_text'] ); ?></span>
						<svg viewBox="0 0 16 16" width="14" height="14" fill="none" aria-hidden="true">
							<path d="M2 8h11M9 4l4 4-4 4" stroke="currentColor" stroke-width="2"
								stroke-linecap="round" stroke-linejoin="round" />
						</svg>
					</a>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}
}
