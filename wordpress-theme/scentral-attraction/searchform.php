<?php
/**
 * Search form.
 *
 * @package Scentral
 */

defined( 'ABSPATH' ) || exit;

$field_id = 'search-' . wp_unique_id();
?>
<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="screen-reader-text" for="<?php echo esc_attr( $field_id ); ?>"><?php esc_html_e( 'Search for', 'scentral' ); ?></label>
	<input type="search" id="<?php echo esc_attr( $field_id ); ?>" class="search-form__input" placeholder="<?php esc_attr_e( 'Search fragrances…', 'scentral' ); ?>" value="<?php echo get_search_query(); ?>" name="s">
	<?php if ( class_exists( 'WooCommerce' ) ) : ?>
		<input type="hidden" name="post_type" value="product">
	<?php endif; ?>
	<button type="submit" class="search-form__submit" aria-label="<?php esc_attr_e( 'Search', 'scentral' ); ?>"><?php echo scentral_icon( 'search' ); // phpcs:ignore ?></button>
</form>
