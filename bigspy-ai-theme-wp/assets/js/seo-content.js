/**
 * BIGSPY blocks are dynamic: their text lives in block attributes, so SEO plugins that read the
 * editor content (Rank Math, Yoast) see almost nothing. This rebuilds the readable HTML of every
 * bst/* block and hands it to their content analysis.
 */
(function (wp) {
	'use strict';
	if (!wp || !wp.hooks || !wp.data) {
		return;
	}
	var TEXT_KEYS = ['subtitle', 'description', 'content', 'quote', 'name', 'role', 'label', 'caption', 'note', 'kicker'];

	function esc(s) {
		return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
	}

	function blockHtml(b) {
		var a = b.attributes || {}, name = b.name, out = '';
		var inner = (b.innerBlocks || []).map(blockHtml).join('');
		if (name.indexOf('bst/') !== 0) {
			return inner; // core blocks are already in the saved content
		}
		var type = name.slice(4);
		switch (type) {
			case 'heading':
				var tag = /^h[1-6]$/.test(a.tag) ? a.tag : 'h2';
				out = a.text ? '<' + tag + '>' + a.text + '</' + tag + '>' : '';
				break;
			case 'title':
				out = (a.text ? '<' + (/^h[1-6]$/.test(a.tag) ? a.tag : 'h2') + '>' + a.text + '</' + (/^h[1-6]$/.test(a.tag) ? a.tag : 'h2') + '>' : '') + (a.subtitle ? '<p>' + a.subtitle + '</p>' : '');
				break;
			case 'text':
				out = a.html || '';
				break;
			case 'code':
				out = (a.code || '').replace(/<script[\s\S]*?<\/script>/gi, '');
				break;
			case 'button':
				out = a.text ? '<a href="' + esc(a.url || '#') + '">' + a.text + '</a>' : '';
				break;
			case 'image':
				out = a.imageUrl ? '<img src="' + esc(a.imageUrl) + '" alt="' + esc(a.alt || '') + '">' + (a.caption ? '<p>' + a.caption + '</p>' : '') : '';
				break;
			case 'gallery':
				out = (a.images || []).map(function (im) { return '<img src="' + esc(im.url || '') + '" alt="' + esc(im.alt || '') + '">'; }).join('');
				break;
			case 'icon-list':
				out = '<ul>' + (a.items || []).map(function (it) { return '<li>' + (it.text || '') + '</li>'; }).join('') + '</ul>';
				break;
			case 'price-table':
				out = '<h3>' + (a.title || '') + '</h3><p>' + (a.price || '') + ' ' + (a.period || '') + '</p><p>' + (a.description || '') + '</p><ul>' + String(a.features || '').split('\n').filter(Boolean).map(function (f) { return '<li>' + f + '</li>'; }).join('') + '</ul>';
				break;
			case 'accordion-item':
			case 'tab':
				out = a.title ? '<h3>' + a.title + '</h3>' : '';
				break;
			default:
				if (a.title) {
					out += '<h3>' + a.title + '</h3>';
				}
				if (a.text && typeof a.text === 'string') {
					out += '<p>' + a.text + '</p>';
				}
				TEXT_KEYS.forEach(function (k) {
					if (typeof a[k] === 'string' && a[k]) {
						out += '<p>' + a[k] + '</p>';
					}
				});
				if (a.imageUrl) {
					out += '<img src="' + esc(a.imageUrl) + '" alt="' + esc(a.alt || a.imageAlt || '') + '">';
				}
		}
		return out + inner;
	}

	function readable() {
		var blocks = wp.data.select('core/block-editor').getBlocks();
		return blocks.map(blockHtml).join('\n');
	}

	// Rank Math
	wp.hooks.addFilter('rank_math_content', 'bigspy/seo-content', function (content) {
		return content + '\n' + readable();
	});
	// Yoast SEO (content analysis via its plugin API)
	if (window.YoastSEO && window.YoastSEO.app && window.YoastSEO.app.registerPlugin) {
		window.YoastSEO.app.registerPlugin('bigspySeoContent', { status: 'ready' });
		window.YoastSEO.app.registerModification('content', function (content) { return content + readable(); }, 'bigspySeoContent', 5);
	}

	// Re-run the analysis shortly after blocks change.
	var last = '', timer;
	wp.data.subscribe(function () {
		window.clearTimeout(timer);
		timer = window.setTimeout(function () {
			var now = readable();
			if (now === last) {
				return;
			}
			last = now;
			if (window.rankMathEditor && typeof window.rankMathEditor.refresh === 'function') {
				window.rankMathEditor.refresh('content');
			}
			if (window.YoastSEO && window.YoastSEO.app && window.YoastSEO.app.pluginReloaded) {
				window.YoastSEO.app.pluginReloaded('bigspySeoContent');
			}
		}, 800);
	});
})(window.wp);
