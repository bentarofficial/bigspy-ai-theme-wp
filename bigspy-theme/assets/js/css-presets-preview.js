/* Live output uses the same recipe order as PHP; manual Additional CSS follows it. */
(function (api) {
	'use strict';
	var recipes = window.bstCSSPresets || {};

	api('css_presets', function (setting) {
		function render(value) {
			var selected = Array.isArray(value) ? value : [];
			var enabled = [];
			var families = {};
			selected.forEach(function (id) {
				if (!Object.prototype.hasOwnProperty.call(recipes, id)) { return; }
				var family = recipes[id].exclusive;
				if (family && families[family]) {
					enabled = enabled.filter(function (key) { return key !== families[family]; });
				}
				if (family) { families[family] = id; }
				if (enabled.indexOf(id) === -1) { enabled.push(id); }
			});
			var style = document.getElementById('bst-css-presets-inline-css');
			if (!style) {
				style = document.createElement('style');
				style.id = 'bst-css-presets-inline-css';
				var manual = document.getElementById('wp-custom-css');
				if (manual) { manual.parentNode.insertBefore(style, manual); }
				else { document.head.appendChild(style); }
			}
			style.textContent = Object.keys(recipes).filter(function (id) { return enabled.indexOf(id) !== -1; }).map(function (id) { return recipes[id].css; }).join('\n');
		}
		setting.bind(render);
		render(setting.get());
	});
})(wp.customize);
