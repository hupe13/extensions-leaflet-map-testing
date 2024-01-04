<?php
/**
 * Leafext-tileproxy
 *
 * @package Extensions for Leaflet Map
 */

// Direktzugriff auf diese Datei verhindern.
defined( 'ABSPATH' ) || die();

require_once TESTLEAFEXT_PLUGIN_DIR . '/php/tileproxy/restapi.php';
require_once TESTLEAFEXT_PLUGIN_DIR . '/php/tileproxy/admin-ajax.php';

// Zu konfigurieren:
// default restapi oder admin-ajax: default admin-ajax.php
// cache ja/nein, default false (nur proxy)
// cache loeschen
// Lebenszeit tiles - automatisch neu? wie lange?
// tiles directory: default wp_get_upload_dir()/tiles/
// tilelayer bounds (Speicherplatz!)?

function leafext_tileproxy_function( $atts, $content, $shortcode ) {
	$text = leafext_should_interpret_shortcode( $shortcode, $atts );
	if ( $text != '' ) {
		return $text;
	} else {
		if ( ! is_array( $atts ) ) {
			$atts = array();
		}
		$defaults = array(
			'tileurl'    => get_option( 'leaflet_map_tile_url', 'https://tile.openstreetmap.org/{z}/{x}/{y}.png' ),
			'subdomains' => get_option( 'leaflet_map_tile_url_subdomains', 'abc' ),
			'cache'      => false,
			'restapi'    => false,
		);
		$options  = shortcode_atts( $defaults, leafext_clear_params( $atts ) );
		if ( strpos( $options['tileurl'], '{s}' ) !== false ) {
			if ( $options['subdomains'] == '' ) {
				$options['subdomains'] = 'abc';
			}
			$sub = '&s=' . $options['subdomains'];
		} else {
			$sub = '';
		}

		if ( $options['restapi'] ) {
			$proxyurl = get_site_url() . '/wp-json/leafext-tileproxy/v1/tiles/?tile=' . filter_var( $options['tileurl'], FILTER_SANITIZE_URL ) . $sub;
		} else {
			$proxyurl = admin_url( 'admin-ajax.php' ) . '?action=leafext_tileproxy&tile=' . filter_var( $options['tileurl'], FILTER_SANITIZE_URL ) . $sub;
		}
		$atts['tileurl'] = $proxyurl;

		if ( $options['cache'] ) {
			$upload_dir = wp_get_upload_dir();
			$upload_url = $upload_dir['baseurl'];
			$tile_parse = wp_parse_url( $options['tileurl'] );
			// array(4) {
			// ["scheme"]=> string(5) "https"
			// ["host"]=> string(22) "tile.openstreetmap.org"
			// ["path"]=> string(17) "/14/8840/5490.png"
			// ["query"]=> string(7) "key=bla"
			// }
			$host = $tile_parse['host'];
			if ( $sub != '' ) {
				$hostparts = explode( '.', $host );
				unset( $hostparts[0] );
				$host = implode( '.', $hostparts );
			}
			$tilelocalurl    = $upload_url . '/tiles/' . $host . $tile_parse['path'];
			$atts['tileurl'] = $tilelocalurl;
			$proxyurl        = $proxyurl . '&c=1';
		}

		$text = $text . '[leaflet-map ';
		// todo: unset some atts.
		if ( is_array( $atts ) ) {
			foreach ( $atts as $key => $item ) {
				if ( is_int( $key ) ) {
					$text = $text . "$item ";
				} else {
					$text = $text . "$key=$item ";
				}
			}
		}
		$text = $text . ']';
		$map  = do_shortcode( $text );
		if ( $options['cache'] ) {
			$map = $map . leafext_tileproxy_script( $proxyurl, $options['subdomains'] );
		}
		return $map;
	}
}
add_shortcode( 'leaflet-map-tileproxy', 'leafext_tileproxy_function' );

function leafext_tileproxy_script( $tileurl, $subdomains ) {
	$text = '<script><!--';
	ob_start();
	?>/*<script>*/
	window.WPLeafletMapPlugin = window.WPLeafletMapPlugin || [];
	window.WPLeafletMapPlugin.push(function () {
	var map = window.WPLeafletMapPlugin.getCurrentMap();
	map.eachLayer(function(layer) {
		if( layer instanceof L.TileLayer ) {
		layer.on('tileerror', function(e) {
			// https://gis.stackexchange.com/questions/347646/specify-an-alternative-url-for-a-tilelayer-to-use-in-leaflet
			// Edit
			if (e.tile._hasError) return;
			var tileSrc = atob('<?php echo base64_encode( filter_var( $tileurl, FILTER_SANITIZE_URL ) ); ?>');
			var subdomains =  "<?php echo $subdomains; ?>";
			var si = Math.floor((Math.random() * 3));
			tileSrc = tileSrc.replace(/{s}/g, subdomains.substring(si, si + 1));
			tileSrc = tileSrc.replace(/{x}/g, e.coords.x);
			tileSrc = tileSrc.replace(/{y}/g, e.coords.y);
			tileSrc = tileSrc.replace(/{z}/g, e.coords.z);
			e.tile._hasError = true;
			e.tile.src = tileSrc;
		});
		}
	});
	});
	<?php
	$javascript = ob_get_clean();
	$text       = $text . $javascript . '//-->' . "\n" . '</script>';
	// $text = \JShrink\Minifier::minify($text);
	return "\n" . $text . "\n";
}
