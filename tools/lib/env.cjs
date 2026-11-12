// Where test dependencies live: BST_PLAYGROUND_MODULES / BST_PLAYWRIGHT, else this repo's node_modules (npm ci).
const path = require('path');
exports.modules = () => process.env.BST_PLAYGROUND_MODULES || path.join(__dirname, '..', '..', 'node_modules');
exports.playwright = () => require(process.env.BST_PLAYWRIGHT || 'playwright');
