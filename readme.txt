=== Briite ===
Requires at least: 5.9
Tested up to: 7.1
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A lightweight classic WordPress portfolio theme with a fixed navigation rail and image-forward project grid.

== Description ==

Briite is a lightweight classic WordPress portfolio theme built for image-forward publishing. The 1.1 line modernizes WordPress, PHP, accessibility, editor, asset-loading, and runtime compatibility while preserving Briite's established layout, navigation, widget areas, template behavior, and visual identity.

Briite uses the WordPress Customizer and classic theme APIs. It does not require a companion plugin, external font service, analytics service, or remote runtime asset CDN.

== Installation ==

1. Upload the Briite theme ZIP from Appearance > Themes > Add New > Upload Theme, or copy the `briite` directory into `wp-content/themes/`.
2. Activate Briite from Appearance > Themes.
3. Assign an existing menu to the Primary Menu location if desired.
4. Existing footer widget assignments continue to use the historical Widget 1 through Widget 4 areas.

== Frequently Asked Questions ==

= Will upgrading change the established Briite design? =

No. The modernization work intentionally preserves Briite's familiar front-end structure and visual identity. Compatibility, accessibility, local assets, editor support, and runtime behavior were repaired without converting the theme into a different product.

= Does Briite load fonts or framework files from a CDN? =

No. Briite serves its required front-end assets from the theme package and uses the copy of jQuery registered by WordPress.

= Why does Briite have consumer and WordPress.org release profiles? =

They are the same Briite theme and share the same active templates, settings, content model, JavaScript behavior, and visual identity. Briite's active image-size names and first-party asset handles are prefixed in both profiles. The normal consumer package additionally preserves a small compatibility membrane for historical unprefixed PHP callbacks, image-size names, asset handles, and the Bootstrap 3.3.7 Glyphicons payload used by older downstream child themes. The WordPress.org package omits those inactive legacy aliases and unused Glyphicon fonts so the directory artifact satisfies the current public-identifier and bundled-font boundaries without redesigning the theme.

= What happens to existing media when image quality or image-size behavior changes? =

Existing media files are not rewritten. WordPress core controls compression for future generated image derivatives unless site code deliberately installs a custom image-quality filter. Briite now registers prefixed active image sizes and automatically reuses an already-generated historical `single-banner` or `grid-thumb` derivative when an older attachment does not yet have the new prefixed derivative.

= What happens to the old Twitter, Facebook, and LinkedIn user-profile fields? =

Briite no longer adds or saves custom user-profile fields because persistent profile data is outside a theme's presentation responsibility. Existing `twitter`, `facebook`, and `linkedin` user-meta values are not deleted or rewritten during the upgrade. The normal consumer package keeps the historical PHP callback names as inert compatibility shims for existing integrations; the WordPress.org release profile omits those unprefixed aliases.

= What happened to the Thumbnail column in the Posts and Pages admin lists? =

Briite no longer registers custom WordPress admin list-table columns because that behavior is separate from the theme's front-end presentation. This does not remove featured images or attachment data. Prefixed historical helpers remain available, and the normal consumer package also keeps the old unprefixed post-column alias for downstream compatibility. The WordPress.org release profile omits that unprefixed alias.

== Changelog ==

= 1.1.1 =
* Hardened compatibility for WordPress 7.1 and PHP 7.4 through 8.5.
* Preserved existing menus, widget IDs, Customizer data, template hierarchy, and visual identity.
* Moved required front-end framework and font assets to local theme files.
* Added the exact upstream Bootstrap 3.3.7 unminified CSS source alongside the minified runtime stylesheet and pinned both files by upstream Git blob identity.
* Added a deterministic WordPress.org release profile that omits the unused Glyphicons font payload without changing Briite's templates or normal consumer package.
* Added modern editor styles without opting the front end into a redesigned block-layout model.
* Repaired keyboard navigation, skip-link behavior, responsive navigation semantics, and reduced-motion support.
* Removed inactive legacy template and Customizer wiring that duplicated live behavior.
* Returned future JPEG compression policy to WordPress core while preserving the historical callback symbol in the normal consumer package.
* Returned generator output policy to WordPress core and retired theme-owned social-profile persistence without deleting stored user metadata.
* Retired automatic admin post/page Thumbnail columns while preserving compatibility helpers for explicit downstream opt-in.
* Prefixed active Briite image-size names and first-party asset handles while preserving historical media derivatives and consumer-only aliases for existing child themes.
* Added automated coding-standard, compatibility, contract, JavaScript, package, and WordPress runtime gates.

= 1.0 =
* Original Briite classic portfolio theme release.

== Upgrade Notice ==

= 1.1.1 =
Compatibility-focused maintenance release for current WordPress and PHP versions. Existing Briite front-end layout, navigation, widget IDs, Customizer data, featured images, attachment data, stored user metadata, and historical consumer-package integration aliases are preserved.

== Copyright ==

Briite WordPress Theme, Copyright 2014-2026 Agustealo Johnson.
Briite is distributed under the terms of the GNU General Public License v2 or later.
Source: https://github.com/agustealo/briite

Briite is based on Underscores (_s), Copyright 2012-2014 Automattic, Inc.
Underscores is distributed under the terms of the GNU General Public License v2 or later.
Source: https://underscores.me/

== Resources ==

Bootstrap 3.3.7 CSS, Copyright 2011-2016 Twitter, Inc.
License: MIT
Source: https://github.com/twbs/bootstrap/tree/v3.3.7
Briite includes the official `dist/css/bootstrap.css` source as `css/bootstrap-3.3.7.css` and the official `dist/css/bootstrap.min.css` distribution as `css/bootstrap-3.3.7.min.css`.

normalize.css 3.0.3, Copyright Nicolas Gallagher and Jonathan Neal.
License: MIT
Source: https://github.com/necolas/normalize.css

Glyphicons Halflings font files are bundled as part of the Bootstrap 3.3.7 distribution in the normal Briite consumer package.
Copyright: Jan Kovarik.
Source: https://getbootstrap.com/docs/3.3/components/#glyphicons
Bootstrap documents the Halflings set as made available for Bootstrap use without cost, with attribution requested when practical. Briite preserves the files in the normal consumer package for compatibility with existing Bootstrap-based child-theme markup. The WordPress.org release profile omits these font files and their primary font/icon-definition block because Briite's own templates do not use Glyphicon classes.

Raleway font files, Copyright 2010-2012 Matt McInerney, Pablo Impallari, and Rodrigo Fuenzalida, with later contributors to the Raleway project.
License: SIL Open Font License 1.1
Source: https://github.com/theleagueof/raleway

Meyer CSS Reset v2.0, Eric A. Meyer.
License: Public Domain
Source: https://meyerweb.com/eric/tools/css/reset/

Briite theme artwork in `images/`, including the Briite logo, background patterns, menu/navigation artwork, and supporting interface imagery, was introduced as part of the Briite theme source by Agustealo Johnson.
Copyright 2014-2026 Agustealo Johnson.
License: GPLv2 or later
Source: https://github.com/agustealo/briite

Briite `screenshot.png`, Copyright 2014-2026 Agustealo Johnson.
License: GPLv2 or later
Source: https://github.com/agustealo/briite
