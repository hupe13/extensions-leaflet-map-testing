# Extensions Leaflet Map Testing

Contributors: hupe13    
Tags: leaflet, Leaflet Plugins   
Tested up to: 6.6  
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

# tile proxy and caching

- Don't use this on production or mission-critical websites.
- It is still in development.
- Cached tiles are stored in the upload_dir/tiles/tileserver/...
- Why? DSGVO, GDPR, ...
- use ajax-admin.php or Rest API
- If you use Rest API and you have disable these, you need enable the endpoint '/wp-json/leafext-tileproxy/v1/tiles'
- <del>The plugin enables this for [Disable WP REST API](https://wordpress.org/plugins/disable-wp-rest-api/)</del> - Does not work at the moment.
- If you cache, you need space on your server and the first calls of a tile take more time as normal.
- If you cache, 404 errors are okay, it can take several calls until they are gone, the cause is the various server caches.
- Maybe slower as the original servers (depends).
- If you don't cache, it is slower.

## Shortcode:

```
[leaflet-map-tileproxy]
```

Options:
- see leaflet-map
- cache - cache tiles on your server
- restapi - use Rest API, default is admin-ajax.php

# elevation proxy

<a href="https://github.com/hupe13/extensions-leaflet-map-testing/tree/elevation-proxy">Archive</a>
