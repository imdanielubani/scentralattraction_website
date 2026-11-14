<?php
/**
 * Site header, modelled on the reference navigation:
 * a fixed header with a collapsible announcement strip and a floating rounded bar
 * (brand · pill links · Search + Bag), plus a floating Shop mega panel.
 *
 * @package Scentral
 */

defined( 'ABSPATH' ) || exit;

$has_wc = class_exists( 'WooCommerce' );
?>
<header class="site-header is-top" data-header>
	<?php get_template_part( 'template-parts/header/announcement' ); ?>
	<div class="nav-wrap">
		<div class="nav-bar">
			<div class="nav-bar__brand">
				<?php scentral_site_logo( 'header' ); ?>
			</div>

			<nav class="nav-links" aria-label="<?php esc_attr_e( 'Main', 'scentral' ); ?>">
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

			<div class="nav-act">
				<button type="button" class="nav-ic" data-open="search-overlay" aria-controls="search-overlay" aria-expanded="false" aria-label="<?php esc_attr_e( 'Search', 'scentral' ); ?>">
					<?php echo scentral_icon( 'search' ); // phpcs:ignore ?>
					<span><?php esc_html_e( 'Search', 'scentral' ); ?></span>
				</button>
				<?php if ( $has_wc ) : ?>
					<a class="nav-ic nav-ic--icon hide-md" href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" aria-label="<?php echo esc_attr( is_user_logged_in() ? __( 'My account', 'scentral' ) : __( 'Sign in', 'scentral' ) ); ?>">
						<?php echo scentral_icon( 'user' ); // phpcs:ignore ?>
					</a>
					<a class="nav-ic nav-ic--icon hide-md" href="<?php echo esc_url( scentral_wishlist_url() ); ?>" aria-label="<?php esc_attr_e( 'Wishlist', 'scentral' ); ?>">
						<?php echo scentral_icon( 'heart' ); // phpcs:ignore ?>
						<span class="wish-count is-empty" data-wish-count>0</span>
					</a>
					<a class="nav-bag cart-toggle" href="<?php echo esc_url( wc_get_cart_url() ); ?>" data-open="cart-drawer" aria-controls="cart-drawer" aria-label="<?php esc_attr_e( 'Shopping bag', 'scentral' ); ?>">
						<span class="nav-bag__label"><?php esc_html_e( 'Bag', 'scentral' ); ?></span>
						<?php echo scentral_cart_count_html(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in helper. ?>
					</a>
				<?php endif; ?>
				<button type="button" class="nav-burger" data-open="mobile-menu" aria-controls="mobile-menu" aria-expanded="false" aria-label="<?php esc_attr_e( 'Menu', 'scentral' ); ?>">
					<i></i><i></i>
				</button>
			</div>
		</div>
		<?php
		if ( $has_wc && scentral_option( 'header_mega_menu' ) ) {
			get_template_part( 'template-parts/header/mega-menu' );
		}
		?>
	</div>
</header>
