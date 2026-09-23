<?php
/**
 * Consumer-only legacy public callback aliases.
 *
 * These generic callback names came from historical WordPress snippets that
 * Briite bundled or exposed in older releases. They are retained in the normal
 * consumer package for downstream child-theme compatibility, but the
 * WordPress.org release profile removes this file because current Theme
 * Directory requirements require every theme-owned public function to use a
 * unique prefix of at least four characters.
 *
 * None of these callbacks is registered by Briite itself.
 *
 * @package kriate
 */

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedFunctionFound -- Consumer-only historical public aliases; excluded from the WordPress.org package.

if ( ! function_exists( 'complete_version_removal' ) ) {
	/**
	 * Preserve the historical generator callback without changing core output.
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
	 * Preserve the historical JPEG-quality callback for explicit downstream use.
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
	 * Preserve the historical post-column callback for explicit downstream use.
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
	 * Preserve the historical social-profile renderer as an inert alias.
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
	 * Preserve the historical social-profile save callback as an inert alias.
	 *
	 * @param int $user_id User ID being updated.
	 * @return void
	 */
	function social_save_profile_fields( $user_id ) {
		unset( $user_id );
	}
}

// phpcs:enable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedFunctionFound
