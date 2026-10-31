# BIGSPY AI Designer — Guide

[← Overview](../README.md) · [Theme](README-THEME.md) · [Updates](README-UPDATES.md)

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

