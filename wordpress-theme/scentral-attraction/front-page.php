<?php
/**
 * Homepage.
 *
 * Layout "theme" (default): the hero followed by the Customizer-ordered sections.
 * Layout "page": the Home page's own content (built with Elementor or the block editor).
 * The Elementor editor preview always renders the page content so it can be edited.
 *
 * @package Scentral
 */

defined( 'ABSPATH' ) || exit;

get_header();

$use_page_content = is_page() && ( 'page' === scentral_option( 'home_layout' ) || scentral_is_elementor_preview() );

if ( $use_page_content ) :
	$builder = scentral_is_builder_page( get_queried_object_id() );
	?>
	<main id="main" class="site-main <?php echo $builder ? 'site-main--builder' : 'site-main--page'; ?>">
		<?php
		while ( have_posts() ) :
			the_post();
			if ( $builder || scentral_is_elementor_preview() ) {
				the_content();
			} else {
				echo '<div class="container prose">';
				the_content();
				echo '</div>';
			}
		endwhile;
		?>
	</main>
	<?php
else :
	$section_ids = array_filter( array_map( 'sanitize_key', explode( ',', (string) scentral_option( 'home_sections' ) ) ) );
	$section_ids = apply_filters( 'scentral_home_sections', $section_ids );
	?>
	<main id="main" class="site-main site-main--home">
		<?php
		scentral_render_section( 'hero' );
		do_action( 'scentral_after_hero' );

		foreach ( $section_ids as $section_id ) {
			if ( 'hero' !== $section_id ) {
				scentral_render_section( $section_id );
			}
			do_action( 'scentral_home_section_' . $section_id );
		}
		?>
	</main>
	<?php
endif;

get_footer();
