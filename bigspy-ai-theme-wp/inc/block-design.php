<?php
/**
 * "Design" options shared by every BIGSPY block (like Flatsome's common element
 * options): responsive margin/padding, background, text color, border, radius,
 * depth (shadow) + hover depth, max width and z-index.
 *
 * Stored in one `bsDesign` object attribute and printed as a tiny scoped <style>.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

add_filter(
	'register_block_type_args',
	function ( $args, $name ) {
		if ( 0 === strpos( (string) $name, 'bst/' ) ) {
			$args['attributes'] = isset( $args['attributes'] ) && is_array( $args['attributes'] ) ? $args['attributes'] : array();
			$args['attributes'] += array(
				'bsDesign' => array( 'type' => 'object', 'default' => array() ),
				'bsWidth'  => array( 'type' => 'string', 'default' => '' ),
			);
		}
		return $args;
	},
	10,
	2
);

/** Shadow presets shared with the editor (depth 1–5, Flatsome style). */
function bst_design_shadows() {
	return array(
		1 => '0 1px 3px -2px rgba(0,0,0,.12),0 1px 1px rgba(0,0,0,.08)',
		2 => '0 3px 6px -4px rgba(0,0,0,.16),0 3px 6px rgba(0,0,0,.1)',
		3 => '0 10px 20px rgba(0,0,0,.12),0 6px 6px rgba(0,0,0,.1)',
		4 => '0 14px 28px rgba(0,0,0,.18),0 10px 10px rgba(0,0,0,.12)',
		5 => '0 19px 38px rgba(0,0,0,.22),0 15px 12px rgba(0,0,0,.14)',
	);
}

/** One CSS length from a design value ("", number or number+unit). */
function bst_design_len( $value ) {
	$value = trim( (string) $value );
	if ( '' === $value ) {
		return '';
	}
	if ( is_numeric( $value ) ) {
		return ( 0 == $value ? '0' : ( $value + 0 ) . 'px' ); // phpcs:ignore Universal.Operators.StrictComparisons
	}
	return preg_match( '/^-?[0-9.]+(px|em|rem|%|vh|vw)$|^auto$/', $value ) ? $value : '';
}

function bst_design_color( $value ) {
	$value = trim( (string) $value );
	if ( '' === $value ) {
		return '';
	}
	if ( preg_match( '/^#[0-9a-f]{3,8}$/i', $value ) || preg_match( '/^(rgb|rgba|hsl|hsla)\([0-9.,%\s\/]+\)$/i', $value ) || preg_match( '/^var\(--[a-z0-9-]+\)$/i', $value ) ) {
		return $value;
	}
	return '';
}

/**
 * CSS declarations for one device. $d is the design array, $suffix '' | 'T' | 'M'.
 */
function bst_design_rules( $d, $suffix ) {
	$out = array();
	foreach ( array( 'margin' => 'm', 'padding' => 'p' ) as $prop => $key ) {
		foreach ( array( 'top' => 't', 'right' => 'r', 'bottom' => 'b', 'left' => 'l' ) as $side => $s ) {
			$v = bst_design_len( $d[ $key . $s . $suffix ] ?? '' );
			if ( '' !== $v ) {
				$out[] = "$prop-$side:$v";
			}
		}
	}
	$mw = bst_design_len( $d[ 'mw' . $suffix ] ?? '' );
	if ( '' !== $mw ) {
		$out[] = "max-width:$mw";
	}
	$w = (string) ( $d[ 'w' . $suffix ] ?? '' );
	if ( 'auto' === $w ) {
		$out[] = 'width:auto;display:inline-block';
	} elseif ( 'full' === $w ) {
		$out[] = 'width:100%';
	} elseif ( '' !== bst_design_len( $w ) ) {
		$out[] = 'width:' . bst_design_len( $w );
	}
	$ta = (string) ( $d[ 'ta' . $suffix ] ?? '' );
	if ( in_array( $ta, array( 'left', 'center', 'right', 'justify' ), true ) ) {
		$jc    = array( 'left' => 'flex-start', 'center' => 'center', 'right' => 'flex-end', 'justify' => 'space-between' );
		$out[] = "text-align:$ta;justify-content:{$jc[ $ta ]};--bs-ta:$ta";
	}
	$fs = bst_design_len( $d[ 'fs' . $suffix ] ?? '' );
	if ( '' !== $fs ) {
		$out[] = "font-size:$fs;--bs-fs:$fs";
	}
	$lh = trim( (string) ( $d[ 'lh' . $suffix ] ?? '' ) );
	if ( '' !== $lh && preg_match( '/^[0-9.]+(px|em|rem)?$/', $lh ) ) {
		$out[] = "line-height:$lh";
	}
	$ls = bst_design_len( $d[ 'ls' . $suffix ] ?? '' );
	if ( '' !== $ls ) {
		$out[] = "letter-spacing:$ls";
	}
	$pos = (string) ( $d[ 'pos' . $suffix ] ?? '' );
	if ( in_array( $pos, array( 'relative', 'absolute', 'fixed', 'static' ), true ) ) {
		$out[] = "position:$pos";
	}
	foreach ( array( 'top' => 'ot', 'right' => 'or', 'bottom' => 'ob', 'left' => 'ol' ) as $side => $key ) {
		$v = bst_design_len( $d[ $key . $suffix ] ?? '' );
		if ( '' !== $v ) {
			$out[] = "$side:$v";
		}
	}
	if ( '' === $suffix ) {
		if ( $mw && ! empty( $d['mwc'] ) ) {
			$out[] = 'margin-left:auto;margin-right:auto';
		}
		$fonts = array( 'heading' => 'var(--bs-font-heading)', 'body' => 'var(--bs-font-body,inherit)', 'serif' => 'Georgia,"Times New Roman",serif', 'mono' => 'ui-monospace,Menlo,Consolas,monospace', 'system' => 'system-ui,-apple-system,"Segoe UI",Roboto,sans-serif' );
		if ( isset( $fonts[ $d['ff'] ?? '' ] ) ) {
			$out[] = 'font-family:' . $fonts[ $d['ff'] ];
		}
		if ( in_array( (string) ( $d['fw'] ?? '' ), array( '300', '400', '500', '600', '700', '800', '900' ), true ) ) {
			$out[] = 'font-weight:' . $d['fw'];
		}
		if ( in_array( $d['tt'] ?? '', array( 'uppercase', 'lowercase', 'capitalize', 'none' ), true ) ) {
			$out[] = 'text-transform:' . $d['tt'];
		}
		if ( 'italic' === ( $d['fst'] ?? '' ) ) {
			$out[] = 'font-style:italic';
		}
		if ( in_array( $d['td'] ?? '', array( 'underline', 'line-through', 'overline' ), true ) ) {
			$out[] = 'text-decoration:' . $d['td'];
		}
		$tsh = array( 1 => '0 1px 2px rgba(0,0,0,.25)', 2 => '0 2px 10px rgba(0,0,0,.35)', 3 => '0 4px 24px rgba(0,0,0,.5)' );
		if ( isset( $tsh[ absint( $d['tsh'] ?? 0 ) ] ) ) {
			$out[] = 'text-shadow:' . $tsh[ absint( $d['tsh'] ) ];
		}
		$bg = bst_design_color( $d['bg'] ?? '' );
		if ( $bg ) {
			$out[] = "background-color:$bg";
		}
		$color = bst_design_color( $d['color'] ?? '' );
		if ( $color ) {
			$out[] = "color:$color;--bs-heading-color:$color";
		}
		$bw = bst_design_len( $d['bw'] ?? '' );
		if ( '' !== $bw && '0' !== $bw ) {
			$style = in_array( $d['bs'] ?? '', array( 'solid', 'dashed', 'dotted', 'double' ), true ) ? $d['bs'] : 'solid';
			$bc    = bst_design_color( $d['bc'] ?? '' );
			$out[] = "border:$bw $style " . ( $bc ? $bc : 'currentColor' );
		}
		$br = bst_design_len( $d['br'] ?? '' );
		if ( '' !== $br ) {
			$out[] = "border-radius:$br;overflow:hidden";
		}
		$shadows = bst_design_shadows();
		$depth   = absint( $d['depth'] ?? 0 );
		if ( isset( $shadows[ $depth ] ) ) {
			$out[] = 'box-shadow:' . $shadows[ $depth ];
		}
		if ( isset( $d['z'] ) && '' !== $d['z'] && is_numeric( $d['z'] ) ) {
			$out[] = 'position:relative;z-index:' . (int) $d['z'];
		}
		if ( bst_design_has_hover( $d ) ) {
			$out[] = 'transition:background-color .3s ease,color .3s ease,border-color .3s ease,border-radius .3s ease,box-shadow .3s ease,transform .3s ease';
		}
	}
	return $out;
}

function bst_design_has_hover( $d ) {
	foreach ( array( 'depthHover', 'hbg', 'hcolor', 'hbc', 'hbr', 'hmove', 'hscale' ) as $key ) { // Typography keys need no transition.
		if ( isset( $d[ $key ] ) && '' !== $d[ $key ] && 0 !== $d[ $key ] ) {
			return true;
		}
	}
	return false;
}

/** :hover declarations. */
function bst_design_hover_rules( $d ) {
	$out     = array();
	$shadows = bst_design_shadows();
	$depth   = absint( $d['depthHover'] ?? 0 );
	if ( isset( $shadows[ $depth ] ) ) {
		$out[] = 'box-shadow:' . $shadows[ $depth ];
	}
	foreach ( array( 'hbg' => 'background-color', 'hcolor' => 'color', 'hbc' => 'border-color' ) as $key => $prop ) {
		$c = bst_design_color( $d[ $key ] ?? '' );
		if ( $c ) {
			$out[] = "$prop:$c";
		}
	}
	$br = bst_design_len( $d['hbr'] ?? '' );
	if ( '' !== $br ) {
		$out[] = "border-radius:$br";
	}
	$transform = array();
	if ( isset( $d['hmove'] ) && is_numeric( $d['hmove'] ) && 0 != $d['hmove'] ) { // phpcs:ignore Universal.Operators.StrictComparisons
		$transform[] = 'translateY(' . ( -1 * (float) $d['hmove'] ) . 'px)';
	}
	if ( isset( $d['hscale'] ) && is_numeric( $d['hscale'] ) && 100 != $d['hscale'] && $d['hscale'] > 0 ) { // phpcs:ignore Universal.Operators.StrictComparisons
		$transform[] = 'scale(' . ( (float) $d['hscale'] / 100 ) . ')';
	}
	if ( $transform ) {
		$out[] = 'transform:' . implode( ' ', $transform );
	}
	return $out;
}

/** Full scoped CSS for a design array, or ''. */
function bst_design_css( $d, $selector ) {
	if ( ! is_array( $d ) || ! $d ) {
		return '';
	}
	$css = '';
	foreach ( array( '' => '', 'T' => '@media (max-width:849px)', 'M' => '@media (max-width:549px)' ) as $suffix => $media ) {
		$rules = bst_design_rules( $d, $suffix );
		if ( ! $rules ) {
			continue;
		}
		$block = $selector . '{' . implode( ';', $rules ) . '}';
		$ta    = (string) ( $d[ 'ta' . $suffix ] ?? '' );
		if ( in_array( $ta, array( 'left', 'center', 'right' ), true ) ) {
			$m      = array( 'left' => 'margin-left:0;margin-right:auto', 'center' => 'margin-left:auto;margin-right:auto', 'right' => 'margin-left:auto;margin-right:0' );
			$ai     = array( 'left' => 'flex-start', 'center' => 'center', 'right' => 'flex-end' );
			$block .= $selector . ' :is(img,.bs-image__frame,.bs-icon,.bs-icon-box__icon,.bs-stars,.bs-countdown,.bs-video-button){' . $m[ $ta ] . '}' . $selector . ' :is(.bs-buttons,.bs-social,.bs-share__list,.bs-pay,.bs-icon-list){justify-content:' . $ai[ $ta ] . '}' . $selector . '.bs-column,' . $selector . ' > .bs-column__inner{align-items:' . $ai[ $ta ] . '}';
		}
		$css .= $media ? $media . '{' . $block . '}' : $block;
	}
	$hover = bst_design_hover_rules( $d );
	if ( $hover ) {
		$css .= '@media (hover:hover){' . $selector . ':hover{' . implode( ';', $hover ) . '}}';
	}
	return $css;
}

/** Scoped class for a design array. */
function bst_design_class( $d ) {
	return 'bsd-' . substr( md5( wp_json_encode( $d ) ), 0, 10 );
}

/** Registry: class => css; printed in <head> when known in advance, else in the footer. */
function bst_design_registry( $class = null, $css = null ) {
	static $all = array();
	if ( null !== $class ) {
		$all[ $class ] = $css;
	}
	return $all;
}

function bst_design_collect( $blocks ) {
	foreach ( (array) $blocks as $block ) {
		$d = $block['attrs']['bsDesign'] ?? null;
		if ( 0 === strpos( (string) ( $block['blockName'] ?? '' ), 'bst/' ) && is_array( $d ) && $d ) {
			$class = bst_design_class( $d );
			bst_design_registry( $class, bst_design_css( $d, '.' . $class ) );
		}
		if ( ! empty( $block['innerBlocks'] ) ) {
			bst_design_collect( $block['innerBlocks'] );
		}
	}
}

/** Pre-scan the main post and the reusable header/footer designs so their CSS lands in <head>. */
function bst_design_head_css() {
	$ids = array();
	if ( is_singular() ) {
		$ids[] = get_queried_object_id();
	}
	foreach ( array( 'header_design_page', 'footer_design_page', 'footer_block' ) as $mod ) {
		if ( function_exists( 'bst_mod' ) && absint( bst_mod( $mod ) ) ) {
			$ids[] = absint( bst_mod( $mod ) );
		}
	}
	foreach ( array_unique( $ids ) as $id ) {
		$post = get_post( $id );
		if ( $post && has_blocks( $post->post_content ) ) {
			bst_design_collect( parse_blocks( $post->post_content ) );
		}
	}
	$css = implode( '', array_filter( bst_design_registry() ) );
	if ( $css ) {
		echo '<style id="bst-block-design">' . wp_strip_all_tags( $css ) . '</style>' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
	bst_design_printed( array_keys( bst_design_registry() ) );
}
add_action( 'wp_head', 'bst_design_head_css', 99 );

function bst_design_printed( $add = array() ) {
	static $done = array();
	foreach ( $add as $class ) {
		$done[ $class ] = true;
	}
	return $done;
}

/** Blocks rendered outside the pre-scan (widgets, shortcodes, archives…). */
function bst_design_footer_css() {
	$css = '';
	foreach ( array_diff_key( bst_design_registry(), bst_design_printed() ) as $rule ) {
		$css .= $rule;
	}
	if ( $css ) {
		echo '<style id="bst-block-design-late">' . wp_strip_all_tags( $css ) . '</style>' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}
add_action( 'wp_footer', 'bst_design_footer_css', 5 );

/** Width outside a Section: container (default), wide or full screen. */
function bst_apply_block_width( $content, $block ) {
	$name  = (string) ( $block['blockName'] ?? '' );
	$width = (string) ( $block['attrs']['bsWidth'] ?? '' );
	if ( 0 !== strpos( $name, 'bst/' ) || ! in_array( $width, array( 'wide', 'full' ), true ) || '' === trim( (string) $content ) || ! class_exists( 'WP_HTML_Tag_Processor' ) ) {
		return $content;
	}
	$tags = new WP_HTML_Tag_Processor( $content );
	if ( $tags->next_tag() ) {
		$tags->add_class( 'align' . $width );
		$tags->add_class( 'bs-width-' . $width );
		return $tags->get_updated_html();
	}
	return $content;
}
add_filter( 'render_block', 'bst_apply_block_width', 12, 2 );

/** Add the scoped class to the block's first element. */
function bst_apply_block_design( $content, $block ) {
	$name = (string) ( $block['blockName'] ?? '' );
	$d    = $block['attrs']['bsDesign'] ?? null;
	if ( 0 !== strpos( $name, 'bst/' ) || ! is_array( $d ) || ! $d || '' === trim( (string) $content ) || ! class_exists( 'WP_HTML_Tag_Processor' ) ) {
		return $content;
	}
	$class = bst_design_class( $d );
	$css   = bst_design_css( $d, '.' . $class );
	if ( '' === $css ) {
		return $content;
	}
	$tags = new WP_HTML_Tag_Processor( $content );
	if ( ! $tags->next_tag() ) {
		return $content;
	}
	$tags->add_class( $class );
	bst_design_registry( $class, $css );
	return $tags->get_updated_html();
}
add_filter( 'render_block', 'bst_apply_block_design', 11, 2 );
