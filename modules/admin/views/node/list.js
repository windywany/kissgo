$(function() {
	$('.btn-selectall').click(function() {
		$('#page-list').uiTable('selectAll');
	});
	$('#use-advanced-search').click(function(){
		if($('#advanced-search-wrapper').hasClass('hide')){
			$('#advanced-search-wrapper').removeClass('hide');
			$('#advanced-search-wrapper').find('input,select').removeAttr('disabled','disabled');
			$('#use-advanced').val('1');
		}else{
			$('#advanced-search-wrapper').addClass('hide');
			$('#advanced-search-wrapper').find('input,select').attr('disabled','disabled');
			$('#use-advanced').val('');
		}
		return false;
	});
	if($('#use-advanced').val() == '1'){
		$('#use-advanced-search').click();
	}else{
		$('#advanced-search-wrapper').find('input,select').attr('disabled','disabled');
	}
	$('#navi-menu').click(function() {		
		$('#tpls-menu-tree').empty();
		$.fn.zTree.destroy('tpls-menu-tree');
		$.fn.zTree.init($('#tpls-menu-tree'), ztree_setting('tpls-menu-tree','browser_menus'));		
		$('#menu-selector-box').modal('show');
		return false;
	});
	$('#btn-menu-done').click(function(){
		var treeObj = $.fn.zTree.getZTreeObj("tpls-menu-tree");
		var nodes = treeObj.getSelectedNodes();
		if (nodes.length == 0) {			
			return false;
		}
		$('#menu-selector-box').modal('hide');
		var menu = nodes[0];
		if(menu.id == '*none'){
			$('#navi-menu').val('');
			$('#navi-menu-id').val(0);
		}else{
			$('#navi-menu').val(menu.cb);
			$('#navi-menu-id').val(menu.id);
		}
		return false;
	});
	$('.edit-page').click(function(){
		var type = $(this).attr('data-type'),noteId = $(this).attr('data-content');
		Kissgo.publish(type,noteId,function(id){
			window.location.reload();
		});
		return false;
	});	
	function ztree_setting(id,op){
		return {
			treeId : id,
			async : {
				enable : true,
				url : Kissgo.AJAX,
				autoParam : [ "id",'cb' ],
				otherParam : {
					"__op" : op
				}
			}
		};
	};
});