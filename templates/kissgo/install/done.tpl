{extends file="kissgo/install/welcome.tpl"}
{block name="title"}正在安装...{/block}
{block name="body"}
<div class="alert alert-block" id="tip">
	<h3>警告!</h3>
	请不要关闭，退回或刷新本页，安装正在进行中,请耐心等候。
</div>
<div class="progress progress-striped active">
	<div id="progress-bar" class="bar" style="width: 1%;">1%</div>
</div>
<div class="well">
	<table class="table">
		<caption>安装明细</caption>
		<thead>
			<tr><th>操作</th><th class="span2">状态</th></tr>
		</thead>
		<tbody id="detail-list">
		</tbody>
	</table>
</div>
<script type="text/javascript"> var BASE_URL = '{$base_url}';</script>
<script type="text/javascript" src="{'install.js'|here}"></script>
{/block}