<?php
/**
 * Briite Theme Customizer compatibility callbacks.
 *
 * @package kriate
 */

/**
 * Historical Customizer callback retained for child-theme compatibility.
 *
 * Briite does not render the site-title, site-description, or header-text
 * targets that its legacy asynchronous preview code expected. WordPress core
 * therefore keeps ownership of those settings and their normal refresh
 * transport instead of Briite forcing a broken postMessage transport.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 * @return void
 */
function kriate_customize_register( $wp_customize ) {
	// Intentionally left empty for backwards compatibility with public callbacks.
}

/**
 * Historical Customizer preview callback retained for child-theme compatibility.
 *
 * The legacy preview script is intentionally not enqueued because its DOM
 * targets do not exist in Briite's header markup.
 *
 * @return void
 */
function kriate_customize_preview_js() {
	// Intentionally left empty for backwards compatibility with public callbacks.
}
