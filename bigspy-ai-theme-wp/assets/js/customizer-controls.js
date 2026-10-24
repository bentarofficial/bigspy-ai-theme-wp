/* BIGSPY AI Theme WP – drag & drop header builder for the Customizer. */
(function ($, api) {
	'use strict';

	var data = window.bstCustomizer || {};
	var ROWS = ['top', 'main', 'bottom'];
	var ZONES = ['left', 'center', 'right'];

	// Element → its own Customizer section (registered in inc/header-elements.php).
	var SETTINGS_SECTION = new Proxy({}, {
		get: function (target, key) {
			return typeof key === 'string' && api.section('bst_he_' + key) ? 'bst_he_' + key : (key === 'nav_secondary' ? 'menu_locations' : undefined);
		}
	});

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

		// Full-width banner code for the current device (settings header_banner_desktop / _mobile).
		var bannerId = 'header_banner_' + this.device;
		if (wp.customize.has(bannerId)) {
			var $banner = $('<div class="bst-hb__row bst-hb__banner" />').appendTo($root);
			$('<div class="bst-hb__row-title" />').text(i18n.banner).appendTo($banner);
			$('<textarea class="bst-hb__banner-code" rows="5" spellcheck="false" />')
				.attr('placeholder', '<div style="...">...</div>')
				.val(wp.customize(bannerId).get() || '')
				.on('input', function () { wp.customize(bannerId).set(this.value); })
				.appendTo($banner);
			$('<p class="description" />').text(i18n.bannerHelp).appendTo($banner);
		}

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
			// Highlight which element is being edited.
			$('.bst-hb__chip').removeClass('is-editing');
			$(this).closest('.bst-hb__chip').addClass('is-editing');
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

	/* ------------------------------------------------------------ Floating contact buttons: collapsible, sortable list */
	api.controlConstructor['bst-float-items'] = api.Control.extend({
		ready: function () {
			var control = this;
			var cfg = window.bstFloatItems || { meta: {}, items: [], i18n: {} };
			var t = cfg.i18n;
			var items = (cfg.items || []).map(function (r) { return $.extend({}, r); });
			var $root = control.container.find('[data-bst-float-items]');
			var openId = null;

			var save = function () {
				control.setting.set(JSON.stringify(items));
			};
			var esc = function (str) {
				return $('<div />').text(str == null ? '' : String(str)).html();
			};
			var field = function (label, html) {
				return '<label class="bst-fi__field"><span>' + esc(label) + '</span>' + html + '</label>';
			};
			var colorField = function (label, key, value, fallback) {
				return '<div class="bst-fi__field bst-fi__field--color"><span>' + esc(label) + '</span><span class="bst-fi__color"><input type="color" data-key="' + key + '" value="' + esc(value || fallback || '#000000') + '"' + (value ? '' : ' data-unset="1"') + '><button type="button" class="button-link" data-reset="' + key + '">' + esc(t.reset) + '</button></span></div>';
			};
			var select = function (key, value, options) {
				return '<select data-key="' + key + '">' + options.map(function (o) {
					return '<option value="' + o[0] + '"' + ((value || '') === o[0] ? ' selected' : '') + '>' + esc(o[1]) + '</option>';
				}).join('') + '</select>';
			};

			var render = function () {
				var html = '<ul class="bst-fi__list">';
				items.forEach(function (row) {
					var m = cfg.meta[row.k] || {};
					var open = openId === row.id;
					html += '<li class="bst-fi__item' + (row.on ? ' is-on' : '') + (open ? ' is-open' : '') + '" data-id="' + esc(row.id) + '">';
					html += '<div class="bst-fi__head">';
					html += '<span class="bst-fi__drag dashicons dashicons-menu" title="Drag"></span>';
					html += '<span class="bst-fi__icon" style="background:' + esc(row.c || m.color) + ';color:' + esc(row.ic || '#fff') + '">' + (m.icon || '') + '</span>';
					html += '<button type="button" class="bst-fi__title" aria-expanded="' + (open ? 'true' : 'false') + '"><strong>' + esc(row.l || m.label) + '</strong><small>' + esc(row.v || t.empty) + '</small></button>';
					html += '<label class="bst-fi__switch"><input type="checkbox" data-toggle' + (row.on ? ' checked' : '') + '><span></span></label>';
					html += '<span class="bst-fi__chev dashicons dashicons-arrow-down-alt2"></span>';
					html += '</div>';
					if (open) {
						html += '<div class="bst-fi__body">';
						html += field(t.value, '<input type="text" data-key="v" value="' + esc(row.v) + '" placeholder="' + esc(m.hint) + '">');
						html += field(t.label, '<input type="text" data-key="l" value="' + esc(row.l) + '" placeholder="' + esc(m.label) + '">');
						html += field(t.sub, '<input type="text" data-key="s" value="' + esc(row.s) + '">');
						html += '<div class="bst-fi__row">' + colorField(t.bg, 'c', row.c, m.color) + colorField(t.ic, 'ic', row.ic, '#ffffff') + '</div>';
						html += field(t.dev, select('dev', row.dev, [['', t.all], ['desktop', t.desk], ['mobile', t.mob]]));
						html += field(t.fx, select('fx', row.fx, [['', t.fxDef], ['none', t.none], ['ring', t.ring], ['pulse', t.pulse], ['shake', t.shake], ['bounce', t.bounce]]));
						if (row.k === 'custom' || row.k === 'map' || row.k === 'facebook') {
							html += '<label class="bst-fi__check"><input type="checkbox" data-key="nt"' + (row.nt ? ' checked' : '') + '> ' + esc(t.nt) + '</label>';
						}
						if (row.k === 'custom' && row.id !== 'custom') {
							html += '<button type="button" class="button-link button-link-delete" data-remove>' + esc(t.remove) + '</button>';
						}
						html += '</div>';
					}
					html += '</li>';
				});
				html += '</ul><button type="button" class="button bst-fi__add">' + esc(t.add) + '</button>';
				$root.html(html);
				$root.find('.bst-fi__list').sortable({
					handle: '.bst-fi__drag',
					axis: 'y',
					placeholder: 'bst-fi__placeholder',
					stop: function () {
						var order = $root.find('.bst-fi__item').map(function () { return $(this).data('id'); }).get();
						items.sort(function (a, b) { return order.indexOf(a.id) - order.indexOf(b.id); });
						save();
					}
				});
			};
			var rowOf = function (el) {
				var id = $(el).closest('.bst-fi__item').data('id');
				return items.filter(function (r) { return String(r.id) === String(id); })[0];
			};

			$root.on('click', '.bst-fi__title, .bst-fi__chev', function () {
				var row = rowOf(this);
				openId = openId === row.id ? null : row.id;
				render();
			});
			$root.on('change', '[data-toggle]', function () {
				rowOf(this).on = this.checked;
				$(this).closest('.bst-fi__item').toggleClass('is-on', this.checked);
				save();
			});
			$root.on('input change', '.bst-fi__body [data-key]', function () {
				var row = rowOf(this);
				var key = $(this).data('key');
				row[key] = this.type === 'checkbox' ? this.checked : $(this).val();
				$(this).removeAttr('data-unset');
				var $item = $(this).closest('.bst-fi__item');
				if (key === 'v' || key === 'l') {
					var m = cfg.meta[row.k] || {};
					$item.find('.bst-fi__title strong').text(row.l || m.label);
					$item.find('.bst-fi__title small').text(row.v || t.empty);
				}
				if (key === 'c' || key === 'ic') {
					$item.find('.bst-fi__icon').css(key === 'c' ? 'background' : 'color', $(this).val());
				}
				save();
			});
			$root.on('click', '[data-reset]', function () {
				var row = rowOf(this);
				row[$(this).data('reset')] = '';
				save();
				render();
			});
			$root.on('click', '[data-remove]', function () {
				var row = rowOf(this);
				items = items.filter(function (r) { return r !== row; });
				openId = null;
				save();
				render();
			});
			$root.on('click', '.bst-fi__add', function () {
				var id = 'custom-' + Date.now().toString(36);
				items.push({ k: 'custom', id: id, on: true, v: '', l: '', s: '', c: '', ic: '', dev: '', fx: '', nt: false });
				openId = id;
				save();
				render();
			});
			render();
		}
	});

	/* ------------------------------------------------------------ Collapsible groups + search for the whole Customizer */
	var GROUP_KEY = 'bstCustomizerGroups';
	var openGroups = {};
	try { openGroups = JSON.parse(window.localStorage.getItem(GROUP_KEY) || '{}') || {}; } catch (e) { openGroups = {}; }
	var rememberGroups = function () {
		try { window.localStorage.setItem(GROUP_KEY, JSON.stringify(openGroups)); } catch (e) {}
	};

	/* Every heading control turns the controls below it (until the next heading) into a collapsible group. */
	function buildGroups(section) {
		var $list = section.contentContainer;
		if (!$list || $list.data('bstGroups')) {
			return;
		}
		var $controls = $list.children('li.customize-control');
		var $heads = $controls.filter('.customize-control-bst-heading');
		if (!$heads.length) {
			return;
		}
		$list.data('bstGroups', true);
		var first = true;
		$heads.each(function () {
			var $head = $(this);
			var key = section.id + '|' + $head.attr('id');
			var $members = $head.nextUntil('.customize-control-bst-heading', 'li.customize-control');
			if (!$members.length) {
				return;
			}
			var open = Object.prototype.hasOwnProperty.call(openGroups, key) ? openGroups[key] : first;
			first = false;
			$head.addClass('bst-group-head').attr({ role: 'button', tabindex: 0, 'aria-expanded': open ? 'true' : 'false' });
			$head.find('.bst-customizer-heading').append('<span class="bst-group-count">' + $members.length + '</span><span class="bst-group-chev dashicons dashicons-arrow-down-alt2" aria-hidden="true"></span>');
			$members.addClass('bst-group-member');
			var apply = function (state) {
				$head.toggleClass('is-open', state).attr('aria-expanded', state ? 'true' : 'false');
				$members.toggleClass('bst-group-hidden', !state);
			};
			apply(open);
			$head.on('click keydown', function (e) {
				if (e.type === 'keydown' && e.key !== 'Enter' && e.key !== ' ') {
					return;
				}
				if ($(e.target).closest('a, input, select, textarea, button').length) {
					return;
				}
				e.preventDefault();
				var state = !$head.hasClass('is-open');
				openGroups[key] = state;
				rememberGroups();
				apply(state);
			});
			$head.data('bstOpen', function () {
				openGroups[key] = true;
				rememberGroups();
				apply(true);
			});
		});
	}

	/* Opening a control (search, gear, focus link) also opens its group. */
	function revealControl(control) {
		var $li = control.container;
		if ($li.hasClass('bst-group-hidden')) {
			var $head = $li.prevAll('.customize-control-bst-heading').first();
			var fn = $head.data('bstOpen');
			if (fn) {
				fn();
			}
		}
	}

	api.bind('ready', function () {
		api.section.each(function (section) {
			if (section.expanded()) {
				buildGroups(section);
			}
			section.expanded.bind(function (isOpen) {
				if (isOpen) {
					buildGroups(section);
				}
			});
		});
		api.control.each(function (control) {
			var focus = control.focus;
			control.focus = function () {
				var section = api.section(control.section());
				if (section) {
					buildGroups(section);
				}
				revealControl(control);
				return focus.apply(control, arguments);
			};
		});

		/* Search box: finds any option by its label, description or section name. */
		var $search = $('<div class="bst-csearch"><span class="dashicons dashicons-search" aria-hidden="true"></span><input type="search" class="bst-csearch__input" placeholder="Search settings… (logo, menu, color, zalo…)" aria-label="Search settings"><ul class="bst-csearch__results" hidden></ul></div>');
		$('#customize-theme-controls').before($search);
		var $input = $search.find('input');
		var $results = $search.find('.bst-csearch__results');
		var norm = function (str) {
			return String(str || '').toLowerCase().normalize('NFD').replace(/[̀-ͯ]/g, '').replace(/đ/g, 'd');
		};
		var index = null;
		var buildIndex = function () {
			index = [];
			api.control.each(function (control) {
				var p = control.params || {};
				var section = api.section(control.section());
				if (!section || p.type === 'bst-heading' || (control.id.indexOf('sidebars_widgets') === 0) || control.id.indexOf('widget_') === 0 || control.id.indexOf('nav_menu_item') === 0) {
					return;
				}
				var label = $('<div />').html(p.label || '').text();
				if (!label) {
					return;
				}
				var panel = section.panel && section.panel() ? api.panel(section.panel()) : null;
				var where = (panel ? $('<div />').html(panel.params.title).text() + ' › ' : '') + $('<div />').html(section.params.title).text();
				index.push({ id: control.id, label: label, where: where, text: norm(label + ' ' + $('<div />').html(p.description || '').text() + ' ' + where + ' ' + control.id.replace(/_/g, ' ')) });
			});
		};
		$input.on('input focus', function () {
			if (!index) {
				buildIndex();
			}
			var q = norm($input.val()).trim();
			if (q.length < 2) {
				$results.prop('hidden', true).empty();
				return;
			}
			var words = q.split(/\s+/);
			var hits = index.filter(function (it) {
				return words.every(function (w) { return it.text.indexOf(w) !== -1; });
			}).slice(0, 30);
			$results.empty().prop('hidden', false);
			if (!hits.length) {
				$results.append('<li class="is-empty">No matching settings</li>');
				return;
			}
			hits.forEach(function (it) {
				$('<li tabindex="0" />').attr('data-id', it.id).append($('<strong />').text(it.label)).append($('<small />').text(it.where)).appendTo($results);
			});
		});
		$results.on('click keydown', 'li[data-id]', function (e) {
			if (e.type === 'keydown' && e.key !== 'Enter') {
				return;
			}
			var control = api.control($(this).data('id'));
			$results.prop('hidden', true);
			if (control) {
				control.focus();
				control.container.addClass('bst-csearch-hit');
				setTimeout(function () { control.container.removeClass('bst-csearch-hit'); }, 1800);
			}
		});
		$(document).on('click', function (e) {
			if (!$(e.target).closest('.bst-csearch').length) {
				$results.prop('hidden', true);
			}
		});
	});

	/* Colors: the palette library and the manual color pickers take turns. */
	api.bind('ready', function () {
		api('color_use_palette', function (setting) {
			var apply = function (usePalette) {
				var section = api.section('bst_colors');
				if (!section) {
					return;
				}
				section.controls().forEach(function (control) {
					if (control.id === 'color_use_palette') {
						return;
					}
					control.container.toggle(control.id === 'color_palette_presets' ? !!usePalette : !usePalette);
				});
			};
			apply(setting.get());
			setting.bind(apply);
			api.section('bst_colors', function (section) {
				section.expanded.bind(function () { apply(setting.get()); });
			});
		});
	});

	/* Palette library: search box + style filter (100 palettes). */
	api.bind('ready', function () {
		api.control('color_palette_presets', function (control) {
			var $wrap = control.container.find('[data-bst-layout-presets]');
			var $buttons = $wrap.children('button');
			var groups = [['', 'All'], ['Vivid', 'Vivid'], ['Pastel', 'Pastel'], ['Deep', 'Deep'], ['Dark', 'Dark']];
			var $tools = $('<div class="bst-pal-tools"><input type="search" class="bst-pal-search" placeholder="Search: blue, spa, food, gold…"><div class="bst-pal-chips"></div><p class="bst-pal-count"></p></div>');
			groups.forEach(function (g, i) {
				$('<button type="button" class="bst-pal-chip" />').toggleClass('is-on', i === 0).attr('data-g', g[0]).text(g[1]).appendTo($tools.find('.bst-pal-chips'));
			});
			$wrap.before($tools);
			var group = '';
			var filter = function () {
				var q = String($tools.find('input').val() || '').toLowerCase().trim();
				var shown = 0;
				$buttons.each(function () {
					var t = $(this).text().toLowerCase();
					var desc = $(this).find('span').text();
					var ok = (!q || q.split(/\s+/).every(function (w) { return t.indexOf(w) !== -1; })) && (!group || desc.indexOf(group + ' ·') === 0 || group === 'Vivid' && desc.indexOf(' · ') === -1);
					$(this).toggle(ok);
					shown += ok ? 1 : 0;
				});
				$tools.find('.bst-pal-count').text(shown + ' / ' + $buttons.length);
			};
			$tools.on('input', 'input', filter);
			$tools.on('click', '.bst-pal-chip', function () {
				group = $(this).attr('data-g');
				$tools.find('.bst-pal-chip').removeClass('is-on');
				$(this).addClass('is-on');
				filter();
			});
			filter();
		});
	});
})(jQuery, wp.customize);
