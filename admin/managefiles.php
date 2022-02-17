<?php
echo '<h2>Manage Files</h2>';
echo
sprintf(__('Here you can see all gpx and kml files in subdirectories of uploads directory.
  You can manage these
  %s with any (S)FTP-Client,
  %s with any File Manager plugin,
  %s with any plugin for importing uploaded files to the media library,','extensions-leaflet-map'),
  '<ul><li> - ','</li><li> - ','</li><li> - ','</li><li> - ').
  '</li><li> - '.
  __('or direct in the media library.','extensions-leaflet-map').
  '</li>
  <li> - If they are in the media library then here appears an edit link. </li>
  </ul>';
//echo leafext_list_files();

$upload_dir = wp_get_upload_dir();
$upload_path = $upload_dir['path'];

function leafext_all_subdirs() {
	$upload_dir = wp_get_upload_dir();
	$upload_path = $upload_dir['path'];

	$iterator = new RecursiveIteratorIterator(
		new RecursiveDirectoryIterator($upload_path)
	);
	$gpx_files = new RegexIterator($iterator, '/\.(gpx|kml)$/'); // Dateiendung ".gpx"
	//var_dump($gpx_files);
	$gpx_dirs = array();
	foreach ($gpx_files as $file) {
		$myfile = str_replace($upload_path,'',$file->getPathname());
		$entry = dirname($myfile);
		if (!in_array($entry,$gpx_dirs)) $gpx_dirs[] = $entry;
	}
	asort($gpx_dirs);
	return $gpx_dirs;
}

function leafext_file_form($verz) {
	echo '<form action="'.admin_url( 'admin.php' ).'" method="get">';
	echo '<input type="hidden" id="page" name="page" value="'.TESTLEAFEXT_PLUGIN_SETTINGS.'">';
	echo '<input type="hidden" id="tab" name="tab" value="manage_files">';
	echo '<select name="dir">';
	if ($verz == "" ) echo '<option selected  value="">  </option>';
	foreach (leafext_all_subdirs() as $dir) {
		if ($verz == $dir) {
			echo '<option selected ';
		} else {
			echo '<option ';
		}
		echo 'value="'.$dir.'">'.$dir.'</option>';
	}
	echo '</select>';
	echo '<input type="submit" value="Submit">';
	echo '</form>';
}

echo '<h2>Listing ...</h2>';

$dir=get_phwquery("dir");
leafext_file_form($dir);
if ( $dir != "" ) {
  echo '<h3>Directory '.$dir.'</h3>';
  echo '<p><code>[leaflet-dir src="'.$dir.'" ]</code>';

  echo ' <a href="#" onclick="createShortcodedir('.
      "'leaflet-dir  src='".','.
      "'".$dir."'".')">Copy</a></p>';

	echo leafext_list_files($dir);
}

function leafext_list_files($dir) {
  $upload_dir = wp_get_upload_dir();
  $upload_path = $upload_dir['path'];
  $upload_url = $upload_dir['url'];
  $text= '<script>
  function createShortcode(shortcode,file) {
    var leafext_short = document.createElement("input");
    document.body.appendChild(leafext_short);
    leafext_short.value = "["+shortcode+"\"'.$upload_url.'" + file + "\"]";
    leafext_short.select();
    document.execCommand("copy",false);
    leafext_short.remove();
    alert("Copied the text: " + leafext_short.value);
  }
  function createShortcodedir(shortcode,file) {
    var leafext_short = document.createElement("input");
    document.body.appendChild(leafext_short);
    leafext_short.value = "["+shortcode+"\"" + file + "\"]";
    leafext_short.select();
    document.execCommand("copy",false);
    leafext_short.remove();
    alert("Copied the text: " + leafext_short.value);
  }
  </script>';

	$gpx_files = glob($upload_path.'/'.$dir.'/*.{gpx,kml}', GLOB_BRACE);

	//var_dump($gpx_files);
  date_default_timezone_set(wp_timezone_string());

  $gpx_table = array();
  $entry = array('<b>'.__('Date','extensions-leaflet-map').'</b>',
    '<b>'.__('Name','extensions-leaflet-map').'</b>',
    '<b>'.__('Edit','extensions-leaflet-map').'</b>',
    '<b>'.__('leaflet Shortcode','extensions-leaflet-map').'</b>',
    '<b>'.__('elevation Shortcode','extensions-leaflet-map').'</b>');
  $gpx_table[] = $entry;

  foreach ($gpx_files as $file) {
    //if (!$file->isFile()) continue;
    $entry = array();
    //$myfile = str_replace($upload_path.'/','',$file->getPathname());
		$myfile = str_replace($upload_path.'/','',$file);
    global $wpdb;
    $sql = "SELECT post_id FROM $wpdb->postmeta WHERE meta_value LIKE '".substr($myfile, 1)."'";
    $results = $wpdb->get_results($sql);
    if (count($results) > 0 ) {
      foreach ($results as $result) {
        //var_dump( get_post(get_object_vars($result)["post_id"]));
        $key = get_post(get_object_vars($result)["post_id"]);
        $entry['post_date'] = $key -> post_date;
        $entry['post_title'] = $key -> post_title;
        //$entry['post_name'] = $key -> post_name;
        //$entry['guid'] = $key -> guid;
        $entry['edit'] = '<a href ="'.get_admin_url().'post.php?post='.$key -> ID.'&action=edit">'.__('Edit').'</a>';
      }
    } else {
      //$entry['post_date'] = date('Y-m-d G:i:s', $file->getMTime());
			$entry['post_date'] = date('Y-m-d G:i:s', filemtime($file));
      $entry['post_title'] = $myfile;
      $entry['edit'] = '';
    }
    //$entry['copy'] = '<div class="input">'.$myfile.'</div><a href="#" onclick="myFunction(event)">Copy</a>';
    $path_parts = pathinfo($myfile);
    $entry['leaflet'] = '<a href="#" onclick="createShortcode('.
        "'leaflet-".$path_parts['extension']."  src='".','.
        "'".$myfile."'".')">leaflet-'.$path_parts['extension'].'</a>';
    $entry['elevation'] = '<a href="#" onclick="createShortcode('.
        "'elevation gpx='".','.
        "'".$myfile."'".')">elevation</a>';
    $gpx_table[] = $entry;
  }

  $text = $text.leafext_html_table($gpx_table);

  return $text;
}

//Bug https://core.trac.wordpress.org/ticket/36418
// add_filter( 'wp_mime_type_icon', function( $icon, $mime, $post_id )
// {
//     if( 'application/gpx+xml' === $mime && $post_id > 0 )
//         $icon = TESTLEAFEXT_PLUGIN_URL . '/icons/gpx-file.svg';
//     return $icon;
// }, 10, 3 );
