<?php
/**
 * Numbers: live store figures only (counted from WooCommerce, never typed in).
 *
 * @package Scentral
 */

defined( 'ABSPATH' ) || exit;

$numbers = scentral_store_numbers();
if ( count( $numbers ) < 2 ) {
	return;
}
$phone = scentral_option( 'contact_phone' );
?>
<section class="section section--dark numbers" aria-label="<?php esc_attr_e( 'The collection in numbers', 'scentral' ); ?>">
	<div class="container">
		<ul class="numbers__list" data-stagger>
			<?php foreach ( $numbers as $row ) : ?>
				<li class="numbers__item" data-reveal>
					<span class="numbers__value" data-count-to="<?php echo esc_attr( $row['value'] ); ?>"><?php echo esc_html( number_format_i18n( $row['value'] ) ); ?></span>
					<span class="numbers__label"><?php echo esc_html( $row['label'] ); ?></span>
				</li>
			<?php endforeach; ?>
			<?php if ( $phone ) : ?>
				<li class="numbers__item numbers__item--cta" data-reveal>
					<span class="numbers__label"><?php esc_html_e( 'Personal advice, one call away', 'scentral' ); ?></span>
					<a class="link-arrow" href="<?php echo esc_attr( scentral_tel( $phone ) ); ?>"><?php echo esc_html( $phone ); ?><?php echo scentral_icon( 'arrow' ); // phpcs:ignore ?></a>
				</li>
			<?php endif; ?>
		</ul>
	</div>
</section>
