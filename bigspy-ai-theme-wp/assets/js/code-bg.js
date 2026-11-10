/* Canvas code backgrounds: particles network, waves, stars.
 * Pauses off screen, draws one still frame for prefers-reduced-motion,
 * and picks up blocks added later (block editor). */
(function () {
	'use strict';
	var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

	function colors(node) {
		var cs = getComputedStyle(node);
		return ['--cbg-c1', '--cbg-c2', '--cbg-c3'].map(function (v) {
			return (cs.getPropertyValue(v) || '').trim() || '#6366f1';
		});
	}

	var painters = {
		particles: function (s) {
			var n = Math.round(20 + s.density * 1.2 * Math.min(1, s.w * s.h / 900000 + .3));
			if (!s.pts || s.pts.length !== n) {
				s.pts = [];
				for (var i = 0; i < n; i++) {
					s.pts.push({ x: Math.random() * s.w, y: Math.random() * s.h, vx: (Math.random() - .5) * .6, vy: (Math.random() - .5) * .6 });
				}
			}
			var ctx = s.ctx, link = 130, p, q, i2, j, dx, dy, d;
			ctx.clearRect(0, 0, s.w, s.h);
			for (i2 = 0; i2 < s.pts.length; i2++) {
				p = s.pts[i2];
				p.x += p.vx * s.speed; p.y += p.vy * s.speed;
				if (p.x < 0 || p.x > s.w) { p.vx *= -1; }
				if (p.y < 0 || p.y > s.h) { p.vy *= -1; }
				for (j = i2 + 1; j < s.pts.length; j++) {
					q = s.pts[j]; dx = p.x - q.x; dy = p.y - q.y; d = dx * dx + dy * dy;
					if (d < link * link) {
						ctx.globalAlpha = 1 - Math.sqrt(d) / link;
						ctx.strokeStyle = s.c[1];
						ctx.lineWidth = 1;
						ctx.beginPath(); ctx.moveTo(p.x, p.y); ctx.lineTo(q.x, q.y); ctx.stroke();
					}
				}
			}
			ctx.globalAlpha = 1;
			ctx.fillStyle = s.c[0];
			for (i2 = 0; i2 < s.pts.length; i2++) {
				ctx.beginPath(); ctx.arc(s.pts[i2].x, s.pts[i2].y, 2, 0, 6.283); ctx.fill();
			}
		},
		waves: function (s) {
			var ctx = s.ctx, lines = Math.max(3, Math.round(s.density / 8)), k, x, y;
			ctx.clearRect(0, 0, s.w, s.h);
			s.t += .012 * s.speed;
			for (k = 0; k < lines; k++) {
				ctx.beginPath();
				ctx.strokeStyle = s.c[k % 3];
				ctx.globalAlpha = .25 + .5 * (k / lines);
				ctx.lineWidth = 1.5;
				for (x = 0; x <= s.w; x += 8) {
					y = s.h * (.5 + .08 * (k - lines / 2) / lines * 4) +
						Math.sin(x * .006 + s.t + k * .6) * s.h * .08 +
						Math.sin(x * .013 - s.t * 1.3 + k) * s.h * .03;
					if (x) { ctx.lineTo(x, y); } else { ctx.moveTo(x, y); }
				}
				ctx.stroke();
			}
			ctx.globalAlpha = 1;
		},
		stars: function (s) {
			var n = Math.round(s.density * 4 * Math.min(1.5, s.w * s.h / 900000 + .4)), i, st, a;
			if (!s.pts || s.pts.length !== n) {
				s.pts = [];
				for (i = 0; i < n; i++) {
					s.pts.push({ x: Math.random() * s.w, y: Math.random() * s.h, r: Math.random() * 1.4 + .3, p: Math.random() * 6.283, v: Math.random() * .3 + .05, c: i % 7 ? '#ffffff' : s.c[i % 3] });
				}
			}
			var ctx = s.ctx;
			ctx.clearRect(0, 0, s.w, s.h);
			s.t += .02 * s.speed;
			for (i = 0; i < s.pts.length; i++) {
				st = s.pts[i];
				st.y -= st.v * s.speed * .3;
				if (st.y < 0) { st.y = s.h; st.x = Math.random() * s.w; }
				a = .35 + .65 * Math.abs(Math.sin(st.p + s.t));
				ctx.globalAlpha = a;
				ctx.fillStyle = st.c;
				ctx.beginPath(); ctx.arc(st.x, st.y, st.r, 0, 6.283); ctx.fill();
			}
			ctx.globalAlpha = 1;
		}
	};

	function start(node) {
		if (node._bsCbg) { return; }
		var kind = node.getAttribute('data-bs-codebg');
		var canvas = node.querySelector('canvas');
		if (!painters[kind] || !canvas) { return; }
		var s = node._bsCbg = { ctx: canvas.getContext('2d'), t: 0, visible: true, kind: kind };
		function size() {
			var dpr = Math.min(window.devicePixelRatio || 1, 2);
			s.w = node.clientWidth || 1; s.h = node.clientHeight || 1;
			canvas.width = s.w * dpr; canvas.height = s.h * dpr;
			s.ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
			s.pts = null;
			s.speed = parseFloat(node.getAttribute('data-speed')) || 1;
			s.density = parseFloat(node.getAttribute('data-density')) || 50;
			s.c = colors(node);
			painters[kind](s);
		}
		size();
		if ('ResizeObserver' in window) { new ResizeObserver(size).observe(node); }
		if ('MutationObserver' in window) { new MutationObserver(size).observe(node, { attributes: true, attributeFilter: ['data-speed', 'data-density', 'style'] }); }
		if (reduce) { return; }
		if ('IntersectionObserver' in window) {
			new IntersectionObserver(function (e) { s.visible = e[0].isIntersecting; }).observe(node);
		}
		(function loop() {
			if (!node.isConnected) { return; }
			if (s.visible && !document.hidden) { painters[kind](s); }
			requestAnimationFrame(loop);
		})();
	}

	function scan(root) {
		(root || document).querySelectorAll('[data-bs-codebg]').forEach(start);
	}
	if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', function () { scan(); }); } else { scan(); }
	/* Block editor and late-rendered content. */
	if ('MutationObserver' in window) {
		var queued = false;
		new MutationObserver(function () {
			if (queued) { return; }
			queued = true;
			requestAnimationFrame(function () { queued = false; scan(); });
		}).observe(document.documentElement, { childList: true, subtree: true });
	}
	window.bstCodeBg = { scan: scan };
})();
