<?php
/**
 * Mini-cart (bag drawer contents), modelled on the reference bag:
 * photo · name · − qty + pill · line price, then a large subtotal and a Checkout pill.
 * Empty bag suggests three in-stock fragrances with a quick "+" add.
 *
 * Override of woocommerce/templates/cart/mini-cart.php. All standard actions and filters are kept,
 * and items keep the .woocommerce-mini-cart-item / .remove_from_cart_button classes WooCommerce relies on.
 *
 * @package Scentral
 * @version 9.4.0
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_mini_cart' );

if ( WC()->cart && ! WC()->cart->is_empty() ) : ?>

	<ul class="woocommerce-mini-cart cart_list product_list_widget bag-items" data-bag-nonce="<?php echo esc_attr( wp_create_nonce( 'scentral_cart' ) ); ?>">
		<?php
		do_action( 'woocommerce_before_mini_cart_contents' );

		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
			$_product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
			if ( ! $_product || ! $_product->exists() || $cart_item['quantity'] <= 0 || ! apply_filters( 'woocommerce_widget_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
				continue;
			}
			$product_name = apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key );
			$thumbnail    = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image( 'woocommerce_thumbnail' ), $cart_item, $cart_item_key );
			$permalink    = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
			$line_price   = WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] );
			$max_qty      = $_product->get_max_purchase_quantity();
			$spec         = scentral_product_size( $_product );
			$qty          = (int) $cart_item['quantity'];
			?>
			<li class="woocommerce-mini-cart-item <?php echo esc_attr( apply_filters( 'woocommerce_mini_cart_item_class', 'mini_cart_item', $cart_item, $cart_item_key ) ); ?> bag-item">
				<a class="bag-item__media" href="<?php echo esc_url( $permalink ? $permalink : '#' ); ?>" tabindex="-1" aria-hidden="true"><?php echo $thumbnail; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core image markup. ?></a>
				<div class="bag-item__info">
					<?php if ( $permalink ) : ?>
						<a class="bag-item__name" href="<?php echo esc_url( $permalink ); ?>"><?php echo wp_kses_post( $product_name ); ?></a>
					<?php else : ?>
						<span class="bag-item__name"><?php echo wp_kses_post( $product_name ); ?></span>
					<?php endif; ?>
					<?php if ( $spec ) : ?>
						<span class="bag-item__spec"><?php echo esc_html( $spec ); ?></span>
					<?php endif; ?>
					<?php echo wc_get_formatted_cart_item_data( $cart_item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core escaped output. ?>
					<?php if ( ! $_product->is_sold_individually() ) : ?>
						<div class="bag-qty" data-bag-qty data-key="<?php echo esc_attr( $cart_item_key ); ?>" data-qty="<?php echo esc_attr( $qty ); ?>">
							<button type="button" data-step="-1" aria-label="<?php echo esc_attr( sprintf( /* translators: %s: product name. */ __( 'One less %s', 'scentral' ), wp_strip_all_tags( $product_name ) ) ); ?>">&minus;</button>
							<span aria-live="polite"><?php echo esc_html( $qty ); ?></span>
							<button type="button" data-step="1" aria-label="<?php echo esc_attr( sprintf( /* translators: %s: product name. */ __( 'One more %s', 'scentral' ), wp_strip_all_tags( $product_name ) ) ); ?>"<?php echo ( $max_qty > 0 && $qty >= $max_qty ) ? ' disabled' : ''; ?>>+</button>
						</div>
					<?php endif; ?>
				</div>
				<div class="bag-item__end">
					<em class="bag-item__price"><?php echo wp_kses_post( apply_filters( 'woocommerce_widget_cart_item_quantity', $line_price, $cart_item, $cart_item_key ) ); ?></em>
					<?php
					echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core filter, attributes escaped below.
						'woocommerce_cart_item_remove_link',
						sprintf(
							'<a role="button" href="%s" class="remove remove_from_cart_button bag-item__remove" aria-label="%s" data-product_id="%s" data-cart_item_key="%s" data-product_sku="%s">%s</a>',
							esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
							/* translators: %s: product name. */
							esc_attr( sprintf( __( 'Remove %s from your bag', 'scentral' ), wp_strip_all_tags( $product_name ) ) ),
							esc_attr( $_product->get_id() ),
							esc_attr( $cart_item_key ),
							esc_attr( $_product->get_sku() ),
							esc_html__( 'Remove', 'scentral' )
						),
						$cart_item_key
					);
					?>
				</div>
			</li>
			<?php
		}

		do_action( 'woocommerce_mini_cart_contents' );
		?>
	</ul>

	<footer class="bag-foot">
		<p class="woocommerce-mini-cart__total total bag-foot__total">
			<span><?php esc_html_e( 'Subtotal', 'scentral' ); ?></span>
			<b><?php echo WC()->cart->get_cart_subtotal(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core price markup. ?></b>
		</p>
		<small class="bag-foot__note"><?php esc_html_e( 'Delivery and taxes are worked out at checkout.', 'scentral' ); ?></small>
		<?php do_action( 'woocommerce_widget_shopping_cart_before_buttons' ); ?>
		<p class="woocommerce-mini-cart__buttons buttons bag-foot__buttons">
			<a class="button checkout wc-forward bag-foot__checkout" href="<?php echo esc_url( wc_get_checkout_url() ); ?>"><?php esc_html_e( 'Checkout', 'scentral' ); ?><span class="bag-arrow" aria-hidden="true"><?php echo scentral_icon( 'arrow' ); // phpcs:ignore ?></span></a>
			<a class="bag-foot__view link-draw" href="<?php echo esc_url( wc_get_cart_url() ); ?>"><?php esc_html_e( 'View full bag', 'scentral' ); ?></a>
		</p>
		<?php do_action( 'woocommerce_widget_shopping_cart_after_buttons' ); ?>
	</footer>

<?php else : ?>

	<div class="bag-empty">
		<p class="woocommerce-mini-cart__empty-message bag-empty__text"><?php esc_html_e( 'Your bag is empty. Start with a favourite.', 'scentral' ); ?></p>
		<?php
		$suggestions = array_slice(
			array_values(
				array_filter(
					scentral_get_products(
						array(
							'source' => 'newest',
							'limit'  => 10,
						)
					),
					static function ( $item ) {
						return $item->is_in_stock() && $item->is_purchasable() && $item->is_type( 'simple' );
					}
				)
			),
			0,
			3
		);
		?>
		<?php if ( $suggestions ) : ?>
			<ul class="bag-empty__list">
				<?php foreach ( $suggestions as $item ) : ?>
					<li>
						<a class="bag-empty__link" href="<?php echo esc_url( $item->get_permalink() ); ?>">
							<?php echo wp_kses_post( $item->get_image( 'woocommerce_gallery_thumbnail', array( 'loading' => 'eager', 'decoding' => 'async' ) ) ); ?>
							<b><?php echo esc_html( $item->get_name() ); ?></b>
							<i><?php echo wp_kses_post( $item->get_price_html() ); ?></i>
						</a>
						<a class="bag-empty__add add_to_cart_button ajax_add_to_cart" href="<?php echo esc_url( $item->add_to_cart_url() ); ?>" data-quantity="1" data-product_id="<?php echo esc_attr( $item->get_id() ); ?>" data-product_sku="<?php echo esc_attr( $item->get_sku() ); ?>" rel="nofollow" aria-label="<?php echo esc_attr( sprintf( /* translators: %s: product name. */ __( 'Add %s to your bag', 'scentral' ), $item->get_name() ) ); ?>">+</a>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
		<a class="button button--outline bag-empty__shop" href="<?php echo esc_url( scentral_shop_url() ); ?>"><?php esc_html_e( 'Shop all fragrances', 'scentral' ); ?></a>
	</div>

<?php endif; ?>

<?php
do_action( 'woocommerce_after_mini_cart' );
