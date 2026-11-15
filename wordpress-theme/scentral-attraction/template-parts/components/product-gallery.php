<?php
/**
 * Product gallery shared by the product page and quick view (reference product gallery):
 * vertical thumbnails beside a large rounded stage, previous / next arrow buttons, an image
 * counter and, on the product page, hover zoom. Works without JavaScript (first image shown).
 *
 * Args: product (WC_Product), context ('single' | 'quick').
 *
 * @package Scentral
 */

defined( 'ABSPATH' ) || exit;

$args    = wp_parse_args( $args ?? array(), array( 'product' => null, 'context' => 'single' ) );
$product = $args['product'];
if ( ! $product instanceof WC_Product ) {
	return;
}

$context = 'quick' === $args['context'] ? 'quick' : 'single';
$ids     = array_values( array_unique( array_filter( array_merge( array( (int) $product->get_image_id() ), array_map( 'intval', $product->get_gallery_image_ids() ) ) ) ) );
$count   = count( $ids );
$badge   = scentral_product_badge( $product );
$name    = $product->get_name();
?>
<div class="pg pg--<?php echo esc_attr( $context ); ?><?php echo $count > 1 ? ' pg--multi' : ''; ?>" data-gallery data-gallery-context="<?php echo esc_attr( $context ); ?>">
	<?php if ( $count > 1 ) : ?>
		<div class="pg__thumbs" role="group" aria-label="<?php esc_attr_e( 'Product images', 'scentral' ); ?>">
			<?php foreach ( $ids as $i => $id ) : ?>
				<button type="button" class="pg__thumb<?php echo 0 === $i ? ' is-on' : ''; ?>" data-gallery-go="<?php echo esc_attr( $i ); ?>" aria-label="<?php echo esc_attr( sprintf( /* translators: 1: image number, 2: total. */ __( 'Show image %1$d of %2$d', 'scentral' ), $i + 1, $count ) ); ?>"<?php echo 0 === $i ? ' aria-current="true"' : ''; ?>>
					<?php echo wp_get_attachment_image( $id, 'woocommerce_gallery_thumbnail', false, array( 'alt' => '', 'loading' => 'lazy' ) ); ?>
				</button>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>

	<div class="pg__stage"<?php echo 'single' === $context ? ' data-gallery-zoom' : ''; ?> aria-roledescription="<?php esc_attr_e( 'carousel', 'scentral' ); ?>" aria-label="<?php echo esc_attr( $name ); ?>">
		<?php if ( $ids ) : ?>
			<?php foreach ( $ids as $i => $id ) : ?>
				<?php
				echo wp_get_attachment_image(
					$id,
					'woocommerce_single',
					false,
					array(
						'class'         => 'pg__img' . ( 0 === $i ? ' is-on' : '' ),
						'loading'       => 0 === $i ? 'eager' : 'lazy',
						'fetchpriority' => 0 === $i && 'single' === $context ? 'high' : 'auto',
						'alt'           => 0 === $i ? $name : sprintf( /* translators: 1: product name, 2: image number. */ __( '%1$s — image %2$d', 'scentral' ), $name, $i + 1 ),
						'data-index'    => $i,
						'aria-hidden'   => 0 === $i ? 'false' : 'true',
					)
				);
				?>
			<?php endforeach; ?>
		<?php else : ?>
			<?php echo wc_placeholder_img( 'woocommerce_single', array( 'class' => 'pg__img is-on' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core output. ?>
		<?php endif; ?>

		<?php if ( $badge ) : ?>
			<div class="pg__badge"><?php echo wp_kses_post( $badge ); ?></div>
		<?php endif; ?>

		<?php if ( $count > 1 ) : ?>
			<button type="button" class="pg__nav pg__nav--prev" data-gallery-prev aria-label="<?php esc_attr_e( 'Previous image', 'scentral' ); ?>"><?php echo scentral_icon( 'arrow-left' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?></button>
			<button type="button" class="pg__nav pg__nav--next" data-gallery-next aria-label="<?php esc_attr_e( 'Next image', 'scentral' ); ?>"><?php echo scentral_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?></button>
			<p class="pg__count" aria-live="polite"><span data-gallery-current>1</span> / <?php echo esc_html( $count ); ?></p>
		<?php endif; ?>

		<?php if ( 'single' === $context ) : ?>
			<span class="pg__hint" aria-hidden="true"><?php esc_html_e( 'Hover to zoom', 'scentral' ); ?></span>
		<?php endif; ?>
	</div>
</div>
