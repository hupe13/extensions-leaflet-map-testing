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

$upload_dir = wp_upload_dir();
$upload_path = $upload_dir['path'];
$upload_url = $upload_dir['url'];
echo '
<script>
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
$entry = array("<b>Date</b>","<b>Name</b>","<b>Edit</b>","<b>leaflet-Shortcode</b>","<b>elevation-Shortcode</b>");
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
echo leafext_html_table($gpx_table);
