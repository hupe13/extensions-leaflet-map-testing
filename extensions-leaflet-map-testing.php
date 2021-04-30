<?php
/**
 * Plugin Name: extensions-leaflet-map-testing
 * Description: Tests for leaflet-map
 * Maybe candidates for https://wordpress.org/plugins/extensions-leaflet-map/
 * Version: 0.0.16
 * Author: hupe13
 * GitHub Plugin URI: https://github.com/hupe13/extensions-leaflet-map-testing
 * Primary Branch: main
**/

// Direktzugriff auf diese Datei verhindern:
defined( 'ABSPATH' ) or die();

define('TESTLEAFEXT_PLUGIN_FILE', __FILE__);
define('TESTLEAFEXT_PLUGIN_DIR', plugin_dir_path(__FILE__));

if (! is_admin()) {
	if ( ! function_exists( 'is_plugin_active' ) )
		require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
	if ( is_plugin_active( 'leaflet-map/leaflet-map.php' ) ) {
		require_once( __DIR__ . '/php/multielevation.php');
		require_once( __DIR__ . '/php/elevation.php');
		require_once( __DIR__ . '/php/hovergeojson.php');
		require_once( __DIR__ . '/php/markercluster.php');
		require_once( __DIR__ . '/php/zoomhome.php');
	}
} else {
	require_once( __DIR__ . '/admin.php');
}

// Add settings to plugin page
function testleafext_add_action_links ( $actions ) {
	$setting_page = dirname( plugin_basename( __FILE__ ) );
	$actions[] = '<a href="'. esc_url( get_admin_url(null, 'admin.php?page='.$settings_page ) ) .'">'. esc_html__( "Settings").'</a>';
  return $actions;
}
add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'testleafext_add_action_links' );

?>
