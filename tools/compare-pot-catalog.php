<?php
/**
 * Compare gettext catalog entries while ignoring generated POT metadata.
 *
 * Usage:
 * php tools/compare-pot-catalog.php committed.pot generated.pot
 *
 * @package kriate
 */

// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Development-only CLI arguments; WordPress is not bootstrapped.
$kriate_cli_args = isset( $_SERVER['argv'] ) && is_array( $_SERVER['argv'] ) ? $_SERVER['argv'] : array();

if ( 3 !== count( $kriate_cli_args ) ) {
	// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fwrite -- Development-only CLI output; WordPress is not bootstrapped.
	fwrite( STDERR, "Usage: php tools/compare-pot-catalog.php committed.pot generated.pot\n" );
	exit( 2 );
}

$kriate_committed_path = $kriate_cli_args[1];
$kriate_generated_path = $kriate_cli_args[2];

$kriate_decode_po_string = static function ( $kriate_quoted, $kriate_path, $kriate_line_number ) {
	$kriate_decoded = json_decode( $kriate_quoted, true );

	if ( ! is_string( $kriate_decoded ) ) {
		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fwrite -- Development-only CLI output; WordPress is not bootstrapped.
		fwrite( STDERR, "Unable to parse PO string in {$kriate_path} on line {$kriate_line_number}.\n" );
		exit( 2 );
	}

	return $kriate_decoded;
};

$kriate_parse_catalog = static function ( $kriate_path ) use ( $kriate_decode_po_string ) {
	if ( ! is_file( $kriate_path ) ) {
		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fwrite -- Development-only CLI output; WordPress is not bootstrapped.
		fwrite( STDERR, "POT file does not exist: {$kriate_path}\n" );
		exit( 2 );
	}

	// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- Development-only local source read; WordPress is not bootstrapped.
	$kriate_contents = file_get_contents( $kriate_path );
	if ( false === $kriate_contents ) {
		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fwrite -- Development-only CLI output; WordPress is not bootstrapped.
		fwrite( STDERR, "Unable to read POT file: {$kriate_path}\n" );
		exit( 2 );
	}

	$kriate_lines = preg_split( '/\R/', $kriate_contents );
	if ( false === $kriate_lines ) {
		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fwrite -- Development-only CLI output; WordPress is not bootstrapped.
		fwrite( STDERR, "Unable to split POT file into lines: {$kriate_path}\n" );
		exit( 2 );
	}

	$kriate_catalog = array();
	$kriate_entry   = array(
		'msgctxt'      => null,
		'msgid'        => null,
		'msgid_plural' => null,
	);
	$kriate_field   = null;

	$kriate_flush_entry = static function () use ( &$kriate_catalog, &$kriate_entry, &$kriate_field ) {
		if ( null !== $kriate_entry['msgid'] && '' !== $kriate_entry['msgid'] ) {
			// phpcs:ignore WordPress.WP.AlternativeFunctions.json_encode_json_encode -- Development-only standalone CLI; WordPress is not bootstrapped.
			$kriate_key = json_encode(
				array(
					'context' => $kriate_entry['msgctxt'],
					'msgid'   => $kriate_entry['msgid'],
					'plural'  => $kriate_entry['msgid_plural'],
				),
				JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
			);

			if ( false === $kriate_key ) {
				// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fwrite -- Development-only CLI output; WordPress is not bootstrapped.
				fwrite( STDERR, "Unable to encode a POT catalog key.\n" );
				exit( 2 );
			}

			$kriate_catalog[ $kriate_key ] = $kriate_entry;
		}

		$kriate_entry = array(
			'msgctxt'      => null,
			'msgid'        => null,
			'msgid_plural' => null,
		);
		$kriate_field = null;
	};

	foreach ( $kriate_lines as $kriate_line_index => $kriate_line ) {
		$kriate_line_number = $kriate_line_index + 1;
		$kriate_trimmed     = trim( $kriate_line );

		if ( '' === $kriate_trimmed ) {
			$kriate_flush_entry();
			continue;
		}

		if ( 0 === strpos( $kriate_trimmed, '#' ) ) {
			continue;
		}

		$kriate_matched_field = false;
		foreach ( array( 'msgctxt', 'msgid_plural', 'msgid' ) as $kriate_candidate_field ) {
			$kriate_prefix = $kriate_candidate_field . ' ';
			if ( 0 !== strpos( $kriate_trimmed, $kriate_prefix ) ) {
				continue;
			}

			$kriate_quoted = substr( $kriate_trimmed, strlen( $kriate_prefix ) );
			$kriate_entry[ $kriate_candidate_field ] = $kriate_decode_po_string(
				$kriate_quoted,
				$kriate_path,
				$kriate_line_number
			);
			$kriate_field         = $kriate_candidate_field;
			$kriate_matched_field = true;
			break;
		}

		if ( $kriate_matched_field ) {
			continue;
		}

		if ( 0 === strpos( $kriate_trimmed, 'msgstr' ) ) {
			$kriate_field = null;
			continue;
		}

		if ( '"' === substr( $kriate_trimmed, 0, 1 ) && null !== $kriate_field ) {
			$kriate_entry[ $kriate_field ] .= $kriate_decode_po_string(
				$kriate_trimmed,
				$kriate_path,
				$kriate_line_number
			);
		}
	}

	$kriate_flush_entry();
	ksort( $kriate_catalog, SORT_STRING );

	return $kriate_catalog;
};

$kriate_describe_entry = static function ( $kriate_entry ) {
	// phpcs:ignore WordPress.WP.AlternativeFunctions.json_encode_json_encode -- Development-only standalone CLI; WordPress is not bootstrapped.
	$kriate_encoded_msgid = json_encode( $kriate_entry['msgid'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
	$kriate_description   = 'msgid ' . ( false === $kriate_encoded_msgid ? '"<encoding-error>"' : $kriate_encoded_msgid );

	if ( null !== $kriate_entry['msgctxt'] ) {
		// phpcs:ignore WordPress.WP.AlternativeFunctions.json_encode_json_encode -- Development-only standalone CLI; WordPress is not bootstrapped.
		$kriate_encoded_context = json_encode( $kriate_entry['msgctxt'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
		$kriate_description    .= ' context ' . ( false === $kriate_encoded_context ? '"<encoding-error>"' : $kriate_encoded_context );
	}

	if ( null !== $kriate_entry['msgid_plural'] ) {
		// phpcs:ignore WordPress.WP.AlternativeFunctions.json_encode_json_encode -- Development-only standalone CLI; WordPress is not bootstrapped.
		$kriate_encoded_plural = json_encode( $kriate_entry['msgid_plural'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
		$kriate_description   .= ' plural ' . ( false === $kriate_encoded_plural ? '"<encoding-error>"' : $kriate_encoded_plural );
	}

	return $kriate_description;
};

$kriate_committed_catalog = $kriate_parse_catalog( $kriate_committed_path );
$kriate_generated_catalog = $kriate_parse_catalog( $kriate_generated_path );
$kriate_missing_entries   = array_diff_key( $kriate_generated_catalog, $kriate_committed_catalog );
$kriate_stale_entries     = array_diff_key( $kriate_committed_catalog, $kriate_generated_catalog );

if ( $kriate_missing_entries || $kriate_stale_entries ) {
	foreach ( $kriate_missing_entries as $kriate_entry ) {
		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fwrite -- Development-only CLI output; WordPress is not bootstrapped.
		fwrite( STDERR, 'Missing from languages/briite.pot: ' . $kriate_describe_entry( $kriate_entry ) . "\n" );
	}

	foreach ( $kriate_stale_entries as $kriate_entry ) {
		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fwrite -- Development-only CLI output; WordPress is not bootstrapped.
		fwrite( STDERR, 'Stale in languages/briite.pot: ' . $kriate_describe_entry( $kriate_entry ) . "\n" );
	}

	exit( 1 );
}

// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fwrite -- Development-only CLI output; WordPress is not bootstrapped.
fwrite( STDOUT, "Briite translation catalog entries match generated source strings.\n" );
