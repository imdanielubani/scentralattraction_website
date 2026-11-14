<?php
/**
 * Template helpers and tags.
 *
 * @package Scentral
 */

defined( 'ABSPATH' ) || exit;

/**
 * Convert *word* markers to <em> and escape everything else.
 *
 * @param string $text Raw text with optional *emphasis* markers.
 * @return string Safe HTML.
 */
function scentral_rich_title( $text ) {
	$safe = esc_html( (string) $text );
	return preg_replace( '/\*(.+?)\*/u', '<em>$1</em>', $safe );
}

/**
 * Split a headline into masked words for the reveal animation.
 *
 * @param string $text Raw text with optional *emphasis* markers.
 * @return string Safe HTML.
 */
function scentral_split_words( $text ) {
	$parts = preg_split( '/(\*.+?\*)/u', (string) $text, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY );
	$html  = '';
	$index = 0;

	foreach ( $parts as $part ) {
		$is_em = '*' === mb_substr( $part, 0, 1 ) && '*' === mb_substr( $part, -1 );
		$words = preg_split( '/\s+/u', trim( $is_em ? mb_substr( $part, 1, -1 ) : $part ), -1, PREG_SPLIT_NO_EMPTY );
		foreach ( $words as $word ) {
			$inner = $is_em ? '<em>' . esc_html( $word ) . '</em>' : esc_html( $word );
			$html .= sprintf( '<span class="w"><span class="w__i" style="--i:%d">%s</span></span> ', $index, $inner );
			++$index;
		}
	}
	return trim( $html );
}

/**
 * Output the brand emblem as inline SVG (vector redraw of the official mark).
 *
 * @param array $args { class, title, draw (bool: prepare paths for stroke animation) }.
 */
function scentral_emblem( $args = array() ) {
	$args = wp_parse_args(
		$args,
		array(
			'class' => 'emblem',
			'title' => '',
			'draw'  => false,
		)
	);
	$path_attr = $args['draw'] ? ' pathLength="1"' : '';
	$label     = $args['title'] ? 'role="img" aria-label="' . esc_attr( $args['title'] ) . '"' : 'aria-hidden="true" focusable="false"';
	?>
	<svg class="<?php echo esc_attr( $args['class'] ); ?>" viewBox="60 80 392 320" <?php echo $label; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built from esc_attr above. ?>>
		<g class="emblem__line" fill="none" stroke="currentColor" stroke-linejoin="miter" stroke-linecap="round">
			<path class="emblem__cap"<?php echo $path_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> stroke-width="3.2" d="M214 88H298L308 98V113L300 121H212L204 113V98Z"/>
			<path<?php echo $path_attr; // phpcs:ignore ?> stroke-width="1.8" d="M220 95H292L299 102V110L295 114H217L211 110V102Z"/>
			<path class="emblem__neck"<?php echo $path_attr; // phpcs:ignore ?> stroke-width="2.4" d="M230 121V151M282 121V151M230 129H282M230 136H282M230 143H282"/>
			<path class="emblem__body"<?php echo $path_attr; // phpcs:ignore ?> stroke-width="3.2" d="M199 151H313L328 166V378L313 393H199L184 378V166Z"/>
			<path<?php echo $path_attr; // phpcs:ignore ?> stroke-width="1.6" d="M206 159H306L320 173V371L306 385H206L192 371V173Z"/>
			<path<?php echo $path_attr; // phpcs:ignore ?> stroke-width="2" d="M268 175A68 68 0 0 1 314 228"/>
			<path<?php echo $path_attr; // phpcs:ignore ?> stroke-width="2" d="M244 369A68 68 0 0 1 198 316"/>
			<g class="emblem__flourish">
				<path<?php echo $path_attr; // phpcs:ignore ?> stroke-width="3.2" d="M94 280C140 294 188 334 210 384C174 362 130 340 94 280Z"/>
				<path<?php echo $path_attr; // phpcs:ignore ?> stroke-width="1.6" d="M101 286C140 318 178 348 206 380"/>
				<path<?php echo $path_attr; // phpcs:ignore ?> stroke-width="2.6" d="M206 383C182 354 148 340 124 351C106 360 108 384 128 385C143 386 147 366 135 362C127 359 121 368 127 372"/>
				<path<?php echo $path_attr; // phpcs:ignore ?> stroke-width="2.6" d="M121 350C101 337 77 341 70 357C66 369 78 379 88 373C96 368 92 357 84 359"/>
			</g>
			<g class="emblem__flourish" transform="translate(512 0) scale(-1 1)">
				<path<?php echo $path_attr; // phpcs:ignore ?> stroke-width="3.2" d="M94 280C140 294 188 334 210 384C174 362 130 340 94 280Z"/>
				<path<?php echo $path_attr; // phpcs:ignore ?> stroke-width="1.6" d="M101 286C140 318 178 348 206 380"/>
				<path<?php echo $path_attr; // phpcs:ignore ?> stroke-width="2.6" d="M206 383C182 354 148 340 124 351C106 360 108 384 128 385C143 386 147 366 135 362C127 359 121 368 127 372"/>
				<path<?php echo $path_attr; // phpcs:ignore ?> stroke-width="2.6" d="M121 350C101 337 77 341 70 357C66 369 78 379 88 373C96 368 92 357 84 359"/>
			</g>
		</g>
		<g class="emblem__monogram" fill="none" stroke="currentColor" stroke-linejoin="miter">
			<path<?php echo $path_attr; // phpcs:ignore ?> stroke-width="2.6" d="M231 290L257 198"/>
			<path<?php echo $path_attr; // phpcs:ignore ?> stroke-width="8" d="M257 198L284 290"/>
			<path<?php echo $path_attr; // phpcs:ignore ?> stroke-width="2.4" d="M223 290H240M276 290H294"/>
			<path<?php echo $path_attr; // phpcs:ignore ?> stroke-width="2.2" d="M242 254H274"/>
			<path<?php echo $path_attr; // phpcs:ignore ?> stroke-width="6.5" stroke-linecap="butt" d="M289 210C278 199 236 198 236 225C236 252 285 246 285 273C285 300 244 302 227 284"/>
			<path<?php echo $path_attr; // phpcs:ignore ?> stroke-width="2.4" d="M289 203V218M227 277V292"/>
		</g>
	</svg>
	<?php
}

/**
 * Site logo: custom logo when set, otherwise emblem + wordmark.
 *
 * @param string $context 'header' | 'footer'.
 */
function scentral_site_logo( $context = 'header' ) {
	$name = get_bloginfo( 'name' );

	if ( has_custom_logo() && 'header' === $context ) {
		echo '<div class="site-logo site-logo--custom">' . get_custom_logo() . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core output.
		return;
	}

	$layout = scentral_option( 'brand_logo_layout' );
	?>
	<a class="site-logo site-logo--<?php echo esc_attr( $layout ); ?>" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home" aria-label="<?php echo esc_attr( $name . ' — ' . __( 'Home', 'scentral' ) ); ?>">
		<?php if ( 'wordmark' !== $layout ) : ?>
			<?php scentral_emblem( array( 'class' => 'site-logo__emblem emblem' ) ); ?>
		<?php endif; ?>
		<?php if ( 'emblem' !== $layout ) : ?>
			<span class="site-logo__wordmark">
				<span class="site-logo__name"><?php esc_html_e( 'Scentral Attraction', 'scentral' ); ?></span>
				<span class="site-logo__tagline"><?php echo esc_html( scentral_option( 'brand_tagline' ) ); ?></span>
			</span>
		<?php endif; ?>
	</a>
	<?php
}

/**
 * Announcement messages from the Customizer.
 *
 * @return string[]
 */
function scentral_get_announcements() {
	$lines = preg_split( '/\r\n|\r|\n/', (string) scentral_option( 'announcement_lines' ) );
	$lines = array_values( array_filter( array_map( 'trim', $lines ), 'strlen' ) );
	return apply_filters( 'scentral_announcements', $lines );
}

/**
 * Bundled campaign image (assets/images/campaign/{name}.webp) with a mobile crop if present.
 *
 * @param string $name Base name without extension.
 * @return array{src:string,srcset:string,mobile:string,width:int,height:int}|array
 */
function scentral_campaign_image( $name ) {
	$dir  = '/assets/images/campaign/';
	$file = SCENTRAL_DIR . $dir . $name . '.webp';
	if ( ! file_exists( $file ) ) {
		return array();
	}
	$size   = wp_getimagesize( $file );
	$srcset = array();
	foreach ( array( 960, 1600 ) as $w ) {
		if ( file_exists( SCENTRAL_DIR . $dir . $name . '-' . $w . '.webp' ) ) {
			$srcset[] = SCENTRAL_URI . $dir . $name . '-' . $w . '.webp ' . $w . 'w';
		}
	}
	if ( $srcset && $size ) {
		$srcset[] = SCENTRAL_URI . $dir . $name . '.webp ' . $size[0] . 'w';
	}
	$mobile = file_exists( SCENTRAL_DIR . $dir . $name . '-mobile.webp' ) ? SCENTRAL_URI . $dir . $name . '-mobile.webp' : '';

	return array(
		'src'    => SCENTRAL_URI . $dir . $name . '.webp',
		'srcset' => implode( ', ', $srcset ),
		'mobile' => $mobile,
		'width'  => $size ? (int) $size[0] : 0,
		'height' => $size ? (int) $size[1] : 0,
	);
}

/**
 * Resolve an image: a Customizer attachment if set, otherwise a bundled campaign image.
 *
 * @param string $option   Option key holding an attachment ID.
 * @param string $fallback Campaign image base name.
 * @param string $size     Attachment size.
 * @return array{src:string,srcset:string,mobile:string,width:int,height:int,id:int}|array
 */
function scentral_resolve_image( $option, $fallback, $size = 'scentral-banner' ) {
	$id = $option ? absint( scentral_option( $option ) ) : 0;
	if ( $id ) {
		$img = wp_get_attachment_image_src( $id, $size );
		if ( $img ) {
			return array(
				'id'     => $id,
				'src'    => $img[0],
				'srcset' => (string) wp_get_attachment_image_srcset( $id, $size ),
				'mobile' => '',
				'width'  => (int) $img[1],
				'height' => (int) $img[2],
			);
		}
	}
	$campaign = scentral_campaign_image( $fallback );
	return $campaign ? array_merge( array( 'id' => 0 ), $campaign ) : array();
}

/**
 * Hero image data (desktop + mobile).
 *
 * @return array
 */
function scentral_hero_image() {
	$image = scentral_resolve_image( 'hero_image', 'hero-noir-oud', 'scentral-hero' );
	$mid   = absint( scentral_option( 'hero_image_mobile' ) );
	if ( $image && $mid ) {
		$m = wp_get_attachment_image_src( $mid, 'scentral-portrait' );
		if ( $m ) {
			$image['mobile'] = $m[0];
		}
	}
	return $image;
}

/**
 * Render a responsive <picture> for resolved image data.
 *
 * @param array  $image Image data from scentral_resolve_image().
 * @param string $alt   Alt text.
 * @param array  $args  { class, eager (bool), sizes }.
 */
function scentral_picture( $image, $alt, $args = array() ) {
	if ( empty( $image['src'] ) ) {
		return;
	}
	$args = wp_parse_args(
		$args,
		array(
			'class' => '',
			'eager' => false,
			'sizes' => '100vw',
		)
	);
	?>
	<picture class="<?php echo esc_attr( $args['class'] ); ?>">
		<?php if ( ! empty( $image['mobile'] ) ) : ?>
			<source media="(max-width: 767px)" srcset="<?php echo esc_url( $image['mobile'] ); ?>">
		<?php endif; ?>
		<img src="<?php echo esc_url( $image['src'] ); ?>"
			<?php if ( ! empty( $image['srcset'] ) ) : ?>
				srcset="<?php echo esc_attr( $image['srcset'] ); ?>" sizes="<?php echo esc_attr( $args['sizes'] ); ?>"
			<?php endif; ?>
			<?php if ( ! empty( $image['width'] ) ) : ?>
				width="<?php echo esc_attr( $image['width'] ); ?>" height="<?php echo esc_attr( $image['height'] ); ?>"
			<?php endif; ?>
			alt="<?php echo esc_attr( $alt ); ?>"
			<?php echo $args['eager'] ? 'fetchpriority="high" loading="eager"' : 'loading="lazy"'; ?>
			decoding="async">
	</picture>
	<?php
}

/**
 * Resolve a URL option with a fallback.
 *
 * @param string $option   Option key.
 * @param string $fallback Fallback URL.
 * @return string
 */
function scentral_url_option( $option, $fallback ) {
	$url = scentral_option( $option );
	return $url ? $url : $fallback;
}

/**
 * URL of a page by template, or by slug as fallback.
 *
 * @param string $slug Page slug.
 * @return string
 */
function scentral_page_url( $slug ) {
	$page = get_page_by_path( $slug );
	return $page ? get_permalink( $page ) : home_url( '/' . $slug . '/' );
}

/**
 * Shop URL with WooCommerce fallback.
 *
 * @return string
 */
function scentral_shop_url() {
	return function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' );
}

/**
 * Configured social links (only the ones filled in).
 *
 * @return array<string,array{url:string,label:string}>
 */
function scentral_social_links() {
	$networks = array(
		'instagram' => 'Instagram',
		'facebook'  => 'Facebook',
		'tiktok'    => 'TikTok',
		'x'         => 'X',
		'youtube'   => 'YouTube',
	);
	$links = array();
	foreach ( $networks as $key => $label ) {
		$url = scentral_option( 'social_' . $key );
		if ( $url ) {
			$links[ $key ] = array(
				'url'   => $url,
				'label' => $label,
			);
		}
	}
	return $links;
}

/**
 * Render social links list.
 *
 * @param string $class Wrapper class.
 */
function scentral_social_list( $class = 'social-list' ) {
	$links = scentral_social_links();
	if ( ! $links ) {
		return;
	}
	echo '<ul class="' . esc_attr( $class ) . '">';
	foreach ( $links as $key => $link ) {
		printf(
			'<li><a href="%1$s" target="_blank" rel="noopener noreferrer" aria-label="%2$s">%3$s</a></li>',
			esc_url( $link['url'] ),
			/* translators: %s: social network name. */
			esc_attr( sprintf( __( '%s (opens in a new tab)', 'scentral' ), $link['label'] ) ),
			scentral_icon( $key ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG.
		);
	}
	echo '</ul>';
}

/**
 * Telephone href from a display number.
 *
 * @param string $phone Display phone.
 * @return string
 */
function scentral_tel( $phone ) {
	return 'tel:' . preg_replace( '/[^0-9+]/', '', (string) $phone );
}

/**
 * Section heading component.
 *
 * @param array $args { eyebrow, title, text, link_url, link_label, tag, id, align }.
 */
function scentral_section_heading( $args ) {
	$args = wp_parse_args(
		$args,
		array(
			'eyebrow'    => '',
			'title'      => '',
			'text'       => '',
			'link_url'   => '',
			'link_label' => '',
			'tag'        => 'h2',
			'id'         => '',
			'align'      => 'split',
		)
	);
	$tag = in_array( $args['tag'], array( 'h1', 'h2', 'h3' ), true ) ? $args['tag'] : 'h2';
	?>
	<header class="section-heading section-heading--<?php echo esc_attr( $args['align'] ); ?>" data-reveal>
		<div class="section-heading__main">
			<?php if ( $args['eyebrow'] ) : ?>
				<p class="eyebrow"><?php echo esc_html( $args['eyebrow'] ); ?></p>
			<?php endif; ?>
			<<?php echo esc_html( $tag ); ?> class="section-heading__title"<?php echo $args['id'] ? ' id="' . esc_attr( $args['id'] ) . '"' : ''; ?>>
				<?php echo scentral_rich_title( $args['title'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in helper. ?>
			</<?php echo esc_html( $tag ); ?>>
		</div>
		<?php if ( $args['text'] || $args['link_url'] ) : ?>
			<div class="section-heading__aside">
				<?php if ( $args['text'] ) : ?>
					<p class="section-heading__text"><?php echo esc_html( $args['text'] ); ?></p>
				<?php endif; ?>
				<?php if ( $args['link_url'] ) : ?>
					<a class="link-arrow" href="<?php echo esc_url( $args['link_url'] ); ?>"><?php echo esc_html( $args['link_label'] ); ?><?php echo scentral_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
				<?php endif; ?>
			</div>
		<?php endif; ?>
	</header>
	<?php
}

/**
 * Breadcrumbs: Yoast / Rank Math when present, otherwise WooCommerce, otherwise a simple trail.
 */
function scentral_breadcrumbs() {
	if ( is_front_page() ) {
		return;
	}
	if ( function_exists( 'yoast_breadcrumb' ) && get_option( 'wpseo_titles' ) ) {
		$crumbs = yoast_breadcrumb( '<nav class="breadcrumbs" aria-label="' . esc_attr__( 'Breadcrumb', 'scentral' ) . '">', '</nav>', false );
		if ( $crumbs ) {
			echo $crumbs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- plugin output.
			return;
		}
	}
	if ( function_exists( 'rank_math_the_breadcrumbs' ) ) {
		rank_math_the_breadcrumbs();
		return;
	}
	if ( function_exists( 'woocommerce_breadcrumb' ) ) {
		woocommerce_breadcrumb(
			array(
				'wrap_before' => '<nav class="breadcrumbs" aria-label="' . esc_attr__( 'Breadcrumb', 'scentral' ) . '"><ol>',
				'wrap_after'  => '</ol></nav>',
				'before'      => '<li>',
				'after'       => '</li>',
				'delimiter'   => '',
			)
		);
	}
}

/**
 * Posted-on meta for journal posts.
 */
function scentral_post_meta() {
	$cats = get_the_category_list( ', ' );
	printf(
		'<p class="post-meta"><time datetime="%1$s">%2$s</time>%3$s<span>%4$s</span></p>',
		esc_attr( get_the_date( DATE_W3C ) ),
		esc_html( get_the_date() ),
		$cats ? '<span class="post-meta__sep" aria-hidden="true">·</span><span class="post-meta__cats">' . wp_kses_post( $cats ) . '</span>' : '',
		esc_html( scentral_reading_time() )
	);
}

/**
 * Estimated reading time.
 *
 * @return string
 */
function scentral_reading_time() {
	$words   = str_word_count( wp_strip_all_tags( (string) get_post_field( 'post_content', get_the_ID() ) ) );
	$minutes = max( 1, (int) ceil( $words / 220 ) );
	/* translators: %d: minutes. */
	return ' · ' . sprintf( _n( '%d min read', '%d min read', $minutes, 'scentral' ), $minutes );
}

/**
 * Primary menu fallback: Shop, About, Contact (pages that exist on the site).
 */
function scentral_primary_menu_fallback() {
	$items = array(
		scentral_shop_url()             => __( 'Shop', 'scentral' ),
		scentral_page_url( 'about-us' ) => __( 'About', 'scentral' ),
		scentral_page_url( 'contact-us' ) => __( 'Contact', 'scentral' ),
	);
	echo '<ul class="menu">';
	foreach ( $items as $url => $label ) {
		$classes = 'menu-item' . ( scentral_shop_url() === $url ? ' has-mega' : '' );
		printf( '<li class="%1$s"><a href="%2$s">%3$s</a></li>', esc_attr( $classes ), esc_url( $url ), esc_html( $label ) );
	}
	echo '</ul>';
}

/**
 * Inline SVG icon markup.
 *
 * @param string $name Icon name.
 * @return string
 */
function scentral_icon( $name ) {
	$icons = scentral_icon_paths();
	if ( ! isset( $icons[ $name ] ) ) {
		return '';
	}
	return '<svg class="icon icon--' . esc_attr( $name ) . '" viewBox="0 0 24 24" aria-hidden="true" focusable="false">' . $icons[ $name ] . '</svg>';
}

/**
 * Icon path library (stroke icons, 1.5px, 24px grid).
 *
 * @return array<string,string>
 */
function scentral_icon_paths() {
	$s = 'fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"';
	return array(
		'search'    => '<circle ' . $s . ' cx="11" cy="11" r="6.5"/><path ' . $s . ' d="M20 20l-4.2-4.2"/>',
		'bag'       => '<path ' . $s . ' d="M5 8h14l-1 12H6L5 8z"/><path ' . $s . ' d="M9 8V6.5a3 3 0 016 0V8"/>',
		'user'      => '<circle ' . $s . ' cx="12" cy="8.5" r="3.5"/><path ' . $s . ' d="M5 20c1.2-3.6 4-5 7-5s5.8 1.4 7 5"/>',
		'heart'     => '<path ' . $s . ' d="M12 20s-7-4.4-7-10a4 4 0 017-2.6A4 4 0 0119 10c0 5.6-7 10-7 10z"/>',
		'menu'      => '<path ' . $s . ' d="M3 8h18M3 16h12"/>',
		'close'     => '<path ' . $s . ' d="M6 6l12 12M18 6L6 18"/>',
		'arrow'     => '<path ' . $s . ' d="M4 12h15M14 7l5 5-5 5"/>',
		'arrow-left' => '<path ' . $s . ' d="M20 12H5M10 7l-5 5 5 5"/>',
		'plus'      => '<path ' . $s . ' d="M12 5v14M5 12h14"/>',
		'minus'     => '<path ' . $s . ' d="M5 12h14"/>',
		'eye'       => '<path ' . $s . ' d="M2.5 12S6 5.5 12 5.5 21.5 12 21.5 12 18 18.5 12 18.5 2.5 12 2.5 12z"/><circle ' . $s . ' cx="12" cy="12" r="2.8"/>',
		'filter'    => '<path ' . $s . ' d="M4 7h10M18 7h2M4 17h4M12 17h8"/><circle ' . $s . ' cx="16" cy="7" r="2"/><circle ' . $s . ' cx="10" cy="17" r="2"/>',
		'phone'     => '<path ' . $s . ' d="M5 4h4l2 5-2.5 1.5a11 11 0 005 5L15 13l5 2v4a1 1 0 01-1 1A16 16 0 014 5a1 1 0 011-1z"/>',
		'mail'      => '<rect ' . $s . ' x="3.5" y="5.5" width="17" height="13"/><path ' . $s . ' d="M4 6l8 7 8-7"/>',
		'pin'       => '<path ' . $s . ' d="M12 21s-6.5-6.2-6.5-11a6.5 6.5 0 0113 0c0 4.8-6.5 11-6.5 11z"/><circle ' . $s . ' cx="12" cy="10" r="2.3"/>',
		'clock'     => '<circle ' . $s . ' cx="12" cy="12" r="8.5"/><path ' . $s . ' d="M12 7.5V12l3 2"/>',
		'truck'     => '<path ' . $s . ' d="M3 6.5h11v9H3zM14 10h4l3 3v2.5h-7z"/><circle ' . $s . ' cx="7" cy="17.5" r="1.8"/><circle ' . $s . ' cx="17.5" cy="17.5" r="1.8"/>',
		'shield'    => '<path ' . $s . ' d="M12 3l7 3v5.5c0 4.5-3 8-7 9.5-4-1.5-7-5-7-9.5V6l7-3z"/><path ' . $s . ' d="M9 12l2 2 4-4"/>',
		'return'    => '<path ' . $s . ' d="M9 7L4.5 11.5 9 16"/><path ' . $s . ' d="M5 11.5h9.5a5 5 0 010 10H11"/>',
		'drop'      => '<path ' . $s . ' d="M12 3.5s6 6.7 6 11a6 6 0 01-12 0c0-4.3 6-11 6-11z"/>',
		'star'      => '<path fill="currentColor" d="M12 3.6l2.5 5.3 5.8.7-4.3 4 1.1 5.7L12 16.5l-5.1 2.8L8 13.6 3.7 9.6l5.8-.7z"/>',
		'instagram' => '<rect ' . $s . ' x="3.5" y="3.5" width="17" height="17" rx="4.5"/><circle ' . $s . ' cx="12" cy="12" r="4"/><circle fill="currentColor" cx="17.2" cy="6.8" r="1"/>',
		'facebook'  => '<path ' . $s . ' d="M14.5 21v-7.5h2.6l.4-3h-3V8.6c0-.9.3-1.6 1.6-1.6h1.6V4.3a21 21 0 00-2.4-.1c-2.4 0-4 1.4-4 4.1v2.2H8.6v3h2.7V21"/>',
		'tiktok'    => '<path ' . $s . ' d="M14 3.5v11.2a3.3 3.3 0 11-3.3-3.3M14 3.5c.4 2.4 2.2 4.2 4.8 4.4"/>',
		'x'         => '<path ' . $s . ' d="M4 4l16 16M20 4L4 20"/>',
		'youtube'   => '<rect ' . $s . ' x="2.5" y="5.5" width="19" height="13" rx="3.5"/><path fill="currentColor" d="M10 9.2v5.6l5-2.8z"/>',
		'whatsapp'  => '<path ' . $s . ' d="M4.5 19.5l1.2-3.6A8 8 0 1112 20a8 8 0 01-3.9-1z"/><path ' . $s . ' d="M9 9.5c.3 2 2.2 4.1 4.5 4.6l1-1.2 1.8.9c-.3 1.2-1.3 1.7-2.3 1.6-3-.4-5.8-3.1-6-6.1 0-1 .5-1.9 1.6-2.2l.9 1.7z"/>',
	);
}

/**
 * Legal links for the footer bottom row: Privacy Policy and Terms & Conditions.
 * A link appears only when its page is published, so visitors never land on a 404.
 *
 * @return array<int,array{url:string,label:string}>
 */
function scentral_legal_links() {
	$links = array();

	$privacy_id = (int) get_option( 'wp_page_for_privacy_policy' );
	if ( ! $privacy_id || 'publish' !== get_post_status( $privacy_id ) ) {
		$privacy    = scentral_find_page( 'privacy-policy' );
		$privacy_id = $privacy ? (int) $privacy->ID : 0;
	}
	if ( $privacy_id && 'publish' === get_post_status( $privacy_id ) ) {
		$links[] = array(
			'url'   => get_permalink( $privacy_id ),
			'label' => __( 'Privacy Policy', 'scentral' ),
		);
	}

	$terms_id = function_exists( 'wc_terms_and_conditions_page_id' ) ? (int) wc_terms_and_conditions_page_id() : 0;
	if ( ! $terms_id || 'publish' !== get_post_status( $terms_id ) ) {
		$terms    = scentral_find_page( 'terms-and-conditions' );
		$terms_id = $terms ? (int) $terms->ID : 0;
	}
	if ( $terms_id && 'publish' === get_post_status( $terms_id ) ) {
		$links[] = array(
			'url'   => get_permalink( $terms_id ),
			'label' => __( 'Terms & Conditions', 'scentral' ),
		);
	}

	return apply_filters( 'scentral_legal_links', $links );
}

/**
 * Accepted payment logos for the footer (implemented in the next release step).
 */
function scentral_payment_logos() {
}
