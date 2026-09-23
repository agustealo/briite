#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "${ROOT_DIR}"

fail() {
	printf '%s\n' "$1" >&2
	exit 1
}

grep -qxF 'Text Domain: briite' < <(grep '^Text Domain:' style.css) \
	|| fail 'style.css must declare Text Domain: briite.'

grep -qF "load_theme_textdomain( 'briite'" functions.php \
	|| fail 'functions.php must register the canonical briite text domain.'

grep -qF "require get_template_directory() . '/inc/i18n.php';" functions.php \
	|| fail 'functions.php must load the Briite i18n compatibility module.'

test -s languages/briite.pot \
	|| fail 'languages/briite.pot is missing or empty.'

test ! -e languages/kriate.pot \
	|| fail 'The stale languages/kriate.pot template must not return.'

grep -qF '"Project-Id-Version: Briite 1.1.1\n"' languages/briite.pot \
	|| fail 'languages/briite.pot does not identify the current Briite release.'

grep -qF '"X-Domain: briite\n"' languages/briite.pot \
	|| fail 'languages/briite.pot does not declare the briite domain.'

grep -qF "function briite_load_legacy_translation_fallback" inc/i18n.php \
	|| fail 'The legacy Kriate translation fallback is missing.'

grep -qF "WP_LANG_DIR . '/themes/kriate-'" inc/i18n.php \
	|| fail 'The legacy global Kriate language-pack path is missing.'

grep -qF "load_textdomain( 'briite', \$legacy_file )" inc/i18n.php \
	|| fail 'Legacy language packs must load into the canonical briite domain.'

runtime_files=()
while IFS= read -r -d '' file; do
	runtime_files+=("${file}")
done < <(
	find . -type f -name '*.php' \
		-not -path './.git/*' \
		-not -path './.github/*' \
		-not -path './dist/*' \
		-not -path './tools/*' \
		-not -path './vendor/*' \
		-print0
)

if ((${#runtime_files[@]} == 0)); then
	fail 'No runtime PHP files were found for the text-domain contract.'
fi

if grep -nE "(['\"]kriate['\"])" "${runtime_files[@]}"; then
	fail 'A runtime PHP file still uses the historical kriate gettext domain.'
fi

if ! grep -Rqs --include='*.php' 'kriate_' . \
	--exclude-dir=.git \
	--exclude-dir=.github \
	--exclude-dir=dist \
	--exclude-dir=tools \
	--exclude-dir=vendor; then
	fail 'Historical kriate_* compatibility APIs unexpectedly disappeared.'
fi

printf 'Briite text-domain contract checks passed.\n'
