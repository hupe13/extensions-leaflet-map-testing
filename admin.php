<?php

//include TESTLEAFEXT_PLUGIN_DIR . '/admin/.php';
include TESTLEAFEXT_PLUGIN_DIR . '/admin/filemgr/uploader.php';
include TESTLEAFEXT_PLUGIN_DIR . '/admin/filemgr/filemgr.php';
include TESTLEAFEXT_PLUGIN_DIR . '/admin/filemgr/main.php';

// Admin Menu

add_action('admin_init', 'testleafext_init' );
add_action('admin_menu', 'testleafext_add_page', 99);

// Init plugin options to white list our options
function testleafext_init(){
	register_setting( 'testleafext_options', 'testleafext_maps', 'testleafext_validate' );
}

// Draw the menu page itself
function testleafext_do_page() {
	$active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'help';
	$leafext_plugin_name = basename(dirname(  __FILE__  ));
	echo '<div class="wrap nothickbox">
	<h2>Extensions Tests Options</h2>';
	echo '</div>';
	echo '<h3 class="nav-tab-wrapper">';

	echo '<a href="?page='.$leafext_plugin_name.'&tab=help" class="nav-tab';
	echo $active_tab == 'help' ? ' nav-tab-active' : '';
	echo '">Hilfe!</a>';

	$tabs = array (
		array (
			'tab' => 'filemgr',
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
	} else if ( strpos( $active_tab, 'filemgr' ) !== false ) {
		leafext_admin_filemgr($active_tab);
	}

	echo '</div>';
}
