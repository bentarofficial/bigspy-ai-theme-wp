/**
 * Marquee runtime (bst/marquee; reusable by any strip that uses the bs-marquee markup).
 * - pauses the CSS animation while the strip is off screen;
 * - pause / play button;
 * - repeats the items of a short row until it fills the strip (new markup only);
 * - optional scroll effects: faster with scroll speed, reversed while scrolling up.
 * Content is complete without it, and reduced motion keeps everything static.
 */
(function () {
	'use strict';
	if (window.matchMedia('(prefers-reduced-motion: reduce)').matches || !('IntersectionObserver' in window)) {
		return;
	}
	var driven = [];
	var scroll = { y: window.pageYOffset, t: 0, boost: 0, sign: 1 };

	function tracks(lane) {
		return Array.prototype.filter.call(lane.children, function (n) { return n.classList.contains('bs-marquee__track'); });
	}

	function lanesOf(m) {
		var rows = m.querySelectorAll('.bs-marquee__row');
		return rows.length ? Array.prototype.slice.call(rows) : [m];
	}

	function size(node, vertical) {
		return vertical ? node.offsetHeight : node.offsetWidth;
	}

	/* Repeat the items so a short row has no empty stretch; the speed in px/s stays the same. */
	function fill(lane, vertical) {
		var t = tracks(lane);
		var own = t[0] ? size(t[0], vertical) : 0;
		var need = size(lane, vertical);
		if (!own || own >= need) {
			return;
		}
		var times = Math.min(12, Math.ceil(need / own));
		t.forEach(function (track) {
			var kids = Array.prototype.slice.call(track.children);
			for (var i = 1; i < times; i++) {
				kids.forEach(function (kid) {
					var copy = kid.cloneNode(true);
					copy.setAttribute('aria-hidden', 'true');
					Array.prototype.forEach.call(copy.querySelectorAll('a'), function (a) { a.tabIndex = -1; });
					track.appendChild(copy);
				});
			}
		});
		var k = parseFloat(getComputedStyle(lane).getPropertyValue('--bs-mq-k')) || 1;
		lane.style.setProperty('--bs-mq-k', String(k * times));
	}

	function setup(m) {
		var vertical = m.classList.contains('bs-marquee--vertical');
		var lanes = lanesOf(m);
		if (m.classList.contains('bs-marquee--fill')) {
			lanes.forEach(function (lane) { fill(lane, vertical); });
		}

		var button = m.querySelector('.bs-marquee__toggle');
		if (button) {
			button.hidden = false;
			button.addEventListener('click', function () {
				var paused = m.classList.toggle('is-paused');
				button.setAttribute('aria-pressed', paused ? 'true' : 'false');
				button.setAttribute('aria-label', button.getAttribute(paused ? 'data-play' : 'data-pause'));
			});
		}

		var state = { el: m, visible: false, lanes: [] };
		var mode = m.getAttribute('data-bs-mq-scroll');
		if (mode) {
			state.boost = mode.indexOf('boost') > -1;
			state.reverse = mode.indexOf('reverse') > -1;
			state.hover = m.classList.contains('bs-marquee--pause');
			state.vertical = vertical;
			lanes.forEach(function (lane) {
				var t = tracks(lane);
				if (!t.length) {
					return;
				}
				// Seconds per loop as the CSS resolved it (device value and row multiplier included).
				var seconds = parseFloat(getComputedStyle(t[0]).animationDuration) || 30;
				var rev = lane === m ? m.classList.contains('bs-marquee--right') : lane.classList.contains('is-rev');
				state.lanes.push({ tracks: t, seconds: seconds, dir: rev ? -1 : 1, pos: 0 });
			});
			m.classList.add('bs-marquee--js');
			driven.push(state);
		}

		new IntersectionObserver(function (entries) {
			state.visible = entries[0].isIntersecting;
			m.classList.toggle('is-off', !state.visible);
			if (state.visible && mode) {
				start();
			}
		}, { rootMargin: '120px 0px' }).observe(m);
	}

	var running = false;
	var last = 0;

	function frame(now) {
		var dt = Math.min(64, now - last);
		last = now;
		// The boost fades out when scrolling stops.
		scroll.boost *= Math.pow(0.9, dt / 16.7);
		if (scroll.boost < 0.01) {
			scroll.boost = 0;
		}
		var active = false;
		driven.forEach(function (s) {
			if (!s.visible) {
				return;
			}
			active = true;
			if (s.el.classList.contains('is-paused') || s.el.matches(':focus-within') || (s.hover && s.el.matches(':hover'))) {
				return;
			}
			var factor = (s.boost ? 1 + scroll.boost : 1) * (s.reverse ? scroll.sign : 1);
			s.lanes.forEach(function (lane) {
				var len = size(lane.tracks[0], s.vertical);
				if (!len) {
					return;
				}
				lane.pos = (((lane.pos + lane.dir * factor * (len / lane.seconds) * dt / 1000) % len) + len) % len;
				var move = s.vertical ? 'translate3d(0,' + (-lane.pos) + 'px,0)' : 'translate3d(' + (-lane.pos) + 'px,0,0)';
				lane.tracks.forEach(function (track) { track.style.transform = move; });
			});
		});
		if (active) {
			requestAnimationFrame(frame);
		} else {
			running = false;
		}
	}

	function start() {
		if (!running) {
			running = true;
			last = performance.now();
			requestAnimationFrame(frame);
		}
	}

	function onScroll() {
		var now = performance.now();
		var y = window.pageYOffset;
		var dt = now - scroll.t;
		if (dt > 0 && dt < 250 && y !== scroll.y) {
			var v = (y - scroll.y) / dt; // px per ms
			scroll.boost = Math.min(4, Math.max(scroll.boost, Math.abs(v) * 2.5));
			scroll.sign = v < 0 ? -1 : 1;
		}
		scroll.y = y;
		scroll.t = now;
	}

	function init() {
		Array.prototype.forEach.call(document.querySelectorAll('.bs-marquee'), setup);
		if (driven.length) {
			window.addEventListener('scroll', onScroll, { passive: true });
		}
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
