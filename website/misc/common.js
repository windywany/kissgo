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
	$.fn.verticalTabs = function(){
		return $(this).each(function(i,n){			
			$(n).find('.vertical-tab-button').click(function(){			
				var $this = $(this);
				var tabC = $this.parents('.vertical-tabs');			
				var wrapper = $this.find('a').attr('href');
				if(!$this.hasClass('selected')){
					tabC.find('.vertical-tab-button').not($this).removeClass('selected');
					tabC.find('.vertical-tabs-pane').not(wrapper).hide();
					$this.addClass('selected');
					$(wrapper).show();
				}
			});
			
			$(n).find('.vertical-tab-button').find('a').click(function(e){
				e.preventDefault();
			});
			
			$(n).find('.vertical-tab-button:first').addClass('first');
			$(n).find('.vertical-tab-button:last').addClass('last');
			if($(n).find('.vertical-tab-button').has('.selected').length==0){
				$(n).find('.vertical-tab-button:first').click();
			}
		});
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
	
	$.warn = function(msg,callback){
		$.showMessageBox('warn','Warn',msg,[{text:'OK',callback:callback}]);
	};
	$.error = function(msg,callback){
		$.showMessageBox('error','Error',msg,[{text:'OK',callback:callback}]);
	};
	$.success = function(msg,callback){
		$.showMessageBox('success','Success',msg,[{text:'OK',callback:callback}]);
	};
	$.alert = function(msg,callback){
		$.showMessageBox('info','Alert',msg,[{text:'OK',callback:callback}]);
	};
	$.confirm = function(msg,callback){
		$.showMessageBox('confirm','Confirm',msg,[{text:'Yes',callback:callback},{text:'No'}]);
	};	
	$.dialog = function(title,body,buttons){
		$.showMessageBox('prompt',title,body,buttons);
	};
	$.prompt = function(title,inputs,callback){
		var msgs = [];
		$.each(inputs,function(i,n){
			msgs.push('<div class="input-prepend"><span class="add-on">'+n+'</span><input type="text" class="w300" id="prompt-ipt-'+i+'"></div>');			
		});
		$.showMessageBox('prompt',title,msgs.join('<br class="clear"/>'),[{text:'OK',callback:function(){if($.isFunction(callback)){return callback( function(x){return $('#prompt-ipt-'+x).val();},function(x){$('#prompt-ipt-'+x).unbind().click(function(){$(this).removeClass('error');}).addClass('error');$('#overlay').click();});}}},{text:'Cancel'}]);		
	};	
	
	$.showMessageBoxTpl = '<div id="xui-messagebox" class="xui_dialog"><div class="dialog_head"><span></span></div>';
	$.showMessageBoxTpl += '<div class="dialog_body"><div class="dialog_content"><div class="cnfx_content"><span class="dialog_icon"></span><div class="dialog_f_c"></div></div></div>';
	$.showMessageBoxTpl += '<div class="dialog_operate"><div class="txt_right cnfx_btn"></div><div class="clearfix"></div></div></div></div>';
	
	$.showMessageBox = function(type, title, message,buttons) {
		var ov = $.getOverlay(),cls='icon_info_b';		
		var msgbox = $('#xui-messagebox');
		if (msgbox.length == 0) {
			msgbox = $($.showMessageBoxTpl);
			msgbox.appendTo($('body'));
		}
		msgbox.find('.dialog_content').removeClass('no-icon');
		switch(type){
			case 'error':
				cls = 'icon_err_b';
				break;
			case 'success':
				cls = 'icon_suc_b';
				break;
			case 'warn':
				cls = 'icon_warn_b';
				break;
			case 'confirm':
				cls = 'icon_con_b';
				break;
			case 'prompt':
				msgbox.find('.dialog_content').addClass('no-icon');
				break;
			default:
				break;			
		}
		msgbox.find('.dialog_head span').html(title);
		msgbox.find('.dialog_f_c').empty().html(message);		
		msgbox.find('.dialog_icon').attr('class','dialog_icon').addClass(cls);			
		$.createMessageButtons(msgbox,buttons);
		var w = msgbox.width(),h = msgbox.height(),ww = $(window).width(),wh=$(window).height();
		msgbox.css({top:$(window).scrollTop()+(wh-h)/2,left:(ww-w)/2}).show();
		ov.show();
	};
	$.closeMessageBox = function(){
		var ov = $('#overlay'),msgbox = $('#xui-messagebox');
		if($('#overlay-body:visible').length == 0){
			ov.hide();
		}		
		msgbox.hide();
	};
	$.createMessageButtons = function(dialog,buttons){
		var btnWrapper = dialog.find('.cnfx_btn').find('.btn').unbind().end().empty(),$btn=null;
		buttons = buttons || [{text:'OK'}];
		$.each(buttons,function(i,btn){
			$btn = $('<a class="btn '+(btn.cls?btn.cls:'')+'">'+btn.text+'</a>');
			if(btn.iconCls){
				$btn.prepend('<i class="'+btn.iconCls+'"></i>');
			}
			$btn.click(function(){
				var rst = true;
				if($.isFunction(btn.callback)){
					rst = btn.callback(btn,dialog);
				}
				if(rst !==false){
					$.closeMessageBox();
				}
			});
			btnWrapper.append($btn);
		});
	};
	$.getOverlay = function(){
		var ov = $('#overlay');
		if(ov.length == 0){
			ov = $('<div id="overlay"></div>').appendTo($('body'));
			ov.click(function(){
				var msgbox = $('#xui-messagebox:visible');
				if(msgbox.length>0){
					var w = msgbox.width(),ww = $(window).width(),mw = (ww-w)/2;
					$('#xui-messagebox').animate({left:mw-50},{duration:50})
										.animate({left:mw+50},{duration:100})
										.animate({left:mw-50},{duration:100})
										.animate({left:mw},{duration:50});
				}
			});
		}
		return ov;
	};
	$.scrollbarWidth = function(){		
		var scrollDiv = $('<div></div>').css({width: '100px',
									height: '100px',
									overflow: 'scroll',
									position: 'absolute',
									padding:0,
									margin:0,
									top: '-9999px'});		
		$('body').append(scrollDiv);	
		scrollD = scrollDiv.get(0);
		var scrollbarWidth = scrollD.offsetWidth - scrollD.clientWidth;;
		scrollDiv.remove();
		return scrollbarWidth;
	};
	$.fn.selectag = function(options){
		var $tag = $(this),opt = options;
		opt.tags = [];
		opt.tokenizer = function(input, selection, selectCallback, opts){
			if(input.length > 1){
				var len = input.length ,token = input.substring(len - 1,len);
				if(token == ',' || token == ' '){
					var sl = selection.length;
					input = input.replace(/[, ]+$/g,'');
					for(var i=0;i<sl;i++){
						if(input == selection[i].id){
							$tag.select2('close');
							return;
						}
					}
					selectCallback({id:input,text:input});					
				}
			}
		};
		opt.initSelection = function (element, callback) {
	        var val = element.val().split(','),data = [];
	        $.each(val,function(i,n){
	        	data.push({id:n,text:n});
	        });
	        callback(data);
	    };
	    opt.tokenSeparators = [',',' '];	    
		if($.fn.select2){
			return $tag.select2(opt);
		}
		return $tag;
	};
	$.fn.selectimg = function(options){
		var $tag = $(this);
		if(window.Kissgo){		
			options = options || {};
			var format = function(state) {			
				var img = Kissgo.uploadurl(state.t1.toLowerCase());
				return "<img class='flag sltimg' src='" + img + "'/>" + state.text;
			};			
			options.minimumInputLength = 1;
			options.formatResult = format;
			options.formatSelection = format;
			options.escapeMarkup = function(m) { return m; };
			options.containerCssClass = 'sltimg';	
			options.allowClear = true;
			options.placeholder = "Select Image";
			options.ajax = {
				cache:true,
				url : Kissgo.AJAX + '?__op=images_autocomplete',
				data : function(term, page) {
					if(term.length < 1){
						return null;
					}
					return {q : term,p : page};					
				},
				results : function(data, page) {
					return data;
				}
			};
			options.initSelection = function (element, callback) {
				var val = element.val(),imgData = element.data('imgData'),data = {id: val, text: '',t1:val,t2:val};
				if(imgData){
					data.text = imgData.name || '';
					data.t1   = imgData.t1 || val;
					data.t2   = imgData.t2 || val;
				}						        
		        callback(data);		        
		    };
			$tag.select2(options);
		}
		return $tag;
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
		if($.fn.placeholder){
			$('input, textarea').placeholder();
		}
		$('.ksg-publish').click(function(){
			var type = $(this).attr('data-type');
			var id = $(this).attr('data-content');
			var title = $(this).attr('data-title');
			var url  = $(this).attr('data-url');
			var data = {};
			if(title){
				data.title = title;
				data.url = url;
			}
			Kissgo.publish(type,id?id:0,function(data){
				if($.isFunction(window.onpublished)){
					window.onpublished(data);
				}else if(data != false){
					window.location.reload();
				}
			},data);			
		});
	});
})(jQuery);

function showWaitMask(text, keep) {
	var ov = $.getOverlay(),ob = $('#overlay-body');	
	if(ob.length == 0){
		ob = $('<div id="overlay-body"><img/><div class="msg">处理中...</div></div>').appendTo($('body'));
		if(window.Kissgo){
			ob.find('img').attr('src',Kissgo.misc('images/overlay.gif'));
		}else{
			ob.find('img').attr('src','/website/misc/images/overlay.gif');
		}
	}
    text = text ? text : '处理中...';
    var msg = ob.find('div.msg');
    ov.show();
    if (!keep) {
        msg.html(text);
    }
    ob.show();
}
function hideWaitMask() {
	var ov = $('#overlay'),ob = $('#overlay-body');
	ob.fadeOut(0);
	if($('#xui-messagebox:visible').length==0){
		ov.fadeOut(0);
	}	
}

if(window.Kissgo){
	window.Kissgo.emptyFun = function(){};
	window.Kissgo.hasVScrollbar = function(){
		if(window.innerHeight){
            return document.body.offsetHeight> innerHeight;
        }
        else {
            return  document.documentElement.scrollHeight > 
                document.documentElement.offsetHeight ||
                document.body.scrollHeight>document.body.offsetHeight;
        }
	};
	window.Kissgo.publish = function(type, id, callback, data){
		if(!type){
			alert('error type or id');
		}else{			
			Kissgo.openIframe(Kissgo.murl('admin','pages/publish/'+type+'/'+id), function(win,data){
				if($.isFunction(win.setNodeData)){
					win.setNodeData(data);
				}
			},callback,data);
		}
	};
	
	window.Kissgo.murl = function(module, action){
		var alias = Kissgo.alias[module];
		if(!alias){
			alias = module;
		}
		return Kissgo.ROUTER_BASE + alias + (action ? '/'+action: '');
	};
	window.Kissgo.uploadurl = function(url){
		if(/^https?:\/\/.+/i.test(url)){
			return url;
		} else {
			return Kissgo.WEBSITE + url;
		}
	};
	window.Kissgo.misc = function(res){
		return Kissgo.MISCURL + res;
	};
	window.Kissgo.website = function(res){
		return Kissgo.WEBSITE + res;
	};
	window.Kissgo.base = function(res){
		return Kissgo.BASE + res;
	};
	window.Kissgo._iframeOnload  = function(iframe){
		var doc=false,win=false;
		if (iframe.contentWindow) {
		  win =  iframe.contentWindow;
		}	else  if (iframe.window) {
		  win = iframe_object.window;
		} else if (!doc && iframe.contentDocument) {
	      doc = iframe.contentDocument;	  
	
		  if (!doc && iframe.document) {
		    doc = iframe.document;
		  }		
		  if (doc && doc.defaultView) {
			  win = doc.defaultView;
		  }else if (doc && doc.parentWindow) {
			  win = doc.parentWindow;
		  }
		} 
		if(win){
			Kissgo.iframeOnload(win,Kissgo.iframeData);
		}		
	};
	
	window.Kissgo.openIframe = function(url, onload, onclose,data){
		Kissgo.iframeOnload = $.isFunction(onload)?onload:Kissgo.emptyFun;
		Kissgo.iframeOnclosed = $.isFunction(onclose)?onclose:Kissgo.emptyFun;
		Kissgo.iframeData = data;
		var tpl = '<div id="overlay-container">'+
			'<div class="overlay-modal-background"></div>'+
			'<iframe scrolling="auto" frameborder="0" allowtransparency="true" class="overlay-element" tabindex="-1"></iframe>'+
			'<iframe scrolling="yes" onload="Kissgo._iframeOnload(this)" frameborder="0" allowtransparency="true" class="overlay-element overlay-active" id="overlay-iframe"></iframe></div>';
			
		var overIframe = $(tpl),sw=0;
		overIframe.appendTo($('body'));	
		if(Kissgo.hasVScrollbar()){			
			sw = $(window).width();			
		}else{			
			sw = $(window).width() - $.scrollbarWidth();	
		}
		$('#navbar').width(sw);
		$('#foot').width(sw);
		$('body').addClass('show-overlay');
		url += (url.indexOf('?')>0?'&':'?')+'__ifm='+ encodeURIComponent(window.location.href);
		overIframe.find('#overlay-iframe').attr('src',url);
	};
	
	window.Kissgo.closeIframe = function(args){
		var win = window;
		while (win.location.href != win.parent.location.href) {
			win = win.parent;
		}
		win.Kissgo._closeIframe (win,args);
	};
	window.Kissgo._closeIframe = function(win,args){
		win.Kissgo.iframeOnclosed(args);		
		$('#overlay-container').remove();		
		$('#navbar').width('auto');
		$('#foot').width('auto');
		$('body').removeClass('show-overlay');
	};
	window.Kissgo.parseJson = function (data) {
		if(typeof data == 'string'){
			if ((data.substring(0, 1) != '{') && (data.substring(0, 1) != '[')) {
				return { success: false, msg: data };
			}
			return eval('(' + data + ');');
		}else if(typeof data == 'object'){
			return data;
		}else{
			return { success: false, msg: 'Unspecified error!',data:data };
		}
	};
}