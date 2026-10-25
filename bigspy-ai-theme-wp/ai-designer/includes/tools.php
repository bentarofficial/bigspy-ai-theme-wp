<?php
/**
 * Tools the text model may call. This list is the hard limit: there is no tool to update,
 * install or remove themes / plugins, edit files, manage users, change system settings,
 * delete permanently, or read orders, customers and payments.
 *
 * Each tool: description, JSON schema, capability, callback( $args ) returning an array.
 * A result may carry 'links' (label / edit / view) that the workspace shows to the user.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

function bst_aid_tools() {
	static $tools = null;
	if ( null !== $tools ) {
		return $tools;
	}
	$seo = array(
		'type'        => 'object',
		'description' => 'Rank Math fields.',
		'properties'  => array(
			'focus_keyword' => array( 'type' => 'string' ),
			'title'         => array( 'type' => 'string', 'description' => 'SEO title, about 50–60 characters.' ),
			'description'   => array( 'type' => 'string', 'description' => 'Meta description, about 140–160 characters.' ),
		),
	);
	$tools = array(
		'site_info'      => array(
			'description' => 'Read the site: name, language, colors, fonts, pages, menus, post categories, WooCommerce product categories and the user guidelines. Call this before planning.',
			'parameters'  => array( 'type' => 'object', 'properties' => new stdClass() ),
			'cap'         => 'edit_pages',
			'callback'    => 'bst_aid_tool_site_info',
		),
		'list_content'   => array(
			'description' => 'List existing pages, posts or products (id, title, status, link). Use search to find one.',
			'parameters'  => array(
				'type'       => 'object',
				'properties' => array(
					'type'   => array( 'type' => 'string', 'enum' => array( 'page', 'post', 'product' ) ),
					'search' => array( 'type' => 'string' ),
					'limit'  => array( 'type' => 'integer', 'minimum' => 1, 'maximum' => 50 ),
				),
				'required'   => array( 'type' ),
			),
			'cap'         => 'edit_pages',
			'callback'    => 'bst_aid_tool_list_content',
		),
		'get_post'       => array(
			'description' => 'Read one page, post or product: title, status, content (HTML), excerpt, categories, SEO fields.',
			'parameters'  => array( 'type' => 'object', 'properties' => array( 'id' => array( 'type' => 'integer' ) ), 'required' => array( 'id' ) ),
			'cap'         => 'edit_pages',
			'callback'    => 'bst_aid_tool_get_post',
		),
		'create_post'    => array(
			'description' => 'Create a blog post. content is HTML using h2–h4, p, ul/ol/li, strong, em, a, blockquote, table; no h1 (the title is the H1). Saved as a draft unless status is "publish", which you may use only when the user clearly asked to publish.',
			'parameters'  => array(
				'type'       => 'object',
				'properties' => array(
					'title'      => array( 'type' => 'string' ),
					'content'    => array( 'type' => 'string' ),
					'excerpt'    => array( 'type' => 'string' ),
					'slug'       => array( 'type' => 'string' ),
					'categories' => array( 'type' => 'array', 'items' => array( 'type' => 'string' ), 'description' => 'Category names; missing ones are created.' ),
					'tags'       => array( 'type' => 'array', 'items' => array( 'type' => 'string' ) ),
					'status'     => array( 'type' => 'string', 'enum' => array( 'draft', 'publish' ) ),
					'featured_image_id' => array( 'type' => 'integer', 'description' => 'Media id (e.g. from ImageGen).' ),
					'seo'        => $seo,
				),
				'required'   => array( 'title', 'content' ),
			),
			'cap'         => 'edit_posts',
			'callback'    => 'bst_aid_tool_create_post',
		),
		'update_post'    => array(
			'description' => 'Update a post (only the fields given). A revision is kept. For a published post, ask the user first and pass confirmed: true.',
			'parameters'  => array(
				'type'       => 'object',
				'properties' => array(
					'id'         => array( 'type' => 'integer' ),
					'title'      => array( 'type' => 'string' ),
					'content'    => array( 'type' => 'string' ),
					'excerpt'    => array( 'type' => 'string' ),
					'categories' => array( 'type' => 'array', 'items' => array( 'type' => 'string' ) ),
					'tags'       => array( 'type' => 'array', 'items' => array( 'type' => 'string' ) ),
					'status'     => array( 'type' => 'string', 'enum' => array( 'draft', 'publish' ) ),
					'featured_image_id' => array( 'type' => 'integer' ),
					'seo'        => $seo,
					'confirmed'  => array( 'type' => 'boolean' ),
				),
				'required'   => array( 'id' ),
			),
			'cap'         => 'edit_posts',
			'callback'    => 'bst_aid_tool_update_post',
		),
		'save_guidelines' => array(
			'description' => 'Save the user\'s standards to the Guidelines screen. Use only after the user agreed to save them. Each field replaces the stored text.',
			'parameters'  => array(
				'type'       => 'object',
				'properties' => array(
					'business' => array( 'type' => 'string' ),
					'writing'  => array( 'type' => 'string' ),
					'images'   => array( 'type' => 'string' ),
				),
			),
			'cap'         => 'edit_pages',
			'callback'    => 'bst_aid_tool_save_guidelines',
		),
	);
	if ( class_exists( 'WooCommerce' ) ) {
		$product = array(
			'name'              => array( 'type' => 'string' ),
			'regular_price'     => array( 'type' => 'string', 'description' => 'Number only, e.g. "490000".' ),
			'sale_price'        => array( 'type' => 'string' ),
			'short_description' => array( 'type' => 'string', 'description' => 'HTML, 2–4 lines.' ),
			'description'       => array( 'type' => 'string', 'description' => 'HTML.' ),
			'sku'               => array( 'type' => 'string' ),
			'categories'        => array( 'type' => 'array', 'items' => array( 'type' => 'string' ) ),
			'status'            => array( 'type' => 'string', 'enum' => array( 'draft', 'publish' ) ),
			'image_id'          => array( 'type' => 'integer', 'description' => 'Main product image (media id).' ),
			'gallery_ids'       => array( 'type' => 'array', 'items' => array( 'type' => 'integer' ) ),
			'seo'               => $seo,
		);
		$tools['create_product'] = array(
			'description' => 'Create a WooCommerce simple product. Draft unless the user clearly asked to publish. Use only prices the user gave.',
			'parameters'  => array( 'type' => 'object', 'properties' => $product, 'required' => array( 'name' ) ),
			'cap'         => 'edit_products',
			'callback'    => 'bst_aid_tool_create_product',
		);
		$tools['update_product'] = array(
			'description' => 'Update a product (only the fields given). Changing the price of a published product needs the user\'s OK: pass confirmed: true only after asking.',
			'parameters'  => array( 'type' => 'object', 'properties' => array_merge( array( 'id' => array( 'type' => 'integer' ), 'confirmed' => array( 'type' => 'boolean' ) ), $product ), 'required' => array( 'id' ) ),
			'cap'         => 'edit_products',
			'callback'    => 'bst_aid_tool_update_product',
		);
	}
	$tools = apply_filters( 'bst_aid_tools', $tools );
	return $tools;
}

/** Tool definitions in OpenAI format, only the ones the current user may run. */
function bst_aid_tool_specs() {
	$out = array();
	foreach ( bst_aid_tools() as $name => $t ) {
		if ( current_user_can( $t['cap'] ) ) {
			$out[] = array( 'type' => 'function', 'function' => array( 'name' => $name, 'description' => $t['description'], 'parameters' => $t['parameters'] ) );
		}
	}
	return $out;
}

/** Run a tool by name. Unknown tools and missing rights are refused (and logged by the caller). */
function bst_aid_run_tool( $name, $args ) {
	$tools = bst_aid_tools();
	if ( ! isset( $tools[ $name ] ) ) {
		return array( 'error' => 'Unknown tool. Only the listed tools can be used.' );
	}
	if ( ! current_user_can( $tools[ $name ]['cap'] ) ) {
		return array( 'error' => 'The current user is not allowed to do this.' );
	}
	try {
		$result = call_user_func( $tools[ $name ]['callback'], is_array( $args ) ? $args : array() );
	} catch ( Throwable $e ) {
		$result = array( 'error' => $e->getMessage() );
	}
	return is_array( $result ) ? $result : array( 'result' => $result );
}

/* ------------------------------------------------------------------ Helpers */

/** Links for the workspace. */
function bst_aid_links( $post_id, $label ) {
	return array( array( 'label' => $label, 'edit' => get_edit_post_link( $post_id, 'raw' ), 'view' => get_permalink( $post_id ) ) );
}

/** Term ids for names in a taxonomy, creating missing terms. */
function bst_aid_terms( $names, $taxonomy ) {
	$ids = array();
	foreach ( (array) $names as $name ) {
		$name = trim( wp_strip_all_tags( (string) $name ) );
		if ( '' === $name ) {
			continue;
		}
		$term = term_exists( $name, $taxonomy );
		if ( ! $term ) {
			$term = wp_insert_term( $name, $taxonomy );
		}
		if ( ! is_wp_error( $term ) ) {
			$ids[] = (int) ( is_array( $term ) ? $term['term_id'] : $term );
		}
	}
	return $ids;
}

function bst_aid_set_seo( $post_id, $seo ) {
	if ( ! is_array( $seo ) ) {
		return;
	}
	$map = array( 'focus_keyword' => 'rank_math_focus_keyword', 'title' => 'rank_math_title', 'description' => 'rank_math_description' );
	foreach ( $map as $k => $meta ) {
		if ( isset( $seo[ $k ] ) && '' !== trim( (string) $seo[ $k ] ) ) {
			update_post_meta( $post_id, $meta, sanitize_text_field( bst_aid_clean_plain( (string) $seo[ $k ] ) ) );
		}
	}
}

/**
 * Model HTML to block markup: headings, paragraphs and lists become core blocks so the post
 * edits nicely; anything else (tables, quotes…) becomes a Custom HTML block.
 */
function bst_aid_html_to_blocks( $html ) {
	$html = wp_kses_post( bst_aid_clean_html( (string) $html ) );
	if ( '' === trim( $html ) || ! class_exists( 'DOMDocument' ) ) {
		return $html;
	}
	$doc = new DOMDocument();
	libxml_use_internal_errors( true );
	$doc->loadHTML( '<?xml encoding="utf-8"?><div id="bst-root">' . $html . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD );
	libxml_clear_errors();
	$root = $doc->getElementById( 'bst-root' );
	if ( ! $root ) {
		return $html;
	}
	$inner = function ( $node ) use ( $doc ) {
		$s = '';
		foreach ( $node->childNodes as $c ) {
			$s .= $doc->saveHTML( $c );
		}
		return trim( $s );
	};
	$out = array();
	foreach ( $root->childNodes as $node ) {
		if ( XML_TEXT_NODE === $node->nodeType ) {
			$text = trim( $node->textContent );
			if ( '' !== $text ) {
				$out[] = "<!-- wp:paragraph -->\n<p>" . esc_html( $text ) . "</p>\n<!-- /wp:paragraph -->";
			}
			continue;
		}
		if ( XML_ELEMENT_NODE !== $node->nodeType ) {
			continue;
		}
		$tag = strtolower( $node->nodeName );
		if ( preg_match( '/^h([2-6])$/', $tag, $m ) ) {
			$attrs = '2' === $m[1] ? '' : ' {"level":' . $m[1] . '}';
			$out[] = '<!-- wp:heading' . $attrs . " -->\n<" . $tag . ' class="wp-block-heading">' . $inner( $node ) . '</' . $tag . ">\n<!-- /wp:heading -->";
		} elseif ( 'h1' === $tag ) {
			$out[] = "<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">" . $inner( $node ) . "</h2>\n<!-- /wp:heading -->";
		} elseif ( 'p' === $tag ) {
			$out[] = "<!-- wp:paragraph -->\n<p>" . $inner( $node ) . "</p>\n<!-- /wp:paragraph -->";
		} elseif ( 'ul' === $tag || 'ol' === $tag ) {
			$items = '';
			foreach ( $node->childNodes as $li ) {
				if ( XML_ELEMENT_NODE === $li->nodeType && 'li' === strtolower( $li->nodeName ) ) {
					$items .= "<!-- wp:list-item -->\n<li>" . $inner( $li ) . "</li>\n<!-- /wp:list-item -->\n";
				}
			}
			$attrs = 'ol' === $tag ? ' {"ordered":true}' : '';
			$out[] = '<!-- wp:list' . $attrs . " -->\n<" . $tag . ' class="wp-block-list">' . $items . '</' . $tag . ">\n<!-- /wp:list -->";
		} else {
			$out[] = "<!-- wp:html -->\n" . $doc->saveHTML( $node ) . "\n<!-- /wp:html -->";
		}
	}
	return implode( "\n\n", $out );
}

/* ------------------------------------------------------------------ Tools */

function bst_aid_tool_site_info() {
	$colors = array();
	foreach ( array( 'color_primary', 'color_secondary', 'color_text', 'color_heading', 'color_link', 'color_light', 'color_body_bg' ) as $k ) {
		$colors[ str_replace( 'color_', '', $k ) ] = bst_mod( $k );
	}
	$pages = array();
	foreach ( get_posts( array( 'post_type' => 'page', 'post_status' => array( 'publish', 'draft', 'private' ), 'numberposts' => 60, 'orderby' => 'menu_order title', 'order' => 'ASC' ) ) as $p ) {
		$pages[] = array( 'id' => $p->ID, 'title' => get_the_title( $p ), 'status' => $p->post_status, 'link' => get_permalink( $p ) );
	}
	$menus = array();
	foreach ( wp_get_nav_menus() as $m ) {
		$menus[] = $m->name;
	}
	$info = array(
		'name'            => get_bloginfo( 'name' ),
		'tagline'         => get_bloginfo( 'description' ),
		'url'             => home_url( '/' ),
		'language'        => get_locale(),
		'front_page_id'   => (int) get_option( 'page_on_front' ),
		'colors'          => $colors,
		'fonts'           => array( 'body' => bst_mod( 'font_body' ), 'heading' => bst_mod( 'font_heading' ) ),
		'has_logo'        => (bool) get_theme_mod( 'custom_logo' ),
		'pages'           => $pages,
		'menus'           => $menus,
		'post_categories' => wp_list_pluck( get_terms( array( 'taxonomy' => 'category', 'hide_empty' => false, 'number' => 50 ) ), 'name' ),
		'woocommerce'     => class_exists( 'WooCommerce' ),
		'guidelines'      => bst_aid_guidelines(),
	);
	if ( class_exists( 'WooCommerce' ) ) {
		$info['product_categories'] = wp_list_pluck( get_terms( array( 'taxonomy' => 'product_cat', 'hide_empty' => false, 'number' => 50 ) ), 'name' );
		$info['currency']           = get_woocommerce_currency();
	}
	return $info;
}

function bst_aid_tool_list_content( $a ) {
	$type = in_array( $a['type'] ?? '', array( 'page', 'post', 'product' ), true ) ? $a['type'] : 'page';
	if ( 'product' === $type && ! post_type_exists( 'product' ) ) {
		return array( 'error' => 'WooCommerce is not active.' );
	}
	$q   = get_posts(
		array(
			'post_type'   => $type,
			'post_status' => array( 'publish', 'draft', 'pending', 'private', 'future' ),
			'numberposts' => max( 1, min( 50, (int) ( $a['limit'] ?? 20 ) ) ),
			's'           => (string) ( $a['search'] ?? '' ),
		)
	);
	$out = array();
	foreach ( $q as $p ) {
		$out[] = array( 'id' => $p->ID, 'title' => get_the_title( $p ), 'status' => $p->post_status, 'modified' => $p->post_modified, 'link' => get_permalink( $p ) );
	}
	return array( 'items' => $out );
}

function bst_aid_tool_get_post( $a ) {
	$p = get_post( (int) ( $a['id'] ?? 0 ) );
	if ( ! $p || ! in_array( $p->post_type, array( 'page', 'post', 'product' ), true ) || ! current_user_can( 'edit_post', $p->ID ) ) {
		return array( 'error' => 'Not found or not editable.' );
	}
	$out = array(
		'id'      => $p->ID,
		'type'    => $p->post_type,
		'title'   => $p->post_title,
		'status'  => $p->post_status,
		'excerpt' => $p->post_excerpt,
		'content' => mb_substr( $p->post_content, 0, 20000 ),
		'link'    => get_permalink( $p ),
		'seo'     => array(
			'focus_keyword' => get_post_meta( $p->ID, 'rank_math_focus_keyword', true ),
			'title'         => get_post_meta( $p->ID, 'rank_math_title', true ),
			'description'   => get_post_meta( $p->ID, 'rank_math_description', true ),
		),
	);
	if ( 'post' === $p->post_type ) {
		$out['categories'] = wp_get_post_categories( $p->ID, array( 'fields' => 'names' ) );
		$out['tags']       = wp_get_post_tags( $p->ID, array( 'fields' => 'names' ) );
	}
	if ( 'product' === $p->post_type && function_exists( 'wc_get_product' ) ) {
		$prod                = wc_get_product( $p->ID );
		$out['regular_price'] = $prod ? $prod->get_regular_price() : '';
		$out['sale_price']    = $prod ? $prod->get_sale_price() : '';
		$out['short_description'] = $prod ? $prod->get_short_description() : '';
	}
	return $out;
}

function bst_aid_tool_create_post( $a ) {
	$id = wp_insert_post(
		array(
			'post_type'    => 'post',
			'post_title'   => sanitize_text_field( bst_aid_clean_plain( (string) $a['title'] ) ),
			'post_content' => bst_aid_html_to_blocks( bst_aid_drop_title_heading( bst_aid_clean_html( (string) ( $a['content'] ?? '' ) ), bst_aid_clean_plain( (string) $a['title'] ) ) ),
			'post_excerpt' => sanitize_textarea_field( bst_aid_clean_plain( (string) ( $a['excerpt'] ?? '' ) ) ),
			'post_name'    => sanitize_title( (string) ( $a['slug'] ?? '' ) ),
			'post_status'  => ( 'publish' === ( $a['status'] ?? '' ) && current_user_can( 'publish_posts' ) ) ? 'publish' : 'draft',
			'post_category' => bst_aid_terms( $a['categories'] ?? array(), 'category' ),
		),
		true
	);
	if ( is_wp_error( $id ) ) {
		return array( 'error' => $id->get_error_message() );
	}
	if ( ! empty( $a['tags'] ) ) {
		wp_set_post_tags( $id, array_map( 'sanitize_text_field', (array) $a['tags'] ) );
	}
	bst_aid_set_seo( $id, $a['seo'] ?? null );
	if ( ! empty( $a['featured_image_id'] ) && wp_attachment_is_image( (int) $a['featured_image_id'] ) ) {
		set_post_thumbnail( $id, (int) $a['featured_image_id'] );
	}
	update_post_meta( $id, '_bst_aid_created', 1 );
	if ( function_exists( 'bst_aid_track_created' ) ) {
		bst_aid_track_created( $id );
	}
	return array( 'id' => $id, 'status' => get_post_status( $id ), 'links' => bst_aid_links( $id, get_the_title( $id ) ) );
}

function bst_aid_tool_update_post( $a ) {
	$p = get_post( (int) ( $a['id'] ?? 0 ) );
	if ( ! $p || 'post' !== $p->post_type || ! current_user_can( 'edit_post', $p->ID ) ) {
		return array( 'error' => 'Post not found or not editable.' );
	}
	if ( 'publish' === $p->post_status && empty( $a['confirmed'] ) ) {
		return array( 'error' => 'needs_confirmation', 'message' => 'This post is published. Ask the user to confirm the change, then call again with confirmed: true.' );
	}
	$data = array( 'ID' => $p->ID );
	if ( isset( $a['title'] ) ) {
		$data['post_title'] = sanitize_text_field( bst_aid_clean_plain( (string) $a['title'] ) );
	}
	if ( isset( $a['content'] ) ) {
		$data['post_content'] = bst_aid_html_to_blocks( bst_aid_drop_title_heading( bst_aid_clean_html( (string) $a['content'] ), isset( $a['title'] ) ? bst_aid_clean_plain( (string) $a['title'] ) : $p->post_title ) );
	}
	if ( isset( $a['excerpt'] ) ) {
		$data['post_excerpt'] = sanitize_textarea_field( bst_aid_clean_plain( (string) $a['excerpt'] ) );
	}
	if ( isset( $a['status'] ) ) {
		$data['post_status'] = ( 'publish' === $a['status'] && current_user_can( 'publish_posts' ) ) ? 'publish' : 'draft';
	}
	if ( isset( $a['categories'] ) ) {
		$data['post_category'] = bst_aid_terms( $a['categories'], 'category' );
	}
	if ( function_exists( 'bst_aid_snapshot_post' ) ) {
		bst_aid_snapshot_post( $p->ID );
	} else {
		wp_save_post_revision( $p->ID );
	}
	$r = wp_update_post( $data, true );
	if ( is_wp_error( $r ) ) {
		return array( 'error' => $r->get_error_message() );
	}
	if ( isset( $a['tags'] ) ) {
		wp_set_post_tags( $p->ID, array_map( 'sanitize_text_field', (array) $a['tags'] ) );
	}
	bst_aid_set_seo( $p->ID, $a['seo'] ?? null );
	if ( ! empty( $a['featured_image_id'] ) && wp_attachment_is_image( (int) $a['featured_image_id'] ) ) {
		set_post_thumbnail( $p->ID, (int) $a['featured_image_id'] );
	}
	return array( 'id' => $p->ID, 'status' => get_post_status( $p->ID ), 'links' => bst_aid_links( $p->ID, get_the_title( $p->ID ) ) );
}

function bst_aid_tool_save_guidelines( $a ) {
	$changes = array_intersect_key( (array) $a, array_flip( array( 'business', 'writing', 'images' ) ) );
	if ( ! $changes ) {
		return array( 'error' => 'Nothing to save.' );
	}
	bst_aid_save_guidelines( $changes );
	return array( 'saved' => array_keys( $changes ), 'links' => array( array( 'label' => __( 'Guidelines', 'bigspy-ai-theme-wp' ), 'edit' => admin_url( 'admin.php?page=bst-ai-guidelines' ), 'view' => '' ) ) );
}

/**
 * Price from the model: "120.000đ", "120,000 VND", "1.250.000", "99.5" → WooCommerce decimal.
 * Groups of three digits after a dot or comma are thousands separators.
 */
function bst_aid_price( $value ) {
	$v = preg_replace( '/[^0-9.,]/', '', (string) $value );
	if ( '' === $v ) {
		return '';
	}
	if ( preg_match( '/^\d{1,3}([.,]\d{3})+$/', $v ) ) {
		$v = preg_replace( '/[.,]/', '', $v );
	} elseif ( preg_match( '/^\d{1,3}([.,]\d{3})+[.,]\d{1,2}$/', $v ) ) {
		$v = preg_replace( '/[.,](?=\d{3})/', '', $v );
	}
	return wc_format_decimal( str_replace( ',', '.', $v ) );
}

/** Drop a first heading that only repeats the title (the title is already the H1). */
function bst_aid_drop_title_heading( $html, $title ) {
	$norm = function ( $s ) {
		return mb_strtolower( trim( preg_replace( '/\s+/u', ' ', wp_strip_all_tags( (string) $s ) ) ) );
	};
	if ( preg_match( '/^\s*<h[1-6][^>]*>(.*?)<\/h[1-6]>/is', (string) $html, $m ) && $norm( $m[1] ) === $norm( $title ) ) {
		return substr( (string) $html, strlen( $m[0] ) );
	}
	return $html;
}

/** Apply product fields to a WC_Product. */
function bst_aid_fill_product( $prod, $a ) {
	if ( isset( $a['name'] ) ) {
		$prod->set_name( sanitize_text_field( bst_aid_clean_plain( (string) $a['name'] ) ) );
	}
	foreach ( array( 'regular_price', 'sale_price' ) as $k ) {
		if ( isset( $a[ $k ] ) ) {
			$v = bst_aid_price( $a[ $k ] );
			'regular_price' === $k ? $prod->set_regular_price( $v ) : $prod->set_sale_price( $v );
		}
	}
	if ( isset( $a['short_description'] ) ) {
		$prod->set_short_description( wp_kses_post( bst_aid_clean_html( (string) $a['short_description'] ) ) );
	}
	if ( isset( $a['description'] ) ) {
		$prod->set_description( wp_kses_post( bst_aid_clean_html( (string) $a['description'] ) ) );
	}
	if ( isset( $a['sku'] ) ) {
		try {
			$prod->set_sku( sanitize_text_field( (string) $a['sku'] ) );
		} catch ( Exception $e ) {
			unset( $e ); // Duplicate SKU: keep the product without it.
		}
	}
	if ( isset( $a['categories'] ) ) {
		$prod->set_category_ids( bst_aid_terms( $a['categories'], 'product_cat' ) );
	}
	if ( ! empty( $a['image_id'] ) && wp_attachment_is_image( (int) $a['image_id'] ) ) {
		$prod->set_image_id( (int) $a['image_id'] );
	}
	if ( isset( $a['gallery_ids'] ) ) {
		$prod->set_gallery_image_ids( array_values( array_filter( array_map( 'intval', (array) $a['gallery_ids'] ), 'wp_attachment_is_image' ) ) );
	}
	if ( isset( $a['status'] ) ) {
		$prod->set_status( ( 'publish' === $a['status'] && current_user_can( 'publish_products' ) ) ? 'publish' : 'draft' );
	}
}

function bst_aid_tool_create_product( $a ) {
	$prod = new WC_Product_Simple();
	$prod->set_status( 'draft' );
	bst_aid_fill_product( $prod, $a );
	$id = $prod->save();
	if ( ! $id ) {
		return array( 'error' => 'The product could not be saved.' );
	}
	bst_aid_set_seo( $id, $a['seo'] ?? null );
	update_post_meta( $id, '_bst_aid_created', 1 );
	if ( function_exists( 'bst_aid_track_created' ) ) {
		bst_aid_track_created( $id );
	}
	return array( 'id' => $id, 'status' => get_post_status( $id ), 'links' => bst_aid_links( $id, $prod->get_name() ) );
}

function bst_aid_tool_update_product( $a ) {
	$prod = wc_get_product( (int) ( $a['id'] ?? 0 ) );
	if ( ! $prod || ! current_user_can( 'edit_post', $prod->get_id() ) ) {
		return array( 'error' => 'Product not found or not editable.' );
	}
	$price_change = ( isset( $a['regular_price'] ) && (string) $a['regular_price'] !== (string) $prod->get_regular_price() ) || ( isset( $a['sale_price'] ) && (string) $a['sale_price'] !== (string) $prod->get_sale_price() );
	if ( 'publish' === $prod->get_status() && $price_change && empty( $a['confirmed'] ) ) {
		return array( 'error' => 'needs_confirmation', 'message' => 'This product is on sale. Ask the user to confirm the new price, then call again with confirmed: true.' );
	}
	if ( function_exists( 'bst_aid_snapshot_post' ) ) {
		bst_aid_snapshot_post( $prod->get_id() );
	}
	bst_aid_fill_product( $prod, $a );
	$prod->save();
	bst_aid_set_seo( $prod->get_id(), $a['seo'] ?? null );
	return array( 'id' => $prod->get_id(), 'status' => $prod->get_status(), 'links' => bst_aid_links( $prod->get_id(), $prod->get_name() ) );
}
