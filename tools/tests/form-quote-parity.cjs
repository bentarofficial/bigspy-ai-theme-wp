/* G-58: the browser preview (form-quote.js) and the server (form-quote.php) give the same quote.
   node tools/tests/form-quote-parity.cjs  (needs php on PATH) */
const fs = require('fs'), path = require('path'), { execFileSync } = require('child_process');
const theme = path.resolve(__dirname, '../../bigspy-ai-theme-wp');
global.window = {};
eval(fs.readFileSync(path.join(theme, 'assets/js/form-quote.js'), 'utf8'));
const Q = window.bsQuote;
const fields = [
  { id: 'f_kg', type: 'number', name: 'kg', required: true },
  { id: 'f_ship', type: 'checkbox', name: 'ship', options: 'A\nB', prices: 'A = 25000\nB = 1.234,5' },
  { id: 'f_speed', type: 'radio', name: 'speed', options: 'Slow\nFast' },
];
const exprs = ['{f_kg} * 18000', '{l_a} * 0.2 * {f_speed=Fast}', '{f_ship}', 'max({f_kg} * 1.005, 2.5)', 'round({f_kg} / 3, 2)', 'if({f_kg} >= 10, -min(50, {f_kg}), 0) + ceil({f_kg} / 4) + floor(1.999)', '100 / ({f_kg} - 3)', '1 +', '{f_kg} * 1e3'];
const inputs = [{ kg: '3', ship: 'A', speed: 'Fast' }, { kg: '1,5', ship: 'A, B', speed: 'Slow' }, { kg: '12' }, { kg: '' }, { kg: '-1' }, { kg: '99999999999' }, { kg: '0' }];
const cases = [];
for (const decimals of [0, 2]) for (const e of exprs) for (const v of inputs) {
  cases.push({ pricing: { enabled: true, decimals, symbol: '₫', lines: [{ id: 'l_a', label: 'A', expr: '{f_kg} * 18000' }, { id: 'l_b', label: 'B', expr: e }] }, values: v });
}
const php = `<?php define('ABSPATH',1); function sanitize_key($k){return preg_replace('/[^a-z0-9_\\-]/','',strtolower((string)$k));} function wp_json_encode($v){return json_encode($v);} function __($s){return $s;}
preg_match('/function bst_native_field_options\\(.*?\\n}\\n/s', file_get_contents('${theme}/inc/form-native.php'), $m); eval($m[0]);
require '${theme}/inc/form-schema.php'; require '${theme}/inc/form-quote.php';
$in = json_decode(stream_get_contents(STDIN), true); $out = array();
foreach ($in['cases'] as $c) { $q = bst_quote_compute($c['pricing'], $in['fields'], $c['values']); $out[] = array($q['status'], $q['total'], array_column($q['lines'], 'amount')); }
echo json_encode($out);`;
const tmp = path.join(require('os').tmpdir(), 'bst-quote-parity.php');
fs.writeFileSync(tmp, php);
const server = JSON.parse(execFileSync('php', [tmp], { input: JSON.stringify({ cases, fields }) }));
let fail = 0;
cases.forEach((c, i) => {
  const errs = []; try { Q.parse(c.pricing.lines[1].expr); } catch (e) { errs.push(e); }
  const cfg = { ok: !errs.length, lines: c.pricing.lines, symbol: '₫', decimals: c.pricing.decimals, types: {}, prices: {} };
  fields.forEach(f => { cfg.types[f.id] = f.type; cfg.prices[f.id] = {}; String(f.prices || '').split('\n').forEach(r => { const at = r.lastIndexOf('='); if (at > 0) cfg.prices[f.id][r.slice(0, at).trim()] = Q.number(r.slice(at + 1)); }); });
  const vis = { f_kg: true, f_ship: true, f_speed: true }, name = { f_kg: 'kg', f_ship: 'ship', f_speed: 'speed' };
  const q = Q.compute(cfg, { visible: id => vis[id], required: id => id === 'f_kg', value: id => String(c.values[name[id]] || '') });
  const mine = [q.status, q.total === undefined ? 0 : q.total, (q.lines || []).map(l => l.amount)];
  if (JSON.stringify(mine) !== JSON.stringify(server[i]).replace(/\.0\b/g, '')) { fail++; console.log('FAIL', c.pricing.lines[1].expr, JSON.stringify(c.values), c.pricing.decimals, JSON.stringify(mine), JSON.stringify(server[i])); }
});
console.log(`${cases.length - fail}/${cases.length} same quote in browser and server`);
process.exit(fail ? 1 : 0);
