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
			if (root.dataset.pause !== '0') {
				root.addEventListener('mouseenter', pause);
				root.addEventListener('mouseleave', resume);
			}
			root.addEventListener('focusin', pause);
			root.addEventListener('focusout', resume);
			root.addEventListener('touchstart', pause, { passive: true });
			var bar = root.querySelector('[data-bs-slider-progress]');
			var restartBar = function () {
				if (!bar) {
					return;
				}
				bar.style.animation = 'none';
				void bar.offsetWidth; // restart the CSS animation
				bar.style.animation = 'bs-slider-progress ' + interval + 'ms linear forwards';
			};
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
