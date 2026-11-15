/**
 * Product gallery (product page + quick view) and product rails.
 *
 * Gallery: previous / next arrow buttons, thumbnails, keyboard arrows, swipe, hover zoom on the
 * product page, and variation image swaps. Listeners are delegated on the document so galleries
 * injected later (quick view) work without re-initialising.
 * Rails: previous / next buttons scroll a horizontal track and drive a progress bar.
 */

const SWIPE_PX = 40;

function galleryState(gallery) {
	const images = Array.from(gallery.querySelectorAll('.pg__img'));
	const index = Number(gallery.dataset.index || 0);
	return { images, index };
}

/**
 * Show image `index` (wraps around) in a gallery.
 *
 * @param {HTMLElement} gallery
 * @param {number} index
 */
export function showImage(gallery, index) {
	const { images } = galleryState(gallery);
	const total = images.length;
	if (!total) return;
	const next = ((index % total) + total) % total;

	images.forEach((img, i) => {
		const on = i === next;
		img.classList.toggle('is-on', on);
		img.setAttribute('aria-hidden', String(!on));
		// Load the shown image and its neighbours straight away.
		if (Math.abs(i - next) <= 1 && img.loading === 'lazy') img.loading = 'eager';
	});

	gallery.querySelectorAll('[data-gallery-go]').forEach((thumb) => {
		const on = Number(thumb.dataset.galleryGo) === next;
		thumb.classList.toggle('is-on', on);
		if (on) thumb.setAttribute('aria-current', 'true');
		else thumb.removeAttribute('aria-current');
		if (on) {
			const rail = thumb.parentElement;
			const start = thumb.offsetLeft - rail.offsetLeft;
			const top = thumb.offsetTop - rail.offsetTop;
			if (rail.scrollWidth > rail.clientWidth) rail.scrollTo({ left: start - rail.clientWidth / 2 + thumb.clientWidth / 2, behavior: 'smooth' });
			if (rail.scrollHeight > rail.clientHeight) rail.scrollTo({ top: top - rail.clientHeight / 2 + thumb.clientHeight / 2, behavior: 'smooth' });
		}
	});

	const current = gallery.querySelector('[data-gallery-current]');
	if (current) current.textContent = String(next + 1);
	gallery.dataset.index = String(next);
}

function step(gallery, delta) {
	showImage(gallery, galleryState(gallery).index + delta);
}

function onClick(event) {
	const gallery = event.target.closest('[data-gallery]');
	if (!gallery) return;
	if (event.target.closest('[data-gallery-prev]')) step(gallery, -1);
	else if (event.target.closest('[data-gallery-next]')) step(gallery, 1);
	else {
		const thumb = event.target.closest('[data-gallery-go]');
		if (thumb) showImage(gallery, Number(thumb.dataset.galleryGo));
	}
}

function onKey(event) {
	if (event.key !== 'ArrowLeft' && event.key !== 'ArrowRight') return;
	if (event.target.closest('input, textarea, select, [contenteditable]')) return;
	const gallery = event.target.closest('[data-gallery]');
	if (!gallery || !gallery.classList.contains('pg--multi')) return;
	event.preventDefault();
	step(gallery, event.key === 'ArrowRight' ? 1 : -1);
}

function initSwipe() {
	let start = null;
	document.addEventListener('pointerdown', (event) => {
		const stage = event.target.closest('.pg--multi .pg__stage');
		if (!stage || event.pointerType === 'mouse' || event.target.closest('button')) return;
		start = { x: event.clientX, y: event.clientY, gallery: stage.closest('[data-gallery]') };
	}, { passive: true });
	document.addEventListener('pointerup', (event) => {
		if (!start) return;
		const dx = event.clientX - start.x;
		const dy = event.clientY - start.y;
		if (Math.abs(dx) > SWIPE_PX && Math.abs(dx) > Math.abs(dy)) step(start.gallery, dx < 0 ? 1 : -1);
		start = null;
	}, { passive: true });
	document.addEventListener('pointercancel', () => { start = null; }, { passive: true });
}

function initZoom() {
	if (!window.matchMedia('(hover: hover) and (pointer: fine)').matches) return;
	document.addEventListener('pointermove', (event) => {
		const stage = event.target.closest('[data-gallery-zoom]');
		document.querySelectorAll('[data-gallery-zoom].is-zoom').forEach((s) => { if (s !== stage) s.classList.remove('is-zoom'); });
		if (!stage || event.target.closest('button, .pg__badge')) {
			if (stage) stage.classList.remove('is-zoom');
			return;
		}
		const box = stage.getBoundingClientRect();
		stage.style.setProperty('--zx', `${((event.clientX - box.left) / box.width) * 100}%`);
		stage.style.setProperty('--zy', `${((event.clientY - box.top) / box.height) * 100}%`);
		stage.classList.add('is-zoom');
	});
	document.addEventListener('pointerleave', () => {
		document.querySelectorAll('[data-gallery-zoom].is-zoom').forEach((s) => s.classList.remove('is-zoom'));
	});
}

/**
 * Variable products: show the chosen variation's image first (WooCommerce fires jQuery events).
 */
function initVariationImages() {
	const $ = window.jQuery;
	if (!$) return;
	const galleryFor = (form) => {
		const scope = form.closest('.qv, .product');
		return scope ? scope.querySelector('[data-gallery]') : null;
	};
	$(document).on('found_variation', 'form.variations_form', function (event, variation) {
		const gallery = galleryFor(this);
		const img = gallery ? gallery.querySelector('.pg__img[data-index="0"], .pg__img') : null;
		if (!img || !variation || !variation.image || !variation.image.src) return;
		if (!img.dataset.origSrc) {
			img.dataset.origSrc = img.getAttribute('src') || '';
			img.dataset.origSrcset = img.getAttribute('srcset') || '';
		}
		img.setAttribute('src', variation.image.src);
		if (variation.image.srcset) img.setAttribute('srcset', variation.image.srcset);
		else img.removeAttribute('srcset');
		showImage(gallery, 0);
	});
	$(document).on('reset_image', 'form.variations_form', function () {
		const gallery = galleryFor(this);
		const img = gallery ? gallery.querySelector('.pg__img[data-index="0"], .pg__img') : null;
		if (!img || !img.dataset.origSrc) return;
		img.setAttribute('src', img.dataset.origSrc);
		if (img.dataset.origSrcset) img.setAttribute('srcset', img.dataset.origSrcset);
	});
}

export function initGalleries() {
	document.addEventListener('click', onClick);
	document.addEventListener('keydown', onKey);
	initSwipe();
	initZoom();
	initVariationImages();
}

/* -------------------------------------------------------------------------
 * Rails with previous / next buttons and a progress bar
 * ---------------------------------------------------------------------- */
export function initRails() {
	document.querySelectorAll('[data-rail]').forEach((rail) => {
		const track = rail.querySelector('[data-rail-track]');
		const prev = rail.querySelector('[data-rail-prev]');
		const next = rail.querySelector('[data-rail-next]');
		const bar = rail.querySelector('[data-rail-bar]');
		if (!track) return;

		const update = () => {
			const max = track.scrollWidth - track.clientWidth;
			if (prev) prev.disabled = track.scrollLeft <= 2;
			if (next) next.disabled = track.scrollLeft >= max - 2;
			if (bar) {
				const visible = track.scrollWidth ? track.clientWidth / track.scrollWidth : 1;
				bar.style.width = `${Math.min(100, visible * 100)}%`;
				bar.style.transform = `translateX(${max > 0 ? (track.scrollLeft / max) * (1 / visible - 1) * 100 : 0}%)`;
			}
			rail.classList.toggle('is-static', max <= 2);
		};
		const page = (dir) => track.scrollBy({ left: dir * track.clientWidth * 0.85, behavior: 'smooth' });

		// Cards scrolled out sideways never cross the viewport, so reveal the whole rail at once
		// when the rail itself comes into view.
		const revealAll = () => track.querySelectorAll('[data-reveal]').forEach((n) => n.classList.add('is-visible'));
		if ('IntersectionObserver' in window) {
			const io = new IntersectionObserver((entries) => {
				if (entries.some((e) => e.isIntersecting)) { revealAll(); io.disconnect(); }
			}, { rootMargin: '0px 0px -10% 0px' });
			io.observe(track);
		} else {
			revealAll();
		}

		if (prev) prev.addEventListener('click', () => page(-1));
		if (next) next.addEventListener('click', () => page(1));
		track.addEventListener('scroll', update, { passive: true });
		window.addEventListener('resize', update);
		update();
	});
}
