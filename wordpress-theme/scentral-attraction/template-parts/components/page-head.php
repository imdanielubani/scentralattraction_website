<?php
/**
 * Page head modelled on the reference contact page: pill breadcrumb, a large headline
 * (wrap one word in *asterisks* for the red italic accent) and an optional intro on the right.
 * Shared by the Contact and Order Tracking templates.
 *
 * Args: id (heading id), title, intro.
 *
 * @package Scentral
 */

defined( 'ABSPATH' ) || exit;

$args = wp_parse_args(
	$args ?? array(),
	array(
		'id'    => 'page-title',
		'title' => get_the_title(),
		'intro' => '',
	)
);
?>
<section class="ph" aria-labelledby="<?php echo esc_attr( $args['id'] ); ?>">
	<div class="container">
		<?php scentral_breadcrumbs(); ?>
		<div class="ph__grid">
			<h1 class="ph__title split" id="<?php echo esc_attr( $args['id'] ); ?>"><?php echo scentral_split_words( $args['title'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside. ?></h1>
			<?php if ( $args['intro'] ) : ?>
				<p class="ph__intro" data-reveal><?php echo esc_html( $args['intro'] ); ?></p>
			<?php endif; ?>
		</div>
	</div>
</section>
