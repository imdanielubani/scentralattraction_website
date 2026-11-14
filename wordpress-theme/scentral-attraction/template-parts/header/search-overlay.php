<?php
/**
 * Search dialog with live product suggestions (WooCommerce Store API).
 *
 * @package Scentral
 */

defined( 'ABSPATH' ) || exit;

$has_wc = class_exists( 'WooCommerce' );
?>
<dialog class="dialog dialog--search" id="search-overlay" aria-labelledby="search-overlay-title">
	<div class="search-panel container">
		<div class="search-panel__head">
			<h2 class="eyebrow" id="search-overlay-title"><?php esc_html_e( 'Search the collection', 'scentral' ); ?></h2>
			<button type="button" class="dialog__close" data-close aria-label="<?php esc_attr_e( 'Close search', 'scentral' ); ?>"><?php echo scentral_icon( 'close' ); // phpcs:ignore ?></button>
		</div>
		<form class="search-panel__form" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" data-live-search>
			<label class="screen-reader-text" for="search-overlay-input"><?php esc_html_e( 'Search for', 'scentral' ); ?></label>
			<input id="search-overlay-input" class="search-panel__input" type="search" name="s" autocomplete="off" placeholder="<?php esc_attr_e( 'Oud, rose, Anfar London…', 'scentral' ); ?>" aria-controls="search-results" aria-autocomplete="list">
			<?php if ( $has_wc ) : ?>
				<input type="hidden" name="post_type" value="product">
			<?php endif; ?>
			<button class="search-panel__submit" type="submit" aria-label="<?php esc_attr_e( 'Submit search', 'scentral' ); ?>"><?php echo scentral_icon( 'arrow' ); // phpcs:ignore ?></button>
		</form>
		<div class="search-panel__results" id="search-results" aria-live="polite" data-search-results></div>
		<?php if ( $has_wc ) : ?>
			<?php $categories = scentral_featured_categories(); ?>
			<?php if ( $categories ) : ?>
				<div class="search-panel__suggest" data-search-suggest>
					<p class="eyebrow"><?php esc_html_e( 'Browse', 'scentral' ); ?></p>
					<ul class="chips">
						<?php foreach ( $categories as $term ) : ?>
							<li><a class="chip" href="<?php echo esc_url( get_term_link( $term ) ); ?>"><?php echo esc_html( $term->name ); ?></a></li>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endif; ?>
		<?php endif; ?>
	</div>
</dialog>
