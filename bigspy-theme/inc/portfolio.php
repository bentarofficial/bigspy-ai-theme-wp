<?php
/** Opt-in portfolio content uses native WordPress archives and Query blocks. */
defined( 'ABSPATH' ) || exit;
add_filter( 'bst_defaults', function ( $d ) { return $d + array( 'portfolio_enable' => false ); } );
add_filter( 'bst_customizer_fields', function ( $fields ) {
	$fields[] = array( 'id' => 'portfolio_enable', 'section' => 'bst_layout_site', 'type' => 'checkbox', 'label' => 'Bật Portfolio / Dự án', 'description' => 'Thêm loại nội dung Dự án và danh mục dự án. Sau khi bật lần đầu, lưu lại Cài đặt → Đường dẫn tĩnh để cập nhật URL.' );
	return $fields;
} );
add_action( 'init', function () {
	if ( ! bst_mod( 'portfolio_enable' ) ) { return; }
	register_post_type( 'bst_project', array( 'labels' => array( 'name' => 'Dự án', 'singular_name' => 'Dự án', 'add_new_item' => 'Thêm dự án', 'edit_item' => 'Sửa dự án' ), 'public' => true, 'show_in_rest' => true, 'has_archive' => true, 'rewrite' => array( 'slug' => 'du-an' ), 'supports' => array( 'title', 'editor', 'thumbnail', 'excerpt', 'revisions' ), 'menu_icon' => 'dashicons-portfolio' ) );
	register_taxonomy( 'bst_project_category', 'bst_project', array( 'label' => 'Danh mục dự án', 'public' => true, 'hierarchical' => true, 'show_in_rest' => true, 'rewrite' => array( 'slug' => 'danh-muc-du-an' ) ) );
}, 12 );
