# Extensions Leaflet Map Testing

Contributors: hupe13    
Tags: leaflet, Leaflet Plugins   
Tested up to: 6.4  
Requires at least: 5.5.3     
Requires PHP: 7.4  
License: GPLv2 or later

## Description

This is the backup from elevation-proxy.
I will no longer test and further develop these. Firstly, the goal of encrypting the url is not achieved.
Secondly, the repository cryptojs-aes-php has been archived.

<h2>Installation</h2>

Clone it or download the <a href="https://github.com/hupe13/extensions-leaflet-map-testing/archive/refs/heads/main.zip">zip file</a> and <a href="https://wordpress.org/support/article/managing-plugins/#manual-upload-via-wordpress-admin">upload it via WordPress Admin</a>.
Activate the plugin. Prerequisites are <a href="https://wordpress.org/plugins/leaflet-map/">Leaflet Map</a> and <a href="https://wordpress.org/plugins/extensions-leaflet-map/">Extensions Leaflet Map</a>.

<h2>Testing</h2>

# elevation-proxy (shortcode elevation)

- Attempt to obfuscate the URL to the gpx file and its data
- The url to the gpx file is valid only once (<a href="https://github.com/wahabmirjan/wp-simple-nonce">wp-simple-nonce</a>), when elevation is called.
- The data are encrypted with <a href="https://github.com/brainfoolong/cryptojs-aes-php">cryptojs-aes-php</a>.
- But you can see the password to decrypt in the JavaScript code. It is impossible to protect it.
- Configure the settings in Leaflet Map - Extensions Tests - track proxy

## Shortcode:

```
[ leaflet-map fitbounds]
[testelevation proxy=1 gpx="https://your-domain.tld/path/to/track.gpx"]
```
