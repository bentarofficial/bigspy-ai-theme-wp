/* BIGSPY AI Theme WP – Table block: optional column sorting and row search. Loaded only when a table turns them on. */
(function () {
	'use strict';
	var L = window.bstTableL10n || {};

	function cellText(row, i) {
		var c = row.children[i];
		return c ? c.textContent.trim() : '';
	}

	function toNumber(s) {
		var n = s.replace(/[^\d,.\-]/g, '');
		if (/^-?\d{1,3}([.,]\d{3})+$/.test(n)) {
			n = n.replace(/[.,]/g, '');
		}
		n = parseFloat(n.replace(',', '.'));
		return isNaN(n) ? null : n;
	}

	function initSort(fig, table) {
		var head = table.tHead;
		var body = table.tBodies[0];
		if (!head || !body) {
			return;
		}
		Array.prototype.forEach.call(head.rows[0].cells, function (th, i) {
			var btn = document.createElement('button');
			btn.type = 'button';
			btn.className = 'bs-table__sort';
			btn.setAttribute('aria-label', (L.sort || 'Sort by this column') + ': ' + th.textContent.trim());
			while (th.firstChild) {
				btn.appendChild(th.firstChild);
			}
			th.appendChild(btn);
			btn.addEventListener('click', function () {
				var asc = th.getAttribute('aria-sort') !== 'ascending';
				Array.prototype.forEach.call(head.rows[0].cells, function (o) { o.removeAttribute('aria-sort'); });
				th.setAttribute('aria-sort', asc ? 'ascending' : 'descending');
				var rows = Array.prototype.slice.call(body.rows);
				rows.sort(function (a, b) {
					var x = cellText(a, i), y = cellText(b, i);
					var nx = toNumber(x), ny = toNumber(y);
					var r = nx !== null && ny !== null ? nx - ny : x.localeCompare(y, undefined, { numeric: true, sensitivity: 'base' });
					return asc ? r : -r;
				});
				rows.forEach(function (r) { body.appendChild(r); });
			});
		});
	}

	function initSearch(fig, table) {
		var body = table.tBodies[0];
		if (!body) {
			return;
		}
		var input = document.createElement('input');
		input.type = 'search';
		input.className = 'bs-table__search';
		input.placeholder = L.search || 'Search the table';
		input.setAttribute('aria-label', input.placeholder);
		fig.insertBefore(input, fig.firstChild);
		input.addEventListener('input', function () {
			var q = input.value.trim().toLowerCase();
			Array.prototype.forEach.call(body.rows, function (r) {
				r.hidden = q !== '' && r.textContent.toLowerCase().indexOf(q) === -1;
			});
		});
	}

	function init() {
		document.querySelectorAll('[data-bs-table]').forEach(function (fig) {
			if (fig._bsTable) {
				return;
			}
			fig._bsTable = true;
			var table = fig.querySelector('table');
			var mode = fig.getAttribute('data-bs-table');
			if (!table) {
				return;
			}
			if (mode.indexOf('sort') !== -1) {
				initSort(fig, table);
			}
			if (mode.indexOf('search') !== -1) {
				initSearch(fig, table);
			}
		});
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
}());
