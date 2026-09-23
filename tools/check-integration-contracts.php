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
	'functions.php',
	'header.php',
	'css/compat.css',
	'inc/custom-header.php',
	'inc/customizer.php',
	'inc/jetpack.php',
	'inc/legacy-compat.php',
);

$kriate_sources = array();

foreach ( $kriate_files as $kriate_relative_path ) {
	$kriate_absolute_path = $kriate_root . '/' . $kriate_relative_path;

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

if ( isset( $kriate_sources['functions.php'] ) ) {
	$kriate_functions_source = $kriate_sources['functions.php'];

	if ( false !== strpos( $kriate_functions_source, 'function smashing_jpeg_quality' ) ) {
		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fwrite -- Development-only CLI output; WordPress is not bootstrapped.
		fwrite( STDERR, "Generic JPEG quality compatibility callback remains in active functions.php.\n" );
		$kriate_failed = true;
	}

	$kriate_retired_quality_hooks = array(
		"add_filter( 'wp_editor_set_quality', 'smashing_jpeg_quality'",
		"add_filter( 'jpeg_quality', 'smashing_jpeg_quality'",
	);

	foreach ( $kriate_retired_quality_hooks as $kriate_retired_quality_hook ) {
		if ( false !== strpos( $kriate_functions_source, $kriate_retired_quality_hook ) ) {
			// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fwrite -- Development-only CLI output; WordPress is not bootstrapped.
			fwrite( STDERR, "Retired Briite global JPEG quality override remains: {$kriate_retired_quality_hook}\n" );
			$kriate_failed = true;
		}
	}
}

if ( isset( $kriate_sources['inc/legacy-compat.php'] ) ) {
	$kriate_legacy_compat_source = $kriate_sources['inc/legacy-compat.php'];

	if ( false === strpos( $kriate_legacy_compat_source, 'function smashing_jpeg_quality' ) ) {
		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fwrite -- Development-only CLI output; WordPress is not bootstrapped.
		fwrite( STDERR, "Missing Briite consumer JPEG quality compatibility callback.\n" );
		$kriate_failed = true;
	}

	$kriate_retired_quality_hooks = array(
		"add_filter( 'wp_editor_set_quality', 'smashing_jpeg_quality'",
		"add_filter( 'jpeg_quality', 'smashing_jpeg_quality'",
	);

	foreach ( $kriate_retired_quality_hooks as $kriate_retired_quality_hook ) {
		if ( false !== strpos( $kriate_legacy_compat_source, $kriate_retired_quality_hook ) ) {
			// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fwrite -- Development-only CLI output; WordPress is not bootstrapped.
			fwrite( STDERR, "Consumer compatibility module re-registers retired JPEG quality override: {$kriate_retired_quality_hook}\n" );
			$kriate_failed = true;
		}
	}
}

if ( isset( $kriate_sources['header.php'] ) ) {
	$kriate_header_source = $kriate_sources['header.php'];

	if ( false === strpos( $kriate_header_source, 'id="content"' ) ) {
		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fwrite -- Development-only CLI output; WordPress is not bootstrapped.
		fwrite( STDERR, "Briite's Infinite Scroll container markup is missing #content.\n" );
		$kriate_failed = true;
	}

	if ( false === strpos( $kriate_header_source, 'class="screen-reader-text skip-link" href="#content"' ) ) {
		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fwrite -- Development-only CLI output; WordPress is not bootstrapped.
		fwrite( STDERR, "Briite's required skip-to-content link is missing or no longer targets #content.\n" );
		$kriate_failed = true;
	}

	if ( false === strpos( $kriate_header_source, 'id="content" class="main" tabindex="-1"' ) ) {
		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fwrite -- Development-only CLI output; WordPress is not bootstrapped.
		fwrite( STDERR, "Briite's skip-link target is not programmatically focusable.\n" );
		$kriate_failed = true;
	}

	if ( false !== strpos( $kriate_header_source, 'href="#"' ) ) {
		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fwrite -- Development-only CLI output; WordPress is not bootstrapped.
		fwrite( STDERR, "Briite header contains a fake hash-only link.\n" );
		$kriate_failed = true;
	}

	if ( false === strpos( $kriate_header_source, 'get_feed_link()' ) ) {
		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fwrite -- Development-only CLI output; WordPress is not bootstrapped.
		fwrite( STDERR, "Briite header RSS control is not wired to the canonical feed URL.\n" );
		$kriate_failed = true;
	}
}

if ( isset( $kriate_sources['css/compat.css'] ) ) {
	$kriate_compat_source = $kriate_sources['css/compat.css'];

	$kriate_required_accessibility_styles = array(
		'.screen-reader-text:focus',
		'.main .work a:focus .caption',
		'.main .work a:focus-visible .caption',
		'.main-navigation li:focus-within > ul',
	);

	foreach ( $kriate_required_accessibility_styles as $kriate_required_accessibility_style ) {
		if ( false === strpos( $kriate_compat_source, $kriate_required_accessibility_style ) ) {
			// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fwrite -- Development-only CLI output; WordPress is not bootstrapped.
			fwrite( STDERR, "Missing Briite accessibility compatibility style: {$kriate_required_accessibility_style}\n" );
			$kriate_failed = true;
		}
	}
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
}

if ( $kriate_failed ) {
	exit( 1 );
}

// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fwrite -- Development-only CLI output; WordPress is not bootstrapped.
fwrite( STDOUT, "Briite integration contract checks passed.\n" );
