<?php
// Direktzugriff auf diese Datei verhindern:
defined( 'ABSPATH' ) or die();

//Shortcode: [hidemarkers]
function leafext_testmarkers_function(){
	include_once LEAFEXT_PLUGIN_DIR . '/pkg/JShrink/Minifier.php';
	$text = '
	<script>
		window.WPLeafletMapPlugin = window.WPLeafletMapPlugin || [];
		window.WPLeafletMapPlugin.push(function () {
			var map = window.WPLeafletMapPlugin.getCurrentMap();
			map.eachLayer(function(layer) {
				if (layer.options.type == "gpx" ) {
					//console.log(layer);
					var markers = L.markerClusterGroup();
					var rest = L.layerGroup();
					layer.options.onEachFeature = function (feature, geolayer) {
						if ( feature.properties && feature.properties.name ) {
							feature.properties.popupContent = feature.properties.name;
							geolayer.bindPopup(feature.properties.popupContent);
						}
						if ( feature.geometry.type == "Point" ) {
							markers.addLayer(geolayer);
						} else {
							rest.addLayer(geolayer);
						}
						map.removeLayer(layer);
					}
					map.addLayer(markers);
					map.addLayer(rest);
				}
			}); //map.eachLayer
			window.addEventListener("load", main);
		});
	</script>
	';
	$text = \JShrink\Minifier::minify($text);
	return $text;
}
add_shortcode('testmarkers', 'leafext_testmarkers_function' );

?>
