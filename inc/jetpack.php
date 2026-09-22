<?php
/**
 * Jetpack compatibility helpers.
 *
 * @package kriate
 */

/**
 * Add theme support for Jetpack Infinite Scroll.
 *
 * @return void
 */
function kriate_jetpack_setup() {
	add_theme_support(
		'infinite-scroll',
		array(
			'container' => 'main',
			'footer'    => 'page',
		)
	);
}
add_action( 'after_setup_theme', 'kriate_jetpack_setup' );
