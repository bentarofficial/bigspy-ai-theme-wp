(function () {
	'use strict';
	var bar = document.querySelector('[data-product-purchase-bar]');
	var trigger = document.querySelector('[data-product-purchase-trigger]');
	var form = document.querySelector('form.cart');
	if (!bar || !trigger || !form) return;
	trigger.addEventListener('click', function () {
		var firstSelect = form.querySelector('select');
		if (firstSelect && !firstSelect.value) {
			firstSelect.focus();
			firstSelect.scrollIntoView({ behavior: 'smooth', block: 'center' });
			return;
		}
		var addButton = form.querySelector('.single_add_to_cart_button');
		if (!addButton || addButton.disabled) {
			form.scrollIntoView({ behavior: 'smooth', block: 'center' });
			return;
		}
		addButton.click();
	});
	var syncVisibility = function () {
		bar.classList.toggle('is-form-visible', form.getBoundingClientRect().top < window.innerHeight && form.getBoundingClientRect().bottom > 0);
	};
	if ('IntersectionObserver' in window) {
		new IntersectionObserver(function (entries) {
			bar.classList.toggle('is-form-visible', entries[0].isIntersecting);
		}).observe(form);
	} else {
		window.addEventListener('scroll', syncVisibility, { passive: true });
		syncVisibility();
	}
})();
