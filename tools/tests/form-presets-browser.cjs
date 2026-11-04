#!/usr/bin/env node
/**
 * G-69: the browser price preview (assets/js/blocks.js) gives the same totals as
 * the server for every industry preset, at 375px without horizontal overflow.
 * Usage: node tools/tests/form-presets-browser.cjs
 * Pages are rendered by tools/tests/form-presets-check.php (real Form template,
 * stubbed WordPress), so no WordPress site is needed.
 */
'use strict';
const { execFileSync } = require('child_process');
const fs = require('fs');
const os = require('os');
const path = require('path');
const { chromium } = require('playwright');

const dir = fs.mkdtempSync(path.join(os.tmpdir(), 'bst-presets-'));
try {
  execFileSync('php', [path.join(__dirname, 'form-presets-check.php'), dir], { stdio: 'inherit' });
} catch (e) {
  console.error('form-presets-check.php failed; fix it first.');
  process.exit(1);
}
const pages = JSON.parse(fs.readFileSync(path.join(dir, 'samples.json'), 'utf8'));

(async () => {
  const browser = await chromium.launch();
  let pass = 0;
  const fail = [];
  const check = (ok, label) => { if (ok) pass++; else fail.push(label); };
  for (const [key, data] of Object.entries(pages)) {
    const page = await browser.newPage({ viewport: { width: 375, height: 900 } });
    const errors = [];
    page.on('pageerror', e => errors.push(e.message));
    await page.goto('file://' + path.join(dir, key + '.html'));
    for (const [i, smp] of data.samples.entries()) {
      const got = await page.evaluate(({ fields, values }) => {
        const form = document.querySelector('[data-bs-native-form] form, form');
        form.reset();
        for (const f of fields) {
          const v = values[f.name];
          const els = [...form.querySelectorAll(`[name="${f.name}"], [name="${f.name}[]"]`)];
          if (!els.length) continue;
          if (f.type === 'checkbox' || f.type === 'radio') {
            const picked = v === undefined ? [] : String(v).split(', ');
            els.forEach(el => { el.checked = picked.includes(el.value); });
          } else if (v !== undefined) {
            els[0].value = v;
          }
        }
        form.querySelectorAll('input, select, textarea').forEach(el => el.dispatchEvent(new Event('input', { bubbles: true })));
        form.querySelectorAll('input, select, textarea').forEach(el => el.dispatchEvent(new Event('change', { bubbles: true })));
        return form.querySelector('[name="bst_quote_seen"]').value;
      }, { fields: data.fields, values: smp.values });
      check(got === `ok:${smp.total}`, `${key} sample ${i}: browser ${got}, server ok:${smp.total}`);
    }
    const over = await page.evaluate(() => document.documentElement.scrollWidth - document.documentElement.clientWidth);
    check(over <= 0, `${key}: no horizontal overflow at 375px (${over}px)`);
    check(!errors.length, `${key}: no JS errors ${errors.join(' | ')}`);
    await page.close();
  }
  await browser.close();
  fs.rmSync(dir, { recursive: true, force: true });
  console.log(`form presets browser: ${pass} passed, ${fail.length} failed`);
  fail.forEach(f => console.log('  FAIL ' + f));
  process.exit(fail.length ? 1 : 0);
})();
