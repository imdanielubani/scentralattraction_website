<?php
/**
 * Cached WooCommerce product queries for homepage sections.
 *
 * All product data is read live from WooCommerce. Results are cached as product IDs
 * (never as rendered prices) and the cache is invalidated whenever a product changes.
 *
 * @package Scentral
 */

defined( 'ABSPATH' ) || exit;

/**
 * Get product objects for a section.
 *
 * @param array $args { source: newest|bestsellers|on_sale|featured|ids|category, limit, ids (csv), category (slug), in_stock_first (bool) }.
 * @return WC_Product[]
 */
function scentral_get_products( $args ) {
	if ( ! function_exists( 'wc_get_products' ) ) {
		return array();
	}

	$args = wp_parse_args(
		$args,
		array(
			'source'   => 'newest',
			'limit'    => 8,
			'ids'      => '',
			'category' => '',
		)
	);

	$limit     = max( 1, min( 24, absint( $args['limit'] ) ) );
	$cache_key = 'scentral_q_' . md5( wp_json_encode( $args ) . '|' . get_option( 'scentral_cache_version', 1 ) );
	$ids       = get_transient( $cache_key );

	if ( false === $ids ) {
		$ids = scentral_query_product_ids( $args['source'], $limit, $args['ids'], $args['category'] );
		set_transient( $cache_key, $ids, 6 * HOUR_IN_SECONDS );
	}

	return array_values( array_filter( array_map( 'wc_get_product', (array) $ids ) ) );
}

/**
 * Run the uncached product ID query.
 *
 * @param string $source   Source key.
 * @param int    $limit    Limit.
 * @param string $ids      CSV IDs.
 * @param string $category Category slug.
 * @return int[]
 */
function scentral_query_product_ids( $source, $limit, $ids, $category ) {
	$query = array(
		'status'     => 'publish',
		'visibility' => 'catalog',
		'limit'      => $limit,
		'return'     => 'ids',
	);

	switch ( $source ) {
		case 'bestsellers':
			$query['orderby']  = 'meta_value_num';
			$query['meta_key'] = 'total_sales'; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			$query['order']    = 'DESC';
			break;
		case 'on_sale':
			$sale = wc_get_product_ids_on_sale();
			if ( ! $sale ) {
				return array();
			}
			$query['include'] = $sale;
			break;
		case 'featured':
			$query['featured'] = true;
			break;
		case 'ids':
			$list = array_filter( array_map( 'absint', explode( ',', (string) $ids ) ) );
			if ( ! $list ) {
				return array();
			}
			$query['include'] = $list;
			$query['orderby'] = 'include';
			break;
		case 'category':
			if ( ! $category ) {
				return array();
			}
			$query['category'] = array( sanitize_title( $category ) );
			$query['orderby']  = 'date';
			$query['order']    = 'DESC';
			break;
		default:
			$query['orderby'] = 'date';
			$query['order']   = 'DESC';
	}

	$found = wc_get_products( $query );

	// Bestsellers fall back to newest when the store has no sales history yet.
	if ( 'bestsellers' === $source && $found && ! scentral_has_sales( $found ) ) {
		return array();
	}

	return array_map( 'absint', (array) $found );
}

/**
 * Whether any of the given products has recorded sales.
 *
 * @param int[] $ids Product IDs.
 * @return bool
 */
function scentral_has_sales( $ids ) {
	foreach ( $ids as $id ) {
		if ( (int) get_post_meta( $id, 'total_sales', true ) > 0 ) {
			return true;
		}
	}
	return false;
}

/**
 * Product categories to feature (configured slugs, or all non-empty top-level categories).
 *
 * @return WP_Term[]
 */
function scentral_featured_categories() {
	if ( ! taxonomy_exists( 'product_cat' ) ) {
		return array();
	}
	$slugs = array_filter( array_map( 'sanitize_title', explode( ',', (string) scentral_option( 'home_categories' ) ) ) );
	$args  = array(
		'taxonomy'   => 'product_cat',
		'hide_empty' => true,
		'exclude'    => array( (int) get_option( 'default_product_cat' ) ),
	);
	if ( $slugs ) {
		$args['slug']    = $slugs;
		$args['orderby'] = 'slug__in';
	} else {
		$args['parent'] = 0;
	}
	$terms = get_terms( $args );
	return is_wp_error( $terms ) ? array() : $terms;
}

/**
 * Get a category term by slug only when it has products.
 *
 * @param string $slug Category slug.
 * @return WP_Term|null
 */
function scentral_category_with_products( $slug ) {
	$slug = sanitize_title( (string) $slug );
	if ( ! $slug || ! taxonomy_exists( 'product_cat' ) ) {
		return null;
	}
	$term = get_term_by( 'slug', $slug, 'product_cat' );
	return ( $term && ! is_wp_error( $term ) && $term->count > 0 ) ? $term : null;
}

/**
 * Category thumbnail URL (WooCommerce category image, else first product image).
 *
 * @param WP_Term $term Category.
 * @param string  $size Image size.
 * @return string
 */
function scentral_category_image( $term, $size = 'scentral-card' ) {
	$thumb_id = (int) get_term_meta( $term->term_id, 'thumbnail_id', true );
	if ( $thumb_id ) {
		$src = wp_get_attachment_image_url( $thumb_id, $size );
		if ( $src ) {
			return $src;
		}
	}
	$products = scentral_get_products(
		array(
			'source'   => 'category',
			'category' => $term->slug,
			'limit'    => 1,
		)
	);
	if ( $products && $products[0]->get_image_id() ) {
		return (string) wp_get_attachment_image_url( $products[0]->get_image_id(), $size );
	}
	return '';
}

/**
 * Store figures for the "numbers" section, all counted live.
 *
 * @return array<int,array{value:int,label:string}>
 */
function scentral_store_numbers() {
	if ( ! function_exists( 'wc_get_products' ) ) {
		return array();
	}
	$cache_key = 'scentral_numbers_' . get_option( 'scentral_cache_version', 1 );
	$numbers   = get_transient( $cache_key );
	if ( false !== $numbers ) {
		return $numbers;
	}

	$products = wc_get_products(
		array(
			'status' => 'publish',
			'limit'  => -1,
			'return' => 'ids',
		)
	);
	$cats     = scentral_featured_categories();
	$sizes    = array();
	foreach ( (array) $products as $id ) {
		$product = wc_get_product( $id );
		$size    = $product ? scentral_product_size( $product ) : '';
		if ( $size ) {
			$sizes[ strtoupper( $size ) ] = true;
		}
	}

	$numbers = array_values(
		array_filter(
			array(
				array(
					'value' => count( (array) $products ),
					'label' => __( 'Fragrances in the collection', 'scentral' ),
				),
				array(
					'value' => count( $cats ),
					'label' => __( 'Curated collections', 'scentral' ),
				),
				array(
					'value' => count( $sizes ),
					'label' => __( 'Bottle sizes', 'scentral' ),
				),
			),
			static function ( $row ) {
				return $row['value'] > 0;
			}
		)
	);

	set_transient( $cache_key, $numbers, 12 * HOUR_IN_SECONDS );
	return $numbers;
}

/**
 * Genuine, approved product reviews for the testimonials section.
 *
 * Excludes reviews written by administrators/shop managers and reviews below the minimum rating.
 *
 * @param int $limit Max reviews.
 * @return WP_Comment[]
 */
function scentral_get_reviews( $limit = 6 ) {
	$min      = max( 1, min( 5, absint( scentral_option( 'home_testimonial_min' ) ) ) );
	$comments = get_comments(
		array(
			'post_type' => 'product',
			'status'    => 'approve',
			'type'      => 'review',
			'number'    => 40,
			'meta_query' => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
				array(
					'key'     => 'rating',
					'value'   => $min,
					'compare' => '>=',
					'type'    => 'NUMERIC',
				),
			),
		)
	);

	$genuine = array();
	foreach ( $comments as $comment ) {
		if ( $comment->user_id && user_can( (int) $comment->user_id, 'manage_woocommerce' ) ) {
			continue;
		}
		if ( strlen( trim( wp_strip_all_tags( $comment->comment_content ) ) ) < 12 ) {
			continue;
		}
		$genuine[] = $comment;
		if ( count( $genuine ) >= $limit ) {
			break;
		}
	}
	return $genuine;
}
