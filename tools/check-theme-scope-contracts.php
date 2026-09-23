<?php
/**
 * Verify Briite's theme-scope and public-identifier boundaries.
 *
 * This script is development tooling and runs from Composer/CI, not WordPress.
 *
 * @package kriate
 */

$kriate_root   = dirname( __DIR__ );
$kriate_failed = false;

$kriate_contracts = array(
	'functions.php'          => array(
		'required'  => array(
			"add_image_size( 'kriate-single-banner'",
			"add_image_size( 'kriate-grid-thumb'",
			'function kriate_get_compatible_image_size',
			'function kriate_Thumbnail_Column',
			'function kriate_render_thumbnail_column',
			'function kriate_AddThumbValue',
			"wp_enqueue_style( 'kriate-style'",
			"wp_enqueue_style( 'kriate-theme-style'",
			"'kriate-theme-script'",
		),
		'forbidden' => array(
			'function complete_version_removal',
			'function smashing_jpeg_quality',
			'function fb_AddThumbValue',
			"add_image_size( 'single-banner'",
			"add_image_size( 'grid-thumb'",
			"wp_enqueue_style( 'wp-style'",
			"wp_enqueue_style( 'theme-style'",
			"wp_enqueue_script( 'theme-js'",
			"add_filter( 'the_generator', 'complete_version_removal' )",
			"remove_action( 'wp_head', 'wp_generator' )",
			"add_filter( 'manage_posts_columns', 'kriate_Thumbnail_Column' )",
			"add_action( 'manage_posts_custom_column', 'fb_AddThumbValue'",
			"add_filter( 'manage_pages_columns', 'kriate_Thumbnail_Column' )",
			"add_action( 'manage_pages_custom_column', 'kriate_AddThumbValue'",
		),
	),
	'inc/profile.php'        => array(
		'required'  => array(
			"__DIR__ . '/legacy-compat.php'",
			'is_readable( $kriate_legacy_compat_file )',
			'require_once $kriate_legacy_compat_file',
		),
		'forbidden' => array(
			'function social_profile_fields',
			'function social_save_profile_fields',
			"add_action( 'show_user_profile'",
			"add_action( 'edit_user_profile'",
			"add_action( 'personal_options_update'",
			"add_action( 'edit_user_profile_update'",
			'update_user_meta(',
			'delete_user_meta(',
			'wp_nonce_field(',
		),
	),
	'inc/legacy-compat.php'  => array(
		'required'  => array(
			'function complete_version_removal',
			'function smashing_jpeg_quality',
			'function fb_AddThumbValue',
			'function social_profile_fields',
			'function social_save_profile_fields',
			'function kriate_register_legacy_image_sizes',
			"add_image_size( 'single-banner'",
			"add_image_size( 'grid-thumb'",
			'function kriate_register_legacy_asset_handles',
			"wp_register_style( 'wp-style'",
			"wp_register_style( 'theme-style'",
			"wp_register_script( 'theme-js'",
		),
		'forbidden' => array(
			"add_filter( 'the_generator'",
			"add_filter( 'wp_editor_set_quality'",
			"add_filter( 'jpeg_quality'",
			"add_filter( 'manage_posts_columns'",
			"add_filter( 'manage_pages_columns'",
			"add_action( 'manage_posts_custom_column'",
			"add_action( 'manage_pages_custom_column'",
			"add_action( 'show_user_profile'",
			"add_action( 'edit_user_profile'",
			"add_action( 'personal_options_update'",
			"add_action( 'edit_user_profile_update'",
			'update_user_meta(',
			'delete_user_meta(',
		),
	),
	'inc/template-tags.php'  => array(
		'required'  => array(
			'function kriate_categorized_blog',
			'function kriate_category_transient_flusher',
		),
		'forbidden' => array(
			'get_transient(',
			'set_transient(',
			'delete_transient(',
			"add_action( 'edit_category', 'kriate_category_transient_flusher' )",
			"add_action( 'save_post', 'kriate_category_transient_flusher' )",
		),
	),
);

$kriate_sources = array();

foreach ( $kriate_contracts as $kriate_relative_path => $kriate_contract ) {
	$kriate_absolute_path = $kriate_root . DIRECTORY_SEPARATOR . $kriate_relative_path;

	if ( ! is_file( $kriate_absolute_path ) ) {
		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fwrite -- Development-only CLI output; WordPress is not bootstrapped.
		fwrite( STDERR, "Missing Briite theme-scope contract file: {$kriate_relative_path}\n" );
		$kriate_failed = true;
		continue;
	}

	// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- Development-only local source read; WordPress is not bootstrapped.
	$kriate_source = file_get_contents( $kriate_absolute_path );

	if ( false === $kriate_source ) {
		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fwrite -- Development-only CLI output; WordPress is not bootstrapped.
		fwrite( STDERR, "Unable to read Briite theme-scope contract file: {$kriate_relative_path}\n" );
		$kriate_failed = true;
		continue;
	}

	$kriate_sources[ $kriate_relative_path ] = $kriate_source;

	foreach ( $kriate_contract['required'] as $kriate_required_source ) {
		if ( false === strpos( $kriate_source, $kriate_required_source ) ) {
			// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fwrite -- Development-only CLI output; WordPress is not bootstrapped.
			fwrite( STDERR, "Missing Briite contract in {$kriate_relative_path}: {$kriate_required_source}\n" );
			$kriate_failed = true;
		}
	}

	foreach ( $kriate_contract['forbidden'] as $kriate_forbidden_source ) {
		if ( false !== strpos( $kriate_source, $kriate_forbidden_source ) ) {
			// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fwrite -- Development-only CLI output; WordPress is not bootstrapped.
			fwrite( STDERR, "Forbidden Briite contract found in {$kriate_relative_path}: {$kriate_forbidden_source}\n" );
			$kriate_failed = true;
		}
	}
}

$kriate_forbidden_runtime_calls = array(
	'add_role(',
	'add_shortcode(',
	'add_user_meta(',
	'delete_transient(',
	'delete_user_meta(',
	'register_block_type(',
	'register_post_type(',
	'register_taxonomy(',
	'remove_role(',
	'set_site_transient(',
	'set_transient(',
	'update_user_meta(',
	'wp_remote_get(',
	'wp_remote_post(',
	'wp_remote_request(',
	'wp_schedule_event(',
	'wp_schedule_single_event(',
);

$kriate_runtime_paths = array(
	'404.php',
	'archive.php',
	'category.php',
	'comments.php',
	'content-grid.php',
	'content-home.php',
	'content-none.php',
	'content-page.php',
	'content-search.php',
	'content.php',
	'footer.php',
	'functions.php',
	'header.php',
	'index.php',
	'page.php',
	'search.php',
	'sidebar.php',
	'single.php',
);

$kriate_inc_files = glob( $kriate_root . DIRECTORY_SEPARATOR . 'inc' . DIRECTORY_SEPARATOR . '*.php' );
if ( false !== $kriate_inc_files ) {
	foreach ( $kriate_inc_files as $kriate_inc_file ) {
		$kriate_runtime_paths[] = 'inc/' . basename( $kriate_inc_file );
	}
}

$kriate_legacy_function_patterns = array(
	'function complete_version_removal',
	'function smashing_jpeg_quality',
	'function fb_AddThumbValue',
	'function social_profile_fields',
	'function social_save_profile_fields',
);

foreach ( $kriate_runtime_paths as $kriate_relative_path ) {
	$kriate_absolute_path = $kriate_root . DIRECTORY_SEPARATOR . $kriate_relative_path;

	if ( ! is_file( $kriate_absolute_path ) ) {
		continue;
	}

	if ( isset( $kriate_sources[ $kriate_relative_path ] ) ) {
		$kriate_source = $kriate_sources[ $kriate_relative_path ];
	} else {
		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- Development-only local source read; WordPress is not bootstrapped.
		$kriate_source = file_get_contents( $kriate_absolute_path );
	}

	if ( false === $kriate_source ) {
		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fwrite -- Development-only CLI output; WordPress is not bootstrapped.
		fwrite( STDERR, "Unable to read Briite runtime file: {$kriate_relative_path}\n" );
		$kriate_failed = true;
		continue;
	}

	foreach ( $kriate_forbidden_runtime_calls as $kriate_forbidden_call ) {
		if ( false !== strpos( $kriate_source, $kriate_forbidden_call ) ) {
			// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fwrite -- Development-only CLI output; WordPress is not bootstrapped.
			fwrite( STDERR, "Plugin-territory runtime API found in {$kriate_relative_path}: {$kriate_forbidden_call}\n" );
			$kriate_failed = true;
		}
	}

	if ( 'inc/legacy-compat.php' !== $kriate_relative_path ) {
		foreach ( $kriate_legacy_function_patterns as $kriate_legacy_function_pattern ) {
			if ( false !== strpos( $kriate_source, $kriate_legacy_function_pattern ) ) {
				// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fwrite -- Development-only CLI output; WordPress is not bootstrapped.
				fwrite( STDERR, "Consumer-only legacy function escaped its compatibility file in {$kriate_relative_path}: {$kriate_legacy_function_pattern}\n" );
				$kriate_failed = true;
			}
		}
	}
}

if ( $kriate_failed ) {
	exit( 1 );
}

// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fwrite -- Development-only CLI output; WordPress is not bootstrapped.
fwrite( STDOUT, "Briite theme-scope and prefix contract checks passed.\n" );
