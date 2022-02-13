<?php
echo '<h3>Manage Files</h3>';
echo
sprintf(__('Here you can see all gpx and kml files in the uploads directory.
  You can manage these
  %s with any (S)FTP-Client,
  %s with any File Manager plugin,
  %s with any plugin for importing uploaded files to the media library, e.g.','extensions-leaflet-map'),
  '<ul><li> - ','</li><li> - ','</li><li> - ','</li><li> - ').
  ' <a href="https://wordpress.org/plugins/bulk-media-register/">Bulk Media Register</a>,</li><li> - '.
  __('or direct in the media library - then here appears an edit link.','extensions-leaflet-map').
  '</li></ul>';
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

$dir=get_phwquery("dir");
leafext_file_form($dir);
if ( $dir != "" ) {
  echo '<h1>Directory '.$dir.'</h1>';
  echo '<p><code>[leaflet-dir src="'.$dir.'" ]</code>';

  echo '<a href="#" onclick="createShortcode('.
      "'leaflet-dir  src='".','.
      "'".$dir."'".')">Copy</a></p>';

	echo leafext_list_files($dir);
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
  </script>';

  // $iterator = new RecursiveIteratorIterator(
  //     new RecursiveDirectoryIterator($upload_path.'/'.$dir)
  // );
  // $gpx_files = new RegexIterator($iterator, '/\.(gpx|kml)$/'); // Dateiendung ".gpx"

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
