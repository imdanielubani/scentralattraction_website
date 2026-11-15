<?php
/**
 * Required and recommended plugins.
 *
 * After the theme is activated, administrators are taken to Appearance → Theme Plugins, which
 * lists the plugins the store depends on with one-click Install / Activate buttons and an
 * "Install & activate all required plugins" action (plugins come from WordPress.org through
 * WordPress's own installer). Until every required plugin is active, a notice stays on every
 * admin screen. The front end keeps working without them (the theme degrades gracefully).
 *
 * @package Scentral
 */

defined( 'ABSPATH' ) || exit;

/**
 * Plugin list. slug = WordPress.org slug (also the plugin folder name).
 *
 * @return array<int,array{slug:string,name:string,required:bool,why:string}>
 */
function scentral_theme_plugins() {
	return apply_filters(
		'scentral_theme_plugins',
		array(
			array(
				'slug'     => 'woocommerce',
				'name'     => 'WooCommerce',
				'required' => true,
				'why'      => __( 'Shop, product pages, bag, checkout and accounts.', 'scentral' ),
			),
			array(
				'slug'     => 'woo-paystack',
				'name'     => 'Paystack WooCommerce Payment Gateway',
				'required' => true,
				'why'      => __( 'Card, bank and transfer payments at checkout (the store’s payment method).', 'scentral' ),
			),
			array(
				'slug'     => 'elementor',
				'name'     => 'Elementor',
				'required' => true,
				'why'      => __( 'Edit any page visually; the theme’s sections are available as Elementor widgets.', 'scentral' ),
			),
			array(
				'slug'     => 'contact-form-7',
				'name'     => 'Contact Form 7',
				'required' => true,
				'why'      => __( 'The Contact page form and its emails (Mail and Mail (2)).', 'scentral' ),
			),
			array(
				'slug'     => 'ti-woocommerce-wishlist',
				'name'     => 'TI WooCommerce Wishlist',
				'required' => false,
				'why'      => __( 'Saved wishlists for customers (the theme has a simple built-in wishlist otherwise).', 'scentral' ),
			),
			array(
				'slug'     => 'mailchimp-for-wp',
				'name'     => 'MC4WP: Mailchimp for WordPress',
				'required' => false,
				'why'      => __( 'Send newsletter sign-ups to Mailchimp.', 'scentral' ),
			),
			array(
				'slug'     => 'wordpress-seo',
				'name'     => 'Yoast SEO',
				'required' => false,
				'why'      => __( 'Search titles, descriptions, sitemaps and breadcrumbs.', 'scentral' ),
			),
		)
	);
}

/**
 * Installed plugin file for a slug (folder name), or '' when not installed.
 *
 * @param string $slug Plugin slug.
 * @return string
 */
function scentral_plugin_file( $slug ) {
	if ( ! function_exists( 'get_plugins' ) ) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
	}
	$found = '';
	foreach ( array_keys( get_plugins() ) as $file ) {
		if ( 0 !== strpos( $file, $slug . '/' ) ) {
			continue;
		}
		// Prefer the main file named after the folder when a plugin ships several.
		if ( $file === $slug . '/' . $slug . '.php' ) {
			return $file;
		}
		if ( '' === $found ) {
			$found = $file;
		}
	}
	return $found;
}

/**
 * Status: 'active', 'inactive' (installed) or 'missing'.
 *
 * @param string $slug Plugin slug.
 * @return string
 */
function scentral_plugin_status( $slug ) {
	$file = scentral_plugin_file( $slug );
	if ( '' === $file ) {
		return 'missing';
	}
	return is_plugin_active( $file ) ? 'active' : 'inactive';
}

/**
 * Required plugins that are not active yet.
 *
 * @return array
 */
function scentral_missing_required_plugins() {
	return array_values(
		array_filter(
			scentral_theme_plugins(),
			static function ( $plugin ) {
				return $plugin['required'] && 'active' !== scentral_plugin_status( $plugin['slug'] );
			}
		)
	);
}

/**
 * Admin page URL.
 *
 * @param array $args Extra query args.
 * @return string
 */
function scentral_plugins_page_url( $args = array() ) {
	return add_query_arg( array_merge( array( 'page' => 'scentral-plugins' ), $args ), admin_url( 'themes.php' ) );
}

/**
 * Register Appearance → Theme Plugins.
 */
function scentral_plugins_menu() {
	add_theme_page( __( 'Theme Plugins', 'scentral' ), __( 'Theme Plugins', 'scentral' ), 'install_plugins', 'scentral-plugins', 'scentral_plugins_page' );
}
add_action( 'admin_menu', 'scentral_plugins_menu' );

/**
 * After activating the theme, go straight to the plugins page when something required is missing.
 */
function scentral_plugins_on_activation() {
	if ( scentral_missing_required_plugins() ) {
		set_transient( 'scentral_plugins_redirect', 1, 60 );
	}
}
add_action( 'after_switch_theme', 'scentral_plugins_on_activation' );

/**
 * Perform the one-time redirect.
 */
function scentral_plugins_redirect() {
	global $pagenow;
	if ( wp_doing_ajax() || ! current_user_can( 'install_plugins' ) ) {
		return;
	}
	// WordPress lands on themes.php?activated=true right after a theme is activated.
	$just_activated = 'themes.php' === $pagenow && isset( $_GET['activated'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- core flag, read only.
	if ( ! $just_activated && ! get_transient( 'scentral_plugins_redirect' ) ) {
		return;
	}
	delete_transient( 'scentral_plugins_redirect' );
	if ( ! scentral_missing_required_plugins() ) {
		return;
	}
	wp_safe_redirect( scentral_plugins_page_url() );
	exit;
}
add_action( 'admin_init', 'scentral_plugins_redirect' );

/**
 * Notice on every admin screen until the required plugins are active.
 */
function scentral_plugins_notice() {
	if ( ! current_user_can( 'activate_plugins' ) ) {
		return;
	}
	$screen = get_current_screen();
	if ( $screen && 'appearance_page_scentral-plugins' === $screen->id ) {
		return;
	}
	$missing = scentral_missing_required_plugins();
	if ( ! $missing ) {
		return;
	}
	$names = implode( ', ', wp_list_pluck( $missing, 'name' ) );
	printf(
		'<div class="notice notice-error"><p><strong>%1$s</strong> %2$s</p><p><a class="button button-primary" href="%3$s">%4$s</a></p></div>',
		esc_html__( 'Scentral Attraction needs these plugins to work fully:', 'scentral' ),
		esc_html( $names ),
		esc_url( scentral_plugins_page_url() ),
		esc_html__( 'Install & activate required plugins', 'scentral' )
	);
}
add_action( 'admin_notices', 'scentral_plugins_notice' );

/**
 * Install (from WordPress.org) and activate one plugin.
 *
 * @param string $slug Plugin slug.
 * @return true|WP_Error
 */
function scentral_install_activate_plugin( $slug ) {
	require_once ABSPATH . 'wp-admin/includes/plugin.php';
	require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/misc.php';
	require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';

	if ( 'missing' === scentral_plugin_status( $slug ) ) {
		$api = plugins_api(
			'plugin_information',
			array(
				'slug'   => $slug,
				'fields' => array( 'sections' => false ),
			)
		);
		if ( is_wp_error( $api ) ) {
			return $api;
		}
		$upgrader = new Plugin_Upgrader( new WP_Ajax_Upgrader_Skin() );
		$result   = $upgrader->install( $api->download_link );
		if ( is_wp_error( $result ) ) {
			return $result;
		}
		if ( ! $result ) {
			$errors = $upgrader->skin->get_errors();
			return is_wp_error( $errors ) && $errors->has_errors() ? $errors : new WP_Error( 'scentral_install', __( 'The plugin could not be installed. Check the file permissions or install it from Plugins → Add New.', 'scentral' ) );
		}
		wp_clean_plugins_cache();
	}

	$file = scentral_plugin_file( $slug );
	if ( '' === $file ) {
		return new WP_Error( 'scentral_missing', __( 'The plugin was not found after installing.', 'scentral' ) );
	}
	if ( ! is_plugin_active( $file ) ) {
		$activated = activate_plugin( $file, '', false, true );
		if ( is_wp_error( $activated ) ) {
			return $activated;
		}
	}
	return true;
}

/**
 * Handle Install / Activate requests from the Theme Plugins page.
 */
function scentral_plugins_handle() {
	if ( ! current_user_can( 'install_plugins' ) || ! current_user_can( 'activate_plugins' ) ) {
		wp_die( esc_html__( 'You are not allowed to install plugins on this site.', 'scentral' ), '', array( 'response' => 403 ) );
	}
	check_admin_referer( 'scentral_plugins' );

	$known = wp_list_pluck( scentral_theme_plugins(), 'required', 'slug' );
	$slug  = isset( $_POST['plugin'] ) ? sanitize_key( wp_unslash( $_POST['plugin'] ) ) : '';
	$slugs = 'all-required' === $slug ? array_keys( array_filter( $known ) ) : array( $slug );

	$done   = array();
	$failed = array();
	foreach ( $slugs as $item ) {
		if ( ! array_key_exists( $item, $known ) ) {
			continue; // Only plugins from the theme's own list.
		}
		$result = scentral_install_activate_plugin( $item );
		if ( is_wp_error( $result ) ) {
			$failed[] = $item . ': ' . $result->get_error_message();
		} else {
			$done[] = $item;
		}
	}
	set_transient(
		'scentral_plugins_result_' . get_current_user_id(),
		array(
			'done'   => $done,
			'failed' => $failed,
		),
		120
	);
	wp_safe_redirect( scentral_plugins_page_url() );
	exit;
}
add_action( 'admin_post_scentral_plugins', 'scentral_plugins_handle' );

/**
 * One table row.
 *
 * @param array $plugin Plugin entry.
 */
function scentral_plugins_row( $plugin ) {
	$status = scentral_plugin_status( $plugin['slug'] );
	$labels = array(
		'active'   => __( 'Active', 'scentral' ),
		'inactive' => __( 'Installed, not active', 'scentral' ),
		'missing'  => __( 'Not installed', 'scentral' ),
	);
	?>
	<tr>
		<td><strong><?php echo esc_html( $plugin['name'] ); ?></strong><br><span class="description"><?php echo esc_html( $plugin['why'] ); ?></span></td>
		<td><?php echo $plugin['required'] ? '<strong>' . esc_html__( 'Required', 'scentral' ) . '</strong>' : esc_html__( 'Recommended', 'scentral' ); ?></td>
		<td><span class="scentral-plugin-status scentral-plugin-status--<?php echo esc_attr( $status ); ?>"><?php echo esc_html( $labels[ $status ] ); ?></span></td>
		<td>
			<?php if ( 'active' !== $status ) : ?>
				<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
					<input type="hidden" name="action" value="scentral_plugins">
					<input type="hidden" name="plugin" value="<?php echo esc_attr( $plugin['slug'] ); ?>">
					<?php wp_nonce_field( 'scentral_plugins' ); ?>
					<button type="submit" class="button<?php echo $plugin['required'] ? ' button-primary' : ''; ?>"><?php echo 'missing' === $status ? esc_html__( 'Install & activate', 'scentral' ) : esc_html__( 'Activate', 'scentral' ); ?></button>
				</form>
			<?php else : ?>
				&#10003;
			<?php endif; ?>
		</td>
	</tr>
	<?php
}

/**
 * Appearance → Theme Plugins screen.
 */
function scentral_plugins_page() {
	$key     = 'scentral_plugins_result_' . get_current_user_id();
	$result  = get_transient( $key );
	$missing = scentral_missing_required_plugins();
	delete_transient( $key );
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Scentral Attraction — Theme Plugins', 'scentral' ); ?></h1>
		<p><?php esc_html_e( 'The theme is built for these plugins. Required plugins power the shop, payments, page editing and the contact form; recommended plugins add extra features. Plugins are installed from WordPress.org.', 'scentral' ); ?></p>

		<?php if ( is_array( $result ) && $result['done'] ) : ?>
			<div class="notice notice-success"><p><?php echo esc_html( sprintf( /* translators: %s: plugin slugs. */ __( 'Installed and activated: %s', 'scentral' ), implode( ', ', $result['done'] ) ) ); ?></p></div>
		<?php endif; ?>
		<?php if ( is_array( $result ) && $result['failed'] ) : ?>
			<div class="notice notice-error"><p><?php echo esc_html( implode( ' — ', $result['failed'] ) ); ?></p></div>
		<?php endif; ?>

		<?php if ( $missing ) : ?>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="margin:1.5em 0">
				<input type="hidden" name="action" value="scentral_plugins">
				<input type="hidden" name="plugin" value="all-required">
				<?php wp_nonce_field( 'scentral_plugins' ); ?>
				<button type="submit" class="button button-primary button-hero"><?php esc_html_e( 'Install & activate all required plugins', 'scentral' ); ?></button>
			</form>
		<?php else : ?>
			<div class="notice notice-success inline"><p><?php esc_html_e( 'All required plugins are active.', 'scentral' ); ?></p></div>
		<?php endif; ?>

		<table class="widefat striped" style="max-width:980px">
			<thead><tr><th><?php esc_html_e( 'Plugin', 'scentral' ); ?></th><th><?php esc_html_e( 'Type', 'scentral' ); ?></th><th><?php esc_html_e( 'Status', 'scentral' ); ?></th><th><?php esc_html_e( 'Action', 'scentral' ); ?></th></tr></thead>
			<tbody>
				<?php array_map( 'scentral_plugins_row', scentral_theme_plugins() ); ?>
			</tbody>
		</table>
		<p class="description" style="margin-top:1em"><?php esc_html_e( 'After installing Paystack, add your Paystack keys in WooCommerce → Settings → Payments. After installing Contact Form 7, set up its Mail and Mail (2) emails (see the installation guide).', 'scentral' ); ?></p>
	</div>
	<style>.scentral-plugin-status--active{color:#2f8f5b;font-weight:600}.scentral-plugin-status--missing{color:#b32d2e;font-weight:600}.scentral-plugin-status--inactive{color:#996800;font-weight:600}</style>
	<?php
}
