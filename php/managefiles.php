<?php

function leafext_gpx_content( $content ){
  global $post;
  //
  if ( is_attachment() && 'application/gpx+xml' == get_post_mime_type( $post->ID ) ) {
    $content = '[leaflet-map fitbounds][leaflet-gpx src="'. $post->guid . '"]';
    $gpx_data = leafext_get_gpx_data($post->guid);
    $fields = array();
    $fields[] = array(
      'key' => 'url',
      'value' => $post->guid,
    );
    $fields[] = array(
      'key' => 'filename',
      'value' => basename($post->guid),
    );
    foreach ( $gpx_data as $key => $value ) {
      $fields[] = array(
        'key' => __( $key ),
        'value' => $value,
      );
    }
    $content = $content . leafext_html_table($fields);
  }
  //
  if ( is_attachment() && 'application/vnd.google-earth.kml+xml' == get_post_mime_type( $post->ID ) ) {
    $content = '[leaflet-map fitbounds][leaflet-kml src="'. $post->guid . '"]';
    $fields = array();
    $fields[] = array(
      'key' => 'url',
      'value' => $post->guid,
    );
    $fields[] = array(
      'key' => 'filename',
      'value' => basename($post->guid),
    );
    $content = $content . leafext_html_table($fields);
  }
  return $content;
}
add_filter( 'the_content', 'leafext_gpx_content' );

function leafext_get_gpx_data($file) {
  $gpx_data = array();
	//
	$gpx = simplexml_load_file($file);
	$gpx_data['trackname']= $gpx->trk->name;
  $gpx_data['time']= $gpx->metadata->time;
  if ( $gpx_data['time']== "" ) $gpx_data['time']= $gpx->trk->trkseg->trkpt[0]->time;
  if ( $gpx_data['time']== "" ) $gpx_data['time']= " ";
  return $gpx_data;
}

//https://gist.github.com/jasondavis/6ea170677014aa65aa2ba25269ae16dc
function leafext_html_table($data = array())
{
    $rows = array();
    foreach ($data as $row) {
        $cells = array();
        foreach ($row as $cell) {
            $cells[] = "<td>{$cell}</td>";
        }
        $rows[] = "<tr>" . implode('', $cells) . "</tr>";
    }
    return "<table border='1'>" . implode('', $rows) . "</table>";
}

function leafext_attachment_fields_to_edit( $form_fields, $post ){
  libxml_use_internal_errors(true);
  // get post mime type
  $type = get_post_mime_type( $post->ID );
  // get the attachment path
  $attachment_path = get_attached_file( $post->ID );

  if ( 'application/gpx+xml' == $type ){
    $gpx_data = leafext_get_gpx_data($attachment_path);
    foreach ( $gpx_data as $key => $value ) {
      $form_fields[$key] = array(
        'value' => $value,
        'label' => __( $key ),
        'input' => 'html',
        'html'  => $value,
      );
    }
    $form_fields['overview'] = array(
      'value' => "Map",
      'label' => __( 'Overview' ),
      'input' => 'html',
      'html'  => "Map",
      'helps' => do_shortcode('[leaflet-map height=300 width=300 fitbounds][leaflet-gpx src="'.wp_get_attachment_url( $post->ID ).'"]'),
    );
  }

  if ( 'application/vnd.google-earth.kml+xml' == $type ){
    $kml=simplexml_load_file($attachment_path,"SimpleXMLElement",LIBXML_NOCDATA);
    $trackname = $kml->Document->name;
    $form_fields['overview'] = array(
      'value' => $trackname,
      'label' => __( 'Overview' ),
      'input' => 'html',
      'html'  => $trackname,
      'helps' => do_shortcode('[leaflet-map height=300 width=300 fitbounds][leaflet-kml src="'.wp_get_attachment_url( $post->ID ).'"]'),
    );
  }

  return $form_fields;
}
add_filter( 'attachment_fields_to_edit', 'leafext_attachment_fields_to_edit', 10, 2 );
