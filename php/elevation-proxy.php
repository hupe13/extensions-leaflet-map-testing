<?php
use Nullix\CryptoJsAes\CryptoJsAes;
require TESTLEAFEXT_PLUGIN_DIR.'/pkg/cryptojs-aes-php/src/CryptoJsAes.php';

function leafext_elevation_proxy() {

  include_once TESTLEAFEXT_PLUGIN_DIR.'/pkg/wp-simple-nonce/wp-simple-nonce.php';

  if ( isset( $_GET['nonce_name'] ) && isset( $_GET['nonce_value'] ) ) {
    $result = WPSimpleNonce::checkNonce($_GET['nonce_name'],$_GET['nonce_value']);
    //var_dump($result);
    if ($result) {
      // CHANGE THIS!
      $url='https://your very secret directory/'.$_GET['gpx'];
      $gpxcontent = file_get_contents($url);
      ob_start();
      header("X-Robots-Tag: noindex, nofollow", true);
      // header('Content-Type: application/gpx+xml');
      // header('Content-Disposition: attachment; filename="' . $_GET['gpx'] );
      header('Content-Type: text/html');
      // echo $gpxcontent;
      // echo mb_detect_encoding($gpxcontent);
      // echo base64_encode($gpxcontent);
      //
      // encrypt
      $password = "123456";
      echo CryptoJsAes::encrypt($gpxcontent, $password);
      ob_end_flush();
    }
  }
  // don't forget to end your scripts with a die() function - very important
  die();
}
add_action("wp_ajax_leafext_elevation_proxy", "leafext_elevation_proxy");
add_action("wp_ajax_nopriv_leafext_elevation_proxy", "leafext_elevation_proxy");

function leafext_enqueue_elevation_proxy() {
  wp_enqueue_script( 'elevation_proxy',
  plugins_url('js/elevation_proxy.js',
  TESTLEAFEXT_PLUGIN_FILE),
  array('jquery'),null);

  wp_enqueue_script( 'cryptojs-aes',
  plugins_url('/pkg/cryptojs-aes-php/dist/cryptojs-aes.min.js',
  TESTLEAFEXT_PLUGIN_FILE),
  array('elevation_proxy'),null);
  wp_enqueue_script( 'cryptojs-aes-format',
  plugins_url('/pkg/cryptojs-aes-php/dist/cryptojs-aes-format.js',
  TESTLEAFEXT_PLUGIN_FILE),
  array('elevation_proxy'),null);

  wp_localize_script(
    'elevation_proxy', 'elevation_proxy_ajax',
    array (
      'ajaxurl' => admin_url('admin-ajax.php'),
      //'atts'=> atts,
    )
  );
}
add_action( 'init', 'leafext_enqueue_elevation_proxy' );

function leafext_elevation_getgpx_function($atts) {
  //add_action("wp_ajax_leafext_elevation_proxy", "leafext_elevation_proxy");
  //add_action("wp_ajax_nopriv_leafext_elevation_proxy", "leafext_elevation_proxy");
  //add_action('init', 'leafext_enqueue_elevation_proxy');
  //

  include_once TESTLEAFEXT_PLUGIN_DIR.'/pkg/wp-simple-nonce/wp-simple-nonce.php';
  $nonce = WPSimpleNonce::createNonce('leafext_getgpx');
  $url = admin_url( 'admin-ajax.php' ).'?action=leafext_elevation_proxy&nonce_name='.$nonce['name'].'&nonce_value='.$nonce['value'].'&gpx='.$atts['gpx'];
  return do_shortcode('[testelevation gpx="'.$url.'"]');
  //var_dump($nonce);
  //return '[elevation gpx="'.$url.'"]';
}
add_shortcode('leafext-elevation-getgpx','leafext_elevation_getgpx_function');

define('TESTLEAFEXT_ELEVATION_VERSION',"2.5.0");
define('TESTLEAFEXT_ELEVATION_URL', LEAFEXT_PLUGIN_URL . '/leaflet-plugins/leaflet-elevation-'.TESTLEAFEXT_ELEVATION_VERSION.'/');
define('TESTLEAFEXT_ELEVATION_DIR', LEAFEXT_PLUGIN_DIR . '/leaflet-plugins/leaflet-elevation-'.TESTLEAFEXT_ELEVATION_VERSION.'/');

function testleafext_elevation_function($atts,$content,$shortcode) {
	$text = leafext_should_interpret_shortcode($shortcode,$atts);
	if ( $text != "" ) {
		return $text;
	} else {
		if ( ! $atts['gpx'] ) {
			$text = "[elevation ";
			foreach ($atts as $key=>$item){
				$text = $text. "$key=$item ";
			}
			$text = $text. "]";
			return $text;
		}

		leafext_enqueue_elevation();

		//leafext_enqueue_leafext("elevation");
		leafext_enqueue_leafext_elevation();

		$atts1=leafext_case(array_keys(leafext_elevation_settings(array("changeable","fixed"))),leafext_clear_params($atts));
		$options = shortcode_atts(leafext_elevation_settings(array("changeable","fixed")), $atts1);

		$track = $atts['gpx'];

    //if (isset($atts['proxy']) && $atts['proxy']) {
      wp_dequeue_script( 'elevation_js');
      wp_deregister_script( 'elevation_js');
      wp_enqueue_script( 'elevation_js',
      plugins_url('leaflet-plugins/leaflet-elevation-'.TESTLEAFEXT_ELEVATION_VERSION.'/dist/leaflet-elevation.js',
      TESTLEAFEXT_PLUGIN_FILE),
      array('wp_leaflet_map'),null);
    //}

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
						$wptvalue = $wptvalue.'"'.$wpt['css'].'":  L.divIcon(
							{
								className: "elevation-waypoint-marker",
								html: '."'".'<i class="elevation-waypoint-icon '.$wpt['css'].'"></i>'."'".','.
								html_entity_decode($wpt['js']).'
							}
						),';
					}
					$wptvalue = $wptvalue.'}';
					$options['wptIcons'] =  $wptvalue;
				}
			}
		}

		// acceleration.js
		// altitude.js
		// // cadence.js
		// distance.js
		// // heart.js
		// labels.js
		// lineargradient.js
		// pace.js
		// // runner.js
		// slope.js
		// speed.js
		// time.js

		//var_dump($options);

		$handlers = array();

		if ( (bool)$options['pace'] ) {
			$handlers[] = '"Pace"';
			if ( !(bool)$options['time'] ) $options['time'] = "summary";
			if ( (bool)$options['speed'] ) $handlers[] = '"Speed"';
			if ( (bool)$options['acceleration'] ) $handlers[] = '"Acceleration"';
			if ( (bool)$options['slope'] ) $handlers[] = '"Slope"';
		}
		if ((bool)$options['labelsRotation'] || $options['labelsAlign'] != 'start')
		$handlers[] = '"Labels"';
		if ( (bool)$options['linearGradient'] ) {
			$handlers[] = '"Slope"';
			$handlers[] = '"LinearGradient"';
		}

		$handlers = array_unique($handlers);
		//var_dump($handlers);

		if (count($handlers) > 0) $options['handlers'] = '[...L.Control.Elevation.prototype.options.handlers,'.implode(',',$handlers).']';
		//if (count($handlers) > 0) $options['handlers'] = '["Distance","Time","Altitude",'.implode(',',$handlers).']';
		//if (count($handlers) > 0) $options['handlers'] = '['.implode(',',$handlers).',...L.Control.Elevation.prototype.options.handlers]';
		//if (count($handlers) > 0) $options['handlers'] = '[ "Distance", "Time", "Altitude", "Slope", "Speed", "Acceleration", "Labels"]';

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
		if ( $options['direction'] == true) leafext_enqueue_rotate();
		if ( $options['distanceMarkers'] == true) leafext_enqueue_rotate();

		list($options,$style) = leafext_elevation_color($options);
		ksort($options);

		$text=$style.leafext_elevation_script($track,$options);
		//
		return $text;
	}
}
add_shortcode('testelevation', 'testleafext_elevation_function');
