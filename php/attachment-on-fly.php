<?php
//https://geek.hellyer.kiwi/2018/08/02/creating-fake-wordpress-pages/
//changed hupe13

add_filter( 'the_posts', 'leafext_generate_fake_page', -10 );
/**
 * Create a fake page called "fake"
 *
 * $fake_slug can be modified to match whatever string is required
 *
 *
 * @param   object  $posts  Original posts object
 * @global  object  $wp     The main WordPress object
 * @global  object  $wp     The main WordPress query object
 * @return  object  $posts  Modified posts object
 */
function leafext_generate_fake_page( $posts ) {
	global $wp, $wp_query;

	$url_slug = 'leafext-attachment'; // URL slug of the fake page

	if ( ! defined( 'FAKE_PAGE' ) && ( strtolower( $wp->request ) == $url_slug ) ) {

		// stop interferring with other $posts arrays on this page (only works if the sidebar is rendered *after* the main page)
		define( 'FAKE_PAGE', true );

    $track = isset($_GET['track']) ? $_GET['track'] : "";
    //if ( $track == "" ) return $posts;

    $upload_dir = wp_get_upload_dir();
    $upload_url = $upload_dir['url'];

		// create a fake virtual page
		$post = new stdClass;
		$post->post_author    = 1;
		$post->post_name      = $url_slug;
		$post->guid           = home_url() . '/' . $url_slug;
		$post->post_title     = $track;
		$post->post_content   = '[leaflet-map fitbounds][leaflet-gpx src="'. $upload_url . $track . '"]';
		$post->ID             = -999;
		$post->post_type      = 'post';
		$post->post_status    = 'static';
		$post->comment_status = 'closed';
		$post->ping_status    = 'open';
		$post->comment_count  = 0;
		$post->post_date      = current_time( 'mysql' );
		$post->post_date_gmt  = current_time( 'mysql', 1 );
		$posts                = NULL;
		$posts[]              = $post;

		// make wpQuery believe this is a real page too
		$wp_query->is_page             = true;
		$wp_query->is_singular         = true;
		$wp_query->is_home             = false;
		$wp_query->is_archive          = false;
		$wp_query->is_category         = false;
		unset( $wp_query->query[ 'error' ] );
		$wp_query->query_vars[ 'error' ] = '';
		$wp_query->is_404 = false;
	}

	return $posts;
}
