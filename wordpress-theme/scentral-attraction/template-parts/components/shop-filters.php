<?php
/**
 * Shop filter drawer. Uses the "Shop Filters" widget area when it has widgets
 * (e.g. WooCommerce's attribute and price filter blocks), otherwise a native GET form
 * built on WooCommerce's own min_price / max_price query support.
 *
 * @package Scentral
 */

defined( 'ABSPATH' ) || exit;

// phpcs:disable WordPress.Security.NonceVerification.Recommended -- read-only filter form.
$bounds  = scentral_price_bounds();
$min     = isset( $_GET['min_price'] ) ? absint( $_GET['min_price'] ) : '';
$max     = isset( $_GET['max_price'] ) ? absint( $_GET['max_price'] ) : '';
$on_sale = ! empty( $_GET['on_sale'] );
$orderby = isset( $_GET['orderby'] ) ? sanitize_key( wp_unslash( $_GET['orderby'] ) ) : '';
// phpcs:enable

$action       = is_product_taxonomy() ? get_term_link( get_queried_object() ) : scentral_shop_url();
$current_term = is_product_taxonomy() ? get_queried_object() : null;
?>
<dialog class="dialog dialog--drawer dialog--filters" id="shop-filters" aria-labelledby="shop-filters-title">
	<div class="dialog__head">
		<h2 class="dialog__title" id="shop-filters-title"><?php esc_html_e( 'Refine', 'scentral' ); ?></h2>
		<button type="button" class="dialog__close" data-close aria-label="<?php esc_attr_e( 'Close filters', 'scentral' ); ?>"><?php echo scentral_icon( 'close' ); // phpcs:ignore ?></button>
	</div>
	<div class="filters">
		<?php if ( is_active_sidebar( 'shop-filters' ) ) : ?>
			<?php dynamic_sidebar( 'shop-filters' ); ?>
		<?php else : ?>
			<?php $categories = scentral_featured_categories(); ?>
			<?php if ( $categories ) : ?>
				<section class="filter-widget">
					<h3 class="filter-widget__title"><?php esc_html_e( 'Collection', 'scentral' ); ?></h3>
					<ul class="filter-list">
						<li><a href="<?php echo esc_url( scentral_shop_url() ); ?>"<?php echo is_shop() ? ' aria-current="page"' : ''; ?>><?php esc_html_e( 'All fragrances', 'scentral' ); ?></a></li>
						<?php foreach ( $categories as $term ) : ?>
							<li><a href="<?php echo esc_url( get_term_link( $term ) ); ?>"<?php echo ( $current_term && (int) $current_term->term_id === (int) $term->term_id ) ? ' aria-current="page"' : ''; ?>><?php echo esc_html( $term->name ); ?> <span><?php echo esc_html( number_format_i18n( $term->count ) ); ?></span></a></li>
						<?php endforeach; ?>
					</ul>
				</section>
			<?php endif; ?>

			<form class="filter-form" method="get" action="<?php echo esc_url( $action ); ?>">
				<?php if ( $orderby ) : ?>
					<input type="hidden" name="orderby" value="<?php echo esc_attr( $orderby ); ?>">
				<?php endif; ?>
				<?php if ( $bounds['max'] > 0 ) : ?>
					<fieldset class="filter-widget">
						<legend class="filter-widget__title"><?php esc_html_e( 'Price', 'scentral' ); ?> <span class="muted">(<?php echo esc_html( get_woocommerce_currency_symbol() ); ?>)</span></legend>
						<div class="price-fields">
							<label>
								<span><?php esc_html_e( 'From', 'scentral' ); ?></span>
								<input type="number" name="min_price" inputmode="numeric" min="0" step="1000" value="<?php echo esc_attr( $min ); ?>" placeholder="<?php echo esc_attr( $bounds['min'] ); ?>">
							</label>
							<label>
								<span><?php esc_html_e( 'To', 'scentral' ); ?></span>
								<input type="number" name="max_price" inputmode="numeric" min="0" step="1000" value="<?php echo esc_attr( $max ); ?>" placeholder="<?php echo esc_attr( $bounds['max'] ); ?>">
							</label>
						</div>
					</fieldset>
				<?php endif; ?>
				<?php if ( wc_get_product_ids_on_sale() ) : ?>
					<fieldset class="filter-widget">
						<legend class="filter-widget__title"><?php esc_html_e( 'Offers', 'scentral' ); ?></legend>
						<label class="check"><input type="checkbox" name="on_sale" value="1" <?php checked( $on_sale ); ?>> <?php esc_html_e( 'On sale only', 'scentral' ); ?></label>
					</fieldset>
				<?php endif; ?>
				<div class="filter-form__actions">
					<button type="submit" class="button"><?php esc_html_e( 'Show results', 'scentral' ); ?></button>
					<a class="link-draw" href="<?php echo esc_url( $action ); ?>"><?php esc_html_e( 'Reset', 'scentral' ); ?></a>
				</div>
			</form>
		<?php endif; ?>
	</div>
</dialog>
