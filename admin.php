<?php
// Admin Menu

add_action('admin_init', 'testleafext_init' );
add_action('admin_menu', 'testleafext_add_page', 99);

// Init plugin options to white list our options
function testleafext_init(){
	register_setting( 'testleafext_options', 'testleafext_maps', 'testleafext_validate' );
}

// Add menu page
function testleafext_add_page() {
	//add_options_page('Extensions for Leaflet Map Options', 'Extensions for Leaflet Map', 'manage_options', 'extensions-leaflet-map', 'testleafext_do_page');
	//Add Submenu
	add_submenu_page( 'leaflet-map', 'Extensions Test Options', 'Extensions Tests',
    'manage_options', 'extensions-leaflet-map-testing', 'testleafext_do_page');
}

// Draw the menu page itself
function testleafext_do_page() {
	//var_dump($options);
	?>
	<div class="wrap">
	<h2>Extensions Tests Options</h2>

	Nothing


	</div>
	<?php
}

// Sanitize and validate input. Accepts an array, return a sanitized array.

