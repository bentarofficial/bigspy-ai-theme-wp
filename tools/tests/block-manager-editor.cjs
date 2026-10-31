/* Check the real registration filter without mounting the editor UI. */
const assert = require('node:assert/strict');
const fs = require('node:fs');
const path = require('node:path');
const vm = require('node:vm');
const theme = path.resolve(__dirname, '../../bigspy-ai-theme-wp');
const filters = new Map();
const window = {
  bstEditor: { disabledBlocks: [] },
  wp: {
    element: {}, i18n: { __: text => text }, blockEditor: {}, components: {},
    compose: { createHigherOrderComponent: callback => callback },
    hooks: { addFilter: (hook, namespace, callback) => filters.set(namespace, callback) }
  }
};
vm.runInNewContext(fs.readFileSync(path.join(theme, 'assets/js/block-design.js'), 'utf8'), { window });
const register = filters.get('bst/design-attribute');
let checked = 0;
for (const dir of fs.readdirSync(path.join(theme, 'blocks'))) {
  const metadata = JSON.parse(fs.readFileSync(path.join(theme, 'blocks', dir, 'block.json'), 'utf8'));
  window.bstEditor.disabledBlocks = [metadata.name];
  const hidden = register({ supports: { html: false } }, metadata.name);
  assert.equal(hidden.supports.inserter, false, metadata.name + ' must be hidden');
  assert.equal(hidden.supports.html, false, metadata.name + ' must retain other supports');
  if (metadata.name.startsWith('bigspy/')) {
    assert.equal(hidden.attributes, undefined, 'Hiding motion must not add unsupported design attributes');
  }
  window.bstEditor.disabledBlocks = [];
  const shown = register({ supports: { html: false } }, metadata.name);
  assert.equal(shown.supports.inserter, undefined, metadata.name + ' must remain available when enabled');
  checked++;
}
const core = { attributes: {}, supports: { html: true } };
assert.equal(register(core, 'core/paragraph'), core);
assert.equal(core.attributes.bsDesign, undefined);
console.log('BLOCK_MANAGER_EDITOR', checked, 'block types passed');
