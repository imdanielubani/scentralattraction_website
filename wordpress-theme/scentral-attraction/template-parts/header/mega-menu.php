<?php
/**
 * Shop mega menu panel. Opened by the primary-menu item that links to the Shop page
 * (a toggle is injected next to it by scentral_mega_menu_toggle()).
 *
 * @package Scentral
 */

defined( 'ABSPATH' ) || exit;

$categories = scentral_featured_categories();
$featured   = absint( scentral_option( 'header_featured_product' ) );
$products   = scentral_get_products(
	$featured ? array(
		'source' => 'ids',
		'ids'    => (string) $featured,
		'limit'  => 1,
	) : array(
		'source' => 'newest',
		'limit'  => 6,
	)
);
$feature    = null;
foreach ( $products as $candidate ) {
	if ( $candidate->is_in_stock() ) {
		$feature = $candidate;
		break;
	}
}
?>
<div class="mega" id="mega-shop" data-mega hidden>
	<div class="mega__inner container">
		<div class="mega__col">
			<p class="eyebrow"><?php esc_html_e( 'Collections', 'scentral' ); ?></p>
			<ul class="mega__list">
				<li><a href="<?php echo esc_url( scentral_shop_url() ); ?>"><?php esc_html_e( 'All fragrances', 'scentral' ); ?></a></li>
				<?php foreach ( $categories as $term ) : ?>
					<li>
						<a href="<?php echo esc_url( get_term_link( $term ) ); ?>">
							<?php echo esc_html( $term->name ); ?>
							<sup><?php echo esc_html( number_format_i18n( $term->count ) ); ?></sup>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
		<div class="mega__col">
			<p class="eyebrow"><?php esc_html_e( 'Discover', 'scentral' ); ?></p>
			<ul class="mega__list mega__list--small">
				<li><a href="<?php echo esc_url( add_query_arg( 'orderby', 'date', scentral_shop_url() ) ); ?>"><?php esc_html_e( 'New arrivals', 'scentral' ); ?></a></li>
				<li><a href="<?php echo esc_url( add_query_arg( 'orderby', 'popularity', scentral_shop_url() ) ); ?>"><?php esc_html_e( 'Most loved', 'scentral' ); ?></a></li>
				<li><a href="<?php echo esc_url( add_query_arg( 'orderby', 'price', scentral_shop_url() ) ); ?>"><?php esc_html_e( 'Price: low to high', 'scentral' ); ?></a></li>
				<?php if ( wc_get_product_ids_on_sale() ) : ?>
					<li><a href="<?php echo esc_url( add_query_arg( 'on_sale', '1', scentral_shop_url() ) ); ?>"><?php esc_html_e( 'On sale', 'scentral' ); ?></a></li>
				<?php endif; ?>
			</ul>
		</div>
		<?php if ( $feature ) : ?>
			<a class="mega__feature" href="<?php echo esc_url( $feature->get_permalink() ); ?>">
				<span class="mega__feature-media"><?php echo wp_kses_post( $feature->get_image( 'woocommerce_thumbnail', array( 'loading' => 'lazy' ) ) ); ?></span>
				<span class="mega__feature-text">
					<span class="eyebrow"><?php esc_html_e( 'Just in', 'scentral' ); ?></span>
					<span class="mega__feature-name"><?php echo esc_html( $feature->get_name() ); ?></span>
					<span class="mega__feature-price"><?php echo wp_kses_post( $feature->get_price_html() ); ?></span>
				</span>
			</a>
		<?php endif; ?>
	</div>
</div>
