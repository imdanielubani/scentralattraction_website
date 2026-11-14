# Design Direction — White, Red & Black

## 1. Concept

**Scent is invisible; the website makes it visible.**

Scentral Attraction's own emblem is a perfume bottle drawn in a single red line, holding an "SA" monogram. The design system grows out of that mark:

- **The line**: a hairline drawn in brand rouge. It draws the bottle in the preloader, underlines links, divides editorial sections and marks the active state.
- **The noir**: deep, warm black evoking oud, leather and smoke, the heart of the catalogue.
- **The ivory**: warm paper surfaces that make supplier pack shots, all white backgrounds, sit naturally on the page as if printed on a fine catalogue.

The site should feel like walking into a quiet, well-lit fragrance boutique in Abuja: unhurried, confident, personal.

## 2. Studying the reference (what was taken, what was not)

The reference is a furniture-store site template. It was studied for **philosophy and motion**, not copied.

| Reference pattern | What we learned | Scentral reinterpretation |
|---|---|---|
| **Loader**: two "doors" each mirroring the face. A line drawing of a sofa draws itself with `stroke-dashoffset` tied to progress. A dimension rule grows. Words cycle (Sofas → Tables → Lighting → Your room). A giant 0–100% counter. On completion an accent seam grows, a dot pings and the doors part. Once per session, skipped for reduced motion, CSS failsafe at 6s. | Progress is *narrated*, not just shown. The drawing ties loading to the product. A single accent colour carries the climax. | **"The First Spray"**: the Scentral emblem bottle draws itself in rouge hairline. Juice rises inside it with progress. Fragrance notes cycle (Oud → Rose → Lavender → Amber → *Your signature*). A volume rule reads "EAU DE PARFUM · 50 — 100 ML". At 100% the **cap lifts**: a horizontal rouge seam opens at the bottle's neck, a mist of particles escapes, and the screen splits **top/bottom** (a cap lifting and a base dropping) to reveal the homepage. |
| Page transition "doors" | Continuity between pages | **Veil**: a native cross-document View Transition. Content fades and lifts 12px. No JavaScript, so it can never block navigation. |
| Hero: word-by-word headline reveal, "In this room — 04 pieces" index | Editorial voice plus shoppable index | Headline reveal of the brand tagline. An **"In this edit — 04 scents"** index that is a live product list from WooCommerce, with prices always current. |
| Numbered product rail ("01 —") | Catalogue rhythm | Numbered new-arrivals rail with volume badges (from the Size attribute). |
| Zoom: inset image scales to full-bleed on scroll | Cinematic pause | "The Oud Room": the luxury showcase. |
| Sheets / Deck: stacked sticky panels | Storytelling by layering | Men / Women / Unisex collection panels stacking as you scroll (each renders only if its category exists). |
| Numbers | Proof | **Live counts only**: fragrances in the collection, houses, categories, pulled from WooCommerce. No invented statistics. |
| Footer "curtain" reveal | A satisfying end | The footer is revealed beneath the page with the emblem drawn large in a faint line. |
| Monospace spec typography | Technical credibility | Replaced with tracked uppercase sans "spec labels" (`50 ML · EDP · OUD`). They are more perfumery, less furniture. |

Not taken: the reference's grid dimensions, copy, imagery, iconography, brand naming and colour tokens.

## 3. Colour — the logo's colours only

The palette is taken directly from the emblem: **red line art, black monogram, white ground**. No brown, beige, champagne or gold.

| Token | Hex | Use | Contrast (measured) |
|---|---|---|---|
| `--sa-white` | `#FFFFFF` | Page background, header bar | — |
| `--sa-surface` | `#F5F5F5` | Light grey behind product pack shots | — |
| `--sa-black` | `#0A0A0A` | Dark sections, footer, Bag button, announcement strip | — |
| `--sa-ink` | `#111111` | Body text | 18.9:1 on white |
| `--sa-red` | `#E3151C` | Logo red: emblem, buttons on hover, active dots, eyebrows, sale badges, focus rings | 4.78:1 on white · white on red 4.78:1 |
| `--sa-red-light` | `#FF5A5F` | Small red text on black only | 6.49:1 on black |
| `--sa-grey` | `#5C5C5C` | Muted text | 6.69:1 on white |

Rules: red is the single accent; large surfaces are white or black; campaign photography is shot in the same black/white/red palette.

## 4. Typography

| Role | Typeface | Weights | Why |
|---|---|---|---|
| Display / editorial serif | **Cormorant Garamond** | 300, 400, 500, plus 300/400 italic | High-contrast Garamond with a perfumer's elegance. Its italics carry note names ("*oud*, *rose*"). |
| Interface / body sans | **Manrope** | 400–600 (variable) | Clean, contemporary, excellent at small sizes and for price figures (tabular numerals). |

Both are SIL Open Font License fonts, **self-hosted** as WOFF2 (no third-party requests, no privacy leak, no render-blocking), with `font-display: swap`, preloading for the two critical faces and size-adjusted fallbacks to limit layout shift.

Type scale (fluid, 360 → 1440px):

| Step | Size | Font | Line height | Tracking |
|---|---|---|---|---|
| Display XL | clamp(3.25rem, 8vw, 8.5rem) | Cormorant 300 | 0.92 | -0.02em |
| Display | clamp(2.5rem, 5.5vw, 5.5rem) | Cormorant 300 | 1.0 | -0.015em |
| H2 | clamp(2rem, 3.6vw, 3.5rem) | Cormorant 400 | 1.05 | -0.01em |
| H3 | clamp(1.5rem, 2.2vw, 2rem) | Cormorant 400 | 1.15 | 0 |
| Body L | 1.125rem | Manrope 400 | 1.65 | 0 |
| Body | 1rem | Manrope 400 | 1.6 | 0 |
| Label | 0.6875–0.75rem | Manrope 600 uppercase | 1.3 | 0.18em |
| Price | 0.95rem | Manrope 500 tabular | 1.3 | 0.02em |

## 5. Layout and spacing

- Container max 1440px; side gutter `clamp(16px, 4vw, 56px)`.
- 12-column grid desktop, 6 tablet, 2 (product) / 1 (editorial) mobile.
- Spacing scale (rem): 0.25, 0.5, 0.75, 1, 1.5, 2, 3, 4, 6, 8, 12. Section rhythm is `clamp(4rem, 10vw, 9rem)`.
- Product cards: 4:5 image well on parchment, pack shot padded at 8%, with name (serif) and spec label above the price.
- Hairlines (1px `--sa-mist`) instead of boxes and shadows.

## 6. Motion principles

1. **Purposeful**: motion narrates (loading, revealing, confirming); it never decorates.
2. **One easing family**: `--ease-out: cubic-bezier(.22,1,.36,1)` for entrances; `--ease-inout: cubic-bezier(.76,0,.24,1)` for curtains and splits.
3. **Durations**: micro 160–240ms; reveals 700–900ms; preloader total ≤ 2.6s on a fast connection and hard-capped at 4.5s.
4. **Non-blocking**: content is in the HTML and visible without JavaScript. Animations only enhance (`.js` class gating).
5. **Reduced motion**: with `prefers-reduced-motion: reduce`, the preloader is skipped, reveals become instant, parallax and zoom are disabled, and the page transition becomes a simple fade.
6. **Lightweight**: no animation library. The motion system uses IntersectionObserver, CSS transitions, CSS scroll-driven animations with a requestAnimationFrame fallback, and native View Transitions, about 12 KB of JS in total. GSAP was considered and rejected: its features were not needed, and avoiding it saves around 70 KB.

### Animation inventory
| # | Animation | Technique |
|---|---|---|
| 1 | Preloader "The First Spray" | SVG stroke draw plus clip-path juice fill, split-screen exit |
| 2 | Logo entrance | Header emblem stroke draw after preloader |
| 3 | Hero image reveal | `clip-path: inset()` wipe plus 1.08 → 1 scale |
| 4 | Text reveal | Line/word masks (`overflow:hidden` spans) |
| 5 | Page transitions | Cross-document View Transitions |
| 6 | Section entrances | IntersectionObserver `[data-reveal]` |
| 7 | Product image hover | Crossfade to second gallery image plus 1.04 scale |
| 8 | Product cards | Staggered rise |
| 9 | Image parallax | `[data-parallax]` rAF transform, desktop only |
| 10 | Collection showcase | Sticky stacking panels plus scroll zoom |
| 11 | Navigation | Underline draw, mega-menu fade-slide, header hide/show on scroll |
| 12 | Cart feedback | Drawer slide-in, count bump, button "Added" state |
| 13 | Mobile menu | Full-screen panel with staggered links |
| 14 | Newsletter | Field underline focus, success state morph |
| 15 | Footer reveal | Curtain (sticky footer under page) |

## 7. Photography and campaign art direction

**Style:** still-life fragrance editorial. Dark warm noir or warm ivory/stone backdrops, raking side light, crisp specular highlights on glass, real reflections and soft contact shadows. Raw materials appear as props (oud wood, dried rose, lavender, amber resin, citrus peel, saffron threads, smoke). Generous negative space on one side for HTML text overlays.

**Rules:**
- **Unbranded bottles only.** No labels, no third-party logos, no recognisable designer bottle shapes.
- No text baked into images. All headlines are HTML overlays.
- Desktop crop 16:9 plus mobile crop 4:5, delivered as WebP, with meaningful filenames and alt text.

| Asset | Subject | Mood |
|---|---|---|
| `hero-noir-oud` | Unbranded amber-glass bottle on black stone, oud wood, a curl of smoke, rouge rim-light | Noir, rouge |
| `campaign-new-arrivals` | Three clear bottles on travertine plinths, morning light | Ivory, fresh |
| `campaign-bestsellers` | Bottle on silk with dried rose petals | Champagne |
| `collection-men` | Smoked-glass bottle, leather, vetiver, slate | Charcoal |
| `collection-women` | Blush bottle, peony, jasmine, satin | Ivory and rose |
| `collection-unisex` | Clear bottle, lavender, citrus, linen | Stone |
| `showcase-luxury` | Black-and-gold faceted bottle, gold leaf, amber resin, deep shadow | Noir, gold |
| `editorial-story` | A fragrance-ingredient still life | Warm |
| `newsletter` | Atomiser mist backlit, macro | Ivory |
| `promo` | Gift composition: ribbon, wrapped box, bottle | Rouge and ivory |

## 8. Components

- **Header**: announcement bar, then the main bar (menu left, centred emblem lockup, actions right). Transparent on the hero, ivory once scrolled, hides on scroll-down and shows on scroll-up.
- **Mega menu (Shop)**: category list with counts, collections column, and one live "featured" product card.
- **Product card**: image well, wishlist heart, volume badge, sale or sold-out badge, quick view, add to cart (AJAX).
- **Mini-cart drawer**: right-side dialog with focus trap, live WooCommerce fragments and a free-delivery progress bar (only if a threshold is configured in the Customizer).
- **Search overlay**: full-screen dialog with live product suggestions through the WooCommerce Store API.
- **Breadcrumbs**: Yoast when active, otherwise WooCommerce breadcrumbs.
- **Buttons**: primary (noir fill → rouge on hover), outline, and a text link with a drawing underline.

## 9. Accessibility targets
WCAG 2.2 AA: all text meets 4.5:1. Visible 2px rouge focus rings with offset. Targets are at least 44 × 44px. Dialogs use `role="dialog"`, `aria-modal`, a focus trap, Esc to close and return focus. Skip link. Correct heading order (one `h1` per page).
