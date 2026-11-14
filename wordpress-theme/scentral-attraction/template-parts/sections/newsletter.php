<?php
/**
 * Newsletter section.
 *
 * @package Scentral
 */

defined( 'ABSPATH' ) || exit;

$image = scentral_resolve_image( 'newsletter_image', 'newsletter-mist', 'scentral-portrait' );
?>
<section class="newsletter section--dark" id="newsletter" aria-labelledby="newsletter-title">
	<div class="newsletter__grid">
		<?php if ( ! empty( $image['src'] ) ) : ?>
			<div class="newsletter__media" data-reveal="clip">
				<?php scentral_picture( $image, __( 'A fine mist of perfume caught in red backlight', 'scentral' ), array( 'sizes' => '(min-width: 900px) 50vw, 100vw' ) ); ?>
			</div>
		<?php endif; ?>
		<div class="newsletter__content" data-reveal>
			<p class="eyebrow"><?php esc_html_e( 'Newsletter', 'scentral' ); ?></p>
			<h2 class="newsletter__title" id="newsletter-title"><?php echo scentral_rich_title( scentral_option( 'newsletter_title' ) ); // phpcs:ignore ?></h2>
			<p class="lead"><?php echo esc_html( scentral_option( 'newsletter_text' ) ); ?></p>
			<?php scentral_newsletter_form( 'home' ); ?>
		</div>
	</div>
</section>
