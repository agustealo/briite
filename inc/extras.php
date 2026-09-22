<?php
/**
 * Custom functions that act independently of the theme templates.
 *
 * @package kriate
 */

/**
 * Get the wp_nav_menu() fallback, wp_page_menu(), to show a home link.
 *
 * @param array $args Configuration arguments.
 * @return array
 */
function kriate_page_menu_args( $args ) {
	$args['show_home'] = true;
	return $args;
}
add_filter( 'wp_page_menu_args', 'kriate_page_menu_args' );

/**
 * Add custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 * @return array
 */
function kriate_body_classes( $classes ) {
	if ( is_multi_author() ) {
		$classes[] = 'group-blog';
	}

	return $classes;
}
add_filter( 'body_class', 'kriate_body_classes' );

/**
 * Filter wp_title output for backwards compatibility with older integrations.
 *
 * Briite uses WordPress core title-tag support for the document title. This
 * filter remains available for plugins or child themes that still call
 * wp_title() directly.
 *
 * @param string $title Default title text for the current view.
 * @param string $sep   Separator.
 * @return string
 */
function kriate_wp_title( $title, $sep ) {
	if ( is_feed() ) {
		return $title;
	}

	global $page, $paged;

	$title .= get_bloginfo( 'name', 'display' );

	$kriate_site_description = get_bloginfo( 'description', 'display' );
	if ( $kriate_site_description && ( is_home() || is_front_page() ) ) {
		$title .= " $sep $kriate_site_description";
	}

	if ( ( $paged >= 2 || $page >= 2 ) && ! is_404() ) {
		/* translators: %s: current page number. */
		$title .= " $sep " . sprintf( __( 'Page %s', 'kriate' ), max( $paged, $page ) );
	}

	return $title;
}
add_filter( 'wp_title', 'kriate_wp_title', 10, 2 );

/**
 * Retain the historical author setup callback for child-theme compatibility.
 *
 * Modern WordPress core already prepares author archive data. The legacy
 * implementation manually overwrote the authordata global and is no longer
 * required.
 *
 * @return void
 */
function kriate_setup_author() {
	// Intentionally empty: WordPress core owns author archive state.
}
