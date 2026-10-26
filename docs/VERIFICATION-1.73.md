# BIGSPY AI Theme WP 1.73.0 verification

Date: 2026-10-26. Target for user installation: the Hostinger draft site.
No production or draft-site files were changed by these checks.

## Completed checks

- PHP parser lint: all **175 shipped PHP files passed**, including the updater.
- All 58 block JSON schemas parse successfully.
- WordPress Playground CLI 3.1.60, cached WordPress 7.1.3, PHP 8.3:
  **81 integration checks passed**. Includes UX Block creation/publication/footer
  assignment, static homepage, menu locations, Theme Builder rules, SEO metadata,
  advice-only writes, stop-before-late-write, option whitelist, invalid plugin ZIP
  rejection, approval expiry/replay, exact arguments applied by approval endpoint,
  plugin activation/undo, fourth-image policy, atomic executor exclusion and
  task-scoped budget approval. A deterministic Kyma mock exercises the real task
  loop. Hex colors normalize to inline styles rather than invalid preset slugs.
- Chrome frontend: **50 checks passed** across 1440px, 820px and 375px. Tickers
  have 1/3/20 slides, both directions and both modes. Loop tests check inert clones,
  duplicate IDs, coverage at both seams, constant px/sec and more than two actual
  cycles. Galleries have 2/3/4/6 panels, requested flex ratios, keyboard activation,
  75% mobile panels with left/right alignment at the ends and position dots.
  Reduced motion stops the ticker and removes gallery transitions. No page overflow
  or JavaScript exceptions were observed.
- Chrome admin/editor: **7 smoke checks passed**. Workspace can open/create tasks;
  advice mode is visible; all four blocks register; new ticker/gallery have three
  editable children; both editor layouts receive their flex CSS inside the Gutenberg
  iframe. No JavaScript exceptions were observed. External resource failures in the
  restricted local environment were not counted as application exceptions.
- GitHub updater: **9 checks passed** in the coordinated updater test run
  (commit `5d31f0c`). A local stable-release fixture drives the real WordPress
  Theme_Upgrader from 1.73.0 to 1.73.1 inside Playground storage. The test verifies
  detection, release notes, foreign ZIP rejection, harmless API failures, ZIP
  installation, the installed version, active theme and preserved settings.
- Packaging validates matching versions, ZIP CRC, forward-slash paths, the single
  theme root, new block metadata, CSS segments, bootstrap include files and byte-for-
  byte agreement with a stable source snapshot. SHA-256 is saved alongside the ZIP.

## Scope and limits

Kyma/image billing and live provider generation were not exercised; tests use local
fixtures and incur no provider charges. The AI plugin test mocks directory metadata
and exercises real activation/undo of a local fixture; it does not install a real
third-party plugin over the network. Browser checks use the disposable local test
site, not the user's existing Hostinger content. Undo retains generated media,
installed plugin files/data and API charges; image optimization cannot restore
original image bytes. A user installs the ZIP and performs the final staging-site
check before further website design work.

Screenshots and machine-readable reports are under the local ignored directory
`output/verification-1.73/`. Updater coverage is in
`tools/tests/updater-integration.php` and `tools/updater-playground-check.cjs`.
