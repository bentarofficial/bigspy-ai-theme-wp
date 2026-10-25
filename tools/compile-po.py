"""Compile languages/<locale>.po into .mo and .l10n.php (simple PO: no context, no plurals).

vi.po is the source of the Vietnamese translation; edit it, then run:
    python tools/compile-po.py vi
(tools/build-translations.js only knows an old subset of strings; do not use it for vi.)
"""
import json
import os
import struct
import sys

LANG = os.path.join(os.path.dirname(os.path.abspath(__file__)), '..', 'bigspy-ai-theme-wp', 'languages')


def unq(s):
	return json.loads(s)


def parse(path):
	entries, key, cur = [], None, {}
	for raw in open(path, encoding='utf-8'):
		line = raw.strip()
		if not line or line.startswith('#'):
			continue
		if line.startswith('msgid '):
			if 'msgstr' in cur:
				entries.append((cur['msgid'], cur['msgstr']))
			cur, key = {'msgid': unq(line[6:])}, 'msgid'
		elif line.startswith('msgstr '):
			cur['msgstr'], key = unq(line[7:]), 'msgstr'
		elif line.startswith('"') and key:
			cur[key] += unq(line)
		else:
			raise SystemExit('unsupported line: ' + line)
	if 'msgstr' in cur:
		entries.append((cur['msgid'], cur['msgstr']))
	return entries


def phpq(s):
	return "'" + s.replace('\\', '\\\\').replace("'", "\\'") + "'"


def main(loc):
	entries = parse(os.path.join(LANG, loc + '.po'))
	header = dict(entries).get('', '')
	tr = {k: v for k, v in entries if k and v}
	plural = next((l.split(':', 1)[1].strip() for l in header.split('\n') if l.startswith('Plural-Forms:')), 'nplurals=2; plural=(n != 1);')
	items = [('', header)] + sorted(tr.items())
	ids = strs = b''
	offs = []
	for k, v in items:
		kb, vb = k.encode('utf-8'), v.encode('utf-8')
		offs.append((len(ids), len(kb), len(strs), len(vb)))
		ids += kb + b'\0'
		strs += vb + b'\0'
	n = len(items)
	ks = 28 + 16 * n
	vs = ks + len(ids)
	ko, vo = [], []
	for o1, l1, o2, l2 in offs:
		ko += [l1, o1 + ks]
		vo += [l2, o2 + vs]
	mo = struct.pack('Iiiiiii', 0x950412de, 0, n, 28, 28 + n * 8, 0, 0) + struct.pack('%di' % len(ko), *ko) + struct.pack('%di' % len(vo), *vo) + ids + strs
	open(os.path.join(LANG, loc + '.mo'), 'wb').write(mo)
	lines = ['<?php', 'return array(', "\t'domain' => 'bigspy-ai-theme-wp',", "\t'plural-forms' => %s," % phpq(plural), "\t'language' => %s," % phpq(loc), "\t'messages' => array("]
	for k in sorted(tr):
		lines.append('\t\t%s => %s,' % (phpq(k), phpq(tr[k])))
	lines += ['\t),', ');', '']
	open(os.path.join(LANG, loc + '.l10n.php'), 'w', encoding='utf-8', newline='\n').write('\n'.join(lines))
	print('%s: %d strings' % (loc, len(tr)))


if __name__ == '__main__':
	main(sys.argv[1] if len(sys.argv) > 1 else 'vi')
