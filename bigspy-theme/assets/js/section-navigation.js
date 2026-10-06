/* Highlight real section anchors without replacing native link navigation. */
(function () {
	'use strict';
	var content = document.getElementById('content');
	if (!content) return;
	var entries = [];
	document.querySelectorAll('.bs-nav a[href*="#"], .bs-offcanvas-menu a[href*="#"]').forEach(function (link) {
		try {
			var url = new URL(link.href, location.href);
			if (url.origin !== location.origin || url.pathname !== location.pathname || url.search !== location.search || !url.hash || url.hash === '#') return;
			var target = document.getElementById(decodeURIComponent(url.hash.slice(1)));
			if (!target || !content.contains(target)) return;
			entries.push({ link: link, target: target });
		} catch (error) { /* Invalid URLs/anchors keep their native behavior. */ }
	});
	if (!entries.length) return;
	var scheduled = false;
	function update() {
		scheduled = false;
		var sticky = document.querySelector('#masthead.has-sticky .bs-header__sticky');
		var admin = document.getElementById('wpadminbar');
		var offset = (sticky ? sticky.getBoundingClientRect().height : 0) + (admin && getComputedStyle(admin).position === 'fixed' ? admin.offsetHeight : 0) + 24;
		var active = null;
		var bestTop = -Infinity;
		entries.forEach(function (entry) {
			entry.target.style.setProperty('--bst-anchor-offset', offset + 'px');
			entry.target.classList.add('bs-section-anchor');
			var top = entry.target.getBoundingClientRect().top;
			if (entry.target.getClientRects().length && top <= offset + 1 && top > bestTop) { active = entry.target; bestTop = top; }
		});
		entries.forEach(function (entry) {
			var current = entry.target === active;
			entry.link.classList.toggle('is-current-section', current);
			if (current) entry.link.setAttribute('aria-current', 'location');
			else entry.link.removeAttribute('aria-current');
		});
	}
	function schedule() {
		if (!scheduled) { scheduled = true; requestAnimationFrame(update); }
	}
	window.addEventListener('scroll', schedule, { passive: true });
	window.addEventListener('resize', schedule);
	window.addEventListener('load', schedule);
	window.addEventListener('hashchange', schedule);
	update();
})();
