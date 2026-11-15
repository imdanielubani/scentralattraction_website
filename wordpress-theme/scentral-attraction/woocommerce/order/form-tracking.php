<?php
/**
 * Order tracking form — theme override.
 *
 * Same fields, names, nonce and hooks as WooCommerce's template; presented like the
 * Contact page form (floating-label underline fields and a pill button with arrow).
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 7.0.1
 */

defined( 'ABSPATH' ) || exit;

global $post;

// phpcs:disable WordPress.Security.NonceVerification.Recommended -- repopulating the form only.
$order_id    = isset( $_REQUEST['orderid'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['orderid'] ) ) : '';
$order_email = isset( $_REQUEST['order_email'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['order_email'] ) ) : '';
// phpcs:enable
?>

<form action="<?php echo esc_url( get_permalink( $post->ID ) ); ?>" method="post" class="woocommerce-form woocommerce-form-track-order track_order contact-form track-form">

	<?php do_action( 'woocommerce_order_tracking_form_start' ); // phpcs:ignore WooCommerce.Commenting.CommentHooks.MissingHookComment ?>

	<p class="track-form__intro"><?php esc_html_e( 'Your order number is on your receipt and in the confirmation email we sent you.', 'scentral' ); ?></p>

	<div class="contact-form__grid">
		<label class="ff" for="orderid"><span class="ff__box">
			<input class="input-text" type="text" name="orderid" id="orderid" value="<?php echo esc_attr( $order_id ); ?>" placeholder=" " autocomplete="off" required>
			<span class="ff__label"><?php esc_html_e( 'Order number', 'scentral' ); ?></span><i aria-hidden="true"></i>
		</span></label>
		<label class="ff" for="order_email"><span class="ff__box">
			<input class="input-text" type="email" name="order_email" id="order_email" value="<?php echo esc_attr( $order_email ); ?>" placeholder=" " autocomplete="email" required>
			<span class="ff__label"><?php esc_html_e( 'Billing email', 'scentral' ); ?></span><i aria-hidden="true"></i>
		</span></label>
	</div>

	<?php do_action( 'woocommerce_order_tracking_form' ); // phpcs:ignore WooCommerce.Commenting.CommentHooks.MissingHookComment ?>

	<div class="contact-form__foot">
		<button type="submit" class="pill-btn" name="track" value="<?php esc_attr_e( 'Track', 'woocommerce' ); ?>"><span><?php esc_html_e( 'Track order', 'scentral' ); ?></span><i class="pill-btn__arrow" aria-hidden="true"><?php echo scentral_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?></i></button>
		<p class="contact-form__note"><?php esc_html_e( 'Guest and account orders', 'scentral' ); ?></p>
	</div>
	<?php wp_nonce_field( 'woocommerce-order_tracking', 'woocommerce-order-tracking-nonce' ); ?>

	<?php do_action( 'woocommerce_order_tracking_form_end' ); // phpcs:ignore WooCommerce.Commenting.CommentHooks.MissingHookComment ?>

</form>
