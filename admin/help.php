<?php
function leafext_help_text () {
    $text =
	"<p>".
    '<h2>Multiple Maps with elevation profile on one page</h2>
    <pre>
<code>[leaflet-map]
[testelevation gpx="url_to_file1" summary=0]
[leaflet-map]
[testelevation gpx="url_to_file2" summary=1]</code>
</pre>
    <h2>Geojson Popup on mouseover</h2>
    <p>Use it like <code>[hover]</code></p>
<pre>
<code>[testhover]</code>
</pre>
    <h2>Markercluster</h2>
    <p>You can define radius (maxClusterRadius) and zoom (disableClusteringAtZoom) in Settings -&gt; Leaflet Map -&gt; Extensions Tests or per map.</p>
<pre>
<code>[testcluster radius="..." zoom="..."]</code>
</pre>
<h2>Zoomhome</h2>
You can define wether zoomhomemap should zoom to all objects when calling the map.
<pre>
[leaflet-map lat=... lng=... zoom=... !fitbounds !zoomcontrol]
[leaflet-marker ....]
[testzoomhomemap !fit]
</pre>'
."</p>";
	return $text;
}
