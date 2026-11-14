# Theme Customization Guide

Everything here is done in **WordPress Admin → Appearance → Customize → Scentral Theme**. Changes preview live before you publish. No code editing is needed for ordinary content.

## Brand & Colours
| Setting | What it does |
|---|---|
| Wordmark tagline | Text under "Scentral Attraction" in the logo lockup (default "Perfume & More"). |
| Header logo layout | Emblem + wordmark, wordmark only, or emblem only (used when no custom logo is uploaded). |
| Accent / Dark / Background / Surface / Gold | The five brand colours. The whole site follows them. Keep the accent deep enough for white text on buttons. |
| Typography | Editorial (Cormorant Garamond + Manrope, self-hosted) or System fonts (fastest). |

**Uploading your own logo:** Customize → **Site Identity → Logo**. A transparent PNG or SVG about 320px wide works best. The site icon (favicon) is set in the same panel.

## Announcement Bar
- Show or hide the black bar above the header.
- **Messages**: one per line. They rotate every 4.5 seconds (and pause on hover).
- Optional link applied to every message.

## Header
- Transparent header over the homepage hero.
- Hide the header while scrolling down (it comes back when scrolling up).
- **Shop mega menu**: opens from the menu item that links to your Shop page. It lists your categories and a featured product (set a product ID, or leave 0 to show the newest in-stock fragrance).

## Homepage — Hero
Eyebrow, headline (wrap a word in `*asterisks*` to set it in rouge italics, e.g. `Where elegance meets *scent*.`), intro text, two buttons, a desktop image and an optional portrait mobile image.
**"In this edit" list**: newest, bestsellers, on sale, featured (starred in Products), or your chosen product IDs. Prices update automatically.

## Homepage — Sections
**Section order**: a comma-separated list. Reorder the words to reorder the page. Delete a word to hide that section.

| id | Section | Data source |
|---|---|---|
| `notes` | Moving ribbon of fragrance notes | "Notes ribbon" text |
| `categories` | Collection collage | Product categories (all with products, or the slugs you list) |
| `new_arrivals` | Newest products | WooCommerce |
| `showcase` | Scroll-zoom luxury showcase | Showcase settings + 3 products from its category |
| `bestsellers` | Most loved | WooCommerce sales count (hidden until the store has sales) |
| `collections` | Stacked full-screen collection panels | See below |
| `intro` | Brand philosophy | Intro quote, text and image |
| `numbers` | Live counts | Counted from WooCommerce |
| `promo` | Promotional banner | Enable under Collections & Banners |
| `testimonials` | Client words | Genuine approved product reviews only (hidden when there are none) |
| `social` | Instagram | Instagram URL + optional tiles (Media Library image IDs) |
| `newsletter` | Newsletter signup | Newsletter settings |
| `on_sale`, `featured` | Optional extra rails | WooCommerce |

To find an image ID: Media → click the image → the URL shows `item=123`.

## Homepage — Collections & Banners
- **Showcase**: heading, text, category slug (default `oud-perfume`), image.
- **Collections 1–3** (For Him / For Her / Shared): set a **category slug** for each. A panel appears only when that category has products. Until any are set, the panels show your existing collections instead.
- **Promo banner**: off by default. Turn it on and set the eyebrow, heading, text, button and image.

## Preloader & Motion
| Setting | Default |
|---|---|
| Enable the branded preloader | On |
| Show on | Homepage only (or every page) |
| Once per visit | On (the preloader plays once per browser session) |
| Rotating words | Oud, Rose, Lavender, Amber, Your signature |
| Caption under the bottle | Eau de Parfum · 50 — 100 ML |
| Scroll animations / parallax / page transitions | On |

The preloader never shows to visitors who ask their device for reduced motion. It is capped at about 4 seconds and disappears on its own even if a script fails.

## Contact & Social
Address, phone, email, hours, an optional map link and an optional WhatsApp number (international format, e.g. 2348072344444). Instagram, Facebook, TikTok, X and YouTube links appear wherever icons are shown. Empty links are hidden.

## Newsletter
Heading, text and image. To use **Mailchimp for WP** (already installed on the live site), paste its shortcode, e.g. `[mc4wp_form id="123"]`. If left blank, the theme's own form stores sign-ups under **Tools → Newsletter subscribers** (visible to shop managers only).

## Shop & Product Page
Products per row and per page, quick view on/off, a free-delivery threshold for the bag progress bar (0 hides it), delivery and returns text shown on product pages (blank hides them), and recently viewed on/off.

## Footer
Brand text, copyright name and the payments note ("Secure payments by Paystack").

## Menus
**Appearance → Menus → Manage Locations**: Primary, Utility (top bar), Footer — Shop, Footer — Help, Legal.

## Page templates
Edit a page → **Template**: About (Editorial), Contact, FAQ, Policy, Wishlist, Full width.
- **FAQ**: add each question as a **Details** block (the question is the summary line).
- **Policy**: use **Heading 2** for each section. A table of contents is built automatically.

## Fragrance profile (per product)
Products → edit a product → **Fragrance Profile** box: top, heart and base notes, scent family, longevity (1–5), sillage (1–5) and best worn. Only filled-in fields appear on the product page.
