/* BIGSPY Theme – shared editor components + responsive/animation panel for every block. */
(function (wp) {
	'use strict';

	var el = wp.element.createElement;
	var Fragment = wp.element.Fragment;
	var __ = wp.i18n.__;
	var be = wp.blockEditor;
	var c = wp.components;
	var data = window.bstEditor || {};

	/* ---------------------------------------------------------------- Components */

	function Icon(props) {
		var path = (data.icons || {})[props.name];
		if (!path) {
			return null;
		}
		return el('svg', {
			className: 'bs-icon',
			width: props.size || 24,
			height: props.size || 24,
			viewBox: '0 0 24 24',
			'aria-hidden': 'true',
			dangerouslySetInnerHTML: { __html: path }
		});
	}

	function IconPicker(props) {
		var names = Object.keys(data.icons || {});
		return el(c.BaseControl, { label: props.label || __('Icon', 'bigspy-theme'), __nextHasNoMarginBottom: true },
			el('div', { className: 'bst-icon-picker' },
				names.map(function (name) {
					return el(c.Button, {
						key: name,
						className: 'bst-icon-picker__item' + (props.value === name ? ' is-selected' : ''),
						label: name,
						showTooltip: true,
						onClick: function () {
							props.onChange(props.value === name ? '' : name);
						}
					}, el(Icon, { name: name, size: 20 }));
				})
			)
		);
	}

	function MediaPicker(props) {
		return el(c.BaseControl, { label: props.label || __('Image', 'bigspy-theme'), __nextHasNoMarginBottom: true },
			el(be.MediaUploadCheck, null,
				el(be.MediaUpload, {
					allowedTypes: ['image'],
					value: props.id || 0,
					onSelect: function (media) {
						props.onChange({ id: media.id || 0, url: media.url || '' });
					},
					render: function (o) {
						return el('div', { className: 'bst-media-picker' },
							props.url ? el('img', { src: props.url, alt: '', onClick: o.open }) : null,
							el('div', { className: 'bst-media-picker__actions' },
								el(c.Button, { variant: 'secondary', onClick: o.open }, props.url ? __('Replace', 'bigspy-theme') : __('Select image', 'bigspy-theme')),
								props.url ? el(c.Button, {
									variant: 'link',
									isDestructive: true,
									onClick: function () {
										props.onChange({ id: 0, url: '' });
									}
								}, __('Remove', 'bigspy-theme')) : null
							)
						);
					}
				})
			)
		);
	}

	function ColorPanel(props) {
		return el(be.PanelColorSettings, {
			title: props.title || __('Colors', 'bigspy-theme'),
			initialOpen: !!props.initialOpen,
			colorSettings: props.settings
		});
	}

	function opts(map) {
		return Object.keys(map).map(function (value) {
			return { value: value, label: map[value] };
		});
	}

	function Select(props) {
		return el(c.SelectControl, {
			label: props.label,
			value: props.value,
			options: Array.isArray(props.options) ? props.options : opts(props.options),
			onChange: props.onChange,
			help: props.help,
			__nextHasNoMarginBottom: true,
			__next40pxDefaultSize: true
		});
	}

	function Range(props) {
		return el(c.RangeControl, {
			label: props.label,
			value: props.value,
			min: props.min || 0,
			max: props.max || 100,
			step: props.step || 1,
			help: props.help,
			allowReset: typeof props.reset !== 'undefined',
			resetFallbackValue: props.reset,
			onChange: function (v) {
				props.onChange(typeof v === 'number' ? v : (props.reset || 0));
			},
			__nextHasNoMarginBottom: true,
			__next40pxDefaultSize: true
		});
	}

	function Toggle(props) {
		return el(c.ToggleControl, {
			label: props.label,
			checked: !!props.checked,
			help: props.help,
			onChange: props.onChange,
			__nextHasNoMarginBottom: true
		});
	}

	function Text(props) {
		return el(c.TextControl, {
			label: props.label,
			value: props.value || '',
			type: props.type || 'text',
			help: props.help,
			placeholder: props.placeholder,
			onChange: props.onChange,
			__nextHasNoMarginBottom: true,
			__next40pxDefaultSize: true
		});
	}

	/**
	 * Shorthand to bind a control to an attribute: bind(props)('key') → {value, onChange}.
	 */
	function binder(props) {
		return function (key, isToggle) {
			var out = {
				onChange: function (v) {
					var attrs = {};
					attrs[key] = v;
					props.setAttributes(attrs);
				}
			};
			out[isToggle ? 'checked' : 'value'] = props.attributes[key];
			return out;
		};
	}

	function assign() {
		var out = {};
		for (var i = 0; i < arguments.length; i++) {
			var src = arguments[i] || {};
			for (var k in src) {
				if (Object.prototype.hasOwnProperty.call(src, k)) {
					out[k] = src[k];
				}
			}
		}
		return out;
	}

	window.bstEditor = assign(data, {
		el: el,
		Fragment: Fragment,
		Icon: Icon,
		IconPicker: IconPicker,
		MediaPicker: MediaPicker,
		ColorPanel: ColorPanel,
		Select: Select,
		Range: Range,
		Toggle: Toggle,
		Text: Text,
		opts: opts,
		binder: binder,
		assign: assign
	});

	/* ---------------------------------------------------------------- Global responsive + animation panel */

	var EXTRA_ATTRS = {
		bsHideDesktop: { type: 'boolean', default: false },
		bsHideTablet: { type: 'boolean', default: false },
		bsHideMobile: { type: 'boolean', default: false },
		bsAnimation: { type: 'string', default: '' },
		bsAnimationDelay: { type: 'number', default: 0 }
	};

	function skip(name) {
		return !name || name === 'core/freeform' || name === 'core/missing' || name.indexOf('core/post-') === 0 && name !== 'core/post-content';
	}

	wp.hooks.addFilter('blocks.registerBlockType', 'bst/extra-attributes', function (settings, name) {
		if (skip(name)) {
			return settings;
		}
		settings.attributes = assign(settings.attributes, EXTRA_ATTRS);
		return settings;
	});

	var withPanel = wp.compose.createHigherOrderComponent(function (BlockEdit) {
		return function (props) {
			if (skip(props.name) || !props.isSelected) {
				return el(BlockEdit, props);
			}
			var a = props.attributes;
			var set = function (key) {
				return function (v) {
					var attrs = {};
					attrs[key] = v;
					props.setAttributes(attrs);
				};
			};
			var hidden = [a.bsHideDesktop && __('desktop', 'bigspy-theme'), a.bsHideTablet && __('tablet', 'bigspy-theme'), a.bsHideMobile && __('mobile', 'bigspy-theme')].filter(Boolean);

			return el(Fragment, null,
				el(BlockEdit, props),
				el(be.InspectorControls, null,
					el(c.PanelBody, {
						title: __('Visibility & animation', 'bigspy-theme') + (hidden.length || a.bsAnimation ? ' •' : ''),
						initialOpen: false,
						className: 'bst-global-panel'
					},
						el(Toggle, { label: __('Hide on desktop', 'bigspy-theme'), checked: a.bsHideDesktop, onChange: set('bsHideDesktop') }),
						el(Toggle, { label: __('Hide on tablet', 'bigspy-theme'), checked: a.bsHideTablet, onChange: set('bsHideTablet') }),
						el(Toggle, { label: __('Hide on mobile', 'bigspy-theme'), checked: a.bsHideMobile, onChange: set('bsHideMobile') }),
						el(Select, {
							label: __('Animation on scroll', 'bigspy-theme'),
							value: a.bsAnimation || '',
							options: {
								'': __('None', 'bigspy-theme'),
								'fade-in': __('Fade in', 'bigspy-theme'),
								'fade-up': __('Fade up', 'bigspy-theme'),
								'fade-down': __('Fade down', 'bigspy-theme'),
								'fade-left': __('Fade from right', 'bigspy-theme'),
								'fade-right': __('Fade from left', 'bigspy-theme'),
								'zoom-in': __('Zoom in', 'bigspy-theme'),
								'zoom-out': __('Zoom out', 'bigspy-theme'),
								'flip-up': __('Flip up', 'bigspy-theme')
							},
							onChange: set('bsAnimation')
						}),
						a.bsAnimation ? el(Range, { label: __('Delay (ms)', 'bigspy-theme'), min: 0, max: 2000, step: 50, value: a.bsAnimationDelay || 0, onChange: set('bsAnimationDelay') }) : null,
						hidden.length ? el('p', { className: 'description' }, __('Still visible in the editor. Hidden on the live site for: ', 'bigspy-theme') + hidden.join(', ')) : null
					)
				)
			);
		};
	}, 'withBstPanel');

	wp.hooks.addFilter('editor.BlockEdit', 'bst/global-panel', withPanel);

	/* Dim hidden blocks slightly in the editor so editors notice. */
	var withHiddenClass = wp.compose.createHigherOrderComponent(function (BlockListBlock) {
		return function (props) {
			var a = props.attributes || {};
			if (a.bsHideDesktop || a.bsHideTablet || a.bsHideMobile) {
				return el(BlockListBlock, assign(props, { className: ((props.className || '') + ' bst-editor-has-visibility').trim() }));
			}
			return el(BlockListBlock, props);
		};
	}, 'withBstHiddenClass');

	wp.hooks.addFilter('editor.BlockListBlock', 'bst/hidden-class', withHiddenClass);
})(window.wp);
