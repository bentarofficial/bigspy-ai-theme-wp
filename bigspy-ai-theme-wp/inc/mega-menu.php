<?php
/** Native menu checkbox enables a column-based desktop dropdown. */
defined( 'ABSPATH' ) || exit;
add_action( 'wp_nav_menu_item_custom_fields', function ( $id, $item, $depth ) {
	if ( 0 !== (int) $depth ) { return; }
	echo '<p class="description description-wide"><label><input type="checkbox" name="bst_mega_menu[' . absint( $id ) . ']" value="1" ' . checked( get_post_meta( $id, '_bst_mega_menu', true ), '1', false ) . '> ' . esc_html__( 'BIGSPY AI: Mega menu (child items become columns on desktop)', 'bigspy-ai-theme-wp' ) . '</label></p>';
}, 10, 3 );
add_action( 'wp_update_nav_menu_item', function ( $menu_id, $item_id ) {
	if ( ! current_user_can( 'edit_theme_options' ) || empty( $_POST['update-nav-menu-nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['update-nav-menu-nonce'] ) ), 'update-nav_menu' ) ) { return; }
	$value = isset( $_POST['bst_mega_menu'] ) && is_array( $_POST['bst_mega_menu'] ) && isset( $_POST['bst_mega_menu'][ $item_id ] ) ? '1' : '';
	update_post_meta( $item_id, '_bst_mega_menu', $value );
}, 10, 2 );
add_filter( 'nav_menu_css_class', function ( $classes, $item, $args, $depth ) {
	if ( 0 === (int) $depth && '1' === get_post_meta( $item->ID, '_bst_mega_menu', true ) ) { $classes[] = 'bs-mega-menu'; }
	return $classes;
}, 10, 4 );
add_action( 'wp_enqueue_scripts', function () {
	wp_enqueue_style( 'bst-mega-menu', BST_URI . '/assets/css/mega-menu.css', array( 'bst-main' ), bst_asset_version( 'assets/css/mega-menu.css' ) );
	wp_enqueue_script( 'bst-mega-menu', BST_URI . '/assets/js/mega-menu.js', array(), bst_asset_version( 'assets/js/mega-menu.js' ), array( 'in_footer' => true, 'strategy' => 'defer' ) );
} );

/*
 * Multi-column dropdown for flat lists: a top-level item whose children have no
 * children of their own is split into evenly filled columns when it is ticked as
 * mega menu or has at least "mega_auto" children. Empty mega_auto = off.
 */
add_filter( 'wp_nav_menu_objects', function ( $items ) {
	$kids = array();
	$deep = array();
	foreach ( $items as $it ) {
		$p = (int) $it->menu_item_parent;
		if ( $p ) { $kids[ $p ] = isset( $kids[ $p ] ) ? $kids[ $p ] + 1 : 1; }
	}
	$by_id = array();
	foreach ( $items as $it ) { $by_id[ (int) $it->ID ] = $it; }
	foreach ( $items as $it ) {
		$p = (int) $it->menu_item_parent;
		if ( $p && isset( $by_id[ $p ] ) && ! (int) $by_id[ $p ]->menu_item_parent && ! empty( $kids[ (int) $it->ID ] ) ) { $deep[ $p ] = true; }
	}
	$auto  = (int) get_theme_mod( 'mega_auto', 0 );
	$cols  = (string) get_theme_mod( 'mega_columns', '' );
	$align = (string) get_theme_mod( 'mega_align', '' );
	$style = (string) get_theme_mod( 'mega_style', '' );
	$intro = (bool) get_theme_mod( 'mega_intro', false );
	foreach ( $items as $it ) {
		$id = (int) $it->ID;
		if ( (int) $it->menu_item_parent || empty( $kids[ $id ] ) ) { continue; }
		$n      = $kids[ $id ];
		$ticked = '1' === get_post_meta( $id, '_bst_mega_menu', true );
		$add    = array();
		if ( empty( $deep[ $id ] ) && ( $ticked || ( $auto > 1 && $n >= $auto ) ) ) {
			$c     = in_array( $cols, array( '2', '3', '4', '5', '6' ), true ) ? (int) $cols : ( $n <= 8 ? 2 : ( $n <= 18 ? 3 : 4 ) );
			$add[] = 'bs-mega-menu';
			$add[] = 'bs-mega-flat';
			$add[] = 'bs-mega-n-' . min( $c, $n );
		}
		if ( in_array( $align, array( 'left', 'center', 'right' ), true ) && ( $add || $ticked ) ) { $add[] = 'bs-mega-align-' . $align; }
		if ( ( $add || $ticked ) && in_array( $style, array( 'icons', 'cards', 'divided', 'accent' ), true ) ) { $add[] = 'bs-mega-s-' . $style; }
		if ( $add && $intro ) { $add[] = 'bs-mega-has-intro'; }
		if ( $add ) { $it->classes = array_unique( array_merge( (array) $it->classes, $add ) ); }
	}
	return $items;
} );

/* Intro panel (parent title, description, "See all" link) and item descriptions in mega menus. */
add_filter( 'walker_nav_menu_start_el', function ( $output, $item, $depth, $args ) {
	$classes = (array) $item->classes;
	if ( 0 === (int) $depth && in_array( 'bs-mega-has-intro', $classes, true ) ) {
		$desc    = trim( (string) $item->description );
		$output .= '<div class="bs-mega-intro" aria-hidden="true"><strong class="bs-mega-intro__title">' . esc_html( wp_strip_all_tags( $item->title ) ) . '</strong>'
			. ( '' !== $desc ? '<p class="bs-mega-intro__desc">' . esc_html( $desc ) . '</p>' : '' )
			. ( $item->url && '#' !== $item->url ? '<a class="bs-mega-intro__more" tabindex="-1" href="' . esc_url( $item->url ) . '">' . esc_html__( 'See all', 'bigspy-ai-theme-wp' ) . ' &rarr;</a>' : '' )
			. '</div>';
	}
	if ( (int) $depth > 0 && bst_mod_on( 'mega_desc' ) && '' !== trim( (string) $item->description ) ) {
		$output = preg_replace( '#</a>\s*$#', '<span class="bs-mega-desc">' . esc_html( trim( $item->description ) ) . '</span></a>', $output, 1 );
	}
	return $output;
}, 10, 4 );
function bst_mod_on( $id ) {
	return (bool) get_theme_mod( $id, false );
}
