#!/usr/bin/env node
/**
 * G-70 AI Designer evaluation.
 *
 *   node tools/eval/eval.cjs score <runDir> [--cases=01,02] [--no-browser]
 *       Replay: score saved results (no model call, no cost). Writes
 *       <runDir>/report.json and report.md.
 *   node tools/eval/eval.cjs compare <runDir> <runDir> [...] [--out=file.md]
 *       Compare scored runs: markdown table to stdout (or --out) + compare.json.
 *   node tools/eval/eval.cjs live --site=https://site --user=admin --app-password="xxxx xxxx"
 *       --model=<id> --out=tools/eval/runs/<name> --yes [--cases=..] [--effort=medium]
 *       [--max-cost=5] [--turns=4] [--keep]
 *       Runs each case through the real AI Designer REST API on a WordPress site with
 *       the theme active and the API connected. COSTS MONEY: needs --yes and stops
 *       once the summed task cost reaches --max-cost (USD). Then scores the run.
 *   node tools/eval/eval.cjs list
 *       Print the cases.
 *   node tools/eval/eval.cjs selftest
 *       Score the hand-made fixtures: good ones must pass every check, each bad
 *       one must have a hard failure.
 *
 * A result file (<runDir>/<case>.json) holds: case, status, content (saved
 * post_content) or tree (model block tree, fixtures), rendered_html (live only),
 * log (AI Designer task log events), metrics (task metrics totals).
 */
'use strict';
const fs = require('fs');
const path = require('path');
const { execFileSync } = require('child_process');

const ROOT = __dirname;
const CASES = path.join(ROOT, 'cases');
const args = process.argv.slice(2);
const cmd = args[0];
const opt = Object.fromEntries(args.filter(a => a.startsWith('--')).map(a => { const [k, ...v] = a.slice(2).split('='); return [k, v.length ? v.join('=') : true]; }));
const pos = args.slice(1).filter(a => !a.startsWith('--'));

function loadCases(filter) {
  const all = fs.readdirSync(CASES).filter(f => f.endsWith('.json')).sort().map(f => JSON.parse(fs.readFileSync(path.join(CASES, f), 'utf8')));
  if (!filter) return all;
  const want = String(filter).split(',').map(s => s.trim()).filter(Boolean);
  return all.filter(c => want.some(w => c.id === w || c.id.startsWith(w + '-') || c.id.startsWith(w)));
}

function readJson(f, d = null) { try { return JSON.parse(fs.readFileSync(f, 'utf8')); } catch (e) { return d; } }
function writeJson(f, v) { fs.writeFileSync(f, JSON.stringify(v, null, 2) + '\n'); }

/* ------------------------------------------------------------- Scoring */

async function mobileCheck(browser, file) {
  const page = await browser.newPage({ viewport: { width: 375, height: 812 } });
  const errors = [];
  page.on('pageerror', e => errors.push(e.message));
  await page.goto('file://' + path.resolve(file));
  await page.waitForTimeout(100);
  const m = await page.evaluate(() => {
    const vw = document.documentElement.clientWidth;
    // Fine: inside a sideways scroller (table wrapper, code), a moving strip, or decoration.
    const exempt = el => {
      if (el.closest('.bs-slider__track,.bs-marquee,.bs-ticker,.bs-hscroll,[aria-hidden="true"]') || el.closest('svg') !== null && el.tagName.toLowerCase() !== 'svg') return true;
      for (let p = el; p && p !== document.body; p = p.parentElement) {
        const cs = getComputedStyle(p);
        if (['absolute', 'fixed'].includes(cs.position)) return true;
        if (p !== el && ['auto', 'scroll'].includes(cs.overflowX)) { const r = p.getBoundingClientRect(); if (r.right <= vw + 1 && r.left >= -1) return true; }
      }
      return false;
    };
    // Wider than the screen: the page pans sideways, or a parent with overflow hidden cuts it off.
    const over = [...document.querySelectorAll('main *')].filter(el => {
      const r = el.getBoundingClientRect();
      return r.width && (r.right > vw + 1 || r.left < -1) && !exempt(el);
    });
    const top = over.filter(el => !over.includes(el.parentElement)).slice(0, 5).map(el => {
      const r = el.getBoundingClientRect();
      const blk = el.closest('[class*="wp-block-"]');
      return `${el.tagName.toLowerCase()}${el.className && typeof el.className === 'string' ? '.' + el.className.trim().split(/\s+/).slice(0, 2).join('.') : ''} w=${Math.round(r.width)} right=${Math.round(r.right)}${blk ? ' in ' + (blk.className.match(/wp-block-[\w-]+/) || [''])[0] : ''}`;
    });
    return { vw, docOverflow: document.documentElement.scrollWidth - vw, count: over.length, over: top };
  });
  await page.close();
  return { ...m, errors };
}

function scoreOf(checks) {
  const total = checks.reduce((s, c) => s + c.weight, 0);
  const got = checks.reduce((s, c) => s + (c.pass ? c.weight : 0), 0);
  return total ? Math.round((100 * got) / total) : 0;
}

async function scoreRun(runDir) {
  const cases = loadCases(opt.cases);
  const htmlDir = path.join(runDir, '_html');
  fs.mkdirSync(htmlDir, { recursive: true });
  let browser = null;
  if (!opt['no-browser']) {
    try { browser = await require('playwright').chromium.launch(); } catch (e) { console.warn('Playwright unavailable, mobile check skipped: ' + e.message); }
  }
  const meta = readJson(path.join(runDir, 'run.json'), {});
  const rows = [];
  for (const c of cases) {
    const resFile = path.join(runDir, c.id + '.json');
    if (!fs.existsSync(resFile)) { rows.push({ case: c.id, industry: c.industry, status: 'not-run' }); continue; }
    const html = path.join(htmlDir, c.id + '.html');
    let s;
    try {
      s = JSON.parse(execFileSync('php', [path.join(ROOT, 'score.php'), path.join(CASES, c.id + '.json'), resFile, html], { encoding: 'utf8', stdio: ['ignore', 'pipe', 'pipe'] }));
    } catch (e) {
      rows.push({ case: c.id, industry: c.industry, status: 'score-error', error: String(e.stderr || e.message).slice(0, 400) });
      continue;
    }
    if (browser && fs.existsSync(html)) {
      const m = await mobileCheck(browser, html);
      s.mobile = m;
      s.checks.push({ id: 'mobile_375_no_overflow', pass: m.count === 0 && m.docOverflow <= 0, weight: 2, hard: true, detail: m.count ? `${m.count} element(s) wider than 375px: ${m.over.join('; ')}` : '' });
    }
    const res = readJson(resFile, {});
    s.status = res.status || 'done';
    s.industry = c.industry;
    s.score = scoreOf(s.checks);
    s.hard_fail = s.checks.filter(x => x.hard && !x.pass).map(x => x.id);
    s.failed = s.checks.filter(x => !x.pass).map(x => x.id);
    rows.push(s);
  }
  if (browser) await browser.close();
  const scored = rows.filter(r => r.score !== undefined);
  const sum = k => scored.reduce((a, r) => a + (Number(r.measures[k]) || 0), 0);
  const summary = {
    run: meta.run || path.basename(runDir), mode: meta.mode || '', model: meta.model || '', date: meta.date || '',
    cases: cases.length, scored: scored.length,
    avg_score: scored.length ? Math.round(scored.reduce((a, r) => a + r.score, 0) / scored.length) : 0,
    clean: scored.filter(r => !r.hard_fail.length).length,
    rejections: scored.reduce((a, r) => a + r.measures.rejections.length, 0),
    tool_calls: sum('tool_calls'), tokens_in: sum('tokens_in'), tokens_out: sum('tokens_out'),
    seconds: Math.round(sum('seconds') * 10) / 10, cost: Math.round(sum('cost') * 10000) / 10000,
  };
  const report = { summary, cases: rows };
  writeJson(path.join(runDir, 'report.json'), report);
  fs.writeFileSync(path.join(runDir, 'report.md'), reportMd(report));
  return report;
}

const cell = v => (v === null || v === undefined || v === '' ? '–' : String(v).replace(/\|/g, '\\|').replace(/\n/g, ' '));

function reportMd({ summary: s, cases }) {
  const out = [];
  out.push(`# AI Designer eval: ${s.run}`, '');
  out.push(`Mode: ${cell(s.mode)} · Model: ${cell(s.model)} · Date: ${cell(s.date)}`, '');
  out.push(`Scored ${s.scored}/${s.cases} cases · average score **${s.avg_score}** · no hard failure: ${s.clean}/${s.scored} · server refusals ${s.rejections} · tool calls ${s.tool_calls} · tokens ${s.tokens_in}/${s.tokens_out} · ${s.seconds}s · $${s.cost}`, '');
  out.push('| Case | Score | Hard failures | Editable % | Blocks | H1 | Invented | Refusals | Tools | Tokens in/out | Seconds | Cost | 375px |');
  out.push('| --- | ---: | --- | ---: | ---: | ---: | ---: | ---: | ---: | --- | ---: | ---: | --- |');
  for (const r of cases) {
    if (r.score === undefined) { out.push(`| ${r.case} | ${r.status} | ${cell(r.error)} |  |  |  |  |  |  |  |  |  |  |`); continue; }
    const m = r.measures;
    const mob = r.mobile ? (r.mobile.count ? `${r.mobile.count} over` : 'ok') : 'skipped';
    out.push(`| ${r.case} | ${r.score} | ${cell(r.hard_fail.join(', '))} | ${m.editable_percent} | ${m.blocks} | ${m.h1} | ${m.invented.length} | ${m.rejections.length} | ${m.tool_calls} | ${cell(m.tokens_in)}/${cell(m.tokens_out)} | ${cell(m.seconds)} | ${cell(m.cost)} | ${mob} |`);
  }
  out.push('', '## Failed checks', '');
  for (const r of cases) {
    if (!r.checks) continue;
    const bad = r.checks.filter(c => !c.pass);
    if (!bad.length) continue;
    out.push(`**${r.case}**`, '');
    for (const c of bad) out.push(`- ${c.hard ? '**' + c.id + '**' : c.id}: ${cell(c.detail)}`);
    if (r.render && r.render.source === 'isolated' && (r.render.failed || []).length) out.push(`- (render) ${r.render.failed.length} block(s) not rendered in isolation: ${cell(r.render.failed.slice(0, 3).join('; '))}`);
    out.push('');
  }
  return out.join('\n') + '\n';
}

/* ------------------------------------------------------------- Compare */

function compare(dirs) {
  const reps = dirs.map(d => ({ dir: d, rep: readJson(path.join(d, 'report.json')) }));
  const missing = reps.filter(r => !r.rep).map(r => r.dir);
  if (missing.length) { console.error('Score these runs first: ' + missing.join(', ')); process.exit(1); }
  const names = reps.map(r => r.rep.summary.run);
  const ids = [...new Set(reps.flatMap(r => r.rep.cases.map(c => c.case)))].sort();
  const get = (rep, id) => rep.cases.find(c => c.case === id) || {};
  const out = [`# AI Designer eval comparison`, '', `Runs: ${names.map((n, i) => `**${n}** (${cell(reps[i].rep.summary.model)})`).join(' vs ')}`, ''];
  out.push('| Metric | ' + names.join(' | ') + (names.length === 2 ? ' | Δ |' : ' |'));
  out.push('| --- | ' + names.map(() => '---:').join(' | ') + (names.length === 2 ? ' | ---: |' : ' |'));
  for (const [k, label] of [['avg_score', 'Average score'], ['clean', 'Cases without hard failure'], ['scored', 'Cases scored'], ['rejections', 'Server refusals'], ['tool_calls', 'Tool calls'], ['tokens_in', 'Tokens in'], ['tokens_out', 'Tokens out'], ['seconds', 'Seconds'], ['cost', 'Cost (USD)']]) {
    const v = reps.map(r => r.rep.summary[k]);
    const d = names.length === 2 && typeof v[0] === 'number' ? ' | ' + (Math.round((v[1] - v[0]) * 10000) / 10000) : (names.length === 2 ? ' | –' : '');
    out.push(`| ${label} | ${v.map(cell).join(' | ')}${d} |`);
  }
  out.push('', '| Case | ' + names.map(n => `${n} score`).join(' | ') + (names.length === 2 ? ' | Δ' : '') + ' | Hard failures |');
  out.push('| --- | ' + names.map(() => '---:').join(' | ') + (names.length === 2 ? ' | ---:' : '') + ' | --- |');
  const rows = [];
  for (const id of ids) {
    const cs = reps.map(r => get(r.rep, id));
    const sc = cs.map(c => (c.score === undefined ? (c.status || 'not-run') : c.score));
    const d = names.length === 2 && typeof sc[0] === 'number' && typeof sc[1] === 'number' ? ' | ' + (sc[1] - sc[0]) : (names.length === 2 ? ' | –' : '');
    out.push(`| ${id} | ${sc.join(' | ')}${d} | ${cs.map(c => cell((c.hard_fail || []).join(', '))).join(' / ')} |`);
    rows.push({ case: id, scores: sc, hard_fail: cs.map(c => c.hard_fail || null) });
  }
  const md = out.join('\n') + '\n';
  const json = { runs: reps.map(r => ({ dir: r.dir, summary: r.rep.summary })), cases: rows };
  if (opt.out) { fs.writeFileSync(opt.out, md); writeJson(String(opt.out).replace(/\.md$/, '') + '.json', json); } else { process.stdout.write(md); }
  return json;
}

/* ---------------------------------------------------------------- Live */

async function live() {
  const site = String(opt.site || '').replace(/\/$/, '');
  if (!site || !opt.user || !opt['app-password'] || !opt.out) {
    console.error('live needs --site --user --app-password --out (see header of tools/eval/eval.cjs)'); process.exit(2);
  }
  if (!opt.yes) {
    console.error('live calls the paid model. Re-run with --yes after the owner approved the budget; --max-cost caps the total (default 5 USD).'); process.exit(2);
  }
  const maxCost = Number(opt['max-cost'] || 5);
  const turns = Number(opt.turns || 4);
  const auth = 'Basic ' + Buffer.from(`${opt.user}:${opt['app-password']}`).toString('base64');
  const api = async (p, method = 'GET', body) => {
    const url = p.startsWith('/wp/') ? `${site}/wp-json${p}` : `${site}/wp-json/bst-aid/v1${p}`;
    for (let i = 0; i < 5; i++) {
      const r = await fetch(url, { method, headers: { Authorization: auth, 'Content-Type': 'application/json' }, body: body ? JSON.stringify(body) : undefined });
      const j = await r.json().catch(() => ({}));
      if (r.status === 409) { await new Promise(res => setTimeout(res, 2000)); continue; }
      if (!r.ok) throw new Error(`${method} ${p}: ${r.status} ${j.message || ''}`);
      return j;
    }
    throw new Error(`${method} ${p}: still busy`);
  };
  const out = String(opt.out);
  fs.mkdirSync(out, { recursive: true });
  writeJson(path.join(out, 'run.json'), { run: path.basename(out), mode: 'live', model: opt.model || '(site default)', effort: opt.effort || 'medium', site, date: new Date().toISOString() });
  let spent = 0;
  for (const c of loadCases(opt.cases)) {
    if (spent >= maxCost) { console.log(`Budget ${maxCost} USD reached; remaining cases skipped.`); break; }
    const t0 = Date.now();
    const task = await api('/tasks', 'POST', { title: `eval ${c.id}`, mode: 'design' });
    const id = task.id;
    const caseCap = Math.min(Number(c.limits?.cost_max || 1) * 1.5, maxCost - spent);
    let status = 'done';
    let text = c.prompt;
    try {
      for (let turn = 0; turn < turns && text; turn++) {
        await api(`/tasks/${id}/send`, 'POST', { text, effort: c.effort || opt.effort || 'medium', model: opt.model || '' });
        let state = 'running', approvals = [], busy = 0;
        while (state === 'running' || state === 'busy') {
          const r = await api(`/tasks/${id}/step`, 'POST');
          state = r.state; busy = state === 'busy' ? busy + 1 : 0;
          if (busy > 80) { status = 'stuck'; break; }
          if (state === 'busy') await new Promise(res => setTimeout(res, 1500));
          approvals.push(...(r.events || []).filter(e => e.kind === 'approval' && e.approval).map(e => e.approval.id));
          if ((r.task?.cost || 0) >= caseCap) { await api(`/tasks/${id}/stop`, 'POST'); status = 'budget'; break; }
        }
        if (status !== 'done') break;
        for (const a of approvals) await api(`/tasks/${id}/approval`, 'POST', { approval_id: a, decision: 'approve' }).catch(e => console.warn(e.message));
        const read = await api(`/tasks/${id}`);
        const saved = pageIds(read.log).length;
        const asked = (read.log || []).slice(-1)[0]?.kind === 'assistant' && /\?\s*$/.test((read.log || []).slice(-1)[0].text || '');
        text = approvals.length ? 'Đã duyệt. Tiếp tục phần còn lại.' : (!saved || asked ? (c.followup || 'Đồng ý với đề xuất của bạn, dùng đúng thông tin đã cho, làm luôn và lưu trang.') : '');
      }
    } catch (e) { status = 'error: ' + e.message; }
    const read = await api(`/tasks/${id}`);
    const ids = pageIds(read.log);
    let content = '', rendered = '';
    if (ids.length) {
      const pid = ids[ids.length - 1];
      const pg = await api(`/wp/v2/pages/${pid}?context=edit`).catch(() => api(`/wp/v2/posts/${pid}?context=edit`).catch(() => null));
      content = pg?.content?.raw || '';
      rendered = pg?.content?.rendered || '';
    }
    const total = read.task?.metrics?.total || {};
    const tools = {};
    for (const e of read.log || []) if (['tool', 'tool-error', 'approval'].includes(e.kind) && e.tool) tools[e.tool] = (tools[e.tool] || 0) + 1;
    const metrics = { ...total, tools, wall_seconds: Math.round((Date.now() - t0) / 100) / 10 };
    spent += Number(read.task?.cost || total.cost || 0);
    writeJson(path.join(out, c.id + '.json'), { case: c.id, status, task_id: id, page_ids: ids, content, rendered_html: rendered, log: read.log || [], metrics });
    console.log(`${c.id}: ${status}, ${metrics.tool_calls ?? '?'} tools, $${metrics.cost ?? '?'} (total $${Math.round(spent * 10000) / 10000})`);
    if (!opt.keep) await api(`/tasks/${id}/undo`, 'POST').catch(() => {});
  }
  const rep = await scoreRun(out);
  console.log(`Scored: average ${rep.summary.avg_score}, report ${path.join(out, 'report.md')}`);
}

function pageIds(log) {
  const ids = [];
  for (const e of log || []) {
    if (!['create_page', 'update_page', 'page_sections'].includes(e.tool) || e.kind !== 'tool') continue;
    for (const l of e.links || []) { const m = String(l.edit || '').match(/[?&]post=(\d+)/); if (m && !ids.includes(+m[1])) ids.push(+m[1]); }
  }
  return ids;
}

/* ---------------------------------------------------------------- Main */

(async () => {
  if (cmd === 'score' && pos[0]) {
    const rep = await scoreRun(pos[0]);
    process.stdout.write(reportMd(rep));
  } else if (cmd === 'compare' && pos.length >= 2) {
    compare(pos);
  } else if (cmd === 'live') {
    await live();
  } else if (cmd === 'selftest') {
    const good = await scoreRun(path.join(ROOT, 'fixtures', 'good'));
    const bad = await scoreRun(path.join(ROOT, 'fixtures', 'bad'));
    const problems = [];
    for (const r of good.cases.filter(c => c.score !== undefined)) if (r.failed.length) problems.push(`good ${r.case} failed ${r.failed.join(', ')}`);
    for (const r of bad.cases.filter(c => c.score !== undefined)) if (!r.hard_fail.length) problems.push(`bad ${r.case} has no hard failure`);
    for (const r of [...good.cases, ...bad.cases]) if (r.status === 'score-error') problems.push(`${r.case}: ${r.error}`);
    if (!good.summary.scored || !bad.summary.scored) problems.push('no fixture scored');
    if (opt['no-browser'] === undefined && !good.cases.some(c => c.mobile)) problems.push('375px check did not run');
    console.log(`good: ${good.summary.scored} cases, avg ${good.summary.avg_score}; bad: ${bad.summary.scored} cases, avg ${bad.summary.avg_score}, hard failures in ${bad.summary.scored - bad.summary.clean}`);
    if (problems.length) { console.error(problems.join('\n')); process.exit(1); }
    console.log('selftest ok');
  } else if (cmd === 'list') {
    for (const c of loadCases(opt.cases)) console.log(`${c.id}  [${c.industry}]  ${c.prompt.slice(0, 90)}…`);
  } else {
    console.log(fs.readFileSync(__filename, 'utf8').split('\n').slice(2, 22).join('\n'));
    process.exit(cmd ? 2 : 0);
  }
})().catch(e => { console.error(e); process.exit(1); });
