/**
 * Editor for the Search block (bst/search) and Listing search (bigspy/search), G-134-a.
 * The preview is the server render, so editor and front stay the same.
 */
(function (wp) {
	'use strict';
	var B = window.bstEditor;
	var el = wp.element.createElement;
	var Fragment = wp.element.Fragment;
	var __ = wp.i18n.__;
	var c = wp.components;
	var be = wp.blockEditor;
	var T = 'bigspy-ai-theme-wp';

	function opts(list) {
		var o = {};
		list.forEach(function (x) { o[x[0]] = x[1]; });
		return o;
	}
	var mag = '<circle cx="8" cy="10" r="3" fill="none" stroke="currentColor" stroke-width="1.5"/><path d="m10.5 12.5 2 2" stroke="currentColor" stroke-width="1.5"/>';
	var btn = function (x, r, fill) { return '<rect x="' + x + '" y="5" width="12" height="10" rx="' + r + '" fill="' + (fill || 'currentColor') + '"/>'; };
	var STYLES = [
		{ value: '', label: __('Default', T), svg: '<rect x="2" y="3" width="52" height="14" rx="4" fill="none" stroke="currentColor"/>' + btn(40, 3) },
		{ value: 'pill', label: __('Pill', T), svg: '<rect x="2" y="3" width="52" height="14" rx="7" fill="none" stroke="currentColor"/>' + btn(40, 5) },
		{ value: 'boxed', label: __('Boxed', T), svg: '<rect x="2" y="3" width="52" height="14" fill="none" stroke="currentColor" stroke-width="2"/><rect x="41" y="3" width="13" height="14" fill="currentColor"/>' },
		{ value: 'underline', label: __('Underline', T), svg: '<path d="M2 16H54" stroke="currentColor" stroke-width="2"/>' + mag.replace('cx="8"', 'cx="48"').replace('m10.5', 'm50.5') },
		{ value: 'split', label: __('Split', T), svg: '<rect x="2" y="3" width="36" height="14" rx="4" fill="none" stroke="currentColor"/>' + btn(42, 3) },
		{ value: 'icon', label: __('Icon button', T), svg: '<rect x="2" y="3" width="52" height="14" rx="7" fill="none" stroke="currentColor"/><circle cx="47" cy="10" r="5" fill="currentColor"/>' },
		{ value: 'hero', label: __('Hero', T), svg: '<rect x="2" y="2" width="52" height="16" rx="8" fill="currentColor" fill-opacity=".12"/>' + btn(38, 5) },
		{ value: 'overlay', label: __('Full-screen overlay', T), svg: '<rect x="2" y="2" width="52" height="16" rx="2" fill="currentColor" fill-opacity=".7"/><rect x="10" y="7" width="36" height="6" rx="3" fill="#fff"/>' }
	];

	function edit(name, site) {
		return function (props) {
			var a = props.attributes;
			var bind = B.binder(props);
			var color = function (key, label) {
				return { label: label, value: a[key], onChange: function (v) { var o = {}; o[key] = v || ''; props.setAttributes(o); } };
			};
			var textarea = function (key, label, help) {
				return el(c.TextareaControl, { label: label, help: help, value: a[key] || '', rows: 3, onChange: function (v) { var o = {}; o[key] = v; props.setAttributes(o); }, __nextHasNoMarginBottom: true });
			};
			return el(Fragment, null,
				el(be.InspectorControls, null,
					el(c.PanelBody, { title: __('Search box', T) },
						el(B.Text, Object.assign({ label: __('Placeholder', T) }, bind('placeholder'))),
						textarea('placeholders', __('Rotating placeholders (one per line)', T), __('Two or more lines: the hint changes every 3 seconds.', T)),
						el(B.Toggle, Object.assign({ label: __('Show the button', T) }, bind('showButton', true))),
						a.showButton !== false ? el(B.Text, Object.assign({ label: __('Button text', T) }, bind('buttonText'))) : null,
						el(B.Text, Object.assign({ label: __('Accessible label (aria-label)', T), help: __('Read by screen readers. Empty = “Search”.', T) }, bind('ariaLabel')))
					),
					el(c.PanelBody, { title: __('What to search', T), initialOpen: false },
						site ? el(B.Text, Object.assign({ label: __('Content types (comma separated, empty = all)', T), help: __('For example: post, page, product.', T) }, bind('postTypes'))) : null,
						el(c.TextControl, { type: 'number', label: __('Only in category ID (0 = all)', T), value: a.category || 0, onChange: function (v) { props.setAttributes({ category: parseInt(v, 10) || 0 }); }, __nextHasNoMarginBottom: true }),
						el(B.Toggle, Object.assign({ label: __('Category drop-down in the box', T) }, bind('showCategorySelect', true)))
					),
					el(c.PanelBody, { title: __('Instant suggestions', T), initialOpen: false },
						el(B.Toggle, Object.assign({ label: __('Show results while typing', T), help: __('Arrow keys move through results, Escape closes them.', T) }, bind('live', true))),
						a.live ? el(B.Range, Object.assign({ label: __('Number of results', T), min: 1, max: 12 }, bind('liveCount'))) : null,
						a.live ? el(B.Range, Object.assign({ label: __('Start after this many letters', T), min: 1, max: 5 }, bind('minChars'))) : null,
						a.live ? el(B.Toggle, Object.assign({ label: __('Show images', T) }, bind('liveImage', true))) : null,
						a.live ? el(B.Toggle, Object.assign({ label: __('Show prices', T) }, bind('livePrice', true))) : null,
						a.live ? el(B.Toggle, Object.assign({ label: __('Show the category', T) }, bind('liveCategory', true))) : null
					),
					el(c.PanelBody, { title: __('Extras', T), initialOpen: false },
						textarea('chips', __('Popular searches (one per line)', T), __('Shown as clickable chips under the box.', T)),
						a.chips ? el(B.Text, Object.assign({ label: __('Text before the chips', T) }, bind('chipsLabel'))) : null,
						el(B.Toggle, Object.assign({ label: __('Press “/” to jump to the search box', T) }, bind('shortcut', true)))
					)
				),
				el(B.StyleTab, null,
					el(c.PanelBody, { title: __('Search style', T) },
						el(B.StylePicker, { label: __('Style', T), value: a.style || '', options: STYLES, columns: 2, onChange: function (v) { props.setAttributes({ style: v }); } }),
						el(B.Select, Object.assign({ label: __('Magnifier icon in the box', T), options: opts([['', __('None', T)], ['left', __('Left', T)], ['right', __('Right', T)]]) }, bind('icon'))),
						el(B.Select, Object.assign({ label: __('Button shows', T), options: opts([['', __('Text', T)], ['icon', __('Icon', T)], ['both', __('Icon and text', T)]]) }, bind('buttonMode'))),
						el(B.Select, Object.assign({ label: __('Button position', T), options: opts([['', __('Inside the box', T)], ['outside', __('Next to the box', T)], ['below-mobile', __('Below the box on phones', T)]]) }, bind('buttonPosition')))
					),
					el(c.PanelBody, { title: __('Size and position', T), initialOpen: false },
						el(B.Resp, { props: props, name: 'maxWidth', label: __('Maximum width (px)', T), min: 0, max: 1400 }),
						el(B.Select, Object.assign({ label: __('Align', T), options: opts([['left', __('Left', T)], ['center', __('Center', T)], ['right', __('Right', T)]]) }, bind('alignment'))),
						el(B.Select, Object.assign({ label: __('Align on phones', T), options: opts([['', __('Same as desktop', T)], ['left', __('Left', T)], ['center', __('Center', T)], ['right', __('Right', T)], ['full', __('Full width', T)]]) }, bind('alignmentMobile'))),
						el(B.Range, Object.assign({ label: __('Height (px, 0 = default)', T), min: 0, max: 80 }, bind('height'))),
						el(B.Range, Object.assign({ label: __('Height on phones (px, 0 = same)', T), min: 0, max: 80 }, bind('heightMobile'))),
						el(B.Range, Object.assign({ label: __('Text size (px, 0 = default)', T), min: 0, max: 40 }, bind('fontSize'))),
						el(B.Range, Object.assign({ label: __('Inner side space (px, -1 = default)', T), min: -1, max: 60 }, bind('padX')))
					),
					el(c.PanelBody, { title: __('Border and shadow', T), initialOpen: false },
						el(B.Range, Object.assign({ label: __('Corner radius (px)', T), min: 0, max: 40 }, bind('radius'))),
						el(B.Range, Object.assign({ label: __('Border width (px, -1 = default)', T), min: -1, max: 10 }, bind('borderWidth'))),
						el(B.Range, Object.assign({ label: __('Button corner radius (px, -1 = default)', T), min: -1, max: 60 }, bind('buttonRadius'))),
						el(B.Range, Object.assign({ label: __('Shadow', T), min: 0, max: 5 }, bind('shadow'))),
						el(B.Toggle, Object.assign({ label: __('Glow when focused', T) }, bind('shadowFocus', true)))
					),
					el(B.ColorPanel, { title: __('Box colors', T), settings: [
						color('inputBg', __('Background', T)), color('inputColor', __('Text', T)), color('placeholderColor', __('Placeholder', T)),
						color('borderColor', __('Border', T)), color('focusColor', __('Border when focused', T))
					] }),
					el(B.ColorPanel, { title: __('Button colors', T), settings: [
						color('buttonColor', __('Button', T)), color('buttonTextColor', __('Button text', T)),
						color('buttonHoverColor', __('Button on hover', T)), color('buttonHoverTextColor', __('Button text on hover', T))
					] })
				),
				el('div', be.useBlockProps(), el(wp.serverSideRender, { block: name, attributes: a, httpMethod: 'POST' }))
			);
		};
	}

	wp.blocks.registerBlockType('bst/search', { edit: edit('bst/search', true), save: function () { return null; } });
	wp.blocks.registerBlockType('bigspy/search', { edit: edit('bigspy/search', false), save: function () { return null; } });
})(window.wp);
