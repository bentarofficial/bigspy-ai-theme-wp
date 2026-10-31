# BIGSPY AI Theme WP — Theme guide

[← Overview](../README.md) · [AI Designer](README-AI-DESIGNER.md) · [Updates](README-UPDATES.md)

## Requirements

- WordPress 6.5 or later, PHP 7.4 or later.
- No plugins required. WooCommerce, Rank Math and Yoast SEO are supported when installed.
- AI Designer needs a [Kyma API](https://kymaapi.com/?aff=BIGSPY-AI-Connect) key (pay as you go).

## Features

- **Block library** in the native editor: Section, Row, Column, Banner, Slider, Heading, Text, Image, Icon Box, Tabs, Accordion, Posts, Products, Testimonial, Counter, Map, Code and more, with ready-made patterns.
- **Per-device control:** sizes for desktop, tablet and mobile, responsive visibility and scroll animations on every block.
- **Header and footer builders** with drag and drop, sticky header, and page-based header, footer and product templates.
- **Global styles** in the Customizer: colors, fonts (served locally), buttons, layout; settings import and export.
- **WooCommerce:** product cards with swatches, AJAX shop filters, product page layouts; a Products block with grid, slider or compact list, image ratio and hover effects, shadow depth, infinite scroll and out-of-stock placement; a Products list block; a free-shipping progress bar and checkout coupon/note options for classic cart and checkout pages.
- **Speed:** CSS is loaded only for the blocks used on a page, generated CSS is cached, fonts are local.
- **Motion:** Animated Text, Scroll Story, Stacking Cards, Infinity Flow, Spotlight Panels, Hover Reveal List, Line Draw, Horizontal Scroll, Zoom Hero, Video Hover Cards and Bento Grid blocks; advanced motion on existing blocks; count-up numbers inside any heading or text; site-wide motion intensity and style, optional page transitions and custom cursor. Reduced-motion settings are always respected.
- **Floating contact buttons** (phone, Zalo, Messenger and others).
- **BIGSPY AI Designer** (see below).
- Theme text defaults to English. In **BIGSPY AI Theme → Settings → Theme language**, users can independently choose English, Vietnamese, or **Same as WordPress** for the admin/Customizer and visitor-facing website.

## Installation

1. Download the zip from [Releases](https://github.com/bentarofficial/bigspy-ai-theme-wp/releases/latest). Do not use GitHub's "Download ZIP" source button; that archive has the wrong folder structure.
2. In WordPress go to **Appearance → Themes → Add New → Upload Theme**, choose the zip and click **Install Now**, then **Activate**.
3. From 1.73.0 onward, WordPress checks GitHub Releases for updates. Update from **Appearance → Themes** or **Dashboard → Updates**; you can also enable automatic updates. If you use 1.72.13 or older, upload the 1.73.0 zip once and choose **Replace current with uploaded**. Your settings and content are kept.

If you plan to edit theme files, use a child theme so updates do not overwrite your changes.

From 1.73.3, **Dashboard → Updates → Check again** and **BIGSPY AI Theme → About → Check for updates** refresh the GitHub cache immediately. Automatic background checks retain a 12-hour cache. About shows the installed and latest versions and links to the standard WordPress update screen when a new version is available.

## Using the theme

Start at **BIGSPY AI Theme → About** for the setup checklist, searchable feature guides, current/latest version and update check. **BIGSPY AI Designer → About** explains the workflow, permissions, costs and undo limits. Sample prompts open a new Workspace task with text filled in; they are never sent automatically. Both About pages appear first in their menus and mark unseen major updates.

1. **Set up the brand:** **Appearance → Customize** to set the logo, colors, fonts, buttons and layout.
2. **Build the header and footer:** in the Customizer, drag elements (logo, menu, search, cart, buttons) into header rows. For a custom footer, design it as a page with blocks and select it as the footer.
3. **Build pages:** **Pages → Add New**, open the block inserter (+) and use the **BIGSPY AI Builder** category, or insert a pattern for a complete section. Use Section → Row → Column to lay out content.
4. **Responsive:** select a block and use the device tabs in the settings panel to set different sizes or hide it on mobile.
5. **Shop:** install WooCommerce, then configure product cards and the product page in **Customize → WooCommerce**.
6. **Backup:** export theme settings from the Customizer before big changes; import them on another site to reuse a design.

If the block editor does not open, the Classic Editor plugin is probably active. The theme shows a notice explaining how to switch.

