/**
 * BIGSPY AI List blocks (category "bigspy-list"). While BIGSPY AI List mode is off they
 * stay registered, carry a small red lock on their inserter icon and show a locked card
 * with a button to turn the mode on; the site renders nothing for them.
 */
(function (wp) {
	'use strict';
	var el = wp.element.createElement;
	var __ = wp.i18n.__;
	var c = wp.components;
	var be = wp.blockEditor;
	var T = 'bigspy-ai-theme-wp';
	var L = window.bstListing || {};
	var ON = !!L.on && L.on !== '0';
	var LOCK = el('svg', { width: 12, height: 12, viewBox: '0 0 24 24', className: 'bst-lock-badge', 'aria-hidden': true, style: { position: 'absolute', top: -4, right: -6, background: '#fff', borderRadius: 3 } },
		el('path', { fill: '#9f1239', d: 'M17 9V7a5 5 0 0 0-10 0v2H5v13h14V9h-2zm-8 0V7a3 3 0 0 1 6 0v2H9z' }));

	function LockedCard(props) {
		var busy = wp.element.useState(false);
		var remove = wp.data.useDispatch('core/block-editor').removeBlock;
		function turnOn() {
			busy[1](true);
			wp.apiFetch({ path: '/bst/v1/feature', method: 'POST', data: { id: 'listing', on: true } }).then(function () {
				// Unsaved posts make the browser ask before leaving, so say what happened first.
				wp.data.dispatch('core/notices').createSuccessNotice(__('BIGSPY AI List mode is on. Reload the editor to use this block.', T), { type: 'snackbar' });
				window.location.reload();
			}, function () {
				busy[1](false);
			});
		}
		return el('div', be.useBlockProps({ className: 'bst-listing-locked' }), el(c.Placeholder, {
			icon: el('span', { style: { position: 'relative', display: 'inline-flex' } }, el(c.Dashicon, { icon: 'lock' })),
			label: __('BIGSPY AI List block', T),
			instructions: __('Turn on BIGSPY AI List mode in the theme settings to use this block. It shows nothing on the site until then.', T)
		},
			L.canManage ? el(c.Button, { variant: 'primary', isBusy: busy[0], disabled: busy[0], onClick: turnOn }, __('Turn on BIGSPY AI List', T)) : null,
			L.canManage ? el(c.Button, { variant: 'link', href: L.settings, target: '_blank' }, __('Open settings', T)) : null,
			el(c.Button, { variant: 'tertiary', isDestructive: true, onClick: function () { remove(props.clientId); } }, __('Remove block', T))
		));
	}

	// After the preview filter (priority 5), so the colored icon gets the lock on top.
	wp.hooks.addFilter('blocks.registerBlockType', 'bst/listing-lock', function (settings) {
		if (settings.category !== 'bigspy-list' || ON) {
			return settings;
		}
		var icon = settings.icon && settings.icon.src ? settings.icon : { src: settings.icon };
		var src = typeof icon.src === 'string' ? el(c.Dashicon, { icon: icon.src }) : icon.src;
		settings.icon = Object.assign({}, icon, { src: el('span', { style: { position: 'relative', display: 'inline-flex' } }, src, LOCK) });
		settings.description = (settings.description || '') + ' ' + __('(Locked: turn on BIGSPY AI List mode.)', T);
		var edit = settings.edit;
		settings.edit = function (props) {
			return props.attributes && props.attributes.bstPreview ? edit(props) : el(LockedCard, props);
		};
		return settings;
	}, 20);

	var Inspector = be.InspectorControls;

	/* Choices for text attributes; other attributes get a control from their type. */
	var CHOICES = {
		layout: { square: __('Square card', T), vertical: __('Vertical card', T), horizontal: __('Horizontal card', T), 'horizontal-2': __('Horizontal, two columns', T), minimal: __('Minimal', T) },
		elementType: { featured: __('Featured listings', T), latest: __('Latest listings', T), popular: __('Popular listings', T), related: __('Related listings', T), listing_grid: __('All listings', T), categories: __('Categories', T), blog: __('Blog posts', T) },
		orderBy: { date: __('Date', T), title: __('Name', T), modified: __('Last updated', T), rand: __('Random', T), name: __('Name', T), count: __('Number of listings', T) },
		order: { DESC: __('Newest / Z–A first', T), ASC: __('Oldest / A–Z first', T) },
		alignment: { left: __('Left', T), center: __('Center', T), right: __('Right', T) },
		showWatermark: { 'default': __('Site default', T), show: __('Show', T), hide: __('Hide', T) },
		showFeatured: { 'default': __('Site default', T), show: __('Show', T), hide: __('Hide', T) },
		buttonStyle: { 'default': __('Site default', T), none: __('No button: the whole card is the link', T), soft: __('Soft outline button', T), solid: __('Solid button', T) },
		openNewTab: { 'default': __('Site default', T), yes: __('New tab', T), no: __('Same tab', T) },
		sponsoredPosition: { between: __('Between rows', T), top: __('Above the grid', T) },
		displayMode: { grid: __('Grid', T), slider: __('Slider (one row with arrows)', T) },
		displayLocation: { normal: __('Page content', T), 'homepage-sidebar': __('Sidebar (compact)', T) },
		ctaPosition: { bottom: __('Under the text', T), top: __('Beside the title', T) },
		ctaAlignment: { left: __('Left', T), center: __('Center', T), right: __('Right', T) },
		seeMoreAlignment: { left: __('Left', T), center: __('Center', T), right: __('Right', T) },
		viewMoreBehavior: { redirect: __('Open a page', T), load: __('Load more here', T) },
		viewMoreDestination: { automatic: __('Automatic', T), listings: __('Listings page', T), featured: __('Featured page', T), popular: __('Popular page', T), categories: __('Categories page', T), blog: __('Blog page', T), custom: __('Custom URL', T) },
		blogLayout: { cards: __('Cards', T), compact: __('Compact rows', T), titles: __('Numbered titles', T) },
		categoryHoverShadow: { none: __('None', T), soft: __('Soft', T), medium: __('Medium', T), strong: __('Strong', T) }
	};
	var LABELS = {
		columnsDesktop: __('Columns – desktop', T), columnsTablet: __('Columns – tablet', T), columnsMobile: __('Columns – mobile', T),
		limit: __('Number of listings (0 = all)', T), count: __('Number of items', T), categoryId: __('Category ID (0 = all)', T), parent: __('Only children of category ID', T),
		startPosition: __('Start at item', T), maxWidth: __('Maximum width (px)', T), featuredOnly: __('Featured only', T), showSponsored: __('Sponsored slot', T),
		showWatermark: __('Logo watermark', T), showFeatured: __('Featured badge', T), showCTA: __('Visit button', T), titleLines: __('Title lines', T), descriptionLines: __('Description lines', T),
		buttonStyle: __('Button', T), buttonText: __('Button text (empty = site default)', T), showLabel: __('"Sponsored" label', T), openNewTab: __('Open the partner in', T),
		sponsoredPosition: __('Sponsored position', T), sponsoredEvery: __('Sponsored after every N rows', T),
		elementType: __('Show', T), displayMode: __('Display', T), displayLocation: __('Placed in', T), gap: __('Gap between items (px)', T), relatedContextId: __('Related to listing ID (0 = current)', T),
		ctaPosition: __('Visit button position', T), ctaAlignment: __('Visit button alignment', T),
		showViewMore: __('View more', T), viewMoreText: __('View more text', T), viewMoreBehavior: __('When clicked', T), viewMoreDestination: __('Go to', T), viewMoreUrl: __('Custom URL', T), seeMoreAlignment: __('View more alignment', T),
		blogLayout: __('Layout', T), blogCategoryId: __('Post category ID (0 = all)', T), blogExcerptWords: __('Excerpt words', T), blogShowViewMore: __('View more', T), blogViewMoreText: __('View more text', T),
		categoryCount: __('Number of categories', T), categoryColumnsDesktop: __('Columns – desktop', T), categoryColumnsTablet: __('Columns – tablet', T), categoryColumnsMobile: __('Columns – mobile', T),
		categoryShowViewMore: __('View all link', T), categoryViewMoreText: __('View all text', T), categoryBorderEnabled: __('Border', T), categoryBorderWidth: __('Border width (px)', T), categoryBorderColor: __('Border color', T), categoryBorderRadius: __('Corner radius (px)', T),
		buttonColor: __('Button color', T), buttonTextColor: __('Button text color', T), borderColor: __('Border color', T), radius: __('Corner radius (px)', T), height: __('Height (px, 0 = default)', T),
		categoryHoverEnabled: __('Hover effect', T), categoryHoverBorderWidth: __('Hover border width (px)', T), categoryHoverBorderColor: __('Hover border color', T), categoryHoverLift: __('Hover lift (px)', T), categoryHoverImageZoom: __('Hover image zoom (1–1.3)', T), categoryHoverShadow: __('Hover shadow', T), categoryHoverTransition: __('Hover speed (ms)', T)
	};
	function label(key) {
		return LABELS[key] || key.replace(/^show/, '').replace(/([A-Z])/g, ' $1').replace(/^./, function (s) { return s.toUpperCase(); }).trim();
	}

	/* Listing elements: options in groups, each shown only for the content types it applies to. */
	var LISTINGS = ['featured', 'latest', 'popular', 'related', 'listing_grid'];
	var PANELS = {
		'bigspy/elements': [
			{ title: __('Content', T), keys: ['elementType', 'title', 'count', 'startPosition', 'categoryId', 'orderBy', 'order', 'relatedContextId', 'blogCategoryId', 'displayLocation'] },
			{ title: __('Layout', T), types: LISTINGS.concat(['blog']), keys: ['displayMode', 'layout', 'columnsDesktop', 'columnsTablet', 'columnsMobile', 'gap'] },
			{ title: __('Card parts', T), types: LISTINGS, keys: ['showLogo', 'showDescription', 'showCategory', 'showPricing', 'showRating', 'showTags', 'showFeatured', 'showWatermark', 'titleLines', 'descriptionLines', 'showCTA', 'ctaPosition', 'ctaAlignment'] },
			{ title: __('Sponsored', T), types: LISTINGS, keys: ['showSponsored', 'sponsoredPosition', 'sponsoredEvery'] },
			{ title: __('Blog posts', T), types: ['blog'], keys: ['blogLayout', 'blogShowImage', 'blogShowDate', 'blogShowAuthor', 'blogShowCategory', 'blogShowExcerpt', 'blogExcerptWords', 'blogShowNumber'] },
			{ title: __('Categories', T), types: ['categories'], keys: ['categoryCount', 'categoryColumnsDesktop', 'categoryColumnsTablet', 'categoryColumnsMobile', 'categoryShowImage', 'categoryShowCount', 'categoryShowDescription'] },
			{ title: __('Category style', T), types: ['categories'], keys: ['categoryBorderEnabled', 'categoryBorderWidth', 'categoryBorderColor', 'categoryBorderRadius', 'categoryHoverEnabled', 'categoryHoverBorderWidth', 'categoryHoverBorderColor', 'categoryHoverLift', 'categoryHoverImageZoom', 'categoryHoverShadow', 'categoryHoverTransition'] },
			{ title: __('View more', T), keys: ['showViewMore', 'blogShowViewMore', 'categoryShowViewMore', 'viewMoreText', 'blogViewMoreText', 'categoryViewMoreText', 'viewMoreBehavior', 'viewMoreDestination', 'viewMoreUrl', 'seeMoreAlignment'] }
		]
	};

	/* Options that only make sense for some content types or with other settings. */
	function relevant(k, a) {
		var t = a.elementType || 'featured';
		var list = LISTINGS.indexOf(t) > -1;
		var blogMore = t === 'blog' && a.blogShowViewMore !== false;
		var listMore = list && a.showViewMore !== false;
		var rules = {
			categoryId: list && t !== 'related', orderBy: list && t !== 'popular', order: list && t !== 'popular', relatedContextId: t === 'related',
			blogCategoryId: t === 'blog', displayLocation: t === 'blog', count: t !== 'categories', startPosition: t !== 'categories',
			layout: list, displayMode: list, ctaPosition: !!a.showCTA, ctaAlignment: !!a.showCTA && a.ctaPosition !== 'top',
			sponsoredPosition: a.showSponsored !== false && a.displayMode !== 'slider',
			sponsoredEvery: a.showSponsored !== false && a.displayMode !== 'slider' && a.sponsoredPosition !== 'top',
			blogExcerptWords: !!a.blogShowExcerpt, blogShowNumber: a.blogLayout === 'titles' || a.displayLocation === 'homepage-sidebar',
			showViewMore: list, viewMoreText: listMore, blogShowViewMore: t === 'blog', blogViewMoreText: blogMore,
			categoryShowViewMore: t === 'categories', categoryViewMoreText: t === 'categories' && a.categoryShowViewMore !== false,
			viewMoreBehavior: listMore || blogMore,
			viewMoreDestination: (listMore || blogMore) && a.viewMoreBehavior !== 'load',
			viewMoreUrl: (listMore || blogMore) && a.viewMoreDestination === 'custom' && a.viewMoreBehavior !== 'load',
			seeMoreAlignment: listMore || blogMore || (t === 'categories' && a.categoryShowViewMore !== false),
			categoryBorderWidth: a.categoryBorderEnabled !== false, categoryBorderColor: a.categoryBorderEnabled !== false
		};
		if (/^categoryHover/.test(k) && k !== 'categoryHoverEnabled') { return a.categoryHoverEnabled !== false; }
		return Object.prototype.hasOwnProperty.call(rules, k) ? rules[k] : true;
	}

	function control(k, a, attrs, props) {
		var set = function (v) { var o = {}; o[k] = v; props.setAttributes(o); };
		var common = { key: k, label: label(k), __nextHasNoMarginBottom: true };
		if (attrs[k].type === 'boolean') { return el(c.ToggleControl, Object.assign(common, { checked: !!a[k], onChange: set })); }
		if (attrs[k].type === 'number') {
			var isFloat = String(attrs[k].default).indexOf('.') > -1;
			return el(c.TextControl, Object.assign(common, { type: 'number', step: isFloat ? 0.01 : 1, value: a[k], onChange: function (v) { set(isFloat ? (parseFloat(v) || 0) : (parseInt(v, 10) || 0)); } }));
		}
		if (CHOICES[k]) {
			return el(c.SelectControl, Object.assign(common, { value: a[k], onChange: set, options: Object.keys(CHOICES[k]).map(function (v) { return { value: v, label: CHOICES[k][v] }; }) }));
		}
		if (/Color$/.test(k)) {
			return el(c.BaseControl, { key: k, label: label(k), __nextHasNoMarginBottom: true }, el(c.ColorPalette, { value: a[k] || undefined, onChange: function (v) { set(v || ''); }, clearable: true }));
		}
		return el(c.TextControl, Object.assign(common, { value: a[k], onChange: set }));
	}

	function ssrBlock(name) {
		wp.blocks.registerBlockType(name, {
			edit: function (props) {
				var a = props.attributes;
				var attrs = wp.blocks.getBlockType(name).attributes;
				var panels;
				if (PANELS[name]) {
					var type = a.elementType || 'featured';
					panels = PANELS[name].filter(function (p) { return !p.types || p.types.indexOf(type) > -1; }).map(function (p, i) {
						var rows = p.keys.filter(function (k) { return attrs[k] && relevant(k, a); }).map(function (k) { return control(k, a, attrs, props); });
						return rows.length ? el(c.PanelBody, { key: p.title, title: p.title, initialOpen: i === 0 }, rows) : null;
					});
				} else {
					panels = el(c.PanelBody, { title: __('Settings', T) }, Object.keys(attrs).filter(function (k) { return !/^(bs|bst|mx|className|lock|metadata|anchor)/.test(k); }).map(function (k) { return control(k, a, attrs, props); }));
				}
				return el(wp.element.Fragment, null,
					el(Inspector, null, panels),
					el('div', be.useBlockProps(), el(wp.serverSideRender, {
						block: name,
						attributes: a,
						httpMethod: 'POST',
						EmptyResponsePlaceholder: function () {
							return el(c.Placeholder, { icon: 'list-view', label: wp.blocks.getBlockType(name).title, instructions: __('Nothing to show yet: add listings (WooCommerce products) or change the settings.', T) });
						}
					}))
				);
			},
			save: function () { return null; }
		});
	}
	['bigspy/listing-grid', 'bigspy/category-grid', 'bigspy/search', 'bigspy/elements', 'bigspy/sponsored'].forEach(ssrBlock);
	wp.blocks.registerBlockType('bst/listing-info', {
		edit: function (props) {
			var a = props.attributes;
			var set = function (k) { return function (v) { var o = {}; o[k] = v; props.setAttributes(o); }; };
			return el(wp.element.Fragment, null,
				el(Inspector, null, el(c.PanelBody, { title: __('Listing info', T) },
					el(c.TextControl, { label: __('Listing (product) ID, 0 = current product', T), type: 'number', value: a.productId, onChange: function (v) { props.setAttributes({ productId: parseInt(v, 10) || 0 }); }, __nextHasNoMarginBottom: true }),
					el(c.ToggleControl, { label: __('Tagline', T), checked: a.showTagline, onChange: set('showTagline'), __nextHasNoMarginBottom: true }),
					el(c.ToggleControl, { label: __('Facts', T), checked: a.showFacts, onChange: set('showFacts'), __nextHasNoMarginBottom: true }),
					el(c.ToggleControl, { label: __('Visit button', T), checked: a.showButton, onChange: set('showButton'), __nextHasNoMarginBottom: true }),
					el(c.RangeControl, { label: __('Facts shown', T), min: 1, max: 20, value: a.limit, onChange: set('limit') })
				)),
				el('div', be.useBlockProps(), el(wp.serverSideRender, {
					block: 'bst/listing-info',
					attributes: a,
					httpMethod: 'POST',
					EmptyResponsePlaceholder: function () {
						return el(c.Placeholder, { icon: 'list-view', label: __('Listing info', T), instructions: __('Pick a product that has listing information, or place this block in a product template.', T) });
					}
				}))
			);
		},
		save: function () { return null; }
	});
})(window.wp);
