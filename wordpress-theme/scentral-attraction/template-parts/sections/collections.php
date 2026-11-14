<?php
/**
 * Collections: stacked sticky panels (For Him / For Her / Shared when configured,
 * otherwise the store's own collections). A panel renders only when its category has products.
 *
 * @package Scentral
 */

defined( 'ABSPATH' ) || exit;

$fallback_art = array( 'collection-men', 'collection-women', 'collection-unisex' );
$panels       = array();

for ( $n = 1; $n <= 3; $n++ ) {
	$term = scentral_category_with_products( scentral_option( 'collection_' . $n . '_category' ) );
	if ( $term ) {
		$panels[] = array(
			'term'  => $term,
			'title' => scentral_option( 'collection_' . $n . '_title' ),
			'image' => scentral_resolve_image( 'collection_' . $n . '_image', $fallback_art[ $n - 1 ] ),
		);
	}
}

// No gender collections configured yet: present the store's real collections instead.
if ( ! $panels ) {
	$art = array( 'campaign-bestsellers', 'collection-unisex', 'showcase-luxury', 'collection-women' );
	foreach ( array_slice( scentral_featured_categories(), 0, 3 ) as $i => $term ) {
		$panels[] = array(
			'term'  => $term,
			'title' => $term->name,
			'image' => scentral_campaign_image( $art[ $i ] ),
		);
	}
}
if ( ! $panels ) {
	return;
}
?>
<section class="stack" aria-label="<?php esc_attr_e( 'Collections', 'scentral' ); ?>">
	<?php foreach ( $panels as $index => $panel ) : ?>
		<article class="stack__panel" style="--i:<?php echo esc_attr( $index ); ?>">
			<div class="stack__media">
				<?php if ( ! empty( $panel['image']['src'] ) ) : ?>
					<?php scentral_picture( $panel['image'], '', array( 'class' => 'stack__picture' ) ); ?>
				<?php endif; ?>
			</div>
			<div class="stack__content container">
				<p class="stack__num"><?php echo esc_html( sprintf( '%02d / %02d', $index + 1, count( $panels ) ) ); ?></p>
				<h2 class="stack__title"><?php echo esc_html( $panel['title'] ); ?></h2>
				<p class="stack__meta">
					<?php
					/* translators: %d: number of fragrances. */
					echo esc_html( sprintf( _n( '%d fragrance', '%d fragrances', $panel['term']->count, 'scentral' ), $panel['term']->count ) );
					?>
				</p>
				<a class="button button--light" href="<?php echo esc_url( get_term_link( $panel['term'] ) ); ?>">
					<?php
					/* translators: %s: collection name. */
					echo esc_html( sprintf( __( 'Shop %s', 'scentral' ), $panel['term']->name ) );
					?>
				</a>
			</div>
		</article>
	<?php endforeach; ?>
</section>
