$(function() {
	var form = $('#options-form');
	form.uvalidate();
	$("#btn-reset-preference").click(function() {
		form.get(0).reset();
	});
	$('#btn-save-preference').click(function() {
		form.submit();
	});

	$('#btn-test-email').click(function() {
		if (form.valid()) {
			var email = $('#smtp_test_email').val();
			if (email) {
				showWaitMask('正在测试邮件设置...');
				$.ajax({
					url : Kissgo.AJAX,
					data : {
						email : email,
						"__op" : "test_email"
					},
					success : function(data) {
						hideWaitMask();
						alert(data.msg);
					}
				});
			} else {
				alert('请填写正确的测试邮件地址.');
			}
		}
		return false;
	});
});