{extends file=$ksg_admincp_layout}
{block name="title"}{'Preferences'|ts}{/block}
{block name="breadcrumb"}
	<li>{'Preferences'|ts}</li>
{/block}

{block name="admincp_body"}
<table style="width:100%">
    <tr>
        <td style="width:200px;"> 
            <ul class="nav nav-tabs nav-stacked nav-kissgo">
                {foreach $opt_groups as $item}
                <li class="{if $item@key == $group}active{/if}">
                    <a href="{$_CUR_URL}?group={$item@key}">{$item}</a>
                </li>
                {/foreach}
            </ul>       
        </td>        
        <td style="width:auto;" valign="top">
        <form class="form-horizontal" id="options-form" method="POST" action="{$_CUR_URL}" style="margin-left:15px">
            <input type="hidden" name="group" value="{$group}"/>    
            <div id="tabc-{$group}">
                {if $option_tpl}
                    {include file=$option_tpl}
                {elseif $option_form}	  
                    {$option_form:form}      
                {else}        		
                    {'show_option_control'|fire:$group:$options}
                {/if}	                		
                <div>						            	
                    <button class="btn" type="reset">重置选项</button>
                    <button class="btn btn-primary" type="submit">保存选项</button>
                </div>
            </div>
        </form>
        </td>
    </tr>
</table> 
<script type="text/javascript">
$(function(){
	$('#options-form').uvalidate();
});
</script>
{/block}