<?php
//Parameter and Values
function leafext_elevation_params() {
	$params = array(

		// Default chart colors: theme lime-theme, magenta-theme, ...
		//theme: "lightblue-theme",

		// Chart container outside/inside map container
		//detached: true,
		//array('detached', 'Chart container outside/inside map container', true, 1),

		// if (detached), the elevation chart container
		//elevationDiv: "#elevation-div",

		// if (!detached) autohide chart profile on chart mouseleave
		//autohide: false,
		//array('autohide', 'if (!detached) autohide chart profile on chart mouseleave', false, 1),

		// if (!detached) initial state of chart profile control
		//collapsed: false,
		//array('collapsed', 'if (!detached) initial state of chart profile control', false, 1),

		// if (!detached) control position on one of map corners
		//position: "topright",

		// Autoupdate map center on chart mouseover.
		//followMarker: true,
		array('followMarker', 'Autoupdate map center on chart mouseover.', true, 1),

		// Autoupdate map bounds on chart update.
		//autofitBounds: true,
		//array('autofitBounds', 'Autoupdate map bounds on chart update.', true, 1),

		// Chart distance/elevation units.
		//imperial: false,
		array('imperial', 'Chart distance/elevation units.', false, 1),

		// [Lat, Long] vs [Long, Lat] points. (leaflet default: [Lat, Long])
		//reverseCoords: false,
		array('reverseCoords', '[Lat, Long] vs [Long, Lat] points. (leaflet default: [Lat, Long])', false, 1),

		// Acceleration chart profile: true || "summary" || "disabled" || false
		//acceleration: false,
		array('acceleration', 'Acceleration chart profile', false, array(true,"summary","disabled",false)),

		// Slope chart profile: true || "summary" || "disabled" || false
		//slope: false,
		array('slope', 'Slope chart profile', false, array(true,"summary","disabled",false)),

		// Speed chart profile: true || "summary" || "disabled" || false
		//speed: false,
		array('speed', 'Speed chart profile', false, array(true,"summary","disabled",false)),

		// Display time info: true || "summary" || false
		//time: false,
		array('time', 'Display time info', false, array(true,"summary",false)),

		// Display distance info: true || "summary"
		//distance: true,
		array('distance', 'Display distance info', true, array(true,"summary")),

		// Display altitude info: true || "summary"
		//altitude: true,
		array('altitude', 'Display altitude info', true, array(true,"summary")),

		// Summary track info style: "line" || "multiline" || false
		//Is this an error: line/inline ?
		//summary: 'multiline',
		array('summary', 'Summary track info style:', 'multiline', array("inline","multiline",false)),

		//hupe13: Download Link
		array('downloadLink', 'downloadLink', false, 1),

		// Toggle chart ruler filter.
		//ruler: true,
		array('ruler', 'Toggle chart ruler filter.', true, 1),

		// Toggle chart legend filter.
		//legend: true,
		array('legend', 'Toggle chart legend filter.', true, 1),

		// Toggle "leaflet-almostover" integration
		//almostOver: true,
		array('almostOver', 'Toggle "leaflet-almostover" integration', true, 1),

		// Toggle "leaflet-distance-markers" integration
		//distanceMarkers: false,
		array('distanceMarkers', 'Toggle "leaflet-distance-markers" integration', false, 1),

		// Render chart profiles as Canvas or SVG Paths
		//preferCanvas: true
		array('preferCanvas', 'Render chart profiles as Canvas or SVG Paths', true, 1),
	);
	return $params;
}

function leafext_elevation_case ($array) {
	$params=array_keys(leafext_elevation_settings());
	foreach ($params as $param) {
		if (strtolower($param) != $param) {
			if (isset($array[strtolower($param)])) {
				$array[$param] = $array[strtolower($param)];
				unset($array[strtolower($param)]);
			}
		}
	}
	return $array;
}

function leafext_array_find($needle, $haystack) {
	foreach ($haystack as $item) {
		//var_dump($item[0]);
		if ($item[0] == $needle) {
			return $item;
			break;
		}
	}
}

function leafext_elevation_settings() {
	$defaults=array();
	$params = leafext_elevation_params();
	foreach($params as $param) {
		$defaults[$param[0]] = $param[2];
	}
	$options = shortcode_atts($defaults, get_option('leafext_eleparams'));
	return $options;
}
