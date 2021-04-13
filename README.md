# Tests for including Leaflet Plugins in the Wordpress Plugin leaflet-map

This code is changed from time to time and may (or may not) be published in the plugin Extensions for Leaflet Map. Testers are welcome and new ideas too.

<h2>Installation</h2>

Download the zip file, unzip it. Upload the files to the plugin directory.
Activate the plugin through the 'Plugins' screen in WordPress.
Prerequisites are <a href="https://wordpress.org/plugins/leaflet-map/">Leaflet Map</a> and <a href="https://wordpress.org/plugins/extensions-leaflet-map/">Extensions for Leaflet Map</a>.

<h2>Multiple Maps with elevation profile on one page</h2>

```
[leaflet-map]
[testelevation gpx="url_to_file1" summary=0]
[leaflet-map]
[testelevation gpx="url_to_file2" summary=1]
```

<h2>now in official plugin</h2>
Former tested functions see <a href="https://github.com/hupe13/extensions-leaflet-map/">here</a>.
