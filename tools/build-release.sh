#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
DIST_DIR="${ROOT_DIR}/dist"
STAGE_DIR="${DIST_DIR}/briite"

VERSION="$(awk -F': ' '/^Version:/ { print $2; exit }' "${ROOT_DIR}/style.css" | tr -d '\r')"
if [[ -z "${VERSION}" ]]; then
	echo "Unable to determine Briite version from style.css." >&2
	exit 1
fi

ARCHIVE_PATH="${DIST_DIR}/briite-${VERSION}.zip"
CHECKSUM_PATH="${ARCHIVE_PATH}.sha256"

REQUIRED_FILES=(
	404.php
	archive.php
	category.php
	comments.php
	content-grid.php
	content-home.php
	content-none.php
	content-page.php
	content-search.php
	content.php
	css/bootstrap-3.3.7.css
	css/bootstrap-3.3.7.min.css
	css/compat.css
	css/editor.css
	css/fonts.css
	css/theme.css
	footer.php
	functions.php
	header.php
	index.php
	LICENSE.txt
	page.php
	readme.txt
	rtl.css
	screenshot.png
	search.php
	sidebar.php
	single.php
	style.css
)

REQUIRED_DIRECTORIES=(
	fonts
	images
	inc
	js
	languages
)

for relative_path in "${REQUIRED_FILES[@]}"; do
	if [[ ! -f "${ROOT_DIR}/${relative_path}" ]]; then
		echo "Missing required release file: ${relative_path}" >&2
		exit 1
	fi
done

for relative_path in "${REQUIRED_DIRECTORIES[@]}"; do
	if [[ ! -d "${ROOT_DIR}/${relative_path}" ]]; then
		echo "Missing required release directory: ${relative_path}" >&2
		exit 1
	fi
done

if [[ ! -f "${ROOT_DIR}/inc/legacy-compat.php" ]]; then
	echo "Missing consumer legacy compatibility module: inc/legacy-compat.php" >&2
	exit 1
fi

if [[ -f "${ROOT_DIR}/inc/profile.php" ]]; then
	echo "Retired inc/profile.php compatibility file remains in the release source." >&2
	exit 1
fi

if ! grep -q 'Bootstrap v3.3.7' "${ROOT_DIR}/css/bootstrap-3.3.7.css"; then
	echo "Bundled unminified Bootstrap CSS does not identify itself as version 3.3.7." >&2
	exit 1
fi

if ! grep -q 'Bootstrap v3.3.7' "${ROOT_DIR}/css/bootstrap-3.3.7.min.css"; then
	echo "Bundled minified Bootstrap CSS does not identify itself as version 3.3.7." >&2
	exit 1
fi

if ! grep -q '^=== Briite ===$' "${ROOT_DIR}/readme.txt"; then
	echo "readme.txt does not contain the expected Briite header." >&2
	exit 1
fi

for required_heading in '== Description ==' '== Changelog ==' '== Copyright ==' '== Resources =='; do
	if ! grep -qF "${required_heading}" "${ROOT_DIR}/readme.txt"; then
		echo "readme.txt is missing required section: ${required_heading}" >&2
		exit 1
	fi
done

STYLE_TESTED_UP_TO="$(awk -F': ' '/^Tested up to:/ { print $2; exit }' "${ROOT_DIR}/style.css" | tr -d '\r')"
README_TESTED_UP_TO="$(awk -F': ' '/^Tested up to:/ { print $2; exit }' "${ROOT_DIR}/readme.txt" | tr -d '\r')"
if [[ "${STYLE_TESTED_UP_TO}" != "${README_TESTED_UP_TO}" ]]; then
	echo "style.css and readme.txt Tested up to values differ." >&2
	exit 1
fi

STYLE_REQUIRES_AT_LEAST="$(awk -F': ' '/^Requires at least:/ { print $2; exit }' "${ROOT_DIR}/style.css" | tr -d '\r')"
README_REQUIRES_AT_LEAST="$(awk -F': ' '/^Requires at least:/ { print $2; exit }' "${ROOT_DIR}/readme.txt" | tr -d '\r')"
if [[ "${STYLE_REQUIRES_AT_LEAST}" != "${README_REQUIRES_AT_LEAST}" ]]; then
	echo "style.css and readme.txt Requires at least values differ." >&2
	exit 1
fi

STYLE_REQUIRES_PHP="$(awk -F': ' '/^Requires PHP:/ { print $2; exit }' "${ROOT_DIR}/style.css" | tr -d '\r')"
README_REQUIRES_PHP="$(awk -F': ' '/^Requires PHP:/ { print $2; exit }' "${ROOT_DIR}/readme.txt" | tr -d '\r')"
if [[ "${STYLE_REQUIRES_PHP}" != "${README_REQUIRES_PHP}" ]]; then
	echo "style.css and readme.txt Requires PHP values differ." >&2
	exit 1
fi

php -r '
$image = getimagesize($argv[1]);
if (false === $image) {
    fwrite(STDERR, "Unable to read screenshot dimensions.\n");
    exit(1);
}
[$width, $height] = $image;
if ($width > 1200 || $height > 900) {
    fwrite(STDERR, "screenshot.png exceeds the 1200x900 WordPress limit.\n");
    exit(1);
}
if ($width * 3 !== $height * 4) {
    fwrite(STDERR, "screenshot.png must use a 4:3 aspect ratio.\n");
    exit(1);
}
' "${ROOT_DIR}/screenshot.png"

rm -rf "${DIST_DIR}"
mkdir -p "${STAGE_DIR}"

for relative_path in "${REQUIRED_FILES[@]}"; do
	mkdir -p "$(dirname "${STAGE_DIR}/${relative_path}")"
	cp -p "${ROOT_DIR}/${relative_path}" "${STAGE_DIR}/${relative_path}"
done

for relative_path in "${REQUIRED_DIRECTORIES[@]}"; do
	cp -a "${ROOT_DIR}/${relative_path}" "${STAGE_DIR}/${relative_path}"
done

if find "${STAGE_DIR}" -type l -print -quit | grep -q .; then
	echo "Release package must not contain symbolic links." >&2
	exit 1
fi

if find "${STAGE_DIR}" \( -name '.DS_Store' -o -name 'Thumbs.db' -o -name '*.log' -o -name '*.map' \) -print -quit | grep -q .; then
	echo "Release package contains a development or operating-system artifact." >&2
	exit 1
fi

if ! command -v zip >/dev/null 2>&1; then
	echo "The zip command is required to build the Briite release package." >&2
	exit 1
fi

if ! command -v unzip >/dev/null 2>&1; then
	echo "The unzip command is required to verify the Briite release package." >&2
	exit 1
fi

(
	cd "${DIST_DIR}"
	zip -q -r "$(basename "${ARCHIVE_PATH}")" briite
)

PACKAGE_LIST="$(unzip -Z1 "${ARCHIVE_PATH}")"

for required_path in \
	'briite/style.css' \
	'briite/functions.php' \
	'briite/inc/legacy-compat.php' \
	'briite/index.php' \
	'briite/readme.txt' \
	'briite/LICENSE.txt' \
	'briite/screenshot.png' \
	'briite/css/bootstrap-3.3.7.css' \
	'briite/css/bootstrap-3.3.7.min.css' \
	'briite/css/theme.css' \
	'briite/js/theme.js'; do
	if ! grep -qxF "${required_path}" <<< "${PACKAGE_LIST}"; then
		echo "Release archive is missing required path: ${required_path}" >&2
		exit 1
	fi
done

if grep -qxF 'briite/inc/profile.php' <<< "${PACKAGE_LIST}"; then
	echo "Release archive contains retired inc/profile.php." >&2
	exit 1
fi

FORBIDDEN_PATTERN='^briite/(\.git|\.github|tools|sass|layouts|vendor|dist)(/|$)|^briite/(composer\.json|composer\.lock|phpcs\.xml\.dist|README\.md|\.gitignore|\.gitattributes)$|\.map$'
if grep -Eq "${FORBIDDEN_PATTERN}" <<< "${PACKAGE_LIST}"; then
	echo "Release archive contains development-only files:" >&2
	grep -E "${FORBIDDEN_PATTERN}" <<< "${PACKAGE_LIST}" >&2
	exit 1
fi

if command -v sha256sum >/dev/null 2>&1; then
	(
		cd "${DIST_DIR}"
		sha256sum "$(basename "${ARCHIVE_PATH}")" > "$(basename "${CHECKSUM_PATH}")"
	)
elif command -v shasum >/dev/null 2>&1; then
	(
		cd "${DIST_DIR}"
		shasum -a 256 "$(basename "${ARCHIVE_PATH}")" > "$(basename "${CHECKSUM_PATH}")"
	)
else
	echo "A SHA-256 checksum utility is required." >&2
	exit 1
fi

rm -rf "${STAGE_DIR}"

printf 'Built %s\n' "${ARCHIVE_PATH}"
printf 'Checksum %s\n' "${CHECKSUM_PATH}"
