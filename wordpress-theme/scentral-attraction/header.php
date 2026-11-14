<?php
/**
 * Document head and site header.
 *
 * @package Scentral
 */

defined( 'ABSPATH' ) || exit;
?><!doctype html>
<html <?php language_attributes(); ?> class="no-js">
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
	<meta name="theme-color" content="#0A0A0A">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link" href="#main"><?php esc_html_e( 'Skip to content', 'scentral' ); ?></a>
<?php
if ( scentral_should_show_preloader() ) {
	get_template_part( 'template-parts/components/preloader' );
}
?>
<div class="page-shell">
	<?php
	do_action( 'scentral_before_header' );
	if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'header' ) ) {
		get_template_part( 'template-parts/header/site-header' );
	}
	?>
