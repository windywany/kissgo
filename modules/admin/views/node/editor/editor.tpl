{extends file=$ksg_admincp_layout}
{block name="title"}{'Pages Editor'|ts}{/block}
{block name="admincp_css_block"}
<link rel="stylesheet" href="{'bootstrap/select2/select2.css'|static}"/>
{/block}
{block name="breadcrumb" nocache}
<li>{'Pages Editor'|ts}</li>
{/block}

{block name="toolbar"}
    <button id="btn-save" data-name="s" value="1" class="btn btn-mini btn-success"><i class="icon-ok-circle"></i> {'Save'|ts}</button>
    <button id="btn-save-close" data-name="sc" value="1" class="btn btn-mini btn-primary"><i class="icon-check"></i> {'Save & Close'|ts}</button>    
    <a href="{'admin'|murl:'pages'}" class="btn btn-mini btn-warning"><i class="icon-refresh"></i> {'Cancel'|ts}</a>
{/block}

{block name="admincp_body"}
<ul class="nav nav-tabs">
    <li>&nbsp;&nbsp;</li>
    <li class="active"><a href="{$_CUR_URL}"><i class="icon-file"></i> 草稿箱({$draftTotal})</a></li>
    
</ul>
<div class="tab-content">
</div>
{/block}

{block name="admincp_foot_js_block"}
<script type="text/javascript" src="{'bootstrap/select2/select2.min.js'|static}"></script>
<script type="text/javascript" src="{'editor.js'|here}"></script>
{/block}