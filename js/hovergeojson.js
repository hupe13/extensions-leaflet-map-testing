// For use with only one map on a webpage

window.WPLeafletMapPlugin = window.WPLeafletMapPlugin || [];
window.WPLeafletMapPlugin.push(function () {
	var map = window.WPLeafletMapPlugin.getCurrentMap();
	
	if ( WPLeafletMapPlugin.geojsons.length > 0 ) {
		var geojsons = window.WPLeafletMapPlugin.geojsons;
		var geocount = geojsons.length;

		for (var j = 0, len = geocount; j < len; j++) {
			var geojson = geojsons[j];
			geojson.layer.on('mouseover', function () {
				//console.log("over");
				//console.log(this);
				this.eachLayer(function(layer) {
					if ( !layer.getPopup().isOpen()) {
						map.closePopup();
						var content = layer.getPopup().getContent();
						//console.log(content);
						layer.bindTooltip(content);
					}
				});
				this.setStyle({
					fillOpacity: 0.4,
					weight: 5
				});
				this.bringToFront();
			});
			geojson.layer.on('mouseout', function () {
				//console.log("out");
				this.setStyle({
					fillOpacity: 0.2,
					weight: 3
				});
			});
			
			geojson.layer.on('click', function (e) {
				//console.log('click');
				e.target.eachLayer(function(layer) {
					//console.log(layer);
					layer.unbindTooltip();
				});
			});
			
			geojson.layer.on('mousemove', function (e) {
			 	//console.log('move');
			 	e.target.eachLayer(function(layer) {
			// 		//console.log(layer);
			
					if ( !layer.getPopup().isOpen()) {
						var content = layer.getPopup().getContent();
						//console.log(content);
						layer.bindTooltip(content);
						layer.openTooltip(e.latlng);
					}
			 	});
				
            });
		}
	}
});
