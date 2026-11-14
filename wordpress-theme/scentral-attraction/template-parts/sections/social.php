<?php
/**
 * Social: Instagram tiles chosen from the Media Library (no third-party embed or tracking),
 * or a simple follow band when no tiles are set.
 *
 * @package Scentral
 */

defined( 'ABSPATH' ) || exit;

$instagram = scentral_option( 'social_instagram' );
if ( ! $instagram ) {
	return;
}
$ids    = array_slice( array_filter( array_map( 'absint', explode( ',', (string) scentral_option( 'home_social_images' ) ) ) ), 0, 6 );
$handle = '@' . trim( (string) wp_parse_url( $instagram, PHP_URL_PATH ), '/' );
?>
<section class="section social<?php echo $ids ? '' : ' social--band'; ?>" aria-labelledby="social-title">
	<div class="container">
		<div class="social__head" data-reveal>
			<p class="eyebrow"><?php esc_html_e( 'Instagram', 'scentral' ); ?></p>
			<h2 class="social__title" id="social-title"><?php echo scentral_rich_title( scentral_option( 'home_social_title' ) ); // phpcs:ignore ?></h2>
			<a class="social__handle link-arrow" href="<?php echo esc_url( $instagram ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( $handle ); ?><?php echo scentral_icon( 'arrow' ); // phpcs:ignore ?></a>
		</div>
		<?php if ( $ids ) : ?>
			<ul class="social__grid" data-stagger>
				<?php foreach ( $ids as $image_id ) : ?>
					<li data-reveal>
						<a href="<?php echo esc_url( $instagram ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr( sprintf( /* translators: %s: Instagram handle. */ __( 'View %s on Instagram (opens in a new tab)', 'scentral' ), $handle ) ); ?>">
							<?php echo wp_get_attachment_image( $image_id, 'scentral-card', false, array( 'loading' => 'lazy' ) ); ?>
							<span class="social__icon" aria-hidden="true"><?php echo scentral_icon( 'instagram' ); // phpcs:ignore ?></span>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	</div>
</section>
