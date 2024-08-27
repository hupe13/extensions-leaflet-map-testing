<?php
/**
 * Uninstall handler.
 *
 * @package extensions-leaflet-map
 */

// if uninstall.php is not called by WordPress, die
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

global $wpdb;
// phpcs:ignore
$option_names = $wpdb->get_results( "SELECT option_name FROM $wpdb->options WHERE option_name LIKE 'leafext_proxy' " );
foreach ( $option_names as $key => $value ) {
	delete_option( $value->option_name );
	// for site options in Multisite
	delete_site_option( $value->option_name );
}
