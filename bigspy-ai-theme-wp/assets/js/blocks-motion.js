/* Native InnerBlocks editors for Infinity Flow (ticker) and Spotlight Panels (expanding gallery). */
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
				el(be.InspectorControls, null, el(c.PanelBody, { title: ticker ? __('Infinity Flow', T) : __('Spotlight Panels', T) }, inspector)),
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
		return el(Fragment, null, el(be.InspectorControls, null, el(c.PanelBody, { title: __('Spotlight panel', T) },
			control(props, 'title', __('Panel title', T)),
			el(be.MediaUploadCheck, null, el(be.MediaUpload, { allowedTypes: ['image'], value: a.backgroundId, onSelect: function (image) { props.setAttributes({ backgroundId: image.id, backgroundUrl: image.url }); }, render: function (p) { return el(c.Button, { variant: 'secondary', onClick: p.open }, __('Choose background image', T)); } })),
			a.backgroundUrl ? el(c.FocalPointPicker, { url: a.backgroundUrl, value: a.focalPoint, onChange: function (v) { props.setAttributes({ focalPoint: v }); } }) : null,
			control(props, 'overlay', __('Overlay color or CSS gradient', T)),
			el(c.Button, { onClick: function () { props.setAttributes({ backgroundId: 0, backgroundUrl: '' }); } }, __('Remove background', T))
		)), el('div', be.useBlockProps({ className: 'bst-motion-panel', style: { backgroundImage: a.backgroundUrl ? (/gradient\(/.test(a.overlay) ? a.overlay : 'linear-gradient(' + (a.overlay || '#0006') + ',' + (a.overlay || '#0006') + ')') + ',url("' + a.backgroundUrl + '")' : '', backgroundPosition: (a.focalPoint.x * 100) + '% ' + (a.focalPoint.y * 100) + '%' } }), el('strong', null, a.title), el(be.InnerBlocks)));
	}, save: save });
	/* ------------------------------------------------ Motion wave 1: Animated Text, Scroll Story, Stacking Cards */
	var EFFECTS = [['reveal', __('Light up word by word while scrolling', T)], ['words', __('Words appear one by one', T)], ['typewriter', __('Typewriter', T)], ['rotate', __('Rotating words', T)], ['scramble', __('Scramble then reveal', T)], ['gradient', __('Flowing gradient', T)], ['marquee', __('Big text sliding with scroll', T)]];
	function colorRow(props, key, label) {
		return el('div', { className: 'bst-motion-color' }, el('p', null, label), el(c.ColorPalette, { value: props.attributes[key], onChange: function (v) { var o = {}; o[key] = v || ''; props.setAttributes(o); } }));
	}
	wp.blocks.registerBlockType('bigspy/animated-text', { edit: function (props) {
		var a = props.attributes, fx = a.effect, words = (a.words || '').split(/[\n|]+/).map(function (w) { return w.trim(); }).filter(Boolean);
		var usesWords = fx === 'rotate' || fx === 'typewriter';
		var shown = (a.text || '') + (words.length ? ' ' : '') + (words.length ? (usesWords ? words[0] : words[0]) : '') + (a.after ? ' ' + a.after : '');
		var style = { '--bs-atext-size': a.size ? a.size + 'px' : undefined, '--bs-atext-color': a.color || undefined, '--bs-atext-accent': a.accent || undefined, '--bs-atext-accent2': a.accent2 || undefined, '--bs-atext-weight': a.weight || undefined };
		return el(Fragment, null,
			el(be.InspectorControls, null,
				el(c.PanelBody, { title: __('Animated Text', T) },
					control(props, 'effect', __('Effect', T), 'select', EFFECTS),
					el(c.TextareaControl, { label: __('Text', T), value: a.text, onChange: function (v) { props.setAttributes({ text: v }); } }),
					el(c.TextareaControl, { label: usesWords ? __('Rotating words (one per line)', T) : __('Highlighted word (optional)', T), help: usesWords ? __('Shown one after another after the text, e.g. websites / online shops / brands.', T) : __('Added after the text.', T), value: a.words, onChange: function (v) { props.setAttributes({ words: v }); } }),
					control(props, 'after', __('Text after (optional)', T)),
					control(props, 'tag', __('HTML tag', T), 'select', [['h1', 'H1'], ['h2', 'H2'], ['h3', 'H3'], ['h4', 'H4'], ['p', __('Paragraph', T)], ['div', 'div']]),
					control(props, 'speed', __('Speed', T), 'range', [0.25, 4, 0.25]),
					usesWords || fx === 'typewriter' ? control(props, 'loop', __('Repeat', T), 'toggle') : null,
					control(props, 'label', __('Accessible label (optional)', T))
				),
				el(c.PanelBody, { title: __('Typography', T), initialOpen: false },
					control(props, 'size', __('Font size – desktop (px, 0 = theme)', T), 'range', [0, 240]),
					control(props, 'sizeTablet', __('Font size – tablet', T), 'range', [0, 240]),
					control(props, 'sizeMobile', __('Font size – mobile', T), 'range', [0, 240]),
					control(props, 'weight', __('Weight', T), 'select', [['', __('Theme', T)], ['400', '400'], ['500', '500'], ['600', '600'], ['700', '700'], ['800', '800'], ['900', '900']]),
					control(props, 'align', __('Alignment', T), 'select', [['', __('Default', T)], ['left', __('Left', T)], ['center', __('Center', T)], ['right', __('Right', T)]]),
					colorRow(props, 'color', __('Text color', T)), colorRow(props, 'accent', __('Accent color', T)), colorRow(props, 'accent2', __('Second gradient color', T))
				)
			),
			el(a.tag === 'p' || a.tag === 'div' ? a.tag : a.tag, be.useBlockProps({ className: 'bs-atext bs-atext--' + fx + ' is-static' + (a.align ? ' has-text-align-' + a.align : ''), style: style }),
				shown.trim() ? el('span', { className: 'bs-atext__text' }, shown) : el('span', { className: 'bst-motion-empty' }, __('Write your text in the block settings →', T)))
		);
	}, save: function () { return null; } });

	wp.blocks.registerBlockType('bigspy/scroll-story', { edit: function (props) {
		return el(Fragment, null,
			el(be.InspectorControls, null, el(c.PanelBody, { title: __('Scroll Story', T) },
				control(props, 'mediaSide', __('Image side', T), 'select', [['right', __('Right', T)], ['left', __('Left', T)]]),
				control(props, 'transition', __('Image change', T), 'select', [['fade', __('Fade', T)], ['slide', __('Slide up', T)], ['zoom', __('Zoom', T)]]),
				control(props, 'ratio', __('Image ratio', T), 'select', [['4-5', '4:5'], ['1-1', '1:1'], ['3-4', '3:4'], ['4-3', '4:3'], ['16-9', '16:9']]),
				control(props, 'stickyTop', __('Distance from the top while sticky (px)', T), 'range', [0, 400]),
				control(props, 'radius', __('Image corner radius', T), 'range', [0, 80]),
				control(props, 'gap', __('Gap', T), 'range', [0, 160]),
				control(props, 'progress', __('Progress dots', T), 'toggle'),
				control(props, 'label', __('Accessible label', T))
			)),
			el('div', be.useBlockProps({ className: 'bst-motion-editor bst-story-editor' }),
				el(c.Notice, { status: 'info', isDismissible: false }, __('Each step has its own image (step settings). On the site, the image stays in view and changes as each step scrolls past.', T)),
				el(be.InnerBlocks, { allowedBlocks: ['bigspy/story-step'], template: [['bigspy/story-step'], ['bigspy/story-step'], ['bigspy/story-step']] }))
		);
	}, save: save });

	wp.blocks.registerBlockType('bigspy/story-step', { edit: function (props) {
		var a = props.attributes;
		return el(Fragment, null,
			el(be.InspectorControls, null, el(c.PanelBody, { title: __('Story step', T) },
				el(be.MediaUploadCheck, null, el(be.MediaUpload, { allowedTypes: ['image'], value: a.mediaId, onSelect: function (m) { props.setAttributes({ mediaId: m.id, mediaUrl: m.url, mediaAlt: m.alt || '' }); }, render: function (p) { return el(c.Button, { variant: 'secondary', onClick: p.open }, a.mediaUrl ? __('Replace image', T) : __('Choose image', T)); } })),
				a.mediaUrl ? el(c.Button, { isDestructive: true, variant: 'link', onClick: function () { props.setAttributes({ mediaId: 0, mediaUrl: '', mediaAlt: '' }); } }, __('Remove image', T)) : null
			)),
			el('div', be.useBlockProps({ className: 'bst-story-step' }),
				el('div', { className: 'bst-story-step__thumb', style: a.mediaUrl ? { backgroundImage: 'url("' + a.mediaUrl + '")' } : {} }, a.mediaUrl ? null : __('No image', T)),
				el('div', { className: 'bst-story-step__body' }, el(be.InnerBlocks, { template: [['bst/heading', { tag: 'h3', text: __('Step title', T) }], ['bst/text', { html: '<p>' + __('Describe this step.', T) + '</p>' }]] })))
		);
	}, save: save });

	wp.blocks.registerBlockType('bigspy/stack-cards', { edit: function (props) {
		return el(Fragment, null,
			el(be.InspectorControls, null, el(c.PanelBody, { title: __('Stacking Cards', T) },
				control(props, 'top', __('Distance from the top (px)', T), 'range', [0, 400]),
				control(props, 'step', __('Offset between stacked cards (px)', T), 'range', [0, 80]),
				control(props, 'minHeight', __('Card minimum height (px)', T), 'range', [120, 1200]),
				control(props, 'radius', __('Corner radius', T), 'range', [0, 80]),
				control(props, 'gap', __('Space between cards', T), 'range', [0, 200]),
				control(props, 'shadow', __('Shadow', T), 'toggle'),
				control(props, 'scaleEffect', __('Shrink and dim cards underneath', T), 'toggle'),
				control(props, 'label', __('Accessible label', T))
			)),
			el('div', be.useBlockProps({ className: 'bst-motion-editor bst-stack-editor' }),
				el(c.Notice, { status: 'info', isDismissible: false }, __('Cards are listed here one under another. On the site they stack as visitors scroll.', T)),
				el(be.InnerBlocks, { allowedBlocks: ['bigspy/stack-card'], template: [['bigspy/stack-card'], ['bigspy/stack-card'], ['bigspy/stack-card']] }))
		);
	}, save: save });

	wp.blocks.registerBlockType('bigspy/stack-card', { edit: function (props) {
		var a = props.attributes;
		return el(Fragment, null,
			el(be.InspectorControls, null, el(c.PanelBody, { title: __('Stack card', T) }, colorRow(props, 'background', __('Card background', T)), colorRow(props, 'textColor', __('Text color', T)))),
			el('div', be.useBlockProps({ className: 'bst-stack-card', style: { background: a.background || '#fff', color: a.textColor || undefined } }),
				el(be.InnerBlocks, { template: [['bst/heading', { tag: 'h3', text: __('Card title', T) }], ['bst/text', { html: '<p>' + __('Card content.', T) + '</p>' }]] }))
		);
	}, save: save });
}(window.wp));
