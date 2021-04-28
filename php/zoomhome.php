<?php
//Shortcode: [zoomhomemap]

//https://stackoverflow.com/questions/43228007/check-if-font-awesome-exists-before-enqueueing-to-wordpress/43229715
function testleafext_plugin_stylesheet_installed($array_css) {
    global $wp_styles;
    foreach( $wp_styles->queue as $style ) {
        foreach ($array_css as $css) {
            if (false !== strpos( $wp_styles->registered[$style]->src, $css ))
                return 1;
        }
    }
    return 0;
}

// from php debugging
function console_log( $data ){
  echo '<script>';
  echo 'console.log('. json_encode( $data ) .')';
  echo '</script>';
}

function testleafext_plugin_zoomhome_function($atts){
	wp_enqueue_script('zoomhome',
		plugins_url('leaflet-plugins/leaflet.zoomhome/leaflet.zoomhome.min.js',LEAFEXT_PLUGIN_FILE),
			array('wp_leaflet_map'), null);
	wp_enqueue_style('zoomhome',
		plugins_url('leaflet-plugins/leaflet.zoomhome/leaflet.zoomhome.css',LEAFEXT_PLUGIN_FILE),
			array('leaflet_stylesheet'), null);
	// Font awesome
	$font_awesome = array('font-awesome', 'fontawesome');
	if (leafext_plugin_stylesheet_installed($font_awesome) === 0) {
			wp_enqueue_style('font-awesome',
        plugins_url('css/font-awesome.min.css',LEAFEXT_PLUGIN_FILE),
          array('zoomhome'), null);
	}
	// custom js
	wp_enqueue_script('testmyzoomhome',
		plugins_url('js/zoomhome.js',TESTLEAFEXT_PLUGIN_FILE), array('zoomhome'), null);

  console_log($atts);

	if (is_array($atts)) {

 		foreach ($atts as $attribute => $value) {
								
			if (is_int($attribute)) {
				$atts[strtolower($value)] = true;
				unset($atts[$attribute]);
			}
		}

	$fit=true;
	if ( array_key_exists('!fit', $atts) ) {
		$fit = false;
	} else if ( array_key_exists('fit', $atts) ) {
		if ( $atts['fit'] != "" ) {
			$fit = (bool)$atts['fit'];
												   
		} else {
			$fit = true;
		}
	}
	} else {
		$fit=true;
	}
  console_log($atts);
  //var_dump($atts,$fit); wp_die();
  console_log($fit); //wp_die();
  $params=array('fit'=> $fit);
	// Uebergabe der php Variablen an Javascript
	wp_localize_script( 'testmyzoomhome', 'zoomhomemap', $params);
}
add_shortcode('testzoomhomemap', 'testleafext_plugin_zoomhome_function' );
