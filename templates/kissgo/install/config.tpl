{extends file="kissgo/install/welcome.tpl"}
{block name="title"}基本配置{/block}
{block name="body"}
<form class="form-horizontal well" id="config-form" method="post">
	<input type="hidden" name="step" value="install"/>
	<div class="control-group">
		<label class="control-label" for="site_name">网站名称</label>
		<div class="controls">
			<input type="text" class="input-xlarge" id="site_name" name="site_name" value="KissGO! 演示站"/>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="security_key">网站安全码</label>
		<div class="controls">
			<input type="text" class="input-xxlarge" id="security_key" name="security_key" value="{$security_key}"/>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="gzip">启用GZIP压缩</label>
		<div class="controls">
			<input type="checkbox" id="gzip" name="gzip"/>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="clean_url">启用重写</label>
		<div class="controls">
			<input type="checkbox" id="clean_url" name="clean_url"/>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="i18n">启用多语言支持</label>
		<div class="controls">
			<input type="checkbox" id="i18n" name="i18n"/>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="timezone">选择时区</label>
		<div class="controls">
			<select id="timezone" name="timezone">
				<option value="Asia/Shanghai">Asia/Shanghai</option>
			</select>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="date_format">日期格式</label>
		<div class="controls">
			<select id="date_format" name="date_format">
				<option value="Y-m-d">2012-12-01</option>
				<option value="d/m/Y">01/12/2012</option>
			</select>
		</div>
	</div>
</form>
<div class="row">
	<button class="btn btn-primary pull-right" id="next-btn">安装&gt;&gt;</button>	
	<form class="form-inline pull-right mlr10" method="post">
		<input type="hidden" name="step" value="admin"/>
		<button type="submit" class="btn" id="prev-btn">&lt;&lt;创建管理员</button>
	</form>
</div>
<script type="text/javascript">
	$('#next-btn').click(function(){
		$('#config-form').submit();
	});
</script>
{/block}