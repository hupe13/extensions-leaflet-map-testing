<?php

//include TESTLEAFEXT_PLUGIN_DIR . '/admin/.php';
include TESTLEAFEXT_PLUGIN_DIR . '/admin/uploader.php';

// Admin Menu

add_action('admin_init', 'testleafext_init' );
add_action('admin_menu', 'testleafext_add_page', 99);

// Init plugin options to white list our options
function testleafext_init(){
	register_setting( 'testleafext_options', 'testleafext_maps', 'testleafext_validate' );
}

// Add menu page
function testleafext_add_page() {
	//add_options_page('Extensions for Leaflet Map Options', 'Extensions for Leaflet Map', 'manage_options', 'extensions-leaflet-map', 'testleafext_do_page');
	//Add Submenu
	add_submenu_page( 'leaflet-map', 'Extensions Test Options', 'Extensions Tests',
    'manage_options', 'extensions-leaflet-map-testing', 'testleafext_do_page');
}

function leafext_admin_css() {
  wp_enqueue_style( 'leafext_admin',
  plugins_url('css/admin.css',
		TESTLEAFEXT_PLUGIN_FILE));
}

function leafext_admin_js() {
	wp_enqueue_script('leafext_createShortcode_js',
		plugins_url('js/createShortcode.js',
		TESTLEAFEXT_PLUGIN_FILE));
}

// Draw the menu page itself
function testleafext_do_page() {
	$track = isset($_GET['track']) ? $_GET['track'] : "";

	if ( $track == "" ) {
		$leafext_plugin_name = basename(dirname(  __FILE__  ));
		echo '<div class="wrap">
		<h2>Extensions Tests Options</h2>';

		$active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'help';

		echo '<h3 class="nav-tab-wrapper">';

		echo '<a href="?page='.$leafext_plugin_name.'&tab=help" class="nav-tab';
		echo $active_tab == 'help' ? ' nav-tab-active' : '';
		echo '">Hilfe!</a>';

		$tabs = array (
			array (
				'tab' => 'manage_files',
				'title' => __('Manage Files','extensions-leaflet-map'),
			),
			// array (
			// 	'tab' => '',
			// 	'title' => '',
			// ),
		);

		foreach ( $tabs as $tab) {
			echo '<a href="?page='.$leafext_plugin_name.'&tab='.$tab['tab'].'" class="nav-tab';
			$active = ( $active_tab == $tab['tab'] ) ? ' nav-tab-active' : '' ;
			echo $active;
			echo '">'.$tab['title'].'</a>'."\n";
		}

		echo '</h3>';

		echo '<div class="wrap">';

		if( $active_tab == 'help' ) {
			include TESTLEAFEXT_PLUGIN_DIR . '/admin/help.php';
		} else if ( $active_tab == 'manage_files') {
			include TESTLEAFEXT_PLUGIN_DIR . '/admin/managefiles.php';
		}

		echo '</div>';
	} else {
		include TESTLEAFEXT_PLUGIN_DIR . '/admin/managefiles.php';
	}
}
