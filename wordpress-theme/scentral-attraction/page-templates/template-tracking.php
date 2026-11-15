<?php
/**
 * Template Name: Order Tracking
 * Template Post Type: page
 *
 * Same arrangement as the Contact page: pill breadcrumb + large headline with intro, a
 * rounded card holding WooCommerce's order-tracking form (or the order status once found)
 * and the dark help card beside it. Applied automatically to any page that contains the
 * [woocommerce_order_tracking] shortcode and has no other template chosen.
 *
 * @package Scentral
 */

defined( 'ABSPATH' ) || exit;

get_header();

if ( scentral_render_builder_page() ) {
	get_footer();
	return;
}

// phpcs:ignore WordPress.Security.NonceVerification.Missing -- read-only: WooCommerce verifies the nonce itself.
$is_result = ! empty( $_REQUEST['orderid'] );
?>
<main id="main" class="site-main site-main--contact site-main--tracking">
	<?php
	while ( have_posts() ) :
		the_post();
		get_template_part(
			'template-parts/components/page-head',
			null,
			array(
				'id'    => 'tracking-title',
				'title' => __( 'Track your *order*.', 'scentral' ),
				'intro' => __( 'Enter your order number and the email address you used at checkout to see the latest status of your order.', 'scentral' ),
			)
		);
		?>

		<section class="cf container" id="order-tracking" aria-labelledby="tracking-form-title">
			<div class="cf__card cf__card--tracking" data-reveal>
				<p class="eyebrow cf__eyebrow"><i aria-hidden="true"></i><?php esc_html_e( 'Order status', 'scentral' ); ?></p>
				<h2 class="cf__title" id="tracking-form-title">
					<?php
					echo $is_result // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by scentral_rich_title().
						? scentral_rich_title( __( 'Here is your *order*.', 'scentral' ) )
						: scentral_rich_title( __( 'Where is my *order*?', 'scentral' ) );
					?>
				</h2>
				<div class="cf__plugin tracking-content">
					<?php the_content(); ?>
				</div>
			</div>

			<?php
			$perks = array(
				array(
					'icon' => 'mail',
					'text' => __( 'Your order number is in your confirmation email', 'scentral' ),
				),
				array(
					'icon' => 'truck',
					'text' => __( 'Shipping & delivery', 'scentral' ),
					'url'  => scentral_page_url( 'shipping-delivery' ),
				),
				array(
					'icon' => 'return',
					'text' => __( 'Returns & refunds', 'scentral' ),
					'url'  => scentral_page_url( 'returns-refunds' ),
				),
			);
			if ( function_exists( 'wc_get_page_permalink' ) ) {
				$perks[] = array(
					'icon' => 'user',
					'text' => __( 'See all your orders in My account', 'scentral' ),
					'url'  => wc_get_page_permalink( 'myaccount' ),
				);
			}
			get_template_part(
				'template-parts/components/help-card',
				null,
				array(
					'title' => __( 'Need a hand?', 'scentral' ),
					'label' => __( 'Help with your order', 'scentral' ),
					'perks' => $perks,
				)
			);
			?>
		</section>
	<?php endwhile; ?>
</main>
<?php
get_footer();
