/* Media ATND: the Generate button in the image details of the media picker. */
(function ($) {
	'use strict';
	var t = window.bstAtnd;

	function say(id, text, color) {
		$('.bst-atnd[data-id="' + id + '"] .bst-atnd-status').text(text).css('color', color);
	}

	// Fields on the full attachment edit screen, which has no media model to refresh.
	function fillEditScreen(data) {
		$('#attachment_alt').val(data.alt);
		$('#title').val(data.title);
		$('#attachment_caption').val(data.caption);
		$('#attachment_content').val(data.description);
	}

	$(document).on('click', '.bst-atnd-go', function () {
		var box = $(this).closest('.bst-atnd'), id = box.data('id'), button = $(this);
		var keyword = box.find('.bst-atnd-keyword').val().trim();
		if (!keyword) { say(id, t.noKey, '#d63638'); return; }
		button.prop('disabled', true);
		say(id, t.working, '#666');
		$.post(t.url, {
			action: 'bst_atnd_generate',
			nonce: t.nonce,
			id: id,
			keyword: keyword,
			desc: box.find('.bst-atnd-desc').val(),
			lang: box.find('.bst-atnd-lang').val(),
			model: box.find('.bst-atnd-model').val()
		}).done(function (res) {
			if (!res || !res.success) {
				say(id, (res && res.data && res.data.message) || t.failed, '#d63638');
				return;
			}
			fillEditScreen(res.data);
			var media = window.wp && window.wp.media;
			// Refreshing redraws the panel, so the message goes on the new one.
			if (media && media.model && media.model.Attachment) {
				media.model.Attachment.get(id).fetch().always(function () { say(id, t.done, '#00a32a'); });
			}
			say(id, t.done, '#00a32a');
		}).fail(function () {
			say(id, t.failed, '#d63638');
		}).always(function () {
			button.prop('disabled', false);
		});
	});
}(jQuery));
