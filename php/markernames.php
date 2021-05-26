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
							geolayer.bindTooltip(feature.properties.popupContent);
						} else {
							rest.addLayer(geolayer);
							if ( feature.properties && feature.properties.name ) {
								geolayer.on("mouseover", function (e) {
	 								var popup = e.target.getPopup();
	 								popup.setLatLng(e.latlng).openOn(map);
 								});
 								geolayer.on("mouseout", function(e) {
									e.target.closePopup();
 								});
 								// update popup location
 								//geolayer.on("mousemove", function (e) {
	 								//popup.setLatLng(e.latlng).openOn(map);
 								//});
							}
						}
						map.removeLayer(layer);
					} //
					map.addLayer(markers);
					map.addLayer(rest);
				} //if layer.options.type
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
