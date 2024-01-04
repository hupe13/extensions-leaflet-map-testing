<?php
/**
 * Documentation HELP
 *
 * @package Extensions for Leaflet Map
 */

// Direktzugriff auf diese Datei verhindern.
defined( 'ABSPATH' ) || die();

require_once TESTLEAFEXT_PLUGIN_DIR . '/pkg/parsedown-1.7.4/Parsedown.php';

require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php';
require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-direct.php';
$filesystem = new WP_Filesystem_Direct( true );
$text       = $filesystem->get_contents( TESTLEAFEXT_PLUGIN_DIR . '/readme.md' );

$parsedown = new Parsedown();
echo $parsedown->text( $text );
