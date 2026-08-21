<?php

namespace {
/**
 * Minimal Elementor + WordPress stub, just enough to instantiate every Zlaark
 * widget and run register_controls() the way the editor bootstrap does.
 *
 * Any fatal, warning or notice raised here would corrupt the editor's JSON
 * config on a real site — which is exactly what makes Elementor hang on its
 * loading screen.
 */

define( 'ABSPATH', __DIR__ . '/' );
define( 'ZLAARK_DEALS_VERSION', '3.6.0' );
define( 'ZLAARK_DEALS_FILE', __DIR__ . '/x.php' );
define( 'ZLAARK_DEALS_PATH', dirname( __DIR__ ) . '/' );
define( 'ZLAARK_DEALS_URL', 'https://example.com/wp-content/plugins/zlaark/' );
define( 'ZLAARK_DEALS_CPT', 'zlaark_deal' );
define( 'ZLAARK_DEALS_TAX', 'zlaark_deal_cat' );
define( 'DAY_IN_SECONDS', 86400 );

$GLOBALS['zd_calls'] = array();

/* ------------------------------------------------------------- WP stubs -- */
function __( $t, $d = null ) { return $t; }
function esc_html__( $t, $d = null ) { return $t; }
function esc_attr__( $t, $d = null ) { return $t; }
function esc_html_e( $t, $d = null ) { echo $t; }
function esc_attr_e( $t, $d = null ) { echo $t; }
function _n( $s, $p, $n, $d = null ) { return 1 === $n ? $s : $p; }
function esc_html( $t ) { return htmlspecialchars( (string) $t, ENT_QUOTES ); }
function esc_attr( $t ) { return htmlspecialchars( (string) $t, ENT_QUOTES ); }
function esc_url( $t ) { return (string) $t; }
function esc_url_raw( $t ) { return (string) $t; }
function esc_textarea( $t ) { return htmlspecialchars( (string) $t, ENT_QUOTES ); }
function wp_kses_post( $t ) { return $t; }
function wpautop( $t, $br = true ) { return '<p>' . $t . '</p>'; }
function shortcode_unautop( $t ) { return $t; }
function sanitize_text_field( $t ) { return trim( strip_tags( (string) $t ) ); }
function sanitize_textarea_field( $t ) { return trim( strip_tags( (string) $t ) ); }
function sanitize_key( $t ) { return preg_replace( '/[^a-z0-9_\-]/', '', strtolower( (string) $t ) ); }
function sanitize_title( $t ) { return strtolower( preg_replace( '/[^A-Za-z0-9\-]+/', '-', (string) $t ) ); }
function absint( $v ) { return abs( (int) $v ); }
function wp_parse_args( $a, $d = array() ) { return array_merge( (array) $d, (array) $a ); }
function in_the_loop() { return true; }
function is_main_query() { return true; }
function wp_unslash( $v ) { return is_string( $v ) ? stripslashes( $v ) : $v; }
function add_action( $h, $c, $p = 10, $a = 1 ) { $GLOBALS['zd_calls'][] = "action:$h"; }
function add_filter( $h, $c, $p = 10, $a = 1 ) { $GLOBALS['zd_calls'][] = "filter:$h"; }
function apply_filters( $h, $v ) { return $v; }
function do_shortcode( $s ) { return $s; }
function current_time( $f ) { return gmdate( $f ); }
function number_format_i18n( $n, $d = 0 ) { return number_format( (float) $n, $d ); }
function date_i18n( $f, $ts = false ) { return gmdate( $f, false === $ts ? time() : $ts ); }
function get_bloginfo( $x ) { return 'Test'; }
function home_url() { return 'https://example.com'; }
function is_admin() { return true; }
function wp_count_posts( $t = 'post' ) { $o = new stdClass(); $o->publish = 102; $o->draft = 0; return $o; }
$GLOBALS['zd_options'] = array();
function get_option( $k, $d = false ) {
	if ( array_key_exists( $k, $GLOBALS['zd_options'] ) ) { return $GLOBALS['zd_options'][ $k ]; }
	// Date-format lookups are the only ones the widgets make; keep the old answer.
	return ( false === $d ) ? 'j F Y' : $d;
}
function update_option( $k, $v, $a = null ) { $GLOBALS['zd_options'][ $k ] = $v; return true; }
function delete_option( $k ) { unset( $GLOBALS['zd_options'][ $k ] ); return true; }
function wp_list_pluck( $a, $f ) { return array_map( function ( $x ) use ( $f ) { return is_object( $x ) ? $x->$f : $x[ $f ]; }, $a ); }
function wp_get_attachment_image( $id, $s = 'full', $i = false, $a = array() ) { return '<img src="x.png" alt="">'; }
function wp_get_attachment_image_url( $id, $s = 'full' ) { return 'https://example.com/x.png'; }
function get_post_meta( $id, $k = '', $s = false ) { return ''; }
function get_permalink( $p = 0 ) { return 'https://example.com/deal'; }
function get_the_title( $p = 0 ) { return 'Deal'; }
function get_the_ID() { return 1; }
function get_post_type( $p = null ) { return ZLAARK_DEALS_CPT; }
function has_post_thumbnail( $id ) { return false; }
function get_post_thumbnail_id( $id ) { return 0; }
function wp_get_post_terms( $id, $tax, $args = array() ) { return array(); }
function is_singular( $t = '' ) { return false; }
function get_queried_object_id() { return 0; }
function wp_json_encode( $d, $f = 0 ) { return json_encode( $d, $f ); }
function wp_strip_all_tags( $t ) { return strip_tags( (string) $t ); }
function mb_substr_safe( $s, $a, $b ) { return substr( $s, $a, $b ); }

/* ---- editorial article source (reviews / comparisons strips) ---- */
function get_post_types( $args = array(), $output = 'names' ) {
	$GLOBALS['zd_calls'][] = 'get_post_types';
	// One public custom type, so the Content Type picker has a real choice.
	$obj         = new stdClass();
	$obj->labels = (object) array( 'name' => 'Reviews' );
	return ( 'objects' === $output ) ? array( 'zd_review' => $obj ) : array( 'zd_review' );
}
function post_type_exists( $t ) { return in_array( $t, array( 'post', 'zd_review', ZLAARK_DEALS_CPT ), true ); }
function get_object_taxonomies( $type, $output = 'names' ) {
	$tax = (object) array( 'public' => true, 'hierarchical' => true );
	return ( 'objects' === $output ) ? array( 'category' => $tax ) : array( 'category' );
}
function get_terms( $args = array() ) {
	$GLOBALS['zd_calls'][] = 'get_terms';
	return array(
		(object) array( 'term_id' => 4, 'name' => 'Ecommerce', 'slug' => 'ecommerce', 'count' => 6 ),
		(object) array( 'term_id' => 5, 'name' => 'AI Store Builders', 'slug' => 'ai-store-builders', 'count' => 3 ),
	);
}
function has_excerpt( $p = null ) { return false; }
function get_the_excerpt( $p = null ) { return 'An excerpt.'; }
function strip_shortcodes( $c ) { return (string) $c; }
function wp_trim_words( $text, $n = 55, $more = null ) {
	$words = preg_split( '/\s+/', trim( (string) $text ) );
	return ( count( $words ) <= $n )
		? implode( ' ', $words )
		: implode( ' ', array_slice( $words, 0, $n ) ) . ( null === $more ? '…' : $more );
}
function get_the_date( $format = '', $p = null ) { return '21 August 2026'; }

/** The call the harness is really interested in. */
function get_posts( $args = array() ) {
	$GLOBALS['zd_calls'][] = 'get_posts';
	return array();
}
function get_post( $p = null ) { return isset( $GLOBALS['zd_current'] ) ? new ZD_Fake_Post( $GLOBALS['zd_current'] ) : null; }
function wp_reset_postdata() {}
function wp_get_nav_menus( $a = array() ) { return array(); }
function is_wp_error( $t ) { return false; }
function get_term_link( $t ) { return 'https://example.com/cat'; }
// WordPress polyfills these in wp-includes/compat.php when mbstring is absent,
// so the plugin may use them freely; the stub has to provide them too.
if ( ! function_exists( 'mb_substr' ) ) {
	function mb_substr( $str, $start, $len = null, $enc = null ) {
		return null === $len ? substr( (string) $str, $start ) : substr( (string) $str, $start, $len );
	}
}
if ( ! function_exists( 'mb_strlen' ) ) {
	function mb_strlen( $str, $enc = null ) { return strlen( (string) $str ); }
}
function get_term( $t, $tax = '' ) { return null; }
function wp_nav_menu( $a = array() ) { return ''; }
function wp_get_nav_menu_items( $m, $a = array() ) { return array(); }

class WP_Query {
	public $posts = array();
	private $i = 0;
	public function __construct( $args = array() ) {
		$GLOBALS['zd_calls'][] = 'WP_Query';
		$n = isset( $GLOBALS['zd_fake_posts'] ) ? $GLOBALS['zd_fake_posts'] : 0;

		/*
		 * Deal widgets walk the loop with the_post() and only ever use the id,
		 * so they get ints. The editorial strips read $query->posts directly,
		 * the way WordPress hands back WP_Post objects, so they get objects.
		 */
		$type = isset( $args['post_type'] ) ? $args['post_type'] : ZLAARK_DEALS_CPT;
		$editorial = ( ZLAARK_DEALS_CPT !== $type );

		for ( $k = 1; $k <= $n; $k++ ) {
			$this->posts[] = $editorial ? new ZD_Fake_Post( $k ) : $k;
		}
	}
	public function have_posts() { return $this->i < count( $this->posts ); }
	public function the_post() { $GLOBALS['zd_current'] = $this->posts[ $this->i ]; $this->i++; }
}

class Zlaark_Deals_Post_Type {
	public static function get_category_options() {
		$GLOBALS['zd_calls'][] = 'get_category_options';
		return array( 10 => 'Hosting', 11 => 'Tools' );
	}
}
}

namespace Elementor {

class Controls_Manager {
	const TEXT = 'text';
	const TEXTAREA = 'textarea';
	const NUMBER = 'number';
	const SELECT = 'select';
	const SELECT2 = 'select2';
	const SWITCHER = 'switcher';
	const SLIDER = 'slider';
	const COLOR = 'color';
	const MEDIA = 'media';
	const URL = 'url';
	const REPEATER = 'repeater';
	const RAW_HTML = 'raw_html';
	const HEADING = 'heading';
	const DIVIDER = 'divider';
	const ICONS = 'icons';
	const CHOOSE = 'choose';
	const HIDDEN = 'hidden';
	const TAB_CONTENT = 'content';
	const TAB_STYLE = 'style';
	const TAB_ADVANCED = 'advanced';
	const DIMENSIONS = 'dimensions';
	const FONT = 'font';
	const IMAGE_DIMENSIONS = 'image_dimensions';
	const GALLERY = 'gallery';
	const WYSIWYG = 'wysiwyg';
	const CODE = 'code';
	const POPOVER_TOGGLE = 'popover_toggle';
	const ALERT = 'alert';
}

class Utils {
	public static function get_placeholder_image_src() { return 'https://example.com/placeholder.png'; }
	public static function is_empty( $v ) { return empty( $v ); }
}

class Group_Control_Typography { public static function get_type() { return 'typography'; } }
class Group_Control_Border     { public static function get_type() { return 'border'; } }
class Group_Control_Box_Shadow { public static function get_type() { return 'box-shadow'; } }
class Group_Control_Background { public static function get_type() { return 'background'; } }
class Group_Control_Text_Shadow{ public static function get_type() { return 'text-shadow'; } }
class Group_Control_Image_Size { public static function get_type() { return 'image-size'; } }
class Icons_Manager { public static function render_icon( $i, $a = array() ) { echo '<i></i>'; } }

class Repeater {
	private $controls = array();
	public function add_control( $id, $args = array() ) { $this->controls[ $id ] = $args; return true; }
	public function add_responsive_control( $id, $args = array() ) { return $this->add_control( $id, $args ); }
	public function get_controls() { return $this->controls; }
}

abstract class Widget_Base {
	public $zd_controls = array();
	public $zd_sections = array();
	protected $open = null;

	public function __construct( $data = array(), $args = null ) {}

	public function start_controls_section( $id, $args = array() ) {
		if ( null !== $this->open ) {
			throw new \Exception( "section '$id' opened while '{$this->open}' is still open" );
		}
		if ( isset( $this->zd_sections[ $id ] ) ) {
			throw new \Exception( "duplicate section id '$id'" );
		}
		$this->zd_sections[ $id ] = true;
		$this->open = $id;
	}

	public function end_controls_section() {
		if ( null === $this->open ) {
			throw new \Exception( 'end_controls_section() with no section open' );
		}
		$this->open = null;
	}

	public function add_control( $id, $args = array(), $options = array() ) {
		if ( null === $this->open ) {
			throw new \Exception( "control '$id' added outside any section" );
		}
		if ( isset( $this->zd_controls[ $id ] ) ) {
			throw new \Exception( "duplicate control id '$id' (second use in section '{$this->open}')" );
		}
		if ( ! isset( $args['type'] ) ) {
			throw new \Exception( "control '$id' has no type" );
		}
		if ( 'repeater' === $args['type'] && empty( $args['fields'] ) ) {
			throw new \Exception( "repeater '$id' has no fields" );
		}
		$this->zd_controls[ $id ] = $args;
		return true;
	}

	public function add_responsive_control( $id, $args = array(), $options = array() ) {
		return $this->add_control( $id, $args, $options );
	}

	public function get_current_section() { return $this->open; }
	public function add_group_control( $type, $args = array() ) { return true; }
	public function start_controls_tabs( $id ) {}
	public function end_controls_tabs() {}
	public function start_controls_tab( $id, $args = array() ) {}
	public function end_controls_tab() {}
	public function get_settings_for_display( $k = null ) {
		$out = array();
		foreach ( $this->zd_controls as $id => $args ) {
			$val = array_key_exists( 'default', $args ) ? $args['default'] : '';

			/*
			 * Elementor merges each repeater FIELD's own default into every
			 * repeater ROW. Without doing the same here the harness reports
			 * undefined-key notices that never happen in production.
			 */
			if ( isset( $args['type'] ) && 'repeater' === $args['type'] && is_array( $val ) ) {
				$field_defaults = array();
				foreach ( (array) $args['fields'] as $fid => $fargs ) {
					$field_defaults[ $fid ] = array_key_exists( 'default', $fargs ) ? $fargs['default'] : '';
				}
				foreach ( $val as $i => $row ) {
					$val[ $i ] = array_merge( $field_defaults, (array) $row );
					$val[ $i ]['_id'] = 'row' . $i;
				}
			}

			$out[ $id ] = $val;
		}
		return null === $k ? $out : ( isset( $out[ $k ] ) ? $out[ $k ] : null );
	}
	public function get_settings( $k = null ) { return $this->get_settings_for_display( $k ); }
	public function zd_render() { $this->render(); }
	private $attrs = array();

	public function get_id() { return 'zdtest'; }
	public function get_id_int() { return 1; }

	public function add_render_attribute( $el, $key = null, $value = null, $overwrite = false ) {
		if ( is_array( $el ) ) { return $this; }
		if ( is_array( $key ) ) {
			foreach ( $key as $k => $v ) { $this->add_render_attribute( $el, $k, $v ); }
			return $this;
		}
		if ( ! isset( $this->attrs[ $el ] ) ) { $this->attrs[ $el ] = array(); }
		$this->attrs[ $el ][ $key ] = is_array( $value ) ? implode( ' ', $value ) : (string) $value;
		return $this;
	}

	public function set_render_attribute( $el, $key = null, $value = null ) {
		return $this->add_render_attribute( $el, $key, $value, true );
	}

	public function get_render_attribute_string( $el ) {
		if ( empty( $this->attrs[ $el ] ) ) { return ''; }
		$out = array();
		foreach ( $this->attrs[ $el ] as $k => $v ) {
			$out[] = $k . '="' . htmlspecialchars( $v, ENT_QUOTES ) . '"';
		}
		return implode( ' ', $out );
	}

	public function add_link_attributes( $el, $url_control, $overwrite = false ) {
		if ( ! empty( $url_control['url'] ) ) { $this->add_render_attribute( $el, 'href', $url_control['url'] ); }
		if ( ! empty( $url_control['is_external'] ) ) { $this->add_render_attribute( $el, 'target', '_blank' ); }
		if ( ! empty( $url_control['nofollow'] ) ) { $this->add_render_attribute( $el, 'rel', 'nofollow' ); }
		return $this;
	}

	public function print_render_attribute_string( $el ) {
		echo $this->get_render_attribute_string( $el );
	}

	/** The harness entry point — mirrors what the editor bootstrap does. */
	public function zd_build() {
		$this->register_controls();
		if ( null !== $this->open ) {
			throw new \Exception( "section '{$this->open}' was never closed" );
		}
		return count( $this->zd_controls );
	}
}
}
