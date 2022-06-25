<?php
// Direktzugriff auf diese Datei verhindern:
defined( 'ABSPATH' ) or die();

function leafext_color_name_to_hex($color_name) {
  $colors  =  array(
    'blue'=>'0000FF',
    'green'=>'008000',
    'orange'=>'FFA500',
    'red'=>'FF0000',
    'yellow'=>'FFFF00',
  );
  //
  $color_name = strtolower($color_name);
  if (isset($colors[$color_name])) {
    return ('#' . $colors[$color_name]);
  } else {
    return ($color_name);
  }
}

//Shortcode: [leaflet_dir dir=...]

function leafext_directory_function($atts) {
  $defaults = array (
    'src' => "",
    'url' => "",
    'type' => "gpx",
    'cmd' => "", //leaflet-gpx, leaflet-kml, leaflet-geojson,
    //[elevation-tracks filename=0/1 summary=0/1]
    //[multielevation filename=0/1 option1=value1 option2 !option3 ...]
  );
  $options = shortcode_atts($defaults, $atts);
  if ( $options['src'] == "" ) {
    $options['src'] = '...missing...';
    $text = '[leaflet_dir ';
    foreach ($options as $key=>$item){
      $text = $text. $key.'="'.$item.'" ';
    }
    $text = $text. "]";
    return $text;
  }
  $dir = $atts['src'];
  $text="";

  $upload_dir = wp_get_upload_dir();
  $upload_path = $upload_dir['path'];
  $upload_url = $upload_dir['url'];
  if ($options['url'] == "") {
    $url = $upload_url;
  } else {
    $url = $options['url'];
  }

  if (!is_dir($dir) && !is_dir($upload_path.'/'.$dir)) {
    $text = '[leaflet_dir ';
    $options['src'] = '...not exists... '.$options['src'];
    foreach ($options as $key=>$item){
      $text = $text. $key.'="'.$item.'" ';
    }
    $text = $text. "]";
    return $text;
  }

  if (!is_dir($dir)) {
    $dirpath = $upload_path;
  } else {
    $dirpath = "";
  }

  $files = glob($dirpath.$dir."/*".$options['type']);
  if (count($files) == 0) {
    $options['type'] = '...not found any... '.$options['type'];
    $text = '[leaflet_dir ';
    foreach ($options as $key=>$item){
      $text = $text. $key.'="'.$item.'" ';
    }
    $text = $text. "]";
    return $text;
  }

  switch ($options['cmd']) {
    case "":
    $command = "leaflet-".$options['type'];
    break;
    case "leaflet-gpx":
    case "leaflet-kml":
    case "leaflet-geojson":
    case "elevation-tracks":
    case "multielevation":
    $command = $options['cmd'];
    break;
    default:
    $text = '[leaflet_dir ';
    $options['cmd'] = '...not exists... '.$options['cmd'];
    foreach ($options as $key=>$item){
      $text = $text. $key.'="'.$item.'" ';
    }
    $text = $text. "]";
    return $text;
  }

  if ( preg_match("/leaflet-/", $command)) {
    $farben=array("green","red","blue","yellow","orange");
    $count=1;
    $shortcode='';

    foreach ( $files as $file) {
      $farbe=leafext_color_name_to_hex($farben[$count % count($farben)]);
      $count=$count+1;
      if ($dirpath != "" ) $file = str_replace($dirpath.'/',"",$file);
      $shortcode = $shortcode.'['.$command.' src="'.$url.'/'.$file.'" color="'.$farbe.'"]{name}[/'.$command.']';
    }
    $shortcode = $shortcode.'[hidemarkers]';
    foreach ( $files as $file) {
      if ( $command == "leaflet-gpx" ) {
        $gpx = simplexml_load_file($file);
        $trackname= $gpx->trk->name;
        $startlat = (float)$gpx->trk->trkseg->trkpt[0]->attributes()->lat;
        $startlon = (float)$gpx->trk->trkseg->trkpt[0]->attributes()->lon;
        $point=array(
          "name" => $trackname,
          "lat" => $startlat,
          "lon" => $startlon,
          "file" => $file,
        );
        //
        $shortcode = $shortcode.'[leaflet-marker lat='.$point['lat'].' lng='.$point['lon'].']'.$point['name'].'[/leaflet-marker]';
      }
    }
    $text=do_shortcode($shortcode);
    //$text=$shortcode;
    return $text;
  } else {
    //[elevation-track file="..." ]
    $shortcode='';
    foreach ( $files as $file) {
      if ($dirpath != "" ) $file = str_replace($dirpath.'/',"",$file);
      $shortcode = $shortcode.'[elevation-track file="'.$url.'/'.$file.'"]';
    }
    if ( $command == "elevation-tracks" ) {
      $shortcode = $shortcode.'[elevation-tracks filename=1 summary=1]';
    } else {
      $shortcode = $shortcode.'[multielevation]';
    }
    $text=do_shortcode($shortcode);
    //$text = $shortcode;
    return $text;
  }
}
if ( is_admin() == false ) add_shortcode('leaflet-dir', 'leafext_directory_function' );
