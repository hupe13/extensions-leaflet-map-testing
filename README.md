# Extensions Leaflet Map Testing

Tested up to: 5.8.1  
License: GPLv2 or later  
Contributors: hupe13

## Description

Tests for including Leaflet Plugins in the Wordpress Plugin leaflet-map

This code is changed from time to time and may (or may not) be published in the plugin Extensions Leaflet Map. Testers are welcome and new ideas too.

<h2>Installation</h2>

Download the zip file and <a href="https://wordpress.org/support/article/managing-plugins/#manual-upload-via-wordpress-admin">upload it via WordPress Admin</a>.
Activate the plugin. Prerequisites are <a href="https://wordpress.org/plugins/leaflet-map/">Leaflet Map</a> and <a href="https://wordpress.org/plugins/extensions-leaflet-map/">Extensions Leaflet Map</a>.

<h2>Testing</h2>

<h3>File Manager</h3>
In admin interface: Lists all gpx and kml files in uploads directory. Copy shortcodes.

<h2>Clustering markers in geojson files</h2>

<pre><code>
[leaflet-map ...]
[leaflet-gpx src="url/to/file.gpx" ...]{name}[/leaflet-gpx]
[hover]
[geojsoncluster]
[zoomhomemap]
</code></pre>

<pre><code>
[leaflet-map ...]
[leaflet-geojson src="url/to/file.geojson" ... ]{popup-text}[/leaflet-geojson]
[hover]
[geojsoncluster]
[zoomhomemap]
</code></pre>

<h2>Tracks from all files in a directory</h2>

* url - url to directory. default: URL from wp_get_upload_dir().
* src - (relative) path to directory, accessible both from path and from url
* type - gpx or kml, default: gpx

<pre><code>
[leaflet-map fitbounds]
[leaflet-dir url="..." src="..." type="..."]
[hover]
[hidemarkers]
</code></pre>

<h2>now in official plugin</h2>
Former tested functions see <a href="https://github.com/hupe13/extensions-leaflet-map/">here</a>.
