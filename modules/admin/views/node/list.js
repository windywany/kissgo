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
	$('.edit-page').click(function(){
		var type = $(this).attr('data-type'),noteId = $(this).attr('data-content');
		Kissgo.publish(type,noteId,function(id){
			window.location.reload();
		});
		return false;
	});
});