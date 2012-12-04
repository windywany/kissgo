{extends file="kissgo/install/welcome.tpl"}
{block name="title"}数据库配置{/block}
{block name="body"}
<div class="well">
	<form class="form-horizontal" id="db-form" method="post">
		<input type="hidden" name="step" value="admin"/>
		<div class="control-group">
			<label class="control-label" for="driver">数据库驱动</label>
			<div class="controls">
				<select class="span2" id="driver" name="driver">
					<option value="Mysql">MySQL</option>
					<option value="PdoMysql">MySQL PDO</option>
				</select>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="host">主机地址</label>
			<div class="controls">
				<input type="text" class="input-xlarge" id="host" name="host" placeholder="IP或域名"/><span class="help-inline">数据库所在主机的IP或域名.</span>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="dbuser">用户名</label>
			<div class="controls">
				<input type="text" class="input-xlarge" id="dbuser" name="dbuser" placeholder="用户名"/><span class="help-inline">可以访问数据库的用户.</span>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="passwd">密码</label>
			<div class="controls">
				<input type="password" name="passwd" class="input-xlarge" id="passwd"/><span class="help-inline">可以访问数据库的用户的密码.</span>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="dbname">数据库</label>
			<div class="controls">
				<input type="text" class="input-xlarge" id="dbname" name="dbname" value="kissgodb"/><span class="help-inline">KissGO!将要使用的数据库.</span>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="engine">表引擎</label>
			<div class="controls">
				<select class="span2" id="engine" name="engine">
					<option value="InnoDB">InnoDB</option>
					<option value="MyISAM">MyISAM</option>
					<option value="NDB">NDB</option>
				</select><span class="help-inline">如果你使用MySQL Cluster,请选择NDB.</span>
			</div>
		</div>
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
	$('#next-btn').click(function(){
		$('#db-form').submit();
	});
</script>
{/block}