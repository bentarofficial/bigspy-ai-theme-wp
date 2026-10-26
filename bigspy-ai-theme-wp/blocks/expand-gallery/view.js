/* CSS handles panel growth and scroll-snap; JS synchronizes touch, state and dots. */
(function () {
	'use strict';
	function init(root) {
		if (root.dataset.expandReady || root.closest('[inert]')) { return; }
		root.dataset.expandReady = '1';
		var config = JSON.parse(root.dataset.bsExpand), track = root.firstElementChild;
		var panels = Array.from(track.children).filter(function (p) { return p.classList.contains('bs-expand__panel'); });
		var media = matchMedia('(max-width: 767px)'), index = Math.min(panels.length - 1, Math.max(-1, config.open - 1)), dots = [], raf;
		function active(next) {
			index = next;
			panels.forEach(function (p, i) {
				var open = i === next;
				p.classList.toggle('is-active', open);
				var toggle = p.querySelector('.bs-expand__title');
				if (toggle) { toggle.setAttribute('aria-expanded', String(open)); }
				var content = p.querySelector('.bs-expand__content');
				if (content) { content.inert = !open; }
				if (dots[i]) { dots[i].setAttribute('aria-current', open ? 'true' : 'false'); }
			});
		}
		panels.forEach(function (panel, i) {
			panel.addEventListener('pointerenter', function (e) { if (!media.matches && e.pointerType === 'mouse') { active(i); } });
			panel.addEventListener('focusin', function () { if (!(media.matches && root.classList.contains('bs-expand--swipe'))) { active(i); } });
			panel.addEventListener('click', function (e) {
				if (e.target === panel || e.target.closest('.bs-expand__title')) { active(i); }
			});
			panel.addEventListener('keydown', function (e) {
				if (e.target !== panel) { return; }
				if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); active(i); }
				if (e.key === 'ArrowRight' || e.key === 'ArrowLeft') {
					e.preventDefault(); var n = (i + (e.key === 'ArrowRight' ? 1 : panels.length - 1)) % panels.length; panels[n].focus(); active(n);
				}
			});
		});
		track.addEventListener('pointerleave', function () { if (!config.keep && !media.matches && !track.contains(document.activeElement)) { active(-1); } });
		track.addEventListener('focusout', function (e) { if (!config.keep && !track.contains(e.relatedTarget) && !track.matches(':hover') && !media.matches) { active(-1); } });
		if (config.dots && panels.length) {
			var nav = document.createElement('div'); nav.className = 'bs-expand__dots';
			panels.forEach(function (p, i) {
				var dot = document.createElement('button'); dot.type = 'button'; dot.setAttribute('aria-label', p.getAttribute('aria-label') || String(i + 1));
				dot.addEventListener('click', function () {
					var left = i === panels.length - 1 ? track.scrollWidth - track.clientWidth : p.offsetLeft - panels[0].offsetLeft;
					track.scrollTo({ left: left, behavior: matchMedia('(prefers-reduced-motion: reduce)').matches ? 'instant' : 'smooth' });
					active(i);
				}); nav.appendChild(dot); dots.push(dot);
			}); root.appendChild(nav);
		}
		function scrolled() {
			raf = null;
			if (!media.matches || !root.classList.contains('bs-expand--swipe') || !panels.length) { return; }
			var nearEnd = track.scrollLeft >= track.scrollWidth - track.clientWidth - 2, nearest = 0, best = Infinity;
			panels.forEach(function (p, i) { var d = Math.abs(p.offsetLeft - panels[0].offsetLeft - track.scrollLeft); if (d < best) { best = d; nearest = i; } });
			active(nearEnd ? panels.length - 1 : nearest);
		}
		track.addEventListener('scroll', function () { if (!raf) { raf = requestAnimationFrame(scrolled); } }, { passive: true });
		media.addEventListener('change', function () { active(media.matches && root.classList.contains('bs-expand--swipe') ? 0 : Math.min(panels.length - 1, Math.max(-1, config.open - 1))); });
		active(media.matches && root.classList.contains('bs-expand--swipe') ? 0 : index);
	}
	function start() { document.querySelectorAll('[data-bs-expand]').forEach(init); }
	if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', start); } else { start(); }
}());
