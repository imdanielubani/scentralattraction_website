/**
 * Motion system: scroll reveals, split-text, parallax, scroll-linked zoom, counters.
 * Lightweight (IntersectionObserver + rAF). Fully disabled for reduced motion.
 */

const cfg = window.ScentralConfig || {};
const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
export const motionEnabled = !reduced && cfg.motion !== false;

function revealAll(nodes) {
	nodes.forEach((node) => node.classList.add('is-visible'));
}

function applyStagger() {
	document.querySelectorAll('[data-stagger]').forEach((group) => {
		Array.from(group.querySelectorAll('[data-reveal]')).forEach((child, i) => {
			child.style.setProperty('--stagger', String(i % 8));
		});
	});
}

export function initReveals() {
	const nodes = Array.from(document.querySelectorAll('[data-reveal], .split'))
		.filter((node) => !node.closest('[data-hero]'));

	if (!motionEnabled || !('IntersectionObserver' in window)) {
		revealAll(nodes);
		return;
	}
	applyStagger();

	const io = new IntersectionObserver((entries) => {
		entries.forEach((entry) => {
			if (!entry.isIntersecting) return;
			entry.target.classList.add('is-visible');
			io.unobserve(entry.target);
		});
	}, { rootMargin: '0px 0px -8% 0px', threshold: 0.12 });

	nodes.forEach((node) => io.observe(node));
}

/** Hero choreography starts once the preloader opens (or immediately). */
export function playHero() {
	const hero = document.querySelector('[data-hero]');
	if (!hero) return;
	const parts = hero.querySelectorAll('[data-reveal], .split');
	if (!motionEnabled) {
		revealAll(parts);
		hero.classList.add('is-in');
		return;
	}
	window.requestAnimationFrame(() => {
		hero.classList.add('is-in');
		revealAll(parts);
	});
}

export function initParallax() {
	if (!motionEnabled || cfg.parallax === false) return;
	const desktop = window.matchMedia('(min-width: 1024px)');
	const items = Array.from(document.querySelectorAll('[data-parallax]'));
	const zooms = Array.from(document.querySelectorAll('[data-zoom]'));
	if (!items.length && !zooms.length) return;

	let ticking = false;
	const update = () => {
		ticking = false;
		const vh = window.innerHeight;

		if (desktop.matches) {
			items.forEach((el) => {
				const rect = el.getBoundingClientRect();
				if (rect.bottom < -200 || rect.top > vh + 200) return;
				const speed = parseFloat(el.dataset.parallax) || 0.12;
				const offset = (rect.top + rect.height / 2 - vh / 2) * -speed;
				el.style.transform = `translate3d(0, ${offset.toFixed(1)}px, 0)`;
			});
		}

		zooms.forEach((el) => {
			const rect = el.getBoundingClientRect();
			const total = rect.height - vh;
			const raw = total > 0 ? -rect.top / total : (vh - rect.top) / (vh + rect.height);
			const p = Math.min(1, Math.max(0, raw));
			el.style.setProperty('--zoom', p.toFixed(4));
		});
	};

	const onScroll = () => {
		if (!ticking) {
			ticking = true;
			window.requestAnimationFrame(update);
		}
	};
	window.addEventListener('scroll', onScroll, { passive: true });
	window.addEventListener('resize', onScroll, { passive: true });
	update();
}

export function initCounters() {
	const nodes = document.querySelectorAll('[data-count-to]');
	if (!nodes.length) return;

	const run = (el) => {
		const to = parseInt(el.dataset.countTo, 10) || 0;
		if (!motionEnabled) {
			el.textContent = String(to);
			return;
		}
		const start = performance.now();
		const dur = 1600;
		const step = (now) => {
			const t = Math.min(1, (now - start) / dur);
			const eased = 1 - Math.pow(1 - t, 4);
			el.textContent = String(Math.round(to * eased));
			if (t < 1) window.requestAnimationFrame(step);
		};
		window.requestAnimationFrame(step);
	};

	if (!('IntersectionObserver' in window)) {
		nodes.forEach(run);
		return;
	}
	const io = new IntersectionObserver((entries) => {
		entries.forEach((entry) => {
			if (!entry.isIntersecting) return;
			run(entry.target);
			io.unobserve(entry.target);
		});
	}, { threshold: 0.5 });
	nodes.forEach((node) => io.observe(node));
}
