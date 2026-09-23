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

LEGACY_COMPAT_FILE="${STAGE_DIR}/inc/legacy-compat.php"
if [[ ! -f "${LEGACY_COMPAT_FILE}" ]]; then
	echo "Consumer release is missing the expected legacy compatibility module before WordPress.org filtering." >&2
	exit 1
fi
rm "${LEGACY_COMPAT_FILE}"

if [[ -f "${LEGACY_COMPAT_FILE}" ]]; then
	echo "WordPress.org staging still contains the consumer-only legacy compatibility module." >&2
	exit 1
fi

if [[ -f "${STAGE_DIR}/inc/profile.php" ]]; then
	echo "WordPress.org staging contains retired inc/profile.php." >&2
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
		fwrite( STDERR, "Unable to read Bootstrap stylesheet: {$path}.\n" );
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

	foreach ( array( 'Glyphicons Halflings', 'glyphicons-halflings-regular' ) as $forbidden ) {
		if ( false !== strpos( $sanitized, $forbidden ) ) {
			fwrite( STDERR, "Glyphicon font residue remains in {$path}: {$forbidden}\n" );
			exit( 1 );
		}
	}

	if ( false === file_put_contents( $path, $sanitized ) ) {
		fwrite( STDERR, "Unable to write sanitized Bootstrap stylesheet: {$path}.\n" );
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
	'*{-webkit-box-sizing:border-box'
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

$start_marker = 'Glyphicons Halflings font files are bundled';
$end_marker   = 'Raleway font files,';
$start        = strpos( $content, $start_marker );

if ( false === $start ) {
	fwrite( STDERR, "Unable to locate the Glyphicons resource section in readme.txt.\n" );
	exit( 1 );
}

$end = strpos( $content, $end_marker, $start );
if ( false === $end ) {
	fwrite( STDERR, "Unable to locate the resource section following Glyphicons in readme.txt.\n" );
	exit( 1 );
}

$replacement = <<<'TEXT'
WordPress.org release profile note:
The WordPress.org package does not bundle Bootstrap 3.3.7 Glyphicons Halflings font files. Briite's own templates do not use Glyphicon classes. The normal Briite consumer package retains that historical Bootstrap compatibility surface for downstream child themes.

TEXT;

$content = substr( $content, 0, $start ) . $replacement . substr( $content, $end );

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
	if grep -Eq 'Glyphicons Halflings|glyphicons-halflings-regular' "${stylesheet}"; then
		echo "WordPress.org Bootstrap stylesheet still contains Glyphicon font references: ${stylesheet}" >&2
		exit 1
	fi
done

if grep -R -E -n \
	--include='*.css' \
	--include='*.scss' \
	'Glyphicons Halflings|glyphicons-halflings-regular|FontAwesome' \
	"${STAGE_DIR}"; then
	echo "WordPress.org package contains a theme-owned dependency on a removed or unbundled icon font." >&2
	exit 1
fi

if ! grep -q 'Raleway' "${STAGE_DIR}/css/fonts.css"; then
	echo "WordPress.org package lost Briite's Raleway font configuration." >&2
	exit 1
fi

php "${ROOT_DIR}/tools/check-theme-scope-contracts.php" wordpress-org "${STAGE_DIR}"

STAGE_FUNCTIONS_FILE="${STAGE_DIR}/functions.php"
STAGE_FUNCTIONS_FILE="${STAGE_FUNCTIONS_FILE}" php <<'PHP'
<?php
$functions_path = getenv( 'STAGE_FUNCTIONS_FILE' );

if ( false === $functions_path || '' === $functions_path ) {
	fwrite( STDERR, "Missing staged functions.php path.\n" );
	exit( 1 );
}

$content = file_get_contents( $functions_path );

if ( false === $content ) {
	fwrite( STDERR, "Unable to read staged functions.php.\n" );
	exit( 1 );
}

$legacy_loader = <<<'PHP_CODE'
$kriate_legacy_compatibility_file = get_template_directory() . '/inc/legacy-compat.php';
if ( is_readable( $kriate_legacy_compatibility_file ) ) {
	require $kriate_legacy_compatibility_file;
}
unset( $kriate_legacy_compatibility_file );
PHP_CODE;

if ( 1 !== substr_count( $content, $legacy_loader ) ) {
	fwrite( STDERR, "Unable to locate exactly one consumer-only legacy compatibility loader in staged functions.php.\n" );
	exit( 1 );
}

$content = str_replace( "\n" . $legacy_loader . "\n", "\n", $content, $replacement_count );

if ( 1 !== $replacement_count || false !== strpos( $content, 'legacy-compat.php' ) ) {
	fwrite( STDERR, "WordPress.org staged functions.php still references the consumer-only legacy compatibility module.\n" );
	exit( 1 );
}

if ( false === file_put_contents( $functions_path, $content ) ) {
	fwrite( STDERR, "Unable to write filtered staged functions.php.\n" );
	exit( 1 );
}
PHP

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

for forbidden_path in \
	'briite/inc/legacy-compat.php' \
	'briite/inc/profile.php'; do
	if grep -qxF "${forbidden_path}" <<< "${PACKAGE_LIST}"; then
		echo "WordPress.org release archive contains forbidden compatibility path: ${forbidden_path}" >&2
		exit 1
	fi
done

if unzip -p "${WORDPRESS_ORG_ARCHIVE}" briite/functions.php | grep -qF 'legacy-compat.php'; then
	echo "WordPress.org release functions.php still references the excluded consumer-only legacy compatibility module." >&2
	exit 1
fi

if grep -Eq 'glyphicons-halflings-regular\.(eot|svg|ttf|woff|woff2)$' <<< "${PACKAGE_LIST}"; then
	echo "WordPress.org release archive contains a Glyphicon font file." >&2
	exit 1
fi

if unzip -p "${WORDPRESS_ORG_ARCHIVE}" briite/css/bootstrap-3.3.7.css | grep -Eq 'Glyphicons Halflings|glyphicons-halflings-regular'; then
	echo "WordPress.org unminified Bootstrap CSS contains Glyphicon font references." >&2
	exit 1
fi

if unzip -p "${WORDPRESS_ORG_ARCHIVE}" briite/css/bootstrap-3.3.7.min.css | grep -Eq 'Glyphicons Halflings|glyphicons-halflings-regular'; then
	echo "WordPress.org minified Bootstrap CSS contains Glyphicon font references." >&2
	exit 1
fi

WORDPRESS_ORG_README="$(unzip -p "${WORDPRESS_ORG_ARCHIVE}" briite/readme.txt)"

if ! grep -qF 'WordPress.org package does not bundle Bootstrap 3.3.7 Glyphicons Halflings font files' <<< "${WORDPRESS_ORG_README}"; then
	echo "WordPress.org release readme does not describe the package-specific font boundary." >&2
	exit 1
fi

if ! grep -qF 'The normal consumer package additionally preserves two historical downstream compatibility surfaces' <<< "${WORDPRESS_ORG_README}"; then
	echo "WordPress.org release readme does not describe the consumer compatibility surfaces." >&2
	exit 1
fi

if ! grep -qF 'The WordPress.org package excludes those two consumer-only compatibility surfaces' <<< "${WORDPRESS_ORG_README}"; then
	echo "WordPress.org release readme does not describe the directory compatibility boundary." >&2
	exit 1
fi

if ! grep -qF 'The WordPress.org package excludes those generic aliases to meet the directory public-namespace requirement' <<< "${WORDPRESS_ORG_README}"; then
	echo "WordPress.org release readme does not describe the generic callback alias boundary." >&2
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
