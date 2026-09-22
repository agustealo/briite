<?php
/**
 * Verify Briite's required local font assets and reject remote font services.
 *
 * This script is development tooling and runs from Composer/CI, not WordPress.
 *
 * @package kriate
 */

$kriate_root = dirname( __DIR__ );
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
		fwrite( STDERR, "Missing required Briite font asset: {$kriate_relative_path}\n" );
		$kriate_failed = true;
	}
}

$kriate_asset_sources = array_merge(
	glob( $kriate_root . '/css/*.css' ) ?: array(),
	glob( $kriate_root . '/sass/*.scss' ) ?: array()
);

foreach ( $kriate_asset_sources as $kriate_source_file ) {
	// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_get_contents -- Development-only CLI guard, WordPress is not bootstrapped.
	$kriate_source = file_get_contents( $kriate_source_file );

	if ( false === $kriate_source ) {
		fwrite( STDERR, "Unable to read asset source: {$kriate_source_file}\n" );
		$kriate_failed = true;
		continue;
	}

	if ( preg_match( '~fonts\.(?:googleapis|gstatic)\.com~i', $kriate_source ) ) {
		fwrite( STDERR, "Remote font service reference found: {$kriate_source_file}\n" );
		$kriate_failed = true;
	}
}

if ( $kriate_failed ) {
	exit( 1 );
}

fwrite( STDOUT, "Briite local asset checks passed.\n" );
