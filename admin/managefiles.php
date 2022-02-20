<?php
//$TB_iframe = isset($_GET['TB_iframe']) ? $_GET['TB_iframe'] : "";
//var_dump($TB_iframe);
//var_dump($_SERVER);
$track = isset($_GET['track']) ? $_GET['track'] : "";
//var_dump($track);
if ( $track == "") {
	leafext_managefiles();
} else {
	include TESTLEAFEXT_PLUGIN_DIR . '/admin/thickbox.php';
	leafext_thickbox($track);
}

function leafext_all_subdirs() {
	$upload_dir = wp_get_upload_dir();
	$upload_path = $upload_dir['path'];

	$iterator = new RecursiveIteratorIterator(
		new RecursiveDirectoryIterator($upload_path)
	);
	$track_files = new RegexIterator($iterator, '/\.(gpx|kml)$/'); // Dateiendung ".gpx" und ".kml"
	//var_dump($track_files);
	$gpx_dirs = array();
	foreach ($track_files as $file) {
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

function leafext_managefiles() {
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
	echo "<h3>To Do</h3>
	<ul><li>
	Query allow gpx and kml (and maybe other) upload to media library?
	<li>Target directory is upload_dir/gpx/ or upload_dir/kml/ currently.
	<li>Okay or not okay? Or should they custom paths?
	<li>avoid copy popups";

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
}

function leafext_list_files($dir) {
	//https://codex.wordpress.org/Javascript_Reference/ThickBox
	add_thickbox();
	//
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

	$track_files = glob($upload_path.'/'.$dir.'/*.{gpx,kml}', GLOB_BRACE);

	//var_dump($track_files);
  date_default_timezone_set(wp_timezone_string());

  $track_table = array();
  $entry = array('<b>'.__('Date','extensions-leaflet-map').'</b>',
    '<b>'.__('Name','extensions-leaflet-map').'</b>',
		'<b>'.__('View','extensions-leaflet-map').'</b>',
    '<b>'.__('Edit','extensions-leaflet-map').'</b>',
    '<b>'.__('leaflet Shortcode','extensions-leaflet-map').'</b>',
    '<b>'.__('elevation Shortcode','extensions-leaflet-map').'</b>');
  $track_table[] = $entry;

  foreach ($track_files as $file) {
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
				$entry['view'] = '';
        $entry['edit'] = '<a href ="'.get_admin_url().'post.php?post='.$key -> ID.'&action=edit">'.__('Edit').'</a>';
      }
    } else {
      //$entry['post_date'] = date('Y-m-d G:i:s', $file->getMTime());
			$entry['post_date'] = date('Y-m-d G:i:s', filemtime($file));
      $entry['post_title'] = $myfile;
			$entry['view'] = '<a href="https://leafext.de/dev/wp-admin/admin.php?page=extensions-leaflet-map-testing&tab=manage_files&track='
					.$myfile.'&TB_iframe=true" class="thickbox">Track</a>'; //&width=600&height=550
			$entry['edit'] = "";
    }

    $path_parts = pathinfo($myfile);
    $entry['leaflet'] = '<a href="#" onclick="createShortcode('.
        "'leaflet-".$path_parts['extension']."  src='".','.
        "'".$myfile."'".')">leaflet-'.$path_parts['extension'].'</a>';
    $entry['elevation'] = '<a href="#" onclick="createShortcode('.
        "'elevation gpx='".','.
        "'".$myfile."'".')">elevation</a>';
    $track_table[] = $entry;
  }

  $text = $text.leafext_html_table($track_table);
  return $text;
}

//Bug https://core.trac.wordpress.org/ticket/36418
// add_filter( 'wp_mime_type_icon', function( $icon, $mime, $post_id )
// {
//     if( 'application/gpx+xml' === $mime && $post_id > 0 )
//         $icon = TESTLEAFEXT_PLUGIN_URL . '/icons/gpx-file.svg';
//     return $icon;
// }, 10, 3 );
