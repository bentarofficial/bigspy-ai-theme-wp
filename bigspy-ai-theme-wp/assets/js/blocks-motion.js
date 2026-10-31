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
					control(props, 'alwaysMove', __('Keep moving when the device asks for reduced motion', T), 'toggle'), control(props, 'pauseHover', __('Pause on hover / touch', T), 'toggle'), control(props, 'fadeEdges', __('Fade edges', T), 'toggle')
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
	/* ------------------------------------------------ Motion wave 2 */
	wp.blocks.registerBlockType('bigspy/hover-reveal', { edit: function (props) {
		return el(Fragment, null,
			el(be.InspectorControls, null, el(c.PanelBody, { title: __('Hover Reveal List', T) },
				control(props, 'size', __('Text size (px, 0 = automatic)', T), 'range', [0, 160]),
				control(props, 'imageWidth', __('Hover image width (px)', T), 'range', [120, 600]),
				control(props, 'divider', __('Lines between rows', T), 'toggle'),
				control(props, 'label', __('Accessible label', T))
			)),
			el('div', be.useBlockProps({ className: 'bst-motion-editor bst-hreveal-editor' }),
				el(be.InnerBlocks, { allowedBlocks: ['bigspy/hover-reveal-item'], template: [['bigspy/hover-reveal-item', { title: __('Web design', T), meta: '2026' }], ['bigspy/hover-reveal-item', { title: __('SEO', T), meta: '2026' }], ['bigspy/hover-reveal-item', { title: __('Branding', T), meta: '2026' }]] }))
		);
	}, save: save });
	wp.blocks.registerBlockType('bigspy/hover-reveal-item', { edit: function (props) {
		var a = props.attributes;
		return el(Fragment, null,
			el(be.InspectorControls, null, el(c.PanelBody, { title: __('Hover Reveal row', T) },
				control(props, 'title', __('Title', T)), control(props, 'meta', __('Small text on the right', T)), control(props, 'link', __('Link (URL)', T)),
				el(be.MediaUploadCheck, null, el(be.MediaUpload, { allowedTypes: ['image'], value: a.imageId, onSelect: function (m) { props.setAttributes({ imageId: m.id, imageUrl: m.url }); }, render: function (p) { return el(c.Button, { variant: 'secondary', onClick: p.open }, a.imageUrl ? __('Replace image', T) : __('Choose image', T)); } }))
			)),
			el('div', be.useBlockProps({ className: 'bst-hreveal-row' }),
				a.imageUrl ? el('img', { src: a.imageUrl, alt: '' }) : el('span', { className: 'bst-hreveal-row__noimg' }),
				el(be.RichText, { tagName: 'strong', value: a.title, placeholder: __('Row title…', T), allowedFormats: [], onChange: function (v) { props.setAttributes({ title: v.replace(/<[^>]+>/g, '') }); } }),
				el('em', null, a.meta))
		);
	}, save: function () { return null; } });

	var SHAPES = [['wave', __('Wave', T)], ['curve', __('Curve', T)], ['arrow', __('Arrow', T)], ['loop', __('Loop', T)], ['zigzag', __('Zigzag', T)], ['underline', __('Underline', T)], ['custom', __('Custom SVG path', T)]];
	wp.blocks.registerBlockType('bigspy/line-draw', { edit: function (props) {
		var a = props.attributes;
		return el(Fragment, null,
			el(be.InspectorControls, null, el(c.PanelBody, { title: __('Line Draw', T) },
				control(props, 'shape', __('Shape', T), 'select', SHAPES),
				a.shape === 'custom' ? el(c.TextareaControl, { label: __('SVG path (d attribute, 400 × 100 box)', T), value: a.path, onChange: function (v) { props.setAttributes({ path: v }); } }) : null,
				control(props, 'mode', __('Drawing', T), 'select', [['view', __('Once, when it comes into view', T)], ['scroll', __('Follows the scroll', T)]]),
				control(props, 'duration', __('Drawing time (seconds)', T), 'range', [0.3, 6, 0.1]),
				control(props, 'width', __('Line width', T), 'range', [1, 20]),
				control(props, 'height', __('Height (px)', T), 'range', [20, 600]),
				control(props, 'flip', __('Mirror horizontally', T), 'toggle'),
				colorRow(props, 'color', __('Line color', T))
			)),
			el('div', be.useBlockProps({ className: 'bst-line-editor' }), el(wp.serverSideRender, { block: 'bigspy/line-draw', attributes: a }))
		);
	}, save: function () { return null; } });

	wp.blocks.registerBlockType('bigspy/horizontal-scroll', { edit: function (props) {
		return el(Fragment, null,
			el(be.InspectorControls, null, el(c.PanelBody, { title: __('Horizontal Scroll', T) },
				control(props, 'panelWidth', __('Panel width – desktop (% of screen)', T), 'range', [30, 100]),
				control(props, 'panelWidthMobile', __('Panel width – mobile (% of screen)', T), 'range', [50, 100]),
				control(props, 'gap', __('Gap', T), 'range', [0, 120]),
				control(props, 'label', __('Accessible label', T))
			)),
			el('div', be.useBlockProps({ className: 'bst-motion-editor bst-motion-editor--ticker' }),
				el(c.Notice, { status: 'info', isDismissible: false }, __('On the site, visitors scroll down and these panels slide sideways. On phones they become a swipeable row.', T)),
				el(be.InnerBlocks, { allowedBlocks: ['bigspy/hscroll-panel'], orientation: 'horizontal', template: [['bigspy/hscroll-panel'], ['bigspy/hscroll-panel'], ['bigspy/hscroll-panel']] }))
		);
	}, save: save });
	wp.blocks.registerBlockType('bigspy/hscroll-panel', { edit: function (props) {
		var a = props.attributes;
		return el(Fragment, null,
			el(be.InspectorControls, null, el(c.PanelBody, { title: __('Horizontal panel', T) }, colorRow(props, 'background', __('Background', T)), colorRow(props, 'textColor', __('Text color', T)))),
			el('div', be.useBlockProps({ className: 'bst-motion-slide', style: { background: a.background || undefined, color: a.textColor || undefined, minWidth: '260px', padding: '18px', borderRadius: '14px' } }),
				el(be.InnerBlocks, { template: [['bst/heading', { tag: 'h3', text: __('Panel title', T) }], ['bst/text', { html: '<p>' + __('Panel content.', T) + '</p>' }]] }))
		);
	}, save: save });

	wp.blocks.registerBlockType('bigspy/zoom-hero', { edit: function (props) {
		var a = props.attributes;
		return el(Fragment, null,
			el(be.InspectorControls, null, el(c.PanelBody, { title: __('Zoom Hero', T) },
				el(be.MediaUploadCheck, null, el(be.MediaUpload, { allowedTypes: ['image'], value: a.imageId, onSelect: function (m) { props.setAttributes({ imageId: m.id, imageUrl: m.url, imageAlt: m.alt || '' }); }, render: function (p) { return el(c.Button, { variant: 'secondary', onClick: p.open }, a.imageUrl ? __('Replace image', T) : __('Choose image', T)); } })),
				control(props, 'startInset', __('Starting frame margin (%)', T), 'range', [0, 40]),
				control(props, 'radius', __('Starting corner radius', T), 'range', [0, 80]),
				control(props, 'overlay', __('Dark overlay at the end (%)', T), 'range', [0, 90]),
				control(props, 'length', __('Scroll length (% of screen height)', T), 'range', [100, 400])
			)),
			el('div', be.useBlockProps({ className: 'bst-zhero-editor', style: { backgroundImage: a.imageUrl ? 'linear-gradient(rgba(0,0,0,' + (a.overlay / 100) + '),rgba(0,0,0,' + (a.overlay / 100) + ')),url("' + a.imageUrl + '")' : undefined } }),
				el(be.InnerBlocks, { template: [['bst/heading', { tag: 'h1', text: __('A bold opening headline', T), align: 'center', color: '#ffffff' }], ['bst/button', { text: __('Get started', T) }]] }))
		);
	}, save: save });


	/* ------------------------------------------------ Motion wave 3 */
	wp.blocks.registerBlockType('bigspy/video-cards', { edit: function (props) {
		return el(Fragment, null,
			el(be.InspectorControls, null, el(c.PanelBody, { title: __('Video Hover Cards', T) },
				control(props, 'columns', __('Columns – desktop', T), 'range', [1, 6]), control(props, 'columnsTablet', __('Columns – tablet', T), 'range', [1, 4]), control(props, 'columnsMobile', __('Columns – mobile', T), 'range', [1, 2]),
				control(props, 'ratio', __('Card ratio', T), 'select', [['4-5', '4:5'], ['1-1', '1:1'], ['3-4', '3:4'], ['16-9', '16:9'], ['9-16', '9:16']]),
				control(props, 'gap', __('Gap', T), 'range', [0, 80]), control(props, 'radius', __('Corner radius', T), 'range', [0, 60]), control(props, 'label', __('Accessible label', T))
			)),
			el('div', be.useBlockProps({ className: 'bst-motion-editor bst-vcards-editor', style: { '--bst-cols': props.attributes.columns } }),
				el(c.Notice, { status: 'info', isDismissible: false }, __('Use short, light MP4 clips (a few seconds, under about 3 MB). They load only when a visitor hovers or taps.', T)),
				el(be.InnerBlocks, { allowedBlocks: ['bigspy/video-card'], orientation: 'horizontal', template: [['bigspy/video-card'], ['bigspy/video-card'], ['bigspy/video-card']] }))
		);
	}, save: save });
	wp.blocks.registerBlockType('bigspy/video-card', { edit: function (props) {
		var a = props.attributes;
		var pick = function (type, idKey, urlKey, label) { return el(be.MediaUploadCheck, null, el(be.MediaUpload, { allowedTypes: [type], value: a[idKey], onSelect: function (m) { var o = {}; o[idKey] = m.id; o[urlKey] = m.url; props.setAttributes(o); }, render: function (p) { return el(c.Button, { variant: 'secondary', onClick: p.open, style: { marginBottom: '8px' } }, label); } })); };
		return el(Fragment, null,
			el(be.InspectorControls, null, el(c.PanelBody, { title: __('Video card', T) },
				pick('image', 'posterId', 'posterUrl', a.posterUrl ? __('Replace still image', T) : __('Choose still image', T)),
				pick('video', 'videoId', 'videoUrl', a.videoUrl ? __('Replace video', T) : __('Choose video (MP4)', T)),
				control(props, 'videoUrl', __('…or video link (MP4 URL)', T)),
				control(props, 'title', __('Title', T)), control(props, 'text', __('Short text', T)), control(props, 'link', __('Link (URL)', T))
			)),
			el('div', be.useBlockProps({ className: 'bst-vcard' }),
				el('div', { className: 'bst-vcard__media', style: a.posterUrl ? { backgroundImage: 'url("' + a.posterUrl + '")' } : {} }, a.videoUrl ? el('span', { className: 'bst-vcard__badge' }, '▶ ' + __('Video', T)) : el('span', { className: 'bst-vcard__badge' }, __('No video yet', T))),
				el('strong', null, a.title || __('Card title', T)), a.text ? el('p', null, a.text) : null)
		);
	}, save: function () { return null; } });

	wp.blocks.registerBlockType('bigspy/bento', { edit: function (props) {
		return el(Fragment, null,
			el(be.InspectorControls, null, el(c.PanelBody, { title: __('Bento Grid', T) },
				control(props, 'rowHeight', __('Row height (px)', T), 'range', [80, 600]), control(props, 'gap', __('Gap', T), 'range', [0, 60]), control(props, 'radius', __('Corner radius', T), 'range', [0, 60]),
				control(props, 'glow', __('Light follows the mouse', T), 'toggle'), control(props, 'label', __('Accessible label', T))
			)),
			el('div', be.useBlockProps({ className: 'bst-motion-editor bst-bento-editor' }),
				el(be.InnerBlocks, { allowedBlocks: ['bigspy/bento-cell'], template: [['bigspy/bento-cell', { size: 'large' }], ['bigspy/bento-cell'], ['bigspy/bento-cell', { size: 'tall' }], ['bigspy/bento-cell'], ['bigspy/bento-cell', { size: 'wide' }]] }))
		);
	}, save: save });
	wp.blocks.registerBlockType('bigspy/bento-cell', { edit: function (props) {
		var a = props.attributes;
		return el(Fragment, null,
			el(be.InspectorControls, null, el(c.PanelBody, { title: __('Bento tile', T) },
				control(props, 'size', __('Tile size', T), 'select', [['small', __('Small (1 × 1)', T)], ['wide', __('Wide (2 × 1)', T)], ['tall', __('Tall (1 × 2)', T)], ['large', __('Large (2 × 2)', T)]]),
				colorRow(props, 'background', __('Background', T)), colorRow(props, 'textColor', __('Text color', T)),
				el(be.MediaUploadCheck, null, el(be.MediaUpload, { allowedTypes: ['image'], value: a.imageId, onSelect: function (m) { props.setAttributes({ imageId: m.id, imageUrl: m.url }); }, render: function (p) { return el(c.Button, { variant: 'secondary', onClick: p.open }, a.imageUrl ? __('Replace background image', T) : __('Choose background image', T)); } })),
				a.imageUrl ? el(c.Button, { variant: 'link', isDestructive: true, onClick: function () { props.setAttributes({ imageId: 0, imageUrl: '' }); } }, __('Remove image', T)) : null
			)),
			el('div', be.useBlockProps({ className: 'bst-bento-cell bst-bento-cell--' + a.size, style: { background: a.imageUrl ? 'linear-gradient(transparent 35%,rgba(0,0,0,.55)),url("' + a.imageUrl + '") center/cover' : (a.background || undefined), color: a.textColor || (a.imageUrl ? '#fff' : undefined) } }),
				el(be.InnerBlocks, { template: [['bst/heading', { tag: 'h3', text: __('Tile title', T) }], ['bst/text', { html: '<p>' + __('A short highlight.', T) + '</p>' }]] }))
		);
	}, save: save });

	/* "Advanced motion" panel for Section, Button, Image, Icon Box, Counter and Gallery. */
	var MX = {
		'bst/section': function (p) { return [control(p, 'mxBg', __('Moving background effect', T), 'select', [['', __('None', T)], ['grain', __('Film grain', T)], ['mesh', __('Gradient mesh', T)], ['aurora', __('Aurora', T)]]), control(p, 'mxGlass', __('Frosted glass cards (columns)', T), 'toggle'), control(p, 'mxSticky', __('Stick to the top while scrolling', T), 'toggle'), colorRow(p, 'mxScrollBg', __('Page background while this section is in view', T))]; },
		'bst/button': function (p) { return [control(p, 'mxButton', __('Button motion', T), 'select', [['', __('None', T)], ['magnetic', __('Magnetic (follows the mouse)', T)], ['arrow', __('Sliding arrow', T)], ['shine', __('Shine every few seconds', T)]])]; },
		'bst/image': function (p) { return [control(p, 'mxReveal', __('Image reveal', T), 'select', [['', __('None', T)], ['left', __('Curtain from the left', T)], ['right', __('Curtain from the right', T)], ['up', __('Curtain from the bottom', T)], ['circle', __('Circle opening', T)], ['kenburns', __('Slow zoom (Ken Burns)', T)]])]; },
		'bst/icon-box': function (p) { return [control(p, 'mxDraw', __('Icon draws itself', T), 'toggle')]; },
		'bst/counter': function (p) { return [control(p, 'mxScramble', __('Scramble digits like a flip board', T), 'toggle')]; },
		'bst/gallery': function (p) { return [control(p, 'mxStagger', __('Images appear one after another', T), 'toggle')]; }
	};
	wp.hooks.addFilter('editor.BlockEdit', 'bst/advanced-motion', wp.compose.createHigherOrderComponent(function (BlockEdit) {
		return function (props) {
			if (!MX[props.name] || props.attributes.bstPreview) { return el(BlockEdit, props); }
			return el(Fragment, null, el(BlockEdit, props), el(be.InspectorControls, null, el(c.PanelBody, { title: __('Advanced motion', T), initialOpen: false }, MX[props.name](props), el('p', { className: 'description' }, __('Plays on the site, not in the editor. Visitors who ask for less motion see the static version.', T)))));
		};
	}, 'withAdvancedMotion'));
}(window.wp));
