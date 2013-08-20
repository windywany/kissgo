$(function() {
	$.fn.modalmanager.defaults.resize = true;
	var setting = {
		treeId : 'tpls-tree',
		async : {
			enable : true,
			url : Kissgo.AJAX,
			autoParam : [ "id" ],
			otherParam : {
				"__op" : "browser_all_template_files"
			}
		}
	};
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
	
	$('#keywords').select2({tags:['abc','def','qbc'],tokenSeparators:[',',' ']});
	$('#tags').select2({tags:['abc','def','qbc'],tokenSeparators:[',',' ']});
	$('#source').select2({
			minimumInputLength: 1,
			query: function (query) {
				var data = {results: []};
				data.results.push({id: query.term, text: query.term});				
				query.callback(data);
			},
			initSelection : function (element, callback) {
		        var data = [];
		        $(element.val().split(",")).each(function () {
		            data.push({id: this, text: this});
		        });
		        callback(data);
		    }
	}).select2('val','def');
	
	$('#author').select2({
			minimumInputLength: 1,
			query: function (query) {
				var data = {results: []};
				data.results.push({id: query.term, text: query.term});				
				query.callback(data);
			},
			initSelection : function (element, callback) {
		        var data = [];
		        $(element.val().split(",")).each(function () {
		            data.push({id: this, text: this});
		        });
		        callback(data);
		    }
	});
	
	$('#btn-select-tpl').click(function() {		
		$('#tpls-tree').empty();
		$.fn.zTree.destroy('tpls-tree');
		$.fn.zTree.init($('#tpls-tree'), setting);		
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
	window.setNodeData = function(data) {
		//alert('ok');
	};
});