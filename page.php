<?php
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other pages on the site may use a different template.
 *
 * @package kriate
 */

get_header();

while ( have_posts() ) :
	the_post();
	get_template_part( 'content', 'page' );
endwhile;

get_footer();
