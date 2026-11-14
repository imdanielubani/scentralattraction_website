<?php
/**
 * Shop and product category archives.
 *
 * Override of woocommerce/templates/archive-product.php. All standard WooCommerce actions are still
 * fired so plugins and notices work; only the default result count / ordering placement is moved
 * into the theme toolbar.
 *
 * @package Scentral
 * @version 8.6.0
 */

defined( 'ABSPATH' ) || exit;

get_header( 'shop' );

do_action( 'woocommerce_before_main_content' );

$current_term = is_product_taxonomy() ? get_queried_object() : null;
$categories   = scentral_featured_categories();
$active       = scentral_active_filters();
?>
<header class="shop-hero container">
	<?php scentral_breadcrumbs(); ?>
	<div class="shop-hero__grid">
		<div>
			<?php if ( apply_filters( 'woocommerce_show_page_title', true ) ) : ?>
				<h1 class="shop-hero__title woocommerce-products-header__title page-title" data-reveal><?php woocommerce_page_title(); ?></h1>
			<?php endif; ?>
			<div class="shop-hero__desc" data-reveal><?php do_action( 'woocommerce_archive_description' ); ?></div>
		</div>
		<?php if ( $categories ) : ?>
			<nav class="shop-hero__cats" aria-label="<?php esc_attr_e( 'Collections', 'scentral' ); ?>" data-reveal>
				<ul class="chips">
					<li><a class="chip" href="<?php echo esc_url( scentral_shop_url() ); ?>"<?php echo is_shop() ? ' aria-current="page"' : ''; ?>><?php esc_html_e( 'All', 'scentral' ); ?></a></li>
					<?php foreach ( $categories as $term ) : ?>
						<li><a class="chip" href="<?php echo esc_url( get_term_link( $term ) ); ?>"<?php echo ( $current_term && (int) $current_term->term_id === (int) $term->term_id ) ? ' aria-current="page"' : ''; ?>><?php echo esc_html( $term->name ); ?></a></li>
					<?php endforeach; ?>
				</ul>
			</nav>
		<?php endif; ?>
	</div>
</header>

<div class="shop-body container">
	<?php if ( woocommerce_product_loop() ) : ?>
		<div class="shop-toolbar">
			<button type="button" class="shop-toolbar__filter" data-open="shop-filters" aria-controls="shop-filters" aria-expanded="false">
				<?php echo scentral_icon( 'filter' ); // phpcs:ignore ?><span><?php esc_html_e( 'Filter', 'scentral' ); ?></span>
				<?php if ( $active ) : ?>
					<span class="shop-toolbar__count"><?php echo esc_html( count( $active ) ); ?></span>
				<?php endif; ?>
			</button>
			<?php woocommerce_result_count(); ?>
			<?php woocommerce_catalog_ordering(); ?>
		</div>

		<?php if ( $active ) : ?>
			<ul class="active-filters chips" aria-label="<?php esc_attr_e( 'Active filters', 'scentral' ); ?>">
				<?php foreach ( $active as $filter ) : ?>
					<li><a class="chip chip--remove" href="<?php echo esc_url( $filter['remove'] ); ?>"><?php echo esc_html( $filter['label'] ); ?> <?php echo scentral_icon( 'close' ); // phpcs:ignore ?><span class="screen-reader-text"><?php esc_html_e( 'Remove filter', 'scentral' ); ?></span></a></li>
				<?php endforeach; ?>
				<li><a class="link-draw active-filters__clear" href="<?php echo esc_url( $current_term ? get_term_link( $current_term ) : scentral_shop_url() ); ?>"><?php esc_html_e( 'Clear all', 'scentral' ); ?></a></li>
			</ul>
		<?php endif; ?>

		<?php
		do_action( 'woocommerce_before_shop_loop' );

		woocommerce_product_loop_start();
		if ( wc_get_loop_prop( 'total' ) ) {
			while ( have_posts() ) {
				the_post();
				do_action( 'woocommerce_shop_loop' );
				wc_get_template_part( 'content', 'product' );
			}
		}
		woocommerce_product_loop_end();

		do_action( 'woocommerce_after_shop_loop' );

		$next = get_next_posts_page_link();
		if ( $next && (int) get_query_var( 'paged', 1 ) < (int) $GLOBALS['wp_query']->max_num_pages ) :
			?>
			<div class="load-more"><a class="button button--outline" href="<?php echo esc_url( $next ); ?>" data-load-more><?php esc_html_e( 'Load more', 'scentral' ); ?></a></div>
			<?php
		endif;
	else :
		?>
		<div class="empty-state">
			<?php scentral_emblem( array( 'class' => 'emblem' ) ); ?>
			<p class="empty-state__title"><?php esc_html_e( 'No fragrances match your selection.', 'scentral' ); ?></p>
			<?php do_action( 'woocommerce_no_products_found' ); ?>
			<a class="button" href="<?php echo esc_url( scentral_shop_url() ); ?>"><?php esc_html_e( 'View all fragrances', 'scentral' ); ?></a>
		</div>
	<?php endif; ?>
</div>

<?php get_template_part( 'template-parts/components/shop-filters' ); ?>

<?php
do_action( 'woocommerce_after_main_content' );

get_footer( 'shop' );
