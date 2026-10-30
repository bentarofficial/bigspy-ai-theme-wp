"""Append translations to vi.po and the .pot without opening them, then compile.

    python tools/add-strings.py strings.json [--no-compile]

strings.json maps English to Vietnamese: {"Save": "Lưu"}. Existing msgids are skipped,
never overwritten; the script reports any that hold a different translation.
"""
import json
import re
import subprocess
import sys
from pathlib import Path

REPO = Path(__file__).resolve().parents[1]
LANG = REPO / 'bigspy-ai-theme-wp' / 'languages'
PO = LANG / 'vi.po'
POT = LANG / 'bigspy-ai-theme-wp.pot'


def quote(value):
	return json.dumps(value, ensure_ascii=False)


def known(text):
	found = {}
	for msgid, msgstr in re.findall(r'^msgid (".*")\nmsgstr (".*")$', text, re.M):
		found[json.loads(msgid)] = json.loads(msgstr)
	return found


def append(path, text, entries):
	block = ''.join('msgid %s\nmsgstr %s\n\n' % (quote(k), quote(v)) for k, v in entries)
	with open(path, 'a', encoding='utf-8', newline='\n') as handle:
		handle.write(('\n' if not text.endswith('\n') else '') + ('\n' if not text.endswith('\n\n') else '') + block)


def main():
	sys.stdout.reconfigure(encoding='utf-8')
	args = [a for a in sys.argv[1:] if not a.startswith('--')]
	if len(args) != 1:
		raise SystemExit(__doc__)
	strings = json.loads(Path(args[0]).read_text(encoding='utf-8'))
	if not isinstance(strings, dict) or not all(isinstance(k, str) and k and isinstance(v, str) and v for k, v in strings.items()):
		raise SystemExit('strings.json must map non-empty English text to Vietnamese text')

	po_text = PO.read_text(encoding='utf-8')
	pot_text = POT.read_text(encoding='utf-8')
	po_known, pot_known = known(po_text), known(pot_text)
	new = [(k, v) for k, v in strings.items() if k not in po_known]
	conflicts = [k for k, v in strings.items() if k in po_known and po_known[k] != v]
	if new:
		append(PO, po_text, new)
	pot_new = [(k, '') for k in strings if k not in pot_known]
	if pot_new:
		append(POT, pot_text, pot_new)
	print('vi.po: %d added, %d skipped; pot: %d added' % (len(new), len(strings) - len(new), len(pot_new)))
	for key in conflicts:
		print('Different translation kept: %s => %s' % (key, po_known[key]))
	if new and '--no-compile' not in sys.argv:
		subprocess.run([sys.executable, str(REPO / 'tools' / 'compile-po.py'), 'vi'], check=True)


if __name__ == '__main__':
	main()
