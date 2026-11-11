# 1.74.0 verification

## Scope

- Two searchable About pages, first in their menus, with detailed data catalogs in `inc/welcome/data.php` and `ai-designer/welcome/data.php`.
- 16 theme guides and 12 AI guides, 24 sample prompts, quick-start configuration checklist, local screenshot, connection/model/cached-balance status, major-release news and per-user unread badges.
- A one-time first-activation redirect and clear Coming soon labels for agreed future work.
- Knowledge reads the same catalogs. Prompt links create a new task with text filled in and never send it; advice examples create advice-only tasks.
- English remains the source/default language. Explicit Vietnamese loads a domain-local catalog on modern WordPress without changing the WordPress/plugin locale. Saved admin and website choices remain independent.

## Checks

- The existing WordPress/AI fixture completed on WordPress Playground 7.1.3 / PHP 8.3 with mocked provider responses.
- **50 welcome integration checks passed**, covering catalog completeness, prompts, shared knowledge, unread/seen state, rendering, English defaults, Vietnamese loading, source-text restoration, and unchanged WordPress locale.
- Browser checks passed at 1440px and 375px: first activation redirect exactly once; About first in both menus; search/no-results/reset; no mobile page overflow; both prompt types prefill new tasks without send/step requests; English/Vietnamese selection; no application JavaScript exceptions.
- PHP syntax and JavaScript syntax checks passed for changed runtime files. ZIP packaging verifies stable source bytes, paths, metadata and CRC.
- **14 isolated updater checks passed**, with a non-published 9.9.9 fixture so it remains newer than the installed source.

Screenshots and measured browser results are in ignored `output/verification-1.74/`.

## Boundaries

No production theme/plugin/content/settings were changed. The live GitHub update flow on phonglammedia.com remains a separate pending check while that site runs its existing 1.73.1 updater. No manual production ZIP installation was performed.

Opening About uses cached AI balance only. Local tests make no paid AI requests. Setup checkmarks indicate saved configuration, not a visual quality assessment. Exact AI cost depends on model pricing, usage and images; the guide does not promise fixed prices.
