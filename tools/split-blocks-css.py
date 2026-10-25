"""Tag every rule of assets/css/blocks.css with the class family it needs.

A rule belongs to a family when every selector in it requires a class of that family
(classes inside :not / :is / :where / :has do not count, they are optional). Such a
rule can only match when an element of that family is on the page. Everything else is
"core". Output: assets/css/blocks/segments.json, the stylesheet as ordered
[family, css] segments. For each page the theme keeps core plus the families found in
its HTML, in the original order, so the cascade is unchanged (see inc/performance.php).

Run from the repository root:  python tools/split-blocks-css.py
"""
import json
import os
import re
import sys

THEME = os.path.join(os.path.dirname(os.path.abspath(__file__)), '..', 'bigspy-ai-theme-wp')
SRC = os.path.join(THEME, 'assets', 'css', 'blocks.css')
OUT = os.path.join(THEME, 'assets', 'css', 'blocks')

# family -> class prefixes. A class belongs to a prefix when it equals it or continues
# with "-" or "_" (bs-tab matches bs-tab--active, not bs-tabs).
FAMILIES = {
	'accordion': ['bs-accordion'],
	'alert': ['bs-alert'],
	'animate': ['bs-animate', 'bs-ease'],
	'audio': ['bs-audio'],
	'banner': ['bs-banner'],
	'banner-grid': ['bs-bgrid'],
	'before-after': ['bs-ba'],
	'categories': ['bs-cats', 'bs-cat'],
	'code': ['bs-code', 'bs-html'],
	'cookie': ['bs-cookie'],
	'counter': ['bs-counter'],
	'divider': ['bs-divider'],
	'flip-box': ['bs-flip'],
	'form': ['bs-form'],
	'gallery': ['bs-gallery'],
	'hotspot': ['bs-hotspot'],
	'logos': ['bs-logos'],
	'lottie': ['bs-lottie'],
	'map': ['bs-map'],
	'marquee': ['bs-marquee'],
	'nav-menu': ['bs-navmenu'],
	'native-form': ['bs-native'],
	'payment-icons': ['bs-pay'],
	'popup': ['bs-popup'],
	'price-table': ['bs-price'],
	'pricing-toggle': ['bs-ptg', 'bs-pt'],
	'progress': ['bs-progress'],
	'slider': ['bs-slider'],
	'social': ['bs-follow'],
	'stars': ['bs-stars'],
	'table': ['bs-table'],
	'tabs': ['bs-tabs', 'bs-tab'],
	'team': ['bs-team'],
	'testimonial': ['bs-testimonial'],
	'timeline': ['bs-timeline'],
	'toc': ['bs-toc'],
	'video': ['bs-video', 'bs-vbtn'],
}


def family_of(cls):
	for fam, prefixes in FAMILIES.items():
		for p in prefixes:
			if cls == p or cls.startswith(p + '-') or cls.startswith(p + '_'):
				return fam
	return None


def strip_optional(sel):
	# Remove the contents of :not( ) :is( ) :where( ) :has( ) (nested parentheses included).
	out, i = [], 0
	while i < len(sel):
		m = re.match(r':(not|is|where|has|matches|-webkit-any)\(', sel[i:])
		if m:
			depth, j = 0, i + len(m.group(0)) - 1
			while j < len(sel):
				if sel[j] == '(':
					depth += 1
				elif sel[j] == ')':
					depth -= 1
					if depth == 0:
						break
				j += 1
			i = j + 1
			continue
		out.append(sel[i])
		i += 1
	return ''.join(out)


def split_selectors(sel):
	parts, depth, cur = [], 0, ''
	for ch in sel:
		if ch in '([':
			depth += 1
		elif ch in ')]':
			depth -= 1
		if ch == ',' and depth == 0:
			parts.append(cur)
			cur = ''
		else:
			cur += ch
	parts.append(cur)
	return [p.strip() for p in parts if p.strip()]


def rule_family(selector):
	fams = None
	for sel in split_selectors(selector):
		req = {family_of(c) for c in re.findall(r'\.(bs-[A-Za-z0-9_-]+)', strip_optional(sel))}
		req.discard(None)
		if not req:
			return None
		fams = req if fams is None else fams & req
		if not fams:
			return None
	return sorted(fams)[0] if fams else None


def parse(css):
	"""Return a list of (selector_or_at, body, children) from CSS text without comments."""
	items, i, n = [], 0, len(css)
	while i < n:
		while i < n and css[i] in ' \t\r\n':
			i += 1
		if i >= n:
			break
		start = i
		while i < n and css[i] not in '{;':
			if css[i] in '"\'':
				q = css[i]
				i += 1
				while i < n and css[i] != q:
					i += 2 if css[i] == '\\' else 1
			i += 1
		head = css[start:i].strip()
		if i < n and css[i] == ';':
			items.append((head, None, None))  # @import / @charset
			i += 1
			continue
		i += 1
		depth, bstart = 1, i
		while i < n and depth:
			c = css[i]
			if c in '"\'':
				q = c
				i += 1
				while i < n and css[i] != q:
					i += 2 if css[i] == '\\' else 1
			elif c == '{':
				depth += 1
			elif c == '}':
				depth -= 1
			i += 1
		body = css[bstart:i - 1]
		if re.match(r'@(media|supports|container|layer)\b', head):
			items.append((head, None, parse(body)))
		else:
			items.append((head, body.strip(), None))
	return items


def main():
	css = open(SRC, encoding='utf-8').read()
	css = re.sub(r'/\*.*?\*/', '', css, flags=re.S)
	buckets = {}
	order = []
	css_all = css

	def add(fam, text, wrap):
		buckets.setdefault(fam, []).append((wrap, text))
		order.append((fam, wrap, text))

	for head, body, children in parse(css):
		if children is not None:
			for h, b, ch in children:
				if ch is not None or b is None:
					add('core', h + '{' + ''.join(x[0] + '{' + (x[1] or '') + '}' for x in ch or []) + '}', head)
					continue
				add(rule_family(h) or 'core', h + '{' + b + '}', head)
		elif body is None:
			add('core', head + ';', None)
		elif head.startswith('@'):
			add('core', head + '{' + body + '}', None)
		else:
			add(rule_family(head) or 'core', head + '{' + body + '}', None)

	os.makedirs(OUT, exist_ok=True)
	for f in os.listdir(OUT):
		os.remove(os.path.join(OUT, f))
	segments = []
	for fam, wrap, text in order:
		css = (wrap + '{' + text + '}') if wrap else text
		if segments and segments[-1][0] == fam:
			segments[-1][1] += '\n' + css
		else:
			segments.append([fam, css])
	with open(os.path.join(OUT, 'segments.json'), 'w', encoding='utf-8', newline='\n') as fh:
		json.dump(segments, fh, ensure_ascii=False, separators=(',', ':'))
	with open(os.path.join(OUT, 'families.json'), 'w', encoding='utf-8', newline='\n') as fh:
		json.dump({k: v for k, v in FAMILIES.items() if k in buckets}, fh, indent='\t')
		fh.write('\n')
	sizes = {}
	for fam, css in segments:
		sizes[fam] = sizes.get(fam, 0) + len(css.encode('utf-8'))
	print('source', len(css_all.encode('utf-8')), 'segments', len(segments))
	for fam, n in sorted(sizes.items(), key=lambda x: -x[1]):
		print(f'{fam:16} {n:7}')


if __name__ == '__main__':
	sys.exit(main())
