# Testing Report — v1.2.0

Date: 14 November 2026

Everything below was actually run. Items not tested are listed in section 6.

## 1. Test environment

| Component | Version |
|---|---|
| WordPress | 7.1.4 (local install, `WP_DEBUG` + `WP_DEBUG_LOG` on) |
| WooCommerce | 11.3.0 (Cart and Checkout **blocks**, NGN currency) |
| Elementor | 4.4.0 |
| Contact Form 7 | 6.2.5 |
| PHP / database | PHP 8.3 with OPcache, MariaDB 10.4 (XAMPP) |
| Browser | Google Chrome (headless) driven by playwright-core |

**Data:** the store's real catalogue snapshot (18 products, prices, stock status, Size attribute, descriptions, 3 categories, product photos) imported from the public Store API, plus the live About and Contact copy, a CF7 form, a test customer and one test order. The live site was never modified.

## 2. Automated checks (repeatable)

| Command | Result |
|---|---|
| `npm run lint:php` | ✅ 63/63 PHP files |
| `npm test` (12 static checks: functions, template parts, options, images, assets, bundle, forbidden mentions, WooCommerce guards) | ✅ 12/12 |
| `npm run test:e2e` (4 browser suites, see §4) | ✅ passing |
| Release ZIP: single `scentral-attraction/` root, forward-slash paths, no dev sources, CRC clean | ✅ |

## 3. Server-side results

- Theme activation: ✅ no errors. The page setup created the five draft pages and assigned templates to existing pages.
- **20 URL types**, each serving the right template with **zero PHP warnings or notices** from the theme: home, shop, 3 categories, product (in stock / sold out), About, Contact, FAQ, Shipping, Privacy, Terms, cart, checkout, My account, wishlist, order tracking, product search, general search, price filter, on-sale filter, sorting, pagination, 404.
- Price filter returns exactly the ₦50,000–₦80,000 products. On-sale shows the empty state.
- AJAX add-to-cart returns the theme's cart fragments (bag count, bag drawer, delivery bar).

## 4. Browser results

| Area | Verified | Result |
|---|---|---|
| Preloader | Plays once per browser tab on any page (fresh tab ✅, Ctrl+click new tab ✅), not again when navigating within the tab ✅, never in the Elementor editor | ✅ |
| Page transition | Doors close with the destination name, next page opens; bag drawer and anchors unaffected (Chrome 1440px) | ✅ |
| Order tracking | Form at 1440 and 390px; unknown order shows the error; order #97 + billing email shows "Processing" and order details | ✅ |
| Breadcrumbs | Pill trail on Contact, Order Tracking, Shop, FAQ, About | ✅ |
| Shop | 3 per row (4 with toggle), 2 on phones; Can't decide tile; Load more appends page 2 (18 items); no overflow at 1440/390px | ✅ |
| Product page | Arrow buttons + thumbnails (wraps around), AJAX Add to bag opens the bag (count 0 → 1), accordions, related rail arrows; no overflow at 1440/390px | ✅ |
| Quick view | Arrow buttons cycle through 3 images (1/3 → 2/3 → 1/3); summary and Add to bag | ✅ |
| Footer | Back to top before ©; bold wordmark fits at 1440 and 390px | ✅ |
| TI WooCommerce Wishlist | Plugin hearts on all cards and the product page; add → themed pop-up; wishlist page lists items with pill buttons; Wishlist page falls back to the plugin list when its shortcode is missing | ✅ |
| Mailchimp for WP | Form shortcode in Customize → Newsletter is used; with no Mailchimp API key the plugin prints nothing to visitors, so the theme falls back to its own form (section never empty) | ✅ (rendering with a real API key not tested) |
| Yoast SEO | One title, canonical and og:title (theme adds none); Yoast breadcrumbs shown in the pill style | ✅ |
| Theme Plugins page | Activation redirects to Appearance → Theme Plugins; "Install & activate all required" installed and activated Paystack; notice clears when all required are active | ✅ |
| Account & checkout (desktop + mobile) | Checkout fields and Place order; login/register forms; customer login; orders list | ✅ |
| Contact Form 7 settings from the guide | Applied to the local form and checked with CF7's own validator: Mail passes; Mail (2) to the customer is flagged until Turnstile or reCAPTCHA v3 is connected (now explained in the guide). Delivery itself not tested (no mail server locally) | ✅ / ⚠️ |
| Firefox 141 and WebKit 26 (Safari engine) | 11 pages at 1440px and 390px: no script errors, no sideways scroll, header/footer/h1 present; gallery arrows, AJAX Add to bag + bag drawer, quick view arrows (tests/e2e/cross-browser.mjs, run with SA_XBROWSER=1). Real Safari on iPhone/Mac not tested | ✅ |
| Preloader backstop | WebKit never ran animation frames in the test and the preloader stayed on, leaving the hero black; a timer now always ends the preloader (also covers pages opened in background tabs) | ✅ fixed |
| Lighthouse 13.6 (mobile, local dev server) | Accessibility 100 (home, shop, product, contact, order tracking); Best Practices 100; SEO 92 (only missing meta descriptions — add them in Yoast); Performance 63–76 on PHP's built-in single-threaded server (1.3–1.9 s server response), so expect better on real hosting | ✅ |
| Homepage vs reference | Hero pill buttons, collections mosaic (desktop 4-column, phone 2-column), rounded photos and numbers card; no overflow at 1440/390px; all e2e suites pass in Chrome, Firefox and WebKit | ✅ |
| Navbar | Announcement roll and collapse, hide on scroll, Shop mega panel by hover **and keyboard** on home and shop, burger and bag taps on phones | ✅ |
| Bag drawer | Empty state with 3 suggestions, quick add, quantity +/−, "Bag (n)", subtotal, Checkout | ✅ |
| Footer | Legal links beside ©, 4 payment logos, social gap, no emblem in wordmark, no stray drawers | ✅ |
| Cart / Checkout (blocks) | Render with theme typography at 1440 and 390px, email field and Place order present | ✅ * |
| My account | Login/register, dashboard, orders, order view, addresses, account details at 1440 and 390px | ✅ |
| Inner pages | About, Contact, FAQ, Shipping, Privacy, Wishlist, Order tracking, search, category, 404 | ✅ |
| Responsive | No horizontal overflow at 1440, 1024, 768, 390 and 360px on home, shop, product, About, Contact | ✅ |
| Images | AVIF served (desktop `hero-noir-oud.avif`, phone `hero-noir-oud-mobile.avif`), one hero download per device | ✅ |
| Elementor | Builder page renders full width; editor loads without JS errors; Scentral Section widget listed and previewed | ✅ |
| JS errors | None on any tested page | ✅ |

\* The checkout shows "no payment methods available" on the test site because no gateway is installed there. The live site's Paystack gateway renders in the same place unchanged.

## 5. Defects found by testing and fixed

| Defect | Impact | Fix |
|---|---|---|
| `search.php` reset the global `$posts` | Search results could vanish; 10 PHP warnings | Variables renamed |
| Shop mega menu matched the menu link by exact URL | Dropdown missing when the link is stored as `?page_id=` or with http/https differences | Shop item marked server-side from the shop page ID |
| Closed bag drawer forced to `display:flex` | White block after the footer, horizontal scroll | Drawer only displayed when open |
| Clip reveals clipped the observed element | Newsletter, intro, About and journal images never appeared | Clip moved to the child element |
| List reset out-ranked component margins | No gap before footer social icons | Zero-specificity reset |
| Mini-cart styles only loaded on shop pages | Unstyled bag on other pages | Bag styles moved to the main stylesheet |
| Account greeting took a grid cell | Orders table squeezed into the menu column | Explicit column placement |
| Product rows on tablets | First cards collapsed to 0px wide | Row ignores the generic grid's explicit columns |
| Hero list featured sold-out bottles | Weak merchandising | In-stock fragrances first |
| Headline split before punctuation | "meets scent ." | Punctuation attached to the previous word |
| Unescaped "&" in generated logo SVGs | Invalid XML in some apps | Escaped |

## 6. Not tested

- A real payment: Paystack test mode needs your Paystack test keys on a staging copy.
- Safari (iOS/macOS) and Firefox: only Chrome was automated here.
- Screen-reader walkthrough (keyboard access and focus were checked; a full NVDA/VoiceOver pass was not).
- Contact Form 7 email delivery (Mail and Mail (2)): the local test site has no mail server. Configure both and send a test message on the live site, following the steps in INSTALLATION_GUIDE.md.

## 7. How to re-run

```bash
npm run lint:php && npm test
SA_BASE_URL=https://staging.example.test SA_CUSTOMER_PASS=... SA_ADMIN_PASS=... SA_ELEMENTOR_PAGE_ID=123 npm run test:e2e
```
