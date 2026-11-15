<?php
/**
 * Single product page, modelled on the reference product page.
 *
 * Gallery: vertical thumbnails beside a large rounded stage with arrow buttons and hover zoom
 * (theme gallery, shared with quick view). Summary: pill breadcrumb, collection, title, price
 * with rating, short description, quantity pill + "Add to bag" pill, wishlist, stock and
 * service chips, then accordions (details, notes, specifications, delivery & returns, reviews).
 * Everything is attached through WooCommerce hooks so plugins keep working.
 *
 * @package Scentral
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WooCommerce' ) ) {
	return;
}

/*
 * Layout: theme gallery instead of WooCommerce's slider; no tabs (accordions in the summary).
 */
remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10 );
remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 50 );
remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );

/**
 * Theme gallery (shared with quick view).
 */
function scentral_single_gallery() {
	global $product;
	get_template_part(
		'template-parts/components/product-gallery',
		null,
		array(
			'product' => $product,
			'context' => 'single',
		)
	);
}
add_action( 'woocommerce_before_single_product_summary', 'scentral_single_gallery', 20 );

/**
 * Pill breadcrumb at the top of the summary column (reference placement).
 */
function scentral_single_breadcrumbs() {
	scentral_breadcrumbs();
}
add_action( 'woocommerce_single_product_summary', 'scentral_single_breadcrumbs', 2 );

/**
 * Collection / size eyebrow above the product title.
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
 * Price and rating on one row.
 */
function scentral_single_price_row() {
	global $product;
	echo '<div class="product-meta-row">';
	echo '<p class="' . esc_attr( apply_filters( 'woocommerce_product_price_class', 'price' ) ) . '">' . wp_kses_post( $product->get_price_html() ) . '</p>'; // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- WooCommerce filter.
	if ( wc_review_ratings_enabled() && $product->get_review_count() ) {
		printf(
			'<a class="product-rate" href="#product-reviews">%1$s<span>%2$s</span></a>',
			wc_get_rating_html( $product->get_average_rating() ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core output.
			esc_html(
				sprintf(
					/* translators: 1: average rating, 2: review count. */
					_n( '%1$s · %2$d review', '%1$s · %2$d reviews', $product->get_review_count(), 'scentral' ),
					number_format_i18n( (float) $product->get_average_rating(), 1 ),
					$product->get_review_count()
				)
			)
		);
	}
	echo '</div>';
}
add_action( 'woocommerce_single_product_summary', 'scentral_single_price_row', 10 );

/**
 * Short description fallback: a trimmed long description when no short description exists.
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
 * "Add to bag" wording on the product page button.
 *
 * @param string     $text    Button text.
 * @param WC_Product $product Product.
 * @return string
 */
function scentral_single_add_to_cart_text( $text, $product = null ) {
	if ( $product instanceof WC_Product && $product->is_purchasable() && $product->is_in_stock() ) {
		return __( 'Add to bag', 'scentral' );
	}
	return $text;
}
add_filter( 'woocommerce_product_single_add_to_cart_text', 'scentral_single_add_to_cart_text', 10, 2 );

/**
 * Wishlist, stock line and service chips under the buy row.
 */
function scentral_single_actions() {
	global $product;
	$payments = scentral_option( 'footer_payments_note' );
	$address  = scentral_option( 'contact_address' );
	echo '<div class="product-actions">';
	scentral_wishlist_button( $product, 'single' );
	echo '</div>';
	if ( $product->is_in_stock() ) {
		echo '<p class="product-eta"><i aria-hidden="true"></i>' . esc_html__( 'In stock — order online or visit us in store', 'scentral' ) . '</p>';
	} else {
		echo '<p class="product-eta product-eta--out"><i aria-hidden="true"></i>' . esc_html__( 'Sold out — ask us about the next delivery', 'scentral' ) . '</p>';
	}
	echo '<ul class="product-perks">';
	if ( $payments ) {
		echo '<li>' . esc_html( $payments ) . '</li>';
	}
	if ( $address ) {
		/* translators: %s: store address. */
		echo '<li>' . esc_html( sprintf( __( 'Store: %s', 'scentral' ), $address ) ) . '</li>';
	}
	echo '<li>' . esc_html__( 'Original fragrances', 'scentral' ) . '</li>';
	echo '</ul>';
}
add_action( 'woocommerce_single_product_summary', 'scentral_single_actions', 32 );

/**
 * Accordion row.
 *
 * @param string $title   Summary text.
 * @param string $content Inner HTML (already escaped / kses'd by the caller).
 * @param bool   $open    Open by default.
 * @param string $id      Optional id for anchors.
 */
function scentral_accordion_item( $title, $content, $open = false, $id = '' ) {
	if ( '' === trim( $content ) ) {
		return;
	}
	printf(
		'<details class="acc"%1$s%2$s><summary>%3$s<i aria-hidden="true"></i></summary><div class="acc__body">%4$s</div></details>',
		$id ? ' id="' . esc_attr( $id ) . '"' : '',
		$open ? ' open' : '',
		esc_html( $title ),
		$content // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by callers.
	);
}

/**
 * Accordions: details, fragrance notes, specifications, delivery & returns, reviews.
 */
function scentral_single_accordions() {
	global $product;

	echo '<div class="product-acc">';

	// The details (long description).
	$description = apply_filters( 'the_content', $product->get_description() ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- core filter.
	scentral_accordion_item( __( 'The details', 'scentral' ), wp_kses_post( $description ), true );

	// Fragrance notes (theme product meta; only when filled in).
	ob_start();
	scentral_render_fragrance_profile();
	scentral_accordion_item( __( 'Fragrance notes', 'scentral' ), (string) ob_get_clean() );

	// Specifications.
	$sku   = $product->get_sku();
	$attrs = array_filter(
		$product->get_attributes(),
		static function ( $attribute ) {
			return $attribute->get_visible();
		}
	);
	if ( $attrs || $sku ) {
		$rows = '';
		foreach ( $attrs as $attribute ) {
			$rows .= '<div><dt>' . esc_html( wc_attribute_label( $attribute->get_name(), $product ) ) . '</dt><dd>' . esc_html( $product->get_attribute( $attribute->get_name() ) ) . '</dd></div>';
		}
		if ( $sku ) {
			$rows .= '<div><dt>' . esc_html__( 'SKU', 'scentral' ) . '</dt><dd>' . esc_html( $sku ) . '</dd></div>';
		}
		scentral_accordion_item( __( 'Specifications', 'scentral' ), '<dl class="spec-list">' . $rows . '</dl>' );
	}

	// Delivery & returns: Customizer text when set, otherwise links to the policy pages.
	$delivery = scentral_option( 'shop_delivery_note' );
	$returns  = scentral_option( 'shop_returns_note' );
	$body     = '';
	if ( $delivery ) {
		$body .= wp_kses_post( wpautop( $delivery ) );
	}
	if ( $returns ) {
		$body .= wp_kses_post( wpautop( $returns ) );
	}
	$body .= '<p class="acc__links"><a href="' . esc_url( scentral_page_url( 'shipping-delivery' ) ) . '">' . esc_html__( 'Shipping & delivery', 'scentral' ) . '</a><a href="' . esc_url( scentral_page_url( 'returns-refunds' ) ) . '">' . esc_html__( 'Returns & refunds', 'scentral' ) . '</a></p>';
	$phone = scentral_option( 'contact_phone' );
	if ( $phone ) {
		$body .= '<p class="product-help">' . scentral_icon( 'phone' ) . esc_html__( 'Need advice choosing?', 'scentral' ) . ' <a href="' . esc_attr( scentral_tel( $phone ) ) . '">' . esc_html( $phone ) . '</a></p>';
	}
	scentral_accordion_item( __( 'Delivery & returns', 'scentral' ), $body );

	// Reviews (WooCommerce's own reviews template and form).
	if ( comments_open() || $product->get_review_count() ) {
		ob_start();
		comments_template();
		$title = sprintf( /* translators: %d: review count. */ __( 'Reviews (%d)', 'scentral' ), $product->get_review_count() );
		scentral_accordion_item( $title, (string) ob_get_clean(), false, 'product-reviews' );
	}

	echo '</div>';
}
add_action( 'woocommerce_single_product_summary', 'scentral_single_accordions', 45 );

/**
 * Related products: up to 8 in a rail with arrows (template: woocommerce/single-product/related.php).
 *
 * @param array $args Args.
 * @return array
 */
function scentral_related_args( $args ) {
	$args['posts_per_page'] = 8;
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
