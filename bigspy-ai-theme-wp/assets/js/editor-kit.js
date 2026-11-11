/* BIGSPY AI Theme WP – per-part editor kit (G-129): responsive values, part typography,
 * state colors, part box, visual style picker, link control and the Style tab wrapper.
 * Extends window.bstEditor; mirrors bst_part_vars() in inc/blocks.php. */
(function (wp) {
	'use strict';

	var B = window.bstEditor || {};
	var el = wp.element.createElement;
	var Fragment = wp.element.Fragment;
	var __ = wp.i18n.__;
	var be = wp.blockEditor;
	var c = wp.components;
	var useSelect = wp.data.useSelect;
	var T = 'bigspy-ai-theme-wp';

	var DEVICES = [
		{ id: 'Desktop', suffix: '', icon: 'desktop', label: __('Desktop', T) },
		{ id: 'Tablet', suffix: 'Tablet', icon: 'tablet', label: __('Tablet', T) },
		{ id: 'Mobile', suffix: 'Mobile', icon: 'smartphone', label: __('Mobile', T) }
	];

	function getDevice() {
		var s = wp.data.select;
		var ed = s('core/editor');
		if (ed && ed.getDeviceType) {
			return ed.getDeviceType() || 'Desktop';
		}
		var ep = s('core/edit-post');
		return (ep && ep.__experimentalGetPreviewDeviceType && ep.__experimentalGetPreviewDeviceType()) || 'Desktop';
	}

	function setDevice(id) {
		var ed = wp.data.dispatch('core/editor');
		if (ed && ed.setDeviceType) {
			ed.setDeviceType(id);
			return;
		}
		var ep = wp.data.dispatch('core/edit-post');
		if (ep && ep.__experimentalSetPreviewDeviceType) {
			ep.__experimentalSetPreviewDeviceType(id);
		}
	}

	function set(props, key, v) {
		var o = {};
		o[key] = v;
		props.setAttributes(o);
	}

	function isSet(v) {
		return v !== '' && v !== null && typeof v !== 'undefined';
	}

	/**
	 * Resp: one value per device stored in key, keyTablet, keyMobile.
	 * props: { props, name, label, control (default Range), min, max, step, ...extra }.
	 * Tablet/mobile fall back to the larger device when empty.
	 */
	function Resp(o) {
		var device = useSelect(getDevice, []);
		var cur = DEVICES.filter(function (d) { return d.id === device; })[0] || DEVICES[0];
		var a = o.props.attributes;
		var key = o.name + cur.suffix;
		var value = a[key];
		var inherited = !isSet(value) && cur.suffix ? (isSet(a[o.name + 'Tablet']) && cur.suffix === 'Mobile' ? a[o.name + 'Tablet'] : a[o.name]) : undefined;
		var Control = o.control || B.Range;
		var extra = B.assign(o, {
			label: null,
			value: isSet(value) ? value : inherited,
			onChange: function (v) { set(o.props, key, v); }
		});
		delete extra.props; delete extra.name; delete extra.control;
		return el('div', { className: 'bst-resp' },
			el('div', { className: 'bst-resp__head', style: { display: 'flex', alignItems: 'center', justifyContent: 'space-between', gap: '4px' } },
				el('span', { className: 'components-base-control__label', style: { textTransform: 'uppercase', fontSize: '11px', fontWeight: 500 } }, o.label),
				el('span', { className: 'bst-resp__devices' },
					DEVICES.map(function (d) {
						return el(c.Button, { key: d.id, icon: d.icon, size: 'small', label: d.label, showTooltip: true, isPressed: d.id === cur.id, onClick: function () { setDevice(d.id); } });
					}),
					cur.suffix && isSet(value) ? el(c.Button, { icon: 'image-rotate', size: 'small', label: __('Use the larger screen value', T), showTooltip: true, onClick: function () { set(o.props, key, undefined); } }) : null
				)
			),
			el(Control, extra)
		);
	}

	var WEIGHTS = { '': __('Default', T), '300': '300', '400': '400', '500': '500', '600': '600', '700': '700', '800': '800', '900': '900' };
	var TRANSFORMS = { '': __('Default', T), none: __('None', T), uppercase: 'ABC', lowercase: 'abc', capitalize: 'Abc' };

	/** PartTypo: typography group for attributes prefixed with o.prefix. */
	function PartTypo(o) {
		var p = o.props;
		var a = p.attributes;
		var k = function (s) { return o.prefix + s; };
		var bind = function (s) { return { value: a[k(s)], onChange: function (v) { set(p, k(s), v); } }; };
		return el(c.PanelBody, { title: o.title || __('Typography', T), initialOpen: !!o.initialOpen },
			el(Resp, { props: p, name: k('Size'), label: __('Font size (px)', T), min: 8, max: o.maxSize || 120, reset: 0 }),
			el(B.Select, B.assign({ label: __('Weight', T), options: WEIGHTS }, bind('Weight'))),
			el(B.Range, B.assign({ label: __('Line height', T), min: 0, max: 3, step: 0.05, reset: 0 }, bind('LineHeight'))),
			el(B.Range, B.assign({ label: __('Letter spacing (px)', T), min: -5, max: 20, step: 0.5, reset: 0 }, bind('LetterSpacing'))),
			el(B.Select, B.assign({ label: __('Transform', T), options: TRANSFORMS }, bind('Transform'))),
			el(be.PanelColorSettings, { title: __('Color', T), initialOpen: false, colorSettings: [
				{ label: __('Color', T), value: a[k('Color')], onChange: function (v) { set(p, k('Color'), v || ''); } }
			].concat(o.hover === false ? [] : [{ label: __('Hover color', T), value: a[k('ColorHover')], onChange: function (v) { set(p, k('ColorHover'), v || ''); } }]) })
		);
	}

	/**
	 * ColorStates: tabs Normal / Hover / Active, each a color panel.
	 * o.fields: [{ key: 'navBg', label }] – state keys get Hover / Active appended.
	 * o.states: subset of ['', 'Hover', 'Active'] (default all three).
	 */
	function ColorStates(o) {
		var p = o.props;
		var names = { '': __('Normal', T), Hover: __('Hover', T), Active: __('Active', T) };
		var states = o.states || ['', 'Hover', 'Active'];
		return el(c.TabPanel, {
			className: 'bst-color-states',
			tabs: states.map(function (s) { return { name: s || 'normal', title: names[s] }; })
		}, function (tab) {
			var s = tab.name === 'normal' ? '' : tab.name;
			return el(be.PanelColorSettings, {
				title: o.title || __('Colors', T),
				colorSettings: o.fields.map(function (f) {
					var key = f.key + s;
					return { label: f.label, value: p.attributes[key], onChange: function (v) { set(p, key, v || ''); } };
				})
			});
		});
	}

	/** PartBox: padding, radius, border width + color, shadow 0–5 for prefix. */
	function PartBox(o) {
		var p = o.props;
		var a = p.attributes;
		var k = function (s) { return o.prefix + s; };
		var bind = function (s) { return { value: a[k(s)], onChange: function (v) { set(p, k(s), v); } }; };
		return el(c.PanelBody, { title: o.title || __('Box', T), initialOpen: !!o.initialOpen },
			el(Resp, { props: p, name: k('Padding'), label: __('Padding (px)', T), min: 0, max: 100, reset: 0 }),
			el(B.Range, B.assign({ label: __('Corner radius (px)', T), min: 0, max: 100, reset: 0 }, bind('Radius'))),
			el(B.Range, B.assign({ label: __('Border width (px)', T), min: 0, max: 20, reset: 0 }, bind('BorderWidth'))),
			a[k('BorderWidth')] ? el(be.PanelColorSettings, { title: __('Border color', T), initialOpen: false, colorSettings: [{ label: __('Border color', T), value: a[k('BorderColor')], onChange: function (v) { set(p, k('BorderColor'), v || ''); } }] }) : null,
			el(B.Range, B.assign({ label: __('Shadow', T), min: 0, max: 5, reset: 0 }, bind('Shadow')))
		);
	}

	/** StylePicker: grid of buttons with a small SVG and a label. o.options: [{ value, label, svg }] */
	function StylePicker(o) {
		return el(c.BaseControl, { label: o.label, __nextHasNoMarginBottom: true },
			el('div', { className: 'bst-style-picker', role: 'radiogroup', 'aria-label': o.label, style: { display: 'grid', gridTemplateColumns: 'repeat(' + (o.columns || 3) + ', 1fr)', gap: '6px' } },
				o.options.map(function (opt) {
					var on = o.value === opt.value;
					return el(c.Button, {
						key: opt.value,
						role: 'radio',
						'aria-checked': on,
						className: 'bst-style-picker__item' + (on ? ' is-selected' : ''),
						isPressed: on,
						onClick: function () { o.onChange(opt.value); },
						style: { height: 'auto', flexDirection: 'column', gap: '4px', padding: '6px 4px', justifyContent: 'center' }
					},
					opt.svg ? el('svg', { width: 56, height: 20, viewBox: '0 0 56 20', 'aria-hidden': true, dangerouslySetInnerHTML: { __html: opt.svg } }) : null,
					el('span', { style: { fontSize: '11px', lineHeight: 1.2 } }, opt.label));
				})
			)
		);
	}

	/** LinkControl: URL, new tab, rel, aria-label for prefix (url key = prefix + 'Url' or o.keys). */
	function LinkControl(o) {
		var p = o.props;
		var a = p.attributes;
		var keys = B.assign({ url: o.prefix + 'Url', target: o.prefix + 'NewTab', rel: o.prefix + 'Rel', label: o.prefix + 'AriaLabel' }, o.keys);
		return el(Fragment, null,
			el(B.Text, { label: o.label || __('Link URL', T), type: 'url', value: a[keys.url], onChange: function (v) { set(p, keys.url, v); } }),
			a[keys.url] ? el(B.Toggle, { label: __('Open in new tab', T), checked: a[keys.target], onChange: function (v) { set(p, keys.target, v); } }) : null,
			a[keys.url] ? el(B.Text, { label: __('Rel (e.g. nofollow sponsored)', T), value: a[keys.rel], onChange: function (v) { set(p, keys.rel, v); } }) : null,
			a[keys.url] ? el(B.Text, { label: __('Accessible label (aria-label)', T), value: a[keys.label], onChange: function (v) { set(p, keys.label, v); } }) : null
		);
	}

	/** StyleTab: InspectorControls in the Styles tab. */
	function StyleTab(o) {
		return el(be.InspectorControls, { group: 'styles' }, o.children);
	}

	/* ---------------------------------------------------------------- partVars (mirror of bst_part_vars) */

	var SHADOWS = ['', '0 1px 2px rgba(0,0,0,.08)', '0 2px 6px rgba(0,0,0,.1)', '0 6px 16px rgba(0,0,0,.12)', '0 12px 28px rgba(0,0,0,.16)', '0 20px 48px rgba(0,0,0,.2)'];
	// attribute suffix => [css suffix, unit, responsive]
	var MAP = {
		Size: ['fs', 'px', true], Weight: ['fw', '', false], LineHeight: ['lh', '', false], LetterSpacing: ['ls', 'px', false], Transform: ['tt', '', false],
		Color: ['c', '', false], ColorHover: ['ch', '', false], ColorActive: ['ca', '', false],
		Bg: ['bg', '', false], BgHover: ['bgh', '', false], BgActive: ['bga', '', false],
		BorderColor: ['bc', '', false], BorderColorHover: ['bch', '', false], BorderColorActive: ['bca', '', false],
		Padding: ['pad', 'px', true], Radius: ['r', 'px', false], BorderWidth: ['bw', 'px', false], Shadow: ['sh', '', false],
		Width: ['w', 'px', true], Gap: ['gap', 'px', true]
	};

	function partVars(a, block, part, keys) {
		var out = {};
		(keys || Object.keys(MAP)).forEach(function (s) {
			var m = MAP[s];
			if (!m) {
				return;
			}
			var base = '--bs-' + block + '-' + part + '-' + m[0];
			[['', ''], ['Tablet', '-t'], ['Mobile', '-m']].forEach(function (d) {
				if (d[0] && !m[2]) {
					return;
				}
				var v = a[part + s + d[0]];
				// Empty, false and 0 mean "not set": the CSS fallback applies.
				if (!isSet(v) || v === false || v === 0 || v === '0') {
					return;
				}
				out[base + d[1]] = s === 'Shadow' ? SHADOWS[Math.max(0, Math.min(5, v | 0))] : v + m[1];
			});
		});
		return out;
	}

	window.bstEditor = B.assign(B, {
		Resp: Resp,
		PartTypo: PartTypo,
		ColorStates: ColorStates,
		PartBox: PartBox,
		StylePicker: StylePicker,
		LinkControl: LinkControl,
		StyleTab: StyleTab,
		partVars: partVars,
		getDevice: getDevice
	});
})(window.wp);
