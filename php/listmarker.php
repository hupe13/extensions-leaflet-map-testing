<?php
/**
 * Functions for listmarker
 *
 * @package Extensions for Leaflet Map
 */

// Direktzugriff auf diese Datei verhindern.
defined( 'ABSPATH' ) || die();

function leafext_enqueue_listmarker_test() {
	wp_enqueue_script(
		'listmarker_js',
		plugins_url(
			'leaflet-plugins/listmarkers/leaflet-list-markers.js',
			TESTLEAFEXT_PLUGIN_FILE
		),
		array( 'wp_leaflet_map' ),
		null,
		true
	);

	leafext_enqueue_targetmarker();
	leafext_enqueue_js();

	wp_enqueue_style(
		'listmarker_css',
		plugins_url(
			'leaflet-plugins/listmarkers/leaflet-list-markers.css',
			TESTLEAFEXT_PLUGIN_FILE
		),
		array( 'leaflet_stylesheet' ),
		null
	);
}

function leafext_listmarker_script_test( $options ) {
	$text = '<script><!--';
	ob_start();
	?>/*<script>*/
	window.WPLeafletMapPlugin = window.WPLeafletMapPlugin || [];
	window.WPLeafletMapPlugin.push(function () {
		let overiconurl = <?php echo wp_json_encode( $options['overiconurl'] ); ?>;
		let collapse = <?php echo wp_json_encode( $options['collapse'] ); ?>;
		let update = <?php echo wp_json_encode( $options['update'] ); ?>;
		let maxItems = <?php echo wp_json_encode( $options['maxitems'] ); ?>;
		let hover = <?php echo wp_json_encode( $options['hover'] ); ?>;
		let maxheight = <?php echo wp_json_encode( $options['maxheight'] ); ?>;
		let maxwidth = <?php echo wp_json_encode( $options['maxwidth'] ); ?>;

		var map = window.WPLeafletMapPlugin.getCurrentMap();

		if ( WPLeafletMapPlugin.markers.length > 0 ) {
			if ( maxItems == 0 ) {
				maxItems = WPLeafletMapPlugin.markers.length + 1;
			}

			map.on("update-end", function(e) {
				//console.log("update-end", map.options._collapse);
				if (leafext_map_popups( map ) )  {
					for (var i = 0; i < WPLeafletMapPlugin.markers.length; i++) {
						let thismarker = WPLeafletMapPlugin.markers[i];
						if (thismarker.getPopup()) {
							if (thismarker.getPopup().isOpen()) {
								let thistitle=thismarker.options.listtitle+" ";
								//console.log("map update",thistitle);
								// leafext_set_list_background (map, thistitle, "red", false, "map-update");
								leafext_set_list_background (map, thistitle, "rgba(255, 255, 255, 0.8)", false, "");
							} else {
								leafext_set_origicon ( thismarker );
							}
						}
					}
				}
			});

			// marker with popup open don't get into cluster sometimes
			map.on("zoomstart", function(e) {
				// console.log("zoomstart");
				map.eachLayer((l) => {
					if( l instanceof L.Marker ) {
						if (l.__parent) {
							// console.log("cluster");
							leafext_close_popups(map);
						// } else {
						// 	console.log("no cluster");
						}
					}
				});
			});

			var markersLayer = new L.LayerGroup();	//layer contain searched elements

			for (var i = 0; i < WPLeafletMapPlugin.markers.length; i++) {
				let thismarker = WPLeafletMapPlugin.markers[i];
				//console.log("thismarker",thismarker);
				thismarker.options.riseOnHover = true;
				thismarker._origicon = thismarker.getIcon();
				let markeroptions = thismarker.getIcon().options;

				if (overiconurl == "") {
					thismarker._overicon = thismarker.getIcon();
				} else {
					var markericon    = L.Icon.extend({
						options: markeroptions,
					});
					overicon = new markericon({
						iconUrl: overiconurl,
					});
					thismarker._overicon = overicon;
				}
				thismarker.options.listtitle = thismarker.options.title;

				// hide default tooltip
				leafext_unbind_title(thismarker);

				thismarker.on(
					"mouseover",
					function (e) {
						if (leafext_map_popups( map ) == false)  {
							let thistitle=e.sourceTarget.options.listtitle+" ";
							// console.log("mouseover: "+thistitle);
							if (hover == true) {
								leafext_set_list_background (map, thistitle, "rgba(255, 255, 255, 0.8)", true, "");
							}
							leafext_set_overicon ( e.sourceTarget, false, true );
						}
					}
				);
				thismarker.on(
					"mouseout",
					function (e) {
						if (leafext_map_popups( map ) == false)  {
							let thistitle=e.sourceTarget.options.listtitle+" ";
							// console.log("marker mouseout: "+thistitle);
							if (hover == true) {
								leafext_set_list_background (map, thistitle, "", false, "");
							}
							leafext_set_origicon ( e.sourceTarget );
						}
					}
				);
				thismarker.on(
					"click",
					function (e) {
						let thistitle=e.sourceTarget.options.listtitle+" ";
						//console.log("marker click",thistitle);
						leafext_set_overicon ( e.sourceTarget, true, false );
						// leafext_set_list_background (map, thistitle, "green", true, map, "click");
						leafext_set_list_background (map, thistitle, "rgba(255, 255, 255, 0.8)", true, map, "");
					}
				);
				thismarker.on("popupopen", function(e) {
					let thistitle=e.sourceTarget.options.listtitle;
					//console.log("popupopen",thistitle);
					leafext_unbind_all_tooltips();
				});
				thismarker.on("popupclose", function(e) {
					let thistitle=e.sourceTarget.options.listtitle+" ";
					//console.log("popupclose",thistitle);
					leafext_set_list_background (map, thistitle, "", false, "");
					leafext_set_origicon ( e.sourceTarget );
				});

				markersLayer.addLayer(thismarker);
				map.removeLayer( thismarker );
			}
			// console.log(markersLayer);
			map.addLayer(markersLayer);

			//inizialize Leaflet List Markers
			var list = new L.Control.ListMarkers({
				layer: markersLayer,
				itemIcon: null,
				maxItems: maxItems,
				collapsed: collapse,
				label: 'listtitle',
				update: update,
				maxheight: maxheight,
				maxwidth: maxwidth
			});
			list.on("item-mouseover", function(e) {
				if (leafext_map_popups( map ) == false)  {
					e.layer.fire("mouseover");
				}
			});
			list.on("item-mouseout", function(e) {
				if (leafext_map_popups( map ) == false) {
					e.layer.fire("mouseout");
				}
			});
			list.on("item-click", function(e) {
				leafext_close_popups(map);
				let thistitle=e.layer.options.listtitle+" ";
				//console.log("item-click",thistitle);
				leafext_set_list_background ("rgba(255, 255, 255, 0.8)", thistitle, false, map, "");
				//leafext_set_list_background (map, thistitle, "yellow", true, "item-click");
				leafext_set_overicon ( e.layer, false, false );
				thismapbounds = [];
				leafext_target_latlng_marker_do( map,e.layer.getLatLng().lat,e.layer.getLatLng().lng,e.layer.getPopup(),map.getZoom(),true );
			});
			map.addControl( list );
		}
	});

	function leafext_close_tooltip(map) {
		map.eachLayer(
			function (layer) {
				if (layer.options.pane === "tooltipPane") {
					layer.removeFrom( map );
					//console.log("removed");
				}
			}
		);
	}
	function leafext_unbind_title(thismarker) {
		thismarker.unbindTooltip();
		thismarker.bindTooltip( "", {visibility: 'hidden', opacity: 0} ).closeTooltip();
		thismarker.options.title = "";
	}
	function leafext_close_popups(map) {
		map.eachLayer(
			function (layer) {
				if (layer.options.pane === "popupPane") {
					layer.removeFrom( map );
					// console.log("leafext_close_tooltips");
				}
			}
		);
	}
	function leafext_unbind_all_tooltips() {
		for (var i = 0; i < WPLeafletMapPlugin.markers.length; i++) {
			let thismarker = WPLeafletMapPlugin.markers[i];
			leafext_unbind_title(thismarker);
		}
	}
	function leafext_set_list_background ( map, thistitle, farbe, scroll, debug="" ) {
		let lis = document.querySelectorAll("a");
		for (let i = 0; i < lis.length; i++) {
			let a = lis[i];
			if (a.text.includes(thistitle)) {
				if ( scroll ) {
					//a.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'start' });
					a.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'end' });
				}
				a.style.backgroundColor = farbe;
			}
		}
	}
	function leafext_set_origicon ( thismarker ) {
		thismarker.setIcon(thismarker._origicon);
		thismarker.closeTooltip();
	}
	function leafext_set_overicon ( thismarker, popup, tooltip ) {
		thismarker.setIcon(thismarker._overicon);
		if ( popup ) {
			thismarker.openPopup();
		}
		if (tooltip) {
			thismarker.bindTooltip( thismarker.options.listtitle ,{className: 'leafext-tooltip'});
			thismarker.openTooltip();
		}
	}

	<?php
	$javascript = ob_get_clean();
	$text       = $text . $javascript . '//-->' . "\n" . '</script>';
	// $text       = \JShrink\Minifier::minify( $text );
	return "\n" . $text . "\n";
}

function leafext_listmarker_function_test( $atts, $content, $shortcode ) {
	$text = leafext_should_interpret_shortcode( $shortcode, $atts );
	if ( $text !== '' ) {
		return $text;
	} else {
		if ( ! is_array( $atts ) ) {
			$atts = array();
		}
		$defaults            = array(
			'overiconurl' => TESTLEAFEXT_PLUGIN_URL . '/icons/marker-icon-red.png',
			'collapse'    => false,
			'update'      => true,
			'maxitems'    => 0,
			'hover'       => false,
			'maxheight'   => 0.7,
			'maxwidth'    => 0.5,
		);
		$options             = shortcode_atts( $defaults, leafext_clear_params( $atts ) );
		$options['collapse'] = filter_var( $options['collapse'], FILTER_VALIDATE_BOOLEAN );
		$options['update']   = filter_var( $options['update'], FILTER_VALIDATE_BOOLEAN );
		$options['hover']    = filter_var( $options['hover'], FILTER_VALIDATE_BOOLEAN );
		leafext_enqueue_listmarker_test();
		return leafext_listmarker_script_test( $options );
	}
}
add_shortcode( 'listmarkertest', 'leafext_listmarker_function_test' );
