# Testing Report — v1.1.0

Date: 14 November 2026

This report separates what **has been verified** from what **still needs verifying in a running WordPress site**. Nothing below claims more than was actually run.

## 1. Verified (automated, repeatable)

Run with `npm run lint:php`, `npm test`, `npm run build` and `npm run package`.

| # | Check | Result |
|---|---|---|
| 1 | PHP syntax (`php -l`, PHP 8.3) on all 62 theme PHP files | ✅ 62/62 |
| 2 | Every `scentral_*` function called or hooked is defined | ✅ |
| 3 | Every `get_template_part()` / `wc_get_template_part()` target exists (incl. dynamic homepage sections) | ✅ |
| 4 | Every `scentral_option()` key is registered in the Customizer registry (88 used / 92 registered) | ✅ |
| 5 | Every referenced campaign image exists (9 + mobile crops) | ✅ |
| 6 | Required theme files and compiled assets exist; `style.css` header complete; `theme.json` valid | ✅ |
| 7 | Front-end bundle parses; no `console.log` in sources | ✅ |
| 8 | No forbidden tool/vendor mentions in theme files | ✅ |
| 9 | WooCommerce-only helpers are guarded in templates that render without WooCommerce | ✅ |
| 10 | Release ZIP: single `scentral-attraction/` root, forward-slash paths, no dev sources, CRC check clean (see release section) | ✅ |
| 11 | Asset budget: CSS 61 KB + 26 KB (shop pages) minified; JS 18 KB minified; hero image 57 KB WebP | ✅ within targets |

## 2. Code review

An independent review of all PHP and JavaScript checked fatal-error risks, PHP 7.4 compatibility, escaping and sanitisation, nonces, AJAX flows and cart fragments.

- No undefined functions, no PHP 8-only syntax, no unescaped output or unsanitised input. Nonces are present on every state-changing handler.
- 3 bugs found and **fixed**: invisible cards after "Load more", the add-to-cart fallback losing the product, and the quick-view variation script missing on product pages.
- 1 caching risk **fixed**: forms now fetch a fresh nonce before submitting, so they keep working behind full-page caches.
- Noted, not changed: rate limiting keys on `REMOTE_ADDR`. Behind a CDN/proxy, configure the server to pass the real client IP.

## 3. Not yet verified: requires a running WordPress + WooCommerce

The plan was a disposable local WordPress (XAMPP MariaDB/PHP + WP-CLI) seeded with the real 18-product catalogue from the public Store API. Downloading and running WP-CLI was **not permitted in this session**, so the following have **not** been exercised in a browser yet:

| Area | To verify |
|---|---|
| Install & activation | Upload ZIP, activate, no PHP notices with `WP_DEBUG` on |
| Homepage | Preloader timing and exit, hero reveal, every section with real data, sections hiding when empty |
| Navigation | New reference-style navbar: announcement collapse, hide on scroll, Shop panel (hover, keyboard, Esc), burger menu, Search and Bag pills, admin-bar offset |
| Elementor | Edit with Elementor on each page, full-width rendering, Scentral Section widget in the editor, Theme Builder header/footer (Pro), homepage layout switch |
| Page setup | Draft pages created on activation, Scentral Setup screen, existing pages untouched |
| Archives | Grid, chips, sorting, price and on-sale filters, load more, pagination without JS |
| Product page | Gallery/zoom/lightbox, simple and variable add to cart via AJAX, notices on stock errors, fragrance profile, tabs, reviews, related, recently viewed |
| Cart / checkout | Drawer and fragments, cart page updates and coupons, checkout with **Paystack test mode** |
| Account | Login, registration, orders, addresses, account details, password change |
| Forms | Contact Form 7 page, native contact fallback, newsletter (native and MC4WP shortcode) |
| Wishlist | TI Wishlist button and page; native wishlist when the plugin is off |
| Responsive | 320, 375, 768, 1024, 1440, 1920 px; no horizontal overflow |
| Accessibility | Keyboard-only pass, focus visibility, dialogs, reduced-motion, axe scan |
| Performance | Lighthouse on home, shop and product pages |
| Browsers | Chrome, Safari (iOS), Firefox |

## 4. How to run the runtime tests

1. Create a staging copy of the live site (host control panel), or allow the local test environment.
2. Install `releases/scentral-attraction-theme.zip` and activate it.
3. Set `define( 'WP_DEBUG', true ); define( 'WP_DEBUG_LOG', true );` on staging, then work through the table above.
4. Put Paystack in **test mode** before any checkout test. Never place real orders on production.
