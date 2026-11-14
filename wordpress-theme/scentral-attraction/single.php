<?php
/**
 * Journal article.
 *
 * @package Scentral
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main id="main" class="site-main site-main--article">
	<?php
	while ( have_posts() ) :
		the_post();
		?>
		<article id="post-<?php the_ID(); ?>" <?php post_class( 'article' ); ?>>
			<header class="article__head container">
				<?php scentral_breadcrumbs(); ?>
				<?php scentral_post_meta(); ?>
				<h1 class="article__title split"><?php echo scentral_split_words( get_the_title() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h1>
				<?php if ( has_excerpt() ) : ?>
					<p class="lead article__dek" data-reveal><?php echo esc_html( get_the_excerpt() ); ?></p>
				<?php endif; ?>
			</header>
			<?php if ( has_post_thumbnail() ) : ?>
				<figure class="article__media container" data-reveal="clip">
					<?php the_post_thumbnail( 'scentral-hero', array( 'loading' => 'eager', 'fetchpriority' => 'high' ) ); ?>
				</figure>
			<?php endif; ?>
			<div class="container">
				<div class="prose prose--dropcap article__body">
					<?php
					the_content();
					wp_link_pages(
						array(
							'before' => '<nav class="page-links">',
							'after'  => '</nav>',
						)
					);
					?>
				</div>
				<?php the_tags( '<p class="article__tags chips">', '', '</p>' ); ?>
			</div>
		</article>
		<?php
		$cats    = wp_get_post_categories( get_the_ID() );
		$related = new WP_Query(
			array(
				'post_type'           => 'post',
				'posts_per_page'      => 3,
				'post__not_in'        => array( get_the_ID() ),
				'category__in'        => $cats,
				'ignore_sticky_posts' => true,
				'no_found_rows'       => true,
			)
		);
		if ( $related->have_posts() ) :
			?>
			<section class="section section--surface" aria-labelledby="related-title">
				<div class="container">
					<?php
					scentral_section_heading(
						array(
							'eyebrow' => __( 'Keep reading', 'scentral' ),
							'title'   => __( 'From the journal', 'scentral' ),
							'id'      => 'related-title',
						)
					);
					?>
					<div class="post-grid" data-stagger>
						<?php
						while ( $related->have_posts() ) {
							$related->the_post();
							get_template_part( 'template-parts/components/post-card' );
						}
						wp_reset_postdata();
						?>
					</div>
				</div>
			</section>
			<?php
		endif;

		if ( comments_open() || get_comments_number() ) {
			echo '<div class="container">';
			comments_template();
			echo '</div>';
		}
	endwhile;
	?>
</main>
<?php
get_footer();
