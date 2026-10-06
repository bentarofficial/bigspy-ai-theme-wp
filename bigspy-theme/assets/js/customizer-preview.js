/* BIGSPY AI Theme WP – live preview of CSS-variable options without a page refresh. */
(function (api) {
	'use strict';

	var map = window.bstPreviewVars || {};
	var root = document.documentElement;

	Object.keys(map).forEach(function (key) {
		api(key, function (setting) {
			setting.bind(function (value) {
				if (value === '' || value === null || typeof value === 'undefined') {
					root.style.removeProperty(map[key][0]);
				} else {
					root.style.setProperty(map[key][0], value + map[key][1]);
				}
			});
		});
	});
})(wp.customize);
