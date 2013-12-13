define('admin/js/login', function(require, exports) {
	require('jquery/form');
	require('jquery/blockit');
	require('jquery/validate');
	$('#login-form').validate({focusCleanup : true});
	$('#login-form').submit(function(e) {
		if (!$(this).valid()) {
			return false;
		}
		$(this).ajaxSubmit({
			'dataType' : 'json',
			beforeSerialize : function() {
				$('#loginWin').blockit();
			},
			success : function(data) {
				if (data.success) {
					window.location.href = data.to;
				} else {
					$('#errorMsg').html('Oops: ' + data.msg);
					$('#loginWin').unblockit();
				}
			},
			error : function(data) {
				$('#errorMsg').html('Oops: 服务器未返回未知数据.');
				$('#loginWin').unblockit();
			}
		});
		return false;
	});
	// prepare the login window
	exports.main = function() {
		var win = $('#loginWin'), width = 400, height = 200;
		var w = $(window).width(), h = $(window).height();
		win.css({
			position : 'absolute',
			width : width,
			height : height,
			left : (w - width) / 2,
			top : (h - height) / 2
		}).removeClass('hide');
	};
});
