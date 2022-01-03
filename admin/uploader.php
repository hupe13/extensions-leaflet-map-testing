<?php

//https://wordpress.stackexchange.com/questions/396325/how-can-i-allow-upload-of-ttf-or-otf-font-files-when-hooking-upload-mimes-does
// angepasst
function leafext_correct_filetypes( $data, $file, $filename, $mimes, $real_mime ) {
	if ( ! empty( $data['ext'] ) && ! empty( $data['type'] ) ) {
		return $data;
	}
	$wp_file_type = wp_check_filetype( $filename, $mimes );

	// Check for the file type you want to enable, e.g. 'gpx'.
	if ( 'gpx' === $wp_file_type['ext'] ) {
		$data['ext'] = 'gpx';
		$data['type'] = 'application/gpx+xml';
	}
	if ( 'kml' === $wp_file_type['ext'] ) {
		$data['ext'] = 'kml';
		$data['type'] = 'application/vnd.google-earth.kml+xml';
	}
	return $data;
}
add_filter( 'wp_check_filetype_and_ext', 'leafext_correct_filetypes', 10, 5 );

//Erlaube Upload gpx usw.
function leafext_add_mimes( $mime_types ) {
  $mime_types['gpx'] = 'application/gpx+xml';
	$mime_types['kml'] = 'application/vnd.google-earth.kml+xml';
  return $mime_types;
}
add_filter( 'upload_mimes', 'leafext_add_mimes' );

//https://wordpress.stackexchange.com/questions/47415/change-upload-directory-for-pdf-files
// angepasst
function leafext_pre_upload($file){
    add_filter('upload_dir', 'leafext_custom_upload_dir');
    return $file;
}
add_filter('wp_handle_upload_prefilter', 'leafext_pre_upload');

function leafext_custom_upload_dir($path){
    $extension = substr(strrchr($_POST['name'],'.'),1);
    if(!empty($path['error']) ||
			( $extension != 'gpx' &&
			  $extension != 'kml' )
			) { return $path; } //error or other filetype; do nothing.

    $customdir = '/'.$extension;
    $path['path']    = str_replace($path['subdir'], '', $path['path']); //remove default subdir (year/month)
    $path['url']     = str_replace($path['subdir'], '', $path['url']);
    $path['subdir']  = $customdir;
    $path['path']   .= $customdir;
    $path['url']    .= $customdir;
    return $path;
}
function leafext_post_upload($fileinfo){
    remove_filter('upload_dir', 'leafext_custom_upload_dir');
    return $fileinfo;
}
add_filter('wp_handle_upload', 'leafext_post_upload');
