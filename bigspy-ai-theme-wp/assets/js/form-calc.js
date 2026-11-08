/* Built-in Form price preview (G-58). Same grammar and rounding as inc/form-calc.php.
 * Only a preview: the server computes the total again from the saved form on submit. */
(function () {
	'use strict';

	function num(v) {
		v = String(v == null ? '' : v).replace(/[\s ]/g, '');
		if (v === '') { return null; }
		if (/^-?\d{1,3}([.,]\d{3})+$/.test(v) && !/^-?0[.,]/.test(v)) { v = v.replace(/[.,]/g, ''); } else { v = v.replace(',', '.'); }
		return /^-?(\d+\.?\d*|\.\d+)(e[-+]?\d+)?$/i.test(v) ? parseFloat(v) : null;
	}
	function decimals(cur) { return ['VND', 'JPY', 'KRW'].indexOf(String(cur || '').toUpperCase()) !== -1 ? 0 : 2; }
	function round(v, dec) { var m = Math.pow(10, dec), s = v < 0 ? -1 : 1; return s * Math.floor(Math.abs(v) * m + 0.5 + 1e-9) / m; }
	function format(v, cur) {
		cur = String(cur || '').toUpperCase();
		var dec = decimals(cur), parts = Math.abs(v).toFixed(dec).split('.'), sep = cur === 'VND' ? '.' : ',';
		var int = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, sep), out = (v < 0 ? '-' : '') + int + (parts[1] ? '.' + parts[1] : '');
		return cur === 'VND' ? out + ' ₫' : out + (cur ? ' ' + cur : '');
	}

	function tokens(src) {
		src = String(src || '');
		if (src.length > 2000) { throw new Error('Formula too long.'); }
		var out = [], i = 0, m, rest;
		while (i < src.length) {
			if (/\s/.test(src[i])) { i++; continue; }
			rest = src.slice(i);
			if ((m = /^\d+(\.\d+)?/.exec(rest))) { out.push(['num', parseFloat(m[0])]); }
			else if ((m = /^\{([a-z][a-z0-9_]{1,31})\}/.exec(rest))) { out.push(['ref', m[1]]); }
			else if ((m = /^(min|max|round|ceil|floor|if)\b/.exec(rest))) { out.push(['fn', m[1]]); }
			else if ((m = /^(<=|>=|==|!=|<|>|[-+*\/(),])/.exec(rest))) { out.push(['op', m[1]]); }
			else { throw new Error('Unexpected "' + rest.slice(0, 10) + '" at position ' + (i + 1) + '.'); }
			i += m[0].length;
		}
		if (out.length > 400) { throw new Error('Formula too long.'); }
		return out;
	}

	var ARITY = { min: [1, 20], max: [1, 20], round: [1, 2], ceil: [1, 1], floor: [1, 1], 'if': [3, 3] };
	function parse(src) {
		var t = tokens(src), pos = 0, depth = 0;
		if (!t.length) { throw new Error('Formula is empty.'); }
		function peek() { return t[pos] || ['end', '']; }
		function is(v) { var k = peek(); return k[0] === 'op' && k[1] === v; }
		function expect(v) { if (!is(v)) { throw new Error('Expected "' + v + '".'); } pos++; }
		function primary() {
			var k = peek(), args, e;
			if (k[0] === 'num' || k[0] === 'ref') { pos++; return k; }
			if (k[0] === 'fn') {
				pos++; expect('('); args = [cmp()];
				while (is(',')) { pos++; args.push(cmp()); }
				expect(')');
				if (args.length < ARITY[k[1]][0] || args.length > ARITY[k[1]][1]) { throw new Error(k[1] + '() takes ' + ARITY[k[1]][0] + ' to ' + ARITY[k[1]][1] + ' values.'); }
				return ['fn', k[1], args];
			}
			if (is('(')) {
				if (++depth > 30) { throw new Error('Too many parentheses.'); }
				pos++; e = cmp(); expect(')'); depth--; return e;
			}
			throw new Error(k[0] === 'end' ? 'Formula ends too early.' : 'Unexpected "' + k[1] + '".');
		}
		function unary() { if (is('-')) { pos++; return ['neg', unary()]; } return primary(); }
		function product() { var l = unary(), op; while (is('*') || is('/')) { op = peek()[1]; pos++; l = ['bin', op, l, unary()]; } return l; }
		function sum() { var l = product(), op; while (is('+') || is('-')) { op = peek()[1]; pos++; l = ['bin', op, l, product()]; } return l; }
		function cmp() { var l = sum(), k = peek(); if (k[0] === 'op' && ['<', '<=', '>', '>=', '==', '!='].indexOf(k[1]) !== -1) { pos++; l = ['bin', k[1], l, sum()]; } return l; }
		var tree = cmp();
		if (pos < t.length) { throw new Error('Unexpected "' + t[pos][1] + '".'); }
		return tree;
	}
	function refs(tree) {
		if (tree[0] === 'ref') { return [tree[1]]; }
		var kids = tree[0] === 'fn' ? tree[2] : tree[0] === 'neg' ? [tree[1]] : tree[0] === 'bin' ? [tree[2], tree[3]] : [];
		return kids.reduce(function (a, k) { return a.concat(refs(k)); }, []);
	}
	function evaluate(tree, vars) {
		var a, l, r, v;
		switch (tree[0]) {
			case 'num': return tree[1];
			case 'ref': return vars[tree[1]];
			case 'neg': return -evaluate(tree[1], vars);
			case 'fn':
				if (tree[1] === 'if') { return evaluate(tree[2][0], vars) ? evaluate(tree[2][1], vars) : evaluate(tree[2][2], vars); }
				a = tree[2].map(function (x) { return evaluate(x, vars); });
				if (tree[1] === 'min') { return Math.min.apply(null, a); }
				if (tree[1] === 'max') { return Math.max.apply(null, a); }
				if (tree[1] === 'ceil') { return Math.ceil(a[0] - 1e-9); }
				if (tree[1] === 'floor') { return Math.floor(a[0] + 1e-9); }
				return round(a[0], Math.max(0, Math.min(6, a.length > 1 ? Math.trunc(a[1]) : 0)));
		}
		l = evaluate(tree[2], vars); r = evaluate(tree[3], vars);
		switch (tree[1]) {
			case '+': v = l + r; break;
			case '-': v = l - r; break;
			case '*': v = l * r; break;
			case '/': if (Math.abs(r) < 1e-12) { throw new Error('division'); } v = l / r; break;
			case '<': return l < r - 1e-9 ? 1 : 0;
			case '<=': return l <= r + 1e-9 ? 1 : 0;
			case '>': return l > r + 1e-9 ? 1 : 0;
			case '>=': return l >= r - 1e-9 ? 1 : 0;
			case '==': return Math.abs(l - r) < 1e-9 ? 1 : 0;
			default: return Math.abs(l - r) >= 1e-9 ? 1 : 0;
		}
		if (!isFinite(v) || Math.abs(v) > 1e12) { throw new Error('range'); }
		return v;
	}

	/* Totals from a config (bst_calc_client_config) and a value reader: name => string|string[]. */
	function run(cfg, read) {
		var vars = {}, miss = {}, out = {}, trees = {}, order = [], state = {};
		Object.keys(cfg.fields || {}).forEach(function (id) {
			var f = cfg.fields[id], v = read(f.name), n, picked, sum = 0;
			if (f.type === 'number') { n = num(v); vars[id] = n === null ? 0 : n; miss[id] = n === null && f.required; }
			else if (f.type === 'acceptance') { vars[id] = v && v.length ? 1 : 0; miss[id] = false; }
			else {
				picked = Array.isArray(v) ? v : (v ? [v] : []);
				picked.forEach(function (p) { sum += Number((f.prices || {})[p] || 0); });
				vars[id] = sum; miss[id] = !picked.length && f.required;
			}
		});
		var calcs = {};
		(cfg.calcs || []).forEach(function (c) { calcs[c.id] = c; try { trees[c.id] = c.formula ? parse(c.formula) : null; } catch (e) { trees[c.id] = null; } });
		function visit(id) {
			if (state[id] === 'done') { return !!trees[id]; }
			if (state[id] === 'busy' || !trees[id]) { return false; }
			state[id] = 'busy';
			var ok = refs(trees[id]).every(function (r) { return !calcs[r] || visit(r); });
			state[id] = 'done';
			if (!ok) { trees[id] = null; return false; }
			order.push(id); return true;
		}
		Object.keys(calcs).forEach(visit);
		Object.keys(calcs).forEach(function (id) { if (order.indexOf(id) === -1) { out[id] = { status: 'confirm', amount: null }; } });
		order.forEach(function (id) {
			var c = calcs[id], v = null;
			if (refs(trees[id]).some(function (r) { return miss[r] || (calcs[r] && !(r in vars)); })) { out[id] = { status: 'incomplete', amount: null }; miss[id] = true; vars[id] = 0; return; }
			try { v = round(evaluate(trees[id], vars), decimals(c.currency)); } catch (e) { v = null; }
			if (v === null || v < 0) { out[id] = { status: 'confirm', amount: null }; miss[id] = true; vars[id] = 0; return; }
			out[id] = { status: 'ok', amount: v }; vars[id] = v;
		});
		Object.keys(out).forEach(function (id) {
			var r = out[id];
			r.text = r.status === 'ok' ? format(r.amount, calcs[id].currency) : (cfg.msg || {})[r.status] || '';
		});
		return out;
	}

	(typeof window !== 'undefined' ? window : globalThis).bstFormCalc = { parse: parse, refs: refs, evaluate: evaluate, run: run, format: format, num: num };

	function update(form) {
		var cfg;
		try { cfg = JSON.parse(form.getAttribute('data-bs-calc') || '{}'); } catch (e) { return; }
		var res = run(cfg, function (name) {
			var els = form.querySelectorAll('[name="' + name + '"], [name="' + name + '[]"]'), vals = [];
			if (!els.length) { return ''; }
			if (els[0].type === 'checkbox' || els[0].type === 'radio') {
				els.forEach(function (e) { if (e.checked) { vals.push(e.value); } });
				return els[0].type === 'checkbox' && els[0].name.slice(-2) === '[]' ? vals : (vals[0] || '');
			}
			return els[0].value;
		});
		form.querySelectorAll('output[data-calc]').forEach(function (o) {
			var r = res[o.getAttribute('data-calc')];
			if (r && o.textContent !== r.text) { o.textContent = r.text; }
			if (r) { o.setAttribute('data-status', r.status); }
		});
	}
	function init() {
		document.querySelectorAll('form[data-bs-calc]').forEach(update);
		document.addEventListener('input', function (e) { var f = e.target.closest && e.target.closest('form[data-bs-calc]'); if (f) { update(f); } });
		document.addEventListener('change', function (e) { var f = e.target.closest && e.target.closest('form[data-bs-calc]'); if (f) { update(f); } });
		document.addEventListener('reset', function (e) { var f = e.target; if (f.matches && f.matches('form[data-bs-calc]')) { setTimeout(function () { update(f); }, 0); } });
	}
	if (typeof document !== 'undefined') {
		if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', init); } else { init(); }
	}
}());
