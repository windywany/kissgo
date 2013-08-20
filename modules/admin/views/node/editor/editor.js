$(function() {
	$('.overlay-close').click(function() {
		Kissgo.closeIframe();
		return false;
	});
	$('.vertical-tabs').verticalTabs();
	
	$('#custom-set-tpl').click(function(){
		if($(this).attr('checked')){
			$('#tpl-wrapper').show();
		}else{
			$('#tpl-wrapper').hide();
		}
	});
	$('#ontopto').datepicker({
		'format' : 'yyyy-mm-dd',
		autoclose : true
	});
	$( "a.btn-save" ).on( "click", function( event ) {
		$('#node-form').submit();
	});
	$('#node-form').ajaxForm({
		'dataType':'json',
		error:function(){},
		success:function(data){
			alert(data);
		}
	});
	
});