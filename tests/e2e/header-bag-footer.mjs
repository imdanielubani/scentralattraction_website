// Browser test (Chrome via playwright-core). Run: npm run test:e2e
// Env: SA_BASE_URL (default http://127.0.0.1:8080), CHROME_PATH, SA_ADMIN_USER/SA_ADMIN_PASS,
//      SA_CUSTOMER_USER/SA_CUSTOMER_PASS, SA_SHOTS (screenshot folder, default ./tests/e2e/shots).
import { chromium } from 'playwright-core';
const B = process.env.SA_BASE_URL || (process.env.SA_BASE_URL || 'http://127.0.0.1:8080') + '', OUT = (process.env.SA_SHOTS || 'tests/e2e/shots'), log = [];
const b = await chromium.launch({ executablePath: process.env.CHROME_PATH || 'C:/Program Files/Google/Chrome/Application/chrome.exe', headless: true });
const ctx = await b.newContext({ viewport: { width: 1440, height: 900 } });
const p = await ctx.newPage(); p.setDefaultTimeout(120000);
p.on('pageerror', (e) => log.push('pageerror: ' + e.message)); p.on('console', (m) => m.type() === 'error' && log.push('console: ' + m.text().slice(0, 200)));
const width = () => p.evaluate(() => document.documentElement.scrollWidth - innerWidth);
// Home: skip preloader for speed on 2nd load
await p.goto(B + '/', { waitUntil: 'load' }); await p.waitForTimeout(4500);
log.push('home overflow px: ' + await width());
await p.hover('.nav-links li.has-mega > a'); await p.waitForTimeout(900);
log.push('home mega open: ' + await p.evaluate(() => document.querySelector('[data-mega]').classList.contains('is-open')));
await p.screenshot({ path: OUT + '/a-home-mega.png' });
await p.mouse.move(700, 850); await p.waitForTimeout(700);
// Empty bag
await p.click('.nav-bag'); await p.waitForTimeout(900);
await p.screenshot({ path: OUT + '/b-bag-empty.png' });
log.push('bag open: ' + await p.evaluate(() => document.getElementById('cart-drawer').open) + ' | suggestions: ' + await p.locator('.bag-empty__list li').count());
// Quick add from empty state
await p.click('.bag-empty__add >> nth=0'); await p.waitForSelector('#cart-drawer .bag-item', { timeout: 20000 }).catch(() => {}); await p.waitForTimeout(800);
log.push('after quick add items: ' + await p.locator('#cart-drawer .bag-item').count() + ' | title: ' + (await p.locator('#cart-drawer .bag-head__title').innerText()).replace(/\s+/g, ' '));
await p.click('#cart-drawer .bag-qty button[data-step="1"]'); await p.waitForFunction(() => document.querySelector('#cart-drawer .bag-qty span')?.textContent.trim() === '2', null, { timeout: 20000 }).catch(() => {});
log.push('after + qty: ' + await p.locator('#cart-drawer .bag-qty span').first().innerText() + ' | bag pill: ' + await p.locator('.nav-bag .cart-count').innerText());
await p.screenshot({ path: OUT + '/c-bag-items.png' });
await p.keyboard.press('Escape'); await p.waitForTimeout(700);
// Footer
await p.evaluate(() => window.scrollTo(0, document.body.scrollHeight)); await p.waitForTimeout(1500);
await p.screenshot({ path: OUT + '/d-footer.png' });
log.push('closed drawer hidden: ' + await p.evaluate(() => getComputedStyle(document.getElementById('cart-drawer')).display));
log.push('social gap px: ' + await p.evaluate(() => { const a = document.querySelector('.site-footer__about'), s = document.querySelector('.site-footer .social-list'); return s && a ? Math.round(s.getBoundingClientRect().top - a.getBoundingClientRect().bottom) : 'n/a'; }));
log.push('footer emblem present: ' + await p.locator('.site-footer__mark .emblem').count());
// Shop page mega
await p.goto(B + '/shop/', { waitUntil: 'load' }); await p.waitForTimeout(1500);
await p.hover('.nav-links li.has-mega > a'); await p.waitForTimeout(900);
log.push('shop mega open: ' + await p.evaluate(() => document.querySelector('[data-mega]').classList.contains('is-open')) + ' | shop overflow px: ' + await width());
await p.screenshot({ path: OUT + '/e-shop-mega.png' });
// Keyboard access to the mega toggle
await p.mouse.move(700, 850); await p.waitForTimeout(700);
await p.focus('.mega-toggle'); await p.keyboard.press('Enter'); await p.waitForTimeout(800);
log.push('mega via keyboard: ' + await p.evaluate(() => document.querySelector('[data-mega]').classList.contains('is-open')));
for (const w of [1024, 768, 390, 360]) { await p.setViewportSize({ width: w, height: 844 }); for (const u of ['/', '/shop/', '/product/oud-classic/']) { await p.goto(B + u, { waitUntil: 'load' }); await p.waitForTimeout(600); const o = await width(); if (o > 0) log.push(`OVERFLOW ${o}px at ${w} on ${u}`); } }
log.push('overflow sweep done');
await b.close(); console.log(log.join('\n'));
