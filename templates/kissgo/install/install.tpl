{extends file="kissgo/install/welcome.tpl"}
{block name="title"}安装{/block}
{block name="body"}
<div>
    {if !$db_connection}
	<div class="alert alert-error check_rst">
	    <h3 class="error">出错啦!</h3>
	    {$db_error}
	</div>
    {/if}
	<table class="table table-bordered">
		<caption>数据库配置</caption>
		<thead>
			<tr><th class="span4">项</th><th>值</th><th>检测</th></tr>
		</thead>
		<tbody class="check_rst">
		    {foreach $db_form as $item}
			<tr class="{$item->error_cls}"><td>{$item->label}</td><td>{$item->readable}</td><td class="check">{$item->error}</td></tr>
			{/foreach}			
		</tbody>
	</table>	
</div>
<div>
	<table class="table table-bordered table-striped">
		<caption>管理员</caption>
		<thead>
			<tr><th class="span4">项</th><th>值</th><th>检测</th></tr>
		</thead>
		<tbody class="check_rst">
			{foreach $admin_form as $item}
			<tr class="{$item->error_cls}"><td>{$item->label}</td><td>{$item->readable}</td><td class="check">{$item->error}</td></tr>
			{/foreach}
		</tbody>
	</table>
</div>
<div>
	<table class="table table-bordered table-striped">
		<caption>基本设置</caption>
		<thead>
			<tr><th class="span4">项</th><th>值</th><th>检测</th></tr>
		</thead>
		<tbody class=check_rst>
			{foreach $config_form as $item}
			<tr class="{$item->error_cls}"><td>{$item->label}</td><td>{$item->readable}</td><td class="check">{$item->error}</td></tr>
			{/foreach}
		</tbody>
	</table>
</div>
<div class="row">
	<form class="form-inline pull-right" id="todone" method="post">
		<input type="hidden" name="step" value="done"/>
		<button class="btn pull-right btn-primary" id="next-btn" disabled="disabled">开始安装</button>	
	</form>			
	<form class="form-inline pull-right mlr10" method="post">
		<input type="hidden" name="step" value="config"/>
		<button type="submit" class="btn" id="prev-btn">&lt;&lt;基本配置</button>
	</form>
</div>
<script type="text/javascript">
	$(function(){
		if($('.check_rst').find('.error').length == 0){
			$('#next-btn').removeAttr('disabled');		
		}
		$('#next-btn').click(function(){
			$('#todone').submit();
		});
	});
</script>
{/block}