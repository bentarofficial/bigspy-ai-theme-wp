/* Featured posts slider: arrows and gentle autoplay (pauses on hover and for reduced motion). */
(function () {
	document.querySelectorAll('[data-bst-feat-slider]').forEach(function (box) {
		var track = box.querySelector('.bs-feat__track');
		if (!track) { return; }
		function go(dir) {
			var w = track.clientWidth;
			var max = track.scrollWidth - w;
			var next = track.scrollLeft + dir * w;
			if (next > max + 2) { next = 0; }
			if (next < -2) { next = max; }
			track.scrollTo({ left: next, behavior: 'smooth' });
		}
		var prev = box.querySelector('.is-prev');
		var nxt = box.querySelector('.is-next');
		if (prev) { prev.addEventListener('click', function () { go(-1); }); }
		if (nxt) { nxt.addEventListener('click', function () { go(1); }); }
		if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) { return; }
		var hover = false;
		box.addEventListener('mouseenter', function () { hover = true; });
		box.addEventListener('mouseleave', function () { hover = false; });
		setInterval(function () { if (!hover && !document.hidden) { go(1); } }, 6000);
	});
})();
