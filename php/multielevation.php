<?php
//Shortcode:
//[elevation-track file="'.$file.'" lat="'.$startlat.'" lng="'.$startlon.'" name="'.basename($file).'"]
//[elevation-tracks summary=1]

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
		'latlng' => array($atts['lat'],$atts['lng']),
		'name' => $atts['name'],
	);
	$all_points[]=$point;
	//var_dump($all_files);
}
add_shortcode('elevation-track', 'testleafext_elevation_track' );

//[elevation-tracks]
function testleafext_elevation_tracks_script( $all_files, $all_points, $theme, $summary, $slope ){
	include_once LEAFEXT_PLUGIN_DIR . '/pkg/JShrink/Minifier.php';
	$text = '
	<script>
	window.WPLeafletMapPlugin = window.WPLeafletMapPlugin || [];
	window.WPLeafletMapPlugin.push(function () {
		var map = window.WPLeafletMapPlugin.getCurrentMap();

		var points = '.json_encode($all_points).';
		var tracks = '.json_encode($all_files).';
		var theme =  '.json_encode($theme).';
		console.log(points);
		//console.log(tracks);

		var opts = {
			points: {
				icon: {
					iconUrl: "'.plugin_dir_url(__DIR__).'" + "/leaflet-plugins/leaflet-elevation-1.6.7/images/elevation-poi.png",
					iconSize: [12, 12],
				},
			},
			elevation: {
				theme: theme,
				detachedView: true,
				elevationDiv: "#elevation-div",
				followPositionMarker: true,
				zFollow: 15,
				legend: false,
				followMarker: false,
				downloadLink:false,
				polyline: { weight: 3,},
				summary: '.json_encode($summary).',
				slope: '.json_encode($slope).',
			},
			markers: {
				startIconUrl: null, // "http://mpetazzoni.github.io/leaflet-gpx/pin-icon-start.png",
				endIconUrl: null, // "http://mpetazzoni.github.io/leaflet-gpx/pin-icon-end.png",
				shadowUrl: null, // "http://mpetazzoni.github.io/leaflet-gpx/pin-shadow.png",
				// wptIcon and wptIconUrls seems to be a bug, if configured, elevation chart does not appear
				// console.log in gpx.js is commented out. Nervt.
				wptIcon: null,
				wptIconUrls: null, // params.pluginsUrl + "/leaflet-plugins/leaflet-gpx-1.5.2/pin-icon-wpt.png",
			},
			gpx_options: {
				//parseElements: ["track"],
				parseElements: ["track","route"],
			},
			legend_options:{
				collapsed: true,
			},
		};

		var mylocale = {
			"Altitude"				: "'.__("Altitude", "extensions-leaflet-map").'",
			"Total Length: "	: "'.__("Total Length", "extensions-leaflet-map").': ",
			"Max Elevation: "	: "'.__("Max Elevation", "extensions-leaflet-map").': ",
			"Min Elevation: "	: "'.__("Min Elevation", "extensions-leaflet-map").': ",
			"Total Ascent: "	: "'.__("Total Ascent", "extensions-leaflet-map").': ",
			"Total Descent: "	: "'.__("Total Descent", "extensions-leaflet-map").': ",
			"Min Slope: "			: "'.__("Min Slope", "extensions-leaflet-map").': ",
			"Max Slope: "			: "'.__("Max Slope", "extensions-leaflet-map").': ",
		};
		L.registerLocale("wp", mylocale);
		L.setLocale("wp");

	  var routes;
	  routes = new L.gpxGroup(tracks, {
			points: points,
			points_options: opts.points,
			elevation: true,
			elevation_options: opts.elevation,
			marker_options: opts.markers,
			legend: true,
			distanceMarkers: false,
			gpx_options: opts.gpx_options,
			legend_options: opts.legend_options,
	  });

		map.on("eledata_added eledata_clear", function(e) {
			var p = document.querySelector(".chart-placeholder");
			if(p) {
				p.style.display = e.type=="eledata_added" ? "none" : "";
			}
		});

	  routes.addTo(map);
	});

	window.WPLeafletMapPlugin = window.WPLeafletMapPlugin || [];
	window.WPLeafletMapPlugin.push(function () {
	  var map = window.WPLeafletMapPlugin.getCurrentMap();
		map.options.maxZoom = 19;
		var bounds = [];
		bounds = new L.latLngBounds();
		var zoomHome = [];
		zoomHome = L.Control.zoomHome();
		var zoomhomemap=false;
		map.on("zoomend", function(e) {
			//console.log("zoomend");
			//console.log( zoomhomemap );
			if ( ! zoomhomemap ) {
				//console.log(map.getBounds());
				zoomhomemap=true;
				zoomHome.addTo(map);
				zoomHome.setHomeBounds(map.getBounds());
			}
	  });

	  window.addEventListener("load", main);
	});
	</script>';
//$text = \JShrink\Minifier::minify($text);
return "\n".$text."\n";
}

function testleafext_elevation_tracks( $atts ){
	wp_enqueue_script( 'elevation_js',
		plugins_url('leaflet-plugins/leaflet-elevation-1.6.7/js/leaflet-elevation.min.js',
		LEAFEXT_PLUGIN_FILE),
	array('wp_leaflet_map'),null);
	wp_enqueue_style( 'elevation_css',
		plugins_url('leaflet-plugins/leaflet-elevation-1.6.7/css/leaflet-elevation.min.css',
		LEAFEXT_PLUGIN_FILE),
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

	wp_enqueue_style( 'my_elevation_css',
		plugins_url('css/multielevation.css',dirname(__FILE__)),
		array('elevation_css'), null);

	$options = get_option('leafext_values');
	if (!is_array($options )) {
		$theme = "lime-theme";
	} else if ($options['theme'] == "other") {
		$theme=$options['othertheme'];
	} else {
		$theme=$options['theme'].'-theme';
	}

	$chart_options = shortcode_atts( array('summary' => false), $atts);

	//Parameters see the sources from https://github.com/Raruto/leaflet-elevation
	if ( ! $chart_options['summary'] ) {
		$summary = false;
		$slope = false;
	} else {
		$summary = "inline";
		$slope = "summary";
	}

	$text = testleafext_elevation_tracks_script( $all_files, $all_points, $theme, $summary, $slope);
	$text = $text.'<div id="elevation-div" class="leaflet-control elevation"><p class="chart-placeholder">move mouse over a track...</p></div>';
	return $text;
}
add_shortcode('elevation-tracks', 'testleafext_elevation_tracks' );
