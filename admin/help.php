<?php
include_once TESTLEAFEXT_PLUGIN_DIR . "/pkg/parsedown-1.7.4/Parsedown.php";
$text = file_get_contents( TESTLEAFEXT_PLUGIN_DIR . "/README.md" );

$Parsedown = new Parsedown();
echo $Parsedown->text($text);

include_once TESTLEAFEXT_PLUGIN_DIR . '/php/leaflet-search.php';
leafext_leafletsearch_help();
