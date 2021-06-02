<?php
//Shortcode:
//[elevation-track file="'.$file.'" lat="'.$startlat.'" lon="'.$startlon.'" name="'.basename($file).'"]
//[elevation-tracks]

function testleafext_elevation_track( $atts ){
	// echo '<pre>';
	//var_dump($atts);
	// echo '</pre>';
	global $all_files;
	if (!is_array($all_files)) $all_files = array();
	$all_files[]=$atts['file'];
	//
	global $all_points;
	if (!is_array($all_points)) $all_points = array();
	$point = array(
		'latlng' => array($atts['lat'],$atts['lon']),
		'name' => $atts['name'],
	);
	$all_points[]=$point;
	//var_dump($all_files);
}
add_shortcode('elevation-track', 'testleafext_elevation_track' );

//[elevation-tracks]
function testleafext_elevation_tracks( $atts ){
	wp_enqueue_script( 'elevation_js',
		plugins_url('leaflet-plugins/leaflet-elevation-1.6.8/js/leaflet-elevation.min.js',LEAFEXT_PLUGIN_FILE),
	array('wp_leaflet_map'),null);
	wp_enqueue_style( 'elevation_css',
		plugins_url('leaflet-plugins/leaflet-elevation-1.6.8/css/leaflet-elevation.min.css',LEAFEXT_PLUGIN_FILE),
		array('leaflet_stylesheet'),null);
	wp_enqueue_script('leaflet.gpx',
		plugins_url('leaflet-plugins/leaflet-gpx-1.5.2/gpx.js',
		TESTLEAFEXT_PLUGIN_FILE),
		array('elevation_js'),null);
	wp_enqueue_script('leaflet.gpxgroup',
		plugins_url('leaflet-plugins/leaflet-elevation-1.6.7/libs/leaflet-gpxgroup.js',TESTLEAFEXT_PLUGIN_FILE),
		array('leaflet.gpx'),null);

		wp_enqueue_script('zoomhome',
			plugins_url('leaflet-plugins/leaflet.zoomhome/leaflet.zoomhome.min.js',LEAFEXT_PLUGIN_FILE),
				array('wp_leaflet_map'), null);
		wp_enqueue_style('zoomhome',
			plugins_url('leaflet-plugins/leaflet.zoomhome/leaflet.zoomhome.css',LEAFEXT_PLUGIN_FILE),
				array('leaflet_stylesheet'), null);

	// echo '<pre>';
	//var_dump($atts);
	// echo '</pre>';
	global $all_files;
	global $all_points;
	//var_dump($all_files);
	//wp_die();
	wp_enqueue_style( 'my_elevation_css',
		plugins_url('css/multielevation.css',dirname(__FILE__)),
		array('elevation_css'), null);
	// custom js
	wp_enqueue_script('multielevation',
		plugins_url('js/multielevation.js',dirname(__FILE__)),
		array('elevation_js'), null);

	$options = get_option('leafext_values');
	if (!is_array($options )) {
		$theme = "lime-theme";
	} else if ($options['theme'] == "other") {
		$theme=$options['othertheme'];
	} else {
		$theme=$options['theme'].'-theme';
	}

	// Uebergabe der php Variablen an Javascript
	wp_localize_script( 'multielevation', 'params', array(
		'tracks' => $all_files,
		'points' => $all_points,
		'pluginsUrl' => plugin_dir_url(__DIR__),
		'theme' => $theme,
	));
	$text = '<div id="elevation-div" class="leaflet-control elevation"><p class="chart-placeholder">move mouse over a track...</p></div>';
	return $text;
}
add_shortcode('elevation-tracks', 'testleafext_elevation_tracks' );
