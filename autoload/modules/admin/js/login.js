define('admin/js/login', [], function(require,exports,module) {
	var win;
	exports.main = function(){		
		if(!win){
			var w = Ext.
			win = Ext.create('widget.window', {
		        height: 200,
		        width: 400,
		        x: 'center',
		        y: 'center',
		        title: 'Login',
		        closable: false,
		        plain: true,
		        layout: 'fit',
		        items: []
		    });
		}
		win.show();
	};
});