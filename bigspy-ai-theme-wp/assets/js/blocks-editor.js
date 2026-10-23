/* BIGSPY AI Theme WP – editor UI for all theme blocks (no build step required). */
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
	 * The last selection inside the editor is remembered so toolbar selects and
	 * color pickers (which take focus) still apply to the selected text.
	 */
	function VisualHtml(props) {
		var ref = wp.element.useRef(null);
		wp.element.useEffect(function () {
			var node = ref.current;
			if (node && node.innerHTML !== (props.value || '') && node.ownerDocument.activeElement !== node) {
				node.innerHTML = props.value || '';
			}
		}, [props.value]);
		wp.element.useEffect(function () {
			var node = ref.current;
			if (!node) {
				return;
			}
			if (props.editorRef) {
				props.editorRef.current = node;
			}
			if (props.onReady) {
				props.onReady(node);
			}
			var doc = node.ownerDocument;
			var remember = function () {
				var sel = doc.getSelection();
				if (sel && sel.rangeCount && node.contains(sel.anchorNode)) {
					node._bstRange = sel.getRangeAt(0).cloneRange();
				}
			};
			doc.addEventListener('selectionchange', remember);
			return function () { doc.removeEventListener('selectionchange', remember); };
		}, []);
		var sync = function (e) {
			props.onChange(e.currentTarget.innerHTML);
		};
		var paste = function (e) {
			if (!props.plainPaste) {
				return;
			}
			var text = (e.clipboardData || window.clipboardData).getData('text/plain');
			e.preventDefault();
			e.currentTarget.ownerDocument.execCommand('insertText', false, text);
		};
		var imgState = wp.element.useState(null);
		var tick = wp.element.useState(0);
		var img = imgState[0];
		var pick = function (e) {
			var t = e.target;
			if (t && t.tagName === 'IMG' && ref.current && ref.current.contains(t)) {
				imgState[1](t);
				tick[1](tick[0] + 1);
			} else if (img) {
				imgState[1](null);
			}
		};
		var commit = function () {
			if (ref.current) {
				props.onChange(ref.current.innerHTML);
			}
			tick[1](tick[0] + 1);
		};
		var box = null;
		if (img && ref.current && ref.current.contains(img)) {
			var wrap = ref.current.parentNode.getBoundingClientRect();
			var r = img.getBoundingClientRect();
			box = { left: r.left - wrap.left, top: r.top - wrap.top, width: r.width, height: r.height };
		}
		var startResize = function (e) {
			e.preventDefault();
			e.stopPropagation();
			var handle = e.currentTarget;
			try { handle.setPointerCapture(e.pointerId); } catch (err) {}
			var startX = e.clientX;
			var startW = img.getBoundingClientRect().width;
			var max = ref.current.getBoundingClientRect().width;
			var move = function (ev) {
				var w = Math.round(Math.max(16, Math.min(max, startW + (ev.clientX - startX))));
				img.style.width = w + 'px';
				img.style.height = 'auto';
				img.removeAttribute('height');
				img.setAttribute('width', w);
				tick[1](function (n) { return n + 1; });
			};
			var up = function () {
				handle.removeEventListener('pointermove', move);
				handle.removeEventListener('pointerup', up);
				commit();
			};
			handle.addEventListener('pointermove', move);
			handle.addEventListener('pointerup', up);
		};
		var align = function (mode) {
			img.classList.remove('alignleft', 'alignright', 'aligncenter');
			img.style.float = '';
			img.style.display = '';
			img.style.margin = '';
			if (mode) {
				img.classList.add('align' + mode);
			}
			commit();
		};
		var setWidth = function (w) {
			if (w) {
				img.style.width = w;
				img.style.height = 'auto';
				img.removeAttribute('width');
				img.removeAttribute('height');
			} else {
				img.style.width = '';
			}
			commit();
		};
		return el('div', { className: 'bst-visual-wrap' },
			el('div', {
				ref: ref,
				className: 'bst-code-editor__visual ' + (props.className || ''),
				contentEditable: true,
				suppressContentEditableWarning: true,
				role: 'textbox',
				'aria-multiline': 'true',
				'aria-label': props.label,
				onInput: sync,
				onBlur: sync,
				onPaste: paste,
				onClick: pick,
				onKeyDown: function () { if (img) { imgState[1](null); } }
			}),
			box ? el('div', { className: 'bst-img-resize', style: { left: box.left + 'px', top: box.top + 'px', width: box.width + 'px', height: box.height + 'px' }, contentEditable: false },
				el('span', { className: 'bst-img-resize__size' }, Math.round(box.width) + ' × ' + Math.round(box.height)),
				el('span', { className: 'bst-img-resize__handle', onPointerDown: startResize, title: __('Drag to resize', 'bigspy-ai-theme-wp') }),
				el('div', { className: 'bst-img-resize__bar', onMouseDown: function (e) { e.preventDefault(); } },
					el(c.Button, { size: 'small', icon: 'align-left', label: __('Float left', 'bigspy-ai-theme-wp'), onClick: function () { align('left'); } }),
					el(c.Button, { size: 'small', icon: 'align-center', label: __('Center', 'bigspy-ai-theme-wp'), onClick: function () { align('center'); } }),
					el(c.Button, { size: 'small', icon: 'align-right', label: __('Float right', 'bigspy-ai-theme-wp'), onClick: function () { align('right'); } }),
					el(c.Button, { size: 'small', icon: 'editor-removeformatting', label: __('Inline (no alignment)', 'bigspy-ai-theme-wp'), onClick: function () { align(''); } }),
					['25%', '50%', '100%'].map(function (w) { return el(c.Button, { key: w, size: 'small', onClick: function () { setWidth(w); } }, w); }),
					el(c.Button, { size: 'small', icon: 'image-rotate', label: __('Original size', 'bigspy-ai-theme-wp'), onClick: function () { setWidth(''); } }),
					el(c.Button, { size: 'small', icon: 'trash', isDestructive: true, label: __('Remove image', 'bigspy-ai-theme-wp'), onClick: function () { var i = img; imgState[1](null); i.parentNode.removeChild(i); commit(); } })
				)
			) : null
		);
	}

	/* Insert an image at the caret of a visual (contentEditable) editor. */
	function insertImageInto(node, media) {
		if (!node || !media || !media.url) {
			return;
		}
		var doc = node.ownerDocument;
		node.focus();
		var range = node._bstRange;
		var sel = doc.getSelection();
		if (range && node.contains(range.startContainer)) {
			sel.removeAllRanges();
			sel.addRange(range);
		} else {
			range = doc.createRange();
			range.selectNodeContents(node);
			range.collapse(false);
			sel.removeAllRanges();
			sel.addRange(range);
		}
		var w = media.width ? Math.min(media.width, 320) : 240;
		var html = '<img src="' + media.url + '" alt="' + (media.alt || '').replace(/"/g, '&quot;') + '" width="' + w + '" style="width:' + w + 'px;height:auto" class="wp-image-' + (media.id || 0) + '">';
		doc.execCommand('insertHTML', false, html);
		node.dispatchEvent(new (doc.defaultView.Event)('input', { bubbles: true }));
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

	var FB_SIZES = ['', '12', '13', '14', '15', '16', '18', '20', '22', '24', '28', '32', '36', '40', '48', '56', '64'];
	var FB_FONTS = [['', __('Font', 'bigspy-ai-theme-wp')], ['var(--bs-font-body)', __('Body font', 'bigspy-ai-theme-wp')], ['var(--bs-font-heading)', __('Heading font', 'bigspy-ai-theme-wp')], ['Arial, sans-serif', 'Arial'], ['Georgia, serif', 'Georgia'], ['"Times New Roman", serif', 'Times New Roman'], ['Tahoma, sans-serif', 'Tahoma'], ['Verdana, sans-serif', 'Verdana'], ['"Courier New", monospace', 'Courier New']];
	var FB_CHARS = ['©', '®', '™', '°', '±', '×', '÷', '…', '–', '—', '“', '”', '‘', '’', '«', '»', '•', '→', '←', '↑', '↓', '✓', '✔', '✗', '★', '☆', '♥', '☎', '✉', '€', '$', '₫', '%', '½', '¼', '²', '³', '§', '¶', '∞'];

	/**
	 * Formatting toolbar for the Text element (like the classic TinyMCE toolbar Flatsome uses):
	 * block format, font, size, inline styles, colors, lists, indent, alignment, link,
	 * table, horizontal line, special characters, plain-text paste, clear, undo / redo.
	 */
	function FormatBar(props) {
		var charsOpen = wp.element.useState(false);
		var getEditor = function () {
			return props.editorRef && props.editorRef.current;
		};
		var restore = function () {
			var node = getEditor();
			if (!node) {
				return null;
			}
			var doc = node.ownerDocument;
			node.focus();
			if (node._bstRange) {
				var sel = doc.getSelection();
				sel.removeAllRanges();
				sel.addRange(node._bstRange);
			}
			return doc;
		};
		var after = function () {
			var node = getEditor();
			if (node && props.onChange) {
				props.onChange(node.innerHTML);
			}
		};
		var exec = function (command, value, css) {
			var doc = restore();
			if (!doc) {
				return;
			}
			doc.execCommand('styleWithCSS', false, !!css);
			doc.execCommand(command, false, value === undefined ? null : value);
			doc.execCommand('styleWithCSS', false, false);
			after();
		};
		/* Wrap the selection in a span with an inline style (font size / family). */
		var wrapStyle = function (prop, value) {
			var doc = restore();
			if (!doc || !value) {
				return;
			}
			var sel = doc.getSelection();
			if (!sel.rangeCount || sel.isCollapsed) {
				return;
			}
			doc.execCommand('styleWithCSS', false, false);
			doc.execCommand('fontSize', false, '7');
			getEditor().querySelectorAll('font[size="7"]').forEach(function (f) {
				var span = doc.createElement('span');
				span.style[prop] = value;
				while (f.firstChild) {
					span.appendChild(f.firstChild);
				}
				f.parentNode.replaceChild(span, f);
			});
			after();
		};
		var btn = function (label, title, onRun, extra) {
			return el('button', Object.assign({ key: title, type: 'button', className: 'bst-format-bar__btn', title: title, 'aria-label': title, onMouseDown: function (e) { e.preventDefault(); onRun(e); } }, extra || {}), label);
		};
		var cmd = function (label, title, command, value) {
			return btn(label, title, function () { exec(command, value); });
		};
		var sep = function (k) { return el('span', { key: 'sep' + k, className: 'bst-format-bar__sep' }); };
		var select = function (key, title, options, onPick) {
			return el('select', {
				key: key, className: 'bst-format-bar__select', title: title, 'aria-label': title, value: '',
				onMouseDown: function () { var n = getEditor(); if (n && n.ownerDocument.getSelection().rangeCount) { var s = n.ownerDocument.getSelection(); if (n.contains(s.anchorNode)) { n._bstRange = s.getRangeAt(0).cloneRange(); } } },
				onChange: function (e) { var v = e.target.value; e.target.value = ''; onPick(v); }
			}, options.map(function (o) { return el('option', { key: o[0] + o[1], value: o[0] }, o[1]); }));
		};
		var color = function (key, title, label, command) {
			return el('label', { key: key, className: 'bst-format-bar__color', title: title },
				el('span', { 'aria-hidden': true }, label),
				el('input', { type: 'color', 'aria-label': title, defaultValue: command === 'foreColor' ? '#e53935' : '#fff59d', onChange: function (e) { exec(command, e.target.value, true); } })
			);
		};
		var link = function () {
			var doc = restore();
			if (!doc) {
				return;
			}
			var url = doc.defaultView.prompt(__('Link URL (https://, tel:, mailto:)', 'bigspy-ai-theme-wp'), 'https://');
			if (!url) {
				return;
			}
			var blank = /^https?:/i.test(url) && doc.defaultView.confirm(__('Open in a new tab?', 'bigspy-ai-theme-wp'));
			doc.execCommand('createLink', false, url);
			if (blank) {
				var sel = doc.getSelection();
				var a = sel.anchorNode && (sel.anchorNode.nodeType === 1 ? sel.anchorNode : sel.anchorNode.parentElement);
				a = a && a.closest('a');
				if (a) {
					a.target = '_blank';
					a.rel = 'noopener';
				}
			}
			after();
		};
		var table = function () {
			var doc = restore();
			if (!doc) {
				return;
			}
			var size = doc.defaultView.prompt(__('Table size: columns x rows', 'bigspy-ai-theme-wp'), '3x3');
			var m = size && size.match(/(\d+)\s*[x×*]\s*(\d+)/i);
			if (!m) {
				return;
			}
			var cols = Math.min(10, +m[1]);
			var rows = Math.min(30, +m[2]);
			var html = '<table class="bs-table"><thead><tr>' + new Array(cols + 1).join('<th>' + __('Heading', 'bigspy-ai-theme-wp') + '</th>') + '</tr></thead><tbody>';
			for (var r = 1; r < rows; r++) {
				html += '<tr>' + new Array(cols + 1).join('<td>&nbsp;</td>') + '</tr>';
			}
			doc.execCommand('insertHTML', false, html + '</tbody></table><p><br></p>');
			after();
		};

		return el('div', { className: 'bst-format-bar', role: 'toolbar', 'aria-label': __('Text formatting', 'bigspy-ai-theme-wp') },
			el('div', { className: 'bst-format-bar__row' },
				select('block', __('Paragraph format', 'bigspy-ai-theme-wp'), [['', __('Format', 'bigspy-ai-theme-wp')], ['p', __('Paragraph', 'bigspy-ai-theme-wp')], ['h1', 'Heading 1'], ['h2', 'Heading 2'], ['h3', 'Heading 3'], ['h4', 'Heading 4'], ['h5', 'Heading 5'], ['h6', 'Heading 6'], ['blockquote', __('Quote', 'bigspy-ai-theme-wp')], ['pre', __('Preformatted', 'bigspy-ai-theme-wp')]], function (v) { if (v) { exec('formatBlock', v); } }),
				select('font', __('Font family', 'bigspy-ai-theme-wp'), FB_FONTS, function (v) { wrapStyle('fontFamily', v); }),
				select('size', __('Font size', 'bigspy-ai-theme-wp'), FB_SIZES.map(function (s) { return [s, s ? s + 'px' : __('Size', 'bigspy-ai-theme-wp')]; }), function (v) { wrapStyle('fontSize', v ? v + 'px' : ''); }),
				sep(1),
				cmd(el('b', null, 'B'), __('Bold', 'bigspy-ai-theme-wp'), 'bold'),
				cmd(el('i', null, 'I'), __('Italic', 'bigspy-ai-theme-wp'), 'italic'),
				cmd(el('u', null, 'U'), __('Underline', 'bigspy-ai-theme-wp'), 'underline'),
				cmd(el('s', null, 'S'), __('Strikethrough', 'bigspy-ai-theme-wp'), 'strikeThrough'),
				cmd(el('span', null, 'x', el('sup', null, '2')), __('Superscript', 'bigspy-ai-theme-wp'), 'superscript'),
				cmd(el('span', null, 'x', el('sub', null, '2')), __('Subscript', 'bigspy-ai-theme-wp'), 'subscript'),
				sep(2),
				color('fore', __('Text color', 'bigspy-ai-theme-wp'), 'A', 'foreColor'),
				color('back', __('Highlight color', 'bigspy-ai-theme-wp'), '▇', 'hiliteColor'),
				sep(3),
				cmd('↶', __('Undo', 'bigspy-ai-theme-wp'), 'undo'),
				cmd('↷', __('Redo', 'bigspy-ai-theme-wp'), 'redo')
			),
			el('div', { className: 'bst-format-bar__row' },
				cmd('• ≡', __('Bulleted list', 'bigspy-ai-theme-wp'), 'insertUnorderedList'),
				cmd('1. ≡', __('Numbered list', 'bigspy-ai-theme-wp'), 'insertOrderedList'),
				cmd('⇤', __('Decrease indent', 'bigspy-ai-theme-wp'), 'outdent'),
				cmd('⇥', __('Increase indent', 'bigspy-ai-theme-wp'), 'indent'),
				sep(4),
				cmd(el('span', { className: 'bst-fb-align bst-fb-align--left' }), __('Align left', 'bigspy-ai-theme-wp'), 'justifyLeft'),
				cmd(el('span', { className: 'bst-fb-align bst-fb-align--center' }), __('Align center', 'bigspy-ai-theme-wp'), 'justifyCenter'),
				cmd(el('span', { className: 'bst-fb-align bst-fb-align--right' }), __('Align right', 'bigspy-ai-theme-wp'), 'justifyRight'),
				cmd(el('span', { className: 'bst-fb-align bst-fb-align--justify' }), __('Justify', 'bigspy-ai-theme-wp'), 'justifyFull'),
				sep(5),
				btn('🔗', __('Insert link', 'bigspy-ai-theme-wp'), link),
				cmd('⛓̸', __('Remove link', 'bigspy-ai-theme-wp'), 'unlink'),
				cmd('“ ”', __('Quote', 'bigspy-ai-theme-wp'), 'formatBlock', 'blockquote'),
				cmd('—', __('Horizontal line', 'bigspy-ai-theme-wp'), 'insertHorizontalRule'),
				btn('⊞', __('Insert table', 'bigspy-ai-theme-wp'), table),
				el('span', { key: 'chars', className: 'bst-format-bar__chars' },
					btn('Ω', __('Special characters', 'bigspy-ai-theme-wp'), function () { charsOpen[1](!charsOpen[0]); }),
					charsOpen[0] ? el('div', { className: 'bst-format-bar__charmap' }, FB_CHARS.map(function (ch) {
						return el('button', { key: ch, type: 'button', onMouseDown: function (e) { e.preventDefault(); exec('insertText', ch); charsOpen[1](false); } }, ch);
					})) : null
				),
				sep(6),
				btn('T̶', __('Paste as plain text', 'bigspy-ai-theme-wp') + (props.plainPaste ? ' ✓' : ''), function () { props.onTogglePlain(!props.plainPaste); }, { 'aria-pressed': !!props.plainPaste, className: 'bst-format-bar__btn' + (props.plainPaste ? ' is-active' : '') }),
				cmd('✕', __('Clear formatting', 'bigspy-ai-theme-wp'), 'removeFormat')
			)
		);
	}

	var ALIGN3 = { left: __('Left', 'bigspy-ai-theme-wp'), center: __('Center', 'bigspy-ai-theme-wp'), right: __('Right', 'bigspy-ai-theme-wp') };

	/* ================================================================ Section */
	var SHAPES = {"wave": ["M0,60 C150,100 350,0 500,40 C650,80 850,20 1000,50 L1000,100 L0,100 Z"], "waves": ["M0,40 C200,90 400,0 600,50 C800,100 900,30 1000,40 L1000,100 L0,100 Z", "M0,70 C200,100 450,30 650,70 C850,100 950,60 1000,70 L1000,100 L0,100 Z"], "curve": ["M0,100 C300,0 700,0 1000,100 Z"], "curve-inverse": ["M0,0 C300,100 700,100 1000,0 L1000,100 L0,100 Z"], "tilt": ["M0,100 L1000,0 L1000,100 Z"], "triangle": ["M0,100 L500,0 L1000,100 Z"], "arrow": ["M0,100 L0,70 L470,70 L500,30 L530,70 L1000,70 L1000,100 Z"], "mountains": ["M0,100 L0,60 L150,20 L300,70 L450,10 L600,60 L750,30 L900,70 L1000,40 L1000,100 Z"], "zigzag": ["M0,100 L0,70 L50,40 L100,70 L150,40 L200,70 L250,40 L300,70 L350,40 L400,70 L450,40 L500,70 L550,40 L600,70 L650,40 L700,70 L750,40 L800,70 L850,40 L900,70 L950,40 L1000,70 L1000,100 Z"], "clouds": ["M0,100 L0,70 C40,50 80,80 120,65 C160,40 220,60 250,70 C290,45 340,55 370,68 C410,40 470,55 500,70 C540,45 600,58 630,68 C670,42 730,55 760,70 C800,48 860,60 890,70 C930,50 970,62 1000,60 L1000,100 Z"], "drops": ["M0,100 L0,40 Q25,95 50,40 Q75,55 100,40 Q125,95 150,40 Q175,55 200,40 Q225,95 250,40 Q275,55 300,40 Q325,95 350,40 Q375,55 400,40 Q425,95 450,40 Q475,55 500,40 Q525,95 550,40 Q575,55 600,40 Q625,95 650,40 Q675,55 700,40 Q725,95 750,40 Q775,55 800,40 Q825,95 850,40 Q875,55 900,40 Q925,95 950,40 Q975,55 1000,40 L1000,100 Z"], "pyramids": ["M0,100 L0,80 L120,30 L240,80 L360,20 L480,80 L600,35 L720,80 L840,25 L1000,80 L1000,100 Z"], "book": ["M0,100 L0,60 C200,60 400,40 500,100 C600,40 800,60 1000,60 L1000,100 Z"], "split": ["M0,100 L0,60 L460,60 L500,100 L540,60 L1000,60 L1000,100 Z"], "waves-pattern": ["M0,100 L0,60 C20,30 40,90 62,60 C82,30 102,90 124,60 C144,30 164,90 186,60 C206,30 226,90 248,60 C268,30 288,90 310,60 C330,30 350,90 372,60 C392,30 412,90 434,60 C454,30 474,90 496,60 C516,30 536,90 558,60 C578,30 598,90 620,60 C640,30 660,90 682,60 C702,30 722,90 744,60 C764,30 784,90 806,60 C826,30 846,90 868,60 C888,30 908,90 930,60 C950,30 970,90 992,60 C1012,30 1032,90 1054,60 L1000,100 Z"]};
	var SHAPE_LABELS = { '': __('None', 'bigspy-ai-theme-wp'), wave: __('Wave', 'bigspy-ai-theme-wp'), waves: __('Layered waves', 'bigspy-ai-theme-wp'), curve: __('Curve', 'bigspy-ai-theme-wp'), 'curve-inverse': __('Curve (inverse)', 'bigspy-ai-theme-wp'), tilt: __('Tilt', 'bigspy-ai-theme-wp'), triangle: __('Triangle', 'bigspy-ai-theme-wp'), arrow: __('Arrow', 'bigspy-ai-theme-wp'), mountains: __('Mountains', 'bigspy-ai-theme-wp'), zigzag: __('Zigzag', 'bigspy-ai-theme-wp'), clouds: __('Clouds', 'bigspy-ai-theme-wp'), drops: __('Drops', 'bigspy-ai-theme-wp'), pyramids: __('Pyramids', 'bigspy-ai-theme-wp'), book: __('Book', 'bigspy-ai-theme-wp'), split: __('Split', 'bigspy-ai-theme-wp'), 'waves-pattern': __('Wave pattern', 'bigspy-ai-theme-wp') };
	function shapeDivider(a, key, pos) {
		var paths = SHAPES[a['divider' + key]];
		if (!paths) {
			return null;
		}
		return el('div', { className: 'bs-shape bs-shape--' + pos + (a['divider' + key + 'Flip'] ? ' is-flipped' : '') + (a['divider' + key + 'Front'] ? ' is-front' : ''), style: { color: a['divider' + key + 'Color'] || '#ffffff', '--bs-shape-h': a['divider' + key + 'Height'] + 'px' }, 'aria-hidden': true },
			el('svg', { viewBox: '0 0 1000 100', preserveAspectRatio: 'none', fill: 'currentColor' },
				paths.map(function (d, i) { return el('path', { key: i, d: d, opacity: i < paths.length - 1 ? 0.45 : 1 }); })
			)
		);
	}
	function shapePanel(a, props, bind, key, title) {
		return el(Panel, { title: title, initialOpen: false },
			el(Select, Object.assign({ label: __('Shape', 'bigspy-ai-theme-wp'), options: SHAPE_LABELS }, bind('divider' + key))),
			a['divider' + key] ? el(Range, Object.assign({ label: __('Height (px)', 'bigspy-ai-theme-wp'), min: 10, max: 400 }, bind('divider' + key + 'Height'))) : null,
			a['divider' + key] ? el(Toggle, Object.assign({ label: __('Flip horizontally', 'bigspy-ai-theme-wp') }, bind('divider' + key + 'Flip', true))) : null,
			a['divider' + key] ? el(Toggle, Object.assign({ label: __('Bring to front (over content)', 'bigspy-ai-theme-wp') }, bind('divider' + key + 'Front', true))) : null,
			a['divider' + key] ? el(B.ColorPanel, { title: __('Shape color', 'bigspy-ai-theme-wp'), settings: [{ label: __('Color (usually the next/previous section background)', 'bigspy-ai-theme-wp'), value: a['divider' + key + 'Color'], onChange: function (v) { var o = {}; o['divider' + key + 'Color'] = v || ''; props.setAttributes(o); } }] }) : null
		);
	}
	var SECTION_BGS = [["soft-primary", "Soft primary"], ["soft-secondary", "Soft secondary"], ["primary", "Primary"], ["gradient", "Brand gradient"], ["gradient-soft", "Soft gradient"], ["sunrise", "Sunrise"], ["ocean", "Ocean"], ["mint", "Mint"], ["peach", "Peach"], ["lavender", "Lavender"], ["dark", "Dark"], ["dots", "Dots"], ["grid", "Grid"], ["mesh", "Mesh"]];
	var SECTION_DARK = ['primary', 'gradient', 'sunrise', 'ocean', 'dark'];
	function SectionBgPicker(props) {
		var a = props.attributes;
		return el('div', { className: 'bst-bg-presets' },
			el('button', { type: 'button', className: 'bst-bg-presets__item bst-bg-presets__item--none' + (!a.bgPreset ? ' is-selected' : ''), onClick: function () { props.setAttributes({ bgPreset: '' }); }, title: __('None', 'bigspy-ai-theme-wp') }, '∅'),
			SECTION_BGS.map(function (bg) {
				return el('button', { key: bg[0], type: 'button', className: 'bst-bg-presets__item bs-bg--' + bg[0] + (a.bgPreset === bg[0] ? ' is-selected' : ''), title: bg[1], 'aria-label': bg[1], onClick: function () { props.setAttributes({ bgPreset: bg[0] }); } });
			})
		);
	}
	register('section', function (props) {
		var a = props.attributes;
		var bind = B.binder(props);
		var classes = ['bs-section', 'bs-section--' + a.contentWidth, 'bs-section--valign-' + a.verticalAlign];
		if (a.bgPreset) {
			classes.push('bs-bg--' + a.bgPreset);
		}
		var scheme = a.textScheme || (SECTION_DARK.indexOf(a.bgPreset) !== -1 ? 'light' : '');
		if (scheme) {
			classes.push('is-scheme-' + scheme);
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
			template: [['bst/title', { text: __('Section title', 'bigspy-ai-theme-wp') }], ['core/paragraph', { placeholder: __('Add content, Rows, Banners…', 'bigspy-ai-theme-wp') }]],
			templateLock: false
		});

		return el(Fragment, null,
			el(Inspector, null,
				el(Panel, { title: __('Layout', 'bigspy-ai-theme-wp') },
					el(Select, Object.assign({ label: __('Content width', 'bigspy-ai-theme-wp'), options: { boxed: __('Container', 'bigspy-ai-theme-wp'), narrow: __('Narrow (800px)', 'bigspy-ai-theme-wp'), full: __('Full width', 'bigspy-ai-theme-wp') } }, bind('contentWidth'))),
					el(Range, Object.assign({ label: __('Padding top/bottom – desktop', 'bigspy-ai-theme-wp'), max: 300, reset: 80 }, bind('paddingDesktop'))),
					el(Range, Object.assign({ label: __('Padding top/bottom – tablet', 'bigspy-ai-theme-wp'), max: 300, reset: 60 }, bind('paddingTablet'))),
					el(Range, Object.assign({ label: __('Padding top/bottom – mobile', 'bigspy-ai-theme-wp'), max: 300, reset: 40 }, bind('paddingMobile'))),
					el(Range, Object.assign({ label: __('Minimum height', 'bigspy-ai-theme-wp'), max: a.minHeightUnit === 'vh' ? 100 : 1200 }, bind('minHeight'))),
					el(Select, Object.assign({ label: __('Height unit', 'bigspy-ai-theme-wp'), options: { px: 'px', vh: __('% of screen (vh)', 'bigspy-ai-theme-wp') } }, bind('minHeightUnit'))),
					el(Select, Object.assign({ label: __('Vertical align', 'bigspy-ai-theme-wp'), options: { top: __('Top', 'bigspy-ai-theme-wp'), center: __('Middle', 'bigspy-ai-theme-wp'), bottom: __('Bottom', 'bigspy-ai-theme-wp') } }, bind('verticalAlign')))
				),
				el(Panel, { title: __('Background color style', 'bigspy-ai-theme-wp') },
					el(SectionBgPicker, props),
					el('p', { className: 'description' }, __('Colors follow your site palette (Customizer → Colors). Dark styles switch the text to white automatically.', 'bigspy-ai-theme-wp'))
				),
				el(Panel, { title: __('Background image & overlay', 'bigspy-ai-theme-wp'), initialOpen: false },
					el(B.MediaPicker, {
						label: __('Background image', 'bigspy-ai-theme-wp'),
						id: a.bgImageId,
						url: a.bgImage,
						onChange: function (m) {
							props.setAttributes({ bgImageId: m.id, bgImage: m.url });
						}
					}),
					a.bgImage ? el(Select, Object.assign({ label: __('Size', 'bigspy-ai-theme-wp'), options: { cover: __('Cover', 'bigspy-ai-theme-wp'), contain: __('Contain', 'bigspy-ai-theme-wp'), auto: __('Original', 'bigspy-ai-theme-wp') } }, bind('bgSize'))) : null,
					a.bgImage ? el(Select, Object.assign({ label: __('Position', 'bigspy-ai-theme-wp'), options: { 'center center': __('Center', 'bigspy-ai-theme-wp'), 'center top': __('Top', 'bigspy-ai-theme-wp'), 'center bottom': __('Bottom', 'bigspy-ai-theme-wp'), 'left center': __('Left', 'bigspy-ai-theme-wp'), 'right center': __('Right', 'bigspy-ai-theme-wp') } }, bind('bgPosition'))) : null,
					a.bgImage ? el(Toggle, Object.assign({ label: __('Fixed background', 'bigspy-ai-theme-wp') }, bind('bgFixed', true))) : null,
					a.bgImage && !a.bgFixed ? el(Range, Object.assign({ label: __('Smooth parallax strength (%, 0 = off)', 'bigspy-ai-theme-wp'), help: __('The background moves slower than the page while scrolling. Works on phones too.', 'bigspy-ai-theme-wp'), max: 60 }, bind('parallax'))) : null,
					a.bgImage ? el(Range, Object.assign({ label: __('Background blur (px)', 'bigspy-ai-theme-wp'), help: __('Adds a frosted blur layer over the photo (the photo itself is not zoomed) so text stays easy to read.', 'bigspy-ai-theme-wp'), max: 30, reset: 0 }, bind('bgBlur'))) : null,
					a.bgImage ? el(Range, Object.assign({ label: __('Background brightness (%)', 'bigspy-ai-theme-wp'), help: __('Below 100 darkens the photo, above 100 lightens it.', 'bigspy-ai-theme-wp'), min: 20, max: 150, reset: 100 }, bind('bgBrightness'))) : null,
					el(Range, Object.assign({ label: __('Overlay opacity (%)', 'bigspy-ai-theme-wp'), max: 100, reset: 50 }, bind('overlayOpacity'))),
					el(Select, Object.assign({ label: __('Text color scheme', 'bigspy-ai-theme-wp'), options: { '': __('Default', 'bigspy-ai-theme-wp'), light: __('Light text (for dark backgrounds)', 'bigspy-ai-theme-wp'), dark: __('Dark text', 'bigspy-ai-theme-wp') } }, bind('textScheme')))
				),
				el(B.ColorPanel, { title: __('Overlay color', 'bigspy-ai-theme-wp'), settings: [{ label: __('Overlay', 'bigspy-ai-theme-wp'), value: a.overlayColor, onChange: function (v) { props.setAttributes({ overlayColor: v || '' }); } }] }),
				el(Panel, { title: __('Background video', 'bigspy-ai-theme-wp'), initialOpen: false },
					el(Text, Object.assign({ label: __('YouTube URL or .mp4/.webm file URL', 'bigspy-ai-theme-wp'), help: __('Plays muted and looped behind the content. Set a background image too as the poster/fallback.', 'bigspy-ai-theme-wp') }, bind('videoUrl'))),
					a.videoUrl ? el(Toggle, Object.assign({ label: __('Also play on mobile', 'bigspy-ai-theme-wp') }, bind('videoMobile', true))) : null
				),
				shapePanel(a, props, bind, 'Top', __('Shape divider – top', 'bigspy-ai-theme-wp')),
				shapePanel(a, props, bind, 'Bottom', __('Shape divider – bottom', 'bigspy-ai-theme-wp'))
			),
			el('section', blockProps,
				shapeDivider(a, 'Top', 'top'),
				shapeDivider(a, 'Bottom', 'bottom'),
				a.bgImage ? el('div', { className: 'bs-section__bg', style: { backgroundImage: 'url(' + a.bgImage + ')', backgroundSize: a.bgSize, backgroundPosition: a.bgPosition, filter: a.bgBrightness != null && a.bgBrightness !== 100 ? 'brightness(' + a.bgBrightness / 100 + ')' : undefined } }) : null,
				a.bgImage && a.bgBlur ? el('div', { className: 'bs-section__blur', style: { '--bs-bg-blur': a.bgBlur + 'px' } }) : null,
				a.overlayColor ? el('div', { className: 'bs-section__overlay', style: { background: a.overlayColor, opacity: a.overlayOpacity / 100 } }) : null,
				el('div', innerProps)
			)
		);
	}, saveInner);

	/* Editor device preview → attribute suffix ('Desktop' | 'Tablet' | 'Mobile'). */
	function useDevice() {
		return useSelect(function (select) {
			var ed = select('core/editor');
			return ed && ed.getDeviceType ? ed.getDeviceType() : 'Desktop';
		}, []);
	}

	/**
	 * Drag the right edge of a Column to change its width (snaps to 12ths).
	 * Follows the editor's device preview: desktop, tablet or mobile span.
	 */
	function ColumnResizer(props) {
		var device = useDevice();
		var key = device === 'Tablet' ? 'spanTablet' : (device === 'Mobile' ? 'spanMobile' : 'spanDesktop');
		var current = props.attributes[key] || props.attributes.spanDesktop;
		var drag = wp.element.useState(null);
		var ed = useDispatch('core/block-editor');
		var info = useSelect(function (select) {
			var store = select('core/block-editor');
			var root = store.getBlockRootClientId(props.clientId);
			var ids = store.getBlockOrder(root);
			var i = ids.indexOf(props.clientId);
			var next = ids[i + 1] ? store.getBlock(ids[i + 1]) : null;
			return { next: next && next.name === 'bst/column' ? next : null };
		}, [props.clientId]);
		var spanOf = function (attrs) {
			return attrs[key] || attrs.spanDesktop || 12;
		};
		var onDown = function (e) {
			if (e.button !== 0) {
				return;
			}
			e.preventDefault();
			e.stopPropagation();
			var handle = e.currentTarget;
			var col = handle.closest('.bs-column');
			var row = col && col.parentElement;
			if (!row) {
				return;
			}
			try { handle.setPointerCapture(e.pointerId); } catch (err) {}
			var rowRect = row.getBoundingClientRect();
			var colRect = col.getBoundingClientRect();
			var unit = rowRect.width / 12;
			var start = current;
			var next = info.next;
			var nextStart = next ? spanOf(next.attributes) : 0;
			// Elementor-style: the column on the right gives / takes the space (unless Alt is held or they don't share a line).
			var pair = !!next && !e.altKey && start + nextStart <= 12;
			var last = start;
			var move = function (ev) {
				var span = Math.max(1, Math.min(12, Math.round((ev.clientX - colRect.left) / unit)));
				var nextSpan = 0;
				if (pair && !ev.altKey) {
					span = Math.min(span, start + nextStart - 1);
					nextSpan = start + nextStart - span;
				}
				if (span !== last) {
					last = span;
					var o = {};
					o[key] = span;
					props.setAttributes(o);
					if (nextSpan) {
						var n = {};
						n[key] = nextSpan;
						ed.updateBlockAttributes(next.clientId, n);
					}
				}
				drag[1](nextSpan ? span + ' | ' + nextSpan : String(span));
			};
			var up = function () {
				handle.removeEventListener('pointermove', move);
				handle.removeEventListener('pointerup', up);
				handle.removeEventListener('pointercancel', up);
				drag[1](null);
			};
			handle.addEventListener('pointermove', move);
			handle.addEventListener('pointerup', up);
			handle.addEventListener('pointercancel', up);
			drag[1](String(start));
		};
		var label = drag[0] ? drag[0].split(' | ').map(function (n) { return n + '/12'; }).join('  ·  ') : current + '/12';
		return el('div', {
			className: 'bst-col-resizer' + (drag[0] ? ' is-dragging' : ''),
			onPointerDown: onDown,
			onDoubleClick: function (e) {
				e.stopPropagation();
				var o = {};
				o[key] = device === 'Desktop' ? 6 : 0;
				props.setAttributes(o);
			},
			title: __('Drag to resize. The next column adjusts too; hold Alt to resize only this one. Double-click to reset.', 'bigspy-ai-theme-wp'),
			contentEditable: false
		},
			el('span', { className: 'bst-col-resizer__grip', 'aria-hidden': true }),
			el('span', { className: 'bst-col-resizer__badge' }, label + (device !== 'Desktop' ? ' · ' + (device === 'Tablet' ? __('Tablet', 'bigspy-ai-theme-wp') : __('Mobile', 'bigspy-ai-theme-wp')) : ''))
		);
	}

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
				el(Panel, { title: __('Column layout', 'bigspy-ai-theme-wp') },
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
					el('p', { className: 'description' }, __('Fine-tune each column width (per device) by selecting the column.', 'bigspy-ai-theme-wp'))
				),
				el(Panel, { title: __('Row settings', 'bigspy-ai-theme-wp') },
					el(Select, Object.assign({ label: __('Gap between columns', 'bigspy-ai-theme-wp'), options: { none: __('None', 'bigspy-ai-theme-wp'), small: __('Small', 'bigspy-ai-theme-wp'), medium: __('Medium', 'bigspy-ai-theme-wp'), large: __('Large', 'bigspy-ai-theme-wp') } }, bind('gap'))),
					el(Select, Object.assign({ label: __('Vertical align', 'bigspy-ai-theme-wp'), options: { stretch: __('Equal height', 'bigspy-ai-theme-wp'), top: __('Top', 'bigspy-ai-theme-wp'), center: __('Middle', 'bigspy-ai-theme-wp'), bottom: __('Bottom', 'bigspy-ai-theme-wp') } }, bind('verticalAlign'))),
					el(Select, Object.assign({ label: __('Horizontal align (when columns do not fill 12)', 'bigspy-ai-theme-wp'), options: { start: __('Left', 'bigspy-ai-theme-wp'), center: __('Center', 'bigspy-ai-theme-wp'), end: __('Right', 'bigspy-ai-theme-wp') } }, bind('justify'))),
					el(Toggle, Object.assign({ label: __('Reverse column order on mobile', 'bigspy-ai-theme-wp') }, bind('reverseMobile', true)))
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
		var innerProps = useInnerBlocksProps({ className: 'bst-column-inner' }, {
			templateLock: false,
			renderAppender: hasChildren ? undefined : InnerBlocks.ButtonBlockAppender
		});
		return el(Fragment, null,
			el(Inspector, null,
				el(Panel, { title: __('Column width (out of 12)', 'bigspy-ai-theme-wp') },
					el(Range, Object.assign({ label: __('Desktop', 'bigspy-ai-theme-wp'), min: 1, max: 12 }, bind('spanDesktop'))),
					el(Range, Object.assign({ label: __('Tablet (0 = same as desktop)', 'bigspy-ai-theme-wp'), min: 0, max: 12 }, bind('spanTablet'))),
					el(Range, Object.assign({ label: __('Mobile', 'bigspy-ai-theme-wp'), min: 1, max: 12 }, bind('spanMobile'))),
					el(Select, Object.assign({ label: __('Vertical align', 'bigspy-ai-theme-wp'), options: { top: __('Top', 'bigspy-ai-theme-wp'), center: __('Middle', 'bigspy-ai-theme-wp'), bottom: __('Bottom', 'bigspy-ai-theme-wp') } }, bind('verticalAlign'))),
					el(Select, Object.assign({ label: __('Text align', 'bigspy-ai-theme-wp'), options: B.assign({ '': __('Default', 'bigspy-ai-theme-wp') }, ALIGN3) }, bind('textAlign'))),
					el('p', { className: 'description' }, __('Tip: drag the right edge of the column on the canvas. Switch the preview to Tablet/Mobile to set those widths.', 'bigspy-ai-theme-wp'))
				)
			),
			el('div', blockProps, el('div', innerProps), el(ColumnResizer, props))
		);
	}, saveInner);

	/* ================================================================ Banner */
	register('banner', function (props) {
		var a = props.attributes;
		var bind = B.binder(props);
		var classes = 'bs-banner bs-banner--x-' + a.posX + ' bs-banner--y-' + a.posY + ' is-scheme-' + a.textScheme + ' has-text-align-' + a.textAlign + (a.heightMode === 'image' && a.imgRatio ? ' bs-banner--h-image' : '');
		var blockProps = useBlockProps({
			className: classes,
			style: {
				'--bs-banner-hd': a.heightMode === 'vh' ? a.heightVhD + 'vh' : a.heightDesktop + 'px',
				'--bs-banner-ht': a.heightMode === 'vh' ? a.heightVhT + 'vh' : a.heightTablet + 'px',
				'--bs-banner-hm': a.heightMode === 'vh' ? a.heightVhM + 'vh' : a.heightMobile + 'px',
				'--bs-banner-ar': a.heightMode === 'image' && a.imgRatio ? a.imgRatio : undefined,
				'--bs-banner-width': a.contentWidth + '%',
				'--bs-banner-radius': a.radius + 'px'
			}
		});
		var innerProps = useInnerBlocksProps({ className: 'bs-banner__box' }, {
			template: [
				['core/heading', { level: 2, placeholder: __('Banner title', 'bigspy-ai-theme-wp') }],
				['core/paragraph', { placeholder: __('Short description…', 'bigspy-ai-theme-wp') }],
				['core/buttons', {}, [['core/button', { text: __('Learn more', 'bigspy-ai-theme-wp') }]]]
			]
		});
		var setImage = function (m) {
			props.setAttributes({ imageId: m.id, imageUrl: m.url, imgRatio: m.width && m.height ? Math.round(m.width / m.height * 10000) / 10000 : 0 });
		};

		return el(Fragment, null,
			el(Inspector, null,
				el(Panel, { title: __('Image', 'bigspy-ai-theme-wp') },
					el(B.MediaPicker, { id: a.imageId, url: a.imageUrl, onChange: setImage }),
					a.imageUrl ? el(B.MediaPicker, { label: __('Mobile image (optional)', 'bigspy-ai-theme-wp'), id: a.imageMobileId, url: a.imageMobileUrl, onChange: function (m) { props.setAttributes({ imageMobileId: m.id, imageMobileUrl: m.url }); } }) : null,
					a.imageUrl ? el(c.FocalPointPicker, {
						label: __('Focal point', 'bigspy-ai-theme-wp'),
						url: a.imageUrl,
						value: { x: a.focalX / 100, y: a.focalY / 100 },
						onChange: function (v) {
							props.setAttributes({ focalX: Math.round(v.x * 100), focalY: Math.round(v.y * 100) });
						}
					}) : null,
					el(Select, Object.assign({ label: __('Hover effect', 'bigspy-ai-theme-wp'), options: { none: __('None', 'bigspy-ai-theme-wp'), zoom: __('Zoom image', 'bigspy-ai-theme-wp'), glow: __('Lighten overlay', 'bigspy-ai-theme-wp') } }, bind('hover')))
				),
				el(Panel, { title: __('Size', 'bigspy-ai-theme-wp') },
					sliderChoice(props, __('Height', 'bigspy-ai-theme-wp'), 'heightMode', [['px', __('Fixed (px)', 'bigspy-ai-theme-wp')], ['vh', __('% of screen', 'bigspy-ai-theme-wp')], ['image', __('Fit image', 'bigspy-ai-theme-wp')]]),
					a.heightMode === 'image' ? el('p', { className: 'description' }, a.imgRatio ? __('The banner follows the image proportions, so the whole image shows. Inside a slider with a common height, that height wins.', 'bigspy-ai-theme-wp') : __('Re-select the image once so its proportions are known.', 'bigspy-ai-theme-wp')) : null,
					a.heightMode === 'vh' ? el(Range, Object.assign({ label: __('Height – desktop (% of screen)', 'bigspy-ai-theme-wp'), min: 10, max: 100, reset: 100 }, bind('heightVhD'))) : null,
					a.heightMode === 'vh' ? el(Range, Object.assign({ label: __('Height – tablet (% of screen)', 'bigspy-ai-theme-wp'), min: 10, max: 100, reset: 80 }, bind('heightVhT'))) : null,
					a.heightMode === 'vh' ? el(Range, Object.assign({ label: __('Height – mobile (% of screen)', 'bigspy-ai-theme-wp'), min: 10, max: 100, reset: 70 }, bind('heightVhM'))) : null,
					!a.heightMode || a.heightMode === 'px' ? el(Range, Object.assign({ label: __('Height – desktop (px)', 'bigspy-ai-theme-wp'), min: 100, max: 1200, reset: 500 }, bind('heightDesktop'))) : null,
					!a.heightMode || a.heightMode === 'px' ? el(Range, Object.assign({ label: __('Height – tablet (px)', 'bigspy-ai-theme-wp'), min: 100, max: 1200, reset: 420 }, bind('heightTablet'))) : null,
					!a.heightMode || a.heightMode === 'px' ? el(Range, Object.assign({ label: __('Height – mobile (px)', 'bigspy-ai-theme-wp'), min: 100, max: 1200, reset: 340 }, bind('heightMobile'))) : null,
					el(Range, Object.assign({ label: __('Corner radius (px)', 'bigspy-ai-theme-wp'), max: 60 }, bind('radius')))
				),
				el(Panel, { title: __('Content position', 'bigspy-ai-theme-wp') },
					el(Select, Object.assign({ label: __('Horizontal', 'bigspy-ai-theme-wp'), options: ALIGN3 }, bind('posX'))),
					el(Select, Object.assign({ label: __('Vertical', 'bigspy-ai-theme-wp'), options: { top: __('Top', 'bigspy-ai-theme-wp'), middle: __('Middle', 'bigspy-ai-theme-wp'), bottom: __('Bottom', 'bigspy-ai-theme-wp') } }, bind('posY'))),
					el(Range, Object.assign({ label: __('Content width (%)', 'bigspy-ai-theme-wp'), min: 20, max: 100, reset: 70 }, bind('contentWidth'))),
					el(Select, Object.assign({ label: __('Text align', 'bigspy-ai-theme-wp'), options: ALIGN3 }, bind('textAlign'))),
					el(Select, Object.assign({ label: __('Text color', 'bigspy-ai-theme-wp'), options: { light: __('Light', 'bigspy-ai-theme-wp'), dark: __('Dark', 'bigspy-ai-theme-wp') } }, bind('textScheme')))
				),
				el(Panel, { title: __('Overlay & link', 'bigspy-ai-theme-wp'), initialOpen: false },
					el(Range, Object.assign({ label: __('Overlay opacity (%)', 'bigspy-ai-theme-wp'), max: 100, reset: 35 }, bind('overlayOpacity'))),
					el(Text, Object.assign({ label: __('Link the whole banner (URL)', 'bigspy-ai-theme-wp'), type: 'url' }, bind('link'))),
					a.link ? el(Toggle, Object.assign({ label: __('Open in new tab', 'bigspy-ai-theme-wp') }, bind('linkNewTab', true))) : null
				),
				el(B.ColorPanel, { title: __('Overlay color', 'bigspy-ai-theme-wp'), settings: [{ label: __('Overlay', 'bigspy-ai-theme-wp'), value: a.overlayColor, onChange: function (v) { props.setAttributes({ overlayColor: v || '' }); } }] })
			),
			el('div', blockProps,
				el('div', { className: 'bs-banner__bg' },
					a.imageUrl ? el('img', { className: 'bs-banner__img', src: a.imageUrl, alt: '', style: { objectPosition: a.focalX + '% ' + a.focalY + '%' } }) :
						el(be.MediaPlaceholder, { icon: 'format-image', labels: { title: __('Banner image', 'bigspy-ai-theme-wp') }, accept: 'image/*', allowedTypes: ['image'], onSelect: function (m) { setImage({ id: m.id, url: m.url }); } })
				),
				a.overlayColor && a.imageUrl ? el('div', { className: 'bs-banner__overlay', style: { background: a.overlayColor, opacity: a.overlayOpacity / 100 } }) : null,
				el('div', { className: 'bs-banner__content' }, el('div', innerProps))
			)
		);
	}, saveInner);

	/* ================================================================ Slider */
	/* Button-row picker used by the slider, accordion and posts panels. */
	function sliderChoice(props, label, key, list) {
		var a = props.attributes;
		return el('div', { className: 'bst-choice' },
			el('span', { className: 'bst-choice__label' }, label),
			el('div', { className: 'bst-choice__row' }, list.map(function (o) {
				return el('button', { key: o[0], type: 'button', className: 'bst-choice__item' + ((a[key] || list[0][0]) === o[0] ? ' is-selected' : ''), onClick: function () { var x = {}; x[key] = o[0]; props.setAttributes(x); } }, o[1]);
			}))
		);
	}


	/* ================================================================ Slide navigator (Flatsome-style) */
	var SLIDE_ADD = [['bst/banner', __('Banner', 'bigspy-ai-theme-wp')], ['bst/image', __('Image', 'bigspy-ai-theme-wp')], ['bst/section', __('Section', 'bigspy-ai-theme-wp')], ['bst/row', __('Grid (row)', 'bigspy-ai-theme-wp')]];
	function useSlideNav(clientId) {
		var state = wp.element.useState(0);
		var single = wp.element.useState(true);
		var data = useSelect(function (select) {
			var store = select('core/block-editor');
			var ids = store.getBlockOrder(clientId);
			var selected = store.getSelectedBlockClientId();
			var owner = -1;
			if (selected) {
				var chain = [selected].concat(store.getBlockParents(selected, true));
				for (var i = 0; i < chain.length && owner < 0; i++) {
					owner = ids.indexOf(chain[i]);
				}
			}
			return {
				ids: ids,
				owner: owner,
				focused: selected === clientId || owner >= 0,
				names: ids.map(function (id) {
					var block = store.getBlock(id);
					var type = block && wp.blocks.getBlockType(block.name);
					var meta = block && block.attributes && block.attributes.metadata;
					return (meta && meta.name) || (type ? type.title : '?');
				})
			};
		}, [clientId]);
		var active = Math.min(state[0], Math.max(0, data.ids.length - 1));
		wp.element.useEffect(function () {
			if (data.owner >= 0 && data.owner !== state[0]) {
				state[1](data.owner);
			}
		}, [data.owner]);
		return {
			ids: data.ids,
			names: data.names,
			focused: data.focused,
			active: active,
			setActive: state[1],
			single: single[0] && data.ids.length > 1,
			setSingle: single[1],
			showAll: !single[0],
			style: single[0] && data.ids.length > 1 ? el('style', null, '#block-' + clientId + ' > .bst-slider-editor__track > .block-editor-block-list__block:not([data-block="' + data.ids[active] + '"]){display:none!important}') : null
		};
	}

	function slideNavBar(nav, clientId) {
		var ed = wp.data.dispatch('core/block-editor');
		var go = function (i, select) {
			nav.setActive(i);
			if (select && nav.ids[i]) {
				ed.selectBlock(nav.ids[i]);
			}
		};
		var add = function (name) {
			var block = wp.blocks.createBlock(name);
			ed.insertBlocks(block, nav.ids.length, clientId);
			nav.setActive(nav.ids.length);
		};
		var id = nav.ids[nav.active];
		var stop = function (e) { e.stopPropagation(); };
		return el('div', { className: 'bst-slidenav', contentEditable: false, onMouseDown: stop, onClick: stop },
			el('span', { className: 'bst-slidenav__title' }, __('Slider', 'bigspy-ai-theme-wp')),
			el('div', { className: 'bst-slidenav__chips', role: 'tablist' },
				nav.ids.map(function (sid, i) {
					return el('button', { key: sid, type: 'button', role: 'tab', 'aria-selected': i === nav.active, className: 'bst-slidenav__chip' + (i === nav.active ? ' is-active' : ''), onClick: function () { go(i, true); }, title: __('Show and edit this slide', 'bigspy-ai-theme-wp') },
						el('b', null, i + 1), ' ', nav.names[i]);
				})
			),
			el(c.Dropdown, {
				className: 'bst-slidenav__add',
				popoverProps: { placement: 'bottom-end' },
				renderToggle: function (t) {
					return el('button', { type: 'button', className: 'bst-slidenav__plus', onClick: t.onToggle, 'aria-expanded': t.isOpen, title: __('Add slide', 'bigspy-ai-theme-wp') }, '+');
				},
				renderContent: function (t) {
					return el('div', { className: 'bst-slidenav__menu' },
						SLIDE_ADD.map(function (opt) {
							return el(c.Button, { key: opt[0], onClick: function () { add(opt[0]); t.onClose(); } }, opt[1]);
						}),
						el(c.Button, { variant: 'link', onClick: function () {
							t.onClose();
							var store = wp.data.dispatch('core/editor') || wp.data.dispatch('core/edit-post');
							if (store && store.setIsInserterOpened) {
								store.setIsInserterOpened({ rootClientId: clientId, insertionIndex: nav.ids.length });
							}
						} }, __('Any block…', 'bigspy-ai-theme-wp'))
					);
				}
			}),
			id ? el('span', { className: 'bst-slidenav__tools' },
				el(c.Button, { icon: 'arrow-left-alt2', label: __('Move slide left', 'bigspy-ai-theme-wp'), size: 'small', disabled: nav.active === 0, onClick: function () { ed.moveBlocksUp([id], clientId); nav.setActive(nav.active - 1); } }),
				el(c.Button, { icon: 'arrow-right-alt2', label: __('Move slide right', 'bigspy-ai-theme-wp'), size: 'small', disabled: nav.active >= nav.ids.length - 1, onClick: function () { ed.moveBlocksDown([id], clientId); nav.setActive(nav.active + 1); } }),
				el(c.Button, { icon: 'admin-page', label: __('Duplicate slide', 'bigspy-ai-theme-wp'), size: 'small', onClick: function () { ed.duplicateBlocks([id]); nav.setActive(nav.active + 1); } }),
				el(c.Button, { icon: 'trash', label: __('Delete slide', 'bigspy-ai-theme-wp'), size: 'small', isDestructive: true, disabled: nav.ids.length < 2, onClick: function () { ed.removeBlock(id, false); nav.setActive(Math.max(0, nav.active - 1)); } }),
				el(c.Button, { icon: nav.showAll ? 'visibility' : 'grid-view', label: nav.showAll ? __('Show one slide', 'bigspy-ai-theme-wp') : __('Show all slides', 'bigspy-ai-theme-wp'), size: 'small', isPressed: nav.showAll, onClick: function () { nav.setSingle(nav.showAll); } })
			) : null
		);
	}

	register('slider', function (props) {
		var a = props.attributes;
		var bind = B.binder(props);
		var nav = useSlideNav(props.clientId);
		var blockProps = useBlockProps({ className: 'bs-slider-block bst-slider-editor' + (nav.single ? ' is-single' : '') + (a.slideHeightD ? ' bs-slider--same-h' : ''), style: { '--bs-slides-d': nav.single ? 1 : a.slidesDesktop, '--bs-slide-gap': a.gap + 'px', '--bs-sh-d': a.slideHeightD ? a.slideHeightD + 'px' : undefined, '--bs-sh-t': a.slideHeightD ? (a.slideHeightT || a.slideHeightD) + 'px' : undefined, '--bs-sh-m': a.slideHeightD ? (a.slideHeightM || a.slideHeightT || a.slideHeightD) + 'px' : undefined } });
		var innerProps = useInnerBlocksProps({ className: 'bst-slider-editor__track' }, {
			template: [['bst/banner', {}], ['bst/banner', {}]],
			orientation: 'horizontal'
		});
		return el(Fragment, null,
			el(Inspector, null,
				el(Panel, { title: __('Slides', 'bigspy-ai-theme-wp') },
					el('p', { className: 'description' }, __('Every block inside the slider is one slide. Banners work great.', 'bigspy-ai-theme-wp')),
					el(Range, Object.assign({ label: __('Slides per view – desktop', 'bigspy-ai-theme-wp'), min: 1, max: 6 }, bind('slidesDesktop'))),
					el(Range, Object.assign({ label: __('Slides per view – tablet', 'bigspy-ai-theme-wp'), min: 1, max: 4 }, bind('slidesTablet'))),
					el(Range, Object.assign({ label: __('Slides per view – mobile', 'bigspy-ai-theme-wp'), min: 1, max: 3 }, bind('slidesMobile'))),
					el(Range, Object.assign({ label: __('Gap (px)', 'bigspy-ai-theme-wp'), max: 80 }, bind('gap')))
				),
				el(Panel, { title: __('Effect & motion', 'bigspy-ai-theme-wp') },
					sliderChoice(props, __('Transition', 'bigspy-ai-theme-wp'), 'effect', [['slide', __('Slide', 'bigspy-ai-theme-wp')], ['fade', __('Fade', 'bigspy-ai-theme-wp')]]),
					a.effect !== 'fade' ? el(Range, Object.assign({ label: __('Peek next slide (%)', 'bigspy-ai-theme-wp'), help: __('Shows part of the next slide so visitors know they can swipe.', 'bigspy-ai-theme-wp'), max: 40 }, bind('peek'))) : null,
					el(Range, Object.assign({ label: __('Autoplay every (seconds, 0 = off)', 'bigspy-ai-theme-wp'), max: 30 }, bind('autoplay'))),
					a.autoplay ? el(Toggle, Object.assign({ label: __('Pause when the mouse is over the slider', 'bigspy-ai-theme-wp') }, bind('pauseHover', true))) : null,
					a.autoplay ? el(Toggle, Object.assign({ label: __('Progress bar (time until next slide)', 'bigspy-ai-theme-wp') }, bind('progress', true))) : null,
					el(Toggle, Object.assign({ label: __('Loop', 'bigspy-ai-theme-wp') }, bind('loop', true))),
					el(Range, Object.assign({ label: __('Same height for all slides – desktop (px, 0 = each slide its own)', 'bigspy-ai-theme-wp'), help: __('Every slide gets this height and its image stretches and crops to fill it, so the slider never jumps between tall and short slides.', 'bigspy-ai-theme-wp'), max: 1200, step: 10, reset: 0 }, bind('slideHeightD'))),
					a.slideHeightD ? el(Range, Object.assign({ label: __('Same height – tablet (px, 0 = as desktop)', 'bigspy-ai-theme-wp'), max: 1200, step: 10, reset: 0 }, bind('slideHeightT'))) : null,
					a.slideHeightD ? el(Range, Object.assign({ label: __('Same height – mobile (px, 0 = as tablet)', 'bigspy-ai-theme-wp'), max: 1200, step: 10, reset: 0 }, bind('slideHeightM'))) : null,
					!a.slideHeightD ? el(Range, Object.assign({ label: __('Minimum height (px, 0 = auto)', 'bigspy-ai-theme-wp'), max: 1200, step: 10 }, bind('minHeight'))) : null,
					el(Range, Object.assign({ label: __('Slide corner radius (px)', 'bigspy-ai-theme-wp'), max: 60 }, bind('slideRadius')))
				),
				el(Panel, { title: __('Arrows', 'bigspy-ai-theme-wp') },
					el(Toggle, Object.assign({ label: __('Show arrows', 'bigspy-ai-theme-wp') }, bind('arrows', true))),
					a.arrows ? el(Fragment, null,
						sliderChoice(props, __('Style', 'bigspy-ai-theme-wp'), 'arrowStyle', [['circle', __('Circle', 'bigspy-ai-theme-wp')], ['square', __('Square', 'bigspy-ai-theme-wp')], ['line', __('Line', 'bigspy-ai-theme-wp')], ['minimal', __('Minimal', 'bigspy-ai-theme-wp')]]),
						sliderChoice(props, __('Position', 'bigspy-ai-theme-wp'), 'arrowPosition', [['inside', __('Inside', 'bigspy-ai-theme-wp')], ['outside', __('Outside', 'bigspy-ai-theme-wp')], ['bottom', __('Below', 'bigspy-ai-theme-wp')]]),
						sliderChoice(props, __('Color', 'bigspy-ai-theme-wp'), 'arrowColor', [['light', __('Light', 'bigspy-ai-theme-wp')], ['dark', __('Dark', 'bigspy-ai-theme-wp')], ['brand', __('Brand', 'bigspy-ai-theme-wp')]]),
						sliderChoice(props, __('Size', 'bigspy-ai-theme-wp'), 'arrowSize', [['sm', 'S'], ['md', 'M'], ['lg', 'L']]),
						el(Toggle, Object.assign({ label: __('Always visible (not only on hover)', 'bigspy-ai-theme-wp') }, bind('arrowsAlways', true))),
						el(Toggle, Object.assign({ label: __('Arrows on mobile', 'bigspy-ai-theme-wp') }, bind('arrowsMobile', true)))
					) : null
				),
				el(Panel, { title: __('Dots', 'bigspy-ai-theme-wp') },
					el(Toggle, Object.assign({ label: __('Show dots', 'bigspy-ai-theme-wp') }, bind('dots', true))),
					a.dots ? sliderChoice(props, __('Style', 'bigspy-ai-theme-wp'), 'dotStyle', [['pill', __('Pill', 'bigspy-ai-theme-wp')], ['dot', __('Dot', 'bigspy-ai-theme-wp')], ['dash', __('Dash', 'bigspy-ai-theme-wp')], ['square', __('Square', 'bigspy-ai-theme-wp')]]) : null,
					a.dots ? el(Toggle, Object.assign({ label: __('Dots over the slides', 'bigspy-ai-theme-wp') }, bind('dotsInside', true))) : null
				)
			),
			el('div', blockProps, nav.focused ? slideNavBar(nav, props.clientId) : null, nav.style, el('div', innerProps))
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
				el(Panel, { title: __('Icon', 'bigspy-ai-theme-wp') },
					el(B.IconPicker, bind('icon')),
					el(B.MediaPicker, {
						label: __('…or use an image instead', 'bigspy-ai-theme-wp'),
						id: a.imageId,
						url: a.imageUrl,
						onChange: function (m) {
							props.setAttributes({ imageId: m.id, imageUrl: m.url });
						}
					}),
					el(Select, Object.assign({ label: __('Icon style', 'bigspy-ai-theme-wp'), options: { plain: __('Plain', 'bigspy-ai-theme-wp'), circle: __('Filled circle', 'bigspy-ai-theme-wp'), square: __('Filled square', 'bigspy-ai-theme-wp'), outline: __('Outline circle', 'bigspy-ai-theme-wp') } }, bind('iconStyle'))),
					el(Range, Object.assign({ label: __('Icon size (px)', 'bigspy-ai-theme-wp'), min: 12, max: 160, reset: 32 }, bind('iconSize')))
				),
				el(Panel, { title: __('Layout', 'bigspy-ai-theme-wp') },
					el(Select, Object.assign({ label: __('Icon position', 'bigspy-ai-theme-wp'), options: { top: __('Top', 'bigspy-ai-theme-wp'), left: __('Left', 'bigspy-ai-theme-wp') } }, bind('position'))),
					el(Select, Object.assign({ label: __('Align', 'bigspy-ai-theme-wp'), options: { left: __('Left', 'bigspy-ai-theme-wp'), center: __('Center', 'bigspy-ai-theme-wp') } }, bind('align'))),
					el(Select, Object.assign({ label: __('Box style', 'bigspy-ai-theme-wp'), options: { none: __('None', 'bigspy-ai-theme-wp'), card: __('Card with shadow', 'bigspy-ai-theme-wp'), bordered: __('Bordered', 'bigspy-ai-theme-wp') } }, bind('boxStyle'))),
					el(Select, Object.assign({ label: __('Title tag', 'bigspy-ai-theme-wp'), options: { h2: 'H2', h3: 'H3', h4: 'H4', h5: 'H5', p: 'P' } }, bind('titleTag'))),
					el(Text, Object.assign({ label: __('Title link (URL)', 'bigspy-ai-theme-wp'), type: 'url' }, bind('link')))
				),
				el(B.ColorPanel, {
					title: __('Icon colors', 'bigspy-ai-theme-wp'),
					settings: [
						{ label: __('Icon', 'bigspy-ai-theme-wp'), value: a.iconColor, onChange: function (v) { props.setAttributes({ iconColor: v || '' }); } },
						{ label: __('Icon background', 'bigspy-ai-theme-wp'), value: a.iconBg, onChange: function (v) { props.setAttributes({ iconBg: v || '' }); } }
					]
				})
			),
			el('div', blockProps,
				media ? el('div', { className: 'bs-icon-box__icon' }, media) : null,
				el('div', { className: 'bs-icon-box__body' },
					el(RichText, { tagName: a.titleTag === 'p' ? 'p' : a.titleTag, className: 'bs-icon-box__title', value: a.title, placeholder: __('Title', 'bigspy-ai-theme-wp'), onChange: function (v) { props.setAttributes({ title: v }); } }),
					el(RichText, { tagName: 'div', className: 'bs-icon-box__text', value: a.text, placeholder: __('Description…', 'bigspy-ai-theme-wp'), onChange: function (v) { props.setAttributes({ text: v }); } }),
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
				el(Panel, { title: __('Title', 'bigspy-ai-theme-wp') },
					el(Select, Object.assign({ label: __('Style', 'bigspy-ai-theme-wp'), options: { plain: __('Plain', 'bigspy-ai-theme-wp'), 'bottom-line': __('Short line below', 'bigspy-ai-theme-wp'), 'center-line': __('Lines on both sides', 'bigspy-ai-theme-wp'), 'left-bar': __('Bar on the left', 'bigspy-ai-theme-wp'), 'underline-full': __('Full-width underline', 'bigspy-ai-theme-wp') } }, bind('style'))),
					el(Select, Object.assign({ label: __('Align', 'bigspy-ai-theme-wp'), options: ALIGN3 }, bind('align'))),
					el(Select, Object.assign({ label: __('Tag', 'bigspy-ai-theme-wp'), options: { h1: 'H1', h2: 'H2', h3: 'H3', h4: 'H4' } }, bind('tag'))),
					el(Range, Object.assign({ label: __('Font size (px, 0 = default)', 'bigspy-ai-theme-wp'), max: 120 }, bind('size'))),
					el(Text, Object.assign({ label: __('“View all” text', 'bigspy-ai-theme-wp') }, bind('linkText'))),
					el(Text, Object.assign({ label: __('“View all” link', 'bigspy-ai-theme-wp'), type: 'url' }, bind('linkUrl')))
				),
				el(B.ColorPanel, {
					settings: [
						{ label: __('Text', 'bigspy-ai-theme-wp'), value: a.color, onChange: function (v) { props.setAttributes({ color: v || '' }); } },
						{ label: __('Line', 'bigspy-ai-theme-wp'), value: a.lineColor, onChange: function (v) { props.setAttributes({ lineColor: v || '' }); } }
					]
				})
			),
			el('div', blockProps,
				el(RichText, { tagName: 'p', className: 'bs-title__subtitle', value: a.subtitle, placeholder: __('Subtitle (optional)', 'bigspy-ai-theme-wp'), onChange: function (v) { props.setAttributes({ subtitle: v }); } }),
				el('div', { className: 'bs-title__row' },
					el(RichText, { tagName: a.tag, className: 'bs-title__text', value: a.text, placeholder: __('Section title', 'bigspy-ai-theme-wp'), onChange: function (v) { props.setAttributes({ text: v }); } }),
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
		var blockProps = useBlockProps({ className: 'bs-tabs bs-tabs--' + a.style + ' bs-tabs--align-' + a.align + (a.stretch ? ' bs-tabs--stretch' : '') + ' bst-tabs-editor', style: styleVars({ '--bs-tabs-size': a.navSize ? a.navSize + 'px' : '', '--bs-tabs-active': a.activeColor, '--bs-tabs-active-text': a.activeText }) });
		var innerProps = useInnerBlocksProps({ className: 'bs-tabs__panels' }, {
			allowedBlocks: ['bst/tab'],
			template: [['bst/tab', { title: __('Tab 1', 'bigspy-ai-theme-wp') }], ['bst/tab', { title: __('Tab 2', 'bigspy-ai-theme-wp') }]]
		});
		return el(Fragment, null,
			el(Inspector, null,
				el(Panel, { title: __('Tabs', 'bigspy-ai-theme-wp') },
					sliderChoice(props, __('Style', 'bigspy-ai-theme-wp'), 'style', [['underline', __('Underline', 'bigspy-ai-theme-wp')], ['pills', __('Pills', 'bigspy-ai-theme-wp')], ['boxed', __('Boxed', 'bigspy-ai-theme-wp')], ['vertical', __('Vertical', 'bigspy-ai-theme-wp')]]),
					sliderChoice(props, __('Align', 'bigspy-ai-theme-wp'), 'align', [['left', __('Left', 'bigspy-ai-theme-wp')], ['center', __('Center', 'bigspy-ai-theme-wp')]]),
					sliderChoice(props, __('Open a tab on', 'bigspy-ai-theme-wp'), 'event', [['click', __('Click', 'bigspy-ai-theme-wp')], ['hover', __('Hover', 'bigspy-ai-theme-wp')]]),
					el(Toggle, Object.assign({ label: __('Stretch tabs to full width', 'bigspy-ai-theme-wp') }, bind('stretch', true))),
					el(Toggle, Object.assign({ label: __('Fade between tabs', 'bigspy-ai-theme-wp') }, bind('fade', true))),
					el(Range, Object.assign({ label: __('Tab text size (px, 0 = default)', 'bigspy-ai-theme-wp'), max: 30 }, bind('navSize'))),
					el('p', { className: 'description' }, __('Select a tab inside to rename it or give it an icon.', 'bigspy-ai-theme-wp'))
				),
				el(B.ColorPanel, { title: __('Active tab colors', 'bigspy-ai-theme-wp'), settings: [
					{ label: __('Active tab', 'bigspy-ai-theme-wp'), value: a.activeColor, onChange: function (v) { props.setAttributes({ activeColor: v || '' }); } },
					{ label: __('Active tab text', 'bigspy-ai-theme-wp'), value: a.activeText, onChange: function (v) { props.setAttributes({ activeText: v || '' }); } }
				] })
			),
			el('div', blockProps,
				el('div', { className: 'bs-tabs__nav' },
					tabs.map(function (tab, i) {
						return el('span', { key: tab.clientId, className: 'bs-tabs__tab' + (i === 0 ? ' is-active' : '') }, tab.attributes.icon ? el(B.Icon, { name: tab.attributes.icon, size: 18 }) : null, el('span', null, tab.attributes.title || __('Tab', 'bigspy-ai-theme-wp')));
					})
				),
				el('div', innerProps)
			)
		);
	}, saveInner);

	register('tab', function (props) {
		var blockProps = useBlockProps({ className: 'bs-tab bst-tab-editor' });
		var innerProps = useInnerBlocksProps({ className: 'bst-tab-editor__content' }, { template: [['core/paragraph', { placeholder: __('Tab content…', 'bigspy-ai-theme-wp') }]] });
		return el('div', blockProps,
			el(Inspector, null, el(Panel, { title: __('Tab', 'bigspy-ai-theme-wp') },
				el(Text, { label: __('Tab title', 'bigspy-ai-theme-wp'), value: props.attributes.title, onChange: function (v) { props.setAttributes({ title: v }); } }),
				el(B.IconPicker, { label: __('Tab icon (optional)', 'bigspy-ai-theme-wp'), value: props.attributes.icon, onChange: function (v) { props.setAttributes({ icon: v }); } })
			)),
			el('div', { className: 'bst-tab-label' }, props.attributes.icon ? el(B.Icon, { name: props.attributes.icon, size: 14 }) : null, props.attributes.title || __('Tab', 'bigspy-ai-theme-wp')),
			el('div', innerProps)
		);
	}, saveInner);

	/* ================================================================ Accordion */
	register('accordion', function (props) {
		var a = props.attributes;
		var bind = B.binder(props);
		var blockProps = useBlockProps({
			className: 'bs-accordion bs-accordion--' + a.style + ' bs-accordion--icon-' + (a.iconStyle || 'chevron') + ' bs-accordion--icon-' + (a.iconPosition || 'right') + (a.numbered ? ' bs-accordion--numbered' : ''),
			style: styleVars({ '--bs-acc-title-size': a.titleSize ? a.titleSize + 'px' : '', '--bs-acc-title': a.titleColor, '--bs-acc-title-bg': a.titleBg, '--bs-acc-active': a.activeColor, '--bs-acc-active-bg': a.activeBg, '--bs-acc-content-bg': a.contentBg, '--bs-acc-radius': a.radius >= 0 ? a.radius + 'px' : '', '--bs-acc-gap': a.gap >= 0 ? a.gap + 'px' : '' })
		});
		var innerProps = useInnerBlocksProps(blockProps, {
			allowedBlocks: ['bst/accordion-item'],
			template: [
				['bst/accordion-item', { title: __('Question 1?', 'bigspy-ai-theme-wp'), open: true }],
				['bst/accordion-item', { title: __('Question 2?', 'bigspy-ai-theme-wp') }],
				['bst/accordion-item', { title: __('Question 3?', 'bigspy-ai-theme-wp') }]
			]
		});
		return el(Fragment, null,
			el(Inspector, null,
				el(Panel, { title: __('Accordion', 'bigspy-ai-theme-wp') },
					el(Select, Object.assign({ label: __('Style', 'bigspy-ai-theme-wp'), options: { bordered: __('Bordered', 'bigspy-ai-theme-wp'), separated: __('Separated boxes', 'bigspy-ai-theme-wp'), minimal: __('Minimal lines', 'bigspy-ai-theme-wp') } }, bind('style'))),
					el(Toggle, Object.assign({ label: __('Only one item open at a time', 'bigspy-ai-theme-wp') }, bind('single', true))),
					el(Toggle, Object.assign({ label: __('Add FAQ schema (Google rich results)', 'bigspy-ai-theme-wp'), help: __('Turn off if an SEO plugin already outputs FAQ schema for this page.', 'bigspy-ai-theme-wp') }, bind('faqSchema', true))),
					el(Toggle, Object.assign({ label: __('Smooth open / close animation', 'bigspy-ai-theme-wp') }, bind('smooth', true))),
					el(Toggle, Object.assign({ label: __('Number the items (01, 02…)', 'bigspy-ai-theme-wp') }, bind('numbered', true))),
					el(Select, Object.assign({ label: __('Question tag for all items (SEO)', 'bigspy-ai-theme-wp'), help: __('Each item can override it in its own settings.', 'bigspy-ai-theme-wp'), options: { div: __('Normal text (div)', 'bigspy-ai-theme-wp'), p: __('Paragraph (p)', 'bigspy-ai-theme-wp'), h2: 'H2', h3: 'H3', h4: 'H4', h5: 'H5', h6: 'H6' } }, bind('titleTag')))
				),
				el(Panel, { title: __('Icon', 'bigspy-ai-theme-wp') },
					sliderChoice(props, __('Icon', 'bigspy-ai-theme-wp'), 'iconStyle', [['chevron', '⌄ ' + __('Chevron', 'bigspy-ai-theme-wp')], ['plus', '+ ' + __('Plus', 'bigspy-ai-theme-wp')], ['circle', '⊕ ' + __('Circle', 'bigspy-ai-theme-wp')], ['arrow', '→ ' + __('Arrow', 'bigspy-ai-theme-wp')], ['none', __('None', 'bigspy-ai-theme-wp')]]),
					sliderChoice(props, __('Position', 'bigspy-ai-theme-wp'), 'iconPosition', [['right', __('Right', 'bigspy-ai-theme-wp')], ['left', __('Left', 'bigspy-ai-theme-wp')]])
				),
				el(Panel, { title: __('Size & spacing', 'bigspy-ai-theme-wp'), initialOpen: false },
					el(Range, Object.assign({ label: __('Title size (px, 0 = default)', 'bigspy-ai-theme-wp'), max: 40 }, bind('titleSize'))),
					el(Range, Object.assign({ label: __('Corner radius (px, -1 = style default)', 'bigspy-ai-theme-wp'), min: -1, max: 40 }, bind('radius'))),
					el(Range, Object.assign({ label: __('Space between items (px, -1 = default)', 'bigspy-ai-theme-wp'), min: -1, max: 40 }, bind('gap')))
				),
				el(B.ColorPanel, { title: __('Colors', 'bigspy-ai-theme-wp'), settings: [
					{ label: __('Title', 'bigspy-ai-theme-wp'), value: a.titleColor, onChange: function (v) { props.setAttributes({ titleColor: v || '' }); } },
					{ label: __('Title background', 'bigspy-ai-theme-wp'), value: a.titleBg, onChange: function (v) { props.setAttributes({ titleBg: v || '' }); } },
					{ label: __('Open item – title', 'bigspy-ai-theme-wp'), value: a.activeColor, onChange: function (v) { props.setAttributes({ activeColor: v || '' }); } },
					{ label: __('Open item – background', 'bigspy-ai-theme-wp'), value: a.activeBg, onChange: function (v) { props.setAttributes({ activeBg: v || '' }); } },
					{ label: __('Answer background', 'bigspy-ai-theme-wp'), value: a.contentBg, onChange: function (v) { props.setAttributes({ contentBg: v || '' }); } }
				] })
			),
			el('div', innerProps)
		);
	}, saveInner);

	register('accordion-item', function (props) {
		var a = props.attributes;
		var blockProps = useBlockProps({ className: 'bs-accordion__item bst-accordion-editor' + (a.open ? ' is-open' : '') });
		var innerProps = useInnerBlocksProps({ className: 'bs-accordion__content' }, { template: [['core/paragraph', { placeholder: __('Answer…', 'bigspy-ai-theme-wp') }]] });
		return el(Fragment, null,
			el(Inspector, null,
				el(Panel, { title: __('Item', 'bigspy-ai-theme-wp') },
					el(Toggle, { label: __('Open by default', 'bigspy-ai-theme-wp'), checked: a.open, onChange: function (v) { props.setAttributes({ open: v }); } }),
					el(c.SelectControl, { label: __('Question tag (SEO)', 'bigspy-ai-theme-wp'), help: __('Same as accordion = the tag set on the Accordion block.', 'bigspy-ai-theme-wp'), value: a.titleTag || '', options: [{ value: '', label: __('Same as accordion', 'bigspy-ai-theme-wp') + (props.context && props.context['bst/accordionTitleTag'] ? ' (' + props.context['bst/accordionTitleTag'].toUpperCase() + ')' : '') }, { value: 'div', label: __('Normal text (div)', 'bigspy-ai-theme-wp') }, { value: 'p', label: __('Paragraph (p)', 'bigspy-ai-theme-wp') }, { value: 'h2', label: 'H2' }, { value: 'h3', label: 'H3' }, { value: 'h4', label: 'H4' }, { value: 'h5', label: 'H5' }, { value: 'h6', label: 'H6' }], onChange: function (v) { props.setAttributes({ titleTag: v }); }, __nextHasNoMarginBottom: true })
				)
			),
			el('div', blockProps,
				el('div', { className: 'bs-accordion__title' },
					el(RichText, { tagName: 'span', className: 'bs-accordion__text', value: a.title, placeholder: __('Question / title', 'bigspy-ai-theme-wp'), onChange: function (v) { props.setAttributes({ title: v }); } }),
					el('span', { className: 'bst-acc-tag', title: __('Question tag', 'bigspy-ai-theme-wp') }, (a.titleTag || (props.context && props.context['bst/accordionTitleTag']) || 'div').toUpperCase()),
					el('span', { className: 'bs-accordion__icon', 'aria-hidden': true, onClick: function () { props.setAttributes({ open: !a.open }); } }, el(B.Icon, { name: 'chevron-down', size: 18 }))
				),
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
		return [{ value: 0, label: __('All', 'bigspy-ai-theme-wp') }].concat(terms.map(function (t) {
			return { value: t.id, label: t.name };
		}));
	}

	/*
	 * Server-rendered preview. Attributes are sent as they are: several BIGSPY blocks have their
	 * own "style" attribute (a string), which skipBlockSupportAttributes would mangle into
	 * an object and the REST API would reject ("Lỗi tải khối: [object Object]").
	 */
	function previewError(props) {
		var err = props && props.response;
		var msg = err && (err.message || (err.data && err.data.message)) ? (err.message || err.data.message) : __('The preview could not be loaded. Save the page and reload the editor.', 'bigspy-ai-theme-wp');
		return el(c.Placeholder, { icon: 'warning', label: __('Preview unavailable', 'bigspy-ai-theme-wp'), instructions: String(msg) });
	}
	function previewLoading() {
		return el('div', { className: 'bst-ssr-loading' }, el(c.Spinner, null), el('span', null, __('Loading preview…', 'bigspy-ai-theme-wp')));
	}
	function dynamicPreview(name, props) {
		return el('div', useBlockProps({ className: 'bst-ssr-preview' }), el(SSR, {
			block: 'bst/' + name,
			attributes: props.attributes,
			httpMethod: 'POST',
			ErrorResponsePlaceholder: previewError,
			LoadingResponsePlaceholder: previewLoading
		}));
	}

	register('posts', function (props) {
		var a = props.attributes;
		var bind = B.binder(props);
		var cats = useTerms('category');
		var tags = useTerms('post_tag');
		return el(Fragment, null,
			el(Inspector, null,
				el(Panel, { title: __('Query', 'bigspy-ai-theme-wp') },
					el(Range, Object.assign({ label: __('Number of posts', 'bigspy-ai-theme-wp'), min: 1, max: 24 }, bind('count'))),
					el(c.SelectControl, { label: __('Category', 'bigspy-ai-theme-wp'), value: a.category, options: termOptions(cats), onChange: function (v) { props.setAttributes({ category: parseInt(v, 10) || 0 }); }, __nextHasNoMarginBottom: true }),
					el(Select, Object.assign({ label: __('Order by', 'bigspy-ai-theme-wp'), options: { date: __('Newest', 'bigspy-ai-theme-wp'), modified: __('Recently updated', 'bigspy-ai-theme-wp'), comment_count: __('Most commented', 'bigspy-ai-theme-wp'), rand: __('Random', 'bigspy-ai-theme-wp'), title: __('Title A–Z', 'bigspy-ai-theme-wp') } }, bind('orderBy'))),
					el(Range, Object.assign({ label: __('Skip first N posts', 'bigspy-ai-theme-wp'), max: 20 }, bind('offset'))),
					el(c.SelectControl, { label: __('Tag', 'bigspy-ai-theme-wp'), value: a.tag, options: termOptions(tags), onChange: function (v) { props.setAttributes({ tag: parseInt(v, 10) || 0 }); }, __nextHasNoMarginBottom: true }),
					el(Text, Object.assign({ label: __('Specific post IDs (comma separated)', 'bigspy-ai-theme-wp') }, bind('ids'))),
					el(Toggle, Object.assign({ label: __('Hide the post being viewed', 'bigspy-ai-theme-wp') }, bind('excludeCurrent', true)))
				),
				el(Panel, { title: __('Layout', 'bigspy-ai-theme-wp') },
					sliderChoice(props, __('Layout', 'bigspy-ai-theme-wp'), 'layout', [['grid', __('Grid', 'bigspy-ai-theme-wp')], ['magazine', __('Magazine', 'bigspy-ai-theme-wp')], ['list', __('List', 'bigspy-ai-theme-wp')], ['slider', __('Slider', 'bigspy-ai-theme-wp')]]),
					a.layout !== 'list' ? el(Range, Object.assign({ label: __('Columns – desktop', 'bigspy-ai-theme-wp'), min: 1, max: 6 }, bind('columns'))) : null,
					a.layout !== 'list' ? el(Range, Object.assign({ label: __('Columns – tablet', 'bigspy-ai-theme-wp'), min: 1, max: 4 }, bind('columnsTablet'))) : null,
					a.layout !== 'list' ? el(Range, Object.assign({ label: __('Columns – mobile', 'bigspy-ai-theme-wp'), min: 1, max: 2 }, bind('columnsMobile'))) : null,
					a.layout === 'slider' ? el(Fragment, null,
						el(Range, Object.assign({ label: __('Autoplay (seconds, 0 = off)', 'bigspy-ai-theme-wp'), max: 30 }, bind('autoplay'))),
						el(Range, Object.assign({ label: __('Peek next card (%)', 'bigspy-ai-theme-wp'), max: 40 }, bind('peek'))),
						el(Toggle, Object.assign({ label: __('Arrows', 'bigspy-ai-theme-wp') }, bind('arrows', true))),
						el(Toggle, Object.assign({ label: __('Dots', 'bigspy-ai-theme-wp') }, bind('dots', true)))
					) : null,
					el(Range, Object.assign({ label: __('Gap between cards (px, -1 = default)', 'bigspy-ai-theme-wp'), min: -1, max: 60 }, bind('gap')))
				),
				el(Panel, { title: __('Card style', 'bigspy-ai-theme-wp') },
					el('div', { className: 'bst-post-styles' },
						[['', __('Theme', 'bigspy-ai-theme-wp')], ['shadow', __('Shadow', 'bigspy-ai-theme-wp')], ['bordered', __('Bordered', 'bigspy-ai-theme-wp')], ['plain', __('Plain', 'bigspy-ai-theme-wp')], ['minimal', __('Minimal', 'bigspy-ai-theme-wp')], ['soft', __('Soft color', 'bigspy-ai-theme-wp')], ['gradient', __('Gradient', 'bigspy-ai-theme-wp')], ['overlay', __('Text on image', 'bigspy-ai-theme-wp')]].map(function (s) {
							return el('button', { key: s[0] || 'theme', type: 'button', className: 'bst-post-styles__item bst-post-styles__item--' + (s[0] || 'theme') + (a.cardStyle === s[0] ? ' is-selected' : ''), onClick: function () { props.setAttributes({ cardStyle: s[0] }); } }, el('span', { className: 'bst-post-styles__img' }), el('span', { className: 'bst-post-styles__line' }), el('span', { className: 'bst-post-styles__line is-short' }), el('em', null, s[1]));
						})
					),
					el(Select, Object.assign({ label: __('Image ratio', 'bigspy-ai-theme-wp'), options: { '16-9': '16:9', '4-3': '4:3', '3-2': '3:2', '1-1': '1:1', auto: __('Original', 'bigspy-ai-theme-wp') } }, bind('imageRatio'))),
					sliderChoice(props, __('Image hover', 'bigspy-ai-theme-wp'), 'imageHover', [['zoom', __('Zoom', 'bigspy-ai-theme-wp')], ['lift', __('Lift card', 'bigspy-ai-theme-wp')], ['grayscale', __('Gray → color', 'bigspy-ai-theme-wp')], ['none', __('None', 'bigspy-ai-theme-wp')]]),
					sliderChoice(props, __('Text align', 'bigspy-ai-theme-wp'), 'align', [['left', __('Left', 'bigspy-ai-theme-wp')], ['center', __('Center', 'bigspy-ai-theme-wp')]]),
					el(Toggle, Object.assign({ label: __('Date badge on the image', 'bigspy-ai-theme-wp') }, bind('dateBadge', true))),
					el(Toggle, Object.assign({ label: __('Category badge on the image', 'bigspy-ai-theme-wp') }, bind('catBadge', true)))
				),
				el(Panel, { title: __('Content', 'bigspy-ai-theme-wp'), initialOpen: false },
					el(Range, Object.assign({ label: __('Title size (px, 0 = default)', 'bigspy-ai-theme-wp'), max: 48 }, bind('titleSize'))),
					el(Range, Object.assign({ label: __('Title lines (0 = no limit)', 'bigspy-ai-theme-wp'), max: 4 }, bind('titleLines'))),
					el(Toggle, Object.assign({ label: __('Show meta line', 'bigspy-ai-theme-wp') }, bind('showMeta', true))),
					a.showMeta ? el(Fragment, null,
						el(Toggle, Object.assign({ label: __('– Category', 'bigspy-ai-theme-wp') }, bind('showCategory', true))),
						el(Toggle, Object.assign({ label: __('– Date', 'bigspy-ai-theme-wp') }, bind('showDate', true))),
						el(Toggle, Object.assign({ label: __('– Author', 'bigspy-ai-theme-wp') }, bind('showAuthor', true))),
						el(Toggle, Object.assign({ label: __('– Reading time', 'bigspy-ai-theme-wp') }, bind('showReadTime', true)))
					) : null,
					el(Toggle, Object.assign({ label: __('Show excerpt', 'bigspy-ai-theme-wp') }, bind('showExcerpt', true))),
					a.showExcerpt ? el(Range, Object.assign({ label: __('Excerpt length (words, 0 = default)', 'bigspy-ai-theme-wp'), max: 80 }, bind('excerptLength'))) : null,
					el(Text, Object.assign({ label: __('“Read more” text', 'bigspy-ai-theme-wp') }, bind('readMore'))),
					sliderChoice(props, __('“Read more” style', 'bigspy-ai-theme-wp'), 'moreStyle', [['link', __('Link', 'bigspy-ai-theme-wp')], ['button', __('Button', 'bigspy-ai-theme-wp')], ['none', __('Hidden', 'bigspy-ai-theme-wp')]])
				),
				el(B.ColorPanel, { title: __('Colors', 'bigspy-ai-theme-wp'), settings: [
					{ label: __('Accent (badges, links, buttons)', 'bigspy-ai-theme-wp'), value: a.accent, onChange: function (v) { props.setAttributes({ accent: v || '' }); } },
					{ label: __('Card background', 'bigspy-ai-theme-wp'), value: a.cardBg, onChange: function (v) { props.setAttributes({ cardBg: v || '' }); } }
				] })
			),
			dynamicPreview('posts', props)
		);
	});

	/* ================================================================ Code / HTML */
	register('code', function (props) {
		var a = props.attributes;
		var bind = B.binder(props);
		var setCode = function (v) {
			props.setAttributes({ code: v });
		};
		var snippet = a.mode === 'snippet';
		var blockProps = useBlockProps({ className: 'bst-code-block bst-code-block--' + a.mode + (snippet ? ' bs-code bs-code--' + (a.theme || 'dark') : '') });
		return el(Fragment, null,
			el(Inspector, null,
				el(Panel, { title: __('Code / HTML', 'bigspy-ai-theme-wp') },
					el(Select, Object.assign({ label: __('Mode', 'bigspy-ai-theme-wp'), options: { html: __('Render HTML on the page', 'bigspy-ai-theme-wp'), snippet: __('Show as code snippet', 'bigspy-ai-theme-wp') } }, bind('mode'))),
					el(c.TextareaControl, {
						className: 'bst-side-code',
						label: snippet ? __('Code to display', 'bigspy-ai-theme-wp') : __('HTML', 'bigspy-ai-theme-wp'),
						value: a.code,
						rows: 14,
						spellCheck: false,
						onChange: setCode,
						help: snippet ? __('Displayed as text with a copy button – nothing is executed.', 'bigspy-ai-theme-wp') : __('HTML, inline CSS and shortcodes. Scripts only work for administrators / editors. You can also type directly on the page.', 'bigspy-ai-theme-wp'),
						__nextHasNoMarginBottom: true
					}),
					snippet ? el(Text, Object.assign({ label: __('Language label', 'bigspy-ai-theme-wp'), placeholder: 'HTML, CSS, JS, PHP…' }, bind('language'))) : null,
					snippet ? el(Select, Object.assign({ label: __('Color theme', 'bigspy-ai-theme-wp'), options: { dark: __('Dark', 'bigspy-ai-theme-wp'), light: __('Light', 'bigspy-ai-theme-wp') } }, bind('theme'))) : null,
					snippet ? el(Toggle, Object.assign({ label: __('Copy button', 'bigspy-ai-theme-wp') }, bind('showCopy', true))) : null,
					snippet ? el(Toggle, Object.assign({ label: __('Wrap long lines', 'bigspy-ai-theme-wp') }, bind('wrap', true))) : null,
					snippet ? el(Range, Object.assign({ label: __('Max height (px, 0 = auto)', 'bigspy-ai-theme-wp'), max: 1200, step: 20 }, bind('maxHeight'))) : null
				),
				!snippet ? el(Panel, { title: __('Custom CSS', 'bigspy-ai-theme-wp'), initialOpen: false },
					el(c.TextareaControl, {
						className: 'bst-side-code',
						value: a.css,
						rows: 8,
						spellCheck: false,
						placeholder: 'selector h2 {\n  color: #0a4d8c;\n}',
						onChange: function (v) { props.setAttributes({ css: v }); },
						help: __('Use “selector” to target this block only.', 'bigspy-ai-theme-wp'),
						__nextHasNoMarginBottom: true
					})
				) : null
			),
			snippet
				? el('div', blockProps, el('div', { className: 'bs-code__bar' }, el('span', { className: 'bs-code__lang' }, a.language || __('Code', 'bigspy-ai-theme-wp')), a.showCopy ? el('span', { className: 'bs-code__copy' }, __('Copy', 'bigspy-ai-theme-wp')) : null), el('pre', { className: 'bs-code__pre', style: { whiteSpace: a.wrap ? 'pre-wrap' : 'pre' } }, el('code', null, a.code)))
				: el('div', blockProps,
					a.code ? el(VisualHtml, { value: a.code, onChange: setCode, className: 'bst-inline-edit', label: __('Editable HTML preview', 'bigspy-ai-theme-wp') })
						: el(c.Placeholder, { icon: 'editor-code', label: __('Code / HTML', 'bigspy-ai-theme-wp'), instructions: __('Paste HTML in the sidebar (Code / HTML panel).', 'bigspy-ai-theme-wp') }),
					a.css ? el('style', null, String(a.css).replace(/selector/g, '#block-' + props.clientId)) : null
				)
		);
	});

	/* ================================================================ Heading */
	function headingEffects(props) {
		var a = props.attributes;
		var bind = B.binder(props);
		return el(Fragment, null,
			el(Panel, { title: __('Text effects', 'bigspy-ai-theme-wp'), initialOpen: !!(a.gradient || a.highlight) },
				el(Toggle, Object.assign({ label: __('Gradient text', 'bigspy-ai-theme-wp') }, bind('gradient', true))),
				a.gradient ? el(Range, Object.assign({ label: __('Gradient angle (°)', 'bigspy-ai-theme-wp'), min: 0, max: 360 }, bind('gradientAngle'))) : null,
				sliderChoice(props, __('Animated highlight', 'bigspy-ai-theme-wp'), 'highlight', [['', __('None', 'bigspy-ai-theme-wp')], ['underline', __('Underline', 'bigspy-ai-theme-wp')], ['marker', __('Marker', 'bigspy-ai-theme-wp')], ['brush', __('Brush', 'bigspy-ai-theme-wp')], ['strike', __('Strike', 'bigspy-ai-theme-wp')]]),
				el('p', { className: 'description' }, __('The highlight draws itself when the heading scrolls into view.', 'bigspy-ai-theme-wp'))
			),
			(a.gradient || a.highlight) ? el(B.ColorPanel, { title: __('Effect colors', 'bigspy-ai-theme-wp'), settings: [
				{ label: __('Gradient start', 'bigspy-ai-theme-wp'), value: a.gradientFrom, onChange: function (v) { props.setAttributes({ gradientFrom: v || '' }); } },
				{ label: __('Gradient end', 'bigspy-ai-theme-wp'), value: a.gradientTo, onChange: function (v) { props.setAttributes({ gradientTo: v || '' }); } },
				{ label: __('Highlight', 'bigspy-ai-theme-wp'), value: a.highlightColor, onChange: function (v) { props.setAttributes({ highlightColor: v || '' }); } }
			] }) : null
		);
	}

	register('heading', function (props) {
		var a = props.attributes;
		var bind = B.binder(props);
		var blockProps = useBlockProps({
			className: 'bs-heading' + (a.gradient ? ' bs-heading--gradient' : '') + (a.highlight ? ' bs-heading--hl is-drawn bs-heading--hl-' + a.highlight : '') + '' + (a.balance ? ' bs-heading--balanced' : '') + (a.align ? ' has-text-align-' + a.align : ''),
			style: styleVars({ '--bs-heading-size': a.size ? a.size + 'px' : '', '--bs-heading-size-tablet': a.sizeTablet ? a.sizeTablet + 'px' : '', '--bs-heading-size-mobile': a.sizeMobile ? a.sizeMobile + 'px' : '', '--bs-heading-leading': a.lineHeight || '', '--bs-heading-max': a.maxWidth ? a.maxWidth + 'px' : '', '--bs-heading-color': a.color, '--bs-heading-weight': a.weight || '', '--bs-grad-from': a.gradientFrom, '--bs-grad-to': a.gradientTo, '--bs-grad-angle': a.gradient ? (a.gradientAngle || 90) + 'deg' : '', '--bs-hl-color': a.highlightColor })
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
				headingEffects(props),
				el(Panel, { title: __('Heading', 'bigspy-ai-theme-wp') },
					el(Select, Object.assign({ label: __('HTML tag', 'bigspy-ai-theme-wp'), options: { h1: 'H1', h2: 'H2', h3: 'H3', h4: 'H4', h5: 'H5', h6: 'H6', p: 'P', div: 'DIV' } }, bind('tag'))),
					el(Range, Object.assign({ label: __('Font size (px, 0 = default)', 'bigspy-ai-theme-wp'), max: 120 }, bind('size'))),
					el(Range, Object.assign({ label: __('Tablet font size (px, 0 = inherit)', 'bigspy-ai-theme-wp'), max: 120 }, bind('sizeTablet'))),
					el(Range, Object.assign({ label: __('Mobile font size (px, 0 = inherit)', 'bigspy-ai-theme-wp'), max: 120 }, bind('sizeMobile'))),
					el(Range, Object.assign({ label: __('Line height (0 = theme default)', 'bigspy-ai-theme-wp'), max: 2.5, step: 0.05 }, bind('lineHeight'))),
					el(Range, Object.assign({ label: __('Heading max width (px, 0 = full)', 'bigspy-ai-theme-wp'), max: 1600, step: 20 }, bind('maxWidth'))),
					el(Toggle, Object.assign({ label: __('Balance heading lines', 'bigspy-ai-theme-wp') }, bind('balance', true))),
					el(Select, Object.assign({ label: __('Font weight', 'bigspy-ai-theme-wp'), options: { '': __('Default', 'bigspy-ai-theme-wp'), '300': '300', '400': '400', '500': '500', '600': '600', '700': '700', '800': '800', '900': '900' } }, bind('weight'))),
					el(Text, Object.assign({ label: __('Link (URL)', 'bigspy-ai-theme-wp'), type: 'url' }, bind('link')))
				),
				el(B.ColorPanel, { settings: [{ label: __('Text', 'bigspy-ai-theme-wp'), value: a.color, onChange: function (v) { props.setAttributes({ color: v || '' }); } }] })
			),
			el(RichText, Object.assign({ tagName: a.tag === 'div' ? 'div' : a.tag, value: a.text, placeholder: __('Write a heading…', 'bigspy-ai-theme-wp'), onChange: function (v) { props.setAttributes({ text: v }); } }, blockProps))
		);
	});

	/* ================================================================ Text */
	register('text', function (props) {
		var a = props.attributes;
		var bind = B.binder(props);
		var anchorState = wp.element.useState(null);
		var setHtml = function (v) {
			props.setAttributes({ html: v });
		};
		var editorRef = wp.element.useRef(null);
		var blockProps = useBlockProps({
			className: 'bs-text bst-text-inline' + (a.align ? ' has-text-align-' + a.align : '') + (a.columns > 1 ? ' has-columns' : '') + (a.dropCap ? ' has-drop-cap' : ''),
			style: styleVars({
				'--bs-text-size': a.size ? a.size + 'px' : '', '--bs-text-color': a.color, '--bs-text-max': a.maxWidth ? a.maxWidth + 'px' : '',
				'--bs-text-lh': a.lineHeight ? a.lineHeight : '', '--bs-text-ls': a.letterSpacing ? a.letterSpacing + 'px' : '', '--bs-text-weight': a.weight, '--bs-text-transform': a.transform,
				'--bs-text-link': a.linkColor, '--bs-text-cols': a.columns > 1 ? a.columns : ''
			})
		});
		return el(Fragment, null,
			el(Inspector, null,
				el(Panel, { title: __('Text', 'bigspy-ai-theme-wp') },
					el(Select, Object.assign({ label: __('Align', 'bigspy-ai-theme-wp'), options: B.assign({ '': __('Default', 'bigspy-ai-theme-wp') }, ALIGN3, { justify: __('Justify', 'bigspy-ai-theme-wp') }) }, bind('align'))),
					el(Range, Object.assign({ label: __('Max width (px, 0 = full)', 'bigspy-ai-theme-wp'), max: 1400, step: 10 }, bind('maxWidth'))),
					el(Range, Object.assign({ label: __('Text columns', 'bigspy-ai-theme-wp'), min: 1, max: 4 }, bind('columns'))),
					a.columns > 1 ? el(Toggle, Object.assign({ label: __('Keep columns on mobile', 'bigspy-ai-theme-wp') }, bind('columnsMobile', true))) : null,
					el(Toggle, Object.assign({ label: __('Large first letter (drop cap)', 'bigspy-ai-theme-wp') }, bind('dropCap', true))),
					el(Toggle, Object.assign({ label: __('Paste as plain text (removes Word/web formatting)', 'bigspy-ai-theme-wp') }, bind('plainPaste', true)))
				),
				el(Panel, { title: __('HTML (advanced)', 'bigspy-ai-theme-wp'), initialOpen: false },
					el(c.TextareaControl, { className: 'bst-side-code', label: __('Edit the HTML of this text', 'bigspy-ai-theme-wp'), value: a.html, rows: 10, spellCheck: false, onChange: setHtml, __nextHasNoMarginBottom: true })
				),
				el(Panel, { title: __('Typography', 'bigspy-ai-theme-wp') },
					el(Range, Object.assign({ label: __('Font size – desktop (px, 0 = default)', 'bigspy-ai-theme-wp'), max: 80 }, bind('size'))),
					el(Range, Object.assign({ label: __('Font size – tablet (px, 0 = same)', 'bigspy-ai-theme-wp'), max: 80 }, bind('sizeTablet'))),
					el(Range, Object.assign({ label: __('Font size – mobile (px, 0 = same)', 'bigspy-ai-theme-wp'), max: 80 }, bind('sizeMobile'))),
					el(Range, Object.assign({ label: __('Line height (0 = default)', 'bigspy-ai-theme-wp'), min: 0, max: 3, step: 0.05 }, bind('lineHeight'))),
					el(Range, Object.assign({ label: __('Letter spacing (px)', 'bigspy-ai-theme-wp'), min: -2, max: 10, step: 0.1 }, bind('letterSpacing'))),
					el(Select, Object.assign({ label: __('Font weight', 'bigspy-ai-theme-wp'), options: { '': __('Default', 'bigspy-ai-theme-wp'), 300: __('Light 300', 'bigspy-ai-theme-wp'), 400: __('Normal 400', 'bigspy-ai-theme-wp'), 500: __('Medium 500', 'bigspy-ai-theme-wp'), 600: __('Semi-bold 600', 'bigspy-ai-theme-wp'), 700: __('Bold 700', 'bigspy-ai-theme-wp'), 800: __('Extra-bold 800', 'bigspy-ai-theme-wp') } }, bind('weight'))),
					el(Select, Object.assign({ label: __('Letter case', 'bigspy-ai-theme-wp'), options: { '': __('As typed', 'bigspy-ai-theme-wp'), uppercase: __('UPPERCASE', 'bigspy-ai-theme-wp'), lowercase: __('lowercase', 'bigspy-ai-theme-wp'), capitalize: __('Capitalize', 'bigspy-ai-theme-wp') } }, bind('transform')))
				),
				el(B.ColorPanel, { title: __('Colors', 'bigspy-ai-theme-wp'), settings: [
					{ label: __('Text', 'bigspy-ai-theme-wp'), value: a.color, onChange: function (v) { props.setAttributes({ color: v || '' }); } },
					{ label: __('Links', 'bigspy-ai-theme-wp'), value: a.linkColor, onChange: function (v) { props.setAttributes({ linkColor: v || '' }); } }
				] })
			),
			el(be.BlockControls, { group: 'other' },
				el(be.MediaUploadCheck, null, el(be.MediaUpload, {
					allowedTypes: ['image'],
					onSelect: function (m) { insertImageInto(editorRef.current, m); },
					render: function (o) { return el(c.ToolbarButton, { icon: 'format-image', label: __('Add image into the text (drag its corner to resize)', 'bigspy-ai-theme-wp'), onClick: o.open }); }
				}))
			),
			el('div', blockProps,
				el(VisualHtml, { value: a.html, onChange: setHtml, editorRef: editorRef, onReady: anchorState[1], plainPaste: a.plainPaste, className: 'bs-text__content bst-inline-edit', label: __('Text content', 'bigspy-ai-theme-wp') })
			),
			props.isSelected && anchorState[0] ? el(c.Popover, {
				anchor: anchorState[0],
				placement: 'top-start',
				offset: 10,
				focusOnMount: false,
				animate: false,
				variant: 'unstyled',
				className: 'bst-format-popover',
				resize: false,
				flip: true,
				shift: true
			}, el(FormatBar, { editorRef: editorRef, onChange: setHtml, plainPaste: a.plainPaste, onTogglePlain: function (v) { props.setAttributes({ plainPaste: v }); } })) : null
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
			className: 'bs-image bs-image--' + a.align + ' bs-image--hover-' + a.hover + (a.shadow ? ' has-shadow' : ''),
			style: { '--bs-image-width': a.width + '%', '--bs-image-radius': a.radius + 'px', '--bs-image-position': a.focalX + '% ' + a.focalY + '%' }
		});
		return el(Fragment, null,
			el(Inspector, null,
				el(Panel, { title: __('Image', 'bigspy-ai-theme-wp') },
					el(B.MediaPicker, { id: a.imageId, url: a.imageUrl, onChange: setImage }),
					el(Text, Object.assign({ label: __('Alt text (describes the image)', 'bigspy-ai-theme-wp') }, bind('alt'))),
					el(Select, Object.assign({ label: __('Resolution', 'bigspy-ai-theme-wp'), options: { thumbnail: __('Thumbnail', 'bigspy-ai-theme-wp'), medium: __('Medium', 'bigspy-ai-theme-wp'), medium_large: __('Medium large', 'bigspy-ai-theme-wp'), large: __('Large', 'bigspy-ai-theme-wp'), full: __('Full size', 'bigspy-ai-theme-wp') } }, bind('size')))
				),
				el(Panel, { title: __('Layout & style', 'bigspy-ai-theme-wp') },
					el(Range, Object.assign({ label: __('Width (%)', 'bigspy-ai-theme-wp'), min: 5, max: 100 }, bind('width'))),
					el(Select, Object.assign({ label: __('Align', 'bigspy-ai-theme-wp'), options: ALIGN3 }, bind('align'))),
					el(Select, Object.assign({ label: __('Crop ratio', 'bigspy-ai-theme-wp'), options: { auto: __('Original', 'bigspy-ai-theme-wp'), '1-1': '1:1', '4-3': '4:3', '3-2': '3:2', '16-9': '16:9', '3-4': '3:4' } }, bind('ratio'))),
					a.ratio !== 'auto' ? el(Range, Object.assign({ label: __('Crop focus horizontal (%)', 'bigspy-ai-theme-wp'), max: 100 }, bind('focalX'))) : null,
					a.ratio !== 'auto' ? el(Range, Object.assign({ label: __('Crop focus vertical (%)', 'bigspy-ai-theme-wp'), max: 100 }, bind('focalY'))) : null,
					el(Range, Object.assign({ label: __('Corner radius (px)', 'bigspy-ai-theme-wp'), max: 200 }, bind('radius'))),
					el(Toggle, Object.assign({ label: __('Shadow', 'bigspy-ai-theme-wp') }, bind('shadow', true))),
					el(Select, Object.assign({ label: __('Hover effect', 'bigspy-ai-theme-wp'), options: { none: __('None', 'bigspy-ai-theme-wp'), zoom: __('Zoom', 'bigspy-ai-theme-wp'), 'zoom-fade': __('Zoom & fade', 'bigspy-ai-theme-wp'), lift: __('Lift', 'bigspy-ai-theme-wp'), grayscale: __('Grayscale to color', 'bigspy-ai-theme-wp'), blur: __('Blur to sharp', 'bigspy-ai-theme-wp'), brighten: __('Brighten', 'bigspy-ai-theme-wp'), darken: __('Darken', 'bigspy-ai-theme-wp') } }, bind('hover'))),
					a.ratio === 'auto' ? el(Text, Object.assign({ label: __('Fixed height (e.g. 300px, 60vh) – crops to fill', 'bigspy-ai-theme-wp') }, bind('height'))) : null
				),
				el(B.ColorPanel, { title: __('Overlay', 'bigspy-ai-theme-wp'), settings: [{ label: __('Overlay color', 'bigspy-ai-theme-wp'), value: a.overlay, onChange: function (v) { props.setAttributes({ overlay: v || '' }); } }] }),
				a.overlay ? el(Panel, { title: __('Overlay options', 'bigspy-ai-theme-wp') },
					el(Range, Object.assign({ label: __('Overlay opacity (%)', 'bigspy-ai-theme-wp'), max: 100 }, bind('overlayOpacity'))),
					el(Toggle, Object.assign({ label: __('Show overlay only on hover', 'bigspy-ai-theme-wp') }, bind('overlayHover', true)))
				) : null,
				el(Panel, { title: __('Link', 'bigspy-ai-theme-wp'), initialOpen: false },
					el(Text, Object.assign({ label: __('Link (URL)', 'bigspy-ai-theme-wp'), type: 'url' }, bind('link'))),
					a.link ? el(Toggle, Object.assign({ label: __('Open in new tab', 'bigspy-ai-theme-wp') }, bind('newTab', true))) : null,
					a.link ? el(Select, Object.assign({ label: __('Link rel', 'bigspy-ai-theme-wp'), options: { '': __('Default', 'bigspy-ai-theme-wp'), nofollow: 'nofollow', sponsored: 'sponsored', 'nofollow sponsored': 'nofollow sponsored' } }, bind('linkRel'))) : null,
					!a.link ? el(Toggle, Object.assign({ label: __('Open full image in lightbox on click', 'bigspy-ai-theme-wp') }, bind('lightbox', true))) : null
				)
			),
			el('figure', blockProps,
				a.imageUrl
					? el('span', { className: 'bs-image__frame' + (a.ratio !== 'auto' ? ' bs-ratio bs-ratio--' + a.ratio : '') + (a.ratio === 'auto' && a.height ? ' has-height' : ''), style: a.ratio === 'auto' && a.height ? { '--bs-image-h': /^[0-9.]+$/.test(a.height) ? a.height + 'px' : a.height } : {} }, el('img', { className: 'bs-image__img', src: a.imageUrl, alt: '' }), props.isSelected ? el('span', { className: 'bst-img-corner', title: __('Drag to resize (width %)', 'bigspy-ai-theme-wp'), onPointerDown: function (e) {
						e.preventDefault();
						e.stopPropagation();
						var h = e.currentTarget;
						try { h.setPointerCapture(e.pointerId); } catch (err) {}
						var fig = h.closest('figure');
						var parentW = fig.parentElement.getBoundingClientRect().width || 1;
						var left = fig.querySelector('.bs-image__frame').getBoundingClientRect().left;
						var move = function (ev) { props.setAttributes({ width: Math.max(5, Math.min(100, Math.round((ev.clientX - left) / parentW * 100))) }); };
						var up = function () { h.removeEventListener('pointermove', move); h.removeEventListener('pointerup', up); };
						h.addEventListener('pointermove', move);
						h.addEventListener('pointerup', up);
					} }, el('em', null, a.width + '%')) : null, a.overlay ? el('span', { className: 'bs-image__overlay' + (a.overlayHover ? ' is-hover' : ''), style: { background: a.overlay, '--bs-ov-o': a.overlayOpacity / 100 } }) : null)
					: el(be.MediaPlaceholder, { icon: 'format-image', labels: { title: __('Image', 'bigspy-ai-theme-wp') }, accept: 'image/*', allowedTypes: ['image'], onSelect: setImage }),
				a.imageUrl ? el(RichText, { tagName: 'figcaption', className: 'bs-image__caption', value: a.caption, placeholder: __('Caption (optional)', 'bigspy-ai-theme-wp'), onChange: function (v) { props.setAttributes({ caption: v }); } }) : null
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
			template: [['bst/button', { text: __('Get started', 'bigspy-ai-theme-wp') }], ['bst/button', { text: __('Learn more', 'bigspy-ai-theme-wp'), style: 'outline' }]],
			orientation: 'horizontal'
		});
		return el(Fragment, null,
			el(Inspector, null,
				el(Panel, { title: __('Buttons', 'bigspy-ai-theme-wp') },
					el(Select, Object.assign({ label: __('Align', 'bigspy-ai-theme-wp'), options: B.assign({}, ALIGN3, { stretch: __('Stretch (equal width)', 'bigspy-ai-theme-wp') }) }, bind('align'))),
					el(Range, Object.assign({ label: __('Gap (px)', 'bigspy-ai-theme-wp'), max: 60 }, bind('gap'))),
					el(Toggle, Object.assign({ label: __('Stack on mobile', 'bigspy-ai-theme-wp') }, bind('stackMobile', true)))
				)
			),
			el('div', innerProps)
		);
	}, saveInner);

	var BTN_STYLES = [['primary', __('Solid', 'bigspy-ai-theme-wp')], ['secondary', __('Secondary', 'bigspy-ai-theme-wp')], ['outline', __('Outline', 'bigspy-ai-theme-wp')], ['soft', __('Soft', 'bigspy-ai-theme-wp')], ['gradient', __('Gradient', 'bigspy-ai-theme-wp')], ['shade', __('3D shade', 'bigspy-ai-theme-wp')], ['bevel', __('Bevel', 'bigspy-ai-theme-wp')], ['gloss', __('Gloss', 'bigspy-ai-theme-wp')], ['glass', __('Glass', 'bigspy-ai-theme-wp')], ['white', __('White', 'bigspy-ai-theme-wp')], ['underline', __('Underline', 'bigspy-ai-theme-wp')], ['link', __('Text link', 'bigspy-ai-theme-wp')]];

	register('button', function (props) {
		var a = props.attributes;
		var bind = B.binder(props);
		var hasColors = a.bgColor || a.textColor || a.borderColor || a.hoverBg || a.hoverText || a.scheme || ['gradient', 'soft', 'shade', 'bevel', 'gloss', 'underline', 'glass'].indexOf(a.style) !== -1;
		var cls = 'bs-btn bs-btn--' + a.style + (a.size !== 'md' ? ' bs-btn--' + a.size : '') + (a.fullWidth ? ' bs-btn--block' : '') + (hasColors ? ' has-custom-colors' : '') + (a.letterCase ? ' bs-btn--case-' + a.letterCase : '') + (a.scheme ? ' bs-btn--c-' + a.scheme : '') + (a.hoverEffect ? ' bs-btn--fx-' + a.hoverEffect : '');
		var bstyle = styleVars({ '--bsb-bg': a.bgColor, '--bsb-c': a.textColor, '--bsb-bc': a.borderColor, '--bsb-hbg': a.hoverBg, '--bsb-hc': a.hoverText, '--bsb-bg2': a.bgColor2, '--bsb-angle': a.style === 'gradient' ? a.gradientAngle + 'deg' : '' });
		if (a.radius >= 0) {
			bstyle.borderRadius = a.radius + 'px';
		}
		var px = function (v) { return v > 0 ? v + 'px' : ''; };
		var SHADOWS = { sm: '0 2px 6px rgba(0,0,0,.12)', md: '0 8px 20px -6px rgba(0,0,0,.25)', lg: '0 16px 34px -10px rgba(0,0,0,.35)', glow: '0 6px 22px -4px color-mix(in srgb, var(--bsb-bg, var(--bs-primary)) 60%, transparent)' };
		var sizing = styleVars({ '--bsb-h': px(a.btnHeight), '--bsb-hm': px(a.btnHeightMobile), '--bsb-w': px(a.btnWidth), '--bsb-px': a.padX >= 0 ? a.padX + 'px' : '', '--bsb-fs': px(a.fontSize), '--bsb-fsm': px(a.fontSizeMobile), '--bsb-fw': a.fontWeight, '--bsb-bw': a.borderWidth >= 0 ? a.borderWidth + 'px' : '', '--bsb-sh': SHADOWS[a.btnShadow] || '', '--bsb-is': px(a.iconSize) });
		if (Object.keys(sizing).length) {
			Object.assign(bstyle, sizing);
			cls += ' bs-btn--sized';
		}
		var blockProps = useBlockProps({ className: cls, style: bstyle });
		var icon = a.icon ? el(B.Icon, { name: a.icon, size: a.size === 'lg' ? 20 : 17 }) : null;
		return el(Fragment, null,
			el(Inspector, null,
				el(Panel, { title: __('Button', 'bigspy-ai-theme-wp') },
					el(Text, Object.assign({ label: __('Link (URL, tel:, mailto:)', 'bigspy-ai-theme-wp') }, bind('url'))),
					el(Toggle, Object.assign({ label: __('Open in new tab', 'bigspy-ai-theme-wp') }, bind('newTab', true))),
					el(Text, Object.assign({ label: __('Small second line (optional, e.g. phone number)', 'bigspy-ai-theme-wp') }, bind('subtext'))),
					el('div', { className: 'bst-btn-styles' },
						el('p', { className: 'bst-btn-styles__label' }, __('Style', 'bigspy-ai-theme-wp')),
						el('div', { className: 'bst-btn-styles__grid' },
							BTN_STYLES.map(function (s) {
								return el('button', { key: s[0], type: 'button', className: 'bst-btn-styles__item' + (a.style === s[0] ? ' is-selected' : ''), onClick: function () { props.setAttributes({ style: s[0] }); }, title: s[1] },
									el('span', { className: 'bs-btn bs-btn--sm bs-btn--' + s[0] + (['gradient', 'soft', 'shade', 'bevel', 'gloss', 'underline', 'glass'].indexOf(s[0]) !== -1 ? ' has-custom-colors' : '') }, s[1])
								);
							})
						)
					),
					el(Select, Object.assign({ label: __('Color', 'bigspy-ai-theme-wp'), options: { '': __('Theme default', 'bigspy-ai-theme-wp'), primary: __('Primary', 'bigspy-ai-theme-wp'), secondary: __('Secondary', 'bigspy-ai-theme-wp'), success: __('Success (green)', 'bigspy-ai-theme-wp'), alert: __('Alert (red)', 'bigspy-ai-theme-wp'), warning: __('Warning (orange)', 'bigspy-ai-theme-wp'), dark: __('Dark', 'bigspy-ai-theme-wp'), white: __('White', 'bigspy-ai-theme-wp') } }, bind('scheme'))),
					el(Select, Object.assign({ label: __('Hover effect', 'bigspy-ai-theme-wp'), options: { '': __('Default', 'bigspy-ai-theme-wp'), lift: __('Lift', 'bigspy-ai-theme-wp'), grow: __('Grow', 'bigspy-ai-theme-wp'), shine: __('Shine sweep', 'bigspy-ai-theme-wp'), pulse: __('Pulse (attention)', 'bigspy-ai-theme-wp'), fill: __('Fill from left', 'bigspy-ai-theme-wp'), 'icon-move': __('Move icon', 'bigspy-ai-theme-wp'), 'icon-reveal': __('Reveal icon on hover', 'bigspy-ai-theme-wp') } }, bind('hoverEffect'))),
					a.style === 'gradient' ? el(Range, Object.assign({ label: __('Gradient angle (°)', 'bigspy-ai-theme-wp'), min: 0, max: 360 }, bind('gradientAngle'))) : null,
					el(Select, Object.assign({ label: __('Size', 'bigspy-ai-theme-wp'), options: { xs: __('Extra small', 'bigspy-ai-theme-wp'), sm: __('Small', 'bigspy-ai-theme-wp'), md: __('Medium', 'bigspy-ai-theme-wp'), lg: __('Large', 'bigspy-ai-theme-wp'), xl: __('Extra large', 'bigspy-ai-theme-wp') } }, bind('size'))),
					el(Select, Object.assign({ label: __('Letter case', 'bigspy-ai-theme-wp'), options: { '': __('Theme default', 'bigspy-ai-theme-wp'), uppercase: __('UPPERCASE', 'bigspy-ai-theme-wp'), capitalize: __('Capitalize', 'bigspy-ai-theme-wp'), none: __('As typed', 'bigspy-ai-theme-wp') } }, bind('letterCase'))),
					el(Toggle, Object.assign({ label: __('Full width', 'bigspy-ai-theme-wp') }, bind('fullWidth', true)))
				),
				el(Panel, { title: __('Size & shape', 'bigspy-ai-theme-wp') },
					el(Range, Object.assign({ label: __('Corner radius (px, -1 = theme default, 99 = pill)', 'bigspy-ai-theme-wp'), min: -1, max: 99, reset: -1 }, bind('radius'))),
					el(Range, Object.assign({ label: __('Height (px, 0 = auto)', 'bigspy-ai-theme-wp'), max: 120, reset: 0 }, bind('btnHeight'))),
					el(Range, Object.assign({ label: __('Height on mobile (px, 0 = same)', 'bigspy-ai-theme-wp'), max: 120, reset: 0 }, bind('btnHeightMobile'))),
					!a.fullWidth ? el(Range, Object.assign({ label: __('Minimum width (px, 0 = auto)', 'bigspy-ai-theme-wp'), max: 600, step: 5, reset: 0 }, bind('btnWidth'))) : null,
					el(Range, Object.assign({ label: __('Side padding (px, -1 = size default)', 'bigspy-ai-theme-wp'), min: -1, max: 80, reset: -1 }, bind('padX'))),
					el(Range, Object.assign({ label: __('Font size (px, 0 = size default)', 'bigspy-ai-theme-wp'), max: 40, reset: 0 }, bind('fontSize'))),
					el(Range, Object.assign({ label: __('Font size on mobile (px, 0 = same)', 'bigspy-ai-theme-wp'), max: 40, reset: 0 }, bind('fontSizeMobile'))),
					el(Select, Object.assign({ label: __('Font weight', 'bigspy-ai-theme-wp'), options: { '': __('Theme default', 'bigspy-ai-theme-wp'), 400: __('Regular', 'bigspy-ai-theme-wp'), 500: __('Medium', 'bigspy-ai-theme-wp'), 600: __('Semi bold', 'bigspy-ai-theme-wp'), 700: __('Bold', 'bigspy-ai-theme-wp'), 800: __('Extra bold', 'bigspy-ai-theme-wp') } }, bind('fontWeight'))),
					el(Range, Object.assign({ label: __('Border width (px, -1 = style default)', 'bigspy-ai-theme-wp'), min: -1, max: 10, reset: -1 }, bind('borderWidth'))),
					el(Select, Object.assign({ label: __('Shadow', 'bigspy-ai-theme-wp'), options: { '': __('Style default', 'bigspy-ai-theme-wp'), sm: __('Small', 'bigspy-ai-theme-wp'), md: __('Medium', 'bigspy-ai-theme-wp'), lg: __('Large', 'bigspy-ai-theme-wp'), glow: __('Colored glow', 'bigspy-ai-theme-wp') } }, bind('btnShadow'))),
					a.icon ? el(Range, Object.assign({ label: __('Icon size (px, 0 = auto)', 'bigspy-ai-theme-wp'), max: 48, reset: 0 }, bind('iconSize'))) : null
				),
				el(B.ColorPanel, { title: __('Button colors', 'bigspy-ai-theme-wp'), settings: [
					{ label: __('Background', 'bigspy-ai-theme-wp'), value: a.bgColor, onChange: function (v) { props.setAttributes({ bgColor: v || '' }); } },
					{ label: __('Gradient end (gradient style)', 'bigspy-ai-theme-wp'), value: a.bgColor2, onChange: function (v) { props.setAttributes({ bgColor2: v || '' }); } },
					{ label: __('Text', 'bigspy-ai-theme-wp'), value: a.textColor, onChange: function (v) { props.setAttributes({ textColor: v || '' }); } },
					{ label: __('Border', 'bigspy-ai-theme-wp'), value: a.borderColor, onChange: function (v) { props.setAttributes({ borderColor: v || '' }); } },
					{ label: __('Hover background', 'bigspy-ai-theme-wp'), value: a.hoverBg, onChange: function (v) { props.setAttributes({ hoverBg: v || '' }); } },
					{ label: __('Hover text', 'bigspy-ai-theme-wp'), value: a.hoverText, onChange: function (v) { props.setAttributes({ hoverText: v || '' }); } }
				] }),
				el(Panel, { title: __('Link & accessibility', 'bigspy-ai-theme-wp'), initialOpen: false },
					el(Select, Object.assign({ label: __('Link rel', 'bigspy-ai-theme-wp'), options: { '': __('Default', 'bigspy-ai-theme-wp'), nofollow: 'nofollow', sponsored: 'sponsored', 'nofollow sponsored': 'nofollow sponsored' } }, bind('linkRel'))),
					el(Text, Object.assign({ label: __('Accessible label (aria-label)', 'bigspy-ai-theme-wp') }, bind('ariaLabel')))
				),
				el(Panel, { title: __('Icon', 'bigspy-ai-theme-wp'), initialOpen: false },
					el(B.IconPicker, bind('icon')),
					a.icon ? el(Select, Object.assign({ label: __('Icon position', 'bigspy-ai-theme-wp'), options: { before: __('Before text', 'bigspy-ai-theme-wp'), after: __('After text', 'bigspy-ai-theme-wp') } }, bind('iconPosition'))) : null
				)
			),
			el('span', blockProps,
				a.iconPosition === 'before' ? icon : null,
				a.subtext ? el('span', { className: 'bs-btn__text' },
					el(RichText, { tagName: 'span', value: a.text, placeholder: __('Button text', 'bigspy-ai-theme-wp'), allowedFormats: ['core/bold', 'core/italic'], withoutInteractiveFormatting: true, onChange: function (v) { props.setAttributes({ text: v }); } }),
					el('small', { className: 'bs-btn__sub' }, a.subtext)
				) : el(RichText, { tagName: 'span', value: a.text, placeholder: __('Button text', 'bigspy-ai-theme-wp'), allowedFormats: ['core/bold', 'core/italic'], withoutInteractiveFormatting: true, onChange: function (v) { props.setAttributes({ text: v }); } }),
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
				el(Panel, { title: __('Divider', 'bigspy-ai-theme-wp') },
					el(Select, Object.assign({ label: __('Line style', 'bigspy-ai-theme-wp'), options: { solid: __('Solid', 'bigspy-ai-theme-wp'), dashed: __('Dashed', 'bigspy-ai-theme-wp'), dotted: __('Dotted', 'bigspy-ai-theme-wp'), double: __('Double', 'bigspy-ai-theme-wp') } }, bind('style'))),
					el(Range, Object.assign({ label: __('Width (%)', 'bigspy-ai-theme-wp'), min: 1, max: 100 }, bind('width'))),
					el(Range, Object.assign({ label: __('Thickness (px)', 'bigspy-ai-theme-wp'), min: 1, max: 20 }, bind('thickness'))),
					el(Range, Object.assign({ label: __('Space above & below (px)', 'bigspy-ai-theme-wp'), max: 200 }, bind('spacing'))),
					el(Select, Object.assign({ label: __('Align', 'bigspy-ai-theme-wp'), options: ALIGN3 }, bind('align'))),
					el(Toggle, Object.assign({ label: __('Gradient line (brand colors)', 'bigspy-ai-theme-wp') }, bind('gradient', true))),
					el(Text, Object.assign({ label: __('Text in the middle (optional)', 'bigspy-ai-theme-wp') }, bind('text'))),
					el(B.IconPicker, { label: __('Icon in the middle (optional)', 'bigspy-ai-theme-wp'), value: a.icon, onChange: function (v) { props.setAttributes({ icon: v }); } })
				),
				el(B.ColorPanel, { settings: [
					{ label: __('Line color', 'bigspy-ai-theme-wp'), value: a.color, onChange: function (v) { props.setAttributes({ color: v || '' }); } },
					{ label: __('Text / icon color', 'bigspy-ai-theme-wp'), value: a.contentColor, onChange: function (v) { props.setAttributes({ contentColor: v || '' }); } }
				] })
			),
			(a.text || a.icon) ? el('div', Object.assign({}, blockProps, { className: blockProps.className.replace('bs-divider bs-divider--', 'bs-divider-x bs-divider-x--') + (a.gradient ? ' is-gradient' : ''), style: Object.assign({}, blockProps.style, styleVars({ '--bs-divider-content': a.contentColor })) }),
				el('span', { className: 'bs-divider-x__content' }, a.icon ? el(B.Icon, { name: a.icon, size: 20 }) : null, a.text ? el('span', null, a.text) : null)
			) : el('hr', Object.assign({}, blockProps, { className: blockProps.className + (a.gradient ? ' is-gradient' : '') }))
		);
	});

	register('spacer', function (props) {
		var bind = B.binder(props);
		var a = props.attributes;
		var blockProps = useBlockProps({ className: 'bs-spacer bst-spacer-editor', style: { '--bs-spacer-d': a.heightDesktop + 'px', '--bs-spacer-t': a.heightTablet + 'px', '--bs-spacer-m': a.heightMobile + 'px' } });
		return el(Fragment, null,
			el(Inspector, null,
				el(Panel, { title: __('Height', 'bigspy-ai-theme-wp') },
					el(Range, Object.assign({ label: __('Desktop (px)', 'bigspy-ai-theme-wp'), max: 400 }, bind('heightDesktop'))),
					el(Range, Object.assign({ label: __('Tablet (px)', 'bigspy-ai-theme-wp'), max: 400 }, bind('heightTablet'))),
					el(Range, Object.assign({ label: __('Mobile (px)', 'bigspy-ai-theme-wp'), max: 400 }, bind('heightMobile')))
				)
			),
			el('div', blockProps,
				el(SpacerHandle, props)
			)
		);
	});

	function SpacerHandle(props) {
		var device = useDevice();
		var key = device === 'Tablet' ? 'heightTablet' : (device === 'Mobile' ? 'heightMobile' : 'heightDesktop');
		var h = props.attributes[key];
		return el(c.ResizableBox, {
			className: 'bst-spacer-resize',
			size: { height: h, width: '100%' },
			minHeight: 0,
			maxHeight: 600,
			enable: { bottom: true },
			showHandle: props.isSelected,
			onResizeStop: function (event, direction, elt, delta) {
				var o = {};
				o[key] = Math.max(0, Math.min(600, Math.round(h + delta.height)));
				props.setAttributes(o);
			}
		}, el('span', { className: 'bst-spacer-resize__label' }, h + 'px' + (device !== 'Desktop' ? ' · ' + device : '')));
	}

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
						label: __('WooCommerce required', 'bigspy-ai-theme-wp'),
						instructions: __('Install and activate WooCommerce to display products here. This block shows nothing on the site until then.', 'bigspy-ai-theme-wp')
					}));
				}
				return edit(props);
			},
			save: saveNull
		});
	}

	function columnsPanel(bind, a, max) {
		return el(Panel, { title: __('Layout', 'bigspy-ai-theme-wp') },
			el(Select, Object.assign({ label: __('Layout', 'bigspy-ai-theme-wp'), options: { grid: __('Grid', 'bigspy-ai-theme-wp'), slider: __('Slider', 'bigspy-ai-theme-wp') } }, bind('layout'))),
			el(Range, Object.assign({ label: __('Columns – desktop', 'bigspy-ai-theme-wp'), min: 1, max: max[0] }, bind('columns'))),
			el(Range, Object.assign({ label: __('Columns – tablet', 'bigspy-ai-theme-wp'), min: 1, max: max[1] }, bind('columnsTablet'))),
			el(Range, Object.assign({ label: __('Columns – mobile', 'bigspy-ai-theme-wp'), min: 1, max: max[2] }, bind('columnsMobile'))),
			a.layout === 'slider' ? el(Range, Object.assign({ label: __('Autoplay (seconds, 0 = off)', 'bigspy-ai-theme-wp'), max: 30 }, bind('autoplay'))) : null
		);
	}

	registerShop('products', function (props) {
		var a = props.attributes;
		var bind = B.binder(props);
		var cats = useTerms('product_cat');
		var tags = useTerms('product_tag');
		return el(Fragment, null,
			el(Inspector, null,
				el(Panel, { title: __('Products', 'bigspy-ai-theme-wp') },
					el(Select, Object.assign({ label: __('Show', 'bigspy-ai-theme-wp'), options: { latest: __('Latest', 'bigspy-ai-theme-wp'), featured: __('Featured', 'bigspy-ai-theme-wp'), sale: __('On sale', 'bigspy-ai-theme-wp'), best_selling: __('Best selling', 'bigspy-ai-theme-wp'), top_rated: __('Top rated', 'bigspy-ai-theme-wp'), random: __('Random', 'bigspy-ai-theme-wp') } }, bind('type'))),
					el(c.SelectControl, { label: __('Category', 'bigspy-ai-theme-wp'), value: a.category, options: termOptions(cats), onChange: function (v) { props.setAttributes({ category: parseInt(v, 10) || 0 }); }, __nextHasNoMarginBottom: true }),
					el(Select, Object.assign({ label: __('Order by', 'bigspy-ai-theme-wp'), options: { '': __('Default (follows “Show”)', 'bigspy-ai-theme-wp'), menu_order: __('Custom order (drag & drop in WooCommerce)', 'bigspy-ai-theme-wp'), date: __('Newest', 'bigspy-ai-theme-wp'), title: __('Name A–Z', 'bigspy-ai-theme-wp'), price: __('Price: low to high', 'bigspy-ai-theme-wp'), 'price-desc': __('Price: high to low', 'bigspy-ai-theme-wp'), popularity: __('Best selling', 'bigspy-ai-theme-wp'), rating: __('Top rated', 'bigspy-ai-theme-wp'), rand: __('Random', 'bigspy-ai-theme-wp') } }, bind('orderBy'))),
					el(Range, Object.assign({ label: __('Number of products', 'bigspy-ai-theme-wp'), min: 1, max: 48 }, bind('count'))),
					a.layout === 'grid' ? el(Toggle, Object.assign({ label: __('“Load more” button', 'bigspy-ai-theme-wp'), help: __('Loads the next products without reloading the page.', 'bigspy-ai-theme-wp') }, bind('loadMore', true))) : null
				),
				el(Panel, { title: __('Filter', 'bigspy-ai-theme-wp'), initialOpen: false },
					el(c.SelectControl, { label: __('Tag', 'bigspy-ai-theme-wp'), value: a.tag, options: termOptions(tags), onChange: function (v) { props.setAttributes({ tag: parseInt(v, 10) || 0 }); }, __nextHasNoMarginBottom: true }),
					el(Text, Object.assign({ label: __('Specific product IDs (comma separated)', 'bigspy-ai-theme-wp'), help: __('Shows exactly these products, in this order.', 'bigspy-ai-theme-wp') }, bind('ids'))),
					el(Range, Object.assign({ label: __('Skip the first N products', 'bigspy-ai-theme-wp'), max: 50 }, bind('offset')))
				),
				columnsPanel(bind, a, [6, 4, 2]),
				el(Panel, { title: __('Product card', 'bigspy-ai-theme-wp') },
					el('div', { className: 'bst-card-styles' },
						[['', __('Theme', 'bigspy-ai-theme-wp')], ['bordered', __('Bordered', 'bigspy-ai-theme-wp')], ['shadow', __('Shadow', 'bigspy-ai-theme-wp')], ['boxed', __('Boxed', 'bigspy-ai-theme-wp')], ['minimal', __('Minimal', 'bigspy-ai-theme-wp')], ['colorful', __('Colorful', 'bigspy-ai-theme-wp')]].map(function (s) {
							return el('button', { key: s[0] || 'theme', type: 'button', className: 'bst-card-styles__item bst-card-styles__item--' + (s[0] || 'theme') + (a.cardStyle === s[0] ? ' is-selected' : ''), onClick: function () { props.setAttributes({ cardStyle: s[0] }); } }, el('span', { className: 'bst-card-styles__img' }), el('span', { className: 'bst-card-styles__line' }), el('em', null, s[1]));
						})
					),
					el(Select, Object.assign({ label: __('Text align', 'bigspy-ai-theme-wp'), options: { '': __('Left', 'bigspy-ai-theme-wp'), center: __('Center', 'bigspy-ai-theme-wp') } }, bind('align'))),
					el(Range, Object.assign({ label: __('Gap between cards (px, -1 = theme)', 'bigspy-ai-theme-wp'), min: -1, max: 60 }, bind('gap'))),
					el(Range, Object.assign({ label: __('Title lines (0 = no limit)', 'bigspy-ai-theme-wp'), max: 4 }, bind('titleLines'))),
					el(Toggle, Object.assign({ label: __('Category', 'bigspy-ai-theme-wp') }, bind('showCat', true))),
					el(Toggle, Object.assign({ label: __('Title', 'bigspy-ai-theme-wp') }, bind('showTitle', true))),
					el(Toggle, Object.assign({ label: __('Rating stars', 'bigspy-ai-theme-wp') }, bind('showRating', true))),
					el(Toggle, Object.assign({ label: __('Price', 'bigspy-ai-theme-wp') }, bind('showPrice', true))),
					el(Toggle, Object.assign({ label: __('Add to cart button', 'bigspy-ai-theme-wp') }, bind('showButton', true))),
					el(Toggle, Object.assign({ label: __('Sale / new badges', 'bigspy-ai-theme-wp') }, bind('showBadge', true)))
				),
				el(B.ColorPanel, { title: __('Card colors', 'bigspy-ai-theme-wp'), settings: [
					{ label: __('Card background', 'bigspy-ai-theme-wp'), value: a.cardBg, onChange: function (v) { props.setAttributes({ cardBg: v || '' }); } },
					{ label: __('Price', 'bigspy-ai-theme-wp'), value: a.priceColor, onChange: function (v) { props.setAttributes({ priceColor: v || '' }); } },
					{ label: __('Sale badge', 'bigspy-ai-theme-wp'), value: a.badgeColor, onChange: function (v) { props.setAttributes({ badgeColor: v || '' }); } },
					{ label: __('Button', 'bigspy-ai-theme-wp'), value: a.buttonColor, onChange: function (v) { props.setAttributes({ buttonColor: v || '' }); } }
				] })
			),
			dynamicPreview('products', props)
		);
	});

	var CAT_STYLES = [['overlay', __('Name on image', 'bigspy-ai-theme-wp')], ['gradient', __('Color gradient', 'bigspy-ai-theme-wp')], ['card', __('Card', 'bigspy-ai-theme-wp')], ['tile', __('Colored tile', 'bigspy-ai-theme-wp')], ['circle', __('Circle', 'bigspy-ai-theme-wp')], ['ring', __('Circle + ring', 'bigspy-ai-theme-wp')], ['pill', __('Pill chip', 'bigspy-ai-theme-wp')], ['split', __('Image + text row', 'bigspy-ai-theme-wp')], ['minimal', __('Minimal', 'bigspy-ai-theme-wp')]];

	registerShop('product-categories', function (props) {
		var a = props.attributes;
		var bind = B.binder(props);
		var cats = useTerms('product_cat');
		return el(Fragment, null,
			el(Inspector, null,
				el(Panel, { title: __('Categories', 'bigspy-ai-theme-wp') },
					el(Range, Object.assign({ label: __('Number of categories', 'bigspy-ai-theme-wp'), min: 1, max: 60 }, bind('count'))),
					el(c.SelectControl, { label: __('Only children of', 'bigspy-ai-theme-wp'), value: a.parent, options: termOptions(cats), onChange: function (v) { props.setAttributes({ parent: parseInt(v, 10) || 0 }); }, __nextHasNoMarginBottom: true }),
					!a.parent ? el(Toggle, Object.assign({ label: __('Top-level categories only', 'bigspy-ai-theme-wp') }, bind('topLevelOnly', true))) : null,
					el(Toggle, Object.assign({ label: __('Hide empty categories', 'bigspy-ai-theme-wp') }, bind('hideEmpty', true))),
					el(Select, Object.assign({ label: __('Order by', 'bigspy-ai-theme-wp'), options: { menu_order: __('Custom order (drag & drop in WooCommerce)', 'bigspy-ai-theme-wp'), name: __('Name', 'bigspy-ai-theme-wp'), count: __('Most products', 'bigspy-ai-theme-wp') } }, bind('orderBy')))
				),
				el(Panel, { title: __('Style', 'bigspy-ai-theme-wp') },
					el('div', { className: 'bst-cat-styles' },
						CAT_STYLES.map(function (s) {
							return el('button', { key: s[0], type: 'button', className: 'bst-cat-styles__item bst-cat-styles__item--' + s[0] + (a.style === s[0] ? ' is-selected' : ''), onClick: function () { props.setAttributes({ style: s[0] }); } }, el('span', { className: 'bst-cat-styles__img' }), el('em', null, s[1]));
						})
					),
					el(Select, Object.assign({ label: __('Hover effect', 'bigspy-ai-theme-wp'), options: { zoom: __('Zoom image', 'bigspy-ai-theme-wp'), lift: __('Lift', 'bigspy-ai-theme-wp'), none: __('None', 'bigspy-ai-theme-wp') } }, bind('hover'))),
					el(Range, Object.assign({ label: __('Name size (px, 0 = default)', 'bigspy-ai-theme-wp'), max: 40 }, bind('nameSize'))),
					el(B.ColorPanel, { title: __('Accent color', 'bigspy-ai-theme-wp'), settings: [{ label: __('Accent (tiles, rings, pills)', 'bigspy-ai-theme-wp'), value: a.accent, onChange: function (v) { props.setAttributes({ accent: v || '' }); } }] }),
					el(Select, Object.assign({ label: __('Image ratio', 'bigspy-ai-theme-wp'), options: { '1-1': '1:1', '4-3': '4:3', '3-4': '3:4', '16-9': '16:9' } }, bind('ratio'))),
					el(Toggle, Object.assign({ label: __('Show product count', 'bigspy-ai-theme-wp') }, bind('showCount', true)))
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
				el(Panel, { title: __('Testimonial', 'bigspy-ai-theme-wp') },
					el(B.MediaPicker, { label: __('Photo', 'bigspy-ai-theme-wp'), id: a.imageId, url: a.imageUrl, onChange: function (m) { props.setAttributes({ imageId: m.id, imageUrl: m.url }); } }),
					el(Range, Object.assign({ label: __('Star rating (0 = hide)', 'bigspy-ai-theme-wp'), max: 5 }, bind('rating'))),
					el(Select, Object.assign({ label: __('Style', 'bigspy-ai-theme-wp'), options: { card: __('Card', 'bigspy-ai-theme-wp'), bubble: __('Speech bubble', 'bigspy-ai-theme-wp'), minimal: __('Minimal', 'bigspy-ai-theme-wp') } }, bind('style'))),
					el(Select, Object.assign({ label: __('Align', 'bigspy-ai-theme-wp'), options: { left: __('Left', 'bigspy-ai-theme-wp'), center: __('Center', 'bigspy-ai-theme-wp') } }, bind('align')))
				)
			),
			el('figure', blockProps,
				a.rating ? el('div', { className: 'bs-testimonial__rating' }, stars) : null,
				el(RichText, { tagName: 'blockquote', className: 'bs-testimonial__text', value: a.text, placeholder: __('What did the customer say?', 'bigspy-ai-theme-wp'), onChange: function (v) { props.setAttributes({ text: v }); } }),
				el('figcaption', { className: 'bs-testimonial__author' },
					a.imageUrl ? el('img', { className: 'bs-testimonial__img', src: a.imageUrl, alt: '' }) : null,
					el('span', null,
						el(RichText, { tagName: 'strong', className: 'bs-testimonial__name', value: a.name, placeholder: __('Name', 'bigspy-ai-theme-wp'), onChange: function (v) { props.setAttributes({ name: v }); } }),
						el(RichText, { tagName: 'span', className: 'bs-testimonial__role', value: a.role, placeholder: __('Position, company', 'bigspy-ai-theme-wp'), onChange: function (v) { props.setAttributes({ role: v }); } })
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
				el(Panel, { title: __('Counter', 'bigspy-ai-theme-wp') },
					el(Text, Object.assign({ label: __('Number', 'bigspy-ai-theme-wp') }, bind('number'))),
					el(Text, Object.assign({ label: __('Prefix', 'bigspy-ai-theme-wp') }, bind('prefix'))),
					el(Text, Object.assign({ label: __('Suffix', 'bigspy-ai-theme-wp') }, bind('suffix'))),
					el(Select, Object.assign({ label: __('Thousands separator', 'bigspy-ai-theme-wp'), options: { '.': '1.000', ',': '1,000', ' ': '1 000', '': '1000' } }, bind('separator'))),
					el(Range, Object.assign({ label: __('Animation duration (ms)', 'bigspy-ai-theme-wp'), min: 200, max: 6000, step: 100 }, bind('duration'))),
					el(Range, Object.assign({ label: __('Number size (px)', 'bigspy-ai-theme-wp'), min: 16, max: 140 }, bind('size'))),
					el(Select, Object.assign({ label: __('Align', 'bigspy-ai-theme-wp'), options: ALIGN3 }, bind('align')))
				),
				el(B.ColorPanel, { settings: [{ label: __('Number color', 'bigspy-ai-theme-wp'), value: a.color, onChange: function (v) { props.setAttributes({ color: v || '' }); } }] })
			),
			el('div', blockProps,
				el('div', { className: 'bs-counter__value' },
					a.prefix ? el('span', { className: 'bs-counter__affix' }, a.prefix) : null,
					el('span', { className: 'bs-counter__number' }, a.number),
					a.suffix ? el('span', { className: 'bs-counter__affix' }, a.suffix) : null
				),
				el(RichText, { tagName: 'div', className: 'bs-counter__label', value: a.label, placeholder: __('Label (e.g. Happy customers)', 'bigspy-ai-theme-wp'), onChange: function (v) { props.setAttributes({ label: v }); } })
			)
		);
	});

	/* ================================================================ Map */
	register('map', function (props) {
		var a = props.attributes;
		var bind = B.binder(props);
		var blockProps = useBlockProps({ className: 'bs-map' + (a.grayscale ? ' is-grayscale' : '') + ((a.boxTitle || a.boxText) ? ' has-box bs-map--box-' + (a.boxPosition || 'left') : ''), style: { '--bs-map-h': a.height + 'px', '--bs-map-radius': a.radius + 'px' } });
		var embedSrc = '';
		var embedOk = null;
		if (a.embed && a.embed.trim()) {
			var m = a.embed.match(/src=["']([^"']+)["']/i);
			var raw = (m ? m[1] : a.embed.trim()).replace(/&amp;/g, '&');
			embedOk = /^https:\/\/(www\.)?google\.[a-z.]+\/maps\/embed\?/i.test(raw) || /^https:\/\/maps\.google\.[a-z.]+\/maps\?.*output=embed/i.test(raw);
			embedSrc = embedOk ? raw : '';
		}
		var where = a.lat && a.lng ? a.lat + ',' + a.lng : a.address;
		var src = embedSrc || (where ? 'https://maps.google.com/maps?q=' + encodeURIComponent(where) + '&z=' + a.zoom + '&output=embed' : '');
		return el(Fragment, null,
			el(Inspector, null,
				el(Panel, { title: __('Map', 'bigspy-ai-theme-wp') },
					el(c.TextareaControl, {
						label: __('Google Maps embed code (recommended)', 'bigspy-ai-theme-wp'),
						help: __('On Google Maps: open your place → Share → Embed a map → Copy HTML, then paste it here. The exact pin, name and reviews are shown. Leave empty to use the address below.', 'bigspy-ai-theme-wp'),
						value: a.embed,
						rows: 4,
						placeholder: '<iframe src="https://www.google.com/maps/embed?pb=…"></iframe>',
						onChange: function (v) { props.setAttributes({ embed: v }); },
						__nextHasNoMarginBottom: true
					}),
					embedOk === false ? el(c.Notice, { status: 'warning', isDismissible: false }, __('This is not a Google Maps embed code. Copy it from Share → Embed a map.', 'bigspy-ai-theme-wp')) : null,
					!embedSrc ? el(Text, Object.assign({ label: __('…or an address', 'bigspy-ai-theme-wp'), help: __('Empty = address from Customizer → Header → Contact info.', 'bigspy-ai-theme-wp') }, bind('address'))) : null,
					!embedSrc ? el('div', { className: 'bst-two' }, el(Text, Object.assign({ label: __('Latitude (optional)', 'bigspy-ai-theme-wp') }, bind('lat'))), el(Text, Object.assign({ label: __('Longitude', 'bigspy-ai-theme-wp') }, bind('lng')))) : null,
					el(Toggle, Object.assign({ label: __('Black & white map', 'bigspy-ai-theme-wp') }, bind('grayscale', true))),
					el(Range, Object.assign({ label: __('Zoom', 'bigspy-ai-theme-wp'), min: 1, max: 21 }, bind('zoom'))),
					el(Range, Object.assign({ label: __('Height (px)', 'bigspy-ai-theme-wp'), min: 150, max: 1000 }, bind('height'))),
					el(Range, Object.assign({ label: __('Corner radius (px)', 'bigspy-ai-theme-wp'), max: 60 }, bind('radius')))
				),
				el(Panel, { title: __('Info box on the map', 'bigspy-ai-theme-wp'), initialOpen: false },
					el(Text, Object.assign({ label: __('Title (e.g. shop name)', 'bigspy-ai-theme-wp') }, bind('boxTitle'))),
					el(c.TextareaControl, { label: __('Text (address, hours, phone)', 'bigspy-ai-theme-wp'), value: a.boxText, onChange: function (v) { props.setAttributes({ boxText: v }); }, __nextHasNoMarginBottom: true }),
					sliderChoice(props, __('Position', 'bigspy-ai-theme-wp'), 'boxPosition', [['left', __('Left', 'bigspy-ai-theme-wp')], ['right', __('Right', 'bigspy-ai-theme-wp')]]),
					el(Toggle, Object.assign({ label: __('“Get directions” button', 'bigspy-ai-theme-wp') }, bind('directions', true)))
				)
			),
			el('div', blockProps,
				(a.boxTitle || a.boxText) ? el('div', { className: 'bs-map__box' }, a.boxTitle ? el('h3', { className: 'bs-map__title' }, a.boxTitle) : null, a.boxText ? el('div', { className: 'bs-map__text' }, a.boxText) : null) : null,
				src ? el('iframe', { src: src, title: a.address, style: { pointerEvents: 'none' } }) :
					el(c.Placeholder, { icon: 'location-alt', label: __('Google Map', 'bigspy-ai-theme-wp'), instructions: __('Enter an address in the block settings (or leave empty to use the site address).', 'bigspy-ai-theme-wp') })
			)
		);
	});
	/* ================================================================ Gallery */
	register('gallery', function (props) {
		var a = props.attributes;
		var bind = B.binder(props);
		var pick = function (list) {
			props.setAttributes({ images: list.map(function (m) { return { id: m.id, url: (m.sizes && m.sizes.large ? m.sizes.large.url : m.url), alt: m.alt || '', caption: m.caption || '' }; }) });
		};
		var setItem = function (i, key, v) {
			var list = a.images.slice();
			list[i] = Object.assign({}, list[i]);
			list[i][key] = v;
			props.setAttributes({ images: list });
		};
		var blockProps = useBlockProps({ className: 'bs-gallery bs-gallery--' + (a.layout === 'slider' ? 'grid' : a.layout) + ' bs-gallery--hover-' + a.hover, style: { '--bs-gal-cols': a.columns, '--bs-gal-gap': a.gap + 'px', '--bs-gal-radius': a.radius + 'px' } });
		var ratio = a.layout === 'masonry' ? 'auto' : a.ratio;
		var picker = function (label) {
			return el(be.MediaUploadCheck, null, el(be.MediaUpload, {
				multiple: true, gallery: true, addToGallery: !!a.images.length, allowedTypes: ['image'],
				value: a.images.map(function (i) { return i.id; }),
				onSelect: pick,
				render: function (o) { return el(c.Button, { variant: 'secondary', onClick: o.open }, label); }
			}));
		};
		return el(Fragment, null,
			el(Inspector, null,
				el(Panel, { title: __('Images', 'bigspy-ai-theme-wp') },
					picker(a.images.length ? __('Edit / add images', 'bigspy-ai-theme-wp') : __('Choose images', 'bigspy-ai-theme-wp')),
					a.images.map(function (img, i) {
						return el('details', { key: img.id || i, className: 'bst-gallery-item' },
							el('summary', null, (i + 1) + '. ' + (img.alt || img.url.split('/').pop())),
							el(Text, { label: __('Alt text', 'bigspy-ai-theme-wp'), value: img.alt || '', onChange: function (v) { setItem(i, 'alt', v); } }),
							el(Text, { label: __('Caption', 'bigspy-ai-theme-wp'), value: img.caption || '', onChange: function (v) { setItem(i, 'caption', v); } }),
							el(Text, { label: __('Custom link (replaces lightbox)', 'bigspy-ai-theme-wp'), value: img.link || '', onChange: function (v) { setItem(i, 'link', v); } })
						);
					})
				),
				el(Panel, { title: __('Layout', 'bigspy-ai-theme-wp') },
					el(Select, Object.assign({ label: __('Layout', 'bigspy-ai-theme-wp'), options: { grid: __('Grid', 'bigspy-ai-theme-wp'), masonry: __('Masonry', 'bigspy-ai-theme-wp'), slider: __('Slider', 'bigspy-ai-theme-wp') } }, bind('layout'))),
					el(Range, Object.assign({ label: __('Columns – desktop', 'bigspy-ai-theme-wp'), min: 1, max: 8 }, bind('columns'))),
					el(Range, Object.assign({ label: __('Columns – tablet', 'bigspy-ai-theme-wp'), min: 1, max: 6 }, bind('columnsTablet'))),
					el(Range, Object.assign({ label: __('Columns – mobile', 'bigspy-ai-theme-wp'), min: 1, max: 4 }, bind('columnsMobile'))),
					el(Range, Object.assign({ label: __('Gap (px)', 'bigspy-ai-theme-wp'), max: 80 }, bind('gap'))),
					a.layout !== 'masonry' ? el(Select, Object.assign({ label: __('Crop ratio', 'bigspy-ai-theme-wp'), options: { auto: __('Original', 'bigspy-ai-theme-wp'), '1-1': '1:1', '4-3': '4:3', '3-2': '3:2', '16-9': '16:9', '3-4': '3:4' } }, bind('ratio'))) : null,
					el(Select, Object.assign({ label: __('Resolution', 'bigspy-ai-theme-wp'), options: { medium: __('Medium', 'bigspy-ai-theme-wp'), medium_large: __('Medium large', 'bigspy-ai-theme-wp'), large: __('Large', 'bigspy-ai-theme-wp'), full: __('Full size', 'bigspy-ai-theme-wp') } }, bind('size'))),
					el(Range, Object.assign({ label: __('Corner radius (px)', 'bigspy-ai-theme-wp'), max: 100 }, bind('radius'))),
					el(Select, Object.assign({ label: __('Hover effect', 'bigspy-ai-theme-wp'), options: { none: __('None', 'bigspy-ai-theme-wp'), zoom: __('Zoom', 'bigspy-ai-theme-wp'), lift: __('Lift', 'bigspy-ai-theme-wp'), grayscale: __('Grayscale to color', 'bigspy-ai-theme-wp'), darken: __('Darken', 'bigspy-ai-theme-wp') } }, bind('hover'))),
					el(Toggle, Object.assign({ label: __('Open in lightbox', 'bigspy-ai-theme-wp') }, bind('lightbox', true))),
					el(Toggle, Object.assign({ label: __('Show captions', 'bigspy-ai-theme-wp') }, bind('captions', true)))
				),
				a.layout === 'slider' ? el(Panel, { title: __('Slider', 'bigspy-ai-theme-wp') },
					el(Range, Object.assign({ label: __('Autoplay (seconds, 0 = off)', 'bigspy-ai-theme-wp'), max: 30 }, bind('autoplay'))),
					el(Toggle, Object.assign({ label: __('Arrows', 'bigspy-ai-theme-wp') }, bind('arrows', true))),
					el(Toggle, Object.assign({ label: __('Dots', 'bigspy-ai-theme-wp') }, bind('dots', true)))
				) : null
			),
			a.images.length ? el('div', blockProps,
				el('div', { className: 'bs-gallery__grid' },
					a.images.map(function (img, i) {
						return el('figure', { key: i, className: 'bs-gallery__item' },
							el('span', { className: 'bs-gallery__frame' + (ratio !== 'auto' ? ' bs-ratio bs-ratio--' + ratio : '') }, el('img', { className: 'bs-gallery__img', src: img.url, alt: '' })),
							a.captions && img.caption ? el('figcaption', { className: 'bs-gallery__caption' }, img.caption) : null
						);
					})
				)
			) : el('div', useBlockProps(), el(c.Placeholder, { icon: 'format-gallery', label: __('Gallery', 'bigspy-ai-theme-wp'), instructions: __('Choose images from the media library.', 'bigspy-ai-theme-wp') }, picker(__('Choose images', 'bigspy-ai-theme-wp'))))
		);
	});

	/* ================================================================ Video */
	register('video', function (props) {
		var a = props.attributes;
		var bind = B.binder(props);
		var yt = (a.url || '').match(/(?:youtube\.com\/(?:watch\?v=|embed\/|shorts\/)|youtu\.be\/)([A-Za-z0-9_-]{6,})/);
		var poster = a.posterUrl || (yt ? 'https://i.ytimg.com/vi/' + yt[1] + '/hqdefault.jpg' : '');
		var blockProps = useBlockProps({ className: 'bs-video bs-ratio bs-ratio--' + a.ratio, style: { '--bs-video-radius': a.radius + 'px' } });
		return el(Fragment, null,
			el(Inspector, null,
				el(Panel, { title: __('Video', 'bigspy-ai-theme-wp') },
					el(Text, Object.assign({ label: __('YouTube, Vimeo or .mp4 URL', 'bigspy-ai-theme-wp') }, bind('url'))),
					el(Text, Object.assign({ label: __('Title (accessibility)', 'bigspy-ai-theme-wp') }, bind('title'))),
					el(Select, Object.assign({ label: __('Ratio', 'bigspy-ai-theme-wp'), options: { '16-9': '16:9', '4-3': '4:3', '1-1': '1:1', '9-16': '9:16 (Shorts/Reels)', '21-9': '21:9' } }, bind('ratio'))),
					el(Range, Object.assign({ label: __('Corner radius (px)', 'bigspy-ai-theme-wp'), max: 60 }, bind('radius'))),
					el(B.MediaPicker, { label: __('Poster image (loads the player only on click – faster pages)', 'bigspy-ai-theme-wp'), id: a.posterId, url: a.posterUrl, onChange: function (m) { props.setAttributes({ posterId: m.id || 0, posterUrl: m.url || '' }); } })
				),
				el(Panel, { title: __('Playback', 'bigspy-ai-theme-wp'), initialOpen: false },
					el(Toggle, Object.assign({ label: __('Autoplay (muted)', 'bigspy-ai-theme-wp') }, bind('autoplay', true))),
					el(Toggle, Object.assign({ label: __('Muted', 'bigspy-ai-theme-wp') }, bind('muted', true))),
					el(Toggle, Object.assign({ label: __('Loop', 'bigspy-ai-theme-wp') }, bind('loop', true))),
					el(Toggle, Object.assign({ label: __('Show controls', 'bigspy-ai-theme-wp') }, bind('controls', true)))
				)
			),
			el('div', blockProps,
				a.url ? (poster ? el('span', { className: 'bs-video__poster' }, el('img', { src: poster, alt: '' }), el('span', { className: 'bs-video__play' }, el(B.Icon, { name: 'play', size: 30 }))) : el('div', { className: 'bst-video-placeholder' }, a.url))
					: el(c.Placeholder, { icon: 'video-alt3', label: __('Video', 'bigspy-ai-theme-wp'), instructions: __('Paste a YouTube, Vimeo or .mp4 URL in the block settings.', 'bigspy-ai-theme-wp') })
			)
		);
	});

	/* ================================================================ Social follow */
	var NETWORKS = { facebook: 'Facebook', instagram: 'Instagram', youtube: 'YouTube', tiktok: 'TikTok', zalo: 'Zalo (link or phone)', messenger: 'Messenger', whatsapp: 'WhatsApp', x: 'X (Twitter)', linkedin: 'LinkedIn', pinterest: 'Pinterest', mail: 'Email', phone: 'Phone' };
	register('social', function (props) {
		var a = props.attributes;
		var bind = B.binder(props);
		var set = Object.keys(NETWORKS).filter(function (k) { return a[k]; });
		var blockProps = useBlockProps({
			className: 'bs-follow bs-follow--' + a.style + ' bs-follow--' + a.shape + ' bs-follow--align-' + a.align + (a.brand ? ' is-brand' : ''),
			style: styleVars({ '--bs-follow-size': a.size + 'px', '--bs-follow-gap': a.gap + 'px', '--bs-follow-color': a.color })
		});
		return el(Fragment, null,
			el(Inspector, null,
				el(Panel, { title: __('Profiles', 'bigspy-ai-theme-wp') },
					Object.keys(NETWORKS).map(function (k) { return el(Text, Object.assign({ key: k, label: NETWORKS[k] }, bind(k))); })
				),
				el(Panel, { title: __('Style', 'bigspy-ai-theme-wp') },
					el(Text, Object.assign({ label: __('Label before icons (optional)', 'bigspy-ai-theme-wp') }, bind('label'))),
					el(Select, Object.assign({ label: __('Style', 'bigspy-ai-theme-wp'), options: { fill: __('Filled', 'bigspy-ai-theme-wp'), outline: __('Outline', 'bigspy-ai-theme-wp'), plain: __('Icon only', 'bigspy-ai-theme-wp') } }, bind('style'))),
					el(Select, Object.assign({ label: __('Shape', 'bigspy-ai-theme-wp'), options: { circle: __('Circle', 'bigspy-ai-theme-wp'), rounded: __('Rounded', 'bigspy-ai-theme-wp'), square: __('Square', 'bigspy-ai-theme-wp') } }, bind('shape'))),
					el(Range, Object.assign({ label: __('Size (px)', 'bigspy-ai-theme-wp'), min: 24, max: 96 }, bind('size'))),
					el(Range, Object.assign({ label: __('Gap (px)', 'bigspy-ai-theme-wp'), max: 40 }, bind('gap'))),
					el(Select, Object.assign({ label: __('Align', 'bigspy-ai-theme-wp'), options: ALIGN3 }, bind('align'))),
					el(Toggle, Object.assign({ label: __('Brand colors', 'bigspy-ai-theme-wp') }, bind('brand', true))),
					!a.brand ? el(B.ColorPanel, { title: __('Icon color', 'bigspy-ai-theme-wp'), settings: [{ label: __('Color', 'bigspy-ai-theme-wp'), value: a.color, onChange: function (v) { props.setAttributes({ color: v || '' }); } }] }) : null
				)
			),
			el('div', blockProps,
				a.label ? el('span', { className: 'bs-follow__label' }, a.label) : null,
				set.length ? el('div', { className: 'bs-follow__icons' }, set.map(function (k) {
					return el('span', { key: k, className: 'bs-follow__link bs-follow__link--' + k }, el(B.Icon, { name: k, size: Math.round(a.size * 0.48) }));
				})) : el('em', null, __('Add profile links in the block settings.', 'bigspy-ai-theme-wp'))
			)
		);
	});
	/* ================================================================ Icon list */
	function IconDropdown(props) {
		return el(c.Dropdown, {
			className: 'bst-icon-dropdown',
			contentClassName: 'bst-icon-dropdown__content',
			popoverProps: { placement: 'bottom-start' },
			renderToggle: function (o) {
				return el('button', { type: 'button', className: 'bs-icon-list__icon bst-icon-toggle', onClick: o.onToggle, 'aria-expanded': o.isOpen, title: __('Change icon', 'bigspy-ai-theme-wp') }, props.value ? el(B.Icon, { name: props.value, size: props.size }) : '+');
			},
			renderContent: function () {
				return el('div', { style: { padding: 8, width: 260 } }, el(B.IconPicker, { value: props.value, onChange: props.onChange }));
			}
		});
	}

	register('icon-list', function (props) {
		var a = props.attributes;
		var bind = B.binder(props);
		var items = a.items || [];
		var setItem = function (i, key, v) {
			var list = items.slice();
			list[i] = Object.assign({}, list[i]);
			list[i][key] = v;
			props.setAttributes({ items: list });
		};
		var remove = function (i) {
			props.setAttributes({ items: items.filter(function (x, j) { return j !== i; }) });
		};
		var add = function (after) {
			var list = items.slice();
			var icon = items.length ? items[items.length - 1].icon : 'check';
			list.splice(after + 1, 0, { icon: icon, text: '', link: '' });
			props.setAttributes({ items: list });
		};
		var blockProps = useBlockProps({
			className: 'bs-icon-list bs-icon-list--' + a.layout + ' bs-icon-list--align-' + a.align + (a.divider ? ' has-divider' : '') + (a.iconBg ? ' has-icon-bg' : ''),
			style: styleVars({ '--bs-il-size': a.iconSize + 'px', '--bs-il-color': a.iconColor, '--bs-il-gap': a.gap + 'px', '--bs-il-cols': a.columns, '--bs-il-text': a.textSize ? a.textSize + 'px' : '' })
		});
		return el(Fragment, null,
			el(Inspector, null,
				el(Panel, { title: __('List', 'bigspy-ai-theme-wp') },
					el(Select, Object.assign({ label: __('Layout', 'bigspy-ai-theme-wp'), options: { vertical: __('Vertical', 'bigspy-ai-theme-wp'), inline: __('Inline (one line)', 'bigspy-ai-theme-wp'), columns: __('Columns', 'bigspy-ai-theme-wp') } }, bind('layout'))),
					a.layout === 'columns' ? el(Range, Object.assign({ label: __('Columns', 'bigspy-ai-theme-wp'), min: 1, max: 4 }, bind('columns'))) : null,
					el(Select, Object.assign({ label: __('Align', 'bigspy-ai-theme-wp'), options: ALIGN3 }, bind('align'))),
					el(Range, Object.assign({ label: __('Space between items (px)', 'bigspy-ai-theme-wp'), max: 60 }, bind('gap'))),
					el(Range, Object.assign({ label: __('Icon size (px)', 'bigspy-ai-theme-wp'), min: 10, max: 64 }, bind('iconSize'))),
					el(Range, Object.assign({ label: __('Text size (px, 0 = default)', 'bigspy-ai-theme-wp'), max: 40 }, bind('textSize'))),
					el(Toggle, Object.assign({ label: __('Icon in a soft circle', 'bigspy-ai-theme-wp') }, bind('iconBg', true))),
					el(Toggle, Object.assign({ label: __('Divider between items', 'bigspy-ai-theme-wp') }, bind('divider', true)))
				),
				el(B.ColorPanel, { title: __('Icon color', 'bigspy-ai-theme-wp'), settings: [{ label: __('Icon', 'bigspy-ai-theme-wp'), value: a.iconColor, onChange: function (v) { props.setAttributes({ iconColor: v || '' }); } }] }),
				el(Panel, { title: __('Links', 'bigspy-ai-theme-wp'), initialOpen: false },
					items.map(function (it, i) {
						return el(Text, { key: i, label: (i + 1) + '. ' + (String(it.text || '').replace(/<[^>]+>/g, '').slice(0, 30) || __('(empty)', 'bigspy-ai-theme-wp')), value: it.link || '', placeholder: 'https://', onChange: function (v) { setItem(i, 'link', v); } });
					})
				)
			),
			el('ul', blockProps,
				items.map(function (it, i) {
					return el('li', { key: i, className: 'bs-icon-list__item bst-il-item' },
						el(IconDropdown, { value: it.icon, size: a.iconSize, onChange: function (v) { setItem(i, 'icon', v); } }),
						el(RichText, {
							tagName: 'span', className: 'bs-icon-list__text', value: it.text, placeholder: __('List item…', 'bigspy-ai-theme-wp'),
							allowedFormats: ['core/bold', 'core/italic', 'core/link'],
							onChange: function (v) { setItem(i, 'text', v); },
							onSplit: undefined,
							__unstableOnSplitAtEnd: function () { add(i); },
							onRemove: function () { remove(i); }
						}),
						props.isSelected ? el('button', { type: 'button', className: 'bst-il-remove', onClick: function () { remove(i); }, 'aria-label': __('Remove item', 'bigspy-ai-theme-wp') }, '×') : null
					);
				}),
				props.isSelected ? el('li', { className: 'bst-il-add' }, el(c.Button, { variant: 'secondary', size: 'small', icon: 'plus', onClick: function () { add(items.length - 1); } }, __('Add item', 'bigspy-ai-theme-wp'))) : null
			)
		);
	});

	/* ================================================================ Image box */
	register('image-box', function (props) {
		var a = props.attributes;
		var bind = B.binder(props);
		var setImage = function (m) { props.setAttributes({ imageId: m.id || 0, imageUrl: (m.sizes && m.sizes.large ? m.sizes.large.url : m.url) || '', alt: a.alt || m.alt || '' }); };
		var blockProps = useBlockProps({
			className: 'bs-image-box bs-image-box--' + a.layout + ' bs-image-box--align-' + a.align + ' bs-image-box--hover-' + a.hover + (a.card ? ' is-card' : ''),
			style: { '--bs-ib-radius': a.radius + 'px', '--bs-ib-img-w': a.imageWidth + '%' }
		});
		return el(Fragment, null,
			el(be.BlockControls, null, el(c.ToolbarGroup, null,
				el(be.MediaReplaceFlow, { mediaId: a.imageId, mediaURL: a.imageUrl, allowedTypes: ['image'], accept: 'image/*', onSelect: setImage, name: a.imageUrl ? __('Replace image', 'bigspy-ai-theme-wp') : __('Add image', 'bigspy-ai-theme-wp') })
			)),
			el(Inspector, null,
				el(Panel, { title: __('Image box', 'bigspy-ai-theme-wp') },
					el(B.MediaPicker, { id: a.imageId, url: a.imageUrl, onChange: setImage }),
					el(Text, Object.assign({ label: __('Alt text', 'bigspy-ai-theme-wp') }, bind('alt'))),
					el(Select, Object.assign({ label: __('Layout', 'bigspy-ai-theme-wp'), options: { top: __('Image on top', 'bigspy-ai-theme-wp'), left: __('Image left', 'bigspy-ai-theme-wp'), right: __('Image right', 'bigspy-ai-theme-wp'), overlay: __('Text over image', 'bigspy-ai-theme-wp') } }, bind('layout'))),
					a.layout === 'left' || a.layout === 'right' ? el(Range, Object.assign({ label: __('Image width (%)', 'bigspy-ai-theme-wp'), min: 15, max: 70 }, bind('imageWidth'))) : null,
					el(Select, Object.assign({ label: __('Image ratio', 'bigspy-ai-theme-wp'), options: { auto: __('Original', 'bigspy-ai-theme-wp'), '1-1': '1:1', '4-3': '4:3', '3-2': '3:2', '16-9': '16:9', '3-4': '3:4' } }, bind('ratio'))),
					el(Select, Object.assign({ label: __('Text align', 'bigspy-ai-theme-wp'), options: { left: __('Left', 'bigspy-ai-theme-wp'), center: __('Center', 'bigspy-ai-theme-wp') } }, bind('align'))),
					el(Select, Object.assign({ label: __('Title tag', 'bigspy-ai-theme-wp'), options: { h2: 'H2', h3: 'H3', h4: 'H4', p: __('Paragraph', 'bigspy-ai-theme-wp') } }, bind('titleTag'))),
					el(Range, Object.assign({ label: __('Corner radius (px)', 'bigspy-ai-theme-wp'), max: 60 }, bind('radius'))),
					el(Select, Object.assign({ label: __('Hover effect', 'bigspy-ai-theme-wp'), options: { none: __('None', 'bigspy-ai-theme-wp'), zoom: __('Zoom image', 'bigspy-ai-theme-wp'), lift: __('Lift card', 'bigspy-ai-theme-wp') } }, bind('hover'))),
					el(Toggle, Object.assign({ label: __('Card style (background + shadow)', 'bigspy-ai-theme-wp') }, bind('card', true)))
				),
				el(Panel, { title: __('Link', 'bigspy-ai-theme-wp') },
					el(Text, Object.assign({ label: __('Link (URL)', 'bigspy-ai-theme-wp') }, bind('link'))),
					el(Text, Object.assign({ label: __('"Read more" text (optional)', 'bigspy-ai-theme-wp') }, bind('linkText'))),
					a.link ? el(Toggle, Object.assign({ label: __('Open in new tab', 'bigspy-ai-theme-wp') }, bind('newTab', true))) : null
				)
			),
			el('div', blockProps,
				a.imageUrl ? el('div', { className: 'bs-image-box__media' + (a.ratio !== 'auto' ? ' bs-ratio bs-ratio--' + a.ratio : '') }, el('img', { className: 'bs-image-box__img', src: a.imageUrl, alt: '' }))
					: el(be.MediaPlaceholder, { icon: 'format-image', labels: { title: __('Image', 'bigspy-ai-theme-wp') }, accept: 'image/*', allowedTypes: ['image'], onSelect: setImage }),
				el('div', { className: 'bs-image-box__body' },
					el(RichText, { tagName: a.titleTag === 'p' ? 'p' : a.titleTag, className: 'bs-image-box__title', value: a.title, placeholder: __('Title', 'bigspy-ai-theme-wp'), onChange: function (v) { props.setAttributes({ title: v }); } }),
					el(RichText, { tagName: 'div', className: 'bs-image-box__text', value: a.text, placeholder: __('Short description…', 'bigspy-ai-theme-wp'), onChange: function (v) { props.setAttributes({ text: v }); } }),
					a.link && a.linkText ? el('span', { className: 'bs-image-box__more' }, a.linkText, el(B.Icon, { name: 'arrow-right', size: 16 })) : null
				)
			)
		);
	});

	/* ================================================================ Message box */
	register('alert', function (props) {
		var a = props.attributes;
		var bind = B.binder(props);
		var ICONS = { info: 'lightbulb', success: 'check', warning: 'shield', error: 'close', custom: 'message' };
		var blockProps = useBlockProps({
			className: 'bs-alert bs-alert--' + a.type + ' bs-alert--' + a.style,
			style: styleVars({ '--bs-alert-bg': a.bgColor, '--bs-alert-color': a.textColor, '--bs-alert-accent': a.accent })
		});
		return el(Fragment, null,
			el(Inspector, null,
				el(Panel, { title: __('Message box', 'bigspy-ai-theme-wp') },
					el(Select, Object.assign({ label: __('Type', 'bigspy-ai-theme-wp'), options: { info: __('Info', 'bigspy-ai-theme-wp'), success: __('Success', 'bigspy-ai-theme-wp'), warning: __('Warning', 'bigspy-ai-theme-wp'), error: __('Error', 'bigspy-ai-theme-wp'), custom: __('Custom colors', 'bigspy-ai-theme-wp') } }, bind('type'))),
					el(Select, Object.assign({ label: __('Style', 'bigspy-ai-theme-wp'), options: { soft: __('Soft background', 'bigspy-ai-theme-wp'), solid: __('Solid', 'bigspy-ai-theme-wp'), outline: __('Outline', 'bigspy-ai-theme-wp'), 'left-bar': __('Left bar', 'bigspy-ai-theme-wp') } }, bind('style'))),
					el(Toggle, Object.assign({ label: __('Show icon', 'bigspy-ai-theme-wp') }, bind('showIcon', true))),
					el(Toggle, Object.assign({ label: __('Visitors can close it', 'bigspy-ai-theme-wp') }, bind('dismiss', true)))
				),
				a.type === 'custom' ? el(B.ColorPanel, { title: __('Colors', 'bigspy-ai-theme-wp'), settings: [
					{ label: __('Background', 'bigspy-ai-theme-wp'), value: a.bgColor, onChange: function (v) { props.setAttributes({ bgColor: v || '' }); } },
					{ label: __('Text', 'bigspy-ai-theme-wp'), value: a.textColor, onChange: function (v) { props.setAttributes({ textColor: v || '' }); } },
					{ label: __('Accent (icon, border)', 'bigspy-ai-theme-wp'), value: a.accent, onChange: function (v) { props.setAttributes({ accent: v || '' }); } }
				] }) : null
			),
			el('div', blockProps,
				a.showIcon ? el('span', { className: 'bs-alert__icon' }, el(B.Icon, { name: ICONS[a.type] || 'message', size: 22 })) : null,
				el('div', { className: 'bs-alert__body' },
					el(RichText, { tagName: 'p', className: 'bs-alert__title', value: a.title, placeholder: __('Title (optional)', 'bigspy-ai-theme-wp'), onChange: function (v) { props.setAttributes({ title: v }); } }),
					el(RichText, { tagName: 'div', className: 'bs-alert__text', value: a.text, placeholder: __('Message…', 'bigspy-ai-theme-wp'), onChange: function (v) { props.setAttributes({ text: v }); } })
				),
				a.dismiss ? el('span', { className: 'bs-alert__close' }, el(B.Icon, { name: 'close', size: 16 })) : null
			)
		);
	});

	/* ================================================================ Progress bars */
	register('progress', function (props) {
		var a = props.attributes;
		var bind = B.binder(props);
		var items = a.items || [];
		var setItem = function (i, key, v) {
			var list = items.slice();
			list[i] = Object.assign({}, list[i]);
			list[i][key] = v;
			props.setAttributes({ items: list });
		};
		var blockProps = useBlockProps({
			className: 'bs-progress is-visible' + (a.striped ? ' is-striped' : '') + (a.labelInside ? ' has-label-inside' : ''),
			style: styleVars({ '--bs-pg-h': a.height + 'px', '--bs-pg-radius': a.radius + 'px', '--bs-pg-color': a.color, '--bs-pg-track': a.track })
		});
		return el(Fragment, null,
			el(Inspector, null,
				el(Panel, { title: __('Bars', 'bigspy-ai-theme-wp') },
					items.map(function (it, i) {
						return el('div', { key: i, className: 'bst-progress-row' },
							el(Range, { label: (it.label || __('Bar', 'bigspy-ai-theme-wp') + ' ' + (i + 1)) + ' (%)', min: 0, max: 100, value: it.value, onChange: function (v) { setItem(i, 'value', v); } }),
							el(c.ColorPalette, { value: it.color || undefined, onChange: function (v) { setItem(i, 'color', v || ''); }, clearable: true, disableCustomColors: false }),
							el(c.Button, { variant: 'link', isDestructive: true, onClick: function () { props.setAttributes({ items: items.filter(function (x, j) { return j !== i; }) }); } }, __('Remove', 'bigspy-ai-theme-wp'))
						);
					}),
					el(c.Button, { variant: 'secondary', icon: 'plus', onClick: function () { props.setAttributes({ items: items.concat([{ label: __('New bar', 'bigspy-ai-theme-wp'), value: 80, color: '' }]) }); } }, __('Add bar', 'bigspy-ai-theme-wp'))
				),
				el(Panel, { title: __('Style', 'bigspy-ai-theme-wp') },
					el(Range, Object.assign({ label: __('Bar height (px)', 'bigspy-ai-theme-wp'), min: 2, max: 60 }, bind('height'))),
					el(Range, Object.assign({ label: __('Corner radius (px)', 'bigspy-ai-theme-wp'), max: 60 }, { value: Math.min(a.radius, 60), onChange: function (v) { props.setAttributes({ radius: v }); } })),
					el(Toggle, Object.assign({ label: __('Show percentage', 'bigspy-ai-theme-wp') }, bind('showValue', true))),
					el(Toggle, Object.assign({ label: __('Label inside the bar', 'bigspy-ai-theme-wp') }, bind('labelInside', true))),
					el(Toggle, Object.assign({ label: __('Striped', 'bigspy-ai-theme-wp') }, bind('striped', true))),
					el(Toggle, Object.assign({ label: __('Animate when scrolled into view', 'bigspy-ai-theme-wp') }, bind('animate', true)))
				),
				el(B.ColorPanel, { title: __('Colors', 'bigspy-ai-theme-wp'), settings: [
					{ label: __('Bar', 'bigspy-ai-theme-wp'), value: a.color, onChange: function (v) { props.setAttributes({ color: v || '' }); } },
					{ label: __('Track', 'bigspy-ai-theme-wp'), value: a.track, onChange: function (v) { props.setAttributes({ track: v || '' }); } }
				] })
			),
			el('div', blockProps,
				items.map(function (it, i) {
					return el('div', { key: i, className: 'bs-progress__item' },
						el('div', { className: 'bs-progress__head' },
							el(RichText, { tagName: 'span', className: 'bs-progress__label', value: it.label, allowedFormats: [], placeholder: __('Label', 'bigspy-ai-theme-wp'), onChange: function (v) { setItem(i, 'label', v); } }),
							a.showValue ? el('span', { className: 'bs-progress__value' }, it.value + '%') : null
						),
						el('div', { className: 'bs-progress__track' }, el('div', { className: 'bs-progress__fill', style: styleVars({ '--v': it.value + '%', '--bs-pg-color': it.color }) }))
					);
				})
			)
		);
	});

	/* ================================================================ Star rating */
	register('star-rating', function (props) {
		var a = props.attributes;
		var bind = B.binder(props);
		var hover = wp.element.useState(null);
		var shown = hover[0] !== null ? hover[0] : a.rating;
		var blockProps = useBlockProps({
			className: 'bs-stars bs-stars--align-' + a.align,
			style: styleVars({ '--bs-star-size': a.size + 'px', '--bs-star-color': a.color, '--bs-star-empty': a.emptyColor, '--bs-star-fill': (shown / a.max * 100) + '%' })
		});
		var star = function (i) {
			return el('svg', { key: i, viewBox: '0 0 24 24', 'aria-hidden': true }, el('path', { d: 'M12 2.5l2.9 6.1 6.6.8-4.9 4.6 1.3 6.6L12 17.3l-5.9 3.3 1.3-6.6L2.5 9.4l6.6-.8z' }));
		};
		var stars = []; for (var i = 0; i < a.max; i++) { stars.push(star(i)); }
		var pick = function (e) {
			var r = e.currentTarget.getBoundingClientRect();
			var v = Math.round(((e.clientX - r.left) / r.width) * a.max * 2) / 2;
			return Math.max(0.5, Math.min(a.max, v));
		};
		return el(Fragment, null,
			el(Inspector, null,
				el(Panel, { title: __('Rating', 'bigspy-ai-theme-wp') },
					el(Range, Object.assign({ label: __('Rating', 'bigspy-ai-theme-wp'), min: 0, max: a.max, step: 0.1 }, bind('rating'))),
					el(Range, Object.assign({ label: __('Number of stars', 'bigspy-ai-theme-wp'), min: 1, max: 10 }, bind('max'))),
					el(Range, Object.assign({ label: __('Size (px)', 'bigspy-ai-theme-wp'), min: 10, max: 80 }, bind('size'))),
					el(Select, Object.assign({ label: __('Align', 'bigspy-ai-theme-wp'), options: ALIGN3 }, bind('align'))),
					el(Text, Object.assign({ label: __('Label (e.g. 4.9/5 from 320 reviews)', 'bigspy-ai-theme-wp') }, bind('label'))),
					el(Toggle, Object.assign({ label: __('Show the number', 'bigspy-ai-theme-wp') }, bind('showNumber', true))),
					el('p', { className: 'description' }, __('Tip: click the stars on the canvas to set the rating (half stars supported).', 'bigspy-ai-theme-wp'))
				),
				el(B.ColorPanel, { title: __('Colors', 'bigspy-ai-theme-wp'), settings: [
					{ label: __('Stars', 'bigspy-ai-theme-wp'), value: a.color, onChange: function (v) { props.setAttributes({ color: v || '' }); } },
					{ label: __('Empty stars', 'bigspy-ai-theme-wp'), value: a.emptyColor, onChange: function (v) { props.setAttributes({ emptyColor: v || '' }); } }
				] })
			),
			el('div', blockProps,
				el('span', {
					className: 'bs-stars__icons bst-stars-pick',
					onMouseMove: function (e) { hover[1](pick(e)); },
					onMouseLeave: function () { hover[1](null); },
					onClick: function (e) { props.setAttributes({ rating: pick(e) }); }
				}, el('span', { className: 'bs-stars__empty' }, stars), el('span', { className: 'bs-stars__full' }, stars)),
				a.showNumber ? el('span', { className: 'bs-stars__number' }, shown) : null,
				el(RichText, { tagName: 'span', className: 'bs-stars__label', value: a.label, allowedFormats: [], placeholder: __('Label (optional)', 'bigspy-ai-theme-wp'), onChange: function (v) { props.setAttributes({ label: v }); } })
			)
		);
	});
	/* ================================================================ Form (Contact Form 7 / WPForms) */
	var FORM_RESET = { accent: '', accent2: '', fieldBg: '', borderColor: '', labelColor: '', textColor: '', boxBg: '', radius: -1, borderWidth: -1, gap: -1, buttonBg: '', buttonText: '', buttonHoverBg: '', buttonHoverText: '', boxBorderColor: '', boxBorderWidth: -1, boxShadow: -1, focusColor: '' };
	var FORM_SHADOWS = { 1: '0 1px 3px -2px rgba(0,0,0,.12),0 1px 1px rgba(0,0,0,.08)', 2: '0 3px 6px -4px rgba(0,0,0,.16),0 3px 6px rgba(0,0,0,.1)', 3: '0 10px 20px rgba(0,0,0,.12),0 6px 6px rgba(0,0,0,.1)', 4: '0 14px 28px rgba(0,0,0,.18),0 10px 10px rgba(0,0,0,.12)', 5: '0 19px 38px rgba(0,0,0,.22),0 15px 12px rgba(0,0,0,.14)' };
	var FORM_EFFECTS = [['none', __('None', 'bigspy-ai-theme-wp')], ['lift', __('Lift', 'bigspy-ai-theme-wp')], ['grow', __('Grow', 'bigspy-ai-theme-wp')], ['shine', __('Shine', 'bigspy-ai-theme-wp')], ['pulse', __('Pulse', 'bigspy-ai-theme-wp')], ['fill', __('Fill', 'bigspy-ai-theme-wp')], ['bounce', __('Bounce', 'bigspy-ai-theme-wp')]];

	/* Same CSS variables as blocks/form/render.php, for the on-canvas sample. */
	function formVars(a) {
		var n = function (v, unit) { return v !== undefined && v >= 0 ? v + unit : ''; };
		return styleVars({
			'--bs-form-accent': a.accent, '--bs-form-accent2': a.accent2, '--bs-form-field': a.fieldBg, '--bs-form-border': a.borderColor, '--bs-form-label': a.labelColor, '--bs-form-text': a.textColor, '--bs-form-boxbg': a.boxBg,
			'--bs-form-radius': n(a.radius, 'px'), '--bs-form-gap': n(a.gap, 'px'), '--bs-form-bw': n(a.borderWidth, 'px'), '--bs-form-max': a.maxWidth ? a.maxWidth + 'px' : '',
			'--bs-form-btn': a.buttonBg, '--bs-form-btn-text': a.buttonText, '--bs-form-btn-hbg': a.buttonHoverBg, '--bs-form-btn-htext': a.buttonHoverText, '--bs-form-focus': a.focusColor,
			'--bs-form-box-bc': a.boxBorderColor, '--bs-form-box-bw': n(a.boxBorderWidth, 'px'), '--bs-form-box-pad': n(a.boxPadding, 'px'),
			'--bs-form-box-shadow': a.boxShadow === 0 ? 'none' : (FORM_SHADOWS[a.boxShadow] || '')
		});
	}
	var FORM_TEMPLATES = [
		['modern', __('Modern card', 'bigspy-ai-theme-wp'), { boxStyle: 'card', formStyle: 'default', layout: '2col', labelStyle: 'small', fieldSize: 'md', buttonStyle: 'gradient', buttonAlign: 'full', radius: 10 }],
		['pastel', __('Soft pastel', 'bigspy-ai-theme-wp'), { boxStyle: 'soft', formStyle: 'rounded', layout: 'stack', labelStyle: 'hidden', fieldSize: 'lg', buttonStyle: 'pill', buttonAlign: 'full' }],
		['dark', __('Dark glow', 'bigspy-ai-theme-wp'), { boxStyle: 'dark', formStyle: 'filled', layout: '2col', labelStyle: 'small', fieldSize: 'md', buttonStyle: 'glow', buttonAlign: 'full', radius: 10 }],
		['glass', __('Glass on image', 'bigspy-ai-theme-wp'), { boxStyle: 'glass', formStyle: 'filled', layout: 'stack', labelStyle: 'hidden', fieldSize: 'lg', buttonStyle: 'pill', buttonAlign: 'full', imagePosition: 'background' }],
		['minimal', __('Minimal lines', 'bigspy-ai-theme-wp'), { boxStyle: 'none', formStyle: 'underline', layout: '2col', labelStyle: 'small', fieldSize: 'md', buttonStyle: 'outline', buttonAlign: 'left' }],
		['newsletter', __('Inline newsletter', 'bigspy-ai-theme-wp'), { boxStyle: 'gradient', formStyle: 'rounded', layout: 'inline', labelStyle: 'hidden', fieldSize: 'lg', buttonStyle: 'pill', buttonAlign: 'left', align: 'center' }],
		['split', __('Image + form', 'bigspy-ai-theme-wp'), { boxStyle: 'card', formStyle: 'shadow', layout: 'stack', labelStyle: 'small', fieldSize: 'md', buttonStyle: 'shade', buttonAlign: 'full', imagePosition: 'left', radius: 12 }],
		['border', __('Gradient border', 'bigspy-ai-theme-wp'), { boxStyle: 'gradient-border', formStyle: 'shadow', layout: 'stack', labelStyle: 'top', fieldSize: 'md', buttonStyle: 'gradient', buttonAlign: 'center', radius: 12 }]
	];

	function formClasses(a) {
		return ['bs-form', 'bs-form--fx-' + (a.buttonEffect || 'lift'), a.buttonIcon ? 'has-btn-arrow' : '', (a.buttonBg || a.buttonText) ? 'has-btn-colors' : '', a.imageUrl ? 'has-image bs-form--image-' + (a.imagePosition || 'left') : '', 'bs-form--' + (a.formStyle || 'default'), 'bs-form--box-' + (a.boxStyle || 'none'), 'bs-form--layout-' + (a.layout || 'stack'), 'bs-form--labels-' + (a.labelStyle || 'top'), 'bs-form--size-' + (a.fieldSize || 'md'), 'bs-form--btn-' + (a.buttonStyle || 'solid'), 'bs-form--btn-align-' + (a.buttonAlign || 'left'), 'bs-form--align-' + (a.align || 'left')].join(' ');
	}

	/* Mini preview of a template for the picker (fake fields, same CSS classes). */
	function FormThumb(props) {
		var t = props.template;
		var a = Object.assign({ formStyle: 'default', boxStyle: 'none', layout: 'stack', labelStyle: 'top', fieldSize: 'md', buttonStyle: 'solid', buttonAlign: 'left', align: 'left' }, t[2]);
		var field = function (k, ph) {
			return el('p', { key: k }, el('label', null, a.labelStyle === 'hidden' ? null : ph, el('input', { type: 'text', placeholder: ph, readOnly: true, tabIndex: -1 })));
		};
		return el('div', { className: formClasses(a) + (a.imagePosition ? ' has-image bs-form--image-' + a.imagePosition : '') + ' bst-form-thumb', 'aria-hidden': true },
			a.imagePosition ? el('div', { className: 'bs-form__image bst-form-thumb__img' }) : null,
			el('div', { className: 'bs-form__inner' },
				el('div', { className: 'bs-form__body' },
					el('div', { className: 'wpcf7' }, el('form', { className: 'wpcf7-form', onSubmit: function (e) { e.preventDefault(); } },
						a.layout === 'inline' ? null : field('n', 'Name'),
						field('e', 'Email'),
						el('p', null, el('input', { type: 'submit', value: a.layout === 'inline' ? 'OK' : 'Send', tabIndex: -1, readOnly: true }))
					))
				)
			)
		);
	}

	function FormSample(props) {
		var a = props.attributes;
		var field = function (k, label, type) {
			return el('p', { key: k }, el('label', null, label, el('br'), el('span', { className: 'wpcf7-form-control-wrap' }, type === 'textarea' ? el('textarea', { placeholder: label, readOnly: true, rows: 3 }) : el('input', { type: type, placeholder: label, readOnly: true }))));
		};
		var inline = a.layout === 'inline';
		return el('div', useBlockProps({ className: formClasses(a) + ' bst-form-sample', style: formVars(a) }),
			a.imageUrl ? el('div', { className: 'bs-form__image', style: { backgroundImage: 'url(' + a.imageUrl + ')' } }) : null,
			el('div', { className: 'bs-form__inner' },
				a.kicker ? el('p', { className: 'bs-form__kicker' }, a.kicker) : null,
				el(RichText, { tagName: 'h3', className: 'bs-form__title', value: a.title, placeholder: __('Form title', 'bigspy-ai-theme-wp'), onChange: function (v) { props.setAttributes({ title: v }); } }),
				el(RichText, { tagName: 'p', className: 'bs-form__desc', value: a.description, placeholder: __('Short description (optional)', 'bigspy-ai-theme-wp'), onChange: function (v) { props.setAttributes({ description: v }); } }),
				el('div', { className: 'bs-form__body' }, el('div', { className: 'wpcf7' }, el('form', { className: 'wpcf7-form', onSubmit: function (e) { e.preventDefault(); } },
					inline ? null : field('n', __('Your name', 'bigspy-ai-theme-wp'), 'text'),
					inline ? null : field('p', __('Phone', 'bigspy-ai-theme-wp'), 'tel'),
					field('e', __('Email', 'bigspy-ai-theme-wp'), 'email'),
					inline ? null : field('m', __('Message', 'bigspy-ai-theme-wp'), 'textarea'),
					el('p', null, el('input', { type: 'submit', value: inline ? __('Subscribe', 'bigspy-ai-theme-wp') : __('Send', 'bigspy-ai-theme-wp'), readOnly: true }))
				))),
				a.note ? el('p', { className: 'bs-form__note' }, el('span', null, a.note)) : null,
				el('p', { className: 'bst-form-sample__hint' }, props.hint)
			)
		);
	}

	/* Toolbar dropdown: pick colors right on the block. */
	function FormColorsDropdown(props) {
		var a = props.attributes;
		var colors = wp.data.useSelect(function (select) { return select(be.store).getSettings().colors || []; }, []);
		var row = function (label, key) {
			return el('div', { key: key, className: 'bst-form-colors__row' },
				el('span', { className: 'bst-form-colors__label' }, el('i', { style: { background: a[key] || 'transparent' } }), label),
				el(c.ColorPalette, { colors: colors, value: a[key] || undefined, enableAlpha: true, onChange: function (v) { var o = {}; o[key] = v || ''; props.setAttributes(o); } })
			);
		};
		return el(c.Dropdown, {
			popoverProps: { placement: 'bottom-start' },
			renderToggle: function (o) { return el(c.ToolbarButton, { icon: 'art', label: __('Form colors', 'bigspy-ai-theme-wp'), onClick: o.onToggle, isPressed: o.isOpen }, __('Colors', 'bigspy-ai-theme-wp')); },
			renderContent: function () {
				return el('div', { className: 'bst-form-colors' },
					row(__('Button', 'bigspy-ai-theme-wp'), 'buttonBg'),
					row(__('Button text', 'bigspy-ai-theme-wp'), 'buttonText'),
					row(__('Button on hover', 'bigspy-ai-theme-wp'), 'buttonHoverBg'),
					row(__('Accent / focus', 'bigspy-ai-theme-wp'), 'accent'),
					row(__('Box border', 'bigspy-ai-theme-wp'), 'boxBorderColor'),
					row(__('Box background', 'bigspy-ai-theme-wp'), 'boxBg'),
					row(__('Field background', 'bigspy-ai-theme-wp'), 'fieldBg'),
					row(__('Field border', 'bigspy-ai-theme-wp'), 'borderColor'),
					row(__('Text', 'bigspy-ai-theme-wp'), 'textColor')
				);
			}
		});
	}

	register('form', function (props) {
		var a = props.attributes;
		var bind = B.binder(props);
		var forms = B.forms || [];
		var providers = B.formProviders || {};
		var options = [{ value: '', label: forms.length ? __('— Choose a form —', 'bigspy-ai-theme-wp') : __('No forms yet', 'bigspy-ai-theme-wp') }].concat(forms.map(function (f) {
			return { value: f[0] + ':' + f[1], label: f[2] + (Object.keys(providers).length > 1 ? ' (' + providers[f[0]] + ')' : '') };
		}));
		var pick = el(c.SelectControl, {
			label: __('Form', 'bigspy-ai-theme-wp'),
			value: a.formId ? a.provider + ':' + a.formId : '',
			options: options,
			onChange: function (v) { var p = v.split(':'); props.setAttributes({ provider: p[0] || 'cf7', formId: parseInt(p[1], 10) || 0 }); },
			__nextHasNoMarginBottom: true
		});
		var noPlugin = !Object.keys(providers).length;
		var applyTemplate = function (t) {
			var next = Object.assign({}, FORM_RESET, { imagePosition: a.imagePosition || 'left', align: 'left' }, t[2]);
			if (!t[2].imagePosition && a.imagePosition === 'background') {
				next.imagePosition = 'left';
			}
			props.setAttributes(next);
		};
		var choice = function (label, key, list) {
			return el('div', { className: 'bst-choice' },
				el('span', { className: 'bst-choice__label' }, label),
				el('div', { className: 'bst-choice__row' }, list.map(function (o) {
					return el('button', { key: o[0], type: 'button', className: 'bst-choice__item' + ((a[key] || list[0][0]) === o[0] ? ' is-selected' : ''), onClick: function () { var x = {}; x[key] = o[0]; props.setAttributes(x); } }, o[1]);
				}))
			);
		};
		var blockPreview = a.formId ? dynamicPreview('form', props) : el(FormSample, Object.assign({}, props, {
			hint: noPlugin ? __('Preview with sample fields. Install Contact Form 7 to collect real messages.', 'bigspy-ai-theme-wp') : __('Preview with sample fields — choose your form in the toolbar or sidebar.', 'bigspy-ai-theme-wp')
		}));
		var toolbar = el(be.BlockControls, null,
			el(c.ToolbarGroup, null,
				el(c.Dropdown, {
					popoverProps: { placement: 'bottom-start' },
					renderToggle: function (o) { return el(c.ToolbarButton, { icon: 'layout', label: __('Form look', 'bigspy-ai-theme-wp'), onClick: o.onToggle, isPressed: o.isOpen }, __('Look', 'bigspy-ai-theme-wp')); },
					renderContent: function (o) {
						return el('div', { className: 'bst-form-templates bst-form-templates--popover' }, FORM_TEMPLATES.map(function (t) {
							return el('button', { key: t[0], type: 'button', className: 'bst-form-templates__item', onClick: function () { applyTemplate(t); o.onClose(); } }, el(FormThumb, { template: t }), el('em', null, t[1]));
						}));
					}
				}),
				el(FormColorsDropdown, props),
				el(c.ToolbarDropdownMenu, {
					icon: 'controls-play',
					label: __('Button hover effect', 'bigspy-ai-theme-wp'),
					controls: FORM_EFFECTS.map(function (fx) { return { title: fx[1], isActive: (a.buttonEffect || 'lift') === fx[0], onClick: function () { props.setAttributes({ buttonEffect: fx[0] }); } }; })
				})
			),
			!noPlugin ? el(c.ToolbarGroup, null, el(c.Dropdown, {
				popoverProps: { placement: 'bottom-start' },
				renderToggle: function (o) { return el(c.ToolbarButton, { icon: 'feedback', label: __('Choose form', 'bigspy-ai-theme-wp'), onClick: o.onToggle, isPressed: o.isOpen }, a.formId ? __('Form', 'bigspy-ai-theme-wp') + ' ✓' : __('Choose form', 'bigspy-ai-theme-wp')); },
				renderContent: function () { return el('div', { style: { padding: 12, minWidth: 260 } }, pick, el(c.ExternalLink, { href: B.formNewUrl }, __('Create a new form', 'bigspy-ai-theme-wp'))); }
			})) : null
		);

		return el(Fragment, null,
			toolbar,
			el(Inspector, null,
				el(Panel, { title: __('Form', 'bigspy-ai-theme-wp') },
					noPlugin ? el('p', null, __('Install and activate Contact Form 7 (or WPForms) to use this block.', 'bigspy-ai-theme-wp')) : pick,
					el(c.ExternalLink, { href: B.formNewUrl }, noPlugin ? __('Install Contact Form 7', 'bigspy-ai-theme-wp') : __('Create a new form', 'bigspy-ai-theme-wp'))
				),
				el(Panel, { title: __('Ready-made looks', 'bigspy-ai-theme-wp') },
					el('p', { className: 'description' }, __('Click a look to apply it, then fine-tune below.', 'bigspy-ai-theme-wp')),
					el('div', { className: 'bst-form-templates' }, FORM_TEMPLATES.map(function (t) {
						return el('button', { key: t[0], type: 'button', className: 'bst-form-templates__item', onClick: function () { applyTemplate(t); } }, el(FormThumb, { template: t }), el('em', null, t[1]));
					}))
				),
				el(Panel, { title: __('Heading & text', 'bigspy-ai-theme-wp'), initialOpen: false },
					el(Text, Object.assign({ label: __('Small label above the title', 'bigspy-ai-theme-wp') }, bind('kicker'))),
					el(Text, Object.assign({ label: __('Title', 'bigspy-ai-theme-wp') }, bind('title'))),
					el(c.TextareaControl, { label: __('Description', 'bigspy-ai-theme-wp'), value: a.description, onChange: function (v) { props.setAttributes({ description: v }); }, __nextHasNoMarginBottom: true }),
					el(Text, Object.assign({ label: __('Note under the form (e.g. "We never share your phone number")', 'bigspy-ai-theme-wp') }, bind('note'))),
					el(Select, Object.assign({ label: __('Heading align', 'bigspy-ai-theme-wp'), options: { left: __('Left', 'bigspy-ai-theme-wp'), center: __('Center', 'bigspy-ai-theme-wp') } }, bind('align')))
				),
				el(Panel, { title: __('Layout & fields', 'bigspy-ai-theme-wp'), initialOpen: false },
					choice(__('Layout', 'bigspy-ai-theme-wp'), 'layout', [['stack', __('Stacked', 'bigspy-ai-theme-wp')], ['2col', __('2 columns', 'bigspy-ai-theme-wp')], ['inline', __('One line', 'bigspy-ai-theme-wp')]]),
					choice(__('Field style', 'bigspy-ai-theme-wp'), 'formStyle', [['default', __('Theme', 'bigspy-ai-theme-wp')], ['outline', __('Outline', 'bigspy-ai-theme-wp')], ['underline', __('Line', 'bigspy-ai-theme-wp')], ['filled', __('Filled', 'bigspy-ai-theme-wp')], ['rounded', __('Pill', 'bigspy-ai-theme-wp')], ['shadow', __('Shadow', 'bigspy-ai-theme-wp')]]),
					choice(__('Field size', 'bigspy-ai-theme-wp'), 'fieldSize', [['sm', 'S'], ['md', 'M'], ['lg', 'L']]),
					choice(__('Labels', 'bigspy-ai-theme-wp'), 'labelStyle', [['top', __('Above', 'bigspy-ai-theme-wp')], ['small', __('Small caps', 'bigspy-ai-theme-wp')], ['hidden', __('Hidden', 'bigspy-ai-theme-wp')]]),
					el(Range, Object.assign({ label: __('Space between fields (px, -1 = default)', 'bigspy-ai-theme-wp'), min: -1, max: 40 }, bind('gap'))),
					el(Range, Object.assign({ label: __('Corner radius (px, -1 = style default)', 'bigspy-ai-theme-wp'), min: -1, max: 40 }, bind('radius'))),
					el(Range, Object.assign({ label: __('Border width (px, -1 = default)', 'bigspy-ai-theme-wp'), min: -1, max: 4 }, bind('borderWidth'))),
					el(Range, Object.assign({ label: __('Max width (px, 0 = full)', 'bigspy-ai-theme-wp'), max: 1400, step: 10 }, bind('maxWidth')))
				),
				el(Panel, { title: __('Submit button', 'bigspy-ai-theme-wp'), initialOpen: false },
					choice(__('Style', 'bigspy-ai-theme-wp'), 'buttonStyle', [['solid', __('Solid', 'bigspy-ai-theme-wp')], ['gradient', __('Gradient', 'bigspy-ai-theme-wp')], ['pill', __('Pill', 'bigspy-ai-theme-wp')], ['outline', __('Outline', 'bigspy-ai-theme-wp')], ['shade', '3D'], ['glow', __('Glow', 'bigspy-ai-theme-wp')]]),
					choice(__('Position', 'bigspy-ai-theme-wp'), 'buttonAlign', [['left', __('Left', 'bigspy-ai-theme-wp')], ['center', __('Center', 'bigspy-ai-theme-wp')], ['right', __('Right', 'bigspy-ai-theme-wp')], ['full', __('Full', 'bigspy-ai-theme-wp')]]),
					choice(__('Hover effect', 'bigspy-ai-theme-wp'), 'buttonEffect', FORM_EFFECTS),
					el(Toggle, Object.assign({ label: __('Arrow on the button', 'bigspy-ai-theme-wp') }, bind('buttonIcon', true)))
				),
				el(B.ColorPanel, { title: __('Button colors', 'bigspy-ai-theme-wp'), settings: [
					{ label: __('Button', 'bigspy-ai-theme-wp'), value: a.buttonBg, onChange: function (v) { props.setAttributes({ buttonBg: v || '' }); } },
					{ label: __('Button text', 'bigspy-ai-theme-wp'), value: a.buttonText, onChange: function (v) { props.setAttributes({ buttonText: v || '' }); } },
					{ label: __('Button on hover', 'bigspy-ai-theme-wp'), value: a.buttonHoverBg, onChange: function (v) { props.setAttributes({ buttonHoverBg: v || '' }); } },
					{ label: __('Button text on hover', 'bigspy-ai-theme-wp'), value: a.buttonHoverText, onChange: function (v) { props.setAttributes({ buttonHoverText: v || '' }); } }
				] }),
				el(Panel, { title: __('Box & image', 'bigspy-ai-theme-wp'), initialOpen: false },
					choice(__('Box', 'bigspy-ai-theme-wp'), 'boxStyle', [['none', __('None', 'bigspy-ai-theme-wp')], ['card', __('Card', 'bigspy-ai-theme-wp')], ['soft', __('Soft', 'bigspy-ai-theme-wp')], ['gradient', __('Gradient', 'bigspy-ai-theme-wp')], ['gradient-border', __('Gradient border', 'bigspy-ai-theme-wp')], ['glass', __('Glass', 'bigspy-ai-theme-wp')], ['dark', __('Dark', 'bigspy-ai-theme-wp')]]),
					el(B.MediaPicker, { label: __('Image (side or background)', 'bigspy-ai-theme-wp'), id: a.imageId, url: a.imageUrl, onChange: function (m) { props.setAttributes({ imageId: m.id || 0, imageUrl: m.url || '', imageAlt: m.alt || '' }); } }),
					a.imageUrl ? choice(__('Image position', 'bigspy-ai-theme-wp'), 'imagePosition', [['left', __('Left', 'bigspy-ai-theme-wp')], ['right', __('Right', 'bigspy-ai-theme-wp')], ['background', __('Background', 'bigspy-ai-theme-wp')]]) : null,
					el(Range, Object.assign({ label: __('Box border width (px, -1 = style default)', 'bigspy-ai-theme-wp'), min: -1, max: 8 }, bind('boxBorderWidth'))),
					el(Range, Object.assign({ label: __('Box shadow (-1 = style default, 0 = none)', 'bigspy-ai-theme-wp'), min: -1, max: 5 }, bind('boxShadow'))),
					el(Range, Object.assign({ label: __('Box padding (px, -1 = auto)', 'bigspy-ai-theme-wp'), min: -1, max: 100 }, bind('boxPadding')))
				),
				el(B.ColorPanel, { title: __('Form colors', 'bigspy-ai-theme-wp'), settings: [
					{ label: __('Accent (button, focus)', 'bigspy-ai-theme-wp'), value: a.accent, onChange: function (v) { props.setAttributes({ accent: v || '' }); } },
					{ label: __('Second accent (gradients)', 'bigspy-ai-theme-wp'), value: a.accent2, onChange: function (v) { props.setAttributes({ accent2: v || '' }); } },
					{ label: __('Box background', 'bigspy-ai-theme-wp'), value: a.boxBg, onChange: function (v) { props.setAttributes({ boxBg: v || '' }); } },
					{ label: __('Field background', 'bigspy-ai-theme-wp'), value: a.fieldBg, onChange: function (v) { props.setAttributes({ fieldBg: v || '' }); } },
					{ label: __('Field border', 'bigspy-ai-theme-wp'), value: a.borderColor, onChange: function (v) { props.setAttributes({ borderColor: v || '' }); } },
					{ label: __('Field focus ring', 'bigspy-ai-theme-wp'), value: a.focusColor, onChange: function (v) { props.setAttributes({ focusColor: v || '' }); } },
					{ label: __('Box border', 'bigspy-ai-theme-wp'), value: a.boxBorderColor, onChange: function (v) { props.setAttributes({ boxBorderColor: v || '' }); } },
					{ label: __('Labels', 'bigspy-ai-theme-wp'), value: a.labelColor, onChange: function (v) { props.setAttributes({ labelColor: v || '' }); } },
					{ label: __('Text', 'bigspy-ai-theme-wp'), value: a.textColor, onChange: function (v) { props.setAttributes({ textColor: v || '' }); } }
				] })
			),
			blockPreview
		);
	});
	/* ================================================================ Team member */
	register('team-member', function (props) {
		var a = props.attributes;
		var bind = B.binder(props);
		var setImage = function (m) { props.setAttributes({ imageId: m.id || 0, imageUrl: (m.sizes && m.sizes.large ? m.sizes.large.url : m.url) || '' }); };
		var blockProps = useBlockProps({ className: 'bs-team bs-team--' + a.style + ' bs-team--align-' + a.align, style: styleVars({ '--bs-team-accent': a.accent }) });
		var ratio = a.style === 'round' ? '1-1' : a.ratio;
		var nets = ['facebook', 'zalo', 'instagram', 'tiktok', 'linkedin', 'mail', 'phone'];
		return el(Fragment, null,
			el(be.BlockControls, null, el(c.ToolbarGroup, null, el(be.MediaReplaceFlow, { mediaId: a.imageId, mediaURL: a.imageUrl, allowedTypes: ['image'], accept: 'image/*', onSelect: setImage, name: a.imageUrl ? __('Replace photo', 'bigspy-ai-theme-wp') : __('Add photo', 'bigspy-ai-theme-wp') }))),
			el(Inspector, null,
				el(Panel, { title: __('Team member', 'bigspy-ai-theme-wp') },
					el(B.MediaPicker, { id: a.imageId, url: a.imageUrl, onChange: setImage }),
					sliderChoice(props, __('Style', 'bigspy-ai-theme-wp'), 'style', [['card', __('Card', 'bigspy-ai-theme-wp')], ['overlay', __('Overlay', 'bigspy-ai-theme-wp')], ['round', __('Round photo', 'bigspy-ai-theme-wp')], ['minimal', __('Minimal', 'bigspy-ai-theme-wp')]]),
					a.style !== 'round' ? sliderChoice(props, __('Photo ratio', 'bigspy-ai-theme-wp'), 'ratio', [['1-1', '1:1'], ['3-4', '3:4'], ['4-3', '4:3']]) : null,
					sliderChoice(props, __('Align', 'bigspy-ai-theme-wp'), 'align', [['center', __('Center', 'bigspy-ai-theme-wp')], ['left', __('Left', 'bigspy-ai-theme-wp')]])
				),
				el(Panel, { title: __('Social links', 'bigspy-ai-theme-wp'), initialOpen: false }, nets.map(function (k) { return el(Text, Object.assign({ key: k, label: k.charAt(0).toUpperCase() + k.slice(1) }, bind(k))); })),
				el(B.ColorPanel, { title: __('Accent color', 'bigspy-ai-theme-wp'), settings: [{ label: __('Accent', 'bigspy-ai-theme-wp'), value: a.accent, onChange: function (v) { props.setAttributes({ accent: v || '' }); } }] })
			),
			el('div', blockProps,
				a.imageUrl ? el('div', { className: 'bs-team__media bs-ratio bs-ratio--' + ratio }, el('img', { className: 'bs-team__img', src: a.imageUrl, alt: '' }))
					: el(be.MediaPlaceholder, { icon: 'businessperson', labels: { title: __('Photo', 'bigspy-ai-theme-wp') }, accept: 'image/*', allowedTypes: ['image'], onSelect: setImage }),
				el('div', { className: 'bs-team__body' },
					el(RichText, { tagName: 'h3', className: 'bs-team__name', value: a.name, allowedFormats: [], placeholder: __('Name', 'bigspy-ai-theme-wp'), onChange: function (v) { props.setAttributes({ name: v }); } }),
					el(RichText, { tagName: 'p', className: 'bs-team__role', value: a.role, allowedFormats: [], placeholder: __('Role', 'bigspy-ai-theme-wp'), onChange: function (v) { props.setAttributes({ role: v }); } }),
					el(RichText, { tagName: 'p', className: 'bs-team__bio', value: a.bio, allowedFormats: [], placeholder: __('Short bio (optional)', 'bigspy-ai-theme-wp'), onChange: function (v) { props.setAttributes({ bio: v }); } }),
					el('div', { className: 'bs-team__social' }, nets.filter(function (k) { return a[k]; }).map(function (k) { return el('span', { key: k }, el(B.Icon, { name: k, size: 16 })); }))
				)
			)
		);
	});

	/* ================================================================ Price table */
	register('price-table', function (props) {
		var a = props.attributes;
		var bind = B.binder(props);
		var lines = String(a.features || '').split(/\n/).map(function (l) { return l.trim(); }).filter(Boolean);
		var blockProps = useBlockProps({ className: 'bs-price bs-price--' + a.style + (a.featured ? ' is-featured' : '') + ' bs-price--align-' + a.align, style: styleVars({ '--bs-price-accent': a.accent }) });
		var rt = function (key, tag, cls, ph) {
			return el(RichText, { tagName: tag, className: cls, value: a[key], allowedFormats: [], placeholder: ph, onChange: function (v) { var o = {}; o[key] = v; props.setAttributes(o); } });
		};
		return el(Fragment, null,
			el(Inspector, null,
				el(Panel, { title: __('Price table', 'bigspy-ai-theme-wp') },
					sliderChoice(props, __('Style', 'bigspy-ai-theme-wp'), 'style', [['card', __('Card', 'bigspy-ai-theme-wp')], ['bordered', __('Bordered', 'bigspy-ai-theme-wp')], ['gradient', __('Gradient', 'bigspy-ai-theme-wp')], ['minimal', __('Minimal', 'bigspy-ai-theme-wp')]]),
					el(Toggle, Object.assign({ label: __('Highlight this plan (popular)', 'bigspy-ai-theme-wp') }, bind('featured', true))),
					a.featured ? el(Text, Object.assign({ label: __('Badge text', 'bigspy-ai-theme-wp') }, bind('badge'))) : null,
					el(Text, Object.assign({ label: __('Old price (crossed out, optional)', 'bigspy-ai-theme-wp') }, bind('oldPrice'))),
					el(c.TextareaControl, { label: __('Features – one per line. Start a line with “-” for a feature NOT included.', 'bigspy-ai-theme-wp'), value: a.features, rows: 6, onChange: function (v) { props.setAttributes({ features: v }); }, __nextHasNoMarginBottom: true }),
					el(Text, Object.assign({ label: __('Button link', 'bigspy-ai-theme-wp') }, bind('buttonUrl'))),
					sliderChoice(props, __('Align', 'bigspy-ai-theme-wp'), 'align', [['center', __('Center', 'bigspy-ai-theme-wp')], ['left', __('Left', 'bigspy-ai-theme-wp')]])
				),
				el(B.ColorPanel, { title: __('Accent color', 'bigspy-ai-theme-wp'), settings: [{ label: __('Accent', 'bigspy-ai-theme-wp'), value: a.accent, onChange: function (v) { props.setAttributes({ accent: v || '' }); } }] })
			),
			el('div', blockProps,
				a.featured && a.badge ? el('span', { className: 'bs-price__badge' }, a.badge) : null,
				rt('title', 'h3', 'bs-price__title', __('Plan name', 'bigspy-ai-theme-wp')),
				rt('description', 'p', 'bs-price__desc', __('Short description (optional)', 'bigspy-ai-theme-wp')),
				el('div', { className: 'bs-price__amount' }, a.oldPrice ? el('del', { className: 'bs-price__old' }, a.oldPrice) : null, rt('price', 'span', 'bs-price__value', '0đ'), rt('period', 'span', 'bs-price__period', '/ month')),
				el('ul', { className: 'bs-price__features' }, lines.map(function (l, i) {
					var off = l.charAt(0) === '-';
					return el('li', { key: i, className: off ? 'is-off' : 'is-on' }, el(B.Icon, { name: off ? 'close' : 'check', size: 16 }), el('span', null, l.replace(/^[-+\s]+/, '')));
				})),
				el('span', { className: 'bs-btn bs-price__btn' + (a.featured ? '' : ' bs-btn--outline') }, rt('buttonText', 'span', '', __('Button', 'bigspy-ai-theme-wp')))
			)
		);
	});

	/* ================================================================ Video popup */
	register('video-button', function (props) {
		var a = props.attributes;
		var bind = B.binder(props);
		var yt = (a.url || '').match(/(?:youtube\.com\/(?:watch\?v=|embed\/|shorts\/)|youtu\.be\/)([A-Za-z0-9_-]{6,})/);
		var img = a.imageUrl || (yt ? 'https://i.ytimg.com/vi/' + yt[1] + '/hqdefault.jpg' : '');
		var blockProps = useBlockProps({ className: 'bs-vbtn bs-vbtn--align-' + a.align, style: styleVars({ '--bs-vbtn-size': a.size + 'px', '--bs-vbtn-color': a.color, '--bs-vbtn-radius': a.radius + 'px' }) });
		return el(Fragment, null,
			el(Inspector, null,
				el(Panel, { title: __('Video popup', 'bigspy-ai-theme-wp') },
					el(Text, Object.assign({ label: __('YouTube, Vimeo or .mp4 URL', 'bigspy-ai-theme-wp') }, bind('url'))),
					el(B.MediaPicker, { label: __('Cover image (optional — YouTube thumbnail is used otherwise)', 'bigspy-ai-theme-wp'), id: a.imageId, url: a.imageUrl, onChange: function (m) { props.setAttributes({ imageId: m.id || 0, imageUrl: m.url || '' }); } }),
					el(Text, Object.assign({ label: __('Text next to the button (optional)', 'bigspy-ai-theme-wp') }, bind('label'))),
					el(Range, Object.assign({ label: __('Button size (px)', 'bigspy-ai-theme-wp'), min: 40, max: 140 }, bind('size'))),
					el(Toggle, Object.assign({ label: __('Pulse animation', 'bigspy-ai-theme-wp') }, bind('pulse', true))),
					img ? sliderChoice(props, __('Image ratio', 'bigspy-ai-theme-wp'), 'ratio', [['16-9', '16:9'], ['4-3', '4:3'], ['1-1', '1:1'], ['21-9', '21:9']]) : null,
					img ? el(Range, Object.assign({ label: __('Corner radius (px)', 'bigspy-ai-theme-wp'), max: 60 }, bind('radius'))) : null,
					sliderChoice(props, __('Align', 'bigspy-ai-theme-wp'), 'align', [['left', __('Left', 'bigspy-ai-theme-wp')], ['center', __('Center', 'bigspy-ai-theme-wp')], ['right', __('Right', 'bigspy-ai-theme-wp')]])
				),
				el(B.ColorPanel, { title: __('Button color', 'bigspy-ai-theme-wp'), settings: [{ label: __('Play button', 'bigspy-ai-theme-wp'), value: a.color, onChange: function (v) { props.setAttributes({ color: v || '' }); } }] })
			),
			el('div', blockProps,
				a.url ? el('span', { className: 'bs-vbtn__trigger' + (img ? ' has-image bs-ratio bs-ratio--' + a.ratio : '') },
					img ? el('img', { src: img, alt: '' }) : null,
					el('span', { className: 'bs-vbtn__inner' }, el('span', { className: 'bs-vbtn__play' + (a.pulse ? ' is-pulse' : '') }, el(B.Icon, { name: 'play', size: 28 })), a.label ? el('span', { className: 'bs-vbtn__label' }, a.label) : null)
				) : el(c.Placeholder, { icon: 'controls-play', label: __('Video popup', 'bigspy-ai-theme-wp'), instructions: __('Paste a YouTube, Vimeo or .mp4 URL in the block settings.', 'bigspy-ai-theme-wp') })
			)
		);
	});

	/* ================================================================ Share buttons */
	register('share', function (props) {
		var a = props.attributes;
		var bind = B.binder(props);
		var all = [['facebook', 'Facebook'], ['zalo', 'Zalo'], ['x', 'X'], ['linkedin', 'LinkedIn'], ['pinterest', 'Pinterest'], ['whatsapp', 'WhatsApp'], ['mail', 'Email'], ['copy', __('Copy link', 'bigspy-ai-theme-wp')]];
		var on = String(a.networks || '').split(',').map(function (s) { return s.trim(); }).filter(Boolean);
		var toggle = function (k, v) {
			var next = all.map(function (n) { return n[0]; }).filter(function (n) { return n === k ? v : on.indexOf(n) !== -1; });
			props.setAttributes({ networks: next.join(',') });
		};
		var blockProps = useBlockProps({ className: 'bs-follow bs-share-block bs-follow--' + a.style + ' bs-follow--' + a.shape + ' bs-follow--align-' + a.align + (a.brand ? ' is-brand' : ''), style: { '--bs-follow-size': a.size + 'px' } });
		return el(Fragment, null,
			el(Inspector, null,
				el(Panel, { title: __('Share buttons', 'bigspy-ai-theme-wp') },
					el(Text, Object.assign({ label: __('Label', 'bigspy-ai-theme-wp') }, bind('label'))),
					all.map(function (n) { return el(Toggle, { key: n[0], label: n[1], checked: on.indexOf(n[0]) !== -1, onChange: function (v) { toggle(n[0], v); } }); })
				),
				el(Panel, { title: __('Style', 'bigspy-ai-theme-wp') },
					sliderChoice(props, __('Style', 'bigspy-ai-theme-wp'), 'style', [['fill', __('Filled', 'bigspy-ai-theme-wp')], ['outline', __('Outline', 'bigspy-ai-theme-wp')], ['plain', __('Icon only', 'bigspy-ai-theme-wp')]]),
					sliderChoice(props, __('Shape', 'bigspy-ai-theme-wp'), 'shape', [['circle', __('Circle', 'bigspy-ai-theme-wp')], ['rounded', __('Rounded', 'bigspy-ai-theme-wp')], ['square', __('Square', 'bigspy-ai-theme-wp')]]),
					el(Range, Object.assign({ label: __('Size (px)', 'bigspy-ai-theme-wp'), min: 24, max: 80 }, bind('size'))),
					sliderChoice(props, __('Align', 'bigspy-ai-theme-wp'), 'align', [['left', __('Left', 'bigspy-ai-theme-wp')], ['center', __('Center', 'bigspy-ai-theme-wp')], ['right', __('Right', 'bigspy-ai-theme-wp')]]),
					el(Toggle, Object.assign({ label: __('Brand colors', 'bigspy-ai-theme-wp') }, bind('brand', true)))
				)
			),
			el('div', blockProps,
				a.label ? el('span', { className: 'bs-follow__label' }, a.label) : null,
				el('div', { className: 'bs-follow__icons' }, on.map(function (k) { return el('span', { key: k, className: 'bs-follow__link bs-follow__link--' + k }, el(B.Icon, { name: k === 'copy' ? 'check' : k, size: Math.round(a.size * 0.45) })); }))
			)
		);
	});
})(window.wp);
