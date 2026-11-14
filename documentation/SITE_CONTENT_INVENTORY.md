# Site Content Inventory

Snapshot of every piece of real content on scentralattraction.com (14 November 2026). Product data is **not** copied into the theme. It stays in WooCommerce and is listed here only so design decisions can be checked against reality.

## 1. Product catalogue (WooCommerce, 18 products, all simple, NGN)

| ID | Product | Category (Store API) | Size | Price ₦ | Stock | Images |
|---|---|---|---|---|---|---|
| 2275 | Hayana | Occidental Fragrance | 100ML EDP | 55,000 | In stock | 2 |
| 2274 | Marj | Occidental Fragrance | 60ML EDP | 105,000 | Out | 3 |
| 2273 | Cherry | Occidental Fragrance | 50ML EDP | 35,000 | Out | 2 |
| 2271 | Rose Noir | Occidental Fragrance | 75ML EDP | 85,000 | In stock | 3 |
| 2270 | Supreme | Occidental Fragrance | 50ML EDP | 57,000 | Out | 3 |
| 2269 | Musk & Roses | Occidental Fragrance | 75ML EDP | 65,000 | In stock | 3 |
| 2245 | Leather | Occidental Fragrance | 50ML EDP | 50,000 | In stock | 3 |
| 2216 | Pearl Oud | — (Oud) | 75ML EDP | 80,000 | Out | 3 |
| 2208 | Oud Classic | — (Oud) | 50ML EDP | 50,000 | In stock | 3 |
| 2207 | Oud Lavender | — (Oud) | 75ML EDP | 85,000 | In stock | 3 |
| 2200 | Summer Oud | — (Oud) | 60ML EDP | 80,000 | In stock | 3 |
| 2199 | Oud & Roses | — (Oud) | 60ML EDP | 80,000 | In stock | 3 |
| 2183 | Anfar London Marble Bruno Imperial | Anfar London | 100ML EDP | 80,000 | In stock | 3 |
| 2179 | Anfar London Intense Sport | Anfar London | 100ML EDP | 70,000 | In stock | 3 |
| 266 | Anfar London Floral Bloom | Anfar London | 100ML EDP | 60,000 | Out | 2 |
| 263 | Anfar London Rituals Of Anfar Chef-d'oeuvre | Anfar London | 80ML EDP | 70,000 | In stock | 3 |
| 258 | Anfar London Midnight Frost Intense | Anfar London | 100ML EDP | 55,000 | In stock | 3 |
| 254 | Anfar London Smoky Wood | Anfar London | 100ML EDP | 75,000 | Out | 3 |

No products are currently on sale.

### Product categories
| Slug | Name | Count |
|---|---|---|
| `anfar-london` | Anfar London | 6 |
| `occidental-fragrance` | Occidental Fragrance | 7 |
| `oud-perfume` | Oud Perfume (menu label "Oud Perfumes") | 5 |

**Not present:** Men, Women, Unisex, Niche, Designer, New Arrivals, Bestsellers, Sale. The theme handles these as follows:
- **New Arrivals**: newest products by date (no category needed).
- **Bestsellers**: ordered by WooCommerce `total_sales` (no category needed).
- **Sale**: products with an active sale price (WooCommerce native).
- **Men / Women / Unisex / Niche / Designer**: the homepage sections and banners are mapped to categories chosen in the Customizer. A section stays hidden until the owner creates the category and assigns products. The theme never guesses a fragrance's gender.

### Fragrance notes
Several descriptions contain explicit note pyramids, for example Pearl Oud: *"Top: Fruity Notes, Pear, Pink pepper / Heart: Tuberose, Jasmine, Ylang-ylang, Agarwood / Base: Amber, Musk, Patchouli"*. The theme provides structured **Top / Heart / Base notes**, **Scent family**, **Longevity**, **Sillage** and **Occasion** fields in a product meta box. They display on the product page only when filled in. Nothing is pre-filled.

## 2. Page copy worth keeping (verbatim, from the live site)

### About Us
> At Scentralattraction, we believe fragrance is more than a scent – it's an experience, a memory, and a statement of style. Our passion lies in curating and creating luxurious perfumes that inspire confidence, elegance, and individuality.
>
> From the deep, captivating aroma of oud to the delicate sweetness of florals, our collection is carefully selected to cater to diverse tastes and personalities. Each bottle tells a story – one that becomes uniquely yours the moment you wear it.
>
> Founded with a love for fine fragrances, Scentralattraction has grown into a trusted destination for perfume lovers who seek quality, authenticity, and exceptional customer care. We are committed to bringing you scents that not only smell incredible but leave a lasting impression wherever you go.
>
> Whether you're searching for your signature scent, a thoughtful gift, or something new to elevate your everyday, we are here to guide you on your fragrance journey.
>
> Scentralattraction – Perfume & More. Where elegance meets scent.

The About template takes this copy from the page itself. Editorial pull-quotes and value headings are selected from these sentences, not invented.

### Contact Us
> We'd love to hear from you! Whether you have a question about our fragrances, need help with your order, or want to explore partnership opportunities, our team is here to assist you.
>
> Drop Us a Line — Simply fill out the form, include your message, and we'll get back to you as soon as we can.

Fields: Your name*, Your email*, Subject*, Your message (Contact Form 7).

### Homepage
- "Long-lasting • Sophisticated"
- "Luxury Perfume — Experience timeless elegance collection — Oud, Rose, & Lavender"
- "Where Elegance Meets Timelessness" (Oud Classic)
- "Unleash the Bloom of Elegance" (Floral Bloom)
- "New Arrivals — Long-lasting fragrance in every drop."

## 3. Content that does not exist yet (owner to supply)

These pages are built as fully designed templates. Their **text must come from the business**. The theme seeds nothing that reads as policy.

| Page | Template | What the owner must provide |
|---|---|---|
| Shipping & Delivery | `page-templates/template-policy.php` | Delivery areas, fees, timelines, couriers |
| Returns & Refunds | `template-policy.php` | Return window, condition rules, refund method |
| Terms & Conditions | `template-policy.php` | Legal terms |
| Privacy Policy | `template-policy.php` | Replace the default text (currently references the theme vendor demo) |
| FAQ | `template-faq.php` | Question and answer pairs, entered as Details blocks in the editor |
| Journal | Blog index | Articles |

When a policy page has no content, the template shows a discreet "This page is being updated — contact us" notice with the verified contact details. It never shows invented policy text.

## 4. Media

| Asset | Size | Use |
|---|---|---|
| Product pack shots (white background, ingredient props) | 1500 × 1500 JPG | Product cards and gallery |
| `slider-image-1.jpg` (Oud Classic on black with smoke) | 771 × 561 | Existing hero slide. Too small for a full-bleed hero, so it is kept as an optional slide image. |
| `home-banner-14/15.jpg`, `slider-image-2/4` | small | Legacy banners |
| `Occidental-Fragrance.jpg` | large | Legacy category banner |
| Brand emblem | 512 × 512 PNG | Verified logo |
| `Purple-and-Blue-Modern-Logo-Animation-Video-1.mp4` | 1920 × 1080 | Unrelated stock animation (not used) |

New campaign art direction (unbranded bottles) lives in `design-assets/` and is documented in DESIGN_DIRECTION.md §7.

## 5. Navigation (current)
- Primary: Home · Shop · About Us · Contact Us
- Top bar: About Us · My account · Wishlist · Order Tracking
- Footer "Useful links": Home · About Us · Shop · My account · Contact Us
- Footer "Categories": Oud Perfumes · Anfar London · Occidental Fragrance
- Legal: Privacy Policy · Terms & Conditions

The theme registers matching menu locations (`primary`, `utility`, `footer-shop`, `footer-help`, `legal`). The existing menus can be assigned to them directly.
