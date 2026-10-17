(function ($) {
	'use strict';
	$(function () {
		$('.bst-swatch-color-field').wpColorPicker();
		$(document).on('click', '.bst-swatch-image-pick', function (event) {
			event.preventDefault();
			var button = $(this), row = button.closest('td, .form-field'), input = row.find('.bst-swatch-image-id'), preview = row.find('.bst-swatch-image-preview'), remove = row.find('.bst-swatch-image-remove');
			var frame = wp.media({ title: 'Choose swatch image', button: { text: 'Use image' }, library: { type: 'image' }, multiple: false });
			frame.on('select', function () {
				var attachment = frame.state().get('selection').first().toJSON();
				input.val(attachment.id);
				preview.empty().append($('<img>', { src: attachment.sizes && attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url, alt: '', width: 48, height: 48 }));
				remove.prop('hidden', false);
			});
			frame.open();
		});
		$(document).on('click', '.bst-swatch-image-remove', function (event) {
			event.preventDefault();
			var row = $(this).closest('td, .form-field');
			row.find('.bst-swatch-image-id').val('');
			row.find('.bst-swatch-image-preview').empty();
			$(this).prop('hidden', true);
		});
	});
})(jQuery);
