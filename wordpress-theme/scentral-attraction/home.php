<?php
/**
 * Journal index (posts page).
 *
 * @package Scentral
 */

defined( 'ABSPATH' ) || exit;

get_header();

$page_for_posts = (int) get_option( 'page_for_posts' );
$title          = $page_for_posts ? get_the_title( $page_for_posts ) : __( 'Journal', 'scentral' );
$categories     = get_categories( array( 'hide_empty' => true ) );
?>
<main id="main" class="site-main site-main--journal">
	<header class="page-hero container">
		<?php scentral_breadcrumbs(); ?>
		<p class="eyebrow" data-reveal><?php esc_html_e( 'The fragrance journal', 'scentral' ); ?></p>
		<h1 class="page-hero__title" data-reveal><?php echo esc_html( $title ); ?></h1>
		<?php if ( count( $categories ) > 1 ) : ?>
			<ul class="chips journal-cats" data-reveal>
				<li><a class="chip" href="<?php echo esc_url( $page_for_posts ? get_permalink( $page_for_posts ) : home_url( '/' ) ); ?>" aria-current="page"><?php esc_html_e( 'All stories', 'scentral' ); ?></a></li>
				<?php foreach ( $categories as $category ) : ?>
					<li><a class="chip" href="<?php echo esc_url( get_category_link( $category ) ); ?>"><?php echo esc_html( $category->name ); ?></a></li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	</header>
	<div class="container journal">
		<?php if ( have_posts() ) : ?>
			<div class="post-grid" data-stagger>
				<?php
				$index = 0;
				while ( have_posts() ) {
					the_post();
					get_template_part( 'template-parts/components/post-card', null, array( 'feature' => 0 === $index && ! is_paged() ) );
					++$index;
				}
				?>
			</div>
			<?php the_posts_pagination( array( 'mid_size' => 1 ) ); ?>
		<?php else : ?>
			<div class="empty-state">
				<?php scentral_emblem( array( 'class' => 'emblem' ) ); ?>
				<p class="empty-state__title"><?php esc_html_e( 'Stories are on their way.', 'scentral' ); ?></p>
				<a class="button" href="<?php echo esc_url( scentral_shop_url() ); ?>"><?php esc_html_e( 'Explore the collection', 'scentral' ); ?></a>
			</div>
		<?php endif; ?>
	</div>
</main>
<?php
get_footer();
