<?php
/**
* Functions for leaflet-search shortcode
* extensions-leaflet-map
* leaflet-search knows: L.Marker L.CircleMarker L.LayerGroup L.Path L.Polyline L.Polygon L.LayerGroup, GeoJson
* implemented: L.Marker, GeoJson
* Leaflet-Map knows CircleMarker only in leaflet-geojson
*/
// Direktzugriff auf diese Datei verhindern:
defined( 'ABSPATH' ) or die();

function testleafext_leafletsearch_function($atts,$content,$shortcode) {
  $text = leafext_should_interpret_shortcode($shortcode,$atts);
  if ( $text != "" ) {
    return $text;
  } else {
    $defaults=array();
    $params = leafext_search_params();
    foreach($params as $param) {
      $defaults[$param['param']] = $param['default'];
    }
    $atts1=leafext_case(array_keys($defaults),leafext_clear_params($atts));
    $options = shortcode_atts($defaults, $atts1);
    if ($options['marker'] == '') unset($options['marker']);
    if ($options['container'] == '') {
      unset($options['container']);
    } else {
      $options['collapsed'] = false;
    }
    if (strpos($options['textPlaceholder'],'"') !== false) {
      $options['textPlaceholder'] = str_replace('"','\"',$options['textPlaceholder']);
    }
    // $options['textPlaceholder'] = htmlentities($options['textPlaceholder']); // geht nicht.
    leafext_enqueue_leafletsearch ();
    //var_dump(leafext_java_params($options));wp_die();
    return testleafext_leafletsearch_script($options,trim(preg_replace('/\s+/', ' ',leafext_java_params($options))));
  }
}
add_shortcode('leaflet-search-test', 'testleafext_leafletsearch_function' );

function testleafext_leafletsearch_script($options,$jsoptions){
  $text = '<script><!--';
  ob_start();
  ?>/*<script>*/
  window.WPLeafletMapPlugin = window.WPLeafletMapPlugin || [];
  window.WPLeafletMapPlugin.push(function () {
    let att_property = <?php echo json_encode($options['propertyName']); ?>;
    let att_zoom = <?php echo json_encode($options['zoom']); ?>;

    if (typeof searchcontrol == "undefined" ) {
      var maps=[];
      var searchcontrol = [];
    }
    var map = window.WPLeafletMapPlugin.getCurrentMap();
    var map_id = map._leaflet_id;
    if (typeof maps[map_id] == "undefined" ) {
      maps[map_id] = map;
      searchcontrol[map_id] = [];
    }
    if (typeof searchcontrol[map_id][att_property] == "undefined") {
      searchcontrol[map_id][att_property] = "found";
      console.log("mapid: "+map_id);
      console.log(<?php echo json_encode($jsoptions);?>);

      if ( WPLeafletMapPlugin.markers.length > 0 ) {
        //console.log("markers "+WPLeafletMapPlugin.markers.length);
        var markersLayer = new L.LayerGroup();	//layer contain searched elements
        let duplicates = {};
        for (var i = 0; i < WPLeafletMapPlugin.markers.length; i++) {
          if ( WPLeafletMapPlugin.markers[i]._map !== null ) {
            if (map_id == WPLeafletMapPlugin.markers[i]._map._leaflet_id) {
              //console.log("Marker");
              let a = WPLeafletMapPlugin.markers[i];
              //console.log(a);

              let this_options = a.getIcon().options;
              if (this_options.hasOwnProperty(att_property)) {
                if (typeof a.options[att_property] != "undefined") {
                  //
                } else {
                  a.options[att_property] = this_options[att_property];
                }
              } else {
                if (att_property == "popupContent") {
                  if (typeof a.getPopup() != "undefined") {
                    if ( typeof a.getPopup().getContent() != "undefined" ) {
                      a.options[att_property] = a.getPopup().getContent();
                    }
                  }
                }
              }

              if (a.options.hasOwnProperty(att_property)) {
                //console.log("added "+i);
                let search = a.options[att_property];
                if (typeof duplicates[search] == "undefined" ) {
                  duplicates[search] = 1;
                } else {
                  duplicates[search] = duplicates[search] + 1;
                }
                a.options["searchindex"] = i;
                markersLayer.addLayer(a);
              }
              //map.removeLayer(a);
            } // _map._leaflet_id
          } // has markers[i]._map
        } // loop markers

        // console.log(markersLayer);
        // console.log(Object.keys(markersLayer._layers).length);
        if (Object.keys(markersLayer._layers).length > 0) {
          if (searchcontrol[map_id][att_property] == "found") {
            searchcontrol[map_id][att_property] = "added";

            markersLayer.eachLayer(function(layer) {
              let search = layer.options[att_property];
              if (duplicates[search] > 1) {
                layer.options[att_property] = layer.options[att_property] + " | "+ layer.options["searchindex"];
              }
            });

            map.addLayer(markersLayer);
            var markerSearchControl = new L.Control.Search({
              layer: markersLayer,
              <?php echo $jsoptions;?>
              initial: false,
              moveToLocation: function(latlng, title, map) {
                //console.log( title);
                map.fitBounds(L.latLngBounds([latlng.layer.getLatLng()]));
                map.setZoom(att_zoom);
              }
            }
          );
          map.addControl( markerSearchControl );
          markerSearchControl.on("search:locationfound", function(e) {
            if (typeof e.layer.getPopup() != "undefined") e.layer.openPopup();
            e.sourceTarget._input.blur();
          });
          markerSearchControl.on("search:cancel", function(e) {
            //console.log(e);
            if (e.target.options.hideMarkerOnCollapse) {
              e.target._map.removeLayer(this._markerSearch);
            }
          });
        }
      } else {
        console.log("Nothing to search in Markers");
      }
    } // markers.length

    //
    var geojsons = window.WPLeafletMapPlugin.geojsons;
    var geocount = geojsons.length;
    if (geocount > 0) {
      //console.log(geojsons);
      var geojsonLayers = new L.layerGroup();

      for (var j = 0, len = geocount; j < len; j++) {
        if (map_id == geojsons[j]._map._leaflet_id) {
          geojsons[j].on("ready", function (e) {
            let duplicates = {};
            j = 0;
            e.target.eachLayer(function(layer) {
              if (att_property == "popupContent") {
                if (typeof layer.getPopup() != "undefined") {
                  layer.feature.properties['popupContent'] = layer.getPopup().getContent();
                }
              }
              let search = layer.feature.properties[att_property];
              if (typeof duplicates[search] == "undefined" ) {
                duplicates[search] = 1;
              } else {
                duplicates[search] = duplicates[search] + 1;
              }
              layer.feature.properties["searchindex"] = j;
              j++;
            });
            //console.log(duplicates);
            e.target.eachLayer(function(layer) {
              let search = layer.feature.properties[att_property];
              if (duplicates[search] > 1) {
                layer.feature.properties[att_property] = layer.feature.properties[att_property] + " | "+ layer.feature.properties["searchindex"];
                //console.log(layer.feature.properties);
              }
            });
          });
          geojsons[j].addTo(geojsonLayers);
        }
      }
      if (Object.keys(geojsonLayers._layers).length > 0) {
        //console.log(Object.keys(geojsonLayers._layers).length);
        if (searchcontrol[map_id][att_property] == "found") {
          searchcontrol[map_id][att_property] = "added";
          map.addLayer(geojsonLayers);
          var geojsonSearchControl = new L.control.search({
            layer: geojsonLayers,
            <?php echo $jsoptions;?>
            initial: false,
            moveToLocation: function(latlng, title, map) {
              //console.log(latlng, title, map);
              //console.log(latlng.layer);
              //console.log(latlng.layer.feature.geometry.type);
              if (latlng.layer.feature.geometry.type == "Point") {
                map.fitBounds(L.latLngBounds([latlng]));
                map.setZoom(att_zoom);
                //map.setView(latlng, att_zoom); // access the zoom
              } else {
                map.fitBounds( latlng.layer.getBounds() );
                var zoom = map.getBoundsZoom(latlng.layer.getBounds());
                map.setView(latlng, zoom); // access the zoom
              }
            }
          });
          map.addControl( geojsonSearchControl );  //inizialize search control
          geojsonSearchControl.on("search:locationfound", function(e) {
            //console.log("search:locationfound" );
            if(e.layer._popup) e.layer.openPopup([e.latlng.lat, e.latlng.lng]);
            e.sourceTarget._input.blur();
          });
          geojsonSearchControl.on("search:cancel", function(e) {
            //console.log(e);
            if (e.target.options.hideMarkerOnCollapse) {
              e.target._map.removeLayer(this._markerSearch);
            }
          });
        } else {
          console.log("Nothing to search in Geojsons");
        }
      }
    }
  }
});

<?php
$javascript = ob_get_clean();
$text = $text . $javascript . '//-->'."\n".'</script>';
//$text = \JShrink\Minifier::minify($text);
return "\n".$text."\n";
}
