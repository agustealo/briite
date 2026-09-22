<?php
/**
 * Kriate Theme Customizer integration.
 *
 * @package kriate
 */

/**
 * Add postMessage support for site title and description settings.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 * @return void
 */
function kriate_customize_register( $wp_customize ) {
	$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
	$wp_customize->get_setting( 'header_textcolor' )->transport = 'postMessage';
}
add_action( 'customize_register', 'kriate_customize_register' );

/**
 * Bind JavaScript handlers for asynchronous Customizer preview updates.
 *
 * @return void
 */
function kriate_customize_preview_js() {
	wp_enqueue_script( 'kriate_customizer', get_template_directory_uri() . '/js/customizer.js', array( 'customize-preview' ), '20130508', true );
}
add_action( 'customize_preview_init', 'kriate_customize_preview_js' );
