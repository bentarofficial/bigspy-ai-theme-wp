/* G-200: "Reset this section" link at the bottom of every BIGSPY Customizer section. */
(function (api) {
	'use strict';
	var data = window.bstSectionReset || {};
	var map = data.map || {};
	var i18n = data.i18n || {};

	api.bind('ready', function () {
		Object.keys(map).forEach(function (id) {
			api.section(id, function (section) {
				var list = section.contentContainer && section.contentContainer[0];
				if (!list || list.querySelector('.bst-section-reset')) {
					return;
				}
				var li = document.createElement('li');
				li.className = 'customize-control bst-section-reset';
				var btn = document.createElement('button');
				btn.type = 'button';
				btn.className = 'button-link button-link-delete';
				btn.textContent = i18n.button;
				var status = document.createElement('p');
				status.className = 'description';
				status.setAttribute('role', 'status');
				btn.addEventListener('click', function () {
					if (!window.confirm(i18n.confirm)) {
						return;
					}
					var defaults = map[id];
					Object.keys(defaults).forEach(function (key) {
						var setting = api(key);
						if (setting) {
							var v = defaults[key];
							setting.set(v === null || v === undefined ? '' : v);
						}
					});
					status.textContent = i18n.done;
				});
				li.appendChild(btn);
				li.appendChild(status);
				list.appendChild(li);
			});
		});
	});
})(wp.customize);
