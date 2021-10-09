<?php

function leafext_enqueue_easybutton() {
	wp_enqueue_style( 'easybutton_css',
		plugins_url('leaflet-plugins/Leaflet.EasyButton/easy-button.css',TESTLEAFEXT_PLUGIN_FILE),
		array('elevation_css'),null);
	wp_enqueue_script('easybutton',
		plugins_url('leaflet-plugins/Leaflet.EasyButton/easy-button.js',TESTLEAFEXT_PLUGIN_FILE),
		array('elevation_js'),null );
	wp_enqueue_style( 'easybutton_mycss',
		plugins_url('css/easy-button.css',TESTLEAFEXT_PLUGIN_FILE),
		array('easybutton_css'),null);
	// Font awesome
	$font_awesome = array('font-awesome', 'fontawesome');
	if (leafext_plugin_stylesheet_installed($font_awesome) === 0) {
		wp_enqueue_style('font-awesome',
        plugins_url('css/font-awesome.min.css',LEAFEXT_PLUGIN_FILE),
		array('easybutton_css'), null);
	}
}
