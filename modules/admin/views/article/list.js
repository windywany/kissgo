$(function() {
	$('#btn-selectall').click(function() {
        $('#article-list').uiTable('selectAll');
    });
	$('.datepicker').datepicker({
		'format' : 'yyyy-mm-dd',
		autoclose : true
	}).on('changeDate', function(ev) {
		var date = ev.date, target = $(ev.target).attr('id');
		if (target == 'time1') {
			$('#time2').datepicker('setStartDate', date);
		} else {
			$('#time1').datepicker('setEndDate', date);
		}
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
});