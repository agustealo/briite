<?php
/**
 * Custom functions that act independently of the theme templates.
 *
 * @package kriate
 */

/**
 * Configure the wp_page_menu() fallback to show a home link.
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
 * Filter legacy wp_title() output for backwards-compatible child templates.
 *
 * Briite itself uses WordPress title-tag support. This filter remains because
 * historical child themes may still call wp_title().
 *
 * @param string $title Default title text for current view.
 * @param string $sep   Optional separator.
 * @return string
 */
function kriate_wp_title( $title, $sep ) {
	if ( is_feed() ) {
		return $title;
	}

	global $page, $paged;

	$title .= get_bloginfo( 'name', 'display' );

	$site_description = get_bloginfo( 'description', 'display' );
	if ( $site_description && ( is_home() || is_front_page() ) ) {
		$title .= " $sep $site_description";
	}

	if ( ( $paged >= 2 || $page >= 2 ) && ! is_404() ) {
		$title .= ' ' . $sep . ' ' . sprintf(
			/* translators: %s: current page number. */
			__( 'Page %s', 'kriate' ),
			max( $paged, $page )
		);
	}

	return $title;
}
add_filter( 'wp_title', 'kriate_wp_title', 10, 2 );

/**
 * Preserve the historical author-data setup callback for child-theme compatibility.
 *
 * Modern WordPress already initializes this global for author archives, but the
 * named callback remains public in Briite's historical surface.
 *
 * @global WP_Query $wp_query WordPress query object.
 * @return void
 */
function kriate_setup_author() {
	global $wp_query;

	if ( $wp_query->is_author() && isset( $wp_query->post ) ) {
		// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- Historical compatibility callback mirrors the pre-2013 core workaround.
		$GLOBALS['authordata'] = get_userdata( $wp_query->post->post_author );
	}
}
add_action( 'wp', 'kriate_setup_author' );
