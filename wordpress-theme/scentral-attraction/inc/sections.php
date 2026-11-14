<?php
/**
 * Section registry: every homepage section can be rendered anywhere —
 * by front-page.php, by the "Scentral Section" Elementor widget, or by the
 * [scentral_section name="…"] shortcode in any page builder.
 *
 * @package Scentral
 */

defined( 'ABSPATH' ) || exit;

/**
 * Product rail presets (rendered by template-parts/sections/products.php).
 *
 * @return array<string,array>
 */
function scentral_product_section_presets() {
	return array(
		'new_arrivals' => array(
			'source'  => 'newest',
			'title'   => scentral_option( 'home_new_title' ),
			'text'    => scentral_option( 'home_new_text' ),
			'eyebrow' => __( 'Just arrived', 'scentral' ),
			'tone'    => 'light',
		),
		'bestsellers'  => array(
			'source'  => 'bestsellers',
			'title'   => scentral_option( 'home_best_title' ),
			'text'    => __( 'The fragrances our clients return to.', 'scentral' ),
			'eyebrow' => __( 'Bestsellers', 'scentral' ),
			'tone'    => 'surface',
		),
		'on_sale'      => array(
			'source'  => 'on_sale',
			'title'   => __( 'Limited offers', 'scentral' ),
			'text'    => '',
			'eyebrow' => __( 'On sale', 'scentral' ),
			'tone'    => 'light',
		),
		'featured'     => array(
			'source'  => 'featured',
			'title'   => __( 'Our selection', 'scentral' ),
			'text'    => '',
			'eyebrow' => __( 'Featured', 'scentral' ),
			'tone'    => 'light',
		),
	);
}

/**
 * All renderable sections (id => label).
 *
 * @return array<string,string>
 */
function scentral_section_choices() {
	return array(
		'hero'         => __( 'Hero (headline + "In this edit")', 'scentral' ),
		'notes'        => __( 'Notes ribbon', 'scentral' ),
		'categories'   => __( 'Collection collage', 'scentral' ),
		'new_arrivals' => __( 'New arrivals rail', 'scentral' ),
		'bestsellers'  => __( 'Bestsellers rail', 'scentral' ),
		'on_sale'      => __( 'On sale rail', 'scentral' ),
		'featured'     => __( 'Featured rail', 'scentral' ),
		'showcase'     => __( 'Luxury showcase (scroll zoom)', 'scentral' ),
		'collections'  => __( 'Collection panels', 'scentral' ),
		'intro'        => __( 'Brand introduction', 'scentral' ),
		'numbers'      => __( 'Live store numbers', 'scentral' ),
		'promo'        => __( 'Promotional banner', 'scentral' ),
		'testimonials' => __( 'Client reviews', 'scentral' ),
		'social'       => __( 'Instagram', 'scentral' ),
		'newsletter'   => __( 'Newsletter', 'scentral' ),
	);
}

/**
 * Render one section by id. Unknown ids render nothing.
 *
 * @param string $section_id Section id.
 */
function scentral_render_section( $section_id ) {
	$section_id = sanitize_key( $section_id );
	$presets    = scentral_product_section_presets();

	if ( 'hero' === $section_id ) {
		get_template_part( 'template-parts/hero/hero' );
		return;
	}
	if ( isset( $presets[ $section_id ] ) ) {
		get_template_part( 'template-parts/sections/products', null, array_merge( $presets[ $section_id ], array( 'id' => $section_id ) ) );
		return;
	}
	if ( array_key_exists( $section_id, scentral_section_choices() ) ) {
		get_template_part( 'template-parts/sections/' . $section_id );
	}
}

/**
 * [scentral_section name="new_arrivals"] — use any section inside page content or a page builder.
 *
 * @param array $atts Shortcode attributes.
 * @return string
 */
function scentral_section_shortcode( $atts ) {
	$atts = shortcode_atts( array( 'name' => '' ), $atts, 'scentral_section' );
	ob_start();
	scentral_render_section( $atts['name'] );
	return (string) ob_get_clean();
}
add_shortcode( 'scentral_section', 'scentral_section_shortcode' );
