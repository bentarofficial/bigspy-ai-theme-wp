# BIGSPY AI Theme WP

> **Coding agents — read first:** [Shared agent instructions](AGENTS.md) and the [owner-approved development/release policy](docs/roadmap-vi.md#quy-tắc-cập-nhật-và-phát-hành--owner-chốt-ngày-28-10-2026). Develop continuously and record pending changes; package/publish only after owner approval. Claude Code and Gemini entry points are [CLAUDE.md](CLAUDE.md) and [GEMINI.md](GEMINI.md).

![BIGSPY AI Theme WP: build websites your way, with the built-in AI Designer](docs/images/bigspy-ai-theme-wp-cover.jpg)

A multipurpose WordPress theme for building client websites fast: service and business sites as well as WooCommerce shops. It ships with a block library for the native WordPress editor, header and footer builders, global styles, and **BIGSPY AI Designer**, an AI assistant that designs pages, writes content and creates images inside your site.

> **Live demo:** the website you see at [phonglammedia.com](https://phonglammedia.com/) is built entirely with BIGSPY AI Theme WP, and its pages were designed with the theme's built-in BIGSPY AI Designer. Browse it on desktop and mobile to see what the theme can do. Product page: [BIGSPY AI Theme WP at Phong Lâm Media](https://phonglammedia.com/giao-dien-wordpress-bigspy-ai-theme-wp/).

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

## BIGSPY AI Designer

AI Designer is an AI agent built into the theme. You work with it the way you work with coding agents such as Codex or Claude Code: you describe the job in a natural-language prompt, discuss it, and the agent does the work with the theme's own blocks, then reports back. It appears as **BIGSPY AI Designer** at the top of the admin menu, with **About**, **Workspace**, **Guidelines** and **Settings**.

AI Designer is optional. The theme works fully without it.

### What it can and cannot do

It can:

- Design and edit pages with BIGSPY AI blocks and patterns: banners, sections, columns, buttons, pricing tables, FAQs, contact sections.
- Write and edit posts and WooCommerce products, with real H2/H3 headings and clean text.
- Create and edit menus, set the logo, change theme colors, fonts, header layout and additional CSS.
- Build reusable UX Blocks and Theme Builder layouts, assign published footer sources, configure whitelisted reading settings and request SEO/plugin approvals.
- Generate images with **ImageGen**, optimize them and fill in title, alt text, caption and description for SEO.
- Undo everything a task changed with **Undo task**.

AI Designer uses three server-enforced permission levels:

- **Level 1:** discovery, draft content, image work and checks; draft writes remain undoable. Advice-only tasks block every AI write.
- **Level 2:** live content, publishing, global design/header/footer/menu changes, whitelisted WordPress reading/title/media settings, SEO metadata, WordPress.org plugin installation, four or more images per run and extra budget require a human approval card. Chat consent and model-supplied `confirmed: true` cannot bypass it. Settings can strengthen permissions per group.
- **Level 3:** guidance only for users, passwords, admin email, site URLs, permalinks, security, plugin/theme/core updates or removals, external ZIPs/themes, payments, service keys, DNS/hosting and permanent data deletion. AI-authored Code blocks allow safe HTML/CSS, never scripts.

Cards show scope, cost information, before/after values and undo limits. Choose **Agree**, **I will do it myself**, or **Skip**. Approvals expire after 15 minutes, bind to the task/tool/arguments/user and are consumed once. After reviewing the pending cards, send **continue** for remaining work. Budget extensions are exact additional USD for that task; global budget settings stay unchanged.

Select **Advice only** before creating a task for analysis without AI writes. Tasks also retain a manual checklist and approval/change history. Discovery reads the live block/pattern and Customizer registries and loads concise theme/WordPress guides on demand. UX Blocks and Theme Builder layouts start as drafts and are published separately.

Undo restores saved content, metadata, terms, settings, menus and header/footer state, trashes task-created content and deactivates plugins the task activated. Installed plugin files/data, generated media and API charges remain. Image file optimization cannot restore original image bytes; retain source uploads. Markup checks identify common H1/SEO/alt/local-link/mobile-width issues; browser review is still needed.

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

AI Designer works on a **discuss first, build after** principle. Before it builds anything with BIGSPY AI blocks, it works out the plan with you, and it starts only after you confirm. The more detailed this discussion, the better the page.

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

Only the latest release is kept on the [Releases](https://github.com/bentarofficial/bigspy-ai-theme-wp/releases) page. Major changes are listed below; small fixes are not. Every release includes one installable ZIP named `bigspy-ai-theme-wp-X.Y.Z.zip`, matching tag `vX.Y.Z` and the theme version.

### 1.77.1: Form migration fixes

- **Move CF7 forms:** keeps default options and fills in `[wpcf7.*]` mail tags.
- **About page:** new support links and a GitHub star button.

### 1.77: Site tools

- **Auto detect:** report-only checks for duplicate focus keywords, H1 tags, unused media, broken links and security.
- **Features page:** WebP on upload, SMTP email, related links in posts, header and footer code. Each one is off by default and loads only when on.
- **Built-in form:** file upload field, server checks, and a Move CF7 forms page. Quiz, captcha and hidden fields are not carried over.
- **AI Designer:** builds with editable blocks instead of new Custom HTML, keeps code and CSS small, and reports the editable share of each page.

### 1.76: Motion update

- **New blocks:** Animated Text (scroll reveal, word by word, typewriter, rotating words, scramble, flowing gradient, big sliding type), Scroll Story, Stacking Cards, Hover Reveal List, Line Draw, Horizontal Scroll, Zoom Hero, Video Hover Cards, Bento Grid.
- **Advanced motion on existing blocks:** sticky sections and page background per section, grain/mesh/aurora backgrounds, frosted glass columns, magnetic/arrow/shine buttons, image curtain and Ken Burns reveals, self-drawing icons, scrambling counters, staggered galleries, slider zoom/flip/coverflow.
- **Count-up numbers:** select a number in a Heading or Text block and press "Count-up number"; the Counter block can sit inside a sentence, start from any value and choose its counting style.
- **Customizer > Performance:** motion intensity (subtle/normal/strong), motion style (elegant/dynamic/minimal), page transitions, magnetic buttons and custom cursor.
- Colored block icons by family; every block has a drawn inserter preview. Brand names now read BIGSPY AI (for example BIGSPY AI Studio).
- Fix: per-page block CSS no longer breaks embedded data: images.

### 1.75: Shop blocks, checkout options and block previews

- **Products block:** compact list layout, per-block image ratio, corner radius and hover effect (zoom, second image, fade, grayscale, lift), shadow depth and hover depth, text size, equal card heights, infinite scroll, and out-of-stock products shown normally, last or hidden.
- **Products list** block: small image, name, rating and price for sidebars and footers.
- **Classic cart and checkout:** free-shipping progress bar (threshold read from WooCommerce shipping zones or set manually), coupon field at the top, below the form or hidden, and a note before the payment methods.
- Every block has a drawn preview in the block inserter and a detailed description; block names and descriptions are translated into Vietnamese.
- Motion blocks renamed: **Infinity Flow** (was Continuous ticker) and **Spotlight Panels** (was Expanding gallery). Existing content keeps working.
- AI Designer: keeps the chosen effort, Stop takes effect at once, failed or blocked tools show as warnings, failed images are reported plainly with one automatic retry, replies stay in the user's language.

### 1.74: Product guides and onboarding

- Detailed, searchable About screens for the theme and AI Designer, using English source text and bundled Vietnamese translations.
- Five-step setup checklist, local screenshot, current/latest versions and manual update refresh.
- Editable feature catalogs shared with AI Designer knowledge, including when to use each feature, steps, troubleshooting and sample prompts.
- New-task prompt prefill without automatic sending, cached connection/model/balance status, one-time activation welcome and unseen-major-update badges.
- Planned features remain explicitly labeled Coming soon. Saved language choices remain independent of WordPress.

### 1.73: Informed AI Designer and motion blocks

- Automatic theme updates from GitHub Releases, with WordPress update notices and optional auto-updates.
- On-demand theme/WordPress guides and discovery of live blocks, patterns, design fields, menus, layouts and plugins.
- Three permission levels with server-bound approval cards, advice-only tasks, manual checklists, approval logs and expanded undo.
- **Infinity Flow**, formerly Continuous ticker (`bigspy/ticker` + `bigspy/ticker-slide`): any blocks per slide, seamless or once-only motion, px/second speed, responsive widths, accessible inert clones and reduced-motion scrolling.
- **Spotlight Panels**, formerly Expanding gallery (`bigspy/expand-gallery` + `bigspy/expand-panel`): 2–6 panels, adjustable flex expansion, background/focal point/overlay, keyboard support and mobile swipe with peek, accordion or horizontal layout.
- Six starter patterns; motion styles use the existing CSS split and scripts load on pages that render the blocks.

### 1.72: BIGSPY AI Designer

- New AI assistant inside the theme: Workspace with tasks, Guidelines and Settings.
- Kyma API connection, three model tiers, Effort levels, ImageGen images, one-click undo.

### 1.70: Speed

- Per-block CSS loading, local fonts, cached generated CSS.
- Color code input for text and headings.

## License

The theme code is licensed under the [GNU General Public License v2 or later](LICENSE), as required for WordPress themes.

The **BIGSPY AI** name, logos and branding are not covered by the GPL. See [TRADEMARKS.md](TRADEMARKS.md) for what is and is not allowed.

© Bentar. Support and issues: [GitHub Issues](https://github.com/bentarofficial/bigspy-ai-theme-wp/issues).
