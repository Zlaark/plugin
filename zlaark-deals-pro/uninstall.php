<?php
/**
 * Removes every Zlaark deal, category and option when the plugin is deleted.
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

$zlaark_cpt = 'zlaark_deal';
$zlaark_tax = 'zlaark_deal_cat';

// The plugin's own hooks never run during uninstall, so the taxonomy has to be
// registered here before get_terms()/wp_delete_term() will accept it.
register_taxonomy( $zlaark_tax, array( $zlaark_cpt ), array( 'public' => false ) );

$zlaark_deals = get_posts(
	array(
		'post_type'        => $zlaark_cpt,
		'post_status'      => 'any',
		'numberposts'      => -1,
		'fields'           => 'ids',
		'suppress_filters' => true,
	)
);

foreach ( $zlaark_deals as $zlaark_deal_id ) {
	wp_delete_post( $zlaark_deal_id, true );
}

$zlaark_terms = get_terms(
	array(
		'taxonomy'   => $zlaark_tax,
		'hide_empty' => false,
		'fields'     => 'ids',
	)
);

if ( ! is_wp_error( $zlaark_terms ) ) {
	foreach ( $zlaark_terms as $zlaark_term_id ) {
		wp_delete_term( $zlaark_term_id, $zlaark_tax );
	}
}

delete_option( 'zlaark_deals_defaults_seeded' );
