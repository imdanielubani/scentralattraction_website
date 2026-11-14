<?php
/**
 * Mobile navigation dialog.
 *
 * @package Scentral
 */

defined( 'ABSPATH' ) || exit;

$has_wc = class_exists( 'WooCommerce' );
$phone  = scentral_option( 'contact_phone' );
$email  = scentral_option( 'contact_email' );
?>
<dialog class="dialog dialog--menu" id="mobile-menu" aria-label="<?php esc_attr_e( 'Menu', 'scentral' ); ?>">
	<div class="dialog__head">
		<?php scentral_site_logo( 'footer' ); ?>
		<button type="button" class="dialog__close" data-close aria-label="<?php esc_attr_e( 'Close menu', 'scentral' ); ?>"><?php echo scentral_icon( 'close' ); // phpcs:ignore ?></button>
	</div>
	<nav class="mobile-nav" aria-label="<?php esc_attr_e( 'Mobile', 'scentral' ); ?>">
		<?php
		wp_nav_menu(
			array(
				'theme_location' => 'primary',
				'container'      => false,
				'menu_class'     => 'mobile-nav__list',
				'depth'          => 2,
				'fallback_cb'    => 'scentral_primary_menu_fallback',
			)
		);
		?>
	</nav>
	<?php if ( $has_wc ) : ?>
		<?php $categories = scentral_featured_categories(); ?>
		<?php if ( $categories ) : ?>
			<div class="mobile-nav__cats">
				<p class="eyebrow"><?php esc_html_e( 'Collections', 'scentral' ); ?></p>
				<ul>
					<?php foreach ( $categories as $term ) : ?>
						<li><a href="<?php echo esc_url( get_term_link( $term ) ); ?>"><?php echo esc_html( $term->name ); ?></a></li>
					<?php endforeach; ?>
				</ul>
			</div>
		<?php endif; ?>
		<ul class="mobile-nav__account">
			<li><a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>"><?php echo scentral_icon( 'user' ); // phpcs:ignore ?><?php echo esc_html( is_user_logged_in() ? __( 'My account', 'scentral' ) : __( 'Sign in / Register', 'scentral' ) ); ?></a></li>
			<li><a href="<?php echo esc_url( scentral_wishlist_url() ); ?>"><?php echo scentral_icon( 'heart' ); // phpcs:ignore ?><?php esc_html_e( 'Wishlist', 'scentral' ); ?></a></li>
		</ul>
	<?php endif; ?>
	<div class="mobile-nav__foot">
		<?php if ( $phone ) : ?>
			<a href="<?php echo esc_attr( scentral_tel( $phone ) ); ?>"><?php echo esc_html( $phone ); ?></a>
		<?php endif; ?>
		<?php if ( $email ) : ?>
			<a href="mailto:<?php echo esc_attr( antispambot( $email ) ); ?>"><?php echo esc_html( antispambot( $email ) ); ?></a>
		<?php endif; ?>
		<?php scentral_social_list(); ?>
	</div>
</dialog>
