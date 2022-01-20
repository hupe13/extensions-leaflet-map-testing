<?php
// Direktzugriff auf diese Datei verhindern:
defined( 'ABSPATH' ) or die();

function leafext_color_name_to_hex($color_name) {
    $colors  =  array(
        'blue'=>'0000FF',
        'green'=>'008000',
        'orange'=>'FFA500',
        'red'=>'FF0000',
        'yellow'=>'FFFF00');
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

  if (!is_dir($dir)) {
    $text = '[leaflet_dir ';
    $options['src'] = '...not exists... '.$options['src'];
		foreach ($options as $key=>$item){
			$text = $text. $key.'="'.$item.'" ';
		}
		$text = $text. "]";
		return $text;
  }

  $files = glob($dir."/*".$options['type']);
  if (! is_array($files)) {
    $options['type'] = '...not found any... '.$options['type'];
		$text = '[leaflet_dir ';
		foreach ($options as $key=>$item){
			$text = $text. $key.'="'.$item.'" ';
		}
		$text = $text. "]";
		return $text;
	}

  $upload_dir = wp_get_upload_dir();
  $upload_path = $upload_dir['path'];
  $upload_url = $upload_dir['url'];

  $path_parts = pathinfo($dir);

  if ($options['url'] == "") {
    $url = get_site_url();
  } else {
    $url = $options['url'];
  }

  $farben=array("green","red","blue","yellow","orange");
  $count=1;

  $shortcode='';
  foreach ( $files as $file) {



    	$gpx = simplexml_load_file($file);
    	$trackname= $gpx->trk->name;
    	$startlat = (float)$gpx->trk->trkseg->trkpt[0]->attributes()->lat;
    	$startlon = (float)$gpx->trk->trkseg->trkpt[0]->attributes()->lon;
    	$point=array(
    		"name" => $trackname,
    		"lat" => $startlat,
    		"lon" => $startlon,
    		"file" => $file);
      $shortcode = $shortcode.'[leaflet-marker lat='.$point['lat'].' lng='.$point['lon'].']'.$point['name'].'[/leaflet-marker]';

    $farbe=leafext_color_name_to_hex($farben[$count % count($farben)]);
    $count=$count+1;
    $shortcode = $shortcode.'[leaflet-'.$options['type'].' src="'.$url.'/'.$file.'" color="'.$farbe.'"]{name}[/leaflet-'.$options['type'].']';
  }

  $text=do_shortcode($shortcode);
  return $text;
}
add_shortcode('leaflet-dir', 'leafext_directory_function' );

?>
