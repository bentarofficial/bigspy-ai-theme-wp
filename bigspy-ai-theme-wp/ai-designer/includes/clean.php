<?php
/**
 * Output cleaning: models sometimes add Markdown (**bold**, ## heading), label prefixes
 * ("H2:", "Title:") or wrap titles in quotes. Everything the AI Designer saves goes through
 * these helpers, so pages, posts and products get clean HTML with real heading tags and
 * the user never has to fix markers by hand.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

/** Remove "H2:", "Heading 3 –", "Tiêu đề:", "Title:"… at the start of a heading or title. */
function bst_aid_strip_label( $text ) {
	return (string) preg_replace( '/^\s*(?:\*\*|__)?\s*(?:\(?\s*(?:h[1-6]|heading\s*[1-6]?|tiêu đề(?:\s*h?[1-6])?|title|meta title|seo title|tên bài|tên sản phẩm|name)\s*\)?)\s*(?:\*\*|__)?\s*[:：\-–—|]\s*/iu', '', (string) $text );
}

/** Remove one pair of quotes wrapping the whole text. */
function bst_aid_strip_quotes( $text ) {
	$text = trim( (string) $text );
	if ( preg_match( '/^(["“”„«\'‘’])(.+)(["“”»\'‘’])$/us', $text, $m ) && ! preg_match( '/["“”]/u', $m[2] ) ) { // Multibyte-safe: strpbrk() compares bytes.
		return trim( $m[2] );
	}
	return $text;
}

/** Inline Markdown to HTML: links, bold, italics, code. Leftover markers are removed. */
function bst_aid_md_inline( $text ) {
	$text = (string) $text;
	$text = preg_replace( '/\[([^\]\n]+)\]\(((?:https?:\/\/|tel:|mailto:|\/|#)[^\s)]*)\)/u', '<a href="$2">$1</a>', $text );
	$text = preg_replace( '/(\*\*\*|___)(?=\S)(.+?)(?<=\S)\1/us', '<strong><em>$2</em></strong>', $text );
	$text = preg_replace( '/(\*\*|__)(?=\S)(.+?)(?<=\S)\1/us', '<strong>$2</strong>', $text );
	$text = preg_replace( '/(?<![\w*])\*(?=[^\s*])([^*\n]+?)(?<=\S)\*(?![\w*])/u', '<em>$1</em>', $text );
	$text = preg_replace( '/`([^`\n]+)`/u', '<code>$1</code>', $text );
	return str_replace( array( '**', '__' ), '', $text );
}

/** Plain text for titles, names, SEO fields: no Markdown, no labels, no wrapping quotes. */
function bst_aid_clean_plain( $text ) {
	$text = wp_strip_all_tags( bst_aid_md_inline( bst_aid_strip_label( (string) $text ) ) );
	$text = preg_replace( '/^\s*#{1,6}\s+/u', '', $text );
	return bst_aid_strip_quotes( trim( preg_replace( '/\s+/u', ' ', $text ) ) );
}

/** Heading text (may keep simple inline HTML such as a colored span). */
function bst_aid_clean_heading( $text ) {
	$text = preg_replace( '/^\s*#{1,6}\s+/u', '', bst_aid_strip_label( (string) $text ) );
	return bst_aid_strip_quotes( trim( bst_aid_md_inline( $text ) ) );
}

/**
 * Body HTML: Markdown headings, lists and emphasis become real HTML; "H2:" labels are
 * removed from headings; empty paragraphs are dropped.
 */
function bst_aid_clean_html( $html ) {
	$html = str_replace( array( "\r\n", "\r" ), "\n", (string) $html );
	// Markdown written as plain lines (no tags at all): convert line by line.
	if ( ! preg_match( '/<(p|h[1-6]|ul|ol|li|div|table|blockquote)\b/i', $html ) ) {
		$out  = array();
		$list = array();
		$type = '';
		$flush = function () use ( &$out, &$list, &$type ) {
			if ( $list ) {
				$out[] = '<' . $type . '>' . implode( '', $list ) . '</' . $type . '>';
			}
			$list = array();
			$type = '';
		};
		foreach ( preg_split( '/\n{1,}/', $html ) as $line ) {
			$line = trim( $line );
			if ( '' === $line ) {
				continue;
			}
			if ( preg_match( '/^(#{1,6})\s+(.+)$/u', $line, $m ) ) {
				$flush();
				$level = max( 2, strlen( $m[1] ) );
				$out[] = '<h' . $level . '>' . bst_aid_clean_heading( $m[2] ) . '</h' . $level . '>';
			} elseif ( preg_match( '/^(?:[-*•+]|(\d+)[.)])\s+(.+)$/u', $line, $m ) ) {
				$t = '' !== $m[1] ? 'ol' : 'ul';
				if ( $type && $type !== $t ) {
					$flush();
				}
				$type   = $t;
				$list[] = '<li>' . bst_aid_md_inline( $m[2] ) . '</li>';
			} elseif ( preg_match( '/^(?:\*\*|__)?\s*(?:h([1-6])|heading\s*([1-6]))\s*(?:\*\*|__)?\s*[:：\-–—]\s*(.+)$/iu', $line, $m ) ) {
				$flush();
				$level = max( 2, (int) ( $m[1] ? $m[1] : $m[2] ) );
				$out[] = '<h' . $level . '>' . bst_aid_clean_heading( $m[3] ) . '</h' . $level . '>';
			} else {
				$flush();
				$out[] = '<p>' . bst_aid_md_inline( preg_replace( '/^>\s*/', '', $line ) ) . '</p>';
			}
		}
		$flush();
		$html = implode( "\n", $out );
	}
	// Inside HTML: clean heading texts, convert stray Markdown, drop "## " in paragraphs.
	$html = preg_replace_callback(
		'/<(h[1-6])([^>]*)>(.*?)<\/\1>/is',
		function ( $m ) {
			return '<' . $m[1] . $m[2] . '>' . bst_aid_clean_heading( $m[3] ) . '</' . $m[1] . '>';
		},
		$html
	);
	$html = preg_replace_callback(
		'/<p([^>]*)>\s*(#{1,6})\s+(.*?)<\/p>/is',
		function ( $m ) {
			$level = max( 2, strlen( $m[2] ) );
			return '<h' . $level . '>' . bst_aid_clean_heading( $m[3] ) . '</h' . $level . '>';
		},
		$html
	);
	$html = preg_replace_callback(
		'/>([^<]+)</u',
		function ( $m ) {
			return '>' . bst_aid_md_inline( $m[1] ) . '<';
		},
		$html
	);
	return preg_replace( '/<p[^>]*>\s*(?:&nbsp;)?\s*<\/p>/i', '', $html );
}

/**
 * Clean the text attributes of a block. Code, URLs, colors and ids stay untouched;
 * heading / title texts lose labels and wrapping quotes; other texts get inline Markdown
 * converted.
 */
function bst_aid_clean_block_attrs( $name, $attrs ) {
	if ( 'bst/code' === $name || 'bst/shortcode' === $name ) {
		return $attrs;
	}
	foreach ( $attrs as $key => $value ) {
		if ( is_array( $value ) ) {
			$attrs[ $key ] = bst_aid_clean_block_attrs( $name, $value );
			continue;
		}
		if ( ! is_string( $value ) || '' === $value ) {
			continue;
		}
		$k = strtolower( (string) $key );
		if ( preg_match( '/(url|link|href|color|colour|bg|image|img|icon|svg|code|css|class|anchor|align|tag|unit|size|width|height|ratio|position|style|font|weight|transform|animation|ease|layout|mode|type|scheme|target|rel|lottie|src|id)$/', $k ) && 'html' !== $k ) {
			continue;
		}
		if ( preg_match( '/^(#[0-9a-f]{3,8}|https?:\/\/\S+|\d+(\.\d+)?(px|em|rem|%|vh|vw)?)$/i', trim( $value ) ) ) {
			continue;
		}
		if ( 'html' === $k || 'content' === $k || 'answer' === $k ) {
			$attrs[ $key ] = bst_aid_clean_html( $value );
		} elseif ( preg_match( '/(title|heading|text|label|name|eyebrow|subtitle|question)$/', $k ) && ( 'bst/heading' === $name || 'bst/title' === $name || 'title' === $k || 'heading' === $k || 'subtitle' === $k || 'eyebrow' === $k || 'question' === $k || 'name' === $k ) ) {
			$attrs[ $key ] = bst_aid_clean_heading( $value );
		} else {
			$attrs[ $key ] = bst_aid_md_inline( $value );
		}
	}
	return $attrs;
}
