define('admin/js/dashboard', function(require, exports, module) {
	exports.main = function() {
		$('#sidebar .dropdown-toggle').on('click',function(){
			$('#sidebar .dropdown-menu').slideDown();
		});
		$('#top-search-wrap input').on('focus', function() {
			$(this).width(350);
		}).on('blur', function() {
			if (!$(this).val().trim()) {
				$(this).width(150);
			}
		});
	};
});