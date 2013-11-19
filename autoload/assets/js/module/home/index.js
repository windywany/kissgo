define('module/home/index', [ 'backbone' ], function(require, exports, module) {
	var Backbone = require('backbone');

	App.Models.Home = Backbone.Model.extend({});

	App.Collections.Home = Backbone.Collection.extend({
		model : App.Models.Home
	});

	App.Views.Home = Backbone.View.extend({
		el : '#container',
		events : {
			'click a':function(e){
				e.preventDefault();
				alert("you click me");
				this.$el.append('<div><a href="#">adfasdfasfasfasf</a></div>');
				App.Router.navigate("#at/m/a/name:moduleA/other:nothing");
			}
		},
		initialize : function(c) {
			this.Collections = c;
		},
		render : function() {
			var html = '';
			this.Collections.each(function(m) {
				html += '<div><a href="' + m.get('link') + '">' + m.get('name')
						+ '</a></div>';
			});
			this.$el.html(html);
			return this;
		}
	})

	exports.init = function() {
		// 模拟数据
		var hc = new App.Collections.Home();
		hc.add([ {
			'name' : '加载模块A',
			'link' : '#at/m/a/name:moduleA/other:nothing'
		}, {
			'name' : '加载模块B',
			'link' : '#at/m/b'
		} ]);
		new App.Views.Home(hc).render();
	}
});