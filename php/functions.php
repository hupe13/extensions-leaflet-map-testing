<?php

function leafext_enqueue_placementstrategies () {
	wp_enqueue_script('placementstrategies',
		plugins_url('leaflet-plugins/Leaflet.MarkerCluster.PlacementStrategies/leaflet-markercluster.placementstrategies.js',TESTLEAFEXT_PLUGIN_FILE),
		array('markercluster'),null );
}
