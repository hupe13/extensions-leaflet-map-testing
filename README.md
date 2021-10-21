# Extensions for Leaflet Map Testing

Tested up to: 5.8.1  
License: GPLv2 or later  
Contributors: hupe13

## Description

Tests for including Leaflet Plugins in the Wordpress Plugin leaflet-map

This code is changed from time to time and may (or may not) be published in the plugin Extensions for Leaflet Map. Testers are welcome and new ideas too.

<h2>Installation</h2>

Download the zip file and <a href="https://wordpress.org/support/article/managing-plugins/#manual-upload-via-wordpress-admin">upload it via WordPress Admin</a>.
Activate the plugin. Prerequisites are <a href="https://wordpress.org/plugins/leaflet-map/">Leaflet Map</a> and <a href="https://wordpress.org/plugins/extensions-leaflet-map/">Extensions for Leaflet Map</a>.

<h2>Testing</h2>
<h3>Leaflet-providers</h3>
<pre><code>[leaflet-map]
[providers maps="WaymarkedTrails.hiking"]

[leaflet-map mapid="OSM"]
[providers maps="WaymarkedTrails.hiking,OPNVKarte"]</code></pre>
For a list of providers see <a href="http://leaflet-extras.github.io/leaflet-providers/preview/">http://leaflet-extras.github.io/leaflet-providers/preview/</a>
(use only these without any api key or similar).

<h2>now in official plugin</h2>
Former tested functions see <a href="https://github.com/hupe13/extensions-leaflet-map/">here</a>.
