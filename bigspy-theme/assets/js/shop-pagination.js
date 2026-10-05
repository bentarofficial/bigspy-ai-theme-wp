(function () {
	'use strict';
	if (!window.fetch || !window.DOMParser || !document.querySelector('[data-bst-shop-results]')) return;
	var active = 0;
	var navigate = async function (url, push) {
		var ticket = ++active;
		var current = document.querySelector('[data-bst-shop-results]');
		if (!current) { location.assign(url); return; }
		current.setAttribute('aria-busy', 'true');
		try {
			var response = await fetch(url, { credentials: 'same-origin' });
			if (!response.ok || new URL(response.url).origin !== location.origin) throw new Error('Navigation unavailable');
			var doc = new DOMParser().parseFromString(await response.text(), 'text/html');
			var next = doc.querySelector('[data-bst-shop-results]');
			if (!next || next.querySelector('script')) throw new Error('Full navigation required');
			if (ticket !== active) return;
			current.replaceWith(next);
			next.querySelectorAll('form.woocommerce-ordering select.orderby').forEach(function (select) {
				select.addEventListener('change', function () { select.form.submit(); });
			});
			document.title = doc.title;
			if (push) history.pushState({ bstShop: true }, '', url);
			next.focus({ preventScroll: true });
			next.scrollIntoView({ block: 'start', behavior: 'auto' });
			document.dispatchEvent(new CustomEvent('bst:shop-updated', { detail: { url: url } }));
		} catch (error) {
			if (ticket === active) location.assign(url);
		}
	};
	history.replaceState(Object.assign({}, history.state, { bstShop: true }), '', location.href);
	document.addEventListener('click', function (event) {
		var link = event.target.closest('[data-bst-shop-results] .woocommerce-pagination a');
		if (!link || event.defaultPrevented || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey || link.target) return;
		var url = new URL(link.href, location.href);
		if (url.origin !== location.origin) return;
		event.preventDefault();
		navigate(url.href, true);
	});
	window.addEventListener('popstate', function (event) {
		if (event.state && event.state.bstShop) navigate(location.href, false);
		else location.reload();
	});
})();
