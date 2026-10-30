"""Check release readiness; prepare a release only with explicit owner approval.

    python tools/release.py                                       read-only checks
    python tools/release.py --version 1.77.0 --owner-approved     bump version, build and verify the ZIP

It never tags, pushes, uploads, deploys or deletes anything. Follow the release
section of docs/DEV-RULES.md for those steps.
"""
import argparse
import re
import subprocess
import sys
from pathlib import Path

REPO = Path(__file__).resolve().parents[1]
THEME = REPO / 'bigspy-ai-theme-wp'
STYLE = THEME / 'style.css'
FUNCTIONS = THEME / 'functions.php'
CSS = THEME / 'assets' / 'css' / 'blocks.css'
SEGMENTS = THEME / 'assets' / 'css' / 'blocks' / 'segments.json'


def git(*args):
	return subprocess.run(['git', *args], cwd=REPO, capture_output=True, text=True, encoding='utf-8').stdout.strip()


def versions():
	style = re.search(r'^Version:\s*([\d.]+)', STYLE.read_text(encoding='utf-8'), re.M).group(1)
	code = re.search(r"define\( 'BST_VERSION', '([\d.]+)' \);", FUNCTIONS.read_text(encoding='utf-8')).group(1)
	return style, code


def check():
	problems = []
	style, code = versions()
	print('Version: style.css %s, BST_VERSION %s' % (style, code))
	print('Branch: %s' % git('branch', '--show-current'))
	if style != code:
		problems.append('Version mismatch')
	dirty = git('status', '--porcelain', '--untracked-files=no')
	if dirty:
		problems.append('Uncommitted tracked changes')
	if git('log', '-1', '--format=%ct', '--', str(CSS)) > git('log', '-1', '--format=%ct', '--', str(SEGMENTS)):
		problems.append('segments.json is older than blocks.css; run tools/split-blocks-css.py')
	if 'Chưa có thay đổi sản phẩm mới' in (REPO / 'docs' / 'PENDING-RELEASE.md').read_text(encoding='utf-8'):
		print('Note: PENDING-RELEASE.md reports no pending product changes.')
	for problem in problems:
		print('PROBLEM: ' + problem)
	print('Ready.' if not problems else 'Not ready.')
	return not problems


def bump(path, pattern, replacement):
	text, count = re.subn(pattern, replacement, path.read_text(encoding='utf-8'), count=1, flags=re.M)
	if count != 1:
		raise SystemExit('Version not found in ' + path.name)
	path.write_text(text, encoding='utf-8', newline='\n')


def main():
	parser = argparse.ArgumentParser()
	parser.add_argument('--version')
	parser.add_argument('--owner-approved', action='store_true')
	args = parser.parse_args()
	if bool(args.version) != args.owner_approved:
		raise SystemExit('--version and --owner-approved must be used together')
	ready = check()
	if not args.version:
		sys.exit(0 if ready else 1)
	if not ready:
		raise SystemExit('Fix the problems first.')
	if not re.fullmatch(r'\d+\.\d+\.\d+', args.version):
		raise SystemExit('Version must look like 1.77.0')
	bump(STYLE, r'^Version:\s*[\d.]+', 'Version: ' + args.version)
	bump(FUNCTIONS, r"define\( 'BST_VERSION', '[\d.]+' \);", "define( 'BST_VERSION', '%s' );" % args.version)
	subprocess.run([sys.executable, str(REPO / 'tools' / 'build-theme.py')], check=True)
	print('Built %s locally. Update readme.txt and About if needed, then continue with docs/DEV-RULES.md.' % args.version)


if __name__ == '__main__':
	main()
