<?php
/**
 * Jetpack compatibility support.
 *
 * @package kriate
 */

/**
 * Add theme support for Infinite Scroll.
 *
 * Briite's post stream lives inside #content. The theme does not expose a
 * separate page-width footer element for Jetpack's sliding footer, so that
 * optional footer is disabled rather than pointed at a nonexistent element.
 *
 * @return void
 */
function kriate_jetpack_setup() {
	add_theme_support(
		'infinite-scroll',
		array(
			'container' => 'content',
			'footer'    => false,
		)
	);
}
add_action( 'after_setup_theme', 'kriate_jetpack_setup' );
