<h3 id="shortcodes">Shortcodes</h3><h4 id="display-a-track-with-elevation-profile">Anzeige eines Tracks mit Höhenprofil</h4>

<p>Gehe zu Einstellungen -> Leaflet Map -> Leaflet Map Extensions und wähle ein Farbthema aus.</p>
<pre><code>[leaflet-map ....]
// at least one marker if you use it with zoomehomemap
[leaflet-marker lat=... lng=... ...]Start[/leaflet-marker]
[elevation gpx="url_gpx_file"]
// or
[elevation gpx="url_gpx_file" summary=1]
</code></pre><h4 id="switching-tile-layers">Umschalten von Tilelayers</h4>
<p>
</p><p>Gehe zuerst zu Einstellungen -> Leaflet Map -> Leaflet Map Extensions und konfiguriere die Tileserver.</p>
<pre><code>[leaflet-map mapid="..." ...]
[layerswitch]
</code></pre><h4 id="leaflet.markercluster">Leaflet.markercluster</h4>
<p>Viele Marker auf einer Karte werden unübersichtlich. Deshalb werden sie geclustert.</p>Du kannst den Radius (maxClusterRadius) und den Zoom (disableClusteringAtZoom) in den Einstellungen -> Leaflet Map -> Leaflet Map Extensions oder pro Karte festlegen.<pre><code>[leaflet-map ....]
// many markers
[leaflet-marker lat=... lng=... ...]poi1[/leaflet-marker]
[leaflet-marker lat=... lng=... ...]poi2[/leaflet-marker]
 ...
[leaflet-marker lat=... lng=... ...]poixx[/leaflet-marker]
[cluster]
// or
[cluster radius="..." zoom="..."]
[zoomhomemap]
</code></pre><h4 id="leaflet.featuregroup.subgroup">Leaflet.FeatureGroup.SubGroup</h4>

<p>Dynamisches Hinzufügen/Entfernen von Markergruppen aus Marker Cluster.
Parameter:</p>
<ul>
<li>feat - mögliche sinnvolle Werte: iconUrl, title, (weitere???)</li>
<li>strings - durch Komma getrennte Zeichenketten zur Unterscheidung der
 Marker, z. B. eine eindeutige Zeichenkette in iconUrl oder title</li>
<li>groups - durch Komma getrennte Beschriftungen werden im Auswahlmenü angezeigt</li>
<li>Die Anzahl von strings und groups muss übereinstimmen.</li>
</ul>
<pre><code>[leaflet-marker title="..." iconUrl="...red..." ... ] ... [/leaflet-marker]
[leaflet-marker title="..." iconUrl="...green..." ... ] ... [/leaflet-marker]
//many markers
[markerClusterGroup feat="iconUrl" strings="red,green" groups="rot,gruen"]
</code></pre>
<p>Hier werden die Gruppen nach der Farbe der Marker unterschieden.</p><h4 id="leaflet.zoomhome">leaflet.zoomhome</h4>
<p>
"Home" Button um die Ansicht zurückzusetzen. Ein Muss für Markercluster.</p><p>Du
 kannst festlegen, ob zoomhomemap beim Aufruf der Karte auf alle Objekte
 zoomen soll. Dies gilt aber nur für synchron geladene Objekte wie
Marker.
Für asynchron geladene Objekte, wie Geojsons, verwende das leaflet-map
Attribut fitbounds. Wenn der Shortcode elevation verwendet wird, muss
mindestens ein Marker (z. B. Startpunkt) angegeben werden.
</p>
<pre><code>[leaflet-map lat=... lng=... zoom=... !fitbounds !zoomcontrol]
[leaflet-marker ....]
[zoomhomemap !fit]</code>
</pre>oder
<pre><code>[leaflet-map !zoomcontrol ....]
  ...
[zoomhomemap]
</code></pre><h4 id="fullscreen">Fullscreen</h4>
<pre><code>[fullscreen]</code></pre><h4 id="hovergeojson">hovergeojson</h4>
<p>Verwende diese Option, um einen Geojson-Bereich oder einen Track beim Überfahren mit der Maus hervorzuheben.</p>
<pre><code>[leaflet-map ...]
[leaflet-geojson src="//url/to/file.geojson" color="..."]...[/leaflet-geojson]
//or / and
[leaflet-gpx src="//url/to/file.gpx" color="..."]...[/leaflet-gpx]
//or / and
[leaflet-kml src="//url/to/file.kml" color="..."]...[/leaflet-kml]
[hover]
</code></pre><h4 id="gesturehandling">GestureHandling</h4>
<pre><code>[leaflet-map dragging ... ]
// or
[leaflet-map scrollwheel ... ]
// or
[leaflet-map dragging scrollwheel ... ]
</code></pre><h4 id="hide-markers">Hide Markers</h4>
<p>Falls ein GPX-Track Wegpunkte enthält, die man nicht anzeigen lassen möchte.</p>
<pre><code>[leaflet-map ...]
[leaflet-gpx src="//url/to/file.gpx" ... ]
[hidemarkers]
</code></pre>
