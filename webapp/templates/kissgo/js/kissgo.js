$(function() {
	$('#btn_goto_top').click(function() {
		$(window).scrollTop(0);
		return false;
	});
	$(window).scroll(function(e) {
		if ($(this).scrollTop() > 40) {
			$('#sideTools').show();
		} else {
			$('#sideTools').hide();
		}
	});
});