{extends file="kissgo/install/welcome.tpl"}
{block name="title"}环境检测{/block}
{block name="body"}
<div class="well">
	<table class="table">
		<caption>目录读写检测</caption>
		<thead>
			<tr><th class="span4">项</th><th>要求</th><th>当前</th></tr>
		</thead>
		<tbody>
			{foreach $dirs as $dir}
			<tr class="{$dir.cls}"><td title="{$dir.path}">{$dir.dir}</td><td>可读写</td><td>{$dir.status}</td></tr>
			{/foreach}			
		</tbody>
	</table>
	<div class="alert alert-info">注: 如检测失败，请修改目录权限。</div>
</div>
<div class="well">
	<table class="table">
		<caption>服务器环境检测</caption>
		<thead>
			<tr><th class="span4">项</th><th>要求</th><th>当前</th></tr>
		</thead>
		<tbody>
			{foreach $envs as $env}
			<tr class="{$env.cls}"><td>{$env.name}</td><td>{$env.requirement}</td><td>{$env.current}</td></tr>
			{/foreach}			
		</tbody>
	</table>
	<div class="alert alert-info">注: 如检测失败，请安装或升级相应的扩展和程序。</div>
</div>
<div class="row">	
	<form class="form-inline pull-right" onsubmit="check" method="post">
		<input type="hidden" name="step" value="db"/>						   
		<button type="submit" class="btn btn-primary" id="next-btn">数据库配置&gt;&gt;</button>
	</form>
	<form class="form-inline pull-right mlr10" method="post">
		<input type="hidden" name="step" value="welcome"/>						   
		<button type="submit" class="btn" id="prev-btn">&lt;&lt;安装协议</button>
	</form>
</div>
<script type="text/javascript">
	function check(){
		return true;
	}
</script>
{/block}