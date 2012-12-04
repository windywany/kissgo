{extends file="kissgo/install/welcome.tpl"}
{block name="title"}安装{/block}
{block name="body"}
<div class="well">
	<table class="table">
		<caption>数据库配置</caption>
		<thead>
			<tr><th class="span4">项</th><th>值</th></tr>
		</thead>
		<tbody>
			<tr><td>数据库驱动</td><td></td></tr>
			<tr><td>主机地址</td><td></td></tr>
			<tr><td>用户名</td><td></td></tr>
			<tr><td>数据库</td><td></td></tr>
			<tr><td>存储引擎</td><td></td></tr>		
		</tbody>
	</table>
</div>
<div class="well">
	<table class="table">
		<caption>管理员</caption>
		<thead>
			<tr><th class="span4">项</th><th>值</th></tr>
		</thead>
		<tbody>
			<tr><td>管理员账户</td><td></td></tr>
			<tr><td>登录密码</td><td></td></tr>
		</tbody>
	</table>
</div>
<div class="well">
	<table class="table">
		<caption>基本设置</caption>
		<thead>
			<tr><th class="span4">项</th><th>值</th></tr>
		</thead>
		<tbody>
			<tr><td>网站名称</td><td></td></tr>
			<tr><td>网站安全码</td><td></td></tr>
			<tr><td>启用GZIP压缩</td><td></td></tr>
			<tr><td>启用重写</td><td></td></tr>
			<tr><td>启用多语言支持</td><td></td></tr>
			<tr><td>时区</td><td></td></tr>
			<tr><td>日期格式</td><td></td></tr>
		</tbody>
	</table>
</div>
<div class="row">
	<form class="form-inline pull-right" id="todone" method="post">
		<input type="hidden" name="step" value="done"/>
		<button class="btn pull-right btn-primary" id="next-btn">开始安装</button>	
	</form>			
	<form class="form-inline pull-right mlr10" method="post">
		<input type="hidden" name="step" value="config"/>
		<button type="submit" class="btn" id="prev-btn">&lt;&lt;基本配置</button>
	</form>
</div>
<script type="text/javascript">
	$(function(){
		$('#next-btn').click(function(){
			$('#todone').submit();
		});
	});
</script>
{/block}