<?php
/**
 * Homepage: hero followed by the ordered, Customizer-driven sections.
 *
 * @package Scentral
 */

defined( 'ABSPATH' ) || exit;

get_header();

$section_ids = array_filter( array_map( 'sanitize_key', explode( ',', (string) scentral_option( 'home_sections' ) ) ) );
$section_ids = apply_filters( 'scentral_home_sections', $section_ids );

$product_sections = array(
	'new_arrivals' => array(
		'source' => 'newest',
		'title'  => scentral_option( 'home_new_title' ),
		'text'   => scentral_option( 'home_new_text' ),
		'eyebrow' => __( 'Just arrived', 'scentral' ),
		'tone'   => 'light',
	),
	'bestsellers'  => array(
		'source' => 'bestsellers',
		'title'  => scentral_option( 'home_best_title' ),
		'text'   => __( 'The fragrances our clients return to.', 'scentral' ),
		'eyebrow' => __( 'Bestsellers', 'scentral' ),
		'tone'   => 'surface',
	),
	'on_sale'      => array(
		'source' => 'on_sale',
		'title'  => __( 'Limited offers', 'scentral' ),
		'text'   => '',
		'eyebrow' => __( 'On sale', 'scentral' ),
		'tone'   => 'light',
	),
	'featured'     => array(
		'source' => 'featured',
		'title'  => __( 'Our selection', 'scentral' ),
		'text'   => '',
		'eyebrow' => __( 'Featured', 'scentral' ),
		'tone'   => 'light',
	),
);
$template_sections = array( 'notes', 'categories', 'showcase', 'collections', 'intro', 'numbers', 'promo', 'testimonials', 'social', 'newsletter' );
?>
<main id="main" class="site-main site-main--home">
	<?php
	get_template_part( 'template-parts/hero/hero' );
	do_action( 'scentral_after_hero' );

	foreach ( $section_ids as $section_id ) {
		if ( isset( $product_sections[ $section_id ] ) ) {
			get_template_part( 'template-parts/sections/products', null, array_merge( $product_sections[ $section_id ], array( 'id' => $section_id ) ) );
		} elseif ( in_array( $section_id, $template_sections, true ) ) {
			get_template_part( 'template-parts/sections/' . $section_id );
		}
		do_action( 'scentral_home_section_' . $section_id );
	}
	?>
</main>
<?php
get_footer();
