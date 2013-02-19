{extends file="$ksg_module/admin/views/install/welcome.tpl"}
{block name="title"}数据库配置{/block}
{block name="body"}
<div class="well">
	<form class="form-horizontal" id="db-form" method="post">
		<input type="hidden" name="step" value="admin"/>
		<input type="hidden" name="from" value="db"/>
		{$form|form}
	</form>
</div>
<div class="row">
	<button class="btn btn-primary pull-right" id="next-btn">创建管理员&gt;&gt;</button>
	<form class="form-inline pull-right mlr10" method="post">
		<input type="hidden" name="step" value="check"/>
		<button type="submit" class="btn" id="prev-btn">&lt;&lt;环境检测</button>
	</form>
</div>
<script type="text/javascript">
	$(function(){
		$('#driver').change(function(){
			if($(this).val() == 'mysql'){
				$('#engine').parents('.control-group').show();
			}else{
				$('#engine').parents('.control-group').hide();
			}
		}).change();
		$('#db-form').uvalidate();
		$('#next-btn').click(function(){			
			$('#db-form').submit();			
		});
	});
	
</script>
{/block}