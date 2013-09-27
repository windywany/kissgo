$(function() {			
	$('.overlay-close').click(function() {
		Kissgo.closeIframe(false);
		return false;
	});
	
	$('.vertical-tabs').verticalTabs();

	$('#custom-set-tpl').click(function() {
		if ($(this).attr('checked')) {
			$('#tpl-wrapper').show();
		} else {
			$('#tpl-wrapper').hide();
		}
	});
	if($.trim($('#tpl-wrapper').val()).length > 0){
		$('#custom-set-tpl').attr('checked','checked') ;
	}
	if($('#custom-set-tpl').attr('checked')){
		$('#tpl-wrapper').show();
	}
	$('#ontopto').datepicker({
		'format' : 'yyyy-mm-dd',
		autoclose : true
	});
	$('#title,#cachetime,#url').click(function(){
		$(this).removeClass('error');
	});
	$("a.btn-save").on("click", function(event) {
		if(validateForm()){
			$('#node-form').ajaxSubmit({
				'dataType' : 'json',		
				success : function(data) {					
					if(data.success){						
						Kissgo.closeIframe(data);
					}else{
						$.error(data.msg);
					}
				}
			});
		}
		return false;
	});
	$('#keywords').selectag({
		minimumInputLength: 1,					
		ajax :tag_ajax('keyword')
	});
	$('#tags').selectag({
		minimumInputLength: 1,		
		ajax :tag_ajax('tag')
	});
	$('#source').select2({
		minimumInputLength: 1,
		placeholder: "Select Source",
		allowClear: true,
		ajax : tag_ajax('source'),
		initSelection : function (element, callback) {
	        var val = element.val(),data = {id: val, text: val};		        
	        callback(data);
	    }
	});
	
	$('#author').select2({
			minimumInputLength: 1,
			placeholder: "Select Author",
			allowClear: true,
			ajax : tag_ajax('author'),
			initSelection : function (element, callback) {
				var val = element.val(),data = {id: val, text: val};		        
		        callback(data);
		    }
	});
	
	$('#page-picture').selectimg().change(function(){
		var img = $('#page-picture').select2('data');
		if(img){
			$('#page-figure').attr('src',Kissgo.uploadurl(img.t2));
		}else{
			$('#page-figure').attr('src',Kissgo.misc('images/260x180.gif'));
		}
	}).change();
	
	$('#ajaxupload-figure').ajaxupload({
		max_file_size: '20mb',
		filters : [ { title : "图片", extensions : "jpg,gif,png,jpeg,bmp" }],
		onUpladed:function(rst,uploader){
			$('#page-picture').data('imgData',rst).select2('val',rst.url).change();
			uploader.addClass('ajaxupload-new');
		}
	});
	
	$('#btn-select-tpl').click(function() {		
		$('#tpls-tree').empty();
		$.fn.zTree.destroy('tpls-tree');
		$.fn.zTree.init($('#tpls-tree'), ztree_setting('tpls-tree','browser_all_template_files'));		
		$('#tpl-selector-box').modal('show');
		return false;
	});
	
	$('#btn-done').click(function() {
		var treeObj = $.fn.zTree.getZTreeObj("tpls-tree");
		var nodes = treeObj.getSelectedNodes();
		if (nodes.length == 0 || nodes[0].isParent) {
			alert('请选择一个模板');
			return false;
		}
		$('#tpl-selector-box').modal('hide');
		var template = nodes[0].id.substring(1);
		$('#template_file').val(template);
		return false;
	});		
	
	$('#vpath').click(function() {		
		$('#path-tree').empty();
		$.fn.zTree.destroy('path-tree');
		$.fn.zTree.init($('#path-tree'), ztree_setting('path-tree','browser_vpath'));		
		$('#path-selector-box').modal('show');
		return false;
	});
	
	$('#btn-path-done').click(function(){
		var treeObj = $.fn.zTree.getZTreeObj("path-tree");
		var nodes = treeObj.getSelectedNodes();
		if (nodes.length == 0) {			
			return false;
		}
		$('#path-selector-box').modal('hide');
		var path = nodes[0];
		$('#vpid').val(path.id);
		$('#vpath').val(path.cb);
		return false;
	});
	
	
	function tag_ajax (type){
		return {
			cache:true,
			url : Kissgo.AJAX + '?__op=tags_autocomplete',
			data : function(term, page) {
				if(term.length<1){
					return null;
				}
				return {
					q : term,
					t: type,
					m:'m',
					p : page
				};					
			},
			results : function(data, page) {
				return data;
			}
		};
	};
	function ztree_setting(id,op){
		return {
			treeId : id,
			async : {
				enable : true,
				url : Kissgo.AJAX,
				autoParam : [ "id",'cb' ],
				otherParam : {
					"__op" : op,
					'nid' : $('#node_id').val()
				}
			}
		};
	};
	
	function validateForm (){
		var title = $('#title').val(),ct=$('#cachetime').val(),tmp = $('#template_file').val(),type = $('#node_type').val();
		title = $.trim(title);
		if(title.length==0){			
			$.alert($('#title').attr('placeholder')+'不能为空.');
			$('#title').addClass('error').focus();			
			return false;
		}
		var subtitle = $.trim($('#subtitle').val());
		if(type == 'catalog' && subtitle.length == 0){
			$.alert('请输入合法的虚拟路径.');
			$('#subtitle').addClass('error').focus();
			return false;
		}
		var url = $.trim($('#url').val()), reg = null,uv = url.length == 0;		
		if(type == 'catalog' && !/^[\d\w][\d\w]*\/?$/.test(url)){
			$.alert('请输入合法的虚拟路径.');
			$('#url').addClass('error').focus();
			return false;
		}else if(!uv && !/^(https?:\/{2})?.+/.test(url)){			
			$.alert('URL不能为空.');
			$('#url').addClass('error').focus();
			return false;
		}
		
		var vpid = $('#vpid').val();
		if(!/^(0|[1-9][0-9]*)$/.test(vpid)){
			$.alert('请选择页面将存储于哪个虚拟目录.');
			$('#vpid').addClass('error').focus;
			return false;
		}
		
		if(!/^(0|[1-9][0-9]*)$/.test(ct)){
			$.alert('缓存时间只能是数字.');
			$('#cachetime').addClass('error').focus;
			return false;
		}
		if($('#custom-set-tpl').attr('checked') && tmp.length == 0){
			$.alert('请选择模板.',function(){
				$('#btn-select-tpl').click();
			});
			return false;	
		}	
		
		return true;
	}
	window.setNodeData = function(data) {
		for(f in data){			
			$('#'+f).val(data[f]);
		}
	};
});