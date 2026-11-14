/**
 * Image pipeline: campaign sources (PNG) -> optimised WebP for the theme.
 *
 *   node scripts/optimize-images.mjs
 *
 * For each source it writes, into the theme's assets/images/campaign/:
 *   {name}.webp          full size (max 1920 wide)
 *   {name}-960.webp      half size for srcset
 *   {name}-mobile.webp   4:5 portrait crop centred on the subject (landscape sources only)
 * plus an .avif twin of each (served first to browsers that support AVIF).
 * Copies of the desktop and mobile crops also go to design-assets/banners/.
 * Finally it renders the theme screenshot.png (1200 x 900).
 */
import sharp from 'sharp';
import { mkdirSync, readdirSync, copyFileSync } from 'node:fs';
import { fileURLToPath } from 'node:url';
import path from 'node:path';

const root = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '..');
const sourceDir = path.join(root, 'design-assets', 'campaign-images', 'source');
const themeDir = path.join(root, 'wordpress-theme', 'scentral-attraction');
const outDir = path.join(themeDir, 'assets', 'images', 'campaign');
const bannerDir = path.join(root, 'design-assets', 'banners');

/** Horizontal focus (0-1) of the subject in each landscape source, for mobile crops. */
const FOCUS = {
	'hero-noir-oud': 0.68,
	'showcase-luxury': 0.6,
	'campaign-bestsellers': 0.68,
	'campaign-new-arrivals': 0.74,
	'collection-men': 0.7,
	'collection-women': 0.68,
	'collection-unisex': 0.7,
	'promo-gifting': 0.5,
};
const QUALITY = 80;
const AVIF_QUALITY = 55;

/** Write the AVIF twin of a WebP output (same pixels, smaller file). */
async function avifTwin(webpPath) {
	await sharp(webpPath).avif({ quality: AVIF_QUALITY, effort: 6 }).toFile(webpPath.replace(/\.webp$/, '.avif'));
}

mkdirSync(outDir, { recursive: true });
mkdirSync(bannerDir, { recursive: true });

async function mobileCrop(file, name, meta) {
	const targetRatio = 4 / 5;
	const cropW = Math.round(meta.height * targetRatio);
	const focus = FOCUS[name] ?? 0.5;
	const left = Math.max(0, Math.min(meta.width - cropW, Math.round(meta.width * focus - cropW / 2)));
	const out = path.join(outDir, `${name}-mobile.webp`);
	await sharp(file)
		.extract({ left, top: 0, width: cropW, height: meta.height })
		.resize({ width: 900, withoutEnlargement: true })
		.webp({ quality: QUALITY, effort: 6 })
		.toFile(out);
	copyFileSync(out, path.join(bannerDir, `${name}-mobile.webp`));
	await avifTwin(out);
}

async function processImage(fileName) {
	const name = path.basename(fileName, path.extname(fileName));
	const file = path.join(sourceDir, fileName);
	const meta = await sharp(file).metadata();

	const full = path.join(outDir, `${name}.webp`);
	await sharp(file).resize({ width: 1920, withoutEnlargement: true }).webp({ quality: QUALITY, effort: 6 }).toFile(full);
	await sharp(file).resize({ width: 960 }).webp({ quality: QUALITY, effort: 6 }).toFile(path.join(outDir, `${name}-960.webp`));
	await avifTwin(full);
	await avifTwin(path.join(outDir, `${name}-960.webp`));
	copyFileSync(full, path.join(bannerDir, `${name}-desktop.webp`));

	if (meta.width > meta.height) {
		await mobileCrop(file, name, meta);
	}
	console.info(`✓ ${name} (${meta.width}×${meta.height})`);
}

async function screenshot() {
	const hero = path.join(sourceDir, 'hero-noir-oud.png');
	const overlay = Buffer.from(`
		<svg xmlns="http://www.w3.org/2000/svg" width="1200" height="900">
			<defs><linearGradient id="g" x1="0" x2="1"><stop offset="0" stop-color="#0A0A0A" stop-opacity=".9"/><stop offset=".65" stop-color="#0A0A0A" stop-opacity="0"/></linearGradient></defs>
			<rect width="1200" height="900" fill="url(#g)"/>
			<text x="80" y="120" font-family="Georgia, serif" font-size="26" letter-spacing="7" fill="#FFFFFF">SCENTRAL ATTRACTION</text>
			<text x="80" y="150" font-family="Arial, sans-serif" font-size="13" letter-spacing="5" fill="#E3151C">PERFUME &amp; MORE</text>
			<text x="80" y="560" font-family="Georgia, serif" font-size="92" font-weight="300" fill="#FFFFFF">Where elegance</text>
			<text x="80" y="660" font-family="Georgia, serif" font-size="92" font-style="italic" fill="#E3151C">meets scent.</text>
			<rect x="80" y="730" width="120" height="2" fill="#E3151C"/>
		</svg>`);
	await sharp(hero)
		.resize({ width: 1200, height: 900, fit: 'cover', position: 'right' })
		.composite([{ input: overlay }])
		.png({ compressionLevel: 9 })
		.toFile(path.join(themeDir, 'screenshot.png'));
	console.info('✓ screenshot.png');
}

const files = readdirSync(sourceDir).filter((f) => /\.(png|jpe?g)$/i.test(f));
for (const f of files) {
	await processImage(f);
}
await screenshot();
