<?php
// Direktzugriff auf diese Datei verhindern:
defined( 'ABSPATH' ) or die();

include TESTLEAFEXT_PLUGIN_DIR . '/php/elevation_functions.php';

//Shortcode: [elevation gpx="...url..."]

function leafexttest_elevation_script($gpx,$theme,$settings){
	$text = '
	<script>
	window.WPLeafletMapPlugin = window.WPLeafletMapPlugin || [];
	window.WPLeafletMapPlugin.push(function () {
		var map = window.WPLeafletMapPlugin.getCurrentMap();
		var elevation_options = {
		//lime-theme (default), magenta-theme, steelblue-theme, purple-theme, yellow-theme, lightblue-theme
			theme: '.json_encode($theme).',
		';
    foreach ($settings as $k => $v) {
      $text = $text. "$k: ";
      $value = $v == "0" ? "false" : ($v == "1" ? "true" : ( $k == "polyline" ? $v : '"'.$v.'"'));
      $text = $text.$value;
      $text = $text.",\n";
    }
	$text = $text.'	};
		var mylocale = {
			"Altitude"				: "'.__("Altitude", "extensions-leaflet-map").'",
			"Total Length: "	: "'.__("Total Length", "extensions-leaflet-map").': ",
			"Max Elevation: "	: "'.__("Max Elevation", "extensions-leaflet-map").': ",
			"Min Elevation: "	: "'.__("Min Elevation", "extensions-leaflet-map").': ",
			"Total Ascent: "	: "'.__("Total Ascent", "extensions-leaflet-map").': ",
			"Total Descent: "	: "'.__("Total Descent", "extensions-leaflet-map").': ",
			"Min Slope: "		: "'.__("Min Slope", "extensions-leaflet-map").': ",
			"Max Slope: "		: "'.__("Max Slope", "extensions-leaflet-map").': ",
		};
		L.registerLocale("wp", mylocale);
		L.setLocale("wp");

		// Instantiate elevation control.
		var controlElevation = L.control.elevation(elevation_options);
		var track_options= { url: "'.$gpx.'" };
		controlElevation.addTo(map);
		// Load track from url (allowed data types: "*.geojson", "*.gpx")
		controlElevation.load(track_options.url);

	});
	</script>';
	//$text = \JShrink\Minifier::minify($text);
	return "\n".$text."\n";
}

function leafexttest_elevation_function( $atts ) {
	leafext_enqueue_elevation ();
	//
  $atts1=leafext_elevation_case(leafext_clear_params($atts));
	$options = shortcode_atts( array_merge(array('gpx' => false),leafext_elevation_settings()), $atts1);
  if ( ! $options['gpx'] ) wp_die("No gpx track!");
  $track = $options['gpx'];
  unset($options['gpx']);
	if ( array_key_exists('theme', $atts) ) {
		$theme = $atts['theme'];
	} else {
  	$theme = leafext_elevation_theme();
	}
	unset($options['theme']);
  return leafexttest_elevation_script($track,$theme,$options);
}
add_shortcode('testelevation', 'leafexttest_elevation_function' );
