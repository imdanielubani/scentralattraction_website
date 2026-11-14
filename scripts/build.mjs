/**
 * Build: bundles and minifies theme assets from assets/src into assets/dist.
 *
 *   node scripts/build.mjs           one-off production build
 *   node scripts/build.mjs --watch   rebuild on change
 */
import * as esbuild from 'esbuild';
import { existsSync, mkdirSync } from 'node:fs';
import { fileURLToPath } from 'node:url';
import path from 'node:path';

const root = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '..');
const theme = path.join(root, 'wordpress-theme', 'scentral-attraction');
const src = path.join(theme, 'assets', 'src');
const dist = path.join(theme, 'assets', 'dist');
const watch = process.argv.includes('--watch');

const entries = [
	'js/main.js',
	'css/main.css',
	'css/woocommerce.css',
	'css/editor.css',
]
	.map((rel) => path.join(src, rel))
	.filter((file) => existsSync(file));

mkdirSync(dist, { recursive: true });

const options = {
	entryPoints: entries,
	outdir: dist,
	entryNames: '[name]',
	bundle: true,
	minify: true,
	sourcemap: false,
	target: ['es2019', 'chrome90', 'safari14', 'firefox90'],
	format: 'iife',
	external: ['*.woff2', '*.webp', '*.svg'],
	logLevel: 'info',
	legalComments: 'none',
};

if (watch) {
	const ctx = await esbuild.context(options);
	await ctx.watch();
	console.info('Watching for changes…');
} else {
	await esbuild.build(options);
}
