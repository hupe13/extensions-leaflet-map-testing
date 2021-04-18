// For use with only one map on a webpage

window.WPLeafletMapPlugin = window.WPLeafletMapPlugin || [];
window.WPLeafletMapPlugin.push(function () {
	var map = window.WPLeafletMapPlugin.getCurrentMap();
	//console.log(map);
	//console.log(window.WPLeafletMapPlugin);
	//map.eachLayer(function (layer) {
		//console.log(layer);
		//console.log(layer.getPopup());
	//});
	if ( WPLeafletMapPlugin.geojsons.length > 0 ) {
		var geojsons = window.WPLeafletMapPlugin.geojsons;
		var geocount = geojsons.length
		var isClicked = false;
		console.log('begin');
		console.log(isClicked);
		for (var j = 0, len = geocount; j < len; j++) {
			var geojson = geojsons[j];
			//console.log(geojson);
			geojson.layer.on('mouseover', function () {
				//console.log("over");
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
			geojson.layer.on('mouseover', function (e) {
				e.target.eachLayer(function(layer) {
					layer.openPopup();
				});
			});
			geojson.layer.on('click', function (e) {
				console.log('click');
				e.target.eachLayer(function(layer) {
					//console.log(layer);
					isClicked = true;
				});
				console.log(isClicked);
			});
			//Wenn ein Popup ein Link ist, kann man den nicht anklicken.
			//Fixed mit isClicked (?)
			//Klappt noch nicht, da mouseover popup = geojson mouseout
			geojson.layer.on('mousemove', function (e) {
				console.log('move');
				console.log(isClicked);
				e.target.eachLayer(function(layer) {
					//console.log(layer);
					if(!isClicked)
						layer.getPopup().setLatLng(e.latlng);
				});
            });
			geojson.layer.on('mouseout', function (e) {
				//console.log(e);
				console.log('mouseout');
				e.target.eachLayer(function(layer) {
					//Klappt irgendwie nicht, arbeitet nicht sauber
					//layer.closePopup();
					isClicked = false;
				});
				console.log(isClicked);
			});
		}
	}
});
