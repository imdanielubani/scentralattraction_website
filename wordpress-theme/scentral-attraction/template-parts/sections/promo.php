<?php
/**
 * Promotional banner (enable in Customizer → Homepage — Collections & Banners).
 *
 * @package Scentral
 */

defined( 'ABSPATH' ) || exit;

if ( ! scentral_option( 'promo_enabled' ) ) {
	return;
}
$image = scentral_resolve_image( 'promo_image', 'promo-gifting', 'scentral-hero' );
$url   = scentral_url_option( 'promo_url', scentral_shop_url() );
?>
<section class="promo" aria-labelledby="promo-title">
	<div class="promo__media">
		<div data-parallax="0.1"><?php scentral_picture( $image, '', array( 'class' => 'promo__picture' ) ); ?></div>
	</div>
	<div class="promo__content container" data-reveal>
		<?php if ( scentral_option( 'promo_eyebrow' ) ) : ?>
			<p class="eyebrow"><?php echo esc_html( scentral_option( 'promo_eyebrow' ) ); ?></p>
		<?php endif; ?>
		<h2 class="promo__title" id="promo-title"><?php echo scentral_rich_title( scentral_option( 'promo_title' ) ); // phpcs:ignore ?></h2>
		<?php if ( scentral_option( 'promo_text' ) ) : ?>
			<p class="promo__text"><?php echo esc_html( scentral_option( 'promo_text' ) ); ?></p>
		<?php endif; ?>
		<?php if ( scentral_option( 'promo_cta_label' ) ) : ?>
			<a class="button button--light" href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( scentral_option( 'promo_cta_label' ) ); ?></a>
		<?php endif; ?>
	</div>
</section>
