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

## Translations
- Source: `languages/vi.po` (never read whole). Add with `python tools/add-strings.py strings.json`, where the JSON maps English to Vietnamese. It skips existing strings and compiles.
- Compile only: `python tools/compile-po.py vi`. Do not use `tools/build-translations.js` for vi; it is outdated.

## Other
- Version: `style.css` and `BST_VERSION` in `functions.php`. Readiness: `python tools/release.py`.
- Theme panel: `inc/admin/`. Updater: `inc/updater.php`. Patterns: `patterns/`.

## Testing
- `node tools/playground-page.cjs` serves the theme on port 9418. Open `/?bst_autologin=1`; a test-only mu-plugin logs in, no password. Options: `--port=N`, `--woo=<plugin dir>`.
- Designer tests: `node tools/playground-check.cjs`; see `docs/DEV-RULES.md`.
- The test machine forces prefers-reduced-motion. The browser pane must be visible for animations to run.
