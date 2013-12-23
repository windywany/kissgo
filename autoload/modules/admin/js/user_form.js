define('admin/js/user_form', function(require, exports) {
	require('jquery/form');
	require('jquery/blockit');
	require('jquery/validate');
	exports.main = function(rules) {
		var validator = $('#user_form').validate($.extend(true, {}, rules, {
			focusCleanup : true
		}));

		$('#user_form').submit(function(e) {
			if (!$(this).valid()) {
				return false;
			}
			$(this).ajaxSubmit({
				dataType : 'json',
				beforeSerialize : function() {
					$('body').blockit();
				},
				success : function(data) {
					if (data.success) {
						$('#userid').val(data.id);
						KsgApp.successmsg('恭喜!用户信息保存成功.');
						if(rules.rules.username.remote.indexOf('?')<=0){
							$('#username').rules('add',{remote:rules.rules.username.remote+'?id='+data.id});
						}						
					} else if (data.formerr) {
						validator.showErrors(data.formerr);
					} else {
						KsgApp.errormsg('出错啦!' + data.msg);
					}
					$('body').unblockit();
				},
				error : function(data) {
					$('body').unblockit();
				}
			});
			return false;
		});
	};
});