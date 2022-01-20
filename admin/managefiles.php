<?php
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

function leafext_list_files() {

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

  $iterator = new RecursiveIteratorIterator(
      new RecursiveDirectoryIterator($upload_path)
  );
  $gpx_files = new RegexIterator($iterator, '/\.(gpx|kml)$/'); // Dateiendung ".gpx"

  date_default_timezone_set(wp_timezone_string());

  $gpx_table = array();
  $entry = array('<b>'.__('Date','extensions-leaflet-map').'</b>',
    '<b>'.__('Name','extensions-leaflet-map').'</b>',
    '<b>'.__('Edit','extensions-leaflet-map').'</b>',
    '<b>'.__('leaflet Shortcode','extensions-leaflet-map').'</b>',
    '<b>'.__('elevation Shortcode','extensions-leaflet-map').'</b>');
  $gpx_table[] = $entry;

  foreach ($gpx_files as $file) {
    if (!$file->isFile()) continue;
    $entry = array();
    $myfile = str_replace($upload_path,'',$file->getPathname());
    //echo $myfile;
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
      $entry['post_date'] = date('Y-m-d G:i:s', $file->getMTime());
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

  //https://davescripts.com/php-pagination-of-an-array-of-data
  // The page to display (Usually is received in a url parameter)
  $data = $gpx_table;
  $page = isset($_GET['p']) ? intval($_GET['p']) : 1;

  // The number of records to display per page
  $page_size = 25;

  // Calculate total number of records, and total number of pages
  $total_records = count($data);
  $total_pages   = ceil($total_records / $page_size);

  // Validation: Page to display can not be greater than the total number of pages
  if ($page > $total_pages) {
      $page = $total_pages;
  }

  // Validation: Page to display can not be less than 1
  if ($page < 1) {
      $page = 1;
  }

  // Calculate the position of the first record of the page to display
  $offset = ($page - 1) * $page_size;

  // Get the subset of records to be displayed from the array
  $data = array_slice($data, $offset, $page_size);

  $text = $text.leafext_html_table($data);

  // page links
  $N = min($total_pages, 9);
  $pages_links = array();
  $tmp = $N;
  if ($tmp < $page || $page > $N) {
      $tmp = 2;
  }
  for ($i = 1; $i <= $tmp; $i++) {
      $pages_links[$i] = $i;
  }
  if ($page > $N && $page <= ($total_pages - $N + 2)) {
      for ($i = $page - 3; $i <= $page + 3; $i++) {
          if ($i > 0 && $i < $total_pages) {
              $pages_links[$i] = $i;
          }
      }
  }
  $tmp = $total_pages - $N + 1;
  if ($tmp > $page - 2) {
      $tmp = $total_pages - 1;
  }
  for ($i = $tmp; $i <= $total_pages; $i++) {
      if ($i > 0) {
          $pages_links[$i] = $i;
      }
  }
  $prev = 0;

  $text = $text. '<div style="text-align: center;">';
  foreach ($pages_links as $p) {
      if (($p - $prev) > 1) {
        $text = $text. '<a href="#">...</a>';
      }
      $prev = $p;

      $style_active = '';
      if ($p == $page) {
          $style_active = 'style="font-weight:bold"';
      }
      $text = $text. '<a '. $style_active .' href="admin.php?page=extensions-leaflet-map-testing&tab=manage_files&p='.$p.'">'. $p.'</a> ';
  }
  $text = $text. '</div>';
  return $text;
}

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
echo leafext_list_files();
