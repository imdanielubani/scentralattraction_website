<?php
/**
 * Site footer (revealed beneath the page like a curtain on large screens).
 *
 * @package Scentral
 */

defined( 'ABSPATH' ) || exit;

$address  = scentral_option( 'contact_address' );
$phone    = scentral_option( 'contact_phone' );
$email    = scentral_option( 'contact_email' );
$hours    = scentral_option( 'contact_hours' );
$map      = scentral_option( 'contact_map_url' );
$payments = scentral_option( 'footer_payments_note' );
?>
<footer class="site-footer" data-footer>
	<div class="site-footer__inner container">
		<div class="site-footer__brand">
			<?php scentral_site_logo( 'footer' ); ?>
			<p class="site-footer__about"><?php echo esc_html( scentral_option( 'footer_about' ) ); ?></p>
			<?php scentral_social_list( 'social-list social-list--footer' ); ?>
		</div>

		<nav class="site-footer__col" aria-label="<?php esc_attr_e( 'Shop', 'scentral' ); ?>">
			<h2 class="site-footer__title"><?php esc_html_e( 'Shop', 'scentral' ); ?></h2>
			<?php
			if ( has_nav_menu( 'footer-shop' ) ) {
				wp_nav_menu(
					array(
						'theme_location' => 'footer-shop',
						'container'      => false,
						'menu_class'     => 'footer-menu',
						'depth'          => 1,
					)
				);
			} else {
				echo '<ul class="footer-menu"><li><a href="' . esc_url( scentral_shop_url() ) . '">' . esc_html__( 'All fragrances', 'scentral' ) . '</a></li>';
				foreach ( scentral_featured_categories() as $term ) {
					echo '<li><a href="' . esc_url( get_term_link( $term ) ) . '">' . esc_html( $term->name ) . '</a></li>';
				}
				echo '</ul>';
			}
			?>
		</nav>

		<nav class="site-footer__col" aria-label="<?php esc_attr_e( 'Help', 'scentral' ); ?>">
			<h2 class="site-footer__title"><?php esc_html_e( 'Client care', 'scentral' ); ?></h2>
			<?php
			if ( has_nav_menu( 'footer-help' ) ) {
				wp_nav_menu(
					array(
						'theme_location' => 'footer-help',
						'container'      => false,
						'menu_class'     => 'footer-menu',
						'depth'          => 1,
					)
				);
			} else {
				$links = array(
					scentral_page_url( 'about-us' )   => __( 'About us', 'scentral' ),
					scentral_page_url( 'contact-us' ) => __( 'Contact us', 'scentral' ),
				);
				if ( class_exists( 'WooCommerce' ) ) {
					$links[ wc_get_page_permalink( 'myaccount' ) ] = __( 'My account', 'scentral' );
					$links[ scentral_page_url( 'order-tracking' ) ] = __( 'Order tracking', 'scentral' );
				}
				echo '<ul class="footer-menu">';
				foreach ( $links as $url => $label ) {
					echo '<li><a href="' . esc_url( $url ) . '">' . esc_html( $label ) . '</a></li>';
				}
				echo '</ul>';
			}
			?>
		</nav>

		<div class="site-footer__col site-footer__contact">
			<h2 class="site-footer__title"><?php esc_html_e( 'Visit', 'scentral' ); ?></h2>
			<ul class="contact-list">
				<?php if ( $address ) : ?>
					<li><?php echo scentral_icon( 'pin' ); // phpcs:ignore ?><?php echo $map ? '<a href="' . esc_url( $map ) . '" target="_blank" rel="noopener noreferrer">' . esc_html( $address ) . '</a>' : esc_html( $address ); ?></li>
				<?php endif; ?>
				<?php if ( $phone ) : ?>
					<li><?php echo scentral_icon( 'phone' ); // phpcs:ignore ?><a href="<?php echo esc_attr( scentral_tel( $phone ) ); ?>"><?php echo esc_html( $phone ); ?></a></li>
				<?php endif; ?>
				<?php if ( $email ) : ?>
					<li><?php echo scentral_icon( 'mail' ); // phpcs:ignore ?><a href="mailto:<?php echo esc_attr( antispambot( $email ) ); ?>"><?php echo esc_html( antispambot( $email ) ); ?></a></li>
				<?php endif; ?>
				<?php if ( $hours ) : ?>
					<li><?php echo scentral_icon( 'clock' ); // phpcs:ignore ?><?php echo esc_html( $hours ); ?></li>
				<?php endif; ?>
			</ul>
		</div>
	</div>

	<div class="site-footer__mark" aria-hidden="true">
		<span><?php esc_html_e( 'Scentral Attraction', 'scentral' ); ?></span>
	</div>

	<div class="site-footer__legal container">
		<p>&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php echo esc_html( scentral_option( 'footer_copyright' ) ); ?></p>
		<?php
		if ( has_nav_menu( 'legal' ) ) {
			wp_nav_menu(
				array(
					'theme_location' => 'legal',
					'container'      => 'nav',
					'container_class' => 'legal-nav',
					'menu_class'     => 'legal-nav__list',
					'depth'          => 1,
				)
			);
		} elseif ( get_privacy_policy_url() ) {
			echo '<nav class="legal-nav"><ul class="legal-nav__list"><li>' . get_the_privacy_policy_link() . '</li></ul></nav>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core output.
		}
		?>
		<?php if ( $payments ) : ?>
			<p class="site-footer__payments"><?php echo scentral_icon( 'shield' ); // phpcs:ignore ?><?php echo esc_html( $payments ); ?></p>
		<?php endif; ?>
	</div>
</footer>
