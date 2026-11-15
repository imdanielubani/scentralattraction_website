<?php
/**
 * WooCommerce integration: supports, layout hooks, fragments, product card helpers.
 *
 * Overrides are kept minimal (archive-product.php, content-product.php). Everything else is done
 * through WooCommerce hooks so core template updates keep working.
 *
 * @package Scentral
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WooCommerce' ) ) {
	return;
}

/**
 * Declare WooCommerce support.
 */
function scentral_woocommerce_setup() {
	add_theme_support(
		'woocommerce',
		array(
			'thumbnail_image_width' => 640,
			'single_image_width'    => 1100,
			'product_grid'          => array(
				'default_columns' => 4,
				'min_columns'     => 2,
				'max_columns'     => 4,
			),
		)
	);
	// Product galleries use the theme's own gallery (inc/product-page.php), so WooCommerce's
	// zoom/lightbox/slider scripts are not loaded.
}
add_action( 'after_setup_theme', 'scentral_woocommerce_setup' );

/**
 * HPOS + cart/checkout blocks compatibility is declared by plugins, not themes; nothing to do here.
 * Enqueue the cart fragments script so the header bag stays in sync on every page.
 */
function scentral_wc_scripts() {
	// The bag drawer is on every page: fragments + add/remove handlers load sitewide.
	wp_enqueue_script( 'wc-cart-fragments' );
	wp_enqueue_script( 'wc-add-to-cart' );
	if ( scentral_option( 'shop_quick_view' ) && ( is_front_page() || is_shop() || is_product_taxonomy() || is_search() || is_product() ) ) {
		// Quick view renders the single-product add-to-cart form (variations need this script).
		wp_enqueue_script( 'wc-add-to-cart-variation' );
	}
}
add_action( 'wp_enqueue_scripts', 'scentral_wc_scripts', 20 );

/* -------------------------------------------------------------------------
 * Wrappers and layout.
 * ---------------------------------------------------------------------- */

remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );
remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );
remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );

/**
 * Open the main wrapper.
 */
function scentral_wc_wrapper_start() {
	$class = is_product() ? 'site-main site-main--product' : 'site-main site-main--shop';
	echo '<main id="main" class="' . esc_attr( $class ) . '">';
}
add_action( 'woocommerce_before_main_content', 'scentral_wc_wrapper_start', 10 );

/**
 * Close the main wrapper.
 */
function scentral_wc_wrapper_end() {
	echo '</main>';
}
add_action( 'woocommerce_after_main_content', 'scentral_wc_wrapper_end', 10 );

/**
 * Products per row / per page from the Customizer.
 *
 * @return int
 */
function scentral_loop_columns() {
	return (int) scentral_option( 'shop_columns' );
}
add_filter( 'loop_shop_columns', 'scentral_loop_columns' );

/**
 * Products per page.
 *
 * @return int
 */
function scentral_loop_per_page() {
	return max( 4, (int) scentral_option( 'shop_per_page' ) );
}
add_filter( 'loop_shop_per_page', 'scentral_loop_per_page', 20 );

/* -------------------------------------------------------------------------
 * Product card helpers (used by woocommerce/content-product.php).
 * ---------------------------------------------------------------------- */

/**
 * Bottle size from the product's Size attribute (global pa_size or local "Size").
 *
 * @param WC_Product $product Product.
 * @return string
 */
function scentral_product_size( $product ) {
	foreach ( array( 'pa_size', 'size', 'pa_volume', 'volume' ) as $attr ) {
		$value = $product->get_attribute( $attr );
		if ( $value ) {
			return $value;
		}
	}
	return '';
}

/**
 * Spec label, e.g. "100ML EDP · Anfar London".
 *
 * @param WC_Product $product Product.
 * @return string
 */
function scentral_product_spec( $product ) {
	$parts = array();
	$size  = scentral_product_size( $product );
	if ( $size ) {
		$parts[] = $size;
	}
	$terms = get_the_terms( $product->get_id(), 'product_cat' );
	if ( $terms && ! is_wp_error( $terms ) ) {
		$default = (int) get_option( 'default_product_cat' );
		foreach ( $terms as $term ) {
			if ( (int) $term->term_id !== $default ) {
				$parts[] = $term->name;
				break;
			}
		}
	}
	return implode( ' · ', $parts );
}

/**
 * Card badge: sold out, or sale percentage.
 *
 * @param WC_Product $product Product.
 * @return string Safe HTML.
 */
function scentral_product_badge( $product ) {
	if ( ! $product->is_in_stock() ) {
		return '<span class="badge badge--muted">' . esc_html__( 'Sold out', 'scentral' ) . '</span>';
	}
	if ( $product->is_on_sale() ) {
		$percent = scentral_sale_percent( $product );
		$label   = $percent ? sprintf( '−%d%%', $percent ) : __( 'Sale', 'scentral' );
		return '<span class="badge badge--sale">' . esc_html( $label ) . '</span>';
	}
	return '';
}

/**
 * Largest sale percentage for simple and variable products.
 *
 * @param WC_Product $product Product.
 * @return int
 */
function scentral_sale_percent( $product ) {
	if ( $product->is_type( 'variable' ) ) {
		$max = 0;
		foreach ( $product->get_visible_children() as $child_id ) {
			$child = wc_get_product( $child_id );
			if ( $child && $child->is_on_sale() ) {
				$max = max( $max, scentral_sale_percent( $child ) );
			}
		}
		return $max;
	}
	$regular = (float) $product->get_regular_price();
	$sale    = (float) $product->get_sale_price();
	return ( $regular > 0 && $sale > 0 && $sale < $regular ) ? (int) round( ( 1 - $sale / $regular ) * 100 ) : 0;
}

/**
 * Replace the default "Sale!" flash with the theme badge.
 *
 * @param string     $html    Default HTML.
 * @param WP_Post    $post    Post.
 * @param WC_Product $product Product.
 * @return string
 */
function scentral_sale_flash( $html, $post, $product ) {
	return scentral_product_badge( $product );
}
add_filter( 'woocommerce_sale_flash', 'scentral_sale_flash', 10, 3 );

/**
 * Card add-to-cart button classes (keeps WooCommerce AJAX classes intact).
 *
 * @param array      $args    Button args.
 * @param WC_Product $product Product.
 * @return array
 */
function scentral_loop_add_to_cart_args( $args, $product ) {
	$args['class'] = trim( $args['class'] . ' card-cta' );
	if ( $product->is_purchasable() && $product->is_in_stock() && $product->supports( 'ajax_add_to_cart' ) ) {
		/* translators: %s: product name. */
		$args['attributes']['aria-label'] = sprintf( __( 'Add %s to your bag', 'scentral' ), $product->get_name() );
	} else {
		/* translators: 1: button text, 2: product name. */
		$args['attributes']['aria-label'] = sprintf( __( '%1$s: %2$s', 'scentral' ), $product->add_to_cart_text(), $product->get_name() );
	}
	return $args;
}
add_filter( 'woocommerce_loop_add_to_cart_args', 'scentral_loop_add_to_cart_args', 10, 2 );

/**
 * Card button as an expanding pill: the label slides out beside a round "+" (or arrow when the
 * button leads to the product page). Markup only — WooCommerce's AJAX add-to-cart is untouched.
 *
 * @param string     $html    Button HTML.
 * @param WC_Product $product Product.
 * @return string
 */
function scentral_loop_add_to_cart_link( $html, $product ) {
	if ( false === strpos( $html, 'card-cta' ) ) {
		return $html;
	}
	$adds = $product->is_purchasable() && $product->is_in_stock() && $product->supports( 'ajax_add_to_cart' );
	$icon = $adds ? '<i class="card-cta__ic" aria-hidden="true">+</i>' : '<i class="card-cta__ic card-cta__ic--arrow" aria-hidden="true">' . scentral_icon( 'arrow' ) . '</i>';
	return preg_replace(
		'#>([^<]*)</a>\s*$#',
		'><span class="card-cta__label">' . esc_html( $adds ? __( 'Add to bag', 'scentral' ) : $product->add_to_cart_text() ) . '</span>' . $icon . '</a>',
		trim( $html ),
		1
	);
}
add_filter( 'woocommerce_loop_add_to_cart_link', 'scentral_loop_add_to_cart_link', 20, 2 );

/* -------------------------------------------------------------------------
 * Cart fragments: header bag count + drawer.
 * ---------------------------------------------------------------------- */

/**
 * Cart count markup.
 *
 * @return string
 */
function scentral_cart_count_html() {
	$count = WC()->cart ? WC()->cart->get_cart_contents_count() : 0;
	return sprintf(
		'<span class="cart-count%1$s" data-cart-count="%2$d">%2$d</span>',
		$count ? '' : ' is-empty',
		(int) $count
	);
}

/**
 * Free-delivery progress markup (only when a threshold is configured).
 *
 * @return string
 */
function scentral_free_shipping_html() {
	$threshold = (float) scentral_option( 'shop_free_shipping_threshold' );
	if ( $threshold <= 0 || ! WC()->cart || WC()->cart->is_empty() ) {
		return '<div class="ship-progress" hidden></div>';
	}
	$subtotal  = (float) WC()->cart->get_displayed_subtotal();
	$remaining = max( 0, $threshold - $subtotal );
	$ratio     = min( 1, $subtotal / $threshold );
	$message   = $remaining > 0
		/* translators: %s: amount remaining. */
		? sprintf( __( 'You are %s away from free delivery', 'scentral' ), wc_price( $remaining ) )
		: __( 'Your order qualifies for free delivery', 'scentral' );

	return sprintf(
		'<div class="ship-progress"><p class="ship-progress__text">%1$s</p><span class="ship-progress__track"><span class="ship-progress__fill" style="--p:%2$s"></span></span></div>',
		wp_kses_post( $message ),
		esc_attr( round( $ratio, 3 ) )
	);
}

/**
 * Register fragments.
 *
 * @param array $fragments Fragments.
 * @return array
 */
function scentral_cart_fragments( $fragments ) {
	$fragments['span.cart-count']   = scentral_cart_count_html();
	$fragments['span.bag-count']    = scentral_bag_count_html();
	$fragments['div.ship-progress'] = scentral_free_shipping_html();
	return $fragments;
}

/**
 * "(n)" count shown next to the bag drawer title.
 *
 * @return string
 */
function scentral_bag_count_html() {
	$count = WC()->cart ? WC()->cart->get_cart_contents_count() : 0;
	return '<span class="bag-count">(' . (int) $count . ')</span>';
}

/**
 * Mark the primary-menu item that leads to the Shop page so the mega panel can attach to it,
 * however the link is stored (page item, ?page_id=, http/https, trailing slash).
 *
 * @param string[] $classes Item classes.
 * @param WP_Post  $item    Menu item.
 * @param stdClass $args    wp_nav_menu() args.
 * @return string[]
 */
function scentral_mark_shop_menu_item( $classes, $item, $args ) {
	if ( empty( $args->theme_location ) || 'primary' !== $args->theme_location || (int) $item->menu_item_parent ) {
		return $classes;
	}
	$shop_id = (int) wc_get_page_id( 'shop' );
	$is_shop = ( 'page' === $item->object && (int) $item->object_id === $shop_id )
		|| ( 'post_type_archive' === $item->type && 'product' === $item->object );
	if ( ! $is_shop && $shop_id > 0 && ! empty( $item->url ) ) {
		$normalise = static function ( $url ) {
			return untrailingslashit( preg_replace( '#^https?://#', '', strtolower( (string) $url ) ) );
		};
		$is_shop = $normalise( $item->url ) === $normalise( get_permalink( $shop_id ) )
			|| (int) url_to_postid( $item->url ) === $shop_id;
	}
	if ( $is_shop ) {
		$classes[] = 'has-mega';
	}
	return $classes;
}
add_filter( 'nav_menu_css_class', 'scentral_mark_shop_menu_item', 10, 3 );
add_filter( 'woocommerce_add_to_cart_fragments', 'scentral_cart_fragments' );

/**
 * Product images (featured first, then gallery) with repeats removed: the same attachment
 * twice, or the same file uploaded twice (identical file size and dimensions).
 *
 * @param WC_Product $product Product.
 * @return int[]
 */
function scentral_product_image_ids( $product ) {
	$ids  = array_merge( array( (int) $product->get_image_id() ), array_map( 'intval', $product->get_gallery_image_ids() ) );
	$seen = array();
	$out  = array();
	foreach ( array_filter( $ids ) as $id ) {
		$meta = wp_get_attachment_metadata( $id );
		$key  = is_array( $meta ) && ! empty( $meta['filesize'] )
			? $meta['filesize'] . 'x' . ( $meta['width'] ?? 0 ) . 'x' . ( $meta['height'] ?? 0 )
			: 'id' . $id;
		if ( isset( $seen[ $key ] ) || in_array( $id, $out, true ) ) {
			continue;
		}
		$seen[ $key ] = true;
		$out[]        = $id;
	}
	return $out;
}

/* Single product page: see inc/product-page.php. */

/* -------------------------------------------------------------------------
 * Archive.
 * ---------------------------------------------------------------------- */

/**
 * Pagination arrows.
 *
 * @param array $args Pagination args.
 * @return array
 */
function scentral_pagination_args( $args ) {
	$args['prev_text'] = scentral_icon( 'arrow-left' ) . '<span class="screen-reader-text">' . esc_html__( 'Previous', 'scentral' ) . '</span>';
	$args['next_text'] = scentral_icon( 'arrow' ) . '<span class="screen-reader-text">' . esc_html__( 'Next', 'scentral' ) . '</span>';
	return $args;
}
add_filter( 'woocommerce_pagination_args', 'scentral_pagination_args' );

/**
 * Price range of visible products for the price filter (cached).
 *
 * @return array{min:int,max:int}
 */
function scentral_price_bounds() {
	global $wpdb;
	$key    = 'scentral_price_bounds_' . get_option( 'scentral_cache_version', 1 );
	$bounds = get_transient( $key );
	if ( false === $bounds ) {
		$row    = $wpdb->get_row( "SELECT MIN(min_price) AS min_price, MAX(max_price) AS max_price FROM {$wpdb->wc_product_meta_lookup}" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$bounds = array(
			'min' => $row ? (int) floor( (float) $row->min_price ) : 0,
			'max' => $row ? (int) ceil( (float) $row->max_price ) : 0,
		);
		set_transient( $key, $bounds, DAY_IN_SECONDS );
	}
	return $bounds;
}

/* Result count and ordering are rendered in the theme toolbar instead. */
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );

/**
 * Mark archive grids for staggered reveals and "load more".
 *
 * @param string $html Loop start markup.
 * @return string
 */
function scentral_loop_start( $html ) {
	if ( is_shop() || is_product_taxonomy() || is_search() ) {
		$html = str_replace( '<ul class="products', '<ul data-grid data-stagger class="products', $html );
	}
	return $html;
}
add_filter( 'woocommerce_product_loop_start', 'scentral_loop_start' );

/**
 * Support ?on_sale=1 on the shop and category archives.
 *
 * @param WP_Query $query Query.
 */
function scentral_on_sale_query( $query ) {
	if ( is_admin() || ! $query->is_main_query() || empty( $_GET['on_sale'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only filter.
		return;
	}
	if ( ! ( $query->is_post_type_archive( 'product' ) || $query->is_tax( get_object_taxonomies( 'product' ) ) ) ) {
		return;
	}
	$ids = wc_get_product_ids_on_sale();
	$query->set( 'post__in', $ids ? $ids : array( 0 ) );
}
add_action( 'pre_get_posts', 'scentral_on_sale_query' );

/**
 * Active archive filters with "remove" URLs.
 *
 * @return array<int,array{label:string,remove:string}>
 */
function scentral_active_filters() {
	// phpcs:disable WordPress.Security.NonceVerification.Recommended -- read-only display of query args.
	$filters = array();
	$min     = isset( $_GET['min_price'] ) ? absint( $_GET['min_price'] ) : 0;
	$max     = isset( $_GET['max_price'] ) ? absint( $_GET['max_price'] ) : 0;
	if ( $min || $max ) {
		$filters[] = array(
			'label'  => trim( ( $min ? wp_strip_all_tags( wc_price( $min, array( 'decimals' => 0 ) ) ) : '' ) . ' – ' . ( $max ? wp_strip_all_tags( wc_price( $max, array( 'decimals' => 0 ) ) ) : '' ) ),
			'remove' => remove_query_arg( array( 'min_price', 'max_price', 'paged' ) ),
		);
	}
	if ( ! empty( $_GET['on_sale'] ) ) {
		$filters[] = array(
			'label'  => __( 'On sale', 'scentral' ),
			'remove' => remove_query_arg( array( 'on_sale', 'paged' ) ),
		);
	}
	foreach ( wc_get_attribute_taxonomies() as $tax ) {
		$key = 'filter_' . $tax->attribute_name;
		if ( ! empty( $_GET[ $key ] ) ) {
			$filters[] = array(
				'label'  => $tax->attribute_label . ': ' . sanitize_text_field( wp_unslash( $_GET[ $key ] ) ),
				'remove' => remove_query_arg( array( $key, 'paged' ) ),
			);
		}
	}
	// phpcs:enable
	return $filters;
}

/* -------------------------------------------------------------------------
 * Account.
 * ---------------------------------------------------------------------- */

/**
 * Wrap account pages.
 */
function scentral_account_intro() {
	if ( ! is_user_logged_in() ) {
		return;
	}
	$user = wp_get_current_user();
	printf(
		'<p class="account-greeting eyebrow">%s</p>',
		/* translators: %s: customer first name or display name. */
		esc_html( sprintf( __( 'Welcome back, %s', 'scentral' ), $user->first_name ? $user->first_name : $user->display_name ) )
	);
}
add_action( 'woocommerce_before_account_navigation', 'scentral_account_intro', 5 );
