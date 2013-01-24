$(function(){
	var detailList = $('#detail-list'),tpl = '<tr><td class="op"></td><td class="status"></td></tr>',pgb=$('#progress-bar'),pgbv=0;	
	var detail = addDetail('初始化安装程序');
	var tasks  = [],errMsg='';
	
	$.post('./install.php',{step:'tasks'},function(data){
		if(data.success){
			tasks = data.taskes;
			updates();
			updatep(5);
			install();			
		}else{
			errMsg = data.msg;
			updates('error');			
			alert(data.msg);
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
	function updates(cls){
		var clz = cls || 'success';
		detail.addClass(clz);
		if(clz == 'error'){
			var tip = $('#tip').removeClass('alert-block').addClass('alert-error');
			tip.html('<h3>出错啦！</h3>在安装的过程中发生了错误:<br/>'+errMsg);	
			$('.page-header').find('h2').html('安装失败');
		}
	}	
	function install(){
		var task = tasks.shift();
		var wt = 0;
		if(task){	
			wt = task.weight/2;
			addDetail(task.text);
			updatep(wt);
			$.post('./install.php',{step:task.step,arg:task.arg || ''},function(data){
				if(!data){
					errMsg = '服务器返回未知数据.';
					updates('error');
					return;
				}
				if(data.success){
					if(data.taskes){
						var len = data.taskes.length;
						for(var i=len-1;i>=0;i--){
							tasks.unshift(data.taskes[i]);
						}
					}
					updates();
					updatep(wt);					
					install();			
				}else{
					errMsg = data.msg;
					updates('error');
					alert(data.msg);
				}
			},'json');			
		}else{
			pgb.css('width','100%').html('100%');
			show_success();
		}
	}
	function show_success(){
		var tip = $('#tip').removeClass('alert-block').addClass('alert-success');
		tip.html('<h3>恭喜！</h3>你已经成功安装了KissGO!,现在你就可以登录<a href="'+BASE_URL+'kissgo/">管理后台</a>进行操作了,祝使用愉快.');	
		$('.page-header').find('h2').html('安装成功');
	}
});