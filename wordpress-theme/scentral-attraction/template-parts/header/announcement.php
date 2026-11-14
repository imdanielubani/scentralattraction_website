<?php
/**
 * Announcement bar with rotating messages and the utility menu.
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
$link  = scentral_option( 'announcement_link' );
$phone = scentral_option( 'contact_phone' );
?>
<div class="announcement" role="region" aria-label="<?php esc_attr_e( 'Announcements', 'scentral' ); ?>">
	<div class="announcement__inner container">
		<div class="announcement__rotator" data-rotator>
			<?php foreach ( $messages as $index => $message ) : ?>
				<p class="announcement__msg<?php echo 0 === $index ? ' is-active' : ''; ?>"<?php echo 0 === $index ? '' : ' aria-hidden="true"'; ?>>
					<?php if ( $link ) : ?>
						<a href="<?php echo esc_url( $link ); ?>"><?php echo esc_html( $message ); ?></a>
					<?php else : ?>
						<?php echo esc_html( $message ); ?>
					<?php endif; ?>
				</p>
			<?php endforeach; ?>
		</div>
		<div class="announcement__utility">
			<?php
			if ( has_nav_menu( 'utility' ) ) {
				wp_nav_menu(
					array(
						'theme_location' => 'utility',
						'container'      => 'nav',
						'container_attr' => array(),
						'container_class' => 'utility-nav',
						'menu_class'     => 'utility-nav__list',
						'depth'          => 1,
						'fallback_cb'    => false,
					)
				);
			}
			?>
			<?php if ( $phone ) : ?>
				<a class="announcement__phone" href="<?php echo esc_attr( scentral_tel( $phone ) ); ?>"><?php echo esc_html( $phone ); ?></a>
			<?php endif; ?>
		</div>
	</div>
</div>
