define(
		'admin/js/dashboard',
		function(require, exports, module) {
			var lang = require('admin/i18n/{locale}');
			if (!lang) {
				lang = {
					start : 'Start',
					loadError : 'Load "{0}" failed!'
				};
			}else{
				lang = lang.lang;
			}
			$.jQueryAjax = $.ajax;
			var _cajax = {
				send : function(options) {
					var win = window, ajaxReq;
					while (win.location.href != win.parent.location.href) {
						win = win.parent;
					}
					options.win = win;
					var true_options = $.extend({}, {
						dataType : 'text json',
						timeout : 90000
					}, options);

					true_options.beforeSend = function(xhr) {
						var data_type = this.dataType.split(' ');
						xhr.setRequestHeader('X-AJAX-TYPE',
								data_type[data_type.length - 1]);
						if ($.isFunction(options.beforeSend)) {
							options.beforeSend(xhr);
						}
					};
					true_options.error = function(xhr, error) {
						var ajaxRes = $.trim(ajaxReq
								.getResponseHeader('X-AJAX-REDIRECT'));
						var ajaxMsg = $.trim(ajaxReq
								.getResponseHeader('X-AJAX-MESSAGE'));
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
									$
											.Dialog({
												shadow : true,
												flat : false,
												icon : '<i class="icon-windows fg-red"></i>',
												title : 'Server Error',
												content : xhr.status
														+ ' '
														+ xhr.statusText
														+ '<br/>URL:'
														+ options.url
														+ (options.data ? '<br/>Params:'
																+ $
																		.param(options.data)
																: '')
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
								content : xhr.status
										+ ' '
										+ xhr.statusText
										+ '<br/>URL:'
										+ options.url
										+ (options.data ? '<br/>Params:'
												+ $.param(options.data) : '')
							});
						}
					};
					ajaxReq = $.jQueryAjax(true_options);
					return ajaxReq;
				}
			};
			$.ajax = _cajax.send;
			window._ksg_onIframeLoaded = function(onload) {
				if ($.isFunction(onload)) {
					onload();
				}
				$('body').unblockit();
				$('#start-screen').hide();
				$('#desktop').show();
			};
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
						exports.notify('Oops!', lang.loadError.format(title));
					}
				});
			};
			exports.openIframe = function(url, title, onload) {
				$('body').blockit();
				$('#app-title').html(title);
				$('#start-screen').hide();
				var desktop = $('#desktop').empty().show();
				var iframe = $('<iframe class="frame-desktop" onload="_ksg_onIframeLoaded(onload)"></iframe>');
				iframe.appendTo(desktop).attr('src',url);
			};
			exports.notify = function(title, content, type) {
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
			exports.assets = function(res) {
				return seajs.data.vars.assets + res;
			};
			exports.acturl = function(module, action, args) {
				if (args) {
					args = '&' + $.param(args);
				} else {
					args = '';
				}
				return seajs.data.base + '?do=' + module
						+ (action ? '.' + action : '') + args;
			};
			if (!String.format) {
				String.prototype.format = function() {
					var formatted = this;
					for ( var arg in arguments) {
						formatted = formatted.replace("{" + arg + "}",
								arguments[arg]);
					}
					return formatted;
				};
			}
		});