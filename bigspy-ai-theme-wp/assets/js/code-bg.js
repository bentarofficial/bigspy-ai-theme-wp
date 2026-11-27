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

	/* Ambient presets. Soft by design: depth layers (z: far = small, slow,
	 * faint), particles fade in and out over a lifetime, motion follows one
	 * slow shared breeze, and brand colors are softened toward white or a
	 * natural tint so the effect stays calm behind content. */
	function rnd(a, b) { return a + Math.random() * (b - a); }
	function count(s, base, mul) { return Math.round(base + s.density * mul * Math.min(1.6, s.w * s.h / 900000 + .35)); }
	function clamp(v) { return v < 0 ? 0 : v > 1 ? 1 : v; }
	/* Slow gusts: several long sines, roughly -1..1. */
	function breeze(t) { return Math.sin(t * .23) * .5 + Math.sin(t * .11 + 1.7) * .35 + Math.sin(t * .053 + 4) * .3; }
	/* Any CSS color → [r, g, b] (the canvas normalizes it for us). */
	function rgb(s, col) {
		s.rgbCache = s.rgbCache || {};
		if (s.rgbCache[col]) { return s.rgbCache[col]; }
		var ctx = s.ctx, v, m;
		ctx.fillStyle = '#000'; ctx.fillStyle = col; v = ctx.fillStyle;
		if (v.charAt(0) === '#') { m = [parseInt(v.substr(1, 2), 16), parseInt(v.substr(3, 2), 16), parseInt(v.substr(5, 2), 16)]; } else { m = (v.match(/[\d.]+/g) || [99, 102, 241]).slice(0, 3).map(Number); }
		s.rgbCache[col] = m;
		return m;
	}
	function mix(a, b, k) { return [a[0] + (b[0] - a[0]) * k, a[1] + (b[1] - a[1]) * k, a[2] + (b[2] - a[2]) * k]; }
	function rgba(c, a) { return 'rgba(' + Math.round(c[0]) + ',' + Math.round(c[1]) + ',' + Math.round(c[2]) + ',' + clamp(a).toFixed(3) + ')'; }
	var WHITE = [255, 255, 255];
	function tint(s, i, k, to) { return mix(rgb(s, s.c[i % 3]), to || WHITE, k); }
	/* Soft round glow. */
	function glow(ctx, x, y, r, c, a) {
		if (a <= .003 || r <= 0) { return; }
		var g = ctx.createRadialGradient(x, y, 0, x, y, r);
		g.addColorStop(0, rgba(c, a)); g.addColorStop(.4, rgba(c, a * .45)); g.addColorStop(1, rgba(c, 0));
		ctx.fillStyle = g; ctx.beginPath(); ctx.arc(x, y, r, 0, 6.283); ctx.fill();
	}
	/* Lifetime fade: 0 → 1 → 0. */
	function life(p, speed) {
		p.age += speed;
		return clamp(Math.min(p.age / p.fin, (p.life - p.age) / p.fout));
	}
	function field(s, n, spawn, step) {
		var i, p;
		if (!s.pts || s.pts.length !== n) {
			s.pts = [];
			for (i = 0; i < n; i++) { s.pts.push(spawn(s, true)); }
		}
		s.t += .016 * s.speed;
		s.wind = breeze(s.t);
		s.ctx.clearRect(0, 0, s.w, s.h);
		for (i = 0; i < s.pts.length; i++) {
			p = s.pts[i];
			if (step(s, p) === false || (p.life && p.age >= p.life)) { s.pts[i] = spawn(s, false); }
		}
		s.ctx.globalAlpha = 1;
	}
	function out(s, p, m) { return p.x < -m || p.x > s.w + m || p.y < -m || p.y > s.h + m; }
	function dot(ctx, x, y, r) { ctx.beginPath(); ctx.arc(x, y, r, 0, 6.283); ctx.fill(); }
	function aged(o, first, a, b) { o.life = rnd(a, b); o.age = first ? rnd(0, o.life) : 0; o.fin = o.fout = Math.min(120, o.life / 3); return o; }

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
		/* Dust: light motes in a sunbeam — a few big blurred bokeh far away,
		 * many tiny sparkles close by, all drifting up slowly with the air. */
		dust: function (s) {
			field(s, count(s, 25, 1.6), function (s, first) {
				var z = rnd(.15, 1), bokeh = z < .35;
				return aged({ x: rnd(0, s.w), y: rnd(0, s.h), z: z, bokeh: bokeh, r: bokeh ? rnd(10, 26) : rnd(.5, 1.6) * (.6 + z), p: rnd(0, 6.283), tw: rnd(.4, 1.1), c: tint(s, Math.floor(rnd(0, 3)), bokeh ? .55 : .75) }, first, 500, 1100);
			}, function (s, p) {
				var a = life(p, s.speed);
				p.x += (s.wind * .18 + .05) * p.z * s.speed + Math.sin(s.t * .4 + p.p) * .06;
				p.y += (-.07 * p.z + Math.cos(s.t * .3 + p.p) * .05) * s.speed;
				if (p.bokeh) {
					glow(s.ctx, p.x, p.y, p.r, p.c, .13 * a);
				} else {
					a *= .35 + .65 * Math.pow(Math.abs(Math.sin(s.t * p.tw + p.p)), 2);
					glow(s.ctx, p.x, p.y, p.r * 4, p.c, .35 * a);
					s.ctx.globalAlpha = .9 * a; s.ctx.fillStyle = rgba(WHITE, 1); dot(s.ctx, p.x, p.y, p.r * .7);
				}
				return !out(s, p, 40);
			});
		},
		/* Wind: barely-there wisps of air that swell and fade, with a few
		 * motes carried along. Tapered at both ends, never a hard line. */
		wind: function (s) {
			field(s, count(s, 6, .35) + count(s, 6, .3), function (s, first) {
				var z = rnd(.4, 1);
				if (Math.random() < .5) {
					return aged({ mote: true, x: rnd(0, s.w), y: rnd(0, s.h), z: z, p: rnd(0, 6.283), c: tint(s, 0, .8) }, first, 300, 700);
				}
				return aged({ x: rnd(-s.w * .3, s.w * .8), y: rnd(s.h * .08, s.h * .92), z: z, len: rnd(.22, .5) * s.w, amp: rnd(6, 22), p: rnd(0, 6.283), lw: rnd(.6, 1.5), c: tint(s, Math.floor(rnd(0, 3)), .82) }, first, 260, 480);
			}, function (s, p) {
				var ctx = s.ctx, a = life(p, s.speed), k, x, y, g, push = (1 + s.wind * .6) * p.z * s.speed;
				if (p.mote) {
					p.x += 1.1 * push; p.y += Math.sin(s.t * .9 + p.p) * .25;
					glow(ctx, p.x, p.y, 3 * p.z, p.c, .45 * a);
					if (p.x > s.w + 10) { p.x = -10; }
					return true;
				}
				p.x += .9 * push;
				g = ctx.createLinearGradient(p.x - p.len, 0, p.x, 0);
				g.addColorStop(0, rgba(p.c, 0)); g.addColorStop(.55, rgba(p.c, .22 * a * p.z)); g.addColorStop(1, rgba(p.c, 0));
				ctx.strokeStyle = g; ctx.lineWidth = p.lw; ctx.lineCap = 'round';
				ctx.beginPath();
				for (k = 0; k <= 24; k++) {
					x = p.x - p.len * (1 - k / 24);
					y = p.y + Math.sin(x * .007 + s.t * .8 + p.p) * p.amp * (.6 + .4 * Math.sin(s.t * .3 + p.p));
					if (k) { ctx.lineTo(x, y); } else { ctx.moveTo(x, y); }
				}
				ctx.stroke();
				return p.x - p.len < s.w;
			});
		},
		/* Fireflies: warm lights that wander slowly and breathe on and off,
		 * resting dark between glows, nearer ones larger and brighter. */
		fireflies: function (s) {
			field(s, count(s, 8, .45), function (s) {
				var z = rnd(.35, 1);
				return { x: rnd(0, s.w), y: rnd(s.h * .15, s.h), z: z, p: rnd(0, 6.283), q: rnd(0, 6.283), f1: rnd(.15, .35), f2: rnd(.12, .3), bs: rnd(.35, .8), c: tint(s, Math.floor(rnd(0, 3)), .62, [255, 222, 120]) };
			}, function (s, p) {
				var b = Math.pow(Math.max(0, Math.sin(s.t * p.bs + p.p)), 3);
				p.x += (Math.sin(s.t * p.f1 + p.p) * .32 + s.wind * .08) * p.z * s.speed;
				p.y += Math.cos(s.t * p.f2 + p.q) * .22 * p.z * s.speed;
				if (p.x < -20) { p.x = s.w + 20; } else if (p.x > s.w + 20) { p.x = -20; }
				if (p.y < -20) { p.y = s.h + 20; } else if (p.y > s.h + 20) { p.y = -20; }
				glow(s.ctx, p.x, p.y, 22 * p.z, p.c, .28 * b * p.z + .03);
				glow(s.ctx, p.x, p.y, 5 * p.z, mix(p.c, WHITE, .5), .9 * b + .08);
				return true;
			});
		},
		/* Bubbles: thin translucent soap bubbles rising slowly, swaying,
		 * with a soft rim, a highlight and a fade before the top. */
		bubbles: function (s) {
			field(s, count(s, 7, .4), function (s, first) {
				var z = rnd(.3, 1), r = rnd(5, 20) * z + 2;
				return { x0: rnd(0, s.w), x: 0, y: first ? rnd(0, s.h) : s.h + r + 5, r: r, z: z, v: .25 + .45 * z, amp: rnd(6, 20), w: rnd(.3, .7), p: rnd(0, 6.283), c: tint(s, Math.floor(rnd(0, 3)), .55) };
			}, function (s, p) {
				var ctx = s.ctx, a = clamp(p.y / (s.h * .3)) * clamp((s.h + p.r - p.y) / 40) * (.45 + .55 * p.z), g, br = 1 + Math.sin(s.t * 1.3 + p.p) * .03;
				p.y -= p.v * s.speed;
				p.x0 += s.wind * .15 * p.z * s.speed;
				p.x = p.x0 + Math.sin(s.t * p.w + p.p) * p.amp;
				g = ctx.createRadialGradient(p.x, p.y, p.r * .2, p.x, p.y, p.r * br);
				g.addColorStop(0, rgba(p.c, 0)); g.addColorStop(.75, rgba(p.c, .08 * a)); g.addColorStop(1, rgba(p.c, .32 * a));
				ctx.fillStyle = g; dot(ctx, p.x, p.y, p.r * br);
				ctx.strokeStyle = rgba(WHITE, .35 * a); ctx.lineWidth = .8;
				ctx.beginPath(); ctx.arc(p.x, p.y, p.r * br, 0, 6.283); ctx.stroke();
				glow(ctx, p.x - p.r * .38, p.y - p.r * .4, p.r * .32, WHITE, .7 * a);
				return p.y > -p.r - 5;
			});
		},
		/* Rain: fine, light drizzle in depth — far drops short and faint,
		 * near drops longer; the slant follows the breeze. */
		rain: function (s) {
			field(s, count(s, 18, 1.5), function (s, first) {
				var z = rnd(.2, 1);
				return { x: rnd(-s.w * .1, s.w * 1.1), y: first ? rnd(0, s.h) : rnd(-80, -10), z: z, l: 8 + 22 * z, v: 3 + 6 * z, c: tint(s, 2, .7) };
			}, function (s, p) {
				var ctx = s.ctx, slant = .12 + s.wind * .1, g, x2, y2;
				p.y += p.v * s.speed; p.x += p.v * slant * s.speed;
				x2 = p.x - p.l * slant; y2 = p.y - p.l;
				g = ctx.createLinearGradient(x2, y2, p.x, p.y);
				g.addColorStop(0, rgba(p.c, 0)); g.addColorStop(1, rgba(p.c, .1 + .32 * p.z));
				ctx.strokeStyle = g; ctx.lineWidth = .5 + .7 * p.z; ctx.lineCap = 'round';
				ctx.beginPath(); ctx.moveTo(x2, y2); ctx.lineTo(p.x, p.y); ctx.stroke();
				return y2 < s.h;
			});
		},
		/* Petals: real petal shapes drifting down, fluttering in 3D and
		 * carried sideways by slow gusts; colors lean to soft blossom pink. */
		petals: function (s) {
			field(s, count(s, 6, .4), function (s, first) {
				var z = rnd(.4, 1);
				return { x: rnd(-s.w * .3, s.w), y: first ? rnd(0, s.h) : rnd(-60, -15), z: z, r: rnd(5, 10) * z + 2, v: .3 + .45 * z, p: rnd(0, 6.283), rs: rnd(.3, .7), fs: rnd(.6, 1.3), spin: rnd(-.25, .25), sw: rnd(.3, .6), c: tint(s, Math.floor(rnd(0, 3)), rnd(.68, .86), [255, 190, 208]) };
			}, function (s, p) {
				var ctx = s.ctx, r = p.r, g, flip = Math.cos(s.t * p.fs + p.p);
				p.y += p.v * (.7 + .3 * Math.abs(flip)) * s.speed;
				p.x += (s.wind * .9 + .35 + Math.sin(s.t * p.sw + p.p) * .5) * p.z * s.speed;
				ctx.save();
				ctx.translate(p.x, p.y);
				ctx.rotate(Math.sin(s.t * p.rs + p.p) * 1.1 + p.spin * s.t);
				ctx.scale(.3 + .7 * Math.abs(flip), 1);
				g = ctx.createLinearGradient(0, -r, 0, r);
				g.addColorStop(0, rgba(mix(p.c, WHITE, .55), .85)); g.addColorStop(1, rgba(p.c, .85));
				ctx.globalAlpha = .55 + .4 * p.z; ctx.fillStyle = g;
				ctx.beginPath(); ctx.moveTo(0, -r);
				ctx.bezierCurveTo(r * .95, -r * .55, r * .7, r * .7, 0, r);
				ctx.bezierCurveTo(-r * .7, r * .7, -r * .95, -r * .55, -r * .25, -r * .85);
				ctx.quadraticCurveTo(-r * .1, -r * .7, 0, -r);
				ctx.fill();
				ctx.restore();
				return !out(s, p, 40);
			});
		},
		/* Confetti: a light, celebratory drift — pastel paper pieces that
		 * flutter and slow down as they turn flat, never a storm. */
		confetti: function (s) {
			field(s, count(s, 8, .55), function (s, first) {
				var z = rnd(.4, 1);
				return { x: rnd(0, s.w), y: first ? rnd(0, s.h) : rnd(-40, -8), z: z, w: rnd(5, 9) * z + 2, h: rnd(2.5, 4.5) * z + 1, round: Math.random() < .3, v: .35 + .5 * z, p: rnd(0, 6.283), fs: rnd(1.2, 2.4), rs: rnd(.4, 1), c: tint(s, Math.floor(rnd(0, 3)), .4) };
			}, function (s, p) {
				var ctx = s.ctx, flip = Math.cos(s.t * p.fs + p.p), a = clamp((p.y + 10) / 60) * clamp((s.h - p.y) / 50);
				p.y += p.v * (.45 + .55 * Math.abs(flip)) * s.speed;
				p.x += (Math.sin(s.t * .8 + p.p) * .45 + s.wind * .3) * p.z * s.speed;
				ctx.save(); ctx.translate(p.x, p.y); ctx.rotate(Math.sin(s.t * p.rs + p.p) * 1.4); ctx.scale(1, .2 + .8 * Math.abs(flip));
				ctx.globalAlpha = (.5 + .4 * p.z) * a;
				ctx.fillStyle = rgba(mix(p.c, flip > 0 ? WHITE : p.c, .25), 1);
				if (p.round) { dot(ctx, 0, 0, p.h); } else { ctx.fillRect(-p.w / 2, -p.h / 2, p.w, p.h); }
				ctx.restore();
				return p.y < s.h + 15;
			});
		},
		/* Shooting stars: a quiet night sky that twinkles, with an occasional
		 * meteor sliding across — bright head, long tail, fading in and out. */
		meteors: function (s) {
			var ctx = s.ctx, i, st, m, prog, a, tx, ty, g, n = count(s, 30, 1.4);
			if (!s.stars || s.stars.length !== n) {
				s.stars = [];
				for (i = 0; i < n; i++) { s.stars.push({ x: rnd(0, s.w), y: rnd(0, s.h), r: rnd(.3, 1.1), p: rnd(0, 6.283), tw: rnd(.3, 1.2) }); }
				s.mets = [];
				for (i = 0; i < Math.max(1, Math.round(s.density / 25)); i++) { s.mets.push({ wait: rnd(20, 200) }); }
			}
			s.t += .016 * s.speed;
			ctx.clearRect(0, 0, s.w, s.h);
			ctx.fillStyle = '#ffffff';
			for (i = 0; i < s.stars.length; i++) {
				st = s.stars[i];
				ctx.globalAlpha = .2 + .5 * Math.pow(Math.abs(Math.sin(s.t * st.tw + st.p)), 2);
				dot(ctx, st.x, st.y, st.r);
			}
			ctx.globalAlpha = 1;
			for (i = 0; i < s.mets.length; i++) {
				m = s.mets[i];
				if (m.wait > 0) {
					m.wait -= s.speed;
					if (m.wait <= 0) {
						m.ang = rnd(.42, .62); m.v = rnd(4, 6.5); m.len = rnd(110, 230); m.dist = rnd(.45, .8) * Math.max(s.w, s.h); m.d = 0;
						m.x = rnd(s.w * .25, s.w * 1.05); m.y = rnd(-s.h * .1, s.h * .45); m.c = tint(s, Math.floor(rnd(0, 3)), .7);
					}
					continue;
				}
				m.d += m.v * s.speed;
				prog = m.d / m.dist;
				if (prog >= 1) { m.wait = rnd(180, 520); continue; }
				a = Math.sin(Math.PI * prog);
				tx = m.x - Math.cos(m.ang) * m.d; ty = m.y + Math.sin(m.ang) * m.d;
				g = ctx.createLinearGradient(tx, ty, tx + Math.cos(m.ang) * m.len * a, ty - Math.sin(m.ang) * m.len * a);
				g.addColorStop(0, rgba(WHITE, .85 * a)); g.addColorStop(.3, rgba(m.c, .35 * a)); g.addColorStop(1, rgba(m.c, 0));
				ctx.strokeStyle = g; ctx.lineWidth = 1.4; ctx.lineCap = 'round';
				ctx.beginPath(); ctx.moveTo(tx, ty); ctx.lineTo(tx + Math.cos(m.ang) * m.len * a, ty - Math.sin(m.ang) * m.len * a); ctx.stroke();
				glow(ctx, tx, ty, 7, m.c, .6 * a);
			}
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
