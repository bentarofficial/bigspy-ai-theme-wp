/* BIGSPY AI Designer – Settings, Guidelines and Workspace screens. */
(function () {
	'use strict';
	var cfg = window.bstAid || {};
	var __ = (window.wp && wp.i18n && wp.i18n.__) || function (s) { return s; };
	var sprintf = (window.wp && wp.i18n && wp.i18n.sprintf) || function (s) { return s; };
	var T = 'bigspy-ai-theme-wp';

	/* ------------------------------------------------------------ helpers */
	var h = function (tag, attrs, kids) {
		var el = document.createElement(tag);
		Object.keys(attrs || {}).forEach(function (k) {
			var v = attrs[k];
			if (v === null || v === undefined || v === false) { return; }
			if (k === 'class') { el.className = v; }
			else if (k === 'text') { el.textContent = v; }
			else if (k.indexOf('on') === 0) { el.addEventListener(k.slice(2), v); }
			else { el.setAttribute(k, v === true ? '' : v); }
		});
		[].concat(kids || []).forEach(function (c) {
			if (c === null || c === undefined || c === false) { return; }
			el.appendChild(typeof c === 'string' ? document.createTextNode(c) : c);
		});
		return el;
	};
	var api = function (path, method, body) {
		// rest_url() may already hold a query (?rest_route=… on sites without pretty permalinks).
		var parts = path.split('?');
		var url = cfg.rest + parts[0] + (parts[1] ? (cfg.rest.indexOf('?') > -1 ? '&' : '?') + parts[1] : '');
		return fetch(url, {
			method: method || 'GET',
			headers: { 'X-WP-Nonce': cfg.nonce, 'Content-Type': 'application/json' },
			credentials: 'same-origin',
			body: body ? JSON.stringify(body) : undefined
		}).then(function (r) {
			return r.json().catch(function () { return {}; }).then(function (data) {
				if (!r.ok) { throw new Error((data && data.message) || ('HTTP ' + r.status)); }
				return data;
			});
		});
	};
	var money = function (v) { return '$' + (Number(v) || 0).toFixed(Number(v) < 1 ? 4 : 2); };
	/* Chat Markdown, rendered safely: text is escaped first, then headings, lists, rules,
	 * bold / italics / code / links become HTML; leftover markers (**, __, ##) never show. */
	var inline = function (t) {
		return t.replace(/`([^`\n]+)`/g, '<code>$1</code>')
			.replace(/\[([^\]\n]+)\]\(((?:https?:\/\/|tel:|mailto:)[^\s)]+)\)/g, '<a href="$2" target="_blank" rel="noopener">$1</a>')
			.replace(/(\*\*\*|___)(?=\S)(.+?)(?<=\S)\1/g, '<strong><em>$2</em></strong>')
			.replace(/(\*\*|__)(?=\S)(.+?)(?<=\S)\1/g, '<strong>$2</strong>')
			.replace(/(^|[\s(])\*(?=[^\s*])([^*\n]+?)(?<=\S)\*(?![\w*])/g, '$1<em>$2</em>')
			.split('**').join('').split('__').join('');
	};
	// Block-level Markdown: headings, rules, nested lists, tables and fenced code (G-192).
	var md = function (text) {
		var esc = String(text || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/\r/g, '');
		var lines = esc.split('\n'), out = [], stack = [];
		var closeTo = function (indent) {
			while (stack.length && stack[stack.length - 1].indent > indent) {
				var top = stack.pop(), html = '<' + top.kind + '>' + top.items.map(function (x) { return '<li>' + x + '</li>'; }).join('') + '</' + top.kind + '>';
				if (stack.length) { var parent = stack[stack.length - 1]; parent.items[parent.items.length - 1] += html; } else { out.push(html); }
			}
		};
		var flush = function () { closeTo(-1); };
		var cells = function (row) { return row.replace(/^\|/, '').replace(/\|\s*$/, '').split('|').map(function (c) { return inline(c.trim()); }); };
		for (var i = 0; i < lines.length; i++) {
			var line = lines[i], t = line.trim(), m;
			if (/^```/.test(t)) {
				flush();
				var lang = t.slice(3).trim(), code = [];
				for (i++; i < lines.length && !/^```/.test(lines[i].trim()); i++) { code.push(lines[i]); }
				out.push('<div class="bst-aid-code"><div class="bst-aid-code-bar"><span>' + (lang || 'code') + '</span><button type="button" class="bst-aid-copy" data-copy-code>' + __('Copy', T) + '</button></div><pre><code>' + code.join('\n') + '</code></pre></div>');
				continue;
			}
			if (/^\|/.test(t) && i + 1 < lines.length && /^\|?\s*:?-{3,}/.test(lines[i + 1].trim())) {
				flush();
				var head = cells(t), body = [];
				for (i += 2; i < lines.length && /^\|/.test(lines[i].trim()); i++) { body.push(cells(lines[i].trim())); }
				i--;
				out.push('<div class="bst-aid-table"><table><thead><tr>' + head.map(function (c) { return '<th>' + c + '</th>'; }).join('') + '</tr></thead><tbody>' +
					body.map(function (r) { return '<tr>' + r.map(function (c) { return '<td>' + c + '</td>'; }).join('') + '</tr>'; }).join('') + '</tbody></table></div>');
				continue;
			}
			if (!t) { flush(); continue; }
			if ((m = /^#{1,6}\s+(.+)$/.exec(t))) { flush(); out.push('<h4>' + inline(m[1].replace(/#+\s*$/, '')) + '</h4>'); continue; }
			if (/^(-{3,}|\*{3,}|_{3,})$/.test(t)) { flush(); out.push('<hr>'); continue; }
			if ((m = /^(?:[-*•+]|(\d+)[.)])\s+(.+)$/.exec(t))) {
				var kind = m[1] ? 'ol' : 'ul', indent = line.length - line.replace(/^\s+/, '').length;
				closeTo(indent);
				var top = stack[stack.length - 1];
				if (top && top.indent === indent && top.kind !== kind) { closeTo(indent - 1); top = null; }
				if (!top || top.indent < indent) { stack.push({ kind: kind, indent: indent, items: [] }); top = stack[stack.length - 1]; }
				top.items.push(inline(m[2]));
				continue;
			}
			flush();
			out.push(/^&gt;\s?/.test(t) ? '<blockquote>' + inline(t.replace(/^&gt;\s?/, '')) + '</blockquote>' : '<p>' + inline(t) + '</p>');
		}
		flush();
		return out.join('');
	};
	// Copy buttons in chat answers and code blocks.
	var copyText = function (text, btn) {
		var done = function () { var old = btn.textContent; btn.textContent = __('Copied', T); setTimeout(function () { btn.textContent = old; }, 1400); };
		if (navigator.clipboard && window.isSecureContext) { navigator.clipboard.writeText(text).then(done); return; }
		var ta = h('textarea', { style: 'position:fixed;opacity:0' }); ta.value = text; document.body.appendChild(ta); ta.select();
		try { document.execCommand('copy'); done(); } catch (e) { /* nothing to do */ }
		ta.remove();
	};
	// A styled confirm dialog: ask({ title, text, ok, danger }) resolves true or false. Falls back to window.confirm.
	var ask = function (o) {
		if (typeof window.HTMLDialogElement !== 'function') { return Promise.resolve(window.confirm(o.text || o.title)); }
		return new Promise(function (resolve) {
			var okBtn = h('button', { type: 'submit', value: 'ok', class: 'button ' + (o.danger ? 'bst-aid-danger' : 'button-primary'), text: o.ok || __('OK', T) });
			var dlg = h('dialog', { class: 'bst-aid-dialog bst-aid', 'aria-labelledby': 'bst-aid-dlg-title' }, h('form', { method: 'dialog' }, [
				h('h2', { id: 'bst-aid-dlg-title', text: o.title || __('Are you sure?', T) }),
				o.text ? h('p', { text: o.text }) : null,
				h('div', { class: 'bst-aid-dialog-acts' }, [h('button', { type: 'submit', value: 'cancel', class: 'button', text: __('Cancel', T) }), okBtn])
			]));
			dlg.addEventListener('close', function () { resolve(dlg.returnValue === 'ok'); dlg.remove(); });
			document.body.appendChild(dlg);
			dlg.showModal();
			(o.danger ? dlg.querySelector('[value="cancel"]') : okBtn).focus();
		});
	};
	document.addEventListener('click', function (e) {
		var b = e.target.closest && e.target.closest('[data-copy-code]');
		if (b) { copyText(b.closest('.bst-aid-code').querySelector('code').textContent, b); }
	});

	/* ------------------------------------------------------------ Guidelines */
	document.addEventListener('click', function (e) {
		var b = e.target.closest && e.target.closest('[data-bst-aid-template]');
		if (!b) { return; }
		var area = document.getElementById(b.getAttribute('data-bst-aid-template'));
		if (!area) { return; }
		(area.value.trim() ? ask({ title: __('Replace the current text with the template?', T), ok: __('Replace', T) }) : Promise.resolve(true)).then(function (yes) {
			if (!yes) { return; }
			area.value = b.getAttribute('data-template');
			area.focus();
			saveContext(area, 0);
		});
	});

	// Guidelines and specialty boxes save as you type; a version guards against overwriting an AI change.
	var saveTimers = {};
	var saveContext = function (area, wait) {
		var key = area.getAttribute('data-bst-aid-context');
		var status = area.parentNode.querySelector('.bst-aid-saved');
		var show = function (text, bad) { if (status) { status.textContent = text; status.className = 'bst-aid-saved' + (bad ? ' is-error' : ''); } };
		clearTimeout(saveTimers[key]);
		show(__('Saving…', T));
		saveTimers[key] = setTimeout(function () {
			api('/context', 'POST', { key: key, text: area.value, version: Number(area.getAttribute('data-version')) || 0 })
				.then(function (r) { area.setAttribute('data-version', r.version); if (r.text !== area.value && document.activeElement !== area) { area.value = r.text; } show(__('Saved', T)); })
				.catch(function (e) { show(e.message, true); });
		}, wait);
	};
	document.addEventListener('input', function (e) {
		if (e.target.getAttribute && e.target.getAttribute('data-bst-aid-context')) { saveContext(e.target, 800); }
	});
	document.addEventListener('click', function (e) {
		var b = e.target.closest && e.target.closest('[data-bst-aid-reset]');
		if (!b) { return; }
		var area = document.getElementById(b.getAttribute('data-bst-aid-reset'));
		if (area) { ask({ title: __('Restore the default context for this specialty?', T), ok: __('Restore default', T) }).then(function (yes) { if (yes) { area.value = ''; saveContext(area, 0); } }); }
	});

	// Two panes (G-207): a list of standards and specialties on the left, one editor on the right.
	// The forms stay in the page as printed, so they still work without this script.
	(function guidesPanes() {
		var page = document.querySelector('.bst-aid-app--guidelines .bst-aid-page');
		if (!page) { return; }
		var cards = Array.prototype.filter.call(page.querySelectorAll('.bst-aid-card'), function (c) { return c.querySelector('textarea[data-bst-aid-context]'); });
		if (!cards.length) { return; }
		var items = [];
		var nav = h('nav', { class: 'bst-aid-gnav', 'aria-label': __('Guidelines', T) });
		var group = '';
		cards.forEach(function (card, i) {
			var area = card.querySelector('textarea'), key = area.getAttribute('data-bst-aid-context');
			var g = key.indexOf('sp_') === 0 ? __('Specialties', T) : __('Standards', T);
			if (g !== group) { group = g; nav.appendChild(h('p', { class: 'bst-aid-gnav-group', text: g })); }
			var title = (card.querySelector('h2, h3') || {}).textContent || key;
			var count = h('small', { class: 'bst-aid-gnav-count' });
			var dot = h('span', { class: 'bst-aid-gnav-dot', 'aria-hidden': 'true' });
			var btn = h('button', { type: 'button', class: 'bst-aid-gnav-item', 'aria-controls': card.id || (card.id = 'bst-aid-g-' + i), onclick: function () { select(i, true); } }, [dot, h('span', { class: 'bst-aid-gnav-title', text: title }), count]);
			var counter = h('span', { class: 'bst-aid-counter', 'aria-live': 'off' });
			area.parentNode.insertBefore(counter, area.nextSibling);
			var sync = function () {
				var n = area.value.length, def = area.getAttribute('data-default');
				var edited = def === null ? n > 0 : area.value.trim() !== def.trim();
				count.textContent = n ? n.toLocaleString() : '';
				counter.textContent = sprintf(__('%1$s / %2$s characters', T), n.toLocaleString(), (6000).toLocaleString());
				counter.classList.toggle('is-near', n > 5400);
				dot.classList.toggle('is-on', edited);
				dot.title = edited ? (def === null ? __('Filled in', T) : __('Changed from the default', T)) : '';
			};
			area.addEventListener('input', sync);
			new window.MutationObserver(sync).observe(area, { attributes: true }); // A template or reset changes the value then the version.
			sync();
			items.push({ card: card, btn: btn, area: area });
			nav.appendChild(btn);
		});
		var select = function (i, focus) {
			items.forEach(function (it, j) { it.card.classList.toggle('is-shown', i === j); it.btn.setAttribute('aria-current', i === j ? 'true' : 'false'); });
			try { window.localStorage.setItem('bstAidGuide', String(i)); } catch (e) { /* storage blocked */ }
			if (focus) { items[i].area.focus(); }
		};
		var picker = h('select', { class: 'bst-aid-gpick', 'aria-label': __('Guideline to edit', T), onchange: function (e) { select(Number(e.target.value), false); } }, items.map(function (it, i) { return h('option', { value: i, text: it.btn.querySelector('.bst-aid-gnav-title').textContent }); }));
		var start = 0;
		try { start = Math.min(items.length - 1, Math.max(0, parseInt(window.localStorage.getItem('bstAidGuide'), 10) || 0)); } catch (e) { /* storage blocked */ }
		page.classList.add('is-panes');
		page.insertBefore(h('div', { class: 'bst-aid-gside' }, [nav, picker]), page.firstChild);
		picker.value = String(start);
		nav.addEventListener('click', function () { picker.value = String(items.findIndex(function (it) { return it.card.classList.contains('is-shown'); })); });
		select(start, false);
	})();

	/* ------------------------------------------------------------ Settings */
	function Settings(root) {
		var state = null, busy = false, msg = '', msgType = 'info', saveBar = null;
		var load = function (refresh) {
			return api('/settings' + (refresh ? '?refresh=1' : '')).then(function (s) { state = s; render(); }).catch(function (e) { msg = e.message; msgType = 'error'; render(); });
		};
		var act = function (path, body, confirmText) {
			var run = function () {
				busy = true; msg = ''; render();
				api(path, 'POST', body).then(function (s) { state = s; busy = false; msg = __('Done.', T); msgType = 'success'; render(); })
					.catch(function (e) { busy = false; msg = e.message; msgType = 'error'; render(); });
			};
			if (!confirmText) { run(); return; }
			ask({ title: confirmText, ok: __('Delete', T), danger: true }).then(function (yes) { if (yes) { run(); } });
		};
		var modelOptions = function (list, value, showPrice) {
			var opts = [h('option', { value: '', text: __('— Choose a model —', T) })];
			// Text models come in tiers (value / premium); image models keep a recommended flag.
			var tier = function (m) { return m.tier || (m.recommended ? 'value' : 'all'); };
			var rec = list.filter(function (m) { return 'value' === tier(m); });
			var premium = list.filter(function (m) { return 'premium' === tier(m); });
			var rest = list.filter(function (m) { return 'all' === tier(m); });
			var opt = function (m) {
				var label = m.name + (m.id !== m.name ? ' (' + m.id + ')' : '');
				if (showPrice && (m.input || m.output)) { label += ' – $' + m.input + ' / $' + m.output; }
				if (m.price) { label += ' – ' + sprintf(__('$%s per image', T), m.price); }
				if (m.free) { label += ' – ' + __('free tier', T); }
				return h('option', { value: m.id, selected: m.id === value, text: label });
			};
			if (rec.length) { opts.push(h('optgroup', { label: showPrice ? __('Recommended – strong and low cost', T) : __('Recommended', T) }, rec.map(opt))); }
			if (premium.length) { opts.push(h('optgroup', { label: __('Most capable – higher cost', T) }, premium.map(opt))); }
			opts.push(h('optgroup', { label: __('All models', T) }, rest.map(opt)));
			return opts;
		};
		var render = function () {
			root.innerHTML = ''; saveBar = null;
			if (!state) { root.appendChild(h('p', { text: msg || __('Loading…', T) })); return; }
			var kids = [];
			if (msg) { kids.push(h('div', { class: 'notice notice-' + msgType + ' inline' }, h('p', { text: msg }))); }
			if (!state.hasKey) {
				var input = h('input', { type: 'password', class: 'regular-text', placeholder: 'kyma-…', autocomplete: 'off', 'aria-label': __('API key', T) });
				kids.push(h('div', { class: 'bst-aid-card', id: 'bst-aid-s-connect' }, [
					h('h2', { text: __('Connect the API', T) }),
					h('p', { class: 'description', text: __('Paste your Kyma API key, then press Connect. The key is checked, encrypted and kept on the server only; it is never shown again in full.', T) }),
					h('p', { class: 'bst-aid-row' }, [input, h('button', { class: 'button button-primary', disabled: busy, onclick: function () { act('/connect', { key: input.value }); } , text: busy ? __('Connecting…', T) : __('Connect', T) })]),
					h('p', { class: 'description' }, [__('No key yet? Create one at ', T), h('a', { href: 'https://kymaapi.com/?aff=BIGSPY-AI-Connect', target: '_blank', rel: 'noopener sponsored', text: 'kymaapi.com' }), '.'])
				]));
			} else {
				var status = state.connected
					? h('span', { class: 'bst-aid-status is-on', text: __('Connected', T) })
					: h('span', { class: 'bst-aid-status is-off', text: __('Disconnected', T) });
				var bal = state.balance ? h('span', { class: 'bst-aid-balance' + (state.balance.low ? ' is-low' : ''), text: sprintf(__('Balance: %s', T), money(state.balance.balance)) }) : null;
				kids.push(h('div', { class: 'bst-aid-card', id: 'bst-aid-s-connect' }, [
					h('h2', { text: __('API connection', T) }),
					h('p', { class: 'bst-aid-row' }, [h('code', { class: 'bst-aid-key', text: state.keyMasked }), status, bal]),
					h('p', { class: 'bst-aid-row' }, [
						state.connected
							? h('button', { class: 'button', disabled: busy, onclick: function () { act('/disconnect'); }, text: __('Disconnect', T) })
							: h('button', { class: 'button button-primary', disabled: busy, onclick: function () { act('/reconnect'); }, text: __('Reconnect', T) }),
						h('button', { class: 'button button-link-delete', disabled: busy, onclick: function () { act('/key/delete', null, __('Delete the API key from this site? You will need to paste it again to use the AI Designer.', T)); }, text: __('Delete API key', T) })
					]),
					h('p', { class: 'description', text: __('Disconnect pauses the AI Designer and keeps the key. Delete API key removes it from the database.', T) })
				]));
				if (state.connected && state.models) {
					var text = h('select', { class: 'bst-aid-select' }, modelOptions(state.models.text || [], state.textModel, true));
					var image = h('select', { class: 'bst-aid-select' }, modelOptions(state.models.image || [], state.imageModel, false));
					var fast = h('select', { class: 'bst-aid-select' }, modelOptions(state.models.text || [], state.fastModel, true));
					fast.options[0].text = __('— Same as the text model —', T);
					var allowed = h('select', { class: 'bst-aid-select', multiple: true, size: 6 }, (state.models.text || []).map(function (m) { return h('option', { value: m.id, selected: (state.allowedModels || []).indexOf(m.id) !== -1, text: m.name + ' (' + m.id + ')' }); }));
					var bTask = h('input', { type: 'number', min: '0', step: '0.5', class: 'small-text', value: state.budgetTask });
					var bDay = h('input', { type: 'number', min: '0', step: '1', class: 'small-text', value: state.budgetDay });
					var testBox = h('div', { class: 'bst-aid-test' });
					var showTest = function (r) {
						testBox.innerHTML = '';
						if (r && r.error) { testBox.appendChild(h('div', { class: 'notice notice-error inline' }, h('p', { text: r.error }))); return; }
						if (r && r.status === 'pending') {
							testBox.appendChild(h('p', { class: 'bst-aid-working' }, [h('span', { class: 'spinner is-active' }), __('Still creating the image…', T)]));
							api('/images/job', 'POST', { job_id: r.job_id }).then(showTest).catch(function (e) { showTest({ error: e.message }); });
							return;
						}
						testBox.appendChild(h('div', { class: 'bst-aid-test-ok' }, [
							h('a', { href: r.url, target: '_blank', rel: 'noopener' }, h('img', { src: r.medium || r.url, alt: r.alt || '' })),
							h('div', null, [
								h('p', null, h('strong', { text: __('ImageGen works.', T) })),
								h('p', { class: 'description', text: sprintf(__('Saved to the Media Library: %1$s × %2$s px, with title, alt text and caption.', T), r.width, r.height) }),
								h('p', null, [h('a', { href: (r.edit || ('post.php?post=' + r.id + '&action=edit')), target: '_blank', rel: 'noopener', text: __('Open in the Media Library', T) })])
							])
						]));
					};
					var testBtn = h('button', { class: 'button', disabled: busy || !state.imageModel, title: state.imageModel ? '' : __('Save an image model first.', T), onclick: function () {
						testBtn.disabled = true;
						testBox.innerHTML = '';
						testBox.appendChild(h('p', { class: 'bst-aid-working' }, [h('span', { class: 'spinner is-active' }), __('Creating a test image… this takes about 10–40 seconds.', T)]));
						api('/images/test', 'POST').then(function (r) { testBtn.disabled = false; showTest(r); }).catch(function (e) { testBtn.disabled = false; showTest({ error: e.message }); });
					}, text: __('Test ImageGen', T) });
					var permissionInputs = {};
					var groupLabels = { content: __('Content', T), images: __('Images', T), design: __('Global design', T), site: __('WordPress settings', T), publish: __('Publishing', T), seo: __('SEO metadata', T), plugins: __('Plugins', T), budget: __('Budget extensions', T) };
					var permissionRows = Object.keys(groupLabels).map(function (group) {
						var select = h('select', { 'data-save': '1' }, [1, 2, 3].map(function (level) { return h('option', { value: level, selected: level === Number((state.permissions || {})[group] || 1), text: [__('Automatic (mandatory approvals still apply)', T), __('Always ask', T), __('Guidance only', T)][level - 1] }); }));
						permissionInputs[group] = select; return h('p', null, [h('label', { text: groupLabels[group] + ': ' }), select]);
					});
					var saveAll = function () { act('/settings', { textModel: text.value, fastModel: fast.value, allowedModels: Array.prototype.filter.call(allowed.options, function (o) { return o.selected; }).map(function (o) { return o.value; }), imageModel: image.value, budgetTask: bTask.value, budgetDay: bDay.value, permissions: Object.keys(permissionInputs).reduce(function (out, key) { out[key] = Number(permissionInputs[key].value); return out; }, {}) }); };
					[text, image, fast, allowed, bTask, bDay].forEach(function (el) { el.setAttribute('data-save', '1'); });
					saveBar = h('div', { class: 'bst-aid-savebar', hidden: true, role: 'region', 'aria-label': __('Unsaved changes', T) }, [
						h('span', { text: __('You have unsaved changes.', T) }),
						h('button', { type: 'button', class: 'button', disabled: busy, onclick: function () { render(); }, text: __('Discard', T) }),
						h('button', { type: 'button', class: 'button button-primary', disabled: busy, onclick: saveAll, text: __('Save changes', T) })
					]);
					kids.push(h('div', { class: 'bst-aid-card', id: 'bst-aid-s-models' }, [
						h('h2', { text: __('Models', T) }),
						h('p', null, [h('label', { class: 'bst-aid-label', text: __('Text model (writes, designs and calls the tools)', T) }), text]),
						h('p', { class: 'description', text: __('Only models that can call tools are listed. Prices are per million input / output tokens. Start with a recommended model; switch to a most capable one if results are not good enough.', T) }),
						h('p', null, [h('label', { class: 'bst-aid-label', text: __('Fast model (Effort Low: lookups and small edits)', T) }), fast]),
						h('p', { class: 'description', text: __('Optional. Runs sent with Effort Low use this cheaper, faster model; other efforts use the text model for writing and design. If the fast model fails, the run switches to the text model.', T) }),
						h('p', null, [h('label', { class: 'bst-aid-label', text: __('Models users may pick in the chat', T) }), allowed]),
						h('p', { class: 'description', text: __('Select none to offer every text model. Ctrl/Cmd-click to select several. Auto always stays available.', T) }),
						h('p', null, [h('label', { class: 'bst-aid-label', text: __('Image model (used by ImageGen)', T) }), image]),
						h('p', null, [
							h('button', { class: 'button', disabled: busy, onclick: function () { load(true); }, text: __('Refresh model list', T) }),
							' ',
							testBtn
						]),
						h('p', { class: 'description', text: __('Test ImageGen creates one low-quality image with the saved image model (a few cents at most) and adds it to the Media Library, so you can check the whole image path before a real task.', T) }),
						testBox
					]));
					kids.push(h('div', { class: 'bst-aid-card', id: 'bst-aid-s-budget' }, [
						h('h2', { text: __('Budget', T) }),
						h('p', { class: 'bst-aid-row' }, [h('label', null, [__('Budget per task (USD, 0 = no limit) ', T), bTask]), h('label', null, [__('Budget per day (USD, 0 = no limit) ', T), bDay])]),
						h('p', { class: 'description', text: sprintf(__('Spent today: %s', T), money(state.spentToday)) })
					]));
					kids.push(h('div', { class: 'bst-aid-card', id: 'bst-aid-s-permissions' }, [h('h2', { text: __('Tool permissions', T) })].concat(permissionRows)));
				}
			}
			if (state.hasKey) {
				var unusedBox = h('div', { class: 'bst-aid-unused' }, h('button', { class: 'button', onclick: function () {
					unusedBox.textContent = __('Checking…', T);
					api('/images/unused').then(function (r) {
						unusedBox.innerHTML = '';
						unusedBox.appendChild(h('p', { text: sprintf(__('%1$d of %2$d AI images and chat uploads are not used anywhere.', T), r.unused.length, r.total) }));
						unusedBox.appendChild(h('div', { class: 'bst-aid-unused-grid' }, r.unused.map(function (i) {
							return h('a', { href: i.edit, target: '_blank', rel: 'noopener', title: i.title }, h('img', { src: i.thumb, alt: i.title }));
						})));
					}).catch(function (e) { unusedBox.textContent = e.message; });
				}, text: __('Find unused AI images', T) }));
				kids.push(h('div', { class: 'bst-aid-card', id: 'bst-aid-s-images' }, [
					h('h2', { text: __('AI images', T) }),
					h('p', { class: 'description', text: __('ImageGen never deletes media. Find generated images that no page, post, product or setting uses, open them and delete the ones you do not need.', T) }),
					unusedBox
				]));
			}
			kids.push(h('details', { class: 'bst-aid-card bst-aid-card--danger', id: 'bst-aid-s-data' }, [
				h('summary', null, h('h2', { text: __('Remove AI Designer data', T) })),
				h('p', { class: 'description', text: __('Deletes the API key, settings, guidelines and all task history. Pages, posts and products already created stay.', T) }),
				h('button', { class: 'button button-link-delete', disabled: busy, onclick: function () { act('/data/delete', null, __('Delete all AI Designer data? This cannot be undone.', T)); }, text: __('Delete AI Designer data', T) })
			]));
			// Two columns: a sticky table of contents and the cards, with a summary strip on top (G-207).
			var cards = kids.filter(function (k) { return k.id; });
			var summary = h('div', { class: 'bst-aid-summary' }, [
				[__('Connection', T), state.hasKey ? (state.connected ? __('Connected', T) : __('Disconnected', T)) : __('Not set up', T), state.hasKey && state.connected ? 'is-on' : 'is-off'],
				[__('Balance', T), state.balance ? money(state.balance.balance) : '—', state.balance && state.balance.low ? 'is-off' : ''],
				[__('Text model', T), state.textModel || '—', ''],
				[__('Spent today', T), money(state.spentToday) + (Number(state.budgetDay) ? ' / ' + money(state.budgetDay) : ''), '']
			].map(function (c) { return h('div', { class: 'bst-aid-summary-item ' + c[2] }, [h('span', { text: c[0] }), h('strong', { text: c[1], title: c[1] })]); }));
			var toc = h('nav', { class: 'bst-aid-toc', 'aria-label': __('Settings sections', T) }, cards.map(function (c) {
				return h('a', { href: '#' + c.id, onclick: function (e) {
					e.preventDefault();
					if (c.tagName === 'DETAILS') { c.open = true; }
					c.scrollIntoView({ behavior: window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth', block: 'start' });
				}, text: c.querySelector('h2').textContent });
			}));
			var main = h('div', { class: 'bst-aid-settings-main' }, [summary].concat(kids, saveBar ? [saveBar] : []));
			root.appendChild(h('div', { class: 'bst-aid-settings-grid' }, [toc, main]));
			if (saveBar) {
				var dirty = function (e) { if (e.target.getAttribute && e.target.getAttribute('data-save')) { saveBar.hidden = false; } };
				main.addEventListener('input', dirty);
				main.addEventListener('change', dirty);
			}
			// Highlight the section in view.
			if (window.IntersectionObserver) {
				var links = toc.querySelectorAll('a');
				var io = new window.IntersectionObserver(function (entries) {
					entries.forEach(function (en) { if (en.isIntersecting) { links.forEach(function (a) { a.classList.toggle('is-active', a.getAttribute('href') === '#' + en.target.id); }); } });
				}, { root: root.closest('.bst-aid-body'), rootMargin: '0px 0px -70% 0px' });
				cards.forEach(function (c) { io.observe(c); });
			}
		};
		render();
		load();
	}

	/* ------------------------------------------------------------ Workspace */
	function Workspace(root) {
		var settings = null, tasks = [], current = null, log = [], running = false, stopFlag = false, error = '';
		var welcomePrompt = new URLSearchParams(window.location.search).get('bst_prompt') || '';
		var welcomeMode = new URLSearchParams(window.location.search).get('bst_mode') === 'advice' ? 'advice' : 'design';
		try {
			if (welcomePrompt) { window.sessionStorage.setItem('bstAidWelcomePrompt', welcomePrompt.slice(0, 12000)); window.sessionStorage.setItem('bstAidWelcomeMode', welcomeMode); }
			welcomePrompt = window.sessionStorage.getItem('bstAidWelcomePrompt') || '';
			welcomeMode = window.sessionStorage.getItem('bstAidWelcomeMode') === 'advice' ? 'advice' : 'design';
		} catch (e) { /* The URL still provides the prompt when session storage is unavailable. */ }
		var effort = 'medium', chatModel = '', specialty = 'auto';
		try { effort = window.localStorage.getItem('bstAidEffort') || 'medium'; chatModel = window.localStorage.getItem('bstAidModel') || ''; specialty = window.localStorage.getItem('bstAidSpecialty') || 'auto'; } catch (e) { /* storage blocked */ }
		var previewOn = true, previewUrl = '', previews = [], previewDevice = 'fit', chatWidth = 400, railMin = null, mobileView = 'chat';
		try {
			previewOn = window.localStorage.getItem('bstAidPreview') !== '0';
			var dev = window.localStorage.getItem('bstAidDevice');
			previewDevice = ['1280', '768', '390'].indexOf(dev) > -1 ? Number(dev) : 'fit';
			chatWidth = Math.min(560, Math.max(340, parseInt(window.localStorage.getItem('bstAidChatW'), 10) || 400));
			var rail = window.localStorage.getItem('bstAidRail');
			railMin = rail === null ? null : rail === '1';
		} catch (e) { /* storage blocked */ }
		var els = {};

		var toolLabel = function (name) {
			var map = {
				site_info: __('Reading the site', T), list_content: __('Listing content', T), get_post: __('Reading content', T),
				create_post: __('Created a post', T), update_post: __('Updated a post', T), create_product: __('Created a product', T),
				update_product: __('Updated a product', T), save_guidelines: __('Saved the guidelines', T),
				list_blocks: __('Reading the blocks', T), block_catalog: __('Choosing blocks', T), section_recipes: __('Choosing section recipes', T), page_plan: __('Page outline', T), build_sections: __('Building sections', T), block_schema: __('Reading block options', T), list_patterns: __('Looking at patterns', T),
				get_pattern: __('Reading a pattern', T), create_page: __('Created a page', T), page_sections: __('Updated page sections', T), get_page: __('Reading a page', T), update_page: __('Updated a page', T),
				get_settings: __('Reading theme settings', T), update_settings: __('Changed theme settings', T), set_logo: __('Set the logo', T),
				custom_css: __('Additional CSS', T), get_menus: __('Reading menus', T), set_menu: __('Saved a menu', T), header_layout: __('Header layout', T),
				show_palette: __('Color options', T), ImageGen: __('ImageGen created an image', T), image_status: __('ImageGen', T),
				optimize_image: __('Optimized an image', T), list_media: __('Looking at the Media Library', T)
			};
			return map[name] || name;
		};
		var eventNode = function (ev) {
			if (ev.kind === 'approval') {
				var a = ev.approval, taskId = current.id, labels = [__('Agree', T), __('I will do it myself', T), __('Skip', T)];
				var resolved = log.some(function (event) { return event.approval_id === a.id; });
				var countdown = h('span', { class: 'bst-aid-countdown', 'aria-live': 'off' });
				var buttons = ['approve', 'manual', 'skip'].map(function (decision, index) {
					return h('button', { type: 'button', class: 'button' + (index === 0 ? ' button-primary' : ''), text: labels[index], onclick: function () {
						buttons.forEach(function (b) { b.disabled = true; });
						api('/tasks/' + taskId + '/approval', 'POST', { approval_id: a.id, decision: decision }).then(function (r) {
							box.appendChild(h('p', { text: r.result.instructions || r.result.error || labels[index] }));
							resolved = true; tick();
							if (current && current.id === taskId) { addEvents(r.events); updateTask(r.task); }
						}).catch(function (e) { box.appendChild(h('p', { text: e.message })); buttons.forEach(function (b) { b.disabled = false; }); });
					} });
				});
				var box = h('div', { class: 'bst-aid-card bst-aid-approval' }, [
					h('h3', { text: __('Approval required', T) + ': ' + toolLabel(a.tool) }),
					h('div', { class: 'bst-aid-approval-meta' }, [a.scope ? h('p', { text: a.scope }) : null, a.cost ? h('p', { text: a.cost }) : null, a.undo ? h('p', { class: 'description', text: a.undo }) : null]),
					h('details', null, [h('summary', { text: __('Before / after', T) }), h('pre', { text: JSON.stringify({ before: a.before, after: a.after }, null, 2) })]),
					a.preview ? h('p', null, h('a', { href: a.preview, target: '_blank', rel: 'noopener', text: __('Preview', T) })) : null,
					h('div', { class: 'bst-aid-approval-acts' }, buttons.concat([countdown]))
				]);
				// Approvals expire after 15 minutes: show the time left and lock the buttons when it runs out.
				var timer = null, tick = function () {
					var left = Math.round(a.expires - Date.now() / 1000);
					if (resolved) { countdown.textContent = ''; buttons.forEach(function (b) { b.disabled = true; }); clearInterval(timer); return; }
					if (left <= 0) { countdown.textContent = __('Expired', T); box.classList.add('is-expired'); buttons.forEach(function (b) { b.disabled = true; }); clearInterval(timer); return; }
					countdown.textContent = sprintf(__('%s left', T), Math.floor(left / 60) + ':' + ('0' + (left % 60)).slice(-2));
					if (!document.body.contains(box) && timer) { clearInterval(timer); }
				};
				tick(); timer = setInterval(tick, 1000);
				return box;
			}
			if (ev.kind === 'user') {
				return h('div', { class: 'bst-aid-msg is-user' }, h('div', { class: 'bst-aid-bubble' }, [h('span', { text: ev.text }), (ev.files || []).length ? h('div', { class: 'bst-aid-files' }, ev.files.map(fileChip)) : null]));
			}
			if (ev.kind === 'assistant') {
				var b = h('div', { class: 'bst-aid-bubble' }); b.innerHTML = md(ev.text);
				var idx = log.indexOf(ev), asked = '';
				for (var k = (idx < 0 ? log.length : idx) - 1; k >= 0; k--) { if (log[k].kind === 'user') { asked = log[k].text; break; } }
				var copyBtn = h('button', { type: 'button', class: 'bst-aid-act', onclick: function () { copyText(ev.text, copyBtn); }, text: __('Copy', T) });
				var actions = h('div', { class: 'bst-aid-acts' }, [copyBtn, asked ? h('button', { type: 'button', class: 'bst-aid-act', title: __('Send your last message again', T), onclick: function () { sendText(asked); }, text: __('Retry', T) }) : null]);
				return h('div', { class: 'bst-aid-msg is-ai' }, [h('span', { class: 'bst-aid-avatar', 'aria-hidden': 'true' }), h('div', { class: 'bst-aid-ai-body' }, [b, actions])]);
			}
			if (ev.kind === 'tool' || ev.kind === 'tool-error') {
				var links = (ev.links || []).map(function (l) {
					return h('span', { class: 'bst-aid-links' }, [
						l.image ? h('a', { href: l.view, target: '_blank', rel: 'noopener' }, h('img', { class: 'bst-aid-thumb', src: l.image, alt: l.label || '' })) : null,
						l.label ? h('strong', { text: l.label }) : null,
						l.edit ? h('a', { href: l.edit, target: '_blank', rel: 'noopener', text: __('Edit', T) }) : null,
						l.view ? h('a', { href: l.view, target: '_blank', rel: 'noopener', text: __('View', T) }) : null]);
				});
				if (ev.preview) {
					links.push(h('button', { type: 'button', class: 'button-link', onclick: function () { previewUrl = ev.preview; setPreviewOn(true); }, text: __('Preview here', T) }));
				}
				if (ev.context_undo && current) {
					var undoBtn = h('button', { type: 'button', class: 'button-link', text: __('Undo this save', T), onclick: function () {
						undoBtn.disabled = true;
						api('/tasks/' + current.id + '/context-undo', 'POST', { undo: ev.context_undo.id }).then(function (r) { addEvents(r.events); undoBtn.hidden = true; }).catch(function (e) { undoBtn.disabled = false; addEvents([{ kind: 'error', text: e.message }]); });
					} });
					links.push(h('span', { text: ' ' + (ev.context_undo.keys || []).join(', ') + ' ' }), undoBtn);
				}
				var row = h('div', { class: 'bst-aid-tool' + (ev.kind === 'tool-error' ? ' is-error' : '') }, [h('span', { class: 'dashicons ' + (ev.kind === 'tool-error' ? 'dashicons-warning' : 'dashicons-yes-alt') }), h('span', { text: toolLabel(ev.tool) + (ev.text ? ': ' + ev.text : '') })].concat(links));
				if (ev.plan) {
					return h('div', null, [row, h('div', { class: 'bst-aid-card bst-aid-plan' }, [
						h('strong', { text: ev.plan.page || '' }),
						h('ol', null, (ev.plan.sections || []).map(function (s) {
							return h('li', null, [h('strong', { text: s.title || s.recipe || '' }), s.purpose ? h('span', { text: ' – ' + s.purpose }) : null]);
						})),
						h('p', { class: 'description', text: __('Reply OK to build every section of this page in one go, or tell the AI what to change.', T) })
					])]);
				}
				if (!ev.palettes) { return row; }
				return h('div', null, [row, h('div', { class: 'bst-aid-palettes' }, ev.palettes.map(function (p) {
					return h('div', { class: 'bst-aid-palette' }, [
						h('strong', { text: p.name || '' }),
						h('div', { class: 'bst-aid-swatches' }, Object.keys(p.colors || {}).map(function (role) {
							return h('span', { class: 'bst-aid-swatch', title: role + ' ' + p.colors[role] }, [h('i', { style: 'background:' + p.colors[role] }), h('small', { text: role }), h('code', { text: p.colors[role] })]);
						})),
						p.fonts && (p.fonts.heading || p.fonts.body) ? h('p', { class: 'bst-aid-fonts', text: [p.fonts.heading, p.fonts.body].filter(Boolean).join(' / ') }) : null,
						p.note ? h('p', { class: 'description', text: p.note }) : null,
						h('button', { type: 'button', class: 'button button-small', onclick: function () { els.input.value = sprintf(__('Use the "%s" palette.', T), p.name || ''); autoGrow(); els.input.focus(); }, text: __('Use this palette', T) })
					]);
				}))]);
			}
			return h('div', { class: 'bst-aid-note' + (ev.kind === 'error' ? ' is-error' : ''), text: ev.text + (ev.approved_by ? ' · #' + ev.approved_by + ' · ' + new Date(ev.t * 1000).toLocaleString() : '') });
		};
		var scroll = function () { if (els.chat) { els.chat.scrollTop = els.chat.scrollHeight; } };
		// Consecutive plain tool rows fold into "Did N things"; cards (plan, palettes, approvals) and errors stay visible.
		var groupable = function (ev) { return (ev.kind === 'tool' || ev.kind === 'tool-error') && !ev.plan && !ev.palettes && !ev.context_undo; };
		var place = function (ev) {
			var node = eventNode(ev), prev = els.working ? els.working.previousElementSibling : els.chat.lastElementChild;
			if (!groupable(ev)) { els.chat.insertBefore(node, els.working || null); return; }
			var group = prev && prev.classList.contains('bst-aid-group') ? prev : null;
			if (!group) {
				group = h('details', { class: 'bst-aid-group' }, [h('summary'), h('div', { class: 'bst-aid-group-rows' })]);
				els.chat.insertBefore(group, els.working || null);
			}
			group.lastElementChild.appendChild(node);
			var n = group.lastElementChild.children.length, bad = group.querySelectorAll('.bst-aid-tool.is-error').length;
			group.classList.toggle('has-error', !!bad);
			group.classList.toggle('is-single', n === 1);
			// One row shows as is; from two rows on the group folds, and stays open when something failed.
			if (n === 1 || bad) { group.open = true; } else if (n === 2 && !group.hasAttribute('data-touched')) { group.open = false; }
			group.firstElementChild.textContent = sprintf(__('Did %d things', T), n) + (bad ? ' · ' + sprintf(__('%d with a problem', T), bad) : '');
			group.firstElementChild.onclick = function () { group.setAttribute('data-touched', '1'); };
		};
		var addEvents = function (evs) {
			(evs || []).forEach(function (ev) {
				log.push(ev);
				if (els.chat) { place(ev); }
				if (ev.preview) { rememberPreview(ev); syncPreview(); } // Show a page as soon as it is built or changed.
			});
			scroll();
		};
		var updateTask = function (t) {
			if (!t) { return; }
			tasks = tasks.map(function (x) { return x.id === t.id ? t : x; });
			if (current && current.id === t.id) { current = t; }
			renderTasks(); renderHead();
		};

		/* Layout (G-207): rail with tasks, conversation, preview. States: empty, chat, chat + preview. */
		var narrow = function () { return root.clientWidth < 1024 || window.innerWidth < 1024; };
		var setRail = function (min) {
			railMin = min;
			try { window.localStorage.setItem('bstAidRail', min ? '1' : '0'); } catch (e) { /* storage blocked */ }
			syncLayout();
		};
		var openDrawer = function (on) {
			root.classList.toggle('is-drawer', on);
			if (els.drawerBtn) { els.drawerBtn.setAttribute('aria-expanded', on ? 'true' : 'false'); }
			if (on && els.search) { els.search.focus(); }
		};
		var syncLayout = function () {
			var hasPreview = previewOn && !!previewUrl;
			var empty = !current || !log.length;
			root.classList.toggle('has-preview', hasPreview);
			root.classList.toggle('is-empty', empty && !running);
			// The rail folds to icons beside a preview or in a narrow area; it never disappears.
			var autoMin = hasPreview || root.clientWidth < 1200;
			root.classList.toggle('is-rail-min', railMin === null ? autoMin : railMin);
			root.classList.toggle('is-narrow', narrow());
			root.classList.toggle('show-preview', hasPreview && mobileView === 'preview' && narrow());
			if (els.railBtn) { els.railBtn.setAttribute('aria-expanded', root.classList.contains('is-rail-min') ? 'false' : 'true'); }
			if (els.viewTabs) {
				els.viewTabs.hidden = !(hasPreview && narrow());
				els.viewTabs.querySelectorAll('button').forEach(function (b) { b.setAttribute('aria-pressed', b.getAttribute('data-view') === mobileView ? 'true' : 'false'); });
			}
			if (els.previewToggle) { els.previewToggle.setAttribute('aria-pressed', previewOn ? 'true' : 'false'); els.previewToggle.disabled = !previewUrl; }
			root.style.setProperty('--aid-chat-w', chatWidth + 'px');
			fitFrame();
		};

		var renderTasks = function () {
			if (!els.list) { return; }
			var q = els.search ? els.search.value.trim().toLowerCase() : '';
			els.list.innerHTML = '';
			var shown = tasks.filter(function (t) { return !q || String(t.title || '').toLowerCase().indexOf(q) > -1; });
			if (!shown.length) { els.list.appendChild(h('li', { class: 'bst-aid-empty', text: q ? __('No matching tasks.', T) : __('No tasks yet.', T) })); }
			shown.forEach(function (t) {
				var on = current && current.id === t.id;
				els.list.appendChild(h('li', { class: on ? 'is-active' : '' }, [
					h('button', { class: 'bst-aid-task', 'aria-current': on ? 'true' : null, onclick: function () { open(t.id); if (narrow()) { openDrawer(false); } }, title: t.title }, [
						h('span', { class: 'bst-aid-task-dot' + (t.running ? ' is-running' : ''), 'aria-hidden': 'true' }),
						h('span', { class: 'bst-aid-task-name', text: t.title })
					])
				]));
			});
		};
		var menuItem = function (label, fn, danger) {
			return h('button', { type: 'button', role: 'menuitem', class: danger ? 'is-danger' : '', onclick: function () { closeMenu(); fn(); }, text: label });
		};
		var closeMenu = function () { if (els.menu) { els.menu.hidden = true; els.menuBtn.setAttribute('aria-expanded', 'false'); } };
		var renameTask = function () {
			if (!current) { return; }
			var t = current, input = h('input', { type: 'text', class: 'bst-aid-title-input', value: t.title, 'aria-label': __('Task name', T) });
			var done = false, finish = function (save) {
				if (done) { return; }
				done = true;
				var name = input.value.trim();
				if (save && name && name !== t.title) { api('/tasks/' + t.id, 'POST', { title: name }).then(updateTask); }
				renderHead();
			};
			input.addEventListener('keydown', function (e) { if (e.key === 'Enter') { e.preventDefault(); finish(true); } else if (e.key === 'Escape') { e.preventDefault(); finish(false); } });
			input.addEventListener('blur', function () { finish(true); });
			els.title.replaceWith(input); input.focus(); input.select();
		};
		var deleteTask = function () {
			var t = current;
			if (!t) { return; }
			ask({ title: __('Delete this task?', T), text: __('Delete this task and its chat history? Pages, posts and products it created stay.', T), ok: __('Delete task', T), danger: true }).then(function (yes) {
				if (!yes) { return; }
				api('/tasks/' + t.id, 'DELETE').then(function () {
					tasks = tasks.filter(function (x) { return x.id !== t.id; });
					current = null; log = []; previewUrl = ''; previews = [];
					renderTasks(); renderHead(); renderChat(); syncPreview();
				});
			});
		};
		var undoTask = function () {
			ask({ title: __('Undo this task?', T), text: __('Undo everything this task changed? Theme settings, CSS, logo, menus, header and edited pages go back to how they were; pages, posts and products it created move to the trash.', T), ok: __('Undo task', T), danger: true }).then(function (yes) {
				if (!yes) { return; }
				api('/tasks/' + current.id + '/undo', 'POST').then(function (r) { addEvents(r.events); updateTask(r.task); }).catch(function (e) { addEvents([{ kind: 'error', text: e.message }]); });
			});
		};
		var showDetails = function () {
			if (!current) { return; }
			var rows = [[__('Task cost', T), money(current.cost)], [__('Model', T), (settings && settings.textModel) || '']];
			if (current.metrics && current.metrics.last) {
				var ml = current.metrics.last;
				rows.push([__('Last run', T), sprintf(__('%1$s s, %2$s model calls, %3$s tool calls', T), ml.seconds, ml.model_calls, ml.tool_calls)]);
			}
			els.details.innerHTML = '';
			els.details.appendChild(h('dl', null, [].concat.apply([], rows.map(function (r) { return [h('dt', { text: r[0] }), h('dd', { text: r[1] })]; }))));
			els.details.hidden = false;
		};
		var statusChip = function () {
			if (!current) { return null; }
			var pending = log.some(function (ev) { return ev.kind === 'approval' && !log.some(function (x) { return x.approval_id === ev.approval.id; }) && ev.approval.expires * 1000 > Date.now(); });
			var last = log.length ? log[log.length - 1] : null;
			var s = running ? ['is-running', __('Working', T)] : pending ? ['is-wait', __('Waiting for approval', T)] : (last && last.kind === 'error') ? ['is-error', __('Error', T)] : null;
			var chips = [];
			if (s) { chips.push(h('span', { class: 'bst-aid-chip ' + s[0], text: s[1] })); }
			if (current.mode === 'advice') { chips.push(h('span', { class: 'bst-aid-chip', text: __('Advice only', T) })); }
			return chips;
		};
		var renderHead = function () {
			if (!els.head) { return; }
			els.head.innerHTML = '';
			els.head.appendChild(els.drawerBtn);
			els.title = h('strong', { class: 'bst-aid-title', text: current ? current.title : __('BIGSPY AI Designer', T), title: current ? __('Double-click to rename', T) : null, ondblclick: renameTask });
			els.head.appendChild(els.title);
			(statusChip() || []).forEach(function (c) { els.head.appendChild(c); });
			els.head.appendChild(h('span', { class: 'bst-aid-spacer' }));
			els.head.appendChild(els.viewTabs);
			els.head.appendChild(els.previewToggle);
			if (current) {
				els.menu = h('div', { class: 'bst-aid-menu', role: 'menu', hidden: true }, [
					current.canUndo && !running ? menuItem(__('Undo task', T), undoTask) : null,
					menuItem(__('Rename', T), renameTask),
					menuItem(__('Details', T), showDetails),
					menuItem(__('Delete task', T), deleteTask, true)
				]);
				els.menuBtn = h('button', { type: 'button', class: 'bst-aid-icon-btn', 'aria-haspopup': 'menu', 'aria-expanded': 'false', title: __('Task actions', T), onclick: function (e) {
					e.stopPropagation(); els.details.hidden = true;
					els.menu.hidden = !els.menu.hidden; els.menuBtn.setAttribute('aria-expanded', els.menu.hidden ? 'false' : 'true');
					if (!els.menu.hidden) { var first = els.menu.querySelector('button'); if (first) { first.focus(); } }
				} }, [h('span', { class: 'dashicons dashicons-ellipsis', 'aria-hidden': 'true' }), h('span', { class: 'screen-reader-text', text: __('Task actions', T) })]);
				els.head.appendChild(h('div', { class: 'bst-aid-menu-wrap' }, [els.menuBtn, els.menu, els.details]));
			}
			renderPins();
			syncLayout();
		};
		// Manual checklist pinned above the composer instead of crowding the header.
		var renderPins = function () {
			if (!els.pins) { return; }
			els.pins.innerHTML = '';
			var items = (current && current.checklist) || [];
			els.pins.hidden = !items.length;
			if (!items.length) { return; }
			var done = items.filter(function (i) { return i.done; }).length;
			els.pins.appendChild(h('details', { class: 'bst-aid-checklist' }, [
				h('summary', { text: sprintf(__('Your to-do list: %1$d/%2$d done', T), done, items.length) }),
				h('div', null, items.map(function (item, index) {
					return h('label', null, [h('input', { type: 'checkbox', checked: item.done, onchange: function (e) {
						item.done = e.target.checked;
						api('/tasks/' + current.id + '/checklist', 'POST', { index: index, done: e.target.checked }).catch(function (er) { addEvents([{ kind: 'error', text: er.message }]); });
						renderPins();
					} }), ' ' + item.text]);
				}))
			]));
		};
		var useSuggestion = function (s) {
			els.input.value = s.prompt;
			if (els.mode) { els.mode.value = s.mode === 'advice' ? 'advice' : 'design'; }
			autoGrow(); els.input.focus();
		};
		var renderChat = function () {
			els.chat.innerHTML = '';
			if (!current || !log.length) {
				var sugg = (cfg.suggestions || []).slice(0, 6);
				els.chat.appendChild(h('div', { class: 'bst-aid-welcome' }, [
					h('h2', { text: __('What should we build today?', T) }),
					h('p', { text: __('Describe a page, a set of posts, products, colors or a fix. I ask what is missing, show a plan, then build drafts with editable BIGSPY AI blocks.', T) })
				]));
				if (sugg.length) {
					els.chat.appendChild(h('div', { class: 'bst-aid-suggest' }, sugg.map(function (s) {
						return h('button', { type: 'button', class: 'bst-aid-suggest-card', onclick: function () { useSuggestion(s); } }, [h('strong', { text: s.title }), h('span', { text: s.prompt })]);
					})));
				}
				var recent = tasks.filter(function (t) { return !current || t.id !== current.id; }).slice(0, 4);
				if (recent.length) {
					els.chat.appendChild(h('div', { class: 'bst-aid-recent' }, [h('span', { text: __('Recent tasks', T) })].concat(recent.map(function (t) {
						return h('button', { type: 'button', class: 'bst-aid-chip-btn', onclick: function () { open(t.id); }, text: t.title });
					}))));
				}
			} else {
				els.working = null;
				log.forEach(place);
			}
			els.working = h('div', { class: 'bst-aid-working', hidden: !running }, [h('span', { class: 'spinner is-active' }), h('span', { text: __('Working…', T) })]);
			els.chat.appendChild(els.working);
			scroll();
			syncComposer();
		};
		var syncComposer = function () {
			if (!els.send) { return; }
			els.send.hidden = running; els.stop.hidden = !running;
			els.input.disabled = running;
			els.working.hidden = !running;
			if (els.costLine) {
				var balInfo = (settings && settings.balance) || cfg.balance; var bal = balInfo ? money(balInfo.balance) : '';
				els.costLine.textContent = current ? money(current.cost) + (bal ? ' · ' + bal : '') : bal;
			}
			renderHead(); // The status chip follows the run.
		};
		var autoGrow = function () {
			if (!els.input) { return; }
			els.input.style.height = 'auto';
			els.input.style.height = Math.min(els.input.scrollHeight + 2, Math.max(120, Math.round(els.main.clientHeight * 0.4))) + 'px';
		};

		var open = function (id) {
			running = false; stopFlag = true;
			api('/tasks/' + id).then(function (d) {
				current = d.task; log = d.log || []; stopFlag = false;
				effort = d.effort || effort;
				if (els.effort) { els.effort.value = effort; }
				if (d.specialty && els.specialty) { specialty = d.specialty; els.specialty.value = specialty; }
				if (d.model && els.model && Array.prototype.some.call(els.model.options, function (o) { return o.value === d.model; })) { chatModel = d.model; els.model.value = chatModel; }
				previewUrl = ''; previews = [];
				log.forEach(rememberPreview);
				renderTasks(); renderHead(); renderChat(); syncPreview();
				if (welcomePrompt) {
					els.input.value = welcomePrompt; welcomePrompt = '';
					try { window.sessionStorage.removeItem('bstAidWelcomePrompt'); window.sessionStorage.removeItem('bstAidWelcomeMode'); } catch (e) { /* ignore */ }
					var cleanUrl = new URL(window.location.href); cleanUrl.searchParams.delete('bst_prompt'); cleanUrl.searchParams.delete('bst_mode'); window.history.replaceState(null, '', cleanUrl);
					autoGrow();
				}
				if (d.task.running) { loop(); }
				els.input.focus();
			});
		};
		// Every page the task built or changed can be previewed, not only the last one.
		var rememberPreview = function (ev) {
			if (!ev.preview) { return; }
			var label = ((ev.links || [])[0] || {}).label || toolLabel(ev.tool);
			previews = previews.filter(function (p) { return p.url !== ev.preview; });
			previews.push({ url: ev.preview, label: label });
			previewUrl = ev.preview;
		};
		var syncPreview = function () {
			if (!els.previewWrap) { return; }
			var show = previewOn && !!previewUrl;
			els.previewWrap.hidden = els.previewHandle.hidden = !show;
			els.pageSel.innerHTML = '';
			previews.slice().reverse().forEach(function (p) { els.pageSel.appendChild(h('option', { value: p.url, selected: p.url === previewUrl, text: p.label })); });
			els.pageSel.hidden = previews.length < 2;
			els.pageLabel.hidden = previews.length > 1;
			els.pageLabel.textContent = previews.length ? previews[previews.length - 1].label : '';
			if (show && els.frame.getAttribute('src') !== previewUrl) { els.frame.src = previewUrl; }
			if (show && narrow() && mobileView === 'chat') { els.viewTabs.classList.add('has-news'); }
			renderHead();
		};
		var setPreviewOn = function (on) {
			previewOn = on;
			try { window.localStorage.setItem('bstAidPreview', on ? '1' : '0'); } catch (e) { /* ignore */ }
			if (on && narrow()) { mobileView = 'preview'; els.viewTabs.classList.remove('has-news'); }
			syncPreview();
		};
		// The frame keeps the device width and is scaled down to fit, so a desktop preview never squeezes the chat.
		var fitFrame = function () {
			if (!els.stage || els.previewWrap.hidden) { return; }
			var sw = els.stage.clientWidth, sh = els.stage.clientHeight;
			if (!sw || !sh) { return; }
			// On a phone only Fit and Phone are offered; a saved desktop or tablet width falls back to Fit.
			var w = previewDevice === 'fit' || (window.innerWidth < 560 && previewDevice > 390) ? sw : previewDevice;
			var scale = Math.min(1, sw / w);
			els.frame.style.width = w + 'px';
			els.frame.style.height = Math.round(sh / scale) + 'px';
			els.frame.style.transform = scale < 1 ? 'scale(' + scale + ')' : '';
			els.frameBox.style.width = Math.round(w * scale) + 'px';
			els.frameBox.style.height = sh + 'px';
			els.previewSize.textContent = w + ' px' + (scale < 1 ? ' · ' + Math.round(scale * 100) + '%' : '');
			els.devices.querySelectorAll('button').forEach(function (b) { b.setAttribute('aria-pressed', String(b.getAttribute('data-w')) === String(previewDevice) ? 'true' : 'false'); });
		};
		var setDevice = function (w) {
			previewDevice = w;
			try { window.localStorage.setItem('bstAidDevice', String(w)); } catch (e) { /* ignore */ }
			fitFrame();
		};
		var setChatWidth = function (w) {
			chatWidth = Math.round(Math.min(560, Math.max(340, w)));
			root.style.setProperty('--aid-chat-w', chatWidth + 'px');
			els.previewHandle.setAttribute('aria-valuenow', chatWidth);
			fitFrame();
		};
		var saveChatWidth = function () { try { window.localStorage.setItem('bstAidChatW', chatWidth); } catch (e) { /* ignore */ } };
		var startPreviewDrag = function (e) {
			if (e.button) { return; }
			e.preventDefault();
			var startX = e.clientX, startW = chatWidth, rtl = document.documentElement.dir === 'rtl';
			var move = function (ev) { setChatWidth(startW + (rtl ? startX - ev.clientX : ev.clientX - startX)); };
			var end = function () {
				els.previewHandle.removeEventListener('pointermove', move);
				els.previewHandle.removeEventListener('pointerup', end);
				els.previewHandle.removeEventListener('pointercancel', end);
				root.classList.remove('is-resizing');
				saveChatWidth();
			};
			els.previewHandle.setPointerCapture(e.pointerId);
			els.previewHandle.addEventListener('pointermove', move);
			els.previewHandle.addEventListener('pointerup', end);
			els.previewHandle.addEventListener('pointercancel', end);
			root.classList.add('is-resizing');
		};
		var nudgePreview = function (e) {
			if (e.key !== 'ArrowLeft' && e.key !== 'ArrowRight') { return; }
			e.preventDefault();
			setChatWidth(chatWidth + (e.key === 'ArrowRight' ? 1 : -1) * (e.shiftKey ? 80 : 16));
			saveChatWidth();
		};
		var newTask = function () {
			return api('/tasks', 'POST', { mode: els.mode ? els.mode.value : 'design' }).then(function (t) { tasks.unshift(t); open(t.id); return t; });
		};
		var loop = function () {
			running = true; stopFlag = false; syncComposer();
			var busy = 0;
			var step = function () {
				if (stopFlag || !current) { running = false; syncComposer(); return; }
				var id = current.id;
				api('/tasks/' + id + '/step', 'POST').then(function (r) {
					if (!current || current.id !== id) { return; }
					addEvents(r.events); updateTask(r.task);
					busy = r.state === 'busy' ? busy + 1 : 0;
					if (busy > 80) { // About two minutes: the previous step probably failed on the server.
						addEvents([{ kind: 'error', text: __('The previous step is still locked. Wait a few minutes, then send "continue".', T) }]);
						running = false; syncComposer(); return;
					}
					if (r.state === 'running' || r.state === 'busy') { setTimeout(step, r.state === 'busy' ? 1500 : 150); }
					else { running = false; syncComposer(); }
				}).catch(function (e) {
					addEvents([{ kind: 'error', text: e.message }]); running = false; syncComposer();
				});
			};
			step();
		};
		var attached = [];
		var fileChip = function (f, remove) {
			return h('span', { class: 'bst-aid-file', title: f.title || '' }, [
				f.thumb ? h('img', { src: f.thumb, alt: '' }) : h('span', { class: 'dashicons ' + (f.type === 'doc' ? 'dashicons-media-text' : 'dashicons-media-default') }),
				h('span', { text: f.title || ('#' + f.id) }),
				remove ? h('button', { type: 'button', class: 'button-link', 'aria-label': __('Remove', T), onclick: remove, text: '×' }) : null]);
		};
		var renderAttached = function () {
			if (!els.files) { return; }
			els.files.innerHTML = '';
			els.files.hidden = !attached.length;
			attached.forEach(function (f, i) { els.files.appendChild(fileChip(f, function () { attached.splice(i, 1); renderAttached(); })); });
		};
		var uploadFiles = function (list) {
			if (!current) { return; }
			Array.prototype.forEach.call(list || [], function (file) {
				var body = new FormData();
				body.append('file', file);
				var parts = ('/tasks/' + current.id + '/upload').split('?');
				var chip = { id: 0, type: 'media', title: file.name + ' …' };
				attached.push(chip); renderAttached();
				fetch(cfg.rest + parts[0], { method: 'POST', headers: { 'X-WP-Nonce': cfg.nonce }, credentials: 'same-origin', body: body })
					.then(function (res) { return res.json().then(function (d) { if (!res.ok) { throw new Error(d.message || res.statusText); } return d; }); })
					.then(function (d) { Object.assign(chip, d); renderAttached(); })
					.catch(function (e) { attached.splice(attached.indexOf(chip), 1); renderAttached(); addEvents([{ kind: 'error', text: file.name + ': ' + e.message }]); });
			});
		};
		var pickMedia = function () {
			if (!window.wp || !wp.media) { return; }
			var frame = wp.media({ title: __('Attach files', T), multiple: true, button: { text: __('Attach', T) } });
			frame.on('select', function () {
				frame.state().get('selection').toJSON().forEach(function (m) {
					if (!attached.some(function (f) { return f.type === 'media' && f.id === m.id; })) {
						attached.push({ id: m.id, type: 'media', title: m.title || m.filename, thumb: (m.sizes && m.sizes.thumbnail && m.sizes.thumbnail.url) || (m.type === 'image' ? m.url : '') });
					}
				});
				renderAttached();
			});
			frame.open();
		};
		var sendText = function (text) { if (running || !current) { return; } els.input.value = text; send(); };
		var send = function () {
			var text = els.input.value.trim();
			if (!text || running || !current) { return; }
			if (attached.some(function (f) { return !f.id; })) { addEvents([{ kind: 'error', text: __('Wait for the uploads to finish.', T) }]); return; }
			var files = attached.map(function (f) { return { id: f.id, type: f.type }; });
			var keep = attached;
			els.input.value = ''; attached = []; renderAttached();
			api('/tasks/' + current.id + '/send', 'POST', { text: text, effort: effort, model: chatModel, specialty: specialty, files: files }).then(function (r) {
				addEvents(r.events); updateTask(r.task); loop();
			}).catch(function (e) { els.input.value = text; attached = keep; renderAttached(); addEvents([{ kind: 'error', text: e.message }]); });
		};

		var build = function () {
			root.innerHTML = '';
			if (!settings.ready) {
				root.classList.add('is-setup');
				root.appendChild(h('div', { class: 'bst-aid-card bst-aid-setup' }, [
					h('h2', { text: __('The API is not connected yet', T) }),
					h('p', { text: settings.hasKey && !settings.connected ? __('The AI Designer is disconnected. Reconnect it in Settings to continue.', T) : __('Connect your API key and choose a text model in Settings, then come back here.', T) }),
					cfg.canManage ? h('a', { class: 'button button-primary', href: cfg.settingsUrl, text: __('Open Settings', T) }) : h('p', { text: __('Ask an administrator to connect the API.', T) })
				]));
				return;
			}
			var icon = function (name) { return h('span', { class: 'dashicons dashicons-' + name, 'aria-hidden': 'true' }); };
			var iconBtn = function (name, label, onclick, extra) {
				return h('button', Object.assign({ type: 'button', class: 'bst-aid-icon-btn', title: label, onclick: onclick }, extra || {}), [icon(name), h('span', { class: 'screen-reader-text', text: label })]);
			};

			/* Rail */
			els.list = h('ul', { class: 'bst-aid-tasks' });
			els.mode = h('select', { class: 'bst-aid-mode', 'aria-label': __('Task mode', T) }, [h('option', { value: 'design', text: __('Design', T) }), h('option', { value: 'advice', text: __('Advice only', T) })]);
			if (welcomePrompt) { els.mode.value = welcomeMode; }
			els.search = h('input', { type: 'search', class: 'bst-aid-search', placeholder: __('Search tasks', T), 'aria-label': __('Search tasks', T), oninput: renderTasks });
			els.railBtn = iconBtn('menu-alt3', __('Show or hide the task list', T), function () { setRail(!root.classList.contains('is-rail-min')); }, { class: 'bst-aid-icon-btn bst-aid-rail-btn', 'aria-expanded': 'true' });
			var newBtn = h('button', { type: 'button', class: 'button button-primary bst-aid-new', title: __('New task', T), onclick: function () { newTask(); if (narrow()) { openDrawer(false); } } }, [icon('plus-alt2'), h('span', { class: 'bst-aid-new-label', text: __('New task', T) })]);
			root.appendChild(h('aside', { class: 'bst-aid-side', 'aria-label': __('Tasks', T) }, [
				h('div', { class: 'bst-aid-side-top' }, [els.railBtn, newBtn]),
				h('div', { class: 'bst-aid-side-tools' }, [els.search, els.mode]),
				els.list
			]));
			root.appendChild(h('div', { class: 'bst-aid-scrim', 'aria-hidden': 'true', onclick: function () { openDrawer(false); } }));

			/* Conversation */
			els.drawerBtn = iconBtn('menu-alt3', __('Tasks', T), function () { openDrawer(!root.classList.contains('is-drawer')); }, { class: 'bst-aid-icon-btn bst-aid-drawer-btn', 'aria-expanded': 'false' });
			els.viewTabs = h('div', { class: 'bst-aid-viewtabs', role: 'group', 'aria-label': __('View', T), hidden: true }, [['chat', __('Chat', T)], ['preview', __('Preview', T)]].map(function (v) {
				return h('button', { type: 'button', 'data-view': v[0], 'aria-pressed': 'false', onclick: function () { mobileView = v[0]; if (v[0] === 'preview') { els.viewTabs.classList.remove('has-news'); } syncLayout(); }, text: v[1] });
			}));
			els.previewToggle = iconBtn('visibility', __('Show or hide the preview', T), function () { setPreviewOn(!previewOn); }, { class: 'bst-aid-icon-btn bst-aid-preview-toggle', 'aria-pressed': 'true' });
			els.details = h('div', { class: 'bst-aid-details', hidden: true, onclick: function (e) { e.stopPropagation(); } });
			els.head = h('div', { class: 'bst-aid-head' });
			els.chat = h('div', { class: 'bst-aid-chat', 'aria-live': 'polite' });
			els.pins = h('div', { class: 'bst-aid-pins', hidden: true });
			els.input = h('textarea', { id: 'bst-aid-input', class: 'bst-aid-input', rows: '1', placeholder: __('Message the designer… (Enter to send, Shift+Enter for a new line)', T), 'aria-label': __('Message', T),
				oninput: autoGrow,
				onkeydown: function (e) { if (e.key === 'Enter' && !e.shiftKey && !e.isComposing) { e.preventDefault(); send(); } },
				onpaste: function (e) { var f = e.clipboardData && e.clipboardData.files; if (f && f.length) { e.preventDefault(); uploadFiles(f); } },
				ondragover: function (e) { e.preventDefault(); }, ondrop: function (e) { if (e.dataTransfer && e.dataTransfer.files.length) { e.preventDefault(); uploadFiles(e.dataTransfer.files); } } });
			els.files = h('div', { class: 'bst-aid-files', hidden: true });
			els.attach = iconBtn('paperclip', __('Attach files from the Media Library, or drop or paste them into the message box', T), pickMedia, { class: 'bst-aid-icon-btn bst-aid-attach' });
			els.send = h('button', { type: 'button', class: 'button button-primary bst-aid-send', onclick: send, text: __('Send', T) });
			els.stop = h('button', { type: 'button', class: 'button bst-aid-send', hidden: true, onclick: function () { stopFlag = true; running = false; syncComposer(); if (current) { api('/tasks/' + current.id + '/stop', 'POST').then(function (r) { addEvents(r.events); }); } }, text: __('Stop', T) });
			els.effort = h('select', { class: 'bst-aid-pill', 'aria-label': __('Effort', T), title: __('Effort', T), onchange: function (e) { effort = e.target.value; try { window.localStorage.setItem('bstAidEffort', effort); } catch (er) { /* ignore */ } } },
				[['low', __('Low – quick answers', T)], ['medium', __('Medium – balanced', T)], ['high', __('High – more careful', T)], ['max', __('Max – deepest work', T)]].map(function (o) { return h('option', { value: o[0], selected: o[0] === effort, text: o[1] }); }));
			var specialties = (settings && settings.specialties) || [];
			if (!specialties.some(function (sp) { return sp.key === specialty; })) { specialty = 'auto'; }
			els.specialty = h('select', { class: 'bst-aid-pill', 'aria-label': __('Specialty', T), title: __('Adds a ready context for this kind of work. Edit it under Guidelines.', T), onchange: function (e) { specialty = e.target.value; try { window.localStorage.setItem('bstAidSpecialty', specialty); } catch (er) { /* ignore */ } } },
				specialties.map(function (sp) { return h('option', { value: sp.key, selected: sp.key === specialty, text: sp.label }); }));
			var models = (settings && settings.chatModels) || [];
			if (chatModel && !models.some(function (m) { return m.id === chatModel; })) { chatModel = ''; }
			els.model = h('select', { class: 'bst-aid-pill', 'aria-label': __('Model', T), title: __('Auto uses the models from Settings: the fast model for Effort Low, the text model otherwise.', T), onchange: function (e) { chatModel = e.target.value; try { window.localStorage.setItem('bstAidModel', chatModel); } catch (er) { /* ignore */ } } },
				[h('option', { value: '', text: __('Auto (Settings)', T) })].concat(models.map(function (m) { return h('option', { value: m.id, selected: m.id === chatModel, text: m.name + ((m.input || m.output) ? ' – $' + m.input + ' / $' + m.output : '') }); })));
			els.opts = h('div', { class: 'bst-aid-opts' }, [specialties.length ? els.specialty : null, models.length ? els.model : null, els.effort]);
			els.optsBtn = iconBtn('admin-settings', __('Options', T), function () { var on = !composer.classList.contains('is-opts-open'); composer.classList.toggle('is-opts-open', on); els.optsBtn.setAttribute('aria-expanded', on ? 'true' : 'false'); }, { class: 'bst-aid-icon-btn bst-aid-opts-btn', 'aria-expanded': 'false' });
			els.costLine = h('span', { class: 'bst-aid-costline', title: __('Task cost · API balance', T) });
			var composer = h('div', { class: 'bst-aid-composer' }, [
				h('div', { class: 'bst-aid-composer-box' }, [els.files, els.input,
					h('div', { class: 'bst-aid-composer-bar' }, [window.wp && wp.media ? els.attach : null, els.optsBtn, els.opts, h('span', { class: 'bst-aid-spacer' }), els.costLine, els.send, els.stop])])
			]);
			els.main = h('section', { class: 'bst-aid-main', 'aria-label': __('Conversation', T) }, [
				h('a', { class: 'bst-aid-skip screen-reader-text', href: '#bst-aid-input', text: __('Skip to the message box', T) }),
				els.head, els.chat, h('div', { class: 'bst-aid-dock' }, [els.pins, composer])
			]);
			root.appendChild(els.main);
			if (window.ResizeObserver) {
				new window.ResizeObserver(function () { composer.classList.toggle('is-narrow', composer.clientWidth < 420); }).observe(composer);
				new window.ResizeObserver(function () { syncLayout(); }).observe(root);
			}

			/* Preview */
			els.previewHandle = h('div', { class: 'bst-aid-preview-handle', role: 'separator', tabindex: '0', hidden: true, 'aria-orientation': 'vertical', 'aria-label': __('Resize the conversation', T), 'aria-valuemin': '340', 'aria-valuemax': '560', 'aria-valuenow': chatWidth, title: __('Drag to change the width of the conversation. Double-click to reset.', T),
				onpointerdown: startPreviewDrag, onkeydown: nudgePreview, ondblclick: function () { setChatWidth(400); saveChatWidth(); } });
			// The preview is same-origin: hide the WordPress toolbar inside it so the page shows as visitors see it.
			els.frame = h('iframe', { class: 'bst-aid-frame', title: __('Preview', T), onload: function () {
				try {
					var d = els.frame.contentDocument;
					if (d && d.head && !d.getElementById('bst-aid-frame-css')) { d.head.appendChild(h('style', { id: 'bst-aid-frame-css', text: '#wpadminbar{display:none!important}html{margin-top:0!important}' })); }
				} catch (e) { /* another origin: leave it */ }
			} });
			els.frameBox = h('div', { class: 'bst-aid-framebox' }, els.frame);
			els.stage = h('div', { class: 'bst-aid-stage' }, els.frameBox);
			els.previewSize = h('span', { class: 'bst-aid-preview-size', 'aria-live': 'polite' });
			els.pageSel = h('select', { class: 'bst-aid-pill', 'aria-label': __('Page to preview', T), onchange: function (e) { previewUrl = e.target.value; syncPreview(); } });
			els.pageLabel = h('span', { class: 'bst-aid-page-label' });
			els.devices = h('div', { class: 'bst-aid-devices', role: 'group', 'aria-label': __('Preview width', T) }, [
				['fit', 'editor-expand', __('Fit the panel', T)], [1280, 'desktop', __('Desktop 1280 px', T)], [768, 'tablet', __('Tablet 768 px', T)], [390, 'smartphone', __('Phone 390 px', T)]
			].map(function (d) { return iconBtn(d[1], d[2], function () { setDevice(d[0]); }, { 'data-w': d[0], 'aria-pressed': 'false' }); }));
			var editUrl = function () { var m = /[?&](?:page_id|p)=(\d+)/.exec(previewUrl) || /[?&]preview_id=(\d+)/.exec(previewUrl); return m ? cfg.adminUrl + 'post.php?post=' + m[1] + '&action=edit' : ''; };
			els.previewWrap = h('section', { class: 'bst-aid-preview', hidden: true, 'aria-label': __('Preview', T) }, [
				h('div', { class: 'bst-aid-preview-bar' }, [
					els.pageSel, els.pageLabel, h('span', { class: 'bst-aid-spacer' }), els.devices, els.previewSize,
					iconBtn('update', __('Reload', T), function () { try { els.frame.contentWindow.location.reload(); } catch (e) { els.frame.src = previewUrl; } }),
					iconBtn('external', __('Open in a new tab', T), function () { window.open(previewUrl, '_blank', 'noopener'); }),
					iconBtn('edit', __('Open in the editor', T), function () { var u = editUrl(); if (u) { window.open(u, '_blank', 'noopener'); } })
				]),
				els.stage
			]);
			root.appendChild(els.previewHandle);
			root.appendChild(els.previewWrap);
			document.addEventListener('click', function () { closeMenu(); if (els.details) { els.details.hidden = true; } });
			document.addEventListener('keydown', function (e) {
				if (e.key !== 'Escape') { return; }
				if (els.menu && !els.menu.hidden) { closeMenu(); els.menuBtn.focus(); }
				else if (root.classList.contains('is-drawer')) { openDrawer(false); els.drawerBtn.focus(); }
			});
			window.addEventListener('resize', syncLayout);
			renderTasks(); renderHead(); renderChat(); autoGrow();
		};

		Promise.all([api('/settings?lite=1'), api('/tasks')]).then(function (r) {
			settings = r[0]; tasks = r[1] || [];
			build();
			if (settings.ready && welcomePrompt) { newTask(); }
			else if (settings.ready && tasks.length) { open(tasks[0].id); }
		}).catch(function (e) { root.innerHTML = ''; root.appendChild(h('div', { class: 'notice notice-error' }, h('p', { text: e.message }))); });
	}

	/* ------------------------------------------------------------ App shell (G-207) */
	(function shell() {
		var bar = document.querySelector('.bst-aid-bar');
		if (!bar) { return; }
		var html = document.documentElement;
		// Full screen hides the admin menu and toolbar; Esc leaves it.
		var fullBtn = bar.querySelector('.bst-aid-full-btn');
		var setFull = function (on) {
			html.classList.toggle('bst-aid-full', on);
			fullBtn.setAttribute('aria-pressed', on ? 'true' : 'false');
			try { window.localStorage.setItem('bstAidFull', on ? '1' : '0'); } catch (e) { /* storage blocked */ }
			window.dispatchEvent(new Event('resize'));
		};
		fullBtn.setAttribute('aria-pressed', html.classList.contains('bst-aid-full') ? 'true' : 'false');
		fullBtn.addEventListener('click', function () { setFull(!html.classList.contains('bst-aid-full')); });
		document.addEventListener('keydown', function (e) {
			if (e.key === 'Escape' && html.classList.contains('bst-aid-full') && !document.querySelector('.bst-aid-notices:not([hidden]), .bst-aid-menu:not([hidden]), dialog[open]')) { setFull(false); }
		});
		// Light / dark: the first press picks the opposite of what shows now; the choice is remembered.
		var themeBtn = bar.querySelector('.bst-aid-theme-btn');
		var isDark = function () { var t = html.getAttribute('data-aid-theme'); return t ? t === 'dark' : window.matchMedia('(prefers-color-scheme: dark)').matches; };
		var syncTheme = function () { themeBtn.setAttribute('aria-pressed', isDark() ? 'true' : 'false'); };
		themeBtn.addEventListener('click', function () {
			var next = isDark() ? 'light' : 'dark';
			html.setAttribute('data-aid-theme', next);
			try { window.localStorage.setItem('bstAidTheme', next); } catch (e) { /* storage blocked */ }
			syncTheme();
		});
		syncTheme();
		// Admin notices of other plugins: WordPress moves them after .wp-header-end, inside the bell panel.
		var bell = bar.querySelector('.bst-aid-bell > button'), panel = document.getElementById('bst-aid-notices');
		var count = bar.querySelector('.bst-aid-bell-count'), empty = panel.querySelector('.bst-aid-notices-empty');
		var gather = function () {
			// Notices printed outside the panel (before WordPress moved them, or by late scripts) are moved in too.
			document.querySelectorAll('#wpbody-content > .notice, #wpbody-content > div.error, #wpbody-content > div.updated, .bst-aid-app > .notice, .bst-aid-body > .notice').forEach(function (n) {
				if (!n.classList.contains('inline')) { panel.appendChild(n); }
			});
			var n = panel.querySelectorAll('.notice, div.error, div.updated').length;
			count.hidden = !n; count.textContent = n > 9 ? '9+' : String(n); empty.hidden = !!n;
		};
		var close = function () { panel.hidden = true; bell.setAttribute('aria-expanded', 'false'); };
		bell.addEventListener('click', function (e) {
			e.stopPropagation(); gather();
			panel.hidden = !panel.hidden; bell.setAttribute('aria-expanded', panel.hidden ? 'false' : 'true');
		});
		panel.addEventListener('click', function (e) { e.stopPropagation(); });
		document.addEventListener('click', close);
		document.addEventListener('keydown', function (e) { if (e.key === 'Escape' && !panel.hidden) { close(); bell.focus(); } });
		gather();
		window.addEventListener('load', gather);
	})();

	var s = document.getElementById('bst-aid-settings');
	if (s) { Settings(s); }
	var w = document.getElementById('bst-aid-workspace');
	if (w) { Workspace(w); }
})();
