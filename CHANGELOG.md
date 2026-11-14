# Changelog

All notable changes to the Scentral Attraction theme are documented here.
The format follows [Keep a Changelog](https://keepachangelog.com/en/1.1.0/) and the project uses [Semantic Versioning](https://semver.org/).

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
