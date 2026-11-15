/**
 * Shop behaviours: cart drawer feedback, single-product AJAX add, quick view,
 * native wishlist, recently viewed, load more, quantity steppers.
 */

import { openDialog, closeDialog, productTile } from './ui.js';

const cfg = window.ScentralConfig || {};
const i18n = cfg.i18n || {};
const WISH_KEY = 'sa-wishlist';
const RECENT_KEY = 'sa-recent';

/* -------------------------------------------------------------------------
 * Local storage helpers (fail safely in private mode)
 * ---------------------------------------------------------------------- */
function readIds(key) {
	try {
		const parsed = JSON.parse(localStorage.getItem(key) || '[]');
		return Array.isArray(parsed) ? parsed.map(Number).filter((n) => n > 0) : [];
	} catch (e) {
		return [];
	}
}
function writeIds(key, ids) {
	try {
		localStorage.setItem(key, JSON.stringify(ids));
	} catch (e) {
		/* Storage unavailable: feature degrades to session-only. */
	}
}

async function fetchProducts(ids) {
	if (!ids.length || !cfg.storeApi) return [];
	const res = await fetch(`${cfg.storeApi}products?include=${ids.join(',')}&per_page=${ids.length}`, { credentials: 'same-origin' });
	if (!res.ok) throw new Error(String(res.status));
	const products = await res.json();
	// Keep the caller's order.
	return ids.map((id) => products.find((p) => p.id === id)).filter(Boolean);
}

function bumpCount() {
	document.querySelectorAll('.cart-count').forEach((node) => {
		node.classList.remove('is-bump');
		void node.offsetWidth; // Restart the animation.
		node.classList.add('is-bump');
	});
}

/* -------------------------------------------------------------------------
 * Cart: open the drawer after AJAX add-to-cart
 * ---------------------------------------------------------------------- */
export function initCartFeedback() {
	const $ = window.jQuery;
	if (!$) return;
	$(document.body).on('added_to_cart', (event, fragments, hash, $button) => {
		bumpCount();
		if ($button && $button[0]) {
			const button = $button[0];
			button.classList.add('is-added');
			window.setTimeout(() => button.classList.remove('is-added'), 2200);
		}
		const quick = document.getElementById('quick-view');
		if (quick && quick.open) closeDialog(quick, true);
		openDialog('cart-drawer');
	});
}

/**
 * AJAX add-to-cart for simple and variable products on single product pages and quick view.
 * Falls back to the normal form submit for anything else (grouped, external, errors).
 */
export function initSingleAddToCart() {
	const $ = window.jQuery;
	if (!$ || !cfg.ajaxUrl) return;

	/** Native submit fallback that keeps the submit button's add-to-cart name/value. */
	const nativeSubmit = (form, button) => {
		form.dataset.noAjax = '1';
		if (button && typeof form.requestSubmit === 'function') {
			form.requestSubmit(button);
			return;
		}
		if (button && button.name && !form.querySelector(`input[type="hidden"][name="${button.name}"]`)) {
			const hidden = document.createElement('input');
			hidden.type = 'hidden';
			hidden.name = button.name;
			hidden.value = button.value;
			form.append(hidden);
		}
		form.submit();
	};

	document.addEventListener('submit', async (event) => {
		const form = event.target.closest('form.cart');
		if (!form || form.classList.contains('grouped_form') || form.dataset.noAjax) return;
		const button = form.querySelector('[type="submit"]');
		if (button && button.classList.contains('disabled')) return;

		const data = new FormData(form);
		const variationId = parseInt(data.get('variation_id') || '0', 10);
		const productId = variationId || parseInt(data.get('add-to-cart') || (button && button.value) || '0', 10);
		if (!productId) return;

		event.preventDefault();
		const body = new URLSearchParams();
		data.forEach((value, key) => {
			if (key !== 'add-to-cart') body.append(key, String(value));
		});
		body.set('product_id', String(productId));

		const label = button ? button.innerHTML : '';
		if (button) {
			button.classList.add('loading');
			button.textContent = i18n.adding || '';
		}
		try {
			const url = (window.wc_add_to_cart_params && window.wc_add_to_cart_params.wc_ajax_url)
				? window.wc_add_to_cart_params.wc_ajax_url.replace('%%endpoint%%', 'add_to_cart')
				: '/?wc-ajax=add_to_cart';
			const res = await fetch(url, { method: 'POST', body, credentials: 'same-origin' });
			const json = await res.json();
			if (!json || json.error) {
				// WooCommerce wants to show a notice (e.g. stock limit): submit normally.
				nativeSubmit(form, button);
				return;
			}
			$(document.body).trigger('added_to_cart', [json.fragments, json.cart_hash, $(button)]);
			$.each(json.fragments || {}, (selector, html) => { $(selector).replaceWith(html); });
		} catch (err) {
			nativeSubmit(form, button);
		} finally {
			if (button) {
				button.classList.remove('loading');
				button.innerHTML = label;
			}
		}
	});
}

/* -------------------------------------------------------------------------
 * Quick view
 * ---------------------------------------------------------------------- */
export function initQuickView() {
	const dialog = document.getElementById('quick-view');
	if (!dialog || !cfg.quickView) return;
	const body = dialog.querySelector('[data-quick-view-body]');

	document.addEventListener('click', async (event) => {
		const trigger = event.target.closest('[data-quick-view]');
		if (!trigger) return;
		event.preventDefault();
		body.classList.add('is-loading');
		body.replaceChildren();
		openDialog('quick-view', trigger);

		const form = new FormData();
		form.append('action', 'scentral_quick_view');
		form.append('nonce', cfg.nonce);
		form.append('product_id', trigger.dataset.quickView);
		try {
			const res = await fetch(cfg.ajaxUrl, { method: 'POST', body: form, credentials: 'same-origin' });
			const json = await res.json();
			if (!json.success) throw new Error('quick view');
			body.innerHTML = json.data.html; // Server-rendered, escaped WooCommerce markup.
			const $ = window.jQuery;
			const variations = body.querySelector('.variations_form');
			if ($ && variations && $.fn.wc_variation_form) $(variations).wc_variation_form();
			enhanceQuantities(body);
			syncWishButtons(body);
		} catch (err) {
			window.location.href = trigger.dataset.href || trigger.href;
		} finally {
			body.classList.remove('is-loading');
		}
	});
}

/* -------------------------------------------------------------------------
 * Native wishlist (only when no wishlist plugin is active)
 * ---------------------------------------------------------------------- */
function syncWishButtons(scope = document) {
	const ids = readIds(WISH_KEY);
	scope.querySelectorAll('[data-wish]').forEach((btn) => {
		const on = ids.includes(Number(btn.dataset.wish));
		btn.setAttribute('aria-pressed', on ? 'true' : 'false');
		btn.dataset.label = on ? (i18n.wishRemove || '') : (i18n.wishAdd || '');
	});
	document.querySelectorAll('[data-wish-count]').forEach((node) => {
		node.textContent = String(ids.length);
		node.classList.toggle('is-empty', ids.length === 0);
	});
}

async function renderWishlistPage() {
	const page = document.querySelector('[data-wishlist-page]');
	if (!page) return;
	const list = page.querySelector('[data-wishlist-list]');
	const empty = page.querySelector('[data-wishlist-empty]');
	const ids = readIds(WISH_KEY);
	list.replaceChildren();
	empty.hidden = ids.length > 0;
	if (!ids.length) return;
	try {
		const products = await fetchProducts(ids);
		products.forEach((p) => {
			const li = document.createElement('li');
			li.className = 'wish-item';
			li.append(productTile(p, 'wish-tile'));
			const remove = document.createElement('button');
			remove.type = 'button';
			remove.className = 'wish-item__remove link-draw';
			remove.dataset.wish = String(p.id);
			remove.textContent = i18n.wishRemove || '';
			li.append(remove);
			list.append(li);
		});
		empty.hidden = products.length > 0;
	} catch (err) {
		empty.hidden = false;
	}
}

export function initWishlist() {
	if (!cfg.nativeWishlist) return;
	syncWishButtons();
	renderWishlistPage();
	document.addEventListener('click', (event) => {
		const btn = event.target.closest('[data-wish]');
		if (!btn) return;
		event.preventDefault();
		const id = Number(btn.dataset.wish);
		const ids = readIds(WISH_KEY);
		const next = ids.includes(id) ? ids.filter((x) => x !== id) : [id, ...ids].slice(0, 60);
		writeIds(WISH_KEY, next);
		btn.classList.remove('is-pop');
		void btn.offsetWidth;
		btn.classList.add('is-pop');
		syncWishButtons();
		if (btn.closest('[data-wishlist-page]')) renderWishlistPage();
	});
}

/* -------------------------------------------------------------------------
 * Recently viewed (client-side so it works with full-page caching)
 * ---------------------------------------------------------------------- */
export async function initRecentlyViewed() {
	if (!cfg.recentlyViewed) return;
	const section = document.querySelector('[data-recent]');
	if (!section) return;
	const current = Number(section.dataset.current);
	const previous = readIds(RECENT_KEY).filter((id) => id !== current);
	if (current) writeIds(RECENT_KEY, [current, ...previous].slice(0, 9));
	if (!previous.length) return;
	try {
		const products = await fetchProducts(previous.slice(0, 4));
		const list = section.querySelector('[data-recent-list]');
		products.forEach((p) => {
			const li = document.createElement('li');
			li.className = 'recent-item';
			li.append(productTile(p, 'recent-tile'));
			list.append(li);
		});
		section.hidden = products.length === 0;
	} catch (err) {
		section.hidden = true;
	}
}

/* -------------------------------------------------------------------------
 * Load more (progressive: the pagination links still work without JS)
 * ---------------------------------------------------------------------- */
export function initLoadMore() {
	const button = document.querySelector('[data-load-more]');
	const grid = document.querySelector('ul.products[data-grid]');
	if (!button || !grid) return;
	button.addEventListener('click', async (event) => {
		event.preventDefault();
		const url = button.getAttribute('href');
		if (!url) return;
		button.classList.add('is-loading');
		try {
			const res = await fetch(url, { credentials: 'same-origin' });
			const doc = new DOMParser().parseFromString(await res.text(), 'text/html');
			const items = doc.querySelectorAll('ul.products[data-grid] > li');
			items.forEach((li) => {
				li.classList.add('is-visible');
				li.querySelectorAll('[data-reveal]').forEach((n) => n.classList.add('is-visible'));
				grid.append(document.importNode(li, true));
			});
			const next = doc.querySelector('[data-load-more]');
			if (next) button.setAttribute('href', next.getAttribute('href'));
			else button.remove();
			window.history.replaceState({}, '', url);
			syncWishButtons(grid);
		} catch (err) {
			window.location.href = url;
		} finally {
			button.classList.remove('is-loading');
		}
	});
}

/* -------------------------------------------------------------------------
 * Quantity steppers
 * ---------------------------------------------------------------------- */
export function enhanceQuantities(scope = document) {
	scope.querySelectorAll('.quantity:not(.is-enhanced)').forEach((wrap) => {
		const input = wrap.querySelector('input.qty');
		if (!input || input.type === 'hidden') return;
		wrap.classList.add('is-enhanced');
		const make = (dir, label) => {
			const b = document.createElement('button');
			b.type = 'button';
			b.className = `qty-step qty-step--${dir > 0 ? 'up' : 'down'}`;
			b.setAttribute('aria-label', label);
			b.textContent = dir > 0 ? '+' : '−';
			b.addEventListener('click', () => {
				const step = parseFloat(input.step) || 1;
				const min = input.min === '' ? 0 : parseFloat(input.min);
				const max = input.max === '' ? Infinity : parseFloat(input.max);
				const value = Math.min(max, Math.max(min, (parseFloat(input.value) || 0) + dir * step));
				input.value = String(value);
				input.dispatchEvent(new Event('change', { bubbles: true }));
			});
			return b;
		};
		wrap.prepend(make(-1, '−'));
		wrap.append(make(1, '+'));
	});
}

export function initQuantities() {
	enhanceQuantities();
	const $ = window.jQuery;
	if ($) $(document.body).on('updated_cart_totals updated_wc_div', () => enhanceQuantities());
}

/* -------------------------------------------------------------------------
 * Bag drawer: − / + quantity pill (0 removes the item)
 * ---------------------------------------------------------------------- */
export function initBagQuantity() {
	const $ = window.jQuery;
	document.addEventListener('click', async (event) => {
		const btn = event.target.closest('[data-bag-qty] button[data-step]');
		if (!btn || btn.disabled || !cfg.ajaxUrl) return;
		const wrap = btn.closest('[data-bag-qty]');
		const list = wrap.closest('[data-bag-nonce]');
		const qty = Math.max(0, (parseInt(wrap.dataset.qty, 10) || 0) + parseInt(btn.dataset.step, 10));
		wrap.classList.add('is-busy');
		const body = new URLSearchParams({
			action: 'scentral_update_cart_item',
			key: wrap.dataset.key,
			qty: String(qty),
			nonce: list ? list.dataset.bagNonce : '',
		});
		try {
			const res = await fetch(cfg.ajaxUrl, { method: 'POST', body, credentials: 'same-origin' });
			const json = await res.json();
			if (!json || !json.fragments) throw new Error('cart update failed');
			if ($) {
				$.each(json.fragments, (selector, html) => { $(selector).replaceWith(html); });
				// Let WooCommerce re-sync its stored fragments for the next page view.
				$(document.body).trigger('wc_fragment_refresh');
			}
			bumpCount();
		} catch (err) {
			wrap.classList.remove('is-busy');
			window.location.href = cfg.cartUrl;
		}
	});
}

/* -------------------------------------------------------------------------
 * Shop grid density (3 or 4 per row), remembered per viewer
 * ---------------------------------------------------------------------- */
const DENSITY_KEY = 'sa-shop-density';

function applyDensity(shop, value) {
	shop.classList.toggle('is-dense', value === '4');
	shop.querySelectorAll('[data-density]').forEach((b) => b.setAttribute('aria-pressed', String(b.dataset.density === value)));
}

export function initShopDensity() {
	const shop = document.querySelector('[data-shop]');
	if (!shop) return;
	let saved = '3';
	try { saved = window.localStorage.getItem(DENSITY_KEY) || '3'; } catch (e) { /* storage blocked: default grid */ }
	applyDensity(shop, saved);
	shop.addEventListener('click', (event) => {
		const button = event.target.closest('[data-density]');
		if (!button) return;
		applyDensity(shop, button.dataset.density);
		try { window.localStorage.setItem(DENSITY_KEY, button.dataset.density); } catch (e) { /* not persisted */ }
	});
}
