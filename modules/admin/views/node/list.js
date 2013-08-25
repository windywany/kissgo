$(function() {
	$('.btn-selectall').click(function() {
		$('#page-list').uiTable('selectAll');
	});
	$('#ipt-tag').select2({
		multiple : true,
		ajax : {
			cache:true,
			url : Kissgo.AJAX + '?__op=tags_autocomplete',
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
	$('.edit-page').click(function(){
		var type = $(this).attr('data-type'),noteId = $(this).attr('data-content');
		Kissgo.publish(type,noteId,function(id){
			window.location.reload();
		});
		return false;
	});	
});