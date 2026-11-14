// Browser test (Chrome via playwright-core). Run: npm run test:e2e
// Env: SA_BASE_URL (default http://127.0.0.1:8080), CHROME_PATH, SA_ADMIN_USER/SA_ADMIN_PASS,
//      SA_CUSTOMER_USER/SA_CUSTOMER_PASS, SA_SHOTS (screenshot folder, default ./tests/e2e/shots).
import { chromium } from 'playwright-core';
const OUT = (process.env.SA_SHOTS || 'tests/e2e/shots'), PASS = process.env.SA_CUSTOMER_PASS || '', B = (process.env.SA_BASE_URL || 'http://127.0.0.1:8080') + '', log = [];
const b = await chromium.launch({ executablePath: process.env.CHROME_PATH || 'C:/Program Files/Google/Chrome/Application/chrome.exe', headless: true });
for (const [w, tag, mobile] of [[1440, 'd', false], [390, 'm', true]]) {
  const ctx = await b.newContext({ viewport: { width: w, height: mobile ? 844 : 900 }, isMobile: mobile, hasTouch: mobile });
  const p = await ctx.newPage(); p.setDefaultTimeout(120000);
  p.on('pageerror', (e) => log.push(`[${tag}] pageerror ${e.message.slice(0, 160)}`));
  p.on('console', (m) => m.type() === 'error' && !/favicon/.test(m.text()) && log.push(`[${tag}] console ${m.text().slice(0, 160)}`));
  const shot = async (name) => { await p.waitForTimeout(1500); await p.screenshot({ path: `${OUT}/${tag}-${name}.png`, fullPage: true }); };
  const ov = async (where) => { const o = await p.evaluate(() => document.documentElement.scrollWidth - innerWidth); if (o > 0) log.push(`[${tag}] OVERFLOW ${o}px on ${where}`); };
  // Cart + checkout (WooCommerce blocks)
  await p.goto(B + '/product/oud-classic/', { waitUntil: 'load' }); await p.click('form.cart .single_add_to_cart_button'); await p.waitForTimeout(3500);
  await p.goto(B + '/cart/', { waitUntil: 'load' }); await p.waitForSelector('.wc-block-cart, .woocommerce-cart-form', { timeout: 60000 }).catch(() => log.push(`[${tag}] cart did not render`)); await shot('cart'); await ov('cart');
  await p.goto(B + '/checkout/', { waitUntil: 'load' }); await p.waitForSelector('.wc-block-checkout, form.checkout', { timeout: 60000 }).catch(() => log.push(`[${tag}] checkout did not render`)); await shot('checkout'); await ov('checkout');
  log.push(`[${tag}] checkout: email field ${await p.locator('#email, #billing_email').count()}, place order ${await p.locator('.wc-block-components-checkout-place-order-button, #place_order').count()}`);
  // Account (logged out)
  await p.goto(B + '/my-account/', { waitUntil: 'load' }); await shot('account-login'); await ov('account login');
  log.push(`[${tag}] login form ${await p.locator('form.woocommerce-form-login').count()}, register form ${await p.locator('form.woocommerce-form-register').count()}`);
  await p.fill('#username', process.env.SA_CUSTOMER_USER || 'customer@example.test'); await p.fill('#password', PASS);
  await Promise.all([p.waitForNavigation({ waitUntil: 'load' }), p.click('button[name="login"]')]);
  log.push(`[${tag}] logged in: ${await p.locator('.woocommerce-MyAccount-navigation').count() > 0}`);
  await shot('account-dashboard'); await ov('dashboard');
  for (const [ep, n] of [['orders', 'orders'], ['edit-address', 'addresses'], ['edit-account', 'details']]) { await p.goto(`${B}/my-account/${ep}/`, { waitUntil: 'load' }); await shot('account-' + n); await ov(ep); }
  await p.goto(B + '/my-account/orders/', { waitUntil: 'load' });
  log.push(`[${tag}] orders rows: ${await p.locator('.woocommerce-orders-table tbody tr').count()}`);
  const view = p.locator('.woocommerce-orders-table a.view').first();
  if (await view.count()) { await view.click(); await p.waitForLoadState('load'); await shot('account-order'); await ov('view order'); }
  await ctx.close();
}
await b.close(); console.log(log.join('\n') || 'no issues');
