<?php
/**
 * SEO fallbacks. Output only when no SEO plugin (Yoast, Rank Math, SEOPress, AIOSEO) is active,
 * so the theme never duplicates or overrides a plugin's meta, Open Graph or schema.
 *
 * @package Scentral
 */

defined( 'ABSPATH' ) || exit;

/**
 * Is an SEO plugin handling meta output?
 *
 * @return bool
 */
function scentral_has_seo_plugin() {
	return defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) || defined( 'SEOPRESS_VERSION' ) || defined( 'AIOSEO_VERSION' );
}

/**
 * Meta description + Open Graph fallback.
 */
function scentral_seo_fallback_meta() {
	if ( scentral_has_seo_plugin() ) {
		return;
	}

	$title       = wp_get_document_title();
	$description = get_bloginfo( 'description' );
	$image       = '';
	$type        = 'website';

	if ( is_singular() ) {
		$post        = get_queried_object();
		$description = has_excerpt( $post ) ? get_the_excerpt( $post ) : wp_trim_words( wp_strip_all_tags( strip_shortcodes( $post->post_content ) ), 30, '…' );
		$image       = get_the_post_thumbnail_url( $post, 'large' );
		$type        = 'product' === get_post_type( $post ) ? 'product' : 'article';
	} elseif ( is_tax() || is_category() ) {
		$description = wp_strip_all_tags( term_description() );
	}

	if ( ! $image ) {
		$hero  = scentral_campaign_image( 'hero-noir-oud' );
		$image = $hero ? $hero['src'] : '';
	}

	$description = trim( (string) $description );
	if ( $description ) {
		printf( '<meta name="description" content="%s">' . "\n", esc_attr( wp_html_excerpt( $description, 160, '…' ) ) );
	}
	printf( '<meta property="og:type" content="%s">' . "\n", esc_attr( $type ) );
	printf( '<meta property="og:title" content="%s">' . "\n", esc_attr( $title ) );
	printf( '<meta property="og:site_name" content="%s">' . "\n", esc_attr( get_bloginfo( 'name' ) ) );
	printf( '<meta property="og:url" content="%s">' . "\n", esc_url( scentral_current_url() ) );
	if ( $description ) {
		printf( '<meta property="og:description" content="%s">' . "\n", esc_attr( wp_html_excerpt( $description, 200, '…' ) ) );
	}
	if ( $image ) {
		printf( '<meta property="og:image" content="%s">' . "\n", esc_url( $image ) );
		echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
	}
}
add_action( 'wp_head', 'scentral_seo_fallback_meta', 5 );

/**
 * Organization / Store schema fallback on the homepage (WooCommerce already prints Product schema).
 */
function scentral_seo_store_schema() {
	if ( scentral_has_seo_plugin() || ! is_front_page() ) {
		return;
	}
	$schema = array(
		'@context'  => 'https://schema.org',
		'@type'     => 'Store',
		'name'      => get_bloginfo( 'name' ),
		'url'       => home_url( '/' ),
		'telephone' => scentral_option( 'contact_phone' ),
		'email'     => scentral_option( 'contact_email' ),
		'address'   => array(
			'@type'          => 'PostalAddress',
			'streetAddress'  => scentral_option( 'contact_address' ),
			'addressCountry' => 'NG',
		),
		'sameAs'    => array_values( wp_list_pluck( scentral_social_links(), 'url' ) ),
	);
	echo '<script type="application/ld+json">' . wp_json_encode( array_filter( $schema ), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
}
add_action( 'wp_head', 'scentral_seo_store_schema', 6 );

/**
 * Current canonical-ish URL.
 *
 * @return string
 */
function scentral_current_url() {
	if ( is_singular() ) {
		return (string) get_permalink();
	}
	global $wp;
	return home_url( add_query_arg( array(), isset( $wp->request ) ? $wp->request : '' ) );
}
