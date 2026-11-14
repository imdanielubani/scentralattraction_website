<?php
/**
 * Elementor compatibility.
 *
 * - Pages built with Elementor render full width, exactly as designed (no theme title or wrappers).
 * - Elementor's own "Elementor Canvas" and "Elementor Full Width" templates work as usual.
 * - Elementor Pro Theme Builder can replace the header/footer (core locations registered).
 * - A "Scentral Section" widget exposes every theme section inside the Elementor editor.
 * Everything here is inert when Elementor is not installed.
 *
 * @package Scentral
 */

defined( 'ABSPATH' ) || exit;

/**
 * Is Elementor loaded?
 *
 * @return bool
 */
function scentral_has_elementor() {
	return did_action( 'elementor/loaded' ) && class_exists( '\Elementor\Plugin' );
}

/**
 * Is this post built with Elementor?
 *
 * @param int $post_id Post ID (default current).
 * @return bool
 */
function scentral_is_builder_page( $post_id = 0 ) {
	if ( ! scentral_has_elementor() ) {
		return false;
	}
	$post_id = $post_id ? (int) $post_id : (int) get_the_ID();
	if ( ! $post_id ) {
		return false;
	}
	$document = \Elementor\Plugin::$instance->documents->get( $post_id );
	return $document && $document->is_built_with_elementor();
}

/**
 * Is the request the Elementor editor preview?
 *
 * @return bool
 */
function scentral_is_elementor_preview() {
	return scentral_has_elementor()
		&& isset( \Elementor\Plugin::$instance->preview )
		&& \Elementor\Plugin::$instance->preview->is_preview_mode();
}

/**
 * Render the current singular page as a full-width builder canvas when it is built with Elementor.
 * Call right after get_header(); when it returns true the template should call get_footer() and stop.
 *
 * @return bool True when the page was rendered here.
 */
function scentral_render_builder_page() {
	if ( ! is_singular() || ! scentral_is_builder_page( get_queried_object_id() ) ) {
		return false;
	}
	echo '<main id="main" class="site-main site-main--builder">';
	while ( have_posts() ) {
		the_post();
		the_content();
	}
	echo '</main>';
	return true;
}

/**
 * Register Elementor Pro Theme Builder locations (header, footer, single, archive).
 *
 * @param object $manager Elementor locations manager.
 */
function scentral_register_elementor_locations( $manager ) {
	$manager->register_all_core_location();
}
add_action( 'elementor/theme/register_locations', 'scentral_register_elementor_locations' );

/**
 * Add a "Scentral Attraction" widget category.
 *
 * @param object $elements_manager Elementor elements manager.
 */
function scentral_elementor_category( $elements_manager ) {
	$elements_manager->add_category(
		'scentral',
		array(
			'title' => __( 'Scentral Attraction', 'scentral' ),
			'icon'  => 'eicon-site-identity',
		)
	);
}
add_action( 'elementor/elements/categories_registered', 'scentral_elementor_category' );

/**
 * Register the "Scentral Section" widget.
 *
 * @param object $widgets_manager Elementor widgets manager.
 */
function scentral_register_elementor_widgets( $widgets_manager ) {
	require_once SCENTRAL_DIR . '/inc/elementor-widget.php';
	$widgets_manager->register( new Scentral_Elementor_Section_Widget() );
}
add_action( 'elementor/widgets/register', 'scentral_register_elementor_widgets' );
