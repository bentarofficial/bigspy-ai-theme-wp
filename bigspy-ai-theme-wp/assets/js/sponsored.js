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
	/* Sponsors in the slot's rotation list: data-pool (set by the Sponsored blocks) or the whole pool. */
	function listSize(slot) {
		var size = parseInt(slot.getAttribute('data-pool'), 10) || 0;
		return size > 0 ? Math.min(size, items.length) : items.length;
	}
	function shiftFor(slot) {
		var context = slot.getAttribute('data-context') || 'elements';
		var jump = context === 'categories' ? 1 : Math.max(1, parseInt(slot.getAttribute('data-jump'), 10) || 3);
		var size = listSize(slot);
		// Blocks with their own rotation list (start, size, step) rotate on their own.
		context += '-' + jump + '-' + size + '-' + (slot.getAttribute('data-start') || '0');
		if (shifts[context] === undefined) {
			var key = 'bst-sponsored-' + context + '-' + (slot.getAttribute('data-version') || '1');
			var shift;
			try {
				var prev = parseInt(window.localStorage.getItem(key), 10);
				shift = isFinite(prev) ? (prev + jump) % size : 0;
				window.localStorage.setItem(key, String(shift));
			} catch (e) {
				shift = Math.floor(Math.random() * size);
			}
			shifts[context] = shift;
		}
		return shifts[context];
	}
	document.querySelectorAll('[data-bst-sponsored]').forEach(function (slot) {
		var index = parseInt(slot.getAttribute('data-bst-sponsored'), 10) || 0;
		var start = parseInt(slot.getAttribute('data-start'), 10) || 0;
		// Move through the rotation list (start .. start + size) and wrap round to its first sponsor.
		var item = items[(start + ((index - start) + shiftFor(slot)) % listSize(slot)) % items.length];
		var logo = slot.querySelector('[data-logo]');
		var name = slot.querySelector('[data-name]');
		var desc = slot.querySelector('[data-desc]');
		var links = slot.querySelectorAll('[data-link]');
		if (name) { name.textContent = item.name || ''; }
		if (desc) { desc.textContent = item.description || ''; }
		links.forEach(function (link) {
			link.href = item.url || '#';
			if (link.hasAttribute('data-cover')) { link.setAttribute('aria-label', item.name || ''); }
		});
		if (!logo) { return; }
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
