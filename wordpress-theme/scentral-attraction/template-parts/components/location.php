<?php
/**
 * Store location: Google Map + details card (modelled on the reference "showrooms" block).
 *
 * @package Scentral
 */

defined( 'ABSPATH' ) || exit;

$query   = trim( (string) scentral_option( 'contact_map_query' ) );
$address = scentral_option( 'contact_address' );
$phone   = scentral_option( 'contact_phone' );
$hours   = scentral_option( 'contact_hours' );
if ( '' === $query ) {
	$query = $address;
}
if ( '' === $query ) {
	return;
}
$embed      = 'https://maps.google.com/maps?q=' . rawurlencode( $query ) . '&z=16&output=embed';
$directions = scentral_url_option( 'contact_map_url', 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode( $query ) );
?>
<section class="loc container" aria-labelledby="loc-title">
	<div class="loc__stage" data-reveal>
		<iframe class="loc__map" src="<?php echo esc_url( $embed ); ?>" title="<?php echo esc_attr( sprintf( /* translators: %s: address. */ __( 'Map: %s', 'scentral' ), $query ) ); ?>" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
		<?php if ( $hours ) : ?>
			<span class="loc__badge"><i aria-hidden="true"></i><?php echo esc_html( $hours ); ?></span>
		<?php endif; ?>
	</div>
	<div class="loc__card" data-reveal>
		<p class="eyebrow"><?php esc_html_e( 'Visit us', 'scentral' ); ?></p>
		<h2 class="loc__city" id="loc-title"><?php esc_html_e( 'Abuja', 'scentral' ); ?></h2>
		<p class="loc__note"><?php esc_html_e( 'Come and discover the collection in person — we are happy to help you find your signature scent.', 'scentral' ); ?></p>
		<dl class="loc__list">
			<?php if ( $address ) : ?>
				<div><dt><?php esc_html_e( 'Address', 'scentral' ); ?></dt><dd><?php echo esc_html( $address ); ?></dd></div>
			<?php endif; ?>
			<?php if ( $phone ) : ?>
				<div><dt><?php esc_html_e( 'Phone', 'scentral' ); ?></dt><dd><a href="<?php echo esc_attr( scentral_tel( $phone ) ); ?>"><?php echo esc_html( $phone ); ?></a></dd></div>
			<?php endif; ?>
			<?php if ( $hours ) : ?>
				<div><dt><?php esc_html_e( 'Hours', 'scentral' ); ?></dt><dd><?php echo esc_html( $hours ); ?></dd></div>
			<?php endif; ?>
		</dl>
		<div class="loc__btns">
			<a class="pill-btn" href="<?php echo esc_url( $directions ); ?>" target="_blank" rel="noopener noreferrer"><span><?php esc_html_e( 'Get directions', 'scentral' ); ?></span><i class="pill-btn__arrow" aria-hidden="true"><?php echo scentral_icon( 'arrow' ); // phpcs:ignore ?></i><span class="screen-reader-text"><?php esc_html_e( '(opens Google Maps in a new tab)', 'scentral' ); ?></span></a>
			<?php if ( $phone ) : ?>
				<a class="loc__call link-arrow" href="<?php echo esc_attr( scentral_tel( $phone ) ); ?>"><?php esc_html_e( 'Call us', 'scentral' ); ?><?php echo scentral_icon( 'arrow' ); // phpcs:ignore ?></a>
			<?php endif; ?>
		</div>
	</div>
</section>
