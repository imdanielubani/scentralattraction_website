# Changelog

All notable changes to the Scentral Attraction theme are documented here.
The format follows [Keep a Changelog](https://keepachangelog.com/en/1.1.0/) and the project uses [Semantic Versioning](https://semver.org/).

## [1.3.0] — 2026-11-14

### Added
- Contact page redesigned after the reference: pill breadcrumb, large "Contact us." headline with intro, rounded form card (Contact Form 7 restyled, or the native form with topic pills and floating labels) and a dark "Other ways in" card.
- Google Map of the store (Beautiful Plaza, Dawaki, Abuja) with a details card, "Get directions" and "Call us"; location editable in the Customizer.
- Page-transition doors: black panels close with a red seam and the destination name, then open on the next page.
- "Back to top ↑" link in the footer bottom row (smooth scroll, focus returns to the top of the page).

### Changed
- Preloader now shows on every page, once per browser tab, so it plays whenever a page is opened in a new tab.
- The cross-document fade is replaced by the door transition.

## [1.2.0] — 2026-11-14

### Added
- Privacy Policy and Terms & Conditions links beside the footer copyright (shown once the pages are published).
- Visa, Mastercard, Verve and Paystack logos in the footer (inline SVG, toggle in the Customizer).
- Wordmark (dark and white) and horizontal lockup logo files with outlined lettering, plus PNG exports.
- AVIF campaign images with WebP fallback; art-directed, format-aware hero preload.
- Browser end-to-end test suite (`npm run test:e2e`), including Elementor editing.

### Fixed
- Shop mega menu attaches to the Shop item however the menu link is stored.
- Bag drawer rebuilt to the reference design; closed drawers no longer render after the footer.
- My account layout, order view and mobile orders list.
- Product rows on tablets (first cards collapsed to zero width).
- Clip-reveal images that never appeared; search results page emptying its own query; headline punctuation spacing.

## [1.1.0] — 2026-11-14

### Changed
- Palette switched to the logo's colours: white page, logo red #E3151C, black; all brown, beige, champagne and gold tones removed.
- Navbar rebuilt to match the reference design: fixed header, collapsing black announcement strip, floating rounded white bar with pill links, Search and black Bag pill, floating Shop mega panel with category image tiles.
- All ten campaign images regenerated in black, white and red.

### Added
- Elementor support: pages built with Elementor render full width; Elementor Pro Theme Builder header/footer locations; "Scentral Section" widget and `[scentral_section]` shortcode; homepage layout setting (theme homepage or Elementor-built Home page).
- Missing pages (FAQ, Shipping & Delivery, Returns & Refunds, Terms & Conditions, Journal, Wishlist) created as drafts on activation; Appearance → Scentral Setup screen with Edit / Edit with Elementor links.

## [1.0.0] — 2026-11-14

### Added
- Project scaffold: repository structure, documentation set, theme skeleton.
- Brand research, existing website audit, content inventory and design direction.
- Classic WordPress theme with WooCommerce support, Customizer registry (90+ options) and theme.json palette.
- "The First Spray" preloader, motion system (reveals, split text, parallax, scroll zoom, counters), view transitions.
- Header with announcement bar, mega menu, mobile menu, live search, cart drawer; curtain footer.
- Homepage: hero with live product index, notes ribbon, collection collage, product rails, showcase, collection panels, brand intro, live numbers, promo, genuine reviews, Instagram, newsletter.
- Shop: product card, archive with filters and load more, single product with fragrance profile, quick view, native wishlist, recently viewed.
- Pages: About, Contact (Contact Form 7 + native fallback), FAQ, Policy, Wishlist, Full width, journal, search, 404.
- Ten original campaign images with WebP desktop and mobile crops; vector emblem redraw.
- Static test suite, PHP lint, image pipeline and release packaging.

### Fixed
- Cards appended by "Load more" now become visible.
- Add-to-cart fallback keeps the product when WooCommerce needs to show a notice.
- Quick view variation forms work on product pages.
- Newsletter and contact forms refresh their nonce, so they keep working behind full-page caches.
