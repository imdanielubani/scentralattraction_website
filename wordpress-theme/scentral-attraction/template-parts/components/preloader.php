<?php
/**
 * Preloader — "The First Spray".
 *
 * The emblem bottle draws itself and fills as the page loads; notes rotate; at 100% the cap lifts
 * (the screen splits at the bottle's neck). Shown only when the inline head script adds
 * `sa-loading` to <html> (never for reduced motion, never twice per session by default).
 * A CSS failsafe hides it after 4.5s even if JavaScript fails.
 *
 * @package Scentral
 */

defined( 'ABSPATH' ) || exit;

$words = array_filter( array_map( 'trim', explode( ',', (string) scentral_option( 'preloader_words' ) ) ) );
$words = apply_filters( 'scentral_preloader_words', $words ? $words : array( __( 'Your signature', 'scentral' ) ) );

$face = static function () use ( $words ) {
	?>
	<div class="preloader__face">
		<div class="preloader__top">
			<span class="preloader__brand"><?php esc_html_e( 'Scentral Attraction', 'scentral' ); ?><i></i></span>
			<span class="preloader__line"><?php echo esc_html( scentral_option( 'brand_tagline' ) ); ?></span>
		</div>
		<div class="preloader__art">
			<?php $clip_id = 'sa-juice-' . wp_unique_id(); ?>
			<svg class="preloader__fill" viewBox="60 80 392 320" aria-hidden="true">
				<defs>
					<clipPath id="<?php echo esc_attr( $clip_id ); ?>">
						<path d="M206 159H306L320 173V371L306 385H206L192 371V173Z"/>
					</clipPath>
				</defs>
				<rect class="preloader__juice" clip-path="url(#<?php echo esc_attr( $clip_id ); ?>)" x="186" y="155" width="140" height="234"/>
			</svg>
			<?php scentral_emblem( array( 'class' => 'preloader__emblem emblem', 'draw' => true ) ); ?>
			<div class="preloader__rule"><i></i><span><?php echo esc_html( scentral_option( 'preloader_caption' ) ); ?></span></div>
		</div>
		<div class="preloader__bottom">
			<div class="preloader__words">
				<?php foreach ( array_values( $words ) as $i => $word ) : ?>
					<span<?php echo 0 === $i ? ' class="is-on"' : ''; ?>><?php echo esc_html( $word ); ?></span>
				<?php endforeach; ?>
			</div>
			<div class="preloader__count"><span data-count>0</span><small>%</small></div>
		</div>
	</div>
	<?php
};
?>
<div class="preloader" data-preloader aria-hidden="true">
	<div class="preloader__half preloader__half--top"><?php $face(); ?></div>
	<div class="preloader__half preloader__half--bottom"><?php $face(); ?></div>
	<div class="preloader__seam"></div>
	<div class="preloader__mist">
		<?php for ( $i = 0; $i < 14; $i++ ) : ?>
			<span style="--x:<?php echo esc_attr( ( $i * 37 ) % 100 ); ?>;--d:<?php echo esc_attr( ( $i * 53 ) % 9 ); ?>"></span>
		<?php endfor; ?>
	</div>
</div>
