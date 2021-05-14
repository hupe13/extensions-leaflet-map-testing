<?php
function leaflet_trails() {
	global $post;
	if ( ! is_page() ) return;
	wp_enqueue_script('trails_leaflet', 'https://unpkg.com/leaflet-trails@0.0.1/leaflet-trails.js', Array('wp_leaflet_map'), '1.0', true);
	wp_enqueue_style('trails_leaflet_styles', 'https://unpkg.com/leaflet-trails@0.0.1/leaflet-trails.css');
	// custom js
	wp_enqueue_script('trails_leaflet_custom', esc_url( plugins_url( 'js/trails.js',
 		dirname(__FILE__) ) ), Array('trails_leaflet'), '1.0', true);
}

add_shortcode('trailmap', 'leaflet_trails' );
?>
