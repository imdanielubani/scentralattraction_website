<?php
/**
 * Security hardening helpers.
 *
 * @package Scentral
 */

defined( 'ABSPATH' ) || exit;

/**
 * Simple per-IP rate limiter for public AJAX endpoints.
 *
 * @param string $action Bucket name.
 * @param int    $limit  Allowed hits per window.
 * @param int    $window Window in seconds.
 * @return bool True when the request is allowed.
 */
function scentral_rate_limit( $action, $limit = 30, $window = 60 ) {
	$ip  = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : 'unknown';
	$key = 'scentral_rl_' . md5( $action . '|' . $ip );
	$hit = (int) get_transient( $key );

	if ( $hit >= $limit ) {
		return false;
	}
	set_transient( $key, $hit + 1, $window );
	return true;
}

/**
 * Sanitize a checkbox value.
 *
 * @param mixed $value Raw value.
 * @return bool
 */
function scentral_sanitize_checkbox( $value ) {
	return (bool) $value;
}

/**
 * Sanitize a select value against allowed choices.
 *
 * @param string               $value   Raw value.
 * @param WP_Customize_Setting $setting Setting object.
 * @return string
 */
function scentral_sanitize_select( $value, $setting ) {
	$control = $setting->manager->get_control( $setting->id );
	$choices = $control ? $control->choices : array();
	return array_key_exists( $value, $choices ) ? $value : $setting->default;
}

/**
 * Sanitize multiline plain text (one item per line).
 *
 * @param string $value Raw value.
 * @return string
 */
function scentral_sanitize_lines( $value ) {
	$lines = array_map( 'sanitize_text_field', preg_split( '/\r\n|\r|\n/', (string) $value ) );
	return implode( "\n", array_filter( $lines, 'strlen' ) );
}

/**
 * Sanitize a comma separated list of IDs.
 *
 * @param string $value Raw value.
 * @return string
 */
function scentral_sanitize_id_list( $value ) {
	$ids = array_filter( array_map( 'absint', explode( ',', (string) $value ) ) );
	return implode( ',', array_unique( $ids ) );
}

/**
 * Sanitize a shortcode string: allow only a single [shortcode ...] tag.
 *
 * @param string $value Raw value.
 * @return string
 */
function scentral_sanitize_shortcode( $value ) {
	$value = trim( wp_strip_all_tags( (string) $value ) );
	return preg_match( '/^\[[a-zA-Z0-9_\-]+[^\]]*\]$/', $value ) ? $value : '';
}

/**
 * Send hardening headers on the front end. HSTS is left to the server/CDN.
 */
function scentral_security_headers() {
	if ( is_admin() || headers_sent() ) {
		return;
	}
	header( 'X-Content-Type-Options: nosniff' );
	header( 'Referrer-Policy: strict-origin-when-cross-origin' );
	header( 'X-Frame-Options: SAMEORIGIN' );
	header( 'Permissions-Policy: camera=(), microphone=(), geolocation=()' );
}
add_action( 'send_headers', 'scentral_security_headers' );

/* Hide the WordPress version from public output. */
remove_action( 'wp_head', 'wp_generator' );
