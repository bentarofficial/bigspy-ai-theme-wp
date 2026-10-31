=== BIGSPY AI Theme WP ===
Contributors: bentar
Requires at least: 6.5
Tested up to: 6.8
Requires PHP: 7.4
Stable tag: 1.19.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Multipurpose, fully customizable theme with a drag & drop header builder and a library of layout blocks.

== Description ==

See README.md in the source repository for the full feature list and the site-building workflow.

== Changelog ==

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

= 1.15.2 =
* Prevent a page selected as a custom header from rendering itself inside the header area; retain the normal site header on that page.
