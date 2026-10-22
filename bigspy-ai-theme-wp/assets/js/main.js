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
	/* Stagger: the animation of a parent is played on each direct child, one after another. */
	doc.querySelectorAll('[data-bs-stagger].bs-animate').forEach(function (parent) {
		var gap = parseInt(parent.getAttribute('data-bs-stagger'), 10) || 120;
		var anim = Array.prototype.filter.call(parent.classList, function (c) { return c.indexOf('bs-animate--') === 0 || c.indexOf('bs-ease--') === 0; });
		var base = parseInt((parent.style.getPropertyValue('--bs-delay') || '0').replace('ms', ''), 10) || 0;
		var kids = parent.querySelectorAll(':scope > *:not(style):not(script), :scope > .bs-row > *, :scope > .bs-block-grid > *, :scope > ul > li, :scope > .bs-section__inner > *');
		var list = Array.prototype.filter.call(kids, function (k) { return !k.classList.contains('bs-row') && !k.classList.contains('bs-block-grid') && !k.classList.contains('bs-section__inner') && k.tagName !== 'UL'; });
		if (!list.length) {
			return;
		}
		parent.classList.remove('bs-animate');
		parent.classList.add('is-staggered');
		list.forEach(function (kid, i) {
			kid.classList.add('bs-animate');
			anim.forEach(function (c) { kid.classList.add(c); });
			kid.style.setProperty('--bs-delay', (base + i * gap) + 'ms');
			kid.style.setProperty('--bs-duration', parent.style.getPropertyValue('--bs-duration') || '700ms');
			if (parent.style.getPropertyValue('--bs-dist')) {
				kid.style.setProperty('--bs-dist', parent.style.getPropertyValue('--bs-dist'));
			}
			if (parent.hasAttribute('data-bs-repeat')) {
				kid.setAttribute('data-bs-repeat', '1');
			}
		});
	});
	var animated = doc.querySelectorAll('.bs-animate');
	if (animated.length) {
		if (reduceMotion || !('IntersectionObserver' in window) || !body.classList.contains('bs-animations')) {
			animated.forEach(function (el) {
				el.classList.add('is-visible');
			});
		} else {
			var observer = new IntersectionObserver(function (entries) {
				entries.forEach(function (entry) {
					var repeat = entry.target.hasAttribute('data-bs-repeat');
					if (entry.isIntersecting) {
						entry.target.classList.add('is-visible');
						if (!repeat) {
							observer.unobserve(entry.target);
						}
					} else if (repeat && entry.boundingClientRect.top > 0) {
						entry.target.classList.remove('is-visible');
					}
				});
			}, { rootMargin: '0px 0px -8% 0px' });
			animated.forEach(function (el) {
				observer.observe(el);
			});
		}
	}

	/* Heading highlight draws itself when visible. */
	var highlights = doc.querySelectorAll('.bs-heading--hl');
	if (highlights.length) {
		if (reduceMotion || !('IntersectionObserver' in window)) {
			highlights.forEach(function (el) { el.classList.add('is-drawn'); });
		} else {
			var hio = new IntersectionObserver(function (entries) {
				entries.forEach(function (entry) {
					if (entry.isIntersecting) {
						entry.target.classList.add('is-drawn');
						hio.unobserve(entry.target);
					}
				});
			}, { threshold: 0.6 });
			highlights.forEach(function (el) { hio.observe(el); });
		}
	}

	/* Scroll motion: move / zoom / rotate / fade with the scroll position. */
	var scrollers = doc.querySelectorAll('[data-bs-scroll]');
	if (scrollers.length && !reduceMotion) {
		var ticking = false;
		var update = function () {
			ticking = false;
			var vh = window.innerHeight;
			scrollers.forEach(function (el) {
				var r = el.getBoundingClientRect();
				if (r.bottom < -100 || r.top > vh + 100) {
					return;
				}
				var p = ((r.top + r.height / 2) - vh / 2) / (vh / 2 + r.height / 2); // -1 (top) … 1 (bottom)
				p = Math.max(-1, Math.min(1, p));
				var s = parseInt(el.getAttribute('data-bs-scroll-strength'), 10) || 5;
				switch (el.getAttribute('data-bs-scroll')) {
					case 'up': el.style.translate = '0 ' + (p * s * 12).toFixed(1) + 'px'; break;
					case 'down': el.style.translate = '0 ' + (-p * s * 12).toFixed(1) + 'px'; break;
					case 'left': el.style.translate = (p * s * 14).toFixed(1) + 'px 0'; break;
					case 'right': el.style.translate = (-p * s * 14).toFixed(1) + 'px 0'; break;
					case 'zoom': el.style.scale = (1 + (1 - Math.abs(p)) * s * 0.012).toFixed(3); break;
					case 'rotate': el.style.rotate = (p * s * 2).toFixed(1) + 'deg'; break;
					case 'fade': el.style.opacity = Math.max(0, 1 - Math.abs(p) * (s / 10) * 1.4).toFixed(2); break;
				}
			});
		};
		window.addEventListener('scroll', function () {
			if (!ticking) {
				ticking = true;
				window.requestAnimationFrame(update);
			}
		}, { passive: true });
		window.addEventListener('resize', update);
		update();
	}

	/* 3D tilt on hover. */
	if (!reduceMotion && window.matchMedia && window.matchMedia('(hover: hover)').matches) {
		doc.querySelectorAll('.bs-hover--tilt').forEach(function (el) {
			el.addEventListener('mousemove', function (e) {
				var r = el.getBoundingClientRect();
				var x = (e.clientX - r.left) / r.width - 0.5;
				var y = (e.clientY - r.top) / r.height - 0.5;
				el.style.transform = 'perspective(900px) rotateY(' + (x * 10).toFixed(2) + 'deg) rotateX(' + (-y * 10).toFixed(2) + 'deg) translateY(-4px)';
			});
			el.addEventListener('mouseleave', function () {
				el.style.transform = '';
			});
		});
	}
})();
