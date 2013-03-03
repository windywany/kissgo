{extends file=$ksg_admincp_layout}
{block name="title"}{'Users & Roles Managerment'|ts}{/block}
{block name="breadcrumb" nocache}
    <li><a href="{'admin'|murl:users}">{'Users'|ts}</a><span class="divider">/</span></li>
    <li>{'Add User'|ts}</li>
{/block}

{block name="toolbar"}
    <button id="btn-save-user" data-name="s" value="1" class="btn btn-mini btn-success">{'Save'|ts}</button>
    <button id="btn-save-close-user" data-name="sc" value="1" class="btn btn-mini btn-primary">{'Save & Close'|ts}</button>
    <button id="btn-save-new-user" data-name="sn" value="1" class="btn btn-mini btn-primary">{'Save & New'|ts}</button>
    <a href="{'admin'|murl:users}" class="btn  btn-mini">{'Cancel'|ts}</a>
{/block}

{block name="admincp_body"}
{include 'modules/admin/views/user/user_form.tpl'}
{/block}

{block name="admincp_foot_js_block"}
<script type="text/javascript" src="{'form.js'|here}"></script>
{/block}