<?php
/**
 * Verify Briite does not automatically own plugin-territory behavior.
 *
 * This script is development tooling and runs from Composer/CI, not WordPress.
 *
 * @package kriate
 */

$kriate_root   = dirname( __DIR__ );
$kriate_failed = false;

$kriate_contracts = array(
	'functions.php'   => array(
		'required'  => array(
			'function complete_version_removal',
			'return $generator;',
		),
		'forbidden' => array(
			"add_filter( 'the_generator', 'complete_version_removal' )",
			"remove_action( 'wp_head', 'wp_generator' )",
		),
	),
	'inc/profile.php' => array(
		'required'  => array(
			'function social_profile_fields',
			'function social_save_profile_fields',
		),
		'forbidden' => array(
			"add_action( 'show_user_profile'",
			"add_action( 'edit_user_profile'",
			"add_action( 'personal_options_update'",
			"add_action( 'edit_user_profile_update'",
			'update_user_meta(',
			'delete_user_meta(',
			'wp_nonce_field(',
		),
	),
);

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

	foreach ( $kriate_contract['required'] as $kriate_required_source ) {
		if ( false === strpos( $kriate_source, $kriate_required_source ) ) {
			// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fwrite -- Development-only CLI output; WordPress is not bootstrapped.
			fwrite( STDERR, "Missing Briite compatibility shim in {$kriate_relative_path}: {$kriate_required_source}\n" );
			$kriate_failed = true;
		}
	}

	foreach ( $kriate_contract['forbidden'] as $kriate_forbidden_source ) {
		if ( false !== strpos( $kriate_source, $kriate_forbidden_source ) ) {
			// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fwrite -- Development-only CLI output; WordPress is not bootstrapped.
			fwrite( STDERR, "Plugin-territory Briite behavior remains in {$kriate_relative_path}: {$kriate_forbidden_source}\n" );
			$kriate_failed = true;
		}
	}
}

if ( $kriate_failed ) {
	exit( 1 );
}

// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fwrite -- Development-only CLI output; WordPress is not bootstrapped.
fwrite( STDOUT, "Briite theme-scope contract checks passed.\n" );
