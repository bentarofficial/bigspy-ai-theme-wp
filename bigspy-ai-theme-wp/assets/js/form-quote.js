/* BIGSPY AI Theme WP – Form price engine (G-58), shared by the page and the editor.
   Same formula rules as inc/form-quote.php; the server always computes the price again. */
(function () {
	'use strict';
	function bsQNumber(v) {
		v = String(v).replace(/[\s ]/g, '');
		if (v === '') { return null; }
		var c = v.lastIndexOf(','), d = v.lastIndexOf('.');
		if (c !== -1 && d !== -1) { v = c > d ? v.replace(/\./g, '').replace(',', '.') : v.replace(/,/g, ''); } else if (c !== -1) { v = v.replace(',', '.'); }
		return /^-?\d+(\.\d+)?$/.test(v) ? parseFloat(v) : null;
	}
	function bsQRound(x, d) {
		var m = Math.pow(10, d), v = Math.abs(x) * m;
		var r = Math.floor(v + 0.5 + 1e-9 * Math.max(1, v));
		return (x < 0 ? -r : r) / m;
	}
	function bsQParse(src) {
		var re = /\s*(?:(\d+(?:\.\d+)?)|\{([a-z0-9_-]+)(?:=([^}]*))?\}|([a-z]+)|(>=|<=|==|!=|[-+*\/(),<>]))/y;
		var t = [], m, end = 0;
		while (end < src.length && (re.lastIndex = end, m = re.exec(src))) {
			end = re.lastIndex;
			t.push(m[5] ? ['op', m[5]] : m[4] ? ['fn', m[4]] : m[2] ? ['ref', m[2], m[3] === undefined ? null : m[3].trim()] : ['num', parseFloat(m[1])]);
		}
		if (src.slice(end).trim() !== '' || !t.length) { throw new Error('syntax'); }
		var i = 0, depth = 0;
		var peek = function () { return t[i] || ['end', '']; };
		var eat = function (v) { if (!t[i] || t[i][0] !== 'op' || t[i][1] !== v) { throw new Error('syntax'); } i++; };
		var isOp = function (list) { var p = peek(); return p[0] === 'op' && list.indexOf(p[1]) !== -1; };
		var cmp, add, mul, unary;
		cmp = function () { var a = add(); if (isOp(['>', '<', '>=', '<=', '==', '!='])) { var o = t[i++][1]; return ['bin', o, a, add()]; } return a; };
		add = function () { var a = mul(); while (isOp(['+', '-'])) { var o = t[i++][1]; a = ['bin', o, a, mul()]; } return a; };
		mul = function () { var a = unary(); while (isOp(['*', '/'])) { var o = t[i++][1]; a = ['bin', o, a, unary()]; } return a; };
		unary = function () {
			if (++depth > 40) { throw new Error('syntax'); }
			var p = peek(), r;
			i++;
			if (p[0] === 'op' && p[1] === '-') { r = ['neg', unary()]; } else if (p[0] === 'num') { r = p; } else if (p[0] === 'ref') { r = p; } else if (p[0] === 'op' && p[1] === '(') { r = cmp(); eat(')'); } else if (p[0] === 'fn' && ['min', 'max', 'round', 'ceil', 'floor', 'if'].indexOf(p[1]) !== -1) {
				eat('(');
				var args = [cmp()];
				while (isOp([','])) { i++; args.push(cmp()); }
				eat(')');
				if ((p[1] === 'if' && args.length !== 3) || ((p[1] === 'ceil' || p[1] === 'floor') && args.length !== 1) || (p[1] === 'round' && args.length > 2)) { throw new Error('syntax'); }
				r = ['fn', p[1], args];
			} else { throw new Error('syntax'); }
			depth--;
			return r;
		};
		var tree = cmp();
		if (peek()[0] !== 'end') { throw new Error('syntax'); }
		return tree;
	}
	function bsQEval(n, ref) {
		var r, a, b;
		if (n[0] === 'num') { return n[1]; }
		if (n[0] === 'ref') { return ref(n[1], n[2]); }
		if (n[0] === 'neg') { return -bsQEval(n[1], ref); }
		if (n[0] === 'bin') {
			a = bsQEval(n[2], ref); b = bsQEval(n[3], ref);
			var e = Math.abs(a - b) < 1e-9;
			switch (n[1]) {
				case '+': r = a + b; break;
				case '-': r = a - b; break;
				case '*': r = a * b; break;
				case '/': if (b === 0) { throw new Error('div0'); } r = a / b; break;
				default: r = +({ '>': a > b && !e, '<': a < b && !e, '>=': a > b || e, '<=': a < b || e, '==': e, '!=': !e })[n[1]];
			}
		} else if (n[1] === 'if') {
			return bsQEval(n[2][0], ref) !== 0 ? bsQEval(n[2][1], ref) : bsQEval(n[2][2], ref);
		} else {
			var v = n[2].map(function (x) { return bsQEval(x, ref); });
			r = n[1] === 'min' ? Math.min.apply(null, v) : n[1] === 'max' ? Math.max.apply(null, v) : n[1] === 'ceil' ? Math.ceil(v[0] - 1e-9) : n[1] === 'floor' ? Math.floor(v[0] + 1e-9) : bsQRound(v[0], Math.max(-6, Math.min(6, Math.trunc(v[1] || 0))));
		}
		if (!isFinite(r) || Math.abs(r) > 1e12) { throw new Error('range'); }
		return r;
	}
	function bsQMoney(x, cfg) {
		var parts = Math.abs(x).toFixed(cfg.decimals).split('.');
		return (x < 0 ? '-' : '') + parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, '.') + (parts[1] ? ',' + parts[1] : '') + (cfg.symbol ? ' ' + cfg.symbol : '');
	}
	function bsQCompute(cfg, ctx) {
		if (!cfg.ok) { return { status: 'error' }; }
		var trees = {}, done = {}, incomplete = false;
		cfg.lines.forEach(function (l) { trees[l.id] = bsQParse(l.expr); });
		var line, ref = function (id, opt) {
			if (trees[id] && opt === null) { return line(id); }
			if (!ctx.visible(id)) { return 0; }
			var v = ctx.value(id);
			if (v === '') { incomplete = incomplete || ctx.required(id); return 0; }
			var type = cfg.types[id] || 'text';
			var picked = ['select', 'radio', 'checkbox'].indexOf(type) !== -1 ? v.split(', ') : [v];
			if (opt !== null) { return picked.indexOf(opt) !== -1 ? 1 : 0; }
			var prices = cfg.prices[id];
			if (prices && Object.keys(prices).length) { return picked.reduce(function (s, o) { return s + (prices[o] || 0); }, 0); }
			var n = bsQNumber(v);
			if (n === null || n < 0) { throw new Error('input'); }
			return n;
		};
		line = function (id) { if (done[id] === undefined) { done[id] = bsQRound(bsQEval(trees[id], ref), cfg.decimals); } return done[id]; };
		var out = [], total = 0;
		try {
			cfg.lines.forEach(function (l) { var a = line(l.id); total += a; out.push({ label: l.label, amount: a }); });
		} catch (err) {
			return { status: 'error' };
		}
		total = bsQRound(total, cfg.decimals);
		if (incomplete || !out.some(function (l) { return l.amount !== 0; })) { return { status: 'incomplete' }; }
		if (total < 0 || Math.abs(total) > 1e12) { return { status: 'error' }; }
		return { status: 'ok', lines: out, total: total };
	}

	window.bsQuote = { number: bsQNumber, round: bsQRound, parse: bsQParse, evaluate: bsQEval, money: bsQMoney, compute: bsQCompute };
})();
