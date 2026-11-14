/**
 * Preloader — "The First Spray".
 *
 * Progress is narrated from real signals (DOM ready, web fonts, hero image, window load)
 * blended with a gentle time curve. Never blocks longer than MAX_MS; a CSS failsafe
 * hides the overlay even if this script never runs.
 */

const MIN_MS = 1400;
const MAX_MS = 3800;
const WORD_MS = 560;
const STORAGE_KEY = 'sa-loaded';

function markSeen() {
	try {
		sessionStorage.setItem(STORAGE_KEY, '1');
	} catch (e) {
		/* Storage unavailable (private mode): the preloader simply shows again next visit. */
	}
}

function whenImageReady(img) {
	if (!img || img.complete) return Promise.resolve();
	return new Promise((resolve) => {
		img.addEventListener('load', resolve, { once: true });
		img.addEventListener('error', resolve, { once: true });
	});
}

export function initPreloader() {
	const root = document.documentElement;
	const el = document.querySelector('[data-preloader]');

	if (!el || !root.classList.contains('sa-loading')) {
		if (el) el.remove();
		root.classList.add('is-ready');
		return Promise.resolve();
	}

	const counters = el.querySelectorAll('[data-count]');
	const wordSets = Array.from(el.querySelectorAll('.preloader__words')).map((set) => Array.from(set.children));
	const start = performance.now();
	let target = 0.08;
	let progress = 0;
	let wordIndex = 0;

	const bump = (value) => {
		target = Math.max(target, value);
	};

	if (document.readyState !== 'loading') bump(0.3);
	else document.addEventListener('DOMContentLoaded', () => bump(0.3), { once: true });

	if (document.fonts && document.fonts.ready) document.fonts.ready.then(() => bump(0.55));
	whenImageReady(document.querySelector('[data-hero-image] img')).then(() => bump(0.8));
	if (document.readyState === 'complete') bump(1);
	else window.addEventListener('load', () => bump(1), { once: true });

	const wordTimer = window.setInterval(() => {
		wordSets.forEach((words) => {
			if (words.length < 2) return;
			const current = words[wordIndex % words.length];
			const next = words[(wordIndex + 1) % words.length];
			current.classList.remove('is-on');
			current.classList.add('is-past');
			next.classList.remove('is-past');
			next.classList.add('is-on');
			window.setTimeout(() => current.classList.remove('is-past'), 620);
		});
		wordIndex += 1;
	}, WORD_MS);

	return new Promise((resolve) => {
		const finish = () => {
			window.clearInterval(wordTimer);
			el.style.setProperty('--p', '1');
			counters.forEach((c) => { c.textContent = '100'; });
			el.classList.add('is-done');
			markSeen();

			window.setTimeout(() => {
				el.classList.add('is-open');
				root.classList.remove('sa-loading');
				root.classList.add('sa-intro', 'is-ready');
				resolve();
			}, 620);
			window.setTimeout(() => el.remove(), 1900);
		};

		const tick = (now) => {
			const elapsed = now - start;
			const timeCurve = Math.min(0.92, elapsed / 2600);
			const goal = elapsed >= MAX_MS ? 1 : Math.max(Math.min(target, 0.97), timeCurve * target + timeCurve * 0.25);
			progress += (Math.min(goal, 1) - progress) * 0.085;
			if (goal >= 1 && progress > 0.985) progress = 1;

			el.style.setProperty('--p', progress.toFixed(4));
			const shown = String(Math.round(progress * 100));
			counters.forEach((c) => { c.textContent = shown; });

			if (progress >= 1 && elapsed >= MIN_MS) {
				finish();
				return;
			}
			window.requestAnimationFrame(tick);
		};
		window.requestAnimationFrame(tick);
	});
}
