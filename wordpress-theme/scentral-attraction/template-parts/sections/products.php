<?php
/**
 * Product rail (new arrivals, bestsellers, on sale, featured). Renders nothing when empty.
 *
 * @package Scentral
 *
 * @var array $args { id, source, title, text, eyebrow, tone }.
 */

defined( 'ABSPATH' ) || exit;

$args     = wp_parse_args(
	$args,
	array(
		'id'      => 'products',
		'source'  => 'newest',
		'title'   => '',
		'text'    => '',
		'eyebrow' => '',
		'tone'    => 'light',
	)
);
$products = scentral_get_products(
	array(
		'source' => $args['source'],
		'limit'  => max( 4, absint( scentral_option( 'home_products_count' ) ) ),
	)
);
if ( ! $products ) {
	return;
}

$link_args = array(
	'newest'      => array( 'orderby' => 'date' ),
	'bestsellers' => array( 'orderby' => 'popularity' ),
	'on_sale'     => array( 'on_sale' => '1' ),
	'featured'    => array(),
);
$view_all  = add_query_arg( isset( $link_args[ $args['source'] ] ) ? $link_args[ $args['source'] ] : array(), scentral_shop_url() );
$title_id  = 'rail-' . sanitize_html_class( $args['id'] );
?>
<section class="section rail rail--<?php echo esc_attr( $args['tone'] ); ?><?php echo 'surface' === $args['tone'] ? ' section--surface' : ''; ?>" aria-labelledby="<?php echo esc_attr( $title_id ); ?>">
	<div class="container">
		<?php
		scentral_section_heading(
			array(
				'eyebrow'    => $args['eyebrow'],
				'title'      => $args['title'],
				'text'       => $args['text'],
				'link_url'   => $view_all,
				'link_label' => __( 'View all', 'scentral' ),
				'id'         => $title_id,
			)
		);
		?>
		<div class="rail__viewport" data-rail>
			<ul class="products products--rail" data-stagger>
				<?php
				foreach ( $products as $index => $rail_product ) {
					$GLOBALS['product'] = $rail_product; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- WooCommerce loop convention.
					$GLOBALS['post']    = get_post( $rail_product->get_id() ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
					setup_postdata( $GLOBALS['post'] );
					wc_get_template_part( 'content', 'product' );
				}
				wp_reset_postdata();
				?>
			</ul>
		</div>
	</div>
</section>
