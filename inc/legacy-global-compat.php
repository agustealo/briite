<?php
/**
 * Historical Briite global callback compatibility.
 *
 * These callbacks were part of Briite's public runtime from the original
 * 2014/2017 releases. They remain available in the normal consumer package so
 * existing child themes and integrations can continue to reference them.
 *
 * The WordPress.org release profile removes this consumer-compatibility module
 * because current Theme Review requirements require theme-defined public PHP
 * symbols to use Briite's unique namespace.
 *
 * None of these callbacks are registered by Briite itself anymore.
 *
 * @package kriate
 */

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedFunctionFound -- Historical consumer compatibility symbols are intentionally isolated from canonical Briite runtime code.

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
	 * Historical JPEG quality callback retained for child-theme compatibility.
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
	 * Historical post-column callback retained for compatibility.
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
