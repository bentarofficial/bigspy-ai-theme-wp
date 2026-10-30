/* Run tools/tests/auto-detect-integration.php in Playground; results land in the result file. */
const fs = require('fs');
const os = require('os');
const path = require('path');
const base = process.env.BST_PLAYGROUND_MODULES || 'C:/Users/DELL/AppData/Local/Temp/claude/D--BIGSPY-AI-SEO/8d5a7c15-c757-46a3-83f5-2a433e0415e4/scratchpad/pg/node_modules';
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
  const out = fs.mkdtempSync(path.join(os.tmpdir(), 'bst-ad-'));
  const code = fs.readFileSync(path.join(__dirname, 'tests/auto-detect-integration.php'), 'utf8');
  const server = await runCLI({ command: 'server', port: 9420, php: '8.3', wp: process.env.BST_TEST_WP || '7.1.3', workers: 2,
    mount: [{ hostPath: path.join(repo, 'bigspy-ai-theme-wp'), vfsPath: '/wordpress/wp-content/themes/bigspy-ai-theme-wp' }, { hostPath: out, vfsPath: '/adt' }],
    blueprint: { steps: [{ step: 'activateTheme', themeFolderName: 'bigspy-ai-theme-wp' }, { step: 'runPHP', code }] },
    skipBrowser: true, verbosity: 'quiet' });
  console.log(fs.readFileSync(path.join(out, 'result.txt'), 'utf8'));
  await server[Symbol.asyncDispose]();
  process.exit(0);
})().catch(e => { console.error(e.messages || e.message); process.exit(1); });
