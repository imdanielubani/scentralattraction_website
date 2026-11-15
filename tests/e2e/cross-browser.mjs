// Cross-browser smoke test: Firefox and WebKit (Safari's engine) at 1440px and 390px.
// Needs the Playwright browser builds: node node_modules/playwright-core/cli.js install firefox webkit
// Env: SA_BASE_URL (default http://127.0.0.1:8080), SA_SHOTS (screenshot folder).
import { mkdirSync } from 'node:fs';
import pw from 'playwright-core';
const out = (process.env.SA_SHOTS || 'tests/e2e/shots') + '/cross-browser';
mkdirSync(out, { recursive: true });
const B = process.env.SA_BASE_URL || 'http://127.0.0.1:8080';
const pages = ['/', '/shop/', '/product/rose-noir/', '/contact-us/', '/order-tracking/', '/about-us/', '/faq/', '/cart/', '/checkout/', '/my-account/', '/wishlist/'];
for (const name of ['firefox', 'webkit']) {
  const browser = await pw[name].launch({ headless: true });
  for (const [w, h, mobile] of [[1440, 900, false], [390, 844, true]]) {
    const ctx = await browser.newContext({ viewport: { width: w, height: h }, ...(mobile && name === 'webkit' ? { isMobile: false, hasTouch: true } : {}) });
    const issues = [];
    for (const u of pages) {
      const p = await ctx.newPage(); p.setDefaultTimeout(120000);
      const errs = []; p.on('pageerror', (e) => errs.push(e.message.slice(0, 120)));
      const r = await p.goto(B + u, { waitUntil: 'load' }); await p.waitForTimeout(2500);
      const H = await p.evaluate(() => document.documentElement.scrollHeight);
      for (let y = 0; y < Math.min(H, 9000); y += 700) { await p.evaluate((v) => scrollTo(0, v), y); await p.waitForTimeout(60); }
      const info = await p.evaluate(() => ({ ov: document.documentElement.scrollWidth - innerWidth, header: !!document.querySelector('.site-header'), footer: !!document.querySelector('.site-footer'), h1: document.querySelectorAll('h1').length, hidden: [...document.querySelectorAll('[data-reveal]:not(.is-visible)')].filter((e) => e.getBoundingClientRect().height > 0 && e.offsetParent !== null).length }));
      if (r.status() >= 400 || errs.length || info.ov > 1 || !info.header || !info.footer || info.h1 < 1) issues.push(`${u} status=${r.status()} ov=${info.ov} h1=${info.h1} errs=${errs.join(' | ')}`);
      if (['/', '/shop/', '/product/rose-noir/'].includes(u)) { await p.evaluate(() => scrollTo(0, 0)); await p.waitForTimeout(900); await p.screenshot({ path: `${out}/${name}-${w}-${u.replace(/\W+/g, '_') || 'home'}.png` }); }
      await p.close();
    }
    // Interactions
    const p = await ctx.newPage(); p.setDefaultTimeout(60000);
    const errs = []; p.on('pageerror', (e) => errs.push(e.message.slice(0, 120)));
    await p.goto(B + '/product/rose-noir/', { waitUntil: 'load' }); await p.waitForTimeout(2500);
    await p.locator('.pg--single .pg__stage').hover().catch(() => {});
    await p.locator('.pg--single [data-gallery-next]').click({ force: true });
    await p.waitForTimeout(500);
    const gal = await p.evaluate(() => document.querySelector('.pg--single [data-gallery-current]')?.textContent);
    await p.locator('form.cart .single_add_to_cart_button').click(); await p.waitForTimeout(3500);
    const bag = await p.evaluate(() => ({ open: document.getElementById('cart-drawer')?.open, items: document.querySelectorAll('#cart-drawer .bag-item').length }));
    await p.keyboard.press('Escape'); await p.waitForTimeout(600);
    await p.goto(B + '/shop/', { waitUntil: 'load' }); await p.waitForTimeout(2500);
    const card = p.locator('li.card', { hasText: 'Leather' }); await card.scrollIntoViewIfNeeded(); await p.waitForTimeout(1500); await card.hover().catch(() => {});
    await card.locator('[data-quick-view]').click(); await p.waitForSelector('.qv .pg__stage'); await p.waitForTimeout(1000);
    await p.locator('.qv [data-gallery-next]').click(); await p.waitForTimeout(400);
    const qv = await p.evaluate(() => document.querySelector('.qv [data-gallery-current]')?.textContent);
    console.log(`${name} ${w}: pages issues=${issues.length ? '\n  ' + issues.join('\n  ') : 'none'} | gallery next -> ${gal} | bag ${JSON.stringify(bag)} | quick view next -> ${qv} | interaction errors ${errs.length ? errs.join(' | ') : 'none'}`);
    await ctx.close();
  }
  await browser.close();
}
