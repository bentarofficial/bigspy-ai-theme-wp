<?php
/**
 * Shape dividers for the Section block (top / bottom), like Flatsome's section dividers.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

function bst_shape_paths() {
	return array(
		'wave' => array( 'M0,60 C150,100 350,0 500,40 C650,80 850,20 1000,50 L1000,100 L0,100 Z' ),
		'waves' => array( 'M0,40 C200,90 400,0 600,50 C800,100 900,30 1000,40 L1000,100 L0,100 Z', 'M0,70 C200,100 450,30 650,70 C850,100 950,60 1000,70 L1000,100 L0,100 Z' ),
		'curve' => array( 'M0,100 C300,0 700,0 1000,100 Z' ),
		'curve-inverse' => array( 'M0,0 C300,100 700,100 1000,0 L1000,100 L0,100 Z' ),
		'tilt' => array( 'M0,100 L1000,0 L1000,100 Z' ),
		'triangle' => array( 'M0,100 L500,0 L1000,100 Z' ),
		'arrow' => array( 'M0,100 L0,70 L470,70 L500,30 L530,70 L1000,70 L1000,100 Z' ),
		'mountains' => array( 'M0,100 L0,60 L150,20 L300,70 L450,10 L600,60 L750,30 L900,70 L1000,40 L1000,100 Z' ),
		'zigzag' => array( 'M0,100 L0,70 L50,40 L100,70 L150,40 L200,70 L250,40 L300,70 L350,40 L400,70 L450,40 L500,70 L550,40 L600,70 L650,40 L700,70 L750,40 L800,70 L850,40 L900,70 L950,40 L1000,70 L1000,100 Z' ),
		'clouds' => array( 'M0,100 L0,70 C40,50 80,80 120,65 C160,40 220,60 250,70 C290,45 340,55 370,68 C410,40 470,55 500,70 C540,45 600,58 630,68 C670,42 730,55 760,70 C800,48 860,60 890,70 C930,50 970,62 1000,60 L1000,100 Z' ),
		'drops' => array( 'M0,100 L0,40 Q25,95 50,40 Q75,55 100,40 Q125,95 150,40 Q175,55 200,40 Q225,95 250,40 Q275,55 300,40 Q325,95 350,40 Q375,55 400,40 Q425,95 450,40 Q475,55 500,40 Q525,95 550,40 Q575,55 600,40 Q625,95 650,40 Q675,55 700,40 Q725,95 750,40 Q775,55 800,40 Q825,95 850,40 Q875,55 900,40 Q925,95 950,40 Q975,55 1000,40 L1000,100 Z' ),
		'pyramids' => array( 'M0,100 L0,80 L120,30 L240,80 L360,20 L480,80 L600,35 L720,80 L840,25 L1000,80 L1000,100 Z' ),
		'book' => array( 'M0,100 L0,60 C200,60 400,40 500,100 C600,40 800,60 1000,60 L1000,100 Z' ),
		'split' => array( 'M0,100 L0,60 L460,60 L500,100 L540,60 L1000,60 L1000,100 Z' ),
		'waves-pattern' => array( 'M0,100 L0,60 C20,30 40,90 62,60 C82,30 102,90 124,60 C144,30 164,90 186,60 C206,30 226,90 248,60 C268,30 288,90 310,60 C330,30 350,90 372,60 C392,30 412,90 434,60 C454,30 474,90 496,60 C516,30 536,90 558,60 C578,30 598,90 620,60 C640,30 660,90 682,60 C702,30 722,90 744,60 C764,30 784,90 806,60 C826,30 846,90 868,60 C888,30 908,90 930,60 C950,30 970,90 992,60 C1012,30 1032,90 1054,60 L1000,100 Z' ),
	);
}

/**
 * @param string $shape  Shape key.
 * @param string $pos    top|bottom.
 * @param array  $a      color, height (px), flip, front.
 */
function bst_shape_divider( $shape, $pos, $a ) {
	$paths = bst_shape_paths();
	if ( ! isset( $paths[ $shape ] ) ) {
		return '';
	}
	$svg = '';
	foreach ( $paths[ $shape ] as $i => $d ) {
		$svg .= '<path d="' . esc_attr( $d ) . '"' . ( $i < count( $paths[ $shape ] ) - 1 ? ' opacity=".45"' : '' ) . '/>';
	}
	$style = bst_style_vars(
		array(
			'color'          => $a['color'] ? $a['color'] : '#ffffff',
			'--bs-shape-h'   => (int) $a['height'] . 'px',
		)
	);
	$class = 'bs-shape bs-shape--' . $pos . ( $a['flip'] ? ' is-flipped' : '' ) . ( $a['front'] ? ' is-front' : '' );
	return '<div class="' . esc_attr( $class ) . '" style="' . esc_attr( $style ) . '" aria-hidden="true"><svg viewBox="0 0 1000 100" preserveAspectRatio="none" fill="currentColor">' . $svg . '</svg></div>';
}

/** Background video markup: YouTube URL or a direct .mp4/.webm file. */
function bst_section_video( $url ) {
	$url = trim( (string) $url );
	if ( '' === $url ) {
		return '';
	}
	if ( preg_match( '~(?:youtube\.com/(?:watch\?v=|embed/|shorts/)|youtu\.be/)([A-Za-z0-9_-]{6,})~', $url, $m ) ) {
		$src = 'https://www.youtube-nocookie.com/embed/' . $m[1] . '?autoplay=1&mute=1&loop=1&controls=0&playsinline=1&rel=0&modestbranding=1&playlist=' . $m[1];
		return '<div class="bs-section__video is-embed" aria-hidden="true"><iframe src="' . esc_url( $src ) . '" title="" tabindex="-1" allow="autoplay; encrypted-media" loading="lazy"></iframe></div>';
	}
	if ( preg_match( '~\.(mp4|webm|ogv)(\?.*)?$~i', $url ) ) {
		return '<div class="bs-section__video" aria-hidden="true"><video src="' . esc_url( $url ) . '" autoplay muted loop playsinline preload="metadata"></video></div>';
	}
	return '';
}
