/* BIGSPY AI Theme WP – front-end behaviour for theme blocks: slider, tabs, accordion, counter. */
(function () {
	'use strict';

	var doc = document;
	var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

	/* ------------------------------------------------------------ Slider */
	function initSlider(root) {
		var track = root.querySelector('[data-bs-slider-track]');
		if (!track) {
			return;
		}
		var slides = function () {
			return Array.prototype.filter.call(track.children, function (el) {
				return el.nodeType === 1;
			});
		};
		var step = function () {
			var items = slides();
			if (items.length < 2) {
				return track.clientWidth;
			}
			return items[1].offsetLeft - items[0].offsetLeft;
		};
		var perView = function () {
			return Math.max(1, Math.round(track.clientWidth / step()));
		};
		var pages = function () {
			return Math.max(1, slides().length - perView() + 1);
		};
		var current = function () {
			return Math.round(track.scrollLeft / step());
		};
		var goTo = function (index) {
			var max = pages() - 1;
			if (index > max) {
				index = root.dataset.loop === '1' ? 0 : max;
			}
			if (index < 0) {
				index = root.dataset.loop === '1' ? max : 0;
			}
			track.scrollTo({ left: index * step(), behavior: reduceMotion ? 'auto' : 'smooth' });
		};

		var prev = root.querySelector('[data-bs-slider-prev]');
		var next = root.querySelector('[data-bs-slider-next]');
		if (prev) {
			prev.addEventListener('click', function () {
				goTo(current() - 1);
			});
		}
		if (next) {
			next.addEventListener('click', function () {
				goTo(current() + 1);
			});
		}

		var dotsWrap = root.querySelector('[data-bs-slider-dots]');
		var buildDots = function () {
			if (!dotsWrap) {
				return;
			}
			dotsWrap.innerHTML = '';
			var count = pages();
			dotsWrap.hidden = count < 2;
			for (var i = 0; i < count; i++) {
				var dot = doc.createElement('button');
				dot.type = 'button';
				dot.className = 'bs-slider__dot';
				dot.setAttribute('aria-label', String(i + 1));
				dot.dataset.index = String(i);
				dotsWrap.appendChild(dot);
			}
			syncDots();
		};
		var syncDots = function () {
			var index = current();
			if (dotsWrap) {
				Array.prototype.forEach.call(dotsWrap.children, function (dot, i) {
					dot.classList.toggle('is-active', i === index);
					dot.setAttribute('aria-current', i === index ? 'true' : 'false');
				});
			}
			if (prev && root.dataset.loop !== '1') {
				prev.disabled = index <= 0;
			}
			if (next && root.dataset.loop !== '1') {
				next.disabled = index >= pages() - 1;
			}
		};
		if (dotsWrap) {
			dotsWrap.addEventListener('click', function (event) {
				var dot = event.target.closest('.bs-slider__dot');
				if (dot) {
					goTo(parseInt(dot.dataset.index, 10));
				}
			});
		}

		var scrollTimer;
		track.addEventListener('scroll', function () {
			window.clearTimeout(scrollTimer);
			scrollTimer = window.setTimeout(syncDots, 60);
		}, { passive: true });
		window.addEventListener('resize', buildDots);
		buildDots();

		var interval = parseInt(root.dataset.autoplay || '0', 10);
		if (interval > 0 && !reduceMotion) {
			var paused = false;
			var pause = function () {
				paused = true;
			};
			var resume = function () {
				paused = false;
			};
			root.addEventListener('mouseenter', pause);
			root.addEventListener('mouseleave', resume);
			root.addEventListener('focusin', pause);
			root.addEventListener('focusout', resume);
			root.addEventListener('touchstart', pause, { passive: true });
			window.setInterval(function () {
				if (paused || doc.hidden) {
					return;
				}
				var index = current() + 1;
				goTo(index >= pages() ? 0 : index);
			}, interval);
		}
	}

	/* ------------------------------------------------------------ Tabs */
	function initTabs(root, uid) {
		var buttons = root.querySelectorAll(':scope > .bs-tabs__nav [role="tab"]');
		var panels = root.querySelectorAll(':scope > .bs-tabs__panels > .bs-tab');
		if (!buttons.length) {
			return;
		}
		var activate = function (index, focus) {
			buttons.forEach(function (btn, i) {
				var active = i === index;
				btn.setAttribute('aria-selected', active ? 'true' : 'false');
				btn.tabIndex = active ? 0 : -1;
				btn.classList.toggle('is-active', active);
				if (active && focus) {
					btn.focus();
				}
			});
			panels.forEach(function (panel, i) {
				panel.classList.toggle('is-active', i === index);
				panel.hidden = i !== index;
			});
		};
		buttons.forEach(function (btn, i) {
			var panel = panels[i];
			btn.id = btn.id || 'bs-tab-' + uid + '-' + i;
			if (panel) {
				panel.id = panel.id || 'bs-tabpanel-' + uid + '-' + i;
				panel.setAttribute('role', 'tabpanel');
				panel.setAttribute('aria-labelledby', btn.id);
				btn.setAttribute('aria-controls', panel.id);
			}
			btn.addEventListener('click', function () {
				activate(i, false);
			});
			btn.addEventListener('keydown', function (event) {
				var key = event.key;
				var last = buttons.length - 1;
				var target = null;
				if (key === 'ArrowRight' || key === 'ArrowDown') {
					target = i === last ? 0 : i + 1;
				} else if (key === 'ArrowLeft' || key === 'ArrowUp') {
					target = i === 0 ? last : i - 1;
				} else if (key === 'Home') {
					target = 0;
				} else if (key === 'End') {
					target = last;
				}
				if (target !== null) {
					event.preventDefault();
					activate(target, true);
				}
			});
		});
		root.classList.add('is-ready');
		activate(0, false);
	}

	/* ------------------------------------------------------------ Accordion (fallback for details[name]) */
	function initAccordion(root) {
		if (root.dataset.single !== '1') {
			return;
		}
		root.addEventListener('toggle', function (event) {
			var item = event.target;
			if (item.tagName !== 'DETAILS' || !item.open) {
				return;
			}
			root.querySelectorAll(':scope > details[open]').forEach(function (other) {
				if (other !== item) {
					other.open = false;
				}
			});
		}, true);
	}

	/* ------------------------------------------------------------ Counter */
	function runCounter(el) {
		var to = parseFloat(el.dataset.to || '0');
		var decimals = (String(el.dataset.to).split('.')[1] || '').length;
		var duration = parseInt(el.dataset.duration || '1600', 10);
		var sep = el.dataset.sep || '';
		var format = function (value) {
			var parts = value.toFixed(decimals).split('.');
			parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, sep);
			return parts.join(sep === '.' ? ',' : '.');
		};
		if (reduceMotion) {
			el.textContent = format(to);
			return;
		}
		var start = null;
		var tick = function (ts) {
			if (!start) {
				start = ts;
			}
			var progress = Math.min(1, (ts - start) / duration);
			var eased = 1 - Math.pow(1 - progress, 3);
			el.textContent = format(to * eased);
			if (progress < 1) {
				window.requestAnimationFrame(tick);
			}
		};
		window.requestAnimationFrame(tick);
	}

	/* ------------------------------------------------------------ Products: load more */
	doc.addEventListener('click', function (event) {
		var btn = event.target.closest('[data-bs-load-more]');
		if (!btn || btn.disabled) {
			return;
		}
		var wrap = btn.closest('.bs-products-block');
		var list = wrap && wrap.querySelector('ul.products');
		if (!list) {
			return;
		}
		var label = btn.textContent;
		var page = parseInt(btn.getAttribute('data-page') || '1', 10) + 1;
		var body = new URLSearchParams();
		body.set('action', 'bst_products');
		body.set('page', String(page));
		body.set('options', btn.getAttribute('data-options') || '{}');

		btn.disabled = true;
		btn.textContent = btn.getAttribute('data-label-loading') || label;

		fetch(btn.getAttribute('data-url'), { method: 'POST', credentials: 'same-origin', body: body })
			.then(function (response) {
				return response.json();
			})
			.then(function (json) {
				if (!json || !json.success) {
					throw new Error('load failed');
				}
				list.insertAdjacentHTML('beforeend', json.data.html);
				btn.setAttribute('data-page', String(page));
				btn.textContent = label;
				btn.disabled = false;
				if (!json.data.more) {
					btn.parentNode.remove();
				}
			})
			.catch(function () {
				btn.textContent = label;
				btn.disabled = false;
			});
	});

	var tabsUid = 0;

	/**
	 * Initialise every block inside `scope` (the whole page by default).
	 * Exposed as window.bstInitBlocks for content inserted after page load.
	 */
	function init(scope) {
		var root = scope && scope.querySelectorAll ? scope : doc;
		root.querySelectorAll('[data-bs-slider]').forEach(initSlider);
		root.querySelectorAll('.bs-tabs').forEach(function (tabs) {
			initTabs(tabs, ++tabsUid);
		});
		root.querySelectorAll('.bs-accordion').forEach(initAccordion);

		var counters = root.querySelectorAll('[data-bs-counter]');
		if (counters.length) {
			if (!('IntersectionObserver' in window)) {
				counters.forEach(runCounter);
			} else {
				var io = new IntersectionObserver(function (entries) {
					entries.forEach(function (entry) {
						if (entry.isIntersecting) {
							runCounter(entry.target);
							io.unobserve(entry.target);
						}
					});
				}, { threshold: 0.4 });
				counters.forEach(function (el) {
					io.observe(el);
				});
			}
		}
	}

	window.bstInitBlocks = init;

	if (doc.readyState === 'loading') {
		doc.addEventListener('DOMContentLoaded', function () {
			init();
		});
	} else {
		init();
	}
})();
