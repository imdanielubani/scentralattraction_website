<?php
/**
 * Customizer: a single registry of theme options.
 *
 * Every option is declared once in scentral_option_registry() and read with scentral_option().
 *
 * @package Scentral
 */

defined( 'ABSPATH' ) || exit;

/**
 * Customizer sections (id => title).
 *
 * @return array
 */
function scentral_customizer_sections() {
	return array(
		'scentral_brand'        => __( 'Brand & Colours', 'scentral' ),
		'scentral_announcement' => __( 'Announcement Bar', 'scentral' ),
		'scentral_header'       => __( 'Header', 'scentral' ),
		'scentral_hero'         => __( 'Homepage — Hero', 'scentral' ),
		'scentral_home'         => __( 'Homepage — Sections', 'scentral' ),
		'scentral_collections'  => __( 'Homepage — Collections & Banners', 'scentral' ),
		'scentral_preloader'    => __( 'Preloader & Motion', 'scentral' ),
		'scentral_contact'      => __( 'Contact & Social', 'scentral' ),
		'scentral_newsletter'   => __( 'Newsletter', 'scentral' ),
		'scentral_shop'         => __( 'Shop & Product Page', 'scentral' ),
		'scentral_footer'       => __( 'Footer', 'scentral' ),
	);
}

/**
 * Option registry: key => [section, type, default, label, (choices), (description)].
 *
 * @return array
 */
function scentral_option_registry() {
	$yes_no = 'checkbox';

	return array(
		// Brand & colours.
		'brand_tagline'        => array( 'scentral_brand', 'text', __( 'Perfume & More', 'scentral' ), __( 'Wordmark tagline', 'scentral' ) ),
		'brand_logo_layout'    => array( 'scentral_brand', 'select', 'emblem_wordmark', __( 'Header logo layout (when no custom logo is uploaded)', 'scentral' ), array( 'emblem_wordmark' => __( 'Emblem + wordmark', 'scentral' ), 'wordmark' => __( 'Wordmark only', 'scentral' ), 'emblem' => __( 'Emblem only', 'scentral' ) ) ),
		'color_accent'         => array( 'scentral_brand', 'color', '#E3151C', __( 'Accent (logo red)', 'scentral' ) ),
		'color_dark'           => array( 'scentral_brand', 'color', '#0A0A0A', __( 'Dark (black)', 'scentral' ) ),
		'color_background'     => array( 'scentral_brand', 'color', '#FFFFFF', __( 'Page background (white)', 'scentral' ) ),
		'color_surface'        => array( 'scentral_brand', 'color', '#F5F5F5', __( 'Surface (light grey behind product photos)', 'scentral' ) ),
		'font_pairing'         => array( 'scentral_brand', 'select', 'editorial', __( 'Typography', 'scentral' ), array( 'editorial' => __( 'Editorial — Cormorant Garamond + Manrope', 'scentral' ), 'system' => __( 'System fonts (fastest)', 'scentral' ) ) ),

		// Announcement bar.
		'announcement_enabled' => array( 'scentral_announcement', $yes_no, true, __( 'Show announcement bar', 'scentral' ) ),
		'announcement_lines'   => array( 'scentral_announcement', 'textarea', "Long-lasting fragrance in every drop.\nVisit us at Beautiful Plaza, Dawaki, Abuja.\nNeed help? Call +234 807 234 4444.", __( 'Messages (one per line, rotate automatically)', 'scentral' ) ),
		'announcement_link'    => array( 'scentral_announcement', 'url', '', __( 'Optional link for the bar', 'scentral' ) ),

		// Header.
		'header_transparent'   => array( 'scentral_header', $yes_no, true, __( 'Transparent header over the homepage hero', 'scentral' ) ),
		'header_hide_on_scroll' => array( 'scentral_header', $yes_no, true, __( 'Hide header when scrolling down', 'scentral' ) ),
		'header_mega_menu'     => array( 'scentral_header', $yes_no, true, __( 'Show the Shop mega menu (categories + featured product)', 'scentral' ) ),
		'header_featured_product' => array( 'scentral_header', 'number', 0, __( 'Mega menu featured product ID (0 = newest in stock)', 'scentral' ) ),

		// Hero.
		'hero_eyebrow'         => array( 'scentral_hero', 'text', __( 'Long-lasting • Sophisticated', 'scentral' ), __( 'Eyebrow', 'scentral' ) ),
		'hero_title'           => array( 'scentral_hero', 'textarea', __( 'Where elegance meets scent.', 'scentral' ), __( 'Headline (wrap a word in *asterisks* for italics)', 'scentral' ) ),
		'hero_text'            => array( 'scentral_hero', 'textarea', __( 'Oud, rose & lavender — a carefully selected collection of long-lasting Eau de Parfum.', 'scentral' ), __( 'Intro text', 'scentral' ) ),
		'hero_cta_label'       => array( 'scentral_hero', 'text', __( 'Shop the collection', 'scentral' ), __( 'Primary button label', 'scentral' ) ),
		'hero_cta_url'         => array( 'scentral_hero', 'url', '', __( 'Primary button link (blank = Shop)', 'scentral' ) ),
		'hero_cta2_label'      => array( 'scentral_hero', 'text', __( 'Our story', 'scentral' ), __( 'Secondary link label', 'scentral' ) ),
		'hero_cta2_url'        => array( 'scentral_hero', 'url', '', __( 'Secondary link (blank = About page)', 'scentral' ) ),
		'hero_image'           => array( 'scentral_hero', 'image', 0, __( 'Hero image (desktop, landscape)', 'scentral' ) ),
		'hero_image_mobile'    => array( 'scentral_hero', 'image', 0, __( 'Hero image (mobile, portrait)', 'scentral' ) ),
		'hero_index_source'    => array( 'scentral_hero', 'select', 'newest', __( '"In this edit" product list', 'scentral' ), scentral_product_source_choices() ),
		'hero_index_ids'       => array( 'scentral_hero', 'text', '', __( 'Product IDs (when source = chosen products), comma separated', 'scentral' ) ),

		// Homepage sections.
		'home_layout'          => array( 'scentral_home', 'select', 'theme', __( 'Homepage layout', 'scentral' ), array( 'theme' => __( 'Theme homepage (sections below)', 'scentral' ), 'page' => __( 'My Home page content (Elementor / editor)', 'scentral' ) ), __( 'Choose "My Home page content" to show the Home page exactly as built in Elementor or the block editor.', 'scentral' ) ),
		'home_sections'        => array( 'scentral_home', 'text', 'notes,categories,new_arrivals,showcase,bestsellers,collections,intro,numbers,promo,testimonials,social,newsletter', __( 'Section order (comma separated; remove an id to hide it)', 'scentral' ), null, __( 'Available: notes, categories, new_arrivals, showcase, bestsellers, collections, intro, numbers, promo, testimonials, social, newsletter, on_sale, featured', 'scentral' ) ),
		'home_notes'           => array( 'scentral_home', 'text', 'Oud, Rose, Lavender, Amber, Musk, Saffron, Leather, Jasmine', __( 'Notes ribbon (comma separated)', 'scentral' ) ),
		'home_categories'      => array( 'scentral_home', 'text', '', __( 'Categories to feature (slugs, comma separated; blank = all with products)', 'scentral' ) ),
		'home_new_title'       => array( 'scentral_home', 'text', __( 'New Arrivals', 'scentral' ), __( 'New arrivals heading', 'scentral' ) ),
		'home_new_text'        => array( 'scentral_home', 'text', __( 'Long-lasting fragrance in every drop.', 'scentral' ), __( 'New arrivals intro', 'scentral' ) ),
		'home_best_title'      => array( 'scentral_home', 'text', __( 'Most loved', 'scentral' ), __( 'Bestsellers heading', 'scentral' ) ),
		'home_products_count'  => array( 'scentral_home', 'number', 8, __( 'Products per homepage rail', 'scentral' ) ),
		'home_intro_title'     => array( 'scentral_home', 'textarea', __( 'Fragrance is more than a scent — it is an *experience*, a memory, and a statement of style.', 'scentral' ), __( 'Brand introduction quote', 'scentral' ) ),
		'home_intro_text'      => array( 'scentral_home', 'textarea', __( 'From the deep, captivating aroma of oud to the delicate sweetness of florals, our collection is carefully selected to cater to diverse tastes and personalities. Each bottle tells a story — one that becomes uniquely yours the moment you wear it.', 'scentral' ), __( 'Brand introduction text', 'scentral' ) ),
		'home_intro_image'     => array( 'scentral_home', 'image', 0, __( 'Brand introduction image', 'scentral' ) ),
		'home_testimonial_min' => array( 'scentral_home', 'number', 4, __( 'Minimum star rating for featured reviews', 'scentral' ) ),
		'home_social_title'    => array( 'scentral_home', 'text', __( 'Follow the scent', 'scentral' ), __( 'Social section heading', 'scentral' ) ),
		'home_social_images'   => array( 'scentral_home', 'text', '', __( 'Social tiles: media library image IDs (comma separated, up to 6)', 'scentral' ) ),

		// Collections & banners.
		'showcase_title'       => array( 'scentral_collections', 'textarea', __( 'The *Oud* Room', 'scentral' ), __( 'Showcase heading', 'scentral' ) ),
		'showcase_text'        => array( 'scentral_collections', 'textarea', __( 'Deep, resinous and quietly powerful — our oud selection is made for evenings that linger.', 'scentral' ), __( 'Showcase text', 'scentral' ) ),
		'showcase_category'    => array( 'scentral_collections', 'text', 'oud-perfume', __( 'Showcase category slug', 'scentral' ) ),
		'showcase_image'       => array( 'scentral_collections', 'image', 0, __( 'Showcase image', 'scentral' ) ),
		'collection_1_category' => array( 'scentral_collections', 'text', '', __( 'Collection 1 — category slug (e.g. men)', 'scentral' ) ),
		'collection_1_title'   => array( 'scentral_collections', 'text', __( 'For Him', 'scentral' ), __( 'Collection 1 — title', 'scentral' ) ),
		'collection_1_image'   => array( 'scentral_collections', 'image', 0, __( 'Collection 1 — image', 'scentral' ) ),
		'collection_2_category' => array( 'scentral_collections', 'text', '', __( 'Collection 2 — category slug (e.g. women)', 'scentral' ) ),
		'collection_2_title'   => array( 'scentral_collections', 'text', __( 'For Her', 'scentral' ), __( 'Collection 2 — title', 'scentral' ) ),
		'collection_2_image'   => array( 'scentral_collections', 'image', 0, __( 'Collection 2 — image', 'scentral' ) ),
		'collection_3_category' => array( 'scentral_collections', 'text', '', __( 'Collection 3 — category slug (e.g. unisex)', 'scentral' ) ),
		'collection_3_title'   => array( 'scentral_collections', 'text', __( 'Shared', 'scentral' ), __( 'Collection 3 — title', 'scentral' ) ),
		'collection_3_image'   => array( 'scentral_collections', 'image', 0, __( 'Collection 3 — image', 'scentral' ) ),
		'promo_enabled'        => array( 'scentral_collections', $yes_no, false, __( 'Show promotional banner', 'scentral' ) ),
		'promo_eyebrow'        => array( 'scentral_collections', 'text', '', __( 'Promo eyebrow', 'scentral' ) ),
		'promo_title'          => array( 'scentral_collections', 'text', __( 'The art of gifting', 'scentral' ), __( 'Promo heading', 'scentral' ) ),
		'promo_text'           => array( 'scentral_collections', 'textarea', __( 'A signature scent, a thoughtful gift, or something new to elevate the everyday.', 'scentral' ), __( 'Promo text', 'scentral' ) ),
		'promo_cta_label'      => array( 'scentral_collections', 'text', __( 'Find a gift', 'scentral' ), __( 'Promo button label', 'scentral' ) ),
		'promo_url'            => array( 'scentral_collections', 'url', '', __( 'Promo link (blank = Shop)', 'scentral' ) ),
		'promo_image'          => array( 'scentral_collections', 'image', 0, __( 'Promo image', 'scentral' ) ),

		// Preloader & motion.
		'preloader_enabled'    => array( 'scentral_preloader', $yes_no, true, __( 'Enable the branded preloader', 'scentral' ) ),
		'preloader_scope'      => array( 'scentral_preloader', 'select', 'home', __( 'Show preloader on', 'scentral' ), array( 'home' => __( 'Homepage only', 'scentral' ), 'all' => __( 'Every page', 'scentral' ) ) ),
		'preloader_once'       => array( 'scentral_preloader', $yes_no, true, __( 'Only once per visit (session)', 'scentral' ) ),
		'preloader_words'      => array( 'scentral_preloader', 'text', 'Oud, Rose, Lavender, Amber, Your signature', __( 'Rotating words (comma separated)', 'scentral' ) ),
		'preloader_caption'    => array( 'scentral_preloader', 'text', __( 'Eau de Parfum · 50 — 100 ML', 'scentral' ), __( 'Caption under the bottle', 'scentral' ) ),
		'motion_enabled'       => array( 'scentral_preloader', $yes_no, true, __( 'Enable scroll animations', 'scentral' ) ),
		'motion_parallax'      => array( 'scentral_preloader', $yes_no, true, __( 'Enable image parallax (desktop)', 'scentral' ) ),
		'motion_page_transitions' => array( 'scentral_preloader', $yes_no, true, __( 'Enable page transitions (supported browsers)', 'scentral' ) ),

		// Contact & social.
		'contact_address'      => array( 'scentral_contact', 'text', 'Beautiful Plaza, Dawaki, Abuja', __( 'Address', 'scentral' ) ),
		'contact_phone'        => array( 'scentral_contact', 'text', '+234 807 234 4444', __( 'Phone', 'scentral' ) ),
		'contact_email'        => array( 'scentral_contact', 'email', 'hello@scentralattraction.com', __( 'Email', 'scentral' ) ),
		'contact_hours'        => array( 'scentral_contact', 'text', __( 'Working 8:00am – 6:00pm', 'scentral' ), __( 'Opening hours', 'scentral' ) ),
		'contact_map_url'      => array( 'scentral_contact', 'url', '', __( 'Map link (Google Maps URL, optional)', 'scentral' ) ),
		'contact_whatsapp'     => array( 'scentral_contact', 'text', '', __( 'WhatsApp number (international format, optional)', 'scentral' ) ),
		'social_instagram'     => array( 'scentral_contact', 'url', 'https://www.instagram.com/scentralattraction/', __( 'Instagram URL', 'scentral' ) ),
		'social_facebook'      => array( 'scentral_contact', 'url', 'https://web.facebook.com/profile.php?id=100064004737193', __( 'Facebook URL', 'scentral' ) ),
		'social_tiktok'        => array( 'scentral_contact', 'url', '', __( 'TikTok URL', 'scentral' ) ),
		'social_x'             => array( 'scentral_contact', 'url', '', __( 'X (Twitter) URL', 'scentral' ) ),
		'social_youtube'       => array( 'scentral_contact', 'url', '', __( 'YouTube URL', 'scentral' ) ),

		// Newsletter.
		'newsletter_title'     => array( 'scentral_newsletter', 'text', __( 'Join the inner circle', 'scentral' ), __( 'Heading', 'scentral' ) ),
		'newsletter_text'      => array( 'scentral_newsletter', 'textarea', __( 'New arrivals and restocks, delivered to your inbox first.', 'scentral' ), __( 'Text', 'scentral' ) ),
		'newsletter_shortcode' => array( 'scentral_newsletter', 'text', '', __( 'Form shortcode (e.g. [mc4wp_form id="123"]). Blank = built-in form.', 'scentral' ) ),
		'newsletter_image'     => array( 'scentral_newsletter', 'image', 0, __( 'Image', 'scentral' ) ),

		// Shop.
		'shop_columns'         => array( 'scentral_shop', 'select', '4', __( 'Products per row (desktop)', 'scentral' ), array( '3' => '3', '4' => '4' ) ),
		'shop_per_page'        => array( 'scentral_shop', 'number', 12, __( 'Products per page', 'scentral' ) ),
		'shop_quick_view'      => array( 'scentral_shop', $yes_no, true, __( 'Enable quick view', 'scentral' ) ),
		'shop_free_shipping_threshold' => array( 'scentral_shop', 'number', 0, __( 'Free delivery threshold for the cart progress bar (0 = hide)', 'scentral' ) ),
		'shop_delivery_note'   => array( 'scentral_shop', 'textarea', '', __( 'Delivery information on product pages (blank = hide)', 'scentral' ) ),
		'shop_returns_note'    => array( 'scentral_shop', 'textarea', '', __( 'Returns information on product pages (blank = hide)', 'scentral' ) ),
		'shop_recently_viewed' => array( 'scentral_shop', $yes_no, true, __( 'Show recently viewed products', 'scentral' ) ),

		// Footer.
		'footer_about'         => array( 'scentral_footer', 'textarea', __( 'Luxurious perfumes that inspire confidence, elegance, and individuality. Where elegance meets scent.', 'scentral' ), __( 'Footer brand text', 'scentral' ) ),
		'footer_copyright'     => array( 'scentral_footer', 'text', __( 'Scentral Attraction — Perfume & More', 'scentral' ), __( 'Copyright name', 'scentral' ) ),
		'footer_payments_note' => array( 'scentral_footer', 'text', __( 'Secure payments by Paystack', 'scentral' ), __( 'Payments note (blank = hide)', 'scentral' ) ),
		'footer_payment_logos' => array( 'scentral_footer', 'checkbox', true, __( 'Show accepted payment logos', 'scentral' ) ),
	);
}

/**
 * Product source choices for product lists.
 *
 * @return array
 */
function scentral_product_source_choices() {
	return array(
		'newest'      => __( 'Newest', 'scentral' ),
		'bestsellers' => __( 'Bestsellers', 'scentral' ),
		'on_sale'     => __( 'On sale', 'scentral' ),
		'featured'    => __( 'Featured (starred)', 'scentral' ),
		'ids'         => __( 'Chosen products', 'scentral' ),
	);
}

/**
 * Read a theme option with its registered default.
 *
 * @param string $key Option key.
 * @return mixed
 */
function scentral_option( $key ) {
	static $registry = null;
	if ( null === $registry ) {
		$registry = scentral_option_registry();
	}
	$default = isset( $registry[ $key ] ) ? $registry[ $key ][2] : '';
	return get_theme_mod( 'scentral_' . $key, $default );
}

/**
 * Pick a sanitize callback for a control type.
 *
 * @param string $type Control type.
 * @return callable|string
 */
function scentral_sanitizer_for( $type ) {
	$map = array(
		'checkbox' => 'scentral_sanitize_checkbox',
		'select'   => 'scentral_sanitize_select',
		'color'    => 'sanitize_hex_color',
		'url'      => 'esc_url_raw',
		'email'    => 'sanitize_email',
		'number'   => 'absint',
		'image'    => 'absint',
		'textarea' => 'sanitize_textarea_field',
	);
	return isset( $map[ $type ] ) ? $map[ $type ] : 'sanitize_text_field';
}

/**
 * Register Customizer panel, sections, settings and controls.
 *
 * @param WP_Customize_Manager $wp_customize Manager.
 */
function scentral_customize_register( $wp_customize ) {
	$wp_customize->add_panel(
		'scentral_panel',
		array(
			'title'    => __( 'Scentral Theme', 'scentral' ),
			'priority' => 30,
		)
	);

	foreach ( scentral_customizer_sections() as $id => $title ) {
		$wp_customize->add_section( $id, array( 'title' => $title, 'panel' => 'scentral_panel' ) );
	}

	foreach ( scentral_option_registry() as $key => $def ) {
		list( $section, $type, $default, $label ) = $def;
		$choices     = isset( $def[4] ) ? $def[4] : array();
		$description = isset( $def[5] ) ? $def[5] : '';
		$setting_id  = 'scentral_' . $key;

		$sanitizer = scentral_sanitizer_for( $type );
		if ( 'announcement_lines' === $key ) {
			$sanitizer = 'scentral_sanitize_lines';
		} elseif ( in_array( $key, array( 'hero_index_ids', 'home_social_images' ), true ) ) {
			$sanitizer = 'scentral_sanitize_id_list';
		} elseif ( 'newsletter_shortcode' === $key ) {
			$sanitizer = 'scentral_sanitize_shortcode';
		}

		$wp_customize->add_setting(
			$setting_id,
			array(
				'default'           => $default,
				'sanitize_callback' => $sanitizer,
				'transport'         => 'refresh',
			)
		);

		$args = array(
			'label'       => $label,
			'section'     => $section,
			'description' => $description,
		);

		if ( 'color' === $type ) {
			$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, $setting_id, $args ) );
		} elseif ( 'image' === $type ) {
			$args['mime_type'] = 'image';
			$wp_customize->add_control( new WP_Customize_Media_Control( $wp_customize, $setting_id, $args ) );
		} else {
			$args['type'] = $type;
			if ( $choices ) {
				$args['choices'] = $choices;
			}
			$wp_customize->add_control( $setting_id, $args );
		}
	}
}
add_action( 'customize_register', 'scentral_customize_register' );

/**
 * Print colour and font overrides as CSS custom properties.
 */
function scentral_customizer_css() {
	$vars = array(
		'--sa-red'     => scentral_option( 'color_accent' ),
		'--sa-black'      => scentral_option( 'color_dark' ),
		'--sa-white'     => scentral_option( 'color_background' ),
		'--sa-surface' => scentral_option( 'color_surface' ),
	);

	$css = ':root{';
	foreach ( $vars as $name => $value ) {
		$hex = sanitize_hex_color( $value );
		if ( $hex ) {
			$css .= $name . ':' . $hex . ';';
		}
	}
	if ( 'system' === scentral_option( 'font_pairing' ) ) {
		$css .= '--font-display:Georgia,"Times New Roman",serif;--font-body:system-ui,-apple-system,"Segoe UI",Roboto,sans-serif;';
	}
	$css .= '}';

	wp_add_inline_style( 'scentral-main', $css );
}
add_action( 'wp_enqueue_scripts', 'scentral_customizer_css', 20 );
