/* Auto detect page: one panel per check; the open page advances running scans batch by batch. */
(function () {
	'use strict';
	var cfg = window.bstAutoDetect, t = cfg.i18n, root = document.getElementById('bst-auto-detect');
	var api = function (path, data) {
		return wp.apiFetch({ path: '/' + cfg.ns + '/' + path, method: data ? 'POST' : 'GET', data: data });
	};
	function node(tag, props, kids) {
		var n = document.createElement(tag);
		Object.keys(props || {}).forEach(function (k) { n[k] = props[k]; });
		(kids || []).forEach(function (c) { if (c) { n.appendChild(typeof c === 'string' ? document.createTextNode(c) : c); } });
		return n;
	}
	function safeUrl(u) { return /^https?:\/\//i.test(u || '') ? u : '#'; }
	function fail() { window.alert(t.failed); }
	function action(id, data) { return api(id + '/action', data).then(function (s) { render(id, s); }).catch(fail); }

	function itemRow(id, item) {
		var kids = [
			node('strong', { textContent: (item.severity === 'error' ? t.error : t.review) + ': ' }),
			document.createTextNode(item.title)
		];
		if (item.detail) { kids.push(node('div', { className: 'description', textContent: item.detail })); }
		var row = node('li', { style: 'padding:8px 0;border-top:1px solid #dcdcde' }, kids);
		(item.posts || []).forEach(function (p) {
			row.appendChild(node('div', {}, [node('a', { href: safeUrl(p.edit), textContent: p.title || ('#' + p.id) })]));
		});
		if (item.select) {
			row.insertBefore(node('input', { type: 'checkbox', className: 'bst-ad-pick', value: item.select }), row.firstChild);
		}
		if (item.ignore) {
			row.appendChild(node('button', { type: 'button', className: 'button-link', textContent: t.ignore, onclick: function () {
				api(id + '/ignore', { key: item.key }).then(function (s) { render(id, s); }).catch(fail);
			} }));
		}
		if (item.link) {
			row.appendChild(node('button', { type: 'button', className: 'button', textContent: t.replaceBtn, onclick: function () {
				var to = window.prompt(t.replaceAsk, '');
				if (to) { action(id, { op: 'replace', url: item.link, to: to }); }
			} }));
			row.appendChild(node('button', { type: 'button', className: 'button', textContent: t.removeBtn, onclick: function () {
				if (window.confirm(t.removeAsk)) { action(id, { op: 'remove', url: item.link }); }
			} }));
		}
		return row;
	}

	var panels = {};
	function render(id, s) {
		var box = panels[id], running = s.status === 'running';
		box.status.textContent = running ? t.scanning + (s.total ? ' ' + s.done + '/' + s.total : ' ' + s.done) : (s.finished ? t.last + ' ' + new Date(s.finished * 1000).toLocaleString() : t.never);
		box.button.disabled = running;
		box.note.textContent = s.note || '';
		box.list.textContent = '';
		s.items.forEach(function (item) { box.list.appendChild(itemRow(id, item)); });
		if (!running && s.finished && !s.items.length) { box.list.appendChild(node('li', { textContent: t.clean })); }
		box.bulk.textContent = '';
		if (s.items.some(function (i) { return i.select; })) {
			box.bulk.appendChild(node('button', { type: 'button', className: 'button', textContent: t.deleteBtn, onclick: function () {
				var ids = Array.prototype.map.call(box.list.querySelectorAll('.bst-ad-pick:checked'), function (c) { return Number(c.value); });
				if (ids.length && window.confirm(t.deleteAsk)) { action(id, { op: 'delete', ids: ids }); }
			} }));
		}
		if (running && !box.busy) { advance(id); }
	}
	function advance(id) {
		panels[id].busy = true;
		api(id + '/step', {}).then(function (s) {
			panels[id].busy = false;
			render(id, s);
		}).catch(function () { panels[id].busy = false; });
	}

	api('state').then(function (state) {
		root.appendChild(node('p', {}, [node('label', {}, [
			node('input', { type: 'checkbox', checked: state.enabled, onchange: function (e) { api('enabled', { enabled: e.target.checked }).catch(fail); } }),
			' ' + t.enable
		])]));
		Object.keys(cfg.modules).forEach(function (id) {
			var m = cfg.modules[id], box = {
				status: node('span', { style: 'margin-left:10px' }),
				note: node('p', { className: 'description' }),
				list: node('ul', {}),
				bulk: node('p', {})
			};
			box.button = node('button', { type: 'button', className: 'button button-primary', textContent: t.scan, onclick: function () {
				api(id + '/start', {}).then(function (s) { render(id, s); }).catch(fail);
			} });
			panels[id] = box;
			root.appendChild(node('div', { className: 'card', style: 'max-width:none' }, [node('h2', { textContent: m.label }), node('p', { textContent: m.intro }), box.button, box.status, box.note, box.list, box.bulk]));
			render(id, state.modules[id]);
		});
	}).catch(fail);
})();
