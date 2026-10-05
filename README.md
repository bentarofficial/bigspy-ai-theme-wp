# BIGSPY Theme

A multipurpose, fully customizable WordPress theme in the spirit of Flatsome – licensed under the GPL, so it can be used on **an unlimited number of websites**. Built for agencies that design websites for clients, both **service / business sites** and **online shops**.

> Rebuilt from scratch on the lessons of the BIGSPY AI LIST theme: all listing-specific code was removed, while the Section/Row/Column blocks, header/footer builder and Customizer ideas were kept and extended.

## Repository structure

- `bigspy-theme/` – theme source (this folder is what goes into `wp-content/themes/`).
- `build.ps1` – packages the theme as a ZIP for upload through WordPress Admin.
- `releases/` – packaged ZIP files.

## Requirements

- WordPress 6.5+ · PHP 7.4+
- No required plugins. WooCommerce and Rank Math / Yoast (breadcrumbs) are optional.

## Features

| Area | Features |
| --- | --- |
| **Header builder** | Drag & drop 16 elements into 3 rows (Top bar / Main / Bottom) × 3 zones (left / center / right), **separately for desktop and mobile**. Logo, main menu, top bar menu, off-canvas menu icon, search (dropdown / lightbox), search form, account, cart (mini cart), 2 buttons, 3 HTML blocks, contact info, social icons, divider. Sticky header, transparent header (per page or homepage), full width, height and colors per row. |
| **Style** | Brand colors (primary, secondary, success, alert, text, heading, links…), 20+ Google fonts plus system fonts, H1–H4 sizes, menu font, button style. Colors sync to the block editor palette. Live preview without reloading. |
| **Layout** | Full width / boxed, container width, left / right / no sidebar for blog, pages, posts and shop. Full-width title bar with background image and breadcrumbs. |
| **Per-page options** | Header: default / solid / transparent / hidden · hide title · hide footer · sidebar · remove top/bottom spacing. **Full width** and **Blank canvas** (landing page) templates. |
| **Layout blocks** | Section, Row (12-column grid, one-click presets), Column (width per desktop / tablet / mobile), Banner, Slider, Icon Box, Section Title, Tabs, Accordion / FAQ, Blog Posts (grid / list / slider), Testimonial, Counter, Google Map – plus Products and Product Categories when WooCommerce is active. |
| **Every block** | Hide per device (desktop / tablet / mobile) and scroll animations (fade, zoom, flip) with delay. |
| **Patterns** | Service sites: hero, services, about, stats, testimonials, pricing, FAQ, news, call to action, contact, plus a complete **business homepage**. Shops (WooCommerce): banner slider, benefits bar, categories, featured products, sale slider, product tabs, plus a complete **online shop homepage**. |
| **Blog** | Grid / list, columns, card style (shadow / bordered / plain / text over image), image ratio, share buttons, author box, related posts, previous / next links. |
| **Footer** | Two widget rows (0–4 columns), copyright bar with `[year]` and `[site_title]` tokens, footer menu, social icons, back-to-top button. |
| **Floating contact buttons** | Call, Zalo, Messenger, WhatsApp, email – round buttons or a bottom bar on mobile. |
| **WooCommerce** | Products per row on desktop / tablet / mobile, shop sidebar, product card style, second-image or zoom hover, **-%** sale badge, AJAX header cart count, mini cart, **catalog mode** (hide prices, show a “Contact for price” button). |
| **Tools** | **Export / import / reset** all theme options as JSON – configure once, reuse on the next website. Head / body / footer scripts (Analytics, Pixel…), disable emoji. |

## Installation

1. Run `build.ps1` (or take the file from `releases/`) to get `bigspy-theme-x.y.z.zip`.
2. WordPress Admin → Appearance → Themes → Add New → Upload Theme → choose the ZIP → Activate.
3. Open **Appearance → BIGSPY Theme** for the quick start guide and links to the Customizer.

## Building a new website

1. **Customizer → Site Identity**: logo and site icon.
2. **Style**: brand colors and fonts.
3. **Header → Header builder**: drag & drop the layout; click the ⚙ icon on an element to open its settings.
4. **Appearance → Menus**: assign menus to “Main menu”, “Top bar menu”, “Mobile menu” and “Footer bottom menu”.
5. Create the homepage → choose the **Full width** template → insert **Patterns → BIGSPY – Full pages → Homepage – business** (or **Homepage – online shop**) → edit the content. In the “BIGSPY – Page layout” box set Header = *Transparent* for a header over the hero.
6. **Settings → Reading**: use a static page as the homepage.
7. **Appearance → BIGSPY Theme → Export** to save the configuration as a starting point for the next project.

## Development

- No build step: editor scripts use `wp.element.createElement`, styles are plain CSS driven by CSS custom properties.
- Options are declared in one place: defaults in `inc/helpers.php` (`bst_defaults()`), Customizer fields in `inc/customizer/fields.php`, CSS variables in `inc/dynamic-css.php`.
- Add a header element: filters `bst_header_elements` and `bst_header_element_{key}`.
- Prefixes: PHP functions `bst_`, CSS classes `bs-`, blocks `bst/…`, text domain `bigspy-theme`.
- Breakpoints: mobile ≤ 549px · tablet 550–849px · desktop ≥ 850px.
- All strings are in English and translation-ready.

## Packaging

```
powershell -ExecutionPolicy Bypass -File build.ps1
```

Creates `releases/bigspy-theme-<version>.zip` with a single top-level `bigspy-theme/` folder (required so WordPress updates the existing theme instead of installing a duplicate).

## Release 1.2.5: product reviews and customer forms

Native WooCommerce reviews now use responsive cards, avatar spacing, themed colors and visible keyboard focus. Desktop places reviews beside the form; tablet/mobile stacks the panels. Login, registration and edit-account forms use consistent cards. Primary navigation spacing compacts between 850px and 1199px while preserving wrapping for longer menus.

These styles inherit Customizer colors and button settings. Logos, favicon, banners, menu items and marketing content remain per-site WordPress configuration. Older Additional CSS may override the theme styles; remove only the duplicated review/account rules after reviewing the updated theme on your website.

For customer-profile fields, uploaded avatars, photo reviews, masked names, moderation, store replies and hearts, install the separate [BigSpy Customer Accounts & Product Reviews](https://github.com/bentarofficial/BigSpy-Customer-Accounts-Product-Reviews) plugin. The theme controls presentation and does not register those account/review behaviors.
