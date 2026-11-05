/* G-41: JS/CSS bytes and unused share per fixture page (Chromium coverage), plus behaviour smoke checks.
   Usage: node measure.cjs <wordpress dir> <dir with text.html landing.html form.html> [label] */
const { chromium } = require(process.env.BST_PLAYWRIGHT || 'playwright');
const fs = require('fs'), path = require('path');
const [wpDir, pagesDir, label = 'run'] = process.argv.slice(2);
(async () => {
  const browser = await chromium.launch(); const rows = []; let fail = 0;
  for (const kind of ['text', 'landing', 'form']) {
    const page = await browser.newPage({ viewport: { width: 375, height: 800 } });
    const errors = []; page.on('pageerror', e => errors.push(e.message));
    const sizes = {};
    await page.route('http://t.test/**', route => {
      const u = new URL(route.request().url());
      if (u.pathname === '/page/') return route.fulfill({ path: path.join(pagesDir, kind + '.html'), contentType: 'text/html; charset=utf-8' });
      const file = path.join(wpDir, decodeURIComponent(u.pathname));
      if (fs.existsSync(file) && fs.statSync(file).isFile()) { sizes[u.pathname] = fs.statSync(file).size; return route.fulfill({ path: file }); }
      return route.fulfill({ status: 404, body: '' });
    });
    await page.coverage.startJSCoverage({ resetOnNavigation: false });
    await page.coverage.startCSSCoverage({ resetOnNavigation: false });
    await page.goto('http://t.test/page/'); await page.waitForTimeout(500);
    // Exercise the page like a visitor, so code that runs on interaction counts as used.
    if (kind === 'landing') { await page.locator('[role="tab"]').nth(1).click(); await page.locator('.bs-accordion button, .bs-accordion summary').first().click().catch(() => {}); }
    if (kind === 'form') { await page.fill('[name=kg]', '3'); await page.waitForTimeout(300); }
    const js = await page.coverage.stopJSCoverage(); const css = await page.coverage.stopCSSCoverage();
    const theme = list => list.filter(e => e.url.includes('/themes/bigspy') || e.url.includes('/uploads/bigspy'));
    const used = e => e.functions ? e.functions.reduce((n, f) => n, 0) : 0;
    const jsRows = theme(js).map(e => {
      const total = e.source.length; const covered = new Uint8Array(total);
      e.functions.forEach(f => f.ranges.forEach(r => { if (r.count > 0) for (let i = r.startOffset; i < r.endOffset && i < total; i++) covered[i] = 1; }));
      e.functions.forEach(f => f.ranges.forEach(r => { if (r.count === 0) for (let i = r.startOffset; i < r.endOffset && i < total; i++) covered[i] = 0; }));
      const u = covered.reduce((a, b) => a + b, 0); return { file: path.basename(new URL(e.url).pathname), total, unused: total - u };
    });
    const cssRows = theme(css).map(e => { const u = e.ranges.reduce((a, r) => a + r.end - r.start, 0); return { file: path.basename(new URL(e.url).pathname), total: e.text.length, unused: e.text.length - u }; });
    const sum = (l, k) => l.reduce((a, r) => a + r[k], 0);
    let ok = !errors.length;
    if (kind === 'landing') ok = ok && await page.locator('[role="tab"]').nth(1).getAttribute('aria-selected') === 'true';
    if (kind === 'form') ok = ok && /54\.000/.test(await page.locator('[data-bs-calc-out]').textContent()) && !(await page.locator('[data-bs-fid="f_n"]').evaluate(e => e.hidden));
    if (!ok) fail++;
    rows.push({ kind, js: jsRows, css: cssRows, jsTotal: sum(jsRows, 'total'), jsUnused: sum(jsRows, 'unused'), cssTotal: sum(cssRows, 'total'), cssUnused: sum(cssRows, 'unused'), behaviour: ok ? 'ok' : 'FAIL ' + errors.join('; ') });
    await page.close();
  }
  await browser.close();
  const kb = n => (n / 1024).toFixed(1) + ' KB';
  const out = [`# ${label}`, '| Page | Theme JS | JS unused | Theme CSS | CSS unused | Behaviour |', '| --- | --- | --- | --- | --- | --- |']
    .concat(rows.map(r => `| ${r.kind} | ${kb(r.jsTotal)} (${r.js.map(j => j.file).join(', ')}) | ${kb(r.jsUnused)} (${Math.round(r.jsUnused / r.jsTotal * 100)}%) | ${kb(r.cssTotal)} | ${kb(r.cssUnused)} (${Math.round(r.cssUnused / r.cssTotal * 100)}%) | ${r.behaviour} |`));
  if (process.env.G41_DETAIL) rows.forEach(r => console.log(r.kind, JSON.stringify(r.js.concat(r.css).map(x => [x.file, kb(x.total), kb(x.unused)]))));
  console.log(out.join('\n')); process.exit(fail ? 1 : 0);
})().catch(e => { console.error(e); process.exit(1); });
