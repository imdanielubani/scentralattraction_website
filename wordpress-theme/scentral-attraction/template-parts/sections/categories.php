<?php
/**
 * Shop by collection: an editorial collage of product categories (live from WooCommerce).
 *
 * @package Scentral
 */

defined( 'ABSPATH' ) || exit;

$categories = scentral_featured_categories();
if ( ! $categories ) {
	return;
}
?>
<section class="section collage" aria-labelledby="collage-title">
	<div class="container">
		<?php
		scentral_section_heading(
			array(
				'eyebrow'    => __( 'Shop by collection', 'scentral' ),
				'title'      => __( 'Find your *signature*', 'scentral' ),
				'text'       => __( 'From deep, resinous oud to delicate florals — each collection carefully selected.', 'scentral' ),
				'link_url'   => scentral_shop_url(),
				'link_label' => __( 'All fragrances', 'scentral' ),
				'id'         => 'collage-title',
			)
		);
		?>
		<ul class="collage__grid collage__grid--<?php echo esc_attr( min( 4, count( $categories ) ) ); ?>" data-stagger>
			<?php foreach ( array_slice( $categories, 0, 4 ) as $index => $term ) : ?>
				<?php $thumb = scentral_category_image( $term, 'scentral-portrait' ); ?>
				<li class="collage__item" data-reveal>
					<a class="collage__link" href="<?php echo esc_url( get_term_link( $term ) ); ?>">
						<span class="collage__media">
							<?php if ( $thumb ) : ?>
								<img src="<?php echo esc_url( $thumb ); ?>" alt="" loading="lazy" decoding="async" width="900" height="1125">
							<?php else : ?>
								<?php scentral_emblem( array( 'class' => 'collage__emblem emblem' ) ); ?>
							<?php endif; ?>
						</span>
						<span class="collage__chip" aria-hidden="true"><?php echo esc_html( sprintf( '%02d', $index + 1 ) ); ?></span>
						<span class="collage__go" aria-hidden="true"><?php echo scentral_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?></span>
						<span class="collage__meta">
							<span class="collage__name"><?php echo esc_html( $term->name ); ?></span>
							<span class="collage__count">
								<?php
								/* translators: %d: number of fragrances. */
								echo esc_html( sprintf( _n( '%d fragrance', '%d fragrances', $term->count, 'scentral' ), $term->count ) );
								?>
							</span>
						</span>
					</a>
				</li>
			<?php endforeach; ?>
			<li class="collage__item collage__item--new" data-reveal>
				<a class="collage__new" href="<?php echo esc_url( add_query_arg( 'orderby', 'date', scentral_shop_url() ) ); ?>">
					<span class="collage__new-kicker"><?php esc_html_e( 'Just arrived', 'scentral' ); ?></span>
					<strong class="collage__new-title"><?php esc_html_e( 'New arrivals', 'scentral' ); ?></strong>
					<span class="collage__new-text"><?php esc_html_e( 'The latest scents in the collection.', 'scentral' ); ?></span>
					<span class="collage__go collage__go--light" aria-hidden="true"><?php echo scentral_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?></span>
				</a>
			</li>
		</ul>
	</div>
</section>
