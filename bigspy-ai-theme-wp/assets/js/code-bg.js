/* Canvas code backgrounds: particles network, waves, stars, snow, dust,
 * wind, fireflies, bubbles, rain, petals, confetti, meteors.
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

	/* Falling / drifting particle presets. Each spawn() makes one particle,
	 * step() moves and draws it; particles leaving the box respawn. */
	function rnd(a, b) { return a + Math.random() * (b - a); }
	function count(s, base, mul) { return Math.round(base + s.density * mul * Math.min(1.6, s.w * s.h / 900000 + .35)); }
	function field(s, n, spawn, step, trail) {
		var i, p;
		if (!s.pts || s.pts.length !== n) {
			s.pts = [];
			for (i = 0; i < n; i++) { s.pts.push(spawn(s, true)); }
		}
		s.t += .016 * s.speed;
		if (trail) {
			s.ctx.globalCompositeOperation = 'destination-out';
			s.ctx.fillStyle = 'rgba(0,0,0,' + trail + ')';
			s.ctx.fillRect(0, 0, s.w, s.h);
			s.ctx.globalCompositeOperation = 'source-over';
		} else {
			s.ctx.clearRect(0, 0, s.w, s.h);
		}
		for (i = 0; i < s.pts.length; i++) {
			p = s.pts[i];
			if (step(s, p) === false) { s.pts[i] = spawn(s, false); }
		}
		s.ctx.globalAlpha = 1;
	}
	function out(s, p, m) { return p.x < -m || p.x > s.w + m || p.y < -m || p.y > s.h + m; }
	function dot(ctx, x, y, r) { ctx.beginPath(); ctx.arc(x, y, r, 0, 6.283); ctx.fill(); }

	Object.assign(painters, {
		/* Snow: soft flakes falling with a gentle sway. */
		snow: function (s) {
			field(s, count(s, 30, 2.2), function (s, first) {
				var r = rnd(.8, 3.2);
				return { x: rnd(0, s.w), y: first ? rnd(0, s.h) : -5, r: r, v: r * .35 + .2, p: rnd(0, 6.283), a: rnd(.4, .95) };
			}, function (s, p) {
				p.y += p.v * s.speed;
				p.x += Math.sin(s.t + p.p) * .4 * s.speed + .15 * s.speed;
				s.ctx.globalAlpha = p.a; s.ctx.fillStyle = '#ffffff';
				dot(s.ctx, p.x, p.y, p.r);
				return !out(s, p, 10);
			});
		},
		/* Dust: tiny glinting motes floating in light. */
		dust: function (s) {
			field(s, count(s, 40, 2.6), function (s) {
				return { x: rnd(0, s.w), y: rnd(0, s.h), r: rnd(.4, 1.6), vx: rnd(-.15, .25), vy: rnd(-.2, .1), p: rnd(0, 6.283), c: s.c[Math.floor(rnd(0, 3))] };
			}, function (s, p) {
				p.x += p.vx * s.speed + Math.sin(s.t * .7 + p.p) * .12;
				p.y += p.vy * s.speed + Math.cos(s.t * .5 + p.p) * .1;
				s.ctx.globalAlpha = .25 + .55 * Math.abs(Math.sin(s.t * 1.5 + p.p));
				s.ctx.fillStyle = p.c;
				dot(s.ctx, p.x, p.y, p.r);
				return !out(s, p, 10);
			});
		},
		/* Wind: thin streaks gusting sideways with a slow wave. */
		wind: function (s) {
			field(s, count(s, 8, .5), function (s, first) {
				return { x: first ? rnd(-s.w, s.w) : rnd(-s.w * .6, -40), y: rnd(0, s.h), len: rnd(60, 220), v: rnd(1.5, 4), amp: rnd(4, 18), p: rnd(0, 6.283), a: rnd(.15, .45), c: s.c[Math.floor(rnd(0, 3))] };
			}, function (s, p) {
				var ctx = s.ctx, k, x, y;
				p.x += p.v * s.speed;
				ctx.globalAlpha = p.a; ctx.strokeStyle = p.c; ctx.lineWidth = 1.2; ctx.lineCap = 'round';
				ctx.beginPath();
				for (k = 0; k <= 12; k++) {
					x = p.x - p.len * k / 12;
					y = p.y + Math.sin(x * .012 + s.t + p.p) * p.amp;
					if (k) { ctx.lineTo(x, y); } else { ctx.moveTo(x, y); }
				}
				ctx.stroke();
				return p.x - p.len < s.w + 20;
			});
		},
		/* Fireflies: glowing dots wandering and pulsing. */
		fireflies: function (s) {
			field(s, count(s, 10, .6), function (s) {
				return { x: rnd(0, s.w), y: rnd(0, s.h), a: rnd(0, 6.283), v: rnd(.2, .6), p: rnd(0, 6.283), r: rnd(1.5, 3), c: s.c[Math.floor(rnd(0, 3))] };
			}, function (s, p) {
				var ctx = s.ctx, g, glow;
				p.a += rnd(-.08, .08);
				p.x += Math.cos(p.a) * p.v * s.speed; p.y += Math.sin(p.a) * p.v * s.speed;
				if (p.x < 0 || p.x > s.w) { p.a = Math.PI - p.a; }
				if (p.y < 0 || p.y > s.h) { p.a = -p.a; }
				glow = .3 + .7 * Math.max(0, Math.sin(s.t * 1.2 + p.p));
				g = ctx.createRadialGradient(p.x, p.y, 0, p.x, p.y, p.r * 6);
				g.addColorStop(0, p.c); g.addColorStop(1, 'rgba(0,0,0,0)');
				ctx.globalAlpha = glow * .6; ctx.fillStyle = g; dot(ctx, p.x, p.y, p.r * 6);
				ctx.globalAlpha = glow; ctx.fillStyle = '#fff'; dot(ctx, p.x, p.y, p.r * .6);
				return true;
			});
		},
		/* Bubbles: outlined circles rising and wobbling. */
		bubbles: function (s) {
			field(s, count(s, 10, .7), function (s, first) {
				var r = rnd(4, 22);
				return { x: rnd(0, s.w), y: first ? rnd(0, s.h) : s.h + r, r: r, v: rnd(.3, 1) + 6 / r, p: rnd(0, 6.283), c: s.c[Math.floor(rnd(0, 3))] };
			}, function (s, p) {
				var ctx = s.ctx;
				p.y -= p.v * s.speed;
				p.x += Math.sin(s.t * 1.5 + p.p) * .5;
				ctx.globalAlpha = .5; ctx.strokeStyle = p.c; ctx.lineWidth = 1.2;
				ctx.beginPath(); ctx.arc(p.x, p.y, p.r, 0, 6.283); ctx.stroke();
				ctx.globalAlpha = .7; ctx.fillStyle = '#fff'; dot(ctx, p.x - p.r * .35, p.y - p.r * .35, p.r * .15 + .5);
				return p.y > -p.r - 5;
			});
		},
		/* Rain: slanted drops. */
		rain: function (s) {
			field(s, count(s, 30, 2.4), function (s, first) {
				return { x: rnd(-s.h * .2, s.w), y: first ? rnd(0, s.h) : rnd(-60, -10), l: rnd(8, 22), v: rnd(6, 11), a: rnd(.2, .55) };
			}, function (s, p) {
				var ctx = s.ctx;
				p.y += p.v * s.speed; p.x += p.v * .18 * s.speed;
				ctx.globalAlpha = p.a; ctx.strokeStyle = s.c[2]; ctx.lineWidth = 1;
				ctx.beginPath(); ctx.moveTo(p.x, p.y); ctx.lineTo(p.x - p.l * .18, p.y - p.l); ctx.stroke();
				return p.y - p.l < s.h;
			});
		},
		/* Petals: soft leaves tumbling down in the wind. */
		petals: function (s) {
			field(s, count(s, 8, .6), function (s, first) {
				return { x: rnd(-s.w * .2, s.w), y: first ? rnd(0, s.h) : -20, r: rnd(4, 9), v: rnd(.5, 1.2), rot: rnd(0, 6.283), vr: rnd(-.03, .03), p: rnd(0, 6.283), c: s.c[Math.floor(rnd(0, 3))] };
			}, function (s, p) {
				var ctx = s.ctx;
				p.y += p.v * s.speed; p.x += (.5 + Math.sin(s.t + p.p) * .8) * s.speed;
				p.rot += p.vr * s.speed;
				ctx.save(); ctx.translate(p.x, p.y); ctx.rotate(p.rot); ctx.scale(1, Math.abs(Math.sin(s.t * 2 + p.p)) * .6 + .4);
				ctx.globalAlpha = .75; ctx.fillStyle = p.c;
				ctx.beginPath(); ctx.ellipse(0, 0, p.r, p.r * .5, 0, 0, 6.283); ctx.fill();
				ctx.restore();
				return !out(s, p, 30);
			});
		},
		/* Confetti: colorful flakes spinning down. */
		confetti: function (s) {
			field(s, count(s, 15, 1.2), function (s, first) {
				return { x: rnd(0, s.w), y: first ? rnd(0, s.h) : -10, w: rnd(4, 9), h: rnd(2, 5), v: rnd(.8, 2), rot: rnd(0, 6.283), vr: rnd(-.1, .1), p: rnd(0, 6.283), c: s.c[Math.floor(rnd(0, 3))] };
			}, function (s, p) {
				var ctx = s.ctx;
				p.y += p.v * s.speed; p.x += Math.sin(s.t * 2 + p.p) * .8;
				p.rot += p.vr * s.speed;
				ctx.save(); ctx.translate(p.x, p.y); ctx.rotate(p.rot); ctx.scale(Math.cos(s.t * 3 + p.p), 1);
				ctx.globalAlpha = .85; ctx.fillStyle = p.c; ctx.fillRect(-p.w / 2, -p.h / 2, p.w, p.h);
				ctx.restore();
				return p.y < s.h + 15;
			});
		},
		/* Meteors: shooting stars with fading tails. */
		meteors: function (s) {
			field(s, Math.max(2, Math.round(s.density / 12)), function (s, first) {
				return { x: rnd(s.w * .1, s.w * 1.3), y: rnd(-s.h * .6, s.h * .3), v: rnd(5, 10), wait: first ? rnd(0, 200) : rnd(40, 260), c: s.c[Math.floor(rnd(0, 3))] };
			}, function (s, p) {
				var ctx = s.ctx;
				if (p.wait > 0) { p.wait -= s.speed; return true; }
				p.x -= p.v * s.speed; p.y += p.v * .55 * s.speed;
				ctx.globalAlpha = 1; ctx.fillStyle = '#fff'; dot(ctx, p.x, p.y, 1.5);
				ctx.fillStyle = p.c; dot(ctx, p.x, p.y, 1);
				return !out(s, p, 40);
			}, .12);
		}
	});

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
