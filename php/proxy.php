<?php
/**
 * leafext-proxy
 * extensions-leaflet-map
 */
// Direktzugriff auf diese Datei verhindern:
defined( 'ABSPATH' ) or die();

use Nullix\CryptoJsAes\CryptoJsAes;
require TESTLEAFEXT_PLUGIN_DIR.'/pkg/cryptojs-aes-php/src/CryptoJsAes.php';
include_once TESTLEAFEXT_PLUGIN_DIR.'/pkg/wp-simple-nonce/wp-simple-nonce.php';

function leafext_proxy() {
  if ( isset( $_GET['name'] ) && isset( $_GET['value'] ) &&
    isset( $_GET['dir']) && isset( $_GET['gpx'])) {
    $result = WPSimpleNonce::checkNonce($_GET['name'],base64_decode($_GET['value']));
    //var_dump($result);
    if ($result) {
      $dir = $_GET['dir'];
      $dirs = get_option('leafext_proxy');
      if ($dirs != "") {
        $directories = explode(" ", $dirs);
        $url = trailingslashit($directories[$dir]).$_GET['gpx'];
        //
        $response = wp_remote_get($url);
        if ( is_array( $response ) && ! is_wp_error( $response ) ) {
          //$headers = $response['headers']; // array of http header lines
          $gpxcontent = $response['body']; // use the content
          ob_start();
          header("X-Robots-Tag: noindex, nofollow", true);
          header('Content-Type: text/html');
          // encrypt
          echo CryptoJsAes::encrypt($gpxcontent,base64_decode($_GET['value']));
          ob_end_flush();
        }
      }
    }
  }
  // don't forget to end your scripts with a die() function - very important
  die();
}
add_action("wp_ajax_leafext_proxy", "leafext_proxy");
add_action("wp_ajax_nopriv_leafext_proxy", "leafext_proxy");

function leafext_enqueue_proxy() {
  wp_enqueue_script( 'leafext_proxy',
  plugins_url('js/proxy.js',
  TESTLEAFEXT_PLUGIN_FILE),
  array('jquery'),null);

  wp_enqueue_script( 'cryptojs-aes',
  plugins_url('/pkg/cryptojs-aes-php/dist/cryptojs-aes.min.js',
  TESTLEAFEXT_PLUGIN_FILE),
  array('leafext_proxy'),null);
  wp_enqueue_script( 'cryptojs-aes-format',
  plugins_url('/pkg/cryptojs-aes-php/dist/cryptojs-aes-format.js',
  TESTLEAFEXT_PLUGIN_FILE),
  array('leafext_proxy'),null);

  wp_localize_script(
    'leafext_proxy', 'leafext_proxy_ajax',
    array (
      'ajaxurl' => admin_url('admin-ajax.php')
    )
  );
}
add_action( 'init', 'leafext_enqueue_proxy' );
