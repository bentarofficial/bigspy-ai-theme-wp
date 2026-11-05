<?php
/**
 * Section recipes for the section_recipes tool: ready block trees with motion already set.
 *
 * Each recipe: title, needs (same groups as block_catalog), use (when it fits), tree (one
 * bst/section). Text in {{double braces}} is a slot the AI replaces with real content; images
 * are left empty and listed in the slots so the AI adds real media ids or removes the block.
 */
defined( 'ABSPATH' ) || exit;

$sec = function ( $inner, $attrs = array() ) {
	return array( 'name' => 'bst/section', 'attrs' => $attrs, 'inner' => $inner );
};
$h = function ( $text, $tag = 'h2', $attrs = array() ) {
	return array( 'name' => 'bst/heading', 'attrs' => array_merge( array( 'text' => $text, 'tag' => $tag ), $attrs ) );
};
$t = function ( $html, $attrs = array() ) {
	return array( 'name' => 'bst/text', 'attrs' => array_merge( array( 'html' => $html ), $attrs ) );
};
$title = function ( $text, $sub, $anim = 'fade-up' ) {
	return array( 'name' => 'bst/title', 'attrs' => array( 'text' => $text, 'subtitle' => $sub, 'tag' => 'h2', 'align' => 'center', 'bsAnimation' => $anim ) );
};
$btns = function ( $n = 2, $align = 'left', $attrs = array() ) {
	$b = array( array( 'name' => 'bst/button', 'attrs' => array( 'text' => '{{primary action}}', 'url' => '{{link}}', 'bsHoverEffect' => 'lift' ) ) );
	if ( $n > 1 ) {
		$b[] = array( 'name' => 'bst/button', 'attrs' => array( 'text' => '{{secondary action}}', 'url' => '{{link}}', 'style' => 'outline' ) );
	}
	return array( 'name' => 'bst/buttons', 'attrs' => array_merge( array( 'align' => $align ), $attrs ), 'inner' => $b );
};
$row = function ( $cols, $attrs = array() ) {
	$inner = array();
	foreach ( $cols as $c ) {
		$inner[] = array( 'name' => 'bst/column', 'attrs' => array( 'spanDesktop' => $c[0], 'spanMobile' => 12 ), 'inner' => $c[1] );
	}
	return array( 'name' => 'bst/row', 'attrs' => $attrs, 'inner' => $inner );
};
$times = function ( $n, $fn ) {
	$out = array();
	for ( $i = 0; $i < $n; $i++ ) {
		$out[] = $fn( $i );
	}
	return $out;
};
$fade = function ( $i, $anim = 'fade-up' ) {
	return array( 'bsAnimation' => $anim, 'bsAnimationDelay' => $i * 120 );
};
$heading_block = function ( $lead = '' ) use ( $title ) {
	return $title( '{{section title}}', '' !== $lead ? $lead : '{{short subtitle}}' );
};

return array(
	/* ---------------------------------------------------------------- Hero */
	'hero-banner-center' => array( 'Centered banner hero', array( 'hero' ), 'Page opening with one strong image, centered H1 and two actions.', 'image: banner background',
		$sec( array( array( 'name' => 'bst/banner', 'attrs' => array( 'heightMode' => 'vh', 'heightVhD' => 85, 'heightVhM' => 75, 'overlayOpacity' => 45 ), 'inner' => array( $h( '{{page H1}}', 'h1', array( 'align' => 'center', 'bsAnimation' => 'reveal-up', 'bsAnimationDuration' => 800 ) ), $t( '<p>{{one clear message}}</p>', array( 'align' => 'center', 'bsAnimation' => 'fade-up', 'bsAnimationDelay' => 200 ) ), $btns( 2, 'center', array( 'bsAnimation' => 'fade-up', 'bsAnimationDelay' => 350 ) ) ) ) ), array( 'contentWidth' => 'full', 'paddingDesktop' => 0, 'paddingTablet' => 0, 'paddingMobile' => 0 ) ) ),
	'hero-banner-left' => array( 'Left-aligned banner hero', array( 'hero' ), 'Banner with text on the left over a photo whose subject sits on the right.', 'image: banner background (subject right)',
		$sec( array( array( 'name' => 'bst/banner', 'attrs' => array( 'heightDesktop' => 620, 'heightMobile' => 520, 'posX' => 'left', 'textAlign' => 'left', 'contentWidth' => 55, 'overlayOpacity' => 40 ), 'inner' => array( $t( '<p><strong>{{eyebrow}}</strong></p>', array( 'bsAnimation' => 'fade-right' ) ), $h( '{{page H1}}', 'h1', array( 'bsAnimation' => 'fade-right', 'bsAnimationDelay' => 120 ) ), $t( '<p>{{one clear message}}</p>', array( 'bsAnimation' => 'fade-right', 'bsAnimationDelay' => 240 ) ), $btns( 2, 'left', array( 'bsAnimation' => 'fade-up', 'bsAnimationDelay' => 360 ) ) ) ) ), array( 'contentWidth' => 'full', 'paddingDesktop' => 0, 'paddingTablet' => 0, 'paddingMobile' => 0 ) ) ),
	'hero-slider' => array( 'Banner slider hero', array( 'hero' ), 'Home page with two or three offers or services, each with its own banner.', 'image: one banner per slide',
		$sec( array( array( 'name' => 'bst/slider', 'attrs' => array( 'slidesDesktop' => 1, 'slidesMobile' => 1, 'dots' => true, 'loop' => true ), 'inner' => $times( 2, function ( $i ) use ( $h, $t, $btns ) {
			return array( 'name' => 'bst/banner', 'attrs' => array( 'heightDesktop' => 600, 'heightMobile' => 480 ), 'inner' => array( $h( 0 === $i ? '{{page H1}}' : '{{second message}}', 0 === $i ? 'h1' : 'h2', array( 'align' => 'center', 'bsAnimation' => 'reveal-up' ) ), $t( '<p>{{slide text}}</p>', array( 'align' => 'center', 'bsAnimation' => 'fade-up', 'bsAnimationDelay' => 200 ) ), $btns( 1, 'center' ) ) );
		} ) ) ), array( 'contentWidth' => 'full', 'paddingDesktop' => 0, 'paddingTablet' => 0, 'paddingMobile' => 0 ) ) ),
	'hero-zoom' => array( 'Zoom hero', array( 'hero', 'media' ), 'A striking photo that grows to full screen while scrolling; for portfolios, venues, products.', 'image: one strong landscape photo',
		$sec( array( array( 'name' => 'bigspy/zoom-hero', 'attrs' => array( 'imageAlt' => '{{image description}}', 'overlay' => 35 ), 'inner' => array( $h( '{{page H1}}', 'h1', array( 'align' => 'center' ) ), $t( '<p>{{one clear message}}</p>', array( 'align' => 'center' ) ), $btns( 1, 'center' ) ) ) ), array( 'contentWidth' => 'full', 'paddingDesktop' => 0, 'paddingTablet' => 0, 'paddingMobile' => 0 ) ) ),
	'hero-split' => array( 'Split hero, text and image', array( 'hero' ), 'Text and actions on the left, product or team image on the right.', 'image: right column picture',
		$sec( array( $row( array( array( 6, array( $t( '<p><strong>{{eyebrow}}</strong></p>', array( 'bsAnimation' => 'fade-up' ) ), $h( '{{page H1}}', 'h1', array( 'bsAnimation' => 'reveal-up', 'bsAnimationDelay' => 100 ) ), $t( '<p>{{one clear message}}</p>', array( 'bsAnimation' => 'fade-up', 'bsAnimationDelay' => 200 ) ), $btns( 2, 'left', array( 'bsAnimation' => 'fade-up', 'bsAnimationDelay' => 300 ) ) ) ), array( 6, array( array( 'name' => 'bst/image', 'attrs' => array( 'alt' => '{{image description}}', 'radius' => 20, 'bsAnimation' => 'zoom-in', 'bsScroll' => 'y', 'bsScrollStrength' => 3 ) ) ) ) ), array( 'verticalAlign' => 'center', 'reverseMobile' => false ) ) ), array( 'paddingDesktop' => 110, 'paddingMobile' => 56 ) ) ),
	'hero-banner-grid' => array( 'Banner grid hero', array( 'hero', 'commerce' ), 'Shops and magazines: one large and two small image tiles with links.', 'image: three tile images',
		$sec( array( array( 'name' => 'bst/banner-grid', 'attrs' => array( 'layout' => '1-2', 'height' => 520, 'items' => $times( 3, function () { return array( 'imageUrl' => '', 'imageId' => 0, 'title' => '{{tile title}}', 'subtitle' => '{{tile action}}', 'link' => '{{link}}' ); } ), 'bsAnimation' => 'fade-up' ) ) ), array( 'paddingDesktop' => 24, 'paddingMobile' => 12 ) ) ),
	'hero-animated-text' => array( 'Typographic hero', array( 'hero', 'text' ), 'Brands without a strong photo: big H1 and one rotating keyword line on a calm background.', '',
		$sec( array( $h( '{{page H1}}', 'h1', array( 'align' => 'center', 'bsAnimation' => 'reveal-up' ) ), array( 'name' => 'bigspy/animated-text', 'attrs' => array( 'text' => '{{lead-in}}', 'words' => "{{keyword 1}}\n{{keyword 2}}\n{{keyword 3}}", 'effect' => 'rotate', 'tag' => 'p', 'align' => 'center' ) ), $t( '<p>{{one clear message}}</p>', array( 'align' => 'center', 'maxWidth' => 640, 'bsAnimation' => 'fade-up', 'bsAnimationDelay' => 200 ) ), $btns( 2, 'center', array( 'bsAnimation' => 'fade-up', 'bsAnimationDelay' => 300 ) ) ), array( 'paddingDesktop' => 140, 'paddingMobile' => 72, 'bgPreset' => 'mesh' ) ) ),

	/* ---------------------------------------------------------------- Features */
	'features-icon-3' => array( 'Three feature boxes', array( 'features' ), 'Three services or benefits with icons, staggered entrance and hover lift.', '',
		$sec( array( $heading_block(), $row( $times( 3, function ( $i ) use ( $fade ) { return array( 4, array( array( 'name' => 'bst/icon-box', 'attrs' => array_merge( array( 'icon' => '{{icon}}', 'title' => '{{feature}}', 'text' => '{{one or two sentences}}', 'boxStyle' => 'card', 'bsHoverEffect' => 'lift' ), $fade( $i ) ) ) ) ); } ) ) ) ) ),
	'features-icon-side-4' => array( 'Four features, icon at the side', array( 'features' ), 'Four shorter benefits in two columns with the icon at the side.', '',
		$sec( array( $heading_block(), $row( $times( 4, function ( $i ) use ( $fade ) { return array( 6, array( array( 'name' => 'bst/icon-box', 'attrs' => array_merge( array( 'icon' => '{{icon}}', 'title' => '{{benefit}}', 'text' => '{{one sentence}}', 'position' => 'left', 'align' => 'left' ), $fade( $i, 'fade-left' ) ) ) ) ); } ) ) ) ) ),
	'features-flip-3' => array( 'Flip cards', array( 'features' ), 'Three services or values with a detail and action on the back.', '',
		$sec( array( $heading_block(), $row( $times( 3, function ( $i ) use ( $fade ) { return array( 4, array( array( 'name' => 'bst/flip-box', 'attrs' => array_merge( array( 'icon' => '{{icon}}', 'frontTitle' => '{{service}}', 'frontText' => '{{short line}}', 'backTitle' => '{{benefit}}', 'backText' => '{{detail}}', 'buttonText' => '{{action}}', 'buttonUrl' => '{{link}}' ), $fade( $i, 'zoom-in' ) ) ) ) ); } ) ) ) ) ),
	'features-bento' => array( 'Bento grid', array( 'features', 'proof' ), 'Modern mix of services, one key figure and an image in tiles of different sizes.', 'image: optional large tile background',
		$sec( array( $heading_block(), array( 'name' => 'bigspy/bento', 'attrs' => array( 'glow' => true ), 'inner' => array(
			array( 'name' => 'bigspy/bento-cell', 'attrs' => array( 'size' => 'large' ), 'inner' => array( $h( '{{main service}}', 'h3' ), $t( '<p>{{two sentences}}</p>' ) ) ),
			array( 'name' => 'bigspy/bento-cell', 'attrs' => array( 'size' => 'small' ), 'inner' => array( array( 'name' => 'bst/counter', 'attrs' => array( 'number' => '{{real number}}', 'suffix' => '+', 'label' => '{{what it counts}}' ) ) ) ),
			array( 'name' => 'bigspy/bento-cell', 'attrs' => array( 'size' => 'small' ), 'inner' => array( $h( '{{service}}', 'h3' ), $t( '<p>{{one sentence}}</p>' ) ) ),
			array( 'name' => 'bigspy/bento-cell', 'attrs' => array( 'size' => 'wide' ), 'inner' => array( $h( '{{service}}', 'h3' ), $t( '<p>{{one sentence}}</p>' ) ) ),
		) ) ) ) ),
	'features-expand' => array( 'Spotlight panels', array( 'features', 'media' ), 'Three to five fields or services as photo panels that expand on hover.', 'image: one background per panel',
		$sec( array( $heading_block(), array( 'name' => 'bigspy/expand-gallery', 'attrs' => array( 'verticalTitle' => true ), 'inner' => $times( 4, function () use ( $t ) { return array( 'name' => 'bigspy/expand-panel', 'attrs' => array( 'title' => '{{field}}' ), 'inner' => array( $t( '<p>{{one sentence}}</p>' ) ) ); } ) ) ) ) ),
	'features-hover-reveal' => array( 'Hover reveal list', array( 'features', 'media' ), 'Services or projects as a large-type list; the image appears on hover.', 'image: one per row',
		$sec( array( $heading_block(), array( 'name' => 'bigspy/hover-reveal', 'inner' => $times( 4, function () { return array( 'name' => 'bigspy/hover-reveal-item', 'attrs' => array( 'title' => '{{service or project}}', 'meta' => '{{short tag}}', 'link' => '{{link}}' ) ); } ) ) ) ) ),
	'features-image-boxes' => array( 'Image cards', array( 'features', 'media' ), 'Three services, products or projects with photo, text and link.', 'image: one per card',
		$sec( array( $heading_block(), $row( $times( 3, function ( $i ) use ( $fade ) { return array( 4, array( array( 'name' => 'bst/image-box', 'attrs' => array_merge( array( 'title' => '{{card title}}', 'text' => '{{one sentence}}', 'link' => '{{link}}', 'linkText' => '{{action}}', 'bsHoverEffect' => 'lift' ), $fade( $i ) ) ) ) ); } ) ) ) ) ),
	'features-tabs' => array( 'Tabbed details', array( 'features', 'faq' ), 'The same kind of content split by category: packages, audiences, product details.', '',
		$sec( array( $heading_block(), array( 'name' => 'bst/tabs', 'attrs' => array( 'align' => 'center', 'fade' => true ), 'inner' => $times( 3, function () use ( $t ) { return array( 'name' => 'bst/tab', 'attrs' => array( 'title' => '{{tab}}' ), 'inner' => array( $t( '<p>{{details}}</p>' ) ) ); } ) ) ) ) ),
	'features-split-checklist' => array( 'Image with checklist', array( 'features', 'text' ), 'Why choose us: an image beside a heading and a check-mark list.', 'image: left column picture',
		$sec( array( $row( array( array( 6, array( array( 'name' => 'bst/image', 'attrs' => array( 'alt' => '{{image description}}', 'radius' => 20, 'bsAnimation' => 'fade-right', 'bsHoverEffect' => 'zoom' ) ) ) ), array( 6, array( $h( '{{section title}}', 'h2', array( 'bsAnimation' => 'fade-left' ) ), $t( '<p>{{short intro}}</p>', array( 'bsAnimation' => 'fade-left', 'bsAnimationDelay' => 100 ) ), array( 'name' => 'bst/icon-list', 'attrs' => array( 'items' => $times( 4, function () { return array( 'icon' => 'check', 'text' => '{{reason}}', 'link' => '' ); } ), 'bsAnimation' => 'fade-left', 'bsAnimationDelay' => 200 ) ), $btns( 1 ) ) ) ), array( 'verticalAlign' => 'center' ) ) ) ) ),

	/* ---------------------------------------------------------------- Process */
	'process-timeline' => array( 'Timeline', array( 'process' ), 'Steps with dates, milestones or company history.', '',
		$sec( array( $heading_block(), array( 'name' => 'bst/timeline', 'attrs' => array( 'layout' => 'alternate', 'items' => $times( 4, function () { return array( 'date' => '{{step or date}}', 'title' => '{{step title}}', 'text' => '{{one sentence}}' ); } ), 'bsAnimation' => 'fade-up' ) ) ) ) ),
	'process-stack-cards' => array( 'Stacking step cards', array( 'process' ), 'Three to five steps as cards stacking while scrolling. Pinned.', '',
		$sec( array( $heading_block(), array( 'name' => 'bigspy/stack-cards', 'inner' => $times( 4, function ( $i ) use ( $h, $t ) { return array( 'name' => 'bigspy/stack-card', 'inner' => array( $t( '<p><strong>' . sprintf( '%02d', $i + 1 ) . '</strong></p>' ), $h( '{{step title}}', 'h3' ), $t( '<p>{{two sentences}}</p>' ) ) ); } ) ) ) ) ),
	'process-scroll-story' => array( 'Scroll story', array( 'process', 'media' ), 'A process or story where the sticky image changes with each step. Pinned.', 'image: one per step',
		$sec( array( $heading_block(), array( 'name' => 'bigspy/scroll-story', 'attrs' => array( 'mediaSide' => 'right' ), 'inner' => $times( 3, function () use ( $h, $t ) { return array( 'name' => 'bigspy/story-step', 'attrs' => array( 'mediaAlt' => '{{image description}}' ), 'inner' => array( $h( '{{step title}}', 'h3' ), $t( '<p>{{two sentences}}</p>' ) ) ); } ) ) ) ) ),
	'process-horizontal' => array( 'Horizontal scroll panels', array( 'process', 'proof' ), 'Projects or steps moving sideways while scrolling down. Pinned.', '',
		$sec( array( $heading_block(), array( 'name' => 'bigspy/horizontal-scroll', 'inner' => $times( 4, function () use ( $h, $t ) { return array( 'name' => 'bigspy/hscroll-panel', 'inner' => array( $h( '{{panel title}}', 'h3' ), $t( '<p>{{two sentences}}</p>' ) ) ); } ) ) ), array( 'contentWidth' => 'full' ) ) ),
	'process-steps-line' => array( 'Three steps with drawn line', array( 'process' ), 'A short three-step process joined by a line that draws itself.', '',
		$sec( array( $heading_block(), array( 'name' => 'bigspy/line-draw', 'attrs' => array( 'shape' => 'wave', 'mode' => 'scroll' ) ), $row( $times( 3, function ( $i ) use ( $fade ) { return array( 4, array( array( 'name' => 'bst/icon-box', 'attrs' => array_merge( array( 'icon' => '{{icon}}', 'title' => ( $i + 1 ) . '. {{step}}', 'text' => '{{one sentence}}' ), $fade( $i ) ) ) ) ); } ) ) ) ) ),

	/* ---------------------------------------------------------------- Proof */
	'proof-counters' => array( 'Key figures', array( 'proof' ), 'Three or four real figures that count up. Only with numbers from the user.', '',
		$sec( array( $row( $times( 4, function ( $i ) use ( $fade ) { return array( 3, array( array( 'name' => 'bst/counter', 'attrs' => array_merge( array( 'number' => '{{real number}}', 'suffix' => '+', 'label' => '{{what it counts}}' ), $fade( $i ) ) ) ) ); } ) ) ), array( 'paddingDesktop' => 64, 'textScheme' => 'light', 'bgPreset' => 'primary' ) ) ),
	'proof-testimonials-slider' => array( 'Testimonial slider', array( 'proof' ), 'Real customer reviews, two or three visible at once.', 'image: optional customer photos',
		$sec( array( $heading_block(), array( 'name' => 'bst/slider', 'attrs' => array( 'slidesDesktop' => 3, 'slidesTablet' => 2, 'slidesMobile' => 1, 'dots' => true ), 'inner' => $times( 3, function () { return array( 'name' => 'bst/testimonial', 'attrs' => array( 'name' => '{{customer name}}', 'role' => '{{role or company}}', 'text' => '{{real quote}}', 'rating' => 5 ) ); } ) ) ) ) ),
	'proof-testimonials-ticker' => array( 'Moving review strip', array( 'proof', 'logos' ), 'Many short real reviews moving slowly in a strip.', '',
		$sec( array( $heading_block(), array( 'name' => 'bigspy/ticker', 'attrs' => array( 'speed' => 60, 'pauseHover' => true, 'fadeEdges' => true ), 'inner' => $times( 5, function () { return array( 'name' => 'bigspy/ticker-slide', 'inner' => array( array( 'name' => 'bst/testimonial', 'attrs' => array( 'name' => '{{customer name}}', 'text' => '{{short real quote}}', 'rating' => 5 ) ) ) ); } ) ) ), array( 'contentWidth' => 'full' ) ) ),
	'proof-before-after' => array( 'Before and after', array( 'proof', 'media' ), 'A real before/after pair beside a short explanation.', 'image: matching before and after photos',
		$sec( array( $row( array( array( 7, array( array( 'name' => 'bst/before-after', 'attrs' => array( 'beforeLabel' => '{{before}}', 'afterLabel' => '{{after}}', 'radius' => 16, 'bsAnimation' => 'fade-up' ) ) ) ), array( 5, array( $h( '{{result title}}', 'h2', array( 'bsAnimation' => 'fade-left' ) ), $t( '<p>{{what changed and how}}</p>', array( 'bsAnimation' => 'fade-left', 'bsAnimationDelay' => 120 ) ), $btns( 1 ) ) ) ), array( 'verticalAlign' => 'center' ) ) ) ) ),
	'proof-logos' => array( 'Client logos strip', array( 'logos', 'proof' ), 'Real partner or client logos scrolling endlessly under a short line.', 'image: logo files',
		$sec( array( $t( '<p>{{trusted by line}}</p>', array( 'align' => 'center' ) ), array( 'name' => 'bst/logos', 'attrs' => array( 'layout' => 'marquee', 'grayscale' => true, 'bsAnimation' => 'fade-up' ) ) ), array( 'paddingDesktop' => 48, 'paddingMobile' => 32 ) ) ),
	'proof-marquee' => array( 'Keyword marquee', array( 'logos', 'text', 'proof' ), 'A strip of services or claims between two sections.', '',
		$sec( array( array( 'name' => 'bst/marquee', 'attrs' => array( 'items' => "{{keyword 1}}\n{{keyword 2}}\n{{keyword 3}}\n{{keyword 4}}\n{{keyword 5}}", 'separator' => 'star' ) ) ), array( 'contentWidth' => 'full', 'paddingDesktop' => 0, 'paddingTablet' => 0, 'paddingMobile' => 0 ) ) ),
	'proof-hotspot' => array( 'Image with hotspots', array( 'proof', 'media', 'commerce' ), 'Explain parts of a product, room or map with clickable points.', 'image: one detailed photo',
		$sec( array( $heading_block(), array( 'name' => 'bst/hotspot', 'attrs' => array( 'alt' => '{{image description}}', 'points' => $times( 3, function ( $i ) { return array( 'x' => 25 + $i * 25, 'y' => 40 + ( $i % 2 ) * 20, 'title' => '{{point title}}', 'text' => '{{one sentence}}', 'link' => '' ); } ), 'bsAnimation' => 'zoom-in' ) ) ) ) ),
	'proof-team' => array( 'Team', array( 'proof' ), 'Three real team members with photo, role and short bio.', 'image: one photo per person',
		$sec( array( $heading_block(), $row( $times( 3, function ( $i ) use ( $fade ) { return array( 4, array( array( 'name' => 'bst/team-member', 'attrs' => array_merge( array( 'name' => '{{name}}', 'role' => '{{role}}', 'bio' => '{{one sentence}}', 'bsHoverEffect' => 'lift' ), $fade( $i ) ) ) ) ); } ) ) ) ) ),

	/* ---------------------------------------------------------------- Pricing, FAQ */
	'pricing-tables' => array( 'Three price plans', array( 'pricing' ), 'Three real plans side by side, the middle one featured.', '',
		$sec( array( $heading_block(), $row( $times( 3, function ( $i ) use ( $fade ) { return array( 4, array( array( 'name' => 'bst/price-table', 'attrs' => array_merge( array( 'title' => '{{plan}}', 'price' => '{{real price}}', 'period' => '{{period}}', 'features' => "{{feature}}\n{{feature}}\n{{feature}}", 'buttonText' => '{{action}}', 'buttonUrl' => '{{link}}', 'featured' => 1 === $i ), $fade( $i ) ) ) ) ); } ) ) ) ) ),
	'pricing-toggle' => array( 'Plans with billing switch', array( 'pricing' ), 'Plans with real monthly and yearly prices.', '',
		$sec( array( $heading_block(), array( 'name' => 'bst/pricing-toggle', 'attrs' => array( 'labelA' => '{{period A}}', 'labelB' => '{{period B}}', 'badgeB' => '{{saving}}', 'plans' => $times( 3, function ( $i ) { return array( 'title' => '{{plan}}', 'priceA' => '{{real price A}}', 'priceB' => '{{real price B}}', 'desc' => '{{who it is for}}', 'features' => "{{feature}}\n{{feature}}", 'button' => '{{action}}', 'url' => '{{link}}', 'featured' => 1 === $i ); } ), 'bsAnimation' => 'fade-up' ) ) ) ) ),
	'faq-split' => array( 'FAQ beside a contact prompt', array( 'faq' ), 'Real questions and answers with an action for the rest.', '',
		$sec( array( $row( array( array( 4, array( $h( '{{FAQ title}}', 'h2', array( 'bsAnimation' => 'fade-right' ) ), $t( '<p>{{invite to ask}}</p>' ), $btns( 1 ) ) ), array( 8, array( array( 'name' => 'bst/accordion', 'attrs' => array( 'faqSchema' => true, 'bsAnimation' => 'fade-up' ), 'inner' => $times( 4, function () use ( $t ) { return array( 'name' => 'bst/accordion-item', 'attrs' => array( 'title' => '{{question}}' ), 'inner' => array( $t( '<p>{{answer}}</p>' ) ) ); } ) ) ) ) ) ) ) ) ),

	/* ---------------------------------------------------------------- CTA, contact */
	'cta-banner' => array( 'Call-to-action banner', array( 'cta', 'hero' ), 'Second banner mid-page or before the footer with one action.', 'image: banner background',
		$sec( array( array( 'name' => 'bst/banner', 'attrs' => array( 'heightDesktop' => 380, 'heightMobile' => 320, 'radius' => 20, 'overlayOpacity' => 55, 'bsAnimation' => 'zoom-in' ), 'inner' => array( $h( '{{call to action}}', 'h2', array( 'align' => 'center' ) ), $t( '<p>{{one line}}</p>', array( 'align' => 'center' ) ), $btns( 1, 'center', array( 'bsLoop' => 'pulse' ) ) ) ) ) ) ),
	'cta-gradient' => array( 'Gradient call to action', array( 'cta' ), 'Closing action without an image: heading, line and two buttons on a brand background.', '',
		$sec( array( array( 'name' => 'bigspy/animated-text', 'attrs' => array( 'text' => '{{call to action}}', 'effect' => 'words', 'tag' => 'h2', 'align' => 'center' ) ), $t( '<p>{{one line}}</p>', array( 'align' => 'center', 'bsAnimation' => 'fade-up' ) ), $btns( 2, 'center', array( 'bsAnimation' => 'fade-up', 'bsAnimationDelay' => 150 ) ) ), array( 'textScheme' => 'light', 'bgPreset' => 'primary', 'dividerTop' => 'wave' ) ) ),
	'contact-form-map' => array( 'Contact form and map', array( 'contact' ), 'Contact page: details and form beside a map of the real address.', '',
		$sec( array( $row( array( array( 5, array( $h( '{{contact title}}', 'h2' ), array( 'name' => 'bst/icon-list', 'attrs' => array( 'items' => array( array( 'icon' => 'phone', 'text' => '{{phone}}', 'link' => '' ), array( 'icon' => 'mail', 'text' => '{{email}}', 'link' => '' ), array( 'icon' => 'pin', 'text' => '{{address}}', 'link' => '' ) ) ) ), array( 'name' => 'bst/map', 'attrs' => array( 'address' => '{{address}}', 'height' => 280, 'radius' => 16 ) ) ) ), array( 7, array( array( 'name' => 'bst/form', 'attrs' => array( 'provider' => 'bigspy', 'title' => '{{form title}}', 'card' => true, 'bsAnimation' => 'fade-up' ) ) ) ) ) ) ) ) ),
	'contact-popup' => array( 'Action with popup form', array( 'cta', 'contact' ), 'A short pitch whose button opens a quote or booking form.', '',
		$sec( array( $h( '{{call to action}}', 'h2', array( 'align' => 'center', 'bsAnimation' => 'fade-up' ) ), $t( '<p>{{one line}}</p>', array( 'align' => 'center' ) ), array( 'name' => 'bst/popup', 'attrs' => array( 'trigger' => 'button', 'buttonText' => '{{action}}' ), 'inner' => array( array( 'name' => 'bst/form', 'attrs' => array( 'provider' => 'bigspy', 'title' => '{{form title}}' ) ) ) ) ) ) ),

	/* ---------------------------------------------------------------- Media, blog, shop */
	'media-video-cards' => array( 'Video hover cards', array( 'media', 'features' ), 'Projects or products with short muted clips playing on hover.', 'image and video: poster + short MP4 per card',
		$sec( array( $heading_block(), array( 'name' => 'bigspy/video-cards', 'attrs' => array( 'columns' => 3, 'columnsMobile' => 1 ), 'inner' => $times( 3, function () { return array( 'name' => 'bigspy/video-card', 'attrs' => array( 'title' => '{{card title}}', 'text' => '{{one sentence}}', 'link' => '{{link}}' ) ); } ) ) ) ) ),
	'media-gallery' => array( 'Gallery', array( 'media' ), 'Real project or venue photos in a masonry grid with lightbox.', 'image: 6 or more photos',
		$sec( array( $heading_block(), array( 'name' => 'bst/gallery', 'attrs' => array( 'layout' => 'masonry', 'columns' => 3, 'columnsMobile' => 2, 'lightbox' => true, 'hover' => 'zoom', 'bsAnimation' => 'fade-up' ) ) ) ) ),
	'media-video' => array( 'Video with text', array( 'media' ), 'An explainer or showreel video beside a short text.', 'video: YouTube, Vimeo or MP4 link; image: poster',
		$sec( array( $row( array( array( 5, array( $h( '{{video title}}', 'h2', array( 'bsAnimation' => 'fade-right' ) ), $t( '<p>{{what the video shows}}</p>' ), $btns( 1 ) ) ), array( 7, array( array( 'name' => 'bst/video', 'attrs' => array( 'url' => '{{video link}}', 'radius' => 16, 'bsAnimation' => 'zoom-in' ) ) ) ) ), array( 'verticalAlign' => 'center' ) ) ) ) ),
	'blog-latest' => array( 'Latest posts', array( 'blog' ), 'Latest blog posts as cards with a view-all link.', '',
		$sec( array( array( 'name' => 'bst/title', 'attrs' => array( 'text' => '{{section title}}', 'subtitle' => '{{short subtitle}}', 'tag' => 'h2', 'linkText' => '{{view all}}', 'linkUrl' => '{{blog link}}' ) ), array( 'name' => 'bst/posts', 'attrs' => array( 'count' => 3, 'layout' => 'grid', 'columns' => 3, 'columnsMobile' => 1, 'bsAnimation' => 'fade-up' ) ) ) ) ),
	'shop-products' => array( 'Product showcase', array( 'commerce' ), 'Best-selling or featured products with a view-all link.', '',
		$sec( array( array( 'name' => 'bst/title', 'attrs' => array( 'text' => '{{section title}}', 'subtitle' => '{{short subtitle}}', 'tag' => 'h2', 'linkText' => '{{view all}}', 'linkUrl' => '{{shop link}}' ) ), array( 'name' => 'bst/products', 'attrs' => array( 'type' => 'best_selling', 'count' => 8, 'layout' => 'grid', 'columns' => 4, 'columnsMobile' => 2 ) ) ) ) ),
	'shop-categories' => array( 'Shop categories', array( 'commerce' ), 'Product categories with images as a grid.', '',
		$sec( array( $heading_block(), array( 'name' => 'bst/product-categories', 'attrs' => array( 'count' => 6, 'layout' => 'grid', 'columns' => 3, 'columnsMobile' => 2, 'bsAnimation' => 'fade-up' ) ) ) ) ),
);
