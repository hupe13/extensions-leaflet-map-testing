<?php
/**
* filemgr
* extensions-leaflet-map
*/
// Direktzugriff auf diese Datei verhindern:
defined( 'ABSPATH' ) or die();

include TESTLEAFEXT_PLUGIN_DIR . '/admin/filemgr/managefiles_functions.php';
include TESTLEAFEXT_PLUGIN_DIR . '/admin/filemgr/help.php';

$track = isset($_GET['track']) ? $_GET['track'] : "";
$page = isset($_GET['page']) ? $_GET['page'] : "";
$tab = isset($_GET['tab']) ? $_GET['tab'] : "";

if ( $track == "") {
	leafext_managefiles();
} else {
	include TESTLEAFEXT_PLUGIN_DIR . '/admin/filemgr/thickbox.php';
	leafext_thickbox($track);
}

function leafext_managefiles() {
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
			</span></div>';
		}
		echo '<p>';
		echo leafext_list_files($dir,$extensions);
		echo '</p>';
	} else if ($all != "") {
		leafext_list_paginate($extensions,$all);
	}
}
