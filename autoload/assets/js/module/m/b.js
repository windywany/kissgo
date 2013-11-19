define('module/m/b', [ 'module/d/a' ], function(require, exports, module) {
	var da = require('module/d/a');
	exports.ok = function() {
		da.info();
		alert('I an B');
	};

	exports.init = function(c) {
		alert('moduleB加载成功，参数：' + c);
	};
});