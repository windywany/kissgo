{extends file="kissgo/admincp_no_sidebar.tpl"}
{block name="breadcrumb"}
	<li>{'Extensions'|ts}</li>
{/block}
{block name="admincp_body"}
    <div>
    	{foreach $extensions as $i => $ext}
    		<p>{$ext.Plugin_Name} == <a class="install_ext" href="#" data-pid="{$ext.Plugin_ID}">安装</a></p>
    	{/foreach}
    </div>
    <script type="text/javascript" src="{'js/extension.js'|here}"></script>
{/block}