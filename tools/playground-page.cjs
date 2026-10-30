/* Serve the working theme in Playground for browser checks. Log in by opening /?bst_autologin=1.
   Options: --port=9418 --woo=<unpacked plugin dir>. Uses the same runtime as playground-check.cjs. */
const path = require('path');
const base = process.env.BST_PLAYGROUND_MODULES || 'C:/Users/DELL/AppData/Local/Temp/claude/D--BIGSPY-AI-SEO/8d5a7c15-c757-46a3-83f5-2a433e0415e4/scratchpad/pg/node_modules';
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
  const mount = [
    { hostPath: path.join(repo, 'bigspy-ai-theme-wp'), vfsPath: '/wordpress/wp-content/themes/bigspy-ai-theme-wp' },
    { hostPath: path.join(__dirname, 'playground/mu-autologin.php'), vfsPath: '/wordpress/wp-content/mu-plugins/bst-autologin.php' },
  ];
  if (woo) mount.push({ hostPath: woo, vfsPath: '/wordpress/wp-content/plugins/woocommerce' });
  const steps = [{ step: 'activateTheme', themeFolderName: 'bigspy-ai-theme-wp' }];
  if (woo) steps.push({ step: 'activatePlugin', pluginPath: 'woocommerce/woocommerce.php' });
  const server = await runCLI({ command: 'server', port: Number(arg('port')) || 9418, php: '8.3', wp: process.env.BST_TEST_WP || '7.1.3', workers: 2,
    mount, blueprint: { steps }, skipBrowser: true, verbosity: 'quiet' });
  console.log('PLAYGROUND_READY', `${server.serverUrl}/?bst_autologin=1`);
})().catch(e => { console.error(e.messages || e.message); process.exit(1); });
