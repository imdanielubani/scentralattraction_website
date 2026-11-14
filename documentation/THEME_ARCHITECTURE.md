# Theme Architecture

## 1. Principles

1. **A genuine classic WordPress theme.** PHP templates follow the WordPress template hierarchy, with hooks and filters. There is no headless front end, and no Node.js is needed at runtime.
2. **WooCommerce-native.** Products, prices, stock, variations, reviews, cart, checkout and accounts all come from WooCommerce. No product is hardcoded. Template overrides are kept to a minimum. Layout changes are made with hooks wherever possible, so WooCommerce updates do not break the theme.
3. **Content is owned by WordPress, not the theme.** Pages keep their editor content. Homepage sections are configured in the Customizer. Fragrance notes live in product meta (removable without data loss). Switching theme never loses store data.
4. **Integrations are preserved.** Paystack, Contact Form 7, TI Wishlist, Mailchimp for WP / Klaviyo and Yoast all work unchanged. The theme detects and defers to them.
5. **Progressive enhancement.** Every page is complete without JavaScript. Motion is additive and respects `prefers-reduced-motion`.

## 2. Directory map

```
scentral-attraction/
├── style.css                 Theme header only
├── functions.php             Bootstrap: constants and module loader
├── theme.json                Editor palette, typography and layout (block editor parity)
├── index.php                 Fallback template
├── header.php / footer.php   Document shell
├── front-page.php            Homepage: ordered section loader (Customizer driven)
├── page.php / single.php / archive.php / home.php / search.php / 404.php / comments.php
├── inc/
│   ├── setup.php             Theme supports, menus, image sizes, content width
│   ├── security.php          Escaping helpers, nonce utilities, header hardening
│   ├── performance.php       Preloads, resource hints, emoji and embed cleanup, lazy defaults
│   ├── enqueue.php           Conditional asset loading (dist/ manifest, versioned)
│   ├── template-functions.php Template tags: icons, emblem, breadcrumbs, product queries
│   ├── customizer.php        Brand, header, announcement, homepage, preloader, motion, contact, social, newsletter, footer
│   ├── woocommerce.php       Support declarations, hooks, AJAX fragments, archive and single layout
│   ├── product-meta.php      Fragrance notes meta box (top/heart/base, family, longevity, sillage)
│   ├── wishlist.php          TI Wishlist bridge plus native fallback
│   ├── newsletter.php        Shortcode bridge plus native nonce-protected fallback
│   ├── ajax.php              Quick view, live search (rate-limited, nonce-checked)
│   └── seo.php               Fallback meta and Open Graph only when no SEO plugin is active
├── template-parts/
│   ├── header/               announcement, site-header, mega-menu, mobile-menu, search-overlay, cart-drawer
│   ├── footer/               site-footer
│   ├── hero/                 hero
│   ├── sections/             one file per homepage section
│   └── components/           preloader, section-heading, emblem, product-index, post-card …
├── woocommerce/              Minimal overrides (archive-product, content-product, content-single-product)
├── page-templates/           About, Contact, FAQ, Policy, Wishlist, Full width
├── assets/
│   ├── src/css/              tokens, base, layout, components, sections, woocommerce, preloader
│   ├── src/js/               main, preloader, motion, header, cart, search, quick-view, wishlist
│   ├── dist/                 Compiled, minified output (committed)
│   ├── fonts/                Self-hosted WOFF2 (OFL)
│   ├── images/               Campaign art (WebP) and the emblem
│   └── icons/                SVG sprite
└── languages/                scentral.pot
```

## 3. Request flow

```
WordPress → functions.php → inc/* modules register hooks
  └─ template hierarchy picks a template (front-page.php, archive-product.php …)
       └─ get_header() → header.php → template-parts/header/*
       └─ template-parts/sections/* (data from WooCommerce queries and Customizer)
       └─ get_footer() → footer.php → template-parts/footer/* → wp_footer (scripts)
```

## 4. Homepage

`front-page.php` renders an **ordered list of sections** from the Customizer (`scentral_home_sections`). Each section:
- reads its own settings (heading, category, image, product source),
- queries WooCommerce through `wc_get_products()` (cached in a transient that is flushed on product save),
- **returns nothing** when it has no data, so the page never shows empty shells.

Product sources: `newest`, `bestsellers` (`total_sales`), `on_sale`, `featured`, `category:<slug>`, `ids`.

## 5. Assets

- One small critical script (preloader gate) is printed inline in `<head>` so the preloader can show before first paint without a flash. It is under 1 KB.
- `main.css` / `main.js` load sitewide (deferred JS). `woocommerce.css` loads only on WooCommerce pages and the homepage. The cart and quick view scripts load only where needed.
- Fonts are preloaded for 2 files (Cormorant 300 and Manrope variable).
- WooCommerce's default stylesheets are dequeued and replaced by the theme's own styles. WooCommerce's block styles stay available for any block-based cart and checkout.
- Build: `scripts/build.mjs` (esbuild) bundles and minifies `src` into `dist`. Files are versioned by content hash in `dist/manifest.json`.

## 6. Security

- All output is escaped (`esc_html`, `esc_attr`, `esc_url`, `wp_kses_post`).
- All input is sanitised with Customizer `sanitize_callback`s and `sanitize_text_field` / `sanitize_email` / `absint`.
- AJAX endpoints use `check_ajax_referer` plus capability checks where relevant, and a per-IP transient rate limit on public endpoints (newsletter, search).
- No direct database queries. WP_Query, `wc_get_products` and the options API are used throughout.
- Template files exit when accessed directly.

## 7. Extensibility

- Action hooks inside templates: `scentral_before_header`, `scentral_after_hero`, `scentral_home_section_{id}`, `scentral_before_footer`.
- Filters: `scentral_home_sections`, `scentral_product_card_classes`, `scentral_preloader_words`, `scentral_announcements`.
- Child themes are supported. Every template part goes through `get_template_part()`.
