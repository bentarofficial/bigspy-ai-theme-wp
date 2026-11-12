/**
 * Search block and Listing search (G-134-a): instant suggestions, rotating placeholders,
 * "/" shortcut and the overlay style. Loaded only on pages whose search form needs it.
 */
(function () {
	'use strict';
	var doc = document;
	var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
	var uid = 0;

	function esc(text) {
		var div = doc.createElement('div');
		div.textContent = text == null ? '' : String(text);
		return div.innerHTML;
	}

	/* ------------------------------------------------------------ Instant suggestions */
	function initLive(form) {
		var input = form.querySelector('input[type="search"]');
		if (!input || !window.fetch) {
			return;
		}
		var d = form.dataset;
		var id = 'bst-lsearch-live-' + (++uid);
		var list = doc.createElement('ul');
		list.className = 'bst-lsearch-live';
		list.id = id;
		list.setAttribute('role', 'listbox');
		list.hidden = true;
		form.insertAdjacentElement('afterend', list);
		if (getComputedStyle(form.parentNode).position === 'static') {
			form.parentNode.style.position = 'relative';
		}
		input.setAttribute('role', 'combobox');
		input.setAttribute('aria-autocomplete', 'list');
		input.setAttribute('aria-expanded', 'false');
		input.setAttribute('aria-controls', id);
		input.setAttribute('autocomplete', 'off');

		var timer = null;
		var request = 0;
		var controller = null;
		var active = -1;

		var place = function () {
			list.style.top = (form.offsetTop + form.offsetHeight) + 'px';
			list.style.left = form.offsetLeft + 'px';
			list.style.width = form.offsetWidth + 'px';
		};
		var options = function () {
			return list.querySelectorAll('[role="option"]');
		};
		var close = function () {
			list.hidden = true;
			active = -1;
			input.setAttribute('aria-expanded', 'false');
			input.removeAttribute('aria-activedescendant');
		};
		var mark = function (index) {
			var opts = options();
			if (!opts.length) {
				return;
			}
			active = (index + opts.length) % opts.length;
			opts.forEach(function (opt, i) {
				opt.setAttribute('aria-selected', i === active ? 'true' : 'false');
			});
			input.setAttribute('aria-activedescendant', opts[active].id);
			opts[active].scrollIntoView({ block: 'nearest' });
		};
		var render = function (items) {
			var html = '';
			items.forEach(function (item, i) {
				var meta = [];
				if (d.price !== '0' && item.price) {
					meta.push(item.price); // Sanitised by the server (wp_kses_post).
				}
				if (d.cat === '1' && item.cat) {
					meta.push(esc(item.cat));
				} else if (!item.price && item.type) {
					meta.push(esc(item.type));
				}
				html += '<li role="option" id="' + id + '-' + i + '" aria-selected="false"><a href="' + encodeURI(item.url) + '" tabindex="-1">' +
					(d.img !== '0' && item.image ? '<img src="' + encodeURI(item.image) + '" alt="" loading="lazy">' : '') +
					'<span class="bst-lsearch-live__text"><span class="bst-lsearch-live__title">' + esc(item.title) + '</span>' +
					(meta.length ? '<span class="bst-lsearch-live__meta">' + meta.join(' · ') + '</span>' : '') + '</span></a></li>';
			});
			if (items.length) {
				html += '<li role="option" id="' + id + '-all" aria-selected="false"><button type="button" class="bst-lsearch-live__all" tabindex="-1">' + esc(d.all) + '</button></li>';
			} else {
				html = '<li class="bst-lsearch-live__empty" role="presentation">' + esc(d.empty) + '</li>';
			}
			list.innerHTML = html;
			var all = list.querySelector('.bst-lsearch-live__all');
			if (all) {
				all.addEventListener('click', function () {
					form.requestSubmit ? form.requestSubmit() : form.submit();
				});
			}
			place();
			active = -1;
			list.hidden = false;
			input.setAttribute('aria-expanded', 'true');
		};

		input.addEventListener('input', function () {
			var term = input.value.trim();
			window.clearTimeout(timer);
			request += 1;
			if (term.length < (parseInt(d.min, 10) || 2)) {
				close();
				return;
			}
			timer = window.setTimeout(function () {
				var mine = request;
				var cat = form.querySelector('.bst-lsearch__cat');
				var url = d.url + '?action=bst_live_search&bst_block=1&s=' + encodeURIComponent(term) + '&count=' + encodeURIComponent(d.count || 6) +
					'&types=' + encodeURIComponent(d.types || '') + '&tax=' + encodeURIComponent(d.tax || '') +
					'&term=' + encodeURIComponent(cat && cat.selectedOptions[0] ? (cat.selectedOptions[0].getAttribute('data-id') || d.term || '') : (d.term || ''));
				if (controller) {
					controller.abort();
				}
				var opts = { credentials: 'same-origin' };
				if (window.AbortController) {
					controller = new AbortController();
					opts.signal = controller.signal;
				}
				fetch(url, opts).then(function (r) {
					return r.json();
				}).then(function (json) {
					if (json && json.success && mine === request && input.value.trim() === term) {
						render(json.data.items || []);
					}
				}).catch(function () {});
			}, 250);
		});

		input.addEventListener('keydown', function (event) {
			if (list.hidden) {
				if (event.key === 'ArrowDown' && list.innerHTML) {
					list.hidden = false;
					input.setAttribute('aria-expanded', 'true');
					event.preventDefault();
				}
				return;
			}
			if (event.key === 'ArrowDown' || event.key === 'ArrowUp') {
				event.preventDefault();
				mark(active + (event.key === 'ArrowDown' ? 1 : -1));
			} else if (event.key === 'Enter' && active > -1) {
				event.preventDefault();
				var target = options()[active].querySelector('a, button');
				if (target) {
					target.click();
				}
			} else if (event.key === 'Escape') {
				event.preventDefault();
				event.stopPropagation();
				close();
			}
		});
		input.addEventListener('focus', function () {
			if (list.innerHTML && input.value.trim().length >= (parseInt(d.min, 10) || 2)) {
				place();
				list.hidden = false;
				input.setAttribute('aria-expanded', 'true');
			}
		});
		doc.addEventListener('click', function (event) {
			if (!form.contains(event.target) && !list.contains(event.target)) {
				close();
			}
		});
		window.addEventListener('resize', function () {
			if (!list.hidden) {
				place();
			}
		});
	}

	/* ------------------------------------------------------------ Rotating placeholders */
	function initPlaceholders(form) {
		var input = form.querySelector('input[type="search"]');
		var list;
		try {
			list = JSON.parse(form.dataset.placeholders || '[]');
		} catch (e) {
			return;
		}
		if (!input || list.length < 2 || reduceMotion) {
			return;
		}
		var i = 0;
		window.setInterval(function () {
			if (doc.activeElement === input || input.value || doc.hidden) {
				return;
			}
			i = (i + 1) % list.length;
			input.setAttribute('placeholder', list[i]);
		}, 3000);
	}

	/* ------------------------------------------------------------ Overlay (focus stays inside) */
	function initOverlay(open) {
		var dlg = doc.getElementById(open.getAttribute('aria-controls'));
		if (!dlg) {
			return;
		}
		var closeBtn = dlg.querySelector('.bst-lsearch-overlay__close');
		var focusables = function () {
			return Array.prototype.filter.call(dlg.querySelectorAll('a[href], button, input, select, [tabindex]:not([tabindex="-1"])'), function (n) {
				return !n.disabled && n.offsetParent !== null;
			});
		};
		var hide = function () {
			dlg.hidden = true;
			open.setAttribute('aria-expanded', 'false');
			doc.documentElement.style.overflow = '';
			open.focus();
		};
		open.addEventListener('click', function () {
			dlg.hidden = false;
			open.setAttribute('aria-expanded', 'true');
			doc.documentElement.style.overflow = 'hidden';
			var input = dlg.querySelector('input[type="search"]');
			if (input) {
				input.focus();
			}
		});
		closeBtn.addEventListener('click', hide);
		dlg.addEventListener('click', function (event) {
			if (event.target === dlg) {
				hide();
			}
		});
		dlg.addEventListener('keydown', function (event) {
			if (event.key === 'Escape') {
				hide();
			} else if (event.key === 'Tab') {
				var items = focusables();
				var first = items[0];
				var last = items[items.length - 1];
				if (event.shiftKey && doc.activeElement === first) {
					event.preventDefault();
					last.focus();
				} else if (!event.shiftKey && doc.activeElement === last) {
					event.preventDefault();
					first.focus();
				}
			}
		});
	}

	/* ------------------------------------------------------------ "/" focuses the first form that asks for it */
	function initShortcut() {
		var form = doc.querySelector('.bst-lsearch[data-shortcut]');
		if (!form) {
			return;
		}
		doc.addEventListener('keydown', function (event) {
			var t = event.target;
			if (event.key !== '/' || event.ctrlKey || event.metaKey || event.altKey || (t && (t.isContentEditable || /^(INPUT|TEXTAREA|SELECT)$/.test(t.tagName)))) {
				return;
			}
			var overlay = form.closest('.bst-lsearch-overlay');
			if (overlay && overlay.hidden) {
				var open = doc.querySelector('[aria-controls="' + overlay.id + '"]');
				if (open) {
					event.preventDefault();
					open.click();
				}
				return;
			}
			var input = form.querySelector('input[type="search"]');
			if (input) {
				event.preventDefault();
				input.focus();
			}
		});
	}

	function init() {
		doc.querySelectorAll('.bst-lsearch[data-bst-live]').forEach(initLive);
		doc.querySelectorAll('.bst-lsearch[data-placeholders]').forEach(initPlaceholders);
		doc.querySelectorAll('.bst-lsearch-open').forEach(initOverlay);
		initShortcut();
	}
	if (doc.readyState === 'loading') {
		doc.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
