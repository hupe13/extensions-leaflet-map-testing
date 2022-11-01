<?php
/**
* Functions for routingmachine shortcode
* extensions-leaflet-map
*/
// Direktzugriff auf diese Datei verhindern:
defined( 'ABSPATH' ) or die();

//Parameter and Values
function leafext_routingmachine_params() {
  $params = array(
    // array(
    //   'param' => 'lat',
    //   'desc' =>  __('Latitude',"extensions-leaflet-map"),
    //   'default' => '',
    // ),
  );
  return $params;
}

//Shortcode: [routingmachine]

function leafext_routingmachine_script($params,$content){
  $text = '
  <script>
  window.WPLeafletMapPlugin = window.WPLeafletMapPlugin || [];
  window.WPLeafletMapPlugin.push(function () {
    var map = window.WPLeafletMapPlugin.getCurrentMap();
    var map_id = map._leaflet_id;

    L.Routing.control({
      waypoints: [
        L.latLng(57.74, 11.94),
        L.latLng(57.6792, 11.949)
      ]
    }).addTo(map);

  });
  </script>';
  //$text = \JShrink\Minifier::minify($text);
  return "\n".$text."\n";
}

function leafext_routingmachine_settings() {
  $defaults=array();
	$params = leafext_routingmachine_params();
	foreach($params as $param) {
		$defaults[$param['param']] = $param['default'];
	}
	$options = shortcode_atts($defaults, get_option('leafext_eleparams'));
	return $options;
}

function leafext_routingmachine_function( $atts, $content="" ){
  //leafext_enqueue_markercluster ();
  leafext_enqueue_routingmachine ();
  //$options=leafext_case(array_keys(leafext_routingmachine_settings()),leafext_clear_params($atts));
  //return leafext_routingmachine_script($options,$content);
  return leafext_routingmachine_script("","");
}
add_shortcode('routingmachine', 'leafext_routingmachine_function' );

function leafext_routingmachines_params ($params) {
	///var_dump($params); wp_die();
	$text = "";
	foreach ($params as $k => $v) {
		//var_dump($v,gettype($v));
		$text = $text. "$k: ";
		switch (gettype($v)) {
			case "string":
			switch ($v) {
				// case "false":
				// case "0": $value = "false"; break;
				// case "true":
				// case "1": $value = "true"; break;
				case strpos($v,"{") !== false:
				case strpos($v,"}") !== false:
				case is_numeric($v):
				$value = $v; break;
				default:
				$value = '"'.$v.'"';
			}
			break;
			case "boolean":
			$value = $v ? "true" : "false"; break;
			case "integer":
			case "double":
			$value = $v; break;
			default: var_dump($k, $v, gettype($v)); wp_die("Type");
		}
		$text = $text.$value;
		$text = $text.",\n";
	}
	//var_dump($text); wp_die();
	return $text;
}
