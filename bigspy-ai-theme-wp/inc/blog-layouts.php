<?php
/**
 * Blog layouts (G-197): picture pickers for layout options, a Featured posts area,
 * separate layouts for category / tag pages, per-category overrides, more post
 * header styles and a reading progress bar.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

/* ---------------------------------------------------------------- Pictures */

/**
 * Small wireframe pictures for layout choices. Each picture is a list of
 * rectangles [x, y, w, h, tone] on a 60×40 canvas; tone 1 = accent, 0 = light.
 */
function bst_layout_pictures() {
	$side_r = array( array( 2, 2, 40, 36, 0 ), array( 45, 2, 13, 36, 1 ) );
	$side_l = array( array( 2, 2, 13, 36, 1 ), array( 18, 2, 40, 36, 0 ) );
	return array(
		'sidebar' => array(
			'right' => $side_r,
			'left'  => $side_l,
			'none'  => array( array( 2, 2, 56, 36, 0 ) ),
		),
		'inherit' => array( array( 2, 2, 56, 36, 0 ), array( 22, 14, 16, 12, 1 ) ),
		'blog'    => array(
			'grid'     => array( array( 2, 2, 17, 17, 0 ), array( 21, 2, 17, 17, 0 ), array( 40, 2, 18, 17, 0 ), array( 2, 21, 17, 17, 0 ), array( 21, 21, 17, 17, 0 ), array( 40, 21, 18, 17, 0 ) ),
			'list'     => array( array( 2, 2, 18, 10, 1 ), array( 22, 4, 36, 6, 0 ), array( 2, 15, 18, 10, 1 ), array( 22, 17, 36, 6, 0 ), array( 2, 28, 18, 10, 1 ), array( 22, 30, 36, 6, 0 ) ),
			'zigzag'   => array( array( 2, 2, 18, 10, 1 ), array( 22, 4, 36, 6, 0 ), array( 40, 15, 18, 10, 1 ), array( 2, 17, 36, 6, 0 ), array( 2, 28, 18, 10, 1 ), array( 22, 30, 36, 6, 0 ) ),
			'masonry'  => array( array( 2, 2, 17, 22, 0 ), array( 21, 2, 17, 13, 0 ), array( 40, 2, 18, 18, 0 ), array( 2, 26, 17, 12, 0 ), array( 21, 17, 17, 21, 0 ), array( 40, 22, 18, 16, 0 ) ),
			'featured' => array( array( 2, 2, 56, 18, 1 ), array( 2, 22, 17, 16, 0 ), array( 21, 22, 17, 16, 0 ), array( 40, 22, 18, 16, 0 ) ),
			'classic'  => array( array( 2, 2, 56, 22, 1 ), array( 2, 26, 56, 4, 0 ), array( 2, 32, 40, 4, 0 ) ),
			'overlay'  => array( array( 2, 2, 27, 36, 1 ), array( 31, 2, 27, 36, 1 ), array( 5, 28, 20, 4, 0 ), array( 34, 28, 20, 4, 0 ) ),
			'minimal'  => array( array( 2, 4, 56, 4, 0 ), array( 2, 13, 50, 4, 0 ), array( 2, 22, 54, 4, 0 ), array( 2, 31, 44, 4, 0 ) ),
		),
		'single'  => array(
			'standard'    => array( array( 2, 2, 40, 5, 0 ), array( 2, 10, 56, 20, 1 ), array( 2, 33, 56, 5, 0 ) ),
			'image-first' => array( array( 2, 2, 56, 20, 1 ), array( 2, 25, 40, 5, 0 ), array( 2, 33, 56, 5, 0 ) ),
			'overlay'     => array( array( 2, 2, 56, 24, 1 ), array( 8, 16, 34, 5, 0 ), array( 2, 31, 56, 5, 0 ) ),
			'centered'    => array( array( 14, 2, 32, 5, 0 ), array( 2, 10, 56, 20, 1 ), array( 2, 33, 56, 5, 0 ) ),
			'hero-full'   => array( array( 0, 0, 60, 24, 1 ), array( 12, 10, 36, 5, 0 ), array( 6, 29, 48, 4, 0 ), array( 6, 35, 48, 3, 0 ) ),
			'split'       => array( array( 2, 6, 26, 5, 0 ), array( 2, 14, 20, 3, 0 ), array( 31, 2, 27, 22, 1 ), array( 2, 29, 56, 4, 0 ) ),
		),
		'featured' => array(
			'off'    => array( array( 2, 2, 56, 36, 0 ) ),
			'big'    => array( array( 2, 2, 56, 26, 1 ), array( 2, 31, 56, 7, 0 ) ),
			'grid-2' => array( array( 2, 2, 34, 36, 1 ), array( 38, 2, 20, 17, 1 ), array( 38, 21, 20, 17, 1 ) ),
			'grid-4' => array( array( 2, 2, 28, 36, 1 ), array( 32, 2, 12, 17, 1 ), array( 46, 2, 12, 17, 1 ), array( 32, 21, 12, 17, 1 ), array( 46, 21, 12, 17, 1 ) ),
			'slider' => array( array( 2, 4, 8, 28, 0 ), array( 12, 2, 36, 32, 1 ), array( 50, 4, 8, 28, 0 ), array( 26, 36, 3, 2, 1 ), array( 31, 36, 3, 2, 0 ) ),
			'row'    => array( array( 2, 8, 17, 24, 1 ), array( 21, 8, 17, 24, 1 ), array( 40, 8, 18, 24, 1 ) ),
		),
	);
}

/** Inline SVG for one picture. */
function bst_layout_svg( array $rects ) {
	$out = '<svg viewBox="0 0 60 40" width="60" height="40" aria-hidden="true" focusable="false">';
	foreach ( $rects as $r ) {
		$out .= '<rect x="' . (float) $r[0] . '" y="' . (float) $r[1] . '" width="' . (float) $r[2] . '" height="' . (float) $r[3] . '" rx="1.5" class="' . ( $r[4] ? 'is-accent' : 'is-base' ) . '"/>';
	}
	return $out . '</svg>';
}

/** Choice values that turn into picture pickers, and which picture set they use. */
function bst_layout_picker_map() {
	return array(
		'blog_layout'     => 'blog',
		'archive_layout'  => 'blog',
		'blog_sidebar'    => 'sidebar',
		'archive_sidebar' => 'sidebar',
		'single_sidebar'  => 'sidebar',
		'page_sidebar'    => 'sidebar',
		'single_layout'   => 'single',
		'blog_featured'   => 'featured',
	);
}

add_action(
	'customize_register',
	function () {
		if ( class_exists( 'BST_Image_Radio_Control' ) ) {
			return;
		}
		/** Radio buttons shown as small layout pictures. */
		class BST_Image_Radio_Control extends WP_Customize_Control {
			public $type = 'bst-image-radio';
			public $pictures = array();

			public function render_content() {
				$name = '_customize-radio-' . $this->id;
				if ( $this->label ) {
					echo '<span class="customize-control-title">' . esc_html( $this->label ) . '</span>';
				}
				if ( $this->description ) {
					echo '<span class="description customize-control-description">' . wp_kses_post( $this->description ) . '</span>';
				}
				echo '<div class="bst-pick" role="radiogroup" aria-label="' . esc_attr( $this->label ) . '">';
				foreach ( $this->choices as $value => $label ) {
					$pic = $this->pictures[ $value ] ?? ( bst_layout_pictures()['inherit'] );
					echo '<label class="bst-pick__item" title="' . esc_attr( $label ) . '">';
					echo '<input type="radio" class="screen-reader-text" name="' . esc_attr( $name ) . '" value="' . esc_attr( $value ) . '" ';
					$this->link();
					checked( (string) $this->value(), (string) $value );
					echo '>';
					echo '<span class="bst-pick__img">' . bst_layout_svg( $pic ) . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- numbers only.
					echo '<span class="bst-pick__label">' . esc_html( $label ) . '</span></label>';
				}
				echo '</div>';
			}
		}
	},
	1
);

/** Turn layout radios / selects into picture pickers. */
add_filter(
	'bst_customizer_fields',
	function ( $fields ) {
		$map = bst_layout_picker_map();
		foreach ( $fields as $i => $field ) {
			if ( isset( $map[ $field['id'] ] ) && in_array( $field['type'], array( 'radio', 'select' ), true ) ) {
				$fields[ $i ]['type'] = 'image_radio';
			}
		}
		return $fields;
	},
	50
);

/** Pictures for a picker field. */
function bst_layout_picker_pictures( $id ) {
	$map  = bst_layout_picker_map();
	$all  = bst_layout_pictures();
	$set  = isset( $map[ $id ] ) ? $all[ $map[ $id ] ] : array();
	$set['inherit'] = $all['inherit'];
	return $set;
}

/* ---------------------------------------------------------------- Options */

/** Category choices for the Featured source. */
function bst_featured_category_choices() {
	$out = array( '0' => __( '— Choose a category —', 'bigspy-ai-theme-wp' ) );
	if ( ! is_customize_preview() && ! is_admin() ) {
		return $out;
	}
	foreach ( get_categories( array( 'hide_empty' => false, 'number' => 200 ) ) as $cat ) {
		$out[ (string) $cat->term_id ] = $cat->name;
	}
	return $out;
}

/** Layout names shared by blog, archive and per-category pickers. */
function bst_blog_layout_choices() {
	return array(
		'grid'     => __( 'Grid', 'bigspy-ai-theme-wp' ),
		'list'     => __( 'List', 'bigspy-ai-theme-wp' ),
		'zigzag'   => __( 'Zigzag', 'bigspy-ai-theme-wp' ),
		'masonry'  => __( 'Masonry', 'bigspy-ai-theme-wp' ),
		'featured' => __( 'Big first post', 'bigspy-ai-theme-wp' ),
		'classic'  => __( 'Classic', 'bigspy-ai-theme-wp' ),
		'overlay'  => __( 'Text on image', 'bigspy-ai-theme-wp' ),
		'minimal'  => __( 'Titles only', 'bigspy-ai-theme-wp' ),
	);
}

function bst_sidebar_choices_short() {
	return array(
		'right' => __( 'Right sidebar', 'bigspy-ai-theme-wp' ),
		'left'  => __( 'Left sidebar', 'bigspy-ai-theme-wp' ),
		'none'  => __( 'No sidebar', 'bigspy-ai-theme-wp' ),
	);
}

add_filter(
	'bst_defaults',
	function ( $d ) {
		return array_merge(
			$d,
			array(
				'archive_layout'       => 'inherit',
				'archive_columns'      => 'inherit',
				'archive_sidebar'      => 'inherit',
				'archive_featured'     => false,
				'archive_desc'         => 'top',
				'blog_featured'        => 'off',
				'blog_featured_source' => 'sticky',
				'blog_featured_cat'    => '0',
				'blog_featured_tag'    => '',
				'blog_featured_count'  => 5,
				'blog_featured_hide'   => true,
				'blog_featured_height' => 420,
				'blog_featured_excerpt' => false,
				'single_progress'      => false,
				'single_progress_color' => '',
			)
		);
	}
);

add_filter(
	'bst_customizer_sections',
	function ( $sections ) {
		$sections['bst_blog_archive']['title'] = __( 'Blog layout (posts page)', 'bigspy-ai-theme-wp' );
		$sections['bst_blog_featured'] = array(
			'title'       => __( 'Featured posts', 'bigspy-ai-theme-wp' ),
			'panel'       => 'bst_blog',
			'priority'    => 0,
			'description' => __( 'A large eye-catching area at the top of the posts page: one big story, a 1+2 or 1+4 grid, a row or a slider.', 'bigspy-ai-theme-wp' ),
		);
		$sections['bst_blog_category'] = array(
			'title'       => __( 'Category & tag pages', 'bigspy-ai-theme-wp' ),
			'panel'       => 'bst_blog',
			'priority'    => 1,
			'description' => __( 'By default category, tag, author and date pages look like the posts page. Pick a different look here. Each category can also have its own layout: Posts → Categories → Edit.', 'bigspy-ai-theme-wp' ),
		);
		return $sections;
	}
);

add_filter(
	'bst_customizer_fields',
	function ( $fields ) {
		$c   = 'bst_blog_category';
		$fe  = 'bst_blog_featured';
		$s   = 'bst_blog_single';
		$inh = array( 'inherit' => __( 'Same as posts page', 'bigspy-ai-theme-wp' ) );
		$f   = array();

		$f[] = array( 'id' => 'archive_layout', 'section' => $c, 'type' => 'image_radio', 'label' => __( 'Layout', 'bigspy-ai-theme-wp' ), 'choices' => $inh + bst_blog_layout_choices() );
		$f[] = array( 'id' => 'archive_columns', 'section' => $c, 'type' => 'select', 'label' => __( 'Grid columns', 'bigspy-ai-theme-wp' ), 'choices' => $inh + array( '1' => '1', '2' => '2', '3' => '3', '4' => '4' ) );
		$f[] = array( 'id' => 'archive_sidebar', 'section' => $c, 'type' => 'image_radio', 'label' => __( 'Sidebar', 'bigspy-ai-theme-wp' ), 'choices' => $inh + bst_sidebar_choices_short() );
		$f[] = array( 'id' => 'archive_desc', 'section' => $c, 'type' => 'select', 'label' => __( 'Category description', 'bigspy-ai-theme-wp' ), 'choices' => array( 'top' => __( 'Above the posts', 'bigspy-ai-theme-wp' ), 'bottom' => __( 'Below the posts (good for long SEO text)', 'bigspy-ai-theme-wp' ), 'none' => __( 'Hidden', 'bigspy-ai-theme-wp' ) ) );
		$f[] = array( 'id' => 'archive_featured', 'section' => $c, 'type' => 'checkbox', 'label' => __( 'Show the Featured posts area on category pages too', 'bigspy-ai-theme-wp' ), 'description' => __( 'Uses the newest posts of that category.', 'bigspy-ai-theme-wp' ) );

		$f[] = array( 'id' => 'blog_featured', 'section' => $fe, 'type' => 'image_radio', 'label' => __( 'Style', 'bigspy-ai-theme-wp' ), 'choices' => array( 'off' => __( 'Off', 'bigspy-ai-theme-wp' ), 'big' => __( 'One big story', 'bigspy-ai-theme-wp' ), 'grid-2' => __( '1 big + 2', 'bigspy-ai-theme-wp' ), 'grid-4' => __( '1 big + 4', 'bigspy-ai-theme-wp' ), 'row' => __( 'Row of 3', 'bigspy-ai-theme-wp' ), 'slider' => __( 'Slider', 'bigspy-ai-theme-wp' ) ) );
		$f[] = array( 'id' => 'blog_featured_source', 'section' => $fe, 'type' => 'select', 'label' => __( 'Posts to feature', 'bigspy-ai-theme-wp' ), 'choices' => array( 'sticky' => __( 'Sticky posts (newest if none)', 'bigspy-ai-theme-wp' ), 'latest' => __( 'Newest posts', 'bigspy-ai-theme-wp' ), 'category' => __( 'From one category', 'bigspy-ai-theme-wp' ), 'tag' => __( 'With a tag', 'bigspy-ai-theme-wp' ), 'comments' => __( 'Most commented', 'bigspy-ai-theme-wp' ) ) );
		$f[] = array( 'id' => 'blog_featured_cat', 'section' => $fe, 'type' => 'select', 'label' => __( 'Category', 'bigspy-ai-theme-wp' ), 'choices' => bst_featured_category_choices() );
		$f[] = array( 'id' => 'blog_featured_tag', 'section' => $fe, 'type' => 'text', 'label' => __( 'Tag slug', 'bigspy-ai-theme-wp' ), 'description' => __( 'For example: editors-pick', 'bigspy-ai-theme-wp' ) );
		$f[] = array( 'id' => 'blog_featured_count', 'section' => $fe, 'type' => 'number', 'label' => __( 'Posts in the slider', 'bigspy-ai-theme-wp' ), 'input_attrs' => array( 'min' => 2, 'max' => 10 ) );
		$f[] = array( 'id' => 'blog_featured_height', 'section' => $fe, 'type' => 'number', 'label' => __( 'Height of the big story (px)', 'bigspy-ai-theme-wp' ), 'input_attrs' => array( 'min' => 240, 'max' => 800, 'step' => 10 ) );
		$f[] = array( 'id' => 'blog_featured_excerpt', 'section' => $fe, 'type' => 'checkbox', 'label' => __( 'Show the excerpt on the big story', 'bigspy-ai-theme-wp' ) );
		$f[] = array( 'id' => 'blog_featured_hide', 'section' => $fe, 'type' => 'checkbox', 'label' => __( 'Do not repeat featured posts in the list below', 'bigspy-ai-theme-wp' ) );

		$f[] = array( 'id' => 'bst_g_single_progress', 'section' => $s, 'type' => 'heading', 'label' => __( 'Reading', 'bigspy-ai-theme-wp' ) );
		$f[] = array( 'id' => 'single_progress', 'section' => $s, 'type' => 'checkbox', 'label' => __( 'Reading progress bar at the top', 'bigspy-ai-theme-wp' ) );
		$f[] = array( 'id' => 'single_progress_color', 'section' => $s, 'type' => 'color', 'label' => __( 'Progress bar color (empty = primary color)', 'bigspy-ai-theme-wp' ) );

		foreach ( $fields as $i => $field ) {
			if ( 'single_layout' === $field['id'] ) {
				$fields[ $i ]['label']   = __( 'Title layout', 'bigspy-ai-theme-wp' );
				$fields[ $i ]['choices'] = array(
					'standard'    => __( 'Title, then image', 'bigspy-ai-theme-wp' ),
					'image-first' => __( 'Image, then title', 'bigspy-ai-theme-wp' ),
					'centered'    => __( 'Centered', 'bigspy-ai-theme-wp' ),
					'split'       => __( 'Title beside image', 'bigspy-ai-theme-wp' ),
					'overlay'     => __( 'Title on image', 'bigspy-ai-theme-wp' ),
					'hero-full'   => __( 'Full-width banner', 'bigspy-ai-theme-wp' ),
				);
			}
			if ( 'blog_layout' === $field['id'] ) {
				$fields[ $i ]['choices'] = bst_blog_layout_choices();
			}
		}
		return array_merge( $fields, $f );
	},
	40
);

/** Options that depend on another one are hidden until they matter. */
function bst_blog_layout_dependencies() {
	return array(
		'blog_featured_source'  => array( 'blog_featured', '!=', 'off' ),
		'blog_featured_cat'     => array( 'blog_featured_source', '==', 'category' ),
		'blog_featured_tag'     => array( 'blog_featured_source', '==', 'tag' ),
		'blog_featured_count'   => array( 'blog_featured', '==', 'slider' ),
		'blog_featured_height'  => array( 'blog_featured', '!=', 'off' ),
		'blog_featured_excerpt' => array( 'blog_featured', '!=', 'off' ),
		'blog_featured_hide'    => array( 'blog_featured', '!=', 'off' ),
		'archive_columns'       => array( 'archive_layout', 'in', array( 'inherit', 'grid', 'masonry', 'featured', 'overlay' ) ),
		'single_progress_color' => array( 'single_progress', '==', true ),
	);
}

/* --------------------------------------------- Category / tag page values */

/** True on category, tag, author and date pages (not the posts page, search or shop). */
function bst_is_post_archive() {
	if ( is_admin() || ! ( is_category() || is_tag() || is_author() || is_date() ) ) {
		return false;
	}
	return ! ( function_exists( 'is_woocommerce' ) && is_woocommerce() );
}

/** Per-category / tag override saved in term meta. */
function bst_term_layout_meta( $key ) {
	if ( ! ( is_category() || is_tag() ) ) {
		return '';
	}
	$value = get_term_meta( get_queried_object_id(), '_bst_' . $key, true );
	return is_string( $value ) ? $value : '';
}

foreach ( array( 'blog_layout' => 'layout', 'blog_columns' => 'columns', 'blog_sidebar' => 'sidebar' ) as $bst_mod_key => $bst_part ) {
	add_filter(
		'theme_mod_' . $bst_mod_key,
		function ( $value ) use ( $bst_part ) {
			if ( ! bst_is_post_archive() ) {
				return $value;
			}
			$term = bst_term_layout_meta( $bst_part );
			if ( '' !== $term && 'inherit' !== $term ) {
				return $term;
			}
			$archive = get_theme_mod( 'archive_' . $bst_part, 'inherit' );
			return ( '' !== (string) $archive && 'inherit' !== $archive ) ? $archive : $value;
		}
	);
}
unset( $bst_mod_key, $bst_part );

/** Move the category description below the posts or hide it. */
add_filter(
	'get_the_archive_description',
	function ( $description ) {
		if ( ! bst_is_post_archive() || did_action( 'bst_after_posts_loop' ) ) {
			return $description;
		}
		$where = bst_choice( bst_mod( 'archive_desc' ), array( 'top', 'bottom', 'none' ), 'top' );
		return 'top' === $where ? $description : '';
	}
);
add_action(
	'bst_after_posts_loop',
	function () {
		if ( bst_is_post_archive() && 'bottom' === bst_mod( 'archive_desc' ) ) {
			$text = term_description();
			if ( $text ) {
				echo '<div class="bs-archive-desc bs-archive-desc--bottom bs-content">' . wp_kses_post( $text ) . '</div>';
			}
		}
	},
	20
);

/* ------------------------------------------------- Per-category settings */

foreach ( array( 'category', 'post_tag' ) as $bst_tax ) {
	add_action( $bst_tax . '_edit_form_fields', 'bst_term_layout_fields', 10, 1 );
	add_action( 'edited_' . $bst_tax, 'bst_term_layout_save', 10, 1 );
}
unset( $bst_tax );

function bst_term_layout_fields( $term ) {
	$inh   = array( 'inherit' => __( 'Same as Customizer', 'bigspy-ai-theme-wp' ) );
	$rows  = array(
		'layout'   => array( __( 'Post layout', 'bigspy-ai-theme-wp' ), $inh + bst_blog_layout_choices() ),
		'columns'  => array( __( 'Grid columns', 'bigspy-ai-theme-wp' ), $inh + array( '1' => '1', '2' => '2', '3' => '3', '4' => '4' ) ),
		'sidebar'  => array( __( 'Sidebar', 'bigspy-ai-theme-wp' ), $inh + bst_sidebar_choices_short() ),
		'featured' => array( __( 'Featured posts area', 'bigspy-ai-theme-wp' ), $inh + array( 'show' => __( 'Show', 'bigspy-ai-theme-wp' ), 'hide' => __( 'Hide', 'bigspy-ai-theme-wp' ) ) ),
	);
	wp_nonce_field( 'bst_term_layout', 'bst_term_layout_nonce' );
	echo '<tr class="form-field"><th colspan="2"><h2>' . esc_html__( 'Layout of this page (BIGSPY AI theme)', 'bigspy-ai-theme-wp' ) . '</h2></th></tr>';
	foreach ( $rows as $key => $row ) {
		$current = (string) get_term_meta( $term->term_id, '_bst_' . $key, true );
		echo '<tr class="form-field"><th scope="row"><label for="bst-term-' . esc_attr( $key ) . '">' . esc_html( $row[0] ) . '</label></th><td><select id="bst-term-' . esc_attr( $key ) . '" name="bst_term_layout[' . esc_attr( $key ) . ']">';
		foreach ( $row[1] as $value => $label ) {
			echo '<option value="' . esc_attr( $value ) . '"' . selected( '' === $current ? 'inherit' : $current, (string) $value, false ) . '>' . esc_html( $label ) . '</option>';
		}
		echo '</select></td></tr>';
	}
}

function bst_term_layout_save( $term_id ) {
	if ( ! isset( $_POST['bst_term_layout_nonce'], $_POST['bst_term_layout'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['bst_term_layout_nonce'] ) ), 'bst_term_layout' ) || ! current_user_can( 'edit_term', $term_id ) ) {
		return;
	}
	$input   = (array) wp_unslash( $_POST['bst_term_layout'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- checked against allow-lists below.
	$allowed = array(
		'layout'   => array_keys( bst_blog_layout_choices() ),
		'columns'  => array( '1', '2', '3', '4' ),
		'sidebar'  => array_keys( bst_sidebar_choices_short() ),
		'featured' => array( 'show', 'hide' ),
	);
	foreach ( $allowed as $key => $values ) {
		$value = isset( $input[ $key ] ) ? (string) $input[ $key ] : 'inherit';
		if ( in_array( $value, $values, true ) ) {
			update_term_meta( $term_id, '_bst_' . $key, $value );
		} else {
			delete_term_meta( $term_id, '_bst_' . $key );
		}
	}
}

/* -------------------------------------------------------- Featured posts */

/** Whether the Featured area shows on this page. */
function bst_featured_enabled() {
	if ( 'off' === bst_featured_style() || is_paged() ) {
		return false;
	}
	if ( is_home() ) {
		return true;
	}
	if ( is_category() || is_tag() ) {
		$term = bst_term_layout_meta( 'featured' );
		if ( 'show' === $term || 'hide' === $term ) {
			return 'show' === $term;
		}
		return (bool) bst_mod( 'archive_featured' );
	}
	return false;
}

function bst_featured_style() {
	return bst_choice( bst_mod( 'blog_featured' ), array( 'off', 'big', 'grid-2', 'grid-4', 'row', 'slider' ), 'off' );
}

/** How many posts each style shows. */
function bst_featured_count() {
	$style = bst_featured_style();
	$fixed = array( 'big' => 1, 'grid-2' => 3, 'grid-4' => 5, 'row' => 3 );
	return $fixed[ $style ] ?? max( 2, min( 10, (int) bst_mod( 'blog_featured_count' ) ) );
}

/** Query args for the Featured posts. */
function bst_featured_query_args() {
	$args = array(
		'post_type'           => 'post',
		'posts_per_page'      => bst_featured_count(),
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
		'meta_query'          => array( array( 'key' => '_thumbnail_id', 'compare' => 'EXISTS' ) ), // phpcs:ignore WordPress.DB.SlowDBQuery -- small capped query.
	);
	if ( is_category() || is_tag() ) {
		$args[ is_category() ? 'cat' : 'tag_id' ] = get_queried_object_id();
		return $args;
	}
	$source = bst_choice( bst_mod( 'blog_featured_source' ), array( 'sticky', 'latest', 'category', 'tag', 'comments' ), 'sticky' );
	if ( 'sticky' === $source ) {
		$sticky = array_filter( array_map( 'absint', (array) get_option( 'sticky_posts' ) ) );
		if ( $sticky ) {
			$args['post__in'] = $sticky;
		}
	} elseif ( 'category' === $source && (int) bst_mod( 'blog_featured_cat' ) > 0 ) {
		$args['cat'] = (int) bst_mod( 'blog_featured_cat' );
	} elseif ( 'tag' === $source && '' !== trim( (string) bst_mod( 'blog_featured_tag' ) ) ) {
		$args['tag'] = sanitize_title( bst_mod( 'blog_featured_tag' ) );
	} elseif ( 'comments' === $source ) {
		$args['orderby'] = 'comment_count';
	}
	return $args;
}

/** Ids shown in the Featured area on this request (cached). */
function bst_featured_ids() {
	static $ids = null;
	if ( null === $ids ) {
		$ids = bst_featured_enabled() ? get_posts( array_merge( bst_featured_query_args(), array( 'fields' => 'ids' ) ) ) : array();
	}
	return $ids;
}

/** Keep featured posts out of the list below. */
add_action(
	'pre_get_posts',
	function ( $query ) {
		if ( is_admin() || ! $query->is_main_query() || $query->is_paged() || ! bst_mod( 'blog_featured_hide' ) ) {
			return;
		}
		if ( ! ( $query->is_home() || $query->is_category() || $query->is_tag() ) ) {
			return;
		}
		// Category pages: the queried term is not known yet, so those skip featured posts in the loop (bst_featured_skip).
		if ( $query->is_home() && 'off' !== bst_featured_style() ) {
			$ids = get_posts( array_merge( bst_featured_query_args(), array( 'fields' => 'ids' ) ) );
			if ( $ids ) {
				$query->set( 'post__not_in', array_merge( (array) $query->get( 'post__not_in' ), $ids ) );
			}
		}
	}
);

/** On category pages skip featured posts inside the loop (the query cannot know them in advance). */
function bst_featured_skip( $post_id ) {
	return ( is_category() || is_tag() ) && bst_mod( 'blog_featured_hide' ) && in_array( (int) $post_id, array_map( 'intval', bst_featured_ids() ), true );
}

/** One featured card. */
function bst_featured_card( $post, $big = false ) {
	$title = get_the_title( $post );
	$cats  = get_the_category( $post->ID );
	$html  = '<article class="bs-feat__item' . ( $big ? ' is-big' : '' ) . '"><a class="bs-feat__link" href="' . esc_url( get_permalink( $post ) ) . '">';
	$html .= get_the_post_thumbnail( $post, $big ? 'large' : 'medium_large', array( 'class' => 'bs-feat__img', 'loading' => $big ? 'eager' : 'lazy' ) );
	$html .= '<span class="bs-feat__body">';
	if ( $cats ) {
		$html .= '<span class="bs-feat__cat">' . esc_html( $cats[0]->name ) . '</span>';
	}
	$html .= '<span class="bs-feat__title">' . esc_html( $title ) . '</span>';
	if ( $big && bst_mod( 'blog_featured_excerpt' ) ) {
		$html .= '<span class="bs-feat__excerpt">' . esc_html( wp_trim_words( get_the_excerpt( $post ), 24 ) ) . '</span>';
	}
	$html .= '<span class="bs-feat__date">' . esc_html( get_the_date( '', $post ) ) . '</span>';
	return $html . '</span></a></article>';
}

/** Print the Featured area above the posts list. */
function bst_featured_render() {
	$ids = bst_featured_ids();
	if ( ! $ids ) {
		if ( is_customize_preview() && bst_featured_enabled() ) {
			echo '<p class="bs-feat-empty">' . esc_html__( 'Featured posts: no posts with a featured image match the chosen source yet.', 'bigspy-ai-theme-wp' ) . '</p>';
		}
		return;
	}
	$style = bst_featured_style();
	$posts = get_posts( array( 'post__in' => $ids, 'orderby' => 'post__in', 'posts_per_page' => count( $ids ), 'ignore_sticky_posts' => true, 'post_type' => 'post' ) );
	$out   = '';
	foreach ( $posts as $i => $post ) {
		$out .= bst_featured_card( $post, 0 === $i && in_array( $style, array( 'big', 'grid-2', 'grid-4' ), true ) || 'slider' === $style );
	}
	$h = max( 240, min( 800, (int) bst_mod( 'blog_featured_height' ) ) );
	echo '<section class="bs-feat bs-feat--' . esc_attr( $style ) . '" style="--bs-feat-h:' . (int) $h . 'px" aria-label="' . esc_attr__( 'Featured posts', 'bigspy-ai-theme-wp' ) . '"' . ( 'slider' === $style ? ' data-bst-feat-slider' : '' ) . '>';
	echo '<div class="bs-feat__track">' . $out . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in bst_featured_card().
	if ( 'slider' === $style && count( $posts ) > 1 ) {
		echo '<button type="button" class="bs-feat__nav is-prev" aria-label="' . esc_attr__( 'Previous', 'bigspy-ai-theme-wp' ) . '">‹</button><button type="button" class="bs-feat__nav is-next" aria-label="' . esc_attr__( 'Next', 'bigspy-ai-theme-wp' ) . '">›</button>';
	}
	echo '</section>';
}
add_action( 'bst_before_posts_loop', 'bst_featured_render', 5 );

/* ------------------------------------------------- Reading progress bar */

add_action(
	'wp_footer',
	function () {
		if ( ! is_singular( 'post' ) || ! bst_mod( 'single_progress' ) ) {
			return;
		}
		$color = sanitize_hex_color( (string) bst_mod( 'single_progress_color' ) );
		echo '<div class="bs-progress" aria-hidden="true"' . ( $color ? ' style="--bs-progress:' . esc_attr( $color ) . '"' : '' ) . '><span></span></div>';
		echo "<script>(function(){var b=document.querySelector('.bs-progress span'),a=document.querySelector('.bs-entry--single')||document.body;if(!b)return;function u(){var r=a.getBoundingClientRect(),t=a.offsetHeight-window.innerHeight,p=t>0?Math.min(1,Math.max(0,-r.top/t)):1;b.style.transform='scaleX('+p+')';}addEventListener('scroll',u,{passive:true});addEventListener('resize',u);u();})();</script>";
	}
);

/* ---------------------------------------------------------------- Assets */

add_action(
	'wp_enqueue_scripts',
	function () {
		if ( ! ( is_home() || is_archive() || is_search() || is_singular( 'post' ) ) ) {
			return;
		}
		wp_enqueue_style( 'bst-blog-layouts', BST_URI . '/assets/css/blog-layouts.css', array(), bst_asset_version( 'assets/css/blog-layouts.css' ) );
		if ( 'slider' === bst_featured_style() && ( is_home() || is_category() || is_tag() ) ) {
			wp_enqueue_script( 'bst-blog-featured', BST_URI . '/assets/js/blog-featured.js', array(), bst_asset_version( 'assets/js/blog-featured.js' ), array( 'in_footer' => true, 'strategy' => 'defer' ) );
		}
	},
	20
);

add_action(
	'customize_controls_enqueue_scripts',
	function () {
		wp_enqueue_style( 'bst-blog-layouts-controls', BST_URI . '/assets/css/blog-layouts-controls.css', array(), bst_asset_version( 'assets/css/blog-layouts-controls.css' ) );
		wp_enqueue_script( 'bst-blog-layouts-controls', BST_URI . '/assets/js/blog-layouts-controls.js', array( 'customize-controls' ), bst_asset_version( 'assets/js/blog-layouts-controls.js' ), true );
		wp_localize_script( 'bst-blog-layouts-controls', 'bstBlogDeps', bst_blog_layout_dependencies() );
	}
);
