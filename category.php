<?php
/**
 * The template for displaying category archives.
 *
 * @package kriate
 */

get_header();

if ( have_posts() ) {
	while ( have_posts() ) {
		the_post();
		get_template_part( 'content', 'grid' );
	}

	kriate_paging_nav();
}

get_footer();
