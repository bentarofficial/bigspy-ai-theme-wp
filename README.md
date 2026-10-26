# BIGSPY AI Theme WP

A multipurpose WordPress theme for building client websites fast: service and business sites as well as WooCommerce shops. It ships with a block library for the native WordPress editor, header and footer builders, global styles, and **BIGSPY AI Designer**, an AI assistant that designs pages, writes content and creates images inside your site.

**[⬇ Download the latest release](https://github.com/bentarofficial/bigspy-ai-theme-wp/releases/latest)**: get the file `bigspy-ai-theme-wp-x.y.z.zip` from the release assets.

## Requirements

- WordPress 6.5 or later, PHP 7.4 or later.
- No plugins required. WooCommerce, Rank Math and Yoast SEO are supported when installed.
- AI Designer needs a [Kyma API](https://kymaapi.com/?aff=BIGSPY-AI-Connect) key (pay as you go).

## Features

- **Block library** in the native editor: Section, Row, Column, Banner, Slider, Heading, Text, Image, Icon Box, Tabs, Accordion, Posts, Products, Testimonial, Counter, Map, Code and more, with ready-made patterns.
- **Per-device control:** sizes for desktop, tablet and mobile, responsive visibility and scroll animations on every block.
- **Header and footer builders** with drag and drop, sticky header, and page-based header, footer and product templates.
- **Global styles** in the Customizer: colors, fonts (served locally), buttons, layout; settings import and export.
- **WooCommerce:** product cards with swatches, AJAX shop filters, product page layouts.
- **Speed:** CSS is loaded only for the blocks used on a page, generated CSS is cached, fonts are local.
- **Floating contact buttons** (phone, Zalo, Messenger and others).
- **BIGSPY AI Designer** (see below).
- Interface in English and Vietnamese.

## Installation

1. Download the zip from [Releases](https://github.com/bentarofficial/bigspy-ai-theme-wp/releases/latest). Do not use GitHub's "Download ZIP" source button; that archive has the wrong folder structure.
2. In WordPress go to **Appearance → Themes → Add New → Upload Theme**, choose the zip and click **Install Now**, then **Activate**.
3. To update, upload the new zip the same way and choose **Replace current with uploaded**. Your settings and content are kept.

If you plan to edit theme files, use a child theme so updates do not overwrite your changes.

## Using the theme

1. **Set up the brand:** **Appearance → Customize** to set the logo, colors, fonts, buttons and layout.
2. **Build the header and footer:** in the Customizer, drag elements (logo, menu, search, cart, buttons) into header rows. For a custom footer, design it as a page with blocks and select it as the footer.
3. **Build pages:** **Pages → Add New**, open the block inserter (+) and use the **BIGSPY AI Builder** category, or insert a pattern for a complete section. Use Section → Row → Column to lay out content.
4. **Responsive:** select a block and use the device tabs in the settings panel to set different sizes or hide it on mobile.
5. **Shop:** install WooCommerce, then configure product cards and the product page in **Customize → WooCommerce**.
6. **Backup:** export theme settings from the Customizer before big changes; import them on another site to reuse a design.

If the block editor does not open, the Classic Editor plugin is probably active. The theme shows a notice explaining how to switch.

## BIGSPY AI Designer

AI Designer is a design assistant built into the theme. It appears as **BIGSPY AI Designer** at the top of the admin menu and has three screens: **Workspace**, **Guidelines** and **Settings**.

### What it can do

- Design and edit pages with the theme's blocks: banners, sections, columns, buttons and so on.
- Write and edit posts and WooCommerce products.
- Create and edit menus, and change the logo, colors, fonts and custom CSS.
- Generate images with **ImageGen**, optimize them, and fill in title, alt text, caption and description for SEO.
- Undo everything a task changed with one click.

It never installs, updates or deletes plugins and themes, never changes users or WordPress core settings, and asks before large changes.

### Connect

1. Create an account at [kymaapi.com](https://kymaapi.com/?aff=BIGSPY-AI-Connect) and add credit.
2. Create an API key.
3. Open **BIGSPY AI Designer → Settings**, paste the key and click **Connect**. The key is stored encrypted in your database. You can disconnect or delete it at any time.

### Choose models

In **Settings** you pick a text model and an image model. Text models are grouped in three tiers:

- **Recommended:** strong models at low cost. Start here.
- **Most capable:** the best results, at a higher price.
- **All models:** everything else that supports tools.

Prices are shown per million tokens (text) and per image. You can also set a spending limit per task.

### Guidelines

In **Guidelines**, describe your business: name, services, prices, tone of voice, colors, words to avoid. AI Designer reads these before every task, so it uses your real information instead of guessing. Facts not in the guidelines (opening hours, phone numbers, "24/7") are not invented; it asks you instead.

### Workspace

1. Click **New task** and describe what you want, for example: "Redesign the home page for a limousine service: banner with a booking button, why choose us, our vehicles, contact".
2. Choose an **Effort** level. Low is quick and cheap; high plans more and checks its work.
3. AI Designer proposes a plan first. Reply to adjust it or confirm to start.
4. Keep the page open while it works. Leaving the page pauses the task; open it again to continue.
5. Review the result before publishing. Click **Undo** on the task to revert all its changes.

Each task keeps its chat history, so you can return later and ask for changes.

### Tips

- Fill in Guidelines first; it is the biggest quality improvement.
- Give one clear goal per task.
- If results are weak, switch to a Most capable model for that task.

## Updates

Only the latest release is kept on the [Releases](https://github.com/bentarofficial/bigspy-ai-theme-wp/releases) page. Major changes are listed below; small fixes are not.

### 1.72: BIGSPY AI Designer

- New AI assistant inside the theme: Workspace with tasks, Guidelines and Settings.
- Kyma API connection, three model tiers, Effort levels, ImageGen images, one-click undo.

### 1.70: Speed

- Per-block CSS loading, local fonts, cached generated CSS.
- Color code input for text and headings.

## License

The theme code is licensed under the [GNU General Public License v2 or later](LICENSE), as required for WordPress themes.

The **BIGSPY** name, logos and branding are not covered by the GPL. See [TRADEMARKS.md](TRADEMARKS.md) for what is and is not allowed.

© Bentar. Support and issues: [GitHub Issues](https://github.com/bentarofficial/bigspy-ai-theme-wp/issues).
