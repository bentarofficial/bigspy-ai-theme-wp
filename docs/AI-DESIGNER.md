# AI Designer 1.73

AI Designer is an optional module in `bigspy-ai-theme-wp/ai-designer/`.
Remove the folder to remove the integration. No production theme files are edited
by the agent. Generated content is stored through WordPress APIs.

## Discovery and design

`site_info` returns site facts and guidelines. `overview` discovers the registered
block/pattern and Customizer registries, menus with location assignments, UX Blocks,
Theme Builder layouts, header configuration, footer sources, reading settings and
active plugin names. `get_knowledge(theme|wordpress)` loads concise guides only when
needed. New block schemas are available through `block_schema` automatically.

`ux_block` and `theme_layout` support list/get/create/update. New reusable content
starts in draft; `publish` requests approval separately. Layout metadata follows
the existing `_bst_tb_*` rules, exclusions, IDs/categories, device/login and priority
settings. `set_footer` validates a public published UX Block/page and clears the
other source, or restores the widget footer. Copyright remains separate.

`site_settings` only allows `show_on_front`, `page_on_front`, `page_for_posts`,
`blogname`, `blogdescription`, `posts_per_page`, and thumbnail/medium/large width
and height options. Validation finishes before any option write. Front and posts
pages must be different, public published pages.

`get_menus` returns menus, items and all registered locations/assignments. A menu
also needs a matching header element. Check both desktop and mobile header layout.

## Permission levels

Every registered tool declares policy metadata. The dispatcher recalculates the
policy against current arguments/state before calling the tool. Unknown tools
are refused. Model-provided `confirmed` is ignored by policy; legacy callbacks
receive it only after the server authorizes the invocation.

1. Reads, draft content, image work and markup checks. Advice-only task mode blocks
   all AI write tools, including guidelines and checklist changes.
2. Public/non-draft content, publication, global design, menus, header/footer,
   whitelisted WordPress settings, SEO, plugin installation, fourth/subsequent
   ImageGen requests in a run and budget extensions require human approval.
3. Guidance only for users/passwords/admin email/site URLs/permalinks/security;
   updating/removing plugins/themes/core; installing themes or external ZIPs;
   payments/keys/DNS/hosting and permanent content deletion. No such tool exists.
   AI-authored Code markup strips scripts and event handlers for administrators too.

Settings can strengthen each tool group's policy to approval or guidance-only;
they cannot reduce a mandatory approval. WordPress capabilities remain mandatory.
The module never raises global budgets. Budget exhaustion stops the model loop and
offers an explicit extra-$1 approval; `budget_extension` can also request exact
additional USD (max $100 per allowance). Extra daily allowance only applies to that
task on the current UTC spend day. A single provider response can exceed an estimate;
provider prices/charges are not a guaranteed spending cap.

## Approval lifecycle

An approval record is server-generated, bound to task ID, tool, SHA-256 of the exact
arguments, requesting user and a 15-minute expiry. It is never a model argument.
The workspace shows scope, cost information, undo limitations and before/after data,
plus a content preview link when available. Choose Agree / I will do it myself /
Skip. Manual mode gives menu-by-menu instructions; skip does not call the tool.

The POST approval endpoint verifies ownership and current capabilities, atomically
consumes the record before execution and issues a single in-process grant. Replays
and expired records are rejected. Model steps, approval execution and undo share an
atomic database lease; concurrent requests cannot execute the same approval twice.
Stop revokes pending cards and every later tool write checks current run state.
An already executing synchronous mutation can finish; Stop prevents later calls.
After reviewing cards, send `continue` to resume the remaining work. Task logs record
tool, argument hash, actor and timestamps; approvals record approver and result.

`install_plugin` requires install_plugins and activate_plugins, rejects multisite
and disabled file modifications, accepts only a strict WordPress.org slug and verifies
the directory API's HTTPS downloads.wordpress.org package. Existing plugins are
never updated. Task undo deactivates a plugin activated by that task; installed files
and plugin data remain. Routine installation should first offer manual instructions.

## Undo and checks

Snapshots preserve original posts, status, metadata and taxonomy assignments,
theme mods, options, menus, CSS and header/footer. Created content is moved to Trash.
Generated media and API charges remain. Image optimization restores metadata but
cannot restore original file bytes; retain source uploads. Undo is blocked until
the active model step finishes. Review the page after undo if others also edited it.

`check_page` reports H1 count including the template title, SEO fields, missing alt,
placeholder links/missing local query-ID targets, large media and fixed-width hints.
It does not fetch every external URL or emulate computed mobile rendering. Test the
preview on desktop and a 375px viewport. `manual_checklist` saves human tasks and
the workspace allows marking items done.

## Motion blocks

- `bigspy/ticker` > `bigspy/ticker-slide`: unrestricted child content, responsive
  auto/px/% width, px/sec speed, left/right, seamless loop/once, hover/touch pause,
  edge fading. The editor is stationary with a sandboxed runtime preview. Frontend
  measures one full sequence including its trailing gap and creates enough inert,
  aria-hidden copies without duplicate IDs to cover the viewport throughout travel.
  ResizeObserver, image/font readiness and visibility synchronize motion; reduced
  motion becomes manual horizontal scrolling.
- `bigspy/expand-gallery` > `bigspy/expand-panel`: 2–6 panels, flex expansion factor,
  initial/retained state, responsive heights, radius/gap, background/focal point and
  color/gradient overlay, optional vertical collapsed titles. Mobile defaults to
  75% scroll-snap panels, first aligned left and last aligned right, with optional
  position dots. Alternatives are vertical accordion or horizontal panels. Focus,
  Enter/Space and arrow keys work; reduced motion disables transitions. DOM order
  remains unchanged. Panel secondary content is inert while collapsed.

Both use existing CSS-family splitting and per-block view scripts, with six sample
patterns. New block names intentionally use the `bigspy/` namespace specified for
1.73; the existing theme blocks retain `bst/`.

## Verification

`tools/playground-check.cjs --serve` boots Playground CLI 3.1.60 and the cached
WordPress archive, runs `tools/tests/designer-integration.php`, and serves localhost
for `tools/tests/browser-check.cjs`. Module/runtime paths can be supplied through
BST_PLAYGROUND_MODULES / BST_PLAYWRIGHT / BST_CHROME. Local verification never uses
a real Kyma key or incurs provider charges. PHP lint uses php-parser when PHP CLI
is unavailable. See `docs/VERIFICATION-1.73.md` for the measured results and limits.
## Product documentation and release gate

Follow the owner-approved development/release policy in `docs/roadmap-vi.md`. A request to update code is not a request to release it. Implement and verify the agreed changes, save/commit them, and record a short entry in `docs/PENDING-RELEASE.md`. Defer About catalogs, What is new, README Updates, changelog and release-note maintenance until the owner approves packaging and release, unless that content is itself the requested change or is required for correct runtime behavior. Ask once at the release boundary if permission has not already been given; no answer means no release. Do not repeat the question after the owner chooses to accumulate more changes.

Only during an approved release, update `inc/welcome/data.php`, `ai-designer/welcome/data.php`, their What is new entries, README Updates, and relevant AI knowledge together. Then update the version, package, tag and publish the consolidated release. Deleting old releases/tags and deploying to a website need the owner's applicable authorization. The `get_knowledge(theme)` tool includes the same catalogs rendered by About. Keep English source strings, update `languages/vi.po`, then compile with `python tools/compile-po.py vi` when the release content requires it. Only shipped capabilities may be described as available; agreed future features belong in Coming soon.

About sample prompts prefill a new Workspace task without sending it. About pages require `edit_theme_options`, use local assets, and display only cached AI balance information; opening them does not make paid provider calls. Users with `edit_pages` can continue using Workspace without receiving access to the About/settings pages.
