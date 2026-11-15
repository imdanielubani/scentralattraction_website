<?php
/**
 * AJAX endpoints: quick view.
 *
 * Live search uses the public WooCommerce Store API directly from the browser,
 * so no custom search endpoint is needed.
 *
 * @package Scentral
 */

defined( 'ABSPATH' ) || exit;

/**
 * Return the quick-view markup for a product.
 */
function scentral_ajax_quick_view() {
	check_ajax_referer( 'scentral_nonce', 'nonce' );

	if ( ! scentral_rate_limit( 'quick_view', 60, 60 ) ) {
		wp_send_json_error( array( 'message' => __( 'Too many requests.', 'scentral' ) ), 429 );
	}

	$product_id = isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : 0;
	$product    = $product_id ? wc_get_product( $product_id ) : null;

	if ( ! $product || 'publish' !== get_post_status( $product_id ) || ! $product->is_visible() ) {
		wp_send_json_error( array( 'message' => __( 'Product not found.', 'scentral' ) ), 404 );
	}

	ob_start();
	scentral_render_quick_view( $product );
	wp_send_json_success( array( 'html' => ob_get_clean() ) );
}
add_action( 'wp_ajax_scentral_quick_view', 'scentral_ajax_quick_view' );
add_action( 'wp_ajax_nopriv_scentral_quick_view', 'scentral_ajax_quick_view' );

/**
 * Change a bag item's quantity from the drawer's − / + pill (0 removes it).
 * Responds with WooCommerce's refreshed cart fragments.
 */
function scentral_ajax_update_cart_item() {
	check_ajax_referer( 'scentral_cart', 'nonce' );

	$key = isset( $_POST['key'] ) ? sanitize_text_field( wp_unslash( $_POST['key'] ) ) : '';
	$qty = isset( $_POST['qty'] ) ? absint( $_POST['qty'] ) : 0;

	if ( ! WC()->cart || '' === $key || ! WC()->cart->get_cart_item( $key ) ) {
		wp_send_json_error( array( 'message' => __( 'This item is no longer in your bag.', 'scentral' ) ), 404 );
	}

	$item    = WC()->cart->get_cart_item( $key );
	$product = $item['data'];
	$max     = $product ? $product->get_max_purchase_quantity() : -1;
	if ( $max > 0 ) {
		$qty = min( $qty, $max );
	}

	WC()->cart->set_quantity( $key, $qty, true );
	WC_AJAX::get_refreshed_fragments(); // Sends JSON and exits.
}
add_action( 'wp_ajax_scentral_update_cart_item', 'scentral_ajax_update_cart_item' );
add_action( 'wp_ajax_nopriv_scentral_update_cart_item', 'scentral_ajax_update_cart_item' );

/**
 * Render quick-view content.
 *
 * @param WC_Product $product Product.
 */
function scentral_render_quick_view( $product ) {
	$GLOBALS['product'] = $product; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- WooCommerce templates read the global.
	$post_object        = get_post( $product->get_id() );
	setup_postdata( $GLOBALS['post'] = $post_object ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited, Squiz.PHP.DisallowMultipleAssignments.Found

	$spec = scentral_product_spec( $product );
	?>
	<div class="qv">
		<?php
		get_template_part(
			'template-parts/components/product-gallery',
			null,
			array(
				'product' => $product,
				'context' => 'quick',
			)
		);
		?>
		<div class="qv__summary">
			<?php if ( $spec ) : ?>
				<p class="eyebrow product-spec"><?php echo esc_html( $spec ); ?></p>
			<?php endif; ?>
			<h2 class="qv__title" id="quick-view-title"><?php echo esc_html( $product->get_name() ); ?></h2>
			<div class="product-meta-row">
				<p class="price"><?php echo wp_kses_post( $product->get_price_html() ); ?></p>
				<?php if ( wc_review_ratings_enabled() && $product->get_review_count() ) : ?>
					<span class="product-rate"><?php echo wc_get_rating_html( $product->get_average_rating() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core output. ?></span>
				<?php endif; ?>
			</div>
			<div class="qv__excerpt"><?php echo wp_kses_post( wpautop( wp_trim_words( $product->get_short_description() ? $product->get_short_description() : $product->get_description(), 34 ) ) ); ?></div>
			<?php if ( $product->is_in_stock() ) : ?>
				<p class="product-eta"><i aria-hidden="true"></i><?php esc_html_e( 'In stock', 'scentral' ); ?></p>
			<?php else : ?>
				<p class="product-eta product-eta--out"><i aria-hidden="true"></i><?php esc_html_e( 'Sold out', 'scentral' ); ?></p>
			<?php endif; ?>
			<div class="qv__buy summary"><?php woocommerce_template_single_add_to_cart(); ?></div>
			<div class="qv__foot">
				<?php scentral_wishlist_button( $product, 'single' ); ?>
				<a class="pill-btn pill-btn--ghost qv__more" href="<?php echo esc_url( $product->get_permalink() ); ?>"><span><?php esc_html_e( 'View full details', 'scentral' ); ?></span><i class="pill-btn__arrow" aria-hidden="true"><?php echo scentral_icon( 'arrow' ); // phpcs:ignore ?></i></a>
			</div>
		</div>
	</div>
	<?php
	wp_reset_postdata();
}
