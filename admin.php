<?php
// Admin Menu

include "admin/help.php";

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

	echo '<a href="?page='.$leafext_plugin_name.'&tab=cluster" class="nav-tab';
	echo $active_tab == 'cluster' ? ' nav-tab-active' : '';
	echo '">Markercluster</a>';
	echo '<a href="?page='.$leafext_plugin_name.'&tab=help" class="nav-tab';
	echo $active_tab == 'help' ? ' nav-tab-active' : '';
	echo '">Hilfe!</a>';

	echo '</h3>';

	echo '<div class="wrap">
	<h2>Extensions for Leaflet Map Options</h2>';
	if( $active_tab != 'help' ) {
	echo '<form method="post" action="options.php">';
	if( $active_tab == 'cluster' ) {
			settings_fields('leafext_settings_cluster');
			do_settings_sections( 'leafext_settings_cluster' );
	}
	submit_button();
	echo '</form>';
}
	if( $active_tab == 'help' ) {
		echo leafext_help_text();
	}
?>
	</div>
	<?php
}
