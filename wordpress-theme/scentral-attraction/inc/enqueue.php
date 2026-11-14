<?php
/**
 * Asset registration and enqueueing.
 *
 * Compiled assets live in assets/dist (built from assets/src by scripts/build.mjs).
 *
 * @package Scentral
 */

defined( 'ABSPATH' ) || exit;

/**
 * Asset URL + cache-busting version for a dist file.
 *
 * @param string $file File name inside assets/dist.
 * @return array{0:string,1:string}
 */
function scentral_dist( $file ) {
	$path    = SCENTRAL_DIR . '/assets/dist/' . $file;
	$version = file_exists( $path ) ? (string) filemtime( $path ) : SCENTRAL_VERSION;
	return array( SCENTRAL_URI . '/assets/dist/' . $file, $version );
}

/**
 * Is the current request a WooCommerce surface that needs shop styles?
 *
 * @return bool
 */
function scentral_is_shop_context() {
	if ( ! function_exists( 'is_woocommerce' ) ) {
		return false;
	}
	return is_woocommerce() || is_cart() || is_checkout() || is_account_page() || is_front_page()
		|| is_page_template( 'page-templates/template-wishlist.php' ) || is_search();
}

/**
 * Enqueue front-end styles and scripts.
 */
function scentral_enqueue_assets() {
	list( $css, $css_ver ) = scentral_dist( 'main.css' );
	wp_enqueue_style( 'scentral-main', $css, array(), $css_ver );

	if ( scentral_is_shop_context() ) {
		list( $wc_css, $wc_ver ) = scentral_dist( 'woocommerce.css' );
		wp_enqueue_style( 'scentral-woocommerce', $wc_css, array( 'scentral-main' ), $wc_ver );
	}

	list( $js, $js_ver ) = scentral_dist( 'main.js' );
	wp_enqueue_script(
		'scentral-main',
		$js,
		array(),
		$js_ver,
		array(
			'strategy'  => 'defer',
			'in_footer' => true,
		)
	);

	wp_localize_script( 'scentral-main', 'ScentralConfig', scentral_script_config() );

	if ( is_singular( 'post' ) && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'scentral_enqueue_assets' );

/**
 * Data passed to the front-end script.
 *
 * @return array
 */
function scentral_script_config() {
	$has_wc = class_exists( 'WooCommerce' );

	return array(
		'ajaxUrl'         => admin_url( 'admin-ajax.php' ),
		'storeApi'        => esc_url_raw( rest_url( 'wc/store/v1/' ) ),
		'nonce'           => wp_create_nonce( 'scentral_nonce' ),
		'homeUrl'         => esc_url_raw( home_url( '/' ) ),
		'shopUrl'         => $has_wc ? esc_url_raw( wc_get_page_permalink( 'shop' ) ) : '',
		'cartUrl'         => $has_wc ? esc_url_raw( wc_get_cart_url() ) : '',
		'motion'          => (bool) scentral_option( 'motion_enabled' ),
		'parallax'        => (bool) scentral_option( 'motion_parallax' ),
		'hideHeader'      => (bool) scentral_option( 'header_hide_on_scroll' ),
		'quickView'       => (bool) scentral_option( 'shop_quick_view' ),
		'recentlyViewed'  => (bool) scentral_option( 'shop_recently_viewed' ),
		'nativeWishlist'  => ! scentral_has_wishlist_plugin(),
		'wishlistUrl'     => esc_url_raw( scentral_wishlist_url() ),
		'i18n'            => array(
			'added'        => __( 'Added', 'scentral' ),
			'adding'       => __( 'Adding…', 'scentral' ),
			'noResults'    => __( 'No fragrances match your search.', 'scentral' ),
			'viewAll'      => __( 'View all results', 'scentral' ),
			'error'        => __( 'Something went wrong. Please try again.', 'scentral' ),
			'wishAdd'      => __( 'Add to wishlist', 'scentral' ),
			'wishRemove'   => __( 'Remove from wishlist', 'scentral' ),
			'wishEmpty'    => __( 'Your wishlist is empty.', 'scentral' ),
			'outOfStock'   => __( 'Sold out', 'scentral' ),
		),
	);
}

/**
 * Replace WooCommerce's generic stylesheets with the theme's own.
 *
 * @param array $styles WooCommerce styles.
 * @return array
 */
function scentral_dequeue_wc_styles( $styles ) {
	unset( $styles['woocommerce-general'], $styles['woocommerce-layout'], $styles['woocommerce-smallscreen'] );
	return $styles;
}
add_filter( 'woocommerce_enqueue_styles', 'scentral_dequeue_wc_styles' );

/**
 * Inline <head> script: marks JS support and decides, before first paint,
 * whether the preloader should show. Runs before any stylesheet paints.
 */
function scentral_head_gate_script() {
	$show = scentral_should_show_preloader();
	$once = (bool) scentral_option( 'preloader_once' );
	?>
	<script>
	(function(d){var h=d.documentElement;h.classList.remove('no-js');h.classList.add('js');
	<?php if ( $show ) : ?>
	try{var r=window.matchMedia('(prefers-reduced-motion: reduce)').matches;var s=<?php echo $once ? 'sessionStorage.getItem("sa-loaded")' : 'null'; ?>;if(!r&&!s){h.classList.add('sa-loading');}}catch(e){}
	<?php endif; ?>
	})(document);
	</script>
	<?php
}
add_action( 'wp_head', 'scentral_head_gate_script', 0 );

/**
 * Should the preloader be rendered for this request?
 *
 * @return bool
 */
function scentral_should_show_preloader() {
	if ( ! scentral_option( 'preloader_enabled' ) || is_customize_preview() || is_admin() ) {
		return false;
	}
	if ( 'home' === scentral_option( 'preloader_scope' ) && ! is_front_page() ) {
		return false;
	}
	return true;
}

/**
 * Self-hosted @font-face rules (inline, so fonts start downloading with the HTML).
 */
function scentral_font_faces() {
	if ( 'system' === scentral_option( 'font_pairing' ) ) {
		return;
	}
	$base  = SCENTRAL_URI . '/assets/fonts/';
	$faces = array(
		array( 'Cormorant Garamond', 'cormorant-garamond-latin-300-normal.woff2', '300', 'normal', '' ),
		array( 'Cormorant Garamond', 'cormorant-garamond-latin-300-italic.woff2', '300', 'italic', '' ),
		array( 'Cormorant Garamond', 'cormorant-garamond-latin-400-normal.woff2', '400', 'normal', '' ),
		array( 'Cormorant Garamond', 'cormorant-garamond-latin-400-italic.woff2', '400', 'italic', '' ),
		array( 'Cormorant Garamond', 'cormorant-garamond-latin-500-normal.woff2', '500', 'normal', '' ),
		array( 'Manrope', 'manrope-latin-wght-normal.woff2', '200 800', 'normal', '' ),
		array( 'Manrope', 'manrope-latin-ext-wght-normal.woff2', '200 800', 'normal', 'U+20A6' ),
	);

	$css = '';
	foreach ( $faces as $face ) {
		$css .= sprintf(
			'@font-face{font-family:"%1$s";src:url(%2$s) format("woff2");font-weight:%3$s;font-style:%4$s;font-display:swap;%5$s}',
			esc_attr( $face[0] ),
			esc_url( $base . $face[1] ),
			esc_attr( $face[2] ),
			esc_attr( $face[3] ),
			$face[4] ? 'unicode-range:' . esc_attr( $face[4] ) . ';' : ''
		);
	}
	// Metric-adjusted fallbacks reduce layout shift while fonts swap in.
	$css .= '@font-face{font-family:"Cormorant Fallback";src:local("Georgia");size-adjust:88%;ascent-override:96%;descent-override:30%}';
	$css .= '@font-face{font-family:"Manrope Fallback";src:local("Arial");size-adjust:103%;ascent-override:100%;descent-override:28%}';

	printf( '<style id="scentral-fonts">%s</style>' . "\n", $css ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- values escaped above.
}
add_action( 'wp_head', 'scentral_font_faces', 3 );

/**
 * Enable cross-document view transitions (progressive enhancement, no JS).
 */
function scentral_view_transition_style() {
	if ( ! scentral_option( 'motion_page_transitions' ) ) {
		return;
	}
	echo '<style id="scentral-vt">@media (prefers-reduced-motion:no-preference){@view-transition{navigation:auto}}</style>' . "\n";
}
add_action( 'wp_head', 'scentral_view_transition_style', 4 );
