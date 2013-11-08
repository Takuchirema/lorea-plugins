$(function() {
	$('.elgg-icon-online').each(function() {
		$(this).appendTo($(this).parent().find('.elgg-avatar'));
		$(this).show();
	});
});
