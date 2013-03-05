$(function() {
	$('#btn-selectall').click(function() {
		$('#group-list').uiTable('selectAll');
	});
	$('#menu-del-group').click(function() {
		var ids = $('#group-list').uiTable('selected');
		if (ids.length == 0) {
			alert('请选择要删除的用户组');
			return false;
		}
		if (!confirm('确定删除所选的用户组?')) {
			return false;
		}
		ids = ids.join(',');
		window.location.href = $(this).attr('href') + '&gids=' + ids;
		return false;
	});	
	$('.grant-group').click(function(){
    	var me = $(this),url = me.attr('href'),id=me.attr('id'),name = me.attr('data-content');
    	openTab(url,id,'授权【'+name+'】','icon_grant');
    	return false;
    });
});