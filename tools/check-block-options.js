#!/usr/bin/env node
/**
 * Warn about block attributes that nothing uses (G-129).
 *
 * For every bigspy-ai-theme-wp/blocks/<slug>/block.json, each attribute should appear
 * in the block's render.php (or inc/listing/blocks.php for listing blocks) and in at
 * least one editor script. Attributes read through bst_part_vars( $a, '<block>', '<part>' )
 * / bstEditor.partVars(a, '<block>', '<part>') count as used when they start with <part>.
 * Only prints warnings; always exits 0.
 *
 * Run from the repository root:  node tools/check-block-options.js
 */
'use strict';

const fs = require('fs');
const path = require('path');

const THEME = path.join(__dirname, '..', 'bigspy-ai-theme-wp');
const BLOCKS = path.join(THEME, 'blocks');
const read = (f) => { try { return fs.readFileSync(f, 'utf8'); } catch (e) { return ''; } };

const editorFiles = fs.readdirSync(path.join(THEME, 'assets', 'js'))
	.filter((f) => /^(blocks-|block-design|editor-)/.test(f) && f.endsWith('.js'))
	.map((f) => read(path.join(THEME, 'assets', 'js', f)));
const editorSrc = editorFiles.join('\n');
const listingSrc = read(path.join(THEME, 'inc', 'listing', 'blocks.php'));

function used(src, name) {
	return new RegExp("['\"]" + name + "['\"]|\\b(a|attributes|attrs|props\\.attributes)\\." + name + '\\b').test(src);
}

function partPrefixes(src) {
	const out = [];
	const re = /(?:bst_part_vars|partVars)\s*\(\s*\$?[\w.]+\s*,\s*['"][\w-]+['"]\s*,\s*['"](\w+)['"]/g;
	let m;
	while ((m = re.exec(src))) out.push(m[1]);
	return out;
}

let warnings = 0;
let blocks = 0;
for (const slug of fs.readdirSync(BLOCKS).sort()) {
	const json = read(path.join(BLOCKS, slug, 'block.json'));
	if (!json) continue;
	let attrs;
	try { attrs = JSON.parse(json).attributes || {}; } catch (e) { console.log(`! ${slug}: block.json is not valid JSON`); warnings++; continue; }
	blocks++;
	const render = read(path.join(BLOCKS, slug, 'render.php')) + '\n' + listingSrc;
	const renderParts = partPrefixes(render);
	const editorParts = partPrefixes(editorSrc);
	for (const name of Object.keys(attrs)) {
		const viaPart = (parts) => parts.some((p) => name.startsWith(p) && name !== p);
		const inRender = used(render, name) || viaPart(renderParts);
		const inEditor = used(editorSrc, name) || viaPart(editorParts);
		if (!inRender || !inEditor) {
			warnings++;
			console.log(`! ${slug}.${name}: not found in ${[!inRender && 'render', !inEditor && 'editor'].filter(Boolean).join(' + ')}`);
		}
	}
}
console.log(`check-block-options: ${blocks} blocks, ${warnings} warning(s).`);
process.exit(0);
