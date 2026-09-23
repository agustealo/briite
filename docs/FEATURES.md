# Briite 1.1.1 feature and behavior reference

This document describes the production behavior shipped by Briite 1.1.1. It is intentionally based on the theme's active WordPress contracts and templates rather than a wishlist. The modernization program preserves the established Briite product while repairing compatibility, accessibility, packaging, and maintenance boundaries.

## Product identity

Briite is a classic WordPress portfolio theme built around a fixed navigation rail and image-forward content grid. It is suitable for portfolios, project journals, visual essays, case studies, studio sites, and lightweight editorial publishing.

Briite remains a classic theme. It uses standard WordPress theme APIs, the Customizer-era theme-mod model, PHP templates, WordPress menus/widgets, and editor styles. It has not been converted into a block theme because doing so would materially change the product and upgrade surface.

## Front-end presentation

### Portfolio and posts index

The blog/posts index uses `content-home.php` to present each post as a visual work tile. Posts with featured images use the canonical `briite-grid-thumb` crop at 450×450 pixels. The title is presented in Briite's existing overlay/caption treatment and the entire tile links to the single post.

### Single project or post

Single posts use Briite's established top hero region. A featured image is requested at the canonical `briite-single-banner` size of 1300×500 pixels and used as the hero background. The screen also provides:

- the post title;
- previous and next post navigation when neighboring posts exist;
- a category/grid return control for a top-level non-default category;
- normal post content and paginated content support;
- category and tag metadata;
- permalink metadata;
- comments when enabled;
- the preserved four-column bottom widget region when configured.

### Navigation

Briite exposes one WordPress menu location: **Primary Menu**. The desktop experience preserves the historical fixed navigation rail. The responsive experience uses the existing `#menu_icon` control and `show_menu` / `close_menu` classes rather than inventing a second navigation system.

Nested WordPress menus remain supported. Keyboard and focus handling are reinforced through compatibility CSS and ARIA state changes without changing the visual menu model.

### Header and identity

Briite supports WordPress Custom Header images at the established 162×21 dimensions. The bundled Briite logo remains the default header image. Header text is disabled because Briite's actual header markup renders the image rather than WordPress's site-title/header-text controls.

The logo/home link includes an accessible label derived from the site name, falling back to “Home”.

### Backgrounds

WordPress Custom Background support remains available with a white default background. Existing theme-mod values are preserved across upgrade.

### Widgets

The following historical widget area IDs are canonical and intentionally preserved:

1. `bottom-widget-1` — Widget 1
2. `bottom-widget-2` — Widget 2
3. `bottom-widget-3` — Widget 3
4. `bottom-widget-4` — Widget 4

The widget region is omitted entirely when none of the four areas is active. Existing installations therefore do not receive empty layout furniture.

## WordPress content features

Briite supports:

- featured images;
- automatic feed links;
- WordPress-managed document titles;
- responsive embeds;
- HTML5 search, comments, galleries, captions, scripts, and styles;
- threaded comment reply support when enabled by WordPress;
- post formats: aside, image, video, quote, and link;
- pages;
- categories and archives;
- search results;
- index pagination;
- post navigation;
- 404 recovery with the WordPress search form;
- Jetpack Infinite Scroll using Briite's real `#content` container, with Jetpack's optional sliding footer disabled because Briite has no matching full-width footer target.

## Image behavior and upgrade compatibility

Briite 1.1.1 uses uniquely prefixed image-size identifiers:

- `briite-grid-thumb` — 450×450, centered hard crop;
- `briite-single-banner` — 1300×500, centered hard crop.

Older Briite installations may already have generated media metadata/files under the historical `grid-thumb` and `single-banner` names. Briite does not force a bulk media regeneration. When a canonical `briite-*` derivative is missing but the historical derivative exists, the `image_downsize` compatibility path reuses the existing generated file.

New uploads generate only the canonical prefixed sizes.

Briite does not override WordPress's global JPEG quality policy. Future media compression remains owned by WordPress core or explicitly installed site code.

## Editor experience

Briite declares `editor-styles` support and loads:

- `css/fonts.css`;
- `css/editor.css`.

This brings Briite's content typography into the editor while avoiding front-end opt-ins such as `wp-block-styles` and `align-wide` that would imply a broader layout redesign.

## Accessibility

The active compatibility work includes:

- a skip link targeting the real `#content` element;
- a focusable main-content destination;
- accessible labels for primary navigation and the logo/home link;
- an actual button for responsive navigation with `aria-controls` and synchronized `aria-expanded` state;
- screen-reader labels for icon-only navigation controls;
- keyboard/focus handling for nested menus;
- focus-triggered tooltip behavior matching mouse hover behavior;
- reduced-motion compatibility;
- semantic HTML5 comment, gallery, caption, script, and style support.

These changes repair access paths without redesigning Briite's established interface.

## Internationalization and RTL

Briite loads translations from `/languages/` under the `briite` text domain. The translation template is checked against source strings in CI to prevent silent POT drift.

`rtl.css` is a real directional mirror of the theme's layout and navigation geometry rather than an empty compatibility stub.

## Asset policy

Required runtime assets are local to the theme package. Briite does not require a runtime CDN or external font service.

The normal consumer package includes:

- Bootstrap 3.3.7 CSS;
- Bootstrap's historical Glyphicons payload for downstream compatibility;
- local Raleway font files;
- Briite's own theme artwork, backgrounds, icons, CSS, and JavaScript.

The WordPress.org package removes the unused Glyphicon font payload and its primary icon-definition block because Briite's own templates do not depend on Glyphicon classes.

## Legacy compatibility boundaries

The active Briite runtime no longer owns unrelated persistent user-profile data or WordPress admin list-table presentation.

The normal consumer package retains a small isolated `inc/legacy-compat.php` module containing generic historical aliases for downstream child themes/plugins. Briite itself does not register those legacy aliases into active runtime behavior.

The WordPress.org package excludes that generic alias module to satisfy the directory's stricter public-namespace requirements. The production Briite templates, settings, content model, and visual behavior remain the same between profiles.

## Responsive behavior

Briite preserves the original responsive presentation model rather than layering a second design system over it. The mobile navigation is driven by the real menu button and the same primary menu markup used on desktop. Required compatibility CSS also covers current keyboard and focus behavior.

See [SCREENSHOTS.md](SCREENSHOTS.md) for real-runtime desktop and mobile captures.

## Release and runtime verification

The repository quality system verifies the theme instead of relying on manual confidence alone. Current automated coverage includes:

- PHP syntax across supported PHP lines;
- PHP 7.4–8.5 compatibility analysis;
- WordPress Coding Standards;
- JavaScript syntax;
- consumer release construction and archive validation;
- WordPress.org package construction and directory-specific exclusions;
- WordPress 7.1.1 installation and activation of the packaged artifact;
- preservation of required theme supports, menu location, editor styles, and widget IDs;
- representative home, single, page, search, category, pagination, and 404 rendering;
- local asset loading;
- nested menu rendering;
- runtime diagnostic rejection for PHP warnings, notices, deprecations, fatal errors, parse errors, and uncaught errors/exceptions;
- translation compatibility and source-string drift prevention.

## Upgrade contract

Briite modernization follows a compatibility-first rule: preserve recognizable user-facing behavior and saved configuration unless a change is required by WordPress compatibility, accessibility, security, packaging rules, or a proven defect.

The 1.1 line intentionally preserves:

- the theme identity and familiar layout;
- menu location and assignments;
- widget area IDs and assignments;
- Customizer/theme-mod data;
- template hierarchy and core content model;
- featured images and attachment data;
- stored historical social user-meta values, even though Briite no longer owns profile-field persistence;
- old generated image derivatives through the compatibility fallback described above.

That boundary is deliberate. Briite 1.1.1 is a maintained Briite, not a replacement theme wearing the same name.
