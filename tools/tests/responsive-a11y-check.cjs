// G-67: responsive and keyboard checks on the PHP-rendered block fixture.
// Usage: node tools/tests/responsive-a11y-check.cjs [outDir]
// Renders tools/tests/responsive-fixture.php (stubbed WordPress, no editor) and
// measures it in Chromium. Exit code 1 when a check fails; report.json in outDir.
const { chromium } = require(process.env.BST_PLAYWRIGHT || 'playwright');
const { execFileSync } = require('child_process');
const fs = require('fs');
const path = require('path');

const out = path.resolve(process.argv[2] || path.join(__dirname, '../../output/g67'));
fs.mkdirSync(out, { recursive: true });
const img = path.join(out, 'banner.jpg');
if (!fs.existsSync(img)) {
  // Real JPEG (not a placeholder colour): 1600x900, made with Pillow.
  execFileSync('python3', ['-c', `from PIL import Image,ImageDraw
im=Image.new('RGB',(1600,900));d=ImageDraw.Draw(im)
[d.line([(0,y),(1600,y)],fill=(30+y//8,60,120+y//10)) for y in range(900)]
d.ellipse((1100,200,1500,600),fill=(240,180,60));im.save(${JSON.stringify(img)},quality=85)`]);
}
const html = path.join(out, 'fixture.html');
execFileSync('php', [path.join(__dirname, 'responsive-fixture.php'), html, 'file://' + img]);

const widths = [375, 549, 550, 768, 849, 850, 1024, 1025, 1440];
const results = [];
const check = (ok, label, detail) => results.push({ ok: !!ok, label, detail });

(async () => {
  const browser = await chromium.launch();
  const version = browser.version();
  const url = 'file://' + html;

  // 1–2. Layout per viewport.
  for (const motion of ['no-preference', 'reduce']) {
    const page = await browser.newPage({ viewport: { width: 1440, height: 900 }, reducedMotion: motion });
    const errors = [];
    page.on('pageerror', e => errors.push(e.message));
    await page.goto(url);
    for (const width of widths) {
      await page.setViewportSize({ width, height: 900 });
      await page.waitForTimeout(50);
      const m = await page.evaluate(() => {
        const vw = document.documentElement.clientWidth;
        const over = [...document.querySelectorAll('main *')].filter(el => {
          const r = el.getBoundingClientRect();
          return r.width && (r.right > vw + 1 || r.left < -1) && !el.closest('.bs-slider__track');
        }).map(el => el.tagName + '.' + el.className).slice(0, 5);
        const cols = id => [...document.querySelectorAll(`#${id} > .bs-column`)].map(c => { const r = c.getBoundingClientRect(); return { label: c.querySelector('h2').textContent, x: Math.round(r.left), y: Math.round(r.top + scrollY), w: Math.round(r.width) }; });
        const banner = document.querySelector('#fx-banner');
        const box = banner.querySelector('.bs-banner__box');
        const cta = banner.querySelector('.wp-block-button__link');
        const cr = cta.getBoundingClientRect(), br = banner.getBoundingClientRect(), bx = box.getBoundingClientRect();
        cta.scrollIntoView({ block: 'center', behavior: 'instant' });
        const cr2 = cta.getBoundingClientRect();
        const hit = document.elementFromPoint(cr2.left + cr2.width / 2, cr2.top + cr2.height / 2);
        const imgEl = banner.querySelector('img');
        return {
          docOverflow: document.documentElement.scrollWidth - vw, over,
          row: cols('fx-row'), rev: cols('fx-row-rev'),
          banner: { h: Math.round(br.height), boxTop: Math.round(bx.top - br.top), boxBottom: Math.round(bx.bottom - br.top), ctaInside: cr.bottom <= br.bottom + 0.5 && cr.top >= br.top - 0.5, ctaHit: hit === cta || cta.contains(hit), img: imgEl && imgEl.complete && imgEl.naturalWidth },
          sectionPad: getComputedStyle(document.querySelector('#fx-section')).paddingTop,
          tabsGrid: getComputedStyle(document.querySelector('#fx-tabs-2')).display,
          tabTarget: Math.min(...[...document.querySelectorAll('#fx-tabs-1 [role=tab]')].map(b => b.getBoundingClientRect().height)),
        };
      });
      const tag = `${motion} @${width}`;
      check(m.docOverflow <= 0 && !m.over.length, `${tag} no horizontal overflow`, m.over);
      check(m.banner.img > 0, `${tag} banner image loaded`, m.banner.img);
      check(m.banner.ctaInside && m.banner.boxTop >= 0 && m.banner.boxBottom <= m.banner.h, `${tag} banner content not cropped`, m.banner);
      check(m.banner.ctaHit, `${tag} banner CTA receives clicks`, m.banner);
      check(m.tabTarget >= 24, `${tag} tab target >= 24px (WCAG 2.5.8)`, m.tabTarget);
      const sideBySide = m.row[0].y === m.row[1].y;
      check(width >= 550 ? sideBySide : !sideBySide, `${tag} two columns ${width >= 550 ? 'side by side' : 'stacked'}`, m.row);
      results.push({ info: true, label: `${tag} layout`, detail: { sectionPad: m.sectionPad, tabsVertical: m.tabsGrid, rev: m.rev, bannerH: m.banner.h } });
    }
    check(!errors.length, `${motion} no JS errors`, errors);
    await page.close();
  }

  // 2. reverseMobile: visual order vs DOM/Tab order at 375.
  {
    const page = await browser.newPage({ viewport: { width: 375, height: 900 } });
    await page.goto(url);
    const visual = await page.evaluate(() => [...document.querySelectorAll('#fx-row-rev > .bs-column')].map(c => [c.getBoundingClientRect().top, c.querySelector('h2').textContent]).sort((a, b) => a[0] - b[0]).map(x => x[1]));
    await page.evaluate(() => document.querySelectorAll('#fx-row .fx-link')[1].focus());
    const tabOrder = [];
    for (let i = 0; i < 2; i++) { await page.keyboard.press('Tab'); tabOrder.push(await page.evaluate(() => document.activeElement.closest('.bs-column')?.querySelector('h2').textContent)); }
    const dom = ['Cột Chữ', 'Cột Ảnh'];
    results.push({ info: true, label: 'reverseMobile @375 order', detail: { dom, visual, tabOrder } });
    check(JSON.stringify(tabOrder) === JSON.stringify(dom), 'reverseMobile keeps DOM order for Tab (no DOM reorder)', tabOrder);
    check(JSON.stringify(visual) !== JSON.stringify(dom), 'reverseMobile changes visual order only on phones (warn author when columns hold links/reading order)', visual);
    await page.close();
  }

  // 3. Tabs (WAI-ARIA Tabs Pattern, automatic activation).
  {
    const page = await browser.newPage({ viewport: { width: 1024, height: 900 } });
    await page.goto(url);
    const aria = await page.evaluate(() => [...document.querySelectorAll('.bs-tabs')].map(t => {
      const tabs = [...t.querySelectorAll(':scope > .bs-tabs__nav > [role=tab]')];
      const list = t.querySelector(':scope > .bs-tabs__nav');
      return {
        id: t.id, role: list.getAttribute('role'), orientation: list.getAttribute('aria-orientation'), dir: getComputedStyle(list).flexDirection,
        tabs: tabs.map(b => { const p = document.getElementById(b.getAttribute('aria-controls')); return { id: b.id, name: b.textContent.trim(), sel: b.getAttribute('aria-selected'), ti: b.tabIndex, panel: !!p, role: p && p.getAttribute('role'), lb: p && p.getAttribute('aria-labelledby') === b.id, hidden: p && p.hidden, visible: p && p.offsetParent !== null }; }),
      };
    }));
    const ids = aria.flatMap(t => t.tabs.flatMap(b => [b.id]));
    check(new Set(ids).size === ids.length && ids.every(Boolean), 'tabs: ids unique across 3 tab blocks', ids);
    for (const t of aria) {
      check(t.role === 'tablist' && t.tabs.length === 3, `${t.id}: tablist with 3 tabs`, t);
      check(t.tabs.every(b => b.name && b.panel && b.role === 'tabpanel' && b.lb), `${t.id}: each tab named and linked to its tabpanel`, t.tabs);
      check(t.tabs.filter(b => b.sel === 'true').length === 1 && t.tabs[0].ti === 0 && t.tabs.slice(1).every(b => b.ti === -1), `${t.id}: one selected, roving tabindex`, t.tabs);
      check(t.tabs[0].visible && t.tabs.slice(1).every(b => b.hidden && !b.visible), `${t.id}: inactive panels hidden`, t.tabs);
      const vertical = t.dir === 'column';
      check(vertical ? t.orientation === 'vertical' : t.orientation !== 'vertical', `${t.id}: aria-orientation matches layout (${t.dir})`, t.orientation);
    }
    const state = id => page.evaluate(id => { const a = document.activeElement; return { focus: a.id, sel: [...document.querySelectorAll(`#${id} [role=tab]`)].findIndex(b => b.getAttribute('aria-selected') === 'true'), inTabs: !!a.closest('#' + id) }; }, id);
    const t1 = '#fx-tabs-1';
    await page.focus(`${t1} [role=tab]`);
    const seq = [];
    for (const key of ['ArrowRight', 'ArrowRight', 'ArrowRight', 'ArrowLeft', 'End', 'Home', 'ArrowDown', 'ArrowUp']) { await page.keyboard.press(key); seq.push([key, (await state('fx-tabs-1')).sel]); }
    check(JSON.stringify(seq.map(s => s[1])) === JSON.stringify([1, 2, 0, 2, 2, 0, 1, 0]), 'fx-tabs-1: arrows wrap, Home/End, focus follows selection', seq);
    const t2before = await state('fx-tabs-2');
    check(t2before.sel === 0, 'fx-tabs-1 keys do not change fx-tabs-2', t2before);
    await page.keyboard.press('ArrowRight');
    await page.keyboard.press('Tab');
    const intoPanel = await page.evaluate(() => { const a = document.activeElement; return { tag: a.tagName, text: a.textContent, inPanel: !!a.closest('[role=tabpanel]:not([hidden])') }; });
    check(intoPanel.inPanel && intoPanel.text === 'liên kết 2', 'Tab from tablist moves into the active panel link', intoPanel);
    await page.keyboard.press('Shift+Tab');
    check((await state('fx-tabs-1')).focus.startsWith('bs-tab-'), 'Shift+Tab returns to the selected tab', await state('fx-tabs-1'));
    const hiddenLinks = await page.evaluate(() => [...document.querySelectorAll('[role=tabpanel][hidden] a')].filter(a => a.offsetParent !== null).length);
    check(hiddenLinks === 0, 'links in hidden panels are not reachable', hiddenLinks);
    await page.hover('#fx-tabs-3 [role=tab]:nth-child(3)');
    check((await state('fx-tabs-3')).sel === 2, 'hover tabs switch on mouseenter', await state('fx-tabs-3'));
    // Viewport change keeps the selected tab and its content.
    await page.click('#fx-tabs-2 [role=tab]:nth-child(2)');
    await page.setViewportSize({ width: 375, height: 900 });
    await page.waitForTimeout(150);
    const after = await page.evaluate(() => { const p = document.querySelector('#fx-tabs-2 [role=tabpanel]:not([hidden])'); return { text: p && p.querySelector('h3').textContent, visible: p && p.offsetParent !== null, orientation: document.querySelector('#fx-tabs-2 > .bs-tabs__nav').getAttribute('aria-orientation'), dir: getComputedStyle(document.querySelector('#fx-tabs-2 > .bs-tabs__nav')).flexDirection }; });
    check(after.text === 'Panel 2' && after.visible, 'resize 1024→375 keeps selected panel visible', after);
    check(after.dir === 'column' ? after.orientation === 'vertical' : after.orientation !== 'vertical', 'aria-orientation follows layout after resize', after);
    await page.close();
  }

  // 3. Tabs with JavaScript off: all content readable, no false tab semantics.
  {
    const ctx = await browser.newContext({ javaScriptEnabled: false, viewport: { width: 375, height: 900 } });
    const page = await ctx.newPage();
    await page.goto(url);
    const nojs = await page.evaluate(() => ({
      visiblePanels: [...document.querySelectorAll('.bs-tab')].filter(p => p.offsetParent !== null).length,
      roles: document.querySelectorAll('.bs-tabs [role=tab], .bs-tabs [role=tablist], .bs-tabs [aria-selected]').length,
    }));
    check(nojs.visiblePanels === 9, 'JS off: all 9 panels visible', nojs);
    check(nojs.roles === 0, 'JS off: no tab/tablist/aria-selected without working tabs', nojs);
    await ctx.close();
  }

  // 4. Reduced motion: from start and when it changes.
  {
    const page = await browser.newPage({ viewport: { width: 1024, height: 900 }, reducedMotion: 'reduce' });
    await page.goto(url);
    await page.click('#fx-tabs-1 [role=tab]:nth-child(2)');
    const anim = await page.evaluate(() => ({ tab: getComputedStyle(document.querySelector('#fx-tabs-1 .bs-tab.is-active')).animationName, img: getComputedStyle(document.querySelector('.bs-banner__img')).transitionDuration }));
    check(anim.tab === 'none', 'reduce: tab panel switch has no animation', anim);
    check(/^0s/.test(anim.img), 'reduce: banner image zoom has no transition', anim);
    const pos = () => page.evaluate(() => document.querySelector('#fx-slider [data-bs-slider-track]').scrollLeft);
    const p0 = await pos(); await page.waitForTimeout(1800);
    check(await pos() === p0, 'reduce from start: slider does not autoplay', [p0, await pos()]);
    await page.close();

    const live = await browser.newPage({ viewport: { width: 1024, height: 900 }, reducedMotion: 'no-preference' });
    await live.goto(url);
    const lpos = () => live.evaluate(() => document.querySelector('#fx-slider [data-bs-slider-track]').scrollLeft);
    await live.waitForTimeout(1600);
    const moved = await lpos();
    check(moved > 0, 'no-preference: slider autoplays', moved);
    await live.emulateMedia({ reducedMotion: 'reduce' });
    await live.waitForTimeout(1000); // let a slide already in motion settle
    const a = await lpos(); await live.waitForTimeout(1800);
    check(await lpos() === a, 'switching to reduce at runtime stops autoplay', [a, await lpos()]);
    const readable = await live.evaluate(() => [...document.querySelectorAll('main h2, main h3, main p')].every(el => getComputedStyle(el).opacity === '1' || !el.offsetParent));
    check(readable, 'content stays readable (opacity 1) in reduce', readable);
    await live.close();
  }

  await browser.close();
  const env = { chromium: version, node: process.version, php: execFileSync('php', ['-r', 'echo PHP_VERSION;']).toString(), os: process.platform, wordpress: 'none (stubbed render.php)', theme: 'working tree' };
  fs.writeFileSync(path.join(out, 'report.json'), JSON.stringify({ env, results }, null, 1));
  const fails = results.filter(r => r.ok === false);
  for (const r of results.filter(r => !r.info)) console.log((r.ok ? 'ok   ' : 'FAIL ') + r.label + (r.ok ? '' : ' ' + JSON.stringify(r.detail)));
  console.log(`${results.filter(r => r.ok).length} ok, ${fails.length} fail; env ${JSON.stringify(env)}`);
  process.exit(fails.length ? 1 : 0);
})().catch(e => { console.error(e); process.exit(2); });
