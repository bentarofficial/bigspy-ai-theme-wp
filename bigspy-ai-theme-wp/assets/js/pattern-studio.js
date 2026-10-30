/* BIGSPY Studio — searchable pattern library for the WordPress block editor. */
(function (wp) {
	'use strict';

	var el = wp.element.createElement;
	var useState = wp.element.useState;
	var useSelect = wp.data.useSelect;
	var __ = wp.i18n.__;
	var PluginSidebar = wp.editPost.PluginSidebar;
	var Button = wp.components.Button;
	var SearchControl = wp.components.SearchControl;
	var patterns = (window.bstPatternStudioData || {}).patterns || [];
	var groups = [
		{ id: 'all', label: __('All designs', 'bigspy-ai-theme-wp') },
		{ id: 'bigspy-pages', label: __('Full pages', 'bigspy-ai-theme-wp') },
		{ id: 'bigspy-sections', label: __('Sections', 'bigspy-ai-theme-wp') },
		{ id: 'bigspy-shop', label: __('Shop', 'bigspy-ai-theme-wp') },
		{ id: 'saved', label: __('Saved designs', 'bigspy-ai-theme-wp') }
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
				}, __('Add to page', 'bigspy-ai-theme-wp'))
			)
		);
	}

	function StudioSidebar() {
		var savedPatterns = useSelect(function (select) {
			return select('core').getEntityRecords('postType', 'wp_block', {
				per_page: 100,
				orderby: 'title',
				order: 'asc',
				status: 'publish'
			}) || [];
		}, []);
		var queryState = useState('');
		var query = queryState[0];
		var setQuery = queryState[1];
		var groupState = useState('all');
		var activeGroup = groupState[0];
		var setActiveGroup = groupState[1];
		var noticeState = useState('');
		var notice = noticeState[0];
		var setNotice = noticeState[1];
		var sourcePatterns = 'saved' === activeGroup ? savedPatterns.map(function (pattern) {
			return {
				name: 'saved-' + pattern.id,
				id: pattern.id,
				title: pattern.title && pattern.title.rendered ? pattern.title.rendered : __('Untitled design', 'bigspy-ai-theme-wp'),
				description: __('A reusable design saved in WordPress.', 'bigspy-ai-theme-wp'),
				categories: ['saved'],
				synced: true
			};
		}) : patterns;
		var filtered = sourcePatterns.filter(function (pattern) {
			var matchesGroup = 'all' === activeGroup || pattern.categories.indexOf(activeGroup) !== -1;
			var haystack = (pattern.title + ' ' + pattern.description).toLocaleLowerCase();
			return matchesGroup && (!query || haystack.indexOf(query.toLocaleLowerCase()) !== -1);
		});

		function insertPattern(pattern) {
			if (pattern.synced && pattern.id) {
				wp.data.dispatch('core/block-editor').insertBlocks([wp.blocks.createBlock('core/block', { ref: pattern.id })]);
				setNotice(__('Added as a synced design. Changes to its source update every copy.', 'bigspy-ai-theme-wp'));
				return;
			}
			var blocks = wp.blocks.parse(pattern.content || '');
			if (!blocks.length) {
				setNotice(__('This design could not be added. Please try another one.', 'bigspy-ai-theme-wp'));
				return;
			}
			wp.data.dispatch('core/block-editor').insertBlocks(blocks);
			setNotice(__('Added to your page. Edit the text and images in the preview.', 'bigspy-ai-theme-wp'));
		}

		return el(PluginSidebar, {
			name: 'bst-pattern-studio',
			title: __('BIGSPY AI Studio', 'bigspy-ai-theme-wp'),
			icon: 'layout'
		},
		el('div', { className: 'bst-pattern-studio' },
			el(SelectedBlockTools),
			el('p', { className: 'bst-pattern-studio__intro' }, __('Choose a ready-made design, then change its text, images and colors on the page.', 'bigspy-ai-theme-wp')),
			el(SearchControl, {
				label: __('Search designs', 'bigspy-ai-theme-wp'),
				value: query,
				onChange: setQuery,
				placeholder: __('Try “hero”, “services” or “products”…', 'bigspy-ai-theme-wp')
			}),
			el('div', { className: 'bst-pattern-groups', role: 'group', 'aria-label': __('Design categories', 'bigspy-ai-theme-wp') },
				groups.filter(function (group) {
					if ('bigspy-shop' === group.id) return patterns.some(function (p) { return p.categories.indexOf('bigspy-shop') !== -1; });
					return true;
				}).map(function (group) {
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
			})) : el('p', { className: 'bst-pattern-studio__empty' }, 'saved' === activeGroup ? __('No saved designs yet. In the editor, open a block’s options and choose Create pattern to save a design here.', 'bigspy-ai-theme-wp') : __('No designs found. Try another search.', 'bigspy-ai-theme-wp')),
			el('p', { className: 'bst-pattern-studio__hint' }, __('Adding a design does not publish the page. Use Preview, then Save draft or Publish when it is ready.', 'bigspy-ai-theme-wp'))
		));
	}

	function SelectedBlockTools() {
		var selected = useSelect(function (select) {
			var store = select('core/block-editor');
			var block = store.getSelectedBlock();
			if (!block) return null;
			var root = store.getBlockRootClientId(block.clientId);
			return { block: block, root: root, index: store.getBlockIndex(block.clientId, root), count: store.getBlockOrder(root).length, locked: !store.canMoveBlock(block.clientId), canEdit: store.canEditBlock(block.clientId), canInsert: store.canInsertBlockType(block.name, root) };
		}, []);
		var copiedState = useState(null);
		var copied = copiedState[0];
		var setCopied = copiedState[1];
		var keys = ['style', 'backgroundColor', 'textColor', 'fontSize', 'bsHideDesktop', 'bsHideTablet', 'bsHideMobile', 'bsAnimation', 'bsAnimationDelay', 'bsAnimationDuration', 'bsHoverEffect'];
		if (!selected) return el('p', null, __('Select a block in the page to edit its layout.', 'bigspy-ai-theme-wp'));
		var block = selected.block;
		var actions = wp.data.dispatch('core/block-editor');
		var definition = wp.blocks.getBlockType(block.name);
		var title = definition ? definition.title : block.name;
		return el('section', { className: 'bst-studio-tools' },
			el('strong', null, title),
			el('div', { className: 'bst-studio-tools__actions' },
				el(Button, { variant: 'secondary', disabled: !selected.canInsert, onClick: function () {
					var copy = wp.blocks.cloneBlock(block, { anchor: undefined });
					actions.insertBlocks(copy, selected.index + 1, selected.root);
					actions.selectBlock(copy.clientId);
				} }, __('Duplicate', 'bigspy-ai-theme-wp')),
				el(Button, { variant: 'secondary', disabled: selected.locked || selected.index === 0, onClick: function () { actions.moveBlocksUp([block.clientId], selected.root); } }, __('Move up', 'bigspy-ai-theme-wp')),
				el(Button, { variant: 'secondary', disabled: selected.locked || selected.index >= selected.count - 1, onClick: function () { actions.moveBlocksDown([block.clientId], selected.root); } }, __('Move down', 'bigspy-ai-theme-wp')),
				el(Button, { variant: 'secondary', onClick: function () {
					var value = {};
					keys.forEach(function (key) { if (typeof block.attributes[key] !== 'undefined') value[key] = JSON.parse(JSON.stringify(block.attributes[key])); });
					setCopied(value);
				} }, __('Copy appearance', 'bigspy-ai-theme-wp')),
				el(Button, { variant: 'secondary', disabled: !copied || !selected.canEdit, onClick: function () {
					var value = {};
					Object.keys(copied).forEach(function (key) { if (definition && definition.attributes && definition.attributes[key]) value[key] = JSON.parse(JSON.stringify(copied[key])); });
					actions.updateBlockAttributes(block.clientId, value);
				} }, __('Paste appearance', 'bigspy-ai-theme-wp'))
			),
			el('p', null, __('Use the block menu to create a reusable pattern. Saved patterns appear under Saved designs.', 'bigspy-ai-theme-wp'))
		);
	}

	wp.plugins.registerPlugin('bst-pattern-studio', { render: StudioSidebar });
})(window.wp);
