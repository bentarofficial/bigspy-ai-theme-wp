/* BIGSPY AI Theme WP – drag & drop header builder for the Customizer. */
(function ($, api) {
	'use strict';

	var data = window.bstCustomizer || {};
	var ROWS = ['top', 'main', 'bottom'];
	var ZONES = ['left', 'center', 'right'];

	// Element → Customizer section that holds its options.
	var SETTINGS_SECTION = {
		logo: 'bst_header_logo',
		nav: 'bst_header_nav',
		nav_secondary: 'menu_locations',
		nav_icon: 'bst_header_nav',
		search: 'bst_header_search',
		search_form: 'bst_header_search',
		cart: 'bst_shop',
		button_1: 'bst_header_buttons',
		button_2: 'bst_header_buttons',
		html_1: 'bst_header_html',
		html_2: 'bst_header_html',
		html_3: 'bst_header_html',
		contact: 'bst_contact',
		social: 'bst_contact'
	};

	function clone(obj) {
		return JSON.parse(JSON.stringify(obj));
	}

	function chip(key) {
		var label = data.elements[key] || key;
		var $chip = $('<li class="bst-hb__chip" />').attr('data-key', key);
		$('<span class="bst-hb__chip-label" />').text(label).appendTo($chip);
		if (SETTINGS_SECTION[key]) {
			$('<button type="button" class="bst-hb__chip-edit" aria-label="Settings"><span class="dashicons dashicons-admin-generic"></span></button>')
				.attr('data-section', SETTINGS_SECTION[key])
				.appendTo($chip);
		}
		return $chip;
	}

	function HeaderBuilder($control, setting) {
		this.$root = $control.find('[data-bst-header-builder]');
		this.setting = setting;
		this.device = 'desktop';
		this.layout = this.read();
		this.render();
	}

	HeaderBuilder.prototype.read = function () {
		var value = this.setting.get();
		var parsed = null;
		try {
			parsed = typeof value === 'string' && value ? JSON.parse(value) : value;
		} catch (e) {
			parsed = null;
		}
		return parsed && parsed.desktop ? parsed : clone(data.defaults);
	};

	HeaderBuilder.prototype.save = function () {
		this.setting.set(JSON.stringify(this.layout));
	};

	HeaderBuilder.prototype.used = function () {
		var used = [];
		var dev = this.layout[this.device];
		ROWS.forEach(function (row) {
			ZONES.forEach(function (zone) {
				used = used.concat((dev[row] && dev[row][zone]) || []);
			});
		});
		return used;
	};

	HeaderBuilder.prototype.render = function () {
		var self = this;
		var i18n = data.i18n;
		var $root = this.$root.empty();

		var $tabs = $('<div class="bst-hb__tabs" role="tablist" />').appendTo($root);
		['desktop', 'mobile'].forEach(function (device) {
			$('<button type="button" class="button" role="tab" />')
				.text(i18n[device])
				.toggleClass('button-primary', device === self.device)
				.attr('aria-selected', device === self.device ? 'true' : 'false')
				.on('click', function () {
					self.device = device;
					self.render();
				})
				.appendTo($tabs);
		});

		var dev = this.layout[this.device];
		ROWS.forEach(function (row) {
			var $row = $('<div class="bst-hb__row" />').attr('data-row', row).appendTo($root);
			$('<div class="bst-hb__row-title" />').text(i18n[row]).appendTo($row);
			var $zones = $('<div class="bst-hb__zones" />').appendTo($row);
			ZONES.forEach(function (zone) {
				var $zone = $('<div class="bst-hb__zone" />').appendTo($zones);
				$('<span class="bst-hb__zone-title" />').text(i18n[zone]).appendTo($zone);
				var $list = $('<ul class="bst-hb__list" />').attr({ 'data-row': row, 'data-zone': zone }).appendTo($zone);
				((dev[row] && dev[row][zone]) || []).forEach(function (key) {
					if (data.elements[key]) {
						chip(key).appendTo($list);
					}
				});
			});
		});

		var used = this.used();
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
					self.layout[self.device] = clone(data.defaults[self.device]);
					self.save();
					self.render();
				}
			})
			.appendTo($root);

		$root.find('.bst-hb__list').sortable({
			connectWith: $root.find('.bst-hb__list'),
			placeholder: 'bst-hb__placeholder',
			tolerance: 'pointer',
			cancel: '.bst-hb__chip-edit',
			stop: function () {
				self.collect();
			}
		});

		$root.find('.bst-hb__chip-edit').on('click', function (event) {
			event.preventDefault();
			var section = api.section($(this).data('section'));
			if (section) {
				section.focus();
			}
		});
	};

	HeaderBuilder.prototype.collect = function () {
		var self = this;
		var dev = {};
		ROWS.forEach(function (row) {
			dev[row] = {};
			ZONES.forEach(function (zone) {
				dev[row][zone] = self.$root
					.find('.bst-hb__list[data-row="' + row + '"][data-zone="' + zone + '"] .bst-hb__chip')
					.map(function () {
						return $(this).attr('data-key');
					})
					.get();
			});
		});
		this.layout[this.device] = dev;
		this.save();
	};

	api.controlConstructor['bst-header-builder'] = api.Control.extend({
		ready: function () {
			new HeaderBuilder(this.container, this.setting);
		}
	});

	api.controlConstructor['bst-layout-presets'] = api.Control.extend({
		ready: function () {
			var $root = this.container.find('[data-bst-layout-presets]');
			var $status = this.container.find('.bst-layout-presets__status');
			$root.on('click', '[data-settings]', function () {
				var settings;
				try {
					settings = JSON.parse($(this).attr('data-settings'));
				} catch (error) {
					return;
				}
				Object.keys(settings).forEach(function (key) {
					var setting = api(key);
					if (setting) setting.set('header_layout' === key ? JSON.stringify(settings[key]) : settings[key]);
				});
				$status.text(data.i18n.presetApplied || 'Layout applied. Preview it, then Publish to save.');
			});
		}
	});
})(jQuery, wp.customize);
