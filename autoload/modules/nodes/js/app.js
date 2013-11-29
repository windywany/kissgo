define('nodes/js/app', function(require, exports) {
	var dashboard = require('admin/js/dashboard');
	require('jquery/blockit');
	var args = {
		start : 0,
		limit : 20,
		sort : 'id',
		dir : 'a',
		'do' : 'nodes'
	};
	exports.open = function(desktop) {
		this.loadPage();
	};
	exports.publishAsPage = function(id) {

	};
	exports.loadPage = function() {
		$('body').blockit();
		$.get(seajs.data.base, args, function(data) {
			alert(data);
		}, 'html').done(function() {
			$('body').unblockit();
		}).fail(function() {
			$('body').unblockit();
		});
	};
});