{extends file="$ksg_module/kissgo/views/install/welcome.tpl"}
{block name="title"}基本配置{/block}
{block name="body"}
<form class="form-horizontal well" id="config-form" method="post">
	<input type="hidden" name="step" value="install"/>
	<input type="hidden" name="from" value="config"/>
	{$form|form}
</form>
<div class="row">
	<button class="btn btn-primary pull-right" id="next-btn">安装&gt;&gt;</button>	
	<form class="form-inline pull-right mlr10" method="post">
		<input type="hidden" name="step" value="admin"/>
		<button type="submit" class="btn" id="prev-btn">&lt;&lt;创建管理员</button>
	</form>
</div>
<script type="text/javascript">
$(function(){
	$('#config-form').uvalidate();

	$('#next-btn').click(function(){
		$('#config-form').submit();
	});
});
</script>
{/block}