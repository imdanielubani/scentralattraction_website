/**
 * Static integrity checks for the theme (no WordPress needed).
 *   npm test
 *
 * Verifies:
 *  1. Every scentral_*() function that is called is defined.
 *  2. Every get_template_part() / wc_get_template_part() target exists.
 *  3. Every scentral_option('key') is registered in the Customizer registry.
 *  4. Every bundled campaign image referenced by name exists (+ mobile crop for landscape).
 *  5. Compiled assets and required theme files exist; style.css header is complete.
 *  6. The front-end bundle parses as JavaScript and contains no console.log.
 *  7. Theme files contain no forbidden tool or vendor mentions.
 *  8. WooCommerce-only helpers are not called from templates that render without WooCommerce.
 */
import { readFileSync, readdirSync, statSync, existsSync } from 'node:fs';
import { fileURLToPath } from 'node:url';
import path from 'node:path';

const root = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '..');
const theme = path.join(root, 'wordpress-theme', 'scentral-attraction');
const results = [];
const check = (name, ok, detail = '') => results.push({ name, ok, detail });

function walk(dir, exts) {
	return readdirSync(dir).flatMap((name) => {
		const full = path.join(dir, name);
		if (statSync(full).isDirectory()) return walk(full, exts);
		return exts.some((e) => full.endsWith(e)) ? [full] : [];
	});
}
const rel = (f) => path.relative(theme, f).split(path.sep).join('/');
const phpFiles = walk(theme, ['.php']);
const php = Object.fromEntries(phpFiles.map((f) => [rel(f), readFileSync(f, 'utf8')]));
const allPhp = Object.values(php).join('\n');

// 1. Defined vs called functions.
const defined = new Set([...allPhp.matchAll(/function\s+(scentral_\w+)\s*\(/g)].map((m) => m[1]));
const called = new Set([...allPhp.matchAll(/(?<![\w$>:])(scentral_\w+)\s*\(/g)].map((m) => m[1]));
// Strings passed as callbacks (hooks, sanitize callbacks, menu fallbacks) must also be defined.
const callbacks = new Set([
	...[...allPhp.matchAll(/add_(?:action|filter)\(\s*'[^']+'\s*,\s*'(scentral_\w+)'/g)].map((m) => m[1]),
	...[...allPhp.matchAll(/(?:fallback_cb|sanitize_callback|sanitizer)'?\s*(?:=>|=)\s*'(scentral_\w+)'/g)].map((m) => m[1]),
	...[...allPhp.matchAll(/add_(?:meta_box|management_page)\([^;]*?'(scentral_render_\w+)'/gs)].map((m) => m[1]),
]);
const missing = [...new Set([...called, ...callbacks])].filter((n) => !defined.has(n));
check('All scentral_* functions are defined', missing.length === 0, missing.join(', '));

// 2. Template parts exist.
const parts = [...allPhp.matchAll(/(?<!wc_)get_template_part\(\s*'([^']+)'(?:\s*,\s*'([^']+)')?/g)]
	.filter((m) => !m[1].endsWith('/')); // Dynamic paths ('…/sections/' . $id) are checked below.
const missingParts = parts.map((m) => (m[2] ? `${m[1]}-${m[2]}.php` : `${m[1]}.php`))
	.filter((p) => !existsSync(path.join(theme, p)));
const wcParts = [...allPhp.matchAll(/wc_get_template_part\(\s*'([^']+)'\s*,\s*'([^']+)'/g)]
	.map((m) => `woocommerce/${m[1]}-${m[2]}.php`)
	.filter((p) => !existsSync(path.join(theme, p)));
missingParts.push(...new Set(wcParts));
const dynamicSections = (php['front-page.php'].match(/\$template_sections\s*=\s*array\(([^)]*)\)/) || [, ''])[1]
	.match(/'([a-z_]+)'/g)?.map((s) => s.replace(/'/g, '')) || [];
const missingSections = dynamicSections.filter((s) => !existsSync(path.join(theme, 'template-parts', 'sections', `${s}.php`)));
check('All template parts exist', missingParts.length + missingSections.length === 0, [...missingParts, ...missingSections].join(', '));
check('WooCommerce content-product override exists', existsSync(path.join(theme, 'woocommerce', 'content-product.php')));

// 3. Options registered.
const registry = php['inc/customizer.php'];
const registered = new Set([...registry.matchAll(/^\s*'([a-z0-9_]+)'\s*=>\s*array\(\s*'scentral_/gm)].map((m) => m[1]));
const usedOptions = new Set([...allPhp.matchAll(/scentral_option\(\s*'([a-z0-9_]+)'\s*\)/g)].map((m) => m[1]));
for (const m of allPhp.matchAll(/scentral_option\(\s*'(collection_)'\s*\.\s*\$n\s*\.\s*'(_\w+)'\s*\)/g)) {
	[1, 2, 3].forEach((n) => usedOptions.add(`${m[1]}${n}${m[2]}`));
}
for (const m of allPhp.matchAll(/scentral_(?:resolve_image|url_option)\(\s*'([a-z0-9_]+)'\s*,/g)) usedOptions.add(m[1]);
for (const m of allPhp.matchAll(/scentral_resolve_image\(\s*'(collection_)'\s*\.\s*\$n\s*\.\s*'(_\w+)'/g)) {
	[1, 2, 3].forEach((n) => usedOptions.add(`${m[1]}${n}${m[2]}`));
}
const unregistered = [...usedOptions].filter((k) => !registered.has(k) && !k.startsWith('social_' + 'x_'));
check(`All ${usedOptions.size} option keys are registered (${registered.size} in registry)`, unregistered.length === 0, unregistered.join(', '));

// 4. Campaign images.
const imgDir = path.join(theme, 'assets', 'images', 'campaign');
const imageNames = new Set([
	...[...allPhp.matchAll(/scentral_campaign_image\(\s*'([a-z0-9-]+)'/g)].map((m) => m[1]),
	...[...allPhp.matchAll(/scentral_resolve_image\(\s*'[a-z0-9_]+'\s*,\s*'([a-z0-9-]+)'/g)].map((m) => m[1]),
	...[...allPhp.matchAll(/'((?:campaign|collection|showcase|editorial|newsletter|promo|hero)-[a-z0-9-]+)'/g)].map((m) => m[1]),
]);
const missingImages = [...imageNames].filter((n) => !existsSync(path.join(imgDir, `${n}.webp`)));
check(`All ${imageNames.size} referenced campaign images exist`, missingImages.length === 0, missingImages.join(', '));

// 5. Required files and assets.
const required = ['style.css', 'index.php', 'functions.php', 'screenshot.png', 'theme.json', 'assets/dist/main.css', 'assets/dist/main.js', 'assets/dist/woocommerce.css', 'assets/dist/editor.css'];
const missingFiles = required.filter((f) => !existsSync(path.join(theme, f)));
check('Required theme files and compiled assets exist', missingFiles.length === 0, missingFiles.join(', '));
const header = readFileSync(path.join(theme, 'style.css'), 'utf8');
const headerFields = ['Theme Name', 'Version', 'Requires at least', 'Requires PHP', 'License', 'Text Domain'];
const missingHeader = headerFields.filter((f) => !new RegExp(`^${f}:`, 'm').test(header));
check('style.css header is complete', missingHeader.length === 0, missingHeader.join(', '));
try {
	JSON.parse(readFileSync(path.join(theme, 'theme.json'), 'utf8'));
	check('theme.json is valid JSON', true);
} catch (e) {
	check('theme.json is valid JSON', false, e.message);
}

// 6. Bundle sanity.
const bundle = readFileSync(path.join(theme, 'assets', 'dist', 'main.js'), 'utf8');
let parses = true;
try { new Function(bundle); } catch (e) { parses = false; } // eslint-disable-line no-new-func
check('main.js bundle parses', parses);
const srcJs = walk(path.join(theme, 'assets', 'src', 'js'), ['.js']).map((f) => readFileSync(f, 'utf8')).join('\n');
check('No console.log in front-end sources', !/console\.log\(/.test(srcJs));

// 7. Forbidden mentions.
const forbidden = /\b(figma|framer|claude|anthropic|openai|chatgpt|midjourney|copilot|generated by ai|ai-generated)\b/i;
const textFiles = walk(theme, ['.php', '.css', '.js', '.json', '.txt', '.md']).filter((f) => !f.includes(`${path.sep}dist${path.sep}`));
const offenders = textFiles.filter((f) => forbidden.test(readFileSync(f, 'utf8'))).map(rel);
check('No forbidden tool/vendor mentions in theme files', offenders.length === 0, offenders.join(', '));

// 8. WooCommerce-only helpers in templates that also render without WooCommerce.
const wcOnly = [...php['inc/woocommerce.php'].matchAll(/function\s+(scentral_\w+)\s*\(/g)].map((m) => m[1]);
const alwaysRendered = ['header.php', 'footer.php', 'template-parts/header/site-header.php', 'template-parts/header/announcement.php', 'template-parts/footer/site-footer.php', 'template-parts/header/mobile-menu.php', 'template-parts/header/search-overlay.php', 'page.php', 'index.php', '404.php', 'single.php', 'home.php', 'archive.php', 'search.php'];
const unguarded = [];
for (const file of alwaysRendered) {
	const src = php[file] || '';
	for (const fn of wcOnly) {
		if (src.includes(`${fn}(`) && !/\$has_wc|class_exists\(\s*'WooCommerce'\s*\)|function_exists\(\s*'wc_/.test(src)) {
			unguarded.push(`${file}: ${fn}`);
		}
	}
}
check('WooCommerce-only helpers are guarded in non-shop templates', unguarded.length === 0, unguarded.join('; '));

// Report.
let failures = 0;
for (const r of results) {
	if (!r.ok) failures += 1;
	console.info(`${r.ok ? 'PASS' : 'FAIL'}  ${r.name}${r.ok || !r.detail ? '' : `\n      -> ${r.detail}`}`);
}
console.info(`\n${results.length - failures}/${results.length} checks passed`);
process.exit(failures ? 1 : 0);
