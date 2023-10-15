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

# tile proxy and caching

- Don't use this on production or mission-critical websites.
- It is still in development.
- Cached tiles are stored in the upload_dir/tiles/tileserver/...
- Why? DSGVO, GDPR.
- If you cache, you need space on your server and the first call of a tile takes more time as normal.
- Maybe slower as the original servers (depends).
- If you don't cache, it is slower.

## Shortcode:

Options like leaflet-map

Without caching
```
[leaflet-map-tileproxy]
```
With caching
```
[leaflet-map-tileproxy cache]
```
