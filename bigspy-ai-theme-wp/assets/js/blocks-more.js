/* BIGSPY AI Theme WP – editor for Marquee, Before/after, Flip box, Timeline, Table, Pricing toggle,
   Lottie, Audio, Navigation menu and Banner grid. Previews are rendered by the server (render.php). */
(function (wp) {
	var B = window.bstEditor;
	var el = wp.element.createElement;
	var Fragment = wp.element.Fragment;
	var useState = wp.element.useState;
	var __ = wp.i18n.__;
	var be = wp.blockEditor;
	var c = wp.components;
	var T = 'bigspy-ai-theme-wp';
	var NB = { __nextHasNoMarginBottom: true };

	var set = function (props, key) { return function (v) { var o = {}; o[key] = v; props.setAttributes(o); }; };
	var text = function (props, label, key, help) { return el(c.TextControl, Object.assign({ label: label, help: help, value: props.attributes[key] || '', onChange: set(props, key) }, NB)); };
	var area = function (props, label, key, help, rows) { return el(c.TextareaControl, Object.assign({ label: label, help: help, rows: rows || 4, value: props.attributes[key] || '', onChange: set(props, key) }, NB)); };
	var range = function (props, label, key, min, max, step) { return el(c.RangeControl, Object.assign({ label: label, value: props.attributes[key], min: min, max: max, step: step || 1, onChange: set(props, key) }, NB)); };
	var toggle = function (props, label, key) { return el(c.ToggleControl, Object.assign({ label: label, checked: !!props.attributes[key], onChange: set(props, key) }, NB)); };
	var choice = function (props, label, key, list) {
		return el(c.SelectControl, Object.assign({ label: label, value: props.attributes[key], options: list.map(function (o) { return { value: o[0], label: o[1] }; }), onChange: set(props, key) }, NB));
	};
	var colors = function (props, list) {
		return el(B.ColorPanel, { title: __('Colors', T), settings: list.map(function (it) { return { label: it[0], value: props.attributes[it[1]], onChange: function (v) { set(props, it[1])(v || ''); } }; }) });
	};
	var media = function (props, idKey, urlKey, label, type) {
		return el(B.MediaPicker, { label: label, id: props.attributes[idKey], url: props.attributes[urlKey], allowedTypes: type ? [type] : undefined, onChange: function (m) { var o = {}; o[idKey] = m.id || 0; o[urlKey] = m.url || ''; props.setAttributes(o); } });
	};
	var preview = function (name, props, extra) {
		return el('div', be.useBlockProps(extra || {}), el(wp.serverSideRender, { block: name, attributes: props.attributes, httpMethod: 'POST' }));
	};
	var reg = function (name, edit) { wp.blocks.registerBlockType(name, { edit: edit, save: function () { return null; } }); };

	/* Repeater for array attributes: one collapsible panel per item. */
	function Repeater(props) {
		var list = props.attributes[props.attr] || [];
		var open = useState(0);
		var update = function (next) { var o = {}; o[props.attr] = next; props.setAttributes(o); };
		var change = function (i, key, v) { update(list.map(function (it, j) { if (j !== i) { return it; } var n = Object.assign({}, it); n[key] = v; return n; })); };
		var move = function (i, d) { var n = list.slice(); var t = n[i]; n[i] = n[i + d]; n[i + d] = t; update(n); open[1](i + d); };
		return el('div', { className: 'bst-repeater' },
			list.map(function (it, i) {
				var isOpen = open[0] === i;
				return el('div', { key: i, className: 'bst-repeater__item' + (isOpen ? ' is-open' : '') },
					el('div', { className: 'bst-repeater__head' },
						el(c.Button, { variant: 'tertiary', onClick: function () { open[1](isOpen ? -1 : i); } }, (i + 1) + '. ' + (String(it[props.titleKey] || '').replace(/<[^>]+>/g, '') || __('Item', T))),
						el(c.Button, { icon: 'arrow-up-alt2', label: __('Move up', T), disabled: i === 0, onClick: function () { move(i, -1); } }),
						el(c.Button, { icon: 'arrow-down-alt2', label: __('Move down', T), disabled: i === list.length - 1, onClick: function () { move(i, 1); } }),
						el(c.Button, { icon: 'trash', label: __('Remove', T), isDestructive: true, onClick: function () { update(list.filter(function (x, j) { return j !== i; })); } })
					),
					isOpen ? el('div', { className: 'bst-repeater__body' }, props.fields.map(function (f) {
						var v = it[f[0]];
						var on = function (val) { change(i, f[0], val); };
						if (f[2] === 'textarea') { return el(c.TextareaControl, Object.assign({ key: f[0], label: f[1], value: v || '', onChange: on, rows: 3 }, NB)); }
						if (f[2] === 'toggle') { return el(c.ToggleControl, Object.assign({ key: f[0], label: f[1], checked: !!v, onChange: on }, NB)); }
						if (f[2] === 'image') {
							return el(B.MediaPicker, { key: f[0], label: f[1], id: it.imageId, url: it.imageUrl, onChange: function (m) { var n = list.slice(); n[i] = Object.assign({}, it, { imageId: m.id || 0, imageUrl: m.url || '' }); update(n); } });
						}
						return el(c.TextControl, Object.assign({ key: f[0], label: f[1], value: v || '', onChange: on }, NB));
					})) : null
				);
			}),
			el(c.Button, { variant: 'secondary', icon: 'plus', onClick: function () { update(list.concat([Object.assign({}, props.blank)])); open[1](list.length); } }, props.addLabel || __('Add item', T))
		);
	}

	var ICONS = [['', __('None', T)], ['rocket', 'Rocket'], ['star', 'Star'], ['heart', 'Heart'], ['shield', 'Shield'], ['check', 'Check'], ['sparkles', 'Sparkles'], ['lightbulb', 'Lightbulb'], ['chart', 'Chart'], ['code', 'Code'], ['globe', 'Globe'], ['users', 'Users'], ['gift', 'Gift'], ['award', 'Award'], ['phone', 'Phone'], ['message', 'Message']];

	/* ---------------- Marquee */
	reg('bst/marquee', function (props) {
		return el(Fragment, null,
			el(be.InspectorControls, null,
				el(c.PanelBody, { title: __('Text', T) },
					area(props, __('Items (one per line)', T), 'items', __('Basic HTML like <strong> works.', T), 5),
					choice(props, __('Separator', T), 'separator', [['star', __('Star', T)], ['sparkles', __('Sparkle', T)], ['dot', __('Dot', T)], ['check', __('Check', T)], ['none', __('None', T)]]),
					toggle(props, __('Outlined text', T), 'outline')
				),
				el(c.PanelBody, { title: __('Motion & size', T), initialOpen: false },
					range(props, __('Seconds per loop (lower = faster)', T), 'speed', 5, 120),
					choice(props, __('Direction', T), 'direction', [['left', __('Right to left', T)], ['right', __('Left to right', T)]]),
					toggle(props, __('Pause on hover', T), 'pauseHover'),
					range(props, __('Font size (px)', T), 'size', 10, 120),
					choice(props, __('Weight', T), 'weight', [['400', '400'], ['500', '500'], ['600', '600'], ['700', '700'], ['800', '800'], ['900', '900']]),
					range(props, __('Space between items (px)', T), 'gap', 0, 160),
					range(props, __('Top / bottom padding (px)', T), 'paddingY', 0, 100)
				),
				colors(props, [[__('Text', T), 'color'], [__('Background', T), 'bgColor']])
			),
			preview('bst/marquee', props)
		);
	});

	/* ---------------- Before / after */
	reg('bst/before-after', function (props) {
		return el(Fragment, null,
			el(be.InspectorControls, null,
				el(c.PanelBody, { title: __('Images', T) },
					media(props, 'beforeId', 'beforeUrl', __('Before image', T), 'image'),
					media(props, 'afterId', 'afterUrl', __('After image', T), 'image'),
					el('p', { className: 'description' }, __('Use two images with the same size and framing.', T)),
					text(props, __('Alt text', T), 'alt')
				),
				el(c.PanelBody, { title: __('Labels & layout', T), initialOpen: false },
					text(props, __('Before label', T), 'beforeLabel'),
					text(props, __('After label', T), 'afterLabel'),
					range(props, __('Divider start position (%)', T), 'start', 0, 100),
					range(props, __('Corner radius (px)', T), 'radius', 0, 40)
				)
			),
			(props.attributes.beforeUrl && props.attributes.afterUrl) ? preview('bst/before-after', props) : el('div', be.useBlockProps({ className: 'bst-more-empty' }), __('Choose a before and an after image in the block settings.', T))
		);
	});

	/* ---------------- Flip box */
	reg('bst/flip-box', function (props) {
		var flip = useState(false);
		return el(Fragment, null,
			el(be.InspectorControls, null,
				el(c.PanelBody, { title: __('Front', T) },
					choice(props, __('Icon', T), 'icon', ICONS),
					text(props, __('Title', T), 'frontTitle'),
					area(props, __('Text', T), 'frontText', '', 3),
					media(props, 'frontImageId', 'frontImageUrl', __('Background image (optional)', T), 'image')
				),
				el(c.PanelBody, { title: __('Back', T) },
					text(props, __('Title', T), 'backTitle'),
					area(props, __('Text', T), 'backText', '', 3),
					text(props, __('Button text', T), 'buttonText'),
					text(props, __('Button link', T), 'buttonUrl')
				),
				el(c.PanelBody, { title: __('Layout', T), initialOpen: false },
					choice(props, __('Flip direction', T), 'direction', [['horizontal', __('Left / right', T)], ['vertical', __('Up / down', T)]]),
					choice(props, __('Text alignment', T), 'align', [['left', __('Left', T)], ['center', __('Center', T)], ['right', __('Right', T)]]),
					range(props, __('Height (px)', T), 'height', 160, 700, 10),
					range(props, __('Corner radius (px)', T), 'radius', 0, 40)
				),
				colors(props, [[__('Front background', T), 'frontBg'], [__('Front text', T), 'frontColor'], [__('Back background', T), 'backBg'], [__('Back text', T), 'backColor']])
			),
			el(be.BlockControls, null, el(c.ToolbarGroup, null, el(c.ToolbarButton, { icon: 'image-rotate', label: __('Show the other side', T), isPressed: flip[0], onClick: function () { flip[1](!flip[0]); } }))),
			preview('bst/flip-box', props, { className: flip[0] ? 'bst-flip-preview-back' : '' })
		);
	});

	/* ---------------- Timeline */
	reg('bst/timeline', function (props) {
		return el(Fragment, null,
			el(be.InspectorControls, null,
				el(c.PanelBody, { title: __('Milestones', T) },
					el(Repeater, { attributes: props.attributes, setAttributes: props.setAttributes, attr: 'items', titleKey: 'title', blank: { date: '', title: __('New milestone', T), text: '' }, addLabel: __('Add milestone', T),
						fields: [['date', __('Date / label', T)], ['title', __('Title', T)], ['text', __('Text', T), 'textarea'], ['icon', __('Icon name (when marker = icon)', T)]] })
				),
				el(c.PanelBody, { title: __('Style', T), initialOpen: false },
					choice(props, __('Layout', T), 'layout', [['alternate', __('Alternating left / right', T)], ['left', __('All on one side', T)]]),
					choice(props, __('Marker', T), 'marker', [['dot', __('Dot', T)], ['number', __('Number', T)], ['icon', __('Icon', T)]])
				),
				colors(props, [[__('Accent', T), 'accent']])
			),
			preview('bst/timeline', props)
		);
	});

	/* ---------------- Table */
	reg('bst/table', function (props) {
		return el(Fragment, null,
			el(be.InspectorControls, null,
				el(c.PanelBody, { title: __('Data', T) },
					area(props, __('Rows', T), 'data', __('One row per line. Separate cells with |. Paste from a spreadsheet and replace tabs with |.', T), 8),
					text(props, __('Caption (optional)', T), 'caption')
				),
				el(c.PanelBody, { title: __('Style', T), initialOpen: false },
					toggle(props, __('First row is a header', T), 'header'),
					toggle(props, __('First column is a header', T), 'firstColumn'),
					toggle(props, __('Striped rows', T), 'striped'),
					toggle(props, __('Borders', T), 'bordered'),
					choice(props, __('Text alignment', T), 'align', [['left', __('Left', T)], ['center', __('Center', T)], ['right', __('Right', T)]]),
					range(props, __('Font size (px)', T), 'size', 11, 22)
				),
				colors(props, [[__('Header color', T), 'accent']])
			),
			preview('bst/table', props)
		);
	});

	/* ---------------- Pricing toggle */
	reg('bst/pricing-toggle', function (props) {
		return el(Fragment, null,
			el(be.InspectorControls, null,
				el(c.PanelBody, { title: __('Switch', T) },
					text(props, __('First option', T), 'labelA'),
					text(props, __('Second option', T), 'labelB'),
					text(props, __('Badge on the second option', T), 'badgeB'),
					text(props, __('Period text – first', T), 'periodA'),
					text(props, __('Period text – second', T), 'periodB'),
					choice(props, __('Show first', T), 'start', [['a', __('First option', T)], ['b', __('Second option', T)]])
				),
				el(c.PanelBody, { title: __('Plans', T) },
					el(Repeater, { attributes: props.attributes, setAttributes: props.setAttributes, attr: 'plans', titleKey: 'title', blank: { title: __('New plan', T), priceA: '', priceB: '', desc: '', features: '', button: __('Choose', T), url: '', featured: false }, addLabel: __('Add plan', T),
						fields: [['title', __('Plan name', T)], ['priceA', __('Price – first option', T)], ['priceB', __('Price – second option', T)], ['desc', __('Short description', T)], ['features', __('Features (one per line)', T), 'textarea'], ['button', __('Button text', T)], ['url', __('Button link', T)], ['featured', __('Highlight this plan', T), 'toggle']] })
				),
				colors(props, [[__('Accent', T), 'accent']])
			),
			preview('bst/pricing-toggle', props)
		);
	});

	/* ---------------- Lottie */
	reg('bst/lottie', function (props) {
		var a = props.attributes;
		return el(Fragment, null,
			el(be.InspectorControls, null,
				el(c.PanelBody, { title: __('Animation', T) },
					text(props, __('Lottie JSON URL', T), 'src', __('Paste a .json link (e.g. from LottieFiles). Upload JSON files to Media first if your site allows it.', T)),
					toggle(props, __('Loop', T), 'loop'),
					toggle(props, __('Play automatically', T), 'autoplay'),
					toggle(props, __('Play only on hover', T), 'hover'),
					range(props, __('Speed', T), 'speed', 0.1, 3, 0.1),
					range(props, __('Max width (px)', T), 'width', 40, 1200, 10),
					choice(props, __('Alignment', T), 'align', [['left', __('Left', T)], ['center', __('Center', T)], ['right', __('Right', T)]]),
					text(props, __('Description for screen readers (optional)', T), 'label')
				)
			),
			el('div', be.useBlockProps({ className: 'bst-more-empty bst-lottie-editor' }),
				a.src ? el('span', null, '▶ ' + __('Lottie animation', T) + ': ' + a.src.split('/').pop()) : __('Paste a Lottie JSON URL in the block settings.', T),
				el('small', null, __('The animation plays on the published page.', T)))
		);
	});

	/* ---------------- Audio */
	reg('bst/audio', function (props) {
		return el(Fragment, null,
			el(be.InspectorControls, null,
				el(c.PanelBody, { title: __('Audio', T) },
					media(props, 'fileId', 'fileUrl', __('Audio file', T), 'audio'),
					text(props, __('Or audio URL', T), 'fileUrl'),
					text(props, __('Title', T), 'title'),
					text(props, __('Artist / show', T), 'artist'),
					media(props, 'coverId', 'coverUrl', __('Cover image', T), 'image')
				),
				el(c.PanelBody, { title: __('Style', T), initialOpen: false },
					choice(props, __('Style', T), 'style', [['card', __('Card', T)], ['minimal', __('Minimal', T)], ['dark', __('Dark', T)]])
				),
				colors(props, [[__('Accent', T), 'accent']])
			),
			props.attributes.fileUrl ? preview('bst/audio', props) : el('div', be.useBlockProps({ className: 'bst-more-empty' }), __('Choose an audio file in the block settings.', T))
		);
	});

	/* ---------------- Navigation menu */
	reg('bst/nav-menu', function (props) {
		var menus = wp.data.useSelect(function (select) { return select('core').getEntityRecords('root', 'menu', { per_page: 100 }) || []; }, []);
		return el(Fragment, null,
			el(be.InspectorControls, null,
				el(c.PanelBody, { title: __('Menu', T) },
					el(c.SelectControl, Object.assign({ label: __('Menu', T), value: props.attributes.menuId, options: [{ value: 0, label: __('First menu', T) }].concat(menus.map(function (m) { return { value: m.id, label: m.name }; })), onChange: function (v) { props.setAttributes({ menuId: parseInt(v, 10) || 0 }); } }, NB)),
					el('p', { className: 'description' }, el('a', { href: (window.ajaxurl || '/wp-admin/admin-ajax.php').replace('admin-ajax.php', 'nav-menus.php'), target: '_blank' }, __('Edit menus', T))),
					choice(props, __('Layout', T), 'layout', [['horizontal', __('Horizontal', T)], ['vertical', __('Vertical (sidebar)', T)]]),
					choice(props, __('Alignment', T), 'align', [['left', __('Left', T)], ['center', __('Center', T)], ['right', __('Right', T)]]),
					choice(props, __('Style', T), 'style', [['plain', __('Plain', T)], ['underline', __('Underline on hover', T)], ['pill', __('Pill on hover', T)], ['boxed', __('Boxed items', T)]]),
					choice(props, __('On phones', T), 'mobile', [['toggle', __('Collapse behind a Menu button', T)], ['stack', __('Stack items', T)], ['scroll', __('Scroll sideways', T)]]),
					range(props, __('Space between items (px)', T), 'gap', 0, 60),
					range(props, __('Font size (px, 0 = default)', T), 'size', 0, 32)
				),
				colors(props, [[__('Link', T), 'color'], [__('Hover', T), 'hover']])
			),
			preview('bst/nav-menu', props)
		);
	});

	/* ---------------- Banner grid */
	reg('bst/banner-grid', function (props) {
		return el(Fragment, null,
			el(be.InspectorControls, null,
				el(c.PanelBody, { title: __('Banners', T) },
					el(Repeater, { attributes: props.attributes, setAttributes: props.setAttributes, attr: 'items', titleKey: 'title', blank: { imageUrl: '', imageId: 0, title: __('New banner', T), subtitle: '', link: '' }, addLabel: __('Add banner', T),
						fields: [['image', __('Image', T), 'image'], ['title', __('Title', T)], ['subtitle', __('Small text above', T)], ['link', __('Link', T)]] })
				),
				el(c.PanelBody, { title: __('Layout', T), initialOpen: false },
					choice(props, __('Grid', T), 'layout', [['1-2', __('1 large + 2 small (right)', T)], ['2-1', __('2 small + 1 large (right)', T)], ['1-3', __('1 large + 3 small', T)], ['2x2', __('2 × 2', T)], ['row', __('One row', T)], ['mosaic', __('Mosaic (5)', T)]]),
					range(props, __('Height (px)', T), 'height', 160, 900, 10),
					range(props, __('Gap (px)', T), 'gap', 0, 40),
					range(props, __('Corner radius (px)', T), 'radius', 0, 40),
					range(props, __('Dark overlay (%)', T), 'overlay', 0, 90),
					choice(props, __('Hover effect', T), 'hover', [['zoom', __('Zoom image', T)], ['lift', __('Lift', T)], ['none', __('None', T)]])
				)
			),
			preview('bst/banner-grid', props)
		);
	});
})(window.wp);
