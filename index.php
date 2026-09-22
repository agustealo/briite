<?php
/**
 * The main template file.
 *
 * Displays the blog index or the most appropriate content template when no
 * more-specific template matches the current request.
 *
 * @package kriate
 */

get_header();

if ( have_posts() ) {
	while ( have_posts() ) {
		the_post();

		if ( is_home() ) {
			get_template_part( 'content', 'home' );
		} else {
			get_template_part( 'content', get_post_format() );
		}
	}
} else {
	get_template_part( 'content', 'none' );
}

get_footer();
