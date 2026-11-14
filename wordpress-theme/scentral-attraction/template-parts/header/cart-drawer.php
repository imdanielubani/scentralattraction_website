<?php
/**
 * Bag drawer (reference style): a rounded sheet sliding in from the right.
 * Contents come from woocommerce/cart/mini-cart.php and stay in sync through cart fragments.
 *
 * @package Scentral
 */

defined( 'ABSPATH' ) || exit;

if ( is_cart() || is_checkout() ) {
	return;
}
?>
<dialog class="dialog dialog--drawer dialog--bag" id="cart-drawer" aria-labelledby="cart-drawer-title">
	<header class="bag-head">
		<h2 class="bag-head__title" id="cart-drawer-title"><?php esc_html_e( 'Bag', 'scentral' ); ?> <?php echo scentral_bag_count_html(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in helper. ?></h2>
		<button type="button" class="bag-close" data-close aria-label="<?php esc_attr_e( 'Close bag', 'scentral' ); ?>"><?php echo scentral_icon( 'close' ); // phpcs:ignore ?></button>
	</header>
	<?php echo scentral_free_shipping_html(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in helper. ?>
	<div class="widget_shopping_cart_content">
		<?php woocommerce_mini_cart(); ?>
	</div>
</dialog>
