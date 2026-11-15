<?php
/**
 * "Can't decide?" tile placed inside the shop grid (reference shop page promo tile).
 * Points to the store's real help channels: WhatsApp when set, otherwise the Contact page.
 *
 * @package Scentral
 */

defined( 'ABSPATH' ) || exit;

$whatsapp = preg_replace( '/[^0-9]/', '', (string) scentral_option( 'contact_whatsapp' ) );
$url      = $whatsapp ? 'https://wa.me/' . $whatsapp : scentral_page_url( 'contact-us' );
?>
<li class="shop-tile" data-reveal>
	<span class="shop-tile__kicker"><?php esc_html_e( 'Can’t decide?', 'scentral' ); ?></span>
	<strong class="shop-tile__title"><?php esc_html_e( 'Let us help you find your signature scent.', 'scentral' ); ?></strong>
	<p class="shop-tile__text"><?php esc_html_e( 'Tell us what you love — fresh, sweet, woody or oud — and we will suggest a few to try.', 'scentral' ); ?></p>
	<a class="pill-btn pill-btn--light" href="<?php echo esc_url( $url ); ?>"<?php echo $whatsapp ? ' target="_blank" rel="noopener noreferrer"' : ''; ?>><span><?php esc_html_e( 'Ask us', 'scentral' ); ?></span><i class="pill-btn__arrow" aria-hidden="true"><?php echo scentral_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?></i></a>
</li>
