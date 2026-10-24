(function () {
	'use strict';
	var button = document.querySelector('[data-bst-more]');
	var list = document.querySelector('[data-bst-posts]');
	if (!button || !list || !window.fetch || !window.DOMParser) return;
	var label = button.textContent, loading = false, observer;
	var pager = document.querySelector('.bs-pagination');
	if (pager) pager.hidden = true;

	function load() {
		if (loading || !button.dataset.bstMore) return;
		loading = true;
		button.disabled = true;
		button.textContent = button.dataset.loading || label;
		fetch(button.dataset.bstMore, { credentials: 'same-origin' })
			.then(function (r) { if (!r.ok) throw new Error(r.status); return r.text(); })
			.then(function (html) {
				var doc = new DOMParser().parseFromString(html, 'text/html');
				var fresh = doc.querySelector('[data-bst-posts]');
				if (fresh) {
					Array.prototype.forEach.call(fresh.children, function (el) { list.appendChild(document.importNode(el, true)); });
				}
				var next = doc.querySelector('[data-bst-more]');
				if (next) {
					button.dataset.bstMore = next.dataset.bstMore;
				} else {
					button.parentNode.remove();
					if (observer) observer.disconnect();
				}
			})
			.catch(function () { location.assign(button.dataset.bstMore); })
			.then(function () { loading = false; button.disabled = false; button.textContent = label; });
	}

	button.addEventListener('click', load);
	if (button.dataset.mode === 'infinite' && window.IntersectionObserver) {
		observer = new IntersectionObserver(function (entries) { if (entries[0].isIntersecting) load(); }, { rootMargin: '300px' });
		observer.observe(button);
	}
})();
