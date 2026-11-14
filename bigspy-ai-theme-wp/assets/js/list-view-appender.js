/**
 * List View "+ Add to …" rows.
 *
 * Under the last child of every expanded container block in the editor's
 * List View, adds a small "+ Add to <Block>" button. Clicking it opens the
 * block inserter targeted at the end of that container; from the inserter
 * the user can click a block to add it there, or drag it anywhere on the canvas.
 *
 * Purely an editor UI helper: it never changes post content by itself.
 * If WordPress changes the List View markup, the rows simply do not appear.
 */
( function ( wp ) {
	'use strict';
	if ( ! wp || ! wp.data || ! wp.blocks ) {
		return;
	}
	var cfg = window.bstListViewAppender || {};
	var LABEL = cfg.label || 'Add to %s';
	var ROW = 'bst-lv-appender';
	var busy = false;
	var queued = false;

	function be() {
		return wp.data.select( 'core/block-editor' );
	}

	function openInserter( rootClientId ) {
		var index = be().getBlockCount( rootClientId );
		var target = { rootClientId: rootClientId, insertionIndex: index };
		var stores = [ 'core/editor', 'core/edit-post', 'core/edit-site' ];
		for ( var i = 0; i < stores.length; i++ ) {
			var d = wp.data.dispatch( stores[ i ] );
			if ( d && typeof d.setIsInserterOpened === 'function' ) {
				// Close first so a re-click retargets an already open inserter.
				d.setIsInserterOpened( false );
				d.setIsInserterOpened( target );
				return;
			}
		}
	}

	function title( clientId ) {
		var name = be().getBlockName( clientId );
		var type = name && wp.blocks.getBlockType( name );
		var t = ( type && type.title ) || name || '';
		var custom = be().getBlockAttributes( clientId );
		if ( custom && custom.metadata && custom.metadata.name ) {
			t = custom.metadata.name;
		}
		return t;
	}

	function makeRow( clientId, level ) {
		var tr = document.createElement( 'tr' );
		tr.className = ROW;
		tr.setAttribute( 'data-bst-root', clientId );
		var td = document.createElement( 'td' );
		td.colSpan = 3;
		var btn = document.createElement( 'button' );
		btn.type = 'button';
		btn.className = ROW + '__btn';
		btn.style.marginInlineStart = ( level * 16 + 8 ) + 'px';
		btn.innerHTML = '<span aria-hidden="true">+</span> ';
		btn.appendChild( document.createTextNode( LABEL.replace( '%s', title( clientId ) ) ) );
		btn.addEventListener( 'click', function ( e ) {
			e.preventDefault();
			e.stopPropagation();
			openInserter( clientId );
		} );
		td.appendChild( btn );
		tr.appendChild( td );
		return tr;
	}

	function render() {
		queued = false;
		busy = true;
		try {
			document.querySelectorAll( 'tr.' + ROW ).forEach( function ( n ) {
				n.remove();
			} );
			document.querySelectorAll( '.block-editor-list-view-tree' ).forEach( function ( tree ) {
				var rows = Array.prototype.slice.call( tree.querySelectorAll( 'tr.block-editor-list-view-leaf' ) );
				rows.forEach( function ( row, i ) {
					if ( row.getAttribute( 'aria-expanded' ) !== 'true' ) {
						return;
					}
					var id = row.getAttribute( 'data-block' );
					var level = parseInt( row.getAttribute( 'aria-level' ), 10 ) || 1;
					if ( ! id || ! be().canInsertBlocks || ! be().canInsertBlocks( id ) ) {
						return;
					}
					if ( be().getTemplateLock && be().getTemplateLock( id ) ) {
						return;
					}
					// Last row that is a descendant of this one.
					var last = row;
					for ( var j = i + 1; j < rows.length; j++ ) {
						var l = parseInt( rows[ j ].getAttribute( 'aria-level' ), 10 ) || 1;
						if ( l <= level ) {
							break;
						}
						last = rows[ j ];
					}
					last.parentNode.insertBefore( makeRow( id, level ), last.nextSibling );
				} );
			} );
		} catch ( err ) {
			// Never break the editor over a UI nicety.
		}
		busy = false;
	}

	function schedule() {
		if ( busy || queued ) {
			return;
		}
		queued = true;
		window.requestAnimationFrame( render );
	}

	function start() {
		new window.MutationObserver( function ( list ) {
			if ( busy ) {
				return;
			}
			for ( var i = 0; i < list.length; i++ ) {
				var t = list[ i ].target;
				if ( t.closest && t.closest( '.block-editor-list-view-tree, .editor-list-view-sidebar, .edit-post-editor__list-view-panel' ) ) {
					schedule();
					return;
				}
				var added = list[ i ].addedNodes;
				for ( var k = 0; k < added.length; k++ ) {
					if ( added[ k ].nodeType === 1 && added[ k ].querySelector && added[ k ].querySelector( '.block-editor-list-view-tree' ) ) {
						schedule();
						return;
					}
				}
			}
		} ).observe( document.body, { childList: true, subtree: true, attributes: true, attributeFilter: [ 'aria-expanded' ] } );
		// Block renames / moves that do not touch the visible DOM.
		wp.data.subscribe( schedule );
	}

	if ( document.body ) {
		start();
	} else {
		document.addEventListener( 'DOMContentLoaded', start );
	}
} )( window.wp );
