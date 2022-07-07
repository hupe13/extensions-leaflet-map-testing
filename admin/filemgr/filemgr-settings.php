<?php
// Direktzugriff auf diese Datei verhindern:
defined( 'ABSPATH' ) or die();

//Parameter and Values
function leafext_filemgr_params($typ = array()) {
	$params = array(
		array(
			'param' => 'types',
			'shortdesc' => __('Types',"extensions-leaflet-map"),
			'desc' => __('Allow upload to media library',"extensions-leaflet-map"),
			'default' => array(),
			'values' => array ("gpx","kml","geojson","json","tcx"),
		),
		array(
			'param' => 'gpxupload',
			'shortdesc' => __('Lade gpx-Dateien in das Verzeichnis /upload_dir()/gpx/',"extensions-leaflet-map"),
			'desc' => 'Das kann von Interesse sein, wenn du wp-gpx-maps verwendet hast.',
			'default' => "0",
			'values' => 1,
		),
		array(
			'param' => 'nonadmin',
			'shortdesc' => __('Allow non admin',"extensions-leaflet-map"),
			'desc' => sprintf(__('Erlaube allen Nutzern, die auf das Backend Zugriff haben, die Dateien zu sehen. Eine Berechtigungsprüfung %s findet nur statt, wenn die Dateien in der Mediathek registriert sind.',"extensions-leaflet-map"),
			'(<code>current_user_can("edit_post / read", this_post)</code>)'),
			'default' => "0",
			'values' => 1,
		),
	);
	return $params;
}

// init settings
function leafext_filemgr_init(){
	register_setting( 'leafext_settings_filemgr', 'leafext_filemgr', 'leafext_validate_filemgr_options' );
	//register_setting( 'leafext_settings_filemgr', 'leafext_filemgr' );
	add_settings_section( 'filemgr_settings', __('File Manager','extensions-leaflet-map'), 'leafext_managefiles_help', 'leafext_settings_filemgr' );
	$fields = leafext_filemgr_params();
	foreach($fields as $field) {
		add_settings_field("leafext_filemgr[".$field['param']."]", $field['shortdesc'], 'leafext_form_filemgr','leafext_settings_filemgr', 'filemgr_settings', $field['param']);
	}
}
add_action('admin_init', 'leafext_filemgr_init');

function leafext_validate_filemgr_options($options){
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

	if ( $field == "types" ) {
		foreach ( $option['values'] as $typ ) {
			$checked = in_array($typ, $setting) ? " checked " : "";
			echo ' <input type="checkbox" name="leafext_filemgr['.$option['param'].'][]" value="'.$typ.'" id="'.$typ.'" '.$checked.'>';
			echo ' <label for="'.$typ.'" >'.$typ.'</label> ';
		}
	} else {
		if ($setting != $option['default'] ) {
			//var_dump($setting,$option['default']);
			echo __("Plugins Default", "extensions-leaflet-map").': ';
			echo $option['default'] ? "true" : "false";
			echo '<br>';
		}
		echo '<input type="radio" name="leafext_filemgr['.$option['param'].']" value="1" ';
		echo $setting ? 'checked' : '' ;
		echo '> true &nbsp;&nbsp; ';
		echo '<input type="radio" name="leafext_filemgr['.$option['param'].']" value="0" ';
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
	echo __('Here you can see all gpx, kml, geojson, json and tcx files in subdirectories of uploads directory.','extensions-leaflet-map').' ';
	echo __('You can manage these','extensions-leaflet-map');
	echo '<ul style="list-style: disc;">';
	echo '<li style="margin-left: 1.5em;">';
	echo __('direct in the Media Library.','extensions-leaflet-map');
	echo '</li>';
	echo '<li style="margin-left: 1.5em;">';
	echo __('with any (S)FTP-Client,','extensions-leaflet-map');
	echo '</li>';
	echo '<li style="margin-left: 1.5em;">';
	echo __('with any File Manager plugin,','extensions-leaflet-map');
	echo '</li>';
	echo '<li style="margin-left: 1.5em;">';
	echo __('with any plugin for importing uploaded files to the Media Library.','extensions-leaflet-map');
	echo '</li>';
	echo '<li style="margin-left: 1.5em;">';
	echo __('or in your own way.','extensions-leaflet-map');
	echo '</li>';
	echo '<li style="margin-left: 1.5em;">';
	echo __('','extensions-leaflet-map');
	echo '</li>';
	echo '</ul>';

	echo '<h3>To Do</h3>
	<ul style="list-style: disc;">
  <li style="margin-left: 1.5em;"> Cleanup and Translation
	</ul>';
}
