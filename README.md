# BIGSPY AI Theme WP

A multipurpose, fully customizable WordPress theme in the spirit of Flatsome – licensed under the GPL, so it can be used on **an unlimited number of websites**. Built for agencies that design websites for clients, both **service / business sites** and **online shops**.

> Rebuilt from scratch on the lessons of the BIGSPY AI LIST theme: all listing-specific code was removed, while the Section/Row/Column blocks, header/footer builder and Customizer ideas were kept and extended.

## Repository structure

- `bigspy-ai-theme-wp/` – theme source (this folder is what goes into `wp-content/themes/`).
- `build.ps1` – packages the theme as a ZIP for upload through WordPress Admin.
- `releases/` – packaged ZIP files.

## Requirements

- WordPress 6.5+ · PHP 7.4+
- No required plugins. WooCommerce and Rank Math / Yoast (breadcrumbs) are optional.

## Features

See the [Vietnamese Flatsome comparison](docs/flatsome-gap-audit-vi.md) and [BIGSPY roadmap](docs/roadmap-vi.md) for the current parity status and planned work.

**Page editing:** use **Pages → Edit** and build with BIGSPY blocks in the native WordPress editor. This is the recommended everyday workflow. The separate BIGSPY AI Builder workspace was removed in 1.18.0; existing Flatsome UX Builder content remains readable through compatibility support, with safe conversion planned for legacy pages.

### Changed in 1.19.1

- Renamed the native WordPress block inserter category to **BIGSPY AI Builder**. Existing blocks, saved content and the native editor workflow are preserved.
- Synchronized the runtime version constant with the release version.

### Fixed in 1.18.1

- Empty header/footer/product page selections now use the default layout instead of loading the current page. This fixes repeated homepage content replacing footer widgets.
- A page cannot render itself as its own footer.

### Changed in 1.18.0

- Removed the standalone BIGSPY AI Builder workspace, its launch links and custom save endpoint.
- Design pages with BIGSPY blocks and patterns directly in the WordPress page editor. Existing block content and page-based header/footer/product template selections continue to work.

### Added in 1.17.2

- Optional product-card swatches on WooCommerce shop and category archives for global attributes configured as color or image swatches.
- Each swatch opens the product page with that attribute selected. The option is off by default and is enabled in **Customizer → WooCommerce → BIGSPY: product cards & page**.

### Added in 1.17.1

- Optional AJAX shop navigation now supports WooCommerce's native category, attribute, rating, price and sorting filters. Matching filter widgets refresh with the results; the address bar and browser back/forward remain in sync.
- Loading and result updates are announced accessibly. If the response or markup is incompatible, navigation falls back to a full page load. Third-party filter plugins keep their own navigation.

### Added in 1.17.0

- WooCommerce global attributes can use BIGSPY label, color, or image swatches. Choose the type under **Products → Attributes** and set each term's color, image, and optional short label under **Configure terms**.
- Swatches replace the visible dropdown only when **Customizer → WooCommerce → BIGSPY: product cards & page → Chọn biến thể bằng nút** is enabled. WooCommerce's native select remains the source of truth, including availability, variation matching, reset, add-to-cart validation, and keyboard focus.
- This release covers global product attributes on single-product pages. Custom product attributes remain text buttons. Per-variation image galleries use WooCommerce's native Variation Gallery when the installed WooCommerce version provides and enables it; BIGSPY keeps the standard WooCommerce gallery markup and zoom/lightbox/slider support.

### Added in 1.14.0

- New 1200 × 900 theme cover showing a desktop/mobile website design. This is a showcase mockup, not an installed demo.
- Customizer → Advanced → Site tools: choose public published pages for a custom 404 or maintenance screen. Draft/private/password pages fall back to the built-in screen. A custom 404 keeps its HTTP status.
- Maintenance is off by default. Visitors receive 503/Retry-After/noindex; administrators can keep working. Login, admin and API endpoints are unaffected. Purge page/CDN caches when toggling it; this presentation feature does not restrict direct file/API access.
- Customizer → Layout → Site layout: optional section menu highlighting. Set an HTML anchor on a block and link to `#anchor` in a menu. Scrolling updates the active item; anchor spacing accounts for the sticky header. Native navigation remains available without JavaScript.
- Additional CSS library: three-line product titles, brand-colored coupon button and an optional Countix layout-1 countdown hide on cart/checkout. These were reviewed against Flatsome Child, adapted for BIGSPY markup and remain off by default.
- See [parent/child audit](docs/flatsome-child-audit-vi.md) for the source of each addition and plugin-specific fixes that remain separate.

### Added in 1.13.0

- BIGSPY Studio: duplicate/move the selected block and copy/paste appearance, respecting editor locks.
- Customizer → WooCommerce: optional classic shop AJAX pagination, native URL/history and full-navigation fallback. Sorting and filters still navigate normally; plugins may need to initialize on `bst:shop-updated`.
- Cart & checkout: Default/Simple/Focused presentation, optional classic sticky totals and debounced native cart quantity updates. WooCommerce blocks retain their native behavior.
- Product: pill/underline tabs, an additional shared tab and content before/after the purchase form. Stacked galleries disable FlexSlider.

These additions require live compatibility checks with the store's payment and checkout plugins before enabling them in production. The visual workspace, product templates, Buy Now, Quick View, Portfolio, countdown, mega menu, classic archive load-more and native WooCommerce AJAX filters were added in later releases. Third-party filters still need compatibility work.

| Area | Features |
| --- | --- |
| **BIGSPY Studio** | Guided start for business/service pages and WooCommerce shops. Creates a full-width **draft** from a native BIGSPY page pattern and opens it in the editor; the live site is not published or changed until the user chooses to publish. The editor shelf includes BIGSPY patterns and published WordPress synced patterns saved by the site owner. |
| **Header builder** | One-click online store, service and minimal starter layouts, then drag & drop 16 elements into 3 rows (Top bar / Main / Bottom) × 3 zones (left / center / right), **separately for desktop and mobile**. Logo, main menu, top bar menu, off-canvas menu icon, search (dropdown / lightbox), live search scope/count/delay, optional SKU/tag matching, search form, account, cart (mini cart), 2 buttons, 3 HTML blocks, contact info, social icons, divider. Sticky header, transparent header (per page or homepage), full width, height and colors per row. Direct links to Site Identity and Menus. |
| **Style** | Brand colors (primary, secondary, success, alert, text, heading, links…), 20+ Google fonts plus system fonts, H1–H4 sizes, menu font, button style. Colors sync to the block editor palette. Live preview without reloading. |
| **Layout** | Full width / boxed, container width, left / right / no sidebar for blog, pages, posts and shop. Full-width title bar with background image and breadcrumbs. |
| **Per-page options** | Header: default / solid / transparent / hidden · hide title · hide footer · sidebar · remove top/bottom spacing. **Full width** and **Blank canvas** (landing page) templates. |
| **Layout blocks** | Section, Row (12-column grid, one-click presets), Column (width per desktop / tablet / mobile), Banner, Slider, Icon Box, Section Title, Tabs, Accordion / FAQ, Blog Posts (grid / list / slider), Testimonial, Counter, Google Map – plus Products and Product Categories when WooCommerce is active. |
| **Every block** | Hide per device (desktop / tablet / mobile); scroll animations (fade, zoom, flip, soft focus, gentle rotate and soft bounce) with per-block duration and delay plus an editor preview button; and optional hover motion (lift, zoom, shadow or grayscale-to-color). Motion honors device preferences. |
| **Patterns** | Service sites: hero, services, about, stats, testimonials, pricing, FAQ, news, call to action, contact, plus a complete **business homepage**. Shops (WooCommerce): banner slider, benefits bar, categories, featured products, sale slider, product tabs, plus a complete **online shop homepage**. |
| **Blog** | Grid / list, columns, card style (shadow / bordered / plain / text over image), image ratio, share buttons, author box, related posts, previous / next links. |
| **Footer** | One-click store, service and minimal layouts; two widget rows (0–4 columns), copyright bar with `[year]` and `[site_title]` tokens, footer menu, social icons, back-to-top button. Layout presets preserve widget content, links, copy and colors. Direct link to Widgets. |
| **Floating contact buttons** | Call, Zalo, Messenger, WhatsApp, email – round buttons or a bottom bar on mobile. |
| **WooCommerce** | Products per row on desktop / tablet / mobile, shop sidebar and mobile filter drawer, optional AJAX filtering for native category/attribute/rating/price/sort widgets with URL history and fallback, product card style, second-image or zoom hover, **-%** sale badge, AJAX header cart count, mini cart, **catalog mode** (hide prices, show a “Contact for price” button), single-product stacked/left-thumbnail gallery, sticky summary, optional mobile quick-purchase bar, and optional label/color/image variation swatches for global attributes. |
| **Tools** | **Export / import / reset** all theme options as JSON – configure once, reuse on the next website. Head / body / footer scripts (Analytics, Pixel…), disable emoji. |

## Installation

1. Run `build.ps1` (or take the file from `releases/`) to get `bigspy-ai-theme-wp-x.y.z.zip`.
2. WordPress Admin → Appearance → Themes → Add New → Upload Theme → choose the ZIP → Activate.
3. Open **Appearance → BIGSPY AI Theme WP** for the quick start guide and links to the Customizer.

## Building a new website

1. **Customizer → Site Identity**: logo and site icon.
2. **Style**: brand colors and fonts.
3. **Header → Header builder**: drag & drop the layout; click the ⚙ icon on an element to open its settings.
4. **Appearance → Menus**: assign menus to “Main menu”, “Top bar menu”, “Mobile menu” and “Footer bottom menu”.
5. Create the homepage → choose the **Full width** template → insert **Patterns → BIGSPY – Full pages → Homepage – business** (or **Homepage – online shop**) → edit the content. In the “BIGSPY – Page layout” box set Header = *Transparent* for a header over the hero.
6. **Settings → Reading**: use a static page as the homepage.
7. **Appearance → BIGSPY AI Theme WP → Export** to save the configuration as a starting point for the next project.

## Development

## Release 1.12.0: flexible live search

Search settings now choose products only, pages/posts only, or mixed results; adjust suggestion count and delay; and optionally include product SKU or tag matches. Product-only and content-only choices also apply to the full search results page. Mixed WooCommerce searches query products separately from pages/posts so product visibility rules do not discard regular content. Requests are debounced per search form, and stale responses are ignored.

## Release 1.11.0: quicker header and footer setup

Customizer now offers online store, service and minimal header starting layouts that apply to both desktop and mobile. They change only element placement; the existing drag-and-drop builder remains available. Footer starting layouts set column counts and display toggles while preserving widget contents, links, copy and colors. Direct links open Site Identity, Menus and Widgets, and section guidance explains the steps before publishing.

## Release 1.10.0: mobile shop filter drawer

When a configured shop sidebar contains widgets, shop and category pages show a **Filters** button on small screens. It opens the same WordPress widget area in an accessible drawer with a scrim, close button and Escape-key support. Existing WooCommerce filter widgets keep their native form behavior; no AJAX/PJAX navigation or filter plugin assumptions are introduced.

## Release 1.9.0: better single-product shopping flow

WooCommerce product-page controls now include stacked gallery images, thumbnails on the left on desktop, an optional sticky purchase summary, and a mobile quick-purchase bar. The bar reuses the native WooCommerce add-to-cart form, asks shoppers to choose a variation when needed, hides while the form is visible, and is disabled by default. Settings are in **Customize → WooCommerce → BIGSPY: product cards & page**.

- No build step: editor scripts use `wp.element.createElement`, styles are plain CSS driven by CSS custom properties.
- Options are declared in one place: defaults in `inc/helpers.php` (`bst_defaults()`), Customizer fields in `inc/customizer/fields.php`, CSS variables in `inc/dynamic-css.php`.
- Add a header element: filters `bst_header_elements` and `bst_header_element_{key}`.
- Prefixes: PHP functions `bst_`, CSS classes `bs-`, blocks `bst/…`, text domain `bigspy-ai-theme-wp`.
- Breakpoints: mobile ≤ 549px · tablet 550–849px · desktop ≥ 850px.
- Strings are translation-ready. The CSS library uses Vietnamese task names; existing controls use English source strings.

## Packaging

```
powershell -ExecutionPolicy Bypass -File build.ps1
```

Creates `releases/bigspy-ai-theme-wp-<version>.zip` with a single top-level `bigspy-ai-theme-wp/` folder (required so WordPress updates the existing theme instead of installing a duplicate).
After a successful build, older versioned theme ZIPs in `releases/` are removed; Git history retains prior versions.

## Release 1.4.0: BIGSPY Studio guided page starters and pattern shelf

**Appearance → BIGSPY Studio** begins with a clear choice: create a business/service homepage, create a shop homepage when WooCommerce is active, or build from scratch. Starter pages are created as unpublished drafts using BIGSPY's own registered patterns, then opened in the WordPress editor.

The post editor adds a **BIGSPY Studio** sidebar with searchable visual cards grouped into full pages, sections and shop designs. **Add to page** parses and inserts the selected BIGSPY pattern into the current document. A page remains a draft until the editor explicitly saves or publishes it. The flow uses the WordPress block-pattern registry and does not load or depend on Flatsome code, assets, or UX Builder.

This is the first step toward a more direct editing workflow. Existing WordPress/Gutenberg content and native BIGSPY blocks remain compatible.

## Release 1.5.0: reuse your own saved designs

BIGSPY Studio adds a **Saved designs** group next to its built-in page, section and shop patterns. It lists published synced patterns (`wp_block`) from the current WordPress site. Choose **Add to page** to insert a native synced block; later edits to that saved source are reflected in its instances. Create saved designs with WordPress’s **Create pattern** command from a block’s options. BIGSPY only uses WordPress’s public block editor/core-data APIs here; it does not use Flatsome source code or assets.

## Release 1.6.0: more expressive motion controls

The shared **Visibility & animation** panel adds three restrained scroll effects: **Soft focus** (blur clears as the block appears), **Gentle rotate** and **Soft bounce**. Every selected effect now has a duration control in addition to its existing delay. The animations still wait for the element to enter the viewport, run once, and respect the visitor’s system reduced-motion preference; no animation library or Flatsome script is loaded.

## Release 1.7.0: reusable hover motion

The same shared block panel now offers **Lift gently**, **Soft zoom**, **Soft shadow** and **Grayscale to color**. These can be applied to BIGSPY, WordPress and WooCommerce blocks and preview on hover in the editor. They run only where hover is available and system motion is allowed, and keyboard focus receives the same visual cue where applicable.

## Release 1.8.0: preview scroll motion before publishing

Each block’s **Visibility & animation** panel has a **Preview animation** button. It replays the selected effect in the editor using the saved duration and delay, then clears its temporary classes and styles. The preview does not change block settings, save the page or publish it.

## Release 1.3.0: CSS library for service sites and shops

**Appearance → Customize → Additional CSS → BIGSPY · Thư viện CSS**, directly above the code editor. A quick link is also available on the BIGSPY AI Theme WP dashboard.

- 64 opt-in recipes in 10 collapsible groups; each row shows a checkbox and a task name, with no code editor for the recipe.
- Check/uncheck to preview immediately, then use WordPress **Publish** to save. Leaving without publishing does not save the changed selection.
- Search accepts Vietnamese with or without accents. Use **Chỉ đang bật** to inspect selected recipes, or **Tắt tất cả / Hoàn tác** to clear and restore the selection.
- Different button shapes and image fits are mutually exclusive. Choosing a new variant clears the previous variant, including on sanitized imports.
- Every recipe defaults to off. Only selected CSS is printed on the front end; preview recipe metadata is loaded only inside Customizer.
- Colors inherit the theme configuration. The library applies to BIGSPY components, core WordPress blocks and the explicitly supported WooCommerce markup. It cannot style every third-party builder or form plugin.
- Existing Additional CSS is preserved and follows library CSS. Normal cascade rules still apply: a more specific selector, inline style or `!important` in existing CSS can override a recipe.
- Export/import/reset includes the selection. Stored/imported values are allowlisted IDs, never uploaded executable CSS or arbitrary selectors.

Full Vietnamese task catalog and usage: [docs/css-library.md](docs/css-library.md).

Developer extension: add an entry to `bst_css_presets()` in `inc/css-presets.php` using `ID => array(group, translated label, fixed CSS, exclusive family or empty string, translated scope hint)`. Keep IDs stable for saved configurations. PHP and JavaScript output CSS in the same catalog order. Avoid hiding purchase disclosures, changing review content or overriding interaction state with CSS.

Implementation follows WordPress's [Customizer API](https://developer.wordpress.org/themes/classic-themes/customize-api/customizer-objects/) and [inline style API](https://developer.wordpress.org/reference/functions/wp_add_inline_style/).

## Release 1.2.5: product reviews and customer forms

Native WooCommerce reviews now use responsive cards, avatar spacing, themed colors and visible keyboard focus. Desktop places reviews beside the form; tablet/mobile stacks the panels. Login, registration and edit-account forms use consistent cards. Primary navigation spacing compacts between 850px and 1199px while preserving wrapping for longer menus.

These styles inherit Customizer colors and button settings. Logos, favicon, banners, menu items and marketing content remain per-site WordPress configuration. Older Additional CSS may override the theme styles; remove only the duplicated review/account rules after reviewing the updated theme on your website.

For customer-profile fields, uploaded avatars, photo reviews, masked names, moderation, store replies and hearts, install the separate [BigSpy Customer Accounts & Product Reviews](https://github.com/bentarofficial/BigSpy-Customer-Accounts-Product-Reviews) plugin. The theme controls presentation and does not register those account/review behaviors.

## 1.15.0 — BIGSPY AI visual workspace
Version 1.15.0 introduced a separate visual workspace, which was removed in 1.18.0 in favor of BIGSPY blocks in the native WordPress page editor. Optional page-based header/footer/product templates, text variation buttons, Buy Now, Quick View, native classic shop filters/load more, fixed-date countdown and portfolio are included. See [Vietnamese workflow and limitations](docs/visual-builder-vi.md). This release does not claim full UX Builder parity.
