<?php
/**
 * Template Name: Contact
 * Template Post Type: page
 *
 * Modelled on the reference contact page: pill breadcrumb + large headline with intro,
 * a rounded form card beside a dark "Other ways in" card. The page content (where the
 * Contact Form 7 shortcode lives) is used for the intro and the form; without a form plugin
 * the theme's own form is shown. Only verified business details are displayed.
 *
 * @package Scentral
 */

defined( 'ABSPATH' ) || exit;

get_header();

if ( scentral_render_builder_page() ) {
	get_footer();
	return;
}

?>
<main id="main" class="site-main site-main--contact">
	<?php
	while ( have_posts() ) :
		the_post();
		$content  = (string) get_the_content();
		$has_form = has_shortcode( $content, 'contact-form-7' ) || has_block( 'contact-form-7/contact-form-selector' ) || has_shortcode( $content, 'wpforms' ) || has_shortcode( $content, 'gravityform' );
		// Intro = the page's first paragraph (the store's own words); the rest of the content is the form.
		$intro = '';
		if ( preg_match( '/<p[^>]*>(.*?)<\/p>/is', apply_filters( 'the_content', $content ), $m ) ) { // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- core filter.
			$intro = wp_strip_all_tags( $m[1] );
		}
		?>
		<?php
		get_template_part(
			'template-parts/components/page-head',
			null,
			array(
				'id'    => 'contact-title',
				'title' => __( 'Contact *us*.', 'scentral' ),
				'intro' => $intro,
			)
		);
		?>

		<section class="cf container" id="contact-form" aria-labelledby="contact-form-title">
			<div class="cf__card" data-reveal>
				<p class="eyebrow cf__eyebrow"><i aria-hidden="true"></i><?php esc_html_e( 'Write to us', 'scentral' ); ?></p>
				<h2 class="cf__title" id="contact-form-title"><?php echo scentral_rich_title( __( 'Tell us what you are *looking for*.', 'scentral' ) ); // phpcs:ignore ?></h2>
				<?php if ( $has_form ) : ?>
					<div class="cf__plugin">
						<?php
						// Render only the form part of the content (the intro paragraph is shown above).
						echo do_shortcode( preg_replace( '/<!-- wp:paragraph -->.*?<!-- \/wp:paragraph -->/s', '', $content, 1 ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- form plugin output.
						?>
					</div>
				<?php else : ?>
					<?php scentral_contact_form(); ?>
				<?php endif; ?>
			</div>

			<?php
			$perks = array(
				array(
					'icon' => 'pin',
					'text' => __( 'Visit our store in Dawaki, Abuja', 'scentral' ),
				),
				array(
					'icon' => 'drop',
					'text' => __( 'Ask us to help you choose a signature scent', 'scentral' ),
				),
			);
			if ( function_exists( 'wc_get_page_permalink' ) ) {
				$perks[] = array(
					'icon' => 'truck',
					'text' => __( 'Track your order online', 'scentral' ),
					'url'  => scentral_page_url( 'order-tracking' ),
				);
			}
			get_template_part( 'template-parts/components/help-card', null, array( 'perks' => $perks ) );
			?>
		</section>

		<?php get_template_part( 'template-parts/components/location' ); ?>
		<?php do_action( 'scentral_contact_after_form' ); ?>
	<?php endwhile; ?>
</main>
<?php
get_footer();
