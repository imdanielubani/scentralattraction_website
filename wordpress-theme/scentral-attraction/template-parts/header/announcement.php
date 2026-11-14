<?php
/**
 * Announcement strip (inside the fixed header): rolling messages centred,
 * utility links on the right. Collapses once the page is scrolled.
 *
 * @package Scentral
 */

defined( 'ABSPATH' ) || exit;

if ( ! scentral_option( 'announcement_enabled' ) ) {
	return;
}
$messages = scentral_get_announcements();
if ( ! $messages ) {
	return;
}
$link = scentral_option( 'announcement_link' );
?>
<div class="announcement" role="region" aria-label="<?php esc_attr_e( 'Announcements', 'scentral' ); ?>">
	<div class="announcement__inner">
		<span class="announcement__spacer" aria-hidden="true"></span>
		<p class="announcement__roll" data-rotator>
			<?php foreach ( $messages as $index => $message ) : ?>
				<span class="announcement__msg<?php echo 0 === $index ? ' is-active' : ''; ?>"<?php echo 0 === $index ? '' : ' aria-hidden="true"'; ?>>
					<?php if ( $link ) : ?>
						<a href="<?php echo esc_url( $link ); ?>"><?php echo esc_html( $message ); ?></a>
					<?php else : ?>
						<?php echo esc_html( $message ); ?>
					<?php endif; ?>
				</span>
			<?php endforeach; ?>
		</p>
		<nav class="announcement__links" aria-label="<?php esc_attr_e( 'Utility', 'scentral' ); ?>">
			<?php
			if ( has_nav_menu( 'utility' ) ) {
				wp_nav_menu(
					array(
						'theme_location' => 'utility',
						'container'      => false,
						'menu_class'     => 'utility-list',
						'depth'          => 1,
						'fallback_cb'    => false,
					)
				);
			} else {
				$links = array(
					scentral_page_url( 'about-us' )   => __( 'About', 'scentral' ),
					scentral_page_url( 'contact-us' ) => __( 'Contact', 'scentral' ),
				);
				if ( class_exists( 'WooCommerce' ) ) {
					$links[ scentral_page_url( 'order-tracking' ) ] = __( 'Track order', 'scentral' );
				}
				echo '<ul class="utility-list">';
				foreach ( $links as $url => $label ) {
					echo '<li><a href="' . esc_url( $url ) . '">' . esc_html( $label ) . '</a></li>';
				}
				echo '</ul>';
			}
			?>
		</nav>
	</div>
</div>
