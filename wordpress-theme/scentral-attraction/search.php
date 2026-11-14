<?php
/**
 * Site search results (product-only searches are handled by WooCommerce's archive template).
 *
 * @package Scentral
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main id="main" class="site-main site-main--search">
	<header class="page-hero container">
		<p class="eyebrow" data-reveal><?php esc_html_e( 'Search', 'scentral' ); ?></p>
		<h1 class="page-hero__title" data-reveal>
			<?php
			/* translators: %s: search query. */
			echo esc_html( sprintf( __( 'Results for “%s”', 'scentral' ), get_search_query() ) );
			?>
		</h1>
		<div class="search-page__form" data-reveal><?php get_search_form(); ?></div>
	</header>
	<div class="container journal">
		<?php if ( have_posts() ) : ?>
			<?php
			$products = array();
			$posts    = array();
			while ( have_posts() ) {
				the_post();
				if ( 'product' === get_post_type() ) {
					$products[] = get_the_ID();
				} else {
					$posts[] = get_the_ID();
				}
			}
			?>
			<?php if ( $products && function_exists( 'wc_get_product' ) ) : ?>
				<ul class="products columns-4" data-stagger>
					<?php
					foreach ( $products as $product_id ) {
						$GLOBALS['post']    = get_post( $product_id ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
						$GLOBALS['product'] = wc_get_product( $product_id ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
						setup_postdata( $GLOBALS['post'] );
						wc_get_template_part( 'content', 'product' );
					}
					wp_reset_postdata();
					?>
				</ul>
			<?php endif; ?>
			<?php if ( $posts ) : ?>
				<div class="post-grid search-page__posts" data-stagger>
					<?php
					foreach ( $posts as $post_id ) {
						$GLOBALS['post'] = get_post( $post_id ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
						setup_postdata( $GLOBALS['post'] );
						get_template_part( 'template-parts/components/post-card' );
					}
					wp_reset_postdata();
					?>
				</div>
			<?php endif; ?>
			<?php the_posts_pagination( array( 'mid_size' => 1 ) ); ?>
		<?php else : ?>
			<div class="empty-state">
				<?php scentral_emblem( array( 'class' => 'emblem' ) ); ?>
				<p class="empty-state__title"><?php esc_html_e( 'No results — try another note or name.', 'scentral' ); ?></p>
				<a class="button" href="<?php echo esc_url( scentral_shop_url() ); ?>"><?php esc_html_e( 'Browse all fragrances', 'scentral' ); ?></a>
			</div>
		<?php endif; ?>
	</div>
</main>
<?php
get_footer();
