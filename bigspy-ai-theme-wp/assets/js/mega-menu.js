/* Keeps multi-column dropdown panels inside the window. */
(function () {
	function fit(li) {
		var ul = li.querySelector(':scope > .sub-menu');
		if (!ul || window.innerWidth < 850) { return; }
		li.style.removeProperty('--bs-mega-shift');
		var a = li.querySelector(':scope > a');
		if (a && document.body.classList.contains('bs-dd-shape-full')) {
			var h = li.closest('.bs-header') || a, top = Math.round(Math.max(a.getBoundingClientRect().bottom, h.getBoundingClientRect().bottom));
			li.style.setProperty('--bs-dd-top', top + 'px');
			return;
		}
		var r = ul.getBoundingClientRect(), vw = document.documentElement.clientWidth, s = 0;
		if (r.right > vw - 16) { s = vw - 16 - r.right; }
		if (r.left + s < 16) { s = 16 - r.left; }
		if (s) { li.style.setProperty('--bs-mega-shift', Math.round(s) + 'px'); }
	}
	document.querySelectorAll('.bs-menu > .bs-mega-menu').forEach(function (li) {
		li.addEventListener('mouseenter', function () { fit(li); });
		li.addEventListener('focusin', function () { fit(li); });
	});
})();
