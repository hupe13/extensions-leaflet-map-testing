<?php
/**
 * Functions for elevation shortcode with proxy
 *
 * @package Extensions for Leaflet Map
 */

// Direktzugriff auf diese Datei verhindern.
defined( 'ABSPATH' ) || die();

// Shortcode: [elevation proxy=1 gpx="...url..."]
function testleafext_elevation_script( $gpx, $settings ) {
	global $once;
	list($elevation_settings, $settings) = leafext_ele_java_params( $settings );
	$text                                = '<script><!--';
	ob_start();
	?>/*<script>*/
	window.WPLeafletMapPlugin = window.WPLeafletMapPlugin || [];
	window.WPLeafletMapPlugin.push(function () {
		var map = window.WPLeafletMapPlugin.getCurrentMap();
		var elevation_options = {
			<?php echo $elevation_settings; ?>
			<?php echo leafext_java_params( $settings ); ?>
		};

		leafext_elevation_locale_js();
		leafext_elevation_prep_js ();

		if ( typeof map.rotateControl !== "undefined" ) {
			map.rotateControl.remove();
			map.options.rotate = true;
		}

		<?php
		if ( $settings['track'] ) {
			echo '
			var layersControl_options = {
				collapsed: true,
			};
			var switchtrack = L.control.layers(null, null, layersControl_options);';
		}
		?>

		// Instantiate elevation control.
		L.Control.Elevation.prototype.__btnIcon = "<?php echo LEAFEXT_ELEVATION_URL; ?>/images/elevation.svg";

		<?php if ( isset( $once ) ) { ?>
		L.Control.Elevation.prototype._loadFile = function(url) {
			fetch(url)
			.then((response) => response.text())
			.then((data)     => {
				this._downloadURL = url; // TODO: handle multiple urls?
				this._parseFromString(CryptoJSAesJson.decrypt(data, "<?php echo $once; ?>"))
				.then( geojson => geojson && this._loadLayer(geojson));
			}).catch((err) => console.warn(err));
		}
		<?php } ?>

		var controlElevation = L.control.elevation(elevation_options);
		var track_options= { url: "<?php echo $gpx; ?>" };
		controlElevation.addTo(map);
		<?php
		if ( $settings['track'] ) {
			echo 'switchtrack.addTo(map);';
		}
		?>

		// https://github.com/Raruto/leaflet-elevation/issues/232#issuecomment-1443554554
		var is_chrome = navigator.userAgent.indexOf("Chrome") > -1;
		var is_safari = navigator.userAgent.indexOf("Safari") > -1;
		if ( !is_chrome && is_safari && controlElevation.options.preferCanvas != false ) {
			console.log("is_safari - not setting preferCanvas to false");
			//controlElevation.options.preferCanvas = false;
		}

		// Load track from url (allowed data types: "*.geojson", "*.gpx")
		controlElevation.load(track_options.url);

		<?php
		if ( $settings['chart'] === 'off' ) {
			echo 'map.on("eledata_added", function(e) {
				//console.log(controlElevation);
				controlElevation._toggle();
			});';
		}

		if ( $settings['track'] ) {
			if ( $settings['track'] == 'filename' ) {
				$path_parts = pathinfo( $gpx );
				$switchname = '"' . $path_parts['filename'] . '"';
			} else {
				$switchname = 'e.name';
			}
			echo '
			controlElevation.on("eledata_loaded", function(e) {
				switchtrack.addOverlay(e.layer, ' . $switchname . ');
			});
			';
		}
		?>
	});
	<?php
	$javascript = ob_get_clean();
	$text       = $text . $javascript . '//-->' . "\n" . '</script>';
	$text       = \JShrink\Minifier::minify( $text );
	return "\n" . $text . "\n";
}

function testleafext_elevation_function( $atts, $content, $shortcode ) {
	global $once;
	$text = leafext_should_interpret_shortcode( $shortcode, $atts );
	if ( $text != '' ) {
		return $text;
	} else {
		if ( ! $atts['gpx'] ) {
			$text = '[ERROR elevation ';
			foreach ( $atts as $key => $item ) {
				$text = $text . "$key=$item ";
			}
			$text = $text . ']';
			return $text;
		}

		leafext_enqueue_elevation();
		// leafext_enqueue_leafext("elevation");
		leafext_enqueue_leafext_elevation();

		$atts1   = leafext_case( array_keys( leafext_elevation_settings( array( 'changeable', 'fixed' ) ) ), leafext_clear_params( $atts ) );
		$options = shortcode_atts( leafext_elevation_settings( array( 'changeable', 'fixed' ) ), $atts1 );

		$track = $atts['gpx'];
		// var_dump($atts);
		if ( isset( $atts['proxy'] ) && $atts['proxy'] ) {
			$proxies = get_option( 'leafext_proxy' );
			if ( $proxies != '' ) {
				$directories = explode( ' ', $proxies );
			} else {
				$text = '[ERROR elevation ';
				foreach ( $atts as $key => $item ) {
					$text = $text . "$key=$item ";
				}
				$text = $text . ']';
				return $text;
			}
			$flipped = array_flip( $directories );
			// var_dump($directories);
			$trackurl = trailingslashit( dirname( $track ) );
			// var_dump($trackurl);
			$dir = $flipped[ $trackurl ];
			// var_dump($dir);

			$nonce = WPSimpleNonce::createNonce( 'leafext_getgpx' );
			$track = admin_url( 'admin-ajax.php' ) . '?action=leafext_proxy&name=' . $nonce['name'] .
			'&value=' . base64_encode( $nonce['value'] ) .
			'&dir=' . $dir .
			'&gpx=' . basename( $atts['gpx'] );
			$once  = $nonce['value'];
		}

		if ( $options['chart'] === 'on' || $options['chart'] === 'off' ) {
			$options['closeBtn'] = true;
		} else {
			$options['closeBtn'] = false;
		}

		if ( isset( $options['wptIcons'] ) ) {
			$wpt_icons = $options['wptIcons'];
			if ( ! is_bool( $wpt_icons ) && $wpt_icons == 'defined' ) {
				unset( $options['wptIcons'] );
				$waypoints = get_option( 'leafext_waypoints', '' );
				if ( $waypoints != '' && ( $options['waypoints'] == 'markers' || $options['waypoints'] == '1' ) ) {
					$wptvalue = "{'': L.divIcon({
						className: 'elevation-waypoint-marker',
						html: '<i class=\"elevation-waypoint-icon default\"></i>',
						iconSize: [30, 30],
						iconAnchor: [8, 30],
					}),
					";
					foreach ( $waypoints as $wpt ) {
						$wptvalue = $wptvalue . '"' . $wpt['css'] . '":  L.divIcon(
							{
								className: "elevation-waypoint-marker",
								html: ' . "'" . '<i class="elevation-waypoint-icon ' . $wpt['css'] . '"></i>' . "'" . ',' .
								html_entity_decode( $wpt['js'] ) . '
							}
						),';
					}
					$wptvalue            = $wptvalue . '}';
					$options['wptIcons'] = $wptvalue;
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

		// var_dump($options);

		$handlers = array();

		if ( (bool) $options['pace'] ) {
			$handlers[] = '"Pace"';
			if ( ! (bool) $options['time'] ) {
				$options['time'] = 'summary';
			}
			if ( (bool) $options['speed'] ) {
				$handlers[] = '"Speed"';
			}
			if ( (bool) $options['acceleration'] ) {
				$handlers[] = '"Acceleration"';
			}
			if ( (bool) $options['slope'] ) {
				$handlers[] = '"Slope"';
			}
		}
		if ( (bool) $options['labelsRotation'] || $options['labelsAlign'] != 'start' ) {
			$handlers[] = '"Labels"';
		}
		if ( (bool) $options['linearGradient'] ) {
			$handlers[] = '"Slope"';
			$handlers[] = '"LinearGradient"';
		}

		$handlers = array_unique( $handlers );
		// var_dump($handlers);

		if ( count( $handlers ) > 0 ) {
			$options['handlers'] = '[...L.Control.Elevation.prototype.options.handlers,' . implode( ',', $handlers ) . ']';
		}
		// if (count($handlers) > 0) $options['handlers'] = '["Distance","Time","Altitude",'.implode(',',$handlers).']';
		// if (count($handlers) > 0) $options['handlers'] = '['.implode(',',$handlers).',...L.Control.Elevation.prototype.options.handlers]';
		// if (count($handlers) > 0) $options['handlers'] = '[ "Distance", "Time", "Altitude", "Slope", "Speed", "Acceleration", "Labels"]';

		if ( isset( $options['summary'] ) && $options['summary'] == '1' ) {
			$params = leafext_elevation_params();
			foreach ( $params as $param ) {
				$options['param'] = $param['default'];
			}
			$options['summary']      = 'inline';
			$options['preferCanvas'] = false;
			$options['legend']       = false;
		}
		if ( ! array_key_exists( 'theme', $atts ) ) {
			$options['theme'] = leafext_elevation_theme();
		}

		if ( $options['hotline'] == 'elevation' ) {
			unset( $options['polyline'] );
		}
		if ( $options['direction'] == true ) {
			leafext_enqueue_rotate();
		}
		if ( $options['distanceMarkers'] == true ) {
			leafext_enqueue_rotate();
		}

		list($options, $style) = leafext_elevation_color( $options );
		ksort( $options );

		$text = $style . testleafext_elevation_script( $track, $options );
				return $text;
	}
}
add_shortcode( 'testelevation', 'testleafext_elevation_function' );
