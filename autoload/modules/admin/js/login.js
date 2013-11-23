define('admin/js/login', ['admin/i18n/{{local}}'], function(require, exports, module) {
	var win;
	var lang = require('admin/i18n/{{local}}');
	if(!lang){
		lang = require('admin/i18n/en-US');
	}
	exports.main = function() {
		if (!win) {
			var wsize = Ext.getBody().getViewSize();
			
			win = Ext.create('widget.window', {
				height : 200,
				width : 400,
				x : (wsize.width - 400)/2,
				y : (wsize.height - 200)/2,
				title : lang.login,
				closable : false,
				resizable:false,
				plain : true,
				layout : 'fit',
				items : []
			});
		}
		win.show();
	};
});