/**
 * Motion wave 2 runtime: Hover Reveal List, Line Draw and the "Advanced motion" options of
 * Section, Button, Image, Icon Box and Gallery. Everything is optional enhancement: content is
 * complete without it, and reduced motion keeps it static.
 */
(function () {
	'use strict';
	var reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
	var finePointer = window.matchMedia('(hover: hover) and (pointer: fine)').matches;

	function onView(el, fn, margin) {
		var io = new IntersectionObserver(function (entries) {
			entries.forEach(function (e) { if (e.isIntersecting) { fn(e.target); io.unobserve(e.target); } });
		}, { rootMargin: margin || '0px 0px -15% 0px' });
		io.observe(el);
	}

	function scrollProgress(el, cb) {
		var ticking = false;
		function update() {
			ticking = false;
			var r = el.getBoundingClientRect(), vh = window.innerHeight;
			cb(Math.min(1, Math.max(0, (vh - r.top) / (vh + r.height * 0.5))));
		}
		function onScroll() { if (!ticking) { ticking = true; requestAnimationFrame(update); } }
		new IntersectionObserver(function (entries) {
			var inView = entries[0].isIntersecting;
			window[inView ? 'addEventListener' : 'removeEventListener']('scroll', onScroll, { passive: true });
			if (inView) { update(); }
		}).observe(el);
	}

	/* Hover Reveal List: one floating image that follows the cursor and swaps per row. */
	function hoverReveal(list) {
		if (!finePointer) { return; }
		var float = document.createElement('div');
		float.className = 'bs-hreveal__float';
		float.setAttribute('aria-hidden', 'true');
		list.appendChild(float);
		var x = 0, y = 0, cx = 0, cy = 0, raf = null;
		function loop() {
			cx += (x - cx) * 0.18; cy += (y - cy) * 0.18;
			float.style.transform = 'translate3d(' + cx + 'px,' + cy + 'px,0) translate(-50%,-50%)';
			raf = Math.abs(x - cx) + Math.abs(y - cy) > 0.5 ? requestAnimationFrame(loop) : null;
		}
		list.addEventListener('pointermove', function (e) {
			var r = list.getBoundingClientRect();
			x = e.clientX - r.left; y = e.clientY - r.top;
			if (reduced) { cx = x; cy = y; float.style.transform = 'translate3d(' + x + 'px,' + y + 'px,0) translate(-50%,-50%)'; return; }
			if (!raf) { raf = requestAnimationFrame(loop); }
		});
		list.querySelectorAll('.bs-hreveal__item').forEach(function (item) {
			var img = item.querySelector('.bs-hreveal__img');
			item.addEventListener('pointerenter', function () {
				if (!img) { float.classList.remove('is-on'); return; }
				float.style.backgroundImage = 'url("' + img.currentSrc.replace(/"/g, '') + '")';
				float.classList.add('is-on');
			});
		});
		list.addEventListener('pointerleave', function () { float.classList.remove('is-on'); });
		list.classList.add('is-enhanced');
	}

	/* Line Draw: draw once on view, or follow the scroll position. */
	function lineDraw(el) {
		var path = el.querySelector('path');
		if (!path || reduced) { el.classList.add('is-drawn'); return; }
		el.classList.add('is-ready');
		if (el.getAttribute('data-bs-line') === 'scroll') {
			el.classList.add('is-scroll');
			scrollProgress(el, function (p) { path.style.strokeDashoffset = (1 - Math.min(1, p * 1.4)).toFixed(3); });
		} else {
			onView(el, function () { el.classList.add('is-drawn'); });
		}
	}

	/* Button: magnetic pull towards the cursor. */
	function magnetic(btn) {
		if (!finePointer || reduced) { return; }
		btn.addEventListener('pointermove', function (e) {
			var r = btn.getBoundingClientRect();
			btn.style.translate = ((e.clientX - r.left - r.width / 2) * 0.25).toFixed(1) + 'px ' + ((e.clientY - r.top - r.height / 2) * 0.35).toFixed(1) + 'px';
		});
		btn.addEventListener('pointerleave', function () { btn.style.translate = '0 0'; });
	}

	/* Icon Box: the stroke icon draws itself when it comes into view. */
	function iconDraw(box) {
		var paths = box.querySelectorAll('.bs-icon-box__icon svg path, .bs-icon-box__icon svg circle, .bs-icon-box__icon svg rect, .bs-icon-box__icon svg line, .bs-icon-box__icon svg polyline');
		if (!paths.length || reduced) { return; }
		paths.forEach(function (p) { p.setAttribute('pathLength', '1'); });
		box.classList.add('is-draw-ready');
		onView(box, function () { box.classList.add('is-drawn'); });
	}

	/* Image reveal and gallery stagger: start when in view. */
	function reveal(el) {
		if (reduced) { el.classList.add('is-revealed'); return; }
		el.classList.add('is-reveal-ready');
		onView(el, function () { el.classList.add('is-revealed'); });
	}

	function stagger(gallery) {
		var items = gallery.querySelectorAll('.bs-gallery__item');
		if (reduced) { return; }
		items.forEach(function (item, i) { item.style.transitionDelay = (Math.min(i, 12) * 70) + 'ms'; });
		gallery.classList.add('is-stagger-ready');
		onView(gallery, function () { gallery.classList.add('is-staggered'); });
	}

	/* Section: page background follows the section in the middle of the screen. */
	function scrollBackgrounds(sections) {
		if (!sections.length) { return; }
		var root = document.documentElement;
		var io = new IntersectionObserver(function (entries) {
			entries.forEach(function (e) {
				if (e.isIntersecting) { root.style.setProperty('--bs-page-bg', e.target.getAttribute('data-bs-scroll-bg')); document.body.classList.add('bs-has-scroll-bg'); }
			});
		}, { rootMargin: '-45% 0px -45% 0px' });
		sections.forEach(function (s) { io.observe(s); });
	}

	function boot() {
		document.querySelectorAll('[data-bs-hreveal]:not([data-bs-ready])').forEach(function (el) { el.setAttribute('data-bs-ready', '1'); hoverReveal(el); });
		document.querySelectorAll('[data-bs-line]:not([data-bs-ready])').forEach(function (el) { el.setAttribute('data-bs-ready', '1'); lineDraw(el); });
		document.querySelectorAll('.bs-mx-magnetic:not([data-bs-ready])').forEach(function (el) { el.setAttribute('data-bs-ready', '1'); magnetic(el.matches('a,button') ? el : (el.querySelector('a,button') || el)); });
		document.querySelectorAll('.bs-mx-draw:not([data-bs-ready])').forEach(function (el) { el.setAttribute('data-bs-ready', '1'); iconDraw(el); });
		document.querySelectorAll('[class*="bs-mx-reveal-"]:not([data-bs-ready])').forEach(function (el) { el.setAttribute('data-bs-ready', '1'); reveal(el); });
		document.querySelectorAll('.bs-mx-stagger:not([data-bs-ready])').forEach(function (el) { el.setAttribute('data-bs-ready', '1'); stagger(el); });
		scrollBackgrounds(Array.prototype.slice.call(document.querySelectorAll('[data-bs-scroll-bg]')));
	}
	if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', boot); } else { boot(); }
})();
