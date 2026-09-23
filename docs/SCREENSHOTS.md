# Briite screenshot provenance and regeneration

Briite's public screenshots are runtime evidence. They must be captured from a real WordPress installation running the packaged Briite theme, not assembled as UI mockups or generated as fictional interfaces.

## Published captures

| File | Surface | Purpose |
| --- | --- | --- |
| `docs/screenshots/briite-home-desktop.png` | Desktop posts/portfolio index | Shows Briite's fixed navigation rail, image-forward grid, captions, and overall visual identity. |
| `docs/screenshots/briite-single-project.png` | Single post/project | Shows the featured-image hero, project title, navigation controls, content area, and post presentation. |
| `docs/screenshots/briite-mobile-navigation.png` | Mobile home with menu opened | Shows Briite's real responsive menu control and the same Primary Menu rendered for a narrow viewport. |
| `docs/screenshots/briite-404-recovery.png` | 404 route | Shows the real not-found recovery experience and WordPress search form. |
| `screenshot.png` | WordPress theme-directory screenshot | 1200×900 capture from the same real home runtime, satisfying the repository's 4:3 package gate. |

## Capture source

The documentation capture path uses:

- the consumer package built from the current repository head;
- WordPress 7.1.1, matching Briite's runtime smoke gate;
- PHP 8.3 for the documentation fixture runtime;
- a real MySQL service;
- real WordPress posts, pages, categories, menu assignment, featured-image attachments, and Briite theme activation;
- Chromium through Playwright for browser rendering and screenshots.

The fixture content exists only inside the temporary documentation runtime. It is not shipped in the Briite theme ZIP and does not create demo-content dependencies for users.

## Reproduction

The repository contains two capture helpers:

- `tools/seed-documentation-site.sh` builds the representative WordPress content and local featured-image fixtures;
- `tools/capture-doc-screenshots.mjs` opens the actual rendered routes and writes the screenshots.

`.github/workflows/docs-screenshots.yml` provisions the same runtime in GitHub Actions. The initial release branch commits the generated files back to that branch so they can be reviewed with the documentation that references them.

For future visual maintenance, run the workflow from a non-production branch, review the generated images, then merge them only after the normal Briite quality and package gates are green on the final head.

## Capture rules

1. Never use an image-generation model or a hand-built HTML mockup as a Briite product screenshot.
2. Never hide a broken feature merely to make the screenshot cleaner.
3. Use the packaged theme artifact, not a loose alternate copy of the source tree.
4. Seed representative content through WordPress APIs/WP-CLI so templates, featured-image handling, menus, and media derivatives are exercised normally.
5. Keep the theme-directory `screenshot.png` at 1200×900 and 4:3.
6. Keep README screenshots legible at common GitHub widths and avoid excessive browser chrome.
7. Re-capture only when user-visible behavior or the documentation fixture intentionally changes. Documentation maintenance must not become an excuse to redesign the frozen release.

## What the screenshots prove

The captures are visual evidence for Briite's real surfaces. They supplement, but do not replace, the automated runtime assertions in the Quality Gate. A screenshot can show aesthetics and responsive composition; CI remains responsible for contracts such as namespace boundaries, PHP compatibility, translation drift, packaging exclusions, and runtime diagnostics.
