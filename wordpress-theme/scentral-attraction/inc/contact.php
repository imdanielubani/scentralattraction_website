<?php
/**
 * Native contact form — only used when the Contact page has no form plugin shortcode.
 * Emails the configured address via wp_mail(). Nothing is stored. Nonce, honeypot and rate limit.
 *
 * @package Scentral
 */

defined( 'ABSPATH' ) || exit;

/**
 * Render the native contact form.
 */
function scentral_contact_form() {
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- display-only status flag.
	$state    = isset( $_GET['sent'] ) ? sanitize_key( wp_unslash( $_GET['sent'] ) ) : '';
	$messages = scentral_contact_messages();
	?>
	<form class="contact-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" novalidate>
		<input type="hidden" name="action" value="scentral_contact">
		<?php wp_nonce_field( 'scentral_contact', 'scentral_contact_nonce' ); ?>
		<div class="newsletter-form__hp" aria-hidden="true"><label for="sa-c-website"><?php esc_html_e( 'Website', 'scentral' ); ?></label><input type="text" id="sa-c-website" name="website" tabindex="-1" autocomplete="off"></div>
		<div class="contact-form__grid">
			<p class="field"><label for="sa-c-name"><?php esc_html_e( 'Your name', 'scentral' ); ?> <span aria-hidden="true">*</span></label><input type="text" id="sa-c-name" name="name" required maxlength="120" autocomplete="name"></p>
			<p class="field"><label for="sa-c-email"><?php esc_html_e( 'Your email', 'scentral' ); ?> <span aria-hidden="true">*</span></label><input type="email" id="sa-c-email" name="email" required maxlength="190" autocomplete="email"></p>
			<p class="field field--wide"><label for="sa-c-subject"><?php esc_html_e( 'Subject', 'scentral' ); ?> <span aria-hidden="true">*</span></label><input type="text" id="sa-c-subject" name="subject" required maxlength="160"></p>
			<p class="field field--wide"><label for="sa-c-message"><?php esc_html_e( 'Your message', 'scentral' ); ?></label><textarea id="sa-c-message" name="message" rows="6" maxlength="4000"></textarea></p>
		</div>
		<button type="submit" class="button"><?php esc_html_e( 'Send message', 'scentral' ); ?></button>
		<p class="contact-form__status" role="status" aria-live="polite"><?php echo isset( $messages[ $state ] ) ? esc_html( $messages[ $state ] ) : ''; ?></p>
	</form>
	<?php
}

/**
 * Contact result messages.
 *
 * @return array<string,string>
 */
function scentral_contact_messages() {
	return array(
		'ok'      => __( 'Thank you — your message has been sent. We will get back to you as soon as we can.', 'scentral' ),
		'invalid' => __( 'Please fill in your name, a valid email and a subject.', 'scentral' ),
		'limit'   => __( 'Too many messages. Please try again in a few minutes.', 'scentral' ),
		'error'   => __( 'Your message could not be sent. Please call or email us directly.', 'scentral' ),
	);
}

/**
 * Handle the native contact form post.
 */
function scentral_handle_contact() {
	$state = 'error';
	$nonce = isset( $_POST['scentral_contact_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['scentral_contact_nonce'] ) ) : '';

	if ( wp_verify_nonce( $nonce, 'scentral_contact' ) ) {
		if ( ! empty( $_POST['website'] ) ) {
			$state = 'ok'; // Honeypot: pretend success, send nothing.
		} elseif ( ! scentral_rate_limit( 'contact', 3, 300 ) ) {
			$state = 'limit';
		} else {
			$name    = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
			$email   = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
			$subject = isset( $_POST['subject'] ) ? sanitize_text_field( wp_unslash( $_POST['subject'] ) ) : '';
			$message = isset( $_POST['message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) ) : '';

			if ( '' === $name || '' === $subject || ! is_email( $email ) ) {
				$state = 'invalid';
			} else {
				$to   = scentral_option( 'contact_email' );
				$to   = is_email( $to ) ? $to : get_option( 'admin_email' );
				$body = sprintf( "Name: %s\nEmail: %s\n\n%s", $name, $email, $message );
				$sent = wp_mail(
					$to,
					'[' . wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ) . '] ' . $subject,
					$body,
					array( 'Reply-To: ' . $name . ' <' . $email . '>' )
				);
				$state = $sent ? 'ok' : 'error';
			}
		}
	}

	$referer = wp_get_referer();
	wp_safe_redirect( add_query_arg( 'sent', $state, $referer ? $referer : home_url( '/' ) ) . '#main' );
	exit;
}
add_action( 'admin_post_nopriv_scentral_contact', 'scentral_handle_contact' );
add_action( 'admin_post_scentral_contact', 'scentral_handle_contact' );
