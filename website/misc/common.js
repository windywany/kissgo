(function($) {
	$.fn.uiTable = function(method) {
		switch (method) {
		case 'selected':
			var selected = [], tbody = $(this).find(
					'tbody td.col_chk input:checked');
			tbody.each(function(i, n) {
				selected[i] = $(n).val();
			});
			return selected;
		case 'selectAll':
			if ($(this).find('th.col_chk input:checkbox').attr('checked')) {
				$(this).find('.col_chk input:checkbox').removeAttr('checked')
						.trigger('change');
			} else {
				return $(this).find('.col_chk input:checkbox').attr('checked',
						true).trigger('change');
			}
			break;
		default:
			$(this).each(
					function(i, tb) {
						var $tb = $(tb), tbody = $tb.find('tbody');
						$tb.find('th.col_chk').on(
								'change',
								'input:checkbox',
								function() {
									if ($(this).attr('checked')) {
										tbody.find('td.col_chk input:checkbox')
												.attr('checked', true).trigger(
														'change');
									} else {
										tbody.find('td.col_chk input:checkbox')
												.removeAttr('checked').trigger(
														'change');
									}
								});
						$tb.find('td.has-row-actions').on(
								'mouseover mouseout',
								function(e) {
									if (e.type == 'mouseout') {
										$(this).find('div.row-actions').css(
												'visibility', 'hidden');
									} else {
										$(this).find('div.row-actions').css(
												'visibility', 'visible');
									}
								});
					});
		}
		return $(this);
	};
	$.fn.uiTreeTable = function() {
		$(this).each(function() {
			var $this = $(this);
			$('tbody tr', $this).each(function(i, tr) {
				var row = $(tr), nodeId = row.attr('id');
				addHandl(nodeId, row, $this);
				if (row.hasClass('expanded')) {
					expandNode(nodeId, row, $this);
				}
			});

			$('tbody td i.handl', $this).live('click', function() {
				var hd = $(this), row = hd.parent().parent();
				var nodeId = row.attr('id');

				if (row.hasClass('expanded')) {
					row.addClass('collapsed').removeClass('expanded');
					collapseNode(nodeId, row, $this);
				} else {
					row.addClass('expanded').removeClass('collapsed');
					expandNode(nodeId, row, $this);
				}
			});
		});

		function addHandl(id, row, table) {
			if (row.find('i.shim').length > 0) {
				return;
			}
			if (table.find('.item-of-' + id).length) {
				row.find('.tree-handl').prepend(
						$('<i class="handl shim icon-folder-close"></i>'));
				row.data('moved', moveRows(id, row));
				row.data('left', 0);
			} else {
				row.find('.tree-handl').prepend(
						$('<i class="shim icon-folder-close"></i>'));
			}
		}

		function moveRows(id, row) {
			$('.item-of-' + id).insertAfter(row);
			return true;
		}

		// 展开结点
		function expandNode(id, row, table) {
			if (id) {
				var items = '.item-of-' + id, item, wd;
				row.find('.tree-handl i.handl').addClass('icon-folder-open')
						.removeClass('icon-folder-close');
				var left = row.data('left');
				$(items)
						.each(
								function(i, _item) {
									item = $(_item);
									if (item.find('i.shim').length == 0) {
										addHandl(item.attr('id'), item, table);
									}
									if (item.find('td.tree-handl span.chunk').length == 0) {
										wd = left + 16;
										item.data('left', wd);
										item
												.find('td.tree-handl')
												.prepend(
														$('<span class="chunk" style="width:'
																+ (wd)
																+ 'px;">&nbsp;</span>'));
									}
									item.removeClass('hide');
									if (item.hasClass('expanded')) {
										expandNode(item.attr('id'), item, table);
									} else {
										collapseNode(item.attr('id'), item,
												table);
									}
								});
			}
		}

		// 收起结点
		function collapseNode(id, row, table) {
			if (id) {
				var items = '.item-of-' + id, item;
				row.find('.tree-handl i.handl').addClass('icon-folder-close')
						.removeClass('icon-folder-open');
				$(items).each(function(i, _item) {
					item = $(_item);
					item.addClass('hide');
					collapseNode(item.attr('id'), item, table);
				});
			}
		}

		return $(this);
	};

	$.winGoto = function(url) {
		var win = window;
		while (win.location.href != win.parent.location.href) {
			win = win.parent;
		}
		win.location.href = url;
	};
	// ajax expends
	$.ajax1 = $.ajax;
	var __AJAX = {
		records : {},
		timer : null,
		error_flag : false,
		ajaxReq : null,
		options : {
			dataType : 'text json', // 20s
			timeout : 90000, // 默认的jquery ajax的配置
			timer : 30000, // 断网后,一分钟后重连
			flag : null, // 扩展 唯一标记, 相同标记的禁止叠加请求。
			retry : 0, // 扩展 触发error时的重试次数
			failedData : function(message) {// 扩展,通过error调用success时，传入succes函数的参数。
				return {
					success : false,
					msg : message
				};
			}
		},
		msg : {
			zh : {
				timeout : '请求超时。',
				error : '网络错误，无法正常连接网络。',
				parsererror : '数据解析时发生错误，服务端没有返回正确的数据。',
				unknown : '未知错误。'
			}
		},
		dealMsg : function(error_type) {
			return ($
					.inArray(error_type, [ 'timeout', 'error', 'parsererror' ]) > -1) ? this.msg['zh'][error_type]
					: this.msg['zh']['unknown'];
		},
		setAjaxHeader : function(xhr, data_type) {
			data_type = data_type.split(' ');
			xhr
					.setRequestHeader('X-AJAX-TYPE',
							data_type[data_type.length - 1]);
		},
		send : function(options) {
			var win = window;
			while (win.location.href != win.parent.location.href) {
				win = win.parent;
			}
			var self = __AJAX;
			// will be changed later
			var true_options = $.extend({}, self.options, options), flag = true_options.flag, retry = true_options.retry, failedData = true_options.failedData;
			options.win = win;

			// 如果此次请求处于等待状态，返回什么都不做。
			if (typeof flag === 'string' && flag.length
					&& self.records[flag] === 1) {
				return false;
			}
			self.records[flag] = 1;
			// 对关键功能加入全局动作,扩展beforeSend
			true_options.beforeSend = function(xhr) {
				self.setAjaxHeader(xhr, this.dataType);
				if ($.isFunction(options.beforeSend)) {
					options.beforeSend(xhr);
				}
			};
			// 扩展error
			true_options.error = function(xhr, error) {
				var ajaxRes = $.trim(self.ajaxReq
						.getResponseHeader('X-AJAX-REDIRECT'));
				var ajaxAuth = $.trim(self.ajaxReq
						.getResponseHeader('X-AJAX-TRANSIT'));
				var ajaxMsg = $.trim(self.ajaxReq
						.getResponseHeader('X-AJAX-MESSAGE'));
				if (ajaxAuth && $.isFunction(options.transit)) {
					options.transit(ajaxAuth, ajaxRes);
				} else if (ajaxMsg) {
					var txt = {};
					try {
						eval("txt = (" + xhr.responseText + ")");
						$.showMessageBox(txt.type, txt.title, txt.message);
					} catch (e) {
						if ($.isFunction(options.error)) {
							options.error(xhr, error);
						}
					}
				} else if (ajaxRes) {
					if ($.isFunction(options.error)) {
						options.error(xhr, error);
					}
					options.win.location.href = ajaxRes;
				} else if (error == 'error' && options.reconnect) {// 网络问题哦
					self.error_flag = true;
					// 发生了错误,告诉complete不要执行,因为要继续处理
					this.beforeSend = function(xhr) {
						self.setAjaxHeader(xhr, this.dataType);
					};
					self.timer = setTimeout(function() {
						$.ajax1(true_options);
					}, self.options.timer);
				} else if (retry === 0) {
					var message = self.dealMsg(error);
					if ($.isFunction(options.error)) {
						options.error(xhr, error);
					}
					if ($.isFunction(options.success)) {
						options.success(failedData(message));
					}
				} else if (retry > 0) {
					self.error_flag = true;
					// 发生了错误,告诉complete不要执行,因为要重试
					true_options.beforeSend = function(xhr) {
						self.setAjaxHeader(xhr, this.dataType);
					};
					$.ajax1(true_options);
					retry--;
				}
			};
			// 扩展complete。
			true_options.complete = function(xhr, ts) {
				if (!self.error_flag) {
					if ($.isFunction(options.complete)) {
						options.complete(xhr, ts);
					}
					delete self.records[flag];
				} else {
					self.error_flag = false;// 争取下一次执行权限
				}
			};
			self.ajaxReq = $.ajax1(true_options);// 发送请求
			return self.ajaxReq;
		}
	};
	$.eajax = $.ajax = __AJAX.send;
	// 日期格式化
	Date.prototype.format = function(format) {
		var o = {
			"M+" : this.getMonth() + 1, // month
			"d+" : this.getDate(), // day
			"h+" : this.getHours(), // hour
			"m+" : this.getMinutes(), // minute
			"s+" : this.getSeconds(), // second
			"q+" : Math.floor((this.getMonth() + 3) / 3), // quarter
			"S" : this.getMilliseconds()
		};
		if (/(y+)/.test(format))
			format = format.replace(RegExp.$1, (this.getFullYear() + "")
					.substr(4 - RegExp.$1.length));
		for ( var k in o)
			if (new RegExp("(" + k + ")").test(format))
				format = format.replace(RegExp.$1, RegExp.$1.length == 1 ? o[k]
						: ("00" + o[k]).substr(("" + o[k]).length));
		return format;
	};

	$.extend({
		metadata : {
			defaults : {
				type : 'class',
				name : 'metadata',
				cre : /({.*})/,
				single : 'metadata'
			},
			setType : function(type, name) {
				this.defaults.type = type;
				this.defaults.name = name;
			},
			get : function(elem, opts) {
				var settings = $.extend({}, this.defaults, opts);
				if (!settings.single.length)
					settings.single = 'metadata';
				var data = $.data(elem, settings.single);
				if (data)
					return data;
				data = "{}";
				if (settings.type == "class") {
					var m = settings.cre.exec(elem.className);
					if (m)
						data = m[1];
				} else if (settings.type == "elem") {
					if (!elem.getElementsByTagName)
						return undefined;
					var e = elem.getElementsByTagName(settings.name);
					if (e.length)
						data = $.trim(e[0].innerHTML);
				} else if (elem.getAttribute != undefined) {
					var attr = elem.getAttribute(settings.name);
					if (attr)
						data = attr;
				}
				if (data.indexOf('{') < 0)
					data = "{" + data + "}";
				data = eval("(" + data + ")");
				$.data(elem, settings.single, data);
				return data;
			}
		}
	});
	$.fn.metadata = function(opts) {
		return $.metadata.get(this[0], opts);
	};
	if ($.validator) {
		$.fn.uvalidate = function(options) {
			options = $.extend({
				errorElement : 'span',
				errorPlacement : function(label, element) {
					var ccls = undefined;
					var espan = element.next('span');
					if (espan.length > 0 && !espan.attr('generated')) {
						ccls = espan.attr('data-style');
						espan.remove();
					}else{
						espan = element.parents(".controls").find('span.tip');
						if (espan.length > 0 && !espan.attr('generated')) {
							ccls = espan.attr('data-style');
							espan.remove();
						}
					}
					label.attr('data-content', element.attr('data-content'));
					label.addClass(ccls).appendTo(element.parents(".controls"));
				},
				success : function(label, element) {
					var cls = this.errorClass, $e = $(element);
					if (!$e.hasClass(cls)) {
						label.addClass(this.validClass).html(
								'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
					}
				},
				hideErrors : function(labels) {
					var tip = '', $label = null, vcls = this.validClass;
					$.each(labels, function(i, label) {
						$label = $(label);
						tip = $label.attr('data-content');
						if (tip) {
							$label.removeClass(vcls).addClass('tip').html(tip);
						} else {
							$label.hide();
						}
					});
				}
			}, options);
			return $(this).validate(options);
		};
		$.validator.prototype.hideErrors = function() {
			if (this.settings.hideErrors
					&& $.isFunction(this.settings.hideErrors)) {
				this.settings.hideErrors(this.toHide);
			} else {
				this.addWrapper(this.toHide).hide();
			}
		};
	}
	if ($.datepicker) {
		$.datepicker.setDefaults({
			dateFormat : 'yy-mm-dd',
			currentText : '今天',
			weekHeader : '周',
			dayNames : [ "星期日", "星期一", "星期二", "星期三", "星期四", "星期五", "星期六" ],
			dayNamesMin : [ "日", "一", "二", "三", "四", "五", "六" ],
			dayNamesShort : [ "日", "一", "二", "三", "四", "五", "六" ],
			monthNames : [ "一月", "二月", "三月", "四月", "五月", "六月", "七月", "八月",
					"九月", "十月", "十一", "腊月" ],
			monthNamesShort : [ "一月", "二月", "三月", "四月", "五月", "六月", "七月", "八月",
					"九月", "十月", "十一", "腊月" ]
		});
	}
	$.showMessageBoxTpl = '<div id="xui-messagebox" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="msgModalLabel" aria-hidden="true">';
	$.showMessageBoxTpl += '<div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button><h3></h3></div>';
	$.showMessageBoxTpl += '<div class="modal-body" id="xui-messagebox-body"></div>';
	$.showMessageBoxTpl += '<div class="modal-footer"><button class="btn" data-dismiss="modal" aria-hidden="true">确定</button></div></div>';
	$.showMessageBox = function(type, title, message) {
		$('#overlay').hide();
		var msgbox = $('#xui-messagebox');
		if (msgbox.length == 0) {
			msgbox = $($.showMessageBoxTpl);
			msgbox.appendTo($('body'));
		}
		msgbox.find('.modal-header h3').addClass(type).html(title);
		msgbox.find('#xui-messagebox-body').addClass(type).html(message);
		msgbox.modal('show');
	};
	$.scrollbarWidth = function(){
		
		var scrollDiv = $('<div></div>').css({width: '100px',
									height: '100px',
									overflow: 'scroll',
									position: 'absolute',
									padding:0,
									margin:0,
									top: '-9999px'}
									);		
		$('body').append(scrollDiv);	
		scrollD = scrollDiv.get(0);
		var scrollbarWidth = scrollD.offsetWidth - scrollD.clientWidth;;
		scrollDiv.remove();
		return scrollbarWidth;
	};
	$(function() {
		$('table.ui-table').uiTable();
		$('.stuffbox').on('click','.handlediv',function(){
			var $this = $(this), $box = $this.parents('.stuffbox');
			$box.toggleClass('closed');
		});
		$('.autoset').each(function(i,e){
			var $e = $(e),h=$e.find('.tab-content').height();
			$e.find('.nav-tabs').height(h);
		});		
	});
})(jQuery);
function showWaitMask(text, keep) {
    text = text ? text : '处理中...';
    var ov = $('#overlay-wrapper'), msg = ov.find('div.msg');
    ov.show();
    if (!keep) {
        msg.html(text);
    }
}
function hideWaitMask() {
    $('#overlay-wrapper').fadeOut(350);
}

if(window.Kissgo){
	window.Kissgo.emptyFun = function(){};
	window.Kissgo.publish = function(type, id, callback){
		if(!type||!id){
			alert('error type or id');
		}else{
			Kissgo.openIframe(Kissgo.murl('admin','pages/publish/'+type+'/'+id), false,callback);
		}
	};
	
	window.Kissgo.murl = function(module, action){
		var alias = Kissgo.alias[module];
		if(!alias){
			alias = module;
		}
		return Kissgo.ROUTER_BASE + alias + (action ? '/'+action: '');
	};
	
	window.Kissgo.openIframe = function(url, onload, callback){
		Kissgo.iframeOnload = $.isFunction(onload)?onload:Kissgo.emptyFun;
		Kissgo.iframeOnclosed = $.isFunction(callback)?onload:Kissgo.emptyFun;
		var tpl = '<div id="overlay-container">'+
			'<div class="overlay-modal-background"></div>'+
			'<iframe scrolling="auto" frameborder="0" allowtransparency="true" class="overlay-element" tabindex="-1"></iframe>'+
			'<iframe scrolling="auto" onload="Kissgo.iframeOnload()" frameborder="0" allowtransparency="true" class="overlay-element overlay-active" id="overlay-iframe"></iframe></div>';
			
		var overIframe = $(tpl),body = $('body');
		body.addClass('show-overlay');		
		overIframe.appendTo($('body'));
		var sw = $(window).width() - $.scrollbarWidth();
		$('#navbar').width(sw);
		$('#foot').width(sw-20);
		overIframe.find('#overlay-iframe').attr('src',url);
	}
	
	window.Kissgo.closeIframe = function(){
		Kissgo.iframeOnclosed();
		$('#overlay-container').remove();
		$('#navi,#foot').css('clip','none');
		$('#navbar').width('100%');
		$('#foot').width('100%');
		body.removeClass('show-overlay');
	}
}