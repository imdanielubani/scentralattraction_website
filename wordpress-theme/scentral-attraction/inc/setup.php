<?php
/**
 * Theme setup: supports, menus, image sizes.
 *
 * @package Scentral
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register theme supports, menus and image sizes.
 */
function scentral_setup() {
	load_theme_textdomain( 'scentral', SCENTRAL_DIR . '/languages' );

	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'editor-styles' );
	add_theme_support( 'customize-selective-refresh-widgets' );
	add_theme_support(
		'html5',
		array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' )
	);
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 160,
			'width'       => 160,
			'flex-height' => true,
			'flex-width'  => true,
			'unlink-homepage-logo' => false,
		)
	);

	register_nav_menus(
		array(
			'primary'     => __( 'Primary', 'scentral' ),
			'utility'     => __( 'Utility (top bar)', 'scentral' ),
			'footer-shop' => __( 'Footer — Shop', 'scentral' ),
			'footer-help' => __( 'Footer — Help', 'scentral' ),
			'legal'       => __( 'Legal', 'scentral' ),
		)
	);

	add_image_size( 'scentral-hero', 2400, 1350, true );
	add_image_size( 'scentral-banner', 1600, 1000, true );
	add_image_size( 'scentral-portrait', 900, 1125, true );
	add_image_size( 'scentral-card', 640, 800, true );

	add_editor_style( 'assets/dist/editor.css' );
}
add_action( 'after_setup_theme', 'scentral_setup' );

/**
 * Content width for embeds.
 */
function scentral_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'scentral_content_width', 760 );
}
add_action( 'after_setup_theme', 'scentral_content_width', 0 );

/**
 * Register widget areas (shop filters).
 */
function scentral_widgets_init() {
	register_sidebar(
		array(
			'name'          => __( 'Shop Filters', 'scentral' ),
			'id'            => 'shop-filters',
			'description'   => __( 'Filters shown in the shop filter drawer (price, attributes, categories).', 'scentral' ),
			'before_widget' => '<section id="%1$s" class="filter-widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h3 class="filter-widget__title">',
			'after_title'   => '</h3>',
		)
	);
}
add_action( 'widgets_init', 'scentral_widgets_init' );

/**
 * Add body classes for state-driven styling.
 *
 * @param string[] $classes Body classes.
 * @return string[]
 */
function scentral_body_classes( $classes ) {
	$theme_homepage = is_front_page() && ( 'page' !== scentral_option( 'home_layout' ) || ! is_page() ) && ! scentral_is_elementor_preview();
	if ( $theme_homepage && scentral_option( 'header_transparent' ) ) {
		$classes[] = 'has-transparent-header';
	}
	if ( ! scentral_option( 'motion_enabled' ) ) {
		$classes[] = 'motion-off';
	}
	if ( scentral_option( 'announcement_enabled' ) && scentral_get_announcements() ) {
		$classes[] = 'has-announcement';
	}
	return $classes;
}
add_filter( 'body_class', 'scentral_body_classes' );
