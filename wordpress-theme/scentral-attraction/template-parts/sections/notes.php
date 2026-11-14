<?php
/**
 * Notes ribbon: an endless line of fragrance notes (static for reduced motion).
 *
 * @package Scentral
 */

defined( 'ABSPATH' ) || exit;

$notes = array_filter( array_map( 'trim', explode( ',', (string) scentral_option( 'home_notes' ) ) ) );
if ( ! $notes ) {
	return;
}
$render = static function ( $hidden ) use ( $notes ) {
	echo '<ul class="ribbon__track"' . ( $hidden ? ' aria-hidden="true"' : '' ) . '>';
	foreach ( $notes as $i => $note ) {
		printf( '<li class="ribbon__item%1$s">%2$s</li>', 1 === $i % 2 ? ' ribbon__item--italic' : '', esc_html( $note ) );
	}
	echo '</ul>';
};
?>
<section class="ribbon" aria-label="<?php esc_attr_e( 'Fragrance notes in the collection', 'scentral' ); ?>">
	<div class="ribbon__rail">
		<?php
		$render( false );
		$render( true );
		?>
	</div>
</section>
