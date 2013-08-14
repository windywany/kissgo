$(function() {
	var comment = {};
	$('.btn-selectall').click(function() {
		$('#comment-list').uiTable('selectAll');
	});
	$('#quicktags').quicktags('comment');
	$('a.reply-cmt').click(function() {
		var $this = $(this);
		comment = {};
		comment.op = 'reply';
		comment.id = $this.attr('href').replace('#', '');
		$('#subject').val('');
		$('#comment').val('');
		$('#author').val('');
		$('div.when-edit').hide();
		$('#reply-cmt-box-title').html('回复评论');
		$('#reply-cmt-box').modal('show');
		return false;
	});

	$('a.edit-cmt').click(function() {
		var $this = $(this), pt = $this.parent('.row-actions');
		comment = {};
		comment.op = 'save';
		comment.id = $this.attr('href').replace('#', '');
		$('#subject').val(pt.find('input[name=subject]').val());
		$('#comment').val(pt.find('input[name=comment]').val());
		$('#author').val(pt.find('input[name=author]').val());
		$('#url').val(pt.find('input[name=url]').val());
		$('#email').val(pt.find('input[name=email]').val());
		$('div.when-edit').show();
		$('#reply-cmt-box-title').html('编辑评论');
		$('#reply-cmt-box').modal('show');
		return false;
	});

	$('#btn-done').click(function() {
		comment.subject = $('#subject').val();
		comment.comment = $('#comment').val();
		comment.author = $('#author').val();
		if (comment.op == 'save') {
			comment.url = $('#url').val();
			comment.email = $('#email').val();
		}
		if (comment.comment.length < 10) {
			alert('求求你多写点吧.');
			return;
		}
		showWaitMask('正在保存...');
		$.ajax({
			url : $('#comment-search-form').attr('action'),
			type : 'POST',
			data : comment,
			success : function(data) {
				if (data.success) {
					window.location.reload(true);
				} else {
					alert(data.msg);
					hideWaitMask();
				}
			}
		});
	});

	$('.menu-reset-cmt').click(function() {// reset
		var ids = $('#comment-list').uiTable('selected'), url = $(this).attr('href');
		if (ids.length == 0) {
			alert('请选择要还原的评论');
			return false;
		}
		window.location.href = url + '&cid=' + ids.join(',');
		return false;
	});
	$('.menu-del-cmt').click(function() {// delete
		var ids = $('#comment-list').uiTable('selected'), url = $(this).attr('href');
		if (ids.length == 0) {
			alert('请选择要删除的评论');
			return false;
		}
		if (confirm('你确定要删除所选的评论吗?')) {
			window.location.href = url + '&cid=' + ids.join(',');
		}
		return false;
	});
	$('.menu-new-cmt').click(function() {// no
		var ids = $('#comment-list').uiTable('selected'), url = $(this).attr('href');
		if (ids.length == 0) {
			alert('请选择要设为不是垃圾的评论');
			return false;
		}
		window.location.href = url + '&cid=' + ids.join(',');
		return false;
	});
	$('.menu-pass-cmt').click(function() {// no
		var ids = $('#comment-list').uiTable('selected'), url = $(this).attr('href');
		if (ids.length == 0) {
			alert('请选择要批准通过的评论');
			return false;
		}
		window.location.href = url + '&cid=' + ids.join(',');
		return false;
	});
	$('.menu-unpass-cmt').click(function() {// no
		var ids = $('#comment-list').uiTable('selected'), url = $(this).attr('href');
		if (ids.length == 0) {
			alert('请选择不批准的评论');
			return false;
		}
		window.location.href = url + '&cid=' + ids.join(',');
		return false;
	});
	$('.menu-spam-cmt').click(function() {// no
		var ids = $('#comment-list').uiTable('selected'), url = $(this).attr('href');
		if (ids.length == 0) {
			alert('请选择要设为垃圾的评论');
			return false;
		}
		window.location.href = url + '&cid=' + ids.join(',');
		return false;
	});
	$('.menu-trash-cmt').click(function() {// no
		var ids = $('#comment-list').uiTable('selected'), url = $(this).attr('href');
		if (ids.length == 0) {
			alert('请选择要移到回收站的评论');
			return false;
		}
		window.location.href = url + '&cid=' + ids.join(',');
		return false;
	});

});