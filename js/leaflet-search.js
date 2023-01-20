console.log(att_property,att_zoom);
var map = window.WPLeafletMapPlugin.getCurrentMap();
var map_id = map._leaflet_id;

if ( WPLeafletMapPlugin.markers.length > 0 ) {
  console.log("markers "+WPLeafletMapPlugin.markers.length);
  var markersLayer = new L.LayerGroup();	//layer contain searched elements
  for (var i = 0; i < WPLeafletMapPlugin.markers.length; i++) {
    if ( WPLeafletMapPlugin.markers[i]._map !== null ) {
      if (map_id == WPLeafletMapPlugin.markers[i]._map._leaflet_id) {
        // console.log("Marker");
        let a = WPLeafletMapPlugin.markers[i];
        if (a.options.hasOwnProperty(att_property)) {
          markersLayer.addLayer(a);
        }
        //map.removeLayer(a);
      } // _map._leaflet_id
    } // has markers[i]._map
  } // loop markers

  // console.log(markersLayer);
  // console.log(Object.keys(markersLayer._layers).length);
  if (Object.keys(markersLayer._layers).length > 0) {
    console.log(Object.keys(markersLayer._layers).length);
    map.addLayer(markersLayer);
    var markerSearchControl = new L.Control.Search({
      position:"topright",
      layer: markersLayer,
      propertyName: att_property,
      initial: false,
      zoom: att_zoom,
      marker: false,
      //hideMarkerOnCollapse: true
    });
    map.addControl( markerSearchControl );
    markerSearchControl.on("search:locationfound", function(e) {
      //console.log("search:locationfound" );
      //console.log(e);
      if(e.layer._popup) e.layer.openPopup();
    });
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
      geojsons[j].addTo(geojsonLayers);
    }
  }
  if (Object.keys(geojsonLayers._layers).length > 0) {
    console.log(Object.keys(geojsonLayers._layers).length);
    map.addLayer(geojsonLayers);
    var geojsonSearchControl = new L.control.search({
      layer: geojsonLayers,
      initial: false,
      propertyName: att_property,
      hideMarkerOnCollapse: true,
      moveToLocation: function(latlng, title, map) {
        //console.log(latlng, title, map);
        //console.log(latlng.layer);
        //console.log(latlng.layer.feature.geometry.type);

        if (latlng.layer.feature.geometry.type == "Point") {
          map.setView(latlng, att_zoom); // access the zoom
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
      //console.log(e);
      if(e.layer._popup) e.layer.openPopup();
    });
  } else {
    console.log("Nothing to search in Geojsons");
  }
}
