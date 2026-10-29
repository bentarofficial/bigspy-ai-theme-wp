#!/usr/bin/env node
/**
 * Every BIGSPY block must have an inserter preview drawing and a description.
 * Run before a release: node tools/check-block-previews.js (exit code 1 lists what is missing).
 */
'use strict';
const fs = require('fs');
const path = require('path');

const theme = path.join(__dirname, '..', 'bigspy-ai-theme-wp');
const js = fs.readFileSync(path.join(theme, 'assets', 'js', 'block-previews.js'), 'utf8');
const drawn = new Set([...js.matchAll(/^\t\t'?([a-z-]+)'?: function/gm)].map((m) => m[1]));

// Blocks registered from block.json, plus the two registered in PHP.
const blocks = fs.readdirSync(path.join(theme, 'blocks')).filter((d) => fs.existsSync(path.join(theme, 'blocks', d, 'block.json')));
const problems = [];
for (const dir of blocks) {
	const meta = JSON.parse(fs.readFileSync(path.join(theme, 'blocks', dir, 'block.json'), 'utf8'));
	const key = meta.name.replace(/^(bst|bigspy)\//, '');
	if (!drawn.has(key)) problems.push(`${meta.name}: no preview drawing in block-previews.js`);
	if (!meta.description || meta.description.length < 60) problems.push(`${meta.name}: description missing or too short`);
}
for (const [key, file] of [['countdown', 'inc/countdown.php'], ['product-part', 'inc/product-design.php']]) {
	if (!drawn.has(key)) problems.push(`bst/${key}: no preview drawing in block-previews.js`);
	if (!fs.readFileSync(path.join(theme, file), 'utf8').includes("'description' =>")) problems.push(`bst/${key}: description missing in ${file}`);
}

if (problems.length) {
	console.error(problems.join('\n'));
	process.exit(1);
}
console.log(`OK: ${blocks.length + 2} blocks have a preview and a description.`);
