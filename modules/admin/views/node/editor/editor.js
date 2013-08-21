$(function() {			
	$('.overlay-close').click(function() {
		Kissgo.closeIframe();
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
	if($('#custom-set-tpl').attr('checked')){
		$('#tpl-wrapper').show();
	}
	$('#ontopto').datepicker({
		'format' : 'yyyy-mm-dd',
		autoclose : true
	});
	$("a.btn-save").on("click", function(event) {
		$('#node-form').submit();
	});
	$('#node-form').ajaxForm({
		'dataType' : 'json',
		error : function() {
		},
		success : function(data) {
			alert(data);
		}
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
	$('#quicktags').quicktags('summary');
	$('#page-picture').selectimg().change(function(){
		var img = $('#page-picture').select2('data');
		if(img){
			$('#page-figure').attr('src',Kissgo.uploadurl(img.t2));
		}else{
			$('#page-figure').attr('src',Kissgo.misc('images/260x180.gif'));
		}
	}).change();
	
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
	
	$('#menu').click(function() {		
		$('#tpls-menu-tree').empty();
		$.fn.zTree.destroy('tpls-menu-tree');
		$.fn.zTree.init($('#tpls-menu-tree'), ztree_setting('tpls-menu-tree','browser_menus'));		
		$('#menu-selector-box').modal('show');
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
				autoParam : [ "id" ],
				otherParam : {
					"__op" : op
				}
			}
		};
	};
	window.setNodeData = function(data) {
		//alert('ok');
	};
});