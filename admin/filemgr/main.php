<?php
/**
* filemgr
* extensions-leaflet-map
*/
// Direktzugriff auf diese Datei verhindern:
defined( 'ABSPATH' ) or die();

include TESTLEAFEXT_PLUGIN_DIR . '/admin/filemgr/managefiles_functions.php';

function leafext_filemgr_tab() {
	$TB_iframe = isset($_GET['TB_iframe']) ? $_GET['TB_iframe'] : "";
	if ( $TB_iframe == true ) return "";
	$tabs = array ();
	if (current_user_can('manage_options')) {
		$tabs[] = array (
			'tab' => 'filemgr',
			'title' => __('Settings','extensions-leaflet-map'),
		);
	}
	$tabs[] =	array (
		'tab' => 'filemgrfiles',
		'title' => __('Manage Files','extensions-leaflet-map'),
	);

	$active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : '';
	$textheader = '<div class="nav-tab-wrapper">';

	$page = isset($_GET['page']) ? $_GET['page'] : "";

	foreach ( $tabs as $tab) {
		$textheader = $textheader. '<a href="?page='.$page.'&tab='.$tab['tab'].'" class="nav-tab';
		$active = ( $active_tab == $tab['tab'] ) ? ' nav-tab-active' : '' ;
		$textheader = $textheader. $active;
		$textheader = $textheader. '">'.$tab['title'].'</a>'."\n";
	}

	$textheader = $textheader. '</div>';
	return $textheader;
}

function leafext_admin_filemgr($active_tab) {
	echo '<h2>'.leafext_filemgr_tab().'</h2>';
	if( $active_tab == 'filemgr') {
		echo '<form method="post" action="options.php">';
		settings_fields('leafext_settings_filemgr');
		do_settings_sections( 'leafext_settings_filemgr' );
		submit_button( __( 'Reset', 'extensions-leaflet-map' ), 'delete', 'delete', false);
		submit_button();
		echo '</form>';
	} else if( $active_tab == 'filemgrfiles') {
		leafext_managefiles();
	}
}

function leafext_managefiles() {

	$track = isset($_GET['track']) ? $_GET['track'] : "";
	$page = isset($_GET['page']) ? $_GET['page'] : "";
	$tab = isset($_GET['tab']) ? $_GET['tab'] : "";

	if ( $track == "") {

		echo '<h2>Manage Files</h2>';

		$dir = isset($_GET["dir"]) ? $_GET["dir"] : "";
		$all = isset($_GET["all"]) ? $_GET["all"] : "";
		$count = isset($_GET["count"]) ? $_GET["count"] : "5";
		$extensions = '{gpx,kml,geojson,json,tcx}';

		if ( $dir == "" && $all == "" ) leafext_managefiles_help();

		echo '<h2>List directories in upload directory with files with the extensions '.$extensions.'</h2>';

		leafext_dirs_form($dir,$extensions,$count);

		echo '<h2>Listing all files</h2>';

		leafext_files_form($all);

		if ( $dir != "" || $all != "" ) {
			leafext_createShortcode_js();
			leafext_createShortcode_css();
		}
		if ( $dir != "" ) {
			echo '<h3>Directory '.$dir.'</h3>';
			if ($dir != "/") {
				echo '
				<div>Shortcode for showing all files of this directory on a map:
					<span class="leafexttooltip" href="#" onclick="leafext_createShortcode('.
					"'leaflet-dir  src='".','.
					"'',".
					"'/".trim($dir,'/')."/'".')"
					onmouseout="leafext_outFunc()">
					<span class="leafextcopy" id="leafextTooltip">Copy to clipboard</span>
					<code>[leaflet-dir src="/'.trim($dir,'/').'/"]</code>
					</span>
					</div>';
				}
				echo '<p>';
				echo leafext_list_files($dir,$extensions);
				echo '</p>';
			} else if ($all != "") {
				leafext_list_paginate($extensions,$all);
			}

		} else {
			include TESTLEAFEXT_PLUGIN_DIR . '/admin/filemgr/thickbox.php';
			leafext_thickbox($track);
		}
	}
