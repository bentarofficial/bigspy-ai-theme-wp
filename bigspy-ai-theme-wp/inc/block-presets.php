<?php
/**
 * Per-block style presets: the old Customizer "CSS library" recipes, now picked
 * directly in each block's sidebar ("Style presets") and previewed live.
 *
 * A preset is a class `bsp-<id>` on the block root. `&` in a rule is the block
 * root; options in an exclusive group replace each other.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

/**
 * Catalog: block => group key => array( label, exclusive, options( id => array( label, css ) ) ).
 */
function bst_block_presets() {
	$t = function ( $s ) {
		return __( $s, 'bigspy-ai-theme-wp' ); // phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText
	};
	$shadow_sm = '0 4px 14px rgba(15,23,42,.10)';
	$shadow_md = '0 10px 30px rgba(15,23,42,.14)';
	$shadow_lg = '0 22px 50px -12px rgba(15,23,42,.30)';
	$hover     = '@media(hover:hover) and (prefers-reduced-motion:no-preference){';

	$card_groups = function ( $part, $prefix, $autopad = false ) use ( $t, $shadow_sm, $shadow_md, $shadow_lg, $hover ) {
		$x = $part ? "& $part" : '&';
		// Blocks with no padding of their own (Column) get inner space with a frame, so content never touches the edge.
		$ap = $autopad ? 'padding:24px;' : '';
		return array(
			'shape'  => array( $t( 'Corners' ), true, array(
				$prefix . '-r0'  => array( $t( 'Square' ), "{$x}{border-radius:0}" ),
				$prefix . '-r12' => array( $t( 'Soft' ), "{$x}{border-radius:12px}" ),
				$prefix . '-r20' => array( $t( 'Round' ), "{$x}{border-radius:20px}" ),
				$prefix . '-r32' => array( $t( 'Extra round' ), "{$x}{border-radius:32px}" ),
			) ),
			'frame'  => array( $t( 'Background & frame' ), true, array(
				$prefix . '-wc'  => array( $t( 'White card' ), "{$x}{{$ap}background:#fff;border:1px solid color-mix(in srgb,var(--bs-primary) 10%,#e6edf2)}" ),
				$prefix . '-bd'  => array( $t( 'Thin border' ), "{$x}{{$ap}border:1px solid var(--bs-border)}" ),
				$prefix . '-bdp' => array( $t( 'Brand border' ), "{$x}{{$ap}border:2px solid var(--bs-primary)}" ),
				$prefix . '-bdt' => array( $t( 'Top accent' ), "{$x}{{$ap}border-top:4px solid var(--bs-primary)}" ),
				$prefix . '-bdl' => array( $t( 'Left accent (2nd color)' ), "{$x}{{$ap}border-left:3px solid var(--bs-secondary,var(--bs-primary))}" ),
				$prefix . '-bg'  => array( $t( 'Light background' ), "{$x}{{$ap}background:var(--bs-light)}" ),
				$prefix . '-note' => array( $t( 'Note (2nd color)' ), "{$x}{{$ap}background:color-mix(in srgb,var(--bs-secondary,#f5cf4c) 14%,#fff);border-left:4px solid var(--bs-secondary,#f5cf4c);border-radius:0 14px 14px 0}" ),
				$prefix . '-gl'  => array( $t( 'Glass' ), "{$x}{{$ap}background:rgba(255,255,255,.6);backdrop-filter:blur(10px);border:1px solid rgba(255,255,255,.7)}" ),
				$prefix . '-dk'  => array( $t( 'Dark' ), "{$x}{{$ap}background:var(--bs-heading,#111827);color:#fff}{$x} :is(h1,h2,h3,h4,p,li){color:inherit}" ),
			) ),
			'shadow' => array( $t( 'Shadow' ), true, array(
				$prefix . '-sh0' => array( $t( 'Whisper' ), "{$x}{{$ap}box-shadow:0 10px 32px rgba(17,66,96,.05)}" ),
				$prefix . '-sh1' => array( $t( 'Light' ), "{$x}{{$ap}box-shadow:$shadow_sm}" ),
				$prefix . '-sh2' => array( $t( 'Medium' ), "{$x}{{$ap}box-shadow:$shadow_md}" ),
				$prefix . '-sh3' => array( $t( 'Deep' ), "{$x}{{$ap}box-shadow:$shadow_lg}" ),
				$prefix . '-shc' => array( $t( 'Brand glow' ), "{$x}{{$ap}box-shadow:0 12px 32px -10px color-mix(in srgb,var(--bs-primary) 55%,transparent)}" ),
			) ),
			'pad'    => array( $t( 'Inner space' ), true, array(
				$prefix . '-p0'  => array( $t( 'None' ), "{$x}{padding:0}" ),
				$prefix . '-p16' => array( $t( 'Compact' ), "{$x}{padding:16px}" ),
				$prefix . '-p26' => array( $t( 'Comfortable' ), "{$x}{padding:26px}@media(max-width:849px){{$x}{padding:22px}}" ),
				$prefix . '-p40' => array( $t( 'Spacious' ), "{$x}{padding:40px}@media(max-width:849px){{$x}{padding:26px}}" ),
				$prefix . '-pm'  => array( $t( 'Media card (image to the edge)' ), "{$x}{padding:16px 16px 24px}{$x}>:not(.bs-image):not(.bs-image-box){margin-inline:8px}{$x}>.bs-image{margin-bottom:22px}" ),
			) ),
			'layout' => array( $t( 'Layout' ), false, array(
				$prefix . '-eq'  => array( $t( 'Equal height, buttons at bottom' ), "{$x}{height:100%;display:flex;flex-direction:column;box-sizing:border-box}{$x} .bs-buttons{margin-top:auto;padding-top:12px}" ),
				$prefix . '-mut' => array( $t( 'Muted text' ), "{$x} .bs-text{color:color-mix(in srgb,var(--bs-text) 80%,#fff)}" ),
			) ),
			'hover'  => array( $t( 'On hover' ), true, array(
				$prefix . '-hl' => array( $t( 'Lift' ), "$hover{$x}{transition:transform .25s,box-shadow .25s}{$x}:hover{transform:translateY(-5px);box-shadow:$shadow_md}}" ),
				$prefix . '-hls' => array( $t( 'Lift a little' ), "$hover{$x}{transition:transform .2s,box-shadow .2s}{$x}:hover{transform:translateY(-4px);box-shadow:0 16px 32px rgba(17,66,96,.1)}}" ),
				$prefix . '-hb' => array( $t( 'Brand border' ), "{$x}{transition:border-color .2s}{$x}:hover{border-color:var(--bs-primary)}" ),
				$prefix . '-hs' => array( $t( 'Grow' ), "$hover{$x}{transition:transform .25s}{$x}:hover{transform:scale(1.03)}}" ),
			) ),
		);
	};

	return array(
		'bst/section'     => array(
			'bg'    => array( $t( 'Background' ), true, array(
				'sec-soft'  => array( $t( 'Soft brand gradient' ), '&{background:linear-gradient(115deg,color-mix(in srgb,var(--bs-primary) 8%,#fff),color-mix(in srgb,var(--bs-primary) 3%,#fff))!important}' ),
				'sec-light' => array( $t( 'Light' ), '&{background:var(--bs-light)!important}' ),
				'sec-white' => array( $t( 'White' ), '&{background:#fff!important}' ),
				'sec-brand' => array( $t( 'Brand gradient (white text)' ), '&{background:linear-gradient(135deg,var(--bs-primary),color-mix(in srgb,var(--bs-primary) 60%,#000))!important;color:#fff}& :is(h1,h2,h3,h4,p,li){color:inherit}' ),
				'sec-dark'  => array( $t( 'Dark (white text)' ), '&{background:var(--bs-heading,#111827)!important;color:#fff}& :is(h1,h2,h3,h4,p,li){color:inherit}' ),
				'sec-dots'  => array( $t( 'Dotted pattern' ), '&{background-image:radial-gradient(color-mix(in srgb,var(--bs-primary) 18%,transparent) 1px,transparent 1px)!important;background-size:18px 18px!important}' ),
				'sec-warm'  => array( $t( 'Warm (2nd color tint)' ), '&{background:color-mix(in srgb,var(--bs-secondary,#f5cf4c) 12%,#fff)!important}' ),
			) ),
			'lines' => array( $t( 'Lines' ), false, array(
				'sec-lb' => array( $t( 'Line at the bottom' ), '&{border-bottom:1px solid color-mix(in srgb,var(--bs-primary) 10%,#e3edf3)}' ),
				'sec-lt' => array( $t( 'Line at the top' ), '&{border-top:1px solid color-mix(in srgb,var(--bs-primary) 10%,#e3edf3)}' ),
				'sec-at' => array( $t( 'Brand bar at the top' ), '&{border-top:4px solid var(--bs-primary)}' ),
			) ),
			'title' => array( $t( 'Headings inside' ), false, array(
				'sec-htight' => array( $t( 'Slightly tight headings' ), '& .bs-heading{letter-spacing:-.025em}' ),
				'sec-hnarrow' => array( $t( 'Headings max 850px' ), '& .bs-heading{max-width:850px}' ),
				'sec-hsm' => array( $t( 'Main headings 32px on phones' ), '@media(max-width:549px){& :is(h1,h2).bs-heading{font-size:32px!important;line-height:1.2}& .bs-heading{overflow-wrap:normal}}' ),
			) ),
			'shape' => array( $t( 'Shape' ), true, array(
				'sec-r24' => array( $t( 'Rounded box' ), '&{border-radius:24px;overflow:hidden}' ),
				'sec-inset' => array( $t( 'Floating card' ), '&{margin-inline:clamp(12px,3vw,40px);border-radius:28px;overflow:hidden;box-shadow:0 20px 50px -20px rgba(15,23,42,.25)}' ),
			) ),
		),
		'bst/row'         => array(
			'cols' => array( $t( 'Columns' ), false, array(
				'row-mid'  => array( $t( 'Center columns vertically' ), '&{align-items:center}' ),
				'row-gap20' => array( $t( 'Tighter gap on phones' ), '@media(max-width:549px){&{gap:20px}}' ),
				'row-div'  => array( $t( 'Divider lines between columns' ), '@media(min-width:850px){&>.bs-column+.bs-column{border-left:1px solid var(--bs-border)}}' ),
			) ),
		),
		'bst/buttons'     => array(
			'align' => array( $t( 'Alignment' ), true, array(
				'bts-end'  => array( $t( 'Right on desktop, left on phones' ), '&{justify-content:flex-end}@media(max-width:849px){&{justify-content:flex-start}}' ),
				'bts-push' => array( $t( 'Pushed to the bottom of a card' ), '&{margin-top:auto;padding-top:12px}' ),
			) ),
			'gap'   => array( $t( 'Spacing' ), true, array(
				'bts-g10' => array( $t( 'Close (10px)' ), '&{gap:10px}' ),
				'bts-g24' => array( $t( 'Wide (24px)' ), '&{gap:24px}' ),
			) ),
		),
		'bst/image'       => array(
			'shape'  => array( $t( 'Corners' ), true, array(
				'img-r12'  => array( $t( 'Soft' ), '& .bs-image__frame,& img{border-radius:12px}' ),
				'img-r24'  => array( $t( 'Round' ), '& .bs-image__frame,& img{border-radius:24px}' ),
				'img-circ' => array( $t( 'Circle' ), '& .bs-image__frame,& img{border-radius:50%;aspect-ratio:1;object-fit:cover}' ),
				'img-blob' => array( $t( 'Organic blob' ), '& .bs-image__frame,& img{border-radius:42% 58% 63% 37%/41% 44% 56% 59%}' ),
				'img-arch' => array( $t( 'Arch' ), '& .bs-image__frame,& img{border-radius:999px 999px 16px 16px}' ),
			) ),
			'shadow' => array( $t( 'Shadow' ), true, array(
				'img-sh1' => array( $t( 'Light' ), "& .bs-image__frame,& img{box-shadow:$shadow_sm}" ),
				'img-sh2' => array( $t( 'Medium' ), "& .bs-image__frame,& img{box-shadow:$shadow_md}" ),
				'img-sh3' => array( $t( 'Floating' ), "& .bs-image__frame,& img{box-shadow:$shadow_lg}" ),
				'img-shoff' => array( $t( 'Offset block' ), '& .bs-image__frame,& img{box-shadow:14px 14px 0 var(--bs-secondary,var(--bs-light))}' ),
			) ),
			'frame'  => array( $t( 'Frame' ), true, array(
				'img-bd'   => array( $t( 'Thin border' ), '& .bs-image__frame,& img{border:1px solid var(--bs-border)}' ),
				'img-pol'  => array( $t( 'Photo frame' ), '& .bs-image__frame,& img{padding:10px;background:#fff;border:1px solid var(--bs-border)}' ),
				'img-ring' => array( $t( 'Brand ring' ), '& .bs-image__frame,& img{outline:3px solid var(--bs-primary);outline-offset:6px}' ),
			) ),
			'filter' => array( $t( 'Filter' ), true, array(
				'img-gray'  => array( $t( 'Black & white' ), '& img{filter:grayscale(1)}' ),
				'img-graycol' => array( $t( 'B&W, color on hover' ), '& img{filter:grayscale(1);transition:filter .4s}&:hover img{filter:none}' ),
				'img-warm'  => array( $t( 'Warm' ), '& img{filter:sepia(.25) saturate(1.2)}' ),
				'img-vivid' => array( $t( 'Vivid' ), '& img{filter:saturate(1.35) contrast(1.05)}' ),
			) ),
			'hover'  => array( $t( 'On hover' ), true, array(
				'img-hz' => array( $t( 'Zoom inside' ), "& .bs-image__frame{overflow:hidden}$hover& img{transition:transform .5s}&:hover img{transform:scale(1.07)}}" ),
				'img-hl' => array( $t( 'Lift' ), "$hover& .bs-image__frame,& img{transition:transform .3s}&:hover .bs-image__frame,&:hover img{transform:translateY(-6px)}}" ),
				'img-hr' => array( $t( 'Tilt' ), "$hover& img{transition:transform .4s}&:hover img{transform:rotate(-2deg) scale(1.02)}}" ),
			) ),
			'caption' => array( $t( 'Caption' ), true, array(
				'img-capc' => array( $t( 'Centered' ), '& figcaption{text-align:center;font-size:.875em;opacity:.8}' ),
				'img-capi' => array( $t( 'Italic, small' ), '& figcaption{font-style:italic;font-size:.85em;opacity:.75}' ),
			) ),
		),
		'bst/banner'      => array(
			'mobile' => array( $t( 'On phones' ), true, array(
				'ban-mstack' => array( $t( 'Text on top, image below' ), '@media(max-width:549px){& .bs-banner__bg{top:auto;height:230px;bottom:0}& .bs-banner__img{object-position:78% 50%!important}& .bs-banner__content.bs-banner__content{padding:32px 22px 240px!important;align-items:flex-start}& .bs-banner__box{width:100%!important}& :is(h1,h2){font-size:30px!important;line-height:1.16}& .bs-text{font-size:15px!important;line-height:1.6}& .bs-buttons{gap:10px}}' ),
				'ban-mfull'  => array( $t( 'Text box full width' ), '@media(max-width:849px){& .bs-banner__box{width:100%!important}}' ),
				'ban-mhalf'  => array( $t( 'Text box half width on tablets' ), '@media(max-width:849px) and (min-width:550px){& .bs-banner__box{width:52%!important}}' ),
			) ),
			'space'  => array( $t( 'Content spacing' ), true, array(
				'ban-cwide' => array( $t( 'Site width, roomy' ), '& .bs-banner__content{max-width:1240px;margin:0 auto;padding:48px 40px!important}@media(max-width:849px){& .bs-banner__content{padding:36px 28px!important}}' ),
				'ban-ctight' => array( $t( 'Tight' ), '& .bs-banner__content{padding:20px}' ),
			) ),
			'bg'     => array( $t( 'Background behind image' ), true, array(
				'ban-bgl' => array( $t( 'Light' ), '&{background:var(--bs-light)}' ),
				'ban-bgd' => array( $t( 'Dark' ), '&{background:var(--bs-heading,#111827)}' ),
			) ),
			'shape' => array( $t( 'Corners' ), true, array(
				'ban-r16' => array( $t( 'Soft' ), '&{border-radius:16px;overflow:hidden}' ),
				'ban-r28' => array( $t( 'Round' ), '&{border-radius:28px;overflow:hidden}' ),
				'ban-cut' => array( $t( 'Cut corner' ), '&{clip-path:polygon(0 0,100% 0,100% 85%,90% 100%,0 100%)}' ),
			) ),
			'text'  => array( $t( 'Text box' ), true, array(
				'ban-dark'  => array( $t( 'Dark panel' ), '& .bs-banner__box{padding:28px;background:rgba(0,0,0,.62);color:#fff;border-radius:16px}& .bs-banner__box :is(h1,h2,h3,p){color:inherit}' ),
				'ban-light' => array( $t( 'White card' ), '& .bs-banner__box{padding:28px;background:rgba(255,255,255,.94);color:var(--bs-text);border-radius:16px;box-shadow:0 12px 32px rgba(0,0,0,.15)}& .bs-banner__box :is(h1,h2,h3){color:var(--bs-heading)}' ),
				'ban-glass' => array( $t( 'Frosted glass' ), '& .bs-banner__box{padding:28px;background:rgba(255,255,255,.18);backdrop-filter:blur(12px);border:1px solid rgba(255,255,255,.35);border-radius:18px}' ),
				'ban-brand' => array( $t( 'Brand panel' ), '& .bs-banner__box{padding:28px;background:color-mix(in srgb,var(--bs-primary) 88%,transparent);color:#fff;border-radius:16px}& .bs-banner__box :is(h1,h2,h3,p){color:inherit}' ),
				'ban-line'  => array( $t( 'Accent line' ), '& .bs-banner__box{padding-left:22px;border-left:5px solid var(--bs-secondary,var(--bs-primary))}' ),
			) ),
			'shade' => array( $t( 'Image shade' ), true, array(
				'ban-gl' => array( $t( 'Gradient from left' ), '&::after{content:"";position:absolute;inset:0;z-index:0;pointer-events:none;background:linear-gradient(90deg,rgba(0,0,0,.6),transparent 70%)}& .bs-banner__box{position:relative;z-index:1}' ),
				'ban-gb' => array( $t( 'Gradient from bottom' ), '&::after{content:"";position:absolute;inset:0;z-index:0;pointer-events:none;background:linear-gradient(0deg,rgba(0,0,0,.65),transparent 60%)}& .bs-banner__box{position:relative;z-index:1}' ),
				'ban-vig' => array( $t( 'Vignette' ), '&::after{content:"";position:absolute;inset:0;z-index:0;pointer-events:none;box-shadow:inset 0 0 120px rgba(0,0,0,.55)}& .bs-banner__box{position:relative;z-index:1}' ),
			) ),
			'title' => array( $t( 'Heading' ), true, array(
				'ban-tshadow' => array( $t( 'Text shadow' ), '& :is(h1,h2,h3){text-shadow:0 2px 12px rgba(0,0,0,.45)}' ),
				'ban-tbig'    => array( $t( 'Extra large' ), '& :is(h1,h2){font-size:clamp(2.2rem,5vw,4rem);line-height:1.1}' ),
				'ban-tup'     => array( $t( 'Uppercase' ), '& :is(h1,h2){text-transform:uppercase;letter-spacing:.04em}' ),
			) ),
			'hover' => array( $t( 'On hover' ), true, array(
				'ban-hz' => array( $t( 'Slow zoom' ), "{$hover}& .bs-banner__img,& .bs-banner__bg{transition:transform 1.2s}&:hover .bs-banner__img,&:hover .bs-banner__bg{transform:scale(1.06)}}" ),
				'ban-kb' => array( $t( 'Ken Burns (always)' ), '@media(prefers-reduced-motion:no-preference){& .bs-banner__img,& .bs-banner__bg{animation:bsp-kenburns 18s ease-in-out infinite alternate}}' ),
			) ),
		),
		'bst/button'      => array(
			'look'  => array( $t( 'Extra look' ), true, array(
				'btn-3d'    => array( $t( '3D press' ), '&{box-shadow:0 5px 0 color-mix(in srgb,var(--bsb-bg,var(--bs-primary)) 70%,#000)}&:active{transform:translateY(3px);box-shadow:0 2px 0 color-mix(in srgb,var(--bsb-bg,var(--bs-primary)) 70%,#000)}' ),
				'btn-neon'  => array( $t( 'Neon glow' ), '&{box-shadow:0 0 0 2px var(--bsb-bg,var(--bs-primary)),0 0 18px color-mix(in srgb,var(--bsb-bg,var(--bs-primary)) 70%,transparent)}' ),
				'btn-retro' => array( $t( 'Retro offset' ), '&{border:2px solid var(--bs-heading);box-shadow:4px 4px 0 var(--bs-heading)}&:hover{box-shadow:1px 1px 0 var(--bs-heading);transform:translate(3px,3px)}' ),
				'btn-grad'  => array( $t( 'Brand gradient' ), '&{background:linear-gradient(135deg,var(--bs-primary),var(--bs-secondary,var(--bs-primary)));border-color:transparent;color:#fff}' ),
			) ),
			'icon'  => array( $t( 'Icon' ), true, array(
				'btn-icirc' => array( $t( 'Icon in a circle' ), '& svg{box-sizing:content-box;padding:5px;background:rgba(255,255,255,.22);border-radius:50%}' ),
				'btn-iarrow' => array( $t( 'Arrow slides on hover' ), '& svg{transition:transform .2s}&:hover svg{transform:translateX(4px)}' ),
			) ),
			'anim'  => array( $t( 'Attention' ), true, array(
				'btn-ring'  => array( $t( 'Ring pulse' ), '@media(prefers-reduced-motion:no-preference){&{animation:bsp-ring 2s infinite}}' ),
				'btn-wiggle' => array( $t( 'Wiggle every few seconds' ), '@media(prefers-reduced-motion:no-preference){&{animation:bsp-wiggle 4s infinite}}' ),
				'btn-shine' => array( $t( 'Shine sweep' ), '&{position:relative;overflow:hidden}&::after{content:"";position:absolute;top:0;left:-60%;width:40%;height:100%;background:linear-gradient(100deg,transparent,rgba(255,255,255,.45),transparent);transform:skewX(-20deg)}@media(prefers-reduced-motion:no-preference){&::after{animation:bsp-shine 3s infinite}}' ),
			) ),
			'text'  => array( $t( 'Text' ), true, array(
				'btn-wide' => array( $t( 'Wide letter spacing' ), '&{letter-spacing:.08em}' ),
				'btn-up'   => array( $t( 'Uppercase, small' ), '&{text-transform:uppercase;font-size:.85em;letter-spacing:.06em}' ),
			) ),
		),
		'bst/form'        => array(
			'fields' => array( $t( 'Fields' ), true, array(
				'form-soft'  => array( $t( 'Soft rounded' ), '& :is(input:not([type=submit]):not([type=checkbox]):not([type=radio]),textarea,select){border-radius:12px}' ),
				'form-pill'  => array( $t( 'Pill' ), '& :is(input:not([type=submit]):not([type=checkbox]):not([type=radio]),select){border-radius:999px;padding-inline:18px}& textarea{border-radius:18px}' ),
				'form-line'  => array( $t( 'Underline only' ), '& :is(input:not([type=submit]):not([type=checkbox]):not([type=radio]),textarea,select){border-width:0 0 2px;border-radius:0;background:transparent}' ),
				'form-fill'  => array( $t( 'Filled' ), '& :is(input:not([type=submit]):not([type=checkbox]):not([type=radio]),textarea,select){background:var(--bs-light);border-color:transparent}' ),
			) ),
			'focus'  => array( $t( 'Focus' ), true, array(
				'form-fring' => array( $t( 'Brand ring' ), '& :is(input,textarea,select):focus{outline:none;border-color:var(--bs-primary);box-shadow:0 0 0 4px color-mix(in srgb,var(--bs-primary) 20%,transparent)}' ),
				'form-fline' => array( $t( 'Brand underline' ), '& :is(input,textarea,select):focus{outline:none;border-bottom-color:var(--bs-primary)}' ),
			) ),
			'box'    => array( $t( 'Form box' ), true, array(
				'form-card'  => array( $t( 'White card' ), "&{padding:28px;background:#fff;border-radius:18px;box-shadow:$shadow_md}" ),
				'form-light' => array( $t( 'Light panel' ), '&{padding:28px;background:var(--bs-light);border-radius:18px}' ),
				'form-brand' => array( $t( 'Brand top line' ), '&{padding:28px;border:1px solid var(--bs-border);border-top:4px solid var(--bs-primary);border-radius:14px}' ),
			) ),
			'submit' => array( $t( 'Send button' ), true, array(
				'form-sfull' => array( $t( 'Full width' ), '& :is([type=submit],button[type=submit]){width:100%}' ),
				'form-sbig'  => array( $t( 'Large' ), '& :is([type=submit],button[type=submit]){min-height:52px;font-size:1.05em;padding-inline:32px}' ),
			) ),
			'mobile' => array( $t( 'Mobile' ), false, array(
				'form-m16' => array( $t( '16px text (no auto-zoom)' ), '@media(max-width:849px){& :is(input,textarea,select){font-size:16px}}' ),
			) ),
		),
		'bst/icon-box'    => $card_groups( '', 'ib', true ),
		'bst/image-box'   => $card_groups( '', 'imb' ),
		'bst/team-member' => $card_groups( '', 'tm' ),
		'bst/price-table' => $card_groups( '', 'pt' ),
		'bst/testimonial' => array_merge( $card_groups( '', 'ts', true ), array(
			'quote' => array( $t( 'Quote mark' ), true, array(
				'ts-q' => array( $t( 'Big brand quote' ), '&{position:relative}&::before{content:"\201C";position:absolute;top:-.15em;right:.3em;font:700 5rem/1 Georgia,serif;color:var(--bs-primary);opacity:.18}' ),
			) ),
		) ),
		'bst/column'      => $card_groups( '', 'col', true ),
		'bst/accordion'   => array(
			'width'  => array( $t( 'Width' ), true, array(
				'acc-w700' => array( $t( 'Narrow (700px)' ), '&{max-width:700px}' ),
				'acc-w900' => array( $t( 'Medium (900px)' ), '&{max-width:900px}' ),
				'acc-wc'   => array( $t( 'Centered 900px' ), '&{max-width:900px;margin-inline:auto}' ),
			) ),
			'items'  => array( $t( 'Items' ), true, array(
				'acc-cards' => array( $t( 'Separate cards' ), '& .bs-accordion__item{margin-bottom:12px;border:1px solid var(--bs-border);border-radius:12px;background:var(--bs-content-bg,#fff)}& .bs-accordion__title{padding:16px 20px}& .bs-accordion__content{padding:0 20px 18px}' ),
				'acc-shadow' => array( $t( 'Shadow cards' ), "& .bs-accordion__item{margin-bottom:12px;border:0;border-radius:14px;background:#fff;box-shadow:$shadow_sm}& .bs-accordion__title{padding:16px 20px}& .bs-accordion__content{padding:0 20px 18px}" ),
				'acc-lines' => array( $t( 'Simple lines' ), '& .bs-accordion__item{border:0;border-bottom:1px solid var(--bs-border);border-radius:0;background:transparent}' ),
			) ),
			'active' => array( $t( 'Open item' ), true, array(
				'acc-abg'   => array( $t( 'Light background' ), '& .bs-accordion__item[open]>.bs-accordion__title{background:var(--bs-light);border-radius:12px;color:var(--bs-primary)}' ),
				'acc-abar'  => array( $t( 'Brand side bar' ), '& .bs-accordion__item[open]{box-shadow:inset 4px 0 0 var(--bs-primary)}' ),
				'acc-abrand' => array( $t( 'Brand title' ), '& .bs-accordion__item[open]>.bs-accordion__title{background:var(--bs-primary);color:#fff;border-radius:12px}' ),
			) ),
			'title'  => array( $t( 'Question text' ), true, array(
				'acc-tbold' => array( $t( 'Bold, larger' ), '& .bs-accordion__title{font-weight:700;font-size:1.08em}' ),
			) ),
		),
		'bst/posts'       => array_merge( $card_groups( '.bs-card', 'post' ), array(
			'cpad'  => array( $t( 'Card padding' ), true, array(
				'post-p20' => array( $t( 'Comfortable (20px)' ), '& .bs-card{padding:20px}' ),
				'post-p0'  => array( $t( 'None' ), '& .bs-card{padding:0}' ),
			) ),
			'image' => array( $t( 'Image' ), true, array(
				'post-icontain' => array( $t( 'Show whole image' ), '& .bs-card__media img{object-fit:contain;background:var(--bs-light)}' ),
				'post-izoom'    => array( $t( 'Zoom on hover' ), "& .bs-card__media{overflow:hidden}$hover& .bs-card__media img{transition:transform .5s}& .bs-card:hover .bs-card__media img{transform:scale(1.07)}}" ),
			) ),
			'title' => array( $t( 'Title' ), true, array(
				'post-t2' => array( $t( 'Max 2 lines' ), '& .bs-card__title{display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}' ),
				'post-t3' => array( $t( 'Max 3 lines' ), '& .bs-card__title{display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden}' ),
			) ),
		) ),
		'bst/products'    => array_merge( $card_groups( '.bs-product', 'prod' ), array(
			'image' => array( $t( 'Image' ), true, array(
				'prod-icontain' => array( $t( 'Show whole image' ), '& .bs-product__img{object-fit:contain}& .bs-product__media{background:var(--bs-content-bg,#fff)}' ),
				'prod-icover'   => array( $t( 'Fill the frame' ), '& .bs-product__img{object-fit:cover}' ),
			) ),
			'title' => array( $t( 'Title' ), true, array(
				'prod-t2' => array( $t( 'Even, 2 lines' ), '& .bs-product__title{min-height:2.8em;--bs-product-title-lines:2}' ),
				'prod-t3' => array( $t( 'Even, 3 lines' ), '& .bs-product__title{min-height:4.2em;--bs-product-title-lines:3}' ),
			) ),
			'price' => array( $t( 'Price' ), true, array(
				'prod-pbold' => array( $t( 'Bigger & bold' ), '& .bs-product__price{font-size:1.15em;font-weight:800}' ),
				'prod-pbrand' => array( $t( 'Brand color' ), '& .bs-product__price{color:var(--bs-primary);font-weight:700}' ),
			) ),
			'buy'   => array( $t( 'Buy button' ), true, array(
				'prod-bsolid' => array( $t( 'Brand solid' ), '& .bs-product__actions .button{background:var(--bs-primary);color:#fff;border-color:var(--bs-primary)}' ),
				'prod-bfull'  => array( $t( 'Full width' ), '& .bs-product__actions .button{width:100%;justify-content:center}' ),
			) ),
		) ),
		'bst/gallery'     => array(
			'shape' => array( $t( 'Corners' ), true, array(
				'gal-r12' => array( $t( 'Soft' ), '& img{border-radius:12px}' ),
				'gal-r24' => array( $t( 'Round' ), '& img{border-radius:24px}' ),
			) ),
			'look'  => array( $t( 'Look' ), true, array(
				'gal-sh'   => array( $t( 'Shadow' ), "& img{box-shadow:$shadow_sm}" ),
				'gal-gray' => array( $t( 'B&W, color on hover' ), '& img{filter:grayscale(1);transition:filter .4s}& a:hover img,& figure:hover img{filter:none}' ),
				'gal-dim'  => array( $t( 'Dim others on hover' ), '&:hover img{opacity:.55;transition:opacity .3s}&:hover a:hover img,&:hover figure:hover img{opacity:1}' ),
			) ),
		),
		'bst/video'       => array(
			'shape' => array( $t( 'Corners' ), true, array(
				'vid-r16' => array( $t( 'Soft' ), '&{border-radius:16px;overflow:hidden}' ),
				'vid-r28' => array( $t( 'Round' ), '&{border-radius:28px;overflow:hidden}' ),
			) ),
			'look'  => array( $t( 'Look' ), true, array(
				'vid-sh'  => array( $t( 'Floating shadow' ), "&{box-shadow:$shadow_lg}" ),
				'vid-dev' => array( $t( 'Device frame' ), '&{border:10px solid #111;border-radius:22px;box-shadow:0 20px 40px rgba(0,0,0,.3)}' ),
			) ),
		),
		'bst/tabs'        => array(
			'nav' => array( $t( 'Tab buttons' ), true, array(
				'tab-pill'  => array( $t( 'Pills' ), '& .bs-tabs__nav [role=tab]{border-radius:999px}' ),
				'tab-boxed' => array( $t( 'Boxed bar' ), '& .bs-tabs__nav{padding:6px;background:var(--bs-light);border-radius:14px}' ),
				'tab-line'  => array( $t( 'Underline' ), '& .bs-tabs__nav [role=tab]{border-radius:0;background:transparent}& .bs-tabs__nav [aria-selected=true]{box-shadow:inset 0 -3px 0 var(--bs-primary)}' ),
			) ),
			'panel' => array( $t( 'Content panel' ), true, array(
				'tab-card' => array( $t( 'Card' ), "& .bs-tab{padding:24px;background:#fff;border-radius:16px;box-shadow:$shadow_sm}" ),
			) ),
		),
		'bst/heading'     => array(
			'deco' => array( $t( 'Decoration' ), true, array(
				'hd-under' => array( $t( 'Short brand underline' ), '&::after{content:"";display:block;width:56px;height:4px;margin-top:.35em;background:var(--bs-primary);border-radius:4px}' ),
				'hd-mark'  => array( $t( 'Highlighter' ), '&{background:linear-gradient(transparent 62%,color-mix(in srgb,var(--bs-secondary,var(--bs-primary)) 45%,transparent) 0);display:inline}' ),
				'hd-grad'  => array( $t( 'Gradient text' ), '&{background:linear-gradient(90deg,var(--bs-primary),var(--bs-secondary,var(--bs-primary)));-webkit-background-clip:text;background-clip:text;color:transparent}' ),
				'hd-side'  => array( $t( 'Side bar' ), '&{padding-left:16px;border-left:5px solid var(--bs-primary)}' ),
				'hd-dot'   => array( $t( 'Brand dot before' ), '&::before{content:"";display:inline-block;width:.45em;height:.45em;margin-right:.4em;vertical-align:middle;background:var(--bs-primary);border-radius:50%}' ),
			) ),
			'text' => array( $t( 'Text' ), true, array(
				'hd-balance' => array( $t( 'Balanced lines' ), '&{text-wrap:balance}' ),
				'hd-tight'   => array( $t( 'Tight & bold' ), '&{letter-spacing:-.02em;font-weight:800}' ),
				'hd-up'      => array( $t( 'Uppercase, spaced' ), '&{text-transform:uppercase;letter-spacing:.08em}' ),
				'hd-tight2'  => array( $t( 'Slightly tight' ), '&{letter-spacing:-.025em}' ),
				'hd-kicker'  => array( $t( 'Small label above a title' ), '&{font-size:12px;font-weight:700;letter-spacing:.13em;text-transform:uppercase;color:var(--bs-primary);margin-bottom:14px}' ),
			) ),
		),
		'bst/text'        => array(
			'role'  => array( $t( 'Role' ), true, array(
				'tx-kicker'  => array( $t( 'Small label above a title' ), '&{font-weight:700;letter-spacing:.13em;text-transform:uppercase;color:var(--bs-primary);margin-bottom:14px!important}&>p{margin-bottom:0}' ),
				'tx-caption' => array( $t( 'Photo caption (small, muted)' ), '&{font-size:13px;color:color-mix(in srgb,var(--bs-text) 65%,#fff)}' ),
				'tx-muted'   => array( $t( 'Muted color' ), '&{color:color-mix(in srgb,var(--bs-text) 78%,#fff)}' ),
			) ),
			'read'  => array( $t( 'Reading' ), true, array(
				'tx-airy'  => array( $t( 'Airy lines' ), '&{line-height:1.85}' ),
				'tx-narrow' => array( $t( 'Comfortable width' ), '&{max-width:68ch}' ),
				'tx-lead'  => array( $t( 'Lead paragraph' ), '&{font-size:1.2em;line-height:1.7;opacity:.9}' ),
				'tx-intro' => array( $t( 'Intro under a title (muted, 740px)' ), '&{max-width:740px;color:color-mix(in srgb,var(--bs-text) 78%,#fff);margin-bottom:30px}' ),
				'tx-n500'  => array( $t( 'Narrow (500px)' ), '&{max-width:500px}' ),
			) ),
			'links' => array( $t( 'Links' ), true, array(
				'tx-ulink' => array( $t( 'Underlined links' ), '& a{text-decoration:underline;text-underline-offset:.2em}' ),
				'tx-blink' => array( $t( 'Brand links' ), '& a{color:var(--bs-primary);font-weight:600}' ),
			) ),
			'box'   => array( $t( 'Box' ), true, array(
				'tx-quote' => array( $t( 'Quote box' ), '&{padding:20px 24px;background:var(--bs-light);border-left:4px solid var(--bs-primary);border-radius:12px}' ),
				'tx-drop'  => array( $t( 'Drop cap' ), '&>p:first-child::first-letter{float:left;margin:.05em .12em 0 0;font-size:3.4em;line-height:.85;font-weight:800;color:var(--bs-primary)}' ),
				'tx-note'  => array( $t( 'Note box (2nd color)' ), '&{padding:22px 26px;background:color-mix(in srgb,var(--bs-secondary,#f5cf4c) 14%,#fff);border-left:4px solid var(--bs-secondary,#f5cf4c);border-radius:0 14px 14px 0}@media(max-width:549px){&{padding:20px}}' ),
			) ),
		),
	);
}

/** Flat id => css map with `&` expanded to the preset class. */
function bst_block_preset_rules() {
	static $rules = null;
	if ( null === $rules ) {
		$rules = array();
		foreach ( bst_block_presets() as $groups ) {
			foreach ( $groups as $group ) {
				foreach ( $group[2] as $id => $option ) {
					$rules[ $id ] = str_replace( '&', '.bsp-' . $id, $option[1] );
				}
			}
		}
	}
	return $rules;
}

function bst_block_presets_css() {
	return implode( "\n", bst_block_preset_rules() )
		. "\n@keyframes bsp-ring{0%{box-shadow:0 0 0 0 color-mix(in srgb,var(--bsb-bg,var(--bs-primary)) 55%,transparent)}70%{box-shadow:0 0 0 14px transparent}100%{box-shadow:0 0 0 0 transparent}}"
		. '@keyframes bsp-wiggle{0%,86%,100%{transform:none}88%{transform:rotate(-4deg)}92%{transform:rotate(4deg)}96%{transform:rotate(-2deg)}}'
		. '@keyframes bsp-shine{0%{left:-60%}35%,100%{left:130%}}'
		. '@keyframes bsp-kenburns{from{transform:scale(1)}to{transform:scale(1.12) translate(-2%,-2%)}}';
}

/** Front end and editor canvas. */
function bst_block_presets_assets() {
	wp_register_style( 'bst-block-presets', false, array(), BST_VERSION );
	wp_enqueue_style( 'bst-block-presets' );
	wp_add_inline_style( 'bst-block-presets', bst_block_presets_css() );
}
add_action( 'enqueue_block_assets', 'bst_block_presets_assets' );

/** Catalog for the editor panel (labels only). */
function bst_block_presets_editor_data() {
	$data = array();
	foreach ( bst_block_presets() as $block => $groups ) {
		foreach ( $groups as $key => $group ) {
			$opts = array();
			foreach ( $group[2] as $id => $option ) {
				$opts[] = array( $id, $option[0] );
			}
			$data[ $block ][] = array( 'key' => $key, 'label' => $group[0], 'exclusive' => (bool) $group[1], 'options' => $opts );
		}
	}
	wp_add_inline_script( 'bst-editor-helpers', 'window.bstBlockPresets=' . wp_json_encode( $data ) . ';', 'before' );
}
add_action( 'enqueue_block_editor_assets', 'bst_block_presets_editor_data', 20 );

/** Turn the saved preset ids into classes on the rendered block root. */
function bst_block_presets_render( $content, $block ) {
	$value = isset( $block['attrs']['bsPresets'] ) ? (string) $block['attrs']['bsPresets'] : '';
	if ( '' === $value || '' === $content || ! class_exists( 'WP_HTML_Tag_Processor' ) ) {
		return $content;
	}
	$rules = bst_block_preset_rules();
	$tags  = new WP_HTML_Tag_Processor( $content );
	if ( ! $tags->next_tag() ) {
		return $content;
	}
	foreach ( preg_split( '/\s+/', $value ) as $id ) {
		if ( isset( $rules[ $id ] ) ) {
			$tags->add_class( 'bsp-' . $id );
		}
	}
	return $tags->get_updated_html();
}
add_filter( 'render_block', 'bst_block_presets_render', 11, 2 );
