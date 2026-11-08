/* G-58: the browser preview (assets/js/form-calc.js) and the server (inc/form-calc.php) give the
 * same totals for tools/tests/form-calc/cases.json. Needs `php` on PATH (or BST_PHP). Run: node tools/form-calc-parity.cjs */
const { execFileSync } = require('child_process');
const path = require('path');
const t = path.join(__dirname, 'tests', 'form-calc');
const theme = path.join(__dirname, '..', 'bigspy-ai-theme-wp');
const php = execFileSync(process.env.BST_PHP || 'php', [path.join(t, 'run.php'), path.join(theme, 'inc', 'form-calc.php'), path.join(t, 'cases.json')], { encoding: 'utf8' });
const js = execFileSync(process.execPath, [path.join(t, 'run.cjs'), path.join(theme, 'assets', 'js', 'form-calc.js'), path.join(t, 'cases.json')], { encoding: 'utf8' });
if (php.trim() !== js.trim()) { console.error('PHP:\n' + php + '\nJS:\n' + js); process.exit(1); }
const need = ['ok:79.000 ₫', 'ok:89.800 ₫'];
if (need.some(n => !php.includes(n))) { console.error('Demo totals missing:\n' + php); process.exit(1); }
console.log('FORM_CALC_PARITY', php.trim().split('\n').length, 'cases match');
