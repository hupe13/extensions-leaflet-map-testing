<?php
//Shortcode: [geojsoncluster]

function leafext_geojsoncluster_script(){
  $text = '
	<script>
  window.WPLeafletMapPlugin = window.WPLeafletMapPlugin || [];
  window.WPLeafletMapPlugin.push(function () {
    var map = window.WPLeafletMapPlugin.getCurrentMap();
    var map_id = map._leaflet_id;
    var maps=[];
    maps[map_id] = map;
    var markers = L.markerClusterGroup();

    var geojsons = window.WPLeafletMapPlugin.geojsons;
    if (geojsons.length > 0) {
      var geocount = geojsons.length;
      for (var j = 0, len = geocount; j < len; j++) {
        var geojson = geojsons[j];
        //console.log(geojson);
        if (map_id == geojsons[j]._map._leaflet_id) {
          geojson.on("ready", function () {
            //console.log(this.layer);

            this.layer.eachLayer(function(layer) {
    					//console.log(layer.feature);
              //console.log(layer.feature.properties);
              if (layer.feature.geometry.type == "Point" ) {
                //console.log(layer);
                //console.log(layer.feature.properties.name);
                //console.log(layer.getPopup());
                var content = layer.feature.properties.name;
                if (typeof content != "undefined") {
                  layer.bindTooltip(content);
                  layer.bindPopup(content);
                  console.log(content);
                  map.removeLayer(layer);
                }
                markers.addLayer(layer);
              } else {
                //console.log(layer);
              }
    				});
          });
        }
      }
    }
    map.addLayer(markers);
  });
	</script>
	';
	//$text = \JShrink\Minifier::minify($text);
	return $text;
}

function leafext_geojsoncluster_function(){
	leafext_enqueue_markercluster ();
	return leafext_geojsoncluster_script();
}
add_shortcode('geojsoncluster', 'leafext_geojsoncluster_function' );
