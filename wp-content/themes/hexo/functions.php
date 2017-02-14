<?php
/**
 * hexo functions and definitions.
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package hexo
 */

if ( ! function_exists( 'hexo_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function hexo_setup() {
	/*
	 * Make theme available for translation.
	 * Translations can be filed in the /languages/ directory.
	 * If you're building a theme based on hexo, use a find and replace
	 * to change 'hexo' to the name of your theme in all the template files.
	 */
	load_theme_textdomain( 'hexo', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
	 * Let WordPress manage the document title.
	 * By adding theme support, we declare that this theme does not use a
	 * hard-coded <title> tag in the document head, and expect WordPress to
	 * provide it for us.
	 */
	add_theme_support( 'title-tag' );

	/*
	 * Enable support for Post Thumbnails on posts and pages.
	 *
	 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
	 */
	add_theme_support( 'post-thumbnails' );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus( array(
		'primary' => esc_html__( 'Primary', 'hexo' ),
	) );

	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support( 'html5', array(
		'search-form',
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
	) );

	// Set up the WordPress core custom background feature.
	add_theme_support( 'custom-background', apply_filters( 'hexo_custom_background_args', array(
		'default-color' => 'ffffff',
		'default-image' => '',
	) ) );

	add_editor_style(array('/layouts/editor-style.css','/layouts/editor-style.css'));

}
endif;
add_action( 'after_setup_theme', 'hexo_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function hexo_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'hexo_content_width', 800 );
}
add_action( 'after_setup_theme', 'hexo_content_width', 0 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function hexo_widgets_init() {
	register_sidebar( array(
		'name'          => esc_html__( 'Footer Widgets', 'hexo' ),
		'id'            => 'sidebar-1',
		'description'   => esc_html__( 'Add widgets here.', 'hexo' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
}
add_action( 'widgets_init', 'hexo_widgets_init' );

function hexo_widgets_init2() {
	register_sidebar( array(
		'name'          => esc_html__( 'Top Sidebar', 'hexo' ),
		'id'            => 'sidebar-2',
		'description'   => esc_html__( 'Add widgets here. These will be displayed besides menu', 'hexo' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
}
add_action( 'widgets_init', 'hexo_widgets_init2' );

/**
 * Enqueue scripts and styles.
 */
function hexo_scripts() {

	wp_enqueue_style( 'bootstrap', get_template_directory_uri() . '/layouts/bootstrap.css');

	wp_enqueue_style( 'menu', get_template_directory_uri() . '/layouts/hamburgers.css');

	wp_enqueue_style('hexo-google-fonts', '//fonts.googleapis.com/css?family=Merriweather|Lato:400,700');

	wp_enqueue_style( 'hexo-style', get_stylesheet_uri() );

	wp_enqueue_script( 'tether', get_template_directory_uri() . '/js/tether.js', array('jquery'), '1.3.0', true );

	wp_enqueue_script( 'bootstrap', get_template_directory_uri() . '/js/bootstrap.js', array('jquery'), '4.0', true );

	wp_enqueue_script( 'navigation', get_template_directory_uri() . '/js/navigation.js', array(), '20151215', true );

	wp_enqueue_script( 'skip-link-focus-fix', get_template_directory_uri() . '/js/skip-link-focus-fix.js', array(), '20151215', true );

	wp_enqueue_script( 'hexo-custom', get_template_directory_uri() . '/js/custom.js', array('jquery'), '1.3.0', true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'hexo_scripts' );

/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * navigation bootstrap
 */
 
 if( ! class_exists( 'wp_bootstrap_navwalker' ) ) {
        require get_template_directory() . '/inc/wp_bootstrap_navwalker.php';
    }


/**
 * Custom functions that act independently of the theme templates.
 */
require get_template_directory() . '/inc/extras.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
require get_template_directory() . '/inc/jetpack.php';
