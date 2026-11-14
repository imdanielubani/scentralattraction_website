<?php
/**
 * Performance: resource hints, conditional loading, cleanup.
 *
 * @package Scentral
 */

defined( 'ABSPATH' ) || exit;

/**
 * Preload the two critical font files.
 */
function scentral_preload_fonts() {
	$fonts = array(
		'cormorant-garamond-latin-300-normal.woff2',
		'manrope-latin-wght-normal.woff2',
	);
	foreach ( $fonts as $font ) {
		printf(
			'<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n",
			esc_url( SCENTRAL_URI . '/assets/fonts/' . $font )
		);
	}
}
add_action( 'wp_head', 'scentral_preload_fonts', 1 );

/**
 * Preload the homepage hero image (LCP element).
 */
function scentral_preload_hero() {
	if ( ! is_front_page() ) {
		return;
	}
	$hero = scentral_hero_image();
	if ( empty( $hero['src'] ) ) {
		return;
	}
	printf(
		'<link rel="preload" as="image" href="%1$s"%2$s fetchpriority="high">' . "\n",
		esc_url( $hero['src'] ),
		! empty( $hero['srcset'] ) ? ' imagesrcset="' . esc_attr( $hero['srcset'] ) . '" imagesizes="100vw"' : ''
	);
}
add_action( 'wp_head', 'scentral_preload_hero', 2 );

/* Remove emoji detection script and styles (the theme uses no emoji). */
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
remove_action( 'admin_print_styles', 'print_emoji_styles' );
remove_action( 'wp_head', 'wlwmanifest_link' );
remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'wp_shortlink_wp_head' );

/**
 * Remove the jQuery Migrate dependency on the front end (WooCommerce does not need it).
 *
 * @param WP_Scripts $scripts Scripts registry.
 */
function scentral_remove_jquery_migrate( $scripts ) {
	if ( is_admin() || ! isset( $scripts->registered['jquery'] ) ) {
		return;
	}
	$jquery = $scripts->registered['jquery'];
	if ( $jquery->deps ) {
		$jquery->deps = array_diff( $jquery->deps, array( 'jquery-migrate' ) );
	}
}
add_action( 'wp_default_scripts', 'scentral_remove_jquery_migrate' );

/**
 * Lazy-load and async-decode content images by default (WordPress already lazy-loads; add decoding).
 *
 * @param array $attr Image attributes.
 * @return array
 */
function scentral_image_attributes( $attr ) {
	if ( empty( $attr['decoding'] ) ) {
		$attr['decoding'] = 'async';
	}
	return $attr;
}
add_filter( 'wp_get_attachment_image_attributes', 'scentral_image_attributes' );

/**
 * Flush cached homepage product queries whenever products change.
 */
function scentral_flush_product_cache() {
	$version = (int) get_option( 'scentral_cache_version', 1 );
	update_option( 'scentral_cache_version', $version + 1, false );
}
add_action( 'woocommerce_update_product', 'scentral_flush_product_cache' );
add_action( 'woocommerce_new_product', 'scentral_flush_product_cache' );
add_action( 'woocommerce_delete_product', 'scentral_flush_product_cache' );
add_action( 'woocommerce_product_set_stock', 'scentral_flush_product_cache' );
add_action( 'woocommerce_variation_set_stock', 'scentral_flush_product_cache' );
add_action( 'comment_post', 'scentral_flush_product_cache' );
add_action( 'customize_save_after', 'scentral_flush_product_cache' );
