/* G-209: grid / list switch on shop pages; the choice is remembered per browser. */
(function () {
	'use strict';
	var group = document.querySelector('[data-bs-shop-view]');
	if (!group) {
		return;
	}
	var body = document.body;
	function apply(view, save) {
		body.classList.toggle('bs-view-list', view === 'list');
		group.querySelectorAll('[data-view]').forEach(function (btn) {
			btn.setAttribute('aria-pressed', btn.getAttribute('data-view') === view ? 'true' : 'false');
		});
		if (save) {
			try { localStorage.setItem('bsShopView', view); } catch (e) {}
		}
	}
	apply(body.classList.contains('bs-view-list') ? 'list' : 'grid', false);
	group.addEventListener('click', function (event) {
		var btn = event.target.closest('[data-view]');
		if (btn) {
			apply(btn.getAttribute('data-view'), true);
		}
	});
})();
