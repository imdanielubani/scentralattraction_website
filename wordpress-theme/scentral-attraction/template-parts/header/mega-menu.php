<?php
/**
 * Shop mega panel: category image tiles with live counts, a links column and a "New" product card.
 * Opened from the primary-menu item that links to the Shop page (assets/src/js/ui.js injects
 * an accessible toggle next to that item).
 *
 * @package Scentral
 */

defined( 'ABSPATH' ) || exit;

$categories = array_slice( scentral_featured_categories(), 0, 4 );
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
$shop = scentral_shop_url();
?>
<div class="mega" id="mega-shop" data-mega hidden>
	<?php if ( $categories ) : ?>
		<div class="mega__tiles mega__tiles--<?php echo esc_attr( count( $categories ) ); ?>">
			<?php foreach ( $categories as $term ) : ?>
				<?php $thumb = scentral_category_image( $term, 'scentral-card' ); ?>
				<a class="mega__tile" href="<?php echo esc_url( get_term_link( $term ) ); ?>">
					<span class="mega__tile-media">
						<?php if ( $thumb ) : ?>
							<img src="<?php echo esc_url( $thumb ); ?>" alt="" loading="lazy" decoding="async" width="640" height="800">
						<?php else : ?>
							<?php scentral_emblem( array( 'class' => 'emblem' ) ); ?>
						<?php endif; ?>
					</span>
					<b><?php echo esc_html( $term->name ); ?></b>
					<i>
						<?php
						/* translators: %d: number of fragrances. */
						echo esc_html( sprintf( _n( '%d fragrance', '%d fragrances', $term->count, 'scentral' ), $term->count ) );
						?>
					</i>
				</a>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
	<div class="mega__side">
		<p class="mega__note"><?php esc_html_e( 'Long-lasting Eau de Parfum, carefully selected.', 'scentral' ); ?></p>
		<ul class="mega__links">
			<li><a href="<?php echo esc_url( $shop ); ?>"><?php esc_html_e( 'All fragrances', 'scentral' ); ?><?php echo scentral_icon( 'arrow' ); // phpcs:ignore ?></a></li>
			<li><a href="<?php echo esc_url( add_query_arg( 'orderby', 'date', $shop ) ); ?>"><?php esc_html_e( 'New arrivals', 'scentral' ); ?><?php echo scentral_icon( 'arrow' ); // phpcs:ignore ?></a></li>
			<li><a href="<?php echo esc_url( add_query_arg( 'orderby', 'popularity', $shop ) ); ?>"><?php esc_html_e( 'Most loved', 'scentral' ); ?><?php echo scentral_icon( 'arrow' ); // phpcs:ignore ?></a></li>
			<?php if ( wc_get_product_ids_on_sale() ) : ?>
				<li><a href="<?php echo esc_url( add_query_arg( 'on_sale', '1', $shop ) ); ?>"><?php esc_html_e( 'On sale', 'scentral' ); ?><?php echo scentral_icon( 'arrow' ); // phpcs:ignore ?></a></li>
			<?php endif; ?>
		</ul>
		<?php if ( $feature ) : ?>
			<a class="mega__new" href="<?php echo esc_url( $feature->get_permalink() ); ?>">
				<?php echo wp_kses_post( $feature->get_image( 'woocommerce_gallery_thumbnail', array( 'loading' => 'lazy' ) ) ); ?>
				<span>
					<em><?php esc_html_e( 'New', 'scentral' ); ?></em>
					<b><?php echo esc_html( $feature->get_name() ); ?></b>
					<i><?php echo wp_kses_post( $feature->get_price_html() ); ?></i>
				</span>
			</a>
		<?php endif; ?>
	</div>
</div>
