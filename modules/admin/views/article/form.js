$(function(){
	$('.btn-save').click(function(){
		showWaitMask();
		$('#article-form').ajaxSubmit(function(data){
			if(data.success){
				window.location.href = Kissgo.murl('admin','article');
			}else{
				hideWaitMask();
				$.error(data.msg);
			}
		});
	});
	
	$('#btn-show-summary').click(function(){
		var summary = $('#quicktags-wrap');
		if(summary.hasClass('hide')){
			summary.removeClass('hide');
			$(this).html('hide');
		}else{
			summary.addClass('hide');
			$(this).html('show');
		}
		return false;
	});
	$('#quicktags-wrap').quicktags('summary');
});