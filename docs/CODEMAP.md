# Code map

Theme root: `bigspy-ai-theme-wp/`. Paths below are relative to it unless they start with `tools/` or `docs/`.

## Blocks
- Scaffold: `python tools/add-block.py <slug> "<Title>" --prefix bs-x`.
- Metadata and front output: `blocks/<slug>/block.json`, `render.php`.
- Registration list: `bst_block_names()` in `inc/blocks.php:15`.
- Editors: new motion/InnerBlocks blocks in `assets/js/blocks-motion.js`; extras in `blocks-extra.js`, `blocks-more.js`; older blocks in `blocks-editor.js` (grep only).
- Inserter preview and icon color: `assets/js/block-previews.js`; check with `node tools/check-block-previews.js`.
- CSS: `assets/css/blocks.css` (grep only). After any edit run `python tools/split-blocks-css.py`, which regenerates `assets/css/blocks/segments.json` and `families.json`. Family map: `FAMILIES` in that script.
- Front JS: `assets/js/motion-*.js`, `main.js`. Shared design: `inc/block-design.php`, `assets/js/block-design.js`.
- AI knowledge of blocks: `ai-designer/knowledge/theme.md`.

## Customizer
- Fields: `inc/customizer/fields.php`; groups: `groups.php`; sanitizers: `sanitize.php`; custom controls: `controls.php`; bootstrap: `customizer.php`.
- Topic options: `inc/{blog,shop,product,sidebar}-options.php`, `inc/header-*.php`, `inc/footer-design.php`.
- CSS output: `inc/dynamic-css.php`. Panel JS: `assets/js/customizer-controls.js`, `customizer-preview.js`.

## AI Designer (`ai-designer/`)
- Entry: `ai-designer.php`; loaded by one line in `functions.php`.
- `includes/`: `client.php` Kyma API, `prompt.php` system prompt, `tasks.php` task loop and REST, `tools.php` tool registry, `tools-site.php`, `tools-design.php`, `tools-images.php`, `permissions.php` approval cards, `clean.php` output cleaning, `settings.php`, `guidelines.php`, `workspace.php`.
- UI: `assets/`; knowledge: `knowledge/`; About page: `welcome/`.
- Form tools (G-59): `includes/tools-form.php` (`form_get`, `form_configure` with dry run, diff, sample quote); policy in `permissions.php`. Test inside WordPress: `tools/tests/form-ai-integration.php` (load after wp-load.php, prints ok/FAIL).

## Translations
- Source: `languages/vi.po` (never read whole). Add with `python tools/add-strings.py strings.json`, where the JSON maps English to Vietnamese. It skips existing strings and compiles.
- Compile only: `python tools/compile-po.py vi`.

## Other
- Version: `style.css` and `BST_VERSION` in `functions.php`. Readiness: `python tools/release.py`.
- Optional features: `inc/features.php` (page, toggles, field types), one file per feature in `inc/features/`. Test: `node tools/features-check.cjs`.
- Theme panel: `inc/admin/`. Updater: `inc/updater.php`. Patterns: `patterns/`.
- Built-in Form: engine `inc/form-native.php`; schema (field IDs, conditions, dependency checks) `inc/form-schema.php`; front JS in `assets/js/blocks.js` (`bsNfApply`); editor `NativeFormPanels` in `blocks-editor.js`. Price quote (G-58): `inc/form-quote.php` (formula, quote, snapshot, `[_quote]` tags), shared JS engine `assets/js/form-quote.js` (`window.bsQuote`, loaded only with priced forms and in the editor). Tests: `php tools/tests/form-schema-check.php`, `php tools/tests/form-quote-check.php`, `node tools/tests/form-quote-parity.cjs`.

## Listing (BIGSPY AI List)
- `inc/features/listing.php` loads `inc/listing/` (`core.php`, `blocks.php`, `sponsored.php`, `collections.php`, `schema.php`, `meta-box.php`, `single.php`, `design.php` for Customizer → Listing design, `home-hero.php` for tokens and `[bigspy_stat]`, `home-sidebar.php`). Templates `woocommerce/single-listing.php`, `content-listing.php`; CSS `assets/css/listing.css`; editor `assets/js/blocks-listing.js`; old blocks `legacy-list/`.

## WooCommerce
- `inc/woocommerce.php` (loaded only with WooCommerce), `inc/woocommerce-elements.php`, `inc/shop-options.php`, `inc/product-options.php`, `inc/quick-view.php`, `inc/variation-swatches.php`, `inc/checkout-extras.php`; template `woocommerce/content-product.php`; CSS `assets/css/woocommerce.css`; test `node tools/woo-check.cjs`.

## Performance
- `inc/performance.php`: per-page CSS, self-hosted fonts, generated CSS written to files in `uploads/bigspy/`.

## Testing
- `node tools/playground-page.cjs` serves the theme on port 9418. Open `/?bst_autologin=1`; a test-only mu-plugin logs in, no password. Options: `--port=N`, `--woo=<plugin dir>`.
- Designer tests: `node tools/playground-check.cjs`; see `docs/DEV-RULES.md`.
- Without wordpress.org access: `npm pack @wp-playground/wordpress-builds@0.9.19`, serve its `src/wordpress/wp-6.5.zip` on localhost and set `BST_TEST_WP` to that URL. PHP and the front end work; wp-admin static JS/CSS are missing, so the block editor cannot be tested this way.
- The test machine forces prefers-reduced-motion. The browser pane must be visible for animations to run.
