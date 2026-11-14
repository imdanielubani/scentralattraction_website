<?php
/**
 * Journal post card.
 *
 * @package Scentral
 *
 * @var array $args { feature (bool) }.
 */

defined( 'ABSPATH' ) || exit;

$feature = ! empty( $args['feature'] );
?>
<article <?php post_class( 'post-card' . ( $feature ? ' post-card--feature' : '' ) ); ?> data-reveal>
	<a class="post-card__media<?php echo has_post_thumbnail() ? '' : ' post-card__media--empty'; ?>" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
		<?php if ( has_post_thumbnail() ) : ?>
			<?php the_post_thumbnail( $feature ? 'scentral-banner' : 'scentral-card', array( 'loading' => $feature ? 'eager' : 'lazy' ) ); ?>
		<?php else : ?>
			<?php scentral_emblem( array( 'class' => 'emblem' ) ); ?>
		<?php endif; ?>
	</a>
	<div class="post-card__body">
		<?php scentral_post_meta(); ?>
		<h2 class="post-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
		<?php if ( $feature || has_excerpt() ) : ?>
			<p class="post-card__excerpt muted"><?php echo esc_html( wp_trim_words( get_the_excerpt(), $feature ? 36 : 20 ) ); ?></p>
		<?php endif; ?>
		<a class="link-arrow" href="<?php the_permalink(); ?>"><?php esc_html_e( 'Read', 'scentral' ); ?><span class="screen-reader-text"> <?php the_title(); ?></span><?php echo scentral_icon( 'arrow' ); // phpcs:ignore ?></a>
	</div>
</article>
