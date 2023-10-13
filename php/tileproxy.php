<?php
/**
* leafext-tileproxy
* extensions-leaflet-map
*/
// Direktzugriff auf diese Datei verhindern:
defined( 'ABSPATH' ) or die();

function leafext_tileproxy() {
  // Set SHORTINIT to true
  if (!defined('SHORTINIT')) define( 'SHORTINIT', true );
  require_once WP_CONTENT_DIR . "/../wp-includes/functions.php";
  if ( isset( $_GET['tile'] )) {
    $tile = $_GET['tile'];
    //validate it using esc_url_raw($url) === $url , and if it fails validation, reject it
    if (esc_url_raw($tile) === $tile) {
      if (isset( $_GET['cache'] ) && $_GET['cache'] == '1') {
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
        $tiledir  = $upload_path.'/tiles/'.$tile_parse['host'].dirname($tile_parse['path']);
        $tilefile = $upload_path.'/tiles/'.$tile_parse['host'].$tile_parse['path'];
        $tileurl  =  $upload_url.'/tiles/'.$tile_parse['host'].$tile_parse['path'];

        wp_mkdir_p($tiledir);
        if (!file_exists($tilefile)) {
          /**
          *     @type bool         $stream              Whether to stream to a file. If set to true and no filename was
          *                                             given, it will be droped it in the WP temp dir and its name will
          *                                             be set using the basename of the URL. Default false.
          *     @type string       $filename            Filename of the file to write to when streaming. $stream must be
          *                                             set to true. Default null.
          */
          $args = array (
            //'user-agent'          => apply_filters( 'http_headers_useragent', 'WordPress/' . get_bloginfo( 'version' ) . '; ' . get_bloginfo( 'url' ), $url ),
            'stream'              => true,
            'filename'            => $tilefile,
          );
          $response = wp_remote_get($tile, $args);
          if ( is_array( $response ) && ! is_wp_error( $response ) ) {
            $args = array (
              //'user-agent'          => apply_filters( 'http_headers_useragent', 'WordPress/' . get_bloginfo( 'version' ) . '; ' . get_bloginfo( 'url' ), $url ),
              'stream'              => false,
              //'filename'            => $tilefile,
            );
            $response2 = wp_remote_get($tile,$args);
            if ( is_array( $response2 ) && ! is_wp_error( $response2 ) ) {
              // header('Location: '.$tileurl);
              // exit;
              ob_start();
              header('cache-control: max-age=47600, stale-while-revalidate=604800, stale-if-error=604800');
              header('Content-Type: image/png');
              echo wp_remote_retrieve_body($response2);
              ob_end_flush();
            }
          }
        } else {
          // Kommt theoretisch nicht vor.
          header('Location: '.$tileurl);
          exit;
        }
      } else {
        $response = wp_remote_get($tile);
        if ( is_array( $response ) && ! is_wp_error( $response ) ) {
          ob_start();
          header('cache-control: max-age=47600, stale-while-revalidate=604800, stale-if-error=604800');
          header('Content-Type: image/png');
          echo $response['body'];
          ob_end_flush();
        }
      }
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

function leafext_tileproxy_function( $atts,$content,$shortcode) {
  $text = leafext_should_interpret_shortcode($shortcode,$atts);
  if ( $text != "" ) {
    return $text;
  } else {
    $options = leafext_clear_params($atts);
    if (!is_array($atts)) $atts = array();
    if (isset($atts["tileurl"])) {
      $tileurl = $atts["tileurl"];
    } else {
      $tileurl = get_option('leaflet_map_tile_url', 'https://tile.openstreetmap.org/{z}/{x}/{y}.png');
    }

    if (isset($options['cache']) && $options['cache'] == 1 ) {
      $upload_dir  = wp_get_upload_dir();
      //$upload_path = $upload_dir['basedir'];
      $upload_url  = $upload_dir['baseurl'];
      $tile_parse = wp_parse_url($tileurl);
      // array(4) {
      //   ["scheme"]=> string(5) "https"
      //   ["host"]=> string(22) "tile.openstreetmap.org"
      //   ["path"]=> string(17) "/14/8840/5490.png"
      //   ["query"]=> string(7) "key=bla"
      // }
      $tilelocalurl  =  $upload_url.'/tiles/'.$tile_parse['host'].$tile_parse['path'];
      $atts["tileurl"] = $tilelocalurl;

    } else {
      $ajaxtileurl = admin_url('admin-ajax.php').'?action=leafext_tileproxy&tile='.$tileurl.'&cache=0';
      $atts["tileurl"] = $ajaxtileurl;
    }

    $text = '[leaflet-map ';
    if (is_array($atts)){
      foreach ($atts as $key=>$item){
        if (is_int($key)) {
          $text = $text. "$item ";
        } else {
          $text = $text. "$key=$item ";
        }
      }
    }
    $text = $text. ']';
    $map = do_shortcode($text);
    if (isset($options['cache']) && $options['cache'] == 1 ) {
      $ajaxtileurl = admin_url('admin-ajax.php').'?action=leafext_tileproxy&tile='.$tileurl.'&cache=1';
      $map = $map.leafext_tileproxy_script($ajaxtileurl);
    }
    return $map;
  }
}
add_shortcode('leaflet-map-tileproxy', 'leafext_tileproxy_function');

function leafext_tileproxy_script($tileurl) {
  $text = '<script><!--';
  ob_start();
  ?>/*<script>*/
  window.WPLeafletMapPlugin = window.WPLeafletMapPlugin || [];
  window.WPLeafletMapPlugin.push(function () {
    var map = window.WPLeafletMapPlugin.getCurrentMap();
    map.eachLayer(function(layer) {
      if( layer instanceof L.TileLayer ) {
        layer.on('tileerror', function(e) {
          //console.log(e);
          if (e.tile._hasError) return;
          var tileSrc = "<?php echo $tileurl;?>";
          tileSrc = tileSrc.replace(/{x}/g, e.coords.x);
          tileSrc = tileSrc.replace(/{y}/g, e.coords.y);
          tileSrc = tileSrc.replace(/{z}/g, e.coords.z);
          e.tile._hasError = true;
          e.tile.src = tileSrc;
          //console.log(tileSrc);
        });
      }
    });
  });
  <?php
  $javascript = ob_get_clean();
  $text = $text . $javascript . '//-->'."\n".'</script>';
//  $text = \JShrink\Minifier::minify($text);
  return "\n".$text."\n";
}
