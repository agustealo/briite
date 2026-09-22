<?php
/**
 * Briite theme functions and definitions.
 *
 * @package kriate
 */

/**
 * Historical generator callback retained as an inert compatibility shim.
 *
 * Briite no longer suppresses WordPress generator output. Themes should not
 * own non-presentational generator policy, so core or site-level code remains
 * authoritative.
 *
 * @param string $generator Generator output supplied by WordPress.
 * @return string
 */
function complete_version_removal( $generator = '' ) {
	return $generator;
}

/**
 * Set the content width based on the theme design.
 */
if ( ! isset( $content_width ) ) {
	$content_width = 640;
}

if ( ! function_exists( 'kriate_setup' ) ) :
	/**
	 * Set up theme defaults and register support for WordPress features.
	 */
	function kriate_setup() {
		load_theme_textdomain( 'kriate', get_template_directory() . '/languages' );

		add_theme_support( 'automatic-feed-links' );
		add_theme_support( 'title-tag' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'responsive-embeds' );
		add_theme_support( 'editor-styles' );

		add_editor_style(
			array(
				'css/fonts.css',
				'css/editor.css',
			)
		);

		add_image_size( 'single-banner', 1300, 500, array( 'center', 'center' ) );
		add_image_size( 'grid-thumb', 450, 450, array( 'center', 'center' ) );

		register_nav_menus(
			array(
				'primary' => __( 'Primary Menu', 'kriate' ),
			)
		);

		add_theme_support(
			'html5',
			array(
				'search-form',
				'comment-form',
				'comment-list',
				'gallery',
				'caption',
				'script',
				'style',
			)
		);

		add_theme_support(
			'post-formats',
			array(
				'aside',
				'image',
				'video',
				'quote',
				'link',
			)
		);

		add_theme_support(
			'custom-background',
			apply_filters(
				'kriate_custom_background_args',
				array(
					'default-color' => 'ffffff',
					'default-image' => '',
				)
			)
		);
	}
endif;
add_action( 'after_setup_theme', 'kriate_setup' );

/**
 * Historical JPEG quality callback retained for child-theme compatibility.
 *
 * Briite no longer registers this callback globally. WordPress core now owns
 * image-editor quality defaults and can apply MIME- and size-aware policy.
 *
 * @param int    $quality   Image quality.
 * @param string $mime_type Image MIME type.
 * @return int
 */
function smashing_jpeg_quality( $quality, $mime_type = '' ) {
	if ( 'image/jpeg' === $mime_type || '' === $mime_type ) {
		return 100;
	}

	return $quality;
}

/**
 * Add the existing Thumbnail column to post and page list tables.
 *
 * This function name is preserved for backwards compatibility.
 *
 * @param array $columns Existing admin columns.
 * @return array
 */
function kriate_Thumbnail_Column( $columns ) {
	$columns['thumbnail'] = __( 'Thumbnail', 'kriate' );
	return $columns;
}

/**
 * Render a thumbnail value for post and page list tables.
 *
 * @param string $column_name Current column name.
 * @param int    $post_id     Current post ID.
 * @return void
 */
function kriate_render_thumbnail_column( $column_name, $post_id ) {
	if ( 'thumbnail' !== $column_name ) {
		return;
	}

	$thumbnail_id = get_post_thumbnail_id( $post_id );

	if ( $thumbnail_id ) {
		echo wp_kses_post( wp_get_attachment_image( $thumbnail_id, array( 35, 35 ), true ) );
		return;
	}

	$attachments = get_children(
		array(
			'post_parent'    => $post_id,
			'post_type'      => 'attachment',
			'post_mime_type' => 'image',
			'numberposts'    => 1,
			'orderby'        => 'menu_order ID',
			'order'          => 'ASC',
		)
	);

	if ( $attachments ) {
		$attachment = reset( $attachments );
		if ( $attachment instanceof WP_Post ) {
			echo wp_kses_post( wp_get_attachment_image( $attachment->ID, array( 35, 35 ), true ) );
			return;
		}
	}

	echo esc_html__( 'None', 'kriate' );
}

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedFunctionFound -- Public callback retained for backwards compatibility.
/**
 * Historical post-column callback retained for compatibility.
 *
 * @param string $column_name Current column name.
 * @param int    $post_id     Current post ID.
 * @return void
 */
function fb_AddThumbValue( $column_name, $post_id ) {
	kriate_render_thumbnail_column( $column_name, $post_id );
}
// phpcs:enable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedFunctionFound

/**
 * Historical page-column callback retained for compatibility.
 *
 * @param string $column_name Current column name.
 * @param int    $post_id     Current post ID.
 * @return void
 */
function kriate_AddThumbValue( $column_name, $post_id ) {
	kriate_render_thumbnail_column( $column_name, $post_id );
}

add_filter( 'manage_posts_columns', 'kriate_Thumbnail_Column' );
add_action( 'manage_posts_custom_column', 'fb_AddThumbValue', 10, 2 );
add_filter( 'manage_pages_columns', 'kriate_Thumbnail_Column' );
add_action( 'manage_pages_custom_column', 'kriate_AddThumbValue', 10, 2 );

/**
 * Register the existing footer widget areas.
 *
 * Widget IDs are intentionally unchanged so existing user configuration remains valid.
 */
function kriate_widgets_init() {
	$sidebars = array(
		'bottom-widget-1' => array(
			'name'        => __( 'Widget 1', 'kriate' ),
			'description' => __( 'Located at the bottom of your theme', 'kriate' ),
		),
		'bottom-widget-2' => array(
			'name'        => __( 'Widget 2', 'kriate' ),
			'description' => __( 'The second located at the bottom of your theme', 'kriate' ),
		),
		'bottom-widget-3' => array(
			'name'        => __( 'Widget 3', 'kriate' ),
			'description' => __( 'The third widget located at the bottom of your theme', 'kriate' ),
		),
		'bottom-widget-4' => array(
			'name'        => __( 'Widget 4', 'kriate' ),
			'description' => __( 'The fourth widget located at the bottom of your theme', 'kriate' ),
		),
	);

	foreach ( $sidebars as $id => $sidebar ) {
		register_sidebar(
			array(
				'name'          => $sidebar['name'],
				'description'   => $sidebar['description'],
				'id'            => $id,
				'before_widget' => '<div id="%1$s" class="widget %2$s">',
				'after_widget'  => '</div>',
				'before_title'  => '<h3 class="widget-title">',
				'after_title'   => '</h3>',
			)
		);
	}
}
add_action( 'widgets_init', 'kriate_widgets_init' );

/**
 * Enqueue front-end styles and scripts through WordPress.
 */
function kriate_scripts() {
	$theme_version = wp_get_theme()->get( 'Version' );

	wp_enqueue_style( 'wp-style', get_stylesheet_uri(), array(), $theme_version );
	wp_enqueue_style( 'bootstrap', get_template_directory_uri() . '/css/bootstrap-3.3.7.min.css', array(), '3.3.7' );
	wp_enqueue_style( 'kriate-fonts', get_template_directory_uri() . '/css/fonts.css', array(), $theme_version );
	wp_enqueue_style( 'theme-style', get_template_directory_uri() . '/css/theme.css', array( 'bootstrap', 'wp-style', 'kriate-fonts' ), $theme_version );
	wp_enqueue_style( 'kriate-compat', get_template_directory_uri() . '/css/compat.css', array( 'theme-style' ), $theme_version );

	wp_enqueue_script(
		'theme-js',
		get_template_directory_uri() . '/js/theme.js',
		array( 'jquery' ),
		$theme_version,
		true
	);

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'kriate_scripts' );

require get_template_directory() . '/inc/custom-header.php';
require get_template_directory() . '/inc/template-tags.php';
require get_template_directory() . '/inc/extras.php';
require get_template_directory() . '/inc/customizer.php';
require get_template_directory() . '/inc/jetpack.php';
require get_template_directory() . '/inc/profile.php';
