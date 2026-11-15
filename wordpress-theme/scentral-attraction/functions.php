<?php
/**
 * Scentral Attraction theme bootstrap.
 *
 * @package Scentral
 */

defined( 'ABSPATH' ) || exit;

define( 'SCENTRAL_VERSION', '1.3.0' );
define( 'SCENTRAL_DIR', get_template_directory() );
define( 'SCENTRAL_URI', get_template_directory_uri() );

$scentral_modules = array(
	'setup',
	'security',
	'performance',
	'enqueue',
	'template-functions',
	'customizer',
	'sections',
	'elementor',
	'pages',
	'queries',
	'wishlist',
	'newsletter',
	'contact',
	'product-meta',
	'seo',
	'ajax',
	'woocommerce',
	'product-page',
	'plugins',
);

foreach ( $scentral_modules as $scentral_module ) {
	require_once SCENTRAL_DIR . '/inc/' . $scentral_module . '.php';
}
