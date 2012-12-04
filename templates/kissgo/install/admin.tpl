{extends file="kissgo/install/welcome.tpl"}
{block name="title"}创建管理员{/block}
{block name="body"}
<form class="form-horizontal well" id="admin-form" method="post">
	<input type="hidden" name="step" value="config"/>
	<div class="control-group">
		<label class="control-label" for="name">管理员账号</label>
		<div class="controls">
			<input type="text" class="input-xlarge" id="name" name="name" value="root"/>
		</div>
	</div>	
	<div class="control-group">
		<label class="control-label" for="passwd">登录密码</label>
		<div class="controls">
			<input type="password" name="passwd" class="input-xlarge" id="passwd"/>
		</div>
	</div>
</form>
<div class="row">	
	<button  class="btn btn-primary pull-right" id="next-btn">基本配置&gt;&gt;</button>	
	<form class="form-inline pull-right mlr10" method="post">
		<input type="hidden" name="step" value="db"/>
		<button type="submit" class="btn" id="prev-btn">&lt;&lt;数据库配置</button>
	</form>
</div>
<script type="text/javascript">
	$('#next-btn').click(function(){
		$('#admin-form').submit();
	});
</script>
{/block}