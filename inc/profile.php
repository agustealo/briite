<?php
/**
 * Historical Briite profile compatibility entrypoint.
 *
 * This path remains in the normal consumer package for child themes or
 * integrations that historically loaded `inc/profile.php` directly. The
 * callback implementations now live in one isolated compatibility module.
 *
 * The WordPress.org release profile removes this wrapper together with the
 * legacy global callback module.
 *
 * @package kriate
 */

require_once __DIR__ . '/legacy-global-compat.php';
