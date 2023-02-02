<?php
/**
* Functions for leaflet-search shortcode
* extensions-leaflet-map
*/
// Direktzugriff auf diese Datei verhindern:
defined( 'ABSPATH' ) or die();

//Parameter and Values
function leafext_search_params() {
  $params = array(
    // | url             | ''       | url for search by ajax request, ex: "search.php?q={s}". Can be function to returns string for dynamic parameter setting | |
    // | layer		      | null	 | layer where search markers(is a L.LayerGroup)				 |
    // | sourceData	  | null     | function to fill _recordsCache, passed searching text by first param and callback in second				 |
    // | jsonpParam	  | null	 | jsonp param name for search by jsonp service, ex: "callback" |
    // | propertyLoc	  | 'loc'	 | field for remapping location, using array: ['latname','lonname'] for select double fields(ex. ['lat','lon'] ) support dotted format: 'prop.subprop.title' |
    // | propertyName	  | 'title'	 | property in marker.options(or feature.properties for vector layer) trough filter elements in layer, |
    array(
      'param' => 'propertyName',
      'desc' => sprintf(__('a option / property for marker or a %s for geojson layer, must be unique',"extensions-leaflet-map"),
      'feature.property'),
      'default' => 'title',
      'values' => sprintf(__('%s for marker, %s depending on geojson layer',"extensions-leaflet-map"),
      'title, iconclass, popupContent',
      'feature.property'),
    ),
    // | formatData	  | null	 | callback for reformat all data from source to indexed data object |
    // | filterData	  | null	 | callback for filtering data from text searched, params: textSearch, allRecords |
    // | moveToLocation  | null	 | callback run on location found, params: latlng, title, map |
    // | buildTip		  | null	 | function to return row tip html node(or html string), receive text tooltip in first param |
    // | container		  | ''	     | container id to insert Search Control		 |
    // | zoom		      | null	 | default zoom level for move to location |
    array(
      'param' => 'zoom',
      'desc' => __('zoom level for move to location for searching markers',"extensions-leaflet-map"),
      'default' => get_option('leaflet_default_zoom', '15'),
      'values' => '',
    ),
    // | minLength		  | 1	     | minimal text length for autocomplete |
    // | initial		  | true	 | search elements only by initial text |
    // | casesensitive   | false	 | search elements in case sensitive text |
    // | autoType		  | true	 | complete input with first suggested result and select this filled-in text. |
    // | delayType		  | 400	     | delay while typing for show tooltip |
    // | tooltipLimit	  | -1	     | limit max results to show in tooltip. -1 for no limit, 0 for no results |
    // | tipAutoSubmit	  | true	 | auto map panTo when click on tooltip |
    // | firstTipSubmit  | false	 | auto select first result con enter click |
    // | autoResize	  | true	 | autoresize on input change |
    // | collapsed		  | true	 | collapse search control at startup |
    // | autoCollapse	  | false	 | collapse search control after submit(on button or on tips if enabled tipAutoSubmit) |
    // | autoCollapseTime| 1200	 | delay for autoclosing alert and collapse after blur |
    // | textErr		  | 'Location not found' |	error message |
    array(
      'param' => 'textErr',
      'desc' => __('error message',"extensions-leaflet-map"),
      'default' => __('Location not found',"extensions-leaflet-map"),
      'values' => '',
    ),
    // | textCancel	  | 'Cancel	 | title in cancel button		 |
    // | textPlaceholder | 'Search' | placeholder value			 |
    array(
      'param' => 'textPlaceholder',
      'desc' => __('placeholder value',"extensions-leaflet-map"),
      'default' => __('Search...',"extensions-leaflet-map"),
      'values' => '',
    ),
    // // | hideMarkerOnCollapse		 | false	 | remove circle and marker on search control collapsed		 |
    array(
      'param' => 'hideMarkerOnCollapse',
      'desc' => __('remove circle and marker on search control collapsed',"extensions-leaflet-map"),
      'default' => false,
      'values' => "true, false",
    ),
    // // | position		  | 'topleft'| position in the map		 |
    array(
      'param' => 'position',
      'desc' => __('position in the map',"extensions-leaflet-map"),
      'default' => 'topleft',
      'values' => "topleft, topright, bottomleft, bottomright",
    ),
    // // | marker		  | {}	     | custom L.Marker or false for hide |
    array(
      'param' => 'marker',
      'desc' => __('show or hide marker at the position found',"extensions-leaflet-map"),
      'default' => '',
      'values' => sprintf(__("not specified for default (red circle), %s for no marker, or a definition like %s","extensions-leaflet-map"),
      'false',
      '"{icon: false, animate: true, circle: {radius: 10, weight: 3, color: '."'#e03', stroke: true, fill: false}}".'"'),
    ),
    // | marker.icon	  | false	 | custom L.Icon for maker location or false for hide |
    // | marker.animate  | true	 | animate a circle over location found |
    // | marker.circle	  | L.CircleMarker options |	draw a circle in location found |

  );
  return $params;
}

function leafext_enqueue_leafletsearch () {
  wp_enqueue_script('leafletsearch',
  //plugins_url('leaflet-plugins/leaflet-search/dist/leaflet-search.min.js',
  plugins_url('leaflet-plugins/leaflet-search/dist/leaflet-search.src.js',
  TESTLEAFEXT_PLUGIN_FILE),
  array('wp_leaflet_map'), null);
  wp_enqueue_style('leafletsearch',
  plugins_url('leaflet-plugins/leaflet-search/dist/leaflet-search.min.css',
  TESTLEAFEXT_PLUGIN_FILE),
  array('leaflet_stylesheet'), null);
  // wp_enqueue_style('leafletsearch_mobile',
  // plugins_url('leaflet-plugins/leaflet-search/dist/leaflet-search.mobile.min.css',
  // TESTLEAFEXT_PLUGIN_FILE),
  // array('leaflet_stylesheet'), null);
}

function leafext_leafletsearch_function($atts,$content,$shortcode) {
	$text = leafext_should_interpret_shortcode($shortcode,$atts);
	if ( $text != "" ) {
		return $text;
	} else {
    $defaults=array();
  	$params = leafext_search_params();
  	foreach($params as $param) {
  		$defaults[$param['param']] = $param['default'];
  	}
    $atts1=leafext_case(array_keys($defaults),leafext_clear_params($atts));
  	$options = shortcode_atts($defaults, $atts1);
    if ($options['marker'] == '') unset($options['marker']);
    if (strpos($options['textPlaceholder'],'"') !== false) {
      $options['textPlaceholder'] = str_replace('"','\"',$options['textPlaceholder']);
    }
    leafext_enqueue_leafletsearch ();
    //var_dump(leafext_java_params($options));wp_die();
    return leafext_leafletsearch_script($options,trim(preg_replace('/\s+/', ' ',leafext_java_params($options))));
  }
}
add_shortcode('leaflet-search', 'leafext_leafletsearch_function' );

function leafext_leafletsearch_script($options,$jsoptions){
  $text = '
  <script>
  window.WPLeafletMapPlugin = window.WPLeafletMapPlugin || [];
  window.WPLeafletMapPlugin.push(function () {
    let att_property = '.json_encode($options['propertyName']).';
    let att_zoom = '.json_encode($options['zoom']).';
    ';
    ob_start();
    include (TESTLEAFEXT_PLUGIN_DIR.'/js/leaflet-search.js');
    $text = $text.ob_get_clean();
		$text = $text.'
  });
  </script>';
  //$text = \JShrink\Minifier::minify($text);
  return "\n".$text."\n";
}

function leafext_leafletsearch_help(){
  echo '<h3>leaflet-search</h3>';
  $options=leafext_search_params();
  $new = array();
  $new[] = array(
    'param' => "<strong>Option</strong>",
    'desc' => "<strong>".__('Description','extensions-leaflet-map').'</strong>',
    'default' => "<strong>".__('Default','extensions-leaflet-map').'</strong>',
    'values' => "<strong>".__('Values','extensions-leaflet-map').'</strong>',
  );
  foreach ($options as $option) {
    if ($option['default'] == '' && $option['param'] == "marker") $option['default'] = '('.__("red circle","extensions-leaflet-map").')';
    $new[] = array(
      'param' => $option['param'],
      'desc' => $option['desc'],
      'default' => (gettype($option['default']) == "boolean") ? ($option['default'] ? "true" : "false") : $option['default'],
      //'values' => ($option['values'] != "") ? json_encode($option['values']) : "",
      'values' => $option['values'],
    );
  }
  echo '<div style="width:80%;">'.leafext_html_table($new).'</div>';
}
