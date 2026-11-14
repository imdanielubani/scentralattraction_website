# Changelog

All notable changes to the Scentral Attraction theme are documented here.
The format follows [Keep a Changelog](https://keepachangelog.com/en/1.1.0/) and the project uses [Semantic Versioning](https://semver.org/).

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
