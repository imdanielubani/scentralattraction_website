<?php
/**
 * Template Name: About (Editorial)
 * Template Post Type: page
 *
 * The page's own content is the story. Values below quote the brand's own published words.
 *
 * @package Scentral
 */

defined( 'ABSPATH' ) || exit;

get_header();

$story_image = scentral_campaign_image( 'editorial-story' );
$wide_image  = scentral_campaign_image( 'campaign-bestsellers' );
$values      = apply_filters(
	'scentral_about_values',
	array(
		array(
			'title' => __( 'Curated, not crowded', 'scentral' ),
			'text'  => __( 'Our collection is carefully selected to cater to diverse tastes and personalities.', 'scentral' ),
		),
		array(
			'title' => __( 'Quality & authenticity', 'scentral' ),
			'text'  => __( 'A trusted destination for perfume lovers who seek quality, authenticity, and exceptional customer care.', 'scentral' ),
		),
		array(
			'title' => __( 'A lasting impression', 'scentral' ),
			'text'  => __( 'Scents that not only smell incredible but leave a lasting impression wherever you go.', 'scentral' ),
		),
	)
);
?>
<main id="main" class="site-main site-main--about">
	<?php while ( have_posts() ) : the_post(); ?>
		<header class="about-hero container">
			<?php scentral_breadcrumbs(); ?>
			<p class="eyebrow" data-reveal><?php the_title(); ?></p>
			<h1 class="about-hero__title split"><?php echo scentral_split_words( __( 'Where elegance meets *scent*.', 'scentral' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h1>
		</header>

		<section class="about-story container">
			<?php if ( $story_image ) : ?>
				<figure class="about-story__media" data-reveal="clip">
					<div data-parallax="0.06"><?php scentral_picture( $story_image, __( 'Fragrance ingredients arranged around a perfume bottle', 'scentral' ), array( 'sizes' => '(min-width: 900px) 42vw, 100vw' ) ); ?></div>
				</figure>
			<?php endif; ?>
			<div class="about-story__text prose prose--dropcap" data-reveal>
				<?php the_content(); ?>
			</div>
		</section>

		<section class="section section--dark about-values" aria-labelledby="values-title">
			<div class="container">
				<?php
				scentral_section_heading(
					array(
						'eyebrow' => __( 'What we stand for', 'scentral' ),
						'title'   => __( 'More than a scent — an *experience*', 'scentral' ),
						'id'      => 'values-title',
					)
				);
				?>
				<ol class="values" data-stagger>
					<?php foreach ( $values as $index => $value ) : ?>
						<li class="values__item" data-reveal>
							<span class="values__num"><?php echo esc_html( sprintf( '%02d', $index + 1 ) ); ?></span>
							<h3 class="values__title"><?php echo esc_html( $value['title'] ); ?></h3>
							<p class="values__text"><?php echo esc_html( $value['text'] ); ?></p>
						</li>
					<?php endforeach; ?>
				</ol>
			</div>
		</section>

		<?php if ( $wide_image ) : ?>
			<figure class="about-wide" data-reveal="clip">
				<div data-parallax="0.1"><?php scentral_picture( $wide_image, __( 'A perfume bottle resting on silk with dried rose petals', 'scentral' ) ); ?></div>
			</figure>
		<?php endif; ?>

		<section class="section about-cta">
			<div class="container about-cta__inner" data-reveal>
				<p class="eyebrow"><?php esc_html_e( 'Your fragrance journey', 'scentral' ); ?></p>
				<h2 class="about-cta__title"><?php echo scentral_rich_title( __( 'Find your *signature* scent', 'scentral' ) ); // phpcs:ignore ?></h2>
				<div class="about-cta__actions">
					<a class="button" href="<?php echo esc_url( scentral_shop_url() ); ?>"><?php esc_html_e( 'Shop the collection', 'scentral' ); ?></a>
					<a class="link-arrow" href="<?php echo esc_url( scentral_page_url( 'contact-us' ) ); ?>"><?php esc_html_e( 'Ask for advice', 'scentral' ); ?><?php echo scentral_icon( 'arrow' ); // phpcs:ignore ?></a>
				</div>
			</div>
		</section>
	<?php endwhile; ?>
</main>
<?php
get_footer();
