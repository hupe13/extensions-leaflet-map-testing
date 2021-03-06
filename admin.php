<?php

include TESTLEAFEXT_PLUGIN_DIR . '/admin/placementstrategies.php';

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

// Draw the menu page itself
function testleafext_do_page() {
	//var_dump($options);
	$leafext_plugin_name = basename(dirname(  __FILE__  ));
	?>
	<div class="wrap">
	<h2>Extensions Tests Options</h2>
<?php
	$active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'help';

	echo '<h3 class="nav-tab-wrapper">';

	echo '<a href="?page='.$leafext_plugin_name.'&tab=help" class="nav-tab';
	echo $active_tab == 'help' ? ' nav-tab-active' : '';
	echo '">Hilfe!</a>';

	echo '<a href="?page='.$leafext_plugin_name.'&tab=placementstrategies" class="nav-tab';
	echo $active_tab == 'placementstrategies' ? ' nav-tab-active' : '';
	echo '">MarkerCluster.PlacementStrategies</a>';

	echo '</h3>';

	echo '<div class="wrap">
	<h2>Extensions for Leaflet Map Options (Testing)</h2>';

	if( $active_tab == 'help' ) {
		include TESTLEAFEXT_PLUGIN_DIR . '/admin/help.php';
	} else if ( $active_tab == 'placementstrategies' ) {
		leafext_admin_placementstrategies();
	}
?>
	</div>
	<?php
}
