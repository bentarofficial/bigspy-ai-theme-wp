"""Generate docs/HOOKS.md from the theme source: every bst_* action and filter.

Usage: python tools/list-hooks.py
"""
import os
import re

ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
THEME = os.path.join(ROOT, 'bigspy-ai-theme-wp')
OUT = os.path.join(ROOT, 'docs', 'HOOKS.md')
PAT = re.compile(r"\b(do_action|apply_filters|bst_capture_action)\(\s*'(bst_[a-z0-9_]+)'(\s*\.)?")

hooks = {}
for base, dirs, files in os.walk(THEME):
    dirs[:] = [d for d in dirs if d not in ('node_modules', 'vendor', '.git')]
    for name in files:
        if not name.endswith('.php'):
            continue
        path = os.path.join(base, name)
        rel = os.path.relpath(path, THEME).replace(os.sep, '/')
        with open(path, encoding='utf-8', errors='ignore') as fh:
            for no, line in enumerate(fh, 1):
                for kind, hook, dyn in PAT.findall(line):
                    key = hook + ('<suffix>' if dyn else '')
                    typ = 'filter' if kind == 'apply_filters' else 'action'
                    hooks.setdefault((typ, key), []).append(f'{rel}:{no}')

lines = [
    '# Hooks cho nhà phát triển',
    '',
    'Sinh tự động bởi `python tools/list-hooks.py`; đừng sửa tay.',
    'Không ai móc vào thì HTML không đổi.',
    '',
    'Filter chung cho mọi khối `bst/*`: `bst_block_attributes( $attrs, $name, $block )` trước khi render,',
    '`bst_block_classes( $classes, $name, $attrs, $block )` thêm class vào thẻ ngoài,',
    '`bst_block_html( $html, $name, $attrs, $block )` sau khi render.',
    '',
]
for typ in ('action', 'filter'):
    lines += [f'## {"Action" if typ == "action" else "Filter"}', '', '| Hook | Nơi gọi |', '| --- | --- |']
    for (t, hook), where in sorted(hooks.items()):
        if t == typ:
            lines.append(f'| `{hook}` | {", ".join(where[:4])}{" …" if len(where) > 4 else ""} |')
    lines.append('')
with open(OUT, 'w', encoding='utf-8', newline='\n') as fh:
    fh.write('\n'.join(lines))
print(f'{len(hooks)} hooks -> docs/HOOKS.md')
