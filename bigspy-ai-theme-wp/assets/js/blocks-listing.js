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
