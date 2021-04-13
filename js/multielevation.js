window.WPLeafletMapPlugin = window.WPLeafletMapPlugin || [];
window.WPLeafletMapPlugin.push(function () {
	var map = window.WPLeafletMapPlugin.getCurrentMap();

	var points = params.points;
	var tracks = params.tracks;
	console.log(points);
	//console.log(tracks);

	var opts = {
		points: {
			icon: {
				iconUrl: params.pluginsUrl + '/leaflet-plugins/leaflet-elevation-1.6.7/images/elevation-poi.png',
				iconSize: [12, 12],
			},
		},
		elevation: {
			theme: "lime-theme",
			detachedView: true,
			elevationDiv: '#elevation-div',
			followPositionMarker: true,
			zFollow: 15,
			legend: false,
			followMarker: false,
			downloadLink:false,
			polyline: { weight: 3,},
		},
		markers: {
			startIconUrl: null, // 'http://mpetazzoni.github.io/leaflet-gpx/pin-icon-start.png',
			endIconUrl: null, // 'http://mpetazzoni.github.io/leaflet-gpx/pin-icon-end.png',
			shadowUrl: null, // 'http://mpetazzoni.github.io/leaflet-gpx/pin-shadow.png',
//      	wptIconUrls : {
//      	  '': params.pluginsUrl + '/images/unsichtbar.png',
//     		},
		},
		gpx_options: {
			//parseElements: ['track'],
			parseElements: ['track','route'],
		}
	};

    var routes;
    routes = new L.gpxGroup(tracks, {
		points: points,
		points_options: opts.points,
		elevation: true,
		elevation_options: opts.elevation,
		marker_options: opts.markers,
		legend: true,
		distanceMarkers: false,
		gpx_options: opts.gpx_options,
    });

	map.on('eledata_added eledata_clear', function(e) {
		var p = document.querySelector(".chart-placeholder");
		if(p) {
			p.style.display = e.type=='eledata_added' ? 'none' : '';
		}
	});

    routes.addTo(map);
});
