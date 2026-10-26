const { chromium } = require(process.env.BST_PLAYWRIGHT || 'C:/Users/DELL/.cache/codex-runtimes/codex-primary-runtime/dependencies/node/node_modules/playwright');
const fs = require('fs');
const path = require('path');
const assert = require('assert');
(async () => {
  const out = path.resolve(__dirname, '../../output/verification-1.73'); fs.mkdirSync(out, { recursive: true });
  const browser = await chromium.launch({ executablePath: process.env.BST_CHROME || 'C:/Program Files/Google/Chrome/Application/chrome.exe', headless: true });
  const page = await browser.newPage({ viewport: { width: 1440, height: 960 } });
  const errors = []; const checks = [];
  page.on('pageerror', e => errors.push(e.message));
  await page.goto('http://127.0.0.1:9417/', { waitUntil: 'networkidle' });
  assert.equal(await page.locator('[data-bs-ticker]').count(), 12);
  assert.equal(await page.locator('[data-bs-expand]').count(), 4);
  for (const width of [1440, 820, 375]) {
    await page.setViewportSize({ width, height: 900 });
    for (const ticker of await page.locator('[data-bs-ticker]').all()) {
      await ticker.scrollIntoViewIfNeeded(); await page.waitForTimeout(120);
      const state = await ticker.evaluate(root => {
        const config = JSON.parse(root.dataset.bsTicker), seq = root.querySelector('.bs-ticker__sequence'), track = root.firstElementChild;
        const copies = Array.from(track.children).slice(1);
        const animation = track.getAnimations()[0];
        return { config, viewport: root.clientWidth, length: seq.getBoundingClientRect().width, total: track.getBoundingClientRect().width,
          clonesSafe: copies.every(c => c.hasAttribute('inert') && c.getAttribute('aria-hidden') === 'true' && !c.querySelector('[id]')),
          timing: animation ? animation.effect.getTiming() : null };
      });
      if (state.config.mode === 'loop') {
        assert(state.clonesSafe, 'Clones must be inert and free of duplicate IDs');
        assert(state.total >= 2 * state.viewport - 1, 'At least two viewports of coverage');
        assert(state.total >= state.viewport + state.length - 1, 'Coverage through entire translation');
        assert(Math.abs(state.timing.duration - state.length / state.config.speed * 1000) < 1, 'Constant px/sec');
        // Inspect the first and last animation samples and verify no uncovered viewport.
        const edges = await ticker.evaluate(root => {
          const track = root.firstElementChild, anim = track.getAnimations()[0], duration = anim.effect.getTiming().duration;
          anim.pause(); const results = [];
          for (const time of [0, duration - .01, duration + .01, duration * 2 + .01]) {
            anim.currentTime = time;
            const r = root.getBoundingClientRect(), t = track.getBoundingClientRect();
            results.push({ covered: t.left <= r.left + 1 && t.right >= r.right - 1, root: [r.left,r.right], track: [t.left,t.right], time });
          }
          anim.play(); return results;
        });
        assert(edges.every(e => e.covered), 'No blank viewport at either loop seam: ' + JSON.stringify({width, state, edges}));
      }
      checks.push(`ticker ${width}px ${state.config.direction}/${state.config.mode} count=${await ticker.locator('.bs-ticker__sequence').first().locator(':scope > .bs-ticker__slide').count()}`);
    }
    for (const gallery of await page.locator('[data-bs-expand]').all()) {
      await gallery.scrollIntoViewIfNeeded();
      const panels = gallery.locator('.bs-expand__panel');
      const track = gallery.locator('.bs-expand__track');
      if (width < 768) {
        await track.evaluate(el => el.scrollTo({ left: 0, behavior: 'instant' })); await page.waitForTimeout(150);
        let r = await track.evaluate(el => ({ left: el.getBoundingClientRect().left, width: el.clientWidth, first: el.firstElementChild.getBoundingClientRect().toJSON() }));
        assert(Math.abs(r.first.width / r.width - .75) < .01, 'Mobile main panel is 75%');
        assert(Math.abs(r.first.left - r.left) < 2, 'First panel sticks to left');
        await track.evaluate(el => el.scrollTo({ left: el.scrollWidth, behavior: 'instant' })); await page.waitForTimeout(150);
        r = await track.evaluate(el => ({ right: el.getBoundingClientRect().right, last: el.lastElementChild.getBoundingClientRect().toJSON() }));
        assert(Math.abs(r.right - r.last.right) < 2, 'Last panel sticks to right');
        const dots = gallery.locator('.bs-expand__dots button');
        assert.equal(await dots.last().getAttribute('aria-current'), 'true');
        await track.evaluate(el => el.scrollTo({ left: 0, behavior: 'instant' })); await page.waitForTimeout(100);
      } else {
        await panels.first().hover(); await page.waitForTimeout(600);
        const sizes = await panels.evaluateAll(els => els.map(e => e.getBoundingClientRect().width));
        assert(Math.abs(sizes[0] / sizes[1] - 2) < .05, 'Desktop expansion uses requested factor');
        await panels.nth(1).focus(); await page.keyboard.press('Enter'); await page.waitForTimeout(600);
        assert.equal(await panels.nth(1).locator('.bs-expand__title').getAttribute('aria-expanded'), 'true');
      }
      checks.push(`gallery ${width}px panels=${await panels.count()}`);
    }
    await page.locator('[data-bs-expand]').first().scrollIntoViewIfNeeded();
    await page.screenshot({ path: path.join(out, `gallery-${width}.png`) });
    assert(await page.evaluate(() => document.documentElement.scrollWidth <= innerWidth + 1), 'No page-level horizontal overflow');
  }
  await page.setViewportSize({ width: 1440, height: 900 });
  const loop = page.locator('[aria-label="3-left-loop"]'); await loop.scrollIntoViewIfNeeded();
  const duration = await loop.evaluate(root => root.firstElementChild.getAnimations()[0].effect.getTiming().duration);
  await page.mouse.move(0, 0); await page.waitForTimeout(duration * 2 + 200);
  checks.push('Observed more than two real ticker cycles');
  await page.emulateMedia({ reducedMotion: 'reduce' }); await page.waitForTimeout(150);
  assert.equal(await loop.evaluate(el => el.firstElementChild.getAnimations().length), 0, 'Reduced motion stops animation');
  assert.equal(await loop.evaluate(el => getComputedStyle(el).overflowX), 'auto');
  const gallery = page.locator('[data-bs-expand]').first();
  assert.equal(await gallery.locator('.bs-expand__panel').first().evaluate(el => getComputedStyle(el).transitionDuration), '0s');
  checks.push('Reduced motion: manual ticker scrolling and immediate panel transitions');
  assert.deepEqual(errors, []);
  fs.writeFileSync(path.join(out, 'browser-results.json'), JSON.stringify({ checks, consoleErrors: errors }, null, 2));
  console.log(JSON.stringify({ passed: checks.length, consoleErrors: errors, screenshots: out }, null, 2));
  await browser.close();
})().catch(e => { console.error(e); process.exit(1); });
