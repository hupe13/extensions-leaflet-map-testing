// For use with only one map on a webpage

window.WPLeafletMapPlugin = window.WPLeafletMapPlugin || [];
window.WPLeafletMapPlugin.push(function () {
	var map = window.WPLeafletMapPlugin.getCurrentMap();
	if ( WPLeafletMapPlugin.geojsons.length > 0 ) {
		var geojsons = window.WPLeafletMapPlugin.geojsons;
		var geocount = geojsons.length;

		for (var j = 0, len = geocount; j < len; j++) {
			var geojson = geojsons[j];

			geojson.layer.on('mouseover', function (e) {
				let i = 0;
				e.target.eachLayer(function(){ i += 1; });
				//console.log('mouseover has', i, 'layers.');
				if (i > 1) {
					e.sourceTarget.setStyle({
						fillOpacity: 0.4,
						weight: 5
					});
					e.sourceTarget.bringToFront();
				} else {
					e.target.eachLayer(function(layer) {
						layer.setStyle({
							fillOpacity: 0.4,
							weight: 5
						});
						layer.bringToFront();
					});
				}
			});

			geojson.layer.on('mouseout', function (e) {
				let i = 0;
				e.target.eachLayer(function(){ i += 1; });
				//console.log('mouseout has', i, 'layers.');
				if (i > 1) {
					geojson.resetStyle();
				} else {
					e.target.eachLayer(function(layer) {
						//console.log(layer);
						layer.setStyle({
							fillOpacity: 0.2,
							weight: 3
						});
					});
				}
			});

			geojson.layer.on('click', function (e) {
				//console.log('click');
				e.target.eachLayer(function(layer) {
					if (layer.getPopup().isOpen())
						layer.unbindTooltip();
				});
			});

			geojson.layer.on('mousemove', function (e) {
			 	let i = 0;
				e.target.eachLayer(function(){ i += 1; });
				//console.log('mousemove has', i, 'layers.');
				if (i > 1) {
					if ( !e.sourceTarget.getPopup().isOpen()) {
						map.closePopup();
						var content = e.sourceTarget.getPopup().getContent();
						e.sourceTarget.bindTooltip(content);
						e.sourceTarget.openTooltip(e.latlng);
					}
				} else {
					e.target.eachLayer(function(layer) {
						if ( !layer.getPopup().isOpen()) {
							map.closePopup();
							var content = layer.getPopup().getContent();
							layer.bindTooltip(content);
							layer.openTooltip(e.latlng);
						}
					});
				}
			});
		}
	}
});
