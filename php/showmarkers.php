<?php
// Direktzugriff auf diese Datei verhindern:
defined( 'ABSPATH' ) or die();

//Shortcode: [showmarkers]
//geojson gpx mit Polyline und Markers
function leafext_gpx_markers_function($atts){
	include_once LEAFEXT_PLUGIN_DIR . '/pkg/JShrink/Minifier.php';

	if (is_array($atts)) {
		for ($i = 0; $i < count($atts); $i++) {
			if (isset($atts[$i])) {
				if ( strpos($atts[$i],"!") === false ) {
					$atts[$atts[$i]] = 1;
				} else {
					$atts[substr($atts[$i],1)] = 0;
				}
			}
		}
	}
	$defaults = array(
		'show' => true,
	);
	$options = shortcode_atts($defaults, $atts);
	$text = '
	<script>
		window.WPLeafletMapPlugin = window.WPLeafletMapPlugin || [];
		window.WPLeafletMapPlugin.push(function () {
			var map = window.WPLeafletMapPlugin.getCurrentMap();
			map.eachLayer(function(layer) {
				if (layer.options.type == "gpx" ) {
					//console.log(layer);
					';
					if ($options['show'])
						$text=$text.'
					var markers = L.markerClusterGroup();
					';
					$text=$text.'
					var rest = L.layerGroup();
					layer.options.onEachFeature = function (feature, geolayer) {
						if ( feature.properties && feature.properties.name ) {
							feature.properties.popupContent = feature.properties.name;
							geolayer.bindPopup(feature.properties.popupContent);
						}
						if ( feature.geometry.type == "Point" ) {';
							if ($options['show']) {
								$text=$text.$options['show'];
								$text=$text.'
							//console.log(feature.properties.name);
							markers.addLayer(geolayer);
							geolayer.bindTooltip(feature.properties.popupContent);
							geolayer.on("mouseover", function(e) {
								if ( !geolayer.getPopup().isOpen()) {
							 		map.closePopup();
								}
								if ( e.target.getPopup().isOpen()) {
							 		e.target.getTooltip().setOpacity(0.0);
								} else {
									e.target.getTooltip().setOpacity(1.0);
								}
							});
							geolayer.on("click", function(e) {
								if ( e.target.getPopup().isOpen()) {
							 		e.target.getTooltip().setOpacity(0.0);
								} else {
									e.target.getTooltip().setOpacity(1.0);
								}
							});
							';
							}
						$text=$text.'
						} else {
							rest.addLayer(geolayer);
							if ( feature.properties && feature.properties.name ) {
								geolayer.on("mouseover", function (e) {
									if ( !e.target.getPopup().isOpen()) {
								 		map.closePopup();
									}
	 								var popup = e.target.getPopup();
	 								popup.setLatLng(e.latlng).openOn(map);
 								});
							}
						}
						map.removeLayer(layer);
					} //
					';
					if ($options['show'])
						$text=$text.'
						map.addLayer(markers);
						';
					$text=$text.'
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
add_shortcode('showmarkers', 'leafext_gpx_markers_function' );

//shortcode [hidemarkers]
function leafext_hidemarkers1_function($atts){
	return do_shortcode( '[showmarkers !show]' );
}
add_shortcode('hidemarkers1', 'leafext_hidemarkers1_function' );
?>
