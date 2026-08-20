<?php
/**
 * Uninstall routine.
 *
 * Deals are user-generated content, so by default NOTHING is deleted here — the
 * plugin can be removed and re-added without losing the catalogue. Destruction
 * only happens when the site owner has explicitly opted in at
 * Zlaark Deals → Settings → Data → "On uninstall".
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

$zlaark_settings = get_option( 'zlaark_deals_settings', array() );
$zlaark_optin    = is_array( $zlaark_settings ) && ! empty( $zlaark_settings['delete_data_on_uninstall'] );

if ( ! $zlaark_optin ) {
	// Opt-in absent: leave every deal, category and image exactly where it is.
	return;
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
delete_option( 'zlaark_deals_settings' );
