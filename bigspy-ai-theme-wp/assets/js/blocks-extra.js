/* BIGSPY AI Theme WP – editor for Hotspot, Popup, Logos, Widget area, Shortcode and Payment icons. */
(function (wp) {
	var B = window.bstEditor;
	var el = wp.element.createElement;
	var Fragment = wp.element.Fragment;
	var useState = wp.element.useState;
	var __ = wp.i18n.__;
	var be = wp.blockEditor;
	var c = wp.components;
	var T = 'bigspy-ai-theme-wp';

	var preview = function (name, props) {
		return el('div', be.useBlockProps({ className: 'bst-ssr-preview' }), el(wp.serverSideRender, { block: name, attributes: props.attributes, httpMethod: 'POST' }));
	};
	var reg = function (name, edit, save) {
		wp.blocks.registerBlockType(name, { edit: edit, save: save || function () { return null; } });
	};
	var choice = function (props, label, key, list) {
		return el(c.SelectControl, { label: label, value: props.attributes[key], options: list.map(function (o) { return { value: o[0], label: o[1] }; }), onChange: function (v) { var o = {}; o[key] = v; props.setAttributes(o); }, __nextHasNoMarginBottom: true });
	};
	var range = function (props, label, key, min, max, step) {
		return el(c.RangeControl, { label: label, value: props.attributes[key], min: min, max: max, step: step || 1, onChange: function (v) { var o = {}; o[key] = v; props.setAttributes(o); }, __nextHasNoMarginBottom: true });
	};
	var toggle = function (props, label, key) {
		return el(c.ToggleControl, { label: label, checked: !!props.attributes[key], onChange: function (v) { var o = {}; o[key] = v; props.setAttributes(o); }, __nextHasNoMarginBottom: true });
	};
	var text = function (props, label, key, help) {
		return el(c.TextControl, { label: label, help: help, value: props.attributes[key] || '', onChange: function (v) { var o = {}; o[key] = v; props.setAttributes(o); }, __nextHasNoMarginBottom: true });
	};

	/* ---------------------------------------------------------------- Hotspot: click the image to add a point */
	reg('bst/hotspot', function (props) {
		var a = props.attributes;
		var sel = useState(0);
		var points = a.points || [];
		var setPoints = function (next) { props.setAttributes({ points: next }); };
		var update = function (i, key, v) {
			setPoints(points.map(function (p, j) { if (j !== i) { return p; } var n = Object.assign({}, p); n[key] = v; return n; }));
		};
		var addAt = function (e) {
			if (!e.target.classList.contains('bs-hotspot__img')) {
				return;
			}
			var r = e.target.getBoundingClientRect();
			var x = Math.round((e.clientX - r.left) / r.width * 1000) / 10;
			var y = Math.round((e.clientY - r.top) / r.height * 1000) / 10;
			setPoints(points.concat([{ x: x, y: y, title: __('New point', T), text: '', link: '' }]));
			sel[1](points.length);
		};
		var cur = points[sel[0]];
		return el(Fragment, null,
			el(be.InspectorControls, null,
				el(c.PanelBody, { title: __('Image', T) },
					el(B.MediaPicker, { id: a.imageId, url: a.imageUrl, onChange: function (m) { props.setAttributes({ imageId: m.id, imageUrl: m.url, alt: m.alt || a.alt }); } }),
					text(props, __('Alt text', T), 'alt'),
					range(props, __('Corner radius (px)', T), 'radius', 0, 60)
				),
				el(c.PanelBody, { title: __('Points', T) + ' (' + points.length + ')' },
					el('p', { className: 'description' }, __('Click on the image to add a point. Drag sliders to move it.', T)),
					points.length ? el('div', { className: 'bst-hs-tabs' }, points.map(function (p, i) {
						return el(c.Button, { key: i, size: 'small', variant: i === sel[0] ? 'primary' : 'secondary', onClick: function () { sel[1](i); } }, String(i + 1));
					})) : null,
					cur ? el('div', { className: 'bst-hs-edit' },
						el(c.TextControl, { label: __('Title', T), value: cur.title || '', onChange: function (v) { update(sel[0], 'title', v); }, __nextHasNoMarginBottom: true }),
						el(c.TextareaControl, { label: __('Text', T), value: cur.text || '', onChange: function (v) { update(sel[0], 'text', v); }, __nextHasNoMarginBottom: true }),
						el(c.TextControl, { label: __('Link (optional)', T), value: cur.link || '', onChange: function (v) { update(sel[0], 'link', v); }, __nextHasNoMarginBottom: true }),
						cur.link ? el(c.TextControl, { label: __('Link text', T), value: cur.linkText || '', placeholder: __('View', T), onChange: function (v) { update(sel[0], 'linkText', v); }, __nextHasNoMarginBottom: true }) : null,
						el(c.RangeControl, { label: __('Left (%)', T), value: cur.x, min: 0, max: 100, step: 0.5, onChange: function (v) { update(sel[0], 'x', v); }, __nextHasNoMarginBottom: true }),
						el(c.RangeControl, { label: __('Top (%)', T), value: cur.y, min: 0, max: 100, step: 0.5, onChange: function (v) { update(sel[0], 'y', v); }, __nextHasNoMarginBottom: true }),
						el(c.Button, { variant: 'link', isDestructive: true, onClick: function () { setPoints(points.filter(function (p, j) { return j !== sel[0]; })); sel[1](0); } }, __('Delete this point', T))
					) : null
				),
				el(c.PanelBody, { title: __('Style', T), initialOpen: false },
					choice(props, __('Marker', T), 'marker', [['pulse', __('Pulsing dot', T)], ['plus', __('Plus', T)], ['number', __('Numbers', T)], ['dot', __('Simple dot', T)]]),
					choice(props, __('Open tooltip on', T), 'trigger', [['hover', __('Hover (click on phones)', T)], ['click', __('Click', T)]]),
					range(props, __('Tooltip width (px)', T), 'tipWidth', 140, 420, 10),
					el(B.ColorPanel, { title: __('Marker color', T), settings: [{ label: __('Color', T), value: a.color, onChange: function (v) { props.setAttributes({ color: v || '' }); } }] })
				)
			),
			el('div', be.useBlockProps({ className: 'bs-hotspot bs-hotspot--' + a.marker + ' bst-hotspot-editor', style: { '--bs-hs-color': a.color || undefined, '--bs-hs-radius': a.radius + 'px' }, onClick: addAt }),
				a.imageUrl ? el('img', { className: 'bs-hotspot__img', src: a.imageUrl, alt: '' }) : el(c.Placeholder, { icon: 'location', label: __('Image hotspots', T), instructions: __('Pick an image in the sidebar, then click on it to add points.', T) }),
				points.map(function (p, i) {
					return el('div', { key: i, className: 'bs-hotspot__point' + (i === sel[0] ? ' is-selected' : ''), style: { left: p.x + '%', top: p.y + '%' } },
						el('button', { type: 'button', className: 'bs-hotspot__marker', onClick: function (e) { e.stopPropagation(); sel[1](i); } }, el('span', null, a.marker === 'number' ? String(i + 1) : (a.marker === 'plus' ? '+' : ''))));
				})
			)
		);
	});

	/* ---------------------------------------------------------------- Popup */
	reg('bst/popup', function (props) {
		var a = props.attributes;
		wp.element.useEffect(function () {
			if (!a.popupId) {
				props.setAttributes({ popupId: 'popup-' + Math.random().toString(36).slice(2, 7) });
			}
		}, []);
		var inner = be.useInnerBlocksProps({ className: 'bst-popup-editor__body' }, { template: [['bst/heading', { text: __('Nhận ưu đãi 10%', T), tag: 'h3' }], ['bst/text', { html: '<p>' + __('Để lại số điện thoại, tiệm gọi lại tư vấn trong giờ làm việc.', T) + '</p>' }], ['bst/buttons', {}]] });
		var trig = a.trigger;
		return el(Fragment, null,
			el(be.InspectorControls, null,
				el(c.PanelBody, { title: __('Open the popup', T) },
					choice(props, __('Trigger', T), 'trigger', [['button', __('Button here', T)], ['link', __('Any link to #popup-id', T)], ['delay', __('After a delay', T)], ['scroll', __('After scrolling', T)], ['exit', __('When leaving the page (desktop)', T)]]),
					trig === 'button' ? text(props, __('Button text', T), 'buttonText') : null,
					trig === 'delay' ? range(props, __('Delay (seconds)', T), 'delay', 0, 120) : null,
					trig === 'scroll' ? range(props, __('Scrolled down (%)', T), 'scroll', 5, 100, 5) : null,
					text(props, __('Popup ID', T), 'popupId', __('Link any button or menu item to #', T) + a.popupId + __(' to open this popup.', T)),
					trig !== 'button' && trig !== 'link' ? range(props, __('Show again after (days, 0 = every visit)', T), 'frequency', 0, 365) : null
				),
				el(c.PanelBody, { title: __('Look', T), initialOpen: false },
					choice(props, __('Size', T), 'size', [['sm', __('Small (420px)', T)], ['md', __('Medium (640px)', T)], ['lg', __('Large (960px)', T)], ['full', __('Full screen', T)]]),
					choice(props, __('Position', T), 'position', [['center', __('Center', T)], ['bottom-right', __('Bottom right (slide-in)', T)], ['bottom-left', __('Bottom left (slide-in)', T)], ['side-right', __('Side panel right', T)], ['top', __('Top bar', T)]]),
					range(props, __('Overlay darkness (%)', T), 'overlay', 0, 95, 5),
					range(props, __('Corner radius (px)', T), 'radius', 0, 40),
					toggle(props, __('Close when clicking outside', T), 'closeOutside'),
					el(B.ColorPanel, { title: __('Background', T), settings: [{ label: __('Popup background', T), value: a.bgColor, onChange: function (v) { props.setAttributes({ bgColor: v || '' }); } }] })
				)
			),
			el('div', be.useBlockProps({ className: 'bst-popup-editor' }),
				el('div', { className: 'bst-popup-editor__bar' }, el('span', { className: 'dashicons dashicons-admin-comments' }), ' ', __('Popup', T), ' · #' + a.popupId + ' · ', ({ button: __('opens from its button', T), link: __('opens from #links', T), delay: __('opens after ', T) + a.delay + 's', scroll: __('opens at ', T) + a.scroll + '% scroll', exit: __('opens on exit intent', T) })[trig]),
				el('div', { className: 'bst-popup-editor__frame', style: { borderRadius: a.radius + 'px', background: a.bgColor || undefined } }, el('div', inner))
			)
		);
	}, function () { return el(be.InnerBlocks.Content); });

	/* ---------------------------------------------------------------- Logos */
	reg('bst/logos', function (props) {
		var a = props.attributes;
		var imgs = a.images || [];
		return el(Fragment, null,
			el(be.InspectorControls, null,
				el(c.PanelBody, { title: __('Logos', T) },
					el(be.MediaUploadCheck, null, el(be.MediaUpload, { multiple: true, gallery: false, allowedTypes: ['image'], value: imgs.map(function (i) { return i.id; }), onSelect: function (list) { props.setAttributes({ images: list.map(function (m) { var old = imgs.filter(function (i) { return i.id === m.id; })[0] || {}; return { id: m.id, url: m.url, alt: m.alt || '', link: old.link || '' }; }) }); }, render: function (o) { return el(c.Button, { variant: 'secondary', onClick: o.open }, imgs.length ? __('Change logos', T) : __('Select logos', T)); } })),
					imgs.map(function (img, i) {
						return el(c.TextControl, { key: img.id || i, label: __('Link for logo', T) + ' ' + (i + 1), value: img.link || '', onChange: function (v) { props.setAttributes({ images: imgs.map(function (x, j) { return j === i ? Object.assign({}, x, { link: v }) : x; }) }); }, __nextHasNoMarginBottom: true });
					})
				),
				el(c.PanelBody, { title: __('Layout', T) },
					choice(props, __('Layout', T), 'layout', [['grid', __('Grid', T)], ['marquee', __('Endless scrolling strip', T)]]),
					a.layout === 'grid' ? range(props, __('Columns', T), 'columns', 1, 8) : range(props, __('Scroll duration (seconds, lower = faster)', T), 'speed', 5, 120),
					a.layout === 'grid' ? range(props, __('Columns on phones', T), 'columnsMobile', 1, 4) : null,
					range(props, __('Logo height (px)', T), 'height', 16, 200),
					range(props, __('Gap (px)', T), 'gap', 0, 120, 2),
					toggle(props, __('Gray, color on hover', T), 'grayscale'),
					toggle(props, __('Each logo in a box', T), 'boxed')
				)
			),
			imgs.length ? preview('bst/logos', props) : el('div', be.useBlockProps(), el(c.Placeholder, { icon: 'images-alt2', label: __('Logo showcase', T), instructions: __('Select logos in the sidebar.', T) }))
		);
	});

	/* ---------------------------------------------------------------- Widget area */
	reg('bst/widget-area', function (props) {
		var areas = (window.bstWidgetAreas || []).map(function (s) { return [s.id, s.name]; });
		return el(Fragment, null,
			el(be.InspectorControls, null, el(c.PanelBody, { title: __('Widget area', T) }, choice(props, __('Show', T), 'area', areas.length ? areas : [['sidebar-blog', 'Blog sidebar']]), el('p', { className: 'description' }, __('Add or reorder widgets in Appearance → Widgets.', T)))),
			preview('bst/widget-area', props)
		);
	});

	/* ---------------------------------------------------------------- Shortcode */
	reg('bst/shortcode', function (props) {
		var a = props.attributes;
		return el(Fragment, null,
			el(be.InspectorControls, null, el(c.PanelBody, { title: __('Shortcode', T) }, el(c.TextareaControl, { label: __('Shortcode', T), value: a.code, placeholder: '[contact-form-7 id="123"]', onChange: function (v) { props.setAttributes({ code: v }); }, __nextHasNoMarginBottom: true }))),
			a.code ? preview('bst/shortcode', props) : el('div', be.useBlockProps(), el(c.Placeholder, { icon: 'shortcode', label: __('Shortcode', T) }, el(c.TextControl, { value: a.code, placeholder: '[shortcode]', onChange: function (v) { props.setAttributes({ code: v }); }, __nextHasNoMarginBottom: true })))
		);
	});

	/* ---------------------------------------------------------------- Payment icons */
	var PAY = [['visa', 'Visa'], ['mastercard', 'Mastercard'], ['jcb', 'JCB'], ['amex', 'American Express'], ['paypal', 'PayPal'], ['momo', 'MoMo'], ['zalopay', 'ZaloPay'], ['vnpay', 'VNPAY'], ['shopeepay', 'ShopeePay'], ['vietqr', 'VietQR'], ['bank', __('Bank transfer', T)], ['cod', 'COD'], ['applepay', 'Apple Pay'], ['googlepay', 'Google Pay']];
	reg('bst/payment-icons', function (props) {
		var a = props.attributes;
		var list = a.methods || [];
		return el(Fragment, null,
			el(be.InspectorControls, null,
				el(c.PanelBody, { title: __('Payment methods', T) }, PAY.map(function (p) {
					return el(c.CheckboxControl, { key: p[0], label: p[1], checked: list.indexOf(p[0]) !== -1, onChange: function (v) { props.setAttributes({ methods: v ? list.concat([p[0]]) : list.filter(function (x) { return x !== p[0]; }) }); }, __nextHasNoMarginBottom: true });
				})),
				el(c.PanelBody, { title: __('Look', T) },
					choice(props, __('Style', T), 'style', [['color', __('Brand colors', T)], ['outline', __('Outline', T)], ['mono', __('Gray', T)]]),
					choice(props, __('Alignment', T), 'align', [['left', __('Left', T)], ['center', __('Center', T)], ['right', __('Right', T)]]),
					range(props, __('Height (px)', T), 'size', 20, 64)
				)
			),
			preview('bst/payment-icons', props)
		);
	});
})(window.wp);
