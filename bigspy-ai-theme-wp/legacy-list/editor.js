/* Legacy BIGSPY AI List blocks: display only (G-88). Saved markup must match the old theme:
 * container blocks save their inner blocks, the rest are dynamic (save null). */
( function ( wp ) {
	if ( ! wp || ! wp.blocks ) {
		return;
	}
	var el = wp.element.createElement;
	var be = wp.blockEditor;
	var __ = wp.i18n.__;
	var SSR = wp.serverSideRender;
	var containers = [ 'section', 'row', 'column', 'button-group', 'tabs', 'tab' ];
	var leaves = [ 'heading', 'text', 'button', 'code', 'callout', 'badge', 'divider', 'home-seo-divider' ];
	var note = __( 'Legacy BIGSPY AI List block (display only). Use the BIGSPY AI Builder blocks for new content.', 'bigspy-ai-theme-wp' );

	function notice() {
		return el( be.InspectorControls, {}, el( 'p', { style: { padding: '0 16px' } }, note ) );
	}

	containers.forEach( function ( name ) {
		if ( wp.blocks.getBlockType( 'bigspy/' + name ) && wp.blocks.getBlockType( 'bigspy/' + name ).edit ) {
			return;
		}
		wp.blocks.registerBlockType( 'bigspy/' + name, {
			edit: function () {
				var props = be.useInnerBlocksProps( be.useBlockProps( { className: 'bst-legacy-list bigspy-' + name } ), { templateLock: false } );
				return el( wp.element.Fragment, {}, notice(), el( 'div', props ) );
			},
			save: function () {
				return el( be.InnerBlocks.Content );
			},
		} );
	} );

	leaves.forEach( function ( name ) {
		if ( wp.blocks.getBlockType( 'bigspy/' + name ) && wp.blocks.getBlockType( 'bigspy/' + name ).edit ) {
			return;
		}
		wp.blocks.registerBlockType( 'bigspy/' + name, {
			edit: function ( p ) {
				var body = 'home-seo-divider' === name
					? el( 'hr', { title: note } )
					: el( SSR, { block: 'bigspy/' + name, attributes: p.attributes } );
				return el( wp.element.Fragment, {}, notice(), el( 'div', be.useBlockProps( { className: 'bst-legacy-list' } ), body ) );
			},
			save: function () {
				return null;
			},
		} );
	} );
} )( window.wp );
