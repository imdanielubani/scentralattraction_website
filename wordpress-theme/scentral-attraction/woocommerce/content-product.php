<?php
/**
 * Product card used in every product loop (shop, categories, related, homepage rails).
 *
 * Override of woocommerce/templates/content-product.php. The add-to-cart button is still rendered
 * by woocommerce_template_loop_add_to_cart(), so AJAX add-to-cart and plugin filters keep working.
 *
 * @package Scentral
 * @version 9.4.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

if ( ! is_a( $product, WC_Product::class ) || ! $product->is_visible() ) {
	return;
}

$images    = scentral_product_image_ids( $product );
$alt_image = isset( $images[1] ) ? (int) $images[1] : 0;
$spec      = scentral_product_spec( $product );
$badge     = scentral_product_badge( $product );
$link      = apply_filters( 'woocommerce_loop_product_link', get_the_permalink( $product->get_id() ), $product );
$classes   = apply_filters( 'scentral_product_card_classes', array( 'card', $alt_image ? 'card--has-alt' : '' ) );
?>
<li <?php wc_product_class( array_filter( $classes ), $product ); ?> data-reveal>
	<div class="card__media">
		<a class="card__image" href="<?php echo esc_url( $link ); ?>" tabindex="-1" aria-hidden="true">
			<?php echo $product->get_image( 'woocommerce_thumbnail', array( 'class' => 'card__img', 'loading' => 'lazy' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core output. ?>
			<?php if ( $alt_image ) : ?>
				<?php echo wp_get_attachment_image( $alt_image, 'woocommerce_thumbnail', false, array( 'class' => 'card__img card__img--alt', 'loading' => 'lazy', 'alt' => '' ) ); ?>
			<?php endif; ?>
		</a>
		<?php if ( $badge ) : ?>
			<div class="card__badges"><?php echo wp_kses_post( $badge ); ?></div>
		<?php endif; ?>
		<div class="card__wish"><?php scentral_wishlist_button( $product, 'card' ); ?></div>
		<div class="card__actions">
			<?php if ( scentral_option( 'shop_quick_view' ) ) : ?>
				<button type="button" class="card__quick" data-quick-view="<?php echo esc_attr( $product->get_id() ); ?>" data-href="<?php echo esc_url( $link ); ?>" aria-label="<?php echo esc_attr( sprintf( /* translators: %s: product name. */ __( 'Quick view: %s', 'scentral' ), $product->get_name() ) ); ?>">
					<i aria-hidden="true"><?php echo scentral_icon( 'eye' ); // phpcs:ignore ?></i><span><?php esc_html_e( 'Quick view', 'scentral' ); ?></span>
				</button>
			<?php endif; ?>
			<?php woocommerce_template_loop_add_to_cart(); ?>
		</div>
	</div>
	<div class="card__body">
		<?php if ( $spec ) : ?>
			<p class="card__spec"><?php echo esc_html( $spec ); ?></p>
		<?php endif; ?>
		<h3 class="card__title woocommerce-loop-product__title"><a href="<?php echo esc_url( $link ); ?>"><?php echo esc_html( $product->get_name() ); ?></a></h3>
		<div class="card__row">
			<div class="card__price"><?php echo wp_kses_post( $product->get_price_html() ); ?></div>
			<?php if ( $product->get_review_count() && wc_review_ratings_enabled() ) : ?>
				<span class="card__rate"><?php echo wc_get_rating_html( $product->get_average_rating() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core output. ?><i><?php echo esc_html( number_format_i18n( (float) $product->get_average_rating(), 1 ) . ' (' . $product->get_review_count() . ')' ); ?></i></span>
			<?php endif; ?>
		</div>
	</div>
</li>
