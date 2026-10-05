/* BIGSPY Studio — searchable pattern library for the WordPress block editor. */
(function (wp) {
	'use strict';

	var el = wp.element.createElement;
	var useState = wp.element.useState;
	var __ = wp.i18n.__;
	var PluginSidebar = wp.editPost.PluginSidebar;
	var Button = wp.components.Button;
	var SearchControl = wp.components.SearchControl;
	var patterns = (window.bstPatternStudioData || {}).patterns || [];
	var groups = [
		{ id: 'all', label: __('All designs', 'bigspy-theme') },
		{ id: 'bigspy-pages', label: __('Full pages', 'bigspy-theme') },
		{ id: 'bigspy-sections', label: __('Sections', 'bigspy-theme') },
		{ id: 'bigspy-shop', label: __('Shop', 'bigspy-theme') }
	];

	function Preview(props) {
		var kind = (props.pattern.categories || []).indexOf('bigspy-shop') !== -1 ? 'shop' :
			((props.pattern.categories || []).indexOf('bigspy-pages') !== -1 ? 'page' : 'section');
		return el('div', { className: 'bst-pattern-preview bst-pattern-preview--' + kind, 'aria-hidden': 'true' },
			el('div', { className: 'bst-pattern-preview__nav' }, el('i'), el('i'), el('i'), el('b')),
			el('div', { className: 'bst-pattern-preview__hero' }, el('i'), el('b'), el('b'), el('span')),
			el('div', { className: 'bst-pattern-preview__blocks' }, el('i'), el('i'), el('i'))
		);
	}

	function PatternCard(props) {
		return el('article', { className: 'bst-pattern-card' },
			el(Preview, { pattern: props.pattern }),
			el('div', { className: 'bst-pattern-card__body' },
				el('h3', null, props.pattern.title),
				props.pattern.description ? el('p', null, props.pattern.description) : null,
				el(Button, {
					variant: 'secondary',
					className: 'bst-pattern-card__insert',
					onClick: function () { props.onInsert(props.pattern); }
				}, __('Add to page', 'bigspy-theme'))
			)
		);
	}

	function StudioSidebar() {
		var queryState = useState('');
		var query = queryState[0];
		var setQuery = queryState[1];
		var groupState = useState('all');
		var activeGroup = groupState[0];
		var setActiveGroup = groupState[1];
		var noticeState = useState('');
		var notice = noticeState[0];
		var setNotice = noticeState[1];
		var filtered = patterns.filter(function (pattern) {
			var matchesGroup = 'all' === activeGroup || pattern.categories.indexOf(activeGroup) !== -1;
			var haystack = (pattern.title + ' ' + pattern.description).toLocaleLowerCase();
			return matchesGroup && (!query || haystack.indexOf(query.toLocaleLowerCase()) !== -1);
		});

		function insertPattern(pattern) {
			var blocks = wp.blocks.parse(pattern.content || '');
			if (!blocks.length) {
				setNotice(__('This design could not be added. Please try another one.', 'bigspy-theme'));
				return;
			}
			wp.data.dispatch('core/block-editor').insertBlocks(blocks);
			setNotice(__('Added to your page. Edit the text and images in the preview.', 'bigspy-theme'));
		}

		return el(PluginSidebar, {
			name: 'bst-pattern-studio',
			title: __('BIGSPY Studio', 'bigspy-theme'),
			icon: 'layout'
		},
		el('div', { className: 'bst-pattern-studio' },
			el('p', { className: 'bst-pattern-studio__intro' }, __('Choose a ready-made design, then change its text, images and colors on the page.', 'bigspy-theme')),
			el(SearchControl, {
				label: __('Search designs', 'bigspy-theme'),
				value: query,
				onChange: setQuery,
				placeholder: __('Try “hero”, “services” or “products”…', 'bigspy-theme')
			}),
			el('div', { className: 'bst-pattern-groups', role: 'group', 'aria-label': __('Design categories', 'bigspy-theme') },
				groups.filter(function (group) { return 'bigspy-shop' !== group.id || patterns.some(function (p) { return p.categories.indexOf('bigspy-shop') !== -1; }); }).map(function (group) {
					return el(Button, {
						key: group.id,
						variant: activeGroup === group.id ? 'primary' : 'tertiary',
						'onClick': function () { setActiveGroup(group.id); }
					}, group.label);
				})
			),
			notice ? el('p', { className: 'bst-pattern-studio__notice', role: 'status' }, notice) : null,
			filtered.length ? el('div', { className: 'bst-pattern-list' }, filtered.map(function (pattern) {
				return el(PatternCard, { key: pattern.name, pattern: pattern, onInsert: insertPattern });
			})) : el('p', { className: 'bst-pattern-studio__empty' }, __('No designs found. Try another search.', 'bigspy-theme')),
			el('p', { className: 'bst-pattern-studio__hint' }, __('Adding a design does not publish the page. Use Preview, then Save draft or Publish when it is ready.', 'bigspy-theme'))
		));
	}

	wp.plugins.registerPlugin('bst-pattern-studio', { render: StudioSidebar });
})(window.wp);
