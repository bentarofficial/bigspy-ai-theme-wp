/* BIGSPY Theme – editor UI for all theme blocks (no build step required). */
(function (wp) {
	'use strict';

	var B = window.bstEditor;
	var el = B.el;
	var Fragment = B.Fragment;
	var __ = wp.i18n.__;
	var be = wp.blockEditor;
	var c = wp.components;
	var InnerBlocks = be.InnerBlocks;
	var Inspector = be.InspectorControls;
	var RichText = be.RichText;
	var useBlockProps = be.useBlockProps;
	var useInnerBlocksProps = be.useInnerBlocksProps;
	var useSelect = wp.data.useSelect;
	var useDispatch = wp.data.useDispatch;
	var SSR = wp.serverSideRender;
	var Panel = c.PanelBody;
	var Select = B.Select;
	var Range = B.Range;
	var Toggle = B.Toggle;
	var Text = B.Text;

	var saveInner = function () {
		return el(InnerBlocks.Content);
	};
	var saveNull = function () {
		return null;
	};

	function register(name, edit, save) {
		wp.blocks.registerBlockType('bst/' + name, { edit: edit, save: save || saveNull });
	}

	function styleVars(map) {
		var out = {};
		Object.keys(map).forEach(function (k) {
			if (map[k] !== '' && map[k] !== null && typeof map[k] !== 'undefined') {
				out[k] = map[k];
			}
		});
		return out;
	}

	/**
	 * Editable HTML preview (contentEditable). Markup is only pushed into the
	 * element when it changes from outside, so the caret never jumps while typing.
	 */
	function VisualHtml(props) {
		var ref = wp.element.useRef(null);
		wp.element.useEffect(function () {
			var node = ref.current;
			if (node && node.innerHTML !== (props.value || '') && node.ownerDocument.activeElement !== node) {
				node.innerHTML = props.value || '';
			}
		}, [props.value]);
		var sync = function (e) {
			props.onChange(e.currentTarget.innerHTML);
		};
		return el('div', {
			ref: ref,
			className: 'bst-code-editor__visual ' + (props.className || ''),
			contentEditable: true,
			suppressContentEditableWarning: true,
			role: 'textbox',
			'aria-multiline': 'true',
			'aria-label': props.label,
			onInput: sync,
			onBlur: sync
		});
	}

	function editorTabs(tabs, current, setTab, label) {
		if (!tabs.length) {
			return el('div', { className: 'bst-code-editor__tabs' }, el('span', { className: 'bst-code-editor__tab is-active' }, label));
		}
		return el('div', { className: 'bst-code-editor__tabs', role: 'tablist' },
			tabs.map(function (t) {
				return el('button', {
					key: t[0],
					type: 'button',
					role: 'tab',
					'aria-selected': current === t[0],
					className: 'bst-code-editor__tab' + (current === t[0] ? ' is-active' : ''),
					onClick: function () { setTab(t[0]); }
				}, t[1]);
			})
		);
	}

	/**
	 * Formatting toolbar for the Text element: paragraph / H1–H6, inline styles, lists, links, alignment.
	 */
	function FormatBar() {
		var run = function (command, value) {
			return function (e) {
				e.preventDefault(); // keep the text selection
				var doc = e.currentTarget.ownerDocument;
				if (command === 'createLink') {
					value = doc.defaultView.prompt(__('Link URL', 'bigspy-theme'), 'https://');
					if (!value) {
						return;
					}
				}
				doc.execCommand(command, false, value || null);
			};
		};
		var btn = function (label, command, value, title) {
			return el('button', { key: label + (value || ''), type: 'button', className: 'bst-format-bar__btn', title: title || label, onMouseDown: run(command, value) }, label);
		};
		return el('div', { className: 'bst-format-bar', role: 'toolbar', 'aria-label': __('Text formatting', 'bigspy-theme') },
			btn('P', 'formatBlock', 'p', __('Paragraph', 'bigspy-theme')),
			['h1', 'h2', 'h3', 'h4', 'h5', 'h6'].map(function (h) {
				return btn(h.toUpperCase(), 'formatBlock', h);
			}),
			el('span', { className: 'bst-format-bar__sep' }),
			btn('B', 'bold', null, __('Bold', 'bigspy-theme')),
			btn('I', 'italic', null, __('Italic', 'bigspy-theme')),
			btn('U', 'underline', null, __('Underline', 'bigspy-theme')),
			btn('S', 'strikeThrough', null, __('Strikethrough', 'bigspy-theme')),
			el('span', { className: 'bst-format-bar__sep' }),
			btn('• List', 'insertUnorderedList', null, __('Bulleted list', 'bigspy-theme')),
			btn('1. List', 'insertOrderedList', null, __('Numbered list', 'bigspy-theme')),
			btn('“ ”', 'formatBlock', 'blockquote', __('Quote', 'bigspy-theme')),
			btn('Link', 'createLink', null, __('Insert link', 'bigspy-theme')),
			btn('Unlink', 'unlink', null, __('Remove link', 'bigspy-theme')),
			el('span', { className: 'bst-format-bar__sep' }),
			btn('⇤', 'justifyLeft', null, __('Align left', 'bigspy-theme')),
			btn('↔', 'justifyCenter', null, __('Align center', 'bigspy-theme')),
			btn('⇥', 'justifyRight', null, __('Align right', 'bigspy-theme')),
			btn('✕', 'removeFormat', null, __('Clear formatting', 'bigspy-theme'))
		);
	}

	var ALIGN3 = { left: __('Left', 'bigspy-theme'), center: __('Center', 'bigspy-theme'), right: __('Right', 'bigspy-theme') };

	/* ================================================================ Section */
	register('section', function (props) {
		var a = props.attributes;
		var bind = B.binder(props);
		var classes = ['bs-section', 'bs-section--' + a.contentWidth, 'bs-section--valign-' + a.verticalAlign];
		if (a.textScheme) {
			classes.push('is-scheme-' + a.textScheme);
		}
		var blockProps = useBlockProps({
			className: classes.join(' '),
			style: styleVars({
				'--bs-sec-pd': a.paddingDesktop + 'px',
				'--bs-sec-pt': a.paddingTablet + 'px',
				'--bs-sec-pm': a.paddingMobile + 'px',
				'--bs-sec-min': a.minHeight ? a.minHeight + a.minHeightUnit : ''
			})
		});
		var innerProps = useInnerBlocksProps({ className: 'bs-section__inner' }, {
			template: [['bst/title', { text: __('Section title', 'bigspy-theme') }], ['core/paragraph', { placeholder: __('Add content, Rows, Banners…', 'bigspy-theme') }]],
			templateLock: false
		});

		return el(Fragment, null,
			el(Inspector, null,
				el(Panel, { title: __('Layout', 'bigspy-theme') },
					el(Select, Object.assign({ label: __('Content width', 'bigspy-theme'), options: { boxed: __('Container', 'bigspy-theme'), narrow: __('Narrow (800px)', 'bigspy-theme'), full: __('Full width', 'bigspy-theme') } }, bind('contentWidth'))),
					el(Range, Object.assign({ label: __('Padding top/bottom – desktop', 'bigspy-theme'), max: 300, reset: 80 }, bind('paddingDesktop'))),
					el(Range, Object.assign({ label: __('Padding top/bottom – tablet', 'bigspy-theme'), max: 300, reset: 60 }, bind('paddingTablet'))),
					el(Range, Object.assign({ label: __('Padding top/bottom – mobile', 'bigspy-theme'), max: 300, reset: 40 }, bind('paddingMobile'))),
					el(Range, Object.assign({ label: __('Minimum height', 'bigspy-theme'), max: a.minHeightUnit === 'vh' ? 100 : 1200 }, bind('minHeight'))),
					el(Select, Object.assign({ label: __('Height unit', 'bigspy-theme'), options: { px: 'px', vh: __('% of screen (vh)', 'bigspy-theme') } }, bind('minHeightUnit'))),
					el(Select, Object.assign({ label: __('Vertical align', 'bigspy-theme'), options: { top: __('Top', 'bigspy-theme'), center: __('Middle', 'bigspy-theme'), bottom: __('Bottom', 'bigspy-theme') } }, bind('verticalAlign')))
				),
				el(Panel, { title: __('Background image & overlay', 'bigspy-theme'), initialOpen: false },
					el(B.MediaPicker, {
						label: __('Background image', 'bigspy-theme'),
						id: a.bgImageId,
						url: a.bgImage,
						onChange: function (m) {
							props.setAttributes({ bgImageId: m.id, bgImage: m.url });
						}
					}),
					a.bgImage ? el(Select, Object.assign({ label: __('Size', 'bigspy-theme'), options: { cover: __('Cover', 'bigspy-theme'), contain: __('Contain', 'bigspy-theme'), auto: __('Original', 'bigspy-theme') } }, bind('bgSize'))) : null,
					a.bgImage ? el(Select, Object.assign({ label: __('Position', 'bigspy-theme'), options: { 'center center': __('Center', 'bigspy-theme'), 'center top': __('Top', 'bigspy-theme'), 'center bottom': __('Bottom', 'bigspy-theme'), 'left center': __('Left', 'bigspy-theme'), 'right center': __('Right', 'bigspy-theme') } }, bind('bgPosition'))) : null,
					a.bgImage ? el(Toggle, Object.assign({ label: __('Fixed background (parallax)', 'bigspy-theme') }, bind('bgFixed', true))) : null,
					el(Range, Object.assign({ label: __('Overlay opacity (%)', 'bigspy-theme'), max: 100, reset: 50 }, bind('overlayOpacity'))),
					el(Select, Object.assign({ label: __('Text color scheme', 'bigspy-theme'), options: { '': __('Default', 'bigspy-theme'), light: __('Light text (for dark backgrounds)', 'bigspy-theme'), dark: __('Dark text', 'bigspy-theme') } }, bind('textScheme')))
				),
				el(B.ColorPanel, { title: __('Overlay color', 'bigspy-theme'), settings: [{ label: __('Overlay', 'bigspy-theme'), value: a.overlayColor, onChange: function (v) { props.setAttributes({ overlayColor: v || '' }); } }] })
			),
			el('section', blockProps,
				a.bgImage ? el('div', { className: 'bs-section__bg', style: { backgroundImage: 'url(' + a.bgImage + ')', backgroundSize: a.bgSize, backgroundPosition: a.bgPosition } }) : null,
				a.overlayColor ? el('div', { className: 'bs-section__overlay', style: { background: a.overlayColor, opacity: a.overlayOpacity / 100 } }) : null,
				el('div', innerProps)
			)
		);
	}, saveInner);

	/* ================================================================ Row & Column */
	var PRESETS = {
		'12': [12],
		'6-6': [6, 6],
		'4-4-4': [4, 4, 4],
		'3-3-3-3': [3, 3, 3, 3],
		'8-4': [8, 4],
		'4-8': [4, 8],
		'3-9': [3, 9],
		'9-3': [9, 3],
		'3-6-3': [3, 6, 3]
	};

	register('row', function (props) {
		var a = props.attributes;
		var bind = B.binder(props);
		var inner = useSelect(function (select) {
			return select('core/block-editor').getBlocks(props.clientId);
		}, [props.clientId]);
		var replaceInnerBlocks = useDispatch('core/block-editor').replaceInnerBlocks;

		var applyPreset = function (key) {
			var spans = PRESETS[key];
			var blocks = spans.map(function (span, i) {
				var existing = inner[i];
				var tablet = spans.length > 2 ? 6 : 0;
				if (existing) {
					return wp.blocks.createBlock('bst/column', Object.assign({}, existing.attributes, { spanDesktop: span, spanTablet: tablet }), existing.innerBlocks);
				}
				return wp.blocks.createBlock('bst/column', { spanDesktop: span, spanTablet: tablet });
			});
			// Keep the content of removed columns by moving it into the last column.
			if (inner.length > spans.length) {
				var last = blocks[blocks.length - 1];
				inner.slice(spans.length).forEach(function (col) {
					last.innerBlocks = last.innerBlocks.concat(col.innerBlocks);
				});
			}
			replaceInnerBlocks(props.clientId, blocks, false);
		};

		var classes = 'bs-row bs-row--gap-' + a.gap + ' bs-row--valign-' + a.verticalAlign + ' bs-row--justify-' + a.justify + (a.reverseMobile ? ' bs-row--reverse-mobile' : '');
		var blockProps = useBlockProps({ className: classes });
		var innerProps = useInnerBlocksProps(blockProps, {
			allowedBlocks: ['bst/column'],
			template: [['bst/column', { spanDesktop: 6 }], ['bst/column', { spanDesktop: 6 }]],
			orientation: 'horizontal'
		});

		return el(Fragment, null,
			el(Inspector, null,
				el(Panel, { title: __('Column layout', 'bigspy-theme') },
					el('div', { className: 'bst-row-presets' },
						Object.keys(PRESETS).map(function (key) {
							return el(c.Button, {
								key: key,
								className: 'bst-row-preset',
								label: key.split('-').map(function (s) { return s + '/12'; }).join(' + '),
								showTooltip: true,
								onClick: function () {
									applyPreset(key);
								}
							}, PRESETS[key].map(function (span, i) {
								return el('span', { key: i, style: { flexGrow: span } });
							}));
						})
					),
					el('p', { className: 'description' }, __('Fine-tune each column width (per device) by selecting the column.', 'bigspy-theme'))
				),
				el(Panel, { title: __('Row settings', 'bigspy-theme') },
					el(Select, Object.assign({ label: __('Gap between columns', 'bigspy-theme'), options: { none: __('None', 'bigspy-theme'), small: __('Small', 'bigspy-theme'), medium: __('Medium', 'bigspy-theme'), large: __('Large', 'bigspy-theme') } }, bind('gap'))),
					el(Select, Object.assign({ label: __('Vertical align', 'bigspy-theme'), options: { stretch: __('Equal height', 'bigspy-theme'), top: __('Top', 'bigspy-theme'), center: __('Middle', 'bigspy-theme'), bottom: __('Bottom', 'bigspy-theme') } }, bind('verticalAlign'))),
					el(Select, Object.assign({ label: __('Horizontal align (when columns do not fill 12)', 'bigspy-theme'), options: { start: __('Left', 'bigspy-theme'), center: __('Center', 'bigspy-theme'), end: __('Right', 'bigspy-theme') } }, bind('justify'))),
					el(Toggle, Object.assign({ label: __('Reverse column order on mobile', 'bigspy-theme') }, bind('reverseMobile', true)))
				)
			),
			el('div', innerProps)
		);
	}, saveInner);

	register('column', function (props) {
		var a = props.attributes;
		var bind = B.binder(props);
		var hasChildren = useSelect(function (select) {
			return select('core/block-editor').getBlockCount(props.clientId) > 0;
		}, [props.clientId]);
		var blockProps = useBlockProps({
			className: 'bs-column bs-column--valign-' + a.verticalAlign + (a.textAlign ? ' has-text-align-' + a.textAlign : ''),
			style: { '--bs-span-d': a.spanDesktop, '--bs-span-t': a.spanTablet || a.spanDesktop, '--bs-span-m': a.spanMobile }
		});
		var innerProps = useInnerBlocksProps(blockProps, {
			templateLock: false,
			renderAppender: hasChildren ? undefined : InnerBlocks.ButtonBlockAppender
		});
		return el(Fragment, null,
			el(Inspector, null,
				el(Panel, { title: __('Column width (out of 12)', 'bigspy-theme') },
					el(Range, Object.assign({ label: __('Desktop', 'bigspy-theme'), min: 1, max: 12 }, bind('spanDesktop'))),
					el(Range, Object.assign({ label: __('Tablet (0 = same as desktop)', 'bigspy-theme'), min: 0, max: 12 }, bind('spanTablet'))),
					el(Range, Object.assign({ label: __('Mobile', 'bigspy-theme'), min: 1, max: 12 }, bind('spanMobile'))),
					el(Select, Object.assign({ label: __('Vertical align', 'bigspy-theme'), options: { top: __('Top', 'bigspy-theme'), center: __('Middle', 'bigspy-theme'), bottom: __('Bottom', 'bigspy-theme') } }, bind('verticalAlign'))),
					el(Select, Object.assign({ label: __('Text align', 'bigspy-theme'), options: B.assign({ '': __('Default', 'bigspy-theme') }, ALIGN3) }, bind('textAlign')))
				)
			),
			el('div', innerProps)
		);
	}, saveInner);

	/* ================================================================ Banner */
	register('banner', function (props) {
		var a = props.attributes;
		var bind = B.binder(props);
		var classes = 'bs-banner bs-banner--x-' + a.posX + ' bs-banner--y-' + a.posY + ' is-scheme-' + a.textScheme + ' has-text-align-' + a.textAlign;
		var blockProps = useBlockProps({
			className: classes,
			style: {
				'--bs-banner-hd': a.heightDesktop + 'px',
				'--bs-banner-ht': a.heightTablet + 'px',
				'--bs-banner-hm': a.heightMobile + 'px',
				'--bs-banner-width': a.contentWidth + '%',
				'--bs-banner-radius': a.radius + 'px'
			}
		});
		var innerProps = useInnerBlocksProps({ className: 'bs-banner__box' }, {
			template: [
				['core/heading', { level: 2, placeholder: __('Banner title', 'bigspy-theme') }],
				['core/paragraph', { placeholder: __('Short description…', 'bigspy-theme') }],
				['core/buttons', {}, [['core/button', { text: __('Learn more', 'bigspy-theme') }]]]
			]
		});
		var setImage = function (m) {
			props.setAttributes({ imageId: m.id, imageUrl: m.url });
		};

		return el(Fragment, null,
			el(Inspector, null,
				el(Panel, { title: __('Image', 'bigspy-theme') },
					el(B.MediaPicker, { id: a.imageId, url: a.imageUrl, onChange: setImage }),
					a.imageUrl ? el(c.FocalPointPicker, {
						label: __('Focal point', 'bigspy-theme'),
						url: a.imageUrl,
						value: { x: a.focalX / 100, y: a.focalY / 100 },
						onChange: function (v) {
							props.setAttributes({ focalX: Math.round(v.x * 100), focalY: Math.round(v.y * 100) });
						}
					}) : null,
					el(Select, Object.assign({ label: __('Hover effect', 'bigspy-theme'), options: { none: __('None', 'bigspy-theme'), zoom: __('Zoom image', 'bigspy-theme'), glow: __('Lighten overlay', 'bigspy-theme') } }, bind('hover')))
				),
				el(Panel, { title: __('Size', 'bigspy-theme') },
					el(Range, Object.assign({ label: __('Height – desktop (px)', 'bigspy-theme'), min: 100, max: 1200, reset: 500 }, bind('heightDesktop'))),
					el(Range, Object.assign({ label: __('Height – tablet (px)', 'bigspy-theme'), min: 100, max: 1200, reset: 420 }, bind('heightTablet'))),
					el(Range, Object.assign({ label: __('Height – mobile (px)', 'bigspy-theme'), min: 100, max: 1200, reset: 340 }, bind('heightMobile'))),
					el(Range, Object.assign({ label: __('Corner radius (px)', 'bigspy-theme'), max: 60 }, bind('radius')))
				),
				el(Panel, { title: __('Content position', 'bigspy-theme') },
					el(Select, Object.assign({ label: __('Horizontal', 'bigspy-theme'), options: ALIGN3 }, bind('posX'))),
					el(Select, Object.assign({ label: __('Vertical', 'bigspy-theme'), options: { top: __('Top', 'bigspy-theme'), middle: __('Middle', 'bigspy-theme'), bottom: __('Bottom', 'bigspy-theme') } }, bind('posY'))),
					el(Range, Object.assign({ label: __('Content width (%)', 'bigspy-theme'), min: 20, max: 100, reset: 70 }, bind('contentWidth'))),
					el(Select, Object.assign({ label: __('Text align', 'bigspy-theme'), options: ALIGN3 }, bind('textAlign'))),
					el(Select, Object.assign({ label: __('Text color', 'bigspy-theme'), options: { light: __('Light', 'bigspy-theme'), dark: __('Dark', 'bigspy-theme') } }, bind('textScheme')))
				),
				el(Panel, { title: __('Overlay & link', 'bigspy-theme'), initialOpen: false },
					el(Range, Object.assign({ label: __('Overlay opacity (%)', 'bigspy-theme'), max: 100, reset: 35 }, bind('overlayOpacity'))),
					el(Text, Object.assign({ label: __('Link the whole banner (URL)', 'bigspy-theme'), type: 'url' }, bind('link'))),
					a.link ? el(Toggle, Object.assign({ label: __('Open in new tab', 'bigspy-theme') }, bind('linkNewTab', true))) : null
				),
				el(B.ColorPanel, { title: __('Overlay color', 'bigspy-theme'), settings: [{ label: __('Overlay', 'bigspy-theme'), value: a.overlayColor, onChange: function (v) { props.setAttributes({ overlayColor: v || '' }); } }] })
			),
			el('div', blockProps,
				el('div', { className: 'bs-banner__bg' },
					a.imageUrl ? el('img', { className: 'bs-banner__img', src: a.imageUrl, alt: '', style: { objectPosition: a.focalX + '% ' + a.focalY + '%' } }) :
						el(be.MediaPlaceholder, { icon: 'format-image', labels: { title: __('Banner image', 'bigspy-theme') }, accept: 'image/*', allowedTypes: ['image'], onSelect: function (m) { setImage({ id: m.id, url: m.url }); } })
				),
				a.overlayColor && a.imageUrl ? el('div', { className: 'bs-banner__overlay', style: { background: a.overlayColor, opacity: a.overlayOpacity / 100 } }) : null,
				el('div', { className: 'bs-banner__content' }, el('div', innerProps))
			)
		);
	}, saveInner);

	/* ================================================================ Slider */
	register('slider', function (props) {
		var a = props.attributes;
		var bind = B.binder(props);
		var blockProps = useBlockProps({ className: 'bs-slider-block bst-slider-editor', style: { '--bs-slides-d': a.slidesDesktop, '--bs-slide-gap': a.gap + 'px' } });
		var innerProps = useInnerBlocksProps({ className: 'bst-slider-editor__track' }, {
			template: [['bst/banner', {}], ['bst/banner', {}]],
			orientation: 'horizontal'
		});
		return el(Fragment, null,
			el(Inspector, null,
				el(Panel, { title: __('Slides', 'bigspy-theme') },
					el('p', { className: 'description' }, __('Every block inside the slider is one slide. Banners work great.', 'bigspy-theme')),
					el(Range, Object.assign({ label: __('Slides per view – desktop', 'bigspy-theme'), min: 1, max: 6 }, bind('slidesDesktop'))),
					el(Range, Object.assign({ label: __('Slides per view – tablet', 'bigspy-theme'), min: 1, max: 4 }, bind('slidesTablet'))),
					el(Range, Object.assign({ label: __('Slides per view – mobile', 'bigspy-theme'), min: 1, max: 3 }, bind('slidesMobile'))),
					el(Range, Object.assign({ label: __('Gap (px)', 'bigspy-theme'), max: 80 }, bind('gap')))
				),
				el(Panel, { title: __('Behaviour', 'bigspy-theme') },
					el(Range, Object.assign({ label: __('Autoplay every (seconds, 0 = off)', 'bigspy-theme'), max: 30 }, bind('autoplay'))),
					el(Toggle, Object.assign({ label: __('Arrows', 'bigspy-theme') }, bind('arrows', true))),
					el(Select, Object.assign({ label: __('Arrow style', 'bigspy-theme'), options: { circle: __('Circle', 'bigspy-theme'), square: __('Square', 'bigspy-theme'), minimal: __('Minimal', 'bigspy-theme') } }, bind('arrowStyle'))),
					el(Toggle, Object.assign({ label: __('Dots', 'bigspy-theme') }, bind('dots', true))),
					el(Toggle, Object.assign({ label: __('Loop', 'bigspy-theme') }, bind('loop', true)))
				)
			),
			el('div', blockProps, el('div', innerProps))
		);
	}, saveInner);

	/* ================================================================ Icon box */
	register('icon-box', function (props) {
		var a = props.attributes;
		var bind = B.binder(props);
		var blockProps = useBlockProps({
			className: 'bs-icon-box bs-icon-box--' + a.position + ' bs-icon-box--align-' + a.align + ' bs-icon-box--icon-' + a.iconStyle + ' bs-icon-box--box-' + a.boxStyle,
			style: styleVars({ '--bs-ib-size': a.iconSize + 'px', '--bs-ib-color': a.iconColor, '--bs-ib-bg': a.iconBg })
		});
		var innerProps = useInnerBlocksProps({ className: 'bs-icon-box__inner' }, { templateLock: false, renderAppender: props.isSelected ? InnerBlocks.ButtonBlockAppender : false });
		var media = a.imageUrl ? el('img', { className: 'bs-icon-box__img', src: a.imageUrl, alt: '' }) : el(B.Icon, { name: a.icon, size: a.iconSize });

		return el(Fragment, null,
			el(Inspector, null,
				el(Panel, { title: __('Icon', 'bigspy-theme') },
					el(B.IconPicker, bind('icon')),
					el(B.MediaPicker, {
						label: __('…or use an image instead', 'bigspy-theme'),
						id: a.imageId,
						url: a.imageUrl,
						onChange: function (m) {
							props.setAttributes({ imageId: m.id, imageUrl: m.url });
						}
					}),
					el(Select, Object.assign({ label: __('Icon style', 'bigspy-theme'), options: { plain: __('Plain', 'bigspy-theme'), circle: __('Filled circle', 'bigspy-theme'), square: __('Filled square', 'bigspy-theme'), outline: __('Outline circle', 'bigspy-theme') } }, bind('iconStyle'))),
					el(Range, Object.assign({ label: __('Icon size (px)', 'bigspy-theme'), min: 12, max: 160, reset: 32 }, bind('iconSize')))
				),
				el(Panel, { title: __('Layout', 'bigspy-theme') },
					el(Select, Object.assign({ label: __('Icon position', 'bigspy-theme'), options: { top: __('Top', 'bigspy-theme'), left: __('Left', 'bigspy-theme') } }, bind('position'))),
					el(Select, Object.assign({ label: __('Align', 'bigspy-theme'), options: { left: __('Left', 'bigspy-theme'), center: __('Center', 'bigspy-theme') } }, bind('align'))),
					el(Select, Object.assign({ label: __('Box style', 'bigspy-theme'), options: { none: __('None', 'bigspy-theme'), card: __('Card with shadow', 'bigspy-theme'), bordered: __('Bordered', 'bigspy-theme') } }, bind('boxStyle'))),
					el(Select, Object.assign({ label: __('Title tag', 'bigspy-theme'), options: { h2: 'H2', h3: 'H3', h4: 'H4', h5: 'H5', p: 'P' } }, bind('titleTag'))),
					el(Text, Object.assign({ label: __('Title link (URL)', 'bigspy-theme'), type: 'url' }, bind('link')))
				),
				el(B.ColorPanel, {
					title: __('Icon colors', 'bigspy-theme'),
					settings: [
						{ label: __('Icon', 'bigspy-theme'), value: a.iconColor, onChange: function (v) { props.setAttributes({ iconColor: v || '' }); } },
						{ label: __('Icon background', 'bigspy-theme'), value: a.iconBg, onChange: function (v) { props.setAttributes({ iconBg: v || '' }); } }
					]
				})
			),
			el('div', blockProps,
				media ? el('div', { className: 'bs-icon-box__icon' }, media) : null,
				el('div', { className: 'bs-icon-box__body' },
					el(RichText, { tagName: a.titleTag === 'p' ? 'p' : a.titleTag, className: 'bs-icon-box__title', value: a.title, placeholder: __('Title', 'bigspy-theme'), onChange: function (v) { props.setAttributes({ title: v }); } }),
					el(RichText, { tagName: 'div', className: 'bs-icon-box__text', value: a.text, placeholder: __('Description…', 'bigspy-theme'), onChange: function (v) { props.setAttributes({ text: v }); } }),
					el('div', innerProps)
				)
			)
		);
	}, saveInner);

	/* ================================================================ Section title */
	register('title', function (props) {
		var a = props.attributes;
		var bind = B.binder(props);
		var blockProps = useBlockProps({
			className: 'bs-title bs-title--' + a.style + ' bs-title--align-' + a.align,
			style: styleVars({ '--bs-title-size': a.size ? a.size + 'px' : '', '--bs-title-color': a.color, '--bs-title-line': a.lineColor })
		});
		return el(Fragment, null,
			el(Inspector, null,
				el(Panel, { title: __('Title', 'bigspy-theme') },
					el(Select, Object.assign({ label: __('Style', 'bigspy-theme'), options: { plain: __('Plain', 'bigspy-theme'), 'bottom-line': __('Short line below', 'bigspy-theme'), 'center-line': __('Lines on both sides', 'bigspy-theme'), 'left-bar': __('Bar on the left', 'bigspy-theme'), 'underline-full': __('Full-width underline', 'bigspy-theme') } }, bind('style'))),
					el(Select, Object.assign({ label: __('Align', 'bigspy-theme'), options: ALIGN3 }, bind('align'))),
					el(Select, Object.assign({ label: __('Tag', 'bigspy-theme'), options: { h1: 'H1', h2: 'H2', h3: 'H3', h4: 'H4' } }, bind('tag'))),
					el(Range, Object.assign({ label: __('Font size (px, 0 = default)', 'bigspy-theme'), max: 120 }, bind('size'))),
					el(Text, Object.assign({ label: __('“View all” text', 'bigspy-theme') }, bind('linkText'))),
					el(Text, Object.assign({ label: __('“View all” link', 'bigspy-theme'), type: 'url' }, bind('linkUrl')))
				),
				el(B.ColorPanel, {
					settings: [
						{ label: __('Text', 'bigspy-theme'), value: a.color, onChange: function (v) { props.setAttributes({ color: v || '' }); } },
						{ label: __('Line', 'bigspy-theme'), value: a.lineColor, onChange: function (v) { props.setAttributes({ lineColor: v || '' }); } }
					]
				})
			),
			el('div', blockProps,
				el(RichText, { tagName: 'p', className: 'bs-title__subtitle', value: a.subtitle, placeholder: __('Subtitle (optional)', 'bigspy-theme'), onChange: function (v) { props.setAttributes({ subtitle: v }); } }),
				el('div', { className: 'bs-title__row' },
					el(RichText, { tagName: a.tag, className: 'bs-title__text', value: a.text, placeholder: __('Section title', 'bigspy-theme'), onChange: function (v) { props.setAttributes({ text: v }); } }),
					a.linkText ? el('span', { className: 'bs-title__link' }, a.linkText) : null
				)
			)
		);
	});

	/* ================================================================ Tabs */
	register('tabs', function (props) {
		var a = props.attributes;
		var bind = B.binder(props);
		var tabs = useSelect(function (select) {
			return select('core/block-editor').getBlocks(props.clientId);
		}, [props.clientId]);
		var blockProps = useBlockProps({ className: 'bs-tabs bs-tabs--' + a.style + ' bs-tabs--align-' + a.align + ' bst-tabs-editor' });
		var innerProps = useInnerBlocksProps({ className: 'bs-tabs__panels' }, {
			allowedBlocks: ['bst/tab'],
			template: [['bst/tab', { title: __('Tab 1', 'bigspy-theme') }], ['bst/tab', { title: __('Tab 2', 'bigspy-theme') }]]
		});
		return el(Fragment, null,
			el(Inspector, null,
				el(Panel, { title: __('Tabs', 'bigspy-theme') },
					el(Select, Object.assign({ label: __('Style', 'bigspy-theme'), options: { underline: __('Underline', 'bigspy-theme'), pills: __('Pills', 'bigspy-theme'), boxed: __('Boxed', 'bigspy-theme'), vertical: __('Vertical', 'bigspy-theme') } }, bind('style'))),
					el(Select, Object.assign({ label: __('Align', 'bigspy-theme'), options: { left: __('Left', 'bigspy-theme'), center: __('Center', 'bigspy-theme') } }, bind('align')))
				)
			),
			el('div', blockProps,
				el('div', { className: 'bs-tabs__nav' },
					tabs.map(function (tab, i) {
						return el('span', { key: tab.clientId, className: 'bs-tabs__tab' + (i === 0 ? ' is-active' : '') }, tab.attributes.title || __('Tab', 'bigspy-theme'));
					})
				),
				el('div', innerProps)
			)
		);
	}, saveInner);

	register('tab', function (props) {
		var blockProps = useBlockProps({ className: 'bs-tab bst-tab-editor' });
		var innerProps = useInnerBlocksProps({ className: 'bst-tab-editor__content' }, { template: [['core/paragraph', { placeholder: __('Tab content…', 'bigspy-theme') }]] });
		return el('div', blockProps,
			el(Text, { label: __('Tab title', 'bigspy-theme'), value: props.attributes.title, onChange: function (v) { props.setAttributes({ title: v }); } }),
			el('div', innerProps)
		);
	}, saveInner);

	/* ================================================================ Accordion */
	register('accordion', function (props) {
		var a = props.attributes;
		var bind = B.binder(props);
		var blockProps = useBlockProps({ className: 'bs-accordion bs-accordion--' + a.style });
		var innerProps = useInnerBlocksProps(blockProps, {
			allowedBlocks: ['bst/accordion-item'],
			template: [
				['bst/accordion-item', { title: __('Question 1?', 'bigspy-theme'), open: true }],
				['bst/accordion-item', { title: __('Question 2?', 'bigspy-theme') }],
				['bst/accordion-item', { title: __('Question 3?', 'bigspy-theme') }]
			]
		});
		return el(Fragment, null,
			el(Inspector, null,
				el(Panel, { title: __('Accordion', 'bigspy-theme') },
					el(Select, Object.assign({ label: __('Style', 'bigspy-theme'), options: { bordered: __('Bordered', 'bigspy-theme'), separated: __('Separated boxes', 'bigspy-theme'), minimal: __('Minimal lines', 'bigspy-theme') } }, bind('style'))),
					el(Toggle, Object.assign({ label: __('Only one item open at a time', 'bigspy-theme') }, bind('single', true)))
				)
			),
			el('div', innerProps)
		);
	}, saveInner);

	register('accordion-item', function (props) {
		var a = props.attributes;
		var blockProps = useBlockProps({ className: 'bs-accordion__item bst-accordion-editor' + (a.open ? ' is-open' : '') });
		var innerProps = useInnerBlocksProps({ className: 'bs-accordion__content' }, { template: [['core/paragraph', { placeholder: __('Answer…', 'bigspy-theme') }]] });
		return el(Fragment, null,
			el(Inspector, null,
				el(Panel, { title: __('Item', 'bigspy-theme') },
					el(Toggle, { label: __('Open by default', 'bigspy-theme'), checked: a.open, onChange: function (v) { props.setAttributes({ open: v }); } })
				)
			),
			el('div', blockProps,
				el(RichText, { tagName: 'div', className: 'bs-accordion__title', value: a.title, placeholder: __('Question / title', 'bigspy-theme'), onChange: function (v) { props.setAttributes({ title: v }); } }),
				el('div', innerProps)
			)
		);
	}, saveInner);

	/* ================================================================ Dynamic: posts & products */
	function useTerms(taxonomy) {
		return useSelect(function (select) {
			return select('core').getEntityRecords('taxonomy', taxonomy, { per_page: 100, hide_empty: false, _fields: 'id,name' }) || [];
		}, [taxonomy]);
	}

	function termOptions(terms) {
		return [{ value: 0, label: __('All', 'bigspy-theme') }].concat(terms.map(function (t) {
			return { value: t.id, label: t.name };
		}));
	}

	function dynamicPreview(name, props) {
		return el('div', useBlockProps(), el(SSR, { block: 'bst/' + name, attributes: props.attributes, skipBlockSupportAttributes: true }));
	}

	register('posts', function (props) {
		var a = props.attributes;
		var bind = B.binder(props);
		var cats = useTerms('category');
		return el(Fragment, null,
			el(Inspector, null,
				el(Panel, { title: __('Query', 'bigspy-theme') },
					el(Range, Object.assign({ label: __('Number of posts', 'bigspy-theme'), min: 1, max: 24 }, bind('count'))),
					el(c.SelectControl, { label: __('Category', 'bigspy-theme'), value: a.category, options: termOptions(cats), onChange: function (v) { props.setAttributes({ category: parseInt(v, 10) || 0 }); }, __nextHasNoMarginBottom: true }),
					el(Select, Object.assign({ label: __('Order by', 'bigspy-theme'), options: { date: __('Newest', 'bigspy-theme'), modified: __('Recently updated', 'bigspy-theme'), comment_count: __('Most commented', 'bigspy-theme'), rand: __('Random', 'bigspy-theme'), title: __('Title A–Z', 'bigspy-theme') } }, bind('orderBy'))),
					el(Range, Object.assign({ label: __('Skip first N posts', 'bigspy-theme'), max: 20 }, bind('offset')))
				),
				el(Panel, { title: __('Layout', 'bigspy-theme') },
					el(Select, Object.assign({ label: __('Layout', 'bigspy-theme'), options: { grid: __('Grid', 'bigspy-theme'), list: __('List', 'bigspy-theme'), slider: __('Slider', 'bigspy-theme') } }, bind('layout'))),
					a.layout !== 'list' ? el(Range, Object.assign({ label: __('Columns – desktop', 'bigspy-theme'), min: 1, max: 6 }, bind('columns'))) : null,
					a.layout !== 'list' ? el(Range, Object.assign({ label: __('Columns – tablet', 'bigspy-theme'), min: 1, max: 4 }, bind('columnsTablet'))) : null,
					a.layout !== 'list' ? el(Range, Object.assign({ label: __('Columns – mobile', 'bigspy-theme'), min: 1, max: 2 }, bind('columnsMobile'))) : null,
					a.layout === 'slider' ? el(Range, Object.assign({ label: __('Autoplay (seconds, 0 = off)', 'bigspy-theme'), max: 30 }, bind('autoplay'))) : null,
					el(Select, Object.assign({ label: __('Card style', 'bigspy-theme'), options: { '': __('Theme default', 'bigspy-theme'), shadow: __('Shadow', 'bigspy-theme'), bordered: __('Bordered', 'bigspy-theme'), plain: __('Plain', 'bigspy-theme'), overlay: __('Text over image', 'bigspy-theme') } }, bind('cardStyle'))),
					el(Select, Object.assign({ label: __('Image ratio', 'bigspy-theme'), options: { '16-9': '16:9', '4-3': '4:3', '3-2': '3:2', '1-1': '1:1', auto: __('Original', 'bigspy-theme') } }, bind('imageRatio'))),
					el(Toggle, Object.assign({ label: __('Show category & date', 'bigspy-theme') }, bind('showMeta', true))),
					el(Toggle, Object.assign({ label: __('Show excerpt', 'bigspy-theme') }, bind('showExcerpt', true))),
					el(Text, Object.assign({ label: __('“Read more” text (empty to hide)', 'bigspy-theme') }, bind('readMore')))
				)
			),
			dynamicPreview('posts', props)
		);
	});

	/* ================================================================ Code / HTML */
	register('code', function (props) {
		var a = props.attributes;
		var bind = B.binder(props);
		var tabState = wp.element.useState('visual');
		var tab = a.mode === 'snippet' ? 'code' : tabState[0];
		var setTab = tabState[1];
		var setCode = function (v) {
			props.setAttributes({ code: v });
		};
		var blockProps = useBlockProps({ className: 'bst-code-editor bst-code-editor--' + a.mode });
		var tabs = a.mode === 'snippet' ? [] : [['visual', __('Visual', 'bigspy-theme')], ['code', __('Code', 'bigspy-theme')], ['css', __('CSS', 'bigspy-theme')]];

		var body;
		if (tab === 'visual') {
			body = el(VisualHtml, { value: a.code, onChange: setCode, label: __('Editable HTML preview', 'bigspy-theme') });
		} else if (tab === 'css') {
			body = el(c.TextareaControl, {
				className: 'bst-code-editor__textarea',
				value: a.css,
				rows: 10,
				spellCheck: false,
				placeholder: 'selector h2 {\n  color: #0a4d8c;\n}',
				onChange: function (v) { props.setAttributes({ css: v }); },
				help: __('Use “selector” to target this block only. Applied on the site when the page author is an administrator or editor.', 'bigspy-theme'),
				__nextHasNoMarginBottom: true
			});
		} else {
			body = el(c.TextareaControl, {
				className: 'bst-code-editor__textarea',
				value: a.code,
				rows: 14,
				spellCheck: false,
				onChange: setCode,
				help: a.mode === 'snippet' ? __('Displayed as text with a copy button – nothing is executed.', 'bigspy-theme') : __('HTML, inline CSS and shortcodes. Scripts only work for administrators / editors.', 'bigspy-theme'),
				__nextHasNoMarginBottom: true
			});
		}

		return el(Fragment, null,
			el(Inspector, null,
				el(Panel, { title: __('Code / HTML', 'bigspy-theme') },
					el(Select, Object.assign({ label: __('Mode', 'bigspy-theme'), options: { html: __('Render HTML on the page', 'bigspy-theme'), snippet: __('Show as code snippet', 'bigspy-theme') } }, bind('mode'))),
					a.mode === 'snippet' ? el(Text, Object.assign({ label: __('Language label', 'bigspy-theme'), placeholder: 'HTML, CSS, JS, PHP…' }, bind('language'))) : null,
					a.mode === 'snippet' ? el(Select, Object.assign({ label: __('Color theme', 'bigspy-theme'), options: { dark: __('Dark', 'bigspy-theme'), light: __('Light', 'bigspy-theme') } }, bind('theme'))) : null,
					a.mode === 'snippet' ? el(Toggle, Object.assign({ label: __('Copy button', 'bigspy-theme') }, bind('showCopy', true))) : null,
					a.mode === 'snippet' ? el(Toggle, Object.assign({ label: __('Wrap long lines', 'bigspy-theme') }, bind('wrap', true))) : null,
					a.mode === 'snippet' ? el(Range, Object.assign({ label: __('Max height (px, 0 = auto)', 'bigspy-theme'), max: 1200, step: 20 }, bind('maxHeight'))) : null
				)
			),
			el('div', blockProps, editorTabs(tabs, tab, setTab, a.mode === 'snippet' ? (a.language || __('Code', 'bigspy-theme')) + ' · ' + __('snippet', 'bigspy-theme') : ''), body)
		);
	});

	/* ================================================================ Heading */
	register('heading', function (props) {
		var a = props.attributes;
		var bind = B.binder(props);
		var blockProps = useBlockProps({
			className: 'bs-heading' + (a.align ? ' has-text-align-' + a.align : ''),
			style: styleVars({ '--bs-heading-size': a.size ? a.size + 'px' : '', '--bs-heading-color': a.color, '--bs-heading-weight': a.weight || '' })
		});
		return el(Fragment, null,
			el(be.BlockControls, null,
				el(c.ToolbarGroup, null,
					['h1', 'h2', 'h3', 'h4', 'h5', 'h6'].map(function (t) {
						return el(c.ToolbarButton, { key: t, isPressed: a.tag === t, onClick: function () { props.setAttributes({ tag: t }); } }, t.toUpperCase());
					})
				),
				el(be.AlignmentToolbar, { value: a.align, onChange: function (v) { props.setAttributes({ align: v || '' }); } })
			),
			el(Inspector, null,
				el(Panel, { title: __('Heading', 'bigspy-theme') },
					el(Select, Object.assign({ label: __('HTML tag', 'bigspy-theme'), options: { h1: 'H1', h2: 'H2', h3: 'H3', h4: 'H4', h5: 'H5', h6: 'H6', p: 'P', div: 'DIV' } }, bind('tag'))),
					el(Range, Object.assign({ label: __('Font size (px, 0 = default)', 'bigspy-theme'), max: 120 }, bind('size'))),
					el(Select, Object.assign({ label: __('Font weight', 'bigspy-theme'), options: { '': __('Default', 'bigspy-theme'), '300': '300', '400': '400', '500': '500', '600': '600', '700': '700', '800': '800', '900': '900' } }, bind('weight'))),
					el(Text, Object.assign({ label: __('Link (URL)', 'bigspy-theme'), type: 'url' }, bind('link')))
				),
				el(B.ColorPanel, { settings: [{ label: __('Text', 'bigspy-theme'), value: a.color, onChange: function (v) { props.setAttributes({ color: v || '' }); } }] })
			),
			el(RichText, Object.assign({ tagName: a.tag === 'div' ? 'div' : a.tag, value: a.text, placeholder: __('Write a heading…', 'bigspy-theme'), onChange: function (v) { props.setAttributes({ text: v }); } }, blockProps))
		);
	});

	/* ================================================================ Text */
	register('text', function (props) {
		var a = props.attributes;
		var bind = B.binder(props);
		var tabState = wp.element.useState('visual');
		var tab = tabState[0];
		var setHtml = function (v) {
			props.setAttributes({ html: v });
		};
		var blockProps = useBlockProps({
			className: 'bst-code-editor bst-text-editor bs-text' + (a.align ? ' has-text-align-' + a.align : ''),
			style: styleVars({ '--bs-text-size': a.size ? a.size + 'px' : '', '--bs-text-color': a.color, '--bs-text-max': a.maxWidth ? a.maxWidth + 'px' : '' })
		});
		return el(Fragment, null,
			el(Inspector, null,
				el(Panel, { title: __('Text', 'bigspy-theme') },
					el(Select, Object.assign({ label: __('Align', 'bigspy-theme'), options: B.assign({ '': __('Default', 'bigspy-theme') }, ALIGN3, { justify: __('Justify', 'bigspy-theme') }) }, bind('align'))),
					el(Range, Object.assign({ label: __('Font size (px, 0 = default)', 'bigspy-theme'), max: 60 }, bind('size'))),
					el(Range, Object.assign({ label: __('Max width (px, 0 = full)', 'bigspy-theme'), max: 1400, step: 10 }, bind('maxWidth')))
				),
				el(B.ColorPanel, { settings: [{ label: __('Text', 'bigspy-theme'), value: a.color, onChange: function (v) { props.setAttributes({ color: v || '' }); } }] })
			),
			el('div', blockProps,
				editorTabs([['visual', __('Visual', 'bigspy-theme')], ['code', __('Code', 'bigspy-theme')]], tab, tabState[1], ''),
				tab === 'visual' ? el(Fragment, null,
					el(FormatBar, null),
					el(VisualHtml, { value: a.html, onChange: setHtml, className: 'bs-text__content', label: __('Text content', 'bigspy-theme') })
				) : el(c.TextareaControl, { className: 'bst-code-editor__textarea', value: a.html, rows: 12, spellCheck: false, onChange: setHtml, __nextHasNoMarginBottom: true })
			)
		);
	});

	/* ================================================================ Image */
	register('image', function (props) {
		var a = props.attributes;
		var bind = B.binder(props);
		var setImage = function (m) {
			props.setAttributes({ imageId: m.id || 0, imageUrl: m.url || '', alt: a.alt || m.alt || '' });
		};
		var blockProps = useBlockProps({
			className: 'bs-image bs-image--' + a.align + (a.shadow ? ' has-shadow' : ''),
			style: { '--bs-image-width': a.width + '%', '--bs-image-radius': a.radius + 'px' }
		});
		return el(Fragment, null,
			el(Inspector, null,
				el(Panel, { title: __('Image', 'bigspy-theme') },
					el(B.MediaPicker, { id: a.imageId, url: a.imageUrl, onChange: setImage }),
					el(Text, Object.assign({ label: __('Alt text (describes the image)', 'bigspy-theme') }, bind('alt'))),
					el(Select, Object.assign({ label: __('Resolution', 'bigspy-theme'), options: { thumbnail: __('Thumbnail', 'bigspy-theme'), medium: __('Medium', 'bigspy-theme'), medium_large: __('Medium large', 'bigspy-theme'), large: __('Large', 'bigspy-theme'), full: __('Full size', 'bigspy-theme') } }, bind('size')))
				),
				el(Panel, { title: __('Layout & style', 'bigspy-theme') },
					el(Range, Object.assign({ label: __('Width (%)', 'bigspy-theme'), min: 5, max: 100 }, bind('width'))),
					el(Select, Object.assign({ label: __('Align', 'bigspy-theme'), options: ALIGN3 }, bind('align'))),
					el(Select, Object.assign({ label: __('Crop ratio', 'bigspy-theme'), options: { auto: __('Original', 'bigspy-theme'), '1-1': '1:1', '4-3': '4:3', '3-2': '3:2', '16-9': '16:9', '3-4': '3:4' } }, bind('ratio'))),
					el(Range, Object.assign({ label: __('Corner radius (px)', 'bigspy-theme'), max: 200 }, bind('radius'))),
					el(Toggle, Object.assign({ label: __('Shadow', 'bigspy-theme') }, bind('shadow', true))),
					el(Select, Object.assign({ label: __('Hover effect', 'bigspy-theme'), options: { none: __('None', 'bigspy-theme'), zoom: __('Zoom', 'bigspy-theme'), lift: __('Lift', 'bigspy-theme'), grayscale: __('Grayscale to color', 'bigspy-theme') } }, bind('hover')))
				),
				el(Panel, { title: __('Link', 'bigspy-theme'), initialOpen: false },
					el(Text, Object.assign({ label: __('Link (URL)', 'bigspy-theme'), type: 'url' }, bind('link'))),
					a.link ? el(Toggle, Object.assign({ label: __('Open in new tab', 'bigspy-theme') }, bind('newTab', true))) : null
				)
			),
			el('figure', blockProps,
				a.imageUrl
					? el('span', { className: 'bs-image__frame' + (a.ratio !== 'auto' ? ' bs-ratio bs-ratio--' + a.ratio : '') }, el('img', { className: 'bs-image__img', src: a.imageUrl, alt: '' }))
					: el(be.MediaPlaceholder, { icon: 'format-image', labels: { title: __('Image', 'bigspy-theme') }, accept: 'image/*', allowedTypes: ['image'], onSelect: setImage }),
				a.imageUrl ? el(RichText, { tagName: 'figcaption', className: 'bs-image__caption', value: a.caption, placeholder: __('Caption (optional)', 'bigspy-theme'), onChange: function (v) { props.setAttributes({ caption: v }); } }) : null
			)
		);
	});

	/* ================================================================ Buttons & Button */
	register('buttons', function (props) {
		var a = props.attributes;
		var bind = B.binder(props);
		var blockProps = useBlockProps({
			className: 'bs-buttons bs-buttons--' + a.align + (a.stackMobile ? ' bs-buttons--stack-mobile' : ''),
			style: { '--bs-buttons-gap': a.gap + 'px' }
		});
		var innerProps = useInnerBlocksProps(blockProps, {
			allowedBlocks: ['bst/button'],
			template: [['bst/button', { text: __('Get started', 'bigspy-theme') }], ['bst/button', { text: __('Learn more', 'bigspy-theme'), style: 'outline' }]],
			orientation: 'horizontal'
		});
		return el(Fragment, null,
			el(Inspector, null,
				el(Panel, { title: __('Buttons', 'bigspy-theme') },
					el(Select, Object.assign({ label: __('Align', 'bigspy-theme'), options: B.assign({}, ALIGN3, { stretch: __('Stretch (equal width)', 'bigspy-theme') }) }, bind('align'))),
					el(Range, Object.assign({ label: __('Gap (px)', 'bigspy-theme'), max: 60 }, bind('gap'))),
					el(Toggle, Object.assign({ label: __('Stack on mobile', 'bigspy-theme') }, bind('stackMobile', true)))
				)
			),
			el('div', innerProps)
		);
	}, saveInner);

	register('button', function (props) {
		var a = props.attributes;
		var bind = B.binder(props);
		var cls = 'bs-btn bs-btn--' + a.style + (a.size !== 'md' ? ' bs-btn--' + a.size : '') + (a.fullWidth ? ' bs-btn--block' : '');
		var blockProps = useBlockProps({ className: cls, style: a.radius >= 0 ? { borderRadius: a.radius + 'px' } : {} });
		var icon = a.icon ? el(B.Icon, { name: a.icon, size: a.size === 'lg' ? 20 : 17 }) : null;
		return el(Fragment, null,
			el(Inspector, null,
				el(Panel, { title: __('Button', 'bigspy-theme') },
					el(Text, Object.assign({ label: __('Link (URL, tel:, mailto:)', 'bigspy-theme') }, bind('url'))),
					el(Toggle, Object.assign({ label: __('Open in new tab', 'bigspy-theme') }, bind('newTab', true))),
					el(Select, Object.assign({ label: __('Style', 'bigspy-theme'), options: { primary: __('Primary', 'bigspy-theme'), secondary: __('Secondary', 'bigspy-theme'), outline: __('Outline', 'bigspy-theme'), white: __('White', 'bigspy-theme'), link: __('Text link', 'bigspy-theme') } }, bind('style'))),
					el(Select, Object.assign({ label: __('Size', 'bigspy-theme'), options: { sm: __('Small', 'bigspy-theme'), md: __('Medium', 'bigspy-theme'), lg: __('Large', 'bigspy-theme') } }, bind('size'))),
					el(Toggle, Object.assign({ label: __('Full width', 'bigspy-theme') }, bind('fullWidth', true))),
					el(Range, Object.assign({ label: __('Corner radius (px, -1 = theme default)', 'bigspy-theme'), min: -1, max: 60 }, bind('radius')))
				),
				el(Panel, { title: __('Icon', 'bigspy-theme'), initialOpen: false },
					el(B.IconPicker, bind('icon')),
					a.icon ? el(Select, Object.assign({ label: __('Icon position', 'bigspy-theme'), options: { before: __('Before text', 'bigspy-theme'), after: __('After text', 'bigspy-theme') } }, bind('iconPosition'))) : null
				)
			),
			el('span', blockProps,
				a.iconPosition === 'before' ? icon : null,
				el(RichText, { tagName: 'span', value: a.text, placeholder: __('Button text', 'bigspy-theme'), allowedFormats: ['core/bold', 'core/italic'], withoutInteractiveFormatting: true, onChange: function (v) { props.setAttributes({ text: v }); } }),
				a.iconPosition !== 'before' ? icon : null
			)
		);
	});

	/* ================================================================ Divider & Spacer */
	register('divider', function (props) {
		var a = props.attributes;
		var bind = B.binder(props);
		var blockProps = useBlockProps({
			className: 'bs-divider bs-divider--' + a.align,
			style: styleVars({ '--bs-divider-width': a.width + '%', '--bs-divider-size': a.thickness + 'px', '--bs-divider-color': a.color, '--bs-divider-spacing': a.spacing + 'px', '--bs-divider-style': a.style })
		});
		return el(Fragment, null,
			el(Inspector, null,
				el(Panel, { title: __('Divider', 'bigspy-theme') },
					el(Select, Object.assign({ label: __('Line style', 'bigspy-theme'), options: { solid: __('Solid', 'bigspy-theme'), dashed: __('Dashed', 'bigspy-theme'), dotted: __('Dotted', 'bigspy-theme'), double: __('Double', 'bigspy-theme') } }, bind('style'))),
					el(Range, Object.assign({ label: __('Width (%)', 'bigspy-theme'), min: 1, max: 100 }, bind('width'))),
					el(Range, Object.assign({ label: __('Thickness (px)', 'bigspy-theme'), min: 1, max: 20 }, bind('thickness'))),
					el(Range, Object.assign({ label: __('Space above & below (px)', 'bigspy-theme'), max: 200 }, bind('spacing'))),
					el(Select, Object.assign({ label: __('Align', 'bigspy-theme'), options: ALIGN3 }, bind('align')))
				),
				el(B.ColorPanel, { settings: [{ label: __('Line color', 'bigspy-theme'), value: a.color, onChange: function (v) { props.setAttributes({ color: v || '' }); } }] })
			),
			el('hr', blockProps)
		);
	});

	register('spacer', function (props) {
		var bind = B.binder(props);
		var a = props.attributes;
		var blockProps = useBlockProps({ className: 'bs-spacer bst-spacer-editor', style: { '--bs-spacer-d': a.heightDesktop + 'px', '--bs-spacer-t': a.heightTablet + 'px', '--bs-spacer-m': a.heightMobile + 'px' } });
		return el(Fragment, null,
			el(Inspector, null,
				el(Panel, { title: __('Height', 'bigspy-theme') },
					el(Range, Object.assign({ label: __('Desktop (px)', 'bigspy-theme'), max: 400 }, bind('heightDesktop'))),
					el(Range, Object.assign({ label: __('Tablet (px)', 'bigspy-theme'), max: 400 }, bind('heightTablet'))),
					el(Range, Object.assign({ label: __('Mobile (px)', 'bigspy-theme'), max: 400 }, bind('heightMobile')))
				)
			),
			el('div', blockProps)
		);
	});

	/* ================================================================ Shop blocks (WooCommerce) */
	// Always registered so existing pages never show "unsupported block" when
	// WooCommerce is switched off; hidden from the inserter until it is active.
	function registerShop(name, edit) {
		wp.blocks.registerBlockType('bst/' + name, {
			supports: { inserter: !!B.hasWoo },
			edit: function (props) {
				if (!B.hasWoo) {
					return el('div', useBlockProps(), el(c.Placeholder, {
						icon: 'cart',
						label: __('WooCommerce required', 'bigspy-theme'),
						instructions: __('Install and activate WooCommerce to display products here. This block shows nothing on the site until then.', 'bigspy-theme')
					}));
				}
				return edit(props);
			},
			save: saveNull
		});
	}

	function columnsPanel(bind, a, max) {
		return el(Panel, { title: __('Layout', 'bigspy-theme') },
			el(Select, Object.assign({ label: __('Layout', 'bigspy-theme'), options: { grid: __('Grid', 'bigspy-theme'), slider: __('Slider', 'bigspy-theme') } }, bind('layout'))),
			el(Range, Object.assign({ label: __('Columns – desktop', 'bigspy-theme'), min: 1, max: max[0] }, bind('columns'))),
			el(Range, Object.assign({ label: __('Columns – tablet', 'bigspy-theme'), min: 1, max: max[1] }, bind('columnsTablet'))),
			el(Range, Object.assign({ label: __('Columns – mobile', 'bigspy-theme'), min: 1, max: max[2] }, bind('columnsMobile'))),
			a.layout === 'slider' ? el(Range, Object.assign({ label: __('Autoplay (seconds, 0 = off)', 'bigspy-theme'), max: 30 }, bind('autoplay'))) : null
		);
	}

	registerShop('products', function (props) {
		var a = props.attributes;
		var bind = B.binder(props);
		var cats = useTerms('product_cat');
		return el(Fragment, null,
			el(Inspector, null,
				el(Panel, { title: __('Products', 'bigspy-theme') },
					el(Select, Object.assign({ label: __('Show', 'bigspy-theme'), options: { latest: __('Latest', 'bigspy-theme'), featured: __('Featured', 'bigspy-theme'), sale: __('On sale', 'bigspy-theme'), best_selling: __('Best selling', 'bigspy-theme'), top_rated: __('Top rated', 'bigspy-theme'), random: __('Random', 'bigspy-theme') } }, bind('type'))),
					el(c.SelectControl, { label: __('Category', 'bigspy-theme'), value: a.category, options: termOptions(cats), onChange: function (v) { props.setAttributes({ category: parseInt(v, 10) || 0 }); }, __nextHasNoMarginBottom: true }),
					el(Select, Object.assign({ label: __('Order by', 'bigspy-theme'), options: { '': __('Default (follows “Show”)', 'bigspy-theme'), menu_order: __('Custom order (drag & drop in WooCommerce)', 'bigspy-theme'), date: __('Newest', 'bigspy-theme'), title: __('Name A–Z', 'bigspy-theme'), price: __('Price: low to high', 'bigspy-theme'), 'price-desc': __('Price: high to low', 'bigspy-theme'), popularity: __('Best selling', 'bigspy-theme'), rating: __('Top rated', 'bigspy-theme'), rand: __('Random', 'bigspy-theme') } }, bind('orderBy'))),
					el(Range, Object.assign({ label: __('Number of products', 'bigspy-theme'), min: 1, max: 48 }, bind('count'))),
					a.layout === 'grid' ? el(Toggle, Object.assign({ label: __('“Load more” button', 'bigspy-theme'), help: __('Loads the next products without reloading the page.', 'bigspy-theme') }, bind('loadMore', true))) : null
				),
				columnsPanel(bind, a, [6, 4, 2])
			),
			dynamicPreview('products', props)
		);
	});

	registerShop('product-categories', function (props) {
		var a = props.attributes;
		var bind = B.binder(props);
		var cats = useTerms('product_cat');
		return el(Fragment, null,
			el(Inspector, null,
				el(Panel, { title: __('Categories', 'bigspy-theme') },
					el(Range, Object.assign({ label: __('Number of categories', 'bigspy-theme'), min: 1, max: 60 }, bind('count'))),
					el(c.SelectControl, { label: __('Only children of', 'bigspy-theme'), value: a.parent, options: termOptions(cats), onChange: function (v) { props.setAttributes({ parent: parseInt(v, 10) || 0 }); }, __nextHasNoMarginBottom: true }),
					!a.parent ? el(Toggle, Object.assign({ label: __('Top-level categories only', 'bigspy-theme') }, bind('topLevelOnly', true))) : null,
					el(Toggle, Object.assign({ label: __('Hide empty categories', 'bigspy-theme') }, bind('hideEmpty', true))),
					el(Select, Object.assign({ label: __('Order by', 'bigspy-theme'), options: { menu_order: __('Custom order (drag & drop in WooCommerce)', 'bigspy-theme'), name: __('Name', 'bigspy-theme'), count: __('Most products', 'bigspy-theme') } }, bind('orderBy')))
				),
				el(Panel, { title: __('Style', 'bigspy-theme') },
					el(Select, Object.assign({ label: __('Style', 'bigspy-theme'), options: { overlay: __('Name over image', 'bigspy-theme'), card: __('Name below image', 'bigspy-theme'), circle: __('Round image', 'bigspy-theme') } }, bind('style'))),
					el(Select, Object.assign({ label: __('Image ratio', 'bigspy-theme'), options: { '1-1': '1:1', '4-3': '4:3', '3-4': '3:4', '16-9': '16:9' } }, bind('ratio'))),
					el(Toggle, Object.assign({ label: __('Show product count', 'bigspy-theme') }, bind('showCount', true)))
				),
				columnsPanel(bind, a, [8, 6, 3])
			),
			dynamicPreview('product-categories', props)
		);
	});

	/* ================================================================ Testimonial */
	register('testimonial', function (props) {
		var a = props.attributes;
		var bind = B.binder(props);
		var blockProps = useBlockProps({ className: 'bs-testimonial bs-testimonial--' + a.style + ' bs-testimonial--align-' + a.align });
		var stars = [];
		for (var i = 1; i <= 5; i++) {
			stars.push(el('span', { key: i, className: i <= a.rating ? 'is-on' : 'is-off' }, el(B.Icon, { name: 'star', size: 16 })));
		}
		return el(Fragment, null,
			el(Inspector, null,
				el(Panel, { title: __('Testimonial', 'bigspy-theme') },
					el(B.MediaPicker, { label: __('Photo', 'bigspy-theme'), id: a.imageId, url: a.imageUrl, onChange: function (m) { props.setAttributes({ imageId: m.id, imageUrl: m.url }); } }),
					el(Range, Object.assign({ label: __('Star rating (0 = hide)', 'bigspy-theme'), max: 5 }, bind('rating'))),
					el(Select, Object.assign({ label: __('Style', 'bigspy-theme'), options: { card: __('Card', 'bigspy-theme'), bubble: __('Speech bubble', 'bigspy-theme'), minimal: __('Minimal', 'bigspy-theme') } }, bind('style'))),
					el(Select, Object.assign({ label: __('Align', 'bigspy-theme'), options: { left: __('Left', 'bigspy-theme'), center: __('Center', 'bigspy-theme') } }, bind('align')))
				)
			),
			el('figure', blockProps,
				a.rating ? el('div', { className: 'bs-testimonial__rating' }, stars) : null,
				el(RichText, { tagName: 'blockquote', className: 'bs-testimonial__text', value: a.text, placeholder: __('What did the customer say?', 'bigspy-theme'), onChange: function (v) { props.setAttributes({ text: v }); } }),
				el('figcaption', { className: 'bs-testimonial__author' },
					a.imageUrl ? el('img', { className: 'bs-testimonial__img', src: a.imageUrl, alt: '' }) : null,
					el('span', null,
						el(RichText, { tagName: 'strong', className: 'bs-testimonial__name', value: a.name, placeholder: __('Name', 'bigspy-theme'), onChange: function (v) { props.setAttributes({ name: v }); } }),
						el(RichText, { tagName: 'span', className: 'bs-testimonial__role', value: a.role, placeholder: __('Position, company', 'bigspy-theme'), onChange: function (v) { props.setAttributes({ role: v }); } })
					)
				)
			)
		);
	});

	/* ================================================================ Counter */
	register('counter', function (props) {
		var a = props.attributes;
		var bind = B.binder(props);
		var blockProps = useBlockProps({ className: 'bs-counter has-text-align-' + a.align, style: styleVars({ '--bs-counter-size': a.size + 'px', '--bs-counter-color': a.color }) });
		return el(Fragment, null,
			el(Inspector, null,
				el(Panel, { title: __('Counter', 'bigspy-theme') },
					el(Text, Object.assign({ label: __('Number', 'bigspy-theme') }, bind('number'))),
					el(Text, Object.assign({ label: __('Prefix', 'bigspy-theme') }, bind('prefix'))),
					el(Text, Object.assign({ label: __('Suffix', 'bigspy-theme') }, bind('suffix'))),
					el(Select, Object.assign({ label: __('Thousands separator', 'bigspy-theme'), options: { '.': '1.000', ',': '1,000', ' ': '1 000', '': '1000' } }, bind('separator'))),
					el(Range, Object.assign({ label: __('Animation duration (ms)', 'bigspy-theme'), min: 200, max: 6000, step: 100 }, bind('duration'))),
					el(Range, Object.assign({ label: __('Number size (px)', 'bigspy-theme'), min: 16, max: 140 }, bind('size'))),
					el(Select, Object.assign({ label: __('Align', 'bigspy-theme'), options: ALIGN3 }, bind('align')))
				),
				el(B.ColorPanel, { settings: [{ label: __('Number color', 'bigspy-theme'), value: a.color, onChange: function (v) { props.setAttributes({ color: v || '' }); } }] })
			),
			el('div', blockProps,
				el('div', { className: 'bs-counter__value' },
					a.prefix ? el('span', { className: 'bs-counter__affix' }, a.prefix) : null,
					el('span', { className: 'bs-counter__number' }, a.number),
					a.suffix ? el('span', { className: 'bs-counter__affix' }, a.suffix) : null
				),
				el(RichText, { tagName: 'div', className: 'bs-counter__label', value: a.label, placeholder: __('Label (e.g. Happy customers)', 'bigspy-theme'), onChange: function (v) { props.setAttributes({ label: v }); } })
			)
		);
	});

	/* ================================================================ Map */
	register('map', function (props) {
		var a = props.attributes;
		var bind = B.binder(props);
		var blockProps = useBlockProps({ className: 'bs-map', style: { '--bs-map-h': a.height + 'px', '--bs-map-radius': a.radius + 'px' } });
		var src = a.address ? 'https://maps.google.com/maps?q=' + encodeURIComponent(a.address) + '&z=' + a.zoom + '&output=embed' : '';
		return el(Fragment, null,
			el(Inspector, null,
				el(Panel, { title: __('Map', 'bigspy-theme') },
					el(Text, Object.assign({ label: __('Address', 'bigspy-theme'), help: __('Empty = address from Customizer → Header → Contact info.', 'bigspy-theme') }, bind('address'))),
					el(Range, Object.assign({ label: __('Zoom', 'bigspy-theme'), min: 1, max: 21 }, bind('zoom'))),
					el(Range, Object.assign({ label: __('Height (px)', 'bigspy-theme'), min: 150, max: 1000 }, bind('height'))),
					el(Range, Object.assign({ label: __('Corner radius (px)', 'bigspy-theme'), max: 60 }, bind('radius')))
				)
			),
			el('div', blockProps,
				src ? el('iframe', { src: src, title: a.address, style: { pointerEvents: 'none' } }) :
					el(c.Placeholder, { icon: 'location-alt', label: __('Google Map', 'bigspy-theme'), instructions: __('Enter an address in the block settings (or leave empty to use the site address).', 'bigspy-theme') })
			)
		);
	});
})(window.wp);
