<?php
/**
 * Plugin Name:       extensions-leaflet-map-testing - elevation proxy
 * Plugin URI:        https://github.com/hupe13/extensions-leaflet-map-testing
 * GitHub Plugin URI: https://github.com/hupe13/extensions-leaflet-map-testing
 * Primary Branch:    main
 * Description:       elevation-proxy / extensions-leaflet-map
 * Version:           240827
 * Requires PHP:      7.4
 * Author:      hupe13
 * License:     GPL v2 or later
 * Text Domain: extensions-leaflet-map
 * Domain Path: /lang/
 **/

// Direktzugriff auf diese Datei verhindern:
defined( 'ABSPATH' ) || die();

define( 'TESTLEAFEXT_ELE_PLUGIN_FILE', __FILE__ ); // /pfad/wp-content/plugins/extensions-leaflet-map/extensions-leaflet-map.php
define( 'TESTLEAFEXT_ELE_PLUGIN_DIR', plugin_dir_path( __FILE__ ) ); // /pfad/wp-content/plugins/extensions-leaflet-map-github/
define( 'TESTLEAFEXT_ELE_PLUGIN_URL', WP_PLUGIN_URL . '/' . basename( TESTLEAFEXT_ELE_PLUGIN_DIR ) ); // https://url/wp-content/plugins/extensions-leaflet-map-github/
define( 'TESTLEAFEXT_ELE_PLUGIN_SETTINGS', dirname( plugin_basename( __FILE__ ) ) ); // extensions-leaflet-map

if ( ! function_exists( 'is_plugin_active' ) ) {
	require_once ABSPATH . '/wp-admin/includes/plugin.php';
}

$leafext_active = preg_grep( '/extensions-leaflet-map.php/', get_option( 'active_plugins' ) );
if ( count( $leafext_active ) == 0 ) {
	function leafext_require_leafext_plugin() {
		?>
	<div class="notice notice-error" >
		<p> Please install and activate <a href="https://wordpress.org/plugins/extensions-leaflet-map/">Extensions for Leaflet Map</a>
		before using Extensions for Leaflet Map Testing.</p>
	</div>
		<?php
	}
	add_action( 'admin_notices', 'leafext_require_leafext_plugin' );
	// register_activation_hook(__FILE__, 'leafext_require_leafext_plugin');
}

// Add settings to plugin page
function testleafext_ele_add_action_links( $actions ) {
	$actions[] = '<a href="' . esc_url( get_admin_url( null, 'admin.php?page=' . TESTLEAFEXT_ELE_PLUGIN_SETTINGS ) ) . '">' .
	esc_html__( 'Settings' ) . '</a>';
	return $actions;
}
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'testleafext_ele_add_action_links' );

if ( is_admin() ) {
	include_once TESTLEAFEXT_ELE_PLUGIN_DIR . 'admin.php';
}

require_once TESTLEAFEXT_ELE_PLUGIN_DIR . '/php/proxy.php';
require_once TESTLEAFEXT_ELE_PLUGIN_DIR . '/php/elevation.php';
