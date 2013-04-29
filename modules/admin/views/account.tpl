{extends file=$ksg_admincp_layout}
{block name="title"}{'Account'|ts}{/block}
{block name="breadcrumb"}
	<li>{'Account'|ts}</li>
{/block}

{block name="admincp_body"}

<form class="form-horizontal" id="account-form" method="POST" action="{$_CUR_URL}">
    <input type="hidden" name="__group" value="{$__group}"/>    
        <div class="tabbable">
            <ul class="nav nav-tabs">
                <li>&nbsp;&nbsp;&nbsp;&nbsp;</li>
                {foreach $__opt_groups as $item}
				<li class="{if $item@key == $__group}active{/if}">
				    <a href="{$_CUR_URL}?group={$item@key}"><i class="icon-user"></i> {$item}</a>
			    </li>
			    {/foreach}
		    </ul>
	        <div id="tabc-{$__group}">
	            {if $__account_tpl}
                    {include file=$__account_tpl}
                {elseif $__account_form}	  
                    {$__account_form|form}      
                {else}        		
                    {'show_account_form'|fire:$__group:$data}
                {/if}
	        </div>
         </div>
    <div style="margin:0" class="form-actions">						            	
        <button type="reset" class="btn btn-danger">重置选项</button>
        <button type="submit" class="btn btn-primary">保存选项</button>
    </div>
</form>

<script type="text/javascript">
$(function(){
	$('#account-form').uvalidate();
});
</script>
{/block}