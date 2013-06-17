$(function() {
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
	/*
	$('#page-autoc').autocomplete({
		source : './?Ctlr=TopPages',
		autoFocus : true,
		select : function(event, ui) {
			if (ui.item) {
				$('#autoc-id').val(ui.item.id);
			} else {
				$('#autoc-id').val('');
			}
		}
	}).change(function() {
		var val = $.trim($(this).val());
		if (!val) {
			$('#autoc-id').val('');
		}
	});*/

	$('.edit-item').live('click', function() {
		var me = $(this), wrap = me.parents('.menu-wrap');
		if (wrap.hasClass('menu-wrap-inactive')) {
			wrap.removeClass('menu-wrap-inactive').addClass('menu-wrap-active');
		} else {
			wrap.removeClass('menu-wrap-active').addClass('menu-wrap-inactive');
		}
		return false;
	});

	$('.hide-item-form').live('click', function() {
		var me = $(this), wrap = me.parents('.menu-wrap');
		wrap.removeClass('menu-wrap-active').addClass('menu-wrap-inactive');
		return false;
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
			ids[0] = $('#autoc-id').val();
		}

		if (ids.length == 0 || !ids[0]) {
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

	function addMenuItem(data) {
		$.ajax({
			url : './?Ctlr=AddMenuItem',
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
});
