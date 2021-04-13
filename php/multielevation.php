<?php
//Shortcode: [elevation-tracks gpx="...url..."]
// For use with only one map on a webpage

//[elevation-track file="'.$file.'" lat="'.$startlat.'" lon="'.$startlon.'" name="'.basename($file).'"]
function testleafext_elevation_track( $atts ){
	// echo '<pre>';
	//var_dump($atts);
	// echo '</pre>';
	global $all_files;
	if (!is_array($all_files)) $all_files = array();
	$all_files[]=$atts['file'];
	//
	global $all_tracks;
	if (!is_array($all_tracks)) $all_tracks = array();
	$point = array(
		'latlng' => array($atts['lat'],$atts['lon']),
		'name' => $atts['name'],
	);
	$all_tracks[]=$point;
	//var_dump($all_files);
}
add_shortcode('elevation-track', 'testleafext_elevation_track' );

//[elevation-tracks]
function testleafext_elevation_tracks( $atts ){
//	wp_enqueue_script( 'leaflet_ui',"https://unpkg.com/leaflet-ui@0.4.7/dist/leaflet-ui.js",
//		array('wp_leaflet_map'),null);

//wp_enqueue_script('fm-script3', "https://cdnjs.cloudflare.com/ajax/libs/d3/6.5.0/d3.min.js", array('jquery'), '3.1.0', true);

	// wp_enqueue_script( 'geometryutil_js',
	// 	plugins_url('../js/leaflet.geometryutil.js',__FILE__),
	// 	array('wp_leaflet_map'),null);
	wp_enqueue_script( 'elevation_js',
		plugins_url('leaflet-plugins/leaflet-elevation-1.6.7/js/leaflet-elevation.min.js',LEAFEXT_PLUGIN_FILE),
//		array('geometryutil_js'),null);
	array('wp_leaflet_map'),null);
	wp_enqueue_style( 'elevation_css',
		plugins_url('leaflet-plugins/leaflet-elevation-1.6.7/css/leaflet-elevation.min.css',LEAFEXT_PLUGIN_FILE),
		array('leaflet_stylesheet'),null);
	wp_enqueue_script('leaflet.gpx',
		plugins_url('leaflet-plugins/leaflet-gpx-1.5.2/gpx.js',
		TESTLEAFEXT_PLUGIN_FILE),
		array('elevation_js'),null);
	wp_enqueue_script('leaflet.gpxgroup',
		plugins_url('leaflet-plugins/leaflet-elevation-1.6.7/libs/leaflet-gpxgroup.js',TESTLEAFEXT_PLUGIN_FILE),
		array('leaflet.gpx'),null);
	// language
	$lang = get_locale();
	if ( strlen( $lang ) > 0 ) {
		$lang = explode( '_', $lang )[0];
	}
	if( file_exists( LEAFEXT_PLUGIN_DIR . 'locale/elevation_'.$lang.'.js') ) {
		wp_enqueue_script('elevation_lang',
			plugins_url('locale/elevation_'.$lang.'.js',LEAFEXT_PLUGIN_FILE),
			array('elevation_js'), null);
	}
	// echo '<pre>';
	//var_dump($atts);
	// echo '</pre>';
	global $all_files;
	global $all_tracks;
	//var_dump($all_files);
	//wp_die();
	// custom js
	wp_enqueue_script('multielevation',
		plugins_url('js/multielevation.js',dirname(__FILE__)),
		array('elevation_js'), null);

	// Uebergabe der php Variablen an Javascript
	wp_localize_script( 'multielevation', 'params', array(
//		'points' => $params['points'],
		'tracks' => $all_files,
		'points' => $all_tracks,
		'pluginsUrl' => plugin_dir_url(__DIR__),
	));
}
add_shortcode('elevation-tracks', 'testleafext_elevation_tracks' );
