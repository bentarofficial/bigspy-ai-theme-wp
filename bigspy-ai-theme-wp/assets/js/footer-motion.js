/**
 * Footer scroll effect (Customizer > Footer widgets > Footer scroll effect).
 * Reveal: footer columns fade up in turn when the footer comes into view.
 * Curtain: the page content slides up and uncovers the footer, only while the footer fits the screen.
 * Nothing moves for visitors who ask for reduced motion.
 */
(function () {
	'use strict';
	var body = document.body, footer = document.querySelector('.bs-footer');
	if (!footer || !body.classList.contains('bs-footer-fx') || window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
		return;
	}
	var items = footer.querySelectorAll('.bs-footer-widgets__col, .bs-footer-design .bs-column, .bs-footer-design .bs-section__inner > :not(.bs-row), .bs-footer-bottom');
	if (!items.length) { items = footer.children; }
	Array.prototype.forEach.call(items, function (el, i) { el.classList.add('bs-fx-item'); el.style.setProperty('--bs-fx-i', Math.min(i, 8)); });

	if (body.classList.contains('bs-footer-fx--curtain')) {
		var fit = function () { body.classList.toggle('is-curtain', footer.offsetHeight < window.innerHeight * 0.85 && window.innerWidth >= 768); };
		fit();
		window.addEventListener('resize', fit, { passive: true });
	}
	if (!('IntersectionObserver' in window)) { return; }
	footer.classList.add('is-fx-ready');
	var io = new IntersectionObserver(function (entries) {
		if (entries[0].isIntersecting) { footer.classList.add('is-in'); io.disconnect(); }
	}, { rootMargin: '0px 0px -10% 0px', threshold: 0.05 });
	io.observe(footer);
}());
