=== BIGSPY AI Theme WP ===
Contributors: bentar
Requires at least: 6.5
Tested up to: 6.8
Requires PHP: 7.4
Stable tag: 1.96.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Multipurpose, fully customizable theme with a drag & drop header builder and a library of layout blocks.

== Description ==

See README.md in the source repository for the full feature list and the site-building workflow.

== Changelog ==

= 1.96.0 =
* Scrolling text (marquee): items are now Marquee item blocks with text, icon or logo image, link and color; several rows with opposite directions and speed shift, item styles (plain, pill, card, logo), edge fade, tilt, vertical orientation, scroll-linked speed, pause on hover and per-device font and image sizes. Old marquees render as before.
* Floating contact: real Zalo brand mark, an icon picker for each button (the custom button defaults to a form icon), glossy buttons, card labels, ring waves and a phone-bar shine.
* Sidebar width per view (blog, single post, page, shop, product) in px or %.
* Editor List View: an "Add to <block>" row under each expanded container opens the inserter at the end of that container.

= 1.95.0 =
* Listing texts (Customizer > Layout, BIGSPY AI List mode): choose what to call a listing (Listings, Tools, Products, Services, Places, Companies or your own singular and plural) and every default text follows; each text of the filter bar, cards, listing page, Sponsored and categories can be overridden. Filter bar parts (search, category, sort, result count, Clear filters), sort choices and default sort, and category depth can be set. Search, Elements, Sponsored and Listing sidebar blocks use these texts when their own fields are empty.
* Note for sites already in List mode: they are set to "Tools" once on update, so the filter bar reads as before. A few texts now follow the chosen name, e.g. "12 tools found" (was "12 listings found"), "Related tools", "Sponsored tool.", and the Search block placeholder "Search tools...". Pick "Listings" or fill the field to change them. The Sponsored badge with an empty Badge text now shows "Sponsored by".
* Listing disclosure: the affiliate commission line is off by default ("Sponsored line only"); the new "Sponsored and affiliate lines" choice shows it. Sites that chose a value keep it.
* Stacking Cards and Flip Box look the same in the editor as on the published page; Stacking Cards has Edit cards / Preview stack, Flip Box has Front / Back toolbar buttons.
* Fix: the Customizer search box no longer overlaps the first control of an open panel or section.

= 1.94.1 =
* Fix: a script error on every page ("root is not defined", added with the Nav menu click sub-menus in 1.94.0) stopped the block scripts that run after it.

= 1.94.0 =
* Block spacing: blocks in page content are spaced by the theme.json block gap on the published page, as in the editor, so stacked blocks no longer touch. Block and author margins still win; full-width sections stay flush.
* Banner: on tablets and phones the set height is a minimum, so stacked content is never cut off.
* Map: per-device height or aspect ratio, load on click with a placeholder, info box beside, above or below with its own colors, direction button styles, full width, border and shadow.
* Popup: open on a CSS selector, after idle time or at the end of a post; show by device, login state and date range; side panel, thin top/bottom bar and full-screen positions; open animations, close button styles, overlay color, width, padding, shadow, border and close after form submit.
* Nav menu: divider and vertical accordion styles, active item colors, click-to-open sub-menus, depth, mobile drawer and accordion.

= 1.93.0 =
* Bento Grid: Tile height (fixed rows or grow with content), Content position (top, middle, bottom), Fill gaps automatically, and a switch for Lift tiles on hover.
* Stacking Cards: Layout "Simple list (no overlap)" with no sticky stacking, and Shadow strength (strong or soft).
* Customizer > Footer widgets: Footer scroll effect: Off, Reveal columns, or Curtain + reveal (the page slides up to uncover the footer when it fits the screen). Loads a small script only when on; visitors who ask for less motion see the footer as usual.
* Defaults keep the current look.

= 1.92.0 =
* Listing page sidebar: new "Listing page sidebar" widget area (Appearance > Widgets, List mode). With widgets it replaces the default sidebar; empty, the default Sponsor, Categories and Related resources still show. New Listing sidebar block (bst/listing-sidebar) with three inserter variations and editable titles, and an "Add them as blocks to edit" button that copies the default parts into blocks.
* Customizer > Layout > Listing design: "Disclosure line on listings" shows sponsorship and affiliate notes (default, as before), only the sponsorship note, or nothing. The Sponsored label on cards is unchanged.
* Social: Telegram, Threads, GitHub, Discord, Shopee, Lazada, Google Maps, website and custom links (icon, URL, color), labels next to icons, grid or vertical layouts, hover effects, separate icon size, background and border colors, tooltips, mobile alignment and spacing.
* Share: Telegram, Messenger and Print, pill buttons with text, a floating left bar (bottom bar on phones) or a phone-only bottom bar, label position, colors and spacing.
* Payment icons: NAPAS, white bordered card and dark styles, uploaded custom logos, spacing, radius, gray-to-color on hover and a leading label.

= 1.91.0 =
* Auto detect: new "Empty categories and tags" check. Lists post categories and tags with no published content (child categories counted), plus listing categories and tags in BIGSPY AI List mode. Select and delete; default categories and terms used in a menu are kept, and every deletion is logged so the term, its parent, meta and attached drafts can be restored.
* New Search block (bst/search), shared with Listing search: eight styles, live suggestions with images, prices and categories, content type and category filters, popular keyword buttons, rotating placeholder and the / shortcut.
* Tabs, Accordion, Table, Timeline and Icon List: many new styles, layouts and per-part colors (sticky tab bar, mobile accordion, 2-column accordion with search, table grid editor with sticky header and mobile cards, horizontal timelines, numbered icon lists). Existing content renders as before.
* Heading, Text, Star rating, Button, Alert, Progress, Counter, Spacer, Buttons and Section Title: new styles and options (kicker, decorative lines, text stroke, list and quote styles, 3D/neon buttons, icon-only buttons, circle progress, counter cards and more).
* Editor: shared controls and a wider Design layer (per-side borders, per-corner radius, 2-3 color gradients), and a Ready styles panel to pick presets by picture.
* Customizer: quick-edit labels on the preview open the matching section.
* Developers: before/after hooks for the main areas and blocks (docs/HOOKS.md). Optional "AI abilities (MCP)" feature, off by default, WordPress 6.9+.
* BIGSPY AI List: [bigspy_stat] shortcode and stat tokens in widgets, Listing design settings in the Customizer, widgets mapped automatically when switching from the old theme, AI Designer knows the listing blocks.
* Section background images chosen from the Media Library print as images with srcset; popups, lightbox, video popup and the mobile menu keep keyboard focus inside; Cart and Checkout block buttons match the theme colors; hard-coded Vietnamese strings are now translatable.
* Flatsome compatibility is now a feature in BIGSPY > Features (turned on once automatically for sites with Flatsome content). After updating, open an old Flatsome page; if you see raw shortcodes, turn it on.

= 1.90.0 =
* Automatic image load priority: the first large image on a page (Banner, slider's first slide, Zoom hero, Image, Gallery, Before/After, Hotspot, video posters, Scroll story, the single post featured image and Flatsome-compat banners and images) loads at once with fetchpriority high and is preloaded in the head; every later image is lazy-loaded. The logo loads at once but no longer takes the high priority. The per-block Load priority option still overrides it.
* Sponsored block and Listing elements Sponsored cards: new "Sponsors in the rotation list (0 = all)". Each reload shows the next sponsors by the number of cards and starts over only after the whole list was shown (fixes sponsors repeating between two sets).
* Editor: a Banner without an image no longer shows the upload box under its text.
* Admin: other plugins' notices are hidden on the theme's own admin pages (they still show on the Dashboard).

= 1.89.0 =
* Form price quote: price formulas (numbers, + - * /, comparisons, min/max/round/if, field references) and per-option prices, live total with line items, server-side recalculation and a saved quote snapshot with each submission. No eval; tampered input is ignored.
* Form fields keep stable IDs through renames and reordering, and can be shown or required depending on other fields (all/any rules). Duplicate names, missing references and loops are reported. Existing forms and mail tags keep working.
* AI Designer: form_get and form_configure read and change Form fields, conditions and price rules with a dry run, a diff and undo.

= 1.88.0 =
* New Sponsored block (BIGSPY AI List group): cards, columns per device, label, description, new tab, button style and text; the whole card is the partner link; soft outline buttons like the old theme (no button by default, set in Features or the block). Optional rotating "Sponsored by" badge for heroes.
* Sponsored cards: button position, border, radius, font size and weight, height, padding, colors and hover colors; card border, radius and background. Listing elements and Listing grid Sponsored rows get the same options; Listing elements can show rotating Sponsored cards.
* Listing elements Popular ranks by clicks, website traffic (Site Insights) or both.
* Heading keeps its own color inside Banner and Section text schemes. Listing search: button color, text color, border, radius and height; keeps a gap below it.
* Listing pages no longer overflow on phones (images, captions and tables stay inside). New Customizer switch to show the author on listing pages (off by default).
* Divider: spacing as padding so it never collapses, new Fade line style.

= 1.87.0 =
* Section and Banner: new Code background (animated) panel with nine presets (animated gradient, aurora, floating blobs, perspective grid, drifting dots, light rays, particles network, flowing lines, starfield) and a custom mode with scoped CSS plus HTML/animated SVG (scripts removed). Colors follow the palette; speed, density, opacity and mobile switch. Stops for reduced motion and pauses off screen.
* Listing elements: View more or Load more, slider mode, gap, Visit button position, blog card/compact/numbered layouts, category grid columns, border, radius and hover, grouped editor panels.
* Icon List: edit, open or remove the selected item's link from the toolbar; linked items are marked while editing.
* Breadcrumbs fall back to the theme trail when the SEO plugin breadcrumb module is off; optional breadcrumbs on pages whose H1 is hidden. New [bigspy_sponsored_card] and [bigspy_category_directory] shortcodes, Key features as cards, rewrite flush after switching from BIGSPY AI List.
* Live search caches results for visitors, counts characters correctly and cancels stale requests.
* Map accepts only real Google Maps embeds; Lottie player is self-hosted; the updater reports Requires WordPress and PHP.

= 1.86.1 =
* Fix: the admin area showed a critical error in 1.86.0 (unescaped quote in the What is new text).

= 1.86.0 =
* BIGSPY AI List: listing and Sponsored cards pick their layout from their own width (horizontal with the logo beside the text, one column when narrow), so they fit any column, sidebar or grid.
* BIGSPY AI List: faint logo watermark on cards (follows the old theme setting, Show/Hide per block), tags, title and description line counts, show/hide logo, rating, Featured badge and Visit button.
* BIGSPY AI List: Sponsored can sit between rows (after every N rows, as in the old theme) or above the grid.
* BIGSPY AI List: listing blocks use the site CSS in the editor, so they look the same while editing.

= 1.85.3 =
* BIGSPY AI List: the homepage "Sponsored by" brand ([bigspy_home_sponsor]) moves to the next Sponsored listing on every visit, like the other Sponsored slots.
* BIGSPY AI List: Featured on the Categories page changes order on every load (unless rotation was turned off in the old theme).

= 1.85.2 =
* Layout: a page whose first block is full width (such as a Banner hero) and shows no title now sits right under the header, without the gap above.

= 1.85.1 =
* BIGSPY AI List: the theme no longer adds its own homepage and page heroes; design heroes with the Banner block. [bigspy_home_destination_buttons] and {{listing_count}}-style counters work inside it.
* Fix: the admin area showed a critical error in 1.85.0 (syntax error in the What is new text).

= 1.85.0 =
* BIGSPY AI List: switching from the old BIGSPY AI List theme turns List mode on once.
* BIGSPY AI List: homepage hero, collection page heroes with a Sponsored tool, Categories directory and listings search toolbar, read from the old theme settings.
* BIGSPY AI List: directory layout for listing pages (header card, facts grid, sidebar with sponsor, categories and related resources).

= 1.84.0 =
* BIGSPY AI List: BIGSPY FAQ & FAQ Schema box on the listing edit screen; FAQ read from the description when the field is empty.
* BIGSPY AI List: listing page table of contents, section dividers, Key features, Pros & Cons, Integrations, Related resources and last updated date, each with a switch in Features.

= 1.83.2 =
* Settings: BIGSPY AI List mode switch, shared with the Features page; notes when WooCommerce is missing.

= 1.83.1 =
* AI Designer: uses only theme blocks while BIGSPY AI List is off, adds listing blocks when it is on; display-only legacy blocks are never offered.
* AI Designer: every block profile has use, avoid, mobile, pairs and minimum attributes.
* AI Designer: page_plan reports block variety (count, target, advice) so pages use more fitting blocks.

= 1.83.0 =
* New BIGSPY AI List mode (Features, off by default): WooCommerce products become directory listings with 21 listing fields (ACF optional), FAQ and ItemPage/FAQPage schema.
* Sponsored slots and one-click AD column in Products, rotating by views; [bigspy_sponsored_grid] shortcode.
* Blocks bigspy/listing-grid, category-grid, search, elements and bst/listing-info, plus the old shortcodes; locked with an enable button while the mode is off.
* Listing cards on shop, category, tag and search pages; Related listings; BIGSPY AI List patterns.
* Customizer > Layout > Homepage sidebar: choose the block range shown beside the new Homepage sidebar widget area.
* Legacy BIGSPY AI List layout blocks render on old content (hidden from the inserter).
* AI Designer: plugins can declare read or content tools with bst_aid_extra_tool_groups; listing blocks are skipped while the mode is off.
* 117 new Vietnamese strings.

= 1.82.0 =
* New block: Theme Showcase (bigspy/theme-showcase) shows the installed version, block count, features and latest updates, with a Get the theme button.
* Banner and Image: Load priority option (Auto, Above the fold, Lazy) adds fetchpriority=high for hero images.
* WooCommerce: the free-shipping bar follows min_amount/either/both, ignore discounts and free-shipping coupons.

= 1.81.0 =
* AI Designer: block profiles (block_catalog) and 42 section recipes with motion (section_recipes).
* AI Designer: page outline card (page_plan); build_sections builds up to 8 recipe sections per call.
* AI Designer: SVG/HTML/CSS illustrations in Code blocks: 8 blocks and 6 KB each without approval, 12 blocks and 40 KB at most.
* AI Designer: model and specialty pickers in the chat bar; optional fast model for Effort Low; admins can limit models.
* AI Designer: attach Media files, drop or paste images, videos and text documents in the chat.
* AI Designer: per-run time, model and tool calls, tokens and cost.
* AI Designer: Guidelines and specialty contexts autosave; the AI saves what you say applies from now on, with undo in the chat.
* AI Designer: default design rules for banners, block variety and motion.

= 1.80.3 =
* Media ATND moved from Auto detect into the media picker, with keyword, short description, language and model.

= 1.80.2 =
* Horizontal Scroll: up to three panels on desktop; sliding starts at the fourth.
* Horizontal Scroll: full-width sections keep their width while pinned.

= 1.80.1 =
* Horizontal Scroll: panel width follows the block width and matches the editor.
* Horizontal Scroll: the whole section stays on screen while pinned, removing blank space above and below.

= 1.80.0 =
* Auto detect: deeper unused media scan (shortcodes, WebP copies, all options and metadata); green tick and red X in the Media Library, usage filter, thumbnails, select all, keep a file.
* Auto detect: unused files move to a restorable trash after a fresh check; files in use are refused.
* Media ATND: in the media picker, enter a keyword and short description, choose language and model, and AI writes alt text, title, caption and description.
* Horizontal Scroll: pins on all screens; pager removed.
* AI Designer: keeps attributes and nested content of bigspy blocks when editing; parent and child rules for motion blocks.

= 1.79.1 =
* Horizontal Scroll: panels show 3:1 on phones so the next one always peeks; drag with a mouse when not pinned.
* Section: keeps content clear of shape dividers on phones.

= 1.79.0 =
* Horizontal Scroll: previous and next buttons with position dots, on phones and desktop.
* Infinity Flow: new option to keep moving under reduced motion, on by default; pauses only on hover, press and hold, or keyboard focus.
* Flip Box: fills flex columns instead of collapsing.

= 1.78.1 =
* Stacking Cards, Scroll Story, Horizontal Scroll and Zoom Hero now stick correctly inside a Section.
* Infinity Flow: no scrollbar when reduced motion is on; swiping still works.

= 1.78.0 =
* AI Designer: a fixed block rule that context and prompts cannot override, enforced on the server.
* Move CF7 forms: Form blocks set to Contact Form 7 are moved too, and redirects from Redirection for Contact Form 7 are carried over.

= 1.77.2 =
* Features page: a saved SMTP password shows as ******** instead of an empty box.

= 1.77.1 =
* Move CF7 forms: default options of select, radio and checkbox fields are kept, and [wpcf7.*] mail tags are filled in.
* About page: new support links and a GitHub star button.
* AI Designer: preview switch in the header and a draggable preview width.

= 1.77.0 =
* Auto detect menu: duplicate focus keywords, H1 tags, unused media, broken links and a security scan. Reports only.
* Features page: WebP on upload, SMTP email, related links in posts, header and footer code. Each is off by default.
* Built-in form: file upload field and server checks for URL, number, date and choices. Move CF7 forms page.
* AI Designer: no new core HTML blocks, limits on code and CSS, editable share report, style_options tool.

= 1.76.0 =
* Motion update: Animated Text, Scroll Story, Stacking Cards, Hover Reveal List, Line Draw, Horizontal Scroll, Zoom Hero, Video Hover Cards and Bento Grid blocks.
* Advanced motion options on Section, Button, Image, Icon Box, Counter, Gallery and Slider; count-up numbers in Heading/Text; Counter inside a sentence with start value and counting style.
* Customizer: motion intensity and style, page transitions, magnetic buttons, custom cursor (off by default).
* Colored block icons; brand names read BIGSPY AI.
* Fixed per-page block CSS rewriting embedded data: images.

= 1.75.0 =
* Products block: compact list, image ratio, radius and hover effect, shadow depth, text size, equal heights, infinite scroll, out-of-stock placement. New Products list block.
* Classic cart and checkout: free-shipping progress bar, coupon field position, note before payment.
* Drawn inserter previews and detailed descriptions for every block; Vietnamese block names and descriptions.
* Renamed Continuous ticker to Infinity Flow and Expanding gallery to Spotlight Panels (block slugs unchanged).
* AI Designer fixes: effort kept, immediate Stop, warnings for blocked tools, clear image failures with retry, user-language replies. Fixed a pattern preview error in the editor.

= 1.19.1 =
* Renamed the native block inserter category to BIGSPY AI Builder; all existing blocks and the native editing workflow are preserved.
* Synchronized the runtime version with the theme release version.

= 1.19.0 =
* Renamed the theme folder, text domain and pattern slugs to bigspy-ai-theme-wp to match the repository. Install as a new theme; export options from the old theme first and import them here (old export files are still accepted).

= 1.18.1 =
* Fixed empty page-design selections incorrectly loading the current page as the footer.
* Prevented a page from rendering itself as its own footer and kept native footer widgets as the fallback.
* Added explicit empty-ID handling for header and product page designs.

= 1.18.0 =
* Removed the separate BIGSPY AI Builder workspace, its page actions, admin-bar link, assets and save endpoint.
* Page design now uses BIGSPY blocks and patterns directly in the native WordPress page editor.
* Updated header/footer/product template instructions to use the native page editor; existing block content and template selections are preserved.

= 1.17.2 =
* Added optional archive product-card swatches for configured global color/image attributes, linking to the product with the chosen value preselected.
* Added accessible labels, keyboard focus styling, and kept the feature disabled by default.

= 1.17.1 =
* Added optional AJAX navigation for native WooCommerce category, attribute, rating, price and sorting filters.
* Refreshes native filter widgets and announces product updates while preserving browser history and full-page fallback.

= 1.17.0 =
* Added opt-in label, color and image swatches for global WooCommerce product attributes.

= 1.16.5 =
* Replaced unreliable native outline dragging with pointer-based sorting and clear before, inside, and after drop feedback.
* Added compatible block movement checks and corrected disclosure spacing so arrows no longer overlap root labels.

= 1.16.4 =
* Made the full structure row a native drag source and removed reliance on the undocumented move-position selector.
* Added compatible drop targeting, invalid-drop feedback, and a saved-change notice after moving a block.

= 1.16.3 =
* Fixed a missing drag-lock state that prevented the visual builder structure from rendering.

= 1.16.2 =
* Aligned top-level page structure labels with the sidebar guide and replaced reorder arrows with drag handles.
* Added validated drag and drop between compatible Sections, Rows, Columns, and sibling positions.

= 1.16.1 =
* Tightened visual builder page-tree indentation and controls so nested columns and blocks remain readable in the sidebar.

= 1.16.0 =
* Reworked the visual builder sidebar into focused Elements and Structure views.
* Added element categories, a separate patterns library, and selection-aware insertion.
* Added collapsible, selectable page structure with clearer reorder controls.

= 1.15.1 =
* Added a standalone BIGSPY AI visual page workspace with native blocks, patterns, inspector, responsive widths, undo/redo and revision-aware save.
* Added optional text variation buttons, buy-now checkout flow and previous/next product navigation.
* Added optional page-based header/footer/product designs and WooCommerce product-part blocks.
* Added read-only Quick View, classic native AJAX price/sort filters, load-more/infinite scrolling, fixed-date countdown and optional portfolio content.

= 1.14.3 =
* Updated cover wordmarks to BIGSPY AI / THEME WP, including desktop and mobile mockup branding.

= 1.14.2 =
* Renamed the theme to BIGSPY AI Theme WP for the BIGSPY AI ecosystem. Existing theme identifiers and saved settings remain compatible.

= 1.14.1 =
* Aligned the theme cover with the BIGSPY AI ecosystem: navy, teal and a light background.

= 1.14.0 =
* Replaced the wireframe theme cover with a clear desktop/mobile website showcase.
* Added a published-page design selector for 404 errors, keeping the HTTP 404 response.
* Added opt-in maintenance presentation with HTTP 503, no-cache/noindex and administrator access.
* Added optional section menu highlighting with native anchor navigation and sticky-header spacing.
* Added three opt-in CSS recipes derived from the active child-theme audit: three-line product titles, branded coupon button and Countix countdown hiding on cart/checkout.
* Added Vietnamese translations for site tools and Studio block actions.

= 1.13.0 =
* Added selected-block tools for duplicate, move and copy/paste appearance in BIGSPY Studio.
* Added opt-in classic shop pagination with browser history and full-navigation fallback.
* Added default/simple/focused cart and checkout presentation, classic sticky totals and automatic cart quantity updates.
* Added product tab styles, a shared additional tab and content before/after the purchase form.
* Disabled WooCommerce FlexSlider for the stacked gallery layout.

= 1.12.0 =
* Added live search scope settings for products, pages/posts, or mixed results.
* Added configurable suggestion count and debounce delay.
* Added optional product SKU and product-tag matches; these queries run only when enabled.
* Mixed suggestions retain both content and WooCommerce product results, with independent debouncing for each search form.

= 1.11.0 =
* Added one-click online store, service and minimal header layouts, editable in the existing drag-and-drop builder.
* Added one-click store, service and minimal footer layouts that preserve widget content, copy and colors.
* Added direct links from Customizer to Site Identity, Menus and Widgets, plus clearer setup instructions.

= 1.10.0 =
* Added a mobile drawer for existing WooCommerce shop sidebar and filter widgets.
* Added accessible open/close controls, background scrim, Escape-key close and desktop breakpoint reset.
* The drawer is shown only when the shop sidebar is configured and has widgets.

= 1.9.0 =
* Added optional stacked and left-thumbnail product gallery layouts.
* Added optional sticky product summary and mobile quick-purchase bar using the native WooCommerce form.
* All new product page controls are disabled or use the default layout unless configured.

= 1.8.0 =
* Added a per-block Preview animation action in the editor to replay the selected scroll effect using its configured delay and duration, without saving or publishing the page.

= 1.7.0 =
* Added shared block hover controls: gentle lift, soft zoom, soft shadow and grayscale-to-color.
* Hover motion is available only on hover-capable devices and is disabled when reduced motion is requested.

= 1.6.0 =
* Added Soft focus, Gentle rotate and Soft bounce scroll effects to the shared block animation panel.
* Animation duration can now be tuned per block, alongside the existing delay setting.
* Reduced-motion settings also clear blur and compositor hints.

= 1.5.0 =
* BIGSPY Studio now includes published WordPress synced patterns in a Saved designs group.
* Saved designs are inserted as native synced blocks, so they remain editable in WordPress and can update every instance when their source changes.
* Uses WordPress core data and block APIs; no Flatsome code or assets are included.

= 1.3.0 =
* Opt-in CSS library above the native Additional CSS editor: 64 checkbox recipes in 10 collapsible groups for service sites and shops.
* Live CSS preview, accent-insensitive search, active-only filter, group counts, disable-all and undo.
* Conflicting button shapes and image fits replace each other; only allowlisted recipe IDs are saved.
* CSS library selections are included in theme configuration export, import and reset. All recipes default to off.
* Recipes use theme colors and target BIGSPY/core/WooCommerce components. No review data or checkout behavior is changed.

= 1.2.5 =
* Native WooCommerce reviews: responsive cards, readable customer avatars, themed rating colors, focus states and a two-column desktop review/form layout.
* My Account: responsive login/registration cards and a consistent edit-account form surface.
* Compact primary-menu spacing on narrow desktop headers; long menus can still wrap safely.
* Customer accounts, review photos, masking, moderation and store reactions remain in the optional BigSpy Customer Accounts & Product Reviews plugin.

= 1.2.4 =
* Banner: optional mobile image (shown up to 549px) so a portrait crop can keep the text area clear on phones.
* Slider: “Arrows on mobile” option – hide the arrows on phones and rely on swipe and dots.
* Posts block: the default “Read more” label is translated again (it bypassed the option translation).

= 1.2.3 =
* Banners and other fixed-height block images now fill their frame inside page content (the content image rule no longer overrides them).
* Shop grid drops to fewer columns when the content column is narrow (e.g. next to a sidebar), instead of squeezing cards; long “Add to cart” labels end with an ellipsis.
* Products block: the “Order by” setting is now applied, with a new control in the editor (custom order, price, name, best selling, rating, random).
* New block icons for beauty, shop and service sites: leaf, droplet, sun, sparkles, flask, award, tag, refresh, card, smile.
* Default option labels (header button, footer call-to-action, copyright, catalog mode, read more) follow the site language until they are edited.

= 1.2.2 =
* Fixes from the second live preview: the full-screen search panel no longer opens on load, product sliders keep equal slide widths, product and category names use the text color, compact menu in the bottom header row, legacy banners show their background color when the image is missing.
* Search field placeholder option (inherited from Flatsome).
* Vietnamese translation of visitor-facing strings (admin stays in English).

= 1.2.1 =
* Inherit Flatsome design settings as defaults: logo, colors, fonts, header layout and heights, buttons, contact details, footer colors, shop columns, menu locations. Nothing is written to the database.
* Safety: the product card template falls back to the active theme / WooCommerce template when the theme is not loaded, and WooCommerce template cache is cleared around theme previews and theme switches.
* Google Fonts are requested through the endpoint that tolerates single-weight families.
* Fixes from the first live preview (list offset, category columns, empty sidebars, add-to-cart label).

= 1.2.0 =
* Shop: theme-designed product card (badges, category, rating, modern add-to-cart), image ratio and button options, "Load more" for product grids, compact product list.
* Legacy layout shortcodes, part 2: [ux_products], [ux_product_categories], [ux_products_list], [ux_banner], [text_box], [ux_banner_grid], [tabgroup], [featured_box], [ux_image_box], [ux_stack], [divider], [message_box], [ux_gallery], [ux_video], [search], [follow], [price_table] and more.
* Header: vertical categories menu element (menu or product categories) and live search suggestions (products, or posts / pages without WooCommerce).
* New blocks: Heading, Text (Visual / Code), Image, Buttons, Button, Divider, Spacer, Code / HTML, Product Categories.
* Footer call-to-action band.

= 1.1.0 =
* Legacy layout shortcodes: pages built with Flatsome UX Builder shortcodes ([section], [row], [col], [ux_image], [ux_html], [button], [accordion], [blog_posts]…) render with BIGSPY markup, so a site can switch themes without rebuilding content. UX Blocks (e.g. the footer block) keep working.
* New `bst_show_title` filter and `bst_footer_start` action; post cards accept an h5 heading.

= 1.0.0 =
* First release: header builder, global styles, layout options, per-page layout, 16 layout blocks, responsive visibility and scroll animations for every block, block patterns, WooCommerce integration, floating contact buttons, options import/export.

== Copyright ==

BIGSPY AI Theme WP, (C) 2026 Bentar. Distributed under the terms of the GNU GPL v2 or later.
Bundled icons are simple inline SVG paths shipped with the theme. Social network names and logos are trademarks of their respective owners.
Bundled library: @lottiefiles/lottie-player 2.0.8 (MIT), https://www.npmjs.com/package/@lottiefiles/lottie-player, in assets/js/vendor/lottie-player.js.

= 1.15.2 =
* Prevent a page selected as a custom header from rendering itself inside the header area; retain the normal site header on that page.
