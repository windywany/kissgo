define('admin/js/user_form', function(require, exports) {
	require('jquery/form');
	require('jquery/blockit');
	require('jquery/validate');
	exports.main = function(rules) {
		$('#user_form').validate($.extend(true, {}, rules, {
			focusCleanup : true
		}));

		$('#user_form').submit(function(e) {
			if (!$(this).valid()) {
				return false;
			}
			return false;
		});
	};
});