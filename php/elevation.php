<?php
/**
 * Functions for elevation shortcode
 * extensions-leaflet-map
 */
// Direktzugriff auf diese Datei verhindern:
defined( 'ABSPATH' ) or die();

//Shortcode: [elevation gpx="...url..."]

function testleafext_elevation_script($gpx,$theme,$settings){
	$text = '<script>
	window.WPLeafletMapPlugin = window.WPLeafletMapPlugin || [];
	window.WPLeafletMapPlugin.push(function () {
		var map = window.WPLeafletMapPlugin.getCurrentMap();
		var elevation_options = {
		//lime-theme (default), magenta-theme, steelblue-theme, purple-theme, yellow-theme, lightblue-theme
			theme: '.json_encode($theme).',
		';
		//old settings
		if ( $settings['summary'] == "1" ) {
			$text = $text.'
				summary: "inline",
				slope: "summary",
				speed:  false,
				acceleration:  false,
				time: false,
				downloadLink: false,
				preferCanvas: false,
				legend: false,
				polyline:  { weight: 3, },
				';
				//old settings end
		} else {
			foreach ($settings as $k => $v) {
				switch ($k) {
					case "polyline":
						$text = $text. "$k: ". $v .',';
						unset ($settings[$k]);
						break;
					default:
				}
			}
			$text = $text.leafext_java_params ($settings);

		}//old settings end
		
		$text = $text.'
				detached: true,
				collapsed: false,
				';
	// autohide: true, ohne Wirkung, siehe https://github.com/Raruto/leaflet-elevation/blob/c9ad81ef609d12d0020fe82429299f8b53cc3656/src/leaflet-elevation.js#L1304

	$text = $text.'	};
		var mylocale = {
			"Altitude"	: "'.__("Altitude", "extensions-leaflet-map").'",
			"Total Length: "	: "'.__("Total Length", "extensions-leaflet-map").': ",
			"Max Elevation: "	: "'.__("Max Elevation", "extensions-leaflet-map").': ",
			"Min Elevation: "	: "'.__("Min Elevation", "extensions-leaflet-map").': ",
			"Total Ascent: "	: "'.__("Total Ascent", "extensions-leaflet-map").': ",
			"Total Descent: "	: "'.__("Total Descent", "extensions-leaflet-map").': ",
			"Min Slope: "	: "'.__("Min Slope", "extensions-leaflet-map").': ",
			"Max Slope: "	: "'.__("Max Slope", "extensions-leaflet-map").': ",
			"Speed: "	: "'.__("Speed", "extensions-leaflet-map").': ",
			"Min Speed: "	: "'.__("Min Speed", "extensions-leaflet-map").': ",
			"Max Speed: "	: "'.__("Max Speed", "extensions-leaflet-map").': ",
			"Avg Speed: "	: "'.__("Avg Speed", "extensions-leaflet-map").': ",
			"Acceleration: "	: "'.__("Acceleration", "extensions-leaflet-map").': ",
			"Min Acceleration: "	: "'.__("Min Acceleration", "extensions-leaflet-map").': ",
			"Max Acceleration: "	: "'.__("Max Acceleration", "extensions-leaflet-map").': ",
			"Avg Acceleration: "	: "'.__("Avg Acceleration", "extensions-leaflet-map").': ",
		};
		L.registerLocale("wp", mylocale);
		L.setLocale("wp");

		// Instantiate elevation control.
		var controlElevation = L.control.elevation(elevation_options);
		var track_options= { url: "'.$gpx.'" };
		controlElevation.addTo(map);
		
		var controlButton = L.easyButton(
			"<i class=\"fa fa-area-chart\" aria-hidden=\"true\"></i>",
			function(btn, map) { 
				controlElevation._toggle(); },
				"Elevation",
				//{ position: "bottomleft" }
				).addTo( map );
		
		// Load track from url (allowed data types: "*.geojson", "*.gpx")
		controlElevation.load(track_options.url);
	
	
		map.on("eledata_added", function(e) {
			//console.log("eledata_added");
			//Ja 2x!!! Koennte man als Parameter setzen
			controlElevation._toggle();
			controlElevation._toggle();
		});
		
	});
	</script>';
	$text = \JShrink\Minifier::minify($text);
	return "\n".$text."\n";
}

function testleafext_elevation_function( $atts ) {
	if ( ! $atts['gpx'] ) {
		$text = "[elevation ";
		foreach ($atts as $key=>$item){
			$text = $text. "$key = $item ";
		}
		$text = $text. "]";
		return $text;
	}
	leafext_enqueue_elevation ();
	leafext_enqueue_easybutton();
	
	$atts1=leafext_case(array_keys(leafext_elevation_settings()),leafext_clear_params($atts));
	$options = shortcode_atts(leafext_elevation_settings(), $atts1);

	$track = $atts['gpx'];
	
	if ( array_key_exists('theme', $atts) ) {
		$theme = $atts['theme'];
	} else {
		$theme = leafext_elevation_theme();
	}
	unset($options['theme']);
	return testleafext_elevation_script($track,$theme,$options);
}
add_shortcode('testelevation', 'testleafext_elevation_function' );
