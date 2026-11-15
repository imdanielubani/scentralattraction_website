<?php
/**
 * Newsletter: renders a configured form shortcode (e.g. Mailchimp for WP) or a native fallback.
 *
 * The native fallback stores addresses in a non-autoloaded option (`scentral_subscribers`) that only
 * shop managers can view or export (Tools → Newsletter subscribers). Nonce-protected, rate-limited,
 * honeypot-guarded.
 *
 * @package Scentral
 */

defined( 'ABSPATH' ) || exit;

/**
 * Render the newsletter form.
 *
 * @param string $id_suffix Unique suffix for field IDs.
 */
function scentral_newsletter_form( $id_suffix = 'main' ) {
	$shortcode = scentral_option( 'newsletter_shortcode' );
	if ( $shortcode ) {
		$tag = preg_match( '/^\[([a-zA-Z0-9_\-]+)/', $shortcode, $m ) ? $m[1] : '';
		if ( $tag && shortcode_exists( $tag ) ) {
			$html = do_shortcode( $shortcode );
			// Plugins may print nothing to visitors (e.g. Mailchimp for WP without an API key, or a
			// wrong form ID). Only use the plugin form when it actually renders one.
			if ( false !== stripos( $html, '<form' ) || ( current_user_can( 'manage_options' ) && '' !== trim( $html ) ) ) {
				echo '<div class="newsletter-form newsletter-form--plugin">' . $html . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- plugin output.
				return;
			}
		}
	}
	$field_id = 'sa-newsletter-' . sanitize_html_class( $id_suffix );
	?>
	<form class="newsletter-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" data-newsletter novalidate>
		<input type="hidden" name="action" value="scentral_subscribe">
		<?php wp_nonce_field( 'scentral_subscribe', 'scentral_subscribe_nonce' ); ?>
		<div class="newsletter-form__hp" aria-hidden="true">
			<label for="<?php echo esc_attr( $field_id ); ?>-website"><?php esc_html_e( 'Website', 'scentral' ); ?></label>
			<input type="text" id="<?php echo esc_attr( $field_id ); ?>-website" name="website" tabindex="-1" autocomplete="off">
		</div>
		<label class="screen-reader-text" for="<?php echo esc_attr( $field_id ); ?>"><?php esc_html_e( 'Email address', 'scentral' ); ?></label>
		<div class="newsletter-form__row">
			<input class="newsletter-form__input" type="email" id="<?php echo esc_attr( $field_id ); ?>" name="email" required autocomplete="email" placeholder="<?php esc_attr_e( 'Your email address', 'scentral' ); ?>">
			<button class="newsletter-form__submit" type="submit">
				<span class="newsletter-form__label"><?php esc_html_e( 'Subscribe', 'scentral' ); ?></span>
				<?php echo scentral_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</button>
		</div>
		<p class="newsletter-form__status" role="status" aria-live="polite"><?php echo esc_html( scentral_newsletter_flash() ); ?></p>
	</form>
	<?php
}

/**
 * Status message after a non-JS submission redirect.
 *
 * @return string
 */
function scentral_newsletter_flash() {
	$state = isset( $_GET['subscribed'] ) ? sanitize_key( wp_unslash( $_GET['subscribed'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- display only.
	$map   = scentral_newsletter_messages();
	return isset( $map[ $state ] ) ? $map[ $state ] : '';
}

/**
 * Newsletter result messages.
 *
 * @return array<string,string>
 */
function scentral_newsletter_messages() {
	return array(
		'ok'      => __( 'Thank you — you are on the list.', 'scentral' ),
		'exists'  => __( 'You are already subscribed. Thank you!', 'scentral' ),
		'invalid' => __( 'Please enter a valid email address.', 'scentral' ),
		'error'   => __( 'Something went wrong. Please try again.', 'scentral' ),
		'limit'   => __( 'Too many attempts. Please try again in a minute.', 'scentral' ),
	);
}

/**
 * Validate and store a subscription.
 *
 * @return string Result state key.
 */
function scentral_process_subscription() {
	$nonce = isset( $_POST['scentral_subscribe_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['scentral_subscribe_nonce'] ) ) : '';
	if ( ! wp_verify_nonce( $nonce, 'scentral_subscribe' ) ) {
		return 'error';
	}
	if ( ! empty( $_POST['website'] ) ) {
		return 'ok'; // Honeypot filled: silently accept, store nothing.
	}
	if ( ! scentral_rate_limit( 'newsletter', 5, 60 ) ) {
		return 'limit';
	}
	$email = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	if ( ! is_email( $email ) ) {
		return 'invalid';
	}

	$list = get_option( 'scentral_subscribers', array() );
	$list = is_array( $list ) ? $list : array();
	foreach ( $list as $row ) {
		if ( isset( $row['email'] ) && strtolower( $row['email'] ) === strtolower( $email ) ) {
			return 'exists';
		}
	}
	$list[] = array(
		'email' => $email,
		'date'  => current_time( 'mysql' ),
	);
	update_option( 'scentral_subscribers', $list, false );

	/**
	 * Fires after a native newsletter subscription (integrate with a mailing service here).
	 *
	 * @param string $email Subscriber email.
	 */
	do_action( 'scentral_newsletter_subscribed', $email );
	return 'ok';
}

/**
 * Handle non-JS form posts.
 */
function scentral_handle_subscribe_post() {
	$state    = scentral_process_subscription();
	$referer  = wp_get_referer();
	$redirect = $referer ? $referer : home_url( '/' );
	wp_safe_redirect( add_query_arg( 'subscribed', $state, $redirect ) . '#newsletter' );
	exit;
}
add_action( 'admin_post_nopriv_scentral_subscribe', 'scentral_handle_subscribe_post' );
add_action( 'admin_post_scentral_subscribe', 'scentral_handle_subscribe_post' );

/**
 * Handle AJAX submissions.
 */
function scentral_handle_subscribe_ajax() {
	$state    = scentral_process_subscription();
	$messages = scentral_newsletter_messages();
	$payload  = array(
		'state'   => $state,
		'message' => $messages[ $state ],
	);
	if ( in_array( $state, array( 'ok', 'exists' ), true ) ) {
		wp_send_json_success( $payload );
	}
	wp_send_json_error( $payload, 'limit' === $state ? 429 : 400 );
}
add_action( 'wp_ajax_nopriv_scentral_subscribe', 'scentral_handle_subscribe_ajax' );
add_action( 'wp_ajax_scentral_subscribe', 'scentral_handle_subscribe_ajax' );

/**
 * Fresh nonce for the anonymous newsletter / contact forms.
 *
 * Full-page caches can serve HTML whose embedded nonce has expired; the front end requests a
 * fresh one (admin-ajax is never page-cached) just before submitting. Only these two actions
 * are allowed, and the endpoint is rate-limited.
 */
function scentral_ajax_fresh_nonce() {
	$allowed = array( 'scentral_subscribe', 'scentral_contact' );
	$action  = isset( $_GET['for'] ) ? sanitize_key( wp_unslash( $_GET['for'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- issues a nonce; nothing is changed.
	if ( ! in_array( $action, $allowed, true ) || ! scentral_rate_limit( 'fresh_nonce', 20, 60 ) ) {
		wp_send_json_error( null, 400 );
	}
	nocache_headers();
	wp_send_json_success( array( 'nonce' => wp_create_nonce( $action ) ) );
}
add_action( 'wp_ajax_nopriv_scentral_fresh_nonce', 'scentral_ajax_fresh_nonce' );
add_action( 'wp_ajax_scentral_fresh_nonce', 'scentral_ajax_fresh_nonce' );

/**
 * Admin page listing native subscribers (shop managers only).
 */
function scentral_subscribers_admin_menu() {
	add_management_page(
		__( 'Newsletter subscribers', 'scentral' ),
		__( 'Newsletter subscribers', 'scentral' ),
		'manage_woocommerce',
		'scentral-subscribers',
		'scentral_render_subscribers_page'
	);
}
add_action( 'admin_menu', 'scentral_subscribers_admin_menu' );

/**
 * Render the subscribers admin page.
 */
function scentral_render_subscribers_page() {
	if ( ! current_user_can( 'manage_woocommerce' ) ) {
		return;
	}
	$list = get_option( 'scentral_subscribers', array() );
	echo '<div class="wrap"><h1>' . esc_html__( 'Newsletter subscribers', 'scentral' ) . '</h1>';
	echo '<p>' . esc_html__( 'Collected by the theme\'s built-in form. Copy into your mailing service. If you use a newsletter plugin, set its shortcode under Customize → Scentral Theme → Newsletter.', 'scentral' ) . '</p>';
	if ( ! $list ) {
		echo '<p><em>' . esc_html__( 'No subscribers yet.', 'scentral' ) . '</em></p></div>';
		return;
	}
	echo '<table class="widefat striped"><thead><tr><th>' . esc_html__( 'Email', 'scentral' ) . '</th><th>' . esc_html__( 'Date', 'scentral' ) . '</th></tr></thead><tbody>';
	foreach ( $list as $row ) {
		printf( '<tr><td>%1$s</td><td>%2$s</td></tr>', esc_html( $row['email'] ), esc_html( $row['date'] ) );
	}
	echo '</tbody></table></div>';
}
