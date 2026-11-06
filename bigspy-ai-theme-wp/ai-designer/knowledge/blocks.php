<?php
/**
 * Block profiles for the block_catalog tool: when to use each block and what it needs.
 *
 * Keys are block names. Fields: needs (groups), use, avoid, data (facts or media it needs),
 * weight (light | medium | heavy frontend cost), pin (pins while scrolling), mobile, pairs,
 * min (attributes to set at least; children listed as "> child"). Child blocks are not listed:
 * they are described in their parent's "min".
 */
defined( 'ABSPATH' ) || exit;

return array(
	'bst/section'             => array( array( 'structure' ), 'Every top-level section of a page: background, padding, width, entrance motion.', 'Nesting sections inside sections.', '', 'light', false, 'Set paddingMobile smaller than paddingDesktop.', 'bst/row, bst/title', 'contentWidth, paddingDesktop; bgImageId+bgImage only with a real image' ),
	'bst/row'                 => array( array( 'structure' ), 'Columns side by side: text + image, 2–4 cards.', 'A single column (put blocks straight in the section).', '', 'light', false, 'Columns stack by spanMobile 12; reverseMobile puts the image after the text.', 'bst/column', '> bst/column with spanDesktop (out of 12)' ),
	'bst/spacer'              => array( array( 'structure' ), 'Fine-tuning vertical distance.', 'Building layout; use section padding first.', '', 'light', false, '', '', 'heightDesktop, heightMobile' ),
	'bst/divider'             => array( array( 'structure' ), 'Separating groups of content, optionally with a word or icon.', 'Between every block.', '', 'light', false, '', '', 'style, width' ),

	'bst/banner'              => array( array( 'hero' ), 'Page opening with an image, H1, message and call to action; also a mid-page promo banner.', 'Without a fitting image (use a section with a gradient instead).', 'One wide image (ImageGen or Media).', 'light', false, 'Set heightMobile and imageMobileId for a portrait crop when the subject is off-centre.', 'bst/heading, bst/text, bst/buttons', 'imageId+imageUrl, heightMode, loadPriority high only for the first banner at the top of the page (not in a slider); inner heading h1 + text + buttons' ),
	'bst/slider'              => array( array( 'hero', 'media', 'proof' ), 'Two or more banners in turn on the home page; testimonials or images one by one.', 'Hiding important content in slide 3+.', 'Two or more slides of real content.', 'medium', false, 'slidesMobile 1; arrowsMobile off when dots are on.', 'bst/banner, bst/testimonial', 'slidesDesktop, autoplay, dots; inner banners' ),
	'bst/banner-grid'         => array( array( 'hero', 'media', 'commerce' ), 'Magazine grid of image tiles: categories, collections, several offers.', 'Fewer than three images.', '3–5 images with short titles and links.', 'light', false, 'Tiles stack on phones.', '', 'layout, items (image, title, link)' ),
	'bigspy/zoom-hero'        => array( array( 'hero', 'media' ), 'A striking hero picture that grows to full screen while scrolling.', 'Twice on a page, or when the first message must show instantly without scrolling.', 'One strong landscape image.', 'heavy', true, 'Works; keep the overlay text short.', 'bigspy/animated-text', 'imageId+imageUrl, imageAlt, overlay' ),

	'bst/heading'             => array( array( 'text' ), 'Every heading H1–H6; the page H1.', 'Body text.', '', 'light', false, 'Set sizeMobile for big headings.', '', 'text, tag' ),
	'bst/title'               => array( array( 'text' ), 'Section title with a small subtitle and optional "view all" link.', 'The page H1.', '', 'light', false, '', 'bst/posts, bst/products', 'text, subtitle, tag' ),
	'bst/text'                => array( array( 'text' ), 'Paragraphs, lists, quotes and links.', 'Layout HTML, cards or columns (the server refuses it).', '', 'light', false, '', '', 'html' ),
	'bigspy/animated-text'    => array( array( 'text', 'hero' ), 'One or two key lines per page: slogan, rotating keywords, highlighted claim.', 'Body text or the H1 itself; more than two per page.', 'A short line.', 'light', false, 'Set sizeMobile.', 'bst/banner, bigspy/zoom-hero', 'text, effect, tag' ),
	'bst/marquee'             => array( array( 'text', 'proof', 'logos' ), 'A strip of short keywords, claims or services that scrolls endlessly.', 'Sentences people must read.', 'Five or more short items.', 'light', false, '', '', 'items, speed' ),
	'bst/alert'               => array( array( 'text', 'utility' ), 'A notice: offer deadline, warning, important note.', 'Decoration.', '', 'light', false, '', '', 'type, title, text' ),
	'bst/table'               => array( array( 'text', 'pricing' ), 'Specifications, comparisons, schedules.', 'Layout.', 'Real tabular data.', 'light', false, 'Scrolls sideways on phones; keep 4 columns or fewer.', '', 'data, header' ),
	'bst/code'                => array( array( 'utility', 'media' ), 'Small illustration no block can show: mockup, diagram, decorative CSS art.', 'Real content, layout or JavaScript; limited count and size per page.', '', 'light', false, 'Must fit 375px.', '', 'code, mode visual' ),

	'bst/icon-box'            => array( array( 'features' ), 'Features, services and benefits as icon + title + text, in a row of 3–4.', 'Long text.', 'A title and one or two sentences per item.', 'light', false, 'Columns stack.', 'bst/row', 'icon, title, text, position' ),
	'bst/image-box'           => array( array( 'features', 'media' ), 'Service, product or project cards with an image and link.', 'Without real images.', 'One image per card.', 'light', false, '', 'bst/row, bst/slider', 'imageId+imageUrl, title, text, link' ),
	'bst/icon-list'           => array( array( 'features', 'text', 'contact' ), 'Check-mark lists of benefits, contact lines, what is included.', 'More than about 10 lines.', '', 'light', false, '', 'bst/price-table', 'items' ),
	'bst/flip-box'            => array( array( 'features' ), 'A few services or values with a detail on the back of the card.', 'Information visitors must see at once (the back is hidden).', 'Front title and back text per card.', 'light', false, 'Flips on tap.', 'bst/row', 'icon, frontTitle, backTitle, backText, height' ),
	'bigspy/bento'            => array( array( 'features', 'media' ), 'A modern mixed-size grid of services, figures and images.', 'Fewer than four tiles.', '4–7 tiles with short content.', 'light', false, 'Tiles stack.', 'bst/counter', '> bigspy/bento-cell with size' ),
	'bigspy/expand-gallery'   => array( array( 'features', 'media' ), 'Two to six fields or services as image panels that expand on hover.', 'Without an image per panel.', 'A background image and title per panel.', 'medium', false, 'Uses mobileLayout (stack or peek).', '', '> bigspy/expand-panel with title, backgroundId+backgroundUrl' ),
	'bigspy/hover-reveal'     => array( array( 'features', 'media' ), 'A list of services or projects in large type; the image floats on hover.', 'Short lists of 1–2 rows.', 'Title, link and image per row.', 'light', false, 'Images show inline on phones.', '', '> bigspy/hover-reveal-item with title, link, imageId+imageUrl' ),
	'bst/tabs'                => array( array( 'features', 'faq' ), 'Same kind of content split by category: packages, product details.', 'Content everyone must read.', '', 'light', false, 'Tab titles must stay short.', '', '> bst/tab with title' ),
	'bst/accordion'           => array( array( 'faq', 'text' ), 'FAQs, policies, long details.', 'Key selling points (hidden by default).', 'Real questions and answers.', 'light', false, '', '', 'faqSchema; > bst/accordion-item with title' ),

	'bst/timeline'            => array( array( 'process' ), 'Steps, milestones, company history with dates.', '', 'Steps or dates.', 'light', false, 'Vertical on phones.', '', 'items, layout' ),
	'bigspy/stack-cards'      => array( array( 'process', 'features' ), '3–5 steps or reasons as cards stacking while scrolling.', 'Next to another pinned block.', '3–5 cards of content.', 'medium', true, 'Works; keep cards short.', 'bigspy/line-draw', '> bigspy/stack-card with background' ),
	'bigspy/scroll-story'     => array( array( 'process', 'media' ), 'A story or process where the image changes with each text step.', 'Without an image per step; next to another pinned block.', 'Image + text per step.', 'heavy', true, 'Images show above each step on phones.', '', '> bigspy/story-step with mediaId+mediaUrl' ),
	'bigspy/horizontal-scroll' => array( array( 'process', 'media', 'proof' ), 'Projects, a timeline or a collection moving sideways while scrolling down.', 'Fewer than three panels; next to another pinned block.', '3–8 panels.', 'heavy', true, 'Swipeable cards on phones.', '', 'panelWidth; > bigspy/hscroll-panel' ),
	'bigspy/line-draw'        => array( array( 'process', 'utility' ), 'A line or arrow that draws itself to connect steps or underline a claim.', 'As main content.', '', 'light', false, '', 'bigspy/stack-cards, bst/timeline', 'shape, mode' ),

	'bst/counter'             => array( array( 'proof' ), 'Real figures: years, customers, projects.', 'Invented numbers.', 'Real numbers from the user.', 'light', false, '', 'bst/row, bigspy/bento', 'number, suffix, label' ),
	'bst/testimonial'         => array( array( 'proof' ), 'Customer reviews.', 'Invented reviews.', 'Real quotes, names and roles.', 'light', false, '', 'bst/slider, bigspy/ticker', 'name, role, text, rating' ),
	'bst/listing-info'        => array( array( 'commerce', 'proof' ), 'Facts, price, rating and visit button of one directory listing (BIGSPY AI List mode).', 'When BIGSPY AI List mode is off: suggest turning it on instead.', 'A WooCommerce product with listing information.', 'light', false, '', '', 'productId, showFacts, showButton' ),
	'bigspy/theme-showcase'   => array( array( 'features' ), 'Showcase of the theme features on its demo site.', 'Any client site.', 'Nothing.', 'medium', false, '', '', 'showHero, showFeatures, showNews' ),
	'bst/star-rating'         => array( array( 'proof' ), 'Overall rating such as 4.9/5 from 320 reviews.', 'Invented ratings.', 'A real rating.', 'light', false, '', '', 'rating, label' ),
	'bst/before-after'        => array( array( 'proof', 'media' ), 'Before and after results: renovation, retouching, cleaning, treatment.', 'Two unrelated images.', 'A matching pair of images.', 'light', false, '', '', 'beforeId+beforeUrl, afterId+afterUrl, labels' ),
	'bst/hotspot'             => array( array( 'proof', 'media', 'commerce' ), 'An image with points explaining parts, products in a room, locations.', 'Without a detailed image.', 'One image and 3–6 points.', 'light', false, 'Tooltips open on tap.', '', 'imageId+imageUrl, points' ),
	'bst/progress'            => array( array( 'proof' ), 'Skill or completion levels.', 'Invented percentages.', 'Real values.', 'light', false, '', '', 'items' ),
	'bst/logos'               => array( array( 'logos', 'proof' ), 'Partner or client logos as a grid or endless strip.', 'Fewer than four logos.', 'Real logo images.', 'light', false, '', '', 'images, layout' ),
	'bigspy/ticker'           => array( array( 'logos', 'proof', 'commerce' ), 'A moving strip of any blocks: partners, reviews, products, a fleet.', 'Content people must read slowly.', 'Five or more slides.', 'medium', false, '', 'bst/testimonial', 'speed, widthDesktop; > bigspy/ticker-slide' ),
	'bst/team-member'         => array( array( 'proof' ), 'Team cards with photo, name and role.', 'Without real people.', 'Real names and photos.', 'light', false, '', 'bst/row', 'imageId+imageUrl, name, role' ),

	'bst/image'               => array( array( 'media' ), 'Single images with caption, lightbox and hover effect.', 'Decoration with no meaning.', 'One image.', 'light', false, '', '', 'imageId+imageUrl, alt' ),
	'bst/gallery'             => array( array( 'media' ), 'Several images as grid, masonry or slider with lightbox.', '', 'Several images.', 'light', false, 'Set columnsMobile.', '', 'images, layout, columns' ),
	'bst/video'               => array( array( 'media' ), 'One YouTube, Vimeo or MP4 video.', '', 'A video link.', 'light', false, '', '', 'url, posterId+posterUrl' ),
	'bst/video-button'        => array( array( 'media', 'hero' ), 'A play button over an image opening a video lightbox.', '', 'A video link.', 'light', false, '', 'bst/banner', 'url, imageId+imageUrl' ),
	'bigspy/video-cards'      => array( array( 'media', 'features' ), 'Cards that play a short muted clip on hover: projects, products, showreel.', 'Without short video clips.', 'Poster image + short MP4 per card.', 'medium', false, 'Plays on tap.', '', '> bigspy/video-card with posterId+posterUrl, videoUrl, title' ),
	'bst/audio'               => array( array( 'media' ), 'A song, podcast or voice message.', '', 'An audio file.', 'light', false, '', '', 'fileId+fileUrl, title' ),
	'bst/lottie'              => array( array( 'media', 'utility' ), 'A small vector animation from a Lottie JSON file.', 'Without a Lottie file.', 'A Lottie JSON URL.', 'medium', false, '', '', 'src' ),

	'bst/price-table'         => array( array( 'pricing' ), 'Pricing plans side by side, one marked popular.', 'Invented prices.', 'Real prices and features.', 'light', false, 'Columns stack.', 'bst/row', 'title, price, period, features, buttonText, buttonUrl' ),
	'bst/pricing-toggle'      => array( array( 'pricing' ), 'Plans with monthly/yearly switch.', 'A single billing period.', 'Real prices for both periods.', 'light', false, '', '', 'labelA, labelB, plans' ),
	'bst/countdown'           => array( array( 'pricing', 'cta' ), 'Offer or event deadline.', 'Fake urgency without a real date.', 'A real deadline.', 'light', false, '', 'bst/banner', 'deadline, label' ),

	'bst/buttons'             => array( array( 'cta' ), 'One or more calls to action.', '', 'Real link targets.', 'light', false, 'stackMobile.', '', '> bst/button with text, url' ),
	'bst/popup'               => array( array( 'cta', 'contact' ), 'Form or offer opened from a button or after a delay.', 'Opening automatically on every visit.', '', 'light', false, '', 'bst/form', 'trigger, buttonText; inner blocks' ),
	'bst/form'                => array( array( 'contact', 'cta' ), 'Contact, quote or booking form.', '', '', 'light', false, '', 'bst/map, bst/icon-list', 'provider, title' ),
	'bst/map'                 => array( array( 'contact' ), 'The business location.', 'Without a real address.', 'A real address.', 'medium', false, '', 'bst/form', 'address, zoom' ),
	'bst/social'              => array( array( 'contact', 'social' ), 'Links to social profiles.', 'Invented profiles.', 'Real profile links.', 'light', false, '', '', 'facebook, zalo, … links' ),
	'bst/share'               => array( array( 'social', 'blog' ), 'Share buttons on posts and products.', '', '', 'light', false, '', '', 'networks' ),

	'bst/posts'               => array( array( 'blog' ), 'Latest or chosen blog posts.', 'A site with no posts.', 'Published posts.', 'light', false, '', 'bst/title', 'count, layout, columns' ),
	'bst/products'            => array( array( 'commerce' ), 'WooCommerce products: latest, best selling, on sale, chosen.', '', 'Products in the shop.', 'medium', false, '', 'bst/title', 'type, count, layout' ),
	'bst/products-list'       => array( array( 'commerce' ), 'Compact product list for sidebars and footers.', '', 'Products in the shop.', 'light', false, '', '', 'type, count' ),
	'bst/product-categories'  => array( array( 'commerce' ), 'Shop categories with images.', '', 'Categories with images.', 'light', false, '', '', 'count, layout' ),
	'bst/payment-icons'       => array( array( 'commerce' ), 'Accepted payment methods near prices or in the footer.', '', 'The real methods accepted.', 'light', false, '', '', 'methods' ),

	'bst/nav-menu'            => array( array( 'navigation' ), 'A WordPress menu inside a page or footer.', '', 'An existing menu.', 'light', false, '', '', 'menuId, layout' ),
	'bst/widget-area'         => array( array( 'navigation', 'utility' ), 'A sidebar or widget area inside a page.', '', '', 'light', false, '', '', 'area' ),
	'bst/shortcode'           => array( array( 'utility' ), 'A plugin shortcode with no block equivalent.', 'Contact forms (use bst/form).', '', 'light', false, '', '', 'code' ),
);
