/* Seamless ticker: one measured sequence, enough inert copies, pixels per second. */
(function () {
	'use strict';
	function init(root) {
		if (root.dataset.tickerReady || root.closest('[inert]')) { return; }
		root.dataset.tickerReady = '1';
		var config = JSON.parse(root.dataset.bsTicker), track = root.firstElementChild, sequence = track.firstElementChild;
		var reduced = matchMedia('(prefers-reduced-motion: reduce)'), animation, frame, visible = true, hovered = false, touched = false, focused = false;
		function pause() {
			if (!animation) { return; }
			if (!visible || focused || (config.pause && (hovered || touched))) { animation.pause(); }
			else { animation.play(); }
		}
		function rebuild() {
			frame = null;
			var oldTime = animation ? Number(animation.currentTime || 0) : 0;
			var oldDuration = animation ? animation.effect.getTiming().duration : 1;
			if (animation) { animation.cancel(); animation = null; }
			Array.from(track.children).slice(1).forEach(function (el) { el.remove(); });
			var vw = root.clientWidth, width = config.widths[innerWidth < 768 ? 2 : innerWidth < 1024 ? 1 : 0];
			if (!vw) { return; }
			var slideWidth = /%$/.test(width) ? (parseFloat(width) * vw / 100) + 'px' : width;
			root.style.setProperty('--bs-ticker-width', slideWidth);
			var length = sequence.getBoundingClientRect().width;
			if (!length || reduced.matches) { return; }
			var loop = config.mode === 'loop';
			if (loop) {
				// Coverage must include a viewport at BOTH ends of the entire translation.
				var copies = Math.max(2, Math.ceil((Math.max(vw * 2, vw + length)) / length));
				for (var n = 1; n < copies; n++) {
					var clone = sequence.cloneNode(true);
					clone.setAttribute('aria-hidden', 'true'); clone.setAttribute('inert', '');
					clone.removeAttribute('id');
					clone.querySelectorAll('[id]').forEach(function (el) { el.removeAttribute('id'); });
					clone.querySelectorAll('a,button,input,select,textarea,[tabindex]').forEach(function (el) { el.setAttribute('tabindex', '-1'); });
					track.appendChild(clone);
				}
			}
			var gap = parseFloat(getComputedStyle(sequence).paddingRight) || 0;
			var distance = loop ? length : Math.max(0, length - gap - vw);
			if (!distance) { return; }
			var duration = distance / Math.max(1, config.speed) * 1000;
			var frames = [{ transform: 'translate3d(0,0,0)' }, { transform: 'translate3d(' + (-distance) + 'px,0,0)' }];
			if (config.direction === 'right') { frames.reverse(); }
			animation = track.animate(frames, { duration: duration, iterations: loop ? Infinity : 1, easing: 'linear', fill: 'forwards' });
			animation.currentTime = (loop ? (oldTime / oldDuration) % 1 : Math.min(1, oldTime / oldDuration)) * duration;
			pause();
		}
		function schedule() { if (!frame) { frame = requestAnimationFrame(rebuild); } }
		root.addEventListener('pointerenter', function (e) { if (e.pointerType === 'mouse') { hovered = true; pause(); } });
		root.addEventListener('pointerleave', function () { hovered = false; pause(); });
		root.addEventListener('pointerdown', function () { touched = true; pause(); });
		['pointerup', 'pointercancel'].forEach(function (event) { window.addEventListener(event, function () { touched = false; pause(); }); });
		root.addEventListener('focusin', function () { focused = true; pause(); });
		root.addEventListener('focusout', function (e) { focused = root.contains(e.relatedTarget); pause(); });
		var resize = new ResizeObserver(schedule); resize.observe(root); resize.observe(sequence);
		new IntersectionObserver(function (entries) { visible = entries[0].isIntersecting; pause(); }).observe(root);
		root.querySelectorAll('img').forEach(function (img) { if (!img.complete) { img.addEventListener('load', schedule, { once: true }); img.addEventListener('error', schedule, { once: true }); } });
		if (document.fonts) { document.fonts.ready.then(schedule); document.fonts.addEventListener('loadingdone', schedule); }
		reduced.addEventListener('change', schedule); window.addEventListener('resize', schedule);
		schedule();
	}
	function start() { document.querySelectorAll('[data-bs-ticker]').forEach(init); }
	if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', start); } else { start(); }
}());
