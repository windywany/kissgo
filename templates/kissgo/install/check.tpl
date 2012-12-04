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
			<tr class="success"><td>appdata/</td><td>可读写</td><td>可读写</td></tr>
			<tr class="success"><td>appdata/logs/</td><td>可读写</td><td>可读写</td></tr>
			<tr class="success"><td>appdata/tmp/</td><td>可读写</td><td>可读写</td></tr>
			<tr class="success"><td>uploads/</td><td>可读写</td><td>可读写</td></tr>
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
			<tr class="success"><td>PHP</td><td>5.2+</td><td>5.3.19</td></tr>
			<tr class="success"><td>MySQL</td><td>5.0+</td><td>5.1.6</td></tr>			
			<tr class="success"><td>mb_string extension</td><td>有</td><td>有</td></tr>
			<tr class="error"><td>json extension</td><td>有</td><td>无</td></tr>
			<tr class="success"><td>pdo_mysql</td><td>可选</td><td>有</td></tr>
		</tbody>
	</table>
	<div class="alert alert-info">注: 如检测失败，请安装或升级相应的扩展和程序。</div>
</div>
<div class="row">	
	<form class="form-inline pull-right" onsubmit="return check();" method="post">
		<input type="hidden" name="step" value="db"/>						   
		<button type="submit" class="btn btn-primary" id="next-btn">数据库配置&gt;&gt;</button>
	</form>
	<form class="form-inline pull-right mlr10" method="post">
		<input type="hidden" name="step" value="welcome"/>						   
		<button type="submit" class="btn" id="prev-btn">&lt;&lt;安装协议</button>
	</form>
</div>
{/block}