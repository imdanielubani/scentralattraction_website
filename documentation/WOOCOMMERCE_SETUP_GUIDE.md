# WooCommerce Setup Guide

The theme reads everything from WooCommerce. Nothing about products, prices, stock, payments or shipping is stored in the theme. This guide covers the store settings that make the design shine, plus the clean-up items found in the audit.

## 1. What the theme leaves untouched
- **Payments (Paystack)**: the checkout's payment section is WooCommerce's own markup. The theme only applies fonts and spacing. No gateway setting changes.
- **Shipping zones, taxes, coupons, emails, orders, customers**: unchanged.
- **Cart and Checkout**: work with both the classic shortcodes and the newer Cart/Checkout blocks.
- **Plugins**: Contact Form 7, TI WooCommerce Wishlist, Mailchimp for WP, Klaviyo and Yoast SEO keep working. Yoast keeps control of titles, meta, Open Graph, schema and breadcrumbs.

## 2. Recommended store settings
| Where | Setting | Why |
|---|---|---|
| WooCommerce → Settings → General | Number of decimals: **0** | ₦80,000 reads more refined than ₦80,000.00 |
| WooCommerce → Settings → Products → Inventory | Keep stock management on and "Out of stock visibility" unchecked | Sold-out bottles show a "Sold out" badge instead of disappearing |
| WooCommerce → Settings → Products → General | Enable AJAX add to cart buttons on archives | Adding to the bag opens the drawer without reloading the page |
| Customize → WooCommerce → Product Images | Thumbnail cropping **4:5** (custom 4 × 5) | Matches the card proportions |
| Products → each product | Product image + 1–2 gallery images | The second image appears on hover in product cards |

After changing image sizes, regenerate thumbnails (for example with the "Regenerate Thumbnails" plugin).

## 3. Product data conventions
- **Size**: keep the existing **Size** attribute (e.g. `100ML EDP`). The theme shows it on cards ("100ML EDP · Anfar London") and in Specifications.
- **Variable products**: create a global attribute **Size** (Products → Attributes) and make variations per size. Prices update in the add-to-cart area automatically.
- **Sale prices**: set a sale price and the card shows a "−X%" badge. The `?on_sale=1` filter and the "On sale" rail work automatically.
- **Featured**: star a product in Products to use the "Featured" hero list or rail.
- **Fragrance Profile**: fill in the notes to display the scent pyramid on the product page.

## 4. Categories for the homepage collections
The homepage can show **For Him / For Her / Shared** panels. Create categories (Products → Categories), e.g. `men`, `women`, `unisex`. Assign products, then enter the slugs under **Customize → Scentral Theme → Homepage — Collections & Banners**. A product can be in several categories (e.g. *Anfar London* and *Men*).

Only assign genders you are confident of. The theme never guesses a fragrance's gender.

## 5. Audit clean-up (recommended, done by the store owner)
These were found on the live site during the audit (14 November 2026):

1. **Placeholder review**: "Anfar London Smoky Wood" has a 4★ review by *admin* with lorem-ipsum text. Delete it under Products → Reviews. (The theme already hides admin reviews from the homepage.)
2. **Product slug**: Smoky Wood's URL is `/product/sol-ti-coconut-charcoal-superade/`. Change the slug to `anfar-london-smoky-wood` *and* add a 301 redirect (Yoast Premium or the Redirection plugin).
3. **Oud categories**: Pearl Oud, Oud Classic, Oud Lavender, Summer Oud and Oud & Roses showed no category in the public catalogue. Check that each is ticked in **Oud Perfume**.
4. **Vendor typo**: "Scentralatrraction" on Oud & Roses (Attributes tab).
5. **Policies**: Privacy Policy still contains WordPress's default text. Shipping, Returns and Terms pages don't exist. Create them, assign the **Policy** template, and write the actual policies. The theme will not invent them.
6. **Category images**: add an image to each product category (Products → Categories → Thumbnail) for the collage. Otherwise the first product's image is used.

## 6. Pages to assign
| WooCommerce → Settings → Advanced | Page |
|---|---|
| Cart | Cart |
| Checkout | Checkout |
| My account | My account |
| Terms and conditions | Terms & Conditions (once written) |

Wishlist page: keep the TI Wishlist page as it is. Set its template to **Wishlist** for matching styling.

## 7. Testing the store safely
Use a **staging copy** (most hosts provide one) and Paystack's **test mode** with its published test card. Don't place real orders on production to test.
