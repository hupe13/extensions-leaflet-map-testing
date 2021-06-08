<?php
// Direktzugriff auf diese Datei verhindern:
defined( 'ABSPATH' ) or die();

$text='<h2 id="hovergeojson">hovering</h2>
<img src="'.LEAFEXT_PLUGIN_PICTS.'hover.png">
<p>'.__('Use it to highlight a geojson or marker on mouse over.','extensions-leaflet-map').' '.
__('It works on leaflet-geojson, leaflet-gpx, leaflet-kml and leaflet-marker.','extensions-leaflet-map').'</p>
<h2>Shortcode</h2>
<pre><code>[leaflet-map ...]
[leaflet-geojson src="//url/to/file.geojson" color="..."]...[/leaflet-geojson]
//or / and
[leaflet-gpx src="//url/to/file.gpx" color="..."]...[/leaflet-gpx]
//or / and
[leaflet-kml src="//url/to/file.kml" color="..."]...[/leaflet-kml]
//or / and
[leaflet-marker ....]Marker ....[/leaflet-marker]

[testhover]
//or
[testhover exclude="url-substring"]
</code></pre>'.

__('The parameter <code>exclude</code> is a very special case for my website. I would like to exclude
some leaflet-geojson with a specific string in the src url from changing its style
on hovering. If the url to the geojson file is e.g. "//url/to/special.geojson"
url-substring should be "special".','extensions-leaflet-map').'
';

echo $text;
