{extends file="$ksg_module/kissgo/views/install/welcome.tpl"}
{block name="title"}创建管理员{/block}
{block name="body"}
<form class="form-horizontal well" id="admin-form" method="post">
	<input type="hidden" name="step" value="config"/>
	<input type="hidden" name="from" value="admin"/>
	{$form|form}
</form>
<div class="row">	
	<button  class="btn btn-primary pull-right" id="next-btn">基本配置&gt;&gt;</button>	
	<form class="form-inline pull-right mlr10" method="post">
		<input type="hidden" name="step" value="db"/>
		<button type="submit" class="btn" id="prev-btn">&lt;&lt;数据库配置</button>
	</form>
</div>
<script type="text/javascript">
$(function(){
	$('#admin-form').uvalidate();

	$('#next-btn').click(function(){
		$('#admin-form').submit();
	});
});
</script>
{/block}