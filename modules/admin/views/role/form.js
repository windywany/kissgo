$(function() {
	var form = $('#role-form');
	form.uvalidate();

	$('#btn-save').click(submit);
	$('#btn-save-close').click(submit);
	$('#btn-save-new').click(submit);

	function submit() {
		$('#nextOp').val($(this).attr('data-name'));
		form.submit();
	}
});