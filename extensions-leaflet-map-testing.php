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

if ( ! is_plugin_active( 'extensions-leaflet-map/extensions-leaflet-map.php' ) ) {
  function testleafext_require_leafext_plugin(){?>
    <div class="notice notice-error" >
      <p> Please install and activate <a href="https://wordpress.org/plugins/leaflet-map/">Leaflet Map Plugin</a> before using Extensions for Leaflet Map.</p>
    </div><?php
  }
  add_action('admin_notices','testleafext_require_leafext_plugin');
  register_activation_hook(__FILE__, 'testleafext_require_leafext_plugin');
}

if (! is_admin()) {
	include_once TESTLEAFEXT_PLUGIN_DIR . '/php/multielevation.php';
	include_once TESTLEAFEXT_PLUGIN_DIR . '/php/hovergeojson.php';
	include_once TESTLEAFEXT_PLUGIN_DIR . '/php/showmarkers.php';
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
