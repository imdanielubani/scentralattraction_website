<?php
/**
 * Homepage hero: campaign image, word-reveal headline and a live "In this edit" product index.
 *
 * @package Scentral
 */

defined( 'ABSPATH' ) || exit;

$image    = scentral_hero_image();
$cta_url  = scentral_url_option( 'hero_cta_url', scentral_shop_url() );
$cta2_url = scentral_url_option( 'hero_cta2_url', scentral_page_url( 'about-us' ) );
$products = scentral_get_products(
	array(
		'source' => scentral_option( 'hero_index_source' ),
		'ids'    => scentral_option( 'hero_index_ids' ),
		'limit'  => 4,
	)
);
if ( ! $products && 'newest' !== scentral_option( 'hero_index_source' ) ) {
	$products = scentral_get_products(
		array(
			'source' => 'newest',
			'limit'  => 4,
		)
	);
}
?>
<section class="hero<?php echo empty( $image['src'] ) ? ' hero--no-image' : ''; ?>" data-hero aria-labelledby="hero-title">
	<div class="hero__media" data-hero-image>
		<?php if ( ! empty( $image['src'] ) ) : ?>
			<?php scentral_picture( $image, __( 'An amber perfume bottle on dark stone with oud wood and a curl of smoke', 'scentral' ), array( 'class' => 'hero__picture', 'eager' => true ) ); ?>
		<?php else : ?>
			<?php scentral_emblem( array( 'class' => 'hero__emblem emblem', 'draw' => true ) ); ?>
		<?php endif; ?>
	</div>
	<div class="hero__veil" aria-hidden="true"></div>

	<div class="hero__content container">
		<div class="hero__copy">
			<?php if ( scentral_option( 'hero_eyebrow' ) ) : ?>
				<p class="eyebrow hero__eyebrow" data-reveal><?php echo esc_html( scentral_option( 'hero_eyebrow' ) ); ?></p>
			<?php endif; ?>
			<h1 class="hero__title split" id="hero-title" style="--split-delay:150ms">
				<?php echo scentral_split_words( scentral_option( 'hero_title' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in helper. ?>
			</h1>
			<?php if ( scentral_option( 'hero_text' ) ) : ?>
				<p class="hero__text" data-reveal style="--stagger:4"><?php echo esc_html( scentral_option( 'hero_text' ) ); ?></p>
			<?php endif; ?>
			<div class="hero__actions" data-reveal style="--stagger:5">
				<?php if ( scentral_option( 'hero_cta_label' ) ) : ?>
					<a class="button button--light" href="<?php echo esc_url( $cta_url ); ?>"><?php echo esc_html( scentral_option( 'hero_cta_label' ) ); ?></a>
				<?php endif; ?>
				<?php if ( scentral_option( 'hero_cta2_label' ) ) : ?>
					<a class="link-arrow" href="<?php echo esc_url( $cta2_url ); ?>"><?php echo esc_html( scentral_option( 'hero_cta2_label' ) ); ?><?php echo scentral_icon( 'arrow' ); // phpcs:ignore ?></a>
				<?php endif; ?>
			</div>
		</div>

		<?php if ( $products ) : ?>
			<aside class="hero__index" data-reveal style="--stagger:6" aria-label="<?php esc_attr_e( 'In this edit', 'scentral' ); ?>">
				<p class="hero__index-head">
					<span class="eyebrow"><?php esc_html_e( 'In this edit', 'scentral' ); ?></span>
					<span class="hero__index-count"><?php echo esc_html( sprintf( '%02d', count( $products ) ) ); ?> <small><?php esc_html_e( 'scents', 'scentral' ); ?></small></span>
				</p>
				<ol class="hero__index-list">
					<?php foreach ( $products as $index => $item ) : ?>
						<li>
							<a href="<?php echo esc_url( $item->get_permalink() ); ?>">
								<span class="hero__index-num"><?php echo esc_html( sprintf( '%02d', $index + 1 ) ); ?></span>
								<span class="hero__index-name"><?php echo esc_html( $item->get_name() ); ?></span>
								<span class="hero__index-price"><?php echo wp_kses_post( $item->get_price_html() ); ?></span>
							</a>
						</li>
					<?php endforeach; ?>
				</ol>
			</aside>
		<?php endif; ?>
	</div>

	<a class="hero__scroll" href="#after-hero" aria-label="<?php esc_attr_e( 'Scroll to content', 'scentral' ); ?>"><span><?php esc_html_e( 'Scroll', 'scentral' ); ?></span><i></i></a>
</section>
<div id="after-hero"></div>
