/**
 * Scroll Story and Stacking Cards. Neither blocks or takes over scrolling: CSS sticky does the
 * layout, this script only marks the active story step and adds the card depth effect.
 */
(function () {
	'use strict';
	var reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

	function story(root) {
		var steps = Array.prototype.slice.call(root.querySelectorAll(':scope > .bs-story__steps > .bs-story__step'));
		var media = Array.prototype.slice.call(root.querySelectorAll('.bs-story__media-item'));
		var dots = Array.prototype.slice.call(root.querySelectorAll('.bs-story__dot'));
		if (!steps.length) { return; }
		function activate(i) {
			steps.forEach(function (s, n) { s.classList.toggle('is-active', n === i); });
			media.forEach(function (m, n) { m.classList.toggle('is-active', n === i); });
			dots.forEach(function (d, n) { d.classList.toggle('is-active', n === i); });
		}
		// A step is active while it crosses the middle band of the screen.
		var io = new IntersectionObserver(function (entries) {
			entries.forEach(function (e) { if (e.isIntersecting) { activate(steps.indexOf(e.target)); } });
		}, { rootMargin: '-45% 0px -45% 0px' });
		steps.forEach(function (s) { io.observe(s); });
		activate(0);
	}

	function stack(root) {
		var cards = Array.prototype.slice.call(root.querySelectorAll(':scope > .bs-stack__card'));
		cards.forEach(function (c, i) { c.style.setProperty('--i', i); });
		if (reduced || !root.classList.contains('bs-stack--scale') || cards.length < 2) { return; }
		var ticking = false;
		function update() {
			ticking = false;
			cards.forEach(function (card, i) {
				var next = cards[i + 1];
				if (!next) { card.style.setProperty('--p', 0); return; }
				var a = card.getBoundingClientRect(), b = next.getBoundingClientRect();
				// How far the next card has slid over this one (0..1).
				var p = Math.min(1, Math.max(0, (a.bottom - b.top) / Math.max(1, a.height)));
				card.style.setProperty('--p', p.toFixed(3));
			});
		}
		function onScroll() { if (!ticking) { ticking = true; requestAnimationFrame(update); } }
		new IntersectionObserver(function (entries) {
			var inView = entries[0].isIntersecting;
			window[inView ? 'addEventListener' : 'removeEventListener']('scroll', onScroll, { passive: true });
			if (inView) { update(); }
		}).observe(root);
	}

	function boot() {
		document.querySelectorAll('[data-bs-story]').forEach(function (el) { if (!el.dataset.bsReady) { el.dataset.bsReady = '1'; story(el); } });
		document.querySelectorAll('[data-bs-stack]').forEach(function (el) { if (!el.dataset.bsReady) { el.dataset.bsReady = '1'; stack(el); } });
	}
	if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', boot); } else { boot(); }
})();
