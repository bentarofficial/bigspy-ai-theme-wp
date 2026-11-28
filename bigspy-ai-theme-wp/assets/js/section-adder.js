/**
 * Canvas "+" between sections.
 *
 * Under every top-level block in the editor canvas, shows a small round "+"
 * on the boundary line. Clicking it opens the block inserter at that spot, so
 * a new section or element goes right after the block above.
 *
 * Purely an editor UI helper: it never changes post content by itself.
 */
( function ( wp ) {
	'use strict';
	if ( ! wp || ! wp.hooks || ! wp.element || ! wp.compose || ! wp.data ) {
		return;
	}
	var el = wp.element.createElement;
	var useState = wp.element.useState;
	var cfg = window.bstSectionAdder || {};
	var LABEL = cfg.label || 'Add section or element';

	function be() {
		return wp.data.select( 'core/block-editor' );
	}

	function openInserter( rootClientId, index ) {
		var target = { rootClientId: rootClientId || undefined, insertionIndex: index };
		var stores = [ 'core/editor', 'core/edit-post', 'core/edit-site' ];
		for ( var i = 0; i < stores.length; i++ ) {
			var d = wp.data.dispatch( stores[ i ] );
			if ( d && typeof d.setIsInserterOpened === 'function' ) {
				d.setIsInserterOpened( false );
				d.setIsInserterOpened( target );
				return;
			}
		}
	}

	function Adder( props ) {
		var hover = useState( false );
		var on = hover[ 0 ];
		return el( 'div', {
			className: 'bst-section-adder',
			contentEditable: false,
			'data-bst-after': props.clientId,
			style: { position: 'relative', height: 0, zIndex: 30, display: 'flex', justifyContent: 'center', overflow: 'visible', margin: 0, padding: 0 }
		}, el( 'button', {
			type: 'button',
			title: LABEL,
			'aria-label': LABEL,
			onMouseEnter: function () { hover[ 1 ]( true ); },
			onMouseLeave: function () { hover[ 1 ]( false ); },
			onMouseDown: function ( e ) { e.preventDefault(); e.stopPropagation(); },
			onClick: function ( e ) {
				e.preventDefault();
				e.stopPropagation();
				var root = be().getBlockRootClientId( props.clientId ) || '';
				if ( window.bstQuickInsert ) {
					window.bstQuickInsert( e.currentTarget, { rootClientId: root, clientId: props.clientId } );
				} else {
					openInserter( root, be().getBlockIndex( props.clientId ) + 1 );
				}
			},
			style: {
				position: 'absolute', top: '-15px', width: '30px', height: '30px', padding: 0, borderRadius: '50%',
				border: '2px solid #fff', background: on ? '#1e1e1e' : 'var(--wp-admin-theme-color, #3858e9)', color: '#fff',
				fontSize: '20px', lineHeight: '26px', fontFamily: 'sans-serif', cursor: 'pointer',
				boxShadow: '0 2px 6px rgba(0,0,0,.25)', opacity: on ? 1 : 0.85, transition: 'background .15s, transform .15s',
				transform: on ? 'scale(1.1)' : 'none'
			}
		}, '+' ) );
	}

	var withAdder = wp.compose.createHigherOrderComponent( function ( BlockListBlock ) {
		return function ( props ) {
			var block = el( BlockListBlock, props );
			// Top-level blocks only; never inside template-locked content.
			if ( props.rootClientId || ! props.clientId ) {
				return block;
			}
			var lock = be().getTemplateLock && be().getTemplateLock();
			if ( lock ) {
				return block;
			}
			return el( wp.element.Fragment, null, block, el( Adder, { clientId: props.clientId } ) );
		};
	}, 'withBstSectionAdder' );

	wp.hooks.addFilter( 'editor.BlockListBlock', 'bst/section-adder', withAdder, 20 );
} )( window.wp );
