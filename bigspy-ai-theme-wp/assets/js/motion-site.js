/**
 * Site-wide motion options (Customizer, off by default): magnetic buttons and a custom cursor.
 * Only on devices with a mouse, never with reduced motion. Page transitions are pure CSS.
 */
(function () {
	'use strict';
	if (window.matchMedia('(prefers-reduced-motion: reduce)').matches || !window.matchMedia('(hover: hover) and (pointer: fine)').matches) {
		return;
	}
	var body = document.body;

	if (body.classList.contains('bs-site-magnetic')) {
		document.addEventListener('pointermove', function (e) {
			var btn = e.target.closest && e.target.closest('.bs-btn, .wp-element-button, button.button, a.button');
			if (!btn) { return; }
			var r = btn.getBoundingClientRect();
			btn.style.translate = ((e.clientX - r.left - r.width / 2) * 0.2).toFixed(1) + 'px ' + ((e.clientY - r.top - r.height / 2) * 0.3).toFixed(1) + 'px';
			if (!btn.dataset.bsMag) {
				btn.dataset.bsMag = '1';
				btn.style.transition = (btn.style.transition ? btn.style.transition + ',' : '') + 'translate .25s cubic-bezier(.2,.7,.2,1)';
				btn.addEventListener('pointerleave', function () { btn.style.translate = '0 0'; });
			}
		}, { passive: true });
	}

	if (body.classList.contains('bs-site-cursor')) {
		var dot = document.createElement('div'), ring = document.createElement('div');
		dot.className = 'bs-cursor bs-cursor--dot';
		ring.className = 'bs-cursor bs-cursor--ring';
		dot.setAttribute('aria-hidden', 'true');
		ring.setAttribute('aria-hidden', 'true');
		body.appendChild(ring);
		body.appendChild(dot);
		var x = -100, y = -100, rx = -100, ry = -100, raf = null;
		function loop() {
			rx += (x - rx) * 0.2; ry += (y - ry) * 0.2;
			ring.style.transform = 'translate3d(' + rx + 'px,' + ry + 'px,0)';
			raf = Math.abs(x - rx) + Math.abs(y - ry) > 0.3 ? requestAnimationFrame(loop) : null;
		}
		document.addEventListener('pointermove', function (e) {
			x = e.clientX; y = e.clientY;
			dot.style.transform = 'translate3d(' + x + 'px,' + y + 'px,0)';
			body.classList.add('bs-cursor-on');
			ring.classList.toggle('is-link', !!(e.target.closest && e.target.closest('a, button, [role="button"], input, select, textarea, label')));
			if (!raf) { raf = requestAnimationFrame(loop); }
		}, { passive: true });
		document.addEventListener('pointerleave', function () { body.classList.remove('bs-cursor-on'); });
		document.addEventListener('pointerdown', function () { ring.classList.add('is-down'); });
		document.addEventListener('pointerup', function () { ring.classList.remove('is-down'); });
	}
})();
