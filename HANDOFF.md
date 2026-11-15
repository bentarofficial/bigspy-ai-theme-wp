# Handoff — AI Designer upgrade (15-11-2026)

Branch `ccr-089178d3-d9tjpf`. Plan: `docs/roadmap-vi.md`, section "Kế hoạch nâng cấp BIGSPY AI Designer 15-11-2026". Pending notes: `docs/PENDING-RELEASE.md`. No release, no version bump.

## Done (each committed and pushed)

- G-191 mock provider + `tools/tests/workspace-browser-check.cjs` (19 checks).
- G-207-a..d app shell, Workspace layout, Settings, Guidelines. WordPress left menu shows only the main AI Designer item (owner request).
- G-192-a..c chat components, dialogs, approval countdown, dark theme, task date groups.
- G-193-a two-phase steps (`activity`, `pending`, `bst_aid_drop_pending`) and the live status line.
- Fix: `bst/marquee-item` declared as a child of `bst/marquee` (designer-integration was stopping there).

## Next, in roadmap order

G-197 → G-194 → G-195 → G-193-b → G-196 → G-198 → G-199 → G-200 → G-201 → G-205 → G-202 → G-204 → G-203 → G-206.
G-194, G-193-b and G-201 need the Kyma API checked first (image input, `stream: true` with tools, parallel tool calls). The cloud session cannot reach kymaapi.com docs without network access: ask the owner, or skip to the next ticket and say so.

## How to test in a cloud session (no wordpress.org access)

See `docs/DEV-RULES.md` (Workspace AI Designer, and the full WordPress zip recipe). Short version:

1. `npm ci`; `npm pack @wp-playground/wordpress-builds@0.9.19`, build `wp-full.zip` from `src/wordpress/wp-nightly.zip` + `public/wp-nightly/`, serve it with `python3 -m http.server 9500`.
2. `BST_TEST_WP=http://127.0.0.1:9500/wp-full.zip node tools/playground-page.cjs --mock-ai`
3. `BST_CHROME=/opt/pw-browsers/chromium node tools/tests/workspace-browser-check.cjs`
4. `BST_TEST_WP=... node tools/playground-check.cjs` (designer-integration, 209 checks).

Edits to `ai-designer.js` with Python: indentation in that file varies; match on content, not on tab counts.
