<?php
// Direktzugriff auf diese Datei verhindern:
defined( 'ABSPATH' ) or die();

//Parameter and Values
function leafext_filemgr_params($typ = array()) {
	$params = array(
		array(
			'param' => 'gpx',
			'shortdesc' => __('Allow gpx.',"extensions-leaflet-map"),
			'desc' => '',
			'default' => false,
			'values' => 1,
		),
		array(
			'param' => 'gpxupload',
			'shortdesc' => __('Allow gpx upload to /upload_dir()/gpx/.',"extensions-leaflet-map"),
			'desc' => 'Das kann von Interesse sein, wenn du wp-gpx-maps verwendet hast.',
			'default' => false,
			'values' => 1,
		),
		array(
			'param' => 'kml',
			'shortdesc' => __('Allow kml.',"extensions-leaflet-map"),
			'desc' => '',
			'default' => false,
			'values' => 1,
		),
		array(
			'param' => 'geojson',
			'shortdesc' => __('Allow geojson / json.',"extensions-leaflet-map"),
			'desc' => '',
			'default' => false,
			'values' => 1,
		),
		array(
			'param' => 'tcx',
			'shortdesc' => __('Allow tcx.',"extensions-leaflet-map"),
			'desc' => '',
			'default' => false,
			'values' => 1,
		),
		array(
			'param' => 'nonadmin',
			'shortdesc' => __('Allow non admin.',"extensions-leaflet-map"),
			'desc' => 'Erlaube allen Nutzern, die auf das Backend Zugriff haben, die Dateien zu sehen. Eine Berechtigungsprüfung (<code>current_user_can( "edit_post / read", this_post)</code>) findet nur statt, wenn die Dateien in der Mediathek registriert sind.',
			'default' => false,
			'values' => 1,
		),
	);
	return $params;
}

// Add menu page
function testleafext_add_page() {
	//Add Submenu
	add_submenu_page( 'leaflet-map', 'Extensions Test Options', 'Extensions Tests',
    'manage_options', 'extensions-leaflet-map-testing', 'testleafext_do_page');

	$options = leafext_filemgr_settings();
	if ($options['nonadmin'] == true) {
	add_submenu_page( 'leaflet-shortcode-helper', 'Extensions Test Autor', 'Extensions Tests Autor',
	  'edit_posts', 'extensions-leaflet-map-testing-autor', 'leafext_filemgr_autor_page');
	}
}

// init settings

function leafext_filemgr_init(){
	register_setting( 'leafext_settings_filemgr', 'leafext_filemgr', 'leafext_validate_filemgr_options' );
	add_settings_section( 'filemgr_settings', __('File Manager','extensions-leaflet-map'), 'leafext_managefiles_help', 'leafext_settings_filemgr' );
	$fields = leafext_filemgr_params();
	foreach($fields as $field) {
		add_settings_field("leafext_filemgr[".$field['param']."]", $field['shortdesc'], 'leafext_form_filemgr','leafext_settings_filemgr', 'filemgr_settings', $field['param']);
	}
}
add_action('admin_init', 'leafext_filemgr_init');

function leafext_validate_filemgr_options($options){
	//var_dump($_POST,$input);
	if (isset($_POST['submit'])) {
		$defaults=array();
		$params = leafext_filemgr_params();
		foreach($params as $param) {
			$defaults[$param['param']] = $param['default'];
		}
		$params = get_option('leafext_filemgr', $defaults);
		foreach ($options as $key => $value) {
			$params[$key] = $value;
		}
		return $params;
	}
	if (isset($_POST['delete'])) delete_option('leafext_filemgr');
	return false;
}

function leafext_form_filemgr($field) {
	$options = leafext_filemgr_params();
	//var_dump($options); wp_die();
	$option = leafext_array_find2($field, $options);
	$settings = leafext_filemgr_settings();
	$setting = $settings[$field];
	if ( $option['desc'] != "" ) echo '<p>'.$option['desc'].'</p>';
	if (!current_user_can('manage_options')) {
		$disabled = " disabled ";
	} else {
		$disabled = "";
	}

	if (!is_array($option['values'])) {
		if ($setting != $option['default'] ) {
			//var_dump($setting,$option['default']);
			echo __("Plugins Default", "extensions-leaflet-map").': ';
			echo $option['default'] ? "true" : "false";
			echo '<br>';
		}
		echo '<input '.$disabled.' type="radio" name="leafext_filemgr['.$option['param'].']" value="1" ';
		echo $setting ? 'checked' : '' ;
		echo '> true &nbsp;&nbsp; ';
		echo '<input '.$disabled.' type="radio" name="leafext_filemgr['.$option['param'].']" value="0" ';
		echo (!$setting) ? 'checked' : '' ;
		echo '> false ';
	}
}

function leafext_filemgr_settings() {
	$defaults=array();
	$params = leafext_filemgr_params();
	foreach($params as $param) {
		$defaults[$param['param']] = $param['default'];
	}
	$options = shortcode_atts($defaults, get_option('leafext_filemgr'));
	//var_dump($options); wp_die();
	return $options;
}

function leafext_managefiles_help() {
	echo sprintf(__('Here you can see all gpx and kml files in subdirectories of uploads directory.
	You can manage these
	%s with any (S)FTP-Client,
	%s with any File Manager plugin,
	%s with any plugin for importing uploaded files to the Media Library,','extensions-leaflet-map'),
	'<ul style="list-style: disc;">
	<li style="margin-left: 1.5em;"> ',
	'</li><li style="margin-left: 1.5em;"> ',
	'</li><li style="margin-left: 1.5em;"> ',
	'</li><li style="margin-left: 1.5em;"> ').
	'</li><li style="margin-left: 1.5em;"> '.
	__('direct in the Media Library.','extensions-leaflet-map').
	'</li>
	<li style="margin-left: 1.5em;"> or in your own way.</li>
	</ul>';
	echo '<h3>To Do</h3>
	<ul style="list-style: disc;">
  <li style="margin-left: 1.5em;"> Select ext to view?
	<li style="margin-left: 1.5em;"> Cleanup and Translation
	</ul>';
}

function leafext_filemgr_autor_page() {
	leafext_managefiles();
}
