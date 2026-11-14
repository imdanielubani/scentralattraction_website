<?php
/**
 * Luxury showcase: a scroll-linked zoom from inset frame to full bleed, then a curated trio.
 *
 * @package Scentral
 */

defined( 'ABSPATH' ) || exit;

$category = scentral_category_with_products( scentral_option( 'showcase_category' ) );
$image    = scentral_resolve_image( 'showcase_image', 'showcase-luxury', 'scentral-hero' );
$products = $category ? scentral_get_products(
	array(
		'source'   => 'category',
		'category' => $category->slug,
		'limit'    => 3,
	)
) : array();

if ( empty( $image['src'] ) && ! $products ) {
	return;
}
$link = $category ? get_term_link( $category ) : scentral_shop_url();
?>
<section class="showcase" aria-labelledby="showcase-title">
	<?php if ( ! empty( $image['src'] ) ) : ?>
		<div class="showcase__stage" data-zoom>
			<div class="showcase__sticky">
				<div class="showcase__frame">
					<?php scentral_picture( $image, __( 'A faceted black perfume bottle on black marble among red rose petals', 'scentral' ), array( 'class' => 'showcase__picture' ) ); ?>
				</div>
				<div class="showcase__caption container">
					<p class="eyebrow"><?php echo esc_html( $category ? $category->name : __( 'The collection', 'scentral' ) ); ?></p>
					<h2 class="showcase__title" id="showcase-title"><?php echo scentral_rich_title( scentral_option( 'showcase_title' ) ); // phpcs:ignore ?></h2>
				</div>
			</div>
		</div>
	<?php endif; ?>

	<div class="showcase__after section section--dark">
		<div class="container showcase__grid">
			<div class="showcase__intro" data-reveal>
				<?php if ( empty( $image['src'] ) ) : ?>
					<h2 class="showcase__title" id="showcase-title"><?php echo scentral_rich_title( scentral_option( 'showcase_title' ) ); // phpcs:ignore ?></h2>
				<?php endif; ?>
				<p class="lead"><?php echo esc_html( scentral_option( 'showcase_text' ) ); ?></p>
				<a class="button button--ghost-light" href="<?php echo esc_url( $link ); ?>"><?php esc_html_e( 'Explore the collection', 'scentral' ); ?></a>
			</div>
			<?php if ( $products ) : ?>
				<ul class="showcase__products" data-stagger>
					<?php foreach ( $products as $item ) : ?>
						<li data-reveal>
							<a class="mini-card" href="<?php echo esc_url( $item->get_permalink() ); ?>">
								<span class="mini-card__media"><?php echo wp_kses_post( $item->get_image( 'woocommerce_thumbnail', array( 'loading' => 'lazy' ) ) ); ?></span>
								<span class="mini-card__name"><?php echo esc_html( $item->get_name() ); ?></span>
								<span class="mini-card__price"><?php echo wp_kses_post( $item->get_price_html() ); ?></span>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>
	</div>
</section>
