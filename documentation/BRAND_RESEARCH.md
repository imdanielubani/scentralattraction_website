# Brand Research — Scentral Attraction

Research date: 14 November 2026
Sources: the live website (scentralattraction.com, including its public WordPress and WooCommerce Store REST endpoints), the official Instagram profile (@scentralattraction) and the official Facebook page linked from the website footer.

Everything below is verified from those sources. Anything not verifiable is marked **Unverified** and must not appear on the website as fact.

---

## 1. Identity

| Item | Verified value | Source |
|---|---|---|
| Trading name | Scentral Attraction (also written "Scentralattraction") | Site title, About page |
| Descriptor | "Perfume & More" | Site title, footer, About page |
| Tagline (About page) | "Where elegance meets scent." | About page |
| Header strapline | "Premium Perfumes & More – Unforgettable, Just Like You" | Site header |
| Hero strapline | "Long-lasting • Sophisticated" / "Long-lasting fragrance in every drop." | Homepage |
| SEO description | "Discover Scentralattraction – your destination for luxury perfumes, body mists, and more. Shop captivating scents that define your presence." | Homepage meta description |
| Instagram display name | "ABUJA PERFUMES" (@scentralattraction), ~550 followers, ~570 posts | Instagram public metadata |
| Location | Beautiful Plaza, Dawaki, Abuja, Nigeria | Contact page, footer |
| Market / currency | Nigeria, Nigerian Naira (₦, NGN) | WooCommerce Store API |

### Brand voice (from existing copy)
The existing copy is warm, sensory and aspirational: *"fragrance is more than a scent – it's an experience, a memory, and a statement of style"*. Key brand promises stated by the business itself:

- Quality and authenticity.
- Exceptional customer care.
- A curated collection "carefully selected to cater to diverse tastes and personalities", spanning deep oud through delicate florals.
- Guidance on "your fragrance journey": signature scents, gifts, everyday elevation.

These are the only brand claims the new website repeats. No awards, founding year, founder names, ingredient sourcing claims or customer counts were found, so none will be used.

## 2. Positioning

Scentral Attraction is an **Abuja-based fragrance retailer and curator**. It is not a perfume house. The catalogue consists of third-party Eau de Parfum lines:

- **Anfar London** (a named brand category on the site).
- **Oud Perfumes** and **Occidental Fragrance** lines. Product photography shows these bottles labelled "AHMED".

Implications for the design:
1. The site sells *curation and trust*: authentic bottles, honest stock levels, personal service, fast local delivery.
2. Campaign photography must show **unbranded or generic bottles**. It must never imply that Scentral manufactures a product, or reuse another house's campaign imagery as its own.
3. Supplier pack shots, mostly 1500 × 1500 on white with ingredient props, remain the product images. The theme must make them look premium: a warm ivory card surface, generous padding and consistent aspect ratios.

### Price tier
₦35,000 – ₦105,000 per bottle (50–100 ml EDP). This is the accessible-luxury tier for Nigeria. The experience should feel premium without feeling unattainable: prices are always visible, and delivery and payment are clearly trustworthy.

### Target customers (inferred from catalogue and copy; for design only, not for publication)
- Abuja and Nigeria-wide shoppers aged 22–45 buying for themselves or as gifts.
- Oud and oriental lovers (the largest category).
- Instagram-driven discovery, mostly mobile. The design must be mobile-first.

## 3. Logo — verification

| Asset | Status | Notes |
|---|---|---|
| Emblem: line-art perfume bottle, interlocking **"SA" monogram**, flourishes either side | **Verified authentic.** It is the site icon configured in WordPress (`cropped-Scentralattraction-favicon-1.png`, 512 × 512). | Red line art (≈ #E8141B) with a black monogram on transparent background. |
| `Scentral-attraction-logos.png` (referenced as og:image) | **Missing.** It returns HTTP 404 on the live site. | The live header `<img>` tags have an empty `src`, so **the live site currently shows no logo in its header**. |
| Facebook profile image | Auto-generated letter "S" placeholder | Not a logo. |
| Instagram profile image | A product photograph | Not a logo. |

**Decision:**
- The verified emblem is kept as the official mark (`design-assets/logos/scentral-emblem-original.png`).
- A faithful **vector redraw of the emblem** is supplied as SVG. It keeps the same bottle, monogram and flourish composition, in single-colour and two-colour versions, so it scales crisply and can be stroke-animated in the preloader.
- A **typographic wordmark** ("SCENTRAL ATTRACTION" with "Perfume & More") is supplied as SVG to pair with the emblem. It is a lockup built around the existing mark, not a rebrand.
- All logo slots are replaceable in **Appearance → Customize → Site Identity** (custom logo) and **Customize → Scentral: Brand**.

## 4. Brand colours (observed)

| Role | Value | Observed on |
|---|---|---|
| Brand red | ≈ #E8141B | Emblem line art |
| Brand black | #000000 / #111 | Emblem monogram |

The new palette (see DESIGN_DIRECTION.md) keeps red and black as the signature pair. It deepens the red to a more luxurious *rouge* and sets it against ivory, champagne and noir.

## 5. Contact and social (verified)

- Address: Beautiful Plaza, Dawaki, Abuja
- Phone: +234 807 234 4444
- Email: hello@scentralattraction.com
- Hours: "Working 8:00am – 6:00pm" (days not specified; shown verbatim)
- Instagram: https://www.instagram.com/scentralattraction/
- Facebook: https://web.facebook.com/profile.php?id=100064004737193

No TikTok, X, WhatsApp, YouTube or other accounts are linked from the site. The theme has optional fields for them, left empty by default.

## 6. Reviews and testimonials

- WooCommerce has **one** product review: 4★ on "Anfar London Smoky Wood" by "admin", with lorem-ipsum text. **It is not genuine** and must not be shown as a testimonial.
- No other testimonials exist on the site.

**Decision:** The testimonials section renders **only genuine, approved WooCommerce product reviews**. Reviews by administrators are excluded, and so are reviews below a configurable rating threshold. The section hides itself when no qualifying reviews exist. No testimonial is ever written by the theme.

## 7. Unverified, so not to be used
- Founding year, founder, team.
- Shipping fees, delivery times, return windows (no policy pages exist).
- "100% authentic" guarantees beyond the business's own wording ("quality, authenticity").
- The "Download App on Mobile" footer text. No app link exists; it is template residue.
- "100% Secure delivery without contacting the courier". This header text comes from the previous demo theme and is not a verified service claim.
