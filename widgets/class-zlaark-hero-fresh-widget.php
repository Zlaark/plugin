<?php
/**
 * Zlaark Hero Fresh — a clean, light hero: headline, description and a
 * single image on either side. No badges, no aurora, no gradients-on-
 * everything — just the content, in one of two flat colour themes
 * (Monochrome + orange, or Green + yellow). Buttons are entirely optional.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;

class Zlaark_Hero_Fresh_Widget extends Zlaark_Widget_Base {

	public function get_name() {
		return 'zlaark_hero_fresh';
	}

	public function get_title() {
		return __( 'Zlaark Hero Fresh', 'zlaark-deals-pro' );
	}

	public function get_icon() {
		return 'eicon-slider-full-screen';
	}

	public function get_keywords() {
		return array( 'hero', 'banner', 'header', 'cta', 'light', 'clean', 'zlaark' );
	}

	protected function register_controls() {
		$this->content_controls();
		$this->buttons_controls();
		$this->media_controls();
		$this->layout_controls();
		$this->theme_controls();
		$this->style_controls();
		$this->motion_section();
	}

	/* ---------------------------------------------------------------- content */

	private function content_controls() {
		$this->start_controls_section(
			'section_content',
			array( 'label' => __( 'Content', 'zlaark-deals-pro' ) )
		);

		$this->add_control(
			'eyebrow',
			array(
				'label'       => __( 'Eyebrow Pill (optional)', 'zlaark-deals-pro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'label_block' => true,
				'description' => __( 'Leave empty to hide — the clean layout works without one.', 'zlaark-deals-pro' ),
			)
		);

		$this->add_control(
			'title',
			array(
				'label'       => __( 'Title', 'zlaark-deals-pro' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 3,
				'default'     => __( 'The best tools for a', 'zlaark-deals-pro' ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'title_highlight',
			array(
				'label'       => __( 'Highlighted Words', 'zlaark-deals-pro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'successful online business', 'zlaark-deals-pro' ),
				'label_block' => true,
				'description' => __( 'Appended to the title in the theme accent colour.', 'zlaark-deals-pro' ),
			)
		);

		$this->add_control(
			'title_tag',
			array(
				'label'   => __( 'Title HTML Tag', 'zlaark-deals-pro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'h1',
				'options' => array(
					'h1'  => 'H1',
					'h2'  => 'H2',
					'h3'  => 'H3',
					'div' => 'div',
				),
			)
		);

		$this->add_control(
			'desc_lead',
			array(
				'label'       => __( 'Description Lead-in (optional)', 'zlaark-deals-pro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'label_block' => true,
				'description' => __( 'A short phrase highlighted at the start of the description, e.g. "Life\'s too short for bad software."', 'zlaark-deals-pro' ),
			)
		);

		$this->add_control(
			'description',
			array(
				'label'   => __( 'Description', 'zlaark-deals-pro' ),
				'type'    => Controls_Manager::TEXTAREA,
				'rows'    => 4,
				'default' => __( "We're constantly using, reviewing and comparing every tool on the market, then reporting back — so you can make informed decisions and choose the best option for you.", 'zlaark-deals-pro' ),
			)
		);

		$this->end_controls_section();
	}

	private function buttons_controls() {
		$this->start_controls_section(
			'section_buttons',
			array( 'label' => __( 'Buttons', 'zlaark-deals-pro' ) )
		);

		$this->add_control(
			'show_buttons',
			array(
				'label'        => __( 'Show Buttons', 'zlaark-deals-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'description'  => __( 'Turn off for a pure headline + description + image hero.', 'zlaark-deals-pro' ),
			)
		);

		$this->add_control(
			'primary_text',
			array(
				'label'     => __( 'Primary Button', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Get Started', 'zlaark-deals-pro' ),
				'condition' => array( 'show_buttons' => 'yes' ),
			)
		);

		$this->add_control(
			'primary_link',
			array(
				'label'     => __( 'Primary Link', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::URL,
				'default'   => array( 'url' => '#' ),
				'condition' => array(
					'show_buttons'  => 'yes',
					'primary_text!' => '',
				),
			)
		);

		$this->add_control(
			'secondary_text',
			array(
				'label'     => __( 'Secondary Button (optional)', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => '',
				'separator' => 'before',
				'condition' => array( 'show_buttons' => 'yes' ),
			)
		);

		$this->add_control(
			'secondary_link',
			array(
				'label'     => __( 'Secondary Link', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::URL,
				'condition' => array(
					'show_buttons'    => 'yes',
					'secondary_text!' => '',
				),
			)
		);

		$this->end_controls_section();
	}

	private function media_controls() {
		$this->start_controls_section(
			'section_media',
			array( 'label' => __( 'Media', 'zlaark-deals-pro' ) )
		);

		$this->add_control(
			'image',
			array(
				'label'       => __( 'Image', 'zlaark-deals-pro' ),
				'type'        => Controls_Manager::MEDIA,
				'description' => __( 'Leave empty to keep a reserved, empty frame on this side — handy while you source the graphic.', 'zlaark-deals-pro' ),
			)
		);

		$this->end_controls_section();
	}

	private function layout_controls() {
		$this->start_controls_section(
			'section_layout',
			array( 'label' => __( 'Layout', 'zlaark-deals-pro' ) )
		);

		$this->add_control(
			'media_side',
			array(
				'label'   => __( 'Media Side', 'zlaark-deals-pro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'right',
				'options' => array(
					'right' => __( 'Text left / media right', 'zlaark-deals-pro' ),
					'left'  => __( 'Media left / text right', 'zlaark-deals-pro' ),
					'none'  => __( 'Text only', 'zlaark-deals-pro' ),
				),
			)
		);

		$this->max_width_control( '{{WRAPPER}} .zd-hf__inner', 1480 );

		$this->add_responsive_control(
			'gap',
			array(
				'label'      => __( 'Column Gap', 'zlaark-deals-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 160 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 72 ),
				'selectors'  => array( '{{WRAPPER}} .zd-hf__inner' => 'gap: {{SIZE}}{{UNIT}};' ),
			)
		);

		$this->end_controls_section();
	}

	/* ------------------------------------------------------------------- theme */

	private function theme_controls() {
		$this->start_controls_section(
			'section_theme',
			array(
				'label' => __( 'Colour Theme', 'zlaark-deals-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'color_theme',
			array(
				'label'   => __( 'Theme', 'zlaark-deals-pro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'mono',
				'options' => array(
					'mono'  => __( 'Monochrome — black & orange', 'zlaark-deals-pro' ),
					'green' => __( 'Fresh — green & yellow', 'zlaark-deals-pro' ),
				),
				'description' => __( 'Sets the whole palette in one go. Use the overrides below only if you need a one-off colour.', 'zlaark-deals-pro' ),
			)
		);

		$this->add_control(
			'accent_override',
			array(
				'label'       => __( 'Accent Override', 'zlaark-deals-pro' ),
				'type'        => Controls_Manager::COLOR,
				'separator'   => 'before',
				'selectors'   => array( '{{WRAPPER}} .zd-hf' => '--zd-hf-accent: {{VALUE}};' ),
				'description' => __( 'Leave empty to use the selected theme\'s accent.', 'zlaark-deals-pro' ),
			)
		);

		$this->add_control(
			'accent_2_override',
			array(
				'label'     => __( 'Accent 2 Override', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .zd-hf' => '--zd-hf-accent-2: {{VALUE}};' ),
			)
		);

		$this->end_controls_section();
	}

	/* ------------------------------------------------------------------ style */

	private function style_controls() {
		$this->start_controls_section(
			'section_style_box',
			array(
				'label' => __( 'Section', 'zlaark-deals-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'padding',
			array(
				'label'      => __( 'Padding', 'zlaark-deals-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'default'    => array(
					'top'      => '110',
					'right'    => '40',
					'bottom'   => '110',
					'left'     => '40',
					'unit'     => 'px',
					'isLinked' => false,
				),
				'selectors'  => array(
					'{{WRAPPER}} .zd-hf' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		/* Typography */
		$this->start_controls_section(
			'section_style_text',
			array(
				'label' => __( 'Typography', 'zlaark-deals-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .zd-hf__title',
			)
		);

		$this->add_responsive_control(
			'title_size',
			array(
				'label'      => __( 'Title Size', 'zlaark-deals-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'vw' ),
				'range'      => array(
					'px' => array( 'min' => 26, 'max' => 96 ),
					'vw' => array( 'min' => 2, 'max' => 8, 'step' => 0.1 ),
				),
				'default'    => array( 'unit' => 'px', 'size' => 52 ),
				'selectors'  => array( '{{WRAPPER}} .zd-hf__title' => 'font-size: {{SIZE}}{{UNIT}};' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'desc_typography',
				'selector'  => '{{WRAPPER}} .zd-hf__desc',
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'desc_width',
			array(
				'label'      => __( 'Description Max Width', 'zlaark-deals-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array( 'min' => 240, 'max' => 900 ),
					'%'  => array( 'min' => 20, 'max' => 100 ),
				),
				'default'    => array( 'unit' => 'px', 'size' => 540 ),
				'selectors'  => array( '{{WRAPPER}} .zd-hf__desc' => 'max-width: {{SIZE}}{{UNIT}};' ),
			)
		);

		$this->end_controls_section();

		/* Buttons */
		$this->start_controls_section(
			'section_style_buttons',
			array(
				'label'     => __( 'Buttons', 'zlaark-deals-pro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'show_buttons' => 'yes' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'button_typography',
				'selector' => '{{WRAPPER}} .zd-hf__btn',
			)
		);

		$this->end_controls_section();

		/* Media */
		$this->start_controls_section(
			'section_style_media',
			array(
				'label'     => __( 'Media', 'zlaark-deals-pro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'media_side!' => 'none' ),
			)
		);

		$this->add_responsive_control(
			'media_radius',
			array(
				'label'      => __( 'Frame Radius', 'zlaark-deals-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 60 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 24 ),
				'selectors'  => array( '{{WRAPPER}} .zd-hf__frame' => 'border-radius: {{SIZE}}{{UNIT}};' ),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'media_shadow',
				'selector' => '{{WRAPPER}} .zd-hf__frame',
			)
		);

		$this->end_controls_section();
	}

	private function motion_section() {
		$this->start_controls_section(
			'section_hf_motion',
			array(
				'label' => __( 'Motion (optional)', 'zlaark-deals-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'media_float',
			array(
				'label'        => __( 'Floating Media', 'zlaark-deals-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'condition'    => array( 'media_side!' => 'none' ),
			)
		);

		$this->add_control(
			'media_parallax',
			array(
				'label'        => __( 'Cursor Parallax', 'zlaark-deals-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'condition'    => array( 'media_side!' => 'none' ),
			)
		);

		$this->add_control(
			'magnetic',
			array(
				'label'        => __( 'Magnetic Buttons', 'zlaark-deals-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'condition'    => array( 'show_buttons' => 'yes' ),
			)
		);

		$this->end_controls_section();
	}

	/* ----------------------------------------------------------------- render */

	protected function render() {
		$s    = $this->get_settings_for_display();
		$side = ! empty( $s['media_side'] ) ? $s['media_side'] : 'right';
		$theme = ! empty( $s['color_theme'] ) ? $s['color_theme'] : 'mono';

		$classes = array( 'zd-hf', 'zd-hf--' . $side, 'zd-hf--' . $theme );

		$this->add_render_attribute( 'wrapper', 'class', $classes );

		$has_media_col = ( 'none' !== $side );
		?>
		<div <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
			<div class="zd-hf__inner">
				<div class="zd-hf__content">
					<?php $this->render_eyebrow( $s ); ?>
					<?php $this->render_title( $s ); ?>
					<?php $this->render_description( $s ); ?>
					<?php if ( 'yes' === $s['show_buttons'] ) : ?>
						<?php $this->render_buttons( $s ); ?>
					<?php endif; ?>
				</div>

				<?php if ( $has_media_col ) : ?>
					<?php $this->render_media( $s ); ?>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}

	private function render_eyebrow( $s ) {
		if ( empty( $s['eyebrow'] ) ) {
			return;
		}
		?>
		<p class="zd-hf__eyebrow"><?php echo esc_html( $s['eyebrow'] ); ?></p>
		<?php
	}

	private function render_title( $s ) {
		if ( empty( $s['title'] ) && empty( $s['title_highlight'] ) ) {
			return;
		}

		$tag = ! empty( $s['title_tag'] ) ? $s['title_tag'] : 'h1';

		printf( '<%1$s class="zd-hf__title">', esc_attr( $tag ) );

		if ( ! empty( $s['title'] ) ) {
			echo esc_html( $s['title'] ) . ' ';
		}

		if ( ! empty( $s['title_highlight'] ) ) {
			printf( '<span class="zd-hf__word--accent">%1$s</span>', esc_html( $s['title_highlight'] ) );
		}

		printf( '</%1$s>', esc_attr( $tag ) );
	}

	private function render_description( $s ) {
		if ( empty( $s['description'] ) && empty( $s['desc_lead'] ) ) {
			return;
		}
		?>
		<p class="zd-hf__desc">
			<?php if ( ! empty( $s['desc_lead'] ) ) : ?>
				<mark class="zd-hf__mark"><?php echo esc_html( $s['desc_lead'] ); ?></mark>
			<?php endif; ?>
			<?php echo nl2br( esc_html( $s['description'] ) ); ?>
		</p>
		<?php
	}

	private function render_buttons( $s ) {
		if ( empty( $s['primary_text'] ) && empty( $s['secondary_text'] ) ) {
			return;
		}
		?>
		<div class="zd-hf__actions">
			<?php
			$this->render_button( 'solid', $s['primary_text'], $s['primary_link'], $s );
			$this->render_button( 'outline', $s['secondary_text'], $s['secondary_link'], $s );
			?>
		</div>
		<?php
	}

	private function render_button( $variant, $text, $link, $s ) {
		if ( empty( $text ) ) {
			return;
		}

		$key     = 'btn_' . $variant;
		$classes = array( 'zd-hf__btn', 'zd-hf__btn--' . $variant );
		if ( 'yes' === $s['magnetic'] ) {
			$classes[] = 'zd-magnetic';
		}

		$this->add_render_attribute( $key, 'class', $classes );
		if ( ! empty( $link['url'] ) ) {
			$this->add_link_attributes( $key, $link );
		}
		?>
		<a <?php $this->print_render_attribute_string( $key ); ?>>
			<span class="zd-hf__btn-label"><?php echo esc_html( $text ); ?></span>
		</a>
		<?php
	}

	private function render_media( $s ) {
		$has_image = ! empty( $s['image']['url'] );

		$classes = array( 'zd-hf__media' );
		if ( $has_image && 'yes' === $s['media_float'] ) {
			$classes[] = 'zd-hf__media--float';
		}
		if ( $has_image && 'yes' === $s['media_parallax'] ) {
			$classes[] = 'zd-parallax';
		}
		?>
		<div class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
			<?php if ( $has_image ) : ?>
				<div class="zd-hf__frame">
					<img src="<?php echo esc_url( $s['image']['url'] ); ?>"
						alt="<?php echo esc_attr( zlaark_deals_media_alt( $s['image'] ) ); ?>" />
				</div>
			<?php else : ?>
				<div class="zd-hf__frame zd-hf__frame--empty" aria-hidden="true">
					<svg viewBox="0 0 24 24" width="34" height="34" fill="none">
						<rect x="3" y="4" width="18" height="16" rx="2.5" stroke="currentColor" stroke-width="1.6" />
						<circle cx="8.5" cy="9.5" r="1.6" stroke="currentColor" stroke-width="1.6" />
						<path d="M4 16.5l5-4.5 3.5 3 3.5-4 4 5.5" stroke="currentColor" stroke-width="1.6"
							stroke-linecap="round" stroke-linejoin="round" />
					</svg>
					<span><?php esc_html_e( 'Image space — add your graphic here', 'zlaark-deals-pro' ); ?></span>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}
}
