<?php
/**
 * Scrolling text (marquee) block.
 *
 * Items come from child blocks (bst/marquee-item). Content saved before child blocks existed
 * keeps its one-per-line `items` text and prints the same HTML as before.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

$a = (array) $attributes;

// Each entry: array( item HTML, row number, 0 = every row ).
$list  = array();
$fresh = false;
if ( isset( $block ) && $block instanceof WP_Block ) {
	foreach ( $block->inner_blocks as $child ) {
		if ( 'bst/marquee-item' !== $child->name ) {
			continue;
		}
		$html = trim( (string) $child->render() );
		if ( '' !== $html ) {
			$list[] = array( $html, (int) ( $child->attributes['row'] ?? 0 ) );
		}
	}
}
// The editor preview cannot send child blocks, so it sends their attributes.
if ( ! $list && ! empty( $a['previewItems'] ) && is_array( $a['previewItems'] ) ) {
	foreach ( array_slice( $a['previewItems'], 0, 60 ) as $item ) {
		$item = is_array( $item ) ? $item : array();
		$html = trim( (string) render_block( array( 'blockName' => 'bst/marquee-item', 'attrs' => $item, 'innerBlocks' => array(), 'innerHTML' => '', 'innerContent' => array() ) ) );
		if ( '' !== $html ) {
			$list[] = array( $html, (int) ( $item['row'] ?? 0 ) );
		}
	}
}
if ( $list ) {
	$fresh = true;
} else {
	foreach ( array_filter( array_map( 'trim', explode( "\n", (string) ( $a['items'] ?? '' ) ) ) ) as $item ) {
		$list[] = array( '<span class="bs-marquee__item">' . wp_kses_post( $item ) . '</span>', 0 );
	}
}
if ( ! $list ) {
	return;
}

$sep = sanitize_key( (string) ( $a['separator'] ?? 'star' ) );
$sep = 'none' === $sep ? '' : ( 'dot' === $sep ? '<span class="bs-marquee__dot" aria-hidden="true"></span>' : '<span class="bs-marquee__sep" aria-hidden="true">' . bst_icon( $sep, 18 ) . '</span>' );

$dir      = 'right' === ( $a['direction'] ?? 'left' ) ? 'right' : 'left';
$vertical = 'vertical' === ( $a['orientation'] ?? '' );
$rows     = (int) bst_block_num( $a, 'rows', 1, 1, 3 );
$tilt     = $vertical ? 0 : round( (float) bst_block_num( $a, 'tilt', 0, -6, 6 ), 1 );
$cross    = $tilt && ! empty( $a['cross'] );
$button   = ! empty( $a['pauseButton'] );
$fade     = (int) bst_block_num( $a, 'fade', 0, 0, 30 );
$loops    = (int) bst_block_num( $a, 'loops', 0, 0, 20 );
$shift    = (int) bst_block_num( $a, 'rowSpeedShift', 0, 0, 80 );
$style    = bst_choice( (string) ( $a['itemStyle'] ?? '' ), array( '', 'pill', 'card', 'logo' ), '' );
$same_dir = 'same' === ( $a['rowDirection'] ?? '' );
// The old single-strip markup is kept whenever no new layout option is on.
$simple = 1 === $rows && ! $vertical && ! $tilt && ! $button;

/** One pair of tracks; the copy makes the loop seamless and is hidden from assistive technology. */
$pair = static function ( $row ) {
	return '<div class="bs-marquee__track">' . $row . '</div><div class="bs-marquee__track" aria-hidden="true">' . str_replace( '<a ', '<a tabindex="-1" ', $row ) . '</div>';
};

$lanes = array();
for ( $r = 0; $r < $rows; $r++ ) {
	$own = array();
	foreach ( $list as $entry ) {
		if ( 0 === $entry[1] || min( $entry[1], $rows ) === $r + 1 ) {
			$own[] = $entry[0];
		}
	}
	if ( ! $own ) {
		continue;
	}
	// Rows that share the list start at a different item so they do not line up.
	$offset = (int) floor( count( $own ) * $r / $rows );
	if ( $offset ) {
		$own = array_merge( array_slice( $own, $offset ), array_slice( $own, 0, $offset ) );
	}
	$lanes[] = implode( $sep, $own ) . $sep;
}
if ( ! $lanes ) {
	return;
}

$build = static function ( $flip ) use ( $lanes, $pair, $dir, $same_dir, $shift ) {
	$out = '';
	foreach ( $lanes as $i => $row ) {
		$rev = ( 'right' === $dir ) !== ( ! $same_dir && 1 === $i % 2 );
		$rev = $flip ? ! $rev : $rev;
		$out .= sprintf(
			'<div class="bs-marquee__row%1$s"%2$s>%3$s</div>',
			$rev ? ' is-rev' : '',
			$shift && $i ? ' style="--bs-mq-k:' . ( 1 + $i * $shift / 100 ) . '"' : '',
			$pair( $row )
		);
	}
	return $out;
};

if ( $simple ) {
	$body = $pair( $lanes[0] );
} else {
	$body = $build( false );
	if ( $tilt ) {
		$body = '<div class="bs-marquee__band">' . $body . '</div>';
		if ( $cross ) {
			$body .= '<div class="bs-marquee__band bs-marquee__band--cross" aria-hidden="true">' . str_replace( '<a ', '<a tabindex="-1" ', $build( true ) ) . '</div>';
		}
	}
	if ( $button ) {
		$body .= sprintf(
			'<button type="button" class="bs-marquee__toggle" hidden aria-pressed="false" aria-label="%1$s" data-pause="%1$s" data-play="%2$s"></button>',
			esc_attr__( 'Pause animation', 'bigspy-ai-theme-wp' ),
			esc_attr__( 'Play animation', 'bigspy-ai-theme-wp' )
		);
	}
}

$class = 'bs-marquee bs-marquee--' . $dir . ( ! empty( $a['pauseHover'] ) ? ' bs-marquee--pause' : '' ) . ( ! empty( $a['outline'] ) ? ' bs-marquee--outline' : '' )
	. ( $style ? ' bs-marquee--' . $style : '' )
	. ( $simple ? '' : ' bs-marquee--rows' )
	. ( $vertical ? ' bs-marquee--vertical' : '' )
	. ( $tilt ? ' bs-marquee--tilt' : '' )
	. ( $cross ? ' bs-marquee--cross' : '' )
	. ( $fade ? ' bs-marquee--fade' : '' )
	. ( $fresh ? ' bs-marquee--fill' : '' );

$opt = static function ( $key, $min, $max, $unit ) use ( $a ) {
	return isset( $a[ $key ] ) && '' !== $a[ $key ] && null !== $a[ $key ] ? bst_block_num( $a, $key, $min, $min, $max ) . $unit : '';
};
$vars = bst_style_vars(
	array(
		'--bs-mq-speed'       => bst_block_num( $a, 'speed', 30, 5, 200 ) . 's',
		'--bs-mq-size'        => bst_block_num( $a, 'size', 28, 10, 160 ) . 'px',
		'--bs-mq-gap'         => bst_block_num( $a, 'gap', 40, 0, 200 ) . 'px',
		'--bs-mq-py'          => bst_block_num( $a, 'paddingY', 18, 0, 120 ) . 'px',
		'--bs-mq-color'       => bst_block_color( $a, 'color' ),
		'--bs-mq-bg'          => bst_block_color( $a, 'bgColor' ),
		'--bs-mq-weight'      => bst_choice( (string) ( $a['weight'] ?? '700' ), array( '400', '500', '600', '700', '800', '900' ), '700' ),
		'--bs-mq-speed-t'     => $opt( 'speedTablet', 5, 200, 's' ),
		'--bs-mq-speed-m'     => $opt( 'speedMobile', 5, 200, 's' ),
		'--bs-mq-size-t'      => $opt( 'sizeTablet', 10, 160, 'px' ),
		'--bs-mq-size-m'      => $opt( 'sizeMobile', 10, 160, 'px' ),
		'--bs-mq-gap-t'       => $opt( 'gapTablet', 0, 200, 'px' ),
		'--bs-mq-gap-m'       => $opt( 'gapMobile', 0, 200, 'px' ),
		'--bs-mq-py-t'        => $opt( 'paddingYTablet', 0, 120, 'px' ),
		'--bs-mq-py-m'        => $opt( 'paddingYMobile', 0, 120, 'px' ),
		'--bs-mq-item-bg'     => $style ? bst_block_color( $a, 'itemBg' ) : '',
		'--bs-mq-item-bc'     => $style ? bst_block_color( $a, 'itemBorderColor' ) : '',
		'--bs-mq-item-r'      => $style ? $opt( 'itemRadius', 0, 100, 'px' ) : '',
		'--bs-mq-img-h'       => $opt( 'imgHeight', 12, 200, 'px' ),
		'--bs-mq-row-gap'     => $simple ? '' : $opt( 'rowGap', 0, 120, 'px' ),
		'--bs-mq-h'           => $vertical ? bst_block_num( $a, 'height', 420, 160, 1200 ) . 'px' : '',
		'--bs-mq-fade'        => $fade ? $fade . '%' : '',
		'--bs-mq-tilt'        => $tilt ? $tilt . 'deg' : '',
		'--bs-mq-tilt-abs'    => $tilt ? abs( $tilt ) : '',
		'--bs-mq-cross-bg'    => $cross ? bst_block_color( $a, 'crossBg' ) : '',
		'--bs-mq-cross-color' => $cross ? bst_block_color( $a, 'crossColor' ) : '',
		'--bs-mq-loops'       => $loops ? $loops : '',
	)
);

$extra  = array( 'class' => $class, 'style' => $vars );
$scroll = trim( ( ! empty( $a['scrollBoost'] ) ? 'boost ' : '' ) . ( ! empty( $a['scrollReverse'] ) ? 'reverse' : '' ) );
if ( $scroll ) {
	$extra['data-bs-mq-scroll'] = $scroll;
}
// Pauses the strip while it is off screen, runs the pause button, scroll effects and short-row filling.
wp_enqueue_script( 'bst-marquee' );

printf(
	'<div %1$s>%2$s</div>',
	get_block_wrapper_attributes( $extra ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	$body // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
);
