$(function() {
	var murl = $('#tag-form').attr('action');
	var type = $('#tag-type').val();
	var tag_select = $('#tag-select-box').val();
	$('#btn-selectall').click(function() {
		var $this = $(this).toggleClass('selected');
		if ($this.hasClass('selected')) {
			$('.tags').addClass('selected');
		} else {
			$('.tags').removeClass('selected');
		}
	});
	$('#btn-delete').click(function() {
		var ids = [], tag;
		$('.tags').each(function(i, n) {
			tag = $(n);
			if (tag.hasClass('selected')) {
				ids.push(tag.attr('id').replace("tag_", ''));
			}
		});
		if (ids.length > 0) {
			deleteTags(ids.join(','));
			return false;
		} else {
			alert('请选择要删除的枚举值');
		}
	});
	$('.btn-add').click(function() {
		var me = $(this), tage = $('#new-tag'), tag = tage.val(), idi = me.find('i'), container = $('#tag-wrap');
		if (!/^[^\s]+$/.test(tag)) {
			return;
		}
		idi.addClass('icon-loading-14').removeClass('icon-plus');
		tag = $.trim(tag);
		$.ajax({
			url : murl + '/add',
			type : 'POST',
			data : {
				type : type,
				tag : tag
			},
			success : function(data) {
				if (data.success) {
					var tpl = '<span id="tag_' + data.id + '" class="tags label">';
					if (tag_select == 'select') {
						tpl += '<input class="tag" type="checkbox" value="' + data.id + '" tag="' + tag + '"/>';
					}
					tpl += '<i class="icon-trash"></i>' + tag + '</span>';
					container.prepend($(tpl));
					tage.val('');
				} else {
					alert(data.msg);
				}
				idi.addClass('icon-plus').removeClass('icon-loading-14');
			}
		});
	});

	$('.tags').find('i').live('click', function() {
		deleteTags($(this).parent().attr('id').replace('tag_', ''), true);
		return false;
	});
	$('.tags').live('click', function() {
		$(this).toggleClass('selected');
	});
	function deleteTags(ids, single) {
		if (!confirm('确认要删除这' + (single ? '个' : '些') + '标签吗?')) {
			return;
		}
		var me = single ? $('#tag_' + ids) : $('.tags.selected'), tagIcons = me.find('i');
		tagIcons.addClass('icon-loading-14').removeClass('icon-plus');
		$.ajax({
			url : murl + '/del?tid=' + ids,
			type : 'POST',
			success : function(data) {
				if (data.success) {
					me.remove();
				} else {
					alert(data.msg);
					tagIcons.addClass('icon-plus').removeClass('icon-loading-14');
				}
			}
		});
	}
});
function getSelectedTags() {
	var tags = [], en;
	$('.tag:checked').each(function(i, e) {
		en = $(e);
		tags[i] = {
			id : en.val(),
			name : en.attr('tag')
		};
	});
	return tags;
}