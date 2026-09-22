<?php
/**
 * Verify Briite's required local front-end assets and reject retired remote hosts.
 *
 * This script is development tooling and runs from Composer/CI, not WordPress.
 *
 * @package kriate
 */

$kriate_root   = dirname( __DIR__ );
$kriate_failed = false;

$kriate_required_assets = array(
	'css/bootstrap-3.3.7.min.css',
	'fonts/glyphicons-halflings-regular.eot',
	'fonts/glyphicons-halflings-regular.svg',
	'fonts/glyphicons-halflings-regular.ttf',
	'fonts/glyphicons-halflings-regular.woff',
	'fonts/glyphicons-halflings-regular.woff2',
	'fonts/raleway-regular.woff',
	'fonts/raleway-regular.ttf',
	'fonts/raleway-semibold.woff',
	'fonts/raleway-semibold.ttf',
	'fonts/raleway-bold.woff',
	'fonts/raleway-bold.ttf',
);

foreach ( $kriate_required_assets as $kriate_relative_path ) {
	$kriate_absolute_path = $kriate_root . DIRECTORY_SEPARATOR . str_replace( '/', DIRECTORY_SEPARATOR, $kriate_relative_path );

	if ( ! is_file( $kriate_absolute_path ) ) {
		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fwrite -- Development-only CLI output; WordPress is not bootstrapped.
		fwrite( STDERR, "Missing required Briite asset: {$kriate_relative_path}\n" );
		$kriate_failed = true;
	}
}

$kriate_expected_git_blobs = array(
	'css/bootstrap-3.3.7.min.css'              => 'ed3905e0e0c91d4ed7d8aa14412dffeb038745ff',
	'fonts/glyphicons-halflings-regular.eot'   => 'b93a4953fff68df523aa7656497ee339d6026d64',
	'fonts/glyphicons-halflings-regular.svg'   => '94fb5490a2ed10b2c69a4a567a4fd2e4f706d841',
	'fonts/glyphicons-halflings-regular.ttf'   => '1413fc609ab6f21774de0cb7e01360095584f65b',
	'fonts/glyphicons-halflings-regular.woff'  => '9e612858f802245ddcbf59788a0db942224bab35',
	'fonts/glyphicons-halflings-regular.woff2' => '64539b54c3751a6d9adb44c8e3a45ba5a73b77f0',
);

foreach ( $kriate_expected_git_blobs as $kriate_relative_path => $kriate_expected_blob ) {
	$kriate_absolute_path = $kriate_root . DIRECTORY_SEPARATOR . str_replace( '/', DIRECTORY_SEPARATOR, $kriate_relative_path );

	if ( ! is_file( $kriate_absolute_path ) ) {
		continue;
	}

	// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- Development-only local source read; WordPress is not bootstrapped.
	$kriate_source = file_get_contents( $kriate_absolute_path );

	if ( false === $kriate_source ) {
		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fwrite -- Development-only CLI output; WordPress is not bootstrapped.
		fwrite( STDERR, "Unable to read required asset: {$kriate_relative_path}\n" );
		$kriate_failed = true;
		continue;
	}

	$kriate_actual_blob = sha1( 'blob ' . strlen( $kriate_source ) . "\0" . $kriate_source );

	if ( $kriate_expected_blob !== $kriate_actual_blob ) {
		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fwrite -- Development-only CLI output; WordPress is not bootstrapped.
		fwrite( STDERR, "Asset integrity mismatch: {$kriate_relative_path}\n" );
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

$kriate_asset_sources   = array_merge( $kriate_css_sources, $kriate_sass_sources );
$kriate_asset_sources[] = $kriate_root . '/functions.php';

$kriate_forbidden_hosts = array(
	'fonts.googleapis.com',
	'fonts.gstatic.com',
	'maxcdn.bootstrapcdn.com',
	'netdna.bootstrapcdn.com',
	'stackpath.bootstrapcdn.com',
);

foreach ( $kriate_asset_sources as $kriate_source_file ) {
	// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- Development-only local source read; WordPress is not bootstrapped.
	$kriate_source = file_get_contents( $kriate_source_file );

	if ( false === $kriate_source ) {
		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fwrite -- Development-only CLI output; WordPress is not bootstrapped.
		fwrite( STDERR, "Unable to read asset source: {$kriate_source_file}\n" );
		$kriate_failed = true;
		continue;
	}

	foreach ( $kriate_forbidden_hosts as $kriate_forbidden_host ) {
		if ( false !== stripos( $kriate_source, $kriate_forbidden_host ) ) {
			// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fwrite -- Development-only CLI output; WordPress is not bootstrapped.
			fwrite( STDERR, "Retired remote asset host found in {$kriate_source_file}: {$kriate_forbidden_host}\n" );
			$kriate_failed = true;
		}
	}
}

if ( $kriate_failed ) {
	exit( 1 );
}

// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fwrite -- Development-only CLI output; WordPress is not bootstrapped.
fwrite( STDOUT, "Briite local asset checks passed.\n" );
