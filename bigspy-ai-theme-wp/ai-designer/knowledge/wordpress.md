# WordPress action guide

Use overview to inspect current state, site_settings without values to read allowed
options, and get_knowledge(theme) for theme-specific workflows.

## Reading and identity
show_on_front=posts shows latest posts. For a static homepage use show_on_front=page,
page_on_front=<published public page>; page_for_posts chooses a DIFFERENT optional
blog page whose body WordPress ignores. These changes require approval. blogname is
site title, blogdescription is tagline, posts_per_page is listing size. Only these
reading/identity options and thumbnail_size_w/h,medium_size_w/h,large_size_w/h are
allowed. New image sizes affect future uploads; existing media needs manual
regeneration. Site URL, admin email and permalink settings are guidance-only.
Permalinks are configured manually in Settings > Permalinks; explain consequences
for old URLs and redirects, never change them with a tool.

## Content and media
Pages are hierarchical; posts use categories/tags. Products require WooCommerce.
Drafts are safe to prepare. Private, scheduled and published content can affect
other people and needs approval before editing. Publishing is a separate approved
action. Page parent/template are edited in the WordPress editor (page tools expose
the theme's default/full-width/blank templates). Full-width removes the template
title, so add one real H1 in content. Standard templates already supply a title H1.
Use H2/H3 for sections and never put Markdown or heading labels in final copy.
Use existing categories/tags when suitable; avoid duplicate terms. Widgets are
managed under Appearance > Widgets; use existing widget areas from the theme.

Media uploads belong to the library. Choose large images for heroes, medium/card
sizes for lists, adequate mobile source sizes. Supply title, meaningful alt (empty
only for decorative images), caption and description; avoid keyword stuffing.
Optimize unused images before inserting them. Generated scenes illustrate a service
and must not be described as verified photos of the actual business/fleet.
API charges and generated media remain after task undo; image optimization may
change files and cannot restore the original bytes, so retain source uploads.

## Navigation
Appearance > Menus stores menus independently of their display locations. A menu
must be assigned to a location AND the header/footer layout must render that location.
Check primary on desktop and mobile menu elements, not only menu creation success.

## WooCommerce and SEO
WooCommerce setup creates Shop, Cart, Checkout and My Account pages; ask the user
to use WooCommerce > Status > Tools or its setup wizard to create missing pages.
Never configure payments, checkout credentials, orders or customer records.
AI may prepare product drafts; live price/content changes and publishing need review.
seo_meta(provider=rankmath|yoast) writes per-page/post/product title and description,
with approval. Plugin activation is separate; offer Plugins > Add New instructions
first, explain the cost and ask manual vs AI. Only exact WordPress.org slugs can be
installed by AI, subject to install_plugins/activate_plugins and site policy.
Never update/remove plugins/themes/core, install external ZIPs or change users,
security, passwords, email administration, service keys, DNS or hosting.

## Review and undo
Use check_page for H1, SEO, image alt, local-link hints, media sizes and mobile-width
warnings. It analyzes markup, not computed rendering or all remote links. Confirm
the public/preview page in a browser at desktop and 375px mobile. A task stores
content/meta/terms, option/theme-mod and menu snapshots; undo moves created content
to Trash and deactivates plugins activated by the task without deleting files/data.
Keep manual_checklist for work such as Search Console verification, real photos,
contact verification, payments or long existing content the human can paste faster.
