<?php
/**
 * Renders every widget off its defaults, so the branches nobody has run get run.
 *
 * render-widgets.php proves each widget survives its own default settings. That
 * is exactly one path through a widget that may have twenty: every switcher-
 * gated section, every SELECT branch past the first option, and every "what if
 * the editor cleared this field" case is untested code. In production a notice
 * raised in one of those is written straight into the editor's AJAX response,
 * which is how a widget ends up spinning forever after someone toggles a
 * control - the widget worked on defaults, so nothing caught it.
 *
 * Five settings profiles per widget. Not exhaustive, but it turns "never
 * executed" into "executed at least once".
 */

error_reporting( E_ALL );
ini_set( 'display_errors', '1' );

$warnings = array();
set_error_handler( function ( $no, $str, $file, $line ) use ( &$warnings ) {
	$warnings[] = sprintf( '%s (%s:%d)', $str, basename( $file ), $line );
	return true;
} );

$GLOBALS['zd_fake_posts'] = 6;
require __DIR__ . '/elementor-stub.php';

$P = dirname( __DIR__ ) . '/';
require __DIR__ . '/fixtures.php';
require $P . 'includes/class-zlaark-deals-computed.php';
require $P . 'includes/class-zlaark-deals-panel.php';
require $P . 'includes/class-zlaark-deals-articles.php';

class Zlaark_Deals_Settings { const SCHEMA = 2; public static function get( $k ) { return 1; } }

class Zlaark_Deals_Meta {
	const FIELDS = array();
	public static function offer_types() {
		return array( '' => '- none -', 'coupon' => 'Coupon', 'exclusive' => 'Exclusive',
		              'free_trial' => 'Free trial', 'free_plan' => 'Free plan', 'seasonal' => 'Seasonal' );
	}
	public static function get_deal_data( $p ) {
		$d = zd_fixture_deals();
		$i = is_object( $p ) ? $p->ID : (int) $p;
		return $d[ $i % count( $d ) ];
	}
	public static function parse_lines( $r ) { return array(); }
	public static function parse_scores( $r ) { return array(); }
}

require $P . 'widgets/class-zlaark-widget-base.php';
require $P . 'widgets/class-zlaark-homepage-widget.php';
require $P . 'widgets/class-zlaark-section-widget-base.php';
require $P . 'includes/class-zlaark-deals-elementor.php';

/**
 * Builds one settings profile for a widget's control stack.
 *
 * @param array  $controls The widget's registered controls.
 * @param string $profile  Which way to push every control.
 * @return array id => value
 */
function zd_profile( $controls, $profile ) {
	$out = array();

	foreach ( $controls as $id => $args ) {
		$type    = isset( $args['type'] ) ? $args['type'] : 'text';
		$options = isset( $args['options'] ) ? array_keys( (array) $args['options'] ) : array();

		switch ( $profile ) {
			case 'switchers-on':
				if ( 'switcher' === $type ) {
					$out[ $id ] = isset( $args['return_value'] ) ? $args['return_value'] : 'yes';
				}
				break;

			case 'switchers-off':
				if ( 'switcher' === $type ) {
					$out[ $id ] = '';
				}
				break;

			case 'last-option':
				// Switchers on as well, or the branch behind the select is
				// never reached in the first place.
				if ( 'switcher' === $type ) {
					$out[ $id ] = isset( $args['return_value'] ) ? $args['return_value'] : 'yes';
				} elseif ( 'select' === $type && $options ) {
					$out[ $id ] = end( $options );
				} elseif ( 'select2' === $type && $options ) {
					$out[ $id ] = ! empty( $args['multiple'] ) ? array( end( $options ) ) : end( $options );
				}
				break;

			case 'emptied':
				/*
				 * The editor cleared the field. Elementor hands back the empty
				 * string, not the default, so anything that assumed a value was
				 * always present shows up here.
				 */
				if ( in_array( $type, array( 'text', 'textarea', 'wysiwyg', 'number', 'color' ), true ) ) {
					$out[ $id ] = '';
				} elseif ( 'repeater' === $type ) {
					$out[ $id ] = array();
				} elseif ( 'select2' === $type && ! empty( $args['multiple'] ) ) {
					$out[ $id ] = array();
				}
				break;

			case 'no-deals':
				// Handled by the caller through zd_fake_posts.
				break;
		}
	}

	return $out;
}

$PROFILES = array( 'switchers-on', 'switchers-off', 'last-option', 'emptied', 'no-deals' );

$pass = 0;
$fail = 0;

echo "rendering every widget off its defaults\n";
echo str_repeat( '-', 78 ) . "\n";

foreach ( Zlaark_Deals_Elementor::WIDGETS as $slug => $class ) {
	require_once $P . 'widgets/class-zlaark-' . $slug . '-widget.php';

	$problems = array();

	foreach ( $PROFILES as $profile ) {
		// An empty catalogue is its own profile: every "no deals" guard runs.
		$GLOBALS['zd_fake_posts'] = ( 'no-deals' === $profile ) ? 0 : 6;

		$before = count( $warnings );

		try {
			$w = new $class();
			$w->zd_build();
			$w->zd_override = zd_profile( $w->zd_controls, $profile );

			ob_start();
			$w->zd_render();
			ob_end_clean();
		} catch ( \Throwable $e ) {
			if ( ob_get_level() > 0 ) { ob_end_clean(); }
			$problems[] = sprintf( '%s: %s %s', $profile, get_class( $e ), $e->getMessage() );
			continue;
		}

		foreach ( array_slice( $warnings, $before ) as $w_msg ) {
			$problems[] = $profile . ': ' . $w_msg;
		}
	}

	if ( $problems ) {
		printf( "  %-16s FAILED\n", $slug );
		foreach ( array_slice( $problems, 0, 4 ) as $p_msg ) {
			printf( "      %s\n", $p_msg );
		}
		if ( count( $problems ) > 4 ) {
			printf( "      ... and %d more\n", count( $problems ) - 4 );
		}
		$fail++;
	} else {
		printf( "  %-16s %d profiles clean\n", $slug, count( $PROFILES ) );
		$pass++;
	}
}

echo str_repeat( '-', 78 ) . "\n";
printf( "%d widgets clean, %d with problems\n", $pass, $fail );
exit( $fail > 0 ? 1 : 0 );
