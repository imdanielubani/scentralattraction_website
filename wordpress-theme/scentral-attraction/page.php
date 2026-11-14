<?php
/**
 * Default page template (also wraps WooCommerce cart, checkout, account and shortcode pages).
 *
 * @package Scentral
 */

defined( 'ABSPATH' ) || exit;

get_header();

if ( scentral_render_builder_page() ) {
	get_footer();
	return;
}

$is_wc_page = function_exists( 'is_cart' ) && ( is_cart() || is_checkout() || is_account_page() );
?>
<main id="main" class="site-main site-main--page<?php echo $is_wc_page ? ' site-main--commerce' : ''; ?>">
	<?php
	while ( have_posts() ) :
		the_post();
		?>
		<article id="post-<?php the_ID(); ?>" <?php post_class( 'page-article' ); ?>>
			<header class="page-hero page-hero--compact container">
				<?php scentral_breadcrumbs(); ?>
				<h1 class="page-hero__title" data-reveal><?php the_title(); ?></h1>
			</header>
			<div class="<?php echo $is_wc_page ? 'container commerce-content' : 'container prose'; ?>">
				<?php
				the_content();
				wp_link_pages(
					array(
						'before' => '<nav class="page-links">',
						'after'  => '</nav>',
					)
				);
				?>
			</div>
		</article>
		<?php
		if ( ! $is_wc_page && ( comments_open() || get_comments_number() ) ) {
			comments_template();
		}
	endwhile;
	?>
</main>
<?php
get_footer();
