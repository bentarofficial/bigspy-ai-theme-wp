/* BIGSPY AI Theme WP – text and highlight color for selected words in BIGSPY rich text (Heading…). */
(function (wp) {
	'use strict';
	if (!wp || !wp.richText || !wp.blockEditor) {
		return;
	}
	var el = wp.element.createElement;
	var __ = wp.i18n.__;
	var T = 'bigspy-ai-theme-wp';
	var rt = wp.richText;
	var be = wp.blockEditor;
	var c = wp.components;
	var NAME = 'bst/text-color';

	var parse = function (style) {
		var out = {};
		String(style || '').split(';').forEach(function (d) {
			var i = d.indexOf(':');
			if (i > 0) {
				out[d.slice(0, i).trim()] = d.slice(i + 1).trim();
			}
		});
		return out;
	};
	var build = function (o) {
		return Object.keys(o).filter(function (k) { return o[k]; }).map(function (k) { return k + ':' + o[k]; }).join(';');
	};
	// HEX (with or without #), rgb(), hsl() or a color name; '' when not a color.
	var norm = function (v) {
		v = String(v || '').trim();
		if (/^[0-9a-f]{3,8}$/i.test(v)) {
			v = '#' + v;
		}
		return v && window.CSS && CSS.supports('color', v) ? v : '';
	};

	function Picker(props) {
		var code = wp.element.useState(props.current || '');
		var bad = wp.element.useState(false);
		var colors = (wp.data.select('core/block-editor').getSettings() || {}).colors || [];
		var apply = function () {
			var v = norm(code[0]);
			if (!v) {
				bad[1](true);
				return;
			}
			props.onPick(v);
		};
		return el('div', { className: 'bst-tc-pop' },
			colors.length ? el(c.ColorPalette, { colors: colors, value: props.current, disableCustomColors: true, clearable: false, onChange: function (v) { if (v) { props.onPick(v); } } }) : null,
			el('div', { className: 'bst-tc-pop__code' },
				el('input', {
					type: 'text', value: code[0], placeholder: '#e53935', 'aria-label': __('Color code', T), className: bad[0] ? 'is-invalid' : '',
					onChange: function (e) { code[1](e.target.value); bad[1](false); },
					onKeyDown: function (e) { if ('Enter' === e.key) { e.preventDefault(); apply(); } }
				}),
				el(c.Button, { variant: 'primary', size: 'small', onClick: apply }, __('Apply', T))
			),
			props.current ? el(c.Button, { variant: 'link', isDestructive: true, onClick: function () { props.onPick(''); } }, __('Remove color', T)) : null
		);
	}

	rt.registerFormatType(NAME, {
		title: __('Text color', T),
		tagName: 'span',
		className: 'bs-tc',
		attributes: { style: 'style' },
		edit: function (props) {
			var block = wp.data.select('core/block-editor').getSelectedBlock();
			if (!block || 0 !== block.name.indexOf('bst/')) {
				return null;
			}
			var active = rt.getActiveFormat(props.value, NAME);
			var cur = parse(active && active.attributes && active.attributes.style);
			var set = function (prop, v) {
				var o = Object.assign({}, cur);
				o[prop] = v;
				var style = build(o);
				props.onChange(style ? rt.applyFormat(props.value, { type: NAME, attributes: { style: style } }) : rt.removeFormat(props.value, NAME));
			};
			var tool = function (prop, icon, label) {
				return el(c.Dropdown, {
					key: prop,
					popoverProps: { placement: 'bottom-start' },
					renderToggle: function (t) {
						return el(c.ToolbarButton, { icon: icon, label: label, isPressed: !!cur[prop], onClick: t.onToggle, 'aria-expanded': t.isOpen });
					},
					renderContent: function (r) {
						return el(Picker, { current: cur[prop] || '', onPick: function (v) { set(prop, v); r.onClose(); } });
					}
				});
			};
			return el(be.BlockControls, { group: 'inline' },
				tool('color', 'editor-textcolor', __('Text color', T)),
				tool('background-color', 'admin-customizer', __('Highlight color', T))
			);
		}
	});
})(window.wp);
