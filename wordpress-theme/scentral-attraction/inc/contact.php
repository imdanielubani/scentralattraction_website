<?php
/**
 * Native contact form (reference style: topic pills + floating-label fields).
 * Used when the Contact page has no form-plugin shortcode. Emails the configured address via
 * wp_mail(); nothing is stored. Nonce (refreshed for cached pages), honeypot and rate limit.
 *
 * @package Scentral
 */

defined( 'ABSPATH' ) || exit;

/**
 * Topics offered as pills above the form.
 *
 * @return string[]
 */
function scentral_contact_topics() {
	return apply_filters(
		'scentral_contact_topics',
		array(
			__( 'A fragrance question', 'scentral' ),
			__( 'An order', 'scentral' ),
			__( 'Partnership', 'scentral' ),
			__( 'Something else', 'scentral' ),
		)
	);
}

/**
 * One floating-label field.
 *
 * @param string $name     Field name.
 * @param string $label    Label.
 * @param string $type     Input type or 'textarea'.
 * @param bool   $required Required.
 * @param string $auto     Autocomplete token.
 */
function scentral_contact_field( $name, $label, $type = 'text', $required = false, $auto = 'off' ) {
	$id = 'sa-c-' . $name;
	echo '<label class="ff' . ( 'textarea' === $type ? ' ff--msg' : '' ) . '" for="' . esc_attr( $id ) . '"><span class="ff__box">';
	if ( 'textarea' === $type ) {
		printf( '<textarea id="%1$s" name="%2$s" rows="4" maxlength="4000" placeholder=" "%3$s></textarea>', esc_attr( $id ), esc_attr( $name ), $required ? ' required' : '' );
	} else {
		printf( '<input id="%1$s" name="%2$s" type="%3$s" autocomplete="%4$s" maxlength="190"%5$s placeholder=" ">', esc_attr( $id ), esc_attr( $name ), esc_attr( $type ), esc_attr( $auto ), $required ? ' required' : '' );
	}
	printf( '<span class="ff__label">%s</span><i aria-hidden="true"></i></span></label>', esc_html( $label ) );
}

/**
 * Render the native contact form.
 */
function scentral_contact_form() {
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- display-only status flag.
	$state    = isset( $_GET['sent'] ) ? sanitize_key( wp_unslash( $_GET['sent'] ) ) : '';
	$messages = scentral_contact_messages();
	$topics   = scentral_contact_topics();
	?>
	<form class="contact-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" data-contact-form novalidate>
		<input type="hidden" name="action" value="scentral_contact">
		<?php wp_nonce_field( 'scentral_contact', 'scentral_contact_nonce' ); ?>
		<div class="newsletter-form__hp" aria-hidden="true"><label for="sa-c-website"><?php esc_html_e( 'Website', 'scentral' ); ?></label><input type="text" id="sa-c-website" name="website" tabindex="-1" autocomplete="off"></div>
		<fieldset class="topics">
			<legend><?php esc_html_e( 'What is it about?', 'scentral' ); ?></legend>
			<?php foreach ( $topics as $i => $topic ) : ?>
				<label class="topics__pill">
					<input type="radio" name="topic" value="<?php echo esc_attr( $topic ); ?>"<?php checked( 0, $i ); ?>>
					<span><?php echo esc_html( $topic ); ?></span>
				</label>
			<?php endforeach; ?>
		</fieldset>
		<div class="contact-form__grid">
			<?php
			scentral_contact_field( 'name', __( 'Your name', 'scentral' ), 'text', true, 'name' );
			scentral_contact_field( 'email', __( 'Email', 'scentral' ), 'email', true, 'email' );
			scentral_contact_field( 'phone', __( 'Phone (optional)', 'scentral' ), 'tel', false, 'tel' );
			scentral_contact_field( 'subject', __( 'Order number or subject (optional)', 'scentral' ) );
			?>
		</div>
		<?php scentral_contact_field( 'message', __( 'Message', 'scentral' ), 'textarea', true ); ?>
		<div class="contact-form__foot">
			<button type="submit" class="pill-btn"><span><?php esc_html_e( 'Send message', 'scentral' ); ?></span><i class="pill-btn__arrow" aria-hidden="true"><?php echo scentral_icon( 'arrow' ); // phpcs:ignore ?></i></button>
			<p class="contact-form__note"><?php esc_html_e( 'We will get back to you as soon as we can', 'scentral' ); ?></p>
		</div>
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
		'invalid' => __( 'Please fill in your name, a valid email and a message.', 'scentral' ),
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
			$phone   = isset( $_POST['phone'] ) ? sanitize_text_field( wp_unslash( $_POST['phone'] ) ) : '';
			$topic   = isset( $_POST['topic'] ) ? sanitize_text_field( wp_unslash( $_POST['topic'] ) ) : '';
			$subject = isset( $_POST['subject'] ) ? sanitize_text_field( wp_unslash( $_POST['subject'] ) ) : '';
			$message = isset( $_POST['message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) ) : '';
			$topic   = in_array( $topic, scentral_contact_topics(), true ) ? $topic : '';

			if ( '' === $name || '' === $message || ! is_email( $email ) ) {
				$state = 'invalid';
			} else {
				$to    = scentral_option( 'contact_email' );
				$to    = is_email( $to ) ? $to : get_option( 'admin_email' );
				$title = trim( $topic . ( $subject ? ' — ' . $subject : '' ) );
				$body  = sprintf( "Name: %s\nEmail: %s\nPhone: %s\nTopic: %s\nSubject: %s\n\n%s", $name, $email, $phone, $topic, $subject, $message );
				$sent  = wp_mail(
					$to,
					'[' . wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ) . '] ' . ( $title ? $title : __( 'New message', 'scentral' ) ),
					$body,
					array( 'Reply-To: ' . $name . ' <' . $email . '>' )
				);
				$state = $sent ? 'ok' : 'error';
			}
		}
	}

	$referer = wp_get_referer();
	wp_safe_redirect( add_query_arg( 'sent', $state, $referer ? $referer : home_url( '/' ) ) . '#contact-form' );
	exit;
}
add_action( 'admin_post_nopriv_scentral_contact', 'scentral_handle_contact' );
add_action( 'admin_post_scentral_contact', 'scentral_handle_contact' );
