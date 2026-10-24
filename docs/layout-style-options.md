# Reusable layout options (1.68.4)

Use block settings first. Do not put shared layout CSS in a hidden HTML block inside a page.

## Where to find and remove options

Select a block â†’ **Style presets â†’ Balanced layout**. Click a chip to enable it; click again or use **Clear all presets** to remove it from that block.

For a deliberate site-wide default: **Appearance â†’ Customize â†’ Additional CSS â†’ BIGSPY CSS library â†’ Bá»‘ cá»¥c cÃ¢n Ä‘á»‘i Â· dÃ¹ng chung vá»›i block**. Check a recipe to preview, uncheck to remove, then Publish. This affects matching BIGSPY blocks throughout the site's content. Keep global options off if only one block needs the style. Existing manual Additional CSS is preserved.

| Option | Corresponding block |
|---|---|
| Readable container with side gutters | Section |
| Balanced row spacing | Row |
| Stack icon boxes without stretching | Column |
| Balanced service card | Icon Box |
| Light hero with readable copy | Banner |
| Preserve intentional line breaks | Heading |
| Space above action buttons | Buttons |
| Compact readable price card | Price Table |

Both surfaces use the same fixed catalog in `inc/layout-style-presets.php`. The block selection stores allowlisted `bsPresets` IDs. The Customizer stores allowlisted `css_presets` IDs. Neither stores arbitrary user CSS. Theme colors are inherited. The new global options all default off and do not change existing saved content. Local preset classes carry greater specificity than the global variants. Direct block typography/spacing controls remain available; selecting a preset deliberately applies its named recipe until it is removed.

The compact price card sets restrained title/price/feature sizes, reduces repeated list spacing and keeps a 44px minimum action target. Mobile uses a 32px price and 22px card padding. It does not hide features, alter prices, invent discounts or change purchase behavior.

## Page migration

1. Back up the current block content and existing Additional CSS.
2. Install the updated theme before using the new preset IDs.
3. Replace hidden page-level style blocks with block options and the matching local presets.
4. Replace simple HTML illustrations with editable native blocks, or keep any truly custom reusable CSS in Additional CSS with a clear label.
5. Check the front end, editor preview, phone layout and reduced-motion behavior. Removing a preset must restore the original block appearance without editing code.

Run `node tools/build-translations.js`, PHP lint and `php tests/layout-style-presets.php` before packaging.
