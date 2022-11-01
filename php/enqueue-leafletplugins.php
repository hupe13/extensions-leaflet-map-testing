<?php

// function leafext_enqueue_routingmachine () {
//   wp_enqueue_script('routing-machine',
//   plugins_url('leaflet-plugins/leaflet-routing-machine/dist/leaflet-routing-machine.min.js',
//   TESTLEAFEXT_PLUGIN_FILE),
//   array('wp_leaflet_map'), null);
//   wp_enqueue_style('routing-machine',
//   plugins_url('leaflet-plugins/leaflet-routing-machine/dist/leaflet-routing-machine.css',
//   TESTLEAFEXT_PLUGIN_FILE),
//   array('leaflet_stylesheet'), null);
//   leafext_enqueue_awesome();
// }

define('TESTLEAFEXT_ELEVATION_VERSION',"2.2.7k");
define('TESTLEAFEXT_ELEVATION_URL', TESTLEAFEXT_PLUGIN_URL . '/leaflet-plugins/leaflet-elevation-'.TESTLEAFEXT_ELEVATION_VERSION.'/');
define('TESTLEAFEXT_ELEVATION_DIR', TESTLEAFEXT_PLUGIN_DIR . '/leaflet-plugins/leaflet-elevation-'.TESTLEAFEXT_ELEVATION_VERSION.'/');
function testleafext_enqueue_elevation () {
  wp_enqueue_script( 'elevation_js',
  plugins_url('leaflet-plugins/leaflet-elevation-'.TESTLEAFEXT_ELEVATION_VERSION.'/dist/leaflet-elevation.min.js',
  //plugins_url('leaflet-plugins/leaflet-elevation-'.TESTLEAFEXT_ELEVATION_VERSION.'/dist/leaflet-elevation.js',
  TESTLEAFEXT_PLUGIN_FILE),
  array('wp_leaflet_map'),null);
  //
  wp_enqueue_script( 'Leaflet.i18n',
  plugins_url('leaflet-plugins/Leaflet.i18n/Leaflet.i18n.js',
  LEAFEXT_PLUGIN_FILE),
  array('elevation_js'),null);
  //
  wp_enqueue_style( 'elevation_css',
  plugins_url('leaflet-plugins/leaflet-elevation-'.TESTLEAFEXT_ELEVATION_VERSION.'/dist/leaflet-elevation.min.css',
  TESTLEAFEXT_PLUGIN_FILE),
  array('leaflet_stylesheet'),null);
  //
  leafext_css();
}
