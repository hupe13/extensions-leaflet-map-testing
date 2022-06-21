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

//http://www.ashleysheridan.co.uk/blog/Speed+Testing+the+SPL+Iterators+for+Fetching+Files
function leafext_list_allfiles($dir,$pattern) {
	$upload_dir = wp_get_upload_dir();
	$upload_path = $upload_dir['path'];
	$files = array();
	$fh = opendir($dir);
	while (($file = readdir($fh)) !== false) {
		if ($file == '.' || $file == '..') continue;
		$filepath = $dir . '/' . $file;
		if (is_dir($filepath) ) {
			$files = array_merge($files, leafext_list_allfiles($filepath, $pattern) );
		} else {
			//if (preg_match($pattern, $file) ) array_push($files, str_replace($upload_path,'',$filepath));
			if (preg_match($pattern, $file) ) array_push($files, $filepath);
		}
	}
	closedir($fh);
	return $files;
}

function leafext_list_dirs($base_dir) {
	$upload_dir = wp_get_upload_dir();
	$upload_path = $upload_dir['path'];
	$directories = array();
	foreach(scandir($base_dir) as $file) {
		if($file == '.' || $file == '..') continue;
		$dir = $base_dir.'/'.$file;
		if(is_dir($dir)) {
			if (count(glob($dir.'/*.{gpx,kml}', GLOB_BRACE)) > 0 ) {
				$directories [] = str_replace($upload_path,'',$dir);
			}
			$directories = array_merge($directories, leafext_list_dirs($dir));
		}
	}
	return $directories;
}

function leafext_file_form($verz) {
	echo '<form action="'.admin_url( 'admin.php' ).'" method="get">';
	echo '<input type="hidden" id="page" name="page" value="'.TESTLEAFEXT_PLUGIN_SETTINGS.'">';
	echo '<input type="hidden" id="tab" name="tab" value="manage_files">';
	echo '<select name="dir">';
	if ($verz == "" ) echo '<option selected  value="">  </option>';

	$upload_dir = wp_get_upload_dir();
	$upload_path = $upload_dir['path'];
	//foreach (leafext_ele_subdirs() as $dir) {
	//foreach (leafext_list_allfiles( $upload_path, '/\.gpx/' ) as $dir) {
	foreach (leafext_list_dirs($upload_path) as $dir) {
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

function leafext_managefiles_help() {
  echo sprintf(__('Here you can see all gpx and kml files in subdirectories of uploads directory.
  You can manage these
  %s with any (S)FTP-Client,
  %s with any File Manager plugin,
  %s with any plugin for importing uploaded files to the Media Library,','extensions-leaflet-map'),
  '<ul><li> - ','</li><li> - ','</li><li> - ','</li><li> - ').
  '</li><li> - '.
  __('or direct in the Media Library.','extensions-leaflet-map').
  '</li>
  <li> - If they are in the Media Library then here appears an edit link. </li>
  </ul>';
	echo "<h3>To Do</h3>
	<ul>
	<li> Query allow gpx and kml (and maybe other) upload to Media Library?
	<li> Permissions (only Media Library)
	<li>
  </ul>";
}

function leafext_managefiles() {
  echo '<h2>Manage Files</h2>';
  leafext_managefiles_help();
  echo '<h2>Listing ...</h2>';
  $dir = isset($_GET["dir"]) ? $_GET["dir"] : "";
	$all = isset($_GET["all"]) ? $_GET["all"] : "";
  leafext_file_form($dir);

	echo '<h2>All Files</h2>';
	echo '
	<form action="'.admin_url( 'admin.php' ).'">
	<input type="hidden" name="page" value="'.TESTLEAFEXT_PLUGIN_SETTINGS.'">
	<input type="hidden" name="tab" value="manage_files">
	<input type="number" min="10" name="all" value="10" size="4">
	<input type="submit" value="Submit">
	</form>';

  if ( $dir != "" || $all != "" ) {
		leafext_admin_css();
		leafext_admin_js();
	}
	if ( $dir != "" ) {
    echo '<h3>Directory '.$dir.'</h3>';
		echo '<div>Shortcode for showing all files of this directory on a map:
			<span class="leafexttooltip" href="#" onclick="leafext_createShortcode('.
			"'leaflet-dir  src='".','.
			"'',".
			"'".$dir."'".')"
			onmouseout="leafext_outFunc()">
			<span class="leafextcopy" id="leafextTooltip">Copy to clipboard</span>
			<code>[leaflet-dir src="'.$dir.'"]</code>
			</span></div>';
		echo '<p>';
    echo leafext_list_files($dir);
		echo '</p>';
  } else if ($all != "") {
		echo '<p>';
		$upload_dir = wp_get_upload_dir();
		$upload_path = $upload_dir['path'];
		$files = leafext_list_allfiles($upload_path.'/','/\.(?:gpx|kml)(?:\?\S+)?$/i');
		$pageurl = admin_url( 'admin.php' ).'?page='.TESTLEAFEXT_PLUGIN_SETTINGS.'&tab=manage_files&all='.$all.'&nr=%_%';
		$pages = intdiv(count($files), $all) + 1;
		$pagenr = max(1,isset($_GET["nr"]) ? $_GET["nr"] : "1");
		echo paginate_links( array(
        'base'               => $pageurl, // http://example.com/all_posts.php%_% : %_% is replaced by format (below).
        'format'             => '%#%', // ?page=%#% : %#% is replaced by the page number.
        'total'              => $pages,
        'current'            => $pagenr,
        'aria_current'       => 'page',
        'show_all'           => false,
        'prev_next'          => true,
        'prev_text'          => __( '&laquo; Previous' ),
        'next_text'          => __( 'Next &raquo;' ),
        'end_size'           => 1,
        'mid_size'           => 2,
        'type'               => 'plain',
        'add_args'           => array(), // Array of query args to add.
        'add_fragment'       => '',
        'before_page_number' => '',
        'after_page_number'  => '',
    ));
		$pagefiles = array_chunk($files, $all);
		echo leafext_files_table($pagefiles[$pagenr - 1]);
		echo '</p>';
	}
}

function leafext_list_files($dir) {
	//https://codex.wordpress.org/Javascript_Reference/ThickBox
	add_thickbox();
	//
  $upload_dir = wp_get_upload_dir();
  $upload_path = $upload_dir['path'];
  $upload_url = $upload_dir['url'];

	$track_files = glob($upload_path.'/'.$dir.'/*.{gpx,kml}', GLOB_BRACE);
	return leafext_files_table($track_files);
}

function leafext_files_table($track_files) {
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
		$upload_dir = wp_get_upload_dir();
	  $upload_path = $upload_dir['path'];
	  $upload_url = $upload_dir['url'];
    $entry = array();
		$myfile = str_replace($upload_path.'/','',$file);
    global $wpdb;
    $sql = "SELECT post_id FROM $wpdb->postmeta WHERE meta_value LIKE '".substr($myfile, 1)."'";
    $results = $wpdb->get_results($sql);
    if (count($results) > 0 ) {
      foreach ($results as $result) {
        $key = get_post(get_object_vars($result)["post_id"]);
        $entry['post_date'] = $key -> post_date;
        $entry['post_title'] = $key -> post_title;
				$entry['view'] = '';
        $entry['edit'] = '<a href ="'.get_admin_url().'post.php?post='.$key -> ID.'&action=edit">'.__('Edit').'</a>';
      }
    } else {
			$entry['post_date'] = date('Y-m-d G:i:s', filemtime($file));
      $entry['post_title'] = $myfile;
			$entry['view'] = '<a href="'. esc_url( get_admin_url(null, 'admin.php?page='.TESTLEAFEXT_PLUGIN_SETTINGS) ) .'&tab=manage_files&track='
					.$myfile.'&TB_iframe=true" class="thickbox">Track</a>'; //&width=600&height=550
			$entry['edit'] = "";
    }

    $path_parts = pathinfo($myfile);

		$entry['leaflet'] = '<span class="leafexttooltip" href="#" onclick="leafext_createShortcode('.
        "'leaflet-".$path_parts['extension']." src='".','.
				"'".$upload_url."',".
        "'".$myfile."'".')"
				onmouseout="leafext_outFunc()">
				<span class="leafextcopy" id="leafextTooltip">Copy to clipboard</span>
				<code>[leaflet-'.$path_parts['extension'].' src="..."]</code></span>';

		$entry['elevation'] = '<span class="leafexttooltip" href="#" onclick="leafext_createShortcode('.
			"'elevation gpx='".','.
			"'".$upload_url."',".
			"'".$myfile."'".')"
			onmouseout="leafext_outFunc()">
			<span class="leafextcopy" id="leafextTooltip">Copy to clipboard</span>
			<code>[elevation gpx="..."]</code></span>';

    $track_table[] = $entry;
  }

  $text = leafext_html_table($track_table);
  return $text;
}

//Bug https://core.trac.wordpress.org/ticket/36418
// add_filter( 'wp_mime_type_icon', function( $icon, $mime, $post_id )
// {
//     if( 'application/gpx+xml' === $mime && $post_id > 0 )
//         $icon = TESTLEAFEXT_PLUGIN_URL . '/icons/gpx-file.svg';
//     return $icon;
// }, 10, 3 );
