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
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );
}
add_action( 'after_setup_theme', 'scentral_woocommerce_setup' );

/**
 * HPOS + cart/checkout blocks compatibility is declared by plugins, not themes; nothing to do here.
 * Enqueue the cart fragments script so the header bag stays in sync on every page.
 */
function scentral_wc_scripts() {
	wp_enqueue_script( 'wc-cart-fragments' );
	if ( is_product() || is_shop() || is_product_taxonomy() || is_front_page() || is_search() ) {
		wp_enqueue_script( 'wc-add-to-cart' );
	}
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
	/* translators: %s: product name. */
	$args['attributes']['aria-label'] = sprintf( __( 'Add %s to your bag', 'scentral' ), $product->get_name() );
	return $args;
}
add_filter( 'woocommerce_loop_add_to_cart_args', 'scentral_loop_add_to_cart_args', 10, 2 );

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
	$fragments['span.cart-count']      = scentral_cart_count_html();
	$fragments['div.ship-progress']    = scentral_free_shipping_html();
	return $fragments;
}
add_filter( 'woocommerce_add_to_cart_fragments', 'scentral_cart_fragments' );

/* -------------------------------------------------------------------------
 * Single product.
 * ---------------------------------------------------------------------- */

remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 50 );

/**
 * Spec eyebrow above the product title.
 */
function scentral_single_spec() {
	global $product;
	$spec = scentral_product_spec( $product );
	if ( $spec ) {
		echo '<p class="eyebrow product-spec">' . esc_html( $spec ) . '</p>';
	}
}
add_action( 'woocommerce_single_product_summary', 'scentral_single_spec', 4 );

/**
 * Short description fallback: use a trimmed long description when no short description exists.
 *
 * @param string $excerpt Short description.
 * @return string
 */
function scentral_short_description_fallback( $excerpt ) {
	global $product;
	if ( trim( wp_strip_all_tags( $excerpt ) ) || ! $product instanceof WC_Product || ! is_product() ) {
		return $excerpt;
	}
	return wpautop( wp_trim_words( wp_strip_all_tags( $product->get_description() ), 45 ) );
}
add_filter( 'woocommerce_short_description', 'scentral_short_description_fallback' );

/**
 * Wishlist button + stock line after add to cart.
 */
function scentral_single_actions() {
	global $product;
	echo '<div class="product-actions">';
	scentral_wishlist_button( $product, 'single' );
	echo '</div>';
}
add_action( 'woocommerce_single_product_summary', 'scentral_single_actions', 32 );

/**
 * Fragrance profile, then delivery/returns details, inside the summary.
 */
add_action( 'woocommerce_single_product_summary', 'scentral_render_fragrance_profile', 35 );

/**
 * Delivery & returns details (Customizer text; hidden when empty) + product meta.
 */
function scentral_single_details() {
	global $product;
	$delivery = scentral_option( 'shop_delivery_note' );
	$returns  = scentral_option( 'shop_returns_note' );
	$sku      = $product->get_sku();
	$attrs    = array_filter(
		$product->get_attributes(),
		static function ( $attribute ) {
			return $attribute->get_visible();
		}
	);
	?>
	<div class="product-details">
		<?php if ( $attrs || $sku ) : ?>
			<details class="disclosure" open>
				<summary><?php esc_html_e( 'Specifications', 'scentral' ); ?><?php echo scentral_icon( 'plus' ); // phpcs:ignore ?></summary>
				<dl class="spec-list">
					<?php foreach ( $attrs as $attribute ) : ?>
						<div><dt><?php echo esc_html( wc_attribute_label( $attribute->get_name(), $product ) ); ?></dt><dd><?php echo esc_html( $product->get_attribute( $attribute->get_name() ) ); ?></dd></div>
					<?php endforeach; ?>
					<?php if ( $sku ) : ?>
						<div><dt><?php esc_html_e( 'SKU', 'scentral' ); ?></dt><dd><?php echo esc_html( $sku ); ?></dd></div>
					<?php endif; ?>
				</dl>
			</details>
		<?php endif; ?>
		<?php if ( $delivery ) : ?>
			<details class="disclosure">
				<summary><?php echo scentral_icon( 'truck' ); // phpcs:ignore ?><?php esc_html_e( 'Delivery', 'scentral' ); ?><?php echo scentral_icon( 'plus' ); // phpcs:ignore ?></summary>
				<div class="disclosure__body"><?php echo wp_kses_post( wpautop( $delivery ) ); ?></div>
			</details>
		<?php endif; ?>
		<?php if ( $returns ) : ?>
			<details class="disclosure">
				<summary><?php echo scentral_icon( 'return' ); // phpcs:ignore ?><?php esc_html_e( 'Returns', 'scentral' ); ?><?php echo scentral_icon( 'plus' ); // phpcs:ignore ?></summary>
				<div class="disclosure__body"><?php echo wp_kses_post( wpautop( $returns ) ); ?></div>
			</details>
		<?php endif; ?>
		<?php
		$phone = scentral_option( 'contact_phone' );
		if ( $phone ) :
			?>
			<p class="product-help"><?php echo scentral_icon( 'phone' ); // phpcs:ignore ?><?php esc_html_e( 'Need advice choosing?', 'scentral' ); ?> <a href="<?php echo esc_attr( scentral_tel( $phone ) ); ?>"><?php echo esc_html( $phone ); ?></a></p>
		<?php endif; ?>
	</div>
	<?php
}
add_action( 'woocommerce_single_product_summary', 'scentral_single_details', 45 );

/**
 * Remove the "Additional information" tab (shown as Specifications in the summary instead).
 *
 * @param array $tabs Tabs.
 * @return array
 */
function scentral_product_tabs( $tabs ) {
	unset( $tabs['additional_information'] );
	if ( isset( $tabs['description'] ) ) {
		$tabs['description']['title'] = __( 'The story', 'scentral' );
	}
	return $tabs;
}
add_filter( 'woocommerce_product_tabs', 'scentral_product_tabs', 20 );

/**
 * Related products: 4 per row.
 *
 * @param array $args Args.
 * @return array
 */
function scentral_related_args( $args ) {
	$args['posts_per_page'] = 4;
	$args['columns']        = 4;
	return $args;
}
add_filter( 'woocommerce_output_related_products_args', 'scentral_related_args' );

/**
 * Recently viewed container (filled client-side via the Store API so it works with page caching).
 */
function scentral_recently_viewed_container() {
	if ( ! scentral_option( 'shop_recently_viewed' ) ) {
		return;
	}
	global $product;
	printf(
		'<section class="recently-viewed container" data-recent data-current="%1$d" hidden aria-labelledby="recent-title"><h2 class="section-heading__title" id="recent-title">%2$s</h2><ul class="products products--rail" data-recent-list></ul></section>',
		$product instanceof WC_Product ? absint( $product->get_id() ) : 0,
		esc_html__( 'Recently viewed', 'scentral' )
	);
}
add_action( 'woocommerce_after_single_product', 'scentral_recently_viewed_container', 30 );

/**
 * Breadcrumbs above the single product.
 */
function scentral_single_breadcrumbs() {
	echo '<div class="container product-crumbs">';
	scentral_breadcrumbs();
	echo '</div>';
}
add_action( 'woocommerce_before_single_product', 'scentral_single_breadcrumbs', 5 );

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
