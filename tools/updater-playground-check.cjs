/* Run an isolated real WordPress theme upgrade against a local release ZIP. */
const fs = require('fs');
const path = require('path');
const cp = require('child_process');
const root = path.resolve(__dirname, '..');
const output = path.join(root, 'output', 'updater-test');
const fixture = path.join(output, 'old', 'bigspy-ai-theme-wp');
const source = path.join(root, 'bigspy-ai-theme-wp');
fs.mkdirSync(path.dirname(fixture), { recursive: true });
if (path.resolve(fixture) !== path.join(path.resolve(output), 'old', 'bigspy-ai-theme-wp')) throw new Error('Unsafe fixture path');
fs.rmSync(fixture, { recursive: true, force: true });
fs.cpSync(source, fixture, { recursive: true });
const script = `import pathlib, zipfile\nbase=pathlib.Path(r'${source.replace(/\\/g, '/')}')\nout=pathlib.Path(r'${fixture.replace(/\\/g, '/')}')/'bst-test-update.zip'\nwith zipfile.ZipFile(out,'w',zipfile.ZIP_DEFLATED) as z:\n for p in base.rglob('*'):\n  if p.is_file():\n   name='bigspy-ai-theme-wp/'+p.relative_to(base).as_posix()\n   data=p.read_bytes()\n   if name=='bigspy-ai-theme-wp/style.css': data=data.replace(b'Version: 1.73.3',b'Version: 1.73.4',1)\n   z.writestr(name,data)\n`;
cp.execFileSync('python', ['-c', script], { stdio: 'inherit' });
const modules = process.env.BST_PLAYGROUND_MODULES || 'C:/Users/DELL/AppData/Local/Temp/claude/D--BIGSPY-AI-SEO/8d5a7c15-c757-46a3-83f5-2a433e0415e4/scratchpad/pg/node_modules';
const nativeFetch = global.fetch;
global.fetch = async (input, options) => {
  const url = String(input.url || input);
  if (url.includes('api.wordpress.org/core/version-check')) return Response.json({ offers: [{ response: 'autoupdate', version: '7.1.3', download: 'https://wordpress.org/wordpress-7.1.3.zip' }] });
  return nativeFetch(input, options);
};
const { runCLI } = require(path.join(modules, '@wp-playground/cli'));
(async () => {
  const code = fs.readFileSync(path.join(__dirname, 'tests', 'updater-integration.php'), 'utf8');
  const installFixture = `<?php
$from='/wordpress/wp-content/bst-updater-source';
$to='/wordpress/wp-content/themes/bigspy-ai-theme-wp';
mkdir($to,0777,true);
$files=new RecursiveIteratorIterator(new RecursiveDirectoryIterator($from,FilesystemIterator::SKIP_DOTS),RecursiveIteratorIterator::SELF_FIRST);
foreach($files as $file){$dest=$to.'/'.substr($file->getPathname(),strlen($from)+1);if($file->isDir()){if(!is_dir($dest))mkdir($dest,0777,true);}else{copy($file->getPathname(),$dest);}}
`;
  const server = await runCLI({ command: 'server', port: 9418, php: '8.3', wp: '7.1.3', workers: 4,
    mount: [{ hostPath: fixture, vfsPath: '/wordpress/wp-content/bst-updater-source' }],
    blueprint: { steps: [{ step: 'runPHP', code: installFixture }, { step: 'activateTheme', themeFolderName: 'bigspy-ai-theme-wp' }, { step: 'runPHP', code }] },
    login: true, skipBrowser: true, verbosity: 'normal' });
  console.log('PLAYGROUND_READY', server.serverUrl);
  const results = JSON.parse(fs.readFileSync(path.join(fixture, 'updater-results.json'), 'utf8'));
  if (results.length !== 14) throw new Error('Incomplete updater checks');
  console.log(JSON.stringify({ passed: results.length, checks: results }, null, 2));
  await server[Symbol.asyncDispose]();
})().catch(e => { console.error(e.messages || e.stack || e); process.exit(1); });
