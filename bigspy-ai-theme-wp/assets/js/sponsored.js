/**
 * Sponsored slots: each visit moves every group of slots (data-context) forward by
 * data-jump sponsors, remembered per browser; a new data-version (AD set changed) starts over.
 */
(function () {
	'use strict';
	var pool = document.querySelector('[data-bst-sponsored-pool]');
	var items = [];
	try { items = JSON.parse(pool ? pool.textContent : '[]'); } catch (e) { items = []; }
	if (items.length < 2) { return; }
	var shifts = {};
	function shiftFor(slot) {
		var context = slot.getAttribute('data-context') || 'elements';
		if (shifts[context] === undefined) {
			var key = 'bst-sponsored-' + context + '-' + (slot.getAttribute('data-version') || '1');
			var jump = context === 'categories' ? 1 : Math.max(1, parseInt(slot.getAttribute('data-jump'), 10) || 3);
			var shift;
			try {
				var prev = parseInt(window.localStorage.getItem(key), 10);
				shift = isFinite(prev) ? (prev + jump) % items.length : 0;
				window.localStorage.setItem(key, String(shift));
			} catch (e) {
				shift = Math.floor(Math.random() * items.length);
			}
			shifts[context] = shift;
		}
		return shifts[context];
	}
	document.querySelectorAll('[data-bst-sponsored]').forEach(function (slot) {
		var item = items[((parseInt(slot.getAttribute('data-bst-sponsored'), 10) || 0) + shiftFor(slot)) % items.length];
		var logo = slot.querySelector('[data-logo]');
		slot.querySelector('[data-name]').textContent = item.name || '';
		slot.querySelector('[data-desc]').textContent = item.description || '';
		slot.querySelector('[data-link]').href = item.url || '#';
		logo.replaceChildren();
		if (item.logo) {
			var img = document.createElement('img');
			img.src = item.logo;
			img.alt = '';
			img.loading = 'lazy';
			logo.appendChild(img);
		} else {
			logo.textContent = (item.name || '?').trim().charAt(0).toUpperCase();
		}
	});
})();
