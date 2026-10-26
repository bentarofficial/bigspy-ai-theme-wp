/* Native InnerBlocks editors for continuous ticker and expanding gallery. */
(function (wp) {
	'use strict';
	var el = wp.element.createElement, Fragment = wp.element.Fragment, be = wp.blockEditor, c = wp.components, __ = wp.i18n.__, T = 'bigspy-ai-theme-wp';
	function control(props, key, label, kind, options) {
		var settings = { label: label, value: props.attributes[key], onChange: function (v) { var a = {}; a[key] = v; props.setAttributes(a); } };
		if (kind === 'toggle') { settings.checked = !!props.attributes[key]; return el(c.ToggleControl, settings); }
		if (kind === 'select') { settings.options = options.map(function (o) { return { value: o[0], label: o[1] }; }); return el(c.SelectControl, settings); }
		if (kind === 'range') { settings.min = options[0]; settings.max = options[1]; settings.step = options[2] || 1; return el(c.RangeControl, settings); }
		return el(c.TextControl, settings);
	}
	function save() { return el(be.InnerBlocks.Content); }
	function parentEdit(kind) {
		return function (props) {
			var a = props.attributes, ticker = kind === 'ticker', child = 'bigspy/' + (ticker ? 'ticker-slide' : 'expand-panel');
			var blocks = wp.data.useSelect(function (select) { return select('core/block-editor').getBlocks(props.clientId); }, [props.clientId]);
			var dispatch = wp.data.useDispatch('core/block-editor');
			var state = wp.element.useState(false), preview = state[0];
			wp.element.useEffect(function () {
				var current = wp.data.select('core/block-editor').getBlocks(props.clientId);
				// Read the current store rather than a stale render while InnerBlocks mounts.
				if (!ticker && current.length < 2) { dispatch.insertBlocks(Array.from({ length: current.length ? 1 : 3 }, function () { return wp.blocks.createBlock(child); }), current.length, props.clientId); }
				if (!ticker && current.length > 6) { dispatch.removeBlocks(current.slice(6).map(function (b) { return b.clientId; }), false); }
			}, [blocks.length, props.clientId]);
			var add = function () { dispatch.insertBlocks(wp.blocks.createBlock(child), blocks.length, props.clientId); };
			var inspector = [control(props, 'label', __('Accessible label', T))];
			if (ticker) {
				inspector = inspector.concat([
					control(props, 'direction', __('Direction', T), 'select', [['left', __('Left', T)], ['right', __('Right', T)]]),
					control(props, 'mode', __('Mode', T), 'select', [['loop', __('Seamless loop', T)], ['once', __('Run once', T)]]),
					control(props, 'speed', __('Speed (px/second)', T), 'range', [1, 500]),
					control(props, 'widthDesktop', __('Desktop slide width (auto, px or %)', T)),
					control(props, 'widthTablet', __('Tablet slide width (auto, px or %)', T)),
					control(props, 'widthMobile', __('Mobile slide width (auto, px or %)', T)),
					control(props, 'verticalAlign', __('Vertical alignment', T), 'select', [['start', __('Top', T)], ['center', __('Center', T)], ['end', __('Bottom', T)]]),
					control(props, 'pauseHover', __('Pause on hover / touch', T), 'toggle'), control(props, 'fadeEdges', __('Fade edges', T), 'toggle')
				]);
			} else {
				inspector = inspector.concat([
					control(props, 'factor', __('Expansion factor', T), 'range', [1, 8, .1]),
					control(props, 'openPanel', __('Initially open panel (0 = none)', T), 'range', [0, blocks.length]),
					control(props, 'keepOpen', __('Keep the last panel open', T), 'toggle'),
					control(props, 'duration', __('Animation duration (seconds)', T), 'range', [0, 3, .1]),
					control(props, 'heightDesktop', __('Desktop height', T), 'range', [180, 1200]), control(props, 'heightTablet', __('Tablet height', T), 'range', [180, 1200]), control(props, 'heightMobile', __('Mobile height', T), 'range', [120, 1000]),
					control(props, 'radius', __('Corner radius', T), 'range', [0, 100]), control(props, 'verticalTitle', __('Vertical titles in collapsed panels', T), 'toggle'),
					control(props, 'mobileLayout', __('Mobile layout', T), 'select', [['swipe', __('Swipe with peek', T)], ['accordion', __('Vertical accordion', T)], ['horizontal', __('Keep horizontal', T)]]),
					control(props, 'peekRatio', __('Main mobile panel (%)', T), 'range', [55, 90]), control(props, 'dots', __('Position dots', T), 'toggle')
				]);
			}
			inspector.push(control(props, 'gap', __('Gap', T), 'range', [0, ticker ? 160 : 80]));
			var wrapper = be.useBlockProps({ className: 'bst-motion-editor bst-motion-editor--' + kind, style: { '--bst-motion-gap': a.gap + 'px' } });
			return el(Fragment, null,
				el(be.InspectorControls, null, el(c.PanelBody, { title: ticker ? __('Continuous ticker', T) : __('Expanding gallery', T) }, inspector)),
				el('div', wrapper,
					!ticker && blocks.length >= 5 ? el(c.Notice, { status: 'info', isDismissible: false }, __('Use clear backgrounds and short titles for easier reading on phones.', T)) : null,
					el(be.InnerBlocks, { allowedBlocks: [child], template: Array.from({ length: 3 }, function () { return [child]; }), orientation: 'horizontal', renderAppender: ticker || blocks.length < 6 ? be.InnerBlocks.ButtonBlockAppender : false }),
					el(c.Button, { variant: 'secondary', onClick: add, disabled: !ticker && blocks.length >= 6 }, ticker ? __('+ Add slide', T) : __('+ Add panel', T)),
					ticker ? el(c.Button, { variant: 'secondary', onClick: function () { state[1](!preview); } }, __('Preview motion', T)) : null,
					preview ? el(MotionPreview, { blocks: blocks, attributes: a, onClose: function () { state[1](false); } }) : null
				)
			);
		};
	}
	function MotionPreview(props) {
		var frame = wp.element.useRef(null), error = wp.element.useState('');
		wp.element.useEffect(function () {
			var alive = true;
			var markup = wp.blocks.serialize([wp.blocks.createBlock('bigspy/ticker', props.attributes, props.blocks)]);
			wp.apiFetch({ path: '/bst-motion/v1/preview', method: 'POST', data: { content: markup } }).then(function (r) {
				if (alive && frame.current) { frame.current.srcdoc = r.html; }
			}).catch(function (e) { if (alive) { error[1](e.message); } });
			return function () { alive = false; };
		}, []);
		return el(c.Modal, { title: __('Preview motion', T), onRequestClose: props.onClose }, error[0] ? el('p', null, error[0]) : el('iframe', { ref: frame, title: __('Preview motion', T), sandbox: 'allow-scripts', style: { width: '70vw', height: '420px', border: 0 } }));
	}
	wp.blocks.registerBlockType('bigspy/ticker', { edit: parentEdit('ticker'), save: save });
	wp.blocks.registerBlockType('bigspy/expand-gallery', { edit: parentEdit('expand-gallery'), save: save });
	wp.blocks.registerBlockType('bigspy/ticker-slide', { edit: function () { return el('div', be.useBlockProps({ className: 'bst-motion-slide' }), el(be.InnerBlocks)); }, save: save });
	wp.blocks.registerBlockType('bigspy/expand-panel', { edit: function (props) {
		var a = props.attributes;
		return el(Fragment, null, el(be.InspectorControls, null, el(c.PanelBody, { title: __('Gallery panel', T) },
			control(props, 'title', __('Panel title', T)),
			el(be.MediaUploadCheck, null, el(be.MediaUpload, { allowedTypes: ['image'], value: a.backgroundId, onSelect: function (image) { props.setAttributes({ backgroundId: image.id, backgroundUrl: image.url }); }, render: function (p) { return el(c.Button, { variant: 'secondary', onClick: p.open }, __('Choose background image', T)); } })),
			a.backgroundUrl ? el(c.FocalPointPicker, { url: a.backgroundUrl, value: a.focalPoint, onChange: function (v) { props.setAttributes({ focalPoint: v }); } }) : null,
			control(props, 'overlay', __('Overlay color or CSS gradient', T)),
			el(c.Button, { onClick: function () { props.setAttributes({ backgroundId: 0, backgroundUrl: '' }); } }, __('Remove background', T))
		)), el('div', be.useBlockProps({ className: 'bst-motion-panel', style: { backgroundImage: a.backgroundUrl ? (/gradient\(/.test(a.overlay) ? a.overlay : 'linear-gradient(' + (a.overlay || '#0006') + ',' + (a.overlay || '#0006') + ')') + ',url("' + a.backgroundUrl + '")' : '', backgroundPosition: (a.focalPoint.x * 100) + '% ' + (a.focalPoint.y * 100) + '%' } }), el('strong', null, a.title), el(be.InnerBlocks)));
	}, save: save });
}(window.wp));
