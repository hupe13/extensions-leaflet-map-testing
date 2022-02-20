<?php
function leafext_thickbox($track){
  //echo '<style>#wpadminbar { display:none; }</style>';
  date_default_timezone_set(wp_timezone_string());
  $upload_dir = wp_get_upload_dir();
  $upload_path = $upload_dir['path'];
  $upload_url = $upload_dir['url'];
  $path_parts = pathinfo($track);
  $type = $path_parts['extension'];
  if ( ! ( 'kml' == $type || 'gpx' == $type ) ) return;
  //
  echo '<div class="attachment-info"><div class="details"><h2>';
  _e( "Details" );
  echo '</h2><div><strong>';
  _e( "Uploaded on:" );
  echo '</strong> '.date('Y-m-d G:i:s', filemtime($upload_path.$track)).'</div><div><strong>';
  _e( "File name:" );
  echo '</strong> '.basename($track).'</div>';
  //echo '<div ><strong>';
  //_e( "File type:" );
  //$type=mime_content_type($upload_path.$track); gibt nur text/xml zurueck
  //echo '</strong> '.$type.'</div>';
  echo '<div ><strong>';
  _e( "File size: " );
  echo '</strong> '.size_format(filesize($upload_path.$track)).'</div></div><p>';
  $content = do_shortcode('[leaflet-map  height=300 width=300 fitbounds][leaflet-'.$type.' src="'. $upload_url . $track .'"]');
	echo $content;
  echo '</p></div>';

  if ( 'gpx' == $type ) $data = leafext_get_gpx_data($upload_path.$track);
  if ( 'kml' == $type ) $data = leafext_get_kml_data($upload_path.$track);
  $form_fields = array();
  foreach ( $data as $key => $value ) {
    $form_fields[$key] = array(
      'key' => __( $key ),
      'value'  => $value,
    );
  }
  echo leafext_html_table($form_fields);
}
