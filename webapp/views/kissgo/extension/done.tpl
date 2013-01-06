{extends file="kissgo/admincp.tpl"}
{block name="title"}Setup Extensions{/block}
{block name="css_block"}
<link rel="stylesheet" href="{'extension.css'|here}"/>
{/block}
{block name="breadcrumb"}
	<li><a href="{'kissgo'|murl:extension}">{'Extensions'|ts}</a><span class="divider">/</span></li>
	<li>{'Setup Extension'|ts} - {$extName}</li>
{/block}
{block name="admincp_body"}
<div class="tabbable">
    <ul class="nav nav-tabs">
	    <li>&nbsp;</li>						
		<li>
		    <a class="tgre" href="{$_page_url}"><i class="icon-check"></i>已安装</a>
		</li>						
		<li>
		    <a href="{$_page_url}?group=uninstall"><i class="icon-inbox"></i>未安装</a>
		</li>
		<li class="active">
		    <a href="#"><i class="icon-circle-arrow-down"></i>{$op_title}明细</a>
		</li>					
	</ul>

    <div class="tab-content">
        <h4>{$op_title}"{$extName}"扩展</h4>                	
	    <div class="tab-pane active">	
            <div class="alert alert-block" id="tip">
            	<h3>警告!</h3>
            	请不要关闭，退回或刷新本页，正在{$op_title}"{$extName}",请耐心等候...
            </div>
            <div class="progress progress-striped active">
            	<div id="progress-bar" class="bar" style="width: 1%;">1%</div>
            </div>
            <div class="well">
            	<table class="table">
            		<caption>{$op_title}明细</caption>
            		<thead>
            			<tr><th>操作</th><th class="span2">状态</th></tr>
            		</thead>
            		<tbody id="detail-list">
            		</tbody>
            	</table>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">    
    var INSTALL_URL = "{'kissgo'|murl:extension}";
    var type = '{$type}';
    var pid  = '{$pid}';
    var operation = '{$operation}';
    var opText = '{$op_title}';
    var extNama = '{$extName}';
</script>
<script type="text/javascript" src="{'extension.js'|here}"></script>
{/block}