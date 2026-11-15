<?php
/**
 * Wishlist: bridges TI WooCommerce Wishlist / YITH when active, otherwise a lightweight native wishlist.
 *
 * The native wishlist stores product IDs in the visitor's browser (localStorage key `sa-wishlist`)
 * and renders them on the Wishlist page template through the WooCommerce Store API. No personal
 * data is sent to the server.
 *
 * @package Scentral
 */

defined( 'ABSPATH' ) || exit;

/**
 * Is a supported wishlist plugin active?
 *
 * @return bool
 */
function scentral_has_wishlist_plugin() {
	return defined( 'TINVWL_FVERSION' ) || defined( 'TINVWL_VERSION' ) || class_exists( 'TInvWL' ) || defined( 'YITH_WCWL' );
}

/**
 * Wishlist page URL.
 *
 * @return string
 */
function scentral_wishlist_url() {
	$url = '';
	if ( function_exists( 'tinv_url_wishlist_default' ) ) {
		$url = (string) tinv_url_wishlist_default();
	} elseif ( defined( 'YITH_WCWL' ) && function_exists( 'YITH_WCWL' ) ) {
		$url = (string) YITH_WCWL()->get_wishlist_url();
	}
	// Plugin installed but its wishlist page not chosen yet: use the theme's Wishlist page.
	return '' !== $url ? $url : scentral_page_url( 'wishlist' );
}

/**
 * Wishlist toggle button for a product card or single product.
 *
 * @param WC_Product $product Product.
 * @param string     $context 'card' | 'single'.
 */
function scentral_wishlist_button( $product, $context = 'card' ) {
	if ( ! $product instanceof WC_Product ) {
		return;
	}

	if ( scentral_has_wishlist_plugin() ) {
		if ( shortcode_exists( 'ti_wishlists_addtowishlist' ) ) {
			echo '<div class="wishlist-plugin wishlist-plugin--' . esc_attr( $context ) . '">' . do_shortcode( '[ti_wishlists_addtowishlist product_id="' . absint( $product->get_id() ) . '" loop="yes"]' ) . '</div>';
		} elseif ( shortcode_exists( 'yith_wcwl_add_to_wishlist' ) ) {
			echo '<div class="wishlist-plugin wishlist-plugin--' . esc_attr( $context ) . '">' . do_shortcode( '[yith_wcwl_add_to_wishlist product_id="' . absint( $product->get_id() ) . '"]' ) . '</div>';
		}
		return;
	}

	printf(
		'<button type="button" class="wish-toggle wish-toggle--%1$s" data-wish="%2$d" aria-pressed="false" aria-label="%3$s">%4$s</button>',
		esc_attr( $context ),
		absint( $product->get_id() ),
		/* translators: %s: product name. */
		esc_attr( sprintf( __( 'Add %s to wishlist', 'scentral' ), $product->get_name() ) ),
		scentral_icon( 'heart' ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG.
	);
}
