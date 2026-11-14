// Browser test (Chrome via playwright-core). Run: npm run test:e2e
// Env: SA_BASE_URL (default http://127.0.0.1:8080), CHROME_PATH, SA_ADMIN_USER/SA_ADMIN_PASS,
//      SA_CUSTOMER_USER/SA_CUSTOMER_PASS, SA_SHOTS (screenshot folder, default ./tests/e2e/shots).
import { chromium } from 'playwright-core';
const OUT = (process.env.SA_SHOTS || 'tests/e2e/shots'), PASS = process.env.SA_ADMIN_PASS || '', B = (process.env.SA_BASE_URL || 'http://127.0.0.1:8080') + '', log = [];
const b = await chromium.launch({ executablePath: process.env.CHROME_PATH || 'C:/Program Files/Google/Chrome/Application/chrome.exe', headless: true });
const ctx = await b.newContext({ viewport: { width: 1440, height: 900 } });
const p = await ctx.newPage(); p.setDefaultTimeout(180000);
const errs = []; p.on('pageerror', (e) => errs.push(e.message.slice(0, 160)));
// 1. Front end (logged out)
await p.goto(B + '/elementor-test/', { waitUntil: 'load' }); await p.waitForTimeout(2000);
log.push('front: main class=' + await p.locator('#main').getAttribute('class') + ' | elementor heading=' + await p.locator('.elementor-widget-heading').count() + ' | scentral widget=' + await p.locator('.scentral-widget--new_arrivals').count() + ' | product cards=' + await p.locator('.scentral-widget li.card').count() + ' | theme page title=' + await p.locator('.page-hero__title').count());
await p.screenshot({ path: OUT + '/el-front.png' });
// 2. Editor (admin)
await p.goto(B + '/wp-login.php', { waitUntil: 'load' });
await p.fill('#user_login', process.env.SA_ADMIN_USER || 'admin'); await p.fill('#user_pass', PASS);
await Promise.all([p.waitForNavigation({ waitUntil: 'load' }), p.click('#wp-submit')]);
await p.goto(B + '/wp-admin/post.php?post=' + (process.env.SA_ELEMENTOR_PAGE_ID || '') + '&action=elementor', { waitUntil: 'load' });
await p.waitForSelector('#elementor-panel', { timeout: 180000 });
const frame = p.frameLocator('#elementor-preview-iframe');
await frame.locator('.elementor-widget-scentral-section').first().waitFor({ timeout: 180000 }).catch(() => log.push('preview widget not found'));
await p.waitForTimeout(3000);
log.push('editor: preview scentral widgets=' + await frame.locator('.elementor-widget-scentral-section').count() + ' | product cards in preview=' + await frame.locator('.scentral-widget li.card').count() + ' | preloader in preview=' + await frame.locator('.preloader').count() + ' | revealed in preview=' + await frame.locator('.scentral-widget li.card.is-visible').count());
// Panel search for the widget
const search = p.locator('#elementor-panel-elements-search-input');
if (await search.count()) { await search.fill('Scentral'); await p.waitForTimeout(1200); log.push('panel search "Scentral": ' + (await p.locator('.elementor-element .title').allInnerTexts()).filter((t) => /Scentral/i.test(t)).join(', ')); }
else { await p.click('#elementor-panel-header-add-button').catch(() => {}); await p.waitForTimeout(800); const s2 = p.locator('#elementor-panel-elements-search-input'); if (await s2.count()) { await s2.fill('Scentral'); await p.waitForTimeout(1200); log.push('panel search "Scentral": ' + (await p.locator('.elementor-element .title').allInnerTexts()).filter((t) => /Scentral/i.test(t)).join(', ')); } }
await p.screenshot({ path: OUT + '/el-editor.png' });
log.push('editor JS errors: ' + (errs.length ? errs.join(' | ') : 'none'));
await b.close(); console.log(log.join('\n'));
