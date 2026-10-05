(function ($) {
	'use strict';
	var timer;
	$(document).on('change', '.woocommerce-cart-form input.qty', function () {
		var form = this.closest('form');
		if (!this.checkValidity()) return;
		clearTimeout(timer);
		timer = setTimeout(function () {
			var button = form && form.querySelector('button[name="update_cart"], input[name="update_cart"]');
			if (button && document.contains(button) && !form.querySelector('.blockUI')) {
				button.disabled = false;
				button.click();
			}
		}, 650);
	});
})(jQuery);
