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
	function fail(e) { window.alert((e && e.message) || t.failed); }
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

	/* Thumbnail card: what the user looks at before deciding to keep or remove a file. */
	function card(id, item, boxClass) {
		var pick = node('input', { type: 'checkbox', className: boxClass, value: item.select || item.id });
		var kids = [node('label', { style: 'display:block;cursor:pointer' }, [
			node('img', { src: safeUrl(item.thumb), alt: '', loading: 'lazy', style: 'display:block;width:100%;height:130px;object-fit:contain;background:#f0f0f1' }),
			pick,
			' ' + item.title
		])];
		if (item.detail) { kids.push(node('div', { className: 'description', style: 'word-break:break-all', textContent: item.detail })); }
		if (item.edit) { kids.push(node('a', { href: safeUrl(item.edit), target: '_blank', textContent: '#' + item.select })); }
		if (item.ignore) {
			kids.push(node('button', { type: 'button', className: 'button-link', style: 'margin-left:8px', textContent: t.keep, onclick: function () {
				api(id + '/ignore', { key: item.key }).then(function (s) { render(id, s); }).catch(fail);
			} }));
		}
		return node('li', { style: 'border:1px solid #dcdcde;padding:8px;margin:0;font-size:12px;overflow:hidden' }, kids);
	}
	function picked(list, cls) {
		return Array.prototype.map.call(list.querySelectorAll('.' + cls + ':checked'), function (c) { return Number(c.value); });
	}
	function selectAll(list) {
		return node('button', { type: 'button', className: 'button', textContent: t.selectAll, onclick: function () {
			var boxes = list.querySelectorAll('input[type=checkbox]');
			var on = Array.prototype.some.call(boxes, function (c) { return !c.checked; });
			boxes.forEach(function (c) { c.checked = on; });
		} });
	}
	/* Generate a few at a time so a long run never hits a server time limit. */
	function generate(id, ids, batch) {
		if (!ids.length) { return; }
		api(id + '/action', { op: 'generate', ids: ids.slice(0, batch) }).then(function (s) {
			render(id, s);
			generate(id, ids.slice(batch), batch);
		}).catch(function (e) { api('state').then(function (all) { render(id, all.modules[id]); }); fail(e); });
	}
	var grid = 'display:grid;grid-template-columns:repeat(auto-fill,minmax(170px,1fr));gap:10px;list-style:none;padding:0';

	var panels = {};
	function render(id, s) {
		var box = panels[id], running = s.status === 'running';
		box.status.textContent = running ? t.scanning + (s.total ? ' ' + s.done + '/' + s.total : ' ' + s.done) : (s.finished ? t.last + ' ' + new Date(s.finished * 1000).toLocaleString() : t.never);
		box.button.disabled = running;
		box.note.textContent = s.note || '';
		box.list.textContent = '';
		var cards = s.items.some(function (i) { return i.thumb; });
		box.list.style.cssText = cards ? grid : '';
		s.items.forEach(function (item) { box.list.appendChild(cards ? card(id, item, 'bst-ad-pick') : itemRow(id, item)); });
		if (!running && s.finished && !s.items.length) { box.list.appendChild(node('li', { textContent: t.clean })); }
		box.bulk.textContent = '';
		var extra = s.extra || {};
		if (id === 'atnd') {
			if (!extra.ready) {
				box.bulk.appendChild(node('a', { className: 'button button-primary', href: safeUrl(extra.connect), textContent: t.connect }));
			} else if (s.items.length) {
				box.bulk.appendChild(selectAll(box.list));
				box.bulk.appendChild(node('button', { type: 'button', className: 'button button-primary', style: 'margin-left:6px', textContent: t.generate, onclick: function () {
					generate(id, picked(box.list, 'bst-ad-pick'), extra.batch);
				} }));
			}
		} else if (s.items.some(function (i) { return i.select; })) {
			if (cards) { box.bulk.appendChild(selectAll(box.list)); }
			box.bulk.appendChild(node('button', { type: 'button', className: 'button', textContent: t.deleteBtn, onclick: function () {
				var ids = picked(box.list, 'bst-ad-pick');
				if (ids.length && window.confirm(t.deleteAsk)) { action(id, { op: 'delete', ids: ids }); }
			} }));
		}
		box.trash.textContent = '';
		if ((extra.trashed || []).length) {
			var bin = node('ul', { style: grid });
			extra.trashed.forEach(function (item) { bin.appendChild(card(id, item, 'bst-ad-bin')); });
			box.trash.appendChild(node('h3', { textContent: t.trash + ' (' + extra.trashed.length + ')' }));
			box.trash.appendChild(bin);
			box.trash.appendChild(selectAll(bin));
			box.trash.appendChild(node('button', { type: 'button', className: 'button', style: 'margin:0 6px', textContent: t.restoreBtn, onclick: function () {
				action(id, { op: 'restore', ids: picked(bin, 'bst-ad-bin') });
			} }));
			box.trash.appendChild(node('button', { type: 'button', className: 'button-link button-link-delete', textContent: t.purgeBtn, onclick: function () {
				var ids = picked(bin, 'bst-ad-bin');
				if (ids.length && window.confirm(t.purgeAsk)) { action(id, { op: 'purge', ids: ids }); }
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
				bulk: node('p', {}),
				trash: node('div', {})
			};
			box.button = node('button', { type: 'button', className: 'button button-primary', textContent: t.scan, onclick: function () {
				api(id + '/start', {}).then(function (s) { render(id, s); }).catch(fail);
			} });
			panels[id] = box;
			root.appendChild(node('div', { className: 'card', style: 'max-width:none' }, [node('h2', { textContent: m.label }), node('p', { textContent: m.intro }), box.button, box.status, box.note, box.list, box.bulk, box.trash]));
			render(id, state.modules[id]);
		});
	}).catch(fail);
})();
