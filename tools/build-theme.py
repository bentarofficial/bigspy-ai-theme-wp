"""Build and verify the installable theme ZIP without removing older local archives."""
from pathlib import Path
import hashlib
import json
import re
import zipfile

repo = Path(__file__).resolve().parents[1]
theme = repo / 'bigspy-ai-theme-wp'
version = re.search(r'^Version:\s*([\d.]+)', (theme / 'style.css').read_text(encoding='utf-8'), re.M).group(1)
assert f"define( 'BST_VERSION', '{version}' );" in (theme / 'functions.php').read_text(encoding='utf-8'), 'Version mismatch'
files = sorted(p for p in theme.rglob('*') if p.is_file() and p.name not in ('.DS_Store', 'Thumbs.db'))
output = repo / 'releases'
output.mkdir(exist_ok=True)
archive = output / f'bigspy-ai-theme-wp-{version}.zip'
temporary = output / f'bigspy-ai-theme-wp-{version}.tmp'
with zipfile.ZipFile(temporary, 'w', zipfile.ZIP_DEFLATED, compresslevel=9) as z:
    for file in files:
        z.write(file, 'bigspy-ai-theme-wp/' + file.relative_to(theme).as_posix())
with zipfile.ZipFile(temporary) as z:
    assert z.testzip() is None, 'ZIP CRC failure'
    assert len(z.infolist()) == len(files)
    assert all(n.startswith('bigspy-ai-theme-wp/') and '\\' not in n and '..' not in n.split('/') for n in z.namelist())
    assert f'Version: {version}' in z.read('bigspy-ai-theme-wp/style.css').decode('utf-8')
    for name in ('ticker', 'ticker-slide', 'expand-gallery', 'expand-panel'):
        assert json.loads(z.read(f'bigspy-ai-theme-wp/blocks/{name}/block.json'))['name'] == 'bigspy/' + name
    assert json.loads(z.read('bigspy-ai-theme-wp/assets/css/blocks/segments.json'))
    bootstrap = z.read('bigspy-ai-theme-wp/functions.php').decode('utf-8')
    for include in re.findall(r"'(inc/[^']+\.php)'", bootstrap):
        assert 'bigspy-ai-theme-wp/' + include in z.namelist(), f'Missing bootstrap include: {include}'
    # Fail instead of publishing a mixed snapshot if another editor changes the theme.
    current = sorted(p for p in theme.rglob('*') if p.is_file() and p.name not in ('.DS_Store', 'Thumbs.db'))
    assert current == files, 'Theme files changed during packaging; retry after editing finishes'
    for file in files:
        assert z.read('bigspy-ai-theme-wp/' + file.relative_to(theme).as_posix()) == file.read_bytes(), f'File changed during packaging: {file}'
temporary.replace(archive)
digest = hashlib.sha256(archive.read_bytes()).hexdigest()
(output / f'bigspy-ai-theme-wp-{version}.sha256').write_text(f'{digest}  {archive.name}\n', encoding='ascii')
print(json.dumps({'archive': str(archive), 'version': version, 'files': len(files), 'bytes': archive.stat().st_size, 'sha256': digest}, indent=2))
