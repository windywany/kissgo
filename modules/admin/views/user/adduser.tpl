{extends file=$ksg_admincp_layout}
{block name="title"}{'Users & Roles Managerment'|ts}{/block}
{block name="breadcrumb" nocache}
    <li><a href="{'admin'|murl:users}">{'Users'|ts}</a><span class="divider">/</span></li>
    <li>{'Add User'|ts}</li>
{/block}

{block name="toolbar"}
    <button id="btn-save" data-name="s" value="1" class="btn btn-mini btn-success"><i class="icon-ok-circle"></i> {'Save'|ts}</button>
    <button id="btn-save-close" data-name="sc" value="1" class="btn btn-mini btn-primary"><i class="icon-check"></i> {'Save & Close'|ts}</button>
    <button id="btn-save-new" data-name="sn" value="1" class="btn btn-mini btn-info"><i class="icon-play-circle"></i> {'Save & New'|ts}</button>
    <a href="{'admin'|murl:users}" class="btn btn-mini btn-warning"><i class="icon-refresh"></i> {'Cancel'|ts}</a>
{/block}

{block name="admincp_body"}
<div class="tabbable">
    <ul class="nav nav-tabs">
		    <li>&nbsp;&nbsp;</li>	
			<li><a href="{$_CUR_URL}"><i class="icon-user"></i>用户列表</a></li>
			<li class="active"><a href="#"><i class="icon-plus"></i>新增用户</a></li>			    
        </ul>
        <div class="tab-content">
            <div class="tab-pane active">
                {include 'modules/admin/views/user/form.tpl'}
            </div>
        </div>
</div> 
{/block}

{block name="admincp_foot_js_block"}
<script type="text/javascript" src="{'form.js'|here}"></script>
{/block}