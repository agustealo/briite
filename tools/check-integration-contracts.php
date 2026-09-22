<?php
/**
 * Verify Briite's external-integration contracts against its own markup.
 *
 * This script is development tooling and runs from Composer/CI, not WordPress.
 *
 * @package kriate
 */

$kriate_root   = dirname( __DIR__ );
$kriate_failed = false;

$kriate_files = array(
	'header.php'           => $kriate_root . '/header.php',
	'inc/custom-header.php' => $kriate_root . '/inc/custom-header.php',
	'inc/customizer.php'   => $kriate_root . '/inc/customizer.php',
	'inc/jetpack.php'      => $kriate_root . '/inc/jetpack.php',
);

$kriate_sources = array();

foreach ( $kriate_files as $kriate_relative_path => $kriate_absolute_path ) {
	if ( ! is_file( $kriate_absolute_path ) ) {
		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fwrite -- Development-only CLI output; WordPress is not bootstrapped.
		fwrite( STDERR, "Missing Briite integration contract file: {$kriate_relative_path}\n" );
		$kriate_failed = true;
		continue;
	}

	// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- Development-only local source read; WordPress is not bootstrapped.
	$kriate_source = file_get_contents( $kriate_absolute_path );

	if ( false === $kriate_source ) {
		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fwrite -- Development-only CLI output; WordPress is not bootstrapped.
		fwrite( STDERR, "Unable to read Briite integration contract file: {$kriate_relative_path}\n" );
		$kriate_failed = true;
		continue;
	}

	$kriate_sources[ $kriate_relative_path ] = $kriate_source;
}

if ( isset( $kriate_sources['header.php'] ) && false === strpos( $kriate_sources['header.php'], 'id="content"' ) ) {
	// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fwrite -- Development-only CLI output; WordPress is not bootstrapped.
	fwrite( STDERR, "Briite's Infinite Scroll container markup is missing #content.\n" );
	$kriate_failed = true;
}

if ( isset( $kriate_sources['inc/custom-header.php'] ) ) {
	$kriate_custom_header_source = $kriate_sources['inc/custom-header.php'];

	if ( false === strpos( $kriate_custom_header_source, "'header-text'        => false" ) ) {
		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fwrite -- Development-only CLI output; WordPress is not bootstrapped.
		fwrite( STDERR, "Briite custom-header support must disable unused header-text controls.\n" );
		$kriate_failed = true;
	}

	if ( false !== strpos( $kriate_custom_header_source, "'header-text'        => true" ) ) {
		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fwrite -- Development-only CLI output; WordPress is not bootstrapped.
		fwrite( STDERR, "Retired Briite custom-header text support remains enabled.\n" );
		$kriate_failed = true;
	}
}

if ( isset( $kriate_sources['inc/customizer.php'] ) ) {
	$kriate_customizer_source = $kriate_sources['inc/customizer.php'];

	$kriate_required_callbacks = array(
		'function kriate_customize_register',
		'function kriate_customize_preview_js',
	);

	foreach ( $kriate_required_callbacks as $kriate_required_callback ) {
		if ( false === strpos( $kriate_customizer_source, $kriate_required_callback ) ) {
			// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fwrite -- Development-only CLI output; WordPress is not bootstrapped.
			fwrite( STDERR, "Missing Briite Customizer compatibility callback: {$kriate_required_callback}\n" );
			$kriate_failed = true;
		}
	}

	$kriate_retired_customizer_patterns = array(
		"->transport         = 'postMessage'",
		"->transport  = 'postMessage'",
		"->transport = 'postMessage'",
		"add_action( 'customize_preview_init'",
		"wp_enqueue_script( 'kriate_customizer'",
	);

	foreach ( $kriate_retired_customizer_patterns as $kriate_retired_customizer_pattern ) {
		if ( false !== strpos( $kriate_customizer_source, $kriate_retired_customizer_pattern ) ) {
			// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fwrite -- Development-only CLI output; WordPress is not bootstrapped.
			fwrite( STDERR, "Retired Briite Customizer preview wiring remains: {$kriate_retired_customizer_pattern}\n" );
			$kriate_failed = true;
		}
	}
}

if ( isset( $kriate_sources['inc/jetpack.php'] ) ) {
	$kriate_jetpack_source = $kriate_sources['inc/jetpack.php'];

	$kriate_required_settings = array(
		"'container' => 'content'",
		"'footer'    => false",
	);

	foreach ( $kriate_required_settings as $kriate_required_setting ) {
		if ( false === strpos( $kriate_jetpack_source, $kriate_required_setting ) ) {
			// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fwrite -- Development-only CLI output; WordPress is not bootstrapped.
			fwrite( STDERR, "Missing Briite Jetpack Infinite Scroll setting: {$kriate_required_setting}\n" );
			$kriate_failed = true;
		}
	}

	$kriate_retired_settings = array(
		"'container' => 'main'",
		"'footer'    => 'page'",
	);

	foreach ( $kriate_retired_settings as $kriate_retired_setting ) {
		if ( false !== strpos( $kriate_jetpack_source, $kriate_retired_setting ) ) {
			// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fwrite -- Development-only CLI output; WordPress is not bootstrapped.
			fwrite( STDERR, "Retired Briite Jetpack Infinite Scroll setting remains: {$kriate_retired_setting}\n" );
			$kriate_failed = true;
		}
}

if ( $kriate_failed ) {
	exit( 1 );
}

// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fwrite -- Development-only CLI output; WordPress is not bootstrapped.
fwrite( STDOUT, "Briite integration contract checks passed.\n" );
