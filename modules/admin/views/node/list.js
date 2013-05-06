$(function() {
	$('.btn-selectall').click(function() {
		$('#comment-list').uiTable('selectAll');
	});
	$('#ipt-tag').select2({
		multiple : true,
		ajax : {
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
});