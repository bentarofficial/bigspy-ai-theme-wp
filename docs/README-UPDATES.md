# BIGSPY AI Theme WP — Updates

[← Overview](../README.md) · [Theme](README-THEME.md) · [AI Designer](README-AI-DESIGNER.md)

## Updates

Download the installable ZIP from [GitHub Releases](https://github.com/bentarofficial/bigspy-ai-theme-wp/releases/latest). The entries below summarize major changes; see individual releases for their assets and notes.

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

