<?php
/**
 * Shop and product category archives — modelled on the reference shop page.
 *
 * Pill breadcrumb + large headline with intro and fact chips; a sticky rounded toolbar with
 * category pills (with counts), filter, sort and a 3/4-column toggle; the product grid with a
 * "Can't decide?" tile on the first page. All standard WooCommerce actions still fire.
 *
 * Override of woocommerce/templates/archive-product.php.
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
$total        = (int) wc_get_loop_prop( 'total' );
$paged        = max( 1, (int) get_query_var( 'paged', 1 ) );
$title        = is_shop() && ! is_search() ? __( 'All *fragrances*.', 'scentral' ) : woocommerce_page_title( false );
$all_count    = (int) wp_count_posts( 'product' )->publish;
?>
<header class="ph shop-hero">
	<div class="container">
		<?php scentral_breadcrumbs(); ?>
		<div class="ph__grid">
			<?php if ( apply_filters( 'woocommerce_show_page_title', true ) ) : ?>
				<h1 class="ph__title split woocommerce-products-header__title page-title"><?php echo scentral_split_words( $title ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside. ?></h1>
			<?php endif; ?>
			<div class="shop-hero__aside" data-reveal>
				<div class="shop-hero__desc"><?php do_action( 'woocommerce_archive_description' ); ?></div>
				<ul class="shop-hero__chips">
					<li><i aria-hidden="true"></i><?php echo esc_html( sprintf( /* translators: %d: number of products. */ _n( '%d fragrance', '%d fragrances', $all_count, 'scentral' ), $all_count ) ); ?></li>
					<?php if ( $categories ) : ?>
						<li><i aria-hidden="true"></i><?php echo esc_html( sprintf( /* translators: %d: number of collections. */ _n( '%d collection', '%d collections', count( $categories ), 'scentral' ), count( $categories ) ) ); ?></li>
					<?php endif; ?>
					<li><i aria-hidden="true"></i><?php esc_html_e( 'Store in Dawaki, Abuja', 'scentral' ); ?></li>
				</ul>
			</div>
		</div>
	</div>
</header>

<div class="shop-body container" data-shop>
	<div class="shop-bar">
		<?php if ( $categories ) : ?>
			<nav class="shop-bar__pills" aria-label="<?php esc_attr_e( 'Collections', 'scentral' ); ?>">
				<a href="<?php echo esc_url( scentral_shop_url() ); ?>"<?php echo is_shop() ? ' aria-current="page"' : ''; ?>><?php esc_html_e( 'All', 'scentral' ); ?> <i><?php echo esc_html( $all_count ); ?></i></a>
				<?php foreach ( $categories as $term ) : ?>
					<a href="<?php echo esc_url( get_term_link( $term ) ); ?>"<?php echo ( $current_term && (int) $current_term->term_id === (int) $term->term_id ) ? ' aria-current="page"' : ''; ?>><?php echo esc_html( $term->name ); ?> <i><?php echo esc_html( $term->count ); ?></i></a>
				<?php endforeach; ?>
			</nav>
		<?php endif; ?>
		<div class="shop-bar__tools">
			<button type="button" class="shop-bar__filter" data-open="shop-filters" aria-controls="shop-filters" aria-expanded="false">
				<?php echo scentral_icon( 'filter' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?><span><?php esc_html_e( 'Filter', 'scentral' ); ?></span>
				<?php if ( $active ) : ?>
					<span class="shop-toolbar__count"><?php echo esc_html( count( $active ) ); ?></span>
				<?php endif; ?>
			</button>
			<?php if ( woocommerce_product_loop() ) : ?>
				<div class="shop-bar__sort"><span aria-hidden="true"><?php esc_html_e( 'Sort', 'scentral' ); ?></span><?php woocommerce_catalog_ordering(); ?></div>
			<?php endif; ?>
			<div class="shop-bar__view" role="group" aria-label="<?php esc_attr_e( 'Grid size', 'scentral' ); ?>">
				<button type="button" data-density="3" aria-pressed="true" aria-label="<?php esc_attr_e( 'Three per row', 'scentral' ); ?>"><i></i><i></i><i></i></button>
				<button type="button" data-density="4" aria-pressed="false" aria-label="<?php esc_attr_e( 'Four per row', 'scentral' ); ?>"><i></i><i></i><i></i><i></i></button>
			</div>
		</div>
	</div>

	<?php if ( woocommerce_product_loop() ) : ?>
		<h2 class="screen-reader-text"><?php esc_html_e( 'Fragrances', 'scentral' ); ?></h2>
		<p class="shop-live"><?php echo esc_html( sprintf( /* translators: %d: number of products. */ _n( '%d fragrance', '%d fragrances', $total, 'scentral' ), $total ) ); ?></p>

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
		if ( $total ) {
			$index = 0;
			while ( have_posts() ) {
				the_post();
				do_action( 'woocommerce_shop_loop' );
				wc_get_template_part( 'content', 'product' );
				++$index;
				if ( 4 === $index && 1 === $paged && ! $active ) {
					get_template_part( 'template-parts/components/shop-tile' );
				}
			}
		}
		woocommerce_product_loop_end();

		do_action( 'woocommerce_after_shop_loop' );

		$next = get_next_posts_page_link();
		if ( $next && $paged < (int) $GLOBALS['wp_query']->max_num_pages ) :
			?>
			<div class="load-more"><a class="pill-btn pill-btn--ghost" href="<?php echo esc_url( $next ); ?>" data-load-more><span><?php esc_html_e( 'Load more', 'scentral' ); ?></span><i class="pill-btn__arrow" aria-hidden="true"><?php echo scentral_icon( 'plus' ); // phpcs:ignore ?></i></a></div>
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
