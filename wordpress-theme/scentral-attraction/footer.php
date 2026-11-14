<?php
/**
 * Site footer and dialogs.
 *
 * @package Scentral
 */

defined( 'ABSPATH' ) || exit;

do_action( 'scentral_before_footer' );
?>
</div><!-- .page-shell -->
<?php
if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'footer' ) ) {
	get_template_part( 'template-parts/footer/site-footer' );
}
get_template_part( 'template-parts/header/mobile-menu' );
get_template_part( 'template-parts/header/search-overlay' );
if ( class_exists( 'WooCommerce' ) ) {
	get_template_part( 'template-parts/header/cart-drawer' );
	if ( scentral_option( 'shop_quick_view' ) ) :
		?>
		<dialog class="dialog dialog--quick-view" id="quick-view" aria-labelledby="quick-view-title">
			<button type="button" class="dialog__close" data-close aria-label="<?php esc_attr_e( 'Close', 'scentral' ); ?>"><?php echo scentral_icon( 'close' ); // phpcs:ignore ?></button>
			<div class="dialog__body" data-quick-view-body></div>
		</dialog>
		<?php
	endif;
}
wp_footer();
?>
</body>
</html>
