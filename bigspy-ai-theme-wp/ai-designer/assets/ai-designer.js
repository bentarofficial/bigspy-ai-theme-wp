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
	var md = function (text) {
		var esc = String(text || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/\r/g, '');
		var out = [], list = null, kind = '';
		var flush = function () { if (list) { out.push('<' + kind + '>' + list.join('') + '</' + kind + '>'); } list = null; kind = ''; };
		esc.split('\n').forEach(function (line) {
			var t = line.trim(), m;
			if (!t) { flush(); return; }
			if ((m = /^#{1,6}\s+(.+)$/.exec(t))) { flush(); out.push('<h4>' + inline(m[1].replace(/#+\s*$/, '')) + '</h4>'); return; }
			if (/^(-{3,}|\*{3,}|_{3,})$/.test(t)) { flush(); out.push('<hr>'); return; }
			if ((m = /^(?:[-*•+]|(\d+)[.)])\s+(.+)$/.exec(t))) {
				var k = m[1] ? 'ol' : 'ul';
				if (kind && kind !== k) { flush(); }
				if (!list) { list = []; kind = k; }
				list.push('<li>' + inline(m[2]) + '</li>');
				return;
			}
			flush();
			out.push('<p>' + inline(t.replace(/^&gt;\s*/, '')) + '</p>');
		});
		flush();
		return out.join('');
	};

	/* ------------------------------------------------------------ Guidelines */
	document.addEventListener('click', function (e) {
		var b = e.target.closest && e.target.closest('[data-bst-aid-template]');
		if (!b) { return; }
		var area = document.getElementById(b.getAttribute('data-bst-aid-template'));
		if (area && (!area.value.trim() || window.confirm(__('Replace the current text with the template?', T)))) {
			area.value = b.getAttribute('data-template');
			area.focus();
		}
	});

	/* ------------------------------------------------------------ Settings */
	function Settings(root) {
		var state = null, busy = false, msg = '', msgType = 'info';
		var load = function (refresh) {
			return api('/settings' + (refresh ? '?refresh=1' : '')).then(function (s) { state = s; render(); }).catch(function (e) { msg = e.message; msgType = 'error'; render(); });
		};
		var act = function (path, body, confirmText) {
			if (confirmText && !window.confirm(confirmText)) { return; }
			busy = true; msg = ''; render();
			api(path, 'POST', body).then(function (s) { state = s; busy = false; msg = __('Done.', T); msgType = 'success'; render(); })
				.catch(function (e) { busy = false; msg = e.message; msgType = 'error'; render(); });
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
			root.innerHTML = '';
			if (!state) { root.appendChild(h('p', { text: msg || __('Loading…', T) })); return; }
			var kids = [];
			if (msg) { kids.push(h('div', { class: 'notice notice-' + msgType + ' inline' }, h('p', { text: msg }))); }
			if (!state.hasKey) {
				var input = h('input', { type: 'password', class: 'regular-text', placeholder: 'kyma-…', autocomplete: 'off', 'aria-label': __('API key', T) });
				kids.push(h('div', { class: 'bst-aid-card' }, [
					h('h2', { text: __('1. Connect the API', T) }),
					h('p', { class: 'description', text: __('Paste your Kyma API key, then press Connect. The key is checked, encrypted and kept on the server only; it is never shown again in full.', T) }),
					h('p', { class: 'bst-aid-row' }, [input, h('button', { class: 'button button-primary', disabled: busy, onclick: function () { act('/connect', { key: input.value }); } , text: busy ? __('Connecting…', T) : __('Connect', T) })]),
					h('p', { class: 'description' }, [__('No key yet? Create one at ', T), h('a', { href: 'https://kymaapi.com/?aff=BIGSPY-AI-Connect', target: '_blank', rel: 'noopener sponsored', text: 'kymaapi.com' }), '.'])
				]));
			} else {
				var status = state.connected
					? h('span', { class: 'bst-aid-status is-on', text: __('Connected', T) })
					: h('span', { class: 'bst-aid-status is-off', text: __('Disconnected', T) });
				var bal = state.balance ? h('span', { class: 'bst-aid-balance' + (state.balance.low ? ' is-low' : ''), text: sprintf(__('Balance: %s', T), money(state.balance.balance)) }) : null;
				kids.push(h('div', { class: 'bst-aid-card' }, [
					h('h2', { text: __('1. API connection', T) }),
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
						var select = h('select', null, [1, 2, 3].map(function (level) { return h('option', { value: level, selected: level === Number((state.permissions || {})[group] || 1), text: [__('Automatic (mandatory approvals still apply)', T), __('Always ask', T), __('Guidance only', T)][level - 1] }); }));
						permissionInputs[group] = select; return h('p', null, [h('label', { text: groupLabels[group] + ': ' }), select]);
					});
					kids.push(h('div', { class: 'bst-aid-card' }, [h('h2', { text: __('Tool permissions', T) })].concat(permissionRows)));
					kids.push(h('div', { class: 'bst-aid-card' }, [
						h('h2', { text: __('2. Models and budget', T) }),
						h('p', null, [h('label', { class: 'bst-aid-label', text: __('Text model (writes, designs and calls the tools)', T) }), text]),
						h('p', { class: 'description', text: __('Only models that can call tools are listed. Prices are per million input / output tokens. Start with a recommended model; switch to a most capable one if results are not good enough.', T) }),
						h('p', null, [h('label', { class: 'bst-aid-label', text: __('Image model (used by ImageGen)', T) }), image]),
						h('p', { class: 'bst-aid-row' }, [h('label', null, [__('Budget per task (USD, 0 = no limit) ', T), bTask]), h('label', null, [__('Budget per day (USD, 0 = no limit) ', T), bDay])]),
						h('p', { class: 'description', text: sprintf(__('Spent today: %s', T), money(state.spentToday)) }),
						h('p', null, [
							h('button', { class: 'button button-primary', disabled: busy, onclick: function () { act('/settings', { textModel: text.value, imageModel: image.value, budgetTask: bTask.value, budgetDay: bDay.value, permissions: Object.keys(permissionInputs).reduce(function (out, key) { out[key] = Number(permissionInputs[key].value); return out; }, {}) }); }, text: __('Save', T) }),
							' ',
							h('button', { class: 'button', disabled: busy, onclick: function () { load(true); }, text: __('Refresh model list', T) }),
							' ',
							testBtn
						]),
						h('p', { class: 'description', text: __('Test ImageGen creates one low-quality image with the saved image model (a few cents at most) and adds it to the Media Library, so you can check the whole image path before a real task.', T) }),
						testBox
					]));
				}
			}
			if (state.hasKey) {
				var unusedBox = h('div', { class: 'bst-aid-unused' }, h('button', { class: 'button', onclick: function () {
					unusedBox.textContent = __('Checking…', T);
					api('/images/unused').then(function (r) {
						unusedBox.innerHTML = '';
						unusedBox.appendChild(h('p', { text: sprintf(__('%1$d of %2$d AI images are not used anywhere.', T), r.unused.length, r.total) }));
						unusedBox.appendChild(h('div', { class: 'bst-aid-unused-grid' }, r.unused.map(function (i) {
							return h('a', { href: i.edit, target: '_blank', rel: 'noopener', title: i.title }, h('img', { src: i.thumb, alt: i.title }));
						})));
					}).catch(function (e) { unusedBox.textContent = e.message; });
				}, text: __('Find unused AI images', T) }));
				kids.push(h('div', { class: 'bst-aid-card' }, [
					h('h2', { text: __('AI images', T) }),
					h('p', { class: 'description', text: __('ImageGen never deletes media. Find generated images that no page, post, product or setting uses, open them and delete the ones you do not need.', T) }),
					unusedBox
				]));
			}
			kids.push(h('div', { class: 'bst-aid-card bst-aid-card--danger' }, [
				h('h2', { text: __('Remove AI Designer data', T) }),
				h('p', { class: 'description', text: __('Deletes the API key, settings, guidelines and all task history. Pages, posts and products already created stay.', T) }),
				h('button', { class: 'button button-link-delete', disabled: busy, onclick: function () { act('/data/delete', null, __('Delete all AI Designer data? This cannot be undone.', T)); }, text: __('Delete AI Designer data', T) })
			]));
			kids.forEach(function (k) { root.appendChild(k); });
		};
		render();
		load();
	}

	/* ------------------------------------------------------------ Workspace */
	function Workspace(root) {
		var settings = null, tasks = [], current = null, log = [], running = false, stopFlag = false, error = '';
		var effort = 'medium';
		try { effort = window.localStorage.getItem('bstAidEffort') || 'medium'; } catch (e) { /* storage blocked */ }
		var els = {};

		var toolLabel = function (name) {
			var map = {
				site_info: __('Reading the site', T), list_content: __('Listing content', T), get_post: __('Reading content', T),
				create_post: __('Created a post', T), update_post: __('Updated a post', T), create_product: __('Created a product', T),
				update_product: __('Updated a product', T), save_guidelines: __('Saved the guidelines', T),
				list_blocks: __('Reading the blocks', T), block_schema: __('Reading block options', T), list_patterns: __('Looking at patterns', T),
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
                var a = ev.approval, taskId = current.id;
                var resolved = log.some(function (event) { return event.approval_id === a.id; });
                var box = h('div', { class: 'bst-aid-card bst-aid-approval' }, [h('h3', { text: __('Approval required', T) + ': ' + a.tool }), h('p', { text: a.scope }), h('p', { text: a.cost }), h('p', { text: a.undo }), h('details', null, [h('summary', { text: __('Before / after', T) }), h('pre', { text: JSON.stringify({ before: a.before, after: a.after }, null, 2) })])]);
                if (a.preview) { box.appendChild(h('a', { href: a.preview, target: '_blank', rel: 'noopener', text: __('Preview', T) })); }
                ['approve', 'manual', 'skip'].forEach(function (decision, index) {
                    box.appendChild(h('button', { class: 'button', disabled: resolved || a.expires * 1000 <= Date.now(), text: [__('Agree', T), __('I will do it myself', T), __('Skip', T)][index], onclick: function () {
                        box.querySelectorAll('button').forEach(function (b) { b.disabled = true; });
                        api('/tasks/' + taskId + '/approval', 'POST', { approval_id: a.id, decision: decision }).then(function (r) {
                            box.appendChild(h('p', { text: r.result.instructions || r.result.error || [__('Agree', T), __('I will do it myself', T), __('Skip', T)][index] }));
                            if (current && current.id === taskId) { addEvents(r.events); updateTask(r.task); }
                        }).catch(function (e) { box.appendChild(h('p', { text: e.message })); box.querySelectorAll('button').forEach(function (b) { b.disabled = false; }); });
                    } }));
                });
                return box;
            }
			if (ev.kind === 'user') { return h('div', { class: 'bst-aid-msg is-user' }, h('div', { class: 'bst-aid-bubble', text: ev.text })); }
			if (ev.kind === 'assistant') {
				var b = h('div', { class: 'bst-aid-bubble' }); b.innerHTML = md(ev.text);
				return h('div', { class: 'bst-aid-msg is-ai' }, b);
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
					links.push(h('button', { type: 'button', class: 'button-link', onclick: function () { showPreview(ev.preview); }, text: __('Preview here', T) }));
				}
				var row = h('div', { class: 'bst-aid-tool' + (ev.kind === 'tool-error' ? ' is-error' : '') }, [h('span', { class: 'dashicons ' + (ev.kind === 'tool-error' ? 'dashicons-warning' : 'dashicons-yes-alt') }), h('span', { text: toolLabel(ev.tool) + (ev.text ? ': ' + ev.text : '') })].concat(links));
				if (!ev.palettes) { return row; }
				return h('div', null, [row, h('div', { class: 'bst-aid-palettes' }, ev.palettes.map(function (p) {
					return h('div', { class: 'bst-aid-palette' }, [
						h('strong', { text: p.name || '' }),
						h('div', { class: 'bst-aid-swatches' }, Object.keys(p.colors || {}).map(function (role) {
							return h('span', { class: 'bst-aid-swatch', title: role + ' ' + p.colors[role] }, [h('i', { style: 'background:' + p.colors[role] }), h('small', { text: role }), h('code', { text: p.colors[role] })]);
						})),
						p.fonts && (p.fonts.heading || p.fonts.body) ? h('p', { class: 'bst-aid-fonts', text: [p.fonts.heading, p.fonts.body].filter(Boolean).join(' / ') }) : null,
						p.note ? h('p', { class: 'description', text: p.note }) : null
					]);
				}))]);
			}
			return h('div', { class: 'bst-aid-note' + (ev.kind === 'error' ? ' is-error' : ''), text: ev.text + (ev.approved_by ? ' · #' + ev.approved_by + ' · ' + new Date(ev.t * 1000).toLocaleString() : '') });
		};
		var scroll = function () { if (els.chat) { els.chat.scrollTop = els.chat.scrollHeight; } };
		var addEvents = function (evs) {
			(evs || []).forEach(function (ev) {
				log.push(ev);
				if (els.chat) { els.chat.insertBefore(eventNode(ev), els.working); }
				if (ev.preview) { showPreview(ev.preview); } // Show a page as soon as it is built or changed.
			});
			scroll();
		};
		var updateTask = function (t) {
			if (!t) { return; }
			tasks = tasks.map(function (x) { return x.id === t.id ? t : x; });
			if (current && current.id === t.id) { current = t; }
			renderTasks(); renderHead();
		};

		var renderTasks = function () {
			if (!els.list) { return; }
			els.list.innerHTML = '';
			if (!tasks.length) { els.list.appendChild(h('li', { class: 'bst-aid-empty', text: __('No tasks yet.', T) })); }
			tasks.forEach(function (t) {
				els.list.appendChild(h('li', { class: current && current.id === t.id ? 'is-active' : '' }, [
					h('button', { class: 'bst-aid-task', onclick: function () { open(t.id); }, title: t.title, text: t.title }),
					h('button', { class: 'bst-aid-icon', title: __('Rename', T), 'aria-label': __('Rename', T), onclick: function () {
						var name = window.prompt(__('Task name', T), t.title);
						if (name) { api('/tasks/' + t.id, 'POST', { title: name }).then(updateTask); }
					} }, h('span', { class: 'dashicons dashicons-edit' })),
					h('button', { class: 'bst-aid-icon', title: __('Delete task', T), 'aria-label': __('Delete task', T), onclick: function () {
						if (!window.confirm(__('Delete this task and its chat history? Pages, posts and products it created stay.', T))) { return; }
						api('/tasks/' + t.id, 'DELETE').then(function () {
							tasks = tasks.filter(function (x) { return x.id !== t.id; });
							if (current && current.id === t.id) { current = null; log = []; renderChat(); }
							renderTasks();
						});
					} }, h('span', { class: 'dashicons dashicons-trash' }))
				]));
			});
		};
		var renderHead = function () {
			if (!els.head) { return; }
			els.head.innerHTML = '';
			els.head.appendChild(h('strong', { class: 'bst-aid-title', text: current ? current.title : __('BIGSPY AI Designer', T) }));
			if (current) { els.head.appendChild(h('span', { class: 'bst-aid-cost', text: sprintf(__('Task cost: %s', T), money(current.cost)) })); }
			if (current && current.canUndo) {
				els.head.appendChild(h('button', { type: 'button', class: 'button button-small', disabled: running, onclick: function () {
					if (!window.confirm(__('Undo everything this task changed? Theme settings, CSS, logo, menus, header and edited pages go back to how they were; pages, posts and products it created move to the trash.', T))) { return; }
					api('/tasks/' + current.id + '/undo', 'POST').then(function (r) { addEvents(r.events); updateTask(r.task); }).catch(function (e) { addEvents([{ kind: 'error', text: e.message }]); });
				}, text: __('Undo task', T) }));
			}
			if (els.previewWrap && !els.previewWrap.hidden) {
				els.head.appendChild(h('button', { type: 'button', class: 'button button-small', onclick: function () { showPreview(''); }, text: __('Close preview', T) }));
			}
			if (current && current.mode === 'advice') { els.head.appendChild(h('span', { text: __('Advice only', T) })); }
			((current && current.checklist) || []).forEach(function (item, index) {
				els.head.appendChild(h('label', null, [h('input', { type: 'checkbox', checked: item.done, onchange: function (e) { api('/tasks/' + current.id + '/checklist', 'POST', { index: index, done: e.target.checked }).catch(function (er) { addEvents([{ kind: 'error', text: er.message }]); }); } }), item.text]));
			});
			if (settings) { els.head.appendChild(h('span', { class: 'bst-aid-model', text: settings.textModel || '' })); }
		};
		var renderChat = function () {
			els.chat.innerHTML = '';
			if (!current) {
				els.chat.appendChild(h('div', { class: 'bst-aid-welcome' }, [
					h('h2', { text: __('Hello! I am your website designer.', T) }),
					h('p', { text: __('Tell me what you need: a new website or page, colors and logo, posts, products, images… I will ask a few questions, show you the plan, then build it as drafts with BIGSPY blocks.', T) }),
					h('button', { class: 'button button-primary', onclick: function () { newTask(); }, text: __('Start a new task', T) })
				]));
			} else {
				log.forEach(function (ev) { els.chat.appendChild(eventNode(ev)); });
			}
			els.working = h('div', { class: 'bst-aid-working', hidden: !running }, [h('span', { class: 'spinner is-active' }), h('span', { text: __('Working…', T) })]);
			els.chat.appendChild(els.working);
			scroll();
			syncComposer();
		};
		var syncComposer = function () {
			if (!els.send) { return; }
			els.send.hidden = running; els.stop.hidden = !running;
			els.input.disabled = running || !current;
			els.working.hidden = !running;
		};

		var open = function (id) {
			running = false; stopFlag = true;
			api('/tasks/' + id).then(function (d) {
				current = d.task; log = d.log || []; stopFlag = false;
				effort = d.effort || effort;
				if (els.effort) { els.effort.value = effort; }
				renderTasks(); renderHead(); renderChat();
				if (d.task.running) { loop(); }
				els.input.focus();
			});
		};
		var showPreview = function (url) {
			if (!els.previewWrap) { return; }
			els.previewWrap.hidden = !url;
			root.classList.toggle('has-preview', !!url);
			if (url) { els.preview.src = url; }
			renderHead();
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
		var send = function () {
			var text = els.input.value.trim();
			if (!text || running || !current) { return; }
			els.input.value = '';
			api('/tasks/' + current.id + '/send', 'POST', { text: text, effort: effort }).then(function (r) {
				addEvents(r.events); updateTask(r.task); loop();
			}).catch(function (e) { els.input.value = text; addEvents([{ kind: 'error', text: e.message }]); });
		};

		var build = function () {
			root.innerHTML = '';
			if (!settings.ready) {
				root.appendChild(h('div', { class: 'bst-aid-card bst-aid-setup' }, [
					h('h2', { text: __('The API is not connected yet', T) }),
					h('p', { text: settings.hasKey && !settings.connected ? __('The AI Designer is disconnected. Reconnect it in Settings to continue.', T) : __('Connect your API key and choose a text model in Settings, then come back here.', T) }),
					cfg.canManage ? h('a', { class: 'button button-primary', href: cfg.settingsUrl, text: __('Open Settings', T) }) : h('p', { text: __('Ask an administrator to connect the API.', T) })
				]));
				return;
			}
			els.list = h('ul', { class: 'bst-aid-tasks' });
			els.head = h('div', { class: 'bst-aid-head' });
			els.chat = h('div', { class: 'bst-aid-chat', 'aria-live': 'polite' });
			els.mode = h('select', { 'aria-label': __('Task mode', T) }, [h('option', { value: 'design', text: __('Design', T) }), h('option', { value: 'advice', text: __('Advice only', T) })]);
			els.input = h('textarea', { class: 'bst-aid-input', rows: '3', placeholder: __('Message the designer… (Enter to send, Shift+Enter for a new line)', T), onkeydown: function (e) { if (e.key === 'Enter' && !e.shiftKey && !e.isComposing) { e.preventDefault(); send(); } } });
			els.send = h('button', { class: 'button button-primary', onclick: send, text: __('Send', T) });
			els.stop = h('button', { class: 'button', hidden: true, onclick: function () { stopFlag = true; if (current) { api('/tasks/' + current.id + '/stop', 'POST').then(function (r) { addEvents(r.events); }); } }, text: __('Stop', T) });
			var effortSel = els.effort = h('select', { class: 'bst-aid-effort', 'aria-label': __('Effort', T), onchange: function (e) { effort = e.target.value; try { window.localStorage.setItem('bstAidEffort', effort); } catch (er) { /* ignore */ } } },
				[['low', __('Low – quick answers', T)], ['medium', __('Medium – balanced', T)], ['high', __('High – more careful', T)], ['max', __('Max – deepest work', T)]].map(function (o) { return h('option', { value: o[0], selected: o[0] === effort, text: o[1] }); }));
			root.appendChild(h('aside', { class: 'bst-aid-side' }, [
				els.mode, h('button', { class: 'button button-primary bst-aid-new', onclick: function () { newTask(); }, text: '+ ' + __('New task', T) }),
				els.list,
				h('p', { class: 'bst-aid-side-links' }, [h('a', { href: cfg.guidesUrl, text: __('Guidelines', T) }), cfg.canManage ? ' · ' : '', cfg.canManage ? h('a', { href: cfg.settingsUrl, text: __('Settings', T) }) : null])
			]));
			root.appendChild(h('section', { class: 'bst-aid-main' }, [
				els.head, els.chat,
				h('div', { class: 'bst-aid-composer' }, [els.input, h('div', { class: 'bst-aid-composer-bar' }, [h('label', { class: 'bst-aid-effort-label' }, [__('Effort', T) + ' ', effortSel]), h('span', { class: 'bst-aid-spacer' }), els.stop, els.send])])
			]));
			els.preview = h('iframe', { class: 'bst-aid-frame', title: __('Preview', T) });
			els.previewWrap = h('section', { class: 'bst-aid-preview', hidden: true }, [els.preview]);
			root.appendChild(els.previewWrap);
			renderTasks(); renderHead(); renderChat();
		};

		Promise.all([api('/settings?lite=1'), api('/tasks')]).then(function (r) {
			settings = r[0]; tasks = r[1] || [];
			build();
			if (settings.ready && tasks.length) { open(tasks[0].id); }
		}).catch(function (e) { root.innerHTML = ''; root.appendChild(h('div', { class: 'notice notice-error' }, h('p', { text: e.message }))); });
	}

	var s = document.getElementById('bst-aid-settings');
	if (s) { Settings(s); }
	var w = document.getElementById('bst-aid-workspace');
	if (w) { Workspace(w); }
})();
