#!/usr/bin/env bash
set -euo pipefail

WP_PATH="${WP_PATH:-/tmp/wordpress}"
SITE_URL="${BRIITE_BASE_URL:-http://127.0.0.1:8080}"
FIXTURE_DIR="${BRIITE_FIXTURE_DIR:-/tmp/briite-doc-fixtures}"
ROUTES_FILE="${BRIITE_ROUTES_FILE:-/tmp/briite-doc-routes.env}"

for command_name in wp convert; do
	if ! command -v "${command_name}" >/dev/null 2>&1; then
		echo "Required command is unavailable: ${command_name}" >&2
		exit 1
	fi
done

if [[ ! -f "${WP_PATH}/wp-load.php" ]]; then
	echo "WordPress is not installed at ${WP_PATH}." >&2
	exit 1
fi

rm -rf "${FIXTURE_DIR}"
mkdir -p "${FIXTURE_DIR}"

wp option update blogname 'Briite Studio' --path="${WP_PATH}" >/dev/null
wp option update blogdescription 'Selected work, essays, and visual experiments.' --path="${WP_PATH}" >/dev/null
wp option update posts_per_page 6 --path="${WP_PATH}" >/dev/null
wp option update show_on_front posts --path="${WP_PATH}" >/dev/null
wp option update thread_comments 1 --path="${WP_PATH}" >/dev/null

EXISTING_CONTENT_IDS="$(wp post list \
	--path="${WP_PATH}" \
	--post_type=post,page,attachment \
	--post_status=any \
	--format=ids)"
if [[ -n "${EXISTING_CONTENT_IDS}" ]]; then
	wp post delete ${EXISTING_CONTENT_IDS} --force --path="${WP_PATH}" >/dev/null
fi

while IFS= read -r menu_id; do
	if [[ -n "${menu_id}" ]]; then
		wp menu delete "${menu_id}" --path="${WP_PATH}" >/dev/null
	fi
done < <(wp menu list --path="${WP_PATH}" --field=term_id 2>/dev/null || true)

create_artwork() {
	local output="$1"
	local start_color="$2"
	local end_color="$3"
	local accent_color="$4"

	# Keep fixture artwork intentionally typeless. Briite's real HTML/CSS owns
	# project titles and captions, and the abstract composition survives both
	# the square grid crop and the wide single-post hero crop without clipping.
	convert \
		-size 1600x1000 \
		gradient:"${start_color}-${end_color}" \
		-fill "${accent_color}" \
		-draw 'polygon 1100,0 1600,0 1600,1000 1280,1000 980,540' \
		-fill 'rgba(255,255,255,0.10)' \
		-draw 'circle 800,500 1130,500' \
		-stroke 'rgba(255,255,255,0.68)' \
		-strokewidth 4 \
		-fill none \
		-draw 'rectangle 610,310 990,690' \
		-strokewidth 2 \
		-draw 'line 800,180 800,820' \
		-draw 'line 480,500 1120,500' \
		"${output}"
}

create_project() {
	local title="$1"
	local slug="$2"
	local date="$3"
	local excerpt="$4"
	local content="$5"
	local start_color="$6"
	local end_color="$7"
	local accent_color="$8"
	local category_id="$9"

	local image_path="${FIXTURE_DIR}/${slug}.png"
	create_artwork "${image_path}" "${start_color}" "${end_color}" "${accent_color}"

	local post_id
	post_id="$(wp post create \
		--path="${WP_PATH}" \
		--post_type=post \
		--post_status=publish \
		--post_date="${date}" \
		--post_name="${slug}" \
		--post_title="${title}" \
		--post_excerpt="${excerpt}" \
		--post_content="${content}" \
		--porcelain)"

	wp post term add "${post_id}" category "${category_id}" --path="${WP_PATH}" >/dev/null

	local attachment_id
	attachment_id="$(wp media import "${image_path}" \
		--path="${WP_PATH}" \
		--post_id="${post_id}" \
		--title="${title}" \
		--alt="${title}" \
		--porcelain)"
	wp post meta set "${post_id}" _thumbnail_id "${attachment_id}" --path="${WP_PATH}" >/dev/null

	printf '%s\n' "${post_id}"
}

PORTFOLIO_CATEGORY_ID="$(wp term create category 'Portfolio' \
	--path="${WP_PATH}" \
	--slug=portfolio \
	--description='Selected Briite project work.' \
	--porcelain)"
JOURNAL_CATEGORY_ID="$(wp term create category 'Journal' \
	--path="${WP_PATH}" \
	--slug=journal \
	--description='Process notes and studio observations.' \
	--porcelain)"

ABOUT_PAGE_ID="$(wp post create \
	--path="${WP_PATH}" \
	--post_type=page \
	--post_status=publish \
	--post_name=about \
	--post_title='About Briite Studio' \
	--post_content='<p>Briite is a compact portfolio and editorial theme built around strong images, quiet typography, and a fixed navigation rail.</p><h2>Designed to stay familiar</h2><p>The 1.1 release keeps the original Briite personality while updating compatibility, accessibility, and runtime behavior for current WordPress.</p>' \
	--porcelain)"

PROJECT_ONE_ID="$(create_project \
	'Editorial Systems' \
	'editorial-systems' \
	'2026-08-18 09:00:00' \
	'A modular editorial identity built around rhythm, hierarchy, and reusable visual rules.' \
	'<p class="lead">A study in editorial rhythm, reusable systems, and image-led storytelling.</p><h2>One visual language, many surfaces</h2><p>The system balances large-format imagery with concise typography so each story has room to breathe while the overall portfolio remains unmistakably connected.</p><blockquote><p>Consistency should create freedom, not sameness.</p></blockquote><p>Reusable spacing, type, and image rules keep the presentation coherent across project notes, case studies, and long-form writing.</p>' \
	'#0b132b' '#31587a' '#182d47' \
	"${PORTFOLIO_CATEGORY_ID}")"

PROJECT_TWO_ID="$(create_project \
	'Northline Identity' \
	'northline-identity' \
	'2026-08-10 09:00:00' \
	'A restrained identity study pairing geometric structure with warm editorial texture.' \
	'<p>Northline explores a visual identity that can move from compact digital layouts to broad campaign surfaces without losing its center.</p><h2>Structure first</h2><p>A limited system of type, scale, and negative space gives imagery the leading role while navigation remains direct and familiar.</p>' \
	'#1f2937' '#8b5e3c' '#5d3d29' \
	"${PORTFOLIO_CATEGORY_ID}")"

PROJECT_THREE_ID="$(create_project \
	'Object / Space' \
	'object-space' \
	'2026-08-02 09:00:00' \
	'An image-first collection studying product form, proportion, and negative space.' \
	'<p>Object / Space uses Briite as a quiet frame for a sequence of visual studies.</p><p>The portfolio grid provides a fast overview, while the single-project view expands each study into a broader narrative surface.</p>' \
	'#2d3748' '#557c83' '#34545b' \
	"${PORTFOLIO_CATEGORY_ID}")"

PROJECT_FOUR_ID="$(create_project \
	'Type Studies' \
	'type-studies' \
	'2026-07-26 09:00:00' \
	'Experiments in scale and pacing for expressive but readable digital typography.' \
	'<p>Type Studies examines how a compact set of typographic decisions can carry a portfolio across image-heavy and text-heavy work.</p><h2>Readable by design</h2><p>The goal is not decoration. It is a dependable rhythm that gives every project a clear entry point.</p>' \
	'#171717' '#5b4b75' '#3a3150' \
	"${PORTFOLIO_CATEGORY_ID}")"

PROJECT_FIVE_ID="$(create_project \
	'Field Notes' \
	'field-notes' \
	'2026-07-18 09:00:00' \
	'Short studio observations collected between larger project releases.' \
	'<p>Field Notes keeps smaller observations close to the main body of work without requiring a second publishing system.</p><p>Briite supports the same WordPress content model for concise notes and larger case studies.</p>' \
	'#263238' '#65786f' '#43534c' \
	"${JOURNAL_CATEGORY_ID}")"

PROJECT_SIX_ID="$(create_project \
	'Quiet Interfaces' \
	'quiet-interfaces' \
	'2026-07-10 09:00:00' \
	'A set of interface studies focused on clarity, restraint, and durable interaction patterns.' \
	'<p>Quiet Interfaces treats navigation and interaction as supporting structure rather than spectacle.</p><p>That principle fits Briite itself: strong imagery, familiar controls, and just enough interface to move through the work.</p>' \
	'#121826' '#4f6675' '#314653' \
	"${PORTFOLIO_CATEGORY_ID}")"

wp post term add "${PROJECT_ONE_ID}" post_tag 'identity' --by=slug --path="${WP_PATH}" >/dev/null 2>&1 || \
	wp term create post_tag 'identity' --slug=identity --path="${WP_PATH}" >/dev/null
wp post term add "${PROJECT_ONE_ID}" post_tag identity --path="${WP_PATH}" >/dev/null

MENU_ID="$(wp menu create 'Briite Primary' --path="${WP_PATH}" --porcelain)"
wp menu item add-custom "${MENU_ID}" 'Work' "${SITE_URL}/" --path="${WP_PATH}" >/dev/null
wp menu item add-post "${MENU_ID}" "${ABOUT_PAGE_ID}" --title='About' --path="${WP_PATH}" >/dev/null
JOURNAL_MENU_ITEM_ID="$(wp menu item add-term "${MENU_ID}" category "${JOURNAL_CATEGORY_ID}" \
	--title='Journal' \
	--path="${WP_PATH}" \
	--porcelain)"
wp menu item add-custom "${MENU_ID}" 'Field Notes' "${SITE_URL}/?p=${PROJECT_FIVE_ID}" \
	--parent-id="${JOURNAL_MENU_ITEM_ID}" \
	--path="${WP_PATH}" >/dev/null
wp menu location assign "${MENU_ID}" primary --path="${WP_PATH}" >/dev/null

wp widget add text bottom-widget-1 \
	--path="${WP_PATH}" \
	--title='Briite Studio' \
	--text='Image-forward work, essays, and project notes in a compact WordPress portfolio.' >/dev/null
wp widget add recent-posts bottom-widget-2 \
	--path="${WP_PATH}" \
	--title='Recent Work' \
	--number=3 >/dev/null
wp widget add categories bottom-widget-3 \
	--path="${WP_PATH}" \
	--title='Browse' \
	--count=0 \
	--hierarchical=1 >/dev/null
wp widget add text bottom-widget-4 \
	--path="${WP_PATH}" \
	--title='Colophon' \
	--text='Briite 1.1.1 · WordPress 7.1 · Real runtime documentation fixture.' >/dev/null

cat > "${ROUTES_FILE}" <<EOF
export BRIITE_BASE_URL='${SITE_URL}'
export BRIITE_FEATURED_POST_ID='${PROJECT_ONE_ID}'
export BRIITE_ABOUT_PAGE_ID='${ABOUT_PAGE_ID}'
export BRIITE_PORTFOLIO_CATEGORY_ID='${PORTFOLIO_CATEGORY_ID}'
EOF

printf 'Seeded Briite documentation runtime at %s\n' "${SITE_URL}"
printf 'Featured post ID: %s\n' "${PROJECT_ONE_ID}"
printf 'Routes file: %s\n' "${ROUTES_FILE}"
