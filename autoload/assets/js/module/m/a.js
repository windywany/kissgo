define('module/m/a', [ '$','module/m/b' ], function(require, exports, module) {
	var $ = require('$');
	var b = require('module/m/b');

	exports.init =  function(c) {
		b.ok();
		alert('module a' + $.param(c));
		console.log(c);
	};
});