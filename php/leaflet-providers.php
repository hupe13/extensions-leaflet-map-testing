<?php
/**
 * Functions for elevation shortcode
 * extensions-leaflet-map
 */
// Direktzugriff auf diese Datei verhindern:
defined( 'ABSPATH' ) or die();

//Shortcode: [providers ..."]

function leafext_providers_script($maps){
	$text = '<script>
	//https://github.com/leaflet-extras/leaflet-providers/blob/57d69ea6e75834235c607eab72cbb4da862ddc96/preview/preview.js#L56';
	$text = $text."
	function isOverlay (providerName, layer) {
		if (layer.options.opacity && layer.options.opacity < 1) {
			return true;
		}
		var overlayPatterns = [
			'^(OpenWeatherMap|OpenSeaMap|OpenSnowMap)',
			'OpenMapSurfer.(Hybrid|AdminBounds|ContourLines|Hillshade|ElementsAtRisk)',
			'Stamen.Toner(Hybrid|Lines|Labels)',
			'Hydda.RoadsAndLabels',
			'^JusticeMap',
			'OpenAIP',
			'OpenRailwayMap',
			'OpenFireMap',
			'SafeCast',
			'WaymarkedTrails.(hiking|cycling|mtb|slopes|riding|skating)'
		];

		return providerName.match('(' + overlayPatterns.join('|') + ')') !== null;
	};";
	//
	$text = $text.'
	window.WPLeafletMapPlugin = window.WPLeafletMapPlugin || [];
	window.WPLeafletMapPlugin.push(function () {
		var map = window.WPLeafletMapPlugin.getCurrentMap();
		var attributions = Object.keys(map.attributionControl._attributions);
		var defaultAttr = String(attributions);
		map.attributionControl._attributions = {};

		var baselayers = {};
		var overlays = {};
		
		map.eachLayer(function(layer) {
			if( layer instanceof L.TileLayer ) {
				map.removeLayer(layer);
				layer.options.attribution = defaultAttr;
				map.addLayer(layer);
				if(typeof layer.options.id !== "undefined") {
					var defaultname = layer.options.id;
				} else {
					var defaultname = "Default";
				}
				baselayers[defaultname] = layer;
			}
	 	});
		';
		foreach ($maps as $map) {
			$text = $text.'
			var layer = L.tileLayer.provider("'.$map.'");
			if (isOverlay("'.$map.'", layer)) {
				overlays["'.$map.'"] = layer;
			} else {
				baselayers["'.$map.'"] = layer;
			}';
		}
		$text = $text.'
		//console.log(baselayers);
		L.control.layers(baselayers,overlays).addTo(map);
	});
	</script>';
	//$text = \JShrink\Minifier::minify($text);
	return "\n".$text."\n";
}

function leafext_providers_function( $atts ) {
	leafext_enqueue_providers();
	$maps = explode ( ',', $atts['maps'] );
	return leafext_providers_script($maps);
}
add_shortcode('providers', 'leafext_providers_function' );
