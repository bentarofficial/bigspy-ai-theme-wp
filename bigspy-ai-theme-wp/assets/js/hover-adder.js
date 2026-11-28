/**
 * "+" on the nearest edge of any block under the mouse (editor canvas).
 *
 * Move the mouse over any block: a blue line with a round "+" appears on the
 * edge closest to the pointer, with the block name in a small label. Blocks
 * laid out side by side (columns, cards) get the line on their left or right
 * edge, stacked blocks on their top or bottom edge. Clicking the "+" opens the
 * block library (window.bstQuickInsert) to add a block exactly there, next to
 * the hovered block and at the same level.
 *
 * Purely an editor UI helper: it never changes post content by itself.
 */
( function ( wp ) {
	'use strict';
	if ( ! wp || ! wp.data || ! wp.blocks ) {
		return;
	}
	var cfg = window.bstHoverAdder || {};
	var LABEL = cfg.label || 'Add a block here';
	var COLOR = '#0ea5e9';

	function be() {
		return wp.data.select( 'core/block-editor' );
	}

	function blockTitle( id ) {
		var name = be().getBlockName( id );
		var type = name && wp.blocks.getBlockType( name );
		var a = be().getBlockAttributes( id ) || {};
		return ( a.metadata && a.metadata.name ) || ( type && type.title ) || '';
	}

	// The innermost real block element under the pointer.
	function blockAt( node ) {
		var el = node && node.closest ? node.closest( '[data-block]' ) : null;
		while ( el && ! be().getBlockName( el.getAttribute( 'data-block' ) ) ) {
			el = el.parentElement && el.parentElement.closest( '[data-block]' );
		}
		return el;
	}

	// Side by side when a sibling block sits on the same line.
	function isHorizontal( el, id ) {
		var root = be().getBlockRootClientId( id );
		var set = root && be().getBlockListSettings ? be().getBlockListSettings( root ) : null;
		if ( set && set.orientation === 'horizontal' ) {
			return true;
		}
		var order = be().getBlockOrder( root || undefined );
		var i = order.indexOf( id );
		var doc = el.ownerDocument;
		var r = el.getBoundingClientRect();
		return [ order[ i - 1 ], order[ i + 1 ] ].some( function ( sid ) {
			var s = sid && doc.querySelector( '[data-block="' + sid + '"]' );
			if ( ! s ) {
				return false;
			}
			var q = s.getBoundingClientRect();
			return Math.abs( q.top - r.top ) < r.height / 2 && ( q.left >= r.right - 2 || q.right <= r.left + 2 );
		} );
	}

	function setup( doc ) {
		if ( ! doc || ! doc.body || doc.bstHoverAdder ) {
			return;
		}
		doc.bstHoverAdder = true;
		var win = doc.defaultView;
		var box = doc.createElement( 'div' );
		box.className = 'bst-hover-adder';
		box.setAttribute( 'contenteditable', 'false' );
		box.style.cssText = 'position:absolute;z-index:40;pointer-events:none;display:none;';
		var line = doc.createElement( 'div' );
		line.style.cssText = 'position:absolute;background:' + COLOR + ';border-radius:2px;';
		var tag = doc.createElement( 'div' );
		tag.style.cssText = 'position:absolute;padding:2px 10px;border-radius:999px;background:#4b5563;color:#fff;font:500 12px/20px -apple-system,Segoe UI,Roboto,sans-serif;white-space:nowrap;';
		var btn = doc.createElement( 'button' );
		btn.type = 'button';
		btn.title = LABEL;
		btn.setAttribute( 'aria-label', LABEL );
		btn.textContent = '+';
		btn.style.cssText = 'position:absolute;width:24px;height:24px;margin:0;padding:0;border:2px solid #fff;border-radius:50%;background:' + COLOR + ';color:#fff;font:700 18px/18px sans-serif;cursor:pointer;pointer-events:auto;box-shadow:0 1px 4px rgba(0,0,0,.3);';
		box.appendChild( line );
		box.appendChild( tag );
		box.appendChild( btn );
		doc.body.appendChild( box );

		var target = null;
		var frame = 0;
		var last = null;

		function pos( n, l, t, w, h ) {
			n.style.left = l + 'px';
			n.style.top = t + 'px';
			n.style.width = w + 'px';
			n.style.height = h + 'px';
		}

		function hide() {
			box.style.display = 'none';
			target = null;
		}

		function show( el, ev ) {
			var id = el.getAttribute( 'data-block' );
			var root = be().getBlockRootClientId( id ) || '';
			if ( be().getTemplateLock && be().getTemplateLock( root || undefined ) ) {
				hide();
				return;
			}
			var r = el.getBoundingClientRect();
			var sx = win.scrollX;
			var sy = win.scrollY;
			var side = isHorizontal( el, id );
			var after = side ? ev.clientX > r.left + r.width / 2 : ev.clientY > r.top + r.height / 2;
			var index = be().getBlockIndex( id ) + ( after ? 1 : 0 );
			box.style.display = 'block';
			box.style.left = ( r.left + sx ) + 'px';
			box.style.top = ( r.top + sy ) + 'px';
			box.style.width = r.width + 'px';
			box.style.height = r.height + 'px';
			if ( side ) {
				var x = after ? r.width - 2 : -2;
				pos( line, x, 0, 3, r.height );
				btn.style.left = ( x - 11 ) + 'px';
				// Fixed at the lower end of the edge: the column width handle sits in the middle.
				btn.style.top = ( Math.max( 0, r.height - 30 ) ) + 'px';
			} else {
				var y = after ? r.height - 2 : -2;
				pos( line, 0, y, r.width, 3 );
				btn.style.left = ( r.width / 2 - 12 ) + 'px';
				btn.style.top = ( y - 11 ) + 'px';
			}
			tag.textContent = blockTitle( id );
			tag.style.left = '0';
			tag.style.top = '-22px';
			target = { rootClientId: root, index: index };
		}

		doc.addEventListener( 'mousemove', function ( ev ) {
			if ( ev.target === btn ) {
				return;
			}
			last = ev;
			if ( frame ) {
				return;
			}
			frame = win.setTimeout( function () {
				frame = 0;
				var el = blockAt( last.target );
				if ( ! el ) {
					hide();
					return;
				}
				show( el, last );
			}, 40 );
		} );
		doc.addEventListener( 'mouseleave', hide );
		win.addEventListener( 'scroll', hide, { passive: true } );
		btn.addEventListener( 'mousedown', function ( e ) {
			e.preventDefault();
			e.stopPropagation();
		} );
		btn.addEventListener( 'click', function ( e ) {
			e.preventDefault();
			e.stopPropagation();
			if ( target && window.bstQuickInsert ) {
				window.bstQuickInsert( btn, target );
			}
			hide();
		} );
	}

	// The canvas is an iframe that can be rebuilt; attach to each new one.
	window.setInterval( function () {
		document.querySelectorAll( 'iframe[name="editor-canvas"]' ).forEach( function ( f ) {
			try {
				setup( f.contentDocument );
			} catch ( err ) {}
		} );
		if ( document.querySelector( '.editor-styles-wrapper' ) && ! document.querySelector( 'iframe[name="editor-canvas"]' ) ) {
			setup( document );
		}
	}, 1000 );
} )( window.wp );
