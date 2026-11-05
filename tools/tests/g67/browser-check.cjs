/* G-67 responsive and keyboard checks on the rendered fixture (render-fixture.php).
   Usage: node browser-check.cjs <fixture.html> <wordpress dir> [out dir]
   Pages load from http://t.test through Playwright routes (no web server); files come from the WordPress dir. */
const { chromium } = require(process.env.BST_PLAYWRIGHT || 'playwright');
const fs = require('fs'), path = require('path');
const [fixture, wpDir, outDir] = process.argv.slice(2);
const results = []; let fail = 0;
const t = (ok, label) => { results.push((ok ? 'ok   ' : 'FAIL ') + label); if (!ok) fail++; };
const note = label => results.push('note ' + label);
const VIEWPORTS = [375, 767, 768, 1023, 1024, 1440];

async function open(browser, width, opts = {}) {
  const ctx = await browser.newContext({ viewport: { width, height: 900 }, javaScriptEnabled: opts.js !== false, reducedMotion: opts.reduced ? 'reduce' : 'no-preference' });
  const page = await ctx.newPage();
  const errors = []; page.on('pageerror', e => errors.push(e.message));
  await page.route('http://t.test/**', route => {
    const u = new URL(route.request().url());
    if (u.pathname === '/g67/') return route.fulfill({ path: fixture, contentType: 'text/html; charset=utf-8' });
    const file = path.join(wpDir, decodeURIComponent(u.pathname));
    return fs.existsSync(file) && fs.statSync(file).isFile() ? route.fulfill({ path: file }) : route.fulfill({ status: 404, body: '' });
  });
  await page.goto('http://t.test/g67/', { waitUntil: 'load' });
  await page.waitForTimeout(400);
  return { ctx, page, errors };
}

(async () => {
  const browser = await chromium.launch();
  for (const w of VIEWPORTS) {
    const { ctx, page, errors } = await open(browser, w);
    const over = await page.evaluate(() => {
      const vw = document.documentElement.clientWidth, bad = [];
      document.querySelectorAll('main *, .bs-banner *, .bs-tabs *').forEach(el => {
        const r = el.getBoundingClientRect();
        if (r.width && (r.right > vw + 1 || r.left < -1) && getComputedStyle(el).position !== 'fixed' && !el.closest('[aria-hidden="true"], .screen-reader-text')) bad.push(el.className || el.tagName);
      });
      return { scroll: document.documentElement.scrollWidth > vw, bad: [...new Set(bad)].slice(0, 5) };
    });
    t(!over.scroll && !over.bad.length, `${w}px: no horizontal overflow ${over.bad.length ? JSON.stringify(over.bad) : ''}`);
    const cta = await page.evaluate(() => { const b = document.querySelector('.bs-banner a, .bs-banner .bs-btn, .bs-banner button'); if (!b) return null; const r = b.getBoundingClientRect(); return { w: r.width, h: r.height, inside: r.left >= 0 && r.right <= document.documentElement.clientWidth }; });
    t(cta && cta.inside && cta.h >= 24 && cta.w >= 24, `${w}px: banner CTA visible and at least 24×24 px ${JSON.stringify(cta)}`);
    const order = await page.evaluate(() => { const cols = [...document.querySelectorAll('.bs-row--reverse-mobile > .bs-column')]; return cols.map(c => ({ text: c.textContent.trim().slice(0, 6), top: Math.round(c.getBoundingClientRect().top), left: Math.round(c.getBoundingClientRect().left) })); });
    const visualFirst = [...order].sort((a, b) => a.top - b.top || a.left - b.left)[0];
    if (visualFirst && visualFirst.text !== order[0].text) note(`${w}px: reverseMobile shows "${visualFirst.text}" first while DOM/Tab order starts at "${order[0].text}" (expected by design; AI/editor should warn)`);
    t(!errors.length, `${w}px: no page errors ${errors.join('; ')}`);
    if (outDir && [375, 768, 1440].includes(w)) await page.screenshot({ path: path.join(outDir, `g67-${w}.png`), fullPage: true });
    await ctx.close();
  }

  // Tabs: ARIA wiring and keyboard, two tab sets on one page.
  {
    const { ctx, page } = await open(browser, 1024);
    const aria = await page.evaluate(() => [...document.querySelectorAll('.bs-tabs')].map(root => {
      const tabs = [...root.querySelectorAll(':scope > .bs-tabs__nav [role="tab"]')];
      const panels = [...root.querySelectorAll(':scope > .bs-tabs__panels > .bs-tab')];
      return {
        vertical: root.classList.contains('bs-tabs--vertical'),
        orientation: root.querySelector('[role="tablist"]').getAttribute('aria-orientation'),
        selected: tabs.filter(b => b.getAttribute('aria-selected') === 'true').length,
        wired: tabs.every((b, i) => b.getAttribute('aria-controls') === panels[i].id && panels[i].getAttribute('aria-labelledby') === b.id && panels[i].getAttribute('role') === 'tabpanel'),
        hidden: panels.filter(p => p.hidden).length === panels.length - 1,
        roving: tabs.filter(b => b.tabIndex === 0).length === 1,
        panelFocusable: panels.every(p => p.tabIndex === 0 || p.querySelector('a,button,input,select,textarea,[tabindex]')),
        ids: tabs.map(b => b.id).concat(panels.map(p => p.id)),
      };
    }));
    const ids = aria.flatMap(a => a.ids);
    t(new Set(ids).size === ids.length, 'tabs: ids unique across two tab sets');
    aria.forEach((a, i) => {
      t(a.selected === 1 && a.wired && a.hidden && a.roving, `tabs #${i + 1}: one selected, tab/panel linked, other panels hidden, roving tabindex`);
      t(a.panelFocusable, `tabs #${i + 1}: every panel can receive focus (APG: tabindex=0 when it has no focusable content)`);
      if (a.vertical) t(a.orientation === 'vertical', `tabs #${i + 1}: vertical tablist has aria-orientation="vertical"`);
    });
    const first = page.locator('.bs-tabs').first().locator('[role="tab"]');
    await first.nth(0).focus();
    await page.keyboard.press('ArrowRight'); t(await first.nth(1).evaluate(b => b === document.activeElement && b.getAttribute('aria-selected') === 'true'), 'tabs: ArrowRight moves focus and selects');
    await page.keyboard.press('End'); t(await first.nth(2).evaluate(b => b === document.activeElement), 'tabs: End goes to the last tab');
    await page.keyboard.press('ArrowRight'); t(await first.nth(0).evaluate(b => b === document.activeElement), 'tabs: ArrowRight wraps to the first');
    await page.keyboard.press('Home'); t(await first.nth(0).evaluate(b => b === document.activeElement), 'tabs: Home goes to the first tab');
    await page.keyboard.press('Tab');
    const into = await page.evaluate(() => { const a = document.activeElement; return a && (a.closest('.bs-tab.is-active') !== null || a.matches('.bs-tab.is-active')); });
    t(into, 'tabs: Tab from the tab list moves into the active panel');
    const second = page.locator('.bs-tabs').nth(1).locator('[role="tab"]');
    await second.nth(0).focus(); await page.keyboard.press('ArrowDown');
    t(await second.nth(1).evaluate(b => b.getAttribute('aria-selected') === 'true'), 'vertical tabs: ArrowDown selects the next tab');
    t(await first.nth(0).evaluate(b => b.getAttribute('aria-selected') === 'true'), 'tabs: the other tab set is not affected');
    // Viewport change keeps the selection and the content.
    await page.setViewportSize({ width: 375, height: 900 }); await page.waitForTimeout(200);
    t(await second.nth(1).evaluate(b => b.getAttribute('aria-selected') === 'true'), 'tabs: selection kept after resizing to 375px');
    await ctx.close();
  }

  // JavaScript off: no content lost.
  {
    const { ctx, page } = await open(browser, 375, { js: false });
    const vis = await page.evaluate(() => [...document.querySelectorAll('.bs-tab')].filter(p => p.offsetHeight > 0).length);
    t(vis === 6, `JS off: all 6 tab panels readable (${vis})`);
    await ctx.close();
  }

  // Reduced motion on and off.
  for (const reduced of [true, false]) {
    const { ctx, page, errors } = await open(browser, 768, { reduced });
    await page.locator('.bs-tabs').first().locator('[role="tab"]').nth(2).click(); await page.waitForTimeout(500);
    const panel = await page.evaluate(() => { const p = document.querySelector('.bs-tabs .bs-tab.is-active'); const s = getComputedStyle(p); return { text: p.textContent.trim(), opacity: s.opacity, anim: s.animationName }; });
    t(panel.text === 'Panel ủi' && panel.opacity === '1' && !errors.length, `reduced motion ${reduced ? 'on' : 'off'}: tab content readable after switching (${panel.anim})`);
    if (reduced) t(panel.anim === 'none', 'reduced motion on: tab fade animation is off');
    await ctx.close();
  }

  await browser.close();
  const report = results.join('\n') + (fail ? `\n\n${fail} failed\n` : '\n\nall passed\n');
  console.log(report);
  if (outDir) fs.writeFileSync(path.join(outDir, 'g67-results.txt'), report);
  process.exit(fail ? 1 : 0);
})().catch(e => { console.error(e); process.exit(1); });
