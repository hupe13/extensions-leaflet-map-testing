<?php
/**
 * Plugin Name: extensions-leaflet-map-testing
 * Description: Tests for leaflet-map
 * Maybe candidates for https://wordpress.org/plugins/extensions-leaflet-map/
 * Version: 1.3
 * Author: hupe13
 * GitHub Plugin URI: https://github.com/hupe13/extensions-leaflet-map-testing
 * Primary Branch: main
**/

// Direktzugriff auf diese Datei verhindern:
defined( 'ABSPATH' ) or die();

define('TESTLEAFEXT_PLUGIN_FILE', __FILE__);
define('TESTLEAFEXT_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('TESTLEAFEXT_PLUGIN_SETTINGS', dirname( plugin_basename( __FILE__ ) ) );

if ( ! function_exists( 'is_plugin_active' ) )
    require_once( ABSPATH . '/wp-admin/includes/plugin.php' );

if (! is_admin()) {
	include_once TESTLEAFEXT_PLUGIN_DIR . '/php/multielevation.php';
	include_once TESTLEAFEXT_PLUGIN_DIR . '/php/hovergeojson.php';
} else {
	include_once TESTLEAFEXT_PLUGIN_DIR . '/admin.php';
}

// Add settings to plugin page
function testleafext_add_action_links ( $actions ) {
	$actions[] = '<a href="'. esc_url( get_admin_url(null, 'admin.php?page='.TESTLEAFEXT_PLUGIN_SETTINGS) ) .'">'. esc_html__( "Settings").'</a>';
  return $actions;
}
add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'testleafext_add_action_links' );

?>
