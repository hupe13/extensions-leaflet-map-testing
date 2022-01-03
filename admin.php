<?php

//include TESTLEAFEXT_PLUGIN_DIR . '/admin/.php';
include TESTLEAFEXT_PLUGIN_DIR . '/admin/uploader.php';
include TESTLEAFEXT_PLUGIN_DIR . '/admin/providers.php';

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


	$tabs = array (
		array (
			'tab' => 'providers',
			'title' => __('Leaflet Providers','extensions-leaflet-map'),
		),
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

	echo '<div class="wrap">
	<h2>Extensions for Leaflet Map Options (Testing)</h2>';

	if( $active_tab == 'help' ) {
		include TESTLEAFEXT_PLUGIN_DIR . '/admin/help.php';
	} else if ( $active_tab == 'providers' ) {
		echo '<form method="post" action="options.php">';
		settings_fields('leafext_providers');
		do_settings_sections( 'leafext_providers' );
		submit_button();
		submit_button( __( 'Reset', 'extensions-leaflet-map' ), 'delete', 'delete', false);
		echo '</form>';
	} else if ( $active_tab == 'manage_files' ) {
		include TESTLEAFEXT_PLUGIN_DIR . '/admin/managefiles.php';
	}
?>
	</div>
	<?php
}
