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
				out.push('border-radius:' + br + (overflow(d) ? '' : ';overflow:hidden'));
			}
			if (SHADOWS[d.depth] && !customShadow(d)) {
				out.push('box-shadow:' + SHADOWS[d.depth]);
			}
			if (d.z !== undefined && d.z !== '' && !isNaN(d.z)) {
				out.push('position:relative;z-index:' + parseInt(d.z, 10));
			}
			if (hasHover(d)) {
				var t = num(d.tdur, 0, 5000);
				t = t === '' ? '.3s' : t + 'ms';
				out.push(['background-color', 'color', 'border-color', 'border-radius', 'box-shadow', 'transform'].map(function (p) { return p + ' ' + t + ' ease'; }).join(',').replace(/^/, 'transition:'));
			}
		}
		return out.concat(rulesExtra(d, s));
	}

	/* G-130 keys; mirrors inc/block-design.php. */
	function num(v, min, max) {
		if (v === undefined || v === null || v === '' || isNaN(v)) {
			return '';
		}
		return String(Math.max(min, Math.min(max, parseFloat(v))));
	}
	function color(v) {
		v = v ? String(v).trim() : '';
		return /^#[0-9a-f]{3,8}$/i.test(v) || /^(rgb|rgba|hsl|hsla)\([0-9.,%\s\/]+\)$/i.test(v) || /^var\(--[a-z0-9-]+\)$/i.test(v) ? v : '';
	}
	function overflow(d) {
		return ['visible', 'hidden', 'clip'].indexOf(d.ov) !== -1 ? d.ov : '';
	}
	function customShadow(d) {
		var c = color(d.shc);
		if (!c) {
			return '';
		}
		var parts = ['shx', 'shy', 'shb', 'shs'].map(function (k) {
			var n = num(d[k] || 0, -200, 200);
			return n === '' || n === '0' ? '0' : n + 'px';
		});
		return (d.shi ? 'inset ' : '') + parts.join(' ') + ' ' + c;
	}
	function transformParts(d, p) {
		var t = [];
		var x = len(d[p + 'tx']);
		var y = len(d[p + 'ty']);
		if (x !== '' || y !== '') {
			t.push('translate(' + (x === '' ? '0' : x) + ',' + (y === '' ? '0' : y) + ')');
		}
		var r = num(d[p + 'rot'], -360, 360);
		if (r !== '' && r !== '0') {
			t.push('rotate(' + r + 'deg)');
		}
		var k = num(d[p + 'skx'], -60, 60);
		if (k !== '' && k !== '0') {
			t.push('skewX(' + k + 'deg)');
		}
		return t;
	}
	var MASKS = {
		circle: 'clip-path:circle(50% at 50% 50%)',
		ellipse: 'clip-path:ellipse(50% 50% at 50% 50%)',
		arch: 'clip-path:inset(0 round 9999px 9999px 0 0)',
		blob: 'border-radius:42% 58% 63% 37%/41% 44% 56% 59%;overflow:hidden',
		hexagon: 'clip-path:polygon(25% 0,75% 0,100% 50%,75% 100%,25% 100%,0 50%)',
		diamond: 'clip-path:polygon(50% 0,100% 50%,50% 100%,0 50%)'
	};
	function gradient(d) {
		if (d.gt !== 'linear' && d.gt !== 'radial') {
			return '';
		}
		var cs = [color(d.g1), color(d.g2), color(d.g3)].filter(Boolean);
		if (cs.length < 2) {
			return '';
		}
		if (d.gt === 'radial') {
			return 'radial-gradient(circle,' + cs.join(',') + ')';
		}
		var a = num(d.ga, 0, 360);
		return 'linear-gradient(' + (a === '' ? '180' : a) + 'deg,' + cs.join(',') + ')';
	}
	function safeUrl(v) {
		v = v ? String(v).trim() : '';
		return /^https?:\/\/[^"'()\\\s<>]+$/i.test(v) ? v : '';
	}
	function rulesExtra(d, s) {
		var out = [];
		var bstyle = ['solid', 'dashed', 'dotted', 'double'].indexOf(d.bs) !== -1 ? d.bs : 'solid';
		if (s !== '') {
			if (color(d['bg' + s])) {
				out.push('background-color:' + color(d['bg' + s]));
			}
			if (color(d['color' + s])) {
				out.push('color:' + color(d['color' + s]) + ';--bs-heading-color:' + color(d['color' + s]));
			}
			if (len(d['bw' + s]) !== '') {
				out.push('border-width:' + len(d['bw' + s]) + ';border-style:' + bstyle);
			}
			if (color(d['bc' + s])) {
				out.push('border-color:' + color(d['bc' + s]));
			}
			if (len(d['br' + s]) !== '') {
				out.push('border-radius:' + len(d['br' + s]));
			}
		}
		if (len(d['mh' + s]) !== '') {
			out.push('min-height:' + len(d['mh' + s]));
		}
		if (['1/1', '4/3', '3/2', '16/9', '21/9', '3/4', '2/3', '9/16'].indexOf(d['ar' + s]) !== -1) {
			out.push('aspect-ratio:' + d['ar' + s]);
		}
		var ord = num(d['ord' + s], -99, 99);
		if (ord !== '') {
			out.push('order:' + parseInt(ord, 10));
		}
		if (['auto', 'flex-start', 'center', 'flex-end', 'stretch'].indexOf(d['as' + s]) !== -1) {
			out.push('align-self:' + d['as' + s]);
		}
		if (s === '' && (d.st === 'all' || d.st === 'desktop')) {
			out.push('position:sticky;top:' + (len(d.stt) === '' ? '0' : len(d.stt)));
		} else if (s === 'T' && d.st === 'desktop' && !d.posT) {
			out.push('position:static');
		}
		if (s !== '') {
			return out;
		}
		var base = len(d.bw);
		var any = false;
		var w = ['bwt', 'bwr', 'bwb', 'bwl'].map(function (k) {
			var v = len(d[k]);
			any = any || v !== '';
			return v !== '' ? v : (base !== '' ? base : '0');
		});
		if (any) {
			out.push('border-style:' + bstyle + ';border-color:' + (color(d.bc) || 'currentColor') + ';border-width:' + w.join(' '));
		}
		[['rtl', 'top-left'], ['rtr', 'top-right'], ['rbr', 'bottom-right'], ['rbl', 'bottom-left']].forEach(function (o) {
			if (len(d[o[0]]) !== '') {
				out.push('border-' + o[1] + '-radius:' + len(d[o[0]]));
			}
		});
		var img = safeUrl(d.bgi);
		var g = gradient(d);
		if (img || g) {
			var layers = [];
			if (color(d.ovl)) {
				layers.push('linear-gradient(' + color(d.ovl) + ',' + color(d.ovl) + ')');
			}
			if (img) {
				layers.push('url("' + img + '")');
			}
			if (g) {
				layers.push(g);
			}
			out.push('background-image:' + layers.join(','));
			if (img) {
				var pos = ['top', 'bottom', 'left', 'right', 'top left', 'top right', 'bottom left', 'bottom right'].indexOf(d.bgp) !== -1 ? d.bgp : 'center';
				var size = ['contain', 'auto'].indexOf(d.bgs) !== -1 ? d.bgs : 'cover';
				var rep = ['repeat', 'repeat-x', 'repeat-y'].indexOf(d.bgr) !== -1 ? d.bgr : 'no-repeat';
				out.push('background-position:' + pos + ';background-size:' + size + ';background-repeat:' + rep);
			}
		}
		if (customShadow(d)) {
			out.push('box-shadow:' + customShadow(d));
		}
		var op = num(d.op, 0, 100);
		if (op !== '' && op !== '100') {
			out.push('opacity:' + parseFloat(op) / 100);
		}
		var f = [];
		var v = num(d.fbl, 0, 50);
		if (v !== '' && v !== '0') {
			f.push('blur(' + v + 'px)');
		}
		v = num(d.fbr, 0, 300);
		if (v !== '' && v !== '0' && v !== '100') {
			f.push('brightness(' + v + '%)');
		}
		v = num(d.fgr, 0, 100);
		if (v !== '' && v !== '0') {
			f.push('grayscale(' + v + '%)');
		}
		if (f.length) {
			out.push('filter:' + f.join(' '));
		}
		v = num(d.bdb, 0, 50);
		if (v !== '' && v !== '0') {
			out.push('-webkit-backdrop-filter:blur(' + v + 'px);backdrop-filter:blur(' + v + 'px)');
		}
		var t = transformParts(d, '');
		if (t.length) {
			out.push('transform:' + t.join(' '));
		}
		if (MASKS[d.msk]) {
			out.push(MASKS[d.msk]);
		}
		if (overflow(d)) {
			out.push('overflow:' + overflow(d));
		}
		return out;
	}

	function needsSpace(d) {
		var boxed = !!d.bg || (len(d.bw) !== '' && len(d.bw) !== '0');
		return boxed && !Object.keys(d).some(function (k) { return /^p[trbl](T|M)?$/.test(k) && String(d[k]).trim() !== ''; });
	}

	function hasHover(d) {
		return ['depthHover', 'hbg', 'hcolor', 'hbc', 'hbr', 'hmove', 'hscale', 'hrot', 'htx', 'hty', 'hskx'].some(function (k) { return d[k] !== undefined && d[k] !== '' && d[k] !== 0; });
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
		var own = transformParts(d, 'h');
		var moves = own.length || d.hmove || (d.hscale && parseFloat(d.hscale) !== 100);
		var t = moves ? transformParts(d, '').concat(own) : [];
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
		}
		var off = (window.bstEditor && window.bstEditor.disabledBlocks) || [];
		if (off.indexOf(name) !== -1) {
			settings.supports = Object.assign({}, settings.supports, { inserter: false });
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

	/* Style attributes of a block (not content): numbers, booleans and short style-named strings. */
	var STYLE_KEY = /color|bg|background|size|style|skin|variant|layout|align|radius|width|height|gap|shadow|font|weight|border|padding|spacing|columns|ratio|position|effect|animation|hover|overlay|opacity|presets/i;
	var CONTENT_KEY = /^(content|text|title|label|url|href|link|items|images|media|id|ids|alt|caption|html|code|css|query|anchor|lock|metadata)$|Url$|Id$|Text$/;
	function styleAttrs(props) {
		var type = wp.blocks.getBlockType(props.name);
		var out = {};
		Object.keys((type && type.attributes) || {}).forEach(function (k) {
			var v = props.attributes[k];
			if (k === 'bsDesign' || CONTENT_KEY.test(k) || v === undefined) {
				return;
			}
			if (typeof v === 'number' || typeof v === 'boolean' || (typeof v === 'string' && v.length <= 60 && STYLE_KEY.test(k)) || k === 'bsPresets') {
				out[k] = v;
			}
		});
		return out;
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
					try {
						window.localStorage.setItem('bstDesignClipboard', JSON.stringify(d));
						window.localStorage.setItem('bstStyleClipboard', JSON.stringify({ name: props.name, attrs: styleAttrs(props) }));
					} catch (e) {}
					wp.data.dispatch('core/notices').createNotice('snackbar', __('Design copied. Select another block and click "Paste design".', T), { type: 'snackbar' });
				} }, __('Copy design', T)),
				el(c.Button, { variant: 'secondary', size: 'small', icon: 'clipboard', onClick: function () {
					var saved = null;
					try { saved = JSON.parse(window.localStorage.getItem('bstDesignClipboard') || 'null'); } catch (e) {}
					var style = null;
					try { style = JSON.parse(window.localStorage.getItem('bstStyleClipboard') || 'null'); } catch (e) {}
					if (saved && typeof saved === 'object') {
						// Same block type: the block's own style options come along too.
						var extra = style && style.name === props.name && style.attrs ? style.attrs : {};
						props.setAttributes(Object.assign({}, extra, { bsDesign: saved }));
					} else {
						wp.data.dispatch('core/notices').createNotice('snackbar', __('Nothing copied yet.', T), { type: 'snackbar' });
					}
				} }, __('Paste design', T))
			),
			isSet(d) ? el(c.Button, { variant: 'link', isDestructive: true, onClick: function () { props.setAttributes({ bsDesign: {} }); } }, __('Reset design', T)) : null
		);
	}

	/* G-130: borders per side, gradient / image, custom shadow, filters, transform, mask, layout per device. */
	function useDevice() {
		return wp.data.useSelect(function (select) {
			var ed = select('core/editor');
			var type = ed && ed.getDeviceType ? ed.getDeviceType() : 'Desktop';
			return type === 'Tablet' ? 'T' : (type === 'Mobile' ? 'M' : '');
		}, []);
	}
	var EFFECT_KEYS = ['bwt', 'bwr', 'bwb', 'bwl', 'rtl', 'rtr', 'rbr', 'rbl', 'gt', 'g1', 'g2', 'g3', 'ga', 'bgi', 'bgp', 'bgs', 'bgr', 'ovl', 'shx', 'shy', 'shb', 'shs', 'shc', 'shi', 'op', 'fbl', 'fbr', 'fgr', 'bdb', 'rot', 'tx', 'ty', 'skx', 'hrot', 'htx', 'hty', 'hskx', 'tdur', 'msk', 'ov', 'st', 'stt'];
	var DEVICE_KEYS = ['bg', 'color', 'bw', 'bc', 'br', 'mh', 'ar', 'ord', 'as'];

	function EffectsPanel(props) {
		var d = props.attributes.bsDesign || {};
		var device = useDevice();
		var set = function (key, value) {
			var next = Object.assign({}, d);
			if (value === '' || value === undefined || value === null || value === false) {
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
		var txt = function (label, key, ph) {
			return el(c.TextControl, { label: label, value: d[key] === undefined ? '' : String(d[key]), placeholder: ph || '', onChange: function (v) { set(key, v); }, __nextHasNoMarginBottom: true });
		};
		var opt = function (label, key, list) {
			return el(c.SelectControl, { label: label, value: d[key] === undefined ? '' : String(d[key]), options: list.map(function (o) { return { value: o[0], label: o[1] }; }), onChange: function (v) { set(key, v); }, __nextHasNoMarginBottom: true });
		};
		var range = function (label, key, min, max, def) {
			return el(c.RangeControl, { label: label, min: min, max: max, value: d[key] === undefined || d[key] === '' ? def : d[key], allowReset: true, resetFallbackValue: def, onChange: function (v) { set(key, v === undefined || v === def ? '' : v); }, __nextHasNoMarginBottom: true });
		};
		var any = EFFECT_KEYS.concat(DEVICE_KEYS.map(function (k) { return k + 'T'; }), DEVICE_KEYS.map(function (k) { return k + 'M'; }), ['mh', 'ar', 'ord', 'as']).some(function (k) { return d[k] !== undefined && d[k] !== ''; });
		var inherit = device ? __('Inherit', T) : __('Default', T);
		return el(c.PanelBody, { title: __('Effects & layout', T) + (any ? ' •' : ''), initialOpen: false, className: 'bst-design-panel bst-effects-panel' },
			el('div', { className: 'bst-design-devices' }, DEVICES.map(function (dev) {
				return el(c.Button, { key: dev[0] || 'd', variant: device === dev[0] ? 'primary' : 'secondary', size: 'small', onClick: function () { setDevice(dev[0]); } }, dev[1]);
			})),
			el('div', { className: 'bst-design-sub' }, __('Layout', T)),
			txt(__('Min height', T), 'mh' + device, device ? __('inherit', T) : 'e.g. 300, 50vh'),
			opt(__('Aspect ratio', T), 'ar' + device, [['', inherit], ['1/1', '1:1'], ['4/3', '4:3'], ['3/2', '3:2'], ['16/9', '16:9'], ['21/9', '21:9'], ['3/4', '3:4'], ['2/3', '2:3'], ['9/16', '9:16']]),
			txt(__('Order in row (flex order)', T), 'ord' + device, device ? __('inherit', T) : '0'),
			opt(__('Align self', T), 'as' + device, [['', inherit], ['flex-start', __('Start', T)], ['center', __('Center', T)], ['flex-end', __('End', T)], ['stretch', __('Stretch', T)]]),
			device ? el(Fragment, null,
				el('div', { className: 'bst-design-sub' }, device === 'T' ? __('Tablet colors & border', T) : __('Mobile colors & border', T)),
				el(ColorField, { label: __('Background color', T), value: d['bg' + device], onChange: function (v) { set('bg' + device, v); } }),
				el(ColorField, { label: __('Text color', T), value: d['color' + device], onChange: function (v) { set('color' + device, v); } }),
				txt(__('Border width', T), 'bw' + device, __('inherit', T)),
				el(ColorField, { label: __('Border color', T), value: d['bc' + device], onChange: function (v) { set('bc' + device, v); } }),
				txt(__('Corner radius', T), 'br' + device, __('inherit', T))
			) : el(Fragment, null,
				opt(__('Sticky', T), 'st', [['', __('Off', T)], ['all', __('All devices', T)], ['desktop', __('Desktop only', T)]]),
				d.st ? txt(__('Sticky offset from top', T), 'stt', '0') : null,
				opt(__('Overflow', T), 'ov', [['', __('Default', T)], ['visible', __('Visible', T)], ['hidden', __('Hidden', T)], ['clip', __('Clip', T)]]),
				el('div', { className: 'bst-design-sub' }, __('Border per side', T)),
				el(BoxInputs, { label: __('Border width per side', T), prefix: 'bw', device: '', d: d, set: set }),
				el('div', { className: 'bst-design-box' },
					el('div', { className: 'bst-design-box__label' }, __('Corner radius per corner', T)),
					el('div', { className: 'bst-design-box__grid' }, [['rtl', __('Top left', T)], ['rtr', __('Top right', T)], ['rbr', __('Bottom right', T)], ['rbl', __('Bottom left', T)]].map(function (o) {
						return el(c.TextControl, { key: o[0], label: o[1], value: d[o[0]] || '', placeholder: '–', onChange: function (v) { set(o[0], v); }, __nextHasNoMarginBottom: true });
					}))
				),
				el('div', { className: 'bst-design-sub' }, __('Background gradient & image', T)),
				opt(__('Gradient', T), 'gt', [['', __('None', T)], ['linear', __('Linear', T)], ['radial', __('Radial', T)]]),
				d.gt ? el(Fragment, null,
					el(ColorField, { label: __('Color 1', T), value: d.g1, onChange: function (v) { set('g1', v); } }),
					el(ColorField, { label: __('Color 2', T), value: d.g2, onChange: function (v) { set('g2', v); } }),
					el(ColorField, { label: __('Color 3 (optional)', T), value: d.g3, onChange: function (v) { set('g3', v); } }),
					d.gt === 'linear' ? range(__('Angle', T), 'ga', 0, 360, 180) : null
				) : null,
				el(be.MediaUploadCheck, null, el(be.MediaUpload, { allowedTypes: ['image'], onSelect: function (m) { set('bgi', m && m.url ? m.url : ''); }, render: function (o) {
					return el('div', { className: 'bst-design-actions' },
						el(c.Button, { variant: 'secondary', size: 'small', onClick: o.open }, d.bgi ? __('Replace background image', T) : __('Background image', T)),
						d.bgi ? el(c.Button, { variant: 'link', isDestructive: true, onClick: function () { set('bgi', ''); } }, __('Remove', T)) : null
					);
				} })),
				d.bgi ? el(Fragment, null,
					opt(__('Image position', T), 'bgp', [['', __('Center', T)], ['top', __('Top', T)], ['bottom', __('Bottom', T)], ['left', __('Left', T)], ['right', __('Right', T)], ['top left', __('Top left', T)], ['top right', __('Top right', T)], ['bottom left', __('Bottom left', T)], ['bottom right', __('Bottom right', T)]]),
					opt(__('Image size', T), 'bgs', [['', __('Cover', T)], ['contain', __('Contain', T)], ['auto', __('Original', T)]]),
					opt(__('Repeat', T), 'bgr', [['', __('No repeat', T)], ['repeat', __('Repeat', T)], ['repeat-x', __('Horizontally', T)], ['repeat-y', __('Vertically', T)]])
				) : null,
				(d.bgi || d.gt) ? el(ColorField, { label: __('Overlay color (use transparency)', T), value: d.ovl, onChange: function (v) { set('ovl', v); } }) : null,
				el('div', { className: 'bst-design-sub' }, __('Custom shadow', T)),
				el(ColorField, { label: __('Shadow color (overrides Depth)', T), value: d.shc, onChange: function (v) { set('shc', v); } }),
				d.shc ? el(Fragment, null,
					range(__('Horizontal (px)', T), 'shx', -50, 50, 0),
					range(__('Vertical (px)', T), 'shy', -50, 50, 0),
					range(__('Blur (px)', T), 'shb', 0, 100, 0),
					range(__('Spread (px)', T), 'shs', -50, 50, 0),
					el(c.ToggleControl, { label: __('Inner shadow (inset)', T), checked: !!d.shi, onChange: function (v) { set('shi', v); }, __nextHasNoMarginBottom: true })
				) : null,
				el('div', { className: 'bst-design-sub' }, __('Filters', T)),
				range(__('Opacity (%)', T), 'op', 0, 100, 100),
				range(__('Blur (px)', T), 'fbl', 0, 20, 0),
				range(__('Brightness (%)', T), 'fbr', 0, 200, 100),
				range(__('Grayscale (%)', T), 'fgr', 0, 100, 0),
				range(__('Backdrop blur (glass, px)', T), 'bdb', 0, 30, 0),
				el('div', { className: 'bst-design-sub' }, __('Transform', T)),
				range(__('Rotate (deg)', T), 'rot', -180, 180, 0),
				txt(__('Move X', T), 'tx', '0'),
				txt(__('Move Y', T), 'ty', '0'),
				range(__('Skew (deg)', T), 'skx', -30, 30, 0),
				el('div', { className: 'bst-design-sub' }, __('Transform on hover', T)),
				range(__('Rotate on hover (deg)', T), 'hrot', -180, 180, 0),
				txt(__('Move X on hover', T), 'htx', '0'),
				txt(__('Move Y on hover', T), 'hty', '0'),
				range(__('Skew on hover (deg)', T), 'hskx', -30, 30, 0),
				range(__('Hover transition (ms)', T), 'tdur', 0, 2000, 300),
				el('div', { className: 'bst-design-sub' }, __('Shape mask', T)),
				opt(__('Shape', T), 'msk', [['', __('None', T)], ['circle', __('Circle', T)], ['ellipse', __('Ellipse', T)], ['arch', __('Arch', T)], ['blob', __('Blob', T)], ['hexagon', __('Hexagon', T)], ['diamond', __('Diamond', T)]])
			),
			any ? el(c.Button, { variant: 'link', isDestructive: true, onClick: function () {
				var next = Object.assign({}, d);
				EFFECT_KEYS.concat(['mh', 'ar', 'ord', 'as']).forEach(function (k) { delete next[k]; });
				DEVICE_KEYS.forEach(function (k) { delete next[k + 'T']; delete next[k + 'M']; });
				props.setAttributes({ bsDesign: next });
			} }, __('Reset effects & layout', T)) : null
		);
	}

	/* Advanced: per-block CSS ("selector" = this block) and data-* / aria-* attributes. */
	function AdvancedPanel(props) {
		var d = props.attributes.bsDesign || {};
		var set = function (key, value) {
			var next = Object.assign({}, d);
			if (value === '') {
				delete next[key];
			} else {
				next[key] = value;
			}
			props.setAttributes({ bsDesign: next });
		};
		return el(be.InspectorControls, { group: 'advanced' },
			el(c.TextareaControl, { label: __('Custom CSS for this block', T), help: __('Use "selector" for this block, e.g. selector h2 { color: red; }. Plain declarations apply to the block. Printed only for users allowed to post unfiltered HTML.', T), value: d.ccss || '', rows: 5, className: 'bst-code-input', onChange: function (v) { set('ccss', v); }, __nextHasNoMarginBottom: true }),
			el(c.TextareaControl, { label: __('Custom attributes', T), help: __('One per line, name=value. Only data-* and aria-* are allowed.', T), value: d.hattr || '', rows: 3, placeholder: 'data-track=hero\naria-label=Intro', onChange: function (v) { set('hattr', v); }, __nextHasNoMarginBottom: true })
		);
	}

	function customCss(d, sel) {
		var s = d && d.ccss ? String(d.ccss).replace(/<\/?style[^>]*>/gi, '') : '';
		if (!s.trim()) {
			return '';
		}
		if (s.indexOf('{') === -1) {
			s = 'selector{' + s + '}';
		}
		return s.split('selector').join(sel);
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
			var style = css(props.attributes.bsDesign, '.bsd-e-' + props.clientId) + customCss(props.attributes.bsDesign, '.bsd-e-' + props.clientId);
			return el(Fragment, null,
				el(BlockEdit, props),
				el(WidthControls, props),
				el(AlignToolbar, props),
				style ? el('style', null, style) : null,
				el(be.InspectorControls, { group: 'styles' }, el(TypographyPanel, props), el(DesignPanel, props), el(EffectsPanel, props)),
				el(AdvancedPanel, props)
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
