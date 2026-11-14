<?php
/**
 * Brand introduction: an editorial split built from the brand's own words.
 *
 * @package Scentral
 */

defined( 'ABSPATH' ) || exit;

$image = scentral_resolve_image( 'home_intro_image', 'editorial-story', 'scentral-portrait' );
?>
<section class="section intro" aria-labelledby="intro-title">
	<div class="container intro__grid">
		<div class="intro__quote">
			<p class="eyebrow" data-reveal><?php esc_html_e( 'Our philosophy', 'scentral' ); ?></p>
			<h2 class="intro__title split" id="intro-title"><?php echo scentral_split_words( scentral_option( 'home_intro_title' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h2>
		</div>
		<?php if ( ! empty( $image['src'] ) ) : ?>
			<figure class="intro__media" data-reveal="clip">
				<div data-parallax="0.08"><?php scentral_picture( $image, __( 'Fragrance ingredients — oud wood, dried rose and amber resin — arranged around a perfume bottle', 'scentral' ), array( 'sizes' => '(min-width: 900px) 40vw, 100vw' ) ); ?></div>
			</figure>
		<?php endif; ?>
		<div class="intro__text" data-reveal>
			<p class="lead"><?php echo esc_html( scentral_option( 'home_intro_text' ) ); ?></p>
			<a class="link-arrow" href="<?php echo esc_url( scentral_page_url( 'about-us' ) ); ?>"><?php esc_html_e( 'Read our story', 'scentral' ); ?><?php echo scentral_icon( 'arrow' ); // phpcs:ignore ?></a>
		</div>
	</div>
</section>
