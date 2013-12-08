{extends file=$layout}
{block name="layout_style_block"}
<link href="{'jquery/flexigrid/flexigrid.css'|module}"	rel="stylesheet" />
{/block}
{block name="subtitle"}页面{/block}
{block name="workbench"}
		
	    <div class="fluent-menu mgb5" data-role="fluentmenu">
    <ul class="tabs-holder">
    <li class="active"><a href="#content_id_1">Tab Name 1</a></li>
    
    <li><a href="#content_id_n">Tab Name N</a></li>
    </ul>
     
    <div class="tabs-content">
    <div class="tab-panel" id="content_id_1">
    <div class="tab-panel-group">
    <div class="tab-group-content">set of menu elements</div>
    <div class="tab-group-caption">group name</div>
    </div>
    </div>
    
    <div class="tab-panel" id="content_id_n">
    <div class="tab-panel-group">
    <div class="tab-group-content">set of menu elements</div>
    <div class="tab-group-caption">group name</div>
    </div>
    </div>
    </div>
    </div>
	
	<div id="nodes_grid"></div>
{/block}
{block name="layout_foot_block"}
<script type="text/javascript">
	seajs.use('nodes/js/app', function(app) {
            $(function(){
            	app.main();
            });        	
        });
</script>
{/block}