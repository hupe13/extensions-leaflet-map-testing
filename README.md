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


<h2>geojson popup on mouseover</h2>

Use it like <code>[hover]</code>

```
[testhover]
```
(not yet perfect)

<h2>leaflet-gpx popup geojson elements</h2>

The opposite of <a href="https://wordpress.org/plugins/extensions-leaflet-map/#hide%20markers"><code>[hidemarkers]</code></a> - show markers and other elements with popup:

```
[leaflet-map ...]
[leaflet-gpx src="//url/to/file.gpx" ... ]
[cluster]  //only for testing to load the js and css for clustering markers
[hover]    //optional
[showmarkers]
```

<h2>Testing ... </h2>

+ https://raruto.github.io/leaflet-elevation/examples/leaflet-elevation_hoverable-tracks.html

<h2>now in official plugin</h2>
Former tested functions see <a href="https://github.com/hupe13/extensions-leaflet-map/">here</a>.

## Changelog

### 1.3

Clean up after release v1.3

### 0.0.17

clean up

### 0.0.16

Parameter zomehomemap and markercluster

### 0.0.15
New functions:
* testhover: Popup on Hover Geojsons
* markercluster: Now you can define disableClusteringAtZoom and maxClusterRadius
