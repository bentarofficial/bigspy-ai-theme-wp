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
  const server = await runCLI({ command: 'server', port: 9417, php: '8.3', wp: process.env.BST_TEST_WP || '7.1.3', workers: 6,
    mount: [{ hostPath: path.join(repo, 'bigspy-ai-theme-wp'), vfsPath: '/wordpress/wp-content/themes/bigspy-ai-theme-wp' }],
    blueprint: { steps: [ { step: 'activateTheme', themeFolderName: 'bigspy-ai-theme-wp' }, { step: 'runPHP', code } ] },
    login: true, skipBrowser: true, verbosity: 'normal' });
  console.log('PLAYGROUND_READY', server.serverUrl);
  if (!process.argv.includes('--serve')) { await server[Symbol.asyncDispose](); }
})().catch(e => { console.error(e.messages || e.message); process.exit(1); });
