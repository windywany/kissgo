var WebPageForm = {
	beforeSerialize : [],
	beforeSubmit : [],
	bi : 0,
	bj : 0,
	addSerializeHandler : function(handler) {
		if($.isFunction(handler)) {
			this.beforeSerialize[this.bi++] = handler;
		}
	},
	addSubmitHandler : function(handler) {
		if($.isFunction(handler)) {
			this.beforeSubmit[this.bj++] = handler;
		}
	}
};
$(function() {
	var setting = {
		data : {
			simpleData : {
				enable : true
			}
		}
	};
	var page_id = $('#page_id').val();
	$('.btn-close-form').click(function() {
		$('.modal').modal('hide');
		return false;
	});
	$('#btn-set-figure').click(function() {
		parent.Desktop.openAttachDialog(setFigure);
		return false;
	});	
	$('#btn-del-figure').click(function(){
		$('#figure').val('');
		$('#page-picture').find('img').attr('src',$('#dfigure').val());
	});
	init_tree_modal();
	init_category_modal();
	init_submit_form();
	$('#ontopto').datepicker();
	$('#btn-add-tag').click(addTag);
	$('#page-tags .tag').find('i').live('click', delTag);	
	$('#custom-set-tpl').change(customTpl);
	$('.postbox').delegate('.handlediv','click',function(){
        var me = $(this),sutffbox= me.parents('.postbox');
        if(sutffbox.hasClass('closed')){
            sutffbox.removeClass('closed');
        }else{
            sutffbox.addClass('closed');
        }
    });
	$('#select-keywords').click(function() {
		parent.Desktop.openIframeDialog('常用关键词', './enum/?Ctlr=Enums&type=keyword', select_keywords);
		return false;
	});
	$('#page_info_close').click(function(){
		$('#page_info').hide();
	});
	$('.btn-enums').click(function() {
		var me = $(this), title = me.attr('title'), type = me.attr('for');
		parent.Desktop.openIframeDialog(title, './enum/?Ctlr=Enums&type=' + type, function(frame) {
			var val = frame.getSelectedEnums(), eipt = $('#' + type);
			if(val.length > 0) {
				eipt.val(val[0].name);
			}
		});
		return false;
	});
	$('#btn-select-tag').click(function() {
		parent.Desktop.openIframeDialog('常用标签', './tags/?Ctlr=Tags', set_select_tags);
		return false;
	});
	function init_submit_form() {
		WebPageForm.addSubmitHandler(check_page_valid);
		$('.btn-publish').click(function() {
			$('#page-form').data('status', 'published').trigger('submit');
			return false;
		});
		$('.btn-save-draft').click(function() {
			$('#page-form').data('status', 'draft').trigger('submit');
			return false;
		});
		$('.btn-move2trash').click(function() {
			$('#page-form').data('status', 'deleted').trigger('submit');
			return false;
		});	
		$('.btn-approving').click(function() {
			$('#page-form').data('status', 'approving').trigger('submit');
			return false;
		});
		$('#page-form').submit(function() {
			var me = $(this), status = me.data('status');
			showWaitMask('正在保存...');
			me.ajaxSubmit({
				beforeSerialize : function($form, ops) {
					var rst = true;
					for( i = 0; i < WebPageForm.bi; i++) {
						rst = WebPageForm.beforeSerialize[i]($form, ops);
						if(rst === false) {
							hideWaitMask();
							$(window).scrollTop(0);
							return false;
						}
					}
					return true;
				},
				beforeSubmit : function(arr, $form, ops) {
					var rst = true;
					arr.status = status;
					for( i = 0; i < WebPageForm.bj; i++) {
						rst = WebPageForm.beforeSubmit[i](arr, $form, ops);
						if(rst === false) {
							hideWaitMask();
							$(window).scrollTop(0);
							return false;
						}
					}
					return true;
				},
				data : {
					status : status
				},
				success : function(data) {
					if(data.success) {
						if(!data.msg){
							if(status == 'deleted'){
								alert('网页已经移至回收站.');
							}else if(status == 'published'){
								alert('网页已经发布.');
							}else if(status == 'approving'){
								alert('网页已经提交审核.');
							}else{
								alert('草稿已经保存.');
							}
						}
						var page_id = data.page_id;
						var page_url = data.url;
						editor.setContent(data.content);
						$('#page_id').val(page_id);
						$('#url').val(page_url);
						$('#btn-preview').attr('href',IMG_BASE_URL+page_url+'?preview');
						$('.btn-move2trash').show();
						$('.btn-preview').show();
						if(data.figure){
							$('#figure').val(data.figure);
							$('#page-picture').find('img').attr('src',data.save_figure);
						}
						
					}
					if(data.msg) {
						alert(data.msg);
					}
					hideWaitMask();
				}
			});
			return false;
		});
	}
	
	function check_page_valid(arr, $form, ops) {
		if(arr.status == 'draft' || arr.status == 'deleted') {
			return true;
		}
		var valid = true, msgdiv = $('#page_msg').empty();
		if(!/^[^\s]+$/.test($('#title').val())) {
			valid = false;
			msgdiv.append($('<p>网页标题不能为空,请填写.</p>'));
		}		
		var page_type = $('#page_type').val(),page_mode=$('#page_mode').val();
		if(!/^[^\s]+$/.test(page_type)) {
			valid = false;
			msgdiv.append($('<p>网页类型不能为空,请选择一个网页类型.</p>'));
		}		
		
		var url = $.trim($('#url').val());
		if(url && !/^[^\/&\?_][^&\?]+(\.s?html?|\{args\})$/.test(url)){
			valid = false;
			msgdiv.append($('<p>url格式有误,url中不能含有_,?,&字符.</p>'));
		}
		
		if(page_type == 'template' && !url){
			valid = false;
			msgdiv.append($('<p>模板页的url必须填写.</p>'));
		}
		var tplf = $('#template_file').val(),tpld = !/^[^\s]+$/.test(tplf);
		if(page_type == 'template' && tpld){
			valid = false;
			msgdiv.append($('<p>模板页必须指定模板文件.</p>'));
		}
		if($('#custom-set-tpl').attr('checked') && tpld) {
			valid = false;
			msgdiv.append($('<p>你选择了自定义模板,请选择模板文件.</p>'));
		}
		var ct = $('#cachetime').val()
		if(ct && !/^[\d]+$/.test(ct)) {
			valid = false;
			msgdiv.append($('<p>缓存时间只能为数字.</p>'));
		}
		if(!valid) {
			$('#page_info').addClass('alert-error').show();
		}
		return valid;
	}

	function customTpl() {
		var me = $(this), tplw = $('#tpl-wrapper');
		if(me.attr('checked')) {
			tplw.show();
		} else {
			tplw.hide();
		}
	}

	function addTag() {// 添加标签
		var tagstr = $.trim($('#tags').val()), tags, tage, tagw = $('#page-tags');
		if(tagstr.length == 0) {
			return false;
		}
		tags = tagstr.split(',');
		if(tags.length == 0) {
			return false;
		}
		$.each(tags, function(i, tag) {
			if(!hasTag(tag)) {
				tage = $('<span class="tag label"><input type="hidden" name="tags[]" value="' + tag + '"/><i class="icon-trash"></i>' + tag + '</span>');
				tagw.prepend(tage);
			}
		});
		$('#tags').val('');
	}

	function delTag() {// 删除标签
		var me = $(this), tag = me.parent();
		me.addClass('icon-loading-14').removeClass('icon-trash');
		tag.remove();
	}

	function set_select_tags(frame) {
		var val = frame.getSelectedTags(), eipt = $('#tags'), tags = [];
		$.each(val, function(i, e) {
			tags[i] = e.name;
		});
		eipt.val(tags.join(','));
		addTag();
	}

	function setFigure(attach) {
		if(attach.length > 0) {
			attach = attach[0];
			src = attach.url;
			$('#page-picture > img').attr('src', IMG_BASE_URL + src);
			$('#figure').val(src);
		}
	}

	function init_category_modal() {// 选择模板

		//$.fn.zTree.init($("#cate-tree"), setting, treeNodes);

		$('#btn-cate-select').click(function() {
			window.pagecate = [$('#category'), $('#category_name')];
			$('#cate-tree-modal').modal('show');
			return false;
		});
		$('#btn-subcate-select').click(function() {
			window.pagecate = [$('#subcategory'), $('#subcategory_name')];
			$('#cate-tree-modal').modal('show');
			return false;
		});
		$('#btn-subcate-reset').click(function() {
			$('#subcategory').val('');
			$('#subcategory_name').val('');
		});
		$('#btn-cate-reset').click(function() {
			$('#category').val('');
			$('#category_name').val('');
		});
		$('#btn-cate-done').click(function() {
			var treeObj = $.fn.zTree.getZTreeObj("cate-tree");
			var nodes = treeObj.getSelectedNodes();
			if(nodes.length) {
				var node = nodes[0];
				$('#cate-tree-modal').modal('hide');
				window.pagecate[0].val(node.cate);
				window.pagecate[1].val(node.name);
			}
			return false;
		});
	}

	function init_tree_modal() {
		$('#custom-set-tpl').change(function() {
			if(!$(this).attr('checked')) {
				$('#template_file').val('');
			}
		});
		/*$.fn.zTree.init($("#tpls-tree"), $.extend(setting, {
			callback : {
				onClick : function() {
					$('#tpl-info').hide();
				}
			}
		}), tplNodes);*/
		$('#btn-select-tpl').click(function() {
			if($('#custom-set-tpl').attr('checked')) {
				$('#tpl-info').hide();
				$('#tpl-tree-modal').modal('show');
			}
			return false;
		});
		$('#btn-tpl-done').click(function() {
			var treeObj = $.fn.zTree.getZTreeObj("tpls-tree");
			var nodes = treeObj.getSelectedNodes();
			if(nodes.length) {
				var node = nodes[0];
				if(node.isParent !== true) {
					$('#tpl-tree-modal').modal('hide');
					var tpl = node.dir + '/' + node.name;
					tpl = tpl.substring(1);
					$('#template_file').val(tpl);
					return false;
				}
			}
			$('#tpl-info').show();
			return false;
		});
	}

	function select_keywords(keyframe) {
		var enums = keyframe.getSelectedEnums(), keyipt = $('#keywords'), keys = keyipt.val(), skeys = [];
		$.each(enums, function(i, e) {
			skeys[i] = e.name;
		});
		skeys = skeys.join(',');
		keys = keys ? (keys + ',' + skeys) : skeys;
		keyipt.val(keys);
	}

	function hasTag(tag) {
		var tags = $('#page-tags').find('.tag'), len = tags.length, _tag;
		for( i = 0; i < len; i++) {
			if(tag == tags.eq(i).text()) {
				return true;
			}
		}
		return false;
	}

});
var editor = new baidu.editor.ui.Editor({
	textarea : 'content'
});
editor.render("myEditor");
WebPageForm.addSerializeHandler(function(){
	editor.sync();
	return true;
});