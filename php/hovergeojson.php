<?php
//Shortcode: [hover]
function testleafext_hover_function(){
	// custom js
	wp_enqueue_script('hovergeojson_custom', plugins_url('js/hovergeojson.js',LEAFEXT_PLUGIN_FILE), array('wp_leaflet_map'), null);
}
add_shortcode('testhover', 'testleafext_hover_function' );
?>
