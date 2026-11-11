/* G-151: Customizer quick-edit labels. Needs a Playground server: node tools/playground-page.cjs --port=9419
   then BST_URL=http://127.0.0.1:9419 node tools/tests/customizer-quick-edit.cjs */
const { chromium } = require(process.env.BST_PLAYWRIGHT || 'C:/Users/DELL/.cache/codex-runtimes/codex-primary-runtime/dependencies/node/node_modules/playwright');
const assert = require('assert');
const base = process.env.BST_URL || 'http://127.0.0.1:9417';
(async () => {
  const browser = await chromium.launch({ executablePath: process.env.BST_CHROME || 'C:/Program Files/Google/Chrome/Application/chrome.exe', headless: true });
  const page = await browser.newPage({ viewport: { width: 1440, height: 960 } });
  const errors = []; page.on('pageerror', e => errors.push(e.message));
  await page.goto(base + '/?bst_autologin=1', { timeout: 180000 });
  await page.goto(base + '/wp-admin/customize.php', { timeout: 180000 });
  await page.waitForFunction(() => window.wp && wp.customize && wp.customize.state && wp.customize.state('processing') && document.querySelector('.bst-qe-toggle'), null, { timeout: 90000 });
  const frame = () => page.frameLocator('#customize-preview iframe');
  await frame().locator('.bs-header-el--logo').first().waitFor({ timeout: 90000 });
  await page.waitForTimeout(1500);
  await frame().locator('.bs-header-el--logo').first().hover();
  const bar = frame().locator('.bst-qe:not([hidden])');
  await bar.waitFor({ timeout: 5000 });
  const labels = await bar.locator('button').allTextContents();
  console.log('labels:', labels.join(' › '));
  assert(labels.length >= 2, 'nested labels (header + logo)');
  await bar.locator('button').last().click();
  await page.waitForFunction(() => wp.customize.section('bst_he_logo').expanded(), null, { timeout: 5000 });
  console.log('ok   logo label opens bst_he_logo');
  // Reverse: open footer bottom section → preview flashes it.
  await page.evaluate(() => wp.customize.section('bst_footer_bottom').focus());
  await frame().locator('.bs-footer-bottom.bst-qe-flash').waitFor({ timeout: 5000 });
  console.log('ok   opening a section highlights its element');
  // Toggle off: no label.
  const setToggle = v => page.evaluate(v => { const i = document.querySelector('.bst-qe-toggle input'); i.checked = v; i.dispatchEvent(new Event('change', { bubbles: true })); }, v);
  await setToggle(false);
  await page.mouse.move(5, 900);
  await page.waitForTimeout(500);
  await frame().locator('.bs-footer-bottom').first().hover();
  await page.waitForTimeout(500);
  assert.equal(await frame().locator('.bst-qe:not([hidden])').count(), 0, 'no labels when switched off');
  console.log('ok   switch off hides labels');
  await setToggle(true);
  assert.deepEqual(errors, []);
  console.log('Customizer quick-edit: 3 passed, no JavaScript errors.');
  await browser.close();
})().catch(e => { console.error(e); process.exit(1); });
