<?php
/**
 * Related products — theme override: a horizontal rail with previous / next arrow buttons
 * (reference "Pieces that sit well with it"). Same data, filters and card template as core.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 10.3.0
 */

defined( 'ABSPATH' ) || exit;

if ( $related_products ) :
	if ( function_exists( 'wp_increase_content_media_count' ) ) {
		$content_media_count = wp_increase_content_media_count( 0 );
		if ( $content_media_count < wp_omit_loading_attr_threshold() ) {
			wp_increase_content_media_count( wp_omit_loading_attr_threshold() - $content_media_count );
		}
	}
	$heading = apply_filters( 'woocommerce_product_related_products_heading', __( 'Scents that pair *well* with it.', 'scentral' ) ); // phpcs:ignore WooCommerce.Commenting.CommentHooks.MissingHookComment
	?>
	<section class="related products related-rail" data-rail aria-labelledby="related-title">
		<div class="related-rail__head">
			<div>
				<p class="eyebrow cf__eyebrow"><i aria-hidden="true"></i><?php esc_html_e( 'You may also like', 'scentral' ); ?></p>
				<?php if ( $heading ) : ?>
					<h2 class="related-rail__title" id="related-title"><?php echo scentral_rich_title( $heading ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside. ?></h2>
				<?php endif; ?>
			</div>
			<div class="rail-nav">
				<button type="button" data-rail-prev aria-label="<?php esc_attr_e( 'Previous', 'scentral' ); ?>"><?php echo scentral_icon( 'arrow-left' ); // phpcs:ignore ?></button>
				<button type="button" data-rail-next aria-label="<?php esc_attr_e( 'Next', 'scentral' ); ?>"><?php echo scentral_icon( 'arrow' ); // phpcs:ignore ?></button>
			</div>
		</div>
		<div class="related-rail__viewport" data-rail-track tabindex="0" aria-label="<?php esc_attr_e( 'Related fragrances', 'scentral' ); ?>">
			<?php woocommerce_product_loop_start(); ?>
				<?php foreach ( $related_products as $related_product ) : ?>
					<?php
					$post_object = get_post( $related_product->get_id() );
					setup_postdata( $GLOBALS['post'] = $post_object ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited, Squiz.PHP.DisallowMultipleAssignments.Found
					wc_get_template_part( 'content', 'product' );
					?>
				<?php endforeach; ?>
			<?php woocommerce_product_loop_end(); ?>
		</div>
		<div class="related-rail__progress" aria-hidden="true"><i data-rail-bar></i></div>
	</section>
	<?php
endif;

wp_reset_postdata();
