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

AI Designer is an AI agent built into the theme. You work with it the way you work with coding agents such as Codex or Claude Code: you describe the job in a natural-language prompt, discuss it, and the agent does the work with the theme's own blocks, then reports back. It appears as **BIGSPY AI Designer** at the top of the admin menu, with three screens: **Workspace**, **Guidelines** and **Settings**.

AI Designer is optional. The theme works fully without it.

### What it can and cannot do

It can:

- Design and edit pages with BIGSPY blocks and patterns: banners, sections, columns, buttons, pricing tables, FAQs, contact sections.
- Write and edit posts and WooCommerce products, with real H2/H3 headings and clean text.
- Create and edit menus, set the logo, change theme colors, fonts, header layout and additional CSS.
- Generate images with **ImageGen**, optimize them and fill in title, alt text, caption and description for SEO.
- Undo everything a task changed with **Undo task**.

It never installs, updates or deletes plugins or themes, never changes users, and never changes WordPress core settings. It discusses and proposes before it builds.

### Step 1: Connect the API

1. Create an account at [kymaapi.com](https://kymaapi.com/?aff=BIGSPY-AI-Connect), add credit and create an API key.
2. Open **BIGSPY AI Designer → Settings → 1. Connect the API**.
3. Paste the key and click **Connect**. The key is checked, encrypted and kept on the server only; it is never shown again in full. Your balance is shown once connected.
4. **Disconnect** pauses AI Designer and keeps the key. **Delete API key** removes it from the database.

### Step 2: Choose models and budget

In **Settings → 2. Models and budget**:

1. **Text model** (writes, designs and calls the tools). Models are grouped in three tiers:
   - **Recommended – strong and low cost:** start here.
   - **Most capable – higher cost:** use for important pages or when results are not good enough.
   - **All models:** everything else that supports tools.
   Prices are shown per million input / output tokens. Click **Refresh model list** to load the latest models.
2. **Image model** (used by ImageGen). The price per image is shown. Save, then click **Test ImageGen** to check that images work (10–40 seconds).
3. **Budget per task** and **Budget per day** in USD (0 = no limit). Defaults are $2 per task and $10 per day. A task stops when it reaches its budget.

### Step 3: Fill in Guidelines

Open **Guidelines**. AI Designer follows these standards in every task. There are three boxes, each with an **Insert suggested template** button:

- **Business information:** name, address, phone, opening hours, services, prices, strengths.
- **Writing standard:** tone of voice, words to use or avoid, heading style, length.
- **Image standard:** photo style, colors, people or no people, things to avoid.

Leave a box empty and the designer asks you in chat, then offers to save your answers here. It does not invent facts such as phone numbers, prices or "24/7"; if something is missing, it asks.

This is the biggest single quality improvement: a few minutes here saves many rounds of corrections.

### Step 4: Work in the Workspace

1. Click **New task** and give the task a name.
2. Choose the **Effort**:
   - **Low – quick answers:** small edits, quick questions.
   - **Medium – balanced:** most pages and posts.
   - **High – more careful:** a full page or redesign; plans more and checks its work.
   - **Max – deepest work:** large, multi-page jobs. Costs the most.
3. Describe the job in the message box (Enter to send, Shift+Enter for a new line).

#### Discuss first, then build

AI Designer works on a **discuss first, build after** principle. Before it builds anything with BIGSPY blocks, it works out the plan with you, and it starts only after you confirm. The more detailed this discussion, the better the page.

1. **Brief:** state the page goal, the target customers, the main services or products, and the action you want visitors to take (call, book, buy).
2. **Structure:** the designer proposes the sections (banner, benefits, services, pricing, testimonials, FAQ, contact). Add, remove or reorder them, or ask why a section is there.
3. **Style:** agree on colors, fonts, image style (luxury, young, minimal…), tone of voice and the main headings. You can describe a website style you like.
4. **Confirm the plan:** the designer summarizes everything. Reply to confirm, and it starts building pages, creating images and writing content.
5. **Refine:** review the result, then ask for changes to any part in the same task.

Example first prompt:

> Redesign the home page for a limousine service in Hanoi. Customers are families and business travellers. Sections: banner with a "Book now" button, why choose us, our vehicles, services, customer reviews, contact. Style: dark blue and gold, premium. Propose the structure and colors first; do not build until I confirm.

#### While it works

- The steps appear live in the chat (Reading the site, Updated page sections, ImageGen created an image…), with the task cost.
- **Keep the page open.** Work runs from your browser; leaving the page pauses the task. Open the task again and send "continue" to resume.
- Click **Stop** to stop at any time.
- Use **Preview here** to view a page without leaving the Workspace.

#### Review and undo

- Check the page before publishing.
- **Undo task** restores everything the task edited (pages, menus, header, logo, colors, CSS, settings) and moves pages, posts and products it created to the trash. Generated images stay in the Media Library.
- Each task keeps its chat history. Come back later to ask for more changes in the same task, or **Rename** / **Delete task** it. Deleting a task keeps the pages, posts and products it created.

### Settings maintenance

- **Find unused AI images** lists AI images not used anywhere so you can clean the Media Library.
- **Remove AI Designer data** deletes the API key, settings, guidelines and all task history. Pages, posts and products already created stay.

### Tips for great results

- Fill in Guidelines first.
- One clear goal per task (one page, one post, one menu).
- Spend time on the discussion before confirming; short one-line prompts give generic pages.
- Use Medium or High effort for pages; Low for small edits.
- If the results are weak, switch to a Most capable model.

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
