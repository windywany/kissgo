$(function(){
	var detailList = $('#detail-list'),tpl = '<tr><td class="op"></td><td class="status">正在处理...</td></tr>',pgb=$('#progress-bar'),pgbv=0;	
	var detail = addDetail('初始化安装程序');
	var tasks  = [];
	
	$.post('./install.php',{step:'tasks'},function(data){
		if(data.success){
			tasks = data.taskes;
			updates('成功');
			updatep(5);
			install();			
		}else{
			updates('失败','error');			
		}
	},'json');
	
	function updatep(value){
		pgbv += value;
		if(pgbv>99){
			pgbv = 99;
		}
		pgb.css('width',pgbv+'%').html(pgbv+'%');
	}
	function addDetail(op){
		detail = $(tpl);
		detail.find('.op').html(op);
		detailList.prepend(detail);
		return detail;
	}
	function updates(status,cls){
		var clz = cls || 'success';
		detail.find('.status').html(status);
		detail.addClass(clz);
	}	
	function install(){
		var task = tasks.shift();
		var wt = 0;
		if(task){	
			wt = task.weight/2;
			addDetail(task.text);
			updatep(wt);
			$.post('./install.php',{step:task.step,arg:task.arg || ''},function(data){
				if(data.success){
					if(data.taskes){
						var len = data.taskes.length;
						for(var i=len-1;i>=0;i--){
							tasks.unshift(data.taskes[i]);
						}
					}
					updates('成功');
					updatep(wt);					
					install();			
				}else{
					updates('失败','error');			
				}
			},'json');			
		}else{
			pgb.css('width','100%').html('100%');
		}
	}
});