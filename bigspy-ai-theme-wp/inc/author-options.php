<?php
/**
 * Author information for posts: from each writer's WordPress profile, or one custom author
 * set in the Customizer (handy for single-author sites). Used by the author box, the post
 * byline and Rank Math's Person schema.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

add_filter(
	'bst_defaults',
	function ( $d ) {
		return $d + array(
			'author_source'        => 'profile',
			'author_custom_name'   => '',
			'author_custom_avatar' => '',
			'author_custom_bio'    => '',
			'author_custom_url'    => '',
			'author_box_title'     => '',
			'author_box_style'     => 'card',
		);
	}
);

add_filter(
	'bst_customizer_fields',
	function ( $fields ) {
		$s = 'bst_blog_single';
		foreach ( $fields as $i => $f ) {
			if ( 'single_author_box' === ( $f['id'] ?? '' ) ) {
				$fields[ $i ]['label'] = __( 'Show author information', 'bigspy-ai-theme-wp' );
			}
		}
		$fields[] = array( 'id' => 'bst_g_single_author', 'section' => $s, 'type' => 'heading', 'label' => __( 'Author information', 'bigspy-ai-theme-wp' ) );
		$fields[] = array(
			'id'          => 'author_source',
			'section'     => $s,
			'type'        => 'select',
			'label'       => __( 'Author details come from', 'bigspy-ai-theme-wp' ),
			'choices'     => array(
				'profile' => __( 'Each post writer’s profile (Users → Profile)', 'bigspy-ai-theme-wp' ),
				'custom'  => __( 'One custom author for all posts (below)', 'bigspy-ai-theme-wp' ),
			),
			'description' => __( 'Custom fields left empty fall back to the writer’s profile.', 'bigspy-ai-theme-wp' ),
		);
		$fields[] = array( 'id' => 'author_custom_name', 'section' => $s, 'type' => 'text', 'label' => __( 'Author name', 'bigspy-ai-theme-wp' ) );
		$fields[] = array( 'id' => 'author_custom_avatar', 'section' => $s, 'type' => 'image', 'label' => __( 'Author avatar', 'bigspy-ai-theme-wp' ) );
		$fields[] = array( 'id' => 'author_custom_bio', 'section' => $s, 'type' => 'html', 'label' => __( 'Author bio', 'bigspy-ai-theme-wp' ), 'description' => __( 'Experience, skills and what the author writes about – this helps readers and Google trust the content.', 'bigspy-ai-theme-wp' ) );
		$fields[] = array( 'id' => 'author_custom_url', 'section' => $s, 'type' => 'url', 'label' => __( 'Author profile URL', 'bigspy-ai-theme-wp' ), 'description' => __( 'An about-the-author page or a public profile (LinkedIn, Facebook…).', 'bigspy-ai-theme-wp' ) );
		$fields[] = array( 'id' => 'author_box_title', 'section' => $s, 'type' => 'text', 'label' => __( 'Author box heading (empty = “Written by”)', 'bigspy-ai-theme-wp' ) );
		$fields[] = array( 'id' => 'author_box_style', 'section' => $s, 'type' => 'select', 'label' => __( 'Author box style', 'bigspy-ai-theme-wp' ), 'choices' => array( 'card' => __( 'Light card', 'bigspy-ai-theme-wp' ), 'bordered' => __( 'Bordered', 'bigspy-ai-theme-wp' ), 'accent' => __( 'Brand line on the left', 'bigspy-ai-theme-wp' ) ) );
		return $fields;
	},
	22
);

function bst_author_custom_mode() {
	return 'custom' === bst_mod( 'author_source' );
}

/**
 * Author details for the current post: name, url, bio, avatar_url, avatar (HTML).
 */
function bst_author_data() {
	$id   = (int) get_the_author_meta( 'ID' );
	$data = array(
		'name'       => (string) get_the_author_meta( 'display_name', $id ),
		'url'        => (string) ( get_the_author_meta( 'user_url', $id ) ? get_the_author_meta( 'user_url', $id ) : get_author_posts_url( $id ) ),
		'bio'        => (string) get_the_author_meta( 'description', $id ),
		'avatar_url' => (string) get_avatar_url( $id, array( 'size' => 160 ) ),
	);
	if ( bst_author_custom_mode() ) {
		foreach ( array( 'name' => 'author_custom_name', 'url' => 'author_custom_url', 'bio' => 'author_custom_bio', 'avatar_url' => 'author_custom_avatar' ) as $key => $mod ) {
			$value = trim( (string) bst_mod( $mod ) );
			if ( '' !== $value ) {
				$data[ $key ] = $value;
			}
		}
	}
	$data['avatar'] = $data['avatar_url'] ? '<img class="bs-author-box__avatar" src="' . esc_url( $data['avatar_url'] ) . '" alt="' . esc_attr( $data['name'] ) . '" width="80" height="80" loading="lazy" decoding="async">' : '';
	return $data;
}

/* Byline and author links follow the custom author on the front end. */
add_filter(
	'the_author',
	function ( $name ) {
		if ( is_admin() || ! bst_author_custom_mode() ) {
			return $name;
		}
		$custom = trim( (string) bst_mod( 'author_custom_name' ) );
		return '' !== $custom ? $custom : $name;
	}
);
add_filter(
	'author_link',
	function ( $link ) {
		if ( is_admin() || ! bst_author_custom_mode() ) {
			return $link;
		}
		$custom = trim( (string) bst_mod( 'author_custom_url' ) );
		return '' !== $custom ? $custom : $link;
	}
);

/* Rank Math: the Person entity uses the same author details. */
add_filter(
	'rank_math/json_ld',
	function ( $data ) {
		if ( ! bst_author_custom_mode() || ! is_singular( 'post' ) || ! is_array( $data ) ) {
			return $data;
		}
		$author = bst_author_data();
		foreach ( $data as $key => $entity ) {
			if ( is_array( $entity ) && isset( $entity['@type'] ) && 'Person' === $entity['@type'] ) {
				$data[ $key ]['name'] = $author['name'];
				if ( $author['url'] ) {
					$data[ $key ]['url'] = $author['url'];
				}
				if ( $author['bio'] ) {
					$data[ $key ]['description'] = wp_strip_all_tags( $author['bio'] );
				}
				if ( $author['avatar_url'] ) {
					$data[ $key ]['image'] = array( '@type' => 'ImageObject', 'url' => $author['avatar_url'], 'caption' => $author['name'] );
				}
			}
		}
		return $data;
	},
	99
);
