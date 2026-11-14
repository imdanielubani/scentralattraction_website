<?php
/**
 * Template Name: Full width
 * Template Post Type: page
 *
 * A blank canvas: title plus full-width editor content (for block layouts and builders).
 *
 * @package Scentral
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main id="main" class="site-main site-main--fullwidth">
	<?php while ( have_posts() ) : the_post(); ?>
		<header class="page-hero page-hero--compact container">
			<?php scentral_breadcrumbs(); ?>
			<h1 class="page-hero__title" data-reveal><?php the_title(); ?></h1>
		</header>
		<div class="entry-wide"><?php the_content(); ?></div>
	<?php endwhile; ?>
</main>
<?php
get_footer();
