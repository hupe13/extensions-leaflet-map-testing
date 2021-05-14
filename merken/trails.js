(function() {
  function main() {
		if (!window.WPLeafletMapPlugin) {
			console.log("no plugin found!");
			return;
		}

		// iterate any of these: `maps`, `markers`, `markergroups`, `lines`, `circles`, `geojsons`
		var maps = window.WPLeafletMapPlugin.maps;
		  for (var i = 0, len = maps.length; i < len; i++) {
			     var map = maps[i];
           var control = L.control.layers(null, null, {
		           collapsed: false
	         }).addTo(map);
           var HikingTrails = L.tileLayer('https://tile.waymarkedtrails.org/{id}/{z}/{x}/{y}.png',
              {
		           id: 'hiking',
		           pointable: true,
		           attribution: '&copy; <a href="http://waymarkedtrails.org">Sarah Hoffmann</a> (<a href="https://creativecommons.org/licenses/by-sa/3.0/">CC-BY-SA</a>)',
             });
	         control.addOverlay(HikingTrails, "Hiking Routes");
			}
	}
	window.addEventListener("load", main);
})();
