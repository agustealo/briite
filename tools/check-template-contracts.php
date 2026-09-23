<?php
/**
 * Verify Briite template-part compatibility contracts.
 *
 * This script is development tooling and runs from Composer/CI, not WordPress.
 *
 * @package kriate
 */

$kriate_root   = dirname( __DIR__ );
$kriate_failed = false;

$kriate_contracts = array(
	'404.php'          => array(
		'get_search_form();',
	),
	'category.php'     => array(
		"get_template_part( 'content', 'grid' )",
		'kriate_paging_nav()',
	),
	'content-grid.php' => array(
		"get_template_part( 'content', 'home' )",
	),
	'content-home.php' => array(
		'id="post-<?php the_ID(); ?>"',
		"post_class( 'work' )",
	),
	'header.php'       => array(
		'href="#content"',
		'id="content" class="main" tabindex="-1"',
		"__( 'Home', 'briite' )",
		'aria-label="<?php echo esc_attr( $kriate_home_label ); ?>"',
	),
	'single.php'       => array(
		'id="post-<?php the_ID(); ?>"',
		"post_class( 'entry-content' )",
		'$kriate_previous_post = get_previous_post();',
		'<a href="<?php echo esc_url( get_permalink( $kriate_previous_post->ID ) ); ?>" class="previous"',
		'$kriate_next_post = get_next_post();',
		'<a href="<?php echo esc_url( get_permalink( $kriate_next_post->ID ) ); ?>" class="next"',
		'kriate_post_nav()',
		'comments_template()',
		'get_sidebar()',
	),
);

foreach ( $kriate_contracts as $kriate_relative_path => $kriate_expected_sources ) {
	$kriate_absolute_path = $kriate_root . DIRECTORY_SEPARATOR . $kriate_relative_path;

	if ( ! is_file( $kriate_absolute_path ) ) {
		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fwrite -- Development-only CLI output; WordPress is not bootstrapped.
		fwrite( STDERR, "Missing required Briite template contract file: {$kriate_relative_path}\n" );
		$kriate_failed = true;
		continue;
	}

	// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- Development-only local source read; WordPress is not bootstrapped.
	$kriate_source = file_get_contents( $kriate_absolute_path );

	if ( false === $kriate_source ) {
		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fwrite -- Development-only CLI output; WordPress is not bootstrapped.
		fwrite( STDERR, "Unable to read Briite template contract file: {$kriate_relative_path}\n" );
		$kriate_failed = true;
		continue;
	}

	foreach ( $kriate_expected_sources as $kriate_expected_source ) {
		if ( false === strpos( $kriate_source, $kriate_expected_source ) ) {
			// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fwrite -- Development-only CLI output; WordPress is not bootstrapped.
			fwrite( STDERR, "Broken Briite template contract in {$kriate_relative_path}: {$kriate_expected_source}\n" );
			$kriate_failed = true;
		}
	}
}

$kriate_retired_templates = array(
	'content-single.php',
);

foreach ( $kriate_retired_templates as $kriate_retired_template ) {
	$kriate_retired_path = $kriate_root . DIRECTORY_SEPARATOR . $kriate_retired_template;

	if ( is_file( $kriate_retired_path ) ) {
		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fwrite -- Development-only CLI output; WordPress is not bootstrapped.
		fwrite( STDERR, "Retired duplicate Briite template remains: {$kriate_retired_template}\n" );
		$kriate_failed = true;
	}
}

if ( $kriate_failed ) {
	exit( 1 );
}

// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fwrite -- Development-only CLI output; WordPress is not bootstrapped.
fwrite( STDOUT, "Briite template contract checks passed.\n" );
