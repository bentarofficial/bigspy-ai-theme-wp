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

		/* Fade effect: slides are stacked, the active one is shown. */
		var fade = root.dataset.fade === '1';
		var fadeIndex = 0;
		if (fade) {
			pages = function () {
				return slides().length;
			};
			current = function () {
				return fadeIndex;
			};
			goTo = function (index) {
				var items = slides();
				var max = items.length - 1;
				fadeIndex = index > max ? 0 : (index < 0 ? max : index);
				items.forEach(function (el, i) {
					el.classList.toggle('is-active', i === fadeIndex);
					el.setAttribute('aria-hidden', i === fadeIndex ? 'false' : 'true');
				});
				if (typeof syncDots === 'function') {
					syncDots();
				}
			};
			slides().forEach(function (el, i) {
				el.classList.toggle('is-active', i === 0);
			});
		}

		/* Swipe / drag: fingers flip fade slides, the mouse can drag every slider (works with arrows off). */
		var swipe = null;
		var dragged = false;
		track.addEventListener('pointerdown', function (event) {
			if (event.button !== 0 || (!fade && event.pointerType !== 'mouse') || event.target.closest('input, select, textarea, iframe, video')) {
				return;
			}
			swipe = { x: event.clientX, y: event.clientY, left: track.scrollLeft, id: event.pointerId };
			dragged = false;
		});
		track.addEventListener('pointermove', function (event) {
			if (!swipe || event.pointerId !== swipe.id) {
				return;
			}
			var dx = event.clientX - swipe.x;
			if (!dragged && Math.abs(dx) > 8 && Math.abs(dx) > Math.abs(event.clientY - swipe.y)) {
				dragged = true;
				root.classList.add('is-dragging');
				try { track.setPointerCapture(event.pointerId); } catch (e) {}
			}
			if (dragged && !fade) {
				track.scrollLeft = swipe.left - dx;
			}
		});
		var endSwipe = function (event) {
			if (!swipe) {
				return;
			}
			var dx = event.clientX - swipe.x;
			var start = swipe;
			swipe = null;
			root.classList.remove('is-dragging');
			if (!dragged) {
				return;
			}
			var base = fade ? current() : Math.round(start.left / step());
			goTo(Math.abs(dx) > 40 ? base + (dx < 0 ? 1 : -1) : base);
		};
		track.addEventListener('pointerup', endSwipe);
		track.addEventListener('pointercancel', endSwipe);
		track.addEventListener('click', function (event) {
			if (dragged) {
				event.preventDefault();
				event.stopPropagation();
				dragged = false;
			}
		}, true);
		track.addEventListener('dragstart', function (event) {
			event.preventDefault();
		});

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
		var dotTimer = !!root.querySelector('[data-bs-slider-dot-timer]') && !!dotsWrap;
		if (dotTimer) {
			root.classList.add('bs-slider--dot-timer');
		}
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
				if (dotTimer) {
					dot.appendChild(doc.createElement('span')).className = 'bs-slider__dot-fill';
				}
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
			if (root.dataset.pause !== '0') {
				root.addEventListener('mouseenter', pause);
				root.addEventListener('mouseleave', resume);
			}
			root.addEventListener('focusin', pause);
			root.addEventListener('focusout', resume);
			root.addEventListener('touchstart', pause, { passive: true });
			var countEl = root.querySelector('[data-bs-slider-count]');
			var totalEl = root.querySelector('[data-bs-slider-total]');
			var pad = function (n) { return (n < 10 ? '0' : '') + n; };
			var restartBar = function () {
				var bar = root.querySelector('[data-bs-slider-progress]');
				if (dotTimer) {
					Array.prototype.forEach.call(dotsWrap.querySelectorAll('.bs-slider__dot-fill'), function (f) { f.style.animation = 'none'; });
					bar = dotsWrap.querySelector('.bs-slider__dot.is-active .bs-slider__dot-fill');
				}
				if (countEl && totalEl) {
					countEl.textContent = pad(current() + 1);
					totalEl.textContent = pad(pages());
				}
				if (!bar) {
					return;
				}
				var name = bar.tagName.toLowerCase() === 'circle' ? 'bs-slider-ring' : (root.querySelector('.bs-slider__progress--line-left') ? 'bs-slider-progress-y' : 'bs-slider-progress');
				bar.style.animation = 'none';
				void bar.getBoundingClientRect(); // restart the CSS animation
				bar.style.animation = name + ' ' + interval + 'ms linear forwards';
			};
			// Arrows, dots and swipes restart the timer too.
			var restartSoon;
			track.addEventListener('scroll', function () {
				window.clearTimeout(restartSoon);
				restartSoon = window.setTimeout(restartBar, 150);
			}, { passive: true });
			restartBar();
			window.setInterval(function () {
				if (paused || doc.hidden) {
					return;
				}
				var index = current() + 1;
				goTo(index >= pages() ? 0 : index);
				restartBar();
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
			if (root.dataset.event === 'hover') {
				btn.addEventListener('mouseenter', function () {
					activate(i, false);
				});
			}
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
		/* Smooth open / close: animate the content height instead of jumping. */
		if (root.dataset.smooth === '1' && !reduceMotion) {
			root.querySelectorAll(':scope > details > summary').forEach(function (summary) {
				summary.addEventListener('click', function (event) {
					var item = summary.parentElement;
					var content = item.querySelector(':scope > .bs-accordion__content');
					if (!content || item._bstAnimating) {
						return;
					}
					event.preventDefault();
					item._bstAnimating = true;
					var closing = item.open;
					if (!closing) {
						if (root.dataset.single === '1') {
							root.querySelectorAll(':scope > details[open]').forEach(function (other) {
								if (other !== item) {
									other.querySelector(':scope > summary').click();
								}
							});
						}
						item.open = true;
					}
					var full = content.scrollHeight;
					content.style.overflow = 'hidden';
					var anim = content.animate([{ height: (closing ? full : 0) + 'px', opacity: closing ? 1 : 0 }, { height: (closing ? 0 : full) + 'px', opacity: closing ? 0 : 1 }], { duration: 280, easing: 'ease' });
					anim.onfinish = function () {
						if (closing) {
							item.open = false;
						}
						content.style.overflow = '';
						item._bstAnimating = false;
					};
				});
			});
		}
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

	/* Products: infinite scroll clicks "Load more" when it comes into view. */
	if ('IntersectionObserver' in window) {
		var infinite = new IntersectionObserver(function (entries) {
			entries.forEach(function (entry) {
				var btn = entry.isIntersecting && entry.target.querySelector('[data-bs-load-more]');
				if (btn && !btn.disabled) {
					btn.click();
				}
			});
		}, { rootMargin: '300px 0px' });
		doc.querySelectorAll('[data-bs-infinite]').forEach(function (el) { infinite.observe(el); });
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

		/* Progress bars fill when scrolled into view. */
		var bars = root.querySelectorAll('[data-bs-progress].is-animated:not(.is-visible)');
		if (bars.length) {
			if (!('IntersectionObserver' in window)) {
				bars.forEach(function (el) { el.classList.add('is-visible'); });
			} else {
				var pio = new IntersectionObserver(function (entries) {
					entries.forEach(function (entry) {
						if (entry.isIntersecting) {
							entry.target.classList.add('is-visible');
							pio.unobserve(entry.target);
						}
					});
				}, { threshold: 0.3 });
				bars.forEach(function (el) { pio.observe(el); });
			}
		}

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

	/* ------------------------------------------------------------ Lightbox (Image + Gallery blocks) */
	var lightbox = null;
	function openLightbox(link) {
		var group = link.getAttribute('data-bs-lightbox');
		var links = group ? Array.prototype.slice.call(doc.querySelectorAll('[data-bs-lightbox="' + group + '"]')) : [link];
		var index = Math.max(0, links.indexOf(link));
		if (!lightbox) {
			lightbox = doc.createElement('div');
			lightbox.className = 'bs-lightbox';
			lightbox.setAttribute('role', 'dialog');
			lightbox.setAttribute('aria-modal', 'true');
			lightbox.innerHTML = '<span class="bs-lightbox__count"></span><figure class="bs-lightbox__figure"><img class="bs-lightbox__img" alt=""><figcaption class="bs-lightbox__caption"></figcaption></figure>' +
				'<button type="button" class="bs-lightbox__btn bs-lightbox__close" aria-label="Close">&#10005;</button>' +
				'<button type="button" class="bs-lightbox__btn bs-lightbox__prev" aria-label="Previous">&#8249;</button>' +
				'<button type="button" class="bs-lightbox__btn bs-lightbox__next" aria-label="Next">&#8250;</button>';
			doc.body.appendChild(lightbox);
			lightbox.addEventListener('click', function (e) {
				if (e.target === lightbox || e.target.classList.contains('bs-lightbox__close')) {
					closeLightbox();
				} else if (e.target.classList.contains('bs-lightbox__prev')) {
					lightbox._go(-1);
				} else if (e.target.classList.contains('bs-lightbox__next')) {
					lightbox._go(1);
				}
			});
			doc.addEventListener('keydown', function (e) {
				if (lightbox.hidden) {
					return;
				}
				if (e.key === 'Escape') {
					closeLightbox();
				} else if (e.key === 'ArrowLeft') {
					lightbox._go(-1);
				} else if (e.key === 'ArrowRight') {
					lightbox._go(1);
				}
			});
			var startX = null;
			lightbox.addEventListener('touchstart', function (e) { startX = e.touches[0].clientX; }, { passive: true });
			lightbox.addEventListener('touchend', function (e) {
				if (startX === null) {
					return;
				}
				var dx = e.changedTouches[0].clientX - startX;
				if (Math.abs(dx) > 40) {
					lightbox._go(dx < 0 ? 1 : -1);
				}
				startX = null;
			});
		}
		var show = function () {
			var current = links[index];
			lightbox.querySelector('.bs-lightbox__img').src = current.getAttribute('href');
			lightbox.querySelector('.bs-lightbox__img').alt = current.getAttribute('data-caption') || '';
			lightbox.querySelector('.bs-lightbox__caption').textContent = current.getAttribute('data-caption') || '';
			lightbox.querySelector('.bs-lightbox__count').textContent = links.length > 1 ? (index + 1) + ' / ' + links.length : '';
			lightbox.querySelector('.bs-lightbox__prev').hidden = links.length < 2;
			lightbox.querySelector('.bs-lightbox__next').hidden = links.length < 2;
		};
		lightbox._go = function (step) {
			index = (index + step + links.length) % links.length;
			show();
		};
		lightbox._opener = link;
		show();
		lightbox.hidden = false;
		doc.documentElement.style.overflow = 'hidden';
		lightbox.querySelector('.bs-lightbox__close').focus();
	}
	function closeLightbox() {
		lightbox.hidden = true;
		doc.documentElement.style.overflow = '';
		if (lightbox._opener) {
			lightbox._opener.focus();
		}
	}
	doc.addEventListener('click', function (e) {
		var link = e.target.closest && e.target.closest('a[data-bs-lightbox]');
		if (link && !e.ctrlKey && !e.metaKey && !e.shiftKey) {
			e.preventDefault();
			openLightbox(link);
			return;
		}
		/* Video popup block: open the video in a lightbox. */
		var vtrigger = e.target.closest && e.target.closest('[data-bs-video-popup]');
		if (vtrigger) {
			e.preventDefault();
			var embed = vtrigger.getAttribute('data-bs-video-popup');
			var file = vtrigger.getAttribute('data-bs-video-file');
			var modal = doc.createElement('div');
			modal.className = 'bs-lightbox bs-video-modal';
			modal.setAttribute('role', 'dialog');
			modal.setAttribute('aria-modal', 'true');
			modal.innerHTML = '<div class="bs-video-modal__frame">' + (embed ? '<iframe src="' + embed.replace(/"/g, '') + '" allow="autoplay; encrypted-media; picture-in-picture; fullscreen" allowfullscreen></iframe>' : '<video src="' + String(file).replace(/"/g, '') + '" controls autoplay playsinline></video>') + '</div><button type="button" class="bs-lightbox__btn bs-lightbox__close" aria-label="Close">&#10005;</button>';
			var close = function () {
				modal.remove();
				doc.documentElement.style.overflow = '';
				doc.removeEventListener('keydown', onKey);
				vtrigger.focus();
			};
			var onKey = function (ev) {
				if (ev.key === 'Escape') {
					close();
				}
			};
			modal.addEventListener('click', function (ev) {
				if (ev.target === modal || ev.target.classList.contains('bs-lightbox__close')) {
					close();
				}
			});
			doc.addEventListener('keydown', onKey);
			doc.body.appendChild(modal);
			doc.documentElement.style.overflow = 'hidden';
			modal.querySelector('.bs-lightbox__close').focus();
			return;
		}
		/* Video block: load the player only when the poster is clicked. */
		var poster = e.target.closest && e.target.closest('[data-bs-video]');
		if (poster) {
			var wrap = doc.createElement('div');
			wrap.innerHTML = poster.getAttribute('data-bs-video');
			if (wrap.firstChild) {
				poster.replaceWith(wrap.firstChild);
			}
		}
	});

	/* ------------------------------------------------------------ Smooth parallax (Section background) */
	var parallaxEls = doc.querySelectorAll('[data-bs-parallax]');
	if (parallaxEls.length && !reduceMotion) {
		var ticking = false;
		var updateParallax = function () {
			ticking = false;
			var vh = window.innerHeight;
			parallaxEls.forEach(function (bg) {
				var rect = bg.parentElement.getBoundingClientRect();
				if (rect.bottom < 0 || rect.top > vh) {
					return;
				}
				var speed = parseFloat(bg.getAttribute('data-bs-parallax')) || 0;
				var offset = (rect.top + rect.height / 2 - vh / 2) * speed;
				bg.style.transform = 'translate3d(0,' + (-offset).toFixed(1) + 'px,0)';
			});
		};
		window.addEventListener('scroll', function () {
			if (!ticking) {
				ticking = true;
				window.requestAnimationFrame(updateParallax);
			}
		}, { passive: true });
		window.addEventListener('resize', updateParallax);
		updateParallax();
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

/* Hotspots, popups, reading progress and instant page prefetch (1.46). */
(function () {
	var doc = document;
	var ready = function (fn) {
		if (doc.readyState === 'loading') {
			doc.addEventListener('DOMContentLoaded', fn);
		} else {
			fn();
		}
	};
	ready(function () {
		/* Hotspots: click toggles (also the hover style on touch screens). */
		doc.addEventListener('click', function (e) {
			var marker = e.target.closest('.bs-hotspot__marker');
			doc.querySelectorAll('.bs-hotspot__point.is-open').forEach(function (p) {
				if (!marker || p !== marker.parentNode) {
					p.classList.remove('is-open');
					p.querySelector('.bs-hotspot__marker').setAttribute('aria-expanded', 'false');
				}
			});
			if (marker) {
				var point = marker.parentNode;
				var open = point.classList.toggle('is-open');
				marker.setAttribute('aria-expanded', open ? 'true' : 'false');
			}
		});

		/* Popups */
		var store = function (id, set) {
			try {
				if (set) {
					localStorage.setItem('bsPopup:' + id, String(Date.now()));
					return null;
				}
				return parseInt(localStorage.getItem('bsPopup:' + id) || '0', 10);
			} catch (err) {
				return 0;
			}
		};
		var lastFocus = null;
		var openPopup = function (pop) {
			if (!pop || !pop.hidden) {
				return;
			}
			lastFocus = doc.activeElement;
			pop.hidden = false;
			requestAnimationFrame(function () { pop.classList.add('is-open'); });
			doc.documentElement.classList.add('bs-popup-lock');
			var dialog = pop.querySelector('.bs-popup__dialog');
			if (dialog) {
				dialog.focus();
			}
			store(pop.getAttribute('data-bs-popup'), true);
		};
		var closePopup = function (pop) {
			pop.classList.remove('is-open');
			doc.documentElement.classList.remove('bs-popup-lock');
			setTimeout(function () { pop.hidden = true; }, 250);
			if (lastFocus && lastFocus.focus) {
				lastFocus.focus();
			}
		};
		doc.querySelectorAll('[data-bs-popup]').forEach(function (pop) {
			var id = pop.getAttribute('data-bs-popup');
			var trigger = pop.getAttribute('data-trigger');
			var days = parseFloat(pop.getAttribute('data-frequency') || '0');
			var seen = store(id);
			var allowed = !days || !seen || Date.now() - seen > days * 864e5;
			if (pop.parentNode !== doc.body) {
				doc.body.appendChild(pop);
			}
			if (trigger === 'delay' && allowed) {
				setTimeout(function () { openPopup(pop); }, parseFloat(pop.getAttribute('data-delay') || '0') * 1000);
			}
			if (trigger === 'scroll' && allowed) {
				var target = parseFloat(pop.getAttribute('data-scroll') || '50') / 100;
				var onScroll = function () {
					var max = doc.documentElement.scrollHeight - innerHeight;
					if (max > 0 && scrollY / max >= target) {
						removeEventListener('scroll', onScroll);
						openPopup(pop);
					}
				};
				addEventListener('scroll', onScroll, { passive: true });
			}
			if (trigger === 'exit' && allowed && matchMedia('(pointer:fine)').matches) {
				var onLeave = function (e) {
					if (e.clientY <= 0) {
						doc.removeEventListener('mouseout', onLeave);
						openPopup(pop);
					}
				};
				doc.addEventListener('mouseout', onLeave);
			}
			pop.addEventListener('click', function (e) {
				var close = e.target.closest('[data-bs-popup-close]');
				if (close && (!close.classList.contains('bs-popup__overlay') || pop.getAttribute('data-close-outside') === '1')) {
					closePopup(pop);
				}
			});
		});
		doc.addEventListener('click', function (e) {
			var btn = e.target.closest('[data-bs-popup-open], a[href^="#popup-"]');
			if (!btn) {
				return;
			}
			var id = btn.getAttribute('data-bs-popup-open') || btn.getAttribute('href').slice(1);
			var pop = doc.querySelector('[data-bs-popup="' + id + '"]');
			if (pop) {
				e.preventDefault();
				openPopup(pop);
			}
		});
		doc.addEventListener('keydown', function (e) {
			if (e.key === 'Escape') {
				doc.querySelectorAll('.bs-popup.is-open').forEach(closePopup);
			}
		});

		/* Reading progress bar */
		var bar = doc.querySelector('.bs-reading-progress');
		if (bar) {
			var article = doc.querySelector('.bs-entry__content') || doc.querySelector('main');
			var update = function () {
				var r = article.getBoundingClientRect();
				var total = r.height - innerHeight;
				var p = total > 0 ? Math.min(1, Math.max(0, -r.top / total)) : 1;
				bar.style.transform = 'scaleX(' + p + ')';
			};
			addEventListener('scroll', update, { passive: true });
			update();
		}

		/* Instant page: prefetch internal links on hover / touch. */
		if (doc.body.classList.contains('bs-instant-page') && !(navigator.connection && navigator.connection.saveData)) {
			var done = {};
			var prefetch = function (e) {
				var a = e.target.closest && e.target.closest('a[href]');
				if (!a || a.origin !== location.origin || a.hash && a.pathname === location.pathname || done[a.href] || /wp-admin|wp-login|add-to-cart|\?|\.(pdf|zip|jpg|png)$/i.test(a.href) || a.hasAttribute('download')) {
					return;
				}
				done[a.href] = true;
				var l = doc.createElement('link');
				l.rel = 'prefetch';
				l.href = a.href;
				doc.head.appendChild(l);
			};
			doc.addEventListener('mouseover', prefetch, { passive: true });
			doc.addEventListener('touchstart', prefetch, { passive: true });
		}

		/* Cookie notice */
		var cookie = doc.querySelector('.bs-cookie');
		if (cookie) {
			var ok = false;
			try { ok = localStorage.getItem('bsCookieOk') === '1'; } catch (err) {}
			if (!ok) {
				cookie.hidden = false;
			}
			cookie.addEventListener('click', function (e) {
				if (e.target.closest('[data-bs-cookie-ok]')) {
					try { localStorage.setItem('bsCookieOk', '1'); } catch (err) {}
					cookie.hidden = true;
				}
			});
		}
	});
})();

/* Form block: redirect, thank-you message and hide-on-success after Contact Form 7 sends (1.51). */
document.addEventListener('wpcf7mailsent', function (e) {
	var box = e.target && e.target.closest && e.target.closest('.bs-form');
	if (!box) {
		return;
	}
	var ok = box.querySelector('.bs-form__success');
	if (ok) {
		ok.hidden = false;
	}
	if (box.getAttribute('data-bs-hide-on-success') === '1') {
		var body = box.querySelector('.bs-form__body');
		if (body) {
			body.hidden = true;
		}
	}
	var url = e.detail && e.detail.native ? '' : box.getAttribute('data-bs-redirect');
	if (url) {
		setTimeout(function () { window.location.href = url; }, parseFloat(box.getAttribute('data-bs-redirect-delay') || '0') * 1000);
	}
}, false);

/* Built-in form submit (1.54): AJAX send, inline errors, success / redirect like Contact Form 7. */
document.addEventListener('submit', function (e) {
	var form = e.target;
	if (!form.matches || !form.matches('[data-bs-native-form]')) {
		return;
	}
	e.preventDefault();
	var out = form.querySelector('.wpcf7-response-output');
	var btn = form.querySelector('[type="submit"]');
	form.querySelectorAll('.wpcf7-not-valid-tip').forEach(function (n) { n.remove(); });
	form.querySelectorAll('.wpcf7-not-valid').forEach(function (n) { n.classList.remove('wpcf7-not-valid'); });
	var tip = function (name, msg) {
		var wrap = form.querySelector('.wpcf7-form-control-wrap[data-name="' + name + '"]');
		if (!wrap) {
			return;
		}
		var ctl = wrap.querySelector('input, select, textarea');
		if (ctl) {
			ctl.classList.add('wpcf7-not-valid');
		}
		var s = document.createElement('span');
		s.className = 'wpcf7-not-valid-tip';
		s.textContent = msg;
		wrap.appendChild(s);
	};
	var bad = false;
	form.querySelectorAll('[required]').forEach(function (ctl) {
		var empty = ctl.type === 'checkbox' ? !ctl.checked : !String(ctl.value).trim();
		if (empty) {
			bad = true;
			tip(ctl.name.replace('[]', ''), form.getAttribute('data-msg-required'));
		}
	});
	form.querySelectorAll('input[type="email"]').forEach(function (ctl) {
		if (ctl.value && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(ctl.value)) {
			bad = true;
			tip(ctl.name, form.getAttribute('data-msg-email'));
		}
	});
	var show = function (msg, ok) {
		out.hidden = false;
		out.textContent = msg;
		form.classList.remove('sent', 'invalid', 'failed');
		form.classList.add(ok ? 'sent' : (ok === null ? 'invalid' : 'failed'));
	};
	if (bad) {
		return;
	}
	var data = new FormData(form);
	data.append('action', 'bst_form_submit');
	data.append('bst_form_key', form.getAttribute('data-key'));
	data.append('bst_form_post', form.getAttribute('data-post') || '0');
	data.append('bst_form_ts', form.getAttribute('data-ts') || '0');
	form.classList.add('submitting');
	if (btn) {
		btn.disabled = true;
	}
	fetch(form.getAttribute('action'), { method: 'POST', body: data, credentials: 'same-origin' }).then(function (r) { return r.json(); }).then(function (res) {
		if (res.errors) {
			Object.keys(res.errors).forEach(function (n) { tip(n, res.errors[n]); });
			show(res.message, null);
			return;
		}
		show(res.message, !!res.ok);
		if (res.ok) {
			form.reset();
			form.dispatchEvent(new CustomEvent('wpcf7mailsent', { bubbles: true, detail: { native: true } }));
			if (res.redirect) {
				var box = form.closest('.bs-form');
				var delay = box ? parseFloat(box.getAttribute('data-bs-redirect-delay') || '0') : 0;
				setTimeout(function () {
					if (form.getAttribute('data-new-tab') === '1') {
						window.open(res.redirect, '_blank', 'noopener');
					} else {
						window.location.href = res.redirect;
					}
				}, delay * 1000);
			}
		}
	}).catch(function () {
		show(form.querySelector('.wpcf7-submit') ? 'Network error, please try again.' : '', false);
	}).then(function () {
		form.classList.remove('submitting');
		if (btn) {
			btn.disabled = false;
		}
	});
});


/* Pricing with monthly / yearly switch (1.66). */
document.addEventListener('click', function (event) {
	var button = event.target.closest('[data-bs-pricing-toggle] [data-set]');
	if (!button) {
		return;
	}
	var root = button.closest('[data-bs-pricing-toggle]');
	var state = button.getAttribute('data-set') === 'b' ? 'b' : 'a';
	root.setAttribute('data-state', state);
	root.querySelectorAll('[data-set]').forEach(function (b) {
		b.setAttribute('aria-pressed', b === button ? 'true' : 'false');
	});
	root.querySelectorAll('[data-a]').forEach(function (node) {
		node.textContent = node.getAttribute('data-' + state) || '';
	});
});
