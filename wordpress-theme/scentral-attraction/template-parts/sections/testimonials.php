<?php
/**
 * Client words: genuine, approved WooCommerce product reviews only.
 * Hidden entirely when no qualifying review exists. Nothing here is ever written by the theme.
 *
 * @package Scentral
 */

defined( 'ABSPATH' ) || exit;

$reviews = scentral_get_reviews( 6 );
if ( ! $reviews ) {
	return;
}
?>
<section class="section voices" aria-labelledby="voices-title">
	<div class="container">
		<?php
		scentral_section_heading(
			array(
				'eyebrow' => __( 'Client words', 'scentral' ),
				'title'   => __( 'In their *own* words', 'scentral' ),
				'text'    => __( 'Verified reviews from our product pages.', 'scentral' ),
				'id'      => 'voices-title',
			)
		);
		?>
		<ul class="voices__list" data-stagger>
			<?php foreach ( $reviews as $review ) : ?>
				<?php
				$rating  = (int) get_comment_meta( $review->comment_ID, 'rating', true );
				$product = wc_get_product( $review->comment_post_ID );
				?>
				<li class="voice" data-reveal>
					<figure>
						<div class="star-rating" role="img" aria-label="<?php echo esc_attr( sprintf( /* translators: %d: rating. */ __( 'Rated %d out of 5', 'scentral' ), $rating ) ); ?>"><span style="width:<?php echo esc_attr( $rating * 20 ); ?>%"></span></div>
						<blockquote class="voice__text"><?php echo esc_html( wp_trim_words( wp_strip_all_tags( $review->comment_content ), 48 ) ); ?></blockquote>
						<figcaption class="voice__by">
							<span class="voice__name"><?php echo esc_html( $review->comment_author ); ?></span>
							<?php if ( $product ) : ?>
								<a class="voice__product" href="<?php echo esc_url( $product->get_permalink() ); ?>"><?php echo esc_html( $product->get_name() ); ?></a>
							<?php endif; ?>
						</figcaption>
					</figure>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
