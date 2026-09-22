<?php
/**
 * Custom Header support for Briite.
 *
 * @package kriate
 */

/**
 * Register the WordPress core custom-header feature.
 *
 * The dimensions and default image are unchanged from the historical theme so
 * existing installations retain the same logo/header behavior.
 *
 * @return void
 */
function kriate_custom_header_setup() {
	add_theme_support(
		'custom-header',
		apply_filters(
			'kriate_custom_header_args',
			array(
				'random-default'     => false,
				'width'              => 162,
				'height'             => 21,
				'flex-height'        => false,
				'flex-width'         => false,
				'default-image'      => get_template_directory_uri() . '/images/logo.png',
				'default-text-color' => '000',
				'header-text'        => true,
				'uploads'            => true,
			)
		)
	);
}
add_action( 'after_setup_theme', 'kriate_custom_header_setup' );
