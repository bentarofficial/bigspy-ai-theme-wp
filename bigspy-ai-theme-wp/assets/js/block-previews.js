/**
 * Inserter previews for every BIGSPY block, drawn with inline HTML/CSS (no images).
 * Each block gets example.attributes.bstPreview; when the editor renders that example,
 * edit() shows the drawing below instead of the real block. Loaded before the block scripts.
 */
(function (wp) {
	'use strict';
	var el = wp.element.createElement;
	var __ = wp.i18n.__;
	var sprintf = wp.i18n.sprintf;
	var T = 'bigspy-ai-theme-wp';
	var A = (window.bstPreview && window.bstPreview.accent) || '#3557a7';
	var INK = '#18394f';
	var MUTED = '#6b7f90';
	var LINE = '#dfe7ed';
	var SOFT = '#f5f8fa';

	function st(o) {
		return Object.keys(o).map(function (k) { return k + ':' + o[k]; }).join(';');
	}
	function box(css, inner) { return '<div style="' + css + '">' + (inner || '') + '</div>'; }
	function span(css, inner) { return '<span style="' + css + '">' + (inner || '') + '</span>'; }
	function bar(w, h, c) { return box('height:' + (h || 4) + 'px;width:' + w + ';background:' + (c || LINE) + ';border-radius:2px;margin:5px 0'); }
	function lines(n, w) {
		var out = '';
		for (var i = 0; i < n; i++) { out += bar(i === n - 1 ? '62%' : (w || '94%')); }
		return out;
	}
	function title(t, size, align) { return box('color:' + INK + ';font-size:' + (size || 14) + 'px;font-weight:800;line-height:1.25;margin:4px 0;text-align:' + (align || 'left'), t); }
	function tag(t) { return span('display:inline-block;padding:3px 8px;border-radius:999px;background:' + A + '1f;color:' + A + ';font-size:8px;font-weight:700;letter-spacing:.06em', t); }
	function btn(t, outline) {
		return span('display:inline-block;padding:5px 11px;border-radius:6px;font-size:8px;font-weight:700;' + (outline ? 'border:1px solid #c9d7e0;color:' + INK : 'background:' + A + ';color:#fff'), t);
	}
	function img(h, radius, icon) {
		return box('height:' + h + 'px;border-radius:' + (radius === undefined ? 10 : radius) + 'px;background:linear-gradient(145deg,' + A + '33,#f4f8fa);display:grid;place-items:center', icon ? svg(icon, 26) : '');
	}
	function dot(c, s) { return '<i style="display:inline-block;width:' + (s || 9) + 'px;height:' + (s || 9) + 'px;border-radius:50%;background:' + c + '"></i>'; }
	function card(inner, css) { return box('flex:1;min-width:0;padding:10px;background:' + SOFT + ';border:1px solid #e8eef2;border-radius:8px;' + (css || ''), inner); }
	function row(items, gap) { return box('display:flex;gap:' + (gap || 8) + 'px;align-items:stretch', items.join('')); }
	function rep(n, fn) { var a = []; for (var i = 0; i < n; i++) { a.push(fn(i)); } return a; }

	var ICONS = {
		chat: '<path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/><path d="M8 9h8M8 13h5"/>',
		image: '<rect x="3" y="4" width="18" height="16" rx="2"/><circle cx="9" cy="10" r="2"/><path d="m21 17-5-5-9 8"/>',
		star: '<path d="m12 3 2.7 5.6 6.1.9-4.4 4.3 1 6.1L12 17l-5.4 2.9 1-6.1-4.4-4.3 6.1-.9z"/>',
		play: '<circle cx="12" cy="12" r="9"/><path d="m10 8 6 4-6 4z"/>',
		pin: '<path d="M12 21s-7-6.2-7-11a7 7 0 0 1 14 0c0 4.8-7 11-7 11z"/><circle cx="12" cy="10" r="2.5"/>',
		check: '<path d="m5 12 4 4 10-10"/>',
		cart: '<circle cx="9" cy="20" r="1.5"/><circle cx="18" cy="20" r="1.5"/><path d="M3 4h2l2.4 11h11l2-8H6"/>',
		music: '<path d="M9 18V5l11-2v13"/><circle cx="6" cy="18" r="3"/><circle cx="17" cy="16" r="3"/>',
		clock: '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
		code: '<path d="m8 8-4 4 4 4M16 8l4 4-4 4"/>',
		share: '<circle cx="18" cy="5" r="2.5"/><circle cx="6" cy="12" r="2.5"/><circle cx="18" cy="19" r="2.5"/><path d="m8.2 10.8 7.6-4.6M8.2 13.2l7.6 4.6"/>',
		user: '<circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/>',
		bolt: '<path d="M13 2 4 14h7l-1 8 9-12h-7z"/>'
	};
	function svg(name, s) {
		return '<svg width="' + (s || 18) + '" height="' + (s || 18) + '" viewBox="0 0 24 24" fill="none" stroke="' + A + '" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' + ICONS[name] + '</svg>';
	}
	function iconCircle(name) { return box('width:28px;height:28px;border-radius:50%;background:' + A + '1f;display:grid;place-items:center;margin:0 auto 6px', svg(name, 15)); }

	/** Browser-window frame with a small site header, like a page on a real site. */
	function frame(body, path) {
		var nav = [__('About', T), __('Services', T), __('Projects', T), __('Contact', T)];
		return '<div style="background:#fff;border:1px solid #dce5ec;border-radius:16px;overflow:hidden;box-shadow:0 18px 44px rgba(18,51,76,.10);font-family:inherit">' +
			box('display:flex;align-items:center;gap:6px;padding:9px 14px;background:#f3f6f9;border-bottom:1px solid #e6edf2', dot('#ff6159') + dot('#ffbd2e') + dot('#28c941') + span('flex:1;margin-left:10px;padding:4px 10px;background:#fff;border:1px solid #e3eaf0;border-radius:6px;color:#8a9bab;font-size:10px', 'yourwebsite.com/' + (path || ''))) +
			box('display:flex;align-items:center;justify-content:space-between;padding:9px 18px;border-bottom:1px solid #eef3f6', span('display:flex;align-items:center;gap:6px;font-size:11px;font-weight:800;color:' + INK, '<i style="width:16px;height:16px;border-radius:5px;background:' + A + '"></i>LOGO') + span('display:flex;gap:12px;font-size:9px;color:' + MUTED, nav.map(function (n, i) { return i === 1 ? '<b style="color:' + A + '">' + n + '</b>' : '<span>' + n + '</span>'; }).join(''))) +
			box('padding:18px', body) + '</div>';
	}

	var feature = function (i) { return card(iconCircle(['chat', 'bolt', 'check'][i % 3]) + box('text-align:center;font-size:8px;font-weight:700;color:' + INK, [__('Consulting', T), __('Delivery', T), __('Support', T)][i % 3]) + lines(1, '80%'), 'text-align:center'); };
	var product = function (i) { return card(img(58, 6, 'cart') + box('font-size:8px;font-weight:700;color:' + INK + ';margin-top:6px', __('Product name', T)) + box('font-size:9px;font-weight:800;color:' + A + ';margin:3px 0 5px', (199 + i * 50) + '.000đ') + btn(__('Add to cart', T)), 'padding:7px'); };
	var post = function () { return card(img(52, 6, 'image') + box('font-size:8px;font-weight:700;color:' + INK + ';margin-top:6px', __('Article title', T)) + lines(2), 'padding:7px'); };
	var quote = function () { return card(box('display:flex;gap:6px;align-items:center', box('width:22px;height:22px;border-radius:50%;background:' + A + '33') + box('font-size:8px;font-weight:700;color:' + INK, __('Customer name', T) + '<br><span style="color:#f5a623">★★★★★</span>')) + lines(2)); };
	var heroText = function () { return tag(__('SERVICES', T)) + title(__('Complete solutions for your business', T), 15) + lines(2) + box('display:flex;gap:6px;margin-top:10px', btn(__('Get a quote', T)) + btn(__('Contact', T), true)); };

	var P = {
		section: function () { return frame(box('padding:16px;border-radius:12px;background:linear-gradient(135deg,' + A + ',' + A + 'aa);color:#fff', box('font-size:8px;opacity:.8;letter-spacing:.08em', 'SECTION') + box('font-size:15px;font-weight:800;margin:4px 0', __('Full-width section', T)) + bar('70%', 4, '#ffffff66') + bar('50%', 4, '#ffffff66'))); },
		row: function () { return frame(row([4, 4, 4].map(function (n) { return card(box('text-align:center;font-size:10px;font-weight:800;color:' + A, n + '/12') + lines(2)); }))); },
		column: function () { return frame(row([card(box('font-size:10px;font-weight:800;color:' + A, '8/12') + lines(3), 'flex:2;border:2px dashed ' + A), card(box('font-size:10px;font-weight:800;color:' + MUTED, '4/12') + lines(3))])); },
		banner: function () { return frame(box('position:relative;height:150px;border-radius:12px;background:linear-gradient(120deg,' + INK + 'ee,' + A + '99),linear-gradient(145deg,#cbd8e6,#eef3f7);padding:18px;color:#fff;display:flex;flex-direction:column;justify-content:center', box('font-size:16px;font-weight:800', __('Big promotion this week', T)) + bar('60%', 4, '#ffffff77') + box('margin-top:8px', btn(__('Shop now', T))))); },
		slider: function () { return frame(box('position:relative', img(130, 12, 'image') + span('position:absolute;left:8px;top:55px;width:20px;height:20px;border-radius:50%;background:#fff;box-shadow:0 2px 6px #0002;text-align:center;font-size:12px;line-height:20px;color:' + INK, '‹') + span('position:absolute;right:8px;top:55px;width:20px;height:20px;border-radius:50%;background:#fff;box-shadow:0 2px 6px #0002;text-align:center;font-size:12px;line-height:20px;color:' + INK, '›') + box('text-align:center;margin-top:8px', dot(A, 7) + ' ' + dot(LINE, 7) + ' ' + dot(LINE, 7)) + bar('35%', 3, A))); },
		'icon-box': function () { return frame(row(rep(3, feature))); },
		title: function () { return frame(box('display:flex;align-items:end;justify-content:space-between;border-bottom:2px solid ' + LINE + ';padding-bottom:6px', box('', box('font-size:8px;color:' + A + ';font-weight:700;letter-spacing:.08em', __('OUR PRODUCTS', T)) + title(__('Featured collection', T), 15)) + span('font-size:8px;color:' + A + ';font-weight:700', __('View all', T) + ' →')) + box('width:50px;height:3px;background:' + A + ';margin-top:-2px') + row(rep(3, function () { return img(50, 8); }))); },
		tabs: function () { return frame(box('display:flex;gap:4px;border-bottom:1px solid ' + LINE, ['Tab 1', 'Tab 2', 'Tab 3'].map(function (t, i) { return span('padding:6px 12px;font-size:9px;font-weight:700;border-radius:6px 6px 0 0;' + (i === 0 ? 'background:' + A + ';color:#fff' : 'color:' + MUTED), t); }).join('')) + box('padding:12px 4px', lines(4))); },
		tab: function () { return P.tabs(); },
		accordion: function () { return frame([__('How long does delivery take?', T), __('Do you offer a warranty?', T), __('How can I pay?', T)].map(function (q, i) { return box('border:1px solid ' + LINE + ';border-radius:8px;margin-bottom:6px;overflow:hidden', box('display:flex;justify-content:space-between;padding:8px 10px;font-size:9px;font-weight:700;color:' + (i === 0 ? A : INK) + ';background:' + (i === 0 ? A + '12' : '#fff'), q + '<span>' + (i === 0 ? '−' : '+') + '</span>') + (i === 0 ? box('padding:2px 10px 8px', lines(2)) : '')); }).join('')); },
		'accordion-item': function () { return P.accordion(); },
		posts: function () { return frame(row(rep(3, post))); },
		testimonial: function () { return frame(box('max-width:300px;margin:0 auto;text-align:center', box('font-size:28px;color:' + A + ';line-height:1', '“') + lines(3) + box('width:30px;height:30px;border-radius:50%;background:' + A + '33;margin:8px auto 4px') + box('font-size:9px;font-weight:700;color:' + INK, __('Customer name', T)) + box('color:#f5a623;font-size:10px', '★★★★★'))); },
		counter: function () { return frame(row([['15+', __('Years', T)], ['2.500', __('Customers', T)], ['98%', __('Satisfied', T)]].map(function (c) { return card(box('text-align:center;font-size:18px;font-weight:800;color:' + A, c[0]) + box('text-align:center;font-size:8px;color:' + MUTED, c[1])); }))); },
		map: function () { return frame(box('position:relative;height:150px;border-radius:12px;background:#e8efe6;overflow:hidden', box('position:absolute;inset:0;background:repeating-linear-gradient(90deg,transparent 0 38px,#fff 38px 42px),repeating-linear-gradient(0deg,transparent 0 30px,#fff 30px 33px)') + box('position:absolute;left:46%;top:32%', svg('pin', 30)))); },
		heading: function () { return frame(['H1', 'H2', 'H3'].map(function (h, i) { return box('display:flex;align-items:baseline;gap:8px', tag(h) + title(__('Your page heading', T), 18 - i * 3)); }).join('')); },
		text: function () { return frame(title(__('Paragraph title', T), 13) + lines(3) + box('margin:8px 0;padding:6px 10px;border-left:3px solid ' + A + ';background:' + SOFT, lines(1, '80%')) + rep(3, function () { return box('display:flex;gap:6px;align-items:center', dot(A, 5) + bar('60%')); }).join('')); },
		image: function () { return frame(box('max-width:280px;margin:0 auto', img(140, 14, 'image') + box('text-align:center;font-size:8px;color:' + MUTED + ';margin-top:5px', __('Image caption', T)))); },
		gallery: function () { return frame(box('display:grid;grid-template-columns:repeat(4,1fr);gap:6px', rep(8, function (i) { return img(i % 3 ? 48 : 48, 6); }).join(''))); },
		video: function () { return frame(box('position:relative;height:150px;border-radius:12px;background:linear-gradient(135deg,' + INK + ',' + A + ');display:grid;place-items:center', box('width:44px;height:44px;border-radius:50%;background:#fff;display:grid;place-items:center', svg('play', 26)))); },
		'video-button': function () { return frame(box('position:relative', img(140, 12) + box('position:absolute;inset:0;display:grid;place-items:center', box('width:46px;height:46px;border-radius:50%;background:' + A + ';box-shadow:0 0 0 10px ' + A + '33;display:grid;place-items:center;color:#fff;font-size:16px', '▶')))); },
		social: function () { return frame(box('display:flex;gap:8px;justify-content:center;padding:20px 0', ['#1877f2', '#0068ff', '#111', '#ff0000', '#e1306c'].map(function (c, i) { return span('width:30px;height:30px;border-radius:50%;background:' + c + ';color:#fff;font-size:10px;font-weight:800;display:grid;place-items:center', ['f', 'Z', '♪', '▶', '◎'][i]); }).join(''))); },
		share: function () { return frame(box('display:flex;gap:6px;align-items:center;padding:20px 0;justify-content:center', span('font-size:9px;font-weight:700;color:' + INK, __('Share', T)) + ['#1877f2', '#0068ff', '#111', '#0a66c2'].map(function (c) { return span('padding:5px 10px;border-radius:6px;background:' + c + ';color:#fff;font-size:8px', '●'); }).join('') + span('padding:5px 10px;border-radius:6px;border:1px solid ' + LINE + ';font-size:8px;color:' + INK, __('Copy link', T)))); },
		form: function () { return frame(box('max-width:300px;margin:0 auto;padding:14px;border:1px solid ' + LINE + ';border-radius:12px', title(__('Request a quote', T), 13) + rep(3, function () { return box('height:20px;border:1px solid ' + LINE + ';border-radius:6px;margin:6px 0;background:#fff'); }).join('') + box('height:40px;border:1px solid ' + LINE + ';border-radius:6px;margin:6px 0') + btn(__('Send', T)))); },
		'team-member': function () { return frame(row(rep(3, function (i) { return card(box('width:44px;height:44px;border-radius:50%;margin:0 auto 6px;background:linear-gradient(145deg,' + A + '55,#eef3f7)') + box('text-align:center;font-size:9px;font-weight:700;color:' + INK, __('Full name', T)) + box('text-align:center;font-size:8px;color:' + A, __('Role', T)), 'text-align:center'); }))); },
		'price-table': function () { return frame(row([0, 1, 2].map(function (i) { return card(box('font-size:9px;font-weight:700;color:' + INK, [__('Basic', T), __('Pro', T), __('Business', T)][i]) + (i === 1 ? tag(__('POPULAR', T)) : '') + box('font-size:16px;font-weight:800;color:' + A + ';margin:4px 0', ['99k', '199k', '399k'][i]) + rep(3, function () { return box('display:flex;gap:4px;align-items:center', svg('check', 10) + bar('70%')); }).join('') + box('margin-top:6px', btn(__('Choose', T), i !== 1)), i === 1 ? 'border:2px solid ' + A : ''); }))); },
		'pricing-toggle': function () { return frame(box('text-align:center;margin-bottom:8px', span('display:inline-flex;gap:2px;padding:3px;border-radius:999px;background:' + SOFT + ';border:1px solid ' + LINE, span('padding:4px 10px;border-radius:999px;background:' + A + ';color:#fff;font-size:8px;font-weight:700', __('Monthly', T)) + span('padding:4px 10px;font-size:8px;color:' + INK, __('Yearly', T) + ' −20%'))) + row([0, 1].map(function (i) { return card(box('font-size:16px;font-weight:800;color:' + A, ['199k', '399k'][i]) + lines(3)); }))); },
		'icon-list': function () { return frame([__('Free delivery', T), __('12-month warranty', T), __('Pay on delivery', T), __('Support 8:00–21:00', T)].map(function (t) { return box('display:flex;gap:8px;align-items:center;padding:5px 0;border-bottom:1px dashed ' + LINE + ';font-size:9px;color:' + INK, svg('check', 13) + t); }).join('')); },
		'image-box': function () { return frame(row(rep(3, function () { return card(img(60, 6, 'image') + box('font-size:9px;font-weight:700;color:' + INK + ';margin-top:6px', __('Service', T)) + lines(2) + box('font-size:8px;color:' + A + ';font-weight:700', __('Learn more', T) + ' →'), 'padding:7px'); }))); },
		alert: function () { return frame([['#e8f3ff', '#1d6fd8', 'i', __('Free shipping on orders over 500k.', T)], ['#e9f8ef', '#1e8e4f', '✓', __('Your request has been sent.', T)], ['#fff6e5', '#b7791f', '!', __('Limited stock this week.', T)]].map(function (a) { return box('display:flex;gap:8px;align-items:center;padding:8px 10px;margin-bottom:6px;border-radius:8px;background:' + a[0] + ';border-left:3px solid ' + a[1] + ';font-size:9px;color:' + a[1], span('width:16px;height:16px;border-radius:50%;background:' + a[1] + ';color:#fff;font-size:9px;font-weight:800;text-align:center;line-height:16px', a[2]) + a[3]); }).join('')); },
		progress: function () { return frame([['SEO', 90], ['Design', 75], ['Ads', 60]].map(function (p) { return box('margin-bottom:9px', box('display:flex;justify-content:space-between;font-size:9px;font-weight:700;color:' + INK, p[0] + '<span>' + p[1] + '%</span>') + box('height:8px;border-radius:99px;background:' + LINE + ';margin-top:3px', box('height:8px;border-radius:99px;width:' + p[1] + '%;background:repeating-linear-gradient(45deg,' + A + ' 0 6px,' + A + 'cc 6px 12px)'))); }).join('')); },
		'star-rating': function () { return frame(box('text-align:center;padding:20px 0', box('font-size:26px;color:#f5a623;letter-spacing:3px', '★★★★<span style="color:#f5a62366">★</span>') + box('font-size:10px;color:' + INK + ';font-weight:700', sprintf(__('%1$s/5 from %2$s reviews', T), '4.9', '320')))); },
		buttons: function () { return frame(box('display:flex;gap:8px;justify-content:center;padding:26px 0', btn(__('Call now', T)) + btn(__('Get a quote', T), true) + span('font-size:8px;font-weight:700;color:' + A + ';align-self:center', __('Learn more', T) + ' →'))); },
		button: function () { return frame(box('display:flex;gap:8px;justify-content:center;padding:26px 0', btn(__('Filled', T)) + btn(__('Outline', T), true) + span('display:inline-block;padding:5px 11px;border-radius:99px;background:' + A + '22;color:' + A + ';font-size:8px;font-weight:700', __('Soft', T)))); },
		divider: function () { return frame(['solid', 'dashed', 'dotted', 'double'].map(function (s) { return box('border-top:3px ' + s + ' ' + A + ';margin:12px 0'); }).join('')); },
		spacer: function () { return frame(card(lines(1)) + box('height:44px;margin:6px 0;border:1px dashed ' + A + ';border-radius:6px;display:grid;place-items:center;font-size:8px;color:' + A, '↕ 40px') + card(lines(1))); },
		code: function () { return frame(box('border-radius:10px;background:#0f1d2b;padding:12px;font-family:monospace;font-size:9px;line-height:1.7', '<span style="color:#7fb2ff">&lt;div</span> <span style="color:#ffcf70">class</span>=<span style="color:#9be38f">"promo"</span><span style="color:#7fb2ff">&gt;</span><br>&nbsp;&nbsp;<span style="color:#cfe1f2">' + __('Custom HTML', T) + '</span><br><span style="color:#7fb2ff">&lt;/div&gt;</span><br><span style="color:#ffcf70">.promo</span> <span style="color:#cfe1f2">{ color: </span><span style="color:#9be38f">' + A + '</span><span style="color:#cfe1f2"> }</span>') + box('text-align:right;margin-top:5px', btn(__('Copy', T), true))); },
		products: function () { return frame(row(rep(3, product))); },
		'products-list': function () { return frame(row([box('flex:2', lines(6)), card(title(__('Best sellers', T), 10) + rep(3, function (i) { return box('display:flex;gap:7px;align-items:center;padding:5px 0;border-bottom:1px solid ' + LINE, box('flex:0 0 30px;height:30px;border-radius:6px;background:linear-gradient(145deg,' + A + '44,#eef3f7)') + box('flex:1;min-width:0', box('font-size:8px;font-weight:700;color:' + INK, __('Product name', T)) + box('font-size:8px;font-weight:800;color:' + A, (149 + i * 40) + '.000đ'))); }).join(''))], 12)); },
		'product-categories': function () { return frame(row(rep(4, function (i) { return card(box('width:42px;height:42px;border-radius:50%;margin:0 auto 5px;background:linear-gradient(145deg,' + A + '44,#eef3f7)') + box('text-align:center;font-size:8px;font-weight:700;color:' + INK, __('Category', T)) + box('text-align:center;font-size:7px;color:' + MUTED, (12 + i * 3) + ' ' + __('products', T)), 'text-align:center;padding:7px'); }))); },
		'product-part': function () { return frame(row([box('flex:1', img(120, 10, 'cart')), box('flex:1', title(__('Product name', T), 13) + box('font-size:13px;font-weight:800;color:' + A, '299.000đ') + box('color:#f5a623;font-size:9px', '★★★★★') + lines(2) + box('margin-top:6px', btn(__('Add to cart', T))))], 12)); },
		hotspot: function () { return frame(box('position:relative', img(150, 12) + [[22, 40], [60, 30], [74, 68]].map(function (p) { return span('position:absolute;left:' + p[0] + '%;top:' + p[1] + '%;width:16px;height:16px;border-radius:50%;background:' + A + ';box-shadow:0 0 0 5px ' + A + '44;color:#fff;font-size:10px;text-align:center;line-height:16px', '+'); }).join('') + box('position:absolute;left:30%;top:16%;padding:5px 8px;border-radius:6px;background:#fff;box-shadow:0 6px 16px #0002;font-size:8px;color:' + INK, __('Product name', T) + ' · 199k'))); },
		popup: function () { return frame(box('position:relative;height:150px;border-radius:10px;background:#18394f55;display:grid;place-items:center', box('width:200px;padding:12px;border-radius:12px;background:#fff;box-shadow:0 16px 40px #0003;position:relative', span('position:absolute;right:8px;top:4px;font-size:12px;color:' + MUTED, '×') + title(__('Get 10% off', T), 12) + lines(2) + btn(__('Claim offer', T))))); },
		logos: function () { return frame(box('display:flex;gap:10px;align-items:center;overflow:hidden;padding:16px 0', rep(6, function (i) { return span('flex:0 0 60px;height:26px;border-radius:6px;background:#e4ebf1;color:#9fb0bf;font-size:9px;font-weight:800;display:grid;place-items:center', 'LOGO ' + (i + 1)); }).join(''))); },
		'widget-area': function () { return frame(row([box('flex:2', lines(6)), card(title(__('Sidebar', T), 10) + rep(4, function () { return box('display:flex;gap:5px;align-items:center', dot(A, 5) + bar('70%')); }).join(''))], 12)); },
		shortcode: function () { return frame(box('padding:10px;border:1px dashed ' + A + ';border-radius:10px;font-family:monospace;font-size:10px;color:' + A + ';text-align:center', '[contact-form-7 id="123"]') + box('margin-top:8px;padding:10px;border:1px solid ' + LINE + ';border-radius:10px', lines(2) + btn(__('Send', T)))); },
		'payment-icons': function () { return frame(box('display:flex;gap:6px;justify-content:center;padding:24px 0', ['VISA', 'MC', 'MoMo', 'ZaloPay', 'COD'].map(function (p) { return span('padding:6px 9px;border:1px solid ' + LINE + ';border-radius:6px;background:#fff;font-size:8px;font-weight:800;color:' + INK, p); }).join(''))); },
		marquee: function () { return frame(box('overflow:hidden;white-space:nowrap;padding:14px 0;background:' + A + ';border-radius:8px;color:#fff;font-size:13px;font-weight:800', '&nbsp;SEO ✦ WEB DESIGN ✦ GOOGLE ADS ✦ BRANDING ✦ SEO ✦ WEB DESIGN ✦') + box('text-align:center;font-size:8px;color:' + MUTED + ';margin-top:6px', '← ← ←')); },
		'before-after': function () { return frame(box('position:relative;height:140px;border-radius:12px;overflow:hidden;display:flex', box('flex:1;background:#c6ced6', span('position:absolute;left:8px;top:8px;padding:2px 6px;border-radius:4px;background:#0006;color:#fff;font-size:8px', __('Before', T))) + box('flex:1;background:linear-gradient(145deg,' + A + '55,#eef6ff)', span('position:absolute;right:8px;top:8px;padding:2px 6px;border-radius:4px;background:#0006;color:#fff;font-size:8px', __('After', T))) + box('position:absolute;left:50%;top:0;bottom:0;width:3px;background:#fff') + span('position:absolute;left:calc(50% - 11px);top:59px;width:24px;height:24px;border-radius:50%;background:#fff;box-shadow:0 2px 8px #0003;font-size:10px;text-align:center;line-height:24px;color:' + INK, '⇆'))); },
		'flip-box': function () { return frame(row([card(iconCircle('bolt') + box('text-align:center;font-size:9px;font-weight:700;color:' + INK, __('Front', T)), 'text-align:center;padding:18px 8px'), card(box('font-size:9px;font-weight:700;color:#fff', __('Back', T)) + bar('90%', 4, '#ffffff66') + bar('70%', 4, '#ffffff66') + box('margin-top:6px', span('display:inline-block;padding:4px 9px;border-radius:6px;background:#fff;color:' + A + ';font-size:8px;font-weight:700', __('Learn more', T))), 'background:' + A + ';border-color:' + A + ';transform:perspective(300px) rotateY(-12deg)')], 14)); },
		timeline: function () { return frame(box('position:relative;padding-left:18px;border-left:2px solid ' + LINE, ['2018', '2021', '2026'].map(function (y) { return box('position:relative;margin-bottom:10px', span('position:absolute;left:-25px;top:2px;width:12px;height:12px;border-radius:50%;background:' + A + ';box-shadow:0 0 0 3px #fff') + box('font-size:9px;font-weight:800;color:' + A, y) + lines(1, '70%')); }).join(''))); },
		table: function () { return frame(box('border:1px solid ' + LINE + ';border-radius:8px;overflow:hidden;font-size:8px;color:' + INK, rep(4, function (r) { return box('display:flex;' + (r === 0 ? 'background:' + A + ';color:#fff;font-weight:700' : (r % 2 ? 'background:#fff' : 'background:' + SOFT)), rep(3, function (c) { return span('flex:1;padding:6px 8px', r === 0 ? [__('Item', T), __('Size', T), __('Price', T)][c] : (c === 2 ? (100 * r) + 'k' : '—')); }).join('')); }).join(''))); },
		lottie: function () { return frame(box('height:140px;display:grid;place-items:center', '<svg width="90" height="90" viewBox="0 0 100 100" aria-hidden="true"><circle cx="50" cy="50" r="40" fill="none" stroke="' + A + '33" stroke-width="8"/><path d="M50 10a40 40 0 0 1 40 40" fill="none" stroke="' + A + '" stroke-width="8" stroke-linecap="round"/><circle cx="50" cy="50" r="12" fill="' + A + '"/></svg>')); },
		audio: function () { return frame(box('display:flex;gap:10px;align-items:center;padding:10px;border:1px solid ' + LINE + ';border-radius:12px', box('width:54px;height:54px;border-radius:8px;background:linear-gradient(145deg,' + A + '66,#eef3f7);display:grid;place-items:center', svg('music', 22)) + box('flex:1', box('font-size:9px;font-weight:700;color:' + INK, __('Episode title', T)) + box('font-size:8px;color:' + MUTED, __('Artist', T)) + box('display:flex;gap:6px;align-items:center;margin-top:6px', span('width:18px;height:18px;border-radius:50%;background:' + A + ';color:#fff;font-size:8px;text-align:center;line-height:18px', '▶') + box('flex:1;height:4px;border-radius:2px;background:' + LINE, box('width:35%;height:4px;border-radius:2px;background:' + A)))))); },
		'nav-menu': function () { return frame(box('display:flex;gap:4px;justify-content:center;padding:18px 0', [__('Home', T), __('Services', T), __('Pricing', T), __('Blog', T), __('Contact', T)].map(function (m, i) { return span('padding:6px 10px;border-radius:6px;font-size:9px;font-weight:700;' + (i === 1 ? 'background:' + A + ';color:#fff' : 'color:' + INK), m); }).join(''))); },
		'banner-grid': function () { return frame(box('display:grid;grid-template-columns:2fr 1fr;grid-template-rows:70px 70px;gap:6px', box('grid-row:span 2;border-radius:10px;padding:10px;color:#fff;font-size:11px;font-weight:800;display:flex;align-items:end;background:linear-gradient(120deg,' + INK + 'dd,' + A + '99)', __('New collection', T)) + box('border-radius:10px;padding:8px;font-size:9px;font-weight:700;color:' + INK + ';display:flex;align-items:end;background:linear-gradient(145deg,' + A + '33,#f4f8fa)', __('Offers', T)) + box('border-radius:10px;padding:8px;font-size:9px;font-weight:700;color:' + INK + ';display:flex;align-items:end;background:linear-gradient(145deg,#ffd9a0,#fff6e8)', __('Best sellers', T)))); },
		countdown: function () { return frame(box('text-align:center;padding:12px 0', box('font-size:9px;font-weight:700;color:' + INK + ';margin-bottom:6px', __('Offer ends in', T)) + box('display:flex;gap:6px;justify-content:center', [['02', __('days', T)], ['14', __('hrs', T)], ['36', __('min', T)], ['09', __('sec', T)]].map(function (c) { return box('width:44px;padding:6px 0;border-radius:8px;background:' + A + ';color:#fff', box('font-size:15px;font-weight:800', c[0]) + box('font-size:7px;opacity:.8', c[1])); }).join('')))); },
		ticker: function () { return frame(box('position:relative;overflow:hidden;padding:6px 0', box('display:flex;gap:8px;margin-left:-30px', rep(5, function () { return box('flex:0 0 90px;min-width:0', quote()); }).join('')) + box('position:absolute;inset:0 auto 0 0;width:40px;background:linear-gradient(90deg,#fff,transparent)') + box('position:absolute;inset:0 0 0 auto;width:40px;background:linear-gradient(270deg,#fff,transparent)')) + box('text-align:center;font-size:9px;color:' + A + ';font-weight:700;margin-top:6px', '← ' + __('seamless loop', T) + ' ←')); },
		'ticker-slide': function () { return P.ticker(); },
		'expand-gallery': function () { return frame(box('display:flex;gap:6px;height:150px', [1, 2, 1].map(function (g, i) { return box('flex:' + g + ';border-radius:12px;padding:10px;color:#fff;display:flex;flex-direction:column;justify-content:end;background:linear-gradient(180deg,transparent 30%,' + INK + 'cc),linear-gradient(145deg,' + ['#9ccf8f', A, '#f0b36b'][i] + ',#eef3f7)', box('font-size:' + (g === 2 ? 12 : 9) + 'px;font-weight:800', [__('Fresh fruit', T), __('Coffee', T), __('Chilled', T)][i]) + (g === 2 ? bar('70%', 4, '#ffffff88') + box('margin-top:5px', btn(__('Explore', T))) : '')); }).join(''))); },
		'expand-panel': function () { return P['expand-gallery'](); }
	};

	function previewHtml(name, settings) {
		var key = name.replace(/^(bst|bigspy)\//, '');
		var fn = P[key];
		var body = fn ? fn() : frame(title(settings.title || key, 14) + lines(3));
		var label = sprintf(__('Preview of the %s block', T), settings.title || key);
		return '<div role="img" aria-label="' + label.replace(/"/g, '&quot;') + '" style="padding:6px;background:transparent">' + body + '</div>';
	}

	window.bstBlockPreviewNames = Object.keys(P);

	/* Icon color per block family, so blocks are easy to tell apart in the inserter. */
	var FAMILY = {
		'#4f46e5': ['section', 'row', 'column', 'spacer', 'divider', 'widget-area'], // layout
		'#2563eb': ['heading', 'title', 'text', 'icon-list', 'table', 'alert', 'code', 'shortcode'], // text
		'#db2777': ['image', 'gallery', 'banner', 'banner-grid', 'video', 'video-button', 'audio', 'lottie', 'before-after', 'hotspot', 'map'], // media
		'#7c3aed': ['slider', 'ticker', 'ticker-slide', 'expand-gallery', 'expand-panel', 'marquee', 'logos', 'flip-box'], // motion
		'#ea580c': ['tabs', 'tab', 'accordion', 'accordion-item', 'popup', 'button', 'buttons', 'form', 'nav-menu'], // interactive
		'#16a34a': ['products', 'products-list', 'product-categories', 'product-part', 'payment-icons', 'price-table', 'pricing-toggle', 'countdown'], // shop and pricing
		'#0891b2': ['icon-box', 'image-box', 'counter', 'progress', 'star-rating', 'timeline', 'team-member', 'testimonial', 'posts', 'social', 'share'] // trust and content cards
	};
	var ICON_COLORS = {};
	Object.keys(FAMILY).forEach(function (c) { FAMILY[c].forEach(function (b) { ICON_COLORS[b] = c; }); });

	wp.hooks.addFilter('blocks.registerBlockType', 'bst/block-previews', function (settings, name) {
		if (!/^(bst|bigspy)\//.test(name) || typeof settings.edit !== 'function') {
			return settings;
		}
		var edit = settings.edit;
		var color = ICON_COLORS[name.replace(/^(bst|bigspy)\//, '')];
		if (color && settings.icon) {
			// Colored inserter icons, one color per block family.
			settings.icon = settings.icon.src ? Object.assign({}, settings.icon, { foreground: color }) : { src: settings.icon, foreground: color };
		}
		settings.attributes = Object.assign({}, settings.attributes, { bstPreview: { type: 'boolean', default: false } });
		settings.example = { attributes: { bstPreview: true }, viewportWidth: 560 };
		settings.edit = function (props) {
			if (props.attributes && props.attributes.bstPreview) {
				return el('div', wp.blockEditor.useBlockProps({ className: 'bst-block-preview' }), el('div', { dangerouslySetInnerHTML: { __html: previewHtml(name, settings) } }));
			}
			return edit(props);
		};
		return settings;
	}, 5);
})(window.wp);
