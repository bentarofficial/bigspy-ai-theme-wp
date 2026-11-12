# BIGSPY AI Theme WP — Updates

[← Overview](../README.md) · [Theme](README-THEME.md) · [AI Designer](README-AI-DESIGNER.md)

## Updates

Download the installable ZIP from [GitHub Releases](https://github.com/bentarofficial/bigspy-ai-theme-wp/releases/latest). The entries below summarize major changes; see individual releases for their assets and notes.

### 1.93.0: Editor-matching block spacing, Banner fit on phones, richer Map, Popup and Nav menu

- Published pages keep the editor's block spacing; Banners on phones grow to fit their content.
- Map loads on click and sets height per device; Popup gains new triggers, rules and positions; Nav menu adds accordions and a mobile drawer.

### 1.92.0: Listing sidebar, disclosure switch, richer Social, Share and Payment icons

- Listing pages get an editable sidebar: widgets or the new Listing sidebar block, with the default kept when empty.
- The listing disclosure line can be shown in full, sponsorship only, or hidden.
- Social, Share and Payment icons gain new networks, layouts, floating bars, NAPAS and custom logos.

### 1.91.0: Search block, richer blocks and empty term cleanup

- A new Search block with eight styles and live suggestions, shared with Listing search.
- Tabs, Accordion, Table, Timeline, Icon List and the basic blocks gain many new styles, with Ready styles picked by picture.
- Auto detect finds empty categories and tags, deletes the ones you select and can restore them.
- Customizer quick-edit labels, developer hooks, optional AI abilities (MCP), better keyboard focus, Flatsome compatibility as a feature switch.

### 1.90.0: Faster hero images and Sponsored rotation lists

- The first large image on each page loads at once with high priority and is preloaded; every later image is lazy-loaded, with no setting needed.
- Sponsored cards rotate through a list of their own: each reload shows the next sponsors until the whole list has been shown.
- Cleaner editor and admin: no upload box under Banner text, no plugin notices on the theme's pages.

### 1.89.0: Form price quotes

- The built-in Form shows a live price before sending, from formulas and per-option prices; the server recalculates it and saves the quote with the submission.
- Fields keep stable IDs and can be shown or required depending on other answers.
- AI Designer builds and edits forms and price rules with a dry run, a diff and undo.

### 1.88.0: Sponsored block

- New Sponsored block with columns, label, description and full button and card styling; the whole card is the partner link and buttons are soft outlines by default.
- Rotating "Sponsored by" badge for heroes; Sponsored rows in Listing elements and Listing grid share the same options.
- Listing elements ranks Popular by clicks or Site Insights traffic.
- Heading colors survive Banner and Section text schemes; Listing search button colors, border, radius and height.
- Listing pages fit phone screens and can show the author; Divider gets a Fade line.

### 1.87.0: Animated code backgrounds

- Section and Banner can use a background drawn with code instead of an image or video: nine animated presets or your own scoped CSS and animated SVG, with palette colors, speed, density, opacity and a mobile switch.
- Listing elements gets the old BIGSPY AI List options back: View more or Load more, slider, blog layouts and category grid styles.
- Icon List edits each item's link from the toolbar.
- Breadcrumb fallback when the SEO plugin breadcrumb is off, Sponsored card and category directory shortcodes, Key features cards.
- Faster live search, Google-only Map embeds, self-hosted Lottie player, updater shows WordPress and PHP requirements.

### 1.86.0: Listing cards that fit anywhere

- Listing and Sponsored cards choose their layout from their own width: horizontal with the logo beside the text when there is room, one column in narrow spots.
- The old theme's faint logo watermark, tags, title and description line counts, and switches for logo, rating, Featured badge and Visit button.
- Sponsored can sit between rows, after every N rows, or above the grid.
- Listing blocks look the same in the editor as on the site.

### 1.85.3: Sponsored and Featured rotate again

- The homepage "Sponsored by" brand moves to the next Sponsored listing on every visit.
- Featured on the Categories page changes order on every load.

### 1.85.2: Banner hero right under the header

- A page that starts with a full-width block, such as a Banner hero, and shows no title no longer has a gap under the header.

### 1.85.1: Fix and Banner heroes

- **Fix:** the admin area showed a critical error in 1.85.0.
- **Heroes use the Banner block:** the theme no longer adds its own homepage or page heroes. The `[bigspy_home_destination_buttons]` shortcode and counters such as `{{listing_count}}` work inside a Banner.

### 1.85.0: Switch from BIGSPY AI List without redesigning

- **List mode on by itself** when the site comes from the old theme.
- **Same look:** homepage hero, page heroes with a Sponsored tool, Categories directory, listings search toolbar and a directory layout for listing pages, read from the old theme's settings. Each part has a switch in Features → BIGSPY AI List.

### 1.84.0: Listing page sections from the old theme

- **FAQ & FAQ Schema box:** edit the listing FAQ, see its schema status and JSON-LD. Listings written by the Generator keep their FAQPage schema from the description.
- **Page sections:** table of contents and dividers, Key features, Pros & Cons, Integrations, Related resources and last updated date, each switchable in Features → BIGSPY AI List.

### 1.83.2: List mode switch in Settings

- **Easier to find:** BIGSPY AI Theme → Settings has a BIGSPY AI List mode switch, the same setting as on the Features page.

### 1.83.1: AI Designer block choice

- **BIGSPY AI List aware:** with the mode off the AI designs with theme blocks only; with it on it also uses the listing blocks. Display-only legacy blocks are never used.
- **Richer pages:** every block profile is complete, and the page outline reports how many different blocks a page uses, with advice before you approve it.

### 1.83.0: BIGSPY AI List mode

- **Directory mode:** turn on BIGSPY AI List in Features (off by default) and WooCommerce products become listings with listing fields (ACF optional), FAQ and structured data.
- **Sponsored:** one-click AD column in Products, rotating Sponsored slots and the `[bigspy_sponsored_grid]` shortcode.
- **Blocks and pages:** listing, category, search and elements blocks plus the old shortcodes, listing cards on shop pages, Related listings and directory patterns. Legacy BIGSPY AI List blocks keep rendering on old content.
- **Homepage sidebar:** Customizer → Layout → Homepage sidebar shows a chosen range of homepage blocks beside a widget area.
- **AI Designer:** plugins such as BIGSPY AI List Suite can add read or content tools without widening permissions.

### 1.82.0: Theme Showcase block

- **Theme Showcase:** a new block that introduces the theme with its live version, block count, features and the latest updates, read from the installed theme so it stays current after each update. Its button points to the Telegram group by default.
- **Hero images:** Banner and Image have a Load priority option; Above the fold loads the image first with fetchpriority=high.
- **WooCommerce:** the free-shipping bar follows every Free shipping rule, including coupons and ignoring discounts.

### 1.81.0: Faster, smarter AI Designer

- **Building:** block profiles tell the AI when each block fits; 42 ready section recipes come with motion. You approve the page outline once, then the AI builds up to 8 sections per call.
- **Illustrations:** SVG/HTML/CSS in Code blocks when you did not ask for images, using theme colors and no JavaScript. Code blocks: 8 per page and 6 KB each without approval, 12 blocks and 40 KB at most; they no longer lower the editable share, but a page never has more Code blocks than other blocks.
- **Chat bar:** pick a specialty (Auto, Designer, Images, Writing, SEO) and a model per message; an optional fast model handles Effort Low. Attach Media files, or drop and paste images, videos and text documents.
- **Context:** Guidelines and specialty contexts save as you type. Say something applies from now on and the AI saves it, with an undo button in the chat.
- **Measurements:** each run records time, model and tool calls, tokens and cost.

### 1.79.1: Mobile layout fixes

- Improved Horizontal Scroll card sizing and mouse drag on mobile.
- Added spacing below shape dividers on mobile.

### 1.79.0: Motion controls and layout fixes

- Horizontal Scroll gained previous/next buttons and position dots.
- Infinity Flow can keep moving with reduced motion enabled and pauses on hover or hold.
- Flip Box fills flex columns correctly.

### 1.78.1: Sticky sections and Infinity Flow

- Fixed sticky motion blocks inside Section and an Infinity Flow scrollbar issue.

### 1.78.0: Fixed block rule, fuller CF7 move

- **AI Designer:** pages are always built from blocks you can edit visually, and context or prompts cannot change that. The server refuses new raw HTML, layout HTML in text blocks, and writes that leave a page under 90% editable; Code blocks are capped at 4 blocks and 10 KB. Existing content stays editable as long as a change does not make it worse.
- **Move CF7 forms:** Form blocks set to Contact Form 7 are now moved too, and redirects from Redirection for Contact Form 7 are carried over. Conditions, selected-field passing and mail tags in a redirect URL are listed under Not carried over.

### 1.77.2: SMTP password box

- **Features page:** a saved SMTP password shows as `********` instead of an empty box.

### 1.77.1: Form migration fixes

- **Move CF7 forms:** keeps default options and fills in `[wpcf7.*]` mail tags.
- **About page:** new support links and a GitHub star button.
- **AI Designer:** preview switch in the header and a draggable preview width.

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

