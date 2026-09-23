<?php
/**
 * Historical Briite social-profile compatibility entry point.
 *
 * The normal consumer package includes inc/legacy-compat.php so established
 * child themes and integrations keep Briite's historical callback symbols.
 * The WordPress.org release profile intentionally omits that consumer-only
 * compatibility file to satisfy the directory's public-identifier prefix rules.
 *
 * @package kriate
 */

$kriate_legacy_compat_file = __DIR__ . '/legacy-compat.php';

if ( is_readable( $kriate_legacy_compat_file ) ) {
	require_once $kriate_legacy_compat_file;
}
