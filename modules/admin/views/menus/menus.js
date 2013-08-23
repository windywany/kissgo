$(function() {	
	var url = $('#edit-menuitem').attr('action'), item = null;
	$('ol.sortable').nestedSortable({
		forcePlaceholderSize : true,
		handle : 'dt.menu-item-handle',
		helper : 'clone',
		items : 'li',
		maxLevels : 10,
		opacity : .6,
		placeholder : 'placeholder',
		revert : 250,
		tabSize : 25,
		tolerance : 'pointer',
		toleranceElement : '> div'
	});
	$('#autoc-id').select2({
		placeholder:'输入页面标题',
		multiple : true,
		ajax : {
			cache:true,
			url : Kissgo.AJAX + '?__op=nodes_autocomplete',
			data : function(term, page) {
				return {
					q : term,
					p : page
				};
			},
			results : function(data, page) {
				return data;
			}
		}
	});

	$('.edit-item').live('click', function() {
		var me = $(this), wrap = me.parents('.menu-wrap');
		item = wrap;
		$('#ipt-menu-name').val(wrap.find('.item_name').val());
		$('#ipt-menu-title').val(wrap.find('.title').val());
		if (wrap.find('.url').length > 0) {
			$('#ipt-menu-url').val(wrap.find('.url').val());
			$('#ipt-url-wrap').removeClass('hide');
		} else {
			$('#ipt-url-wrap').addClass('hide');
		}
		$('#menuitem-editor').find('input[name=item_target][value=' + wrap.find('.target').val() + ']').attr('checked', true);
		$('#menuitem-editor').modal('show');
		return false;
	});
	$('#menuitem-editor-done').click(function() {
		var item_name = $.trim($('#ipt-menu-name').val());
		if (item_name.length == 0) {
			alert('请填菜单项名称.');
			return;
		}
		if (!$('#ipt-url-wrap').hasClass('hide')) {
			var url = $('#ipt-menu-url').val();
			if (!/^https?:\/\/.+/.test(url)) {
				alert('请填写正确的URL.');
				return;
			}
			item.find('.url').val(url);
		}
		item.find('.item_name').val(item_name);
		item.find('.title').val($('#ipt-menu-title').val());
		item.find('.target').val($('#menuitem-editor').find('input[name=item_target]:checked').val());
		item.find('.item-title').text($('#ipt-menu-name').val());
		$('#menuitem-editor').modal('hide');
	});
	$('.del-item').live('click', function() {
		if (!confirm('你确定要移除这个菜单项?')) {
			return false;
		}
	});

	$('#edit-menuitem').submit(function() {
		var items = $('ol.sortable').nestedSortable('toArray', {
			startDepthCount : 1
		}), len = items ? items.length : 0;
		var id, pid, item;
		for ( var i = 0; i < len; i++) {
			item = items[i];
			id = item.item_id;
			pid = item.parent_id == 'root' ? 0 : item.parent_id;
			item = $('#menu-item-' + id + ' > div');
			item.find('.up_id').val(pid);
			item.find('.sort').val(i);
		}
		return true;
	});

	$('#btn-add-url2menu').click(function() {
		var name = $('#n_item_name').val(), title = $('#n_title').val(), url = $('#n_url').val(), target = $('input[name=n_target]:checked').val();
		url = $.trim(url);
		if (!/^https?:\/\/.+$/.test(url)) {
			alert('请填写正确的链接地址.');
			return;
		}
		name = $.trim(name);
		if (!name) {
			alert('请填写名称');
			return;
		}
		var mid = $('#menu_name').val();
		showWaitMask('正在添加菜单项...');
		var data = {
			item_name : name,
			url : url,
			title : title,
			target : target,
			type : 'url',
			menu_name : mid
		};
		$('#n_item_name').val('');
		$('#n_title').val('');
		$('#n_url').val('');
		addMenuItem(data);
	});

	$('#add-page2-menu').click(function() {
		var mid = $('#menu_name').val(), target = $('input[name=n_p_target]:checked').val(), pw = $('#pages-wrapper').find('.active'), pid = pw.attr('id'), ids = [];

		if (pid == 'page-A') {
			pw.find('.npage:checked').each(function(i, e) {
				ids[i] = $(e).val();
			});
		} else {
			ids = $('#autoc-id').select2('val');
		}

		if (ids.length == 0) {
			alert('请选择页面!');
			return;
		}

		showWaitMask('正在添加菜单项...');
		var data = {
			ids : ids.join(','),
			target : target,
			type : 'page',
			menu_name : mid
		};
		addMenuItem(data);
	});
	$.fn.zTree.init($('#browser-vfs'),{
		treeId : 'browser-vfs',
		view: {
			addHoverDom: addHoverDom,
			removeHoverDom: removeHoverDom,
			selectedMulti: false
		},
		async : {
			enable : true,
			url : Kissgo.AJAX,
			autoParam : [ "id",'path' ],
			otherParam : {
				"__op" : 'browser_vfs'
			}
		}
	});
	function addMenuItem(data) {
		$.ajax({
			url : url + '/add/',
			dataType : 'text',
			type : 'POST',
			data : data,
			success : function(data) {
				hideWaitMask();
				if (typeof data == 'string') {
					if (data.indexOf('error:') >= 0) {
						alert(data.substring(6));
					} else {
						$('#menu-instructions').hide();
						$('#menuitem-list').append($(data));
					}
				} else if (!data.success) {
					alert(data.msg);
				}
			}
		});
	}
	function addHoverDom(treeId, treeNode) {
		var sObj = $("#" + treeNode.tId + "_span");
		if (treeNode.editNameFlag || $("#addBtn_"+treeNode.tId).length>0) return;
		var addStr = "<span class='button addCatalog' id='addBtn_" + treeNode.tId
			+ "' title='Create Virtual Directory' onfocus='this.blur();'></span>";
		sObj.after(addStr);
		var btn = $("#addBtn_"+treeNode.tId);
		if (btn) btn.bind("click", function(){
			var zTree = $.fn.zTree.getZTreeObj("browser-vfs");			
			$.prompt('Create Virtual Directory',['Path','Name'],function(get,setError){
				var path = $.trim(get(0)),name = get(1);
				if(!path){
					setError(0);
					return false;
				}
				if(!name){
					name = path;
				}
				showWaitMask();
				$.post(Kissgo.murl('admin','vfs'),{path:path,name:name,pfid:treeNode.id},function(data){
					if(data.success){
						path = treeNode.id?treeNode.path+'/'+path:path;
						zTree.addNodes(treeNode, {id:data.id, pId:treeNode.id, name:name,path:path,isParent:true});
						$.confirm('Do you want publish this path to a front page?',function(){
							Kissgo.publish('catalog',data.id,null,{title:name,url:'{path}/index.html'});
						});
					}else{
						$.alert(data.msg);
					}
					hideWaitMask();
				},'json');
			});			
			return false;
		});
	};
	function removeHoverDom(treeId, treeNode) {
		$("#addBtn_"+treeNode.tId).unbind().remove();
	};
});
