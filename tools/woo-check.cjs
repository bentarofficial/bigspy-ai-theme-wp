/* Run tools/tests/woo-integration.php in Playground with WooCommerce active.
   Usage: node tools/woo-check.cjs --woo=<unpacked woocommerce plugin dir> */
const fs = require('fs');
const os = require('os');
const path = require('path');
const base = require('./lib/env.cjs').modules();
const arg = name => (process.argv.find(a => a.startsWith(`--${name}=`)) || '').split('=').slice(1).join('=');
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
  const woo = arg('woo');
  if (!woo) throw new Error('Pass --woo=<unpacked woocommerce plugin dir>');
  const out = fs.mkdtempSync(path.join(os.tmpdir(), 'bst-woo-'));
  const code = fs.readFileSync(path.join(__dirname, 'tests/woo-integration.php'), 'utf8');
  const server = await runCLI({ command: 'server', port: 9422, php: '8.3', wp: process.env.BST_TEST_WP || '7.1.3', workers: 1,
    mount: [
      { hostPath: path.join(repo, 'bigspy-ai-theme-wp'), vfsPath: '/wordpress/wp-content/themes/bigspy-ai-theme-wp' },
      { hostPath: woo, vfsPath: '/wordpress/wp-content/plugins/woocommerce' },
      { hostPath: out, vfsPath: '/ft' },
    ],
    blueprint: { steps: [
      { step: 'activateTheme', themeFolderName: 'bigspy-ai-theme-wp' },
      { step: 'activatePlugin', pluginPath: 'woocommerce/woocommerce.php' },
      { step: 'runPHP', code },
    ] }, skipBrowser: true, verbosity: 'quiet' });
  const result = fs.readFileSync(path.join(out, 'result.txt'), 'utf8');
  console.log(result);
  await server[Symbol.asyncDispose]();
  process.exit(/FAIL/.test(result) ? 1 : 0);
})().catch(e => { console.error(e.messages || e.message); process.exit(1); });
