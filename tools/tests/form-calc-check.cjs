/* G-58: run the shared formula fixtures against the browser calculator in assets/js/form.js. */
const fs = require('fs'), path = require('path');
const src = fs.readFileSync(path.join(__dirname, '../../bigspy-ai-theme-wp/assets/js/form.js'), 'utf8');
const start = src.indexOf('var bstCalc = '), end = src.indexOf('\n(function () {\n\tif (typeof document', start);
const bstCalc = new Function(src.slice(start, end) + '\nreturn bstCalc;')();
let fail = 0;
for (const c of JSON.parse(fs.readFileSync(path.join(__dirname, 'form-calc-fixtures.json'), 'utf8'))) {
  const seen = c.seen || Object.fromEntries(c.fields.map(f => [f.id, true]));
  const q = bstCalc.quote(c.fields, c.values, seen, c.cur);
  const bad = Object.entries(c.expect).filter(([id, w]) => !q.lines[id] || q.lines[id][0] !== w[0] || Math.abs(q.lines[id][1] - w[1]) > 1e-9);
  bad.forEach(([id, w]) => console.log('  ' + id, 'got', JSON.stringify(q.lines[id]), 'want', JSON.stringify(w)));
  console.log((bad.length ? 'FAIL ' : 'ok   ') + c.name); fail += bad.length ? 1 : 0;
}
const m = bstCalc.money(1234567, { code: 'VND', decimals: 0 }) === '1.234.567 ₫' && bstCalc.money(1234.5, { code: 'USD', decimals: 2 }) === '1,234.50 USD';
console.log((m ? 'ok   ' : 'FAIL ') + 'money text VND/USD'); fail += m ? 0 : 1;
console.log(fail ? `\n${fail} failed` : '\nall passed'); process.exit(fail ? 1 : 0);
