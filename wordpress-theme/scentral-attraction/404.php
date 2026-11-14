<?php
/**
 * 404: a lost note.
 *
 * @package Scentral
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main id="main" class="site-main site-main--404">
	<section class="lost container">
		<div class="lost__art" aria-hidden="true">
			<span class="lost__num">4</span>
			<?php scentral_emblem( array( 'class' => 'lost__emblem emblem' ) ); ?>
			<span class="lost__num">4</span>
		</div>
		<p class="eyebrow"><?php esc_html_e( 'Error 404', 'scentral' ); ?></p>
		<h1 class="lost__title"><?php echo wp_kses_post( scentral_rich_title( __( 'This scent has *faded*.', 'scentral' ) ) ); ?></h1>
		<p class="lost__text"><?php esc_html_e( 'The page you are looking for has moved or no longer exists. Let us guide you back to the collection.', 'scentral' ); ?></p>
		<div class="lost__actions">
			<a class="button" href="<?php echo esc_url( scentral_shop_url() ); ?>"><?php esc_html_e( 'Explore the collection', 'scentral' ); ?></a>
			<a class="link-arrow" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Return home', 'scentral' ); ?><?php echo scentral_icon( 'arrow' ); // phpcs:ignore ?></a>
		</div>
		<div class="lost__search"><?php get_search_form(); ?></div>
	</section>
</main>
<?php
get_footer();
