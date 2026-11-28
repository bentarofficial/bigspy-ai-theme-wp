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
	var AFTER = cfg.after || 'Add section or element';
	var ROW = 'bst-lv-appender';
	var busy = false;
	var queued = false;

	function be() {
		return wp.data.select( 'core/block-editor' );
	}

	// Full inserter panel: fallback when the quick popup is not available.
	function openPanel( rootClientId, index ) {
		if ( typeof index !== 'number' ) {
			index = be().getBlockCount( rootClientId );
		}
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

	var host = null;
	var hostRoot = null;

	// The hover preview is a floating popover; while the dialog is open it is
	// pinned into the empty right half of the dialog (see editor-ui.css).
	function placePreview() {
		var frame = document.querySelector( '.bst-insert-dialog' );
		var side = frame && frame.querySelector( '.block-editor-tabbed-sidebar' );
		if ( ! frame || ! side ) {
			return;
		}
		var f = frame.getBoundingClientRect();
		var sb = side.getBoundingClientRect();
		var st = document.body.style;
		st.setProperty( '--bst-pv-left', ( sb.right + 24 ) + 'px' );
		st.setProperty( '--bst-pv-top', ( sb.top + 16 ) + 'px' );
		st.setProperty( '--bst-pv-w', Math.max( 240, f.right - sb.right - 48 ) + 'px' );
		st.setProperty( '--bst-pv-h', Math.max( 200, f.bottom - sb.top - 32 ) + 'px' );
	}

	function closeQuick() {
		document.body.classList.remove( 'bst-insert-open' );
		window.removeEventListener( 'resize', placePreview );
		if ( hostRoot ) {
			hostRoot.unmount();
			hostRoot = null;
		}
		if ( host ) {
			host.remove();
			host = null;
		}
	}

	/*
	 * Large block library in a dialog at the centre of the screen: search,
	 * block categories, patterns and media, like the left inserter panel but
	 * roomier. opts.rootClientId: container to insert into; opts.clientId:
	 * insert right after this block (else at the end of the container).
	 */
	function quickInsert( anchor, opts ) {
		var bed = wp.blockEditor;
		var cmp = wp.components;
		var el = wp.element && wp.element.createElement;
		var root = opts.rootClientId || undefined;
		var index = typeof opts.index === 'number' ? opts.index : ( opts.clientId ? be().getBlockIndex( opts.clientId ) + 1 : be().getBlockCount( root ) );
		var Library = bed && ( bed.__experimentalLibrary || bed.InserterLibrary );
		if ( ! Library || ! cmp || ! cmp.Modal || ! el || ! wp.element.createRoot ) {
			openPanel( root, index );
			return;
		}
		closeQuick();
		host = document.createElement( 'div' );
		host.className = 'bst-quick-insert';
		document.body.appendChild( host );
		hostRoot = wp.element.createRoot( host );
		var done = function () { window.setTimeout( closeQuick, 0 ); };
		document.body.classList.add( 'bst-insert-open' );
		window.addEventListener( 'resize', placePreview );
		[ 50, 300, 800 ].forEach( function ( t ) { window.setTimeout( placePreview, t ); } );
		hostRoot.render( el( cmp.Modal, {
			title: cfg.dialog || 'Add a block',
			className: 'bst-insert-dialog',
			onRequestClose: done,
			size: 'large'
		}, el( Library, {
			rootClientId: root,
			__experimentalInsertionIndex: index,
			isAppender: ! opts.clientId && typeof opts.index !== 'number',
			showInserterHelpPanel: true,
			shouldFocusBlock: true,
			onSelect: done
		} ) ) );
	}
	window.bstQuickInsert = quickInsert;

	/*
	 * Every "+" in the editor opens the same centred library:
	 * - the "+" in the top toolbar, "Browse all" and anything else that opens
	 *   the left inserter panel (the panel is closed again at once);
	 * - the black "+" inside empty containers and the "+" between blocks,
	 *   which would otherwise open the small search popup.
	 */
	var routing = false;
	function editorStore() {
		var names = [ 'core/editor', 'core/edit-post', 'core/edit-site' ];
		for ( var i = 0; i < names.length; i++ ) {
			var sel = wp.data.select( names[ i ] );
			if ( sel && typeof sel.isInserterOpened === 'function' ) {
				return names[ i ];
			}
		}
		return '';
	}
	function targetFromSelection() {
		var sel = be().getSelectedBlockClientId && be().getSelectedBlockClientId();
		if ( sel ) {
			return { rootClientId: be().getBlockRootClientId( sel ) || '', clientId: sel };
		}
		return { rootClientId: '' };
	}
	function routePanel() {
		var name = editorStore();
		if ( routing || ! name || ! wp.blockEditor || ! ( wp.blockEditor.__experimentalLibrary || wp.blockEditor.InserterLibrary ) ) {
			return;
		}
		var open = wp.data.select( name ).isInserterOpened();
		if ( ! open ) {
			return;
		}
		routing = true;
		var t = targetFromSelection();
		var ip = be().getBlockInsertionPoint && be().getBlockInsertionPoint();
		if ( open && typeof open === 'object' && open.insertionIndex !== undefined ) {
			t = { rootClientId: open.rootClientId || '', index: open.insertionIndex };
		} else if ( ip && be().isBlockInsertionPointVisible && be().isBlockInsertionPointVisible() ) {
			t = { rootClientId: ip.rootClientId || '', index: ip.index };
		}
		wp.data.dispatch( name ).setIsInserterOpened( false );
		window.setTimeout( function () {
			quickInsert( null, t );
			routing = false;
		}, 0 );
	}
	wp.data.subscribe( routePanel );

	// Catch the open request itself, so its target (container + index) is kept.
	[ 'core/editor', 'core/edit-post', 'core/edit-site' ].forEach( function ( name ) {
		var d = wp.data.dispatch( name );
		if ( ! d || typeof d.setIsInserterOpened !== 'function' || d.setIsInserterOpened.bstRouted ) {
			return;
		}
		var orig = d.setIsInserterOpened;
		var routed = function ( value ) {
			if ( value && ! routing && wp.blockEditor && ( wp.blockEditor.__experimentalLibrary || wp.blockEditor.InserterLibrary ) ) {
				var t = targetFromSelection();
				if ( typeof value === 'object' && value.insertionIndex !== undefined ) {
					t = { rootClientId: value.rootClientId || '', index: value.insertionIndex };
				} else if ( typeof value === 'object' && value.rootClientId !== undefined ) {
					t = { rootClientId: value.rootClientId || '' };
				}
				quickInsert( null, t );
				return orig( false );
			}
			return orig( value );
		};
		routed.bstRouted = true;
		try {
			d.setIsInserterOpened = routed;
		} catch ( err ) {}
	} );

	// Clicks on the canvas "+" buttons (they live inside the editor iframe).
	function onPlus( e ) {
		var btn = e.target.closest && e.target.closest( '.block-editor-button-block-appender, .block-list-appender__toggle, .block-editor-block-list__insertion-point-inserter button, .block-editor-block-list__insertion-point-inserter, .block-editor-block-list__layout .block-editor-inserter__toggle' );
		if ( ! btn || btn.closest( '.bst-insert-dialog' ) ) {
			return;
		}
		var t;
		if ( btn.closest( '.block-editor-block-list__insertion-point-inserter' ) ) {
			var ip = be().getBlockInsertionPoint();
			t = { rootClientId: ip.rootClientId || '', index: ip.index };
		} else {
			// The container block that holds this "+" (the appender row itself also carries data-block).
			var holder = btn.closest( '[data-block]' );
			while ( holder && ! be().getBlockName( holder.getAttribute( 'data-block' ) ) ) {
				holder = holder.parentElement && holder.parentElement.closest( '[data-block]' );
			}
			t = { rootClientId: holder ? holder.getAttribute( 'data-block' ) : '' };
		}
		e.preventDefault();
		e.stopPropagation();
		quickInsert( btn, t );
	}
	function hook( doc ) {
		if ( doc && ! doc.bstPlusHooked ) {
			doc.bstPlusHooked = true;
			doc.addEventListener( 'click', onPlus, true );
			doc.addEventListener( 'mousedown', function ( e ) {
				if ( e.target.closest && e.target.closest( '.block-editor-button-block-appender, .block-list-appender__toggle, .block-editor-block-list__insertion-point-inserter, .block-editor-block-list__layout .block-editor-inserter__toggle' ) ) {
					e.stopPropagation();
				}
			}, true );
		}
	}
	hook( document );
	window.setInterval( function () {
		document.querySelectorAll( 'iframe[name="editor-canvas"]' ).forEach( function ( f ) {
			try {
				hook( f.contentDocument );
			} catch ( err ) {}
		} );
	}, 1000 );

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

	function makeRow( clientId, level, label, root ) {
		var tr = document.createElement( 'tr' );
		tr.className = ROW + ( label ? ' ' + ROW + '--after' : '' );
		tr.setAttribute( 'data-bst-root', clientId );
		var td = document.createElement( 'td' );
		td.colSpan = 3;
		var btn = document.createElement( 'button' );
		btn.type = 'button';
		btn.className = ROW + '__btn';
		btn.style.marginInlineStart = ( level * 16 + 8 ) + 'px';
		btn.innerHTML = '<span aria-hidden="true">+</span> ';
		btn.appendChild( document.createTextNode( label || LABEL.replace( '%s', title( clientId ) ) ) );
		btn.addEventListener( 'click', function ( e ) {
			e.preventDefault();
			e.stopPropagation();
			if ( label ) {
				quickInsert( btn, { rootClientId: root, clientId: clientId } );
			} else {
				quickInsert( btn, { rootClientId: clientId } );
			}
		} );
		td.appendChild( btn );
		tr.appendChild( td );
		return tr;
	}

	// WordPress 6.6+ marks open rows with data-expanded, older versions with aria-expanded.
	function isOpen( row ) {
		return row.getAttribute( 'data-expanded' ) === 'true' || row.getAttribute( 'aria-expanded' ) === 'true';
	}

	// A block that renders InnerBlocks registers list settings for itself.
	function holdsBlocks( id ) {
		return !! ( be().getBlockListSettings && be().getBlockListSettings( id ) );
	}

	function locked( id ) {
		return !! ( be().getTemplateLock && be().getTemplateLock( id ) );
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
					var id = row.getAttribute( 'data-block' );
					var level = parseInt( row.getAttribute( 'aria-level' ), 10 ) || 1;
					if ( ! id ) {
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
					var anchor = last;
					// "+ Add to <Block>" at the end of an expanded container.
					if ( isOpen( row ) && holdsBlocks( id ) && ! locked( id ) ) {
						var inner = makeRow( id, level );
						anchor.parentNode.insertBefore( inner, anchor.nextSibling );
						anchor = inner;
					}
					// "+ Add section / element" after every top-level block.
					if ( level === 1 ) {
						var root = be().getBlockRootClientId( id ) || '';
						if ( ! locked( root || undefined ) ) {
							var after = makeRow( id, 0, AFTER, root );
							anchor.parentNode.insertBefore( after, anchor.nextSibling );
						}
					}
				} );
			} );
		} catch ( err ) {
			// Never break the editor over a UI nicety.
		}
		busy = false;
	}

	// Mutations made by render() itself must not schedule another render.
	function own( m ) {
		var nodes = Array.prototype.slice.call( m.addedNodes ).concat( Array.prototype.slice.call( m.removedNodes ) );
		return m.type === 'childList' && nodes.length > 0 && nodes.every( function ( n ) {
			return n.nodeType === 1 && n.classList && n.classList.contains( ROW );
		} );
	}

	function schedule() {
		if ( busy || queued ) {
			return;
		}
		queued = true;
		// A timer, not requestAnimationFrame: it also runs when the window is in the background.
		window.setTimeout( render, 30 );
	}

	function start() {
		new window.MutationObserver( function ( list ) {
			if ( busy ) {
				return;
			}
			for ( var i = 0; i < list.length; i++ ) {
				if ( own( list[ i ] ) ) {
					continue;
				}
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
		} ).observe( document.body, { childList: true, subtree: true, attributes: true, attributeFilter: [ 'aria-expanded', 'data-expanded' ] } );
		// Block renames / moves that do not touch the visible DOM.
		wp.data.subscribe( schedule );
	}

	if ( document.body ) {
		start();
	} else {
		document.addEventListener( 'DOMContentLoaded', start );
	}
} )( window.wp );
