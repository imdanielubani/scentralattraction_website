<?php
/**
 * Template Name: Contact
 * Template Post Type: page
 *
 * Renders the page content (where the existing Contact Form 7 shortcode lives) beside the
 * verified contact details. If the page has no form, the theme's native form is shown.
 *
 * @package Scentral
 */

defined( 'ABSPATH' ) || exit;

get_header();

if ( scentral_render_builder_page() ) {
	get_footer();
	return;
}

$address  = scentral_option( 'contact_address' );
$phone    = scentral_option( 'contact_phone' );
$email    = scentral_option( 'contact_email' );
$hours    = scentral_option( 'contact_hours' );
$map      = scentral_option( 'contact_map_url' );
$whatsapp = preg_replace( '/[^0-9]/', '', (string) scentral_option( 'contact_whatsapp' ) );
?>
<main id="main" class="site-main site-main--contact">
	<?php
	while ( have_posts() ) :
		the_post();
		$content  = get_the_content();
		$has_form = has_shortcode( $content, 'contact-form-7' ) || has_block( 'contact-form-7/contact-form-selector' ) || has_shortcode( $content, 'wpforms' ) || has_shortcode( $content, 'gravityform' );
		?>
		<header class="page-hero container">
			<?php scentral_breadcrumbs(); ?>
			<p class="eyebrow" data-reveal><?php the_title(); ?></p>
			<h1 class="page-hero__title split"><?php echo scentral_split_words( __( 'We would love to *hear* from you', 'scentral' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h1>
		</header>

		<div class="contact container">
			<aside class="contact__details" data-reveal aria-label="<?php esc_attr_e( 'Contact details', 'scentral' ); ?>">
				<?php if ( $address ) : ?>
					<div class="contact__item">
						<span class="label"><?php echo scentral_icon( 'pin' ); // phpcs:ignore ?><?php esc_html_e( 'Visit', 'scentral' ); ?></span>
						<p class="contact__value"><?php echo esc_html( $address ); ?></p>
						<?php if ( $map ) : ?>
							<a class="link-arrow" href="<?php echo esc_url( $map ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Get directions', 'scentral' ); ?><?php echo scentral_icon( 'arrow' ); // phpcs:ignore ?></a>
						<?php endif; ?>
					</div>
				<?php endif; ?>
				<?php if ( $phone ) : ?>
					<div class="contact__item">
						<span class="label"><?php echo scentral_icon( 'phone' ); // phpcs:ignore ?><?php esc_html_e( 'Call', 'scentral' ); ?></span>
						<a class="contact__value" href="<?php echo esc_attr( scentral_tel( $phone ) ); ?>"><?php echo esc_html( $phone ); ?></a>
					</div>
				<?php endif; ?>
				<?php if ( $whatsapp ) : ?>
					<div class="contact__item">
						<span class="label"><?php echo scentral_icon( 'whatsapp' ); // phpcs:ignore ?><?php esc_html_e( 'WhatsApp', 'scentral' ); ?></span>
						<a class="contact__value" href="<?php echo esc_url( 'https://wa.me/' . $whatsapp ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Message us', 'scentral' ); ?></a>
					</div>
				<?php endif; ?>
				<?php if ( $email ) : ?>
					<div class="contact__item">
						<span class="label"><?php echo scentral_icon( 'mail' ); // phpcs:ignore ?><?php esc_html_e( 'Write', 'scentral' ); ?></span>
						<a class="contact__value" href="mailto:<?php echo esc_attr( antispambot( $email ) ); ?>"><?php echo esc_html( antispambot( $email ) ); ?></a>
					</div>
				<?php endif; ?>
				<?php if ( $hours ) : ?>
					<div class="contact__item">
						<span class="label"><?php echo scentral_icon( 'clock' ); // phpcs:ignore ?><?php esc_html_e( 'Hours', 'scentral' ); ?></span>
						<p class="contact__value"><?php echo esc_html( $hours ); ?></p>
					</div>
				<?php endif; ?>
				<?php scentral_social_list( 'social-list social-list--light' ); ?>
			</aside>

			<section class="contact__form" data-reveal aria-label="<?php esc_attr_e( 'Send a message', 'scentral' ); ?>">
				<?php if ( trim( $content ) ) : ?>
					<div class="prose contact__content"><?php the_content(); ?></div>
				<?php endif; ?>
				<?php
				if ( ! $has_form ) {
					scentral_contact_form();
				}
				?>
			</section>
		</div>
	<?php endwhile; ?>
</main>
<?php
get_footer();
