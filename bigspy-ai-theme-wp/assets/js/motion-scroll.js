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

	/* Horizontal Scroll: the wrapper is as tall as the sideways distance; the track follows vertical progress. */
	function hscroll(root) {
		var sticky = root.firstElementChild, track = sticky && sticky.firstElementChild;
		if (!track) { return; }
		var desktop = window.matchMedia('(min-width: 768px)');
		var distance = 0, ticking = false;
		var panels = Array.prototype.slice.call(track.children), dots = [], prev, next;
		function activate(i) {
			dots.forEach(function (d, n) { d.classList.toggle('is-active', n === i); });
			if (prev) { prev.disabled = i === 0; next.disabled = i === panels.length - 1; }
		}
		function current() {
			if (root.classList.contains('is-pinned')) {
				var p = -root.getBoundingClientRect().top / Math.max(1, root.offsetHeight - window.innerHeight);
				return Math.round(Math.min(1, Math.max(0, p)) * (panels.length - 1));
			}
			var max = track.scrollWidth - track.clientWidth, step = panels[1] ? panels[1].offsetLeft - panels[0].offsetLeft : 1;
			return track.scrollLeft >= max - 2 ? panels.length - 1 : Math.round(track.scrollLeft / Math.max(1, step));
		}
		function go(i) {
			var behavior = reduced ? 'auto' : 'smooth';
			if (root.classList.contains('is-pinned')) {
				var top = root.getBoundingClientRect().top + window.pageYOffset;
				window.scrollTo({ top: top + (root.offsetHeight - window.innerHeight) * i / (panels.length - 1), behavior: behavior });
			} else {
				track.scrollTo({ left: panels[i].offsetLeft - panels[0].offsetLeft, behavior: behavior });
			}
		}
		function arrow(cls, label, path, step) {
			var b = document.createElement('button');
			b.type = 'button';
			b.className = 'bs-hscroll__arrow ' + cls;
			b.setAttribute('aria-label', label);
			b.innerHTML = '<svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true"><path d="' + path + '" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
			b.addEventListener('click', function () { go(Math.min(panels.length - 1, Math.max(0, current() + step))); });
			return b;
		}
		if (panels.length > 1) {
			var nav = document.createElement('div'), list = document.createElement('div');
			nav.className = 'bs-hscroll__nav';
			list.className = 'bs-hscroll__dots';
			list.setAttribute('aria-hidden', 'true');
			dots = panels.map(function () { var d = document.createElement('span'); d.className = 'bs-hscroll__dot'; list.appendChild(d); return d; });
			prev = arrow('is-prev', root.dataset.prev || 'Previous', 'M15 5l-7 7 7 7', -1);
			next = arrow('is-next', root.dataset.next || 'Next', 'M9 5l7 7-7 7', 1);
			nav.appendChild(prev);
			nav.appendChild(list);
			nav.appendChild(next);
			sticky.appendChild(nav);
			track.addEventListener('scroll', function () { activate(current()); }, { passive: true });
			activate(0);
		}
		function measure() {
			var on = desktop.matches && !reduced;
			root.classList.toggle('is-pinned', on);
			if (!on) { root.style.height = ''; track.style.transform = ''; return; }
			distance = Math.max(0, track.scrollWidth - sticky.clientWidth);
			root.style.height = (window.innerHeight + distance) + 'px';
			update();
		}
		function update() {
			ticking = false;
			if (!root.classList.contains('is-pinned')) { return; }
			var p = Math.min(1, Math.max(0, -root.getBoundingClientRect().top / Math.max(1, root.offsetHeight - window.innerHeight)));
			track.style.transform = 'translate3d(' + (-p * distance).toFixed(1) + 'px,0,0)';
			activate(current());
		}
		function onScroll() { if (!ticking) { ticking = true; requestAnimationFrame(update); } }
		new IntersectionObserver(function (entries) {
			var inView = entries[0].isIntersecting;
			window[inView ? 'addEventListener' : 'removeEventListener']('scroll', onScroll, { passive: true });
			if (inView) { update(); }
		}).observe(root);
		new ResizeObserver(measure).observe(track);
		window.addEventListener('resize', measure);
		root.querySelectorAll('img').forEach(function (img) { if (!img.complete) { img.addEventListener('load', measure, { once: true }); } });
		measure();
	}

	/* Zoom Hero: --p goes 0 → 1 while the hero is scrolled through. */
	function zoomHero(root) {
		if (reduced) { root.style.setProperty('--p', 1); return; }
		root.classList.add('is-enhanced');
		var ticking = false;
		function update() {
			ticking = false;
			var p = Math.min(1, Math.max(0, -root.getBoundingClientRect().top / Math.max(1, root.offsetHeight - window.innerHeight)));
			root.style.setProperty('--p', p.toFixed(3));
		}
		function onScroll() { if (!ticking) { ticking = true; requestAnimationFrame(update); } }
		new IntersectionObserver(function (entries) {
			var inView = entries[0].isIntersecting;
			window[inView ? 'addEventListener' : 'removeEventListener']('scroll', onScroll, { passive: true });
			if (inView) { update(); }
		}).observe(root);
		update();
	}

	function boot() {
		document.querySelectorAll('[data-bs-hscroll]').forEach(function (el) { if (!el.dataset.bsReady) { el.dataset.bsReady = '1'; hscroll(el); } });
		document.querySelectorAll('[data-bs-zhero]').forEach(function (el) { if (!el.dataset.bsReady) { el.dataset.bsReady = '1'; zoomHero(el); } });
		document.querySelectorAll('[data-bs-story]').forEach(function (el) { if (!el.dataset.bsReady) { el.dataset.bsReady = '1'; story(el); } });
		document.querySelectorAll('[data-bs-stack]').forEach(function (el) { if (!el.dataset.bsReady) { el.dataset.bsReady = '1'; stack(el); } });
	}
	if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', boot); } else { boot(); }
})();
