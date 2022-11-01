<?php
/**
 * Functions for elevation shortcode
 * extensions-leaflet-map
 */
// Direktzugriff auf diese Datei verhindern:
defined( 'ABSPATH' ) or die();

function testleafext_elevation_pace($options) {
	if ( (bool)$options['pace'] ) {
		if ( !(bool) $options['time'] ) $options['time'] = "summary";
		$text = '[';
		$handlers = glob(LEAFEXT_ELEVATION_DIR.'/src/handlers/*');
		foreach ($handlers as $handler) {
			$handle = basename ($handler,'.js');
			if (isset($options[$handle]) && $handle != 'pace') {
				if ((bool)$options[$handle]) {
					$text = $text.'"'.ucfirst($handle).'",';
				}
			}
		}
		$text = $text.'import("'.TESTLEAFEXT_ELEVATION_URL.'src/handlers/pace.js"),';
		$text = $text.'import("'.TESTLEAFEXT_ELEVATION_URL.'src/handlers/speed.js"),';
		if ( (bool)$options['acceleration'] ) {
			$text = $text.'import("'.TESTLEAFEXT_ELEVATION_URL.'src/handlers/acceleration.js"),';
		}
		$text = $text.']';
		$options['handlers'] = $text;
		//pace.label      = opts.paceLabel  || L._(opts.imperial ? 'min/mi' : 'min/km');
		//opts.paceFactor = opts.paceFactor || 60; // 1 min = 60 sec
		//$options['paceFactor'] = 3600;
		//deltaMax: this.options.paceDeltaMax,
		// Mein Standard: 1 (?)
		$options['paceDeltaMax'] = 1;
		//clampRange: this.options.paceRange,
		//$options['paceRange'] = 0.6;
	}
	return $options;
}

//Shortcode: [elevation gpx="...url..."]
function testleafext_elevation_script($gpx,$settings){
	$text = '
	<script>
	window.WPLeafletMapPlugin = window.WPLeafletMapPlugin || [];
	window.WPLeafletMapPlugin.push(function () {
		var map = window.WPLeafletMapPlugin.getCurrentMap();';

	$text = $text.'
	var elevation_options = {';

	list($text1, $settings) = leafext_ele_java_params($settings);
	$text = $text.$text1;
	$text = $text.leafext_java_params ($settings);

	$text = $text.'};
	';

	$text = $text.leafext_elevation_locale();

	$text = $text.'

  //BEGIN
  const toPrecision = (x, n) => Number(parseFloat(x.toPrecision(n)).toFixed(n));

  function formatTime(t) {
		//console.log(t);
		var date = new Date(t);
		//console.log("fkt "+date);
		var days = Math.floor(t/(1000 * 60 * 60 * 24));
    var hours = date.getUTCHours();
		if (days == 0 && hours == 0) { hours = ""; } else { hours = hours + ":";}
    var minutes = "0" + date.getUTCMinutes();
		minutes = minutes.substr(-2) + "\'";
    var seconds = "0" + date.getUTCSeconds();
		if (days > 0) { seconds = ""; } else { seconds = seconds.substr(-2) + "\'\'";}
    if (days == 0) { days = ""; } else { days = days + "d ";}
    return (days + hours + minutes + seconds);
  }

  // Save a reference of default "L.Control.Elevation" (for later use)
  const elevationProto = L.extend({}, L.Control.Elevation.prototype);
  // Override default "_registerHandler" behaviour.
  L.Control.Elevation.include({
     // ref: https://github.com/Raruto/leaflet-elevation/blob/c58250e7c20d52490aa3a50b611dbb282ff00a57/src/control.js#L1063-L1128
    _registerHandler: function(props) {
      if (typeof props === "object") {
        switch(props.name) {
          // ref: https://github.com/Raruto/leaflet-elevation/blob/c58250e7c20d52490aa3a50b611dbb282ff00a57/src/handlers/acceleration.js#L41-L61
          case "acceleration":
						let accelerationLabel = this.options.accelerationLabel || L._(this.options.imperial ? "ft/s²" : "m/s²");
            props.tooltip.chart                 = (item)        => L._("a: ") + toPrecision(item.acceleration || 0, 2) + " " + accelerationLabel;
            props.tooltip.marker                = (item)        => toPrecision(item.acceleration, 2) + " " + accelerationLabel;
            props.summary.minacceleration.value = (track, unit) => toPrecision(track.acceleration_min || 0, 2) + "&nbsp;" + unit;
            props.summary.maxacceleration.value = (track, unit) => toPrecision(track.acceleration_max || 0, 2) + "&nbsp;" + unit;
            props.summary.avgacceleration.value = (track, unit) => toPrecision(track.acceleration_avg || 0, 2) + "&nbsp;" + unit;
          break;
          case "altitude":
            props.summary.minele.value = (track, unit) => (track.elevation_min || 0).toFixed(0) + "&nbsp;" + unit;
            props.summary.maxele.value = (track, unit) => (track.elevation_max || 0).toFixed(0) + "&nbsp;" + unit;
            props.summary.avgele.value = (track, unit) => (track.elevation_avg || 0).toFixed(0) + "&nbsp;" + unit;
          break;
					//cadence
					case "distance":
					if (this.options.distance) {
            let distlabel = this.options.distance.label || L._(this.options.imperial ? "mi" : this.options.xLabel);
            props.tooltip.chart = (item) => L._("x: ") + toPrecision(item.dist, (item.dist > 10) ? 3 : 2 ) + " " + distlabel;
            props.summary.totlen.value = (track) => toPrecision(track.distance || 0, 3 ) + "&nbsp;" + distlabel;
					}
          break;
					//heart
					case "pace":
						if (this.options.pace) {
						//let paceLabel = this.options.paceLabel || L._(opts.imperial ? "min/mi" : "min/km");
            let paceLabel = this.options.imperial ? "/mi" : "/km";
            props.tooltip.chart         = (item)        => L._("pace: ") +  (formatTime(item.pace * 1000 * 60) || 0) + " " + paceLabel;
            props.tooltip.marker        = (item)        =>                  (formatTime(item.pace * 1000 * 60) || 0) + " " + paceLabel;
            props.summary.minpace.value = (track, unit) =>                  (formatTime(track.pace_max * 1000 * 60) || 0) + "&nbsp;" + paceLabel;
            props.summary.maxpace.value = (track, unit) =>                  (formatTime(track.pace_min * 1000 * 60) || 0) + "&nbsp;" + paceLabel;
            props.summary.avgpace.value = (track, unit) => formatTime( Math.abs((track.time / track.distance) / this.options.paceFactor) *60) + "&nbsp;" + paceLabel;
					}
          break;
					case "slope":
						let slopeLabel = this.options.slopeLabel || "%";
						props.tooltip.chart         = (item) => L._("m: ") + Math.round(item.slope) + slopeLabel;
					break;
					case "speed":
					//console.log(this.options.speed);
					if (this.options.speed) {
						let speedLabel = this.options.speedLabel || L._(this.options.imperial ? "mph" : "km/h");
						props.tooltip.chart                 = (item) => L._("v: ") + toPrecision(item.speed,2) + " " + speedLabel;
						props.tooltip.marker                = (item) => toPrecision(item.speed,3) + " " + speedLabel;
						props.summary.minspeed.value = (track, unit) => toPrecision(track.speed_min || 0, 2) + "&nbsp;" + unit;
						props.summary.maxspeed.value = (track, unit) => toPrecision(track.speed_max || 0, 2) + "&nbsp;" + unit;
						props.summary.avgspeed.value = (track, unit) => toPrecision(track.speed_avg || 0, 2) + "&nbsp;" + unit;
					}
					break;
          case "time":
					if (this.options.time) {
						props.tooltips.find(({ name }) => name === "time").chart = (item) => L._("T: ") + formatTime(item.duration || 0);
						props.summary.tottime.value = (track) => formatTime(track.time || 0);
					}
					break;
        }
      }
      elevationProto._registerHandler.apply(this, [props]);
    }
  });

  // Proceed as usual
  //var controlElevation = L.control.elevation(opts.elevationControl.options);
  //controlElevation.load(opts.elevationControl.url);
  //END

	// Instantiate elevation control.
	L.Control.Elevation.prototype.__btnIcon = "'.LEAFEXT_ELEVATION_URL.'/images/elevation.svg";
	var controlElevation = L.control.elevation(elevation_options);
	var track_options= { url: "'.$gpx.'" };
	controlElevation.addTo(map);';

	// not solved with leaflet 1.8 (220503)
	$text = $text.'
	var is_chrome = navigator.userAgent.indexOf("Chrome") > -1;
	var is_safari = navigator.userAgent.indexOf("Safari") > -1;
	if ( !is_chrome && is_safari && controlElevation.options.preferCanvas != false ) {
		console.log("is_safari - setting preferCanvas to false");
		controlElevation.options.preferCanvas = false;
	}';

	$text=$text.'
	// Load track from url (allowed data types: "*.geojson", "*.gpx")
	controlElevation.load(track_options.url);';

	if ( $settings['chart'] === "off") {
		$text=$text.'map.on("eledata_added", function(e) {
			//console.log(controlElevation);
			controlElevation._toggle();
		});';
	}

	$text=$text.'
	});
	</script>';
	//$text = \JShrink\Minifier::minify($text);
	return "\n".$text."\n";
}

function testleafext_elevation_function( $atts ) {
	if ( ! $atts['gpx'] ) {
		$text = "[elevation ";
		foreach ($atts as $key=>$item){
			$text = $text. "$key=$item ";
		}
		$text = $text. "]";
		return $text;
	}

	testleafext_enqueue_elevation ();

	$atts1=leafext_case(array_keys(leafext_elevation_settings(array("changeable","fixed"))),leafext_clear_params($atts));
	$options = shortcode_atts(leafext_elevation_settings(array("changeable","fixed")), $atts1);

	$track = $atts['gpx'];

	if ( $options['chart'] === "on" || $options['chart'] === "off")  {
		$options['closeBtn'] = true;
	} else {
		$options['closeBtn'] = false;
	}

	if (isset($options['wptIcons']) ) {
		$wptIcons = $options['wptIcons'];
		if ( !is_bool($wptIcons) && $wptIcons == "defined" ) {
			unset($options['wptIcons']);
			$waypoints = get_option('leafext_waypoints', "");
			if ( $waypoints != "" && ( $options['waypoints'] == "markers" || $options['waypoints'] == "1" )) {
				$wptvalue="{'': L.divIcon({
					className: 'elevation-waypoint-marker',
					html: '<i class=\"elevation-waypoint-icon default\"></i>',
					iconSize: [30, 30],
					iconAnchor: [8, 30],
				}),
					";
				foreach ( $waypoints as $wpt ) {
					$wptvalue = $wptvalue.'"'.$wpt['css'].'":  L.divIcon({
						className: "elevation-waypoint-marker",
						html: '."'".'<i class="elevation-waypoint-icon '.$wpt['css'].'"></i>'."'".','.
						html_entity_decode($wpt['js']).'}),';
				}
				$wptvalue = $wptvalue.'}';
				$options['wptIcons'] =  $wptvalue;
			}
		}
	}

	if (isset($options['pace']) ) {
		$options = testleafext_elevation_pace($options);
	}

	if ( isset($options['summary']) && $options['summary'] == "1" ) {
		$params = leafext_elevation_params();
		foreach($params as $param) {
			$options['param'] = $param['default'];
		}
		$options['summary'] = "inline";
		$options['preferCanvas'] = false;
		$options['legend'] = false;
	}
	//
	if ( ! array_key_exists('theme', $atts) ) {
		$options['theme'] = leafext_elevation_theme();
	}

	if ( $options['hotline'] == "elevation") unset ($options['polyline'] );
	list($options,$style) = leafext_elevation_color($options);
	ksort($options);

	$text=$style.testleafext_elevation_script($track,$options);
	//
	return $text;
}
add_shortcode('testelevation', 'testleafext_elevation_function' );
