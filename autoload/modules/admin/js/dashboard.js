define('admin/js/dashboard', function(require, exports, module) {
	var lang = require('admin/i18n/{locale}.js');
	if (!lang) {
		lang = {
			start : 'Start'
		};
	}
	exports.main = function() {
		require('jquery/blockit');
		$('#top-search-wrap input').on('focus', function() {
			$(this).width(350);
		}).on('blur', function() {
			if (!$(this).val().trim()) {
				$(this).width(150);
			}
		});
		$('#goto-start-screen').on('click', function() {
			$('#desktop').hide().empty();
			$('#start-screen').show();
			$('#app-title').html(lang.start);
			return false;
		});
	};
	exports.open = function(app, title) {
		$('body').blockit();
		$('#app-title').html(title);
		seajs.use(app, function(appIns) {
			if (appIns) {
				$('#desktop').empty();
				appIns.open($('#desktop'));
				$('#start-screen').hide();
				$('#desktop').show();
			} else {
				$('body').unblockit();
				$.Notify({
					caption : 'Oops!',
					content : '加载"' + title + '"失败!',
					style : {
						background : '#E51400'
					},
					position : 'bottom-right'
				});
			}
		});
	};
	exports.assets = function(res){
		return seajs.data.vars.assets + res;
	};
	exports.acturl =function(module,action,args){
		
	};
});