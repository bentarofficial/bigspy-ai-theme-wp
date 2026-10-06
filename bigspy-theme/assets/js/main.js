/* BIGSPY AI Theme WP – header, off-canvas menu, search, back-to-top, animations. */
(function () {
	'use strict';

	var doc = document;
	var body = doc.body;
	var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

	function onScroll(fn) {
		var ticking = false;
		window.addEventListener('scroll', function () {
			if (!ticking) {
				window.requestAnimationFrame(function () {
					fn();
					ticking = false;
				});
				ticking = true;
			}
		}, { passive: true });
		fn();
	}

	/* ------------------------------------------------------------ Sticky header */
	var header = doc.getElementById('masthead');
	if (header && header.classList.contains('has-sticky')) {
		var sticky = header.querySelector('.bs-header__sticky');
		var isTransparent = header.classList.contains('is-transparent');
		var threshold = 0;
		var stuck = false;

		var measure = function () {
			if (stuck) {
				return;
			}
			var rect = header.getBoundingClientRect();
			threshold = rect.top + window.pageYOffset + header.offsetHeight;
		};

		var update = function () {
			if (!sticky || !sticky.children.length) {
				return;
			}
			var shouldStick = window.pageYOffset > threshold;
			if (shouldStick === stuck) {
				return;
			}
			stuck = shouldStick;
			if (stuck) {
				if (!isTransparent) {
					header.style.height = header.offsetHeight + 'px';
				}
				header.classList.add('is-stuck');
			} else {
				header.classList.remove('is-stuck');
				header.style.height = '';
			}
		};

		measure();
		window.addEventListener('resize', function () {
			measure();
			update();
		});
		window.addEventListener('load', measure);
		onScroll(update);
	}

	/* ------------------------------------------------------------ Off-canvas menu */
	var offcanvas = doc.getElementById('bs-offcanvas');
	var lastToggle = null;

	function setExpanded(selector, value) {
		doc.querySelectorAll(selector).forEach(function (btn) {
			btn.setAttribute('aria-expanded', value ? 'true' : 'false');
		});
	}

	function openOffcanvas(trigger) {
		if (!offcanvas) {
			return;
		}
		lastToggle = trigger || null;
		offcanvas.hidden = false;
		window.requestAnimationFrame(function () {
			offcanvas.classList.add('is-open');
		});
		body.classList.add('bs-no-scroll');
		setExpanded('[data-bs-offcanvas-open]', true);
		var close = offcanvas.querySelector('[data-bs-offcanvas-close].bs-icon-btn');
		if (close) {
			close.focus();
		}
	}

	function closeOffcanvas() {
		if (!offcanvas || offcanvas.hidden) {
			return;
		}
		offcanvas.classList.remove('is-open');
		body.classList.remove('bs-no-scroll');
		setExpanded('[data-bs-offcanvas-open]', false);
		window.setTimeout(function () {
			offcanvas.hidden = true;
		}, reduceMotion ? 0 : 300);
		if (lastToggle) {
			lastToggle.focus();
		}
	}

	if (offcanvas) {
		offcanvas.querySelectorAll('.bs-offcanvas-menu .menu-item-has-children').forEach(function (item, index) {
			var link = item.querySelector(':scope > a');
			var sub = item.querySelector(':scope > .sub-menu');
			if (!link || !sub) {
				return;
			}
			sub.id = sub.id || 'bs-sub-' + index;
			var btn = doc.createElement('button');
			btn.type = 'button';
			btn.className = 'bs-submenu-toggle';
			btn.setAttribute('aria-expanded', item.classList.contains('current-menu-ancestor') ? 'true' : 'false');
			btn.setAttribute('aria-controls', sub.id);
			btn.setAttribute('aria-label', link.textContent.trim());
			btn.innerHTML = '<svg width="18" height="18" viewBox="0 0 24 24" aria-hidden="true"><path fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" d="M6 9l6 6 6-6"/></svg>';
			if (item.classList.contains('current-menu-ancestor')) {
				item.classList.add('is-open');
			}
			btn.addEventListener('click', function () {
				var open = item.classList.toggle('is-open');
				btn.setAttribute('aria-expanded', open ? 'true' : 'false');
			});
			link.insertAdjacentElement('afterend', btn);
		});

		offcanvas.addEventListener('click', function (event) {
			if (event.target.closest('[data-bs-offcanvas-close]')) {
				closeOffcanvas();
			} else if (event.target.closest('.bs-offcanvas-menu a[href*="#"]')) {
				closeOffcanvas();
			}
		});
	}

	doc.addEventListener('click', function (event) {
		var opener = event.target.closest('[data-bs-offcanvas-open]');
		if (opener) {
			event.preventDefault();
			openOffcanvas(opener);
		}
	});

	/* ------------------------------------------------------------ Search panel */
	var searchPanel = doc.getElementById('bs-search-panel');

	function toggleSearch(force) {
		if (!searchPanel) {
			return;
		}
		var open = typeof force === 'boolean' ? force : searchPanel.hidden;
		searchPanel.hidden = !open;
		searchPanel.classList.toggle('is-open', open);
		setExpanded('[data-bs-search-toggle]', open);
		if (open) {
			var input = searchPanel.querySelector('input[type="search"]');
			if (input) {
				input.focus();
			}
		}
	}

	doc.addEventListener('click', function (event) {
		if (event.target.closest('[data-bs-search-toggle]')) {
			event.preventDefault();
			toggleSearch();
			return;
		}
		if (event.target.closest('[data-bs-search-close]')) {
			toggleSearch(false);
			return;
		}
		if (searchPanel && !searchPanel.hidden && !event.target.closest('#bs-search-panel .bs-search-form')) {
			toggleSearch(false);
		}
	});

	doc.addEventListener('keydown', function (event) {
		if (event.key === 'Escape') {
			closeOffcanvas();
			toggleSearch(false);
		}
	});

	/* ------------------------------------------------------------ Back to top */
	var toTop = doc.querySelector('[data-bs-back-to-top]');
	if (toTop) {
		onScroll(function () {
			toTop.classList.toggle('is-visible', window.pageYOffset > 500);
		});
		toTop.addEventListener('click', function (event) {
			event.preventDefault();
			window.scrollTo({ top: 0, behavior: reduceMotion ? 'auto' : 'smooth' });
		});
	}

	/* ------------------------------------------------------------ Vertical categories menu */
	doc.addEventListener('click', function (event) {
		var toggle = event.target.closest('.bs-vmenu__toggle');
		doc.querySelectorAll('[data-bs-vmenu].is-open').forEach(function (menu) {
			if (!toggle || menu !== toggle.parentNode) {
				menu.classList.remove('is-open');
				menu.querySelector('.bs-vmenu__toggle').setAttribute('aria-expanded', 'false');
			}
		});
		if (toggle) {
			var open = toggle.parentNode.classList.toggle('is-open');
			toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
		}
	});

	/* ------------------------------------------------------------ Live search suggestions */
	var live = window.bstLiveSearch;
	if (live && window.fetch) {
		var states = new WeakMap();
		var stateFor = function (form) {
			if (!states.has(form)) states.set(form, { timer: null, request: 0 });
			return states.get(form);
		};

		var escapeHtml = function (text) {
			var div = doc.createElement('div');
			div.textContent = text == null ? '' : String(text);
			return div.innerHTML;
		};

		var panelFor = function (form) {
			var panel = form.querySelector('.bs-live-results');
			if (!panel) {
				panel = doc.createElement('div');
				panel.className = 'bs-live-results';
				panel.hidden = true;
				form.appendChild(panel);
			}
			return panel;
		};

		var render = function (form, items, term) {
			var panel = panelFor(form);
			var html = '';
			items.forEach(function (item) {
				html += '<a class="bs-live-results__item" href="' + encodeURI(item.url) + '">' +
					(item.image ? '<img src="' + encodeURI(item.image) + '" alt="" loading="lazy">' : '<span class="bs-live-results__noimg"></span>') +
					'<span class="bs-live-results__text"><span class="bs-live-results__title">' + escapeHtml(item.title) + '</span>' +
					// Price HTML is sanitised by the server (wp_kses_post).
					(item.price ? '<span class="bs-live-results__price">' + item.price + '</span>' : (item.type ? '<span class="bs-live-results__type">' + escapeHtml(item.type) + '</span>' : '')) +
					'</span></a>';
			});
			if (!items.length) {
				html = '<div class="bs-live-results__empty">' + escapeHtml(live.empty) + '</div>';
			} else {
				html += '<button type="submit" class="bs-live-results__all">' + escapeHtml(live.viewAll) + '</button>';
			}
			panel.innerHTML = html;
			panel.hidden = false;
			form.classList.add('has-live-results');
		};

		var hideAll = function (except) {
			doc.querySelectorAll('.bs-live-results').forEach(function (panel) {
				if (!except || !except.contains(panel)) {
					panel.hidden = true;
				}
			});
		};

		doc.addEventListener('input', function (event) {
			var input = event.target;
			if (!input.classList || !input.classList.contains('bs-search-form__input')) {
				return;
			}
			var form = input.closest('form');
			var term = input.value.trim();
			var state = stateFor(form);
			window.clearTimeout(state.timer);
			state.request += 1;
			if (term.length < 2) {
				hideAll();
				return;
			}
			state.timer = window.setTimeout(function () {
				var request = state.request;
				var postType = form.querySelector('input[name="post_type"]');
				var url = live.url + '?action=bst_live_search&s=' + encodeURIComponent(term) + (postType ? '&post_type=' + encodeURIComponent(postType.value) : '');
				fetch(url, { credentials: 'same-origin' })
					.then(function (response) {
						return response.json();
					})
					.then(function (json) {
						// Ignore answers to a query the visitor has already replaced.
						if (json && json.success && state.request === request && input.value.trim() === term) {
							render(form, json.data.items || [], term);
						}
					})
					.catch(function () {});
			}, Math.max(0, Math.min(700, Number(live.debounce) || 0)));
		});

		doc.addEventListener('focusin', function (event) {
			var input = event.target;
			if (input.classList && input.classList.contains('bs-search-form__input')) {
				var panel = input.closest('form').querySelector('.bs-live-results');
				if (panel && panel.innerHTML && input.value.trim().length >= 2) {
					panel.hidden = false;
				}
			}
		});

		doc.addEventListener('click', function (event) {
			var form = event.target.closest('.bs-search-form');
			hideAll(form);
		});

		doc.addEventListener('keydown', function (event) {
			if (event.key === 'Escape') {
				hideAll();
			}
		});
	}

	/* ------------------------------------------------------------ Copy link */
	doc.addEventListener('click', function (event) {
		var btn = event.target.closest('[data-bs-copy]');
		if (!btn || !navigator.clipboard) {
			return;
		}
		navigator.clipboard.writeText(btn.getAttribute('data-bs-copy')).then(function () {
			btn.classList.add('is-copied');
			window.setTimeout(function () {
				btn.classList.remove('is-copied');
			}, 1500);
		});
	});

	/* ------------------------------------------------------------ Code block copy button */
	doc.addEventListener('click', function (event) {
		var btn = event.target.closest('[data-bs-code-copy]');
		if (!btn) {
			return;
		}
		var code = btn.closest('.bs-code');
		code = code && code.querySelector('code');
		if (!code) {
			return;
		}
		var text = code.textContent || '';
		var done = function () {
			var label = btn.textContent;
			btn.textContent = btn.getAttribute('data-label-done') || label;
			btn.classList.add('is-copied');
			window.setTimeout(function () {
				btn.textContent = label;
				btn.classList.remove('is-copied');
			}, 1500);
		};
		var fallback = function () {
			var area = doc.createElement('textarea');
			area.value = text;
			area.setAttribute('readonly', '');
			area.style.position = 'fixed';
			area.style.opacity = '0';
			body.appendChild(area);
			area.select();
			try {
				doc.execCommand('copy');
			} finally {
				body.removeChild(area);
			}
			done();
		};
		if (navigator.clipboard && window.isSecureContext) {
			navigator.clipboard.writeText(text).then(done, fallback);
		} else {
			fallback();
		}
	});

	/* ------------------------------------------------------------ Scroll animations */
	var animated = doc.querySelectorAll('.bs-animate');
	if (animated.length) {
		if (reduceMotion || !('IntersectionObserver' in window) || !body.classList.contains('bs-animations')) {
			animated.forEach(function (el) {
				el.classList.add('is-visible');
			});
		} else {
			var observer = new IntersectionObserver(function (entries) {
				entries.forEach(function (entry) {
					if (entry.isIntersecting) {
						entry.target.classList.add('is-visible');
						observer.unobserve(entry.target);
					}
				});
			}, { rootMargin: '0px 0px -8% 0px' });
			animated.forEach(function (el) {
				observer.observe(el);
			});
		}
	}
})();
