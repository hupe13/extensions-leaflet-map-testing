# Extensions Leaflet Map Testing

Contributors: hupe13    
Tags: leaflet
Tested up to: 6.3  
Requires at least: 5.5.3     
Requires PHP: 7.4     
License: GPLv2 or later  

## Description

Tests for the Wordpress Plugin <a href="https://wordpress.org/plugins/extensions-leaflet-map/">Extensions Leaflet Map</a>

This code is changed from time to time and may (or may not) be published in the plugin Extensions Leaflet Map. Testers are welcome and new ideas too.

<h2>Installation</h2>

Download the zip file and <a href="https://wordpress.org/support/article/managing-plugins/#manual-upload-via-wordpress-admin">upload it via WordPress Admin</a>.
Activate the plugin. Prerequisites are <a href="https://wordpress.org/plugins/leaflet-map/">Leaflet Map</a> and <a href="https://wordpress.org/plugins/extensions-leaflet-map/">Extensions Leaflet Map</a>.

<h2>Testing</h2>

# elevation-proxy (shortcode elevation)

- Attempt to obfuscate the URL to the gpx file and its data
- The url to the gpx file is valid only once (for shortcode elevation)
- The data are encrypted with Base64. It is very simple to decrypt.
- The interaction with admin-ajax.php is not correct yet.
- Who can help me, please contact me.

Change the url in elevation-proxy.php to "your very secret directory" with the gpx file.

## Shortcode:

```
[leaflet-map fitbounds]
[leafext-elevation-getgpx url="your very secret directory" gpx="track.gpx"]
```
