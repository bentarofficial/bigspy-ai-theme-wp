/* BIGSPY AI Theme WP – "Design" panel shared by every BIGSPY block (see inc/block-design.php). */
(function (wp) {
	'use strict';

	var el = wp.element.createElement;
	var Fragment = wp.element.Fragment;
	var useState = wp.element.useState;
	var __ = wp.i18n.__;
	var be = wp.blockEditor;
	var c = wp.components;
	var T = 'bigspy-ai-theme-wp';

	var SHADOWS = {
		1: '0 1px 3px -2px rgba(0,0,0,.12),0 1px 1px rgba(0,0,0,.08)',
		2: '0 3px 6px -4px rgba(0,0,0,.16),0 3px 6px rgba(0,0,0,.1)',
		3: '0 10px 20px rgba(0,0,0,.12),0 6px 6px rgba(0,0,0,.1)',
		4: '0 14px 28px rgba(0,0,0,.18),0 10px 10px rgba(0,0,0,.12)',
		5: '0 19px 38px rgba(0,0,0,.22),0 15px 12px rgba(0,0,0,.14)'
	};
	var SIDES = [['t', 'top', __('Top', T)], ['r', 'right', __('Right', T)], ['b', 'bottom', __('Bottom', T)], ['l', 'left', __('Left', T)]];
	var DEVICES = [['', __('Desktop', T)], ['T', __('Tablet', T)], ['M', __('Mobile', T)]];

	function isBst(name) {
		return typeof name === 'string' && name.indexOf('bst/') === 0;
	}

	function len(v) {
		v = (v === undefined || v === null) ? '' : String(v).trim();
		if (v === '') {
			return '';
		}
		if (/^-?[0-9.]+$/.test(v)) {
			return parseFloat(v) === 0 ? '0' : parseFloat(v) + 'px';
		}
		return /^-?[0-9.]+(px|em|rem|%|vh|vw)$|^auto$/.test(v) ? v : '';
	}

	function rules(d, s) {
		var out = [];
		[['margin', 'm'], ['padding', 'p']].forEach(function (p) {
			SIDES.forEach(function (side) {
				var v = len(d[p[1] + side[0] + s]);
				if (v !== '') {
					out.push(p[0] + '-' + side[1] + ':' + v);
				}
			});
		});
		var mw = len(d['mw' + s]);
		if (mw !== '') {
			out.push('max-width:' + mw);
		}
		var w = d['w' + s];
		if (w === 'auto') {
			out.push('width:auto;display:inline-block');
		} else if (w === 'full') {
			out.push('width:100%');
		} else if (len(w) !== '') {
			out.push('width:' + len(w));
		}
		var JC = { left: 'flex-start', center: 'center', right: 'flex-end', justify: 'space-between' };
		if (JC[d['ta' + s]]) {
			out.push('text-align:' + d['ta' + s] + ';justify-content:' + JC[d['ta' + s]] + ';--bs-ta:' + d['ta' + s]);
		}
		if (len(d['fs' + s]) !== '') {
			out.push('font-size:' + len(d['fs' + s]) + ';--bs-fs:' + len(d['fs' + s]));
		}
		if (d['lh' + s] && /^[0-9.]+(px|em|rem)?$/.test(String(d['lh' + s]))) {
			out.push('line-height:' + d['lh' + s]);
		}
		if (len(d['ls' + s]) !== '') {
			out.push('letter-spacing:' + len(d['ls' + s]));
		}
		if (['relative', 'absolute', 'fixed', 'static'].indexOf(d['pos' + s]) !== -1) {
			out.push('position:' + (d['pos' + s] === 'fixed' ? 'absolute' : d['pos' + s]));
		}
		[['top', 'ot'], ['right', 'or'], ['bottom', 'ob'], ['left', 'ol']].forEach(function (o) {
			var v = len(d[o[1] + s]);
			if (v !== '') {
				out.push(o[0] + ':' + v);
			}
		});
		if (s === '') {
			if (mw && d.mwc) {
				out.push('margin-left:auto;margin-right:auto');
			}
			var FONTS = { heading: 'var(--bs-font-heading)', body: 'var(--bs-font-body,inherit)', serif: 'Georgia,"Times New Roman",serif', mono: 'ui-monospace,Menlo,Consolas,monospace', system: 'system-ui,-apple-system,"Segoe UI",Roboto,sans-serif' };
			if (FONTS[d.ff]) {
				out.push('font-family:' + FONTS[d.ff]);
			}
			if (d.fw) {
				out.push('font-weight:' + d.fw);
			}
			if (d.tt) {
				out.push('text-transform:' + d.tt);
			}
			if (d.fst === 'italic') {
				out.push('font-style:italic');
			}
			if (d.td) {
				out.push('text-decoration:' + d.td);
			}
			var TSH = { 1: '0 1px 2px rgba(0,0,0,.25)', 2: '0 2px 10px rgba(0,0,0,.35)', 3: '0 4px 24px rgba(0,0,0,.5)' };
			if (TSH[d.tsh]) {
				out.push('text-shadow:' + TSH[d.tsh]);
			}
			if (d.bg) {
				out.push('background-color:' + d.bg);
			}
			if (d.color) {
				out.push('color:' + d.color + ';--bs-heading-color:' + d.color);
			}
			var bw = len(d.bw);
			if (bw && bw !== '0') {
				out.push('border:' + bw + ' ' + (d.bs || 'solid') + ' ' + (d.bc || 'currentColor'));
			}
			var br = len(d.br);
			if (br !== '') {
				out.push('border-radius:' + br + ';overflow:hidden');
			}
			if (SHADOWS[d.depth]) {
				out.push('box-shadow:' + SHADOWS[d.depth]);
			}
			if (d.z !== undefined && d.z !== '' && !isNaN(d.z)) {
				out.push('position:relative;z-index:' + parseInt(d.z, 10));
			}
			if (hasHover(d)) {
				out.push('transition:background-color .3s ease,color .3s ease,border-color .3s ease,border-radius .3s ease,box-shadow .3s ease,transform .3s ease');
			}
		}
		return out;
	}

	function needsSpace(d) {
		var boxed = !!d.bg || (len(d.bw) !== '' && len(d.bw) !== '0');
		return boxed && !Object.keys(d).some(function (k) { return /^p[trbl](T|M)?$/.test(k) && String(d[k]).trim() !== ''; });
	}

	function hasHover(d) {
		return ['depthHover', 'hbg', 'hcolor', 'hbc', 'hbr', 'hmove', 'hscale'].some(function (k) { return d[k] !== undefined && d[k] !== '' && d[k] !== 0; });
	}

	function hoverRules(d) {
		var out = [];
		if (SHADOWS[d.depthHover]) {
			out.push('box-shadow:' + SHADOWS[d.depthHover]);
		}
		[['hbg', 'background-color'], ['hcolor', 'color'], ['hbc', 'border-color']].forEach(function (o) {
			if (d[o[0]]) {
				out.push(o[1] + ':' + d[o[0]]);
			}
		});
		if (len(d.hbr) !== '') {
			out.push('border-radius:' + len(d.hbr));
		}
		var t = [];
		if (d.hmove && !isNaN(d.hmove)) {
			t.push('translateY(' + (-1 * parseFloat(d.hmove)) + 'px)');
		}
		if (d.hscale && !isNaN(d.hscale) && parseFloat(d.hscale) !== 100) {
			t.push('scale(' + parseFloat(d.hscale) / 100 + ')');
		}
		if (t.length) {
			out.push('transform:' + t.join(' '));
		}
		return out;
	}

	function css(d, sel) {
		if (!d || !Object.keys(d).length) {
			return '';
		}
		var out = '';
		[['', ''], ['T', '@media (max-width:849px)'], ['M', '@media (max-width:549px)']].forEach(function (dev) {
			var r = rules(d, dev[0]);
			if (r.length) {
				var block = sel + '{' + r.join(';') + '}';
				if (dev[0] === '' && needsSpace(d)) {
					block += sel + ':where(.bs-column,.bs-row,.bs-text,.bs-heading,.bs-buttons,.bs-icon-list,.bs-counter,.bs-social,.bs-title,.bs-code,.bs-share,.bs-star-rating,.bs-progress){padding:20px}';
				}
				var ta = d['ta' + dev[0]];
				var M = { left: 'margin-left:0;margin-right:auto', center: 'margin-left:auto;margin-right:auto', right: 'margin-left:auto;margin-right:0' };
				var AI = { left: 'flex-start', center: 'center', right: 'flex-end' };
				if (M[ta]) {
					block += sel + ' :is(img,.bs-image__frame,.bs-icon,.bs-icon-box__icon,.bs-stars,.bs-countdown,.bs-video-button){' + M[ta] + '}' + sel + ' :is(.bs-buttons,.bs-social,.bs-share__list,.bs-pay,.bs-icon-list){justify-content:' + AI[ta] + '}' + sel + '.bs-column,' + sel + ' > .bs-column__inner{align-items:' + AI[ta] + '}';
				}
				out += dev[1] ? dev[1] + '{' + block + '}' : block;
			}
		});
		var h = hoverRules(d);
		if (h.length) {
			out += sel + ':hover{' + h.join(';') + '}';
		}
		return out;
	}

	function isSet(d) {
		return d && Object.keys(d).some(function (k) { return d[k] !== '' && d[k] !== 0 && d[k] !== false && d[k] !== undefined; });
	}

	wp.hooks.addFilter('blocks.registerBlockType', 'bst/design-attribute', function (settings, name) {
		if (isBst(name)) {
			settings.attributes = Object.assign({}, settings.attributes, { bsDesign: { type: 'object', default: {} }, bsWidth: { type: 'string', default: '' } });
			var off = (window.bstEditor && window.bstEditor.disabledBlocks) || [];
			if (off.indexOf(name) !== -1) {
				settings.supports = Object.assign({}, settings.supports, { inserter: false });
			}
		}
		return settings;
	});

	function BoxInputs(props) {
		return el('div', { className: 'bst-design-box' },
			el('div', { className: 'bst-design-box__label' }, props.label),
			el('div', { className: 'bst-design-box__grid' },
				SIDES.map(function (side) {
					var key = props.prefix + side[0] + props.device;
					return el(c.TextControl, {
						key: key,
						label: side[2],
						value: props.d[key] === undefined ? '' : String(props.d[key]),
						placeholder: props.device ? __('inherit', T) : '–',
						onChange: function (v) { props.set(key, v); },
						__nextHasNoMarginBottom: true
					});
				})
			)
		);
	}

	function ColorField(props) {
		var colors = wp.data.useSelect(function (select) { // eslint-disable-line
			return (select(be.store).getSettings().colors) || [];
		}, []);
		return el(c.BaseControl, { label: props.label, __nextHasNoMarginBottom: true, className: 'bst-design-color' },
			el(c.ColorPalette, { colors: colors, value: props.value || undefined, enableAlpha: true, onChange: function (v) { props.onChange(v || ''); } })
		);
	}

	function DesignPanel(props) {
		var d = props.attributes.bsDesign || {};
		var editorDevice = wp.data.useSelect(function (select) {
			var ed = select('core/editor');
			var type = ed && ed.getDeviceType ? ed.getDeviceType() : 'Desktop';
			return type === 'Tablet' ? 'T' : (type === 'Mobile' ? 'M' : '');
		}, []);
		var stateDevice = useState(null);
		var device = stateDevice[0] === null ? editorDevice : stateDevice[0];
		var setDevice = function (v) {
			stateDevice[1](v);
			var ed = wp.data.dispatch('core/editor');
			if (ed && ed.setDeviceType) {
				ed.setDeviceType(v === 'T' ? 'Tablet' : (v === 'M' ? 'Mobile' : 'Desktop'));
			}
		};
		var set = function (key, value) {
			var next = Object.assign({}, d);
			if (value === '' || value === undefined || value === null || value === false || value === 0) {
				delete next[key];
			} else {
				next[key] = value;
			}
			props.setAttributes({ bsDesign: next });
		};
		var sel = function (label, key, options) {
			return el(c.SelectControl, {
				label: label,
				value: d[key] === undefined ? '' : String(d[key]),
				options: options.map(function (o) { return { value: String(o[0]), label: o[1] }; }),
				onChange: function (v) { set(key, /^[0-9]+$/.test(v) ? parseInt(v, 10) : v); },
				__nextHasNoMarginBottom: true
			});
		};
		var depthOptions = [[0, __('None', T)], [1, '1'], [2, '2'], [3, '3'], [4, '4'], [5, '5']];

		return el(c.PanelBody, { title: __('Design (spacing, background, border)', T) + (isSet(d) ? ' •' : ''), initialOpen: false, className: 'bst-design-panel' },
			el('div', { className: 'bst-design-devices', role: 'tablist' },
				DEVICES.map(function (dev) {
					return el(c.Button, {
						key: dev[0] || 'd',
						variant: device === dev[0] ? 'primary' : 'secondary',
						size: 'small',
						onClick: function () { setDevice(dev[0]); }
					}, dev[1]);
				})
			),
			el('p', { className: 'description' }, __('Numbers are pixels; also accepts em, rem, %, vh, auto. Negative margins allowed. Tablet/mobile values override desktop.', T)),
			el(BoxInputs, { label: __('Margin', T), prefix: 'm', device: device, d: d, set: set }),
			el(BoxInputs, { label: __('Padding', T), prefix: 'p', device: device, d: d, set: set }),
			el(c.SelectControl, { label: __('Width', T), value: ['', 'auto', 'full'].indexOf(d['w' + device] || '') !== -1 ? (d['w' + device] || '') : 'custom', options: [{ value: '', label: device ? __('Inherit', T) : __('Default', T) }, { value: 'auto', label: __('Fit content (inline)', T) }, { value: 'full', label: __('Full width', T) }, { value: 'custom', label: __('Custom', T) }], onChange: function (v) { set('w' + device, v === 'custom' ? '50%' : v); }, __nextHasNoMarginBottom: true }),
			d['w' + device] && ['auto', 'full'].indexOf(d['w' + device]) === -1 ? el(c.TextControl, { label: __('Custom width', T), value: d['w' + device], onChange: function (v) { set('w' + device, v); }, __nextHasNoMarginBottom: true }) : null,
			el(c.TextControl, { label: __('Max width', T), value: d['mw' + device] || '', placeholder: device ? __('inherit', T) : 'e.g. 600', onChange: function (v) { set('mw' + device, v); }, __nextHasNoMarginBottom: true }),
			el(c.SelectControl, { label: __('Position', T), value: d['pos' + device] || '', options: [{ value: '', label: device ? __('Inherit', T) : __('Default', T) }, { value: 'relative', label: __('Relative (nudge with offsets)', T) }, { value: 'absolute', label: __('Absolute (free placement inside parent)', T) }, { value: 'fixed', label: __('Fixed (stays on screen)', T) }, { value: 'static', label: __('Static (reset)', T) }], onChange: function (v) { set('pos' + device, v); }, help: d['pos' + device] === 'absolute' ? __('Tip: give the parent Column/Section "Position: Relative".', T) : (d['pos' + device] === 'fixed' ? __('Shown as absolute inside the editor so it stays editable.', T) : undefined), __nextHasNoMarginBottom: true }),
			(d['pos' + device] || d['pos'] || d['posT']) ? el(BoxInputs, { label: __('Offsets (top / right / bottom / left)', T), prefix: 'o', device: device, d: d, set: set }) : null,
			!device ? el(c.ToggleControl, { label: __('Center when narrower', T), checked: !!d.mwc, onChange: function (v) { set('mwc', v); }, __nextHasNoMarginBottom: true }) : null,
			!device ? el(Fragment, null,
				el(ColorField, { label: __('Background color', T), value: d.bg, onChange: function (v) { set('bg', v); } }),
				el(ColorField, { label: __('Text color', T), value: d.color, onChange: function (v) { set('color', v); } }),
				el(c.TextControl, { label: __('Border width', T), value: d.bw || '', placeholder: '0', onChange: function (v) { set('bw', v); }, __nextHasNoMarginBottom: true }),
				d.bw ? sel(__('Border style', T), 'bs', [['', __('Solid', T)], ['dashed', __('Dashed', T)], ['dotted', __('Dotted', T)], ['double', __('Double', T)]]) : null,
				d.bw ? el(ColorField, { label: __('Border color', T), value: d.bc, onChange: function (v) { set('bc', v); } }) : null,
				el(c.TextControl, { label: __('Corner radius', T), value: d.br || '', placeholder: '0', onChange: function (v) { set('br', v); }, __nextHasNoMarginBottom: true }),
				sel(__('Depth (shadow)', T), 'depth', depthOptions),
				el('div', { className: 'bst-design-sub' }, __('On hover', T)),
				sel(__('Depth on hover', T), 'depthHover', depthOptions),
				el(ColorField, { label: __('Background on hover', T), value: d.hbg, onChange: function (v) { set('hbg', v); } }),
				el(ColorField, { label: __('Text color on hover', T), value: d.hcolor, onChange: function (v) { set('hcolor', v); } }),
				d.bw ? el(ColorField, { label: __('Border color on hover', T), value: d.hbc, onChange: function (v) { set('hbc', v); } }) : null,
				el(c.TextControl, { label: __('Corner radius on hover', T), value: d.hbr || '', onChange: function (v) { set('hbr', v); }, __nextHasNoMarginBottom: true }),
				el(c.RangeControl, { label: __('Move up on hover (px)', T), min: -20, max: 30, value: d.hmove || 0, onChange: function (v) { set('hmove', v || 0); }, __nextHasNoMarginBottom: true }),
				el(c.RangeControl, { label: __('Scale on hover (%)', T), min: 80, max: 130, value: d.hscale || 100, onChange: function (v) { set('hscale', v === 100 ? 0 : v); }, __nextHasNoMarginBottom: true }),
				el(c.TextControl, { label: __('Layer order (z-index)', T), type: 'number', value: d.z === undefined ? '' : String(d.z), onChange: function (v) { set('z', v === '' ? '' : parseInt(v, 10)); }, __nextHasNoMarginBottom: true })
			) : null,
			el('div', { className: 'bst-design-actions' },
				el(c.Button, { variant: 'secondary', size: 'small', icon: 'admin-page', disabled: !isSet(d), onClick: function () {
					try { window.localStorage.setItem('bstDesignClipboard', JSON.stringify(d)); } catch (e) {}
					wp.data.dispatch('core/notices').createNotice('snackbar', __('Design copied. Select another block and click "Paste design".', T), { type: 'snackbar' });
				} }, __('Copy design', T)),
				el(c.Button, { variant: 'secondary', size: 'small', icon: 'clipboard', onClick: function () {
					var saved = null;
					try { saved = JSON.parse(window.localStorage.getItem('bstDesignClipboard') || 'null'); } catch (e) {}
					if (saved && typeof saved === 'object') {
						props.setAttributes({ bsDesign: saved });
					} else {
						wp.data.dispatch('core/notices').createNotice('snackbar', __('Nothing copied yet.', T), { type: 'snackbar' });
					}
				} }, __('Paste design', T))
			),
			isSet(d) ? el(c.Button, { variant: 'link', isDestructive: true, onClick: function () { props.setAttributes({ bsDesign: {} }); } }, __('Reset design', T)) : null
		);
	}

	/*
	 * Width of a block placed directly on the page (not inside a Section / Column):
	 * container, wide or full screen. Blocks that already support WordPress alignment
	 * use their core "align" attribute; the others use bsWidth.
	 */
	var WIDTHS = [['', __('Container width', T), 'align-none'], ['wide', __('Wide (wider than container)', T), 'align-wide'], ['full', __('Full screen width', T), 'align-full-width']];
	function usesCoreAlign(name) {
		var type = wp.blocks.getBlockType(name);
		var al = type && type.supports && type.supports.align;
		return Array.isArray(al) && al.indexOf('full') !== -1;
	}
	function currentWidth(props) {
		if (usesCoreAlign(props.name)) {
			return props.attributes.align === 'full' || props.attributes.align === 'wide' ? props.attributes.align : '';
		}
		return props.attributes.bsWidth || '';
	}
	function setWidth(props, v) {
		if (usesCoreAlign(props.name)) {
			props.setAttributes({ align: v || undefined });
		} else {
			props.setAttributes({ bsWidth: v });
		}
	}
	function WidthControls(props) {
		var isTopLevel = wp.data.useSelect(function (select) {
			return !select('core/block-editor').getBlockRootClientId(props.clientId);
		}, [props.clientId]);
		if (!isTopLevel) {
			return null;
		}
		var cur = currentWidth(props);
		var active = WIDTHS.filter(function (w) { return w[0] === cur; })[0] || WIDTHS[0];
		return el(Fragment, null,
			el(be.BlockControls, { group: 'block' },
				el(c.ToolbarDropdownMenu, {
					icon: active[2],
					label: __('Block width', T),
					controls: WIDTHS.map(function (w) {
						return { title: w[1], icon: w[2], isActive: w[0] === cur, onClick: function () { setWidth(props, w[0]); } };
					})
				})
			),
			el(be.InspectorControls, null,
				el(c.PanelBody, { title: __('Block width', T), initialOpen: !!cur, className: 'bst-width-panel' },
					el('div', { className: 'bst-width-choices' }, WIDTHS.map(function (w) {
						return el('button', { key: w[0] || 'c', type: 'button', className: 'bst-width-choice bst-width-choice--' + (w[0] || 'container') + (w[0] === cur ? ' is-selected' : ''), onClick: function () { setWidth(props, w[0]); } }, el('span', { className: 'bst-width-choice__bar' }), el('em', null, w[1]));
					})),
					el('p', { className: 'description' }, __('Works for blocks placed directly on the page. Inside a Section, use the Section “Content width” instead.', T))
				)
			)
		);
	}

	/* Typography & alignment (Elementor / Flatsome style), responsive. */
	function TypographyPanel(props) {
		var d = props.attributes.bsDesign || {};
		var device = wp.data.useSelect(function (select) {
			var ed = select('core/editor');
			var type = ed && ed.getDeviceType ? ed.getDeviceType() : 'Desktop';
			return type === 'Tablet' ? 'T' : (type === 'Mobile' ? 'M' : '');
		}, []);
		var set = function (key, value) {
			var next = Object.assign({}, d);
			if (value === '' || value === undefined || value === null || value === 0 || value === false) {
				delete next[key];
			} else {
				next[key] = value;
			}
			props.setAttributes({ bsDesign: next });
		};
		var setDevice = function (v) {
			var ed = wp.data.dispatch('core/editor');
			if (ed && ed.setDeviceType) {
				ed.setDeviceType(v === 'T' ? 'Tablet' : (v === 'M' ? 'Mobile' : 'Desktop'));
			}
		};
		var any = ['ta', 'taT', 'taM', 'fs', 'fsT', 'fsM', 'lh', 'lhT', 'lhM', 'ls', 'lsT', 'lsM', 'ff', 'fw', 'tt', 'fst', 'td', 'tsh'].some(function (k) { return d[k]; });
		var ALIGN = [['left', 'editor-alignleft', __('Left', T)], ['center', 'editor-aligncenter', __('Center', T)], ['right', 'editor-alignright', __('Right', T)], ['justify', 'editor-justify', __('Justify', T)]];
		var opt = function (label, key, list) {
			return el(c.SelectControl, { label: label, value: d[key] || '', options: list.map(function (o) { return { value: o[0], label: o[1] }; }), onChange: function (v) { set(key, v); }, __nextHasNoMarginBottom: true });
		};
		return el(c.PanelBody, { title: __('Typography & alignment', T) + (any ? ' •' : ''), initialOpen: false, className: 'bst-typo-panel' },
			el('div', { className: 'bst-design-devices' }, DEVICES.map(function (dev) {
				return el(c.Button, { key: dev[0] || 'd', variant: device === dev[0] ? 'primary' : 'secondary', size: 'small', onClick: function () { setDevice(dev[0]); } }, dev[1]);
			})),
			el(c.BaseControl, { label: __('Alignment', T) + (device ? ' (' + (device === 'T' ? __('tablet', T) : __('mobile', T)) + ')' : ''), __nextHasNoMarginBottom: true },
				el('div', { className: 'bst-typo-align' },
					ALIGN.map(function (a) {
						var on = d['ta' + device] === a[0];
						return el(c.Button, { key: a[0], icon: a[1], label: a[2], isPressed: on, onClick: function () { set('ta' + device, on ? '' : a[0]); } });
					})
				)
			),
			el('div', { className: 'bst-typo-grid' },
				el(c.TextControl, { label: __('Font size', T), value: d['fs' + device] || '', placeholder: device ? __('inherit', T) : 'e.g. 18, 1.2em', onChange: function (v) { set('fs' + device, v); }, __nextHasNoMarginBottom: true }),
				el(c.TextControl, { label: __('Line height', T), value: d['lh' + device] || '', placeholder: device ? __('inherit', T) : 'e.g. 1.6', onChange: function (v) { set('lh' + device, v); }, __nextHasNoMarginBottom: true }),
				el(c.TextControl, { label: __('Letter spacing', T), value: d['ls' + device] || '', placeholder: device ? __('inherit', T) : 'e.g. 1, -0.02em', onChange: function (v) { set('ls' + device, v); }, __nextHasNoMarginBottom: true })
			),
			!device ? el(Fragment, null,
				opt(__('Font', T), 'ff', [['', __('Default', T)], ['heading', __('Heading font', T)], ['body', __('Body font', T)], ['serif', __('Serif', T)], ['mono', __('Monospace', T)], ['system', __('System UI', T)]]),
				el('div', { className: 'bst-typo-grid bst-typo-grid--2' },
					opt(__('Weight', T), 'fw', [['', __('Default', T)], ['300', __('Light 300', T)], ['400', __('Regular 400', T)], ['500', __('Medium 500', T)], ['600', __('Semi bold 600', T)], ['700', __('Bold 700', T)], ['800', __('Extra bold 800', T)], ['900', __('Black 900', T)]]),
					opt(__('Transform', T), 'tt', [['', __('Default', T)], ['uppercase', 'UPPERCASE'], ['lowercase', 'lowercase'], ['capitalize', 'Capitalize'], ['none', __('As typed', T)]])
				),
				el('div', { className: 'bst-typo-grid bst-typo-grid--2' },
					opt(__('Style', T), 'fst', [['', __('Normal', T)], ['italic', __('Italic', T)]]),
					opt(__('Decoration', T), 'td', [['', __('None', T)], ['underline', __('Underline', T)], ['line-through', __('Strike', T)], ['overline', __('Overline', T)]])
				),
				opt(__('Text shadow', T), 'tsh', [['', __('None', T)], ['1', __('Subtle', T)], ['2', __('Medium', T)], ['3', __('Strong (on photos)', T)]])
			) : el('p', { className: 'description' }, __('Font, weight and style are set on Desktop and apply everywhere.', T)),
			any ? el(c.Button, { variant: 'link', isDestructive: true, onClick: function () {
				var next = Object.assign({}, d);
				['ta', 'taT', 'taM', 'fs', 'fsT', 'fsM', 'lh', 'lhT', 'lhM', 'ls', 'lsT', 'lsM', 'ff', 'fw', 'tt', 'fst', 'td', 'tsh'].forEach(function (k) { delete next[k]; });
				props.setAttributes({ bsDesign: next });
			} }, __('Reset typography', T)) : null
		);
	}

	function AlignToolbar(props) {
		var d = props.attributes.bsDesign || {};
		var device = wp.data.useSelect(function (select) {
			var ed = select('core/editor');
			var type = ed && ed.getDeviceType ? ed.getDeviceType() : 'Desktop';
			return type === 'Tablet' ? 'T' : (type === 'Mobile' ? 'M' : '');
		}, []);
		var hasOwn = props.attributes.textAlign !== undefined || props.attributes.align !== undefined && props.name === 'bst/heading';
		if (hasOwn) {
			return null;
		}
		return el(be.BlockControls, { group: 'block' },
			el(be.AlignmentControl, {
				value: d['ta' + device] || undefined,
				alignmentControls: [{ icon: 'editor-alignleft', title: __('Align left', T), align: 'left' }, { icon: 'editor-aligncenter', title: __('Align center', T), align: 'center' }, { icon: 'editor-alignright', title: __('Align right', T), align: 'right' }, { icon: 'editor-justify', title: __('Justify', T), align: 'justify' }],
				onChange: function (v) {
					var next = Object.assign({}, d);
					if (v) {
						next['ta' + device] = v;
					} else {
						delete next['ta' + device];
					}
					props.setAttributes({ bsDesign: next });
				}
			})
		);
	}

	var withDesignPanel = wp.compose.createHigherOrderComponent(function (BlockEdit) {
		return function (props) {
			if (!isBst(props.name)) {
				return el(BlockEdit, props);
			}
			var style = css(props.attributes.bsDesign, '.bsd-e-' + props.clientId);
			return el(Fragment, null,
				el(BlockEdit, props),
				el(WidthControls, props),
				el(AlignToolbar, props),
				style ? el('style', null, style) : null,
				el(be.InspectorControls, { group: 'styles' }, el(TypographyPanel, props), el(DesignPanel, props))
			);
		};
	}, 'withBstDesignPanel');
	wp.hooks.addFilter('editor.BlockEdit', 'bst/design-panel', withDesignPanel);

	var withDesignClass = wp.compose.createHigherOrderComponent(function (BlockListBlock) {
		return function (props) {
			if (!isBst(props.name)) {
				return el(BlockListBlock, props);
			}
			var extra = '';
			if (isSet(props.attributes.bsDesign)) {
				extra += ' bsd-e-' + props.clientId;
			}
			if (props.attributes.bsWidth === 'wide' || props.attributes.bsWidth === 'full') {
				extra += ' align' + props.attributes.bsWidth;
			}
			if (!extra) {
				return el(BlockListBlock, props);
			}
			var wrapperProps = Object.assign({}, props.wrapperProps);
			if (props.attributes.bsWidth) {
				wrapperProps['data-align'] = props.attributes.bsWidth;
			}
			return el(BlockListBlock, Object.assign({}, props, { className: ((props.className || '') + extra).trim(), wrapperProps: wrapperProps }));
		};
	}, 'withBstDesignClass');
	wp.hooks.addFilter('editor.BlockListBlock', 'bst/design-class', withDesignClass);
})(window.wp);
