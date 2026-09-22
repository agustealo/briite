#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
DIST_DIR="${ROOT_DIR}/dist"
VERSION="$(awk -F': ' '/^Version:/ { print $2; exit }' "${ROOT_DIR}/style.css" | tr -d '\r')"

if [[ -z "${VERSION}" ]]; then
	echo "Unable to determine Briite version from style.css." >&2
	exit 1
fi

CONSUMER_ARCHIVE="${DIST_DIR}/briite-${VERSION}.zip"
WORDPRESS_ORG_ARCHIVE="${DIST_DIR}/briite-${VERSION}-wordpress-org.zip"
WORDPRESS_ORG_CHECKSUM="${WORDPRESS_ORG_ARCHIVE}.sha256"
STAGE_ROOT="${DIST_DIR}/.wordpress-org-stage"
STAGE_DIR="${STAGE_ROOT}/briite"

bash "${ROOT_DIR}/tools/build-release.sh"

if [[ ! -f "${CONSUMER_ARCHIVE}" ]]; then
	echo "Consumer release archive was not created." >&2
	exit 1
fi

rm -rf "${STAGE_ROOT}"
mkdir -p "${STAGE_ROOT}"
unzip -q "${CONSUMER_ARCHIVE}" -d "${STAGE_ROOT}"

if [[ ! -d "${STAGE_DIR}" ]]; then
	echo "Consumer release did not contain the expected briite/ root." >&2
	exit 1
fi

STAGE_DIR="${STAGE_DIR}" php <<'PHP'
<?php
$stage_dir = getenv( 'STAGE_DIR' );

if ( false === $stage_dir || '' === $stage_dir ) {
	fwrite( STDERR, "Missing WordPress.org staging directory.\n" );
	exit( 1 );
}

$strip_glyphicons = static function ( string $path, string $start_marker, string $end_marker ): void {
	$css = file_get_contents( $path );

	if ( false === $css ) {
		fwrite( STDERR, "Unable to read Bootstrap stylesheet: {$path}\n" );
		exit( 1 );
	}

	$start = strpos( $css, $start_marker );
	if ( false === $start ) {
		fwrite( STDERR, "Unable to locate the Glyphicon block start in {$path}.\n" );
		exit( 1 );
	}

	$end = strpos( $css, $end_marker, $start );
	if ( false === $end ) {
		fwrite( STDERR, "Unable to locate the Glyphicon block end in {$path}.\n" );
		exit( 1 );
	}

	$sanitized = substr( $css, 0, $start ) . substr( $css, $end );

	foreach ( array( 'Glyphicons Halflings', 'glyphicons-halflings-regular', '.glyphicon' ) as $forbidden ) {
		if ( false !== strpos( $sanitized, $forbidden ) ) {
			fwrite( STDERR, "Glyphicon residue remains in {$path}: {$forbidden}\n" );
			exit( 1 );
		}
	}

	if ( false === file_put_contents( $path, $sanitized ) ) {
		fwrite( STDERR, "Unable to write sanitized Bootstrap stylesheet: {$path}\n" );
		exit( 1 );
	}
};

$strip_glyphicons(
	$stage_dir . '/css/bootstrap-3.3.7.css',
	"@font-face {\n  font-family: 'Glyphicons Halflings';",
	"* {\n  -webkit-box-sizing: border-box;"
);

$strip_glyphicons(
	$stage_dir . '/css/bootstrap-3.3.7.min.css',
	"@font-face{font-family:'Glyphicons Halflings';",
	' *{-webkit-box-sizing:border-box'
);
PHP

for glyphicons_file in \
	glyphicons-halflings-regular.eot \
	glyphicons-halflings-regular.svg \
	glyphicons-halflings-regular.ttf \
	glyphicons-halflings-regular.woff \
	glyphicons-halflings-regular.woff2; do
	if [[ ! -f "${STAGE_DIR}/fonts/${glyphicons_file}" ]]; then
		echo "Expected Glyphicon compatibility file is missing before WordPress.org filtering: ${glyphicons_file}" >&2
		exit 1
	fi
	rm "${STAGE_DIR}/fonts/${glyphicons_file}"
done

STAGE_DIR="${STAGE_DIR}" php <<'PHP'
<?php
$stage_dir = getenv( 'STAGE_DIR' );
$readme    = $stage_dir . '/readme.txt';
$content   = file_get_contents( $readme );

if ( false === $content ) {
	fwrite( STDERR, "Unable to read staged readme.txt.\n" );
	exit( 1 );
}

$resource_block = <<<'TEXT'
Glyphicons Halflings font files are bundled as part of the Bootstrap 3.3.7 distribution.
Copyright: Jan Kovarik.
Source: https://getbootstrap.com/docs/3.3/components/#glyphicons
Bootstrap documents the Halflings set as made available for Bootstrap use without cost, with attribution requested when practical. Briite preserves the files for compatibility with existing Bootstrap-based child-theme markup.
TEXT;

$replacement = <<<'TEXT'
WordPress.org release profile note:
The WordPress.org package omits Bootstrap 3.3.7 Glyphicons Halflings font files and removes the matching Glyphicon CSS block. Briite's own templates do not use Glyphicon classes. The normal Briite consumer package retains that historical Bootstrap compatibility surface for downstream child themes.
TEXT;

if ( false === strpos( $content, $resource_block ) ) {
	fwrite( STDERR, "Unable to locate the expected Glyphicons resource block in readme.txt.\n" );
	exit( 1 );
}

$content = str_replace( $resource_block, $replacement, $content );

if ( false === file_put_contents( $readme, $content ) ) {
	fwrite( STDERR, "Unable to write WordPress.org readme.txt.\n" );
	exit( 1 );
}
PHP

if find "${STAGE_DIR}/fonts" -maxdepth 1 -type f -name 'glyphicons-halflings-regular.*' -print -quit | grep -q .; then
	echo "WordPress.org package still contains Glyphicon font files." >&2
	exit 1
fi

for stylesheet in \
	"${STAGE_DIR}/css/bootstrap-3.3.7.css" \
	"${STAGE_DIR}/css/bootstrap-3.3.7.min.css"; do
	if grep -Eq 'Glyphicons Halflings|glyphicons-halflings-regular|\.glyphicon' "${stylesheet}"; then
		echo "WordPress.org Bootstrap stylesheet still contains Glyphicon CSS: ${stylesheet}" >&2
		exit 1
	fi
done

if ! grep -q 'Raleway' "${STAGE_DIR}/css/fonts.css"; then
	echo "WordPress.org package lost Briite's Raleway font configuration." >&2
	exit 1
fi

rm -f "${WORDPRESS_ORG_ARCHIVE}" "${WORDPRESS_ORG_CHECKSUM}"
(
	cd "${STAGE_ROOT}"
	zip -q -r "${WORDPRESS_ORG_ARCHIVE}" briite
)

PACKAGE_LIST="$(unzip -Z1 "${WORDPRESS_ORG_ARCHIVE}")"

for required_path in \
	'briite/style.css' \
	'briite/functions.php' \
	'briite/readme.txt' \
	'briite/css/bootstrap-3.3.7.css' \
	'briite/css/bootstrap-3.3.7.min.css' \
	'briite/css/fonts.css' \
	'briite/fonts/raleway-regular.woff' \
	'briite/js/theme.js'; do
	if ! grep -qxF "${required_path}" <<< "${PACKAGE_LIST}"; then
		echo "WordPress.org release archive is missing required path: ${required_path}" >&2
		exit 1
	fi
done

if grep -Eq 'glyphicons-halflings-regular\.(eot|svg|ttf|woff|woff2)$' <<< "${PACKAGE_LIST}"; then
	echo "WordPress.org release archive contains a Glyphicon font file." >&2
	exit 1
fi

if unzip -p "${WORDPRESS_ORG_ARCHIVE}" briite/css/bootstrap-3.3.7.css | grep -Eq 'Glyphicons Halflings|glyphicons-halflings-regular|\.glyphicon'; then
	echo "WordPress.org unminified Bootstrap CSS contains Glyphicon residue." >&2
	exit 1
fi

if unzip -p "${WORDPRESS_ORG_ARCHIVE}" briite/css/bootstrap-3.3.7.min.css | grep -Eq 'Glyphicons Halflings|glyphicons-halflings-regular|\.glyphicon'; then
	echo "WordPress.org minified Bootstrap CSS contains Glyphicon residue." >&2
	exit 1
fi

if command -v sha256sum >/dev/null 2>&1; then
	(
		cd "${DIST_DIR}"
		sha256sum "$(basename "${WORDPRESS_ORG_ARCHIVE}")" > "$(basename "${WORDPRESS_ORG_CHECKSUM}")"
	)
elif command -v shasum >/dev/null 2>&1; then
	(
		cd "${DIST_DIR}"
		shasum -a 256 "$(basename "${WORDPRESS_ORG_ARCHIVE}")" > "$(basename "${WORDPRESS_ORG_CHECKSUM}")"
	)
else
	echo "A SHA-256 checksum utility is required." >&2
	exit 1
fi

rm -rf "${STAGE_ROOT}"

printf 'Built %s\n' "${WORDPRESS_ORG_ARCHIVE}"
printf 'Checksum %s\n' "${WORDPRESS_ORG_CHECKSUM}"
