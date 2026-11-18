/* Show an option only when the option it depends on makes it useful (G-197). */
(function (api) {
	var deps = window.bstBlogDeps || {};
	function ok(rule) {
		var parent = api(rule[0]);
		if (!parent) { return true; }
		var v = parent.get();
		if (rule[1] === '==') { return rule[2] === true ? !!v : String(v) === String(rule[2]); }
		if (rule[1] === '!=') { return String(v) !== String(rule[2]); }
		if (rule[1] === 'in') { return rule[2].indexOf(String(v)) !== -1; }
		return true;
	}
	api.bind('ready', function () {
		Object.keys(deps).forEach(function (id) {
			api.control(id, function (control) {
				var rule = deps[id];
				var chain = [rule];
				// A child of a hidden option is hidden too (e.g. category only matters when Featured is on).
				var up = deps[rule[0]];
				while (up) { chain.push(up); up = deps[up[0]]; }
				function apply() { control.active.set(chain.every(ok)); }
				control.active.validate = function () { return chain.every(ok); };
				chain.forEach(function (r) { api(r[0], function (s) { s.bind(apply); }); });
				apply();
			});
		});
	});
})(wp.customize);
