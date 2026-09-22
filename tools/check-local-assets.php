<?php
/**
 * Verify Briite's required local font assets and reject remote font services.
 *
 * This script is development tooling and runs from Composer/CI, not WordPress.
 *
 * @package kriate
 */

$kriate_root   = dirname( __DIR__ );
$kriate_failed = false;

$kriate_required_fonts = array(
	'fonts/raleway-regular.woff',
	'fonts/raleway-regular.ttf',
	'fonts/raleway-semibold.woff',
	'fonts/raleway-semibold.ttf',
	'fonts/raleway-bold.woff',
	'fonts/raleway-bold.ttf',
);

foreach ( $kriate_required_fonts as $kriate_relative_path ) {
	$kriate_absolute_path = $kriate_root . DIRECTORY_SEPARATOR . str_replace( '/', DIRECTORY_SEPARATOR, $kriate_relative_path );

	if ( ! is_file( $kriate_absolute_path ) ) {
		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fwrite -- Development-only CLI output; WordPress is not bootstrapped.
		fwrite( STDERR, "Missing required Briite font asset: {$kriate_relative_path}\n" );
		$kriate_failed = true;
	}
}

$kriate_css_sources  = glob( $kriate_root . '/css/*.css' );
$kriate_sass_sources = glob( $kriate_root . '/sass/*.scss' );

if ( false === $kriate_css_sources ) {
	$kriate_css_sources = array();
}

if ( false === $kriate_sass_sources ) {
	$kriate_sass_sources = array();
}

$kriate_asset_sources = array_merge( $kriate_css_sources, $kriate_sass_sources );

foreach ( $kriate_asset_sources as $kriate_source_file ) {
	// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- Development-only local source read; WordPress is not bootstrapped.
	$kriate_source = file_get_contents( $kriate_source_file );

	if ( false === $kriate_source ) {
		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fwrite -- Development-only CLI output; WordPress is not bootstrapped.
		fwrite( STDERR, "Unable to read asset source: {$kriate_source_file}\n" );
		$kriate_failed = true;
		continue;
	}

	if ( preg_match( '~fonts\.(?:googleapis|gstatic)\.com~i', $kriate_source ) ) {
		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fwrite -- Development-only CLI output; WordPress is not bootstrapped.
		fwrite( STDERR, "Remote font service reference found: {$kriate_source_file}\n" );
		$kriate_failed = true;
	}
}

if ( $kriate_failed ) {
	exit( 1 );
}

// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fwrite -- Development-only CLI output; WordPress is not bootstrapped.
fwrite( STDOUT, "Briite local asset checks passed.\n" );
