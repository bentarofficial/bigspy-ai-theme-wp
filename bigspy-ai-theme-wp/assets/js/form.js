/* BIGSPY AI Theme WP – Form block behaviour, loaded only on pages with a Form block (G-41):
   Contact Form 7 redirect/thank-you, built-in form conditions (G-57), live price (G-58) and AJAX submit. */
/* Form block: redirect, thank-you message and hide-on-success after Contact Form 7 sends (1.51). */
document.addEventListener('wpcf7mailsent', function (e) {
	var box = e.target && e.target.closest && e.target.closest('.bs-form');
	if (!box) {
		return;
	}
	var ok = box.querySelector('.bs-form__success');
	if (ok) {
		ok.hidden = false;
	}
	if (box.getAttribute('data-bs-hide-on-success') === '1') {
		var body = box.querySelector('.bs-form__body');
		if (body) {
			body.hidden = true;
		}
	}
	var url = e.detail && e.detail.native ? '' : box.getAttribute('data-bs-redirect');
	if (url) {
		setTimeout(function () { window.location.href = url; }, parseFloat(box.getAttribute('data-bs-redirect-delay') || '0') * 1000);
	}
}, false);

/* Built-in form conditions (G-57): show/hide fields by other fields' values, same rules as the server. */
(function () {
	var valueOf = function (form, id) {
		var wrap = form.querySelector('[data-bs-fid="' + id + '"]');
		if (!wrap || wrap.hidden) {
			return '';
		}
		var list = [];
		wrap.querySelectorAll('input, select, textarea').forEach(function (c) {
			if ((c.type === 'checkbox' || c.type === 'radio') ? c.checked : c.type !== 'file') {
				var v = String(c.value).trim();
				if (v !== '') {
					list.push(v);
				}
			}
		});
		return list.join(', ');
	};
	var match = function (r, value) {
		var parts = value === '' ? [] : value.split(', ');
		var num = function (x) { return x !== '' && !isNaN(x); };
		switch (r.op) {
			case 'empty': return value === '';
			case 'not_empty': return value !== '';
			case 'is_not': return parts.indexOf(r.value) === -1 && value !== r.value;
			case 'contains': return r.value !== '' && value.toLowerCase().indexOf(String(r.value).toLowerCase()) !== -1;
			case 'gt': return num(value) && num(r.value) && parseFloat(value) > parseFloat(r.value);
			case 'lt': return num(value) && num(r.value) && parseFloat(value) < parseFloat(r.value);
			default: return parts.indexOf(r.value) !== -1 || value === r.value;
		}
	};
	var apply = function (form) {
		var wraps = form.querySelectorAll('[data-bs-cond]');
		// Repeat so chains settle (a field hidden by another hidden field).
		for (var pass = 0; pass <= wraps.length; pass++) {
			var changed = false;
			wraps.forEach(function (w) {
				var c;
				try { c = JSON.parse(w.getAttribute('data-bs-cond')); } catch (e) { return; }
				var hits = (c.rules || []).map(function (r) { return match(r, valueOf(form, r.field)); });
				var met = c.match === 'any' ? hits.indexOf(true) !== -1 : hits.indexOf(false) === -1;
				var show = c.action === 'hide' ? !met : met;
				if (w.hidden === show) {
					w.hidden = !show;
					changed = true;
				}
				w.querySelectorAll('input, select, textarea').forEach(function (ctl) { ctl.disabled = !show; });
			});
			if (!changed) {
				break;
			}
		}
	};
	var all = function () { document.querySelectorAll('form[data-bs-native-form]').forEach(apply); };
	var on = function (e) {
		var form = e.target && e.target.closest && e.target.closest('form[data-bs-native-form]');
		if (form && form.querySelector('[data-bs-cond]')) {
			apply(form);
		}
	};
	document.addEventListener('input', on);
	document.addEventListener('change', on);
	document.addEventListener('reset', function (e) { if (e.target.matches && e.target.matches('form[data-bs-native-form]')) { setTimeout(function () { apply(e.target); }, 0); } }, true);
	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', all);
	} else {
		all();
	}
})();

/* Built-in form price (G-58): live preview with the same formula rules as inc/form-calc.php. The server recomputes on submit. */
var bstCalc = (function () {
	var FN = { min: [1, 20], max: [1, 20], round: [1, 2], ceil: [1, 1], floor: [1, 1], abs: [1, 1], 'if': [3, 3] };
	var LEVELS = [['||'], ['&&'], ['==', '!=', '>', '<', '>=', '<='], ['+', '-'], ['*', '/']];
	function tokens(src) {
		src = String(src || '');
		if (src.length > 500) { throw new Error('too_long'); }
		var out = [], m, rest;
		for (var i = 0; i < src.length;) {
			rest = src.slice(i);
			if ((m = /^\s+/.exec(rest))) { i += m[0].length; continue; }
			if ((m = /^\d+(?:\.\d+)?/.exec(rest))) { out.push(['n', parseFloat(m[0])]); }
			else if ((m = /^\{([a-z][a-z0-9_]{1,31})\}/.exec(rest))) { out.push(['r', m[1]]); }
			else if ((m = /^[a-z]+/.exec(rest))) { out.push(['f', m[0]]); }
			else if ((m = /^(>=|<=|==|!=|&&|\|\||[-+*\/(),<>])/.exec(rest))) { out.push(['o', m[0]]); }
			else { throw new Error('bad_char'); }
			i += m[0].length;
		}
		return out;
	}
	function parse(src) {
		try {
			var t = tokens(src);
			if (!t.length) { return { error: 'empty' }; }
			var p = { t: t, i: 0, depth: 0 };
			var ast = level(p, 0);
			return p.i < t.length ? { error: 'syntax' } : { ast: ast };
		} catch (e) { return { error: e.message }; }
	}
	function isOp(tok, op) { return tok && tok[0] === 'o' && tok[1] === op; }
	function expect(p, op) { if (!isOp(p.t[p.i], op)) { throw new Error('syntax'); } p.i++; }
	function level(p, l) {
		if (l >= LEVELS.length) { return unary(p); }
		var left = level(p, l + 1);
		while (p.t[p.i] && p.t[p.i][0] === 'o' && LEVELS[l].indexOf(p.t[p.i][1]) !== -1) {
			var op = p.t[p.i++][1];
			left = ['b', op, left, level(p, l + 1)];
		}
		return left;
	}
	function unary(p) {
		if (++p.depth > 40) { throw new Error('too_deep'); }
		var tok = p.t[p.i++], node;
		if (!tok) { throw new Error('syntax'); }
		if (isOp(tok, '-')) { node = ['neg', unary(p)]; }
		else if (isOp(tok, '+')) { node = unary(p); }
		else if (tok[0] === 'n') { node = ['n', tok[1]]; }
		else if (tok[0] === 'r') { node = ['r', tok[1]]; }
		else if (isOp(tok, '(')) { node = level(p, 0); expect(p, ')'); }
		else if (tok[0] === 'f') {
			if (!FN.hasOwnProperty(tok[1])) { throw new Error('unknown_function'); }
			expect(p, '(');
			var args = [level(p, 0)];
			while (isOp(p.t[p.i], ',')) { p.i++; args.push(level(p, 0)); }
			expect(p, ')');
			if (args.length < FN[tok[1]][0] || args.length > FN[tok[1]][1]) { throw new Error('arg_count'); }
			node = ['f', tok[1], args];
		} else { throw new Error('syntax'); }
		p.depth--;
		return node;
	}
	function round(x, d) {
		d = Math.max(0, Math.min(4, d | 0));
		var f = Math.pow(10, d), r = Math.floor(Math.abs(x) * f + 0.5 + 1e-7);
		return (x < 0 ? -r : r) / f;
	}
	function ev(n, ref) {
		var a, b, r;
		switch (n[0]) {
			case 'n': return n[1];
			case 'r': a = ref(n[1]); if (a === null) { throw new Error('incomplete'); } return a;
			case 'neg': return -ev(n[1], ref);
			case 'f':
				if (n[1] === 'if') { return ev(n[2][0], ref) ? ev(n[2][1], ref) : ev(n[2][2], ref); }
				a = n[2].map(function (x) { return ev(x, ref); });
				switch (n[1]) {
					case 'min': return Math.min.apply(null, a);
					case 'max': return Math.max.apply(null, a);
					case 'ceil': return Math.ceil(a[0] - 1e-9);
					case 'floor': return Math.floor(a[0] + 1e-9);
					case 'abs': return Math.abs(a[0]);
					default: return round(a[0], a.length > 1 ? a[1] : 0);
				}
		}
		a = ev(n[2], ref);
		if (n[1] === '&&' && !a) { return 0; }
		if (n[1] === '||' && a) { return 1; }
		b = ev(n[3], ref);
		switch (n[1]) {
			case '+': r = a + b; break;
			case '-': r = a - b; break;
			case '*': r = a * b; break;
			case '/': if (b === 0) { throw new Error('div_zero'); } r = a / b; break;
			case '&&': case '||': return b ? 1 : 0;
			case '==': return Math.abs(a - b) < 1e-9 ? 1 : 0;
			case '!=': return Math.abs(a - b) >= 1e-9 ? 1 : 0;
			case '>': return a > b + 1e-9 ? 1 : 0;
			case '<': return a < b - 1e-9 ? 1 : 0;
			case '>=': return a >= b - 1e-9 ? 1 : 0;
			default: return a <= b + 1e-9 ? 1 : 0;
		}
		if (!isFinite(r) || Math.abs(r) > 1e12) { throw new Error('overflow'); }
		return r;
	}
	function num(v) {
		v = String(v).replace(/ /g, '').trim();
		if (/^-?\d+,\d+$/.test(v)) { v = v.replace(',', '.'); }
		return v !== '' && isFinite(v) && !isNaN(parseFloat(v)) ? parseFloat(v) : null;
	}
	function refs(src) { var out = [], m, re = /\{([a-z][a-z0-9_]{1,31})\}/g; while ((m = re.exec(String(src || '')))) { if (out.indexOf(m[1]) === -1) { out.push(m[1]); } } return out; }
	/* fields: [{id,type,name,formula,isTotal,optionPrices}], values by name, seen by id. Same result shape as bst_calc_quote(). */
	function quote(fields, values, seen, cur) {
		var byId = {}, lines = {}, total = '', deps = {}, order = [], done = {};
		fields.forEach(function (f) { byId[f.id] = f; });
		fields.forEach(function (f) { deps[f.id] = f.type === 'calc' ? refs(f.formula).filter(function (d) { return d !== f.id && byId.hasOwnProperty(d); }) : []; });
		for (var moved = true; moved && order.length < fields.length;) {
			moved = false;
			fields.forEach(function (f) { if (!done[f.id] && deps[f.id].every(function (d) { return done[d]; })) { done[f.id] = 1; order.push(f.id); moved = true; } });
		}
		if (order.length < fields.length) { order = []; }
		var ref = function (id) {
			var f = byId[id];
			if (!f) { throw new Error('missing_ref'); }
			if (!seen[id]) { return 0; }
			var v = String(values[f.name] || '');
			if (f.type === 'calc') { var l = lines[id] || ['error', 0]; if (l[0] !== 'ok') { throw new Error(l[0]); } return l[1]; }
			if (['select', 'radio', 'checkbox'].indexOf(f.type) !== -1) {
				if (v === '') { return f.type === 'checkbox' ? 0 : null; }
				var prices = f.optionPrices || {}, sum = 0;
				v.split(', ').forEach(function (o) { var pr = prices.hasOwnProperty(o) ? num(prices[o]) : null; if (pr === null) { throw new Error('unpriced'); } sum += pr; });
				return sum;
			}
			if (f.type === 'acceptance') { return v === '' ? 0 : 1; }
			if (f.type === 'number') { return v === '' ? null : num(v); }
			return 0;
		};
		order.forEach(function (id) {
			var f = byId[id];
			if (f.type !== 'calc') { return; }
			if (f.isTotal && total === '') { total = id; }
			if (!seen[id]) { lines[id] = ['ok', 0]; return; }
			var p = parse(f.formula);
			if (p.error) { lines[id] = ['error', 0]; return; }
			try {
				var v = round(ev(p.ast, ref), cur.decimals);
				lines[id] = f.isTotal && v < 0 ? ['error', 0] : ['ok', v];
			} catch (e) { lines[id] = [e.message === 'incomplete' ? 'incomplete' : 'error', 0]; }
		});
		return { lines: lines, total: total };
	}
	function money(v, cur) {
		var s = Math.abs(v).toFixed(cur.decimals).split('.');
		var vnd = cur.code === 'VND', sep = vnd ? '.' : ',', dec = vnd ? ',' : '.';
		var str = (v < 0 ? '-' : '') + s[0].replace(/\B(?=(\d{3})+(?!\d))/g, sep) + (s[1] ? dec + s[1] : '');
		return vnd ? str + ' ₫' : str + ' ' + cur.code;
	}
	return { parse: parse, refs: refs, quote: quote, money: money, round: round, num: num };
})();
(function () {
	if (typeof document === 'undefined') { return; }
	var valuesOf = function (form) {
		var out = {};
		form.querySelectorAll('[data-bs-fid]').forEach(function (w) {
			var list = [], name = '';
			w.querySelectorAll('input, select, textarea').forEach(function (c) {
				name = c.name.replace('[]', '');
				if (!c.disabled && ((c.type === 'checkbox' || c.type === 'radio') ? c.checked : c.type !== 'file') && String(c.value).trim() !== '') { list.push(String(c.value).trim()); }
			});
			if (name) { out[name] = list.join(', '); }
		});
		return out;
	};
	var update = function (form) {
		var cfg;
		try { cfg = JSON.parse(form.getAttribute('data-bs-calc')); } catch (e) { return; }
		var seen = {};
		form.querySelectorAll('[data-bs-fid]').forEach(function (w) { seen[w.getAttribute('data-bs-fid')] = !w.hidden; });
		var q = bstCalc.quote(cfg.fields, valuesOf(form), seen, cfg.cur);
		Object.keys(q.lines).forEach(function (id) {
			var out = form.querySelector('[data-bs-fid="' + id + '"] [data-bs-calc-out]');
			if (!out) { return; }
			var l = q.lines[id];
			var text = l[0] === 'ok' ? bstCalc.money(l[1], cfg.cur) : (l[0] === 'incomplete' ? cfg.wait : cfg.check);
			if (out.textContent !== text) { out.textContent = text; }
			out.parentNode.setAttribute('data-state', l[0]);
		});
	};
	var timers = new WeakMap();
	var on = function (e) {
		var form = e.target && e.target.closest && e.target.closest('form[data-bs-calc]');
		if (!form) { return; }
		// Run after the condition handler, and coalesce fast typing so the live region is not noisy.
		clearTimeout(timers.get(form));
		timers.set(form, setTimeout(function () { update(form); }, 120));
	};
	document.addEventListener('input', on);
	document.addEventListener('change', on);
	document.addEventListener('reset', function (e) { if (e.target.matches && e.target.matches('form[data-bs-calc]')) { setTimeout(function () { update(e.target); }, 10); } }, true);
	var all = function () { document.querySelectorAll('form[data-bs-calc]').forEach(update); };
	if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', function () { setTimeout(all, 0); }); } else { setTimeout(all, 0); }
})();

/* Built-in form submit (1.54): AJAX send, inline errors, success / redirect like Contact Form 7. */
document.addEventListener('submit', function (e) {
	var form = e.target;
	if (!form.matches || !form.matches('[data-bs-native-form]')) {
		return;
	}
	e.preventDefault();
	var out = form.querySelector('.wpcf7-response-output');
	var btn = form.querySelector('[type="submit"]');
	form.querySelectorAll('.wpcf7-not-valid-tip').forEach(function (n) { n.remove(); });
	form.querySelectorAll('.wpcf7-not-valid').forEach(function (n) { n.classList.remove('wpcf7-not-valid'); });
	var tip = function (name, msg) {
		var wrap = form.querySelector('.wpcf7-form-control-wrap[data-name="' + name + '"]');
		if (!wrap) {
			return;
		}
		var ctl = wrap.querySelector('input, select, textarea');
		if (ctl) {
			ctl.classList.add('wpcf7-not-valid');
		}
		var s = document.createElement('span');
		s.className = 'wpcf7-not-valid-tip';
		s.textContent = msg;
		wrap.appendChild(s);
	};
	var bad = false;
	form.querySelectorAll('[required]:not(:disabled)').forEach(function (ctl) {
		var empty = ctl.type === 'checkbox' ? !ctl.checked : !String(ctl.value).trim();
		if (empty) {
			bad = true;
			tip(ctl.name.replace('[]', ''), form.getAttribute('data-msg-required'));
		}
	});
	form.querySelectorAll('input[type="email"]').forEach(function (ctl) {
		if (ctl.value && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(ctl.value)) {
			bad = true;
			tip(ctl.name, form.getAttribute('data-msg-email'));
		}
	});
	var show = function (msg, ok) {
		out.hidden = false;
		out.textContent = msg;
		form.classList.remove('sent', 'invalid', 'failed');
		form.classList.add(ok ? 'sent' : (ok === null ? 'invalid' : 'failed'));
	};
	if (bad) {
		return;
	}
	var data = new FormData(form);
	data.append('action', 'bst_form_submit');
	data.append('bst_form_key', form.getAttribute('data-key'));
	data.append('bst_form_post', form.getAttribute('data-post') || '0');
	data.append('bst_form_ts', form.getAttribute('data-ts') || '0');
	form.classList.add('submitting');
	if (btn) {
		btn.disabled = true;
	}
	fetch(form.getAttribute('action'), { method: 'POST', body: data, credentials: 'same-origin' }).then(function (r) { return r.json(); }).then(function (res) {
		if (res.errors) {
			Object.keys(res.errors).forEach(function (n) { tip(n, res.errors[n]); });
			show(res.message, null);
			return;
		}
		show(res.message, !!res.ok);
		if (res.ok) {
			form.reset();
			form.dispatchEvent(new CustomEvent('wpcf7mailsent', { bubbles: true, detail: { native: true } }));
			if (res.redirect) {
				var box = form.closest('.bs-form');
				var delay = box ? parseFloat(box.getAttribute('data-bs-redirect-delay') || '0') : 0;
				setTimeout(function () {
					if (form.getAttribute('data-new-tab') === '1') {
						window.open(res.redirect, '_blank', 'noopener');
					} else {
						window.location.href = res.redirect;
					}
				}, delay * 1000);
			}
		}
	}).catch(function () {
		show(form.querySelector('.wpcf7-submit') ? 'Network error, please try again.' : '', false);
	}).then(function () {
		form.classList.remove('submitting');
		if (btn) {
			btn.disabled = false;
		}
	});
});
