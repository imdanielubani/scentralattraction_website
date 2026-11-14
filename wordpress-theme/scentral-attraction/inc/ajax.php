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
 * Render quick-view content.
 *
 * @param WC_Product $product Product.
 */
function scentral_render_quick_view( $product ) {
	$GLOBALS['product'] = $product; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- WooCommerce templates read the global.
	$post_object        = get_post( $product->get_id() );
	setup_postdata( $GLOBALS['post'] = $post_object ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited, Squiz.PHP.DisallowMultipleAssignments.Found

	$image_ids = array_filter( array_merge( array( $product->get_image_id() ), $product->get_gallery_image_ids() ) );
	?>
	<div class="quick-view">
		<div class="quick-view__media">
			<?php foreach ( array_slice( $image_ids, 0, 3 ) as $index => $image_id ) : ?>
				<?php echo wp_get_attachment_image( $image_id, 'woocommerce_single', false, array( 'loading' => 0 === $index ? 'eager' : 'lazy' ) ); ?>
			<?php endforeach; ?>
		</div>
		<div class="quick-view__summary">
			<?php $spec = scentral_product_spec( $product ); ?>
			<?php if ( $spec ) : ?>
				<p class="eyebrow"><?php echo esc_html( $spec ); ?></p>
			<?php endif; ?>
			<h2 class="quick-view__title" id="quick-view-title"><?php echo esc_html( $product->get_name() ); ?></h2>
			<div class="quick-view__price"><?php echo wp_kses_post( $product->get_price_html() ); ?></div>
			<div class="quick-view__excerpt"><?php echo wp_kses_post( wpautop( wp_trim_words( $product->get_short_description() ? $product->get_short_description() : $product->get_description(), 40 ) ) ); ?></div>
			<?php woocommerce_template_single_add_to_cart(); ?>
			<a class="link-arrow" href="<?php echo esc_url( $product->get_permalink() ); ?>"><?php esc_html_e( 'View full details', 'scentral' ); ?><?php echo scentral_icon( 'arrow' ); // phpcs:ignore ?></a>
		</div>
	</div>
	<?php
	wp_reset_postdata();
}
