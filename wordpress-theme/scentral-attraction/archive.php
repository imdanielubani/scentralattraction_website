<?php
/**
 * Journal archives (categories, tags, dates, authors).
 *
 * @package Scentral
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main id="main" class="site-main site-main--journal">
	<header class="page-hero container">
		<?php scentral_breadcrumbs(); ?>
		<p class="eyebrow" data-reveal><?php esc_html_e( 'Journal', 'scentral' ); ?></p>
		<h1 class="page-hero__title" data-reveal><?php echo esc_html( wp_strip_all_tags( get_the_archive_title() ) ); ?></h1>
		<?php if ( get_the_archive_description() ) : ?>
			<div class="lead page-hero__intro" data-reveal><?php the_archive_description(); ?></div>
		<?php endif; ?>
	</header>
	<div class="container journal">
		<?php if ( have_posts() ) : ?>
			<div class="post-grid" data-stagger>
				<?php
				while ( have_posts() ) {
					the_post();
					get_template_part( 'template-parts/components/post-card' );
				}
				?>
			</div>
			<?php the_posts_pagination( array( 'mid_size' => 1 ) ); ?>
		<?php else : ?>
			<div class="empty-state">
				<p class="empty-state__title"><?php esc_html_e( 'Nothing here yet.', 'scentral' ); ?></p>
			</div>
		<?php endif; ?>
	</div>
</main>
<?php
get_footer();
