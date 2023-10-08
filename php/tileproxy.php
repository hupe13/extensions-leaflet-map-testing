<?php
/**
 * leafext-tileproxy
 * extensions-leaflet-map
 */
// Direktzugriff auf diese Datei verhindern:
defined( 'ABSPATH' ) or die();

function leafext_tileproxy() {
  if ( isset( $_GET['tile'] )) {
    $tile = $_GET['tile'];

    $upload_dir = wp_get_upload_dir();
    $upload_path = $upload_dir['basedir'];
    $upload_url = $upload_dir['baseurl'];

    $tileurl=$upload_url.'/tiles/';

    $tile_parse = wp_parse_url($tile);
    $tiledir=$upload_path.'/tiles/'.dirname($tile_parse['path']);
    $kachel=basename($tile_parse['path']);
    wp_mkdir_p($tiledir);
    if (!file_exists($tiledir.'/'.$kachel)) {
      $response = wp_remote_get($tile);
      if ( is_array( $response ) && ! is_wp_error( $response ) ) {
        //$headers = $response['headers']; // array of http header lines
        $origtile = $response['body']; // use the content
        ob_start();
        header("X-Robots-Tag: noindex, nofollow", true);
        header('Content-Type: image/png');
        echo $origtile;
        ob_end_flush();
        file_put_contents($tiledir.'/'.$kachel, $origtile);
      }
    } else {
      $tile=file_get_contents($tiledir.'/'.$kachel);
      ob_start();
      header("X-Robots-Tag: noindex, nofollow", true);
      header('Content-Type: image/png');
      echo $tile;
      ob_end_flush();
    }
  }
// don't forget to end your scripts with a die() function - very important
die();
}
add_action("wp_ajax_leafext_tileproxy", "leafext_tileproxy");
add_action("wp_ajax_nopriv_leafext_tileproxy", "leafext_tileproxy");

function leafext_enqueue_tileproxy() {
  wp_enqueue_script( 'leafext_tileproxy',
  plugins_url('js/tileproxy.js',
  TESTLEAFEXT_PLUGIN_FILE),
  array('jquery'),null);

  wp_localize_script(
    'leafext_tileproxy', 'leafext_tileproxy_ajax',
    array (
      'ajaxurl' => admin_url('admin-ajax.php')
    )
  );
}
add_action( 'init', 'leafext_enqueue_tileproxy' );
