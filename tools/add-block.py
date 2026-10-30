"""Scaffold a dynamic block: blocks/<slug>/, the registration list and the CSS family.

    python tools/add-block.py <slug> "<Title>" --prefix bs-x [--icon star] [--parent slug] [--dry-run]

The editor script, preview, CSS rules and translations stay manual; the script prints them.
"""
import argparse
import json
import re
from pathlib import Path

REPO = Path(__file__).resolve().parents[1]
THEME = REPO / 'bigspy-ai-theme-wp'
BLOCKS_PHP = THEME / 'inc' / 'blocks.php'
SPLITTER = REPO / 'tools' / 'split-blocks-css.py'


def read(path):
	return path.read_text(encoding='utf-8')


def write(path, text):
	path.write_text(text, encoding='utf-8', newline='\n')


def add_name(text, slug):
	match = re.search(r"(function bst_block_names\(\).*?return array\()(.*?)( \);)", text, re.S)
	if not match:
		raise SystemExit('bst_block_names() not found in inc/blocks.php')
	if "'%s'" % slug in match.group(2):
		raise SystemExit('Block already registered: ' + slug)
	return text[:match.end(2)] + ", '%s'" % slug + text[match.end(2):]


def add_family(text, slug, prefix):
	marker = 'FAMILIES = {\n'
	if marker not in text:
		raise SystemExit('FAMILIES not found in tools/split-blocks-css.py')
	return text.replace(marker, marker + "\t'%s': ['%s'],\n" % (slug, prefix), 1)


def main():
	parser = argparse.ArgumentParser()
	parser.add_argument('slug')
	parser.add_argument('title')
	parser.add_argument('--prefix', required=True, help='CSS class, e.g. bs-hreveal')
	parser.add_argument('--icon', default='screenoptions')
	parser.add_argument('--parent', help='parent block slug for a child block')
	parser.add_argument('--dry-run', action='store_true')
	args = parser.parse_args()
	if not re.fullmatch(r'[a-z][a-z0-9-]*', args.slug):
		raise SystemExit('Slug must use lowercase letters, digits and hyphens')
	if not re.fullmatch(r'bs-[a-z0-9-]+', args.prefix):
		raise SystemExit('Prefix must look like bs-name')
	folder = THEME / 'blocks' / args.slug
	if folder.exists():
		raise SystemExit('Already exists: %s' % folder)

	meta = {
		'$schema': 'https://schemas.wp.org/trunk/block.json',
		'apiVersion': 3,
		'name': 'bigspy/' + args.slug,
		'title': args.title,
		'category': 'bigspy',
		'icon': args.icon,
		'description': '',
		'textdomain': 'bigspy-ai-theme-wp',
	}
	if args.parent:
		meta['parent'] = ['bigspy/' + args.parent]
	meta['attributes'] = {}
	meta['supports'] = {'anchor': True, 'html': False}
	meta['render'] = 'file:./render.php'
	render = """<?php
/** %s block. */
defined( 'ABSPATH' ) || exit;
$a = $attributes;
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => '%s' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>><?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
""" % (args.title, args.prefix)

	blocks_php = add_name(read(BLOCKS_PHP), args.slug)
	splitter = add_family(read(SPLITTER), args.slug, args.prefix)
	if args.dry_run:
		print('Dry run OK: would create blocks/%s and update the two lists.' % args.slug)
		return
	folder.mkdir()
	write(folder / 'block.json', json.dumps(meta, indent='\t', ensure_ascii=False) + '\n')
	write(folder / 'render.php', render)
	write(BLOCKS_PHP, blocks_php)
	write(SPLITTER, splitter)
	print('Created blocks/%s; updated bst_block_names() and FAMILIES.' % args.slug)
	print('Still manual:')
	print('  1. Fill description, attributes in block.json and the output in render.php.')
	print("  2. wp.blocks.registerBlockType('bigspy/%s', ...) in assets/js/blocks-motion.js." % args.slug)
	print("  3. Add a preview and icon color in assets/js/block-previews.js.")
	print('  4. Add .%s rules to blocks.css, then run: python tools/split-blocks-css.py' % args.prefix)
	print('  5. Add strings: python tools/add-strings.py strings.json')
	print('  6. Add the block to ai-designer/knowledge/theme.md.')


if __name__ == '__main__':
	main()
