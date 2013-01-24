{extends file="$ksg_module/admin/views/install/welcome.tpl"}
{block name="title"}安装类型{/block}
{block name="body"}
<div class="form-horizontal">
	 <div class="control-group">
        <label class="control-label" for="profile">请选择安装类型</label>
        <div class="controls">
          <select id="profile" name="profile">
          	{html_options selected=$profile options=$profiles}
          </select>
        </div>
      </div>
</div>
<div class="row">	
	<form class="form-inline pull-right" method="post">
		<input type="hidden" name="step" value="check"/>
		<input type="hidden" name="from" value="profile"/>						   
		<button type="submit" class="btn btn-primary" id="next-btn">环境检测&gt;&gt;</button>
	</form>
	<form class="form-inline pull-right mlr10" method="post">
		<input type="hidden" name="step" value="welcome"/>						   
		<button type="submit" class="btn" id="prev-btn">&lt;&lt;安装协议</button>
	</form>
</div>
{/block}