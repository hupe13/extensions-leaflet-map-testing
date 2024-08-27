<?php
/**
 * File admin.php
 *
 * @package Extensions for Leaflet Map
 */

// Direktzugriff auf diese Datei verhindern.
defined( 'ABSPATH' ) || die();

add_action( 'admin_init', 'testleafext_ele_init' );
add_action( 'admin_menu', 'testleafext_ele_add_page', 99 );

require TESTLEAFEXT_PLUGIN_DIR . '/admin/proxy.php';

// Init plugin options to white list our options
function testleafext_ele_init() {
	register_setting( 'testleafext_options', 'testleafext_maps', 'testleafext_validate' );
}

// Add menu page
function testleafext_ele_add_page() {
	add_submenu_page(
		'leaflet-map',
		'Extensions Test Options',
		'Extensions Tests',
		'manage_options',
		'extensions-leaflet-map-testing',
		'testleafext_ele_do_page'
	);
}

// Draw the menu page itself
function testleafext_ele_do_page() {
	$get                 = map_deep( wp_unslash( $_GET ), 'sanitize_text_field' );
	$active_tab          = isset( $get['tab'] ) ? $get['tab'] : 'help';
	$leafext_plugin_name = basename( __DIR__ );
	echo '<div class="wrap nothickbox">
	<h2>Extensions Tests Options</h2>';
	echo '</div>';
	echo '<h3 class="nav-tab-wrapper">';

	echo '<a href="?page=' . $leafext_plugin_name . '&tab=help" class="nav-tab';
	echo $active_tab == 'help' ? ' nav-tab-active' : '';
	echo '">Hilfe!</a>';

	$tabs = array(
		array(
			'tab'   => 'proxy',
			'title' => 'track proxy',
		),
		// array (
		// 'tab' => '',
		// 'title' => '',
		// ),
	);

	foreach ( $tabs as $tab ) {
		echo '<a href="?page=' . $leafext_plugin_name . '&tab=' . $tab['tab'] . '" class="nav-tab';
		$active = ( $active_tab == $tab['tab'] ) ? ' nav-tab-active' : '';
		echo $active;
		echo '">' . $tab['title'] . '</a>' . "\n";
	}

	echo '</h3>';

	echo '<div class="wrap">';

	if ( $active_tab == 'help' ) {
		include TESTLEAFEXT_PLUGIN_DIR . '/admin/help.php';
	} elseif ( $active_tab == 'proxy' ) {
		leafext_proxy_help_text();
		leafext_admin_proxy();
	}

	echo '</div>';
}
