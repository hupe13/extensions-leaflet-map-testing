<?php
/**
 * Leafext-tileproxy with REST API
 *
 * @package Extensions for Leaflet Map
 */

// Direktzugriff auf diese Datei verhindern.
defined( 'ABSPATH' ) || die();

function leafext_register_tileproxy_endpoint() {
	$namespace = 'leafext-tileproxy/v1';
	$route     = '/tiles/';
	$args      = array(
		'methods'             => 'GET',
		'callback'            => 'leafext_tileproxy_endpoint',
		'permission_callback' => '__return_true',
		'args'                => leafext_restapi_arguments(),
	);
	register_rest_route( $namespace, $route, $args );
}
add_action( 'rest_api_init', 'leafext_register_tileproxy_endpoint' );

function leafext_restapi_arguments() {
	$args         = array();
	$args['tile'] = array(
		'type'              => 'string',
		'required'          => true,
		'sanitize_callback' => 'sanitize_url',
	);
	$args['c']    = array(
		'type'              => 'string',
		'required'          => false,
		'sanitize_callback' => 'rest_sanitize_boolean',
	);
	$args['s']    = array(
		'type'              => 'string',
		'required'          => false,
		'sanitize_callback' => 'rest_sanitize_boolean',
	);
	return $args;
}

function leafext_tileproxy_endpoint( $request ) {
	// Set SHORTINIT to true
	if ( ! defined( 'SHORTINIT' ) ) {
		define( 'SHORTINIT', true );
	}
	require_once WP_CONTENT_DIR . '/../wp-includes/functions.php';
	$tile = $request['tile'];
	$c    = $request['c'];
	$s    = $request['s'];

	// validate it using esc_url_raw($url) === $url , and if it fails validation, reject it
	if ( esc_url_raw( $tile ) === $tile ) {
		if ( (bool) $c ) {
			$upload_dir  = wp_get_upload_dir();
			$upload_path = $upload_dir['basedir'];
			$upload_url  = $upload_dir['baseurl'];

			$tile_parse = wp_parse_url( $tile );
			// array(4) {
			// ["scheme"]=> string(5) "https"
			// ["host"]=> string(22) "tile.openstreetmap.org"
			// ["path"]=> string(17) "/14/8840/5490.png"
			// ["query"]=> string(7) "key=bla"
			// }
			$host = $tile_parse['host'];
			if ( (bool) $s ) {
				$hostparts = explode( '.', $host );
				unset( $hostparts[0] );
				$host = implode( '.', $hostparts );
			}
			// echo $host; die();
			$tiledir  = $upload_path . '/tiles/' . $host . dirname( $tile_parse['path'] );
			$tilefile = $upload_path . '/tiles/' . $host . $tile_parse['path'];
			$tileurl  = $upload_url . '/tiles/' . $host . $tile_parse['path'];

			wp_mkdir_p( $tiledir );
			if ( ! file_exists( $tilefile ) ) {
				// Hole Tile und speichere in filename (stream = true).
				$args     = array(
					// 'user-agent'          => apply_filters( 'http_headers_useragent', 'WordPress/' . get_bloginfo( 'version' ) . '; ' . get_bloginfo( 'url' ), $url ),
					'stream'   => true,
					'filename' => $tilefile,
				);
				$response = wp_remote_get( $tile, $args );
			}
		}

		$args     = array(
			// 'user-agent'          => apply_filters( 'http_headers_useragent', 'WordPress/' . get_bloginfo( 'version' ) . '; ' . get_bloginfo( 'url' ), $url ),
			'stream' => false,
		);
		$response = wp_remote_get( $tile, $args );
		if ( is_array( $response ) && ! is_wp_error( $response ) ) {
			$restresponse = new WP_REST_Response();
			$restresponse->set_data( wp_remote_retrieve_body( $response ) );
			$restresponse->set_headers(
				array(
					'Content-Type'  => 'image/png',
					// 'Content-Length' => filesize( $path ),
					'Cache-Control' => 'max-age=47600, stale-while-revalidate=604800, stale-if-error=604800',
				)
			);
			// HERE → This filter will return our binary image!
			add_filter( 'rest_pre_serve_request', 'leafext_restapi_image', 0, 2 );
			return $restresponse;
		}
	}
}

// https://stackoverflow.com/questions/44524071/wp-rest-response-to-download-a-file/70008574#70008574
/**
 * Action handler to serve a binary image
 * instead of a JSON string.
 *
 * @return bool Returns true, if the image was served; this will skip the
 *              default REST response logic.
 */
function leafext_restapi_image( $served, $result ) {
	$is_image   = false;
	$image_data = null;

	// Check the "Content-Type" header to confirm that we really want to return
	// binary image data.
	foreach ( $result->get_headers() as $header => $value ) {
		if ( 'content-type' === strtolower( $header ) ) {
			$is_image   = 0 === strpos( $value, 'image/' );
			$image_data = $result->get_data();
			break;
		}
	}
	// Output the binary data and tell the REST server to not send any other
	// details (via "return true").
	if ( $is_image && is_string( $image_data ) ) {
		echo $image_data;
		return true;
	}
	return $served;
}

// https://perishablepress.com/contact-form-7-disable-wp-rest-api/
if ( count( preg_grep( '/disable-wp-rest-api.php/', get_option( 'active_plugins' ) ) ) > 0 ) {
	function leafext_enable_restapi_tileproxy() {
		$api_path = wp_parse_url( get_rest_url() )['path'] . 'leafext-tileproxy/v1/tiles/';
		return array(
			$api_path,
		);
	}
	add_filter( 'disable_wp_rest_api_server_var', 'leafext_enable_restapi_tileproxy' );
}
