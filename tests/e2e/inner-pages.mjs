// Browser test (Chrome via playwright-core). Run: npm run test:e2e
// Env: SA_BASE_URL (default http://127.0.0.1:8080), CHROME_PATH, SA_ADMIN_USER/SA_ADMIN_PASS,
//      SA_CUSTOMER_USER/SA_CUSTOMER_PASS, SA_SHOTS (screenshot folder, default ./tests/e2e/shots).
import { chromium } from 'playwright-core';
const OUT = (process.env.SA_SHOTS || 'tests/e2e/shots'), B = (process.env.SA_BASE_URL || 'http://127.0.0.1:8080') + '', log = [];
const b = await chromium.launch({ executablePath: process.env.CHROME_PATH || 'C:/Program Files/Google/Chrome/Application/chrome.exe', headless: true });
const runs = [
  [1440, ['/about-us/', '/contact-us/', '/faq/', '/shipping-delivery/', '/privacy-policy/', '/wishlist/', '/order-tracking/', '/?s=rose', '/product-category/anfar-london/', '/nope-404/']],
  [1024, ['/', '/shop/', '/product/oud-classic/', '/about-us/']],
  [768, ['/', '/shop/', '/product/oud-classic/', '/about-us/', '/contact-us/']],
];
for (const [w, urls] of runs) {
  const ctx = await b.newContext({ viewport: { width: w, height: 1000 } });
  const p = await ctx.newPage(); p.setDefaultTimeout(120000);
  p.on('pageerror', (e) => log.push(`[${w}] pageerror ${e.message.slice(0, 140)}`));
  p.on('console', (m) => m.type() === 'error' && !/favicon|404 \(Not Found\)/.test(m.text()) && log.push(`[${w}] console ${m.text().slice(0, 140)}`));
  for (const u of urls) {
    await p.goto(B + u, { waitUntil: 'load' }); await p.waitForTimeout(u === '/' ? 4500 : 900);
    for (let y = 0; y < 12000; y += 700) { await p.evaluate((v) => scrollTo(0, v), y); await p.waitForTimeout(60); }
    await p.evaluate(() => scrollTo(0, 0)); await p.waitForTimeout(500);
    const o = await p.evaluate(() => document.documentElement.scrollWidth - innerWidth); if (o > 0) log.push(`[${w}] OVERFLOW ${o}px ${u}`);
    const hidden = await p.evaluate(() => [...document.querySelectorAll('[data-reveal]')].filter((n) => { const r = n.getBoundingClientRect(); const offRail = n.closest('[data-rail]') && (r.left >= innerWidth || r.right <= 0); return r.height > 0 && r.width > 0 && !offRail && !n.classList.contains('is-visible'); }).length);
    if (hidden) log.push(`[${w}] ${hidden} unrevealed elements on ${u}`);
    const name = `${w}${u.replace(/[^a-z0-9]+/gi, '-')}`.replace(/-+$/, '');
    await p.screenshot({ path: `${OUT}/${name}.png`, fullPage: true });
  }
  await ctx.close();
}
await b.close(); console.log(log.join('\n') || 'no errors, no overflow, all revealed');
