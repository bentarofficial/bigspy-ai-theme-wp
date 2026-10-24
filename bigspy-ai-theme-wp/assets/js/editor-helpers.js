/* BIGSPY AI Theme WP – shared editor components + responsive/animation panel for every block. */
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
		return el(c.BaseControl, { label: props.label || __('Icon', 'bigspy-ai-theme-wp'), __nextHasNoMarginBottom: true },
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
		return el(c.BaseControl, { label: props.label || __('Image', 'bigspy-ai-theme-wp'), __nextHasNoMarginBottom: true },
			el(be.MediaUploadCheck, null,
				el(be.MediaUpload, {
					allowedTypes: props.allowedTypes || ['image'],
					value: props.id || 0,
					onSelect: function (media) {
						props.onChange({ id: media.id || 0, url: media.url || '', width: media.width || 0, height: media.height || 0, alt: media.alt || '' });
					},
					render: function (o) {
						return el('div', { className: 'bst-media-picker' },
							props.url ? ((props.allowedTypes && props.allowedTypes[0] !== 'image') ? el('code', { className: 'bst-media-picker__file' }, props.url.split('/').pop()) : el('img', { src: props.url, alt: '', onClick: o.open })) : null,
							el('div', { className: 'bst-media-picker__actions' },
								el(c.Button, { variant: 'secondary', onClick: o.open }, props.url ? __('Replace', 'bigspy-ai-theme-wp') : ((props.allowedTypes && props.allowedTypes[0] !== 'image') ? __('Select file', 'bigspy-ai-theme-wp') : __('Select image', 'bigspy-ai-theme-wp'))),
								props.url ? el(c.Button, {
									variant: 'link',
									isDestructive: true,
									onClick: function () {
										props.onChange({ id: 0, url: '' });
									}
								}, __('Remove', 'bigspy-ai-theme-wp')) : null
							)
						);
					}
				})
			)
		);
	}

	function ColorPanel(props) {
		return el(be.PanelColorSettings, {
			title: props.title || __('Colors', 'bigspy-ai-theme-wp'),
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
		bsAnimationDelay: { type: 'number', default: 0 },
		bsAnimationDuration: { type: 'number', default: 700 },
		bsHoverEffect: { type: 'string', default: '' },
		bsPresets: { type: 'string', default: '' },
		bsEase: { type: 'string', default: '' },
		bsDistance: { type: 'number', default: 0 },
		bsRepeat: { type: 'boolean', default: false },
		bsStagger: { type: 'number', default: 0 },
		bsLoop: { type: 'string', default: '' },
		bsScroll: { type: 'string', default: '' },
		bsScrollStrength: { type: 'number', default: 5 }
	};
	var BS_ENTRANCES = [["Fade", [["fade-in", "Fade in"], ["fade-up", "Fade up"], ["fade-down", "Fade down"], ["fade-left", "Fade from right"], ["fade-right", "Fade from left"]]], ["Slide", [["slide-up", "Slide up"], ["slide-down", "Slide down"], ["slide-left", "Slide from right"], ["slide-right", "Slide from left"]]], ["Zoom", [["zoom-in", "Zoom in"], ["zoom-out", "Zoom out"], ["zoom-in-up", "Zoom in up"], ["zoom-in-down", "Zoom in down"]]], ["Bounce", [["bounce-in", "Soft bounce"], ["bounce-up", "Bounce up"], ["bounce-down", "Bounce down"], ["bounce-left", "Bounce from right"], ["bounce-right", "Bounce from left"]]], ["Flip & rotate", [["flip-up", "Flip up"], ["flip-x", "Flip X"], ["flip-y", "Flip Y"], ["rotate-in", "Gentle rotate"], ["roll-in", "Roll in"], ["swing-in", "Swing in"]]], ["Reveal", [["reveal-up", "Reveal up (wipe)"], ["reveal-left", "Reveal from left"], ["reveal-right", "Reveal from right"], ["reveal-center", "Reveal from center"]]], ["Special", [["blur-in", "Soft focus"], ["skew-in", "Skew in"], ["light-speed", "Light speed"], ["pop", "Pop"]]]];
	var BS_ANIMS = [];
	BS_ENTRANCES.forEach(function (g) { g[1].forEach(function (o) { BS_ANIMS.push(o[0]); }); });
	var BS_HOVERS = [["lift", "Lift gently"], ["zoom", "Soft zoom"], ["glow", "Soft shadow"], ["grayscale", "Grayscale to color"], ["tilt", "3D tilt (follows the mouse)"], ["shine", "Shine sweep"], ["border-glow", "Glowing border"], ["bounce", "Bounce"], ["rotate", "Slight rotate"], ["shrink", "Press (shrink)"]];
	var BS_LOOPS = [["float", "Float up & down"], ["pulse", "Pulse"], ["heartbeat", "Heartbeat"], ["swing", "Swing"], ["wobble", "Wobble"], ["spin", "Slow spin"], ["glow", "Glow"], ["shake", "Shake (every few seconds)"]];
	var BS_SCROLLS = [["up", "Move up while scrolling"], ["down", "Move down while scrolling"], ["left", "Move left"], ["right", "Move right"], ["zoom", "Zoom in while scrolling"], ["rotate", "Rotate while scrolling"], ["fade", "Fade in and out with scroll"]];
	var BS_EASES = [["", "Smooth (default)"], ["spring", "Spring (elastic)"], ["soft", "Soft (ease-in-out)"], ["snappy", "Snappy (fast end)"], ["linear", "Linear"]];
	function groupedSelect(label, value, groups, onChange) {
		return el(c.SelectControl, { label: label, value: value || '', onChange: onChange, __nextHasNoMarginBottom: true },
			el('option', { value: '' }, __('None', 'bigspy-ai-theme-wp')),
			groups.map(function (g) {
				return el('optgroup', { key: g[0], label: g[0] }, g[1].map(function (o) { return el('option', { key: o[0], value: o[0] }, o[1]); }));
			})
		);
	}
	function flatOptions(list) {
		return [{ value: '', label: __('None', 'bigspy-ai-theme-wp') }].concat(list.map(function (o) { return { value: o[0], label: o[1] }; }));
	}

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
			var hidden = [a.bsHideDesktop && __('desktop', 'bigspy-ai-theme-wp'), a.bsHideTablet && __('tablet', 'bigspy-ai-theme-wp'), a.bsHideMobile && __('mobile', 'bigspy-ai-theme-wp')].filter(Boolean);
			function previewAnimation() {
				var editorDocuments = [document];
				document.querySelectorAll('iframe').forEach(function (frame) {
					try {
						if (frame.contentDocument) editorDocuments.push(frame.contentDocument);
					} catch (error) { /* Ignore cross-origin frames. */ }
				});
				var target = null;
				var editorDocument = document;
				editorDocuments.some(function (candidate) {
					target = candidate.querySelector('[data-block="' + props.clientId + '"]');
					if (target) editorDocument = candidate;
					return !!target;
				});
				if (!target || !a.bsAnimation || BS_ANIMS.indexOf(a.bsAnimation) === -1) {
					return;
				}
				editorDocument.querySelectorAll('.bst-motion-preview').forEach(function (previous) {
					if (previous._bstMotionTimer) previous.ownerDocument.defaultView.clearTimeout(previous._bstMotionTimer);
					previous.classList.remove('bs-animate', 'is-visible', 'bst-motion-preview');
					Array.prototype.slice.call(previous.classList).forEach(function (className) {
						if (className.indexOf('bs-animate--') === 0) previous.classList.remove(className);
					});
					previous.style.removeProperty('--bs-delay');
					previous.style.removeProperty('--bs-duration');
					if (previous._bstAddedBodyAnimationsClass) editorDocument.body.classList.remove('bs-animations');
				});
				var bodyAlreadyAnimated = editorDocument.body.classList.contains('bs-animations');
				var duration = Math.max(300, Math.min(2500, a.bsAnimationDuration || 700));
				var delay = Math.max(0, Math.min(2000, a.bsAnimationDelay || 0));
				editorDocument.body.classList.add('bs-animations');
				target.classList.remove('is-visible');
				target.classList.add('bs-animate', 'bs-animate--' + a.bsAnimation, 'bst-motion-preview');
				target.style.setProperty('--bs-delay', delay + 'ms');
				target.style.setProperty('--bs-duration', duration + 'ms');
				target._bstAddedBodyAnimationsClass = !bodyAlreadyAnimated;
				target.offsetWidth;
				editorDocument.defaultView.requestAnimationFrame(function () { target.classList.add('is-visible'); });
				target._bstMotionTimer = editorDocument.defaultView.setTimeout(function () {
					target.classList.remove('bs-animate', 'is-visible', 'bst-motion-preview', 'bs-animate--' + a.bsAnimation);
					target.style.removeProperty('--bs-delay');
					target.style.removeProperty('--bs-duration');
					if (target._bstAddedBodyAnimationsClass) editorDocument.body.classList.remove('bs-animations');
					target._bstAddedBodyAnimationsClass = false;
				}, duration + delay + 250);
			}

			return el(Fragment, null,
				el(BlockEdit, props),
				el(be.InspectorControls, null,
					el(c.PanelBody, {
						title: __('Visibility & motion effects', 'bigspy-ai-theme-wp') + (hidden.length || a.bsAnimation || a.bsHoverEffect || a.bsLoop || a.bsScroll ? ' •' : ''),
						initialOpen: false,
						className: 'bst-global-panel'
					},
						el(Toggle, { label: __('Hide on desktop', 'bigspy-ai-theme-wp'), checked: a.bsHideDesktop, onChange: set('bsHideDesktop') }),
						el(Toggle, { label: __('Hide on tablet', 'bigspy-ai-theme-wp'), checked: a.bsHideTablet, onChange: set('bsHideTablet') }),
						el(Toggle, { label: __('Hide on mobile', 'bigspy-ai-theme-wp'), checked: a.bsHideMobile, onChange: set('bsHideMobile') }),
						el('div', { className: 'bst-motion-sub' }, __('Entrance (when scrolled into view)', 'bigspy-ai-theme-wp')),
						groupedSelect(__('Entrance animation', 'bigspy-ai-theme-wp'), a.bsAnimation, BS_ENTRANCES, set('bsAnimation')),
						a.bsAnimation ? el(c.Button, { variant: 'secondary', onClick: previewAnimation, className: 'bst-animation-preview' }, __('Preview animation', 'bigspy-ai-theme-wp')) : null,
						a.bsAnimation ? el(Range, { label: __('Duration (ms)', 'bigspy-ai-theme-wp'), min: 300, max: 2500, step: 50, value: a.bsAnimationDuration || 700, onChange: set('bsAnimationDuration') }) : null,
						a.bsAnimation ? el(Range, { label: __('Delay (ms)', 'bigspy-ai-theme-wp'), min: 0, max: 3000, step: 50, value: a.bsAnimationDelay || 0, onChange: set('bsAnimationDelay') }) : null,
						a.bsAnimation ? el(c.SelectControl, { label: __('Easing', 'bigspy-ai-theme-wp'), value: a.bsEase || '', options: BS_EASES.map(function (o) { return { value: o[0], label: o[1] }; }), onChange: set('bsEase'), __nextHasNoMarginBottom: true }) : null,
						a.bsAnimation ? el(Range, { label: __('Travel distance (px, 0 = default)', 'bigspy-ai-theme-wp'), min: 0, max: 300, step: 5, value: a.bsDistance || 0, onChange: set('bsDistance') }) : null,
						a.bsAnimation ? el(Range, { label: __('Animate inner items one by one (ms between, 0 = off)', 'bigspy-ai-theme-wp'), help: __('Great for rows of columns, cards, icons and lists.', 'bigspy-ai-theme-wp'), min: 0, max: 600, step: 20, value: a.bsStagger || 0, onChange: set('bsStagger') }) : null,
						a.bsAnimation ? el(Toggle, { label: __('Replay every time it scrolls into view', 'bigspy-ai-theme-wp'), checked: !!a.bsRepeat, onChange: set('bsRepeat') }) : null,
						el('div', { className: 'bst-motion-sub' }, __('Hover', 'bigspy-ai-theme-wp')),
						el(c.SelectControl, { label: __('Effect on hover', 'bigspy-ai-theme-wp'), value: a.bsHoverEffect || '', options: flatOptions(BS_HOVERS), onChange: set('bsHoverEffect'), __nextHasNoMarginBottom: true }),
						el('div', { className: 'bst-motion-sub' }, __('Continuous', 'bigspy-ai-theme-wp')),
						el(c.SelectControl, { label: __('Looping effect (draws attention)', 'bigspy-ai-theme-wp'), value: a.bsLoop || '', options: flatOptions(BS_LOOPS), onChange: set('bsLoop'), __nextHasNoMarginBottom: true }),
						el('div', { className: 'bst-motion-sub' }, __('While scrolling', 'bigspy-ai-theme-wp')),
						el(c.SelectControl, { label: __('Scroll motion', 'bigspy-ai-theme-wp'), value: a.bsScroll || '', options: flatOptions(BS_SCROLLS), onChange: set('bsScroll'), __nextHasNoMarginBottom: true }),
						a.bsScroll ? el(Range, { label: __('Strength', 'bigspy-ai-theme-wp'), min: 1, max: 10, value: a.bsScrollStrength || 5, onChange: set('bsScrollStrength') }) : null,
						hidden.length ? el('p', { className: 'description' }, __('Still visible in the editor. Hidden on the live site for: ', 'bigspy-ai-theme-wp') + hidden.join(', ')) : null
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
			var classes = (props.className || '').split(/\s+/).filter(Boolean);
			if (a.bsHideDesktop || a.bsHideTablet || a.bsHideMobile) {
				classes.push('bst-editor-has-visibility');
			}
			if (BS_HOVERS.some(function (h) { return h[0] === a.bsHoverEffect; })) {
				classes.push('bs-hover', 'bs-hover--' + a.bsHoverEffect);
			}
			if (a.bsLoop) {
				classes.push('bs-loop', 'bs-loop--' + a.bsLoop);
			}
			if (a.bsPresets && (window.bstBlockPresets || {})[props.name]) {
				a.bsPresets.split(/\s+/).filter(Boolean).forEach(function (id) { classes.push('bsp-' + id); });
			}
			return el(BlockListBlock, assign(props, { className: classes.join(' ') }));
		};
	}, 'withBstHiddenClass');

	wp.hooks.addFilter('editor.BlockListBlock', 'bst/hidden-class', withHiddenClass);

	/* "+" on the top edge of the first block of every level, so blocks can be added above it directly. */
	var openInserterAt = function (rootClientId, index) {
		var stores = ['core/editor', 'core/edit-post', 'core/edit-site'];
		for (var i = 0; i < stores.length; i++) {
			var store = wp.data.dispatch(stores[i]);
			if (store && typeof store.setIsInserterOpened === 'function') {
				store.setIsInserterOpened({ rootClientId: rootClientId || undefined, insertionIndex: index });
				return;
			}
		}
	};
	/* Sit on the top edge of the block that follows, centred on it (the parent may be wider, e.g. a row). */
	var placeTopInserter = function (node) {
		if (!node) {
			return;
		}
		var place = function () {
			var next = node.nextElementSibling;
			if (!next) {
				return;
			}
			node.style.top = next.offsetTop + 'px';
			node.style.left = (next.offsetLeft + next.offsetWidth / 2) + 'px';
		};
		place();
		(node.ownerDocument.defaultView || window).requestAnimationFrame(place);
		if (!node.bstPlaced && node.nextElementSibling) {
			node.bstPlaced = true;
			node.nextElementSibling.addEventListener('mouseenter', place);
		}
	};
	var withTopInserter = wp.compose.createHigherOrderComponent(function (BlockEdit) {
		return function (props) {
			var info = wp.data.useSelect(function (select) {
				var store = select('core/block-editor');
				var root = store.getBlockRootClientId(props.clientId);
				return { root: root, first: store.getBlockIndex(props.clientId) === 0, canInsert: !store.getTemplateLock || !store.getTemplateLock(root) };
			}, [props.clientId]);
			if (!info.first || !info.canInsert) {
				return el(BlockEdit, props);
			}
			return el(wp.element.Fragment, null,
				el('div', { className: 'bst-top-ins', contentEditable: false, ref: placeTopInserter, onMouseEnter: function (e) { placeTopInserter(e.currentTarget); } },
					el('button', {
						type: 'button',
						className: 'bst-top-ins__btn',
						title: __('Add a block above', 'bigspy-ai-theme-wp'),
						'aria-label': __('Add a block above', 'bigspy-ai-theme-wp'),
						onMouseDown: function (e) { e.preventDefault(); e.stopPropagation(); },
						onClick: function (e) {
							e.preventDefault();
							e.stopPropagation();
							openInserterAt(info.root, 0);
						}
					}, el('svg', { viewBox: '0 0 24 24', width: 16, height: 16, 'aria-hidden': true }, el('path', { d: 'M11 5h2v14h-2zM5 11h14v2H5z', fill: 'currentColor' })))
				),
				el(BlockEdit, props)
			);
		};
	}, 'withBstTopInserter');

	wp.hooks.addFilter('editor.BlockEdit', 'bst/top-inserter', withTopInserter);

	/* Style presets: per-block looks (formerly the Customizer CSS library), previewed live. */
	var withPresetPanel = wp.compose.createHigherOrderComponent(function (BlockEdit) {
		return function (props) {
			var groups = (window.bstBlockPresets || {})[props.name];
			if (!groups || !props.isSelected) {
				return el(BlockEdit, props);
			}
			var current = (props.attributes.bsPresets || '').split(/\s+/).filter(Boolean);
			var count = current.length;
			var toggle = function (group, id) {
				var next = current.filter(function (x) { return x !== id; });
				if (next.length === current.length) {
					if (group.exclusive) {
						var ids = group.options.map(function (o) { return o[0]; });
						next = next.filter(function (x) { return ids.indexOf(x) === -1; });
					}
					next.push(id);
				}
				props.setAttributes({ bsPresets: next.join(' ') });
			};
			return el(wp.element.Fragment, null,
				el(BlockEdit, props),
				el(wp.blockEditor.InspectorControls, null,
					el(c.PanelBody, { title: __('Style presets', 'bigspy-ai-theme-wp') + (count ? ' (' + count + ')' : ''), initialOpen: false, className: 'bst-presets-panel' },
						el('p', { className: 'bst-presets-panel__hint' }, __('Click to apply, click again to remove. Colors follow your theme. Settings above still work.', 'bigspy-ai-theme-wp')),
						groups.map(function (group) {
							return el('div', { key: group.key, className: 'bst-presets-panel__group' },
								el('p', { className: 'bst-presets-panel__label' }, group.label, group.exclusive ? null : el('small', null, ' · ' + __('combine', 'bigspy-ai-theme-wp'))),
								el('div', { className: 'bst-presets-panel__chips' },
									group.options.map(function (o) {
										var on = current.indexOf(o[0]) !== -1;
										return el('button', { key: o[0], type: 'button', className: 'bst-presets-panel__chip' + (on ? ' is-on' : ''), 'aria-pressed': on, onClick: function () { toggle(group, o[0]); } }, o[1]);
									})
								)
							);
						}),
						count ? el(c.Button, { variant: 'link', isDestructive: true, onClick: function () { props.setAttributes({ bsPresets: '' }); } }, __('Clear all presets', 'bigspy-ai-theme-wp')) : null
					)
				)
			);
		};
	}, 'withBstPresetPanel');

	wp.hooks.addFilter('editor.BlockEdit', 'bst/style-presets', withPresetPanel);

	/* Pattern previews in modals (Choose a pattern, pattern explorer): fixed-height cards that
	   scroll through the whole page on hover, like browsing the real site. */
	(function () {
		var bind = function (item) {
			if (item.bstScroll) {
				return;
			}
			item.bstScroll = true;
			var box = item.querySelector('.block-editor-block-preview__container');
			if (!box) {
				item.bstScroll = false;
				return;
			}
			box.classList.add('bst-pattern-scroll');
			// WordPress renders at most 2000px of a pattern in its preview iframe; lift that cap
			// and slide the iframe so the whole page scrolls by inside the fixed-height card.
			var frame = function () {
					var ifr = box.querySelector('iframe');
				var m = inner && (inner.style.transform || '').match(/scale\(([\d.]+)\)/);
				return ifr && inner ? { inner: inner, ifr: ifr, scale: m ? parseFloat(m[1]) : 1 } : null;
			};
			item.addEventListener('mouseenter', function () {
				var f = frame();
				if (!f) {
					return;
				}
				f.inner.style.maxHeight = 'none';
				f.inner.style.overflow = 'visible';
				f.ifr.style.maxHeight = 'none';
				var full = f.ifr.contentDocument ? f.ifr.contentDocument.documentElement.scrollHeight : f.ifr.offsetHeight;
				f.ifr.style.height = full + 'px';
				var dist = full - box.getBoundingClientRect().height / f.scale;
				if (dist > 10) {
					f.ifr.style.transition = 'translate ' + Math.min(16, Math.max(3, dist * f.scale / 120)) + 's linear';
					f.ifr.style.translate = '0 ' + (-dist) + 'px';
				}
			});
			item.addEventListener('mouseleave', function () {
				var f = frame();
				if (f) {
					f.ifr.style.transition = 'translate .7s ease';
					f.ifr.style.translate = '0 0';
				}
			});
		};
		var scan = function () {
			document.querySelectorAll('.components-modal__content .block-editor-block-patterns-list__item').forEach(bind);
		};
		new MutationObserver(function () { scan(); }).observe(document.body, { childList: true, subtree: true });
	})();
})(window.wp);
