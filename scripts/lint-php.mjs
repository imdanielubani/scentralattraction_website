/**
 * Lint every PHP file in the theme with `php -l` (PHP must be on PATH).
 *   npm run lint:php
 */
import { execFileSync } from 'node:child_process';
import { readdirSync, statSync } from 'node:fs';
import { fileURLToPath } from 'node:url';
import path from 'node:path';

const theme = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '..', 'wordpress-theme', 'scentral-attraction');

function walk(dir) {
	return readdirSync(dir).flatMap((name) => {
		const full = path.join(dir, name);
		if (statSync(full).isDirectory()) return name === 'node_modules' ? [] : walk(full);
		return full.endsWith('.php') ? [full] : [];
	});
}

const files = walk(theme);
let failed = 0;
for (const file of files) {
	try {
		execFileSync('php', ['-l', file], { stdio: 'pipe' });
	} catch (err) {
		failed += 1;
		console.error(String(err.stdout || err.message));
	}
}
console.info(`PHP lint: ${files.length - failed}/${files.length} files OK`);
process.exit(failed ? 1 : 0);
