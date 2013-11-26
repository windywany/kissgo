define('admin/js/login', [ 'jquery/form', 'jquery/blockit' ], function(require,
		exports, module) {
	require('jquery/form');
	require('jquery/blockit');
	$('#login-form').submit(function(e) {
		return false;
	});
	// submit the login form
	function doLogin(data) {
		alert(data);
	}
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
		$('#loginWin').blockit();
	};
});