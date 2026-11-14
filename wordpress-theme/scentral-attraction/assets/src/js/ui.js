/**
 * UI: dialogs (native <dialog>), header states, mega menu, announcement rotator,
 * live search (WooCommerce Store API), newsletter.
 */

import { motionEnabled } from './motion.js';

const cfg = window.ScentralConfig || {};
const i18n = cfg.i18n || {};

/* -------------------------------------------------------------------------
 * Dialogs
 * ---------------------------------------------------------------------- */
let lastTrigger = null;

export function openDialog(id, trigger) {
	const dialog = document.getElementById(id);
	if (!dialog || typeof dialog.showModal !== 'function') return false;
	if (dialog.open) return true;

	lastTrigger = trigger || document.activeElement;
	document.querySelectorAll('dialog[open]').forEach((d) => closeDialog(d, true));
	dialog.classList.remove('is-closing');
	dialog.showModal();
	document.body.classList.add('has-dialog');
	if (trigger) trigger.setAttribute('aria-expanded', 'true');
	window.requestAnimationFrame(() => window.requestAnimationFrame(() => dialog.classList.add('is-open')));

	const focusTarget = dialog.querySelector('[autofocus], input[type="search"], input:not([type="hidden"]), button, a');
	if (focusTarget && id === 'search-overlay') window.setTimeout(() => focusTarget.focus(), 60);
	return true;
}

export function closeDialog(dialog, instant = false) {
	if (!dialog || !dialog.open) return;
	const done = () => {
		dialog.classList.remove('is-open', 'is-closing');
		if (dialog.open) dialog.close();
		if (!document.querySelector('dialog[open]')) document.body.classList.remove('has-dialog');
		document.querySelectorAll(`[aria-controls="${dialog.id}"]`).forEach((t) => t.setAttribute('aria-expanded', 'false'));
		if (lastTrigger && typeof lastTrigger.focus === 'function') lastTrigger.focus();
	};
	if (instant || !motionEnabled) {
		done();
		return;
	}
	dialog.classList.add('is-closing');
	dialog.classList.remove('is-open');
	window.setTimeout(done, 420);
}

export function initDialogs() {
	document.addEventListener('click', (event) => {
		const opener = event.target.closest('[data-open]');
		if (opener) {
			if (openDialog(opener.dataset.open, opener)) event.preventDefault();
			return;
		}
		const closer = event.target.closest('[data-close]');
		if (closer) {
			closeDialog(closer.closest('dialog'));
			return;
		}
		// Click on the backdrop (the dialog element itself, outside its content box).
		if (event.target instanceof HTMLDialogElement && event.target.open) {
			const rect = event.target.getBoundingClientRect();
			const inside = event.clientX >= rect.left && event.clientX <= rect.right && event.clientY >= rect.top && event.clientY <= rect.bottom;
			if (!inside) closeDialog(event.target);
		}
	});

	document.querySelectorAll('dialog.dialog').forEach((dialog) => {
		dialog.addEventListener('cancel', (event) => {
			event.preventDefault();
			closeDialog(dialog);
		});
	});

	document.querySelectorAll('.mobile-nav__list > li').forEach((li, i) => {
		const link = li.querySelector(':scope > a');
		if (link) link.style.setProperty('--i', String(i));
	});
}

/* -------------------------------------------------------------------------
 * Header: scrolled state, hide on scroll down
 * ---------------------------------------------------------------------- */
export function initHeader() {
	const header = document.querySelector('[data-header]');
	if (!header) return;
	let lastY = window.scrollY;
	let ticking = false;

	const update = () => {
		ticking = false;
		const y = window.scrollY;
		header.classList.toggle('is-scrolled', y > 12);
		if (cfg.hideHeader !== false) {
			const delta = y - lastY;
			if (y > 320 && delta > 6) header.classList.add('is-hidden');
			else if (delta < -6 || y < 120) header.classList.remove('is-hidden');
		}
		lastY = y;
	};
	window.addEventListener('scroll', () => {
		if (!ticking) {
			ticking = true;
			window.requestAnimationFrame(update);
		}
	}, { passive: true });
	update();
}

/* -------------------------------------------------------------------------
 * Mega menu (attached to the primary-menu item that links to the Shop page)
 * ---------------------------------------------------------------------- */
function normalise(url) {
	try {
		const u = new URL(url, window.location.origin);
		return u.origin + u.pathname.replace(/\/+$/, '');
	} catch (e) {
		return url;
	}
}

export function initMegaMenu() {
	const mega = document.querySelector('[data-mega]');
	const header = document.querySelector('[data-header]');
	if (!mega || !header || !cfg.shopUrl) return;

	const shop = normalise(cfg.shopUrl);
	const link = Array.from(document.querySelectorAll('.primary-nav .menu > li > a')).find((a) => normalise(a.href) === shop);
	if (!link) return;

	const item = link.parentElement;
	const toggle = document.createElement('button');
	toggle.type = 'button';
	toggle.className = 'mega-toggle';
	toggle.setAttribute('aria-expanded', 'false');
	toggle.setAttribute('aria-controls', 'mega-shop');
	toggle.setAttribute('aria-label', link.textContent.trim());
	toggle.innerHTML = '<svg class="icon icon--plus" viewBox="0 0 24 24" aria-hidden="true"><path fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" d="M12 5v14M5 12h14"/></svg>';
	item.appendChild(toggle);

	let closeTimer = null;
	const open = () => {
		window.clearTimeout(closeTimer);
		mega.hidden = false;
		item.classList.add('is-open');
		header.classList.add('mega-open');
		toggle.setAttribute('aria-expanded', 'true');
		window.requestAnimationFrame(() => mega.classList.add('is-open'));
	};
	const close = () => {
		mega.classList.remove('is-open');
		item.classList.remove('is-open');
		header.classList.remove('mega-open');
		toggle.setAttribute('aria-expanded', 'false');
		closeTimer = window.setTimeout(() => { mega.hidden = true; }, 320);
	};
	const scheduleClose = () => {
		window.clearTimeout(closeTimer);
		closeTimer = window.setTimeout(close, 180);
	};

	toggle.addEventListener('click', () => (mega.classList.contains('is-open') ? close() : open()));
	if (window.matchMedia('(hover: hover)').matches) {
		item.addEventListener('mouseenter', open);
		item.addEventListener('mouseleave', scheduleClose);
		mega.addEventListener('mouseenter', () => window.clearTimeout(closeTimer));
		mega.addEventListener('mouseleave', scheduleClose);
	}
	header.addEventListener('keydown', (event) => {
		if (event.key === 'Escape' && mega.classList.contains('is-open')) {
			close();
			toggle.focus();
		}
	});
	header.addEventListener('focusout', (event) => {
		if (!header.contains(event.relatedTarget)) close();
	});
}

/* -------------------------------------------------------------------------
 * Announcement rotator
 * ---------------------------------------------------------------------- */
export function initRotator() {
	const rotator = document.querySelector('[data-rotator]');
	if (!rotator || !motionEnabled) return;
	const items = Array.from(rotator.children);
	if (items.length < 2) return;
	let index = 0;
	let paused = false;
	rotator.addEventListener('mouseenter', () => { paused = true; });
	rotator.addEventListener('mouseleave', () => { paused = false; });
	rotator.addEventListener('focusin', () => { paused = true; });
	rotator.addEventListener('focusout', () => { paused = false; });

	window.setInterval(() => {
		if (paused || document.hidden) return;
		items[index].classList.remove('is-active');
		items[index].setAttribute('aria-hidden', 'true');
		index = (index + 1) % items.length;
		items[index].classList.add('is-active');
		items[index].removeAttribute('aria-hidden');
	}, 4500);
}

/* -------------------------------------------------------------------------
 * Live search via the public WooCommerce Store API
 * ---------------------------------------------------------------------- */
export function formatStorePrice(prices) {
	if (!prices || prices.price === undefined) return '';
	const minor = Math.pow(10, prices.currency_minor_unit || 0);
	const value = (parseInt(prices.price, 10) / minor).toLocaleString(undefined, {
		minimumFractionDigits: 0,
		maximumFractionDigits: prices.currency_minor_unit || 0,
	});
	return `${prices.currency_prefix || ''}${value}${prices.currency_suffix || ''}`;
}

function el(tag, className, text) {
	const node = document.createElement(tag);
	if (className) node.className = className;
	if (text !== undefined) node.textContent = text;
	return node;
}

export function productTile(product, className = 'search-result') {
	const link = el('a', className);
	link.href = product.permalink;
	const img = document.createElement('img');
	const image = product.images && product.images[0];
	img.src = image ? (image.thumbnail || image.src) : '';
	img.alt = image && image.alt ? image.alt : '';
	img.loading = 'lazy';
	img.width = 160;
	img.height = 200;
	const text = el('span', `${className}__text`);
	const name = el('span', `${className}__name`);
	name.textContent = new DOMParser().parseFromString(product.name, 'text/html').body.textContent || '';
	const price = el('span', `${className}__price`, product.is_in_stock === false ? `${formatStorePrice(product.prices)} · ${i18n.outOfStock || ''}` : formatStorePrice(product.prices));
	text.append(name, price);
	link.append(img, text);
	return link;
}

export function initLiveSearch() {
	const form = document.querySelector('[data-live-search]');
	const results = document.querySelector('[data-search-results]');
	if (!form || !results || !cfg.storeApi) return;
	const input = form.querySelector('input[type="search"]');
	const suggest = document.querySelector('[data-search-suggest]');
	let timer = null;
	let controller = null;

	const render = (products, query) => {
		results.replaceChildren();
		if (!products.length) {
			results.append(el('p', 'search-panel__status', i18n.noResults || ''));
			return;
		}
		const list = el('div', 'search-results');
		products.forEach((p) => list.append(productTile(p)));
		const all = el('a', 'link-arrow search-results__all', i18n.viewAll || '');
		const url = new URL(form.action);
		url.searchParams.set('s', query);
		url.searchParams.set('post_type', 'product');
		all.href = url.toString();
		results.append(list, all);
	};

	input.addEventListener('input', () => {
		const query = input.value.trim();
		window.clearTimeout(timer);
		if (suggest) suggest.hidden = query.length > 1;
		if (query.length < 2) {
			results.replaceChildren();
			return;
		}
		timer = window.setTimeout(async () => {
			if (controller) controller.abort();
			controller = new AbortController();
			try {
				const res = await fetch(`${cfg.storeApi}products?search=${encodeURIComponent(query)}&per_page=6`, { signal: controller.signal, credentials: 'same-origin' });
				if (!res.ok) throw new Error(String(res.status));
				render(await res.json(), query);
			} catch (err) {
				if (err.name !== 'AbortError') {
					results.replaceChildren(el('p', 'search-panel__status', i18n.error || ''));
				}
			}
		}, 260);
	});
}

/* -------------------------------------------------------------------------
 * Fresh nonces for forms on (possibly) page-cached HTML
 * ---------------------------------------------------------------------- */
async function refreshNonce(form, action, field) {
	const input = form.querySelector(`input[name="${field}"]`);
	if (!input || !cfg.ajaxUrl) return;
	try {
		const res = await fetch(`${cfg.ajaxUrl}?action=scentral_fresh_nonce&for=${encodeURIComponent(action)}`, { credentials: 'same-origin', cache: 'no-store' });
		const json = await res.json();
		if (json.success && json.data && json.data.nonce) input.value = json.data.nonce;
	} catch (err) {
		/* Keep the embedded nonce; it is valid unless the page is a stale cache copy. */
	}
}

export function initContactForm() {
	document.querySelectorAll('form.contact-form').forEach((form) => {
		form.addEventListener('submit', async (event) => {
			if (form.dataset.refreshed) return;
			event.preventDefault();
			await refreshNonce(form, 'scentral_contact', 'scentral_contact_nonce');
			form.dataset.refreshed = '1';
			form.submit();
		});
	});
}

/* -------------------------------------------------------------------------
 * Newsletter (AJAX enhancement of the native form)
 * ---------------------------------------------------------------------- */
export function initNewsletter() {
	document.querySelectorAll('form[data-newsletter]').forEach((form) => {
		const status = form.querySelector('.newsletter-form__status');
		form.addEventListener('submit', async (event) => {
			event.preventDefault();
			const email = form.querySelector('input[type="email"]');
			if (email && !email.checkValidity()) {
				status.textContent = email.validationMessage;
				email.focus();
				return;
			}
			form.classList.add('is-busy');
			try {
				await refreshNonce(form, 'scentral_subscribe', 'scentral_subscribe_nonce');
				const res = await fetch(cfg.ajaxUrl, { method: 'POST', body: new FormData(form), credentials: 'same-origin' });
				const json = await res.json();
				const data = json.data || {};
				status.textContent = data.message || i18n.error || '';
				if (json.success) {
					form.classList.add('is-success');
					form.reset();
				}
			} catch (err) {
				status.textContent = i18n.error || '';
			} finally {
				form.classList.remove('is-busy');
			}
		});
	});
}
