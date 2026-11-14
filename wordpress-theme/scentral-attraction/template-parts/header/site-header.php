<?php
/**
 * Main site header: navigation, logo, actions.
 *
 * @package Scentral
 */

defined( 'ABSPATH' ) || exit;

$has_wc = class_exists( 'WooCommerce' );
?>
<header class="site-header" data-header>
	<div class="site-header__inner container">
		<div class="site-header__start">
			<button type="button" class="icon-button site-header__burger" data-open="mobile-menu" aria-controls="mobile-menu" aria-expanded="false" aria-label="<?php esc_attr_e( 'Open menu', 'scentral' ); ?>">
				<?php echo scentral_icon( 'menu' ); // phpcs:ignore ?>
			</button>
			<nav class="primary-nav" aria-label="<?php esc_attr_e( 'Primary', 'scentral' ); ?>">
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'primary',
						'container'      => false,
						'menu_class'     => 'menu',
						'depth'          => 2,
						'fallback_cb'    => 'scentral_primary_menu_fallback',
					)
				);
				?>
			</nav>
		</div>

		<div class="site-header__logo">
			<?php scentral_site_logo( 'header' ); ?>
		</div>

		<div class="site-header__actions">
			<button type="button" class="icon-button" data-open="search-overlay" aria-controls="search-overlay" aria-expanded="false" aria-label="<?php esc_attr_e( 'Search', 'scentral' ); ?>">
				<?php echo scentral_icon( 'search' ); // phpcs:ignore ?>
			</button>
			<?php if ( $has_wc ) : ?>
				<a class="icon-button hide-sm" href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" aria-label="<?php echo esc_attr( is_user_logged_in() ? __( 'My account', 'scentral' ) : __( 'Sign in', 'scentral' ) ); ?>">
					<?php echo scentral_icon( 'user' ); // phpcs:ignore ?>
				</a>
				<a class="icon-button hide-sm" href="<?php echo esc_url( scentral_wishlist_url() ); ?>" aria-label="<?php esc_attr_e( 'Wishlist', 'scentral' ); ?>">
					<?php echo scentral_icon( 'heart' ); // phpcs:ignore ?>
					<span class="wish-count is-empty" data-wish-count>0</span>
				</a>
				<a class="icon-button cart-toggle" href="<?php echo esc_url( wc_get_cart_url() ); ?>" data-open="cart-drawer" aria-controls="cart-drawer" aria-label="<?php esc_attr_e( 'Shopping bag', 'scentral' ); ?>">
					<?php echo scentral_icon( 'bag' ); // phpcs:ignore ?>
					<?php echo scentral_cart_count_html(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in helper. ?>
				</a>
			<?php endif; ?>
		</div>
	</div>
	<?php
	if ( $has_wc && scentral_option( 'header_mega_menu' ) ) {
		get_template_part( 'template-parts/header/mega-menu' );
	}
	?>
</header>
