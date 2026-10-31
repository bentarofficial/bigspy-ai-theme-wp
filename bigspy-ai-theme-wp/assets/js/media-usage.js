/* Media Library: green tick for used files, red X for unused ones, and a usage filter in grid view. */
(function ($) {
	'use strict';
	var cfg = window.bstMediaUsage;
	if (window.wp && wp.media && wp.media.view && wp.media.view.Attachment && wp.media.view.Attachment.Library) {
		var Library = wp.media.view.Attachment.Library, render = Library.prototype.render;
		Library.prototype.render = function () {
			render.apply(this, arguments);
			this.$el.find('.bst-usage').remove();
			var status = this.model.get('bstUsage');
			if (status === 'used' || status === 'unused') {
				this.$el.find('.attachment-preview').append($('<span class="bst-usage"></span>').addClass('bst-usage-' + status).attr('title', cfg[status]));
			}
			return this;
		};
	}
	function gridFilter() {
		var bar = $('.media-frame .media-toolbar-secondary').first();
		if (!bar.length) { return false; }
		if (bar.find('#bst-usage-grid').length) { return true; }
		var select = $('<select id="bst-usage-grid" class="attachment-filters"></select>');
		[['', cfg.all], ['unused', cfg.unused], ['used', cfg.used]].forEach(function (o) {
			select.append($('<option></option>').val(o[0]).text(o[1]));
		});
		select.val(cfg.initial).on('change', function () {
			var state = wp.media.frame && wp.media.frame.state(), library = state && state.get('library');
			if (!library) { return; }
			if (this.value) { library.props.set({ bst_usage: this.value }); } else { library.props.unset('bst_usage'); }
		});
		bar.find('.attachment-filters').last().after(select);
		if (cfg.initial) { select.trigger('change'); }
		return true;
	}
	$(function () {
		$('td.column-bst_usage .bst-usage').each(function () {
			$(this).closest('tr').find('.media-icon').append($(this).clone());
		});
		if ($('#wp-media-grid').length) {
			var tries = 0, timer = setInterval(function () {
				if (gridFilter() || ++tries > 50) { clearInterval(timer); }
			}, 200);
		}
	});
})(jQuery);
