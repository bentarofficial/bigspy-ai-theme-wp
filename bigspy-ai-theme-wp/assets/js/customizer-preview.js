/* BIGSPY AI Theme WP – live preview of CSS-variable options without a page refresh. */
(function (api) {
	'use strict';

	var map = window.bstPreviewVars || {};
	var root = document.documentElement;

	Object.keys(map).forEach(function (key) {
		api(key, function (setting) {
			setting.bind(function (value) {
				if (value === '' || value === null || typeof value === 'undefined') {
					root.style.removeProperty(map[key][0]);
				} else {
					root.style.setProperty(map[key][0], value + map[key][1]);
				}
			});
		});
	});
})(wp.customize);

/* Quick-edit labels (G-151): hover an element, click its label, the Customizer opens that section. */
(function (api) {
	'use strict';

	var cfg = window.bstFocus || { items: [], edit: 'Edit: %s' };
	var enabled = true;
	var bar = null;
	var current = null;
	var hideTimer = 0;

	var titles = null;

	function resolve(t) {
		if (t.t === 'url' || t.type === 'url') {
			return { type: 'url', id: t.id, label: t.l || t.label || '' };
		}
		var type = t.t || t.type;
		var title = titles && titles[type + ':' + t.id];
		if (!title) {
			return null;
		}
		return { type: type, id: t.id, label: t.l || t.label || title };
	}

	function targetsOf(el) {
		var out = [];
		if (!titles) {
			return out;
		}
		if (el.hasAttribute && el.hasAttribute('data-bst-edit')) {
			try {
				JSON.parse(el.getAttribute('data-bst-edit')).forEach(function (alts) {
					for (var i = 0; i < alts.length; i++) {
						var r = resolve(alts[i]);
						if (r) {
							out.push(r);
							break;
						}
					}
				});
			} catch (e) { /* ignore broken attribute */ }
		}
		cfg.items.forEach(function (it) {
			var r;
			try {
				r = el.matches(it.selector) && resolve(it);
			} catch (e) { r = null; /* invalid selector from a filter */ }
			if (r) {
				out.push(r);
			}
		});
		return out;
	}

	/* Matching elements from the inside out, then reversed: outermost first. */
	function chainOf(start) {
		var chain = [];
		var seen = {};
		for (var el = start; el && el.nodeType === 1 && el !== document.body; el = el.parentElement) {
			if (bar && bar.contains(el)) {
				return null;
			}
			targetsOf(el).forEach(function (t) {
				var key = t.type + ':' + t.id;
				if (!seen[key]) {
					seen[key] = 1;
					chain.push({ el: el, t: t });
				}
			});
		}
		return chain.reverse();
	}

	function ensureBar() {
		if (bar) {
			return bar;
		}
		bar = document.createElement('div');
		bar.className = 'bst-qe';
		bar.setAttribute('role', 'toolbar');
		bar.addEventListener('mouseenter', function () { clearTimeout(hideTimer); });
		bar.addEventListener('mouseleave', scheduleHide);
		bar.addEventListener('click', function (e) {
			var b = e.target.closest('button[data-i]');
			if (!b || !current) {
				return;
			}
			e.preventDefault();
			var t = current.chain[+b.getAttribute('data-i')].t;
			api.preview.send('bst-focus', { type: t.type, id: t.id });
		});
		document.body.appendChild(bar);
		return bar;
	}

	function place() {
		if (!current || !bar) {
			return;
		}
		var r = current.inner.getBoundingClientRect();
		var h = bar.offsetHeight;
		var top = r.top - h >= 0 ? r.top - h : (r.bottom + h <= window.innerHeight ? r.bottom : r.top);
		top = Math.max(0, Math.min(top, window.innerHeight - h));
		var left = Math.max(0, Math.min(r.left, window.innerWidth - bar.offsetWidth));
		bar.style.top = top + 'px';
		bar.style.left = left + 'px';
	}

	function show(chain) {
		clearTimeout(hideTimer);
		var inner = chain[chain.length - 1].el;
		if (current && current.inner === inner) {
			return;
		}
		hide();
		current = { chain: chain, inner: inner };
		ensureBar().innerHTML = '';
		chain.forEach(function (c, i) {
			if (i) {
				var sep = document.createElement('span');
				sep.className = 'bst-qe__sep';
				sep.setAttribute('aria-hidden', 'true');
				sep.textContent = '›';
				bar.appendChild(sep);
			}
			var b = document.createElement('button');
			b.type = 'button';
			b.className = 'bst-qe__btn';
			b.setAttribute('data-i', i);
			b.setAttribute('aria-label', cfg.edit.replace('%s', c.t.label));
			b.textContent = c.t.label;
			bar.appendChild(b);
		});
		bar.hidden = false;
		inner.classList.add('bst-qe-outline');
		place();
	}

	function hide() {
		clearTimeout(hideTimer);
		if (current) {
			current.inner.classList.remove('bst-qe-outline');
		}
		current = null;
		if (bar && !bar.contains(document.activeElement)) {
			bar.hidden = true;
		}
	}

	function scheduleHide() {
		clearTimeout(hideTimer);
		hideTimer = setTimeout(function () {
			if (bar && bar.contains(document.activeElement)) {
				return;
			}
			hide();
		}, 300);
	}

	function onEnter(e) {
		if (!enabled) {
			return;
		}
		var chain = chainOf(e.target);
		if (chain === null) {
			clearTimeout(hideTimer);
			return;
		}
		if (chain.length) {
			show(chain);
		} else {
			scheduleHide();
		}
	}

	document.addEventListener('mouseover', onEnter);
	document.addEventListener('focusin', onEnter);
	document.addEventListener('mouseleave', scheduleHide);
	document.addEventListener('keydown', function (e) {
		if (e.key === 'Escape' && current) {
			hide();
			if (bar) {
				bar.hidden = true;
			}
		}
	});
	window.addEventListener('scroll', place, { passive: true });
	window.addEventListener('resize', place);

	/* Section opened in the panel: scroll to and flash the matching element. */
	function findTarget(type, id) {
		var found = null;
		cfg.items.some(function (it) {
			if (it.type === type && it.id === id && resolve(it)) {
				try { found = document.querySelector(it.selector); } catch (e) { found = null; }
			}
			return !!found;
		});
		if (!found) {
			[].some.call(document.querySelectorAll('[data-bst-edit]'), function (el) {
				if (targetsOf(el).some(function (t) { return t.type === type && t.id === id; })) {
					found = el;
				}
				return !!found;
			});
		}
		return found;
	}

	api.bind('preview-ready', function () {
		api.preview.bind('bst-labels', function (data) {
			if (data && data.titles) {
				titles = data.titles;
			}
			enabled = !!(data && data.on);
			if (!enabled) {
				hide();
				if (bar) {
					bar.hidden = true;
				}
			}
		});
		api.preview.bind('bst-highlight', function (data) {
			var el = data && findTarget(data.type, data.id);
			if (!el || !el.getClientRects().length) {
				api.preview.send('bst-highlight-miss', data);
				return;
			}
			var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
			el.scrollIntoView({ behavior: reduce ? 'auto' : 'smooth', block: 'center' });
			el.classList.remove('bst-qe-flash');
			void el.offsetWidth;
			el.classList.add('bst-qe-flash');
			setTimeout(function () { el.classList.remove('bst-qe-flash'); }, 1800);
		});
		api.preview.send('bst-labels-ready');
	});
})(wp.customize);

/* G-199: live tablet/mobile values and spacing variables. */
(function (api) {
	'use strict';

	var cfg = window.bstPreviewResponsive || { vars: {}, spacing: {}, devices: {} };
	var state = {};
	var tag = null;

	function spacing(json, device) {
		var data;
		try { data = JSON.parse(json || '{}') || {}; } catch (e) { return ''; }
		var vals = data[device];
		if (!vals || vals.join('') === '') { return ''; }
		return vals.map(function (v) { return (v === '' || +v === 0) ? '0' : v + (data.unit || 'px'); }).join(' ');
	}

	function render() {
		var css = { desktop: [], tablet: [], mobile: [] };
		Object.keys(state).forEach(function (k) {
			var s = state[k];
			if (s.value !== '' && s.value !== null && s.value !== undefined) {
				css[s.device].push(s.prop + ':' + s.value);
			}
		});
		var out = css.desktop.length ? ':root{' + css.desktop.join(';') + '}' : '';
		['tablet', 'mobile'].forEach(function (d) {
			if (css[d].length) {
				out += '@media (max-width:' + cfg.devices[d].max + 'px){:root{' + css[d].join(';') + '}';
				if (d !== 'desktop') {
					['h1', 'h2', 'h3'].forEach(function (h) {
						if (state[h + '_size_' + d] && state[h + '_size_' + d].value !== '') { out += h + '{font-size:var(--bs-' + h + ')}'; }
					});
				}
				out += '}';
			}
		});
		if (!tag) {
			tag = document.createElement('style');
			tag.id = 'bst-responsive-live';
			document.head.appendChild(tag);
		}
		tag.textContent = out;
	}

	Object.keys(cfg.vars || {}).forEach(function (id) {
		['tablet', 'mobile'].forEach(function (d) {
			api(id + '_' + d, function (setting) {
				setting.bind(function (v) {
					state[id + '_' + d] = { device: d, prop: cfg.vars[id][0], value: (v === '' || v === null) ? '' : v + cfg.vars[id][1] };
					render();
				});
			});
		});
	});
	Object.keys(cfg.spacing || {}).forEach(function (id) {
		api(id, function (setting) {
			setting.bind(function (json) {
				['desktop', 'tablet', 'mobile'].forEach(function (d) {
					state[id + ':' + d] = { device: d, prop: cfg.spacing[id], value: spacing(json, d) };
				});
				render();
			});
		});
	});
})(wp.customize);
