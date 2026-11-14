<?php
/**
 * Template Name: FAQ
 * Template Post Type: page
 *
 * Questions are written by the store in the editor as Details blocks (question = summary).
 * The theme styles them as an accessible accordion and invents no answers.
 *
 * @package Scentral
 */

defined( 'ABSPATH' ) || exit;

get_header();

if ( scentral_render_builder_page() ) {
	get_footer();
	return;
}
?>
<main id="main" class="site-main site-main--faq">
	<?php while ( have_posts() ) : the_post(); ?>
		<header class="page-hero container">
			<?php scentral_breadcrumbs(); ?>
			<p class="eyebrow" data-reveal><?php esc_html_e( 'Client care', 'scentral' ); ?></p>
			<h1 class="page-hero__title" data-reveal><?php the_title(); ?></h1>
		</header>
		<div class="container faq-layout">
			<div class="faq prose">
				<?php if ( trim( get_the_content() ) ) : ?>
					<?php the_content(); ?>
				<?php else : ?>
					<?php get_template_part( 'template-parts/components/content-pending' ); ?>
				<?php endif; ?>
			</div>
			<aside class="faq-help" data-reveal>
				<p class="eyebrow"><?php esc_html_e( 'Still curious?', 'scentral' ); ?></p>
				<p class="faq-help__title"><?php esc_html_e( 'Our team is happy to help you choose.', 'scentral' ); ?></p>
				<a class="button button--outline" href="<?php echo esc_url( scentral_page_url( 'contact-us' ) ); ?>"><?php esc_html_e( 'Contact us', 'scentral' ); ?></a>
			</aside>
		</div>
	<?php endwhile; ?>
</main>
<?php
get_footer();
