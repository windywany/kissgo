define('admin/js/dashboard', function(require, exports) {
	var lang = require('admin/i18n/{locale}');
	if (!lang) {
		lang = {
			start : 'Start',
			loadError : 'Load "{0}" failed!'
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
	};	
}); 