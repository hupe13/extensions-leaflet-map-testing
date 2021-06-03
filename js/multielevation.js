window.WPLeafletMapPlugin = window.WPLeafletMapPlugin || [];
window.WPLeafletMapPlugin.push(function () {
	var map = window.WPLeafletMapPlugin.getCurrentMap();

	var points = params.points;
	var tracks = params.tracks;
	var theme =  params.theme;
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
			theme: theme,
			detachedView: true,
			elevationDiv: '#elevation-div',
			followPositionMarker: true,
			zFollow: 15,
			legend: false,
			followMarker: false,
			downloadLink:false,
			polyline: { weight: 3,},
			summary: "inline",
			slope: "summary",
		},
		markers: {
			startIconUrl: null, // 'http://mpetazzoni.github.io/leaflet-gpx/pin-icon-start.png',
			endIconUrl: null, // 'http://mpetazzoni.github.io/leaflet-gpx/pin-icon-end.png',
			shadowUrl: null, // 'http://mpetazzoni.github.io/leaflet-gpx/pin-shadow.png',
			wptIconUrls: null,
		},
		gpx_options: {
			//parseElements: ['track'],
			parseElements: ['track','route'],
		},
		legend_options:{
			collapsed: true,
		},
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
		legend_options: opts.legend_options,
    });

	map.on('eledata_added eledata_clear', function(e) {
		var p = document.querySelector(".chart-placeholder");
		if(p) {
			p.style.display = e.type=='eledata_added' ? 'none' : '';
		}
	});

    routes.addTo(map);
});

window.WPLeafletMapPlugin = window.WPLeafletMapPlugin || [];
window.WPLeafletMapPlugin.push(function () {
  var map = window.WPLeafletMapPlugin.getCurrentMap();
	map.options.maxZoom = 19;
	var bounds = [];
	bounds = new L.latLngBounds();
	var zoomHome = [];
	zoomHome = L.Control.zoomHome();
	var zoomhomemap=false;
	map.on("zoomend", function(e) {
		//console.log("zoomend");
		//console.log( zoomhomemap );
		if ( ! zoomhomemap ) {
			//console.log(map.getBounds());
			zoomhomemap=true;
			zoomHome.addTo(map);
			zoomHome.setHomeBounds(map.getBounds());
		}
  });

  window.addEventListener("load", main);
});