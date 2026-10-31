/* Run real WordPress tests using the locally installed Playground CLI 3.1.60. */
const fs = require('fs');
const path = require('path');
const base = process.env.BST_PLAYGROUND_MODULES || 'C:/Users/DELL/AppData/Local/Temp/claude/D--BIGSPY-AI-SEO/8d5a7c15-c757-46a3-83f5-2a433e0415e4/scratchpad/pg/node_modules';
// Offline boot from the already downloaded archive; no live version API is needed.
const nativeFetch = global.fetch;
global.fetch = async (input, options) => {
  const url = String(input.url || input);
  if (url.includes('api.wordpress.org/core/version-check')) {
    return Response.json({ offers: [{ response: 'autoupdate', version: '7.1.3', download: 'https://wordpress.org/wordpress-7.1.3.zip' }] });
  }
  return nativeFetch(input, options);
};
const { runCLI } = require(path.join(base, '@wp-playground/cli'));
(async () => {
  const repo = path.resolve(__dirname, '..');
  const code = fs.readFileSync(path.join(__dirname, 'tests/designer-integration.php'), 'utf8');
  const workers = Number(process.env.BST_TEST_WORKERS || 1);
  if (!Number.isInteger(workers) || workers < 1 || workers > 6) { throw new Error('BST_TEST_WORKERS must be an integer from 1 to 6.'); }
  const server = await runCLI({ command: 'server', port: 9417, php: '8.3', wp: process.env.BST_TEST_WP || '7.1.3', workers,
    mount: [{ hostPath: path.join(repo, 'bigspy-ai-theme-wp'), vfsPath: '/wordpress/wp-content/themes/bigspy-ai-theme-wp' }],
    blueprint: { steps: [ { step: 'activateTheme', themeFolderName: 'bigspy-ai-theme-wp' }, { step: 'runPHP', code }, ...(process.argv.includes('--welcome') ? [{ step: 'runPHP', code: fs.readFileSync(path.join(__dirname, 'tests/welcome-integration.php'), 'utf8') }] : []) ] },
    login: true, skipBrowser: true, verbosity: 'normal' });
  try {
    const response = await nativeFetch(new URL('/wp-content/uploads/bst-verification.json', server.serverUrl));
    if (!response.ok) { throw new Error('Designer verification results missing: HTTP ' + response.status); }
    const result = await response.json();
    if (!Array.isArray(result.checks) || !result.checks.length || result.checks.some(check => check.pass !== true)) {
      throw new Error('Designer integration checks failed: ' + JSON.stringify(result));
    }
    console.log('DESIGNER_CHECKS', result.checks.length, 'passed');
    for (const check of result.checks) { console.log('PASS', check.test); }
    console.log('PLAYGROUND_READY', server.serverUrl);
  } finally {
    if (!process.argv.includes('--serve')) { await server[Symbol.asyncDispose](); }
  }
})().catch(e => { console.error(e.messages || e.message); process.exit(1); });
