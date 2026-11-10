"""Syntax checks shared by release.py and build-theme.py.

    python tools/lint.py        prints problems, exit code 1 when any
"""
import json
import shutil
import subprocess
import sys
from pathlib import Path

REPO = Path(__file__).resolve().parents[1]
THEME = REPO / 'bigspy-ai-theme-wp'
# Fallback PHP parser (php-parser) for machines without a php binary.
PHPLINT = REPO.parent / 'env' / 'phplint' / 'lint.js'


def php_problems(out, node):
	files = sorted(THEME.rglob('*.php'))
	php = shutil.which('php')
	if php:
		for f in files:
			r = subprocess.run([php, '-l', str(f)], capture_output=True, text=True)
			if r.returncode:
				out.append('PHP syntax: %s: %s' % (f.relative_to(THEME), (r.stdout + r.stderr).strip().splitlines()[0]))
		return
	if not node or not PHPLINT.exists():
		out.append('php not found and env/phplint/lint.js missing: cannot lint PHP files')
		return
	for i in range(0, len(files), 100):
		r = subprocess.run([node, str(PHPLINT)] + [str(f) for f in files[i:i + 100]], capture_output=True, text=True, encoding='utf-8', cwd=str(PHPLINT.parent))
		for line in r.stdout.splitlines():
			if line.startswith('ERR'):
				out.append('PHP syntax: ' + line[4:].replace(str(THEME) + '\\', '').replace(str(THEME) + '/', ''))


def problems():
	out = []
	node = shutil.which('node')
	php_problems(out, node)
	if not node:
		out.append('node not found: cannot check JS files')
	else:
		js = list((THEME / 'assets' / 'js').glob('*.js')) + list((THEME / 'ai-designer' / 'assets').glob('*.js'))
		for f in sorted(js):
			r = subprocess.run([node, '--check', str(f)], capture_output=True, text=True)
			if r.returncode:
				out.append('JS syntax: %s' % f.relative_to(THEME))
	for f in sorted(list(THEME.rglob('block.json')) + [THEME / 'theme.json']):
		try:
			json.loads(f.read_text(encoding='utf-8'))
		except ValueError as e:
			out.append('JSON: %s: %s' % (f.relative_to(THEME), e))
	return out


if __name__ == '__main__':
	found = problems()
	for p in found:
		print('PROBLEM: ' + p)
	print('Lint clean.' if not found else 'Lint failed.')
	sys.exit(1 if found else 0)
