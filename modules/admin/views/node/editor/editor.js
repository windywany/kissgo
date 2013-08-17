$(function() {
	$('.overlay-close').click(function() {
		Kissgo.closeIframe();
		return false;
	});
	$('.vertical-tabs').verticalTabs();
});