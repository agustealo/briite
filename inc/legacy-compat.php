<?php
/**
 * Briite consumer-only legacy compatibility aliases.
 *
 * This file preserves historical public identifiers for existing child themes
 * and integrations installed from Briite's normal consumer package. The
 * WordPress.org release builder intentionally removes this file so the
 * directory artifact exposes only Briite-prefixed public identifiers.
 *
 * @package kriate
 */

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedFunctionFound -- Historical public aliases retained only in the consumer package.

if ( ! function_exists( 'complete_version_removal' ) ) {
	/**
	 * Historical generator callback retained as an inert compatibility shim.
	 *
	 * @param string $generator Generator output supplied by WordPress.
	 * @return string
	 */
	function complete_version_removal( $generator = '' ) {
		return $generator;
	}
}

if ( ! function_exists( 'smashing_jpeg_quality' ) ) {
	/**
	 * Historical JPEG quality callback retained for explicit downstream use.
	 *
	 * Briite no longer registers this callback globally.
	 *
	 * @param int    $quality   Image quality.
	 * @param string $mime_type Image MIME type.
	 * @return int
	 */
	function smashing_jpeg_quality( $quality, $mime_type = '' ) {
		if ( 'image/jpeg' === $mime_type || '' === $mime_type ) {
			return 100;
		}

		return $quality;
	}
}

if ( ! function_exists( 'fb_AddThumbValue' ) ) {
	/**
	 * Historical post-column callback retained for explicit downstream use.
	 *
	 * @param string $column_name Current column name.
	 * @param int    $post_id     Current post ID.
	 * @return void
	 */
	function fb_AddThumbValue( $column_name, $post_id ) {
		kriate_render_thumbnail_column( $column_name, $post_id );
	}
}

if ( ! function_exists( 'social_profile_fields' ) ) {
	/**
	 * Historical social-profile renderer retained as an inert compatibility shim.
	 *
	 * @param WP_User $user User being edited.
	 * @return void
	 */
	function social_profile_fields( $user ) {
		unset( $user );
	}
}

if ( ! function_exists( 'social_save_profile_fields' ) ) {
	/**
	 * Historical social-profile save callback retained as an inert compatibility shim.
	 *
	 * @param int $user_id User ID being updated.
	 * @return void
	 */
	function social_save_profile_fields( $user_id ) {
		unset( $user_id );
	}
}

// phpcs:enable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedFunctionFound

/**
 * Register historical image-size names for existing consumer integrations.
 *
 * The active Briite templates use prefixed image-size names. These registrations
 * remain in the consumer package so child themes requesting the historical
 * names continue to receive the same generated crops for newly uploaded media.
 *
 * @return void
 */
function kriate_register_legacy_image_sizes() {
	add_image_size( 'single-banner', 1300, 500, array( 'center', 'center' ) );
	add_image_size( 'grid-thumb', 450, 450, array( 'center', 'center' ) );
}
add_action( 'after_setup_theme', 'kriate_register_legacy_image_sizes', 11 );

/**
 * Register historical asset handles as aliases of Briite's prefixed handles.
 *
 * Alias handles have no source of their own, so they do not duplicate network
 * requests. Existing child themes can continue to declare dependencies on the
 * old handles, attach inline assets to them, or inspect their enqueue state.
 *
 * @return void
 */
function kriate_register_legacy_asset_handles() {
	$theme_version = wp_get_theme()->get( 'Version' );

	wp_register_style( 'wp-style', false, array( 'kriate-style' ), $theme_version );
	wp_register_style( 'theme-style', false, array( 'kriate-theme-style' ), $theme_version );
	wp_register_script( 'theme-js', false, array( 'kriate-theme-script' ), $theme_version, true );

	wp_enqueue_style( 'wp-style' );
	wp_enqueue_style( 'theme-style' );
	wp_enqueue_script( 'theme-js' );
}
add_action( 'wp_enqueue_scripts', 'kriate_register_legacy_asset_handles', 11 );
