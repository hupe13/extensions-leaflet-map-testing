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
';
	return $text;
}
