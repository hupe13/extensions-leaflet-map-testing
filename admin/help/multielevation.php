<?php
// Direktzugriff auf diese Datei verhindern:
defined( 'ABSPATH' ) or die();

$text='
<h2>'.__('Multiple hoverable tracks','extensions-leaflet-map').'</h2>

'.__('Like this:','extensions-leaflet-map').' <a href="https://raruto.github.io/leaflet-elevation/examples/leaflet-elevation_hoverable-tracks.html">https://raruto.github.io/leaflet-elevation/examples/leaflet-elevation_hoverable-tracks.html</a>

<h2>Shortcode</h2>
<pre>
<code>[leaflet-map fitbounds ...]
[elevation-<span style="color: #d63638">track</span> file="..." lat="..." lng="..." name="..."]
//many of this
[elevation-<span style="color: #d63638">track</span> file="..." lat="..." lng="..." name="..."]
[elevation-<span style="color: #d63638">tracks</span> summary=1]  // like in [elevation]
</code></pre>

'.__('The <strong>elevation theme</strong> is the same as','extensions-leaflet-map').' <a href="?page='.LEAFEXT_PLUGIN_SETTINGS.'&tab=elevation">'.__('here','extensions-leaflet-map').'</a>.

'.__('The filter should be:','extensions-leaflet-map').
"<pre>
add_filter('pre_do_shortcode_tag', function ( &#36;output, &#36;shortcode ) {
	if ( 'elevation' == &#36;shortcode ||
		'elevation-tracks' == &#36;shortcode ) {
		custom_elevation_function();
	}
	return &#36;output;
}, 10, 2);
</pre>".

'<h2>'.__('Example','extensions-leaflet-map').'</h2>
'.__('This example is a very special application. gpx-files with tracks in a region are in a directory.','extensions-leaflet-map').'
<pre>
<code>&lt;?php
echo "[leaflet-map height=400px width=100% fitbounds ]";
$pfad="path/to/directory/with/gpx-files";
foreach (glob($pfad."/*.gpx") as $file)
{
	$gpx = simplexml_load_file($file);
	$trackname= $gpx-&gt;trk->name;
	$startlat = (float)$gpx-&gt;trk-&gt;trkseg-&gt;trkpt[0]-&gt;attributes()-&gt;lat;
	$startlon = (float)$gpx-&gt;trk-&gt;trkseg-&gt;trkpt[0]-&gt;attributes()-&gt;lon;
	echo <span style="color: #d63638">\'</span>[elevation-track file="..url/please/adjust/..<span style="color: #d63638">\'</span>.$file.<span style="color: #d63638">\'</span>" lat="<span style="color: #d63638">\'</span>.$startlat.<span style="color: #d63638">\'</span>" lng="<span style="color: #d63638">\'</span>.$startlon.<span style="color: #d63638">\'</span>" name="<span style="color: #d63638">\'</span>.basename($file).<span style="color: #d63638">\'</span>"]<span style="color: #d63638">\'</span>;
}
echo \'[elevation-tracks summary=1]\';
</code></pre>
';
echo $text;
