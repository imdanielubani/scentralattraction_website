# Existing Website Audit — scentralattraction.com

Audit date: 14 November 2026. The audit was read-only: no account was created, nothing was added to a live cart and no forms were submitted.

## 1. Platform

| Layer | Finding |
|---|---|
| CMS | WordPress (REST API public) |
| Commerce | WooCommerce (Store API v1, REST v3) |
| Active theme | **Bacola** (grocery-store theme by KlbTheme) with a `bacola-child` child theme and the `bacola-core` plugin |
| Page builder | Elementor (homepage built with it) |
| Payment gateway | **Paystack** (only method returned by the Store API cart). The WooCommerce PayPal Payments plugin is installed, but PayPal is not offered at checkout. |
| Forms | Contact Form 7 (contact page) |
| Newsletter / marketing | Mailchimp for WP (`mc4wp`), Klaviyo |
| Wishlist | TI WooCommerce Wishlist (`[ti_wishlistsview]` shortcode on /wishlist/) |
| SEO | Yoast SEO |
| Other | Jetpack, Akismet, Google Site Kit |

### Integrations the new theme must preserve
1. **Paystack checkout.** The theme uses standard WooCommerce checkout templates and does not override `payment.php`, so gateway markup renders unchanged.
2. **Contact Form 7.** The Contact page template renders the page's own content, which contains the CF7 shortcode, and styles CF7 markup. It has a native fallback form if CF7 is ever removed.
3. **TI WooCommerce Wishlist.** The theme detects it and uses its buttons and page. A lightweight native wishlist (localStorage plus a page template) is used only when no wishlist plugin is active.
4. **Mailchimp for WP / Klaviyo.** The newsletter section accepts a shortcode (for example an MC4WP form) set in the Customizer. A native, nonce-protected fallback stores subscribers as a private list in WordPress.
5. **Yoast SEO.** The theme does not output its own meta, Open Graph or schema when Yoast (or Rank Math) is active, and uses Yoast breadcrumbs when available.
6. **Order Tracking** page (`[woocommerce_order_tracking]`): styled.

## 2. Pages found

| Page | URL | State |
|---|---|---|
| Home | `/` | Elementor layout (see §3) |
| Shop | `/shop/` | WooCommerce archive |
| About Us | `/about-us/` | Real brand copy, worth keeping (see SITE_CONTENT_INVENTORY.md) |
| Contact Us | `/contact-us/` | Real contact details plus CF7 form |
| Cart | `/cart/` | Cart |
| Checkout | `/checkout/` | Paystack |
| My Account | `/my-account/` | WooCommerce |
| Wishlist | `/wishlist/` | TI Wishlist |
| Order Tracking | `/order-tracking/` | WooCommerce shortcode |
| Privacy Policy | `/privacy-policy-2/` | **Unedited WordPress template text** that still references the previous theme vendor's demo URL |
| Terms & Conditions | linked as `#` | **Does not exist** |
| Shipping / Returns / FAQ | none | **Do not exist** |
| Blog | none | **No posts published** |

## 3. Homepage sections (current)

1. Top bar: About Us · My account · Wishlist · Order Tracking · "100% Secure delivery without contacting the courier" · "Need help? Call Us: +234807 234 4444".
2. Header: (broken) logo, cart total, menu Home / Shop / About Us / Contact Us.
3. Hero slider (Elementor):
   - "Long-lasting • Sophisticated / Luxury Perfume / Experience timeless elegance collection / Oud, Rose, & Lavender / Shop Now".
   - "50ML EDP / OUD CLASSIC / Where Elegance Meets Timelessness / Price ₦45,000".
   - "100ML EDP / Anfar London Floral Bloom / Unleash the Bloom of Elegance / Price ₦65,000".
4. Three promotional banner tiles ("Shop Now").
5. New Arrivals (4 Anfar London products).
6. Oud Perfumes (4 products).
7. Occidental Fragrance (7 products).
8. Footer: Useful links, Categories, contact block, "Download App on Mobile", payments image, copyright 2025, cookie notice.
9. Mobile bottom bar: Store / Search / Account.

## 4. Issues found

| # | Severity | Issue | Fix in new theme |
|---|---|---|---|
| 1 | High | **Header logo is broken.** Both `<img>` tags have an empty `src` and the referenced logo file is 404. | Logo from Customizer with an SVG fallback. It can never render empty. |
| 2 | High | **Hardcoded hero prices are stale.** The slider says Oud Classic ₦45,000 and Floral Bloom ₦65,000, but the store prices are ₦50,000 and ₦60,000. | Prices always come from WooCommerce. Hero product callouts pull a live product by ID. |
| 3 | High | **Privacy Policy is the default template** and references the theme vendor's demo URL. | Designed policy template. Content must be written and approved by the business (see SITE_CONTENT_INVENTORY.md). |
| 4 | Medium | Terms & Conditions link is `#`. Shipping, Returns and FAQ pages are missing. | Page templates provided. Pages are created as drafts with clearly marked fields for the business to complete. Nothing is invented. |
| 5 | Medium | One fake lorem-ipsum 4★ review is live on Smoky Wood. | Testimonials exclude admin reviews. The business should delete that review (recorded in the setup guide). |
| 6 | Medium | Smoky Wood's permalink slug is `sol-ti-coconut-charcoal-superade` (demo residue). | Recorded in the setup guide. Changing a slug needs a redirect, so it is left to the owner. |
| 7 | Medium | 5 Oud products (Pearl Oud, Oud Classic, Oud Lavender, Summer Oud, Oud & Roses) return **no category** in the public Store API, yet "Oud Perfume" reports 5 items. | Likely a term-cache or visibility inconsistency. Recorded in the setup guide. |
| 8 | Low | "Download App on Mobile" and "100% Secure delivery…" are demo-theme residue. | Removed. |
| 9 | Low | Vendor attribute misspelled "Scentralatrraction" on Oud & Roses. | Recorded in the setup guide. |
| 10 | Low | Grocery theme carries large unused CSS/JS. | New purpose-built theme with conditional asset loading. |
| 11 | Low | Product description text uses Title Case For Every Word. | The theme does not alter content. Recorded as a copy-editing recommendation. |

## 5. WooCommerce configuration (observed)

- Currency NGN (₦), prices shown with 2 decimals on archives (₦80,000.00).
- 18 published products, all **simple** products (no variations currently). The theme still fully supports variable products and size swatches.
- Stock management is **on**, with per-product quantities shown ("6 in stock").
- Out-of-stock products remain visible with a "Read more" button.
- Product attributes used: **Type** (Perfume), **Vendor**, **Size** (50ML/60ML/75ML/80ML/100ML EDP). The theme displays Size as the volume badge.
- Ratings enabled. Only the one placeholder review exists.

## 6. Must-preserve checklist
- [x] Paystack gateway (no payment template overrides).
- [x] URLs: `/shop/`, `/product/…`, `/product-category/…`, `/about-us/`, `/contact-us/`, `/wishlist/`, `/order-tracking/`, `/my-account/`, `/cart/`, `/checkout/`. The theme does not alter permalinks.
- [x] CF7 contact form.
- [x] TI Wishlist.
- [x] MC4WP / Klaviyo newsletter.
- [x] Yoast SEO output.
- [x] All product data stays in WooCommerce. The theme stores no product data.

## 7. Switching risk
Switching themes removes the previous theme's Elementor widgets from the homepage. The new theme supplies its own homepage (`front-page.php`) driven by Customizer settings. The existing Elementor page content is untouched and can be restored by reactivating the previous theme. **Activate on a staging copy first** (see INSTALLATION_GUIDE.md).
