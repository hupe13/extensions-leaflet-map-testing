<?php
// init settings fuer cluster
function testleafext_cluster_init(){
	add_settings_section( 'cluster_settings', 'Marker Cluster', 'testleafext_cluster_text', 'leafext_settings_cluster' );
	add_settings_field( 'leafext_cluster_disableClusteringAtZoom', 'disableClusteringAtZoom', 'testleafext_form_cluster_disableClusteringAtZoom', 'leafext_settings_cluster', 'cluster_settings' );
	add_settings_field( 'leafext_cluster_maxClusterRadius', 'maxClusterRadius', 'testleafext_form_cluster_maxClusterRadius', 'leafext_settings_cluster', 'cluster_settings' );
	add_settings_field( 'leafext_cluster_spiderfyOnMaxZoom', 'spiderfyOnMaxZoom', 'testleafext_form_cluster_spiderfyOnMaxZoom', 'leafext_settings_cluster', 'cluster_settings' );
	register_setting( 'leafext_settings_cluster', 'leafext_cluster', 'testleafext_validate_cluster' );
}
add_action('admin_init', 'testleafext_cluster_init' );

//get Options
function testleafext_form_cluster_get_options($reset=false) {
	if ( ! $reset) $options = get_option('leafext_cluster');
	if ( ! $options ) $options = array();
	//var_dump($options);
	if (!array_key_exists('zoom', $options)) $options['zoom'] = "17";
	if (!array_key_exists('radius', $options)) $options['radius'] = "80";
	if (!array_key_exists('spiderfy', $options)) $options['spiderfy'] = true;
	//var_dump($options);
	return $options;
}

// Baue Abfrage standard zoom
function testleafext_form_cluster_disableClusteringAtZoom () {
	//echo "leafext_form_cluster_disableClusteringAtZoom";
	$options = testleafext_form_cluster_get_options();
	echo '<p>'.__('At this zoom level and below, markers will not be clustered, see', 'extensions-leaflet-map').
	' <a href="https://leaflet.github.io/Leaflet.markercluster/example/marker-clustering-realworld-maxzoom.388.html">'.__('Example','extensions-leaflet-map').
	'</a>.</p><p>'.__('Plugins Default','extensions-leaflet-map').': 17. ';
	echo __('If 0, it is disabled.','extensions-leaflet-map').' ';
	echo __('You can change it for each map:','extensions-leaflet-map');
	echo '</p><pre><code>[cluster zoom=17]</code></pre>';
	echo '<input type="number" class="small-text" name="leafext_cluster[zoom]" value="'.$options['zoom'].'" min="0" max="19" />';
}

function testleafext_form_cluster_maxClusterRadius() {
	//echo "leafext_form_cluster_maxClusterRadius";
	$options = testleafext_form_cluster_get_options();
	//var_dump($options);
	echo '<p>'.__('The maximum radius that a cluster will cover from the central marker (in pixels). Decreasing will make more, smaller clusters.','extensions-leaflet-map')
	.'</p><p>'.__('Default:','extensions-leaflet-map').' 80. ';
	echo __('You can change it for each map:','extensions-leaflet-map').'</p><pre><code>[cluster radius=80]</code></pre>';
	echo '<input type="number" class="small-text" name="leafext_cluster[radius]" value="'.$options['radius'].'" min="10" />';
}

function testleafext_form_cluster_spiderfyOnMaxZoom() {
	//echo "leafext_form_cluster_spiderfyOnMaxZoom";
	//boolean
	$options = testleafext_form_cluster_get_options();
	echo '<p>'.__('When you click a cluster at the bottom zoom level we spiderfy it so you can see all of its markers.','extensions-leaflet-map').'</p>';
	echo '<p>'.__('Default: true. You can change it for each map:','extensions-leaflet-map').'.</p>';
	echo '<pre><code>[cluster spiderfy=1]</code></pre>';
	echo '<input type="checkbox" name="leafext_cluster[spiderfy]" ';
	echo $options['spiderfy'] ? 'checked' : '' ;
	echo '>';
}

// Sanitize and validate input. Accepts an array, return a sanitized array.
function testleafext_validate_cluster($input) {
	if (isset($_POST['submit'])) {
		//echo "submit";
		if( isset( $input['zoom'] ) && $input['zoom'] != "" &&
			isset( $input['radius'] ) && $input['radius'] != ""  ) {
			$input['zoom'] = intval($input['zoom']);
			$input['radius'] = intval($input['radius']);
			$input['spiderfy'] = (bool)($input['spiderfy']);
		} else {
			$input = array();
			$input = testleafext_form_cluster_get_options(1);
		}
	}
	return $input;
}

// Erklaerung
function testleafext_cluster_text() {
  echo '<p>'.__('Please see the <a href="https://github.com/Leaflet/Leaflet.markercluster#options">authors page</a> for options. If you want to change other ones, please tell me','extensions-leaflet-map').'.</p>';
	echo '<p>'.__('To reset all values to their defaults, simply clear the values','extensions-leaflet-map').'.</p>';
}
