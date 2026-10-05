=== BIGSPY Theme ===
Contributors: bentar
Requires at least: 6.5
Tested up to: 6.8
Requires PHP: 7.4
Stable tag: 1.3.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Multipurpose, fully customizable theme with a drag & drop header builder and a library of layout blocks.

== Description ==

See README.md in the source repository for the full feature list and the site-building workflow.

== Changelog ==

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

BIGSPY Theme, (C) 2026 Bentar. Distributed under the terms of the GNU GPL v2 or later.
Bundled icons are simple inline SVG paths shipped with the theme. Social network names and logos are trademarks of their respective owners.
