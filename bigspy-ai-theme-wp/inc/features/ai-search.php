<?php
/**
 * AI search (AEO and GEO): /llms.txt, a plain-text map of the site for AI assistants, and
 * basic SEO tags plus schema (Organization, WebSite, Article, BreadcrumbList) on sites
 * without an SEO plugin. An SEO plugin, when active, keeps full control of tags and schema.
 */
defined( 'ABSPATH' ) || exit;

/** True when an SEO plugin already prints meta tags and schema. */
function bst_ais_seo_plugin() {
	return defined( 'RANK_MATH_VERSION' ) || defined( 'WPSEO_VERSION' ) || defined( 'AIOSEO_VERSION' ) || defined( 'SEOPRESS_VERSION' ) || defined( 'THE_SEO_FRAMEWORK_VERSION' );
}

/** One-line plain text, cut at a word boundary. */
function bst_ais_text( $text, $max = 160 ) {
	$text = trim( preg_replace( '/\s+/u', ' ', html_entity_decode( wp_strip_all_tags( strip_shortcodes( (string) $text ) ), ENT_QUOTES, 'UTF-8' ) ) );
	if ( function_exists( 'mb_strlen' ) && mb_strlen( $text ) > $max ) {
		$text = rtrim( preg_replace( '/\s+\S*$/u', '', mb_substr( $text, 0, $max ) ), ' ,;:.-' ) . '…';
	}
	return $text;
}

/** Short description of a post: SEO description, excerpt, then the start of the content. */
function bst_ais_description( $post, $max = 160 ) {
	$post = get_post( $post );
	if ( ! $post ) {
		return '';
	}
	$text = (string) get_post_meta( $post->ID, 'rank_math_description', true );
	$text = '' !== $text && false === strpos( $text, '%' ) ? $text : $post->post_excerpt;
	if ( '' === trim( $text ) ) {
		$text = excerpt_remove_blocks( $post->post_content );
	}
	return bst_ais_text( $text, $max );
}

function bst_ais_summary() {
	$summary = bst_ais_text( bst_feature_get( 'ai-search', 'summary' ), 400 );
	return '' !== $summary ? $summary : bst_ais_text( get_bloginfo( 'description' ), 400 );
}

/** Profile URLs of the site owner, from the comma-separated setting. */
function bst_ais_same_as() {
	$urls = array();
	foreach ( preg_split( '/[\s,]+/', (string) bst_feature_get( 'ai-search', 'same_as' ) ) as $url ) {
		$url = esc_url_raw( $url, array( 'http', 'https' ) );
		if ( '' !== $url ) {
			$urls[] = $url;
		}
	}
	return array_values( array_unique( $urls ) );
}

/* ---------- llms.txt ---------- */

/** Markdown link line; brackets in the title would break the link. */
function bst_ais_line( $title, $url, $note = '' ) {
	$title = str_replace( array( '[', ']' ), array( '(', ')' ), bst_ais_text( $title, 120 ) );
	return '- [' . $title . '](' . esc_url_raw( $url ) . ')' . ( '' !== $note ? ': ' . $note : '' );
}

function bst_ais_post_lines( $args ) {
	$lines = array();
	$posts = get_posts( $args + array( 'post_status' => 'publish', 'has_password' => false, 'orderby' => 'modified', 'order' => 'DESC', 'suppress_filters' => false ) );
	foreach ( $posts as $post ) {
		$lines[] = bst_ais_line( get_the_title( $post ), get_permalink( $post ), bst_ais_description( $post ) );
	}
	return $lines;
}

function bst_ais_term_lines( $taxonomy, $count ) {
	$lines = array();
	$terms = taxonomy_exists( $taxonomy ) ? get_terms( array( 'taxonomy' => $taxonomy, 'hide_empty' => true, 'orderby' => 'count', 'order' => 'DESC', 'number' => $count ) ) : array();
	foreach ( is_array( $terms ) ? $terms : array() as $term ) {
		$url = get_term_link( $term );
		if ( ! is_wp_error( $url ) && 'uncategorized' !== $term->slug ) {
			$lines[] = bst_ais_line( $term->name, $url, bst_ais_text( $term->description ) );
		}
	}
	return $lines;
}

/** The llms.txt body: site name, summary, then link lists by section (llmstxt.org format). */
function bst_ais_llms_txt() {
	$count    = max( 5, min( 200, (int) bst_feature_get( 'ai-search', 'count', 50 ) ) );
	$shop     = class_exists( 'WooCommerce' );
	$listing  = $shop && bst_feature_on( 'listing' );
	$sections = array();

	// Pages, without the shop's cart, checkout and account pages.
	$skip = array( (int) get_option( 'page_on_front' ), (int) get_option( 'wp_page_for_privacy_policy' ) );
	foreach ( $shop ? array( 'cart', 'checkout', 'myaccount' ) : array() as $page ) {
		$skip[] = (int) wc_get_page_id( $page );
	}
	$pages = bst_ais_post_lines( array( 'post_type' => 'page', 'numberposts' => 30, 'post__not_in' => array_filter( $skip, function ( $id ) { return $id > 0; } ), 'orderby' => 'menu_order title', 'order' => 'ASC' ) );
	array_unshift( $pages, bst_ais_line( __( 'Home', 'bigspy-ai-theme-wp' ), home_url( '/' ), bst_ais_text( get_bloginfo( 'description' ) ) ) );
	$sections[ __( 'Main pages', 'bigspy-ai-theme-wp' ) ] = $pages;

	if ( $shop ) {
		$sections[ $listing ? __( 'Listing categories', 'bigspy-ai-theme-wp' ) : __( 'Product categories', 'bigspy-ai-theme-wp' ) ] = bst_ais_term_lines( 'product_cat', 30 );
		$sections[ $listing ? __( 'Listings', 'bigspy-ai-theme-wp' ) : __( 'Products', 'bigspy-ai-theme-wp' ) ]                     = bst_ais_post_lines( array( 'post_type' => 'product', 'numberposts' => $count ) );
	}
	$sections[ __( 'Articles', 'bigspy-ai-theme-wp' ) ] = bst_ais_post_lines( array( 'post_type' => 'post', 'numberposts' => $count ) );
	$sections[ __( 'Topics', 'bigspy-ai-theme-wp' ) ]   = bst_ais_term_lines( 'category', 20 );

	$optional = array();
	$sitemap  = function_exists( 'get_sitemap_url' ) ? get_sitemap_url( 'index' ) : '';
	if ( $sitemap ) {
		$optional[] = bst_ais_line( __( 'XML sitemap', 'bigspy-ai-theme-wp' ), $sitemap );
	}
	$optional[]            = bst_ais_line( __( 'RSS feed', 'bigspy-ai-theme-wp' ), get_feed_link() );
	$sections['Optional'] = $optional; // Section name fixed by the llms.txt format.

	$out     = '# ' . bst_ais_text( get_bloginfo( 'name' ), 120 ) . "\n";
	$summary = bst_ais_summary();
	if ( '' !== $summary ) {
		$out .= "\n> " . $summary . "\n";
	}
	foreach ( apply_filters( 'bst_llms_txt_sections', $sections ) as $title => $lines ) {
		if ( $lines ) {
			$out .= "\n## " . $title . "\n\n" . implode( "\n", $lines ) . "\n";
		}
	}
	return $out;
}

/** Serve /llms.txt without a rewrite rule; a real file in the site root wins. */
function bst_ais_serve_llms() {
	if ( ! bst_feature_get( 'ai-search', 'llms', 1 ) || empty( $_SERVER['REQUEST_URI'] ) ) {
		return;
	}
	$path = trim( (string) wp_parse_url( wp_unslash( $_SERVER['REQUEST_URI'] ), PHP_URL_PATH ), '/' ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput -- compared, never printed.
	$home = trim( (string) wp_parse_url( home_url(), PHP_URL_PATH ), '/' );
	if ( ltrim( $home . '/llms.txt', '/' ) !== $path ) {
		return;
	}
	// A site hidden from search engines stays hidden from AI assistants too.
	if ( ! get_option( 'blog_public' ) ) {
		status_header( 404 );
		exit;
	}
	$body = get_transient( 'bst_llms_txt' );
	if ( ! is_string( $body ) || '' === $body ) {
		$body = bst_ais_llms_txt();
		set_transient( 'bst_llms_txt', $body, HOUR_IN_SECONDS );
	}
	status_header( 200 );
	header( 'Content-Type: text/plain; charset=utf-8' );
	header( 'X-Robots-Tag: noindex' );
	echo $body; // phpcs:ignore WordPress.Security.EscapeOutput -- plain text response.
	exit;
}
add_action( 'wp_loaded', 'bst_ais_serve_llms' );

function bst_ais_flush() {
	delete_transient( 'bst_llms_txt' );
}
add_action( 'save_post', 'bst_ais_flush' );
add_action( 'deleted_post', 'bst_ais_flush' );
add_action( 'update_option_bst_feature_ai-search', 'bst_ais_flush' );
add_action( 'add_option_bst_feature_ai-search', 'bst_ais_flush' );

/* ---------- Tags and schema without an SEO plugin ---------- */

function bst_ais_image( $post_id = 0 ) {
	$image = $post_id ? get_the_post_thumbnail_url( $post_id, 'full' ) : '';
	if ( ! $image ) {
		$logo  = (int) get_theme_mod( 'custom_logo' );
		$image = $logo ? wp_get_attachment_image_url( $logo, 'full' ) : get_site_icon_url( 512 );
	}
	return $image ? $image : '';
}

/** Graph nodes for the current view. */
function bst_ais_schema_nodes() {
	$home  = home_url( '/' );
	$name  = wp_strip_all_tags( get_bloginfo( 'name' ) );
	$nodes = array();
	$org   = array( '@type' => 'Organization', '@id' => $home . '#organization', 'name' => $name, 'url' => $home );
	$logo  = bst_ais_image();
	if ( '' !== $logo ) {
		$org['logo'] = array( '@type' => 'ImageObject', 'url' => $logo );
	}
	$same = bst_ais_same_as();
	if ( $same ) {
		$org['sameAs'] = $same;
	}
	$site = array( '@type' => 'WebSite', '@id' => $home . '#website', 'url' => $home, 'name' => $name, 'inLanguage' => get_bloginfo( 'language' ), 'publisher' => array( '@id' => $home . '#organization' ) );

	if ( is_front_page() ) {
		$summary = bst_ais_summary();
		if ( '' !== $summary ) {
			$org['description']  = $summary;
			$site['description'] = $summary;
		}
		$site['potentialAction'] = array( '@type' => 'SearchAction', 'target' => array( '@type' => 'EntryPoint', 'urlTemplate' => $home . '?s={search_term_string}' ), 'query-input' => 'required name=search_term_string' );
		return array( $org, $site );
	}
	if ( ! is_singular( 'post' ) ) {
		return array();
	}
	$post   = get_queried_object();
	$link   = get_permalink( $post );
	$title  = wp_strip_all_tags( get_the_title( $post ) );
	$crumbs = array( array( $name, $home ) );
	$terms  = get_the_category( $post->ID );
	if ( $terms && 'uncategorized' !== $terms[0]->slug ) {
		$crumbs[] = array( html_entity_decode( $terms[0]->name, ENT_QUOTES, 'UTF-8' ), get_category_link( $terms[0] ) );
	}
	$crumbs[] = array( $title, $link );
	$items    = array();
	foreach ( $crumbs as $i => $crumb ) {
		$items[] = array( '@type' => 'ListItem', 'position' => $i + 1, 'name' => $crumb[0], 'item' => $crumb[1] );
	}
	$author  = array( '@type' => 'Person', 'name' => get_the_author_meta( 'display_name', $post->post_author ), 'url' => get_author_posts_url( $post->post_author ) );
	$bio     = bst_ais_text( get_the_author_meta( 'description', $post->post_author ), 300 );
	$website = get_the_author_meta( 'user_url', $post->post_author );
	if ( '' !== $bio ) {
		$author['description'] = $bio;
	}
	if ( $website ) {
		$author['sameAs'] = array( esc_url_raw( $website ) );
	}
	$article = array(
		'@type'            => 'Article',
		'@id'              => $link . '#article',
		'headline'         => bst_ais_text( $title, 110 ),
		'mainEntityOfPage' => $link,
		'inLanguage'       => get_bloginfo( 'language' ),
		'datePublished'    => get_post_time( DATE_W3C, true, $post ),
		'dateModified'     => get_post_modified_time( DATE_W3C, true, $post ),
		'author'           => $author,
		'publisher'        => array( '@id' => $home . '#organization' ),
		'isPartOf'         => array( '@id' => $home . '#website' ),
	);
	$description = bst_ais_description( $post );
	if ( '' !== $description ) {
		$article['description'] = $description;
	}
	$image = get_the_post_thumbnail_url( $post->ID, 'full' );
	if ( $image ) {
		$article['image'] = $image;
	}
	return array( $org, $site, $article, array( '@type' => 'BreadcrumbList', '@id' => $link . '#breadcrumb', 'itemListElement' => $items ) );
}

function bst_ais_head() {
	if ( bst_ais_seo_plugin() || ! bst_feature_get( 'ai-search', 'tags', 1 ) || is_404() || is_search() ) {
		return;
	}
	$singular    = is_singular();
	$post_id     = $singular ? get_queried_object_id() : 0;
	$title       = wp_get_document_title();
	$description = $singular && ! is_front_page() ? bst_ais_description( $post_id ) : '';
	if ( '' === $description && ( is_category() || is_tag() || is_tax() ) ) {
		$description = bst_ais_text( term_description() );
	}
	if ( '' === $description && ( is_front_page() || is_home() ) ) {
		$description = bst_ais_text( bst_ais_summary() );
	}
	$url = $singular ? wp_get_canonical_url( $post_id ) : '';
	if ( ! $url && is_front_page() ) {
		$url = home_url( '/' );
	}
	$tags = array(
		'og:type'      => is_singular( 'post' ) ? 'article' : 'website',
		'og:site_name' => get_bloginfo( 'name' ),
		'og:title'     => $title,
		'og:url'       => $url,
		'og:image'     => bst_ais_image( $post_id ),
	);
	if ( '' !== $description ) {
		echo '<meta name="description" content="' . esc_attr( $description ) . '">' . "\n";
		$tags['og:description'] = $description;
	}
	foreach ( array_filter( $tags ) as $property => $content ) {
		echo '<meta property="' . esc_attr( $property ) . '" content="' . esc_attr( $content ) . '">' . "\n";
	}
	echo '<meta name="twitter:card" content="' . ( $post_id && has_post_thumbnail( $post_id ) ? 'summary_large_image' : 'summary' ) . '">' . "\n";
	$nodes = bst_ais_schema_nodes();
	if ( $nodes ) {
		echo '<script type="application/ld+json">' . wp_json_encode( array( '@context' => 'https://schema.org', '@graph' => $nodes ), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG ) . '</script>' . "\n";
	}
}
add_action( 'wp_head', 'bst_ais_head', 5 );

add_filter(
	'bst_feature_fields',
	function ( $fields ) {
		$fields['ai-search'] = array(
			'llms'    => array( 'type' => 'checkbox', 'label' => __( 'Serve /llms.txt', 'bigspy-ai-theme-wp' ), 'default' => 1, 'help' => __( 'A plain-text map of your main pages, categories and newest content for AI assistants. Rebuilt when content changes.', 'bigspy-ai-theme-wp' ) ),
			'summary' => array( 'type' => 'text', 'label' => __( 'Site summary (empty = tagline)', 'bigspy-ai-theme-wp' ), 'help' => __( 'One or two sentences saying what the site offers and for whom. AI assistants quote it.', 'bigspy-ai-theme-wp' ) ),
			'count'   => array( 'type' => 'number', 'label' => __( 'Newest items per list in llms.txt', 'bigspy-ai-theme-wp' ), 'default' => 50, 'min' => 5, 'max' => 200 ),
			'tags'    => array( 'type' => 'checkbox', 'label' => __( 'Basic SEO tags and schema', 'bigspy-ai-theme-wp' ), 'default' => 1, 'help' => __( 'Meta description, social sharing tags and Organization, WebSite, Article and breadcrumb schema. Only printed while no SEO plugin is active.', 'bigspy-ai-theme-wp' ) ),
			'same_as' => array( 'type' => 'text', 'label' => __( 'Brand profile links', 'bigspy-ai-theme-wp' ), 'help' => __( 'Comma-separated URLs of your official profiles (Facebook, LinkedIn, X, YouTube, review pages). They tell AI which brand the site belongs to.', 'bigspy-ai-theme-wp' ) ),
		);
		return $fields;
	}
);
