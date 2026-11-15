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
4. WordPress then opens **Appearance → Theme Plugins**. Click **Install & activate all required plugins** (or use each row's button). Plugins are installed from WordPress.org:

| Plugin | Type | Used for |
|---|---|---|
| WooCommerce | Required | Shop, product pages, bag, checkout, accounts |
| Paystack WooCommerce Payment Gateway | Required | The store's payment method (add your Paystack keys in WooCommerce → Settings → Payments) |
| Elementor | Required | Visual page editing; the theme's sections are Elementor widgets |
| Contact Form 7 | Required | The Contact page form and its emails |
| TI WooCommerce Wishlist | Recommended | Customer wishlists (a simple built-in wishlist is used otherwise) |
| MC4WP: Mailchimp for WordPress | Recommended | Newsletter sign-ups to Mailchimp |
| Yoast SEO | Recommended | Search titles, descriptions, sitemaps, breadcrumbs |

Until every required plugin is active, a red notice with an **Install & activate required plugins** button shows on every admin screen. Plugins that are already installed and active (as on the current live site) are left as they are. If your host blocks installs from the dashboard, install the same plugins from **Plugins → Add New**.

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

### Contact form emails (Contact Form 7: Mail and Mail (2))
The Contact page shows your **Contact Form 7** form. The theme only styles it. Who receives the messages, and what the customer gets back, is set inside the plugin and the theme never changes it.

Go to **Contact → Contact Forms**, open the form used on the Contact page (by default "Contact form 1"), then open the **Mail** tab. The form fields are `[your-name]`, `[your-email]`, `[your-subject]` and `[your-message]`; use them as mail-tags.

**Mail (the message to the store)**

| Field | Recommended value |
|---|---|
| To | `hello@scentralattraction.com` (or the inbox that should receive enquiries) |
| From | `[_site_title] <wordpress@scentralattraction.com>`: an address on **your own domain**, never the customer's address, or the email is often marked as spam |
| Subject | `[_site_title] — [your-subject]` |
| Additional headers | `Reply-To: [your-name] <[your-email]>`, so pressing **Reply** in your inbox answers the customer directly |
| Message body | `From: [your-name] <[your-email]>`<br>`Subject: [your-subject]`<br><br>`[your-message]` |

**Mail (2) (automatic reply to the customer)**

> **Turn on spam protection first.** Mail (2) sends an email to whatever address a visitor types, so without protection spammers can use your form to send mail to anyone. Contact Form 7 shows the warning *"Unsafe email config is used without sufficient protection"* on Mail (2) until **Cloudflare Turnstile** (free) or **Google reCAPTCHA v3** is connected under **Contact → Integration**. Set one up (create the site key and secret key in your Cloudflare or Google account, paste them there), then the warning disappears and the check is added to the form automatically.

Tick **Use Mail (2)**, then:

| Field | Recommended value |
|---|---|
| To | `[your-email]` |
| From | The same own-domain address as Mail, e.g. `[_site_title] <wordpress@scentralattraction.com>` |
| Subject | `Thank you for contacting [_site_title]` |
| Additional headers | `Reply-To: hello@scentralattraction.com` |
| Message body | `Hello [your-name],`<br><br>`Thank you for getting in touch with Scentral Attraction. We have received your message and will get back to you as soon as we can.`<br><br>`Your message:`<br>`[your-message]`<br><br>`Scentral Attraction — Perfume & More`<br>`Beautiful Plaza, Dawaki, Abuja · +234 807 234 4444` |

Click **Save**. Contact Form 7 shows a warning on the Mail tab if a field is invalid (for example a From address on another domain). Fix any warnings before testing.

**Make sure the emails arrive**
1. WordPress's built-in mail is often blocked or sent to spam. Install an SMTP plugin (for example **WP Mail SMTP**) and connect it to your email provider (your host's mailbox, Google Workspace, Zoho, Brevo and so on). Use the same address as the **From** field above.
2. Send a test message from the Contact page using an email address you can check.
3. Confirm **both** emails arrive: the enquiry in the store inbox (Mail) and the thank-you in the test inbox (Mail (2)). Check the spam folders too.
4. Reply to the enquiry from the store inbox and confirm the reply goes to the customer's address.

If the theme's built-in form is ever used instead (only when no form plugin is on the page), it sends to the **Contact email** set in **Customize → Contact & Social** and has no automatic reply.

### Fragrance notes (optional)
Each product has a **Fragrance Profile** box: top, heart and base notes, scent family, longevity, sillage and occasion. These fields appear on the product page only when filled in.

## 4. Updating the theme
Upload the new ZIP through the same screen. WordPress offers **Replace current with uploaded**. Customizer settings are kept.

## 5. Rolling back
**Appearance → Themes** → activate the previous theme. No data migration is needed.
