<?php
/**
 * Pages: every page of the site lives in WordPress → Pages (editable with the block editor or Elementor).
 *
 * On theme activation, pages that do not exist yet are created as drafts with the right template.
 * Existing pages are never overwritten or deleted; an existing page only receives a template when it
 * still uses the default template. Appearance → Scentral Setup lists every page with edit links.
 *
 * @package Scentral
 */

defined( 'ABSPATH' ) || exit;

/**
 * Pages the site needs: slug => [title, template, create-if-missing].
 *
 * @return array<string,array{0:string,1:string,2:bool}>
 */
function scentral_required_pages() {
	return array(
		'about-us'             => array( __( 'About Us', 'scentral' ), 'page-templates/template-about.php', true ),
		'contact-us'           => array( __( 'Contact Us', 'scentral' ), 'page-templates/template-contact.php', true ),
		'faq'                  => array( __( 'FAQ', 'scentral' ), 'page-templates/template-faq.php', true ),
		'shipping-delivery'    => array( __( 'Shipping & Delivery', 'scentral' ), 'page-templates/template-policy.php', true ),
		'returns-refunds'      => array( __( 'Returns & Refunds', 'scentral' ), 'page-templates/template-policy.php', true ),
		'terms-and-conditions' => array( __( 'Terms & Conditions', 'scentral' ), 'page-templates/template-policy.php', true ),
		'journal'              => array( __( 'Journal', 'scentral' ), '', true ),
		'wishlist'             => array( __( 'Wishlist', 'scentral' ), 'page-templates/template-wishlist.php', true ),
	);
}

/**
 * Find an existing page for a slug (also matches the store's numbered slugs, e.g. privacy-policy-2).
 *
 * @param string $slug Slug.
 * @return WP_Post|null
 */
function scentral_find_page( $slug ) {
	$page = get_page_by_path( $slug, OBJECT, 'page' );
	if ( $page ) {
		return $page;
	}
	$found = get_posts(
		array(
			'post_type'      => 'page',
			'post_status'    => array( 'publish', 'draft', 'pending', 'private', 'future' ),
			'name'           => $slug . '-2',
			'posts_per_page' => 1,
		)
	);
	return $found ? $found[0] : null;
}

/**
 * Create missing pages (drafts) and assign templates to pages still on the default template.
 *
 * @return array{created:int[],templated:int[]}
 */
function scentral_setup_pages() {
	$result = array(
		'created'   => array(),
		'templated' => array(),
	);

	foreach ( scentral_required_pages() as $slug => $def ) {
		list( $title, $template, $create ) = $def;
		$page = scentral_find_page( $slug );

		if ( ! $page && $create ) {
			$page_id = wp_insert_post(
				array(
					'post_type'    => 'page',
					'post_status'  => 'draft',
					'post_title'   => $title,
					'post_name'    => $slug,
					'post_content' => '',
				),
				true
			);
			if ( ! is_wp_error( $page_id ) ) {
				if ( $template ) {
					update_post_meta( $page_id, '_wp_page_template', $template );
				}
				$result['created'][] = (int) $page_id;
			}
			continue;
		}

		if ( $page && $template ) {
			$current = get_post_meta( $page->ID, '_wp_page_template', true );
			if ( ( '' === $current || 'default' === $current ) && ! scentral_is_builder_page( $page->ID ) ) {
				update_post_meta( $page->ID, '_wp_page_template', $template );
				$result['templated'][] = (int) $page->ID;
			}
		}
	}

	// Privacy policy page (WordPress setting): give it the Policy template if it is on the default one.
	$privacy_id = (int) get_option( 'wp_page_for_privacy_policy' );
	if ( $privacy_id && 'page' === get_post_type( $privacy_id ) ) {
		$current = get_post_meta( $privacy_id, '_wp_page_template', true );
		if ( ( '' === $current || 'default' === $current ) && ! scentral_is_builder_page( $privacy_id ) ) {
			update_post_meta( $privacy_id, '_wp_page_template', 'page-templates/template-policy.php' );
			$result['templated'][] = $privacy_id;
		}
	}

	$created = get_option( 'scentral_pages_created', array() );
	update_option( 'scentral_pages_created', array_values( array_unique( array_merge( (array) $created, $result['created'] ) ) ), false );
	return $result;
}

/**
 * Run page setup on theme activation.
 */
function scentral_setup_pages_on_activation() {
	if ( current_user_can( 'edit_pages' ) ) {
		scentral_setup_pages();
	}
}
add_action( 'after_switch_theme', 'scentral_setup_pages_on_activation' );

/**
 * Admin screen: Appearance → Scentral Setup.
 */
function scentral_setup_menu() {
	add_theme_page(
		__( 'Scentral Setup', 'scentral' ),
		__( 'Scentral Setup', 'scentral' ),
		'edit_pages',
		'scentral-setup',
		'scentral_render_setup_page'
	);
}
add_action( 'admin_menu', 'scentral_setup_menu' );

/**
 * Handle the "Create missing pages" button.
 */
function scentral_handle_setup_post() {
	if ( ! current_user_can( 'edit_pages' ) ) {
		wp_die( esc_html__( 'You are not allowed to do this.', 'scentral' ) );
	}
	check_admin_referer( 'scentral_setup_pages' );
	$result = scentral_setup_pages();
	wp_safe_redirect(
		add_query_arg(
			array(
				'page'    => 'scentral-setup',
				'created' => count( $result['created'] ),
			),
			admin_url( 'themes.php' )
		)
	);
	exit;
}
add_action( 'admin_post_scentral_setup_pages', 'scentral_handle_setup_post' );

/**
 * Render the setup screen.
 */
function scentral_render_setup_page() {
	if ( ! current_user_can( 'edit_pages' ) ) {
		return;
	}
	$rows = array();
	foreach ( scentral_required_pages() as $slug => $def ) {
		$rows[ $slug ] = array(
			'title' => $def[0],
			'page'  => scentral_find_page( $slug ),
		);
	}
	$special = array(
		__( 'Home (front page)', 'scentral' ) => (int) get_option( 'page_on_front' ),
		__( 'Privacy Policy', 'scentral' )    => (int) get_option( 'wp_page_for_privacy_policy' ),
	);
	if ( class_exists( 'WooCommerce' ) ) {
		$special[ __( 'Shop', 'scentral' ) ]       = wc_get_page_id( 'shop' );
		$special[ __( 'Cart', 'scentral' ) ]       = wc_get_page_id( 'cart' );
		$special[ __( 'Checkout', 'scentral' ) ]   = wc_get_page_id( 'checkout' );
		$special[ __( 'My account', 'scentral' ) ] = wc_get_page_id( 'myaccount' );
	}
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- display-only flag after redirect.
	$created = isset( $_GET['created'] ) ? absint( $_GET['created'] ) : null;
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Scentral Setup', 'scentral' ); ?></h1>
		<?php if ( null !== $created ) : ?>
			<div class="notice notice-success"><p>
				<?php
				/* translators: %d: number of pages created. */
				echo esc_html( sprintf( _n( '%d page created as a draft.', '%d pages created as drafts.', $created, 'scentral' ), $created ) );
				?>
			</p></div>
		<?php endif; ?>
		<p><?php esc_html_e( 'Every page of the site is a normal WordPress page. Edit it with the block editor or with Elementor. New pages are created as drafts so nothing empty is published: add your text, then click Publish.', 'scentral' ); ?></p>
		<table class="widefat striped" style="max-width:900px">
			<thead><tr><th><?php esc_html_e( 'Page', 'scentral' ); ?></th><th><?php esc_html_e( 'Status', 'scentral' ); ?></th><th><?php esc_html_e( 'Edit', 'scentral' ); ?></th></tr></thead>
			<tbody>
				<?php foreach ( $special as $label => $page_id ) : ?>
					<?php scentral_setup_row( $label, $page_id > 0 ? get_post( $page_id ) : null ); ?>
				<?php endforeach; ?>
				<?php foreach ( $rows as $row ) : ?>
					<?php scentral_setup_row( $row['title'], $row['page'] ); ?>
				<?php endforeach; ?>
			</tbody>
		</table>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="margin-top:1.5rem">
			<input type="hidden" name="action" value="scentral_setup_pages">
			<?php wp_nonce_field( 'scentral_setup_pages' ); ?>
			<?php submit_button( __( 'Create missing pages', 'scentral' ), 'primary', 'submit', false ); ?>
		</form>
		<h2 style="margin-top:2.5rem"><?php esc_html_e( 'Using Elementor', 'scentral' ); ?></h2>
		<ul style="list-style:disc;padding-left:1.25rem">
			<li><?php esc_html_e( 'Open any page and click "Edit with Elementor". Pages built with Elementor are shown full width exactly as designed, with the theme header and footer.', 'scentral' ); ?></li>
			<li><?php esc_html_e( 'In the Elementor panel, the "Scentral Attraction" category contains the "Scentral Section" widget: hero, product rails, showcase, collections, newsletter and more.', 'scentral' ); ?></li>
			<li><?php esc_html_e( 'To use your Elementor-built Home page instead of the theme homepage: Customize → Scentral Theme → Homepage — Sections → Homepage layout.', 'scentral' ); ?></li>
		</ul>
	</div>
	<?php
}

/**
 * One row of the setup table.
 *
 * @param string       $label Label.
 * @param WP_Post|null $page  Page.
 */
function scentral_setup_row( $label, $page ) {
	echo '<tr><td>' . esc_html( $label ) . '</td>';
	if ( ! $page ) {
		echo '<td><em>' . esc_html__( 'Missing', 'scentral' ) . '</em></td><td>—</td></tr>';
		return;
	}
	$status = get_post_status_object( get_post_status( $page ) );
	echo '<td>' . esc_html( $status ? $status->label : '' ) . '</td><td>';
	echo '<a href="' . esc_url( get_edit_post_link( $page->ID ) ) . '">' . esc_html__( 'Edit', 'scentral' ) . '</a>';
	if ( scentral_has_elementor() ) {
		$url = add_query_arg(
			array(
				'post'   => $page->ID,
				'action' => 'elementor',
			),
			admin_url( 'post.php' )
		);
		echo ' · <a href="' . esc_url( $url ) . '">' . esc_html__( 'Edit with Elementor', 'scentral' ) . '</a>';
	}
	if ( 'publish' === get_post_status( $page ) ) {
		echo ' · <a href="' . esc_url( get_permalink( $page ) ) . '">' . esc_html__( 'View', 'scentral' ) . '</a>';
	}
	echo '</td></tr>';
}

/**
 * Use the Order Tracking template for a page containing [woocommerce_order_tracking]
 * when no template has been chosen for it (the default WooCommerce setup).
 *
 * @param string $template Resolved template path.
 * @return string
 */
function scentral_tracking_template( $template ) {
	if ( ! is_page() || get_page_template_slug() ) {
		return $template;
	}
	$post = get_queried_object();
	if ( $post instanceof WP_Post && has_shortcode( $post->post_content, 'woocommerce_order_tracking' ) ) {
		$tracking = locate_template( 'page-templates/template-tracking.php' );
		if ( $tracking ) {
			return $tracking;
		}
	}
	return $template;
}
add_filter( 'template_include', 'scentral_tracking_template', 20 );
