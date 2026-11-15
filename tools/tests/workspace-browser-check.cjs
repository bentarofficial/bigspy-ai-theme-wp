/* AI Designer Workspace in a real browser, against the scripted provider of tools/playground/mu-ai-mock.php.
   Start the site first:  node tools/playground-page.cjs --mock-ai   (see docs/DEV-RULES.md for BST_TEST_WP)
   Then:                  node tools/tests/workspace-browser-check.cjs [--base=http://127.0.0.1:9418] [--shots=output/workspace]
   No real key, no provider charges. Prints ok/FAIL per check; exit code 1 on any failure. */
const { chromium } = require('../lib/env.cjs').playwright();
const fs = require('fs');
const path = require('path');
const arg = (name, def) => (process.argv.find(a => a.startsWith(`--${name}=`)) || `=${def}`).split('=').slice(1).join('=');
const base = arg('base', 'http://127.0.0.1:9418');
const shots = path.resolve(arg('shots', path.join(__dirname, '../../output/workspace')));

const results = [];
const check = (ok, name) => { results.push([!!ok, name]); console.log(`${ok ? 'ok  ' : 'FAIL'} ${name}`); };

(async () => {
  fs.mkdirSync(shots, { recursive: true });
  const browser = await chromium.launch({ executablePath: process.env.BST_CHROME || undefined, args: ['--no-proxy-server'] });
  const page = await browser.newPage({ viewport: { width: 1440, height: 900 } });
  const errors = [];
  page.on('pageerror', e => errors.push(e.message));
  // Only the local site: outside requests (fonts, wp.org) would hang without network.
  await page.route(u => !u.href.startsWith(base), r => r.abort());
  page.setDefaultTimeout(60000);

  await page.goto(`${base}/?bst_autologin=1`, { timeout: 120000 });
  await page.goto(`${base}/wp-admin/admin.php?page=bst-ai-designer`, { timeout: 120000 });
  await page.waitForSelector('.bst-aid-new');
  check(true, 'Workspace loads with the mock provider connected');

  // Runs one message in a new task and waits until the run stops.
  const run = async text => {
    const before = await page.locator('.bst-aid-tasks li:not(.bst-aid-empty)').count();
    await page.click('.bst-aid-new');
    // Wait until the new task is open: one more task in the list and an empty chat.
    await page.waitForFunction(n => {
      const i = document.querySelector('.bst-aid-input');
      return document.querySelectorAll('.bst-aid-tasks li:not(.bst-aid-empty)').length > n && i && !i.disabled
        && !document.querySelector('.bst-aid-chat .bst-aid-msg');
    }, before);
    await page.fill('.bst-aid-input', text);
    // The run ends with a /step answer whose state is neither running nor busy.
    const end = (async () => {
      for (;;) {
        const r = await page.waitForResponse(res => /\/step(\?|$)/.test(res.url()), { timeout: 90000 });
        const state = (await r.json().catch(() => ({}))).state;
        if (state !== 'running' && state !== 'busy') { return state; }
      }
    })();
    const seen = new Set();
    const sample = setInterval(() => { page.evaluate(() => { const w = document.querySelector('.bst-aid-working'); return w && !w.hidden ? w.querySelector('.bst-aid-working-text').textContent : ''; }).then(t => { if (t) { seen.add(t); } }).catch(() => {}); }, 200);
    await page.keyboard.press('Enter');
    await end.catch(() => {});
    clearInterval(sample);
    return seen;
    await page.waitForTimeout(800);
  };

  const live = await run('Build a home page');
  check(live.has('Reading the site…') && live.has('Building the page…'), `Live status names the upcoming tools (${[...live].join(' | ')})`);
  check(await page.locator('.bst-aid-palette').count() === 2, 'Palette card shows two palettes');
  check(await page.locator('.bst-aid-plan li').count() === 4, 'Page plan card lists four sections');
  check(await page.locator('.bst-aid-tool').count() >= 4, 'Tool activity rows are shown');
  check(await page.locator('.bst-aid-msg.is-ai').count() >= 1, 'Final answer is shown');
  const src = await page.locator('.bst-aid-frame').getAttribute('src');
  check(!!src && /preview|page_id|\?p=/.test(src), 'Preview frame points at the new draft');
  await page.screenshot({ path: path.join(shots, 'run-1440.png') });

  // G-207 layout: one-row header, the rail folds beside the preview, a desktop preview is scaled, not squeezing the chat.
  const head = await page.evaluate(() => document.querySelector('.bst-aid-head').clientHeight);
  check(head <= 56, `Task header stays on one row (${head}px)`);
  check(await page.locator('.bst-aid-ws.is-rail-min').count() === 1, 'Task rail folds to icons beside the preview');
  const chatBefore = await page.evaluate(() => document.querySelector('.bst-aid-main').clientWidth);
  await page.click('.bst-aid-devices [data-w="1280"]');
  await page.waitForTimeout(400);
  const dev = await page.evaluate(() => ({ chat: document.querySelector('.bst-aid-main').clientWidth, inner: document.querySelector('.bst-aid-frame').contentWindow.innerWidth }));
  check(dev.inner === 1280 && dev.chat === chatBefore, `Desktop preview renders 1280px wide without changing the chat (${dev.inner}px, chat ${dev.chat}px)`);
  await page.click('.bst-aid-devices [data-w="fit"]');
  check(await page.evaluate(() => document.documentElement.scrollHeight - window.innerHeight) <= 1, 'No page-level vertical scroll at 1440px');

  await run('Ask for approval please');
  check(await page.locator('.bst-aid-approval').count() === 1, 'Approval card appears for a site setting');

  await run('Trigger an error');
  check(await page.locator('.bst-aid-note.is-error').count() >= 1, 'Provider error is reported in the chat');

  await run('Give me a long answer');
  check(await page.locator('.bst-aid-msg.is-ai table').count() === 1 && await page.locator('.bst-aid-msg.is-ai .bst-aid-code').count() === 1 && await page.locator('.bst-aid-msg.is-ai li > ul').count() === 1, 'Long answer renders a table, a code block and a nested list');
  await page.screenshot({ path: path.join(shots, 'long-1440.png') });

  for (const [w, h] of [[1024, 768], [390, 844]]) {
    await page.setViewportSize({ width: w, height: h });
    await page.waitForTimeout(400);
    const overflow = await page.evaluate(() => document.documentElement.scrollWidth - window.innerWidth);
    check(overflow <= 1, `No horizontal page scroll at ${w}px (${overflow}px)`);
    const box = await page.evaluate(() => { const r = document.querySelector('.bst-aid-input').getBoundingClientRect(); return r.bottom <= window.innerHeight && r.top >= 0; });
    check(box, `Message box is visible without scrolling at ${w}px`);
    await page.screenshot({ path: path.join(shots, `run-${w}.png`) });
  }

  check(!errors.length, `No script errors${errors.length ? ': ' + errors.slice(0, 3).join(' | ') : ''}`);
  await browser.close();
  const failed = results.filter(r => !r[0]).length;
  console.log(`${results.length - failed}/${results.length} passed. Screenshots: ${shots}`);
  process.exit(failed ? 1 : 0);
})().catch(e => { console.error(e); process.exit(1); });
