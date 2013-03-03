$(function() {
	var form = $('#user-form');
	form.uvalidate();

	$('#btn-save-user').click(submit);
	$('#btn-save-close-user').click(submit);
	$('#btn-save-new-user').click(submit);

	function submit() {
		$('#nextOp').val($(this).attr('data-name'));
		form.submit();
	}
});