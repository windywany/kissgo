{extends file="kissgo/admincp_with_sidemenu.tpl"}
{block name="title"}{'Preferences'|ts}{/block}
{block name="breadcrumb"}
	<li>{'Preferences'|ts}</li>
{/block}
{block name="sidemenu"}   
    {foreach $opt_groups as $item}
        <li class="{if $item@key == $group}active{/if}">
            <a href="{$_CUR_URL}?group={$item@key}"><i class="icon-chevron-right"></i> {$item}</a>
        </li>
    {/foreach}
{/block}
{block name="admincp_body"}
<form class="form-horizontal" id="options-form" method="POST" action="{$_CUR_URL}">
    <input type="hidden" name="group" value="{$group}"/>    
    <div id="tabc-{$group}">
        {if $option_tpl}
            {include file=$option_tpl}
        {else}	                		
            {'show_option_control'|fire:$group:$options}
        {/if}	                		
        <div>						            	
            <button class="btn" type="reset">重置选项</button>
            <button class="btn btn-primary" type="submit">保存选项</button>
        </div>
    </div>
</form> 
{/block}