$(function() {
	var editor = new baidu.editor.ui.Editor({
		textarea : 'body'
	});
	editor.render("myEditor");

	$('.btn-save').click(function() {
		showWaitMask();
		var publish = $(this).hasClass('btn-primary');
		$('#article-form').ajaxSubmit({
			beforeSerialize : function($form, ops) {
				var title = $.trim($('#title').val());
				if (title.length == 0) {
					hideWaitMask();
					$.alert('标题不能为空.');
					return false;
				}
				editor.sync();

				var content = editor.getContent();
				if (content.length == 0) {
					hideWaitMask();
					$.alert('你就写点东西吧.');
					return false;
				}
				return true;
			},
			beforeSubmit : function(arr, $form, ops) {
				return true;
			},
			success : function(data) {
				if (data.success) {
					if(publish){
						hideWaitMask();
						Kissgo.publish('plain',data.article.aid,function(data){
							window.location.href = Kissgo.murl('admin', 'article');
						},{title:data.article.title,url:''});
					}else{
						window.location.href = Kissgo.murl('admin', 'article');
					}					
				} else {
					hideWaitMask();
					$.error(data.msg);
				}
			}
		});
	});

	$('#btn-show-summary').click(function() {
		var summary = $('#quicktags-wrap');
		if (summary.hasClass('hide')) {
			summary.removeClass('hide');
			$(this).html('hide');
		} else {
			summary.addClass('hide');
			$(this).html('show');
		}
		return false;
	});
	$('#quicktags-wrap').quicktags('summary');
});