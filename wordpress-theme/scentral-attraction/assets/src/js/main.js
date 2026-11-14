/**
 * Scentral Attraction — front-end entry (bundled by scripts/build.mjs).
 */

import { initPreloader } from './preloader.js';
import { initReveals, playHero, initParallax, initCounters, initBuilderCompat } from './motion.js';
import { initDialogs, initHeader, initMegaMenu, initRotator, initLiveSearch, initNewsletter, initContactForm, initPageTransitions, initBackToTop } from './ui.js';
import {
	initCartFeedback,
	initSingleAddToCart,
	initQuickView,
	initWishlist,
	initRecentlyViewed,
	initLoadMore,
	initQuantities,
	initBagQuantity,
} from './shop.js';

function safely(fn) {
	try {
		const result = fn();
		if (result && typeof result.catch === 'function') result.catch(() => {});
	} catch (err) {
		/* One feature failing must never break the rest of the page. */
	}
}

function boot() {
	[
		initDialogs,
		initHeader,
		initMegaMenu,
		initRotator,
		initLiveSearch,
		initNewsletter,
		initContactForm,
		initPageTransitions,
		initBackToTop,
		initReveals,
		initParallax,
		initCounters,
		initBuilderCompat,
		initCartFeedback,
		initSingleAddToCart,
		initQuickView,
		initWishlist,
		initRecentlyViewed,
		initLoadMore,
		initQuantities,
		initBagQuantity,
	].forEach(safely);
}

initPreloader().then(playHero, playHero);

if (document.readyState === 'loading') {
	document.addEventListener('DOMContentLoaded', boot, { once: true });
} else {
	boot();
}
