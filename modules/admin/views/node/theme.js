$(function() {
	var cur_url = $('#themes').attr('data-current-url'), select_info = {
		op : 'set'
	};
	var setting = {
		treeId : 'tpls-tree',
		async : {
			enable : true,
			url : Kissgo.AJAX,
			autoParam : [ "id" ],
			otherParam : {
				"__op" : "browser_template_files"
			}
		}
	};

	$('button.reset-all-tpl').click(function() {
		var me = $(this), theme = me.parents('.accordion-body').attr('data-theme-id');
		if (confirm('Are you sure to reset the templates to default?')) {
			showWaitMask('正在重置...');
			$.ajax({
				type : 'POST',
				url : cur_url,
				data : {
					op : 'reset',
					theme : theme
				},
				success : function(data) {
					if (data.success) {
						window.location.reload(true);
					} else {
						hideWaitMask();
						alert(data.msg);
					}
					
				}
			});
		}
	});

	$('button.use-this-tpl').click(function() {
		var me = $(this), theme = me.parents('.accordion-body').attr('data-theme-id');
		if (confirm('Are you sure to use this theme?')) {
			showWaitMask('正在重置...');
			$.ajax({
				type : 'POST',
				url : cur_url,
				data : {
					op : 'use',
					theme : theme
				},
				success : function(data) {
					if (data.success) {
						active_theme(theme);
					} else {
						alert(data.msg);
					}
					hideWaitMask();
				}
			});
		}
	});

	$('a.edit-tpl').click(function() {
		var me = $(this);
		$('#tpls-tree').empty();
		$.fn.zTree.destroy('tpls-tree');
		select_info.theme = me.parents('div.accordion-body').attr('data-theme-id');
		select_info.type = me.attr('href').replace('#', '');
		setting.async.otherParam.theme = select_info.theme;
		// init ztree
		$.fn.zTree.init($('#tpls-tree'), setting);
		// show modal
		$('#tpl-selector-box').modal('show');
		return false;
	});
	$('#btn-done').click(function() {
		var treeObj = $.fn.zTree.getZTreeObj("tpls-tree");
		var nodes = treeObj.getSelectedNodes();
		if (nodes.length == 0 || nodes[0].isParent) {
			alert('请选择一个模板');
			return;
		}
		$('#tpl-selector-box').modal('hide');
		select_info.template = nodes[0].id.substring(1);
		save();
	});
	function save() {
		showWaitMask('正在设置...');
		$.ajax({
			type : 'POST',
			url : cur_url,
			data : select_info,
			success : function(data) {
				if (data.success) {
					$('#' + select_info.theme + '-' + select_info.type + '-tpl').html(select_info.template);
				} else {
					alert(data.msg);
				}
				hideWaitMask();
			}
		});
	}
	function active_theme(theme) {
		$('#themes a.accordion-toggle').find('span.label').removeClass('label-success');
		$('#themes div.accordion-body').removeClass('in');
		$('#themes div.accordion-inner').find('button.use-this-tpl').show();
		var themewrapper = $('#theme-wrapper-' + theme);
		themewrapper.find('span.label').addClass('label-success');
		themewrapper.find('button.use-this-tpl').hide();
		$("#theme-" + theme).addClass('in');
		$('#theme-indicator').html(theme);
	}
});