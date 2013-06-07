$(function(){
	var url = null, oldAlias= null,newAlias=null,curTd=null;
	$('.set-alias').click(function(){		
		oldAlias = $(this).attr('data-value');
		url = $(this).attr('href');
		curTd = $(this).parents('tr').find('td.alias-td');
		$('#module-alias').val(oldAlias);
		$('#alias-form').modal('show');
		return false;
	});
	$('#btn-done').click(function(){		
		set_alias();
		return false;
	});
	function set_alias(){
		newAlias = $('#module-alias').val();
		if(newAlias != oldAlias){
			if(/^[0-9_a-z]+$/.test(newAlias)){
				$('#alias-form').modal('hide');
				showWaitMask('正在设置别名...');
				$.post(url,{alias:newAlias},function(data){
					if(data.success){
						window.location.href = data._page_url;
					}else{
						alert(data.msg);
					}
				},'json');
			}else{
				alert('别名不合法，只能是数字，字母或下划线.');
			}
		}
	}
});