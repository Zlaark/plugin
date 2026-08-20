<?php
/**
 * Zlaark Marquee - an infinitely scrolling logo/trust strip. Logos can be
 * added manually or pulled straight from a deal category, and the track
 * pauses when the pointer rests on it.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Group_Control_Typography;
use Elementor\Utils;

class Zlaark_Marquee_Widget extends Zlaark_Widget_Base {

	public function get_name() {
		return 'zlaark_marquee';
	}

	public function get_title() {
		return __( 'Zlaark Logo Marquee', 'zlaark-deals-pro' );
	}

	public function get_icon() {
		return 'eicon-slider-push';
	}

	public function get_keywords() {
		return array( 'marquee', 'logos', 'ticker', 'carousel', 'trust', 'zlaark' );
	}

	protected function register_controls() {
		$this->content_controls();
		$this->style_controls();
		$this->animation_controls( false );
	}

	private function content_controls() {
		$this->start_controls_section(
			'section_content',
			array( 'label' => __( 'Content', 'zlaark-deals-pro' ) )
		);

		$this->add_control(
			'label',
			array(
				'label'       => __( 'Strip Label', 'zlaark-deals-pro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'As featured in…', 'zlaark-deals-pro' ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'label_position',
			array(
				'label'     => __( 'Label Position', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'above',
				'condition' => array( 'label!' => '' ),
				'options'   => array(
					'above' => __( 'Above the logos', 'zlaark-deals-pro' ),
					'left'  => __( 'Beside the logos', 'zlaark-deals-pro' ),
				),
			)
		);

		$this->add_responsive_control(
			'label_align',
			array(
				'label'     => __( 'Label Alignment', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::CHOOSE,
				'default'   => 'left',
				'condition' => array(
					'label!'         => '',
					'label_position' => 'above',
				),
				'options'   => array(
					'left'   => array(
						'title' => __( 'Left', 'zlaark-deals-pro' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'zlaark-deals-pro' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right'  => array(
						'title' => __( 'Right', 'zlaark-deals-pro' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'selectors' => array( '{{WRAPPER}} .zd-marquee__label' => 'text-align: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'source',
			array(
				'label'   => __( 'Logo Source', 'zlaark-deals-pro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'deals',
				'options' => array(
					'deals'  => __( 'Deals from a category', 'zlaark-deals-pro' ),
					'manual' => __( 'Manual list', 'zlaark-deals-pro' ),
				),
			)
		);

		$this->add_control(
			'category',
			array(
				'label'       => __( 'Category', 'zlaark-deals-pro' ),
				'type'        => Controls_Manager::SELECT2,
				'multiple'    => true,
				'label_block' => true,
				'options'     => Zlaark_Deals_Post_Type::get_category_options(),
				'default'     => array(),
				'description' => __( 'Leave empty to pull from every category.', 'zlaark-deals-pro' ),
				'condition'   => array( 'source' => 'deals' ),
			)
		);

		$this->add_control(
			'limit',
			array(
				'label'     => __( 'Number of Logos', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 2,
				'max'       => 40,
				'default'   => 12,
				'condition' => array( 'source' => 'deals' ),
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'image',
			array(
				'label'   => __( 'Logo', 'zlaark-deals-pro' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => array( 'url' => Utils::get_placeholder_image_src() ),
			)
		);

		$repeater->add_control(
			'name',
			array(
				'label' => __( 'Name (alt text)', 'zlaark-deals-pro' ),
				'type'  => Controls_Manager::TEXT,
			)
		);

		$repeater->add_control(
			'link',
			array(
				'label' => __( 'Link', 'zlaark-deals-pro' ),
				'type'  => Controls_Manager::URL,
			)
		);

		$this->add_control(
			'logos',
			array(
				'label'       => __( 'Logos', 'zlaark-deals-pro' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ name }}}',
				'condition'   => array( 'source' => 'manual' ),
				'default'     => array(
					array( 'image' => array( 'url' => Utils::get_placeholder_image_src() ) ),
					array( 'image' => array( 'url' => Utils::get_placeholder_image_src() ) ),
					array( 'image' => array( 'url' => Utils::get_placeholder_image_src() ) ),
					array( 'image' => array( 'url' => Utils::get_placeholder_image_src() ) ),
				),
			)
		);

		$this->add_control(
			'sizing_mode',
			array(
				'label'       => __( 'Logo Sizing', 'zlaark-deals-pro' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'fit',
				'separator'   => 'before',
				'options'     => array(
					'fit'  => __( 'Fit a number per view', 'zlaark-deals-pro' ),
					'auto' => __( 'Natural widths', 'zlaark-deals-pro' ),
				),
				'description' => __( '"Fit a number per view" gives every logo an equal slot so exactly that many fill the strip. "Natural widths" lets each logo take only the room it needs.', 'zlaark-deals-pro' ),
			)
		);

		$this->add_responsive_control(
			'per_view',
			array(
				'label'          => __( 'Logos Per View', 'zlaark-deals-pro' ),
				'type'           => Controls_Manager::SLIDER,
				'range'          => array( 'px' => array( 'min' => 1, 'max' => 10, 'step' => 1 ) ),
				'default'        => array( 'size' => 5 ),
				'tablet_default' => array( 'size' => 3 ),
				'mobile_default' => array( 'size' => 2 ),
				'condition'      => array( 'sizing_mode' => 'fit' ),
				// Published as a custom property so Elementor's own breakpoints
				// decide the value and the script just reads what's in effect.
				'selectors'      => array( '{{WRAPPER}} .zd-marquee' => '--zd-per: {{SIZE}};' ),
			)
		);

		$this->add_control(
			'direction',
			array(
				'label'     => __( 'Direction', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'left',
				'separator' => 'before',
				'options'   => array(
					'left'  => __( 'Right to left', 'zlaark-deals-pro' ),
					'right' => __( 'Left to right', 'zlaark-deals-pro' ),
				),
			)
		);

		$this->add_control(
			'speed',
			array(
				'label'      => __( 'Speed', 'zlaark-deals-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 's' ),
				'range'      => array( 's' => array( 'min' => 8, 'max' => 90, 'step' => 1 ) ),
				'default'    => array( 'unit' => 's', 'size' => 32 ),
				'selectors'  => array( '{{WRAPPER}} .zd-marquee' => '--zd-marquee-speed: {{SIZE}}s;' ),
				'description' => __( 'Seconds for one full loop - higher is slower.', 'zlaark-deals-pro' ),
			)
		);

		$this->add_control(
			'pause_hover',
			array(
				'label'        => __( 'Pause on Hover', 'zlaark-deals-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'grayscale',
			array(
				'label'        => __( 'Grayscale Until Hover', 'zlaark-deals-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'fade_edges',
			array(
				'label'        => __( 'Fade the Edges', 'zlaark-deals-pro' ),
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
			'label_color',
			array(
				'label'     => __( 'Label Color', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#9ca3af',
				'selectors' => array( '{{WRAPPER}} .zd-marquee__label' => 'color: {{VALUE}};' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'label_typography',
				'selector' => '{{WRAPPER}} .zd-marquee__label',
			)
		);

		$this->add_responsive_control(
			'logo_height',
			array(
				'label'      => __( 'Logo Height', 'zlaark-deals-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 16, 'max' => 220 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 58 ),
				'separator'  => 'before',
				'selectors'  => array( '{{WRAPPER}} .zd-marquee__item img' => 'height: {{SIZE}}{{UNIT}};' ),
			)
		);

		$this->add_responsive_control(
			'logo_gap',
			array(
				'label'      => __( 'Logo Gap', 'zlaark-deals-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 8, 'max' => 120 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 56 ),
				'selectors'  => array( '{{WRAPPER}} .zd-marquee__track' => 'gap: {{SIZE}}{{UNIT}};' ),
			)
		);

		$this->add_control(
			'logo_opacity',
			array(
				'label'     => __( 'Logo Opacity', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'separator' => 'before',
				'range'     => array( 'px' => array( 'min' => 0.1, 'max' => 1, 'step' => 0.05 ) ),
				'default'   => array( 'size' => 0.55 ),
				'selectors' => array( '{{WRAPPER}} .zd-marquee__item' => 'opacity: {{SIZE}};' ),
			)
		);

		$this->add_control(
			'fade_width',
			array(
				'label'      => __( 'Edge Fade Width', 'zlaark-deals-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( '%' ),
				'range'      => array( '%' => array( 'min' => 0, 'max' => 30 ) ),
				'default'    => array( 'unit' => '%', 'size' => 10 ),
				'condition'  => array( 'fade_edges' => 'yes' ),
				'selectors'  => array( '{{WRAPPER}} .zd-marquee' => '--zd-fade: {{SIZE}}{{UNIT}};' ),
			)
		);

		$this->add_control(
			'bg',
			array(
				'label'     => __( 'Strip Background', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::COLOR,
				'separator' => 'before',
				'selectors' => array( '{{WRAPPER}} .zd-marquee' => 'background-color: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'radius',
			array(
				'label'      => __( 'Radius', 'zlaark-deals-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
				'selectors'  => array( '{{WRAPPER}} .zd-marquee' => 'border-radius: {{SIZE}}{{UNIT}};' ),
			)
		);

		$this->add_responsive_control(
			'strip_padding',
			array(
				'label'      => __( 'Padding', 'zlaark-deals-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'default'    => array(
					'top'      => '28',
					'right'    => '0',
					'bottom'   => '28',
					'left'     => '0',
					'unit'     => 'px',
					'isLinked' => false,
				),
				'selectors'  => array(
					'{{WRAPPER}} .zd-marquee' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/* ----------------------------------------------------------------- render */

	/** @return array List of [ 'html' => <img markup>, 'url' => string ]. */
	private function collect_items( $s ) {
		$items = array();

		if ( 'manual' === $s['source'] ) {
			foreach ( (array) $s['logos'] as $logo ) {
				if ( empty( $logo['image']['url'] ) ) {
					continue;
				}
				$items[] = array(
					'html' => sprintf(
						'<img src="%1$s" alt="%2$s" loading="lazy" />',
						esc_url( $logo['image']['url'] ),
						esc_attr( isset( $logo['name'] ) ? $logo['name'] : '' )
					),
					'url'  => ! empty( $logo['link']['url'] ) ? $logo['link']['url'] : '',
				);
			}
			return $items;
		}

		$args = array(
			'post_type'           => ZLAARK_DEALS_CPT,
			'post_status'         => 'publish',
			'posts_per_page'      => ! empty( $s['limit'] ) ? (int) $s['limit'] : 12,
			'orderby'             => 'menu_order title',
			'order'               => 'ASC',
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
		);

		$categories = array_values( array_filter( array_map( 'intval', (array) $s['category'] ) ) );
		if ( ! empty( $categories ) ) {
			$args['tax_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery
				array(
					'taxonomy' => ZLAARK_DEALS_TAX,
					'field'    => 'term_id',
					'terms'    => $categories,
				),
			);
		}

		$query = new WP_Query( $args );
		while ( $query->have_posts() ) {
			$query->the_post();
			$deal = Zlaark_Deals_Meta::get_deal_data( get_post() );
			if ( ! $deal['image_id'] ) {
				continue;
			}
			$items[] = array(
				'html' => wp_get_attachment_image(
					$deal['image_id'],
					'medium',
					false,
					array( 'alt' => $deal['title'], 'loading' => 'lazy' )
				),
				'url'  => '' !== $deal['button_url'] ? $deal['button_url'] : '',
			);
		}
		wp_reset_postdata();

		return $items;
	}

	protected function render() {
		$s     = $this->get_settings_for_display();
		$items = $this->collect_items( $s );

		if ( empty( $items ) ) {
			if ( $this->is_editor() ) {
				echo '<div class="zd-empty">'
					. esc_html__( 'No logos to show. Add deals with images, or switch the source to a manual list.', 'zlaark-deals-pro' )
					. '</div>';
			}
			return;
		}

		$classes = array(
			'zd-marquee',
			'zd-marquee--' . $s['direction'],
			'zd-marquee--label-' . ( '' !== $s['label'] ? $s['label_position'] : 'none' ),
			'fit' === $s['sizing_mode'] ? 'zd-marquee--fit' : 'zd-marquee--auto',
		);
		foreach ( array(
			'pause_hover' => 'zd-marquee--pause',
			'grayscale'   => 'zd-marquee--gray',
			'fade_edges'  => 'zd-marquee--fade',
		) as $flag => $class ) {
			if ( 'yes' === $s[ $flag ] ) {
				$classes[] = $class;
			}
		}
		?>
		<div class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
			<?php if ( '' !== $s['label'] ) : ?>
				<p class="zd-marquee__label"><?php echo esc_html( $s['label'] ); ?></p>
			<?php endif; ?>

			<div class="zd-marquee__viewport">
				<?php
				// Two identical tracks: the first scrolls fully out while the
				// second takes its place, so the loop has no seam. If the logos
				// don't fill the viewport the script clones more into each.
				for ( $copy = 0; $copy < 2; $copy++ ) :
					?>
					<div class="zd-marquee__track" <?php echo 1 === $copy ? 'aria-hidden="true"' : ''; ?>>
						<?php foreach ( $items as $item ) : ?>
							<?php if ( '' !== $item['url'] ) : ?>
								<a class="zd-marquee__item" href="<?php echo esc_url( $item['url'] ); ?>" rel="nofollow sponsored noopener">
									<?php echo $item['html']; // phpcs:ignore WordPress.Security.EscapeOutput ?>
								</a>
							<?php else : ?>
								<span class="zd-marquee__item">
									<?php echo $item['html']; // phpcs:ignore WordPress.Security.EscapeOutput ?>
								</span>
							<?php endif; ?>
						<?php endforeach; ?>
					</div>
				<?php endfor; ?>
			</div>
		</div>
		<?php
	}
}
