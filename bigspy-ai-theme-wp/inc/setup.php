<?php
/**
 * Theme setup: supports, menus, widget areas and small global filters.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

function bst_setup() {
	load_theme_textdomain( 'bigspy-ai-theme-wp', BST_DIR . '/languages' );

	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' ) );
	add_theme_support( 'custom-logo', array( 'height' => 120, 'width' => 400, 'flex-height' => true, 'flex-width' => true ) );
	add_theme_support( 'customize-selective-refresh-widgets' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'editor-styles' );
	add_editor_style( 'assets/css/editor.css' );

	register_nav_menus(
		array(
			'primary'   => __( 'Main menu', 'bigspy-ai-theme-wp' ),
			'secondary' => __( 'Top bar menu', 'bigspy-ai-theme-wp' ),
			'mobile'    => __( 'Mobile menu (falls back to Main menu)', 'bigspy-ai-theme-wp' ),
			'footer'    => __( 'Footer bottom menu', 'bigspy-ai-theme-wp' ),
		)
	);

	add_image_size( 'bst-card', 720, 480, true );
	add_image_size( 'bst-wide', 1400, 700, true );
}
add_action( 'after_setup_theme', 'bst_setup' );

function bst_content_width() {
	$GLOBALS['content_width'] = (int) bst_mod( 'container_width' );
}
add_action( 'after_setup_theme', 'bst_content_width', 0 );

function bst_widgets_init() {
	$common = array(
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	);

	register_sidebar( array_merge( $common, array( 'name' => __( 'Blog sidebar', 'bigspy-ai-theme-wp' ), 'id' => 'sidebar-blog' ) ) );
	register_sidebar( array_merge( $common, array( 'name' => __( 'Page sidebar', 'bigspy-ai-theme-wp' ), 'id' => 'sidebar-page' ) ) );
	if ( class_exists( 'WooCommerce' ) ) {
		register_sidebar( array_merge( $common, array( 'name' => __( 'Shop sidebar', 'bigspy-ai-theme-wp' ), 'id' => 'sidebar-shop' ) ) );
	}
	register_sidebar( array_merge( $common, array( 'name' => __( 'Off-canvas menu (below menu)', 'bigspy-ai-theme-wp' ), 'id' => 'offcanvas' ) ) );

	for ( $i = 1; $i <= 4; $i++ ) {
		/* translators: %d: column number */
		register_sidebar( array_merge( $common, array( 'name' => sprintf( __( 'Footer 1 – Column %d', 'bigspy-ai-theme-wp' ), $i ), 'id' => 'footer-1-' . $i ) ) );
	}
	for ( $i = 1; $i <= 4; $i++ ) {
		/* translators: %d: column number */
		register_sidebar( array_merge( $common, array( 'name' => sprintf( __( 'Footer 2 – Column %d', 'bigspy-ai-theme-wp' ), $i ), 'id' => 'footer-2-' . $i ) ) );
	}
}
add_action( 'widgets_init', 'bst_widgets_init' );

function bst_body_classes( $classes ) {
	$classes[] = 'bs-layout-' . sanitize_html_class( bst_mod( 'site_layout' ) );
	$classes[] = 'bs-sidebar-' . bst_sidebar_position();
	if ( bst_mod( 'header_full_width' ) ) {
		$classes[] = 'bs-header-is-fluid';
		if ( 'container' === bst_mod( 'fullwidth_text_edge' ) ) {
			$classes[] = 'bs-edge-container';
		}
	}
	if ( bst_header_is_transparent() && bst_show_header() ) {
		$classes[] = 'bs-has-transparent-header';
	}
	if ( bst_mod( 'animations' ) ) {
		$classes[] = 'bs-animations';
	}
	if ( 'none' === bst_post_option( 'content_padding' ) ) {
		$classes[] = 'bs-no-content-padding';
	}
	if ( bst_mod( 'float_enable' ) && function_exists( 'bst_float_mobile_style' ) && 'bar' === bst_float_mobile_style() ) {
		$classes[] = 'bs-has-mobile-bar';
	}
	return $classes;
}
add_filter( 'body_class', 'bst_body_classes' );

function bst_excerpt_length( $length ) {
	return is_admin() ? $length : (int) bst_mod( 'blog_excerpt_length' );
}
add_filter( 'excerpt_length', 'bst_excerpt_length', 99 );

function bst_excerpt_more() {
	return '&hellip;';
}
add_filter( 'excerpt_more', 'bst_excerpt_more' );

/**
 * Print user scripts from Theme Options → Advanced.
 *
 * @param string $where head|body|footer.
 */
function bst_print_user_scripts( $where ) {
	$code = (string) bst_mod( 'scripts_' . $where );
	if ( '' !== trim( $code ) ) {
		echo "\n" . $code . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- saved only by users with unfiltered_html.
	}
}
add_action( 'wp_head', function () { bst_print_user_scripts( 'head' ); }, 99 );
add_action( 'wp_body_open', function () { bst_print_user_scripts( 'body' ); }, 1 );
add_action( 'wp_footer', function () { bst_print_user_scripts( 'footer' ); }, 99 );

function bst_disable_emoji() {
	if ( ! bst_mod( 'disable_emoji' ) ) {
		return;
	}
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );
	remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
	remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
	remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
}
add_action( 'init', 'bst_disable_emoji' );

