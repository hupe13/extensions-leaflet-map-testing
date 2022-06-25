<?php
/**
 * filemgr help
 * extensions-leaflet-map
 */
// Direktzugriff auf diese Datei verhindern:
defined( 'ABSPATH' ) or die();

function leafext_managefiles_help() {
	echo sprintf(__('Here you can see all gpx and kml files in subdirectories of uploads directory.
	You can manage these
	%s with any (S)FTP-Client,
	%s with any File Manager plugin,
	%s with any plugin for importing uploaded files to the Media Library,','extensions-leaflet-map'),
	'<ul style="list-style: disc;"><li style="margin-left: 1.5em;"> ','</li><li style="margin-left: 1.5em;"> ','</li><li style="margin-left: 1.5em;"> ','</li><li style="margin-left: 1.5em;"> ').
	'</li><li style="margin-left: 1.5em;"> '.
	__('direct in the Media Library.','extensions-leaflet-map').
	'</li>
	<li style="margin-left: 1.5em;"> or in your own way.</li>
	</ul>';
	echo '<h3>To Do</h3>
	<ul style="list-style: disc;">
	<li style="margin-left: 1.5em;"> Query allow GPX, GeoJSON, KML or TCX upload to Media Library?
  <li style="margin-left: 1.5em;"> Selevt ext to view?
	<li style="margin-left: 1.5em;"> Cleanup and Translation
	</ul>';
}
