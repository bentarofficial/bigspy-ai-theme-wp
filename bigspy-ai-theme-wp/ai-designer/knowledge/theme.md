# BIGSPY AI theme action guide

Call overview for current registered blocks/patterns, Customizer field IDs, menus,
UX Blocks, layouts, header/footer sources and active plugin names. Call block_schema
before writing attributes; never guess a schema. This theme uses the bst/ namespace
(new blocks are bigspy/ticker, bigspy/ticker-slide, bigspy/expand-gallery and bigspy/expand-panel).

## Structure and blocks
Every page stays editable as visual blocks. The server refuses new raw HTML, layout
HTML inside Text, writes that leave a page under 90% editable, and more than 4 Code
blocks or 10 KB of code; rebuild the content as blocks instead of working around it.
Build pages with Section > Row > Column. Sections handle background, padding,
width and responsive spacing; columns contain any content blocks. Use Heading for
real H1–H6, Text for copy, Buttons > Button for actions and Image for media.
Banner combines background imagery and overlaid content; Slider advances between
slides. Logo showcase supports a continuously scrolling strip of logos; use Infinity Flow
(bigspy/ticker) for richer moving cards containing any blocks (reviews, fleet, team, partners with
names). Marquee is for running text. Animated Text (bigspy/animated-text) is a headline with an effect: reveal (words light up while scrolling), words, typewriter, rotate (words list, one per line), scramble, gradient or marquee (big type sliding with scroll); Heading also has textMotion reveal/words/scramble. Scroll Story (bigspy/scroll-story > bigspy/story-step, each step has an image) is for processes and features; Stacking Cards (bigspy/stack-cards > bigspy/stack-card) for services, plans and reasons. Hover Reveal List (bigspy/hover-reveal > bigspy/hover-reveal-item with title, meta, link, image) suits agency/portfolio service or project lists. Line Draw (bigspy/line-draw: shape wave/curve/arrow/loop/zigzag/underline/custom, mode view or scroll) connects steps or decorates. Horizontal Scroll (bigspy/horizontal-scroll > bigspy/hscroll-panel) slides panels sideways while scrolling down. Zoom Hero (bigspy/zoom-hero, image + inner heading/button) grows a framed image to full screen. Advanced motion attributes: Section mxSticky, mxScrollBg (page background color while in view); Button mxButton magnetic/arrow/shine; Image mxReveal left/right/up/circle/kenburns; Icon Box mxDraw; Counter mxScramble; Counter also has layout stack/inline ("Over 3,000 happy customers" in one line), from (start value), ease out/linear/bounce, textSize, textColor. Inside Heading or Text, wrap a number in <span class="bs-countup">11.256</span> to make it count up from 0 on scroll (separators are kept); Gallery mxStagger. Slider effect also accepts zoom, flip, coverflow. Video Hover Cards (bigspy/video-cards > bigspy/video-card: still image, short MP4, title, text, link) play muted clips on hover/tap. Bento Grid (bigspy/bento > bigspy/bento-cell size small/wide/tall/large, background or image) shows highlights. Section mxBg grain/mesh/aurora and mxGlass (frosted columns). Customizer > Performance also has Page transitions (fade/slide), Magnetic buttons everywhere and Custom cursor; keep them off unless the brand wants a playful feel. Site-wide Motion intensity (subtle/normal/strong) and Motion style (elegant/dynamic/minimal) live in Customizer > Performance; suggest them instead of adding effects block by block. Spotlight Panels (bigspy/expand-gallery) presents 2–6 services,
categories or projects; active panels expand and mobile defaults to swipe with peek.
Tabs > Tab groups related content; Accordion > Accordion item suits questions/FAQ.
Price table and Pricing toggle show supplied prices. Never invent reviews or fares.
Icon box, Team member, Testimonial, Counter, Timeline, Gallery, Video, Map, Form,
Posts, Social and shop blocks cover common sections. Code is for custom illustrations
or embeds; prefer native blocks. Call list_patterns/get_pattern for starter sections.

## Header and menus
Header builder has desktop/mobile layouts, rows and left/center/right zones.
header_layout(read) returns exact elements and current placement; header_layout(set)
changes it with approval. Customizer controls sticky/transparent behavior and sizing.
A menu needs BOTH a registered location assignment (primary, secondary, mobile,
footer, vertical) AND the matching menu element in the header. Use get_menus, then
set_menu with the correct location and header_layout to verify desktop AND mobile.

## Footer and Theme Builder
Footer priority: matching published Theme Builder override, then selected published
UX Block, then selected published public page, then widget rows. Copyright bar is
separate. To build: ux_block(create) draft with BIGSPY AI blocks, preview it, request
publish approval, then set_footer(source=blocks,id) approval. A page source uses
set_footer(source=page); source=default returns widget footer. A draft never renders
as a live footer. Widget-area blocks reference existing registered widget areas.
Theme Builder layouts (bst_layout) support before_header, header, after_header,
before_content, after_content, before_footer, footer, single and 404. Create drafts
with theme_layout, set location and display rules, preview then publish by approval.
Rules include entire/front/blog/pages/posts/archives/search/404 and optional shop /
products, inclusion/exclusion IDs and categories, login/device conditions and priority.
Lower priority numbers win. Inspect overview for available choices.

## Other capabilities
get_settings lists permitted Customizer sections/fields: colors, typography,
layout, blog, header, footer, floating contact buttons and WooCommerce presentation.
update_settings and custom_css affect global design and require approval. Never
write security, scripts, performance or maintenance fields through generic settings.
WooCommerce must be active for products/shop blocks. Products block (bst/products): layout grid,
slider or list; imageRatio, imageRadius, imageHover (zoom, swap, fade, gray, lift), depth and
depthHover 0–5, textSize xs–xl, equalize, pagination "infinite", stock include/last/exclude.
Products list (bst/products-list) is a compact list with a heading for sidebars and footers.
Classic cart/checkout options live in Customizer > WooCommerce > BIGSPY AI: cart & checkout
(free-shipping bar, coupon position, note before payment); they do not apply to Cart/Checkout blocks. Create product drafts from
verified prices. Publishing or editing live products needs approval. Payment gateways,
keys, orders and customers are outside AI tools. Explain manual configuration.
Theme options export/import is a manual admin operation; inspect the theme's menu
and follow its UI rather than invent an import tool. Never include service keys.

## Homepage workflow
Read site_info + overview and guidelines. Agree page goal, palette/fonts, sections
and authentic facts. Build a draft page, one or two sections per page_sections call.
Generate necessary imagery with metadata, check_page and visually check desktop /
mobile. Request publication separately. After approval set site_settings with
show_on_front=page,page_on_front=<published page>. Build footer, menus and header
with separate approval cards. Keep manual_checklist for real logo/photos, contact
verification, Search Console and other human tasks. Respect advice-only tasks and
budget limits (extra USD requires a budget_extension approval). If the task is stopped, no new write should start.

Styling order: block options, Style presets (style_options, attrs.bsPresets), CSS
library (suggest it), custom_css, bst/code. Never create core/html. The built-in Form
block has a file field; files are mailed, not stored. WP admin also has Auto detect
(auto_detect_results) and Features pages; only the user turns features on.
