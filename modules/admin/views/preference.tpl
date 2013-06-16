{extends file=$ksg_admincp_layout}
{block name="title"}{'Preferences'|ts}{/block}
{block name="breadcrumb"}
	<li>{'Preferences'|ts}</li>
{/block}

{block name="toolbar"}
	<button class="btn btn-mini btn-success" type="button" id="btn-save-preference">
	    <i class="icon-ok-circle"></i> {'Save'|ts}
	</button>
	<button class="btn btn-mini btn-warning" type="button" id="btn-reset-preference">
	    <i class="icon-refresh"></i> {'Reset'|ts}
	</button>
{/block}

{block name="admincp_body"}

<form class="form-horizontal" id="options-form" method="POST" action="{$_CUR_URL}">
    <input type="hidden" name="_g" value="{$_g}"/>
    <div class="workspace">
        <div class="tabbable tabs-left">
            <ul class="nav nav-tabs" style="min-height:385px;">
                <li>&nbsp;</li>
                {foreach $__opt_groups as $item}
				<li class="{if $item@key == $_g}active{/if}">
				    <a href="{$_CUR_URL}?_g={$item@key}">{$item}</a>
			    </li>
			    {/foreach}
		    </ul>
	        <div class="tab-content" style="padding-top:10px;">	                	
	            <div id="tabc-{$_g}" class="tab-pane active">
	            {if $__option_tpl}
                    {include file=$__option_tpl}
                {elseif $__option_form}	  
                    {$__option_form|form}      
                {else}        		
                    {'show_option_control'|fire:$_g:$__options}
                {/if}
	            </div>	                	
            </div>
         </div>
    </div>			
</form>
{/block}
{block name="admincp_foot_js_block"}
<script type="text/javascript" src="{'preference.js'|here}"></script>
{/block}