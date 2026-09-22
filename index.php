<?php
/**
 * The main template file.
 *
 * This is the most generic template file in the WordPress template hierarchy.
 * It displays content when no more specific template matches the request.
 *
 * @package kriate
 */

get_header();

if ( have_posts() ) :
	while ( have_posts() ) :
		the_post();

		if ( is_home() ) {
			get_template_part( 'content', 'home' );
		} else {
			get_template_part( 'content', get_post_format() );
		}
	endwhile;
else :
	get_template_part( 'content', 'none' );
endif;

get_footer();
