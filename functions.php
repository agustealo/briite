<?php
/**
 * kriate functions and definitions
 *
 * @package kriate
 */
 // remove version info from head and feeds
function complete_version_removal() {
	return '';
}
add_filter('the_generator', 'complete_version_removal');
/**
 * Set the content width based on the theme's design and stylesheet.
 */
if ( ! isset( $content_width ) ) {
	$content_width = 640; /* pixels */
}

if ( ! function_exists( 'kriate_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function kriate_setup() {

	/*
	 * Make theme available for translation.
	 * Translations can be filed in the /languages/ directory.
	 * If you're building a theme based on kriate, use a find and replace
	 * to change 'kriate' to the name of your theme in all the template files
	 */
	load_theme_textdomain( 'kriate', get_template_directory() . '/languages' );
	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );
	/*
	 * Enable support for Post Thumbnails on posts and pages.
	 *
	 * @link http://codex.wordpress.org/Function_Reference/add_theme_support#Post_Thumbnails
	 */
	add_theme_support( 'post-thumbnails' );
	// Enable cropping.
	if ( function_exists( 'add_image_size' ) ) { 
	add_image_size( 'single-banner', 1300, 500, array( 'center', 'center' ) );
	add_image_size( 'grid-thumb', 450, 450, array( 'center', 'center' ) );
}
	// Fallback image support
	
	// Better image quality
	add_filter( 'jpeg_quality', 'smashing_jpeg_quality' );
	function smashing_jpeg_quality() {
	   return 100;
	}
	
	// Add featured thumb to post preview
	if ( !function_exists('kriate_Thumbnail_Column') && function_exists('add_theme_support') ) {
	
	// for post and page
	add_theme_support('post-thumbnails', array( 'post', 'page' ) );
	
	function kriate_Thumbnail_Column($cols) {
		
		$cols['thumbnail'] = __('Thumbnail');
		
		return $cols;
	}
	
	function fb_AddThumbValue($column_name, $post_id) {
			
			$width = (int) 35;
			$height = (int) 35;
			
			if ( 'thumbnail' == $column_name ) {
				// thumbnail of WP 2.9
				$thumbnail_id = get_post_meta( $post_id, '_thumbnail_id', true );
				// image from gallery
				$attachments = get_children( array('post_parent' => $post_id, 'post_type' => 'attachment', 'post_mime_type' => 'image') );
				if ($thumbnail_id)
					$thumb = wp_get_attachment_image( $thumbnail_id, array($width, $height), true );
				elseif ($attachments) {
					foreach ( $attachments as $attachment_id => $attachment ) {
						$thumb = wp_get_attachment_image( $attachment_id, array($width, $height), true );
					}
				}
					if ( isset($thumb) && $thumb ) {
						echo $thumb;
					} else {
						echo __('None');
					}
			}
	}
	
	// for posts
	add_filter( 'manage_posts_columns', 'kriate_Thumbnail_Column' );
	add_action( 'manage_posts_custom_column', 'fb_AddThumbValue', 10, 2 );
	
	// for pages
	add_filter( 'manage_pages_columns', 'kriate_Thumbnail_Column' );
	add_action( 'manage_pages_custom_column', 'kriate_AddThumbValue', 10, 2 );
}

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus( array(
		'primary' => __( 'Primary Menu', 'kriate' ),
	) );
	
	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support( 'html5', array(
		'search-form', 'comment-form', 'comment-list', 'gallery', 'caption'
	) );

	/*
	 * Enable support for Post Formats.
	 * See http://codex.wordpress.org/Post_Formats
	 */
	add_theme_support( 'post-formats', array(
		'aside', 'image', 'video', 'quote', 'link'
	) );

	// Setup the WordPress core custom background feature.
	add_theme_support( 'custom-background', apply_filters( 'kriate_custom_background_args', array(
		'default-color' => 'ffffff',
		'default-image' => '',
	) ) );
}
endif; // kriate_setup
add_action( 'after_setup_theme', 'kriate_setup' );

/**
 * Register widget area.
 *
 * @link http://codex.wordpress.org/Function_Reference/register_sidebar
 */
function kriate_widgets_init() {

    // Define Sidebar Widget Area 1
    register_sidebar(array(
        'name' => __('Widget 1', 'kriate'),
		'description' => __( 'Located at the bottom of your theme', 'kriate theme' ),
        'id' => 'bottom-widget-1',
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
		'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>'
    ));

    // Define Sidebar Widget Area 2
    register_sidebar(array(
        'name' => __('Widget 2', 'kriate'),
		'description' => __( 'The second located at the bottom of your theme', 'kriate theme' ),
        'id' => 'bottom-widget-2',
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
		'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>'
    ));

    // Define Sidebar Widget Area 3
    register_sidebar(array(
        'name' => __('Widget 3', 'kriate'),
		'description' => __( 'The third widget located at the bottom of your theme', 'kriate theme' ),
        'id' => 'bottom-widget-3',
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
		'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>'
    ));

    // Define Sidebar Widget Area 4
    register_sidebar(array(
        'name' => __('Widget 4', 'kriate'),
		'description' => __( 'The fourth widget located at the bottom of your theme', 'kriate theme' ),
        'id' => 'bottom-widget-4',
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
		'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>'
    ));
}
add_action( 'widgets_init', 'kriate_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function kriate_scripts() {
	wp_enqueue_style( 'wp-style', get_stylesheet_uri() );
	wp_enqueue_style( 'bootstrap', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css');
	wp_enqueue_style( 'theme-style', get_template_directory_uri() . '/css/theme.css');
	wp_enqueue_script( 'theme-js', get_template_directory_uri() . '/js/theme.js', array(), '20120301', true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}

/**
 * Setup Lightbox
 */


//Add lightbox rel="lightbox" to all posts images


add_action( 'wp_enqueue_scripts', 'kriate_scripts' );

require get_template_directory() . '/inc/custom-header.php';
require get_template_directory() . '/inc/template-tags.php';
require get_template_directory() . '/inc/extras.php';
require get_template_directory() . '/inc/customizer.php';
require get_template_directory() . '/inc/jetpack.php';
require get_template_directory() . '/inc/profile.php';


//



/**
 * Load Briite Projects compatibility file.
 */
 //require get_template_directory() . '/inc/project.php';