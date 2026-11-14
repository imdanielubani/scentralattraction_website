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

$phone    = scentral_option( 'contact_phone' );
$email    = scentral_option( 'contact_email' );
$hours    = scentral_option( 'contact_hours' );
$address  = scentral_option( 'contact_address' );
$whatsapp = preg_replace( '/[^0-9]/', '', (string) scentral_option( 'contact_whatsapp' ) );
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
		<section class="ph" aria-labelledby="contact-title">
			<div class="container">
				<nav class="ph__crumbs" aria-label="<?php esc_attr_e( 'Breadcrumb', 'scentral' ); ?>">
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'scentral' ); ?></a><i aria-hidden="true">/</i>
					<span aria-current="page"><?php the_title(); ?></span>
				</nav>
				<div class="ph__grid">
					<h1 class="ph__title split" id="contact-title"><?php echo scentral_split_words( __( 'Contact *us*.', 'scentral' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h1>
					<?php if ( $intro ) : ?>
						<p class="ph__intro" data-reveal><?php echo esc_html( $intro ); ?></p>
					<?php endif; ?>
				</div>
			</div>
		</section>

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

			<aside class="cf__side" data-reveal aria-label="<?php esc_attr_e( 'Other ways to reach us', 'scentral' ); ?>">
				<strong class="cf__side-title"><?php esc_html_e( 'Other ways in', 'scentral' ); ?></strong>
				<ul class="cf__perks">
					<li><?php echo scentral_icon( 'pin' ); // phpcs:ignore ?><?php esc_html_e( 'Visit our store in Dawaki, Abuja', 'scentral' ); ?></li>
					<li><?php echo scentral_icon( 'drop' ); // phpcs:ignore ?><?php esc_html_e( 'Ask us to help you choose a signature scent', 'scentral' ); ?></li>
					<?php if ( function_exists( 'wc_get_page_permalink' ) ) : ?>
						<li><?php echo scentral_icon( 'truck' ); // phpcs:ignore ?><a href="<?php echo esc_url( scentral_page_url( 'order-tracking' ) ); ?>"><?php esc_html_e( 'Track your order online', 'scentral' ); ?></a></li>
					<?php endif; ?>
				</ul>
				<dl class="cf__list">
					<?php if ( $email ) : ?>
						<div><dt><?php esc_html_e( 'Email', 'scentral' ); ?></dt><dd><a href="mailto:<?php echo esc_attr( antispambot( $email ) ); ?>"><?php echo esc_html( antispambot( $email ) ); ?></a></dd></div>
					<?php endif; ?>
					<?php if ( $phone ) : ?>
						<div><dt><?php esc_html_e( 'Phone', 'scentral' ); ?></dt><dd><a href="<?php echo esc_attr( scentral_tel( $phone ) ); ?>"><?php echo esc_html( $phone ); ?></a></dd></div>
					<?php endif; ?>
					<?php if ( $whatsapp ) : ?>
						<div><dt><?php esc_html_e( 'WhatsApp', 'scentral' ); ?></dt><dd><a href="<?php echo esc_url( 'https://wa.me/' . $whatsapp ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Message us', 'scentral' ); ?></a></dd></div>
					<?php endif; ?>
					<?php if ( $hours ) : ?>
						<div><dt><?php esc_html_e( 'Hours', 'scentral' ); ?></dt><dd><?php echo esc_html( $hours ); ?></dd></div>
					<?php endif; ?>
					<?php if ( $address ) : ?>
						<div><dt><?php esc_html_e( 'Store', 'scentral' ); ?></dt><dd><?php echo esc_html( $address ); ?></dd></div>
					<?php endif; ?>
				</dl>
				<?php scentral_social_list( 'social-list cf__social' ); ?>
			</aside>
		</section>

		<?php get_template_part( 'template-parts/components/location' ); ?>
		<?php do_action( 'scentral_contact_after_form' ); ?>
	<?php endwhile; ?>
</main>
<?php
get_footer();
