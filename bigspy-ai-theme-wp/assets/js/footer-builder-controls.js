/* BIGSPY AI Theme WP – G-205 drag & drop footer builder for the Customizer. */
(function ($, api) {
	'use strict';

	var data = window.bstFooterBuilder || {};
	var ROWS = ['top', 'main', 'bottom'];

	function clone(obj) {
		return JSON.parse(JSON.stringify(obj));
	}

	function chip(key) {
		var $chip = $('<li class="bst-hb__chip" />').attr('data-key', key);
		$('<span class="bst-hb__chip-label" />').text(data.elements[key] || key).appendTo($chip);
		return $chip;
	}

	function colCount(row) {
		var id = 'fb_' + row + '_cols';
		var v = api.has(id) ? parseInt(api(id).get(), 10) : data.cols[row];
		return isNaN(v) ? data.cols[row] : Math.max(0, Math.min(data.max, v));
	}

	function FooterBuilder($control, setting) {
		var self = this;
		this.$root = $control.find('[data-bst-footer-builder]');
		this.setting = setting;
		this.layout = this.read();
		this.render();
		ROWS.forEach(function (row) {
			var id = 'fb_' + row + '_cols';
			if (api.has(id)) {
				api(id).bind(function () {
					self.render();
				});
			}
		});
	}

	FooterBuilder.prototype.read = function () {
		var value = this.setting.get();
		var parsed = null;
		try {
			parsed = typeof value === 'string' && value ? JSON.parse(value) : value;
		} catch (e) {
			parsed = null;
		}
		return parsed && parsed.main ? parsed : clone(data.defaults);
	};

	FooterBuilder.prototype.save = function () {
		this.setting.set(JSON.stringify(this.layout));
	};

	FooterBuilder.prototype.render = function () {
		var self = this;
		var i18n = data.i18n;
		var $root = this.$root.empty();
		var used = [];

		ROWS.forEach(function (row) {
			var count = colCount(row);
			var $row = $('<div class="bst-hb__row" />').attr('data-row', row).appendTo($root);
			$('<div class="bst-hb__row-title" />').text(i18n[row]).appendTo($row);
			if (!count) {
				$('<p class="description" />').text(i18n.off).appendTo($row);
			}
			var $zones = $('<div class="bst-hb__zones bst-fb__cols" />').appendTo($row);
			for (var c = 0; c < data.max; c++) {
				var items = (self.layout[row] && self.layout[row][c]) || [];
				// Columns past the count are not shown; their elements go back to the pool.
				if (c >= count) {
					continue;
				}
				var $zone = $('<div class="bst-hb__zone" />').appendTo($zones);
				$('<span class="bst-hb__zone-title" />').text(i18n.column + ' ' + (c + 1)).appendTo($zone);
				var $list = $('<ul class="bst-hb__list" />').attr({ 'data-row': row, 'data-col': c }).appendTo($zone);
				items.forEach(function (key) {
					if (data.elements[key]) {
						chip(key).appendTo($list);
						used.push(key);
					}
				});
			}
		});

		var $pool = $('<div class="bst-hb__pool" />').appendTo($root);
		$('<div class="bst-hb__row-title" />').text(i18n.available).appendTo($pool);
		var $poolList = $('<ul class="bst-hb__list bst-hb__list--pool" />').appendTo($pool);
		Object.keys(data.elements).forEach(function (key) {
			if (used.indexOf(key) === -1) {
				chip(key).appendTo($poolList);
			}
		});

		$('<button type="button" class="button-link bst-hb__reset" />')
			.text(i18n.reset)
			.on('click', function () {
				if (window.confirm(i18n.confirm)) {
					self.layout = clone(data.defaults);
					ROWS.forEach(function (row) {
						var id = 'fb_' + row + '_cols';
						if (api.has(id)) {
							api(id).set(String(data.cols[row]));
						}
					});
					self.save();
					self.render();
				}
			})
			.appendTo($root);

		$root.find('.bst-hb__list').sortable({
			connectWith: $root.find('.bst-hb__list'),
			placeholder: 'bst-hb__placeholder',
			tolerance: 'pointer',
			stop: function () {
				self.collect();
			}
		});
	};

	FooterBuilder.prototype.collect = function () {
		var self = this;
		var layout = {};
		ROWS.forEach(function (row) {
			var count = colCount(row);
			layout[row] = [];
			for (var c = 0; c < data.max; c++) {
				if (c < count) {
					layout[row][c] = self.$root
						.find('.bst-hb__list[data-row="' + row + '"][data-col="' + c + '"] .bst-hb__chip')
						.map(function () {
							return $(this).attr('data-key');
						})
						.get();
				} else {
					layout[row][c] = [];
				}
			}
		});
		this.layout = layout;
		this.save();
	};

	api.controlConstructor['bst-footer-builder'] = api.Control.extend({
		ready: function () {
			new FooterBuilder(this.container, this.setting);
		}
	});
})(jQuery, wp.customize);
