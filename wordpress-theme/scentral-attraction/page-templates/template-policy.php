<?php
/**
 * Template Name: Policy
 * Template Post Type: page
 *
 * For Shipping & Delivery, Returns & Refunds, Privacy Policy and Terms & Conditions.
 * Builds a table of contents from the page's H2 headings. Policy text always comes from
 * the store; an empty page shows a contact notice, never invented terms.
 *
 * @package Scentral
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main id="main" class="site-main site-main--policy">
	<?php
	while ( have_posts() ) :
		the_post();
		$html = apply_filters( 'the_content', get_the_content() ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- core filter.
		$toc  = array();
		$html = preg_replace_callback(
			'/<h2([^>]*)>(.*?)<\/h2>/is',
			static function ( $m ) use ( &$toc ) {
				$text = wp_strip_all_tags( $m[2] );
				$id   = preg_match( '/id="([^"]+)"/', $m[1], $idm ) ? $idm[1] : 'section-' . sanitize_title( $text );
				$toc[] = array(
					'id'   => $id,
					'text' => $text,
				);
				$attrs = preg_match( '/id="/', $m[1] ) ? $m[1] : $m[1] . ' id="' . esc_attr( $id ) . '"';
				return '<h2' . $attrs . '>' . $m[2] . '</h2>';
			},
			(string) $html
		);
		?>
		<header class="page-hero container">
			<?php scentral_breadcrumbs(); ?>
			<h1 class="page-hero__title" data-reveal><?php the_title(); ?></h1>
			<p class="policy__updated" data-reveal>
				<?php
				/* translators: %s: date. */
				echo esc_html( sprintf( __( 'Last updated %s', 'scentral' ), get_the_modified_date() ) );
				?>
			</p>
		</header>
		<div class="container policy">
			<?php if ( count( $toc ) > 1 ) : ?>
				<nav class="policy__toc" aria-label="<?php esc_attr_e( 'On this page', 'scentral' ); ?>">
					<p class="eyebrow"><?php esc_html_e( 'On this page', 'scentral' ); ?></p>
					<ol>
						<?php foreach ( $toc as $item ) : ?>
							<li><a class="link-draw" href="#<?php echo esc_attr( $item['id'] ); ?>"><?php echo esc_html( $item['text'] ); ?></a></li>
						<?php endforeach; ?>
					</ol>
				</nav>
			<?php endif; ?>
			<div class="prose policy__content">
				<?php if ( trim( wp_strip_all_tags( $html ) ) ) : ?>
					<?php echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- the_content output with added heading ids. ?>
				<?php else : ?>
					<?php get_template_part( 'template-parts/components/content-pending' ); ?>
				<?php endif; ?>
			</div>
		</div>
	<?php endwhile; ?>
</main>
<?php
get_footer();
