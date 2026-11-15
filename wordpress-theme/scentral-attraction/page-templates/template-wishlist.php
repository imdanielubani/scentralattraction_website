<?php
/**
 * Template Name: Wishlist
 * Template Post Type: page
 *
 * With TI WooCommerce Wishlist (or YITH) active, the page content (the plugin shortcode) is shown.
 * Otherwise the theme's native, browser-stored wishlist is rendered via the Store API.
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
<main id="main" class="site-main site-main--wishlist">
	<?php while ( have_posts() ) : the_post(); ?>
		<header class="page-hero container">
			<?php scentral_breadcrumbs(); ?>
			<p class="eyebrow" data-reveal><?php esc_html_e( 'Saved for later', 'scentral' ); ?></p>
			<h1 class="page-hero__title" data-reveal><?php the_title(); ?></h1>
		</header>
		<div class="container wishlist-page">
			<?php if ( scentral_has_wishlist_plugin() ) : ?>
				<div class="commerce-content">
					<?php
					$wishlist_content = (string) get_the_content();
					if ( has_shortcode( $wishlist_content, 'ti_wishlistsview' ) || has_shortcode( $wishlist_content, 'yith_wcwl_wishlist' ) ) {
						the_content();
					} else {
						// The page has no wishlist shortcode yet: show the active plugin's wishlist.
						echo do_shortcode( defined( 'YITH_WCWL' ) ? '[yith_wcwl_wishlist]' : '[ti_wishlistsview]' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- plugin output.
					}
					?>
				</div>
			<?php else : ?>
				<div data-wishlist-page>
					<ul class="wish-grid" data-wishlist-list></ul>
					<div class="empty-state" data-wishlist-empty>
						<?php scentral_emblem( array( 'class' => 'emblem' ) ); ?>
						<p class="empty-state__title"><?php esc_html_e( 'Your wishlist is empty.', 'scentral' ); ?></p>
						<p class="muted"><?php esc_html_e( 'Tap the heart on any fragrance to keep it here.', 'scentral' ); ?></p>
						<a class="button" href="<?php echo esc_url( scentral_shop_url() ); ?>"><?php esc_html_e( 'Explore the collection', 'scentral' ); ?></a>
					</div>
					<noscript><p class="muted"><?php esc_html_e( 'The wishlist needs JavaScript enabled.', 'scentral' ); ?></p></noscript>
				</div>
			<?php endif; ?>
		</div>
	<?php endwhile; ?>
</main>
<?php
get_footer();
