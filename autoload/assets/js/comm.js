(function($) {
	var top_win = window;
	while (top_win.location.href != top_win.parent.location.href) {
		top_win = top_win.parent;
	}
	$.jQueryAjax = $.ajax;
	var _cajax = {
		send : function(options) {
			var ajaxReq;
			options.win = top_win;
			var true_options = $.extend({}, {
				dataType : 'text json',
				timeout : 90000
			}, options);

			true_options.beforeSend = function(xhr) {
				var data_type = this.dataType.split(' ');
				xhr.setRequestHeader('X-AJAX-TYPE', data_type[data_type.length - 1]);
				if ($.isFunction(options.beforeSend)) {
					options.beforeSend(xhr);
				}
			};
			true_options.error = function(xhr, error) {
				var ajaxRes = $.trim(ajaxReq.getResponseHeader('X-AJAX-REDIRECT'));
				var ajaxMsg = $.trim(ajaxReq.getResponseHeader('X-AJAX-MESSAGE'));
				if (ajaxMsg) {
					var txt = {};
					try {
						eval("txt = (" + xhr.responseText + ")");
						$.Dialog({
							shadow : true,
							flat : false,
							icon : '<i class="icon-windows"></i>',
							title : txt.title,
							content : txt.msg
						});
					} catch (e) {
						if ($.isFunction(options.error)) {
							options.error(xhr, error);
						} else {
							$.Dialog({
								shadow : true,
								flat : false,
								icon : '<i class="icon-windows fg-red"></i>',
								title : 'Server Error',
								content : xhr.status + ' ' + xhr.statusText + '<br/>URL:' + options.url + (options.data ? '<br/>Params:' + $.param(options.data) : '')
							});
						}
					}
				} else if (ajaxRes) {
					if ($.isFunction(options.error)) {
						options.error(xhr, error);
					}
					options.win.location.href = ajaxRes;
				} else if ($.isFunction(options.error)) {
					options.error(xhr, error);
				} else {
					$.Dialog({
						shadow : true,
						flat : false,
						icon : '<i class="icon-windows fg-red"></i>',
						title : 'Server Error',
						content : xhr.status + ' ' + xhr.statusText + '<br/>URL:' + options.url + (options.data ? '<br/>Params:' + $.param(options.data) : '')
					});
				}
			};
			ajaxReq = $.jQueryAjax(true_options);
			return ajaxReq;
		}
	};
	$.ajax = _cajax.send;
	if (!String.format) {
		String.prototype.format = function() {
			var formatted = this;
			for (var arg in arguments) {
				formatted = formatted.replace("{" + arg + "}", arguments[arg]);
			}
			return formatted;
		};
	}
	if (top_win.KsgApp) {
		window.KsgApp = top_win.KsgApp;
	} else {
		window.KsgApp = {};
		KsgApp.assets = function(res) {
			return seajs.data.vars.assets + res;
		};
		KsgApp.acturl = function(module, action, args) {
			if (args) {
				args = '?' + $.param(args);
			} else {
				args = '';
			}
			return seajs.data.vars.admincp + '/' + module + ( action ? '/' + action : '') + '/' + args;
		};
		KsgApp.notify = function(title, content, type) {
			var color = '#E51400';
			if (type == 'tip') {
				color = '#4390DF';
			} else if (type == 'warn') {
				color = '#FA6800';
			}
			$.Notify({
				caption : title,
				content : content,
				style : {
					background : color
				},
				position : 'bottom-right'
			});
		};
		KsgApp.tipmsg = function(msg) {
			$('<div class="notice marker-on-bottom fg-white" >' + msg + '</div>').appendTo($('#msgbox').empty().show());
		};
		KsgApp.errormsg = function(msg) {
			$('<div class="notice marker-on-bottom bg-darkRed fg-white" >' + msg + '</div>').appendTo($('#msgbox').empty().show());
		};
		KsgApp.successmsg = function(msg) {
			$('<div class="notice marker-on-bottom bg-emerald fg-white" >' + msg + '</div>').appendTo($('#msgbox').empty().show());
		};
		KsgApp.warnmsg = function(msg) {
			$('<div class="notice marker-on-bottom bg-amber fg-white" >' + msg + '</div>').appendTo($('#msgbox').empty().show());
		};
	}

	KsgApp.validate_opts1 = function(options) {
		return $.extend(true, {}, {
			focusCleanup : true,
			success : function(label, element) {
				$(element).removeClass(this.errorClass).addClass(this.validClass);
			},
			unhighlight : function(element, ecls, vcls) {
				$(element).removeClass(ecls).removeClass(vcls);
			},
			highlight : function(element, errorClass, validClass) {
				$(element).removeClass(validClass).addClass(errorClass);
			}
		}, options);
	};
})(jQuery);
