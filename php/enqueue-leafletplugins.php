<?php

define('TESTLEAFEXT_PROVIDERS_JS_FILE', TESTLEAFEXT_PLUGIN_DIR .
		'leaflet-plugins/leaflet-providers/leaflet-providers.js');
function testleafext_enqueue_providers() {
	wp_enqueue_script('providers',
		plugins_url('leaflet-plugins/leaflet-providers/leaflet-providers.js',TESTLEAFEXT_PLUGIN_FILE),
		array('wp_leaflet_map'),null );
}
