<?php
/**
 * hupe13 geklaut von leaflet-map/templates/shortcode-helper.php und angepasst
 */
?>
<div class="wrap">
    <div class="wrap">
      <?php
      $drag = __('Suche', 'leaflet-map');
      echo do_shortcode('[leaflet-map zoom=9 zoomcontrol doubleClickZoom=1 height=500 scrollwheel=1 maxbounds="51.374100,11.332917;53.522835,14.753333"]');
      echo do_shortcode(sprintf('[leaflet-marker draggable=1 visible="false"] %s [/leaflet-marker]', $drag));
      ?>
    <script>
    function initShortcodes () {
      var lon_input = document.getElementById('lon');
      var lat_input = document.getElementById('lat');
      marker_1 = WPLeafletMapPlugin.markers[0];
      function update_marker () {
        var latlng = marker_1.getLatLng();
        lon_input.value = latlng.lng.toString().slice(0, 7);
        lat_input.value = latlng.lat.toString().slice(0, 7);
      };
      marker_1.on('drag', update_marker);
      update_marker();
    }
    window.WPLeafletMapPlugin = window.WPLeafletMapPlugin || [];
    window.WPLeafletMapPlugin.push( initShortcodes );
    </script>
    <div class="wrap">
      <hr>
      <p class="description"><?php _e('Positioniere den Marker und klicke:', 'leaflet-map'); ?></p>
      <div class="wrap">
        <form action="link-to-my-script">
          <input type="text" name="lon" id="lon" readonly="readonly" size="6">
          <input type="text" name="lat" id="lat" readonly="readonly" size="6">
          <input type="submit" value="Submit">
        </form>
      </div>
    </div>
  </div>
</div>
