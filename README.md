# Scentral Attraction — WordPress Theme

A bespoke, WooCommerce-native WordPress theme for **[Scentral Attraction](https://scentralattraction.com/)**, Perfume & More, Abuja.

Editorial luxury fragrance design ("Noir & Rouge"), a branded preloader ("The First Spray"), lightweight motion, and full Customizer control. All product data comes from WooCommerce.

> **Status:** in active development. See [CHANGELOG.md](CHANGELOG.md).

---

## Repository layout

```
.
├── documentation/              Research, design direction, architecture and guides
├── wordpress-theme/
│   └── scentral-attraction/    The installable theme (this folder is what ships)
│       ├── assets/src/         Authoring CSS and JS
│       ├── assets/dist/        Compiled, minified assets (committed; no Node needed on the server)
│       ├── inc/                PHP modules (setup, enqueue, WooCommerce, Customizer, …)
│       ├── template-parts/     Header, footer, hero, sections, components
│       ├── woocommerce/        Minimal, update-safe WooCommerce template overrides
│       └── page-templates/     About, Contact, FAQ, Policy, Wishlist …
├── design-assets/              Logos, banners and campaign imagery (source files)
├── scripts/                    Build, image optimisation and packaging scripts
├── tests/                      Theme checks
└── releases/                   Installable ZIP: releases/scentral-attraction-theme.zip
```

## Documentation

| Document | Purpose |
|---|---|
| [Brand research](documentation/BRAND_RESEARCH.md) | Verified brand facts, logo verification, voice |
| [Existing website audit](documentation/EXISTING_WEBSITE_AUDIT.md) | Platform, integrations to preserve, issues found |
| [Site content inventory](documentation/SITE_CONTENT_INVENTORY.md) | Products, categories, real page copy, missing content |
| [Design direction](documentation/DESIGN_DIRECTION.md) | Concept, palette, typography, motion, art direction |
| [Theme architecture](documentation/THEME_ARCHITECTURE.md) | Code structure and decisions |
| [Installation guide](documentation/INSTALLATION_GUIDE.md) | Install, activate, configure, roll back |

## Quick start (development)

```bash
npm install          # build tools only
npm run build        # compile assets/src -> assets/dist
npm run package      # build and create releases/scentral-attraction-theme.zip
```

The theme itself is plain PHP, CSS and JavaScript. It does **not** need Node.js on the server.

## Install on WordPress

**Appearance → Themes → Add New Theme → Upload Theme**, then choose `releases/scentral-attraction-theme.zip`. Read the [installation guide](documentation/INSTALLATION_GUIDE.md) first: activate on staging before production.

## License

GPL-2.0-or-later. Bundled fonts (Cormorant Garamond, Manrope) are licensed under the SIL Open Font License 1.1.
