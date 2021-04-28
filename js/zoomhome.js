//zoomhomemap
(function() {
	console.log(zoomhomemap);
	function main() {
		// iterate any of these: `maps`, `markers`, `markergroups`, `lines`, `circles`, `geojsons`
		var maps = window.WPLeafletMapPlugin.maps;
		console.log("maps "+maps.length);

		for (var i = 0, mapslen = maps.length; i < mapslen; i++) {
			var map = maps[i];
			var zoom = 0;
			var bounds = new L.latLngBounds();
			var zoomHome = L.Control.zoomHome();

			map.on("eledata_loaded", function(e) {
				console.log("elevation loaded");
				bounds.extend(e.layer.getBounds());
				zoomHome.setHomeBounds(bounds);
				map.fitBounds(bounds);
			});

			//
			var lines = window.WPLeafletMapPlugin.lines;
			if (lines.length > 0) {
				zoom++;
				console.log("lines "+lines.length);
				for (var k = 0, len = lines.length; k < len; k++) {
					var line = lines[k];
					bounds.extend(line.getBounds());
				}
			}
			//
			var markers = window.WPLeafletMapPlugin.markers;
			if (markers.length > 0) {
				console.log("markers "+markers.length);
				zoom++;
				var markerArray = [];
				for (var m = 0, len = markers.length; m < len; m++) {
					markerArray.push(markers[m]);
				}
				var group = L.featureGroup(markerArray);
				bounds.extend(group.getBounds());
				//map.fitBounds(bounds);
			}
			//
			var geojsons = window.WPLeafletMapPlugin.geojsons;
			if (geojsons.length > 0) {
				zoom++;
				console.log("geojsons "+geojsons.length);
				var geocount = geojsons.length;
				zoomHome.addTo(map);
				for (var j = 0, len = geocount; j < len; j++) {
					var geojson = geojsons[j];
					geojson.on('ready', function () {
 						bounds.extend(this.getBounds());
						if (bounds.isValid()) {
							zoomHome.setHomeBounds(bounds);
							//map.fitBounds(bounds);
						}
 					});
 				}
			}
			//
			var markergroups = window.WPLeafletMapPlugin.markergroups;
			if (markergroups.length > 0) {
				console.log("markergroups "+markergroups.length);
			}
			//
			if ( zoom > 0 ) {
				if (bounds.isValid()) {
					zoomHome.addTo(map);
					zoomHome.setHomeBounds(bounds);
					map.options.maxZoom = 19;
					if (zoomhomemap.fit) {
						console.log("fit true");
						console.log(map.getZoom());
						map.fitBounds(bounds);
						//if (map.getZoom() > 14 && zoom == 1) {
						//	map.setZoom(14);
						//}
					}
				}
			}
		}
	}
window.addEventListener("load", main);
})();
