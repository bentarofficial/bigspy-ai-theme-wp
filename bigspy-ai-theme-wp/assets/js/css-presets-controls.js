/* BIGSPY CSS library: only allowlisted IDs are stored in a theme mod. */
(function (api) {
	'use strict';

	function fold(value) {
		value = String(value || '').toLowerCase().replace(/đ/g, 'd');
		return value.normalize ? value.normalize('NFD').replace(/[\u0300-\u036f]/g, '') : value;
	}

	api.controlConstructor['bst-css-presets'] = api.Control.extend({
		ready: function () {
			var setting = this.setting;
			var root = this.container[0].querySelector('[data-bst-css-presets]');
			var rows = Array.prototype.slice.call(root.querySelectorAll('[data-preset]'));
			var groups = Array.prototype.slice.call(root.querySelectorAll('details'));
			var search = root.querySelector('input[type=search]');
			var activeOnly = root.querySelector('.bst-css-presets__active-only');
			var clear = root.querySelector('.bst-css-presets__clear');
			var undo = root.querySelector('.bst-css-presets__undo');
			var previous = null;
			var filtering = false;
			var openBeforeFilter = [];

			function sync() {
				var selected = Array.isArray(setting.get()) ? setting.get() : [];
				rows.forEach(function (row) {
					row.querySelector('input').checked = selected.indexOf(row.dataset.preset) !== -1;
				});
				root.querySelector('[data-bst-enabled-count]').textContent = rows.filter(function (row) { return row.querySelector('input').checked; }).length;
				clear.disabled = !selected.length;
				filter();
			}

			function filter() {
				var words = fold(search.value.trim()).split(/\s+/).filter(Boolean);
				var isFiltering = !!words.length || activeOnly.checked;
				if (isFiltering && !filtering) {
					openBeforeFilter = groups.map(function (group) { return group.open; });
				}
				rows.forEach(function (row) {
					var haystack = fold(row.textContent + ' ' + row.closest('details').querySelector('summary').textContent);
					row.hidden = !words.every(function (word) { return haystack.indexOf(word) !== -1; }) || (activeOnly.checked && !row.querySelector('input').checked);
				});
				groups.forEach(function (group, index) {
					var groupRows = Array.prototype.slice.call(group.querySelectorAll('[data-preset]'));
					group.hidden = !groupRows.some(function (row) { return !row.hidden; });
					group.querySelector('.bst-css-presets__group-count').textContent = groupRows.filter(function (row) { return row.querySelector('input').checked; }).length + '/' + groupRows.length;
					if (isFiltering) { group.open = !group.hidden; }
					else if (filtering) { group.open = !!openBeforeFilter[index]; }
				});
				root.querySelector('.bst-css-presets__empty').hidden = rows.some(function (row) { return !row.hidden; });
				filtering = isFiltering;
			}

			rows.forEach(function (row) {
				row.querySelector('input').addEventListener('change', function (event) {
					var id = row.dataset.preset;
					var family = row.dataset.exclusive;
					var selected = Array.isArray(setting.get()) ? setting.get().slice() : [];
					selected = selected.filter(function (key) {
						if (key === id) { return false; }
						return !event.target.checked || !family || !rows.some(function (other) { return other.dataset.preset === key && other.dataset.exclusive === family; });
					});
					if (event.target.checked) { selected.push(id); }
					previous = null;
					undo.hidden = true;
					setting.set(selected);
				});
			});
			search.addEventListener('input', filter);
			activeOnly.addEventListener('change', filter);
			clear.addEventListener('click', function () {
				previous = Array.isArray(setting.get()) ? setting.get().slice() : [];
				undo.hidden = false;
				setting.set([]);
			});
			undo.addEventListener('click', function () {
				if (previous) { setting.set(previous); }
				previous = null;
				undo.hidden = true;
			});
			setting.bind(sync);
			sync();
		}
	});
})(wp.customize);
