// Browser test (Chrome via playwright-core). Run: npm run test:e2e
// Env: SA_BASE_URL (default http://127.0.0.1:8080), CHROME_PATH, SA_ADMIN_USER/SA_ADMIN_PASS,
//      SA_CUSTOMER_USER/SA_CUSTOMER_PASS, SA_SHOTS (screenshot folder, default ./tests/e2e/shots).
import { execFileSync } from 'node:child_process';
import { mkdirSync } from 'node:fs';
mkdirSync(process.env.SA_SHOTS || 'tests/e2e/shots', { recursive: true });
const suites = ['header-bag-footer.mjs', 'inner-pages.mjs'];
if (process.env.SA_CUSTOMER_PASS) suites.push('commerce-account.mjs');
if (process.env.SA_ADMIN_PASS && process.env.SA_ELEMENTOR_PAGE_ID) suites.push('elementor.mjs');
if (process.env.SA_XBROWSER) suites.push('cross-browser.mjs');
for (const s of suites) {
  console.info(`
=== ${s}`);
  execFileSync(process.execPath, [new URL(s, import.meta.url).pathname.replace(/^\/(\w:)/, '$1')], { stdio: 'inherit' });
}
