# Installation Guide

## Requirements

| Component | Minimum | Tested |
|---|---|---|
| WordPress | 6.4 | 7.1 |
| WooCommerce | 8.0 | 11.3 |
| PHP | 7.4 | 8.3 |

No Node.js, build step or extra plugin is required on the server.

## 1. Before you start (production safety)

1. **Back up** the site (files and database). Most hosts have one-click backups. Otherwise use a backup plugin.
2. **Use staging first.** Most hosts offer "Create staging site". Install and review the theme there before production.
3. Nothing in this theme deletes or changes products, orders, customers, payment settings (Paystack) or shipping settings. Switching back to the previous theme restores the old look immediately.

## 2. Install

1. Download `releases/scentral-attraction-theme.zip` from this repository.
2. WordPress Admin → **Appearance → Themes → Add New Theme → Upload Theme**.
3. Choose the ZIP → **Install Now** → **Activate**.

## 3. First-time setup (about 10 minutes)

### Pages
On activation the theme creates any missing pages as **drafts** (FAQ, Shipping & Delivery, Returns & Refunds, Terms & Conditions, Journal, Wishlist) with the right template. Existing pages are never changed or deleted. Open **Appearance → Scentral Setup** to see every page, then add your text (block editor or Elementor) and click **Publish**.

### Homepage
**Settings → Reading → "Your homepage displays" → A static page.** Keep the existing "Home" page selected. The theme's homepage design replaces the old builder layout on the front end. The old layout stays saved in the page and returns if you switch back.

### Menus
**Appearance → Menus → Manage Locations**:

| Location | Suggested menu |
|---|---|
| Primary | Home · Shop · About Us · Contact Us |
| Utility (top bar) | My account · Wishlist · Order Tracking |
| Footer — Shop | Category links |
| Footer — Help | About · Contact · Shipping · Returns · FAQ |
| Legal | Privacy Policy · Terms & Conditions |

### Logo
**Appearance → Customize → Site Identity → Logo.** Upload the brand emblem or lockup. If nothing is uploaded, the theme shows its built-in emblem and wordmark.

### Theme options
**Appearance → Customize** has a panel per area: **Brand, Announcement Bar, Header, Homepage, Preloader & Motion, Contact & Social, Newsletter, Footer, Shop**. Every option is previewed live.

### Page templates
Edit a page → **Template** (in the sidebar):

| Page | Template |
|---|---|
| About Us | About (Editorial) |
| Contact Us | Contact |
| FAQ | FAQ |
| Shipping & Delivery, Returns & Refunds, Privacy Policy, Terms & Conditions | Policy |
| Wishlist | Wishlist (only if the TI Wishlist plugin is removed) |

### Fragrance notes (optional)
Each product has a **Fragrance Profile** box: top, heart and base notes, scent family, longevity, sillage and occasion. These fields appear on the product page only when filled in.

## 4. Updating the theme
Upload the new ZIP through the same screen. WordPress offers **Replace current with uploaded**. Customizer settings are kept.

## 5. Rolling back
**Appearance → Themes** → activate the previous theme. No data migration is needed.
