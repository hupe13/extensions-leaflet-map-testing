# Extensions for Leaflet Map Testing

Tested up to: 5.7  
License: GPLv2 or later  
Contributors: hupe13

## Description

Tests for including Leaflet Plugins in the Wordpress Plugin leaflet-map

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

<h2>Geojson Popup on mouseover</h2>

Use it like <code>[hover]</code>

```
[testhover]
```
(not yet perfect)

<h2>Testing ... </h2>

+ https://raruto.github.io/leaflet-elevation/examples/leaflet-elevation_hoverable-tracks.html

<h2>now in official plugin</h2>
Former tested functions see <a href="https://github.com/hupe13/extensions-leaflet-map/">here</a>.

## Changelog

### 0.0.17

clean up

### 0.0.16

Parameter zomehomemap and markercluster

### 0.0.15
New functions:
* testhover: Popup on Hover Geojsons
* markercluster: Now you can define disableClusteringAtZoom and maxClusterRadius
