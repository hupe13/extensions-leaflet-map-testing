<?php
/**
 * Admin for leafext-proxy
 *
 * @package Extensions for Leaflet Map
 */

// Direktzugriff auf diese Datei verhindern.
defined( 'ABSPATH' ) || die();

function leafext_proxy_init() {
	add_settings_section( 'proxy_settings', '', '', 'leafext_settings_proxy' );
	add_settings_field( 'leafext_proxy_dirs', __( 'Directories', 'extensions-leaflet-map' ), 'leafext_form_proxy', 'leafext_settings_proxy', 'proxy_settings', 'dirs' );
	register_setting( 'leafext_settings_proxy', 'leafext_proxy', 'leafext_validate_proxy' );
}
add_action( 'admin_init', 'leafext_proxy_init' );

function leafext_admin_proxy() {
	if ( current_user_can( 'manage_options' ) ) {
		echo '<form method="post" action="options.php">';
	} else {
		echo '<form>';
	}
	settings_fields( 'leafext_settings_proxy' );
	do_settings_sections( 'leafext_settings_proxy' );
	if ( current_user_can( 'manage_options' ) ) {
		wp_nonce_field( 'leafext_set_proxy', 'leafext_set_proxy' );
		submit_button();
	}
	echo '</form>';
}

function leafext_form_proxy() {
	if ( ! current_user_can( 'manage_options' ) ) {
		$disabled = ' disabled ';
	} else {
		$disabled = '';
	}

	$options = get_option( 'leafext_proxy', array() );
	$i       = 0;
	if ( ! is_array( $options ) ) {
		$params = explode( ' ', $options );
		$count  = count( $params );
		foreach ( $params as $option ) {
			if ( $option != '' ) {
				echo '<div><input ' . $disabled .
				' type="checkbox" checked name="leafext_proxy[' . $i . ']" ' .
				'value="' . esc_attr( $option ) . '" /><label>' . esc_attr( $option ) . '</label></div>';
				++$i;
			}
		}
	}
	echo '<div><input type="text" size="80" placeholder="' .
	wp_get_upload_dir()['baseurl'] . '/path/to/tracks/" name="leafext_proxy[' . $i . ']"/>
	</div>';
}

// Sanitize and validate input. Accepts an array, return a sanitized array.
function leafext_validate_proxy( $input ) {
	if ( isset( $_POST['submit'] ) ) {
		$request = map_deep( wp_unslash( $_REQUEST ), 'sanitize_text_field' );
		if ( wp_verify_nonce( $request['leafext_set_proxy'], 'leafext_set_proxy' ) ) {
			$dirs = array();
			if ( is_array( $input ) ) {
				foreach ( $input as $dir ) {
					if ( $dir != '' ) {
						$thisdir = wp_http_validate_url( $dir, array( 'https' ) );
						if ( $thisdir !== false ) {
							$dirs[] = trailingslashit( $thisdir );
						}
					}
				}
			} elseif ( $input != '' ) {
				$thisdir = wp_http_validate_url( $input, array( 'https' ) );
				if ( $thisdir !== false ) {
					$dirs[] = trailingslashit( $thisdir );
				}
			}
			if ( count( $dirs ) > 0 ) {
				return implode( ' ', $dirs );
			}
		} else {
			wp_die( 'invalid', 404 );
		}
	}
}

// Erklaerung / Hilfe
function leafext_proxy_help_text() {
	$text = '';
	if ( ! ( is_singular() || is_archive() ) ) {
		$text = $text . '<h2>' . __( 'Proxy for Track Files', 'extensions-leaflet-map' ) . '</h2>';
		$text = $text . '<p>';
		$text = $text . __( 'Here you can define your directories used in proxy.', 'extensions-leaflet-map' );
		$text = $text . '</p>';
		$text = $text . '<ul>';
		$text = $text . '<li>' . __( 'Attempt to obfuscate the URL to the gpx file and its data', 'extensions-leaflet-map' ) . '</li>';
		$text = $text . '<li>' . sprintf(
			__( 'The url to the gpx file is valid only once %s, when elevation is called.', 'extensions-leaflet-map' ),
			'(<a href="https://github.com/wahabmirjan/wp-simple-nonce">wp-simple-nonce</a>)'
		) . '</li>';
		$text = $text . '<li>' . __( 'The data are encrypted with', 'extensions-leaflet-map' ) .
		' <a href="https://github.com/brainfoolong/cryptojs-aes-php">cryptojs-aes-php</a>.</li>';
		$text = $text . '<li>' . __( 'But you can see the password to decrypt in the JavaScript code. It is impossible to protect it.', 'extensions-leaflet-map' ) . '</li>';
		// $text = $text.'<li>'.__('','extensions-leaflet-map').'</li>';
		$text = $text . '</ul>';

		$text = $text . '<h2>Shortcode</h2>
		<pre><code>[leaflet-map fitbounds]
[testelevation proxy=1 gpx="https://your-domain.tld/path/to/track.gpx" option=...]</code></pre>';
		$text = $text . __( 'You can use the same options as in <code>[elevation]</code>.', 'extensions-leaflet-map' );
	}
	if ( ! ( is_singular() || is_archive() ) ) {
		$text = $text . '<h2>' . __( 'Settings', 'extensions-leaflet-map' ) . '</h2>';
		$text = $text . '<p>' .
			__( 'Configure the directories.', 'extensions-leaflet-map' ) . ' ' .
			sprintf(
				__(
					'To add a directory enter the url in text field. It must begin with %s, but does not need to be the same host as in the example.',
					'extensions-leaflet-map'
				),
				'<i>https://</i>'
			) . ' ' .
			__( 'To remove a directory simply deselect.', 'extensions-leaflet-map' )
			. '</p>';
	}
	if ( is_singular() || is_archive() ) {
		return $text;
	} else {
		echo $text;
	}
}
