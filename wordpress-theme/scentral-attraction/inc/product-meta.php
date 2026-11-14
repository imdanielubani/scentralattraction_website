<?php
/**
 * Fragrance profile meta box (top/heart/base notes, family, longevity, sillage, occasion).
 *
 * Stored as product post meta with the `_scentral_` prefix. Fields display only when filled in.
 *
 * @package Scentral
 */

defined( 'ABSPATH' ) || exit;

/**
 * Fragrance profile fields.
 *
 * @return array<string,array{label:string,type:string,help:string}>
 */
function scentral_fragrance_fields() {
	return array(
		'notes_top'   => array(
			'label' => __( 'Top notes', 'scentral' ),
			'type'  => 'text',
			'help'  => __( 'Comma separated, e.g. Pear, Pink pepper', 'scentral' ),
		),
		'notes_heart' => array(
			'label' => __( 'Heart notes', 'scentral' ),
			'type'  => 'text',
			'help'  => __( 'Comma separated, e.g. Tuberose, Jasmine', 'scentral' ),
		),
		'notes_base'  => array(
			'label' => __( 'Base notes', 'scentral' ),
			'type'  => 'text',
			'help'  => __( 'Comma separated, e.g. Amber, Musk', 'scentral' ),
		),
		'family'      => array(
			'label' => __( 'Scent family', 'scentral' ),
			'type'  => 'text',
			'help'  => __( 'e.g. Woody Oriental', 'scentral' ),
		),
		'longevity'   => array(
			'label' => __( 'Longevity (1–5)', 'scentral' ),
			'type'  => 'scale',
			'help'  => __( 'Leave empty to hide', 'scentral' ),
		),
		'sillage'     => array(
			'label' => __( 'Sillage / projection (1–5)', 'scentral' ),
			'type'  => 'scale',
			'help'  => __( 'Leave empty to hide', 'scentral' ),
		),
		'occasion'    => array(
			'label' => __( 'Best worn', 'scentral' ),
			'type'  => 'text',
			'help'  => __( 'e.g. Evening, Special occasions', 'scentral' ),
		),
	);
}

/**
 * Register the meta box.
 */
function scentral_add_fragrance_metabox() {
	add_meta_box(
		'scentral-fragrance',
		__( 'Fragrance Profile', 'scentral' ),
		'scentral_render_fragrance_metabox',
		'product',
		'normal',
		'default'
	);
}
add_action( 'add_meta_boxes', 'scentral_add_fragrance_metabox' );

/**
 * Render the meta box.
 *
 * @param WP_Post $post Product post.
 */
function scentral_render_fragrance_metabox( $post ) {
	wp_nonce_field( 'scentral_fragrance_save', 'scentral_fragrance_nonce' );
	echo '<p>' . esc_html__( 'Shown on the product page only when filled in.', 'scentral' ) . '</p>';
	echo '<table class="form-table" role="presentation"><tbody>';
	foreach ( scentral_fragrance_fields() as $key => $field ) {
		$value = get_post_meta( $post->ID, '_scentral_' . $key, true );
		echo '<tr><th scope="row"><label for="scentral_' . esc_attr( $key ) . '">' . esc_html( $field['label'] ) . '</label></th><td>';
		if ( 'scale' === $field['type'] ) {
			echo '<select id="scentral_' . esc_attr( $key ) . '" name="scentral_' . esc_attr( $key ) . '"><option value="">—</option>';
			for ( $i = 1; $i <= 5; $i++ ) {
				printf( '<option value="%1$d"%2$s>%1$d</option>', (int) $i, selected( (string) $value, (string) $i, false ) );
			}
			echo '</select>';
		} else {
			printf(
				'<input type="text" class="regular-text" id="scentral_%1$s" name="scentral_%1$s" value="%2$s">',
				esc_attr( $key ),
				esc_attr( $value )
			);
		}
		echo '<p class="description">' . esc_html( $field['help'] ) . '</p></td></tr>';
	}
	echo '</tbody></table>';
}

/**
 * Save the meta box.
 *
 * @param int $post_id Post ID.
 */
function scentral_save_fragrance_metabox( $post_id ) {
	if ( ! isset( $_POST['scentral_fragrance_nonce'] )
		|| ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['scentral_fragrance_nonce'] ) ), 'scentral_fragrance_save' ) ) {
		return;
	}
	if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ! current_user_can( 'edit_product', $post_id ) ) {
		return;
	}

	foreach ( scentral_fragrance_fields() as $key => $field ) {
		$name  = 'scentral_' . $key;
		$value = isset( $_POST[ $name ] ) ? sanitize_text_field( wp_unslash( $_POST[ $name ] ) ) : '';
		if ( 'scale' === $field['type'] ) {
			$value = ( '' === $value ) ? '' : (string) max( 1, min( 5, absint( $value ) ) );
		}
		if ( '' === $value ) {
			delete_post_meta( $post_id, '_' . $name );
		} else {
			update_post_meta( $post_id, '_' . $name, $value );
		}
	}
}
add_action( 'save_post_product', 'scentral_save_fragrance_metabox' );

/**
 * Read the fragrance profile for a product (only non-empty values).
 *
 * @param int $product_id Product ID.
 * @return array<string,string>
 */
function scentral_get_fragrance_profile( $product_id ) {
	$profile = array();
	foreach ( array_keys( scentral_fragrance_fields() ) as $key ) {
		$value = get_post_meta( $product_id, '_scentral_' . $key, true );
		if ( '' !== (string) $value ) {
			$profile[ $key ] = (string) $value;
		}
	}
	return $profile;
}

/**
 * Render the fragrance profile on the single product page.
 */
function scentral_render_fragrance_profile() {
	global $product;
	if ( ! $product instanceof WC_Product ) {
		return;
	}
	$profile = scentral_get_fragrance_profile( $product->get_id() );
	if ( ! $profile ) {
		return;
	}
	$tiers = array(
		'notes_top'   => __( 'Top', 'scentral' ),
		'notes_heart' => __( 'Heart', 'scentral' ),
		'notes_base'  => __( 'Base', 'scentral' ),
	);
	$scales = array(
		'longevity' => __( 'Longevity', 'scentral' ),
		'sillage'   => __( 'Sillage', 'scentral' ),
	);
	?>
	<section class="fragrance-profile" aria-labelledby="fragrance-profile-title">
		<h2 class="fragrance-profile__title" id="fragrance-profile-title"><?php esc_html_e( 'The scent', 'scentral' ); ?></h2>
		<?php if ( ! empty( $profile['family'] ) ) : ?>
			<p class="fragrance-profile__family"><span class="label"><?php esc_html_e( 'Family', 'scentral' ); ?></span> <?php echo esc_html( $profile['family'] ); ?></p>
		<?php endif; ?>
		<?php if ( array_intersect_key( $profile, $tiers ) ) : ?>
			<ol class="pyramid">
				<?php foreach ( $tiers as $key => $tier ) : ?>
					<?php if ( ! empty( $profile[ $key ] ) ) : ?>
						<li class="pyramid__tier pyramid__tier--<?php echo esc_attr( $key ); ?>">
							<span class="pyramid__label"><?php echo esc_html( $tier ); ?></span>
							<span class="pyramid__notes"><?php echo esc_html( $profile[ $key ] ); ?></span>
						</li>
					<?php endif; ?>
				<?php endforeach; ?>
			</ol>
		<?php endif; ?>
		<?php foreach ( $scales as $key => $label ) : ?>
			<?php if ( ! empty( $profile[ $key ] ) ) : ?>
				<div class="scale" role="img" aria-label="<?php echo esc_attr( sprintf( '%1$s: %2$d / 5', $label, (int) $profile[ $key ] ) ); ?>">
					<span class="label"><?php echo esc_html( $label ); ?></span>
					<span class="scale__track"><span class="scale__fill" style="--v:<?php echo esc_attr( (int) $profile[ $key ] / 5 ); ?>"></span></span>
				</div>
			<?php endif; ?>
		<?php endforeach; ?>
		<?php if ( ! empty( $profile['occasion'] ) ) : ?>
			<p class="fragrance-profile__occasion"><span class="label"><?php esc_html_e( 'Best worn', 'scentral' ); ?></span> <?php echo esc_html( $profile['occasion'] ); ?></p>
		<?php endif; ?>
	</section>
	<?php
}
