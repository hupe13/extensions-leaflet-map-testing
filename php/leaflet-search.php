<?php

function leafext_enqueue_leafletsearch () {
  wp_enqueue_script('leafletsearch',
  plugins_url('leaflet-plugins/leaflet-search/dist/leaflet-search.min.js',
  //plugins_url('leaflet-plugins/leaflet-search/dist/leaflet-search.src.js',
  TESTLEAFEXT_PLUGIN_FILE),
  array('wp_leaflet_map'), null);
  wp_enqueue_style('leafletsearch',
  plugins_url('leaflet-plugins/leaflet-search/dist/leaflet-search.min.css',
  TESTLEAFEXT_PLUGIN_FILE),
  array('leaflet_stylesheet'), null);
  wp_enqueue_style('leafletsearch_mobile',
  plugins_url('leaflet-plugins/leaflet-search/dist/leaflet-search.mobile.min.css',
  TESTLEAFEXT_PLUGIN_FILE),
  array('leaflet_stylesheet'), null);
}

function leafext_leafletsearch_function($atts,$content,$shortcode) {
	$text = leafext_should_interpret_shortcode($shortcode,$atts);
	if ( $text != "" ) {
		return $text;
	} else {
    if ( !is_array($atts) || ! isset($atts['propertyname'] )) {
      $text = "[leafletsearch ";
      if (is_array($atts)){
        foreach ($atts as $key=>$item){
          $text = $text. "$key=$item ";
        }
      }
      $text = $text. "]";
      return $text;
    }
    if ( ! isset( $atts['zoom'] )) $atts['zoom'] = "15";
    leafext_enqueue_leafletsearch ();
    return leafext_leafletsearch_script($atts);
  }
}
add_shortcode('leafletsearch', 'leafext_leafletsearch_function' );

Function leafext_leafletsearch_script($options){
  $text = '
  <script>
  window.WPLeafletMapPlugin = window.WPLeafletMapPlugin || [];
  window.WPLeafletMapPlugin.push(function () {
    let att_property = '.json_encode($options['propertyname']).';
    let att_zoom = '.json_encode($options['zoom']).';
    ';
		$text = $text.file_get_contents(TESTLEAFEXT_PLUGIN_URL.'/js/leaflet-search.js');
		$text = $text.'
  });
  </script>';
  //$text = \JShrink\Minifier::minify($text);
  return "\n".$text."\n";
}
