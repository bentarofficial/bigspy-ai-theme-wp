/**
 * BIGSPY AI List "Listing elements" block: Load more button and slider arrows.
 */
(function () {
	'use strict';
	document.addEventListener('click', function (event) {
		var btn = event.target.closest('[data-bst-le-load]');
		if (btn && !btn.disabled) {
			loadMore(btn);
			return;
		}
		var arrow = event.target.closest('[data-bst-le-slider] .bst-lslider__btn');
		if (arrow) {
			var track = arrow.parentNode.querySelector('.bst-lgrid');
			if (track) {
				track.scrollBy({ left: (parseInt(arrow.getAttribute('data-dir'), 10) || 1) * track.clientWidth * 0.9, behavior: 'smooth' });
			}
		}
	});

	function loadMore(btn) {
		var root = btn.closest('.bst-lelements');
		var label = btn.textContent;
		var attrs;
		try {
			attrs = JSON.parse(btn.getAttribute('data-bst-le-load'));
		} catch (e) {
			return;
		}
		btn.disabled = true;
		btn.setAttribute('aria-busy', 'true');
		btn.textContent = btn.getAttribute('data-loading') || label;
		fetch(btn.getAttribute('data-endpoint'), {
			method: 'POST',
			credentials: 'same-origin',
			headers: { 'Content-Type': 'application/json' },
			body: JSON.stringify({ attrs: attrs })
		}).then(function (r) {
			return r.json();
		}).then(function (data) {
			var tpl = document.createElement('template');
			tpl.innerHTML = (data && data.html) || '';
			var incoming = tpl.content.querySelector('.bst-lgrid, .bst-lposts');
			var target = root && root.querySelector('.bst-lgrid, .bst-lposts');
			var wrap = btn.parentNode;
			if (!incoming || !target || !incoming.children.length) {
				wrap.remove();
				return;
			}
			Array.prototype.slice.call(incoming.children).forEach(function (item) {
				target.appendChild(item);
			});
			var next = tpl.content.querySelector('.bst-lelements__more');
			if (next) {
				wrap.replaceWith(next);
			} else {
				wrap.remove();
			}
		}).catch(function () {
			btn.disabled = false;
			btn.removeAttribute('aria-busy');
			btn.textContent = label;
		});
	}
})();
