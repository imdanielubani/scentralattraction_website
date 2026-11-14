<?php
/**
 * Mini-cart drawer. Contents are kept in sync by WooCommerce cart fragments.
 *
 * @package Scentral
 */

defined( 'ABSPATH' ) || exit;

if ( is_cart() || is_checkout() ) {
	return;
}
?>
<dialog class="dialog dialog--drawer" id="cart-drawer" aria-labelledby="cart-drawer-title">
	<div class="dialog__head">
		<h2 class="dialog__title" id="cart-drawer-title"><?php esc_html_e( 'Your bag', 'scentral' ); ?> <?php echo scentral_cart_count_html(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h2>
		<button type="button" class="dialog__close" data-close aria-label="<?php esc_attr_e( 'Close bag', 'scentral' ); ?>"><?php echo scentral_icon( 'close' ); // phpcs:ignore ?></button>
	</div>
	<?php echo scentral_free_shipping_html(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in helper. ?>
	<div class="widget_shopping_cart_content">
		<?php woocommerce_mini_cart(); ?>
	</div>
</dialog>
