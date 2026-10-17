(function () {
	'use strict';
	var sidebar = document.getElementById('bs-shop-sidebar');
	var openButton = document.querySelector('[data-bs-shop-filter-open]');
	var scrim = document.querySelector('[data-bs-shop-filter-close]');
	if (!sidebar || !openButton || !scrim) return;
	var closeButton = document.createElement('button');
	closeButton.type = 'button';
	closeButton.className = 'bs-shop-sidebar__close';
	closeButton.setAttribute('aria-label', openButton.getAttribute('data-close-label') || 'Close filters');
	closeButton.setAttribute('data-bs-shop-filter-close-button', '');
	closeButton.textContent = '×';
	sidebar.insertBefore(closeButton, sidebar.firstChild);
	var close = function () {
		document.body.classList.remove('bs-shop-filters-open');
		openButton.setAttribute('aria-expanded', 'false');
		window.setTimeout(function () { if (!document.body.classList.contains('bs-shop-filters-open')) scrim.hidden = true; }, 240);
		openButton.focus();
	};
	openButton.addEventListener('click', function () {
		scrim.hidden = false;
		requestAnimationFrame(function () { document.body.classList.add('bs-shop-filters-open'); });
		openButton.setAttribute('aria-expanded', 'true');
		closeButton.focus();
	});
	closeButton.addEventListener('click', close);
	scrim.addEventListener('click', close);
	document.addEventListener('keydown', function (event) {
		if (!document.body.classList.contains('bs-shop-filters-open')) return;
		if (event.key === 'Escape') {
			close();
			return;
		}
		if (event.key === 'Tab') {
			var focusable = sidebar.querySelectorAll('a[href],button:not([disabled]),input:not([disabled]),select:not([disabled]),textarea:not([disabled]),[tabindex]:not([tabindex="-1"])');
			if (!focusable.length) {
				event.preventDefault();
				closeButton.focus();
				return;
			}
			var first = focusable[0];
			var last = focusable[focusable.length - 1];
			if (event.shiftKey && document.activeElement === first) {
				event.preventDefault();
				last.focus();
			} else if (!event.shiftKey && document.activeElement === last) {
				event.preventDefault();
				first.focus();
			}
		}
	});
	var desktopQuery = window.matchMedia('(min-width: 850px)');
	var resetForDesktop = function (event) {
		if (event.matches) {
			document.body.classList.remove('bs-shop-filters-open');
			scrim.hidden = true;
			openButton.setAttribute('aria-expanded', 'false');
		}
	};
	if (desktopQuery.addEventListener) desktopQuery.addEventListener('change', resetForDesktop);
	else if (desktopQuery.addListener) desktopQuery.addListener(resetForDesktop);
})();
