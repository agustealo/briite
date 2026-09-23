<?php
/**
 * Internationalization compatibility helpers.
 *
 * @package Briite
 */

/**
 * Load a legacy global Kriate language pack into Briite's canonical domain.
 *
 * Briite historically used the `kriate` gettext domain even though the theme
 * slug is `briite`. WordPress.org language packs use the theme slug, so Briite
 * now registers `briite` as the canonical domain. Existing site-local theme
 * translations remain compatible because `load_theme_textdomain()` resolves
 * locale-named files from the same `languages` directory.
 *
 * Some installations may also have a historical global language pack at
 * `wp-content/languages/themes/kriate-LOCALE.mo`. When no canonical Briite
 * translation exists for the active locale, load that legacy file into the
 * `briite` domain so an upgrade does not silently discard those translations.
 *
 * @return void
 */
function briite_load_legacy_translation_fallback() {
	$locale = determine_locale();

	if ( '' === $locale ) {
		return;
	}

	$theme_language_directory = get_template_directory() . '/languages';
	$canonical_files          = array(
		WP_LANG_DIR . '/themes/briite-' . $locale . '.mo',
		$theme_language_directory . '/' . $locale . '.mo',
	);

	foreach ( $canonical_files as $canonical_file ) {
		if ( is_readable( $canonical_file ) ) {
			return;
		}
	}

	$legacy_file = WP_LANG_DIR . '/themes/kriate-' . $locale . '.mo';

	if ( ! is_readable( $legacy_file ) ) {
		return;
	}

	load_textdomain( 'briite', $legacy_file );
}
add_action( 'after_setup_theme', 'briite_load_legacy_translation_fallback', 11 );
