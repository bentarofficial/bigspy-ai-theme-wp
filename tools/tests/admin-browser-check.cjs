/* Only the disposable local Playground fixture; never use these credentials on a real site. */
const { chromium } = require(process.env.BST_PLAYWRIGHT || 'C:/Users/DELL/.cache/codex-runtimes/codex-primary-runtime/dependencies/node/node_modules/playwright');
const fs = require('fs'), path = require('path'), assert = require('assert');
(async () => {
  const browser = await chromium.launch({ executablePath: process.env.BST_CHROME || 'C:/Program Files/Google/Chrome/Application/chrome.exe', headless: true });
  const page = await browser.newPage({ viewport: { width: 1440, height: 960 } });
  const errors = []; page.on('pageerror', e => errors.push(e.message));
  page.on('console', m => { if (m.type() === 'error') { console.log('BROWSER_CONSOLE', m.text()); } });
  await page.goto('http://127.0.0.1:9417/wp-admin/admin.php?page=bst-ai-designer');
  if (await page.locator('#user_login').count()) {
    await page.locator('#user_login').fill('admin'); await page.locator('#user_pass').fill('password');
    await page.locator('#wp-submit').click();
  }
  await page.locator('.bst-aid-new').waitFor();
  await page.getByRole('button', { name: '+ New task', exact: true }).click();
  await page.locator('.bst-aid-input:not([disabled])').waitFor();
  const mode = page.getByLabel('Task mode');
  await mode.selectOption('advice');
  await page.getByRole('button', { name: '+ New task', exact: true }).click();
  await page.locator('.bst-aid-head').getByText('Advice only', { exact: true }).waitFor();
  await page.goto('http://127.0.0.1:9417/wp-admin/post-new.php?post_type=page');
  await page.waitForFunction(() => window.wp && wp.blocks && wp.blocks.getBlockType('bigspy/expand-gallery'));
  const welcome = page.getByRole('dialog', { name: 'Welcome to the editor', exact: true });
  if (await welcome.isVisible()) { await welcome.getByRole('button', { name: 'Close', exact: true }).click(); }
  await page.frameLocator('iframe[name="editor-canvas"]').locator('body').waitFor({ timeout: 60000 });
  // Inspect and instantiate through the editor data API to exercise real React effects.
  const registered = await page.evaluate(() => ['bigspy/ticker', 'bigspy/ticker-slide', 'bigspy/expand-gallery', 'bigspy/expand-panel'].every(name => !!wp.blocks.getBlockType(name)));
  assert(registered);
  await page.evaluate(() => wp.data.dispatch('core/block-editor').insertBlocks(wp.blocks.createBlock('bigspy/expand-gallery')));
  await page.waitForFunction(() => wp.data.select('core/block-editor').getBlocks().some(b => b.name === 'bigspy/expand-gallery' && b.innerBlocks.length === 3));
  assert.equal(await page.evaluate(() => wp.data.select('core/block-editor').getBlocks().find(b => b.name === 'bigspy/expand-gallery').innerBlocks.length), 3, 'New gallery defaults to three panels');
  await page.evaluate(() => wp.data.dispatch('core/block-editor').insertBlocks(wp.blocks.createBlock('bigspy/ticker')));
  await page.waitForTimeout(600);
  assert.equal(await page.evaluate(() => wp.data.select('core/block-editor').getBlocks().find(b => b.name === 'bigspy/ticker').innerBlocks.length), 3, 'New ticker defaults to three editable slides');
  const canvas = page.frameLocator('iframe[name="editor-canvas"]');
  const galleryLayout = canvas.locator('.bst-motion-editor--expand-gallery > .block-editor-inner-blocks > .block-editor-block-list__layout');
  assert.equal(await galleryLayout.evaluate(el => getComputedStyle(el).display), 'flex', 'Gallery editor CSS reaches the WordPress iframe');
  const tickerLayout = canvas.locator('.bst-motion-editor--ticker > .block-editor-inner-blocks > .block-editor-block-list__layout');
  assert.equal(await tickerLayout.evaluate(el => getComputedStyle(el).display), 'flex', 'Ticker editor CSS reaches the WordPress iframe');
  const out = path.resolve(__dirname, '../../output/verification-1.73'); fs.mkdirSync(out, { recursive: true });
  await page.screenshot({ path: path.join(out, 'editor.png') });
  assert.deepEqual(errors, []);
  fs.writeFileSync(path.join(out, 'admin-results.json'), JSON.stringify({ passed: 7, consoleErrors: errors }, null, 2));
  console.log('Admin smoke checks: 7 passed, no JavaScript errors.');
  await browser.close();
})().catch(e => { console.error(e); process.exit(1); });
