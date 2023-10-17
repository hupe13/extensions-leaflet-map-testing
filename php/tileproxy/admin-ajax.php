<?php
/**
* leafext-tileproxy with admin-ajax.php
* extensions-leaflet-map
*/
// Direktzugriff auf diese Datei verhindern:
defined( 'ABSPATH' ) or die();

function leafext_ajax_tileproxy() {
  // Set SHORTINIT to true
  if (!defined('SHORTINIT')) define( 'SHORTINIT', true );
  require_once WP_CONTENT_DIR . "/../wp-includes/functions.php";
  if ( isset( $_GET['tile'] )) {
    $tile = $_GET['tile'];
    //validate it using esc_url_raw($url) === $url , and if it fails validation, reject it
    if (esc_url_raw($tile) === $tile) {
      if (isset( $_GET['c'] ) && $_GET['c'] == '1') {
        $upload_dir  = wp_get_upload_dir();
        $upload_path = $upload_dir['basedir'];
        $upload_url  = $upload_dir['baseurl'];

        $tile_parse = wp_parse_url($tile);
        // array(4) {
        //   ["scheme"]=> string(5) "https"
        //   ["host"]=> string(22) "tile.openstreetmap.org"
        //   ["path"]=> string(17) "/14/8840/5490.png"
        //   ["query"]=> string(7) "key=bla"
        // }
        $host=$tile_parse['host'];
        if (isset( $_GET['s'] ) && $_GET['s'] != '') {
          $hostparts = explode('.',$host);
          unset($hostparts[0]);
          $host = implode('.',$hostparts);
        }
        $tiledir  = $upload_path.'/tiles/'.$host.dirname($tile_parse['path']);
        $tilefile = $upload_path.'/tiles/'.$host.$tile_parse['path'];
        $tileurl  =  $upload_url.'/tiles/'.$host.$tile_parse['path'];

        wp_mkdir_p($tiledir);
        if (!file_exists($tilefile)) {
          // Hole Tile und speichere in filename (stream = true).
          $args = array (
            //'user-agent'          => apply_filters( 'http_headers_useragent', 'WordPress/' . get_bloginfo( 'version' ) . '; ' . get_bloginfo( 'url' ), $url ),
            'stream'              => true,
            'filename'            => $tilefile,
          );
          $response = wp_remote_get($tile, $args);
        }
      }
      //
      $args = array (
        //'user-agent'          => apply_filters( 'http_headers_useragent', 'WordPress/' . get_bloginfo( 'version' ) . '; ' . get_bloginfo( 'url' ), $url ),
        'stream'              => false,
      );
      $response = wp_remote_get($tile,$args);
      if ( is_array( $response ) && ! is_wp_error( $response ) ) {
        ob_start();
        header('cache-control: max-age=47600, stale-while-revalidate=604800, stale-if-error=604800');
        header('Content-Type: image/png');
        echo wp_remote_retrieve_body($response);
        ob_end_flush();
      }
    }
  }
  // don't forget to end your scripts with a die() function - very important
  die();
}
add_action("wp_ajax_leafext_tileproxy", "leafext_ajax_tileproxy");
add_action("wp_ajax_nopriv_leafext_tileproxy", "leafext_ajax_tileproxy");

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
