// For use with only one map on a webpage
console.log("cluster.maxZoom");
console.log(cluster.zoom);
console.log("cluster.radius");
console.log(cluster.radius);

window.WPLeafletMapPlugin = window.WPLeafletMapPlugin || [];
window.WPLeafletMapPlugin.push(function () {
	var map = window.WPLeafletMapPlugin.getCurrentMap();
	if ( WPLeafletMapPlugin.markers.length > 0 ) {
		map.options.maxZoom = 19;
		clmarkers = L.markerClusterGroup({
			maxClusterRadius: function(radius)
			//	{ return 60; },
			//	{return ((radius <= 13) ? 50 : 30);},
				{ return cluster.radius; },
			spiderfyOnMaxZoom: true,
			// ab welcher Zoomstufe es nicht mehr tiefer geht, dann wird gespidert.
			disableClusteringAtZoom: cluster.zoom,
		});
		for (var i = 0; i < WPLeafletMapPlugin.markers.length; i++) {
			var a = WPLeafletMapPlugin.markers[i];
			clmarkers.addLayer(a);
			map.removeLayer(a);
		}
		clmarkers.addTo( map );
		WPLeafletMapPlugin.markers.push( clmarkers );
	}
});
