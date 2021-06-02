# Extensions for Leaflet Map Testing

Tested up to: 5.7  
License: GPLv2 or later  
Contributors: hupe13

## Description

Tests for including Leaflet Plugins in the Wordpress Plugin leaflet-map

This code is changed from time to time and may (or may not) be published in the plugin Extensions for Leaflet Map. Testers are welcome and new ideas too.

<h2>Installation</h2>

Download the zip file and <a href="https://wordpress.org/support/article/managing-plugins/#installing-plugins">upload it via WordPress Admin</a>.
Activate the plugin. Prerequisites are <a href="https://wordpress.org/plugins/leaflet-map/">Leaflet Map</a> and <a href="https://wordpress.org/plugins/extensions-leaflet-map/">Extensions for Leaflet Map</a>.
If it doesn't work, use the <a href="https://github.com/hupe13/extensions-leaflet-map">Github version</a>.

<h2>geojson popup on mouseover</h2>

Use it like <code>[hover]</code>

```
[testhover]
```

It works on leaflet-geojson, leaflet-gpx. Not tested yet: leaflet-kml.

<h2>Multiple hoverable tracks</h2>

Like this: https://raruto.github.io/leaflet-elevation/examples/leaflet-elevation_hoverable-tracks.html

```
[leaflet-map fitbounds ...]
[elevation-track file="..." lat="..." lon="..." name="..."]
//many of this
[elevation-track file="..." lat="..." lon="..." name="..."]
[elevation-tracks]
```
This example is a very special application. You have a directory with gpx-files with tracks in a region.
```
<?php
echo '[leaflet-map height=400px width=100% fitbounds ]';
$pfad="path/to/directory/with/gpx-files";
foreach (glob($pfad."/*.gpx") as $file)
{
	$gpx = simplexml_load_file($file);
	$trackname= $gpx->trk->name;
	$startlat = (float)$gpx->trk->trkseg->trkpt[0]->attributes()->lat;
	$startlon = (float)$gpx->trk->trkseg->trkpt[0]->attributes()->lon;
	echo '[elevation-track file="../'.$file.'" lat="'.$startlat.'" lon="'.$startlon.'" name="'.basename($file).'"]';
}
echo '[elevation-tracks]';
```

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
