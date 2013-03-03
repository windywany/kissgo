{extends file=$ksg_admincp_layout}
{block name="title"}{'Preferences'|ts}{/block}
{block name="breadcrumb"}
	<li>{'Preferences'|ts}</li>
{/block}

{block name="toolbar"}
	<button class="btn btn-mini btn-success" type="button">
	    <i class="icon-ok-circle"></i> {'Save'|ts}
	</button>
	<button class="btn btn-mini btn-warning" type="button">
	    <i class="icon-refresh"></i> {'Reset'|ts}
	</button>
{/block}

{block name="admincp_body"}

<form class="form-horizontal" id="options-form" method="POST" action="{$_CUR_URL}">
    <input type="hidden" name="group" value="{$group}"/>
    <div class="workspace">
        <div class="tabbable tabs-left">
            <ul class="nav nav-tabs" style="min-height:385px;">
                <li>&nbsp;</li>
                {foreach $opt_groups as $item}
				<li class="{if $item@key == $group}active{/if}">
				    <a href="{$_CUR_URL}?group={$item@key}">{$item}</a>
			    </li>
			    {/foreach}
		    </ul>
	        <div class="tab-content" style="padding-top:10px;">	                	
	            <div id="tabc-{$group}" class="tab-pane active">
	            {if $option_tpl}
                    {include file=$option_tpl}
                {elseif $option_form}	  
                    {$option_form:form}      
                {else}        		
                    {'show_option_control'|fire:$group:$options}
                {/if}
	            </div>	                	
            </div>
         </div>
    </div>			
</form>

<script type="text/javascript">
$(function(){
	$('#options-form').uvalidate();
});
</script>
{/block}