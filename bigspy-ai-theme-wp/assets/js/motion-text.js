/**
 * Text motion for the Animated Text block and Heading "Text motion": the full text is already
 * in the page; this only splits and animates it. Reduced motion shows the final text.
 */
(function () {
	'use strict';
	var reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
	var CHARS = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789#%&*+';

	/** Wrap every word of the element's text nodes in a span, keeping links and inline markup. */
	function splitWords(root) {
		var words = [];
		var walker = document.createTreeWalker(root, NodeFilter.SHOW_TEXT, null);
		var nodes = [];
		while (walker.nextNode()) { nodes.push(walker.currentNode); }
		nodes.forEach(function (node) {
			if (!node.nodeValue.trim() || node.parentNode.closest('.bs-atext__words')) { return; }
			var frag = document.createDocumentFragment();
			node.nodeValue.split(/(\s+)/).forEach(function (part) {
				if (!part) { return; }
				if (/^\s+$/.test(part)) { frag.appendChild(document.createTextNode(part)); return; }
				var span = document.createElement('span');
				span.className = 'bs-atext__w';
				span.textContent = part;
				frag.appendChild(span);
				words.push(span);
			});
			node.parentNode.replaceChild(frag, node);
		});
		return words;
	}

	function whenVisible(el, fn, once) {
		var io = new IntersectionObserver(function (entries) {
			entries.forEach(function (e) {
				fn(e.isIntersecting);
				if (e.isIntersecting && once) { io.disconnect(); }
			});
		}, { rootMargin: '0px 0px -12% 0px' });
		io.observe(el);
	}

	/** Words light up with scroll position (and dim again when scrolling back). */
	function reveal(el) {
		var words = splitWords(el);
		var ticking = false;
		function update() {
			ticking = false;
			var r = el.getBoundingClientRect(), vh = window.innerHeight;
			// 0 when the text enters the lower 85% of the screen, 1 when it reaches 35%.
			var p = Math.min(1, Math.max(0, (vh * 0.85 - r.top) / (vh * 0.5 + r.height * 0.5)));
			var lit = Math.round(p * words.length);
			words.forEach(function (w, i) { w.classList.toggle('is-lit', i < lit); });
		}
		function onScroll() { if (!ticking) { ticking = true; requestAnimationFrame(update); } }
		whenVisible(el, function (inView) {
			window[inView ? 'addEventListener' : 'removeEventListener']('scroll', onScroll, { passive: true });
			if (inView) { update(); }
		});
	}

	function wordsIn(el, cfg) {
		var words = splitWords(el);
		words.forEach(function (w, i) { w.style.transitionDelay = (i * 70 / cfg.speed) + 'ms'; });
		whenVisible(el, function (inView) { if (inView) { el.classList.add('is-in'); } }, true);
	}

	function scramble(el, cfg) {
		var targets = [];
		var walker = document.createTreeWalker(el, NodeFilter.SHOW_TEXT, null);
		while (walker.nextNode()) { if (walker.currentNode.nodeValue.trim()) { targets.push({ node: walker.currentNode, text: walker.currentNode.nodeValue }); } }
		whenVisible(el, function (inView) {
			if (!inView) { return; }
			var start = performance.now(), total = 900 / cfg.speed;
			(function frame(now) {
				var p = Math.min(1, (now - start) / total);
				targets.forEach(function (t) {
					var keep = Math.floor(t.text.length * p);
					t.node.nodeValue = t.text.slice(0, keep) + t.text.slice(keep).replace(/\S/g, function () { return CHARS[Math.floor(Math.random() * CHARS.length)]; });
				});
				if (p < 1) { requestAnimationFrame(frame); }
			})(start);
		}, true);
	}

	function rotate(el, cfg) {
		var list = el.querySelector('.bs-atext__words');
		var words = list ? Array.prototype.slice.call(list.children) : [];
		if (words.length < 2) { return; }
		var i = 0, timer;
		function fit() { list.style.width = words[i].offsetWidth + 'px'; }
		function next() {
			words[i].classList.remove('is-active');
			words[i].classList.add('is-out');
			var old = words[i];
			setTimeout(function () { old.classList.remove('is-out'); }, 600);
			i = (i + 1) % words.length;
			if (i === 0 && !cfg.loop) { clearInterval(timer); }
			words[i].classList.add('is-active');
			fit();
		}
		list.classList.add('is-rotating');
		fit();
		whenVisible(el, function (inView) {
			clearInterval(timer);
			if (inView) { timer = setInterval(next, 2400 / cfg.speed); }
		});
	}

	function typewriter(el, cfg) {
		var list = el.querySelector('.bs-atext__words');
		var target = list || el.querySelector('.bs-atext__text');
		if (!target) { return; }
		var items = list ? Array.prototype.slice.call(list.children).map(function (w) { return w.textContent; }) : [target.textContent];
		var out = document.createElement('span');
		out.className = 'bs-atext__typed';
		out.setAttribute('aria-hidden', 'true');
		target.classList.add('bs-atext__source');
		target.parentNode.insertBefore(out, target.nextSibling);
		var w = 0, c = 0, deleting = false, step = 55 / cfg.speed, running = false;
		function tick() {
			if (!running) { return; }
			var word = items[w];
			c += deleting ? -1 : 1;
			out.textContent = word.slice(0, c);
			var wait = deleting ? step / 2 : step;
			if (!deleting && c === word.length) {
				if (items.length < 2 && !cfg.loop) { out.classList.add('is-done'); return; }
				if (w === items.length - 1 && !cfg.loop) { out.classList.add('is-done'); return; }
				deleting = true; wait = 1600 / cfg.speed;
			} else if (deleting && c === 0) {
				deleting = false; w = (w + 1) % items.length; wait = 300;
			}
			setTimeout(tick, wait);
		}
		whenVisible(el, function (inView) {
			if (inView && !running) { running = true; tick(); }
		}, true);
	}

	/** Big type that slides sideways as the page scrolls (direction follows the scroll). */
	function marquee(el, cfg) {
		var track = el.querySelector('.bs-atext__track');
		if (!track) { return; }
		var ticking = false;
		function update() {
			ticking = false;
			var r = el.getBoundingClientRect(), vh = window.innerHeight;
			var p = (vh - r.top) / (vh + r.height);
			track.style.transform = 'translate3d(' + (-p * 40 * cfg.speed) + '%,0,0)';
		}
		function onScroll() { if (!ticking) { ticking = true; requestAnimationFrame(update); } }
		whenVisible(el, function (inView) {
			window[inView ? 'addEventListener' : 'removeEventListener']('scroll', onScroll, { passive: true });
			if (inView) { update(); }
		});
	}

	// Gradient is pure CSS; it only needs the "is-enhanced" class to start flowing.
	var FX = { reveal: reveal, words: wordsIn, scramble: scramble, rotate: rotate, typewriter: typewriter, marquee: marquee, gradient: function () {} };

	function init(el) {
		if (el.dataset.bsTextReady) { return; }
		el.dataset.bsTextReady = '1';
		var cfg = {};
		try { cfg = JSON.parse(el.getAttribute('data-bs-text') || '{}'); } catch (e) { cfg = {}; }
		cfg.speed = cfg.speed || 1;
		if (reduced || !FX[cfg.effect]) { el.classList.add('is-static'); return; }
		el.classList.add('is-enhanced');
		FX[cfg.effect](el, cfg);
	}

	function boot() { document.querySelectorAll('[data-bs-text]').forEach(init); }
	if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', boot); } else { boot(); }
})();
