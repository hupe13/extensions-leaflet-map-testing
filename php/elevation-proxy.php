<?php
function leafext_elevation_proxy() {
  include_once WP_CONTENT_DIR.'/uploads/leaflet-plugins/wp-simple-nonce/wp-simple-nonce.php';
  if ( isset( $_GET['nonce_name'] ) && isset( $_GET['nonce_value'] ) ) {
    $result = WPSimpleNonce::checkNonce($_GET['nonce_name'],$_GET['nonce_value']);
    if ($result) {
      $url='https://leafext.de/wp-content/uploads/sites/17/tracks/'.$_GET['gpx'];
      $gpxcontent = file_get_contents($url);
      ob_start();
      header("X-Robots-Tag: noindex, nofollow", true);
      header('Content-Type: application/gpx+xml');
      header('Content-Disposition: attachment; filename="' . $_GET['gpx'] );
      echo $gpxcontent;
      ob_end_flush();
    }
  }
  // don't forget to end your scripts with a die() function - very important
  die();
}
// add_action("wp_ajax_leafext_elevation_proxy", "leafext_elevation_proxy");
// add_action("wp_ajax_nopriv_leafext_elevation_proxy", "leafext_elevation_proxy");

function leafext_enqueue_elevation_proxy() {
  wp_enqueue_script( 'elevation_proxy',
  plugins_url('js/elevation_proxy.js',
  LEAFEXT_PLUGIN_FILE),
  array('jquery'),null);
  wp_localize_script( 'elevation_proxy', 'elevation_proxy_ajax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));
}
//add_action( 'init', 'leafext_enqueue_elevation_proxy' );

function leafext_elevation_getgpx_function($atts) {
  add_action("wp_ajax_leafext_elevation_proxy", "leafext_elevation_proxy");
  add_action("wp_ajax_nopriv_leafext_elevation_proxy", "leafext_elevation_proxy");
  add_action('init', 'leafext_enqueue_elevation_proxy');
  //
  include_once WP_CONTENT_DIR.'/uploads/leaflet-plugins/wp-simple-nonce/wp-simple-nonce.php';
  $nonce = WPSimpleNonce::createNonce('leafext_getgpx');
  $url = admin_url( 'admin-ajax.php' ).'?action=leafext_elevation_proxy&nonce_name='.$nonce['name'].'&nonce_value='.$nonce['value'].'&gpx='.$atts['gpx'];
  return do_shortcode('[elevation gpx="'.$url.'"]');
  //var_dump($nonce);
  //return '[elevation gpx="'.$url.'"]';
}
add_shortcode('leafext-elevation-getgpx','leafext_elevation_getgpx_function');
